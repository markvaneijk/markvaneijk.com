<?php

namespace App\Domain\Socials\Clients;

use Illuminate\Support\Facades\Http;

class X
{
    protected $client;

    protected string $apiKey;

    protected string $apiSecret;

    protected string $bearerToken;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.x.api_key');
        $this->apiSecret = config('services.x.api_secret');
        $this->bearerToken = config('services.x.bearer_token');

        $this->baseUrl = 'https://api.x.com/2/';

        return $this;
    }

    public function client()
    {
        $this->client = Http::withToken($this->getBearerToken());

        return $this;
    }

    public function getBearerToken()
    {
        return $this->bearerToken;
    }

    public function get($endpoint, $data)
    {
        return $this->client->get($this->baseUrl.ltrim($endpoint, '/'), $data)
            ->json()['data'] ?? null;
    }

    public function me()
    {
        return $this->client()->get('users/me', [
            'user.fields' => 'public_metrics',
        ]);
    }
}
