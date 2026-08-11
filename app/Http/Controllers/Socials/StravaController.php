<?php

namespace App\Http\Controllers\Socials;

use App\Domain\Socials\Clients\Strava;
use App\Domain\Socials\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StravaController
{
    protected $client;

    public function __construct()
    {
        $cache = Store::make();

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

    /** Same number the /now widget shows — handy to check the token from a browser. */
    public function activities(): JsonResponse
    {
        $distance = $this->client->distanceInKilometers(30);

        return response()->json(
            ['distance_km_last_30_days' => $distance],
            $distance === null ? 503 : 200
        );
    }
}
