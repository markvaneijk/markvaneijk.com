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
        if (empty($request->code)) {
            return redirect()->route('now')->with('error', 'Strava authorization failed: no code returned.');
        }

        $token = $this->client->getAccessToken($request->code);
        if (empty($token['access_token'])) {
            return redirect()->route('now')->with('error', 'Strava token exchange failed.');
        }

        $this->client->setAccessToken($token['access_token'], $token['expires_in']);
        $this->client->setRefreshToken($token['refresh_token']);

        return redirect()->route('now');
    }
}
