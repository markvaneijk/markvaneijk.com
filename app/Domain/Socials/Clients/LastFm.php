<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use Illuminate\Support\Facades\Http;

class LastFm
{
    use CachesResponses;

    /** Read-only endpoint: an API key is enough, no user token needed. */
    private const API_URL = 'https://ws.audioscrobbler.com/2.0/';

    protected string $username;

    protected string $api_key;

    protected $cache;

    public function __construct($cache)
    {
        $this->username = (string) config('services.lastfm.username');
        $this->api_key = (string) config('services.lastfm.api_key');
        $this->cache = $cache;
    }

    /**
     * The scrobble in progress, or the last one that finished. Same shape as
     * the Spotify client returns, so /now can use either.
     */
    public function nowPlaying(): ?array
    {
        return $this->remember('lastfm.now_playing', 60, function () {
            $response = Http::get(self::API_URL, [
                'method' => 'user.getrecenttracks',
                'user' => $this->username,
                'api_key' => $this->api_key,
                'limit' => 1,
                'format' => 'json',
            ])->json();

            if (isset($response['error']) || ! isset($response['recenttracks']['track'][0])) {
                return null;
            }

            $track = $response['recenttracks']['track'][0];
            $isNowPlaying = ($track['@attr']['nowplaying'] ?? null) === 'true';

            $images = $track['image'] ?? [];
            $imageUrl = ! empty($images) ? end($images)['#text'] : null;

            return [
                'name' => $track['name'] ?? '',
                'artist' => $track['artist']['#text'] ?? $track['artist']['name'] ?? '',
                'album' => $track['album']['#text'] ?? $track['album']['name'] ?? null,
                'image' => $imageUrl ?: null,
                'url' => $track['url'] ?? $this->profileUrl(),
                'playing' => $isNowPlaying,
                'played_at' => $isNowPlaying || empty($track['date']['uts'])
                    ? null
                    : now()->createFromTimestamp((int) $track['date']['uts']),
            ];
        }, failureSeconds: 60);
    }

    /**
     * Most played tracks of the last month. Stands in for Spotify's own top
     * chart, which needs a token carrying the `user-top-read` scope.
     */
    public function topTracks(int $limit = 3): ?array
    {
        return $this->remember("lastfm.top_tracks.{$limit}", 3600, function () use ($limit) {
            $response = Http::get(self::API_URL, [
                'method' => 'user.gettoptracks',
                'user' => $this->username,
                'period' => '1month',
                'limit' => $limit,
                'api_key' => $this->api_key,
                'format' => 'json',
            ])->json();

            $tracks = collect($response['toptracks']['track'] ?? [])->map(fn ($track) => [
                'name' => $track['name'] ?? '',
                'artist' => $track['artist']['name'] ?? $track['artist']['#text'] ?? '',
                'url' => $track['url'] ?? $this->profileUrl(),
                'plays' => (int) ($track['playcount'] ?? 0),
            ])->all();

            return $tracks ?: null;
        });
    }

    public function profileUrl(): string
    {
        return 'https://www.last.fm/user/'.$this->username;
    }
}
