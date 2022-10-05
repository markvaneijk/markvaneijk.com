<?php

namespace App\Http\Controllers\Socials;

use App\Domain\Socials\Clients\Strava;
use Illuminate\Http\Request;

class StravaController
{
    protected $client;

    public function __construct()
    {
        $cache = cache()->driver('file');

        $this->client = new Strava($cache);
    }

    public function authorize()
    {
        return $this->client->authorize();
    }

    public function callback(Request $request)
    {
        $token = $this->client->getAccessToken($request->code);

        $this->client->setAccessToken($token['access_token'], $token['expires_at']);
        $this->client->setRefreshToken($token['refresh_token']);

        cache()->driver('file')->forever('strava.access_token', $token['access_token']);
        cache()->driver('file')->forever('strava.expires_at', $token['access_token']);
        cache()->driver('file')->forever('strava.refresh_token', $token['refresh_token']);
    }
}
