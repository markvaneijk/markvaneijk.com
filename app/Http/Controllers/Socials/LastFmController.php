<?php

namespace App\Http\Controllers\Socials;

use App\Domain\Socials\Clients\LastFm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LastFmController
{
    protected LastFm $client;

    public function __construct()
    {
        $this->client = new LastFm(cache()->driver('file'));
    }

    public function authorize(): RedirectResponse
    {
        return $this->client->authorize();
    }

    public function callback(Request $request): RedirectResponse
    {
        $token = $request->query('token');
        
        if ($token) {
            $this->client->setToken($token, 0);
        }

        return redirect()->route('now');
    }

    public function activities(): JsonResponse
    {
        $track = $this->client->nowPlaying();

        return response()->json($track ?? ['message' => 'No recent track']);
    }
}
