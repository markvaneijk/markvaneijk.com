<?php

namespace App\Http\Controllers\Socials;

use App\Domain\Socials\Clients\LastFm;
use Illuminate\Http\Request;

class LastFmController
{
    protected $client;

    public function __construct()
    {
        $cache = cache()->driver('file');

        $this->client = new LastFm($cache);
    }

    public function authorize()
    {
        return $this->client->authorize();
    }

    public function callback(Request $request)
    {
        dd($this->client->nowPlaying());

        $this->client->setToken($request->token);
    }
}
