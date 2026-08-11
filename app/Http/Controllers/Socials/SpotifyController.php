<?php

namespace App\Http\Controllers\Socials;

use App\Domain\Socials\Clients\Spotify;
use App\Domain\Socials\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SpotifyController
{
    protected Spotify $client;

    public function __construct()
    {
        $this->client = new Spotify(Store::make());
    }

    public function authorize(): RedirectResponse
    {
        return redirect($this->client->authorizeUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('code')) {
            $session = $this->client->newSession();
            $session->requestAccessToken($request->query('code'));

            $this->client->storeTokens($session);
        }

        return redirect()->route('now');
    }

    public function nowPlaying(): JsonResponse
    {
        $track = $this->client->nowPlaying();

        return response()->json($track, $track ? 200 : 204);
    }
}
