<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use App\Domain\Socials\ConnectsThroughOAuth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Strava implements ConnectsThroughOAuth
{
    use CachesResponses;

    /** Everything the /now distance widget reads. */
    public const SCOPES = 'read_all,profile:read_all,activity:read_all';

    private const CACHE_KEY_ACCESS = 'strava.access_token';

    private const CACHE_KEY_REFRESH = 'strava.refresh_token';

    /** The answer the /now widget asks for; stale once the tokens change. */
    private const RESPONSE_KEYS = [
        'strava.distance.30d',
    ];

    protected string $client_id;

    protected string $client_secret;

    protected string $base_url;

    protected string $token_url;

    protected $cache;

    public function __construct($cache)
    {
        $this->client_id = (string) config('services.strava.client_id');
        $this->client_secret = (string) config('services.strava.client_secret');
        $this->cache = $cache;
        $this->base_url = 'https://www.strava.com/api/v3';
        $this->token_url = 'https://www.strava.com/oauth/token';
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
        return (string) config('services.strava.redirect_uri')
            ?: route('socials.callback', 'strava');
    }

    public function authorizeUrl(string $state = ''): string
    {
        return 'https://www.strava.com/oauth/authorize?'.http_build_query([
            'client_id' => $this->client_id,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri(),
            'approval_prompt' => 'force',
            'scope' => self::SCOPES,
            'state' => $state,
        ]);
    }

    public function connect(string $code): void
    {
        $token = $this->requestTokens([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        if (empty($token['access_token']) || empty($token['refresh_token'])) {
            throw new RuntimeException(
                'Strava refused the authorization code: '.($token['message'] ?? 'no token returned').'.'
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
        $distance = $this->distanceInKilometers(30);

        return $distance === null ? null : "{$distance} km over the last 30 days";
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
     * long as Strava says it is valid. Strava rotates both on every exchange.
     */
    public function storeTokens(array $token): void
    {
        $this->cache->put(
            self::CACHE_KEY_ACCESS,
            $token['access_token'],
            (int) ($token['expires_in'] ?? 21600)
        );

        if (! empty($token['refresh_token'])) {
            $this->cache->forever(self::CACHE_KEY_REFRESH, $token['refresh_token']);
        }
    }

    /**
     * Null rather than an empty collection when Strava is unreachable, so a
     * failed call is never mistaken for "no activities".
     */
    public function get(string $path, array $query = []): ?Collection
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return null;
        }

        $response = $this->client()->get(
            $this->base_url.'/'.ltrim($path, '/'),
            $query
        );

        return $response->successful() ? collect($response->json()) : null;
    }

    public function activities(?string $before = null, ?string $after = null): ?Collection
    {
        return $this->get('/athlete/activities',
            array_filter(
                compact(
                    'before', 'after'
                ) + ['per_page' => 200]
            )
        );
    }

    /**
     * Kilometres covered over the last $days days, across every activity type.
     */
    public function distanceInKilometers(int $days = 30): ?float
    {
        return $this->remember("strava.distance.{$days}d", 1800, function () use ($days) {
            $activities = $this->activities(after: (string) now()->subDays($days)->timestamp);

            return $activities === null
                ? null
                : round($activities->sum('distance') / 1000, 1);
        });
    }

    /** Both grants Strava supports here answer with the same token payload. */
    private function requestTokens(array $grant): array
    {
        $response = Http::asForm()->post($this->token_url, [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ] + $grant)->json();

        return is_array($response) ? $response : [];
    }

    private function forgetCachedResponses(): void
    {
        foreach (self::RESPONSE_KEYS as $key) {
            $this->cache->forget($key);
        }
    }
}
