<?php

namespace App\Http\Controllers\Socials;

use Illuminate\Http\Request;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController
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

        $this->client = new SpotifyWebAPI();
    }

    public function authorize()
    {
        $options = [
            'scope' => [
                'user-read-playback-state',
            ],
        ];

        return redirect($this->session->getAuthorizeUrl($options));
    }

    public function callback(Request $request)
    {
        if (isset($request->code)) {
            $this->session->requestAccessToken($request->code);

            $accessToken = $this->session->getAccessToken();
            $refreshToken = $this->session->getRefreshToken();

            cache()->driver('file')->forever('spotify.access_token', $accessToken);
            cache()->driver('file')->forever('spotify.refresh_token', $refreshToken);
        }
    }
}
