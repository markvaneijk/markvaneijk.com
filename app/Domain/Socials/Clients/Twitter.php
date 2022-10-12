<?php

namespace App\Domain\Socials\Clients;

use Illuminate\Support\Facades\Http;

class Twitter
{
    protected $client;

    protected string $api_key;

    protected string $api_secret;

    protected string $access_token;

    protected string $base_url;

    public function __construct()
    {
        $this->api_key = config('services.twitter.api_key');
        $this->api_secret = config('services.twitter.api_secret');
        $this->bearer_token = config('services.twitter.bearer_token');

        $this->base_url = 'https://api.twitter.com/2/';

        return $this;
    }

    public function client()
    {
        $this->client = Http::withToken($this->getBearerToken());

        return $this;
    }

    public function getBearerToken()
    {
        return $this->bearer_token;
    }

    public function get($endpoint, $data)
    {
        return $this->client->get($this->base_url.ltrim($endpoint, '/'), $data)
            ->json()['data'];
    }

    public function me()
    {
        return $this->client()->get('/users/by/username/markvaneijk', [
            'user.fields' => 'public_metrics',
        ]);
    }
}
