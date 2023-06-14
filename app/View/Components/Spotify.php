<?php

namespace App\View\Components;

use Illuminate\View\Component;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class Spotify extends Component
{
    protected $session;

    protected $client;

    public function __construct()
    {
        $this->session = new Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            route('socials.spotify.callback_url'),
        );

        $accessToken = cache()->driver('file')->get('spotify.access_token');
        $refreshToken = cache()->driver('file')->get('spotify.refresh_token');

        if ($accessToken) {
            $this->session->setAccessToken($accessToken);
            $this->session->setRefreshToken($refreshToken);
        } else {
            $this->session->refreshAccessToken($refreshToken);
        }

        $options = [
            'auto_refresh' => true,
        ];

        $this->client = new SpotifyWebAPI($options, $this->session);

        $this->client->setSession($this->session);
    }

    public function render()
    {
        $nowPlaying = $this->client->getMyCurrentTrack();

        if (! $nowPlaying) {
            return;
        }

        return view('components.spotify', compact('nowPlaying'));
    }
}
