<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use Illuminate\Contracts\Cache\Repository;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class Spotify
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

    protected Repository $cache;

    protected ?Session $session = null;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function newSession(): Session
    {
        return new Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            route('socials.spotify.callback_url'),
        );
    }

    public function authorizeUrl(): string
    {
        return $this->newSession()->getAuthorizeUrl(['scope' => self::SCOPES]);
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
     * Null until someone has been through the OAuth flow at
     * /socials/spotify/authorize.
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
     * Most played tracks of roughly the last four weeks — that is what Spotify
     * means by the `short_term` window. Needs a token with `user-top-read`, so
     * this stays null on tokens issued before that scope was asked for.
     */
    public function topTracks(int $limit = 3): ?array
    {
        return $this->remember("spotify.top_tracks.{$limit}", 3600, function () use ($limit) {
            $top = $this->call(fn (SpotifyWebAPI $api) => $api->getMyTop('tracks', [
                'time_range' => 'short_term',
                'limit' => $limit,
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
