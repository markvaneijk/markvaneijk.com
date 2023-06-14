<?php

namespace App\Domain\Socials\Clients;

use Illuminate\Support\Facades\Http;

class Strava
{
    protected $client;

    protected int $client_id;

    protected string $client_secret;

    protected string $access_token;

    protected string $base_url;

    protected string $callback_url;

    protected $cache;

    public function __construct($cache)
    {
        $this->client_id = config('services.strava.client_id');
        $this->client_secret = config('services.strava.client_secret');
        $this->callback_url = route('socials.strava.callback_url');
        $this->cache = $cache;

        $this->base_url = 'https://www.strava.com/api/v3';

        return $this;
    }

    public function client()
    {
        return Http::withToken($this->getAccessToken());
    }

    public function authorize($scope = 'read_all,profile:read_all,activity:read_all')
    {
        return redirect('https://www.strava.com/oauth/authorize?client_id='.$this->client_id.'&response_type=code&redirect_uri='.$this->callback_url.'&scope='.$scope.'&state=strava');
    }

    public function getAccessToken($code = null)
    {
        if (
            is_null($code) &&
            $this->cache->has('access_token')
        ) {
            return $this->cache->get('access_token');
        }

        if (! is_null($code)) {
            return Http::post($this->base_url.'/oauth/token', [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ])->json();
        }

        if (null !== $refreshToken = $this->cache->get('refresh_token')) {
            $token = $this->getRefreshToken($refreshToken);

            $this->setAccessToken($token['access_token'], $token['expires_in']);
            $this->setRefreshToken($token['refresh_token']);

            return $token['access_token'];
        }
    }

    public function setAccessToken(string $accessToken, int $expiresAt)
    {
        return $this->cache->put('access_token', $accessToken, time() - $expiresAt);
    }

    public function getRefreshToken(string $refreshToken)
    {
        return Http::post($this->base_url.'/oauth/token', [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ])->json();
    }

    public function setRefreshToken(string $refreshToken)
    {
        return $this->cache->forever('refresh_token', $refreshToken);
    }

    public function get(string $path, array $query = [])
    {
        return collect($this->client()->get(
            $this->base_url.'/'.ltrim($path, '/'),
            $query
        )->json());
    }

    public function activities(string $before = null, string $after = null)
    {
        return $this->get('/athlete/activities',
            array_filter(
                compact(
                    'before', 'after'
                )
            )
        );
    }

    public function distanceRunBetween(string $before = null, string $after = null)
    {
        return $this->activities($before, $after)->sum('distance');
    }
}
