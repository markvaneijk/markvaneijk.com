<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use App\Domain\Socials\ConnectsThroughOAuth;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class X implements ConnectsThroughOAuth
{
    use CachesResponses;

    /**
     * `users.read` needs `tweet.read` alongside it, and without `offline.access`
     * X hands back no refresh token — the connection would then die two hours
     * after it was made.
     */
    public const SCOPES = 'tweet.read users.read offline.access';

    private const CACHE_KEY_ACCESS = 'x.access_token';

    private const CACHE_KEY_REFRESH = 'x.refresh_token';

    private const CACHE_KEY_VERIFIER = 'x.code_verifier';

    /** The one answer the /now widget asks for; stale once the tokens change. */
    private const CACHE_KEY_FOLLOWERS = 'x.followers';

    /** Long enough to approve the app in a browser, short enough to not linger. */
    private const VERIFIER_TTL = 900;

    protected string $client_id;

    protected string $client_secret;

    protected string $base_url;

    protected string $token_url;

    protected Repository $cache;

    public function __construct(Repository $cache)
    {
        $this->client_id = (string) config('services.x.client_id');
        $this->client_secret = (string) config('services.x.client_secret');
        $this->cache = $cache;
        $this->base_url = 'https://api.x.com/2';
        $this->token_url = 'https://api.x.com/2/oauth2/token';
    }

    public function client()
    {
        return Http::withToken($this->getAccessToken());
    }

    public function isConfigured(): bool
    {
        return $this->client_id !== '' && $this->client_secret !== '';
    }

    public function redirectUri(): string
    {
        return (string) config('services.x.redirect_uri')
            ?: route('socials.callback', 'x');
    }

    /**
     * X requires PKCE even from a client that also sends a secret, and the
     * verifier has to survive until the code comes back — a different process
     * than the one that built this URL, so it goes in the store.
     */
    public function authorizeUrl(string $state = ''): string
    {
        $verifier = Str::random(64);

        $this->cache->put(self::CACHE_KEY_VERIFIER, $verifier, self::VERIFIER_TTL);

        return 'https://x.com/i/oauth2/authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirectUri(),
            'scope' => self::SCOPES,
            'state' => $state,
            'code_challenge' => $this->challengeFor($verifier),
            'code_challenge_method' => 'S256',
        ]);
    }

    public function connect(string $code): void
    {
        $verifier = $this->cache->pull(self::CACHE_KEY_VERIFIER);

        if (! $verifier) {
            throw new RuntimeException(
                'No PKCE verifier stored for this code. X only accepts a code against the '
                .'authorize URL that started the flow — run `php artisan socials:connect x` '
                .'and use the link it prints.'
            );
        }

        $token = $this->requestTokens([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri(),
            'code_verifier' => $verifier,
        ]);

        if (empty($token['access_token'])) {
            throw new RuntimeException(
                'X refused the authorization code: '.$this->reason($token).'.'
            );
        }

        // Without a refresh token the widget goes dark in two hours, which is
        // long enough for the connection to look fine right now.
        if (empty($token['refresh_token'])) {
            throw new RuntimeException(
                'X accepted the code but handed back no refresh token. Make sure '
                .'`offline.access` is among the scopes the app requests.'
            );
        }

        $this->storeTokens($token);

        $this->forgetCachedResponses();
    }

    public function isConnected(): bool
    {
        return $this->cache->has(self::CACHE_KEY_ACCESS)
            || $this->cache->has(self::CACHE_KEY_REFRESH);
    }

    public function disconnect(): void
    {
        $this->cache->forget(self::CACHE_KEY_ACCESS);
        $this->cache->forget(self::CACHE_KEY_REFRESH);

        $this->forgetCachedResponses();
    }

    public function summarize(): ?string
    {
        $followers = $this->followers();

        return $followers === null ? null : number_format($followers).' followers';
    }

    /**
     * The stored access token, minted from the refresh token when it has run
     * out. Null when nobody has connected the account yet.
     */
    public function getAccessToken(): ?string
    {
        if ($accessToken = $this->cache->get(self::CACHE_KEY_ACCESS)) {
            return $accessToken;
        }

        $refreshToken = $this->cache->get(self::CACHE_KEY_REFRESH);

        if ($refreshToken === null) {
            return null;
        }

        $token = $this->requestTokens([
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if (empty($token['access_token'])) {
            return null;
        }

        $this->storeTokens($token);

        return $token['access_token'];
    }

    /**
     * The refresh token outlives the app; the access token is kept only for as
     * long as X says it is valid, minus a minute of slack. X rotates the refresh
     * token on every exchange, so the new one replaces the one just spent.
     */
    public function storeTokens(array $token): void
    {
        $expiresIn = (int) ($token['expires_in'] ?? 7200) - 60;

        if ($expiresIn > 60) {
            $this->cache->put(self::CACHE_KEY_ACCESS, $token['access_token'], $expiresIn);
        }

        if (! empty($token['refresh_token'])) {
            $this->cache->forever(self::CACHE_KEY_REFRESH, $token['refresh_token']);
        }
    }

    public function get(string $endpoint, array $query = []): ?array
    {
        if (! $this->getAccessToken()) {
            return null;
        }

        $response = $this->client()->get($this->base_url.'/'.ltrim($endpoint, '/'), $query);

        // Everything downstream of here turns a refusal into a missing widget,
        // and a blank card cannot say whether the token was rejected, the tier
        // refused the endpoint, or the window ran out. This is the only place
        // that still knows which.
        if (! $response->failed()) {
            return $response->json('data');
        }

        Log::warning('X refused a read.', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'body' => Str::limit($response->body(), 500),
        ]);

        // A token rejected mid-life is worth dropping: the next call mints a
        // fresh one off the refresh token instead of repeating the 401.
        if ($response->status() === 401) {
            $this->cache->forget(self::CACHE_KEY_ACCESS);
        }

        return null;
    }

    /**
     * Follower count for the connected account, or null when X will not say.
     * Read through `users/me` rather than a username lookup: the lookup wants a
     * paid tier, while the authenticated user reads on the free one.
     */
    public function followers(): ?int
    {
        return $this->remember(self::CACHE_KEY_FOLLOWERS, 6 * 3600, function () {
            $user = $this->get('users/me', ['user.fields' => 'public_metrics']);

            return $user['public_metrics']['followers_count'] ?? null;
        });
    }

    /** Both grants X supports here answer with the same token payload. */
    private function requestTokens(array $grant): array
    {
        $response = Http::asForm()
            ->withBasicAuth($this->client_id, $this->client_secret)
            ->post($this->token_url, ['client_id' => $this->client_id] + $grant)
            ->json();

        return is_array($response) ? $response : [];
    }

    /** X spreads its refusals over three different keys depending on the failure. */
    private function reason(array $token): string
    {
        return $token['error_description']
            ?? $token['detail']
            ?? $token['error']
            ?? 'no token returned';
    }

    private function challengeFor(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    private function forgetCachedResponses(): void
    {
        $this->cache->forget(self::CACHE_KEY_FOLLOWERS);
    }
}
