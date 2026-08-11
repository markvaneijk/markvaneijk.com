<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Strava
{
    use CachesResponses;

    protected $client;

    protected int $client_id;

    protected string $client_secret;

    protected string $access_token;

    protected string $base_url;

    protected string $token_url;

    protected string $callback_url;

    protected $cache;

    private const CACHE_KEY_ACCESS = 'strava.access_token';

    private const CACHE_KEY_REFRESH = 'strava.refresh_token';

    public function __construct($cache)
    {
        $this->client_id = config('services.strava.client_id');
        $this->client_secret = config('services.strava.client_secret');
        $this->callback_url = route('socials.strava.callback_url');
        $this->cache = $cache;
        $this->base_url = 'https://www.strava.com/api/v3';
        $this->token_url = 'https://www.strava.com/oauth/token';

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
            $this->cache->has(self::CACHE_KEY_ACCESS)
        ) {
            return $this->cache->get(self::CACHE_KEY_ACCESS);
        }

        if (! is_null($code)) {
            $response = Http::asForm()->post($this->token_url, [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]);

            return $response->json();
        }

        $refreshToken = $this->cache->get(self::CACHE_KEY_REFRESH);
        if ($refreshToken !== null) {
            $token = $this->getRefreshToken($refreshToken);
            if (empty($token['access_token'])) {
                return null;
            }
            $this->setAccessToken($token['access_token'], $token['expires_in']);
            $this->setRefreshToken($token['refresh_token']);

            return $token['access_token'];
        }

        return null;
    }

    public function setAccessToken(string $accessToken, int $expiresInSeconds)
    {
        return $this->cache->put(self::CACHE_KEY_ACCESS, $accessToken, $expiresInSeconds);
    }

    public function getRefreshToken(string $refreshToken)
    {
        return Http::asForm()->post($this->token_url, [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ])->json();
    }

    public function setRefreshToken(string $refreshToken)
    {
        return $this->cache->forever(self::CACHE_KEY_REFRESH, $refreshToken);
    }

    /**
     * Null rather than an empty collection when Strava is unreachable, so a
     * failed call is never mistaken for "no activities".
     */
    public function get(string $path, array $query = []): ?Collection
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return null;
        }

        $response = $this->client()->get(
            $this->base_url.'/'.ltrim($path, '/'),
            $query
        );

        return $response->successful() ? collect($response->json()) : null;
    }

    public function activities(?string $before = null, ?string $after = null): ?Collection
    {
        return $this->get('/athlete/activities',
            array_filter(
                compact(
                    'before', 'after'
                ) + ['per_page' => 200]
            )
        );
    }

    /**
     * Kilometres covered over the last $days days, across every activity type.
     */
    public function distanceInKilometers(int $days = 30): ?float
    {
        return $this->remember("strava.distance.{$days}d", 1800, function () use ($days) {
            $activities = $this->activities(after: (string) now()->subDays($days)->timestamp);

            return $activities === null
                ? null
                : round($activities->sum('distance') / 1000, 1);
        });
    }
}
