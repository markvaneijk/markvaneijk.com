<?php

namespace App\Http\Controllers\Socials;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController
{
    protected Session $session;

    protected SpotifyWebAPI $client;

    public function __construct()
    {
        $this->session = new Session(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            route('socials.spotify.callback_url'),
        );

        $this->client = new SpotifyWebAPI;
    }

    public function authorize(): RedirectResponse
    {
        $options = [
            'scope' => [
                'user-read-playback-state',
            ],
        ];

        return redirect($this->session->getAuthorizeUrl($options));
    }

    public function callback(Request $request): RedirectResponse
    {
        if (isset($request->code)) {
            $this->session->requestAccessToken($request->code);

            $accessToken = $this->session->getAccessToken();
            $refreshToken = $this->session->getRefreshToken();

            cache()->driver('file')->forever('spotify.access_token', $accessToken);
            cache()->driver('file')->forever('spotify.refresh_token', $refreshToken);
        }

        return redirect()->route('now');
    }

    public function nowPlaying(): JsonResponse
    {
        $api = $this->spotifyApiFromCache();
        if (! $api) {
            return response()->json(null, 204);
        }

        try {
            $track = $api->getMyCurrentTrack();
        } catch (\Throwable) {
            return response()->json(null, 204);
        }

        if (! $track || ! isset($track->item)) {
            return response()->json(null, 204);
        }

        return response()->json($track);
    }

    protected function spotifyApiFromCache(): ?SpotifyWebAPI
    {
        $cache = cache()->driver('file');
        $accessToken = $cache->get('spotify.access_token');
        $refreshToken = $cache->get('spotify.refresh_token');

        if (! $refreshToken && ! $accessToken) {
            return null;
        }

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

        $this->client->setAccessToken($this->session->getAccessToken());

        return $this->client;
    }
}
