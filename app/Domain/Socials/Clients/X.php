<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use Illuminate\Support\Facades\Http;

class X
{
    use CachesResponses;

    protected string $bearerToken;

    protected string $username;

    protected string $baseUrl;

    public function __construct()
    {
        $this->bearerToken = (string) config('services.x.bearer_token');
        $this->username = (string) config('services.x.username');

        $this->baseUrl = 'https://api.x.com/2/';
    }

    public function client()
    {
        return Http::withToken($this->bearerToken);
    }

    public function get(string $endpoint, array $query = []): ?array
    {
        $response = $this->client()->get($this->baseUrl.ltrim($endpoint, '/'), $query);

        return $response->successful() ? $response->json('data') : null;
    }

    /**
     * Follower count for the configured account, or null when X will not say —
     * app-only reads need a developer app attached to a project, and free-tier
     * apps get a 403 here.
     */
    public function followers(): ?int
    {
        if ($this->bearerToken === '' || $this->username === '') {
            return null;
        }

        return $this->remember('x.followers', 6 * 3600, function () {
            $user = $this->get("users/by/username/{$this->username}", [
                'user.fields' => 'public_metrics',
            ]);

            return $user['public_metrics']['followers_count'] ?? null;
        });
    }
}
