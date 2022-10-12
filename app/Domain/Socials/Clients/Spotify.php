<?php

namespace App\Domain\Socials\Clients;

use Illuminate\Support\Facades\Http;

class LastFm
{
    protected string $username;

    protected string $api_key;

    public function __construct()
    {
        $this->username = config('services.lastfm.username');
        $this->api_key = config('services.lastfm.api_key');

        return $this;
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
