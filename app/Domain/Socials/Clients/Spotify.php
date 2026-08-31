<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use App\Domain\Socials\ConnectsThroughOAuth;
use Illuminate\Contracts\Cache\Repository;
use RuntimeException;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class Spotify implements ConnectsThroughOAuth
{
    use CachesResponses;

    /** What the /now widgets need: the current track and the personal top chart. */
    public const SCOPES = [
        'user-read-currently-playing',
        'user-read-playback-state',
        'user-top-read',
    ];

    private const CACHE_KEY_ACCESS = 'spotify.access_token';

    private const CACHE_KEY_REFRESH = 'spotify.refresh_token';

    /**
     * The windows a top chart can cover, in Spotify's own words: `short_term`
     * is roughly the last four weeks, `long_term` the whole listening history.
     */
    public const TOP_TRACK_RANGES = ['short_term', 'long_term'];

    /** The longest chart the top endpoint hands over in one call. */
    private const CHART_LENGTH = 50;

    protected Repository $cache;

    protected ?Session $session = null;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function newSession(): Session
    {
        return new Session(
            (string) config('services.spotify.client_id'),
            (string) config('services.spotify.client_secret'),
            $this->redirectUri(),
        );
    }

    public function isConfigured(): bool
    {
        return (string) config('services.spotify.client_id') !== ''
            && (string) config('services.spotify.client_secret') !== '';
    }

    public function redirectUri(): string
    {
        return (string) config('services.spotify.redirect_uri')
            ?: route('socials.callback', 'spotify');
    }

    public function authorizeUrl(string $state = ''): string
    {
        return $this->newSession()->getAuthorizeUrl([
            'scope' => self::SCOPES,
            'state' => $state,
        ]);
    }

    public function connect(string $code): void
    {
        $session = $this->newSession();

        try {
            $granted = $session->requestAccessToken($code);
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                'Spotify refused the authorization code: '.$exception->getMessage(),
                previous: $exception
            );
        }

        if (! $granted || ! $session->getRefreshToken()) {
            throw new RuntimeException('Spotify accepted the code but handed back no refresh token.');
        }

        $this->storeTokens($session);
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

    /**
     * Spotify answers with an empty body while playback is stopped, so a silent
     * account is not a broken token — fall back to the top chart, which also
     * proves the `user-top-read` scope came through.
     */
    public function summarize(): ?string
    {
        if ($track = $this->nowPlaying()) {
            return ($track['playing'] ? 'playing ' : 'last played ')."{$track['name']} — {$track['artist']}";
        }

        if ($top = $this->topTracks()) {
            return "nothing playing; top track is {$top[0]['name']} — {$top[0]['artist']}";
        }

        return null;
    }

    /** The answers the /now widgets ask for; stale once the tokens change. */
    private function forgetCachedResponses(): void
    {
        $this->cache->forget('spotify.now_playing');

        foreach (self::TOP_TRACK_RANGES as $range) {
            $this->cache->forget("spotify.top_tracks.{$range}");
        }
    }

    /**
     * The refresh token outlives the app; the access token is kept only for as
     * long as Spotify says it is valid, minus a minute of slack.
     */
    public function storeTokens(Session $session): void
    {
        if ($refreshToken = $session->getRefreshToken()) {
            $this->cache->forever(self::CACHE_KEY_REFRESH, $refreshToken);
        }

        $expiresIn = $session->getTokenExpiration() - time() - 60;

        if ($session->getAccessToken() && $expiresIn > 60) {
            $this->cache->put(self::CACHE_KEY_ACCESS, $session->getAccessToken(), $expiresIn);
        }
    }

    /**
     * Null until `php artisan socials:connect spotify` has stored a token.
     */
    public function api(): ?SpotifyWebAPI
    {
        $accessToken = $this->cache->get(self::CACHE_KEY_ACCESS);
        $refreshToken = $this->cache->get(self::CACHE_KEY_REFRESH);

        if (! $accessToken && ! $refreshToken) {
            return null;
        }

        $this->session = $this->newSession();

        if ($accessToken) {
            $this->session->setAccessToken($accessToken);
        }

        if ($refreshToken) {
            $this->session->setRefreshToken($refreshToken);
        }

        if (! $accessToken) {
            $this->session->refreshAccessToken($refreshToken);
            $this->storeTokens($this->session);
        }

        return new SpotifyWebAPI(['auto_refresh' => true], $this->session);
    }

    /**
     * Runs one call against the API, or returns null when nobody has authorized
     * the app yet.
     */
    private function call(callable $callback): mixed
    {
        $api = $this->api();

        if (! $api) {
            return null;
        }

        try {
            $result = $callback($api);
        } catch (\Throwable $exception) {
            // The cached access token may have been minted for an older set of
            // scopes, or expired while cached. Drop it so the next request
            // mints a fresh one off the refresh token.
            $this->cache->forget(self::CACHE_KEY_ACCESS);

            throw $exception;
        }

        $this->storeTokens($this->session);

        return $result;
    }

    /**
     * The track playing right now, or null when nothing is — Spotify answers
     * with an empty body while playback is stopped.
     */
    public function nowPlaying(): ?array
    {
        return $this->remember('spotify.now_playing', 30, function () {
            $current = $this->call(fn (SpotifyWebAPI $api) => $api->getMyCurrentTrack());

            $item = $current->item ?? null;

            if (! $item || ($item->type ?? null) !== 'track') {
                return null;
            }

            return [
                'name' => $item->name,
                'artist' => collect($item->artists)->pluck('name')->implode(', '),
                'album' => $item->album->name ?? null,
                'image' => $item->album->images[0]->url ?? null,
                'url' => $item->external_urls->spotify ?? 'https://open.spotify.com',
                'playing' => (bool) ($current->is_playing ?? false),
                'played_at' => null,
            ];
        }, failureSeconds: 60);
    }

    /**
     * The most played tracks over one of the windows in `TOP_TRACK_RANGES`, in
     * descending order. Needs a token with `user-top-read`, so this stays null
     * on tokens issued before that scope was asked for.
     */
    public function topTracks(string $range = 'short_term'): ?array
    {
        return $this->remember("spotify.top_tracks.{$range}", 3600, function () use ($range) {
            $top = $this->call(fn (SpotifyWebAPI $api) => $api->getMyTop('tracks', [
                'time_range' => $range,
                'limit' => self::CHART_LENGTH,
            ]));

            $tracks = collect($top->items ?? [])->map(fn ($item) => [
                'name' => $item->name,
                'artist' => collect($item->artists)->pluck('name')->implode(', '),
                'url' => $item->external_urls->spotify ?? 'https://open.spotify.com',
                'plays' => null,
            ])->all();

            return $tracks ?: null;
        });
    }
}
