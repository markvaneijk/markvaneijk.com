<?php

namespace App\View\Components;

use Illuminate\View\Component;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class Spotify extends Component
{
    protected ?Session $session = null;

    protected ?SpotifyWebAPI $client = null;

    public function __construct()
    {
        $cache = cache()->driver('file');
        $accessToken = $cache->get('spotify.access_token');
        $refreshToken = $cache->get('spotify.refresh_token');

        if (! $refreshToken && ! $accessToken) {
            return;
        }

        $this->session = new Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            route('socials.spotify.callback_url'),
        );

        if ($accessToken) {
            $this->session->setAccessToken($accessToken);
        }
        if ($refreshToken) {
            $this->session->setRefreshToken($refreshToken);
        }
        if (! $accessToken && $refreshToken) {
            $this->session->refreshAccessToken($refreshToken);
            $cache->forever('spotify.access_token', $this->session->getAccessToken());
            $newRefresh = $this->session->getRefreshToken();
            if ($newRefresh) {
                $cache->forever('spotify.refresh_token', $newRefresh);
            }
        }

        $this->client = new SpotifyWebAPI(['auto_refresh' => true], $this->session);
        $this->client->setSession($this->session);
    }

    public function render(): string|\Illuminate\Contracts\View\View
    {
        if (! $this->client) {
            return '';
        }

        try {
            $nowPlaying = $this->client->getMyCurrentTrack();
        } catch (\Throwable) {
            return '';
        }

        if (! $nowPlaying || ! isset($nowPlaying->item)) {
            return '';
        }

        if ($this->session) {
            $cache = cache()->driver('file');
            $cache->forever('spotify.access_token', $this->session->getAccessToken());
            $newRefresh = $this->session->getRefreshToken();
            if ($newRefresh) {
                $cache->forever('spotify.refresh_token', $newRefresh);
            }
        }

        return view('components.spotify', compact('nowPlaying'));
    }
}
