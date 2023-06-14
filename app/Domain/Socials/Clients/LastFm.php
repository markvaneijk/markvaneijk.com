<?php

namespace App\Domain\Socials\Clients;

use Illuminate\Support\Facades\Http;

class LastFm
{
    protected $client;

    protected string $username;

    protected string $api_key;

    protected string $api_secret;

    protected string $access_token;

    protected string $base_url;

    protected string $callback_url;

    protected $cache;

    public function __construct($cache)
    {
        $this->username = config('services.lastfm.username');
        $this->api_key = config('services.lastfm.api_key');
        $this->api_secret = config('services.lastfm.api_secret');
        $this->callback_url = route('socials.lastfm.callback_url');
        $this->cache = $cache;

        $this->base_url = 'https://www.last.fm/api';

        return $this;
    }

    public function client()
    {
        return Http::withToken($this->access_token);
    }

    public function authorize()
    {
        return redirect($this->base_url.'/auth/?api_key='.$this->api_key);
    }

    public function setToken(string $accessToken, int $expiresAt)
    {
        return $this->cache->put('token', [
            'token' => $accessToken,
            'expires_at' => $expiresAt,
        ]);
    }

    public function get(string $path, array $query = [])
    {
        return collect($this->client()->get(
            $this->base_url.'/'.ltrim($path, '/'),
            $query
        )->json());
    }

    public function nowPlaying()
    {
        $scrobbled = Http::get("https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&limit=1&user={$this->username}&api_key={$this->api_key}&format=json")->json();

        if (isset($scrobbled['recenttracks']['track'][0])) {
            $track = $scrobbled['recenttracks']['track'][0];

            return [
                'name' => $track['name'],
                'artist' => $track['artist']['#text'],
                'album' => $track['album']['#text'],
                'image' => end($track['image'])['#text'],
                'date' => now()->createFromTimestamp($track['date']['uts']),
            ];
        }
    }
}
