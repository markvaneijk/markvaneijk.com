<?php

namespace Tests\Feature;

use App\Domain\Socials\Clients\GitHub;
use App\Domain\Socials\Store;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SocialsConnectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.strava.client_id' => '94512',
            'services.strava.client_secret' => 'strava-secret',
            'services.spotify.client_id' => 'spotify-id',
            'services.spotify.client_secret' => 'spotify-secret',
            'services.github.username' => 'markvaneijk',
            'services.github.client_id' => 'github-id',
            'services.github.client_secret' => 'github-secret',
            'services.github.token' => null,
        ]);
    }

    public function test_it_connects_github_from_a_pasted_code(): void
    {
        Http::fake([
            'github.com/login/oauth/access_token' => Http::response([
                'access_token' => 'gho-fresh',
                'scope' => 'read:org,read:user',
                'token_type' => 'bearer',
            ]),
            'api.github.com/graphql' => Http::response($this->contributionCalendar(1326)),
        ]);

        $this->artisan('socials:connect github --code=one-time-code')
            ->expectsOutputToContain('Github connected.')
            ->expectsOutputToContain('1,326 contributions over the last 30 days')
            ->assertSuccessful();

        $this->assertSame('gho-fresh', Store::make()->get('github.access_token'));

        Http::assertSent(fn ($request) => $request->url() !== 'https://api.github.com/graphql'
            || $request->hasHeader('Authorization', 'Bearer gho-fresh'));
    }

    /**
     * GitHub answers a stale code with a 200 and an error in the body, so a
     * client that trusts the status line would store the word "error".
     */
    public function test_it_stores_nothing_when_github_refuses_the_code(): void
    {
        Http::fake([
            'github.com/login/oauth/access_token' => Http::response([
                'error' => 'bad_verification_code',
                'error_description' => 'The code passed is incorrect or expired.',
            ]),
        ]);

        $this->artisan('socials:connect github --code=stale-code')
            ->expectsOutputToContain('The code passed is incorrect or expired.')
            ->assertFailed();

        $this->assertNull(Store::make()->get('github.access_token'));
    }

    public function test_a_connected_github_beats_the_token_in_the_environment(): void
    {
        config(['services.github.token' => 'env-personal-access-token']);
        Store::make()->forever('github.access_token', 'gho-connected');

        Http::fake(['api.github.com/graphql' => Http::response($this->contributionCalendar(7))]);

        (new GitHub(Store::make()))->contributions();

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer gho-connected'));
    }

    public function test_disconnecting_github_forgets_the_token_and_what_it_fetched(): void
    {
        $store = Store::make();
        $store->forever('github.access_token', 'gho-connected');
        $store->put('github.contributions.30d', ['total' => 1326, 'per_day' => []], 900);
        $store->put('github.repositories', [['name' => 'a', 'stars' => 1]], 900);

        $this->artisan('socials:disconnect github')
            ->expectsConfirmation('Forget the Github tokens?', 'yes')
            ->assertSuccessful();

        $this->assertNull($store->get('github.access_token'));
        $this->assertNull($store->get('github.contributions.30d'));
        $this->assertNull($store->get('github.repositories'));
    }

    public function test_it_connects_strava_from_a_pasted_code(): void
    {
        Http::fake([
            'www.strava.com/oauth/token' => Http::response([
                'access_token' => 'fresh-access',
                'refresh_token' => 'fresh-refresh',
                'expires_in' => 21600,
            ]),
            'www.strava.com/api/v3/athlete/activities*' => Http::response([
                ['distance' => 12000.0],
            ]),
        ]);

        $this->artisan('socials:connect strava --code=one-time-code')
            ->expectsOutputToContain('Strava connected.')
            ->expectsOutputToContain('12 km over the last 30 days')
            ->assertSuccessful();

        $this->assertSame('fresh-access', Store::make()->get('strava.access_token'));
        $this->assertSame('fresh-refresh', Store::make()->get('strava.refresh_token'));
    }

    public function test_it_says_so_when_the_new_tokens_read_nothing(): void
    {
        Http::fake([
            'www.strava.com/oauth/token' => Http::response([
                'access_token' => 'fresh-access',
                'refresh_token' => 'fresh-refresh',
                'expires_in' => 21600,
            ]),
            'www.strava.com/api/v3/*' => Http::response(status: 401),
        ]);

        $this->artisan('socials:connect strava --code=one-time-code')
            ->expectsOutputToContain('did not answer a read')
            ->assertSuccessful();
    }

    public function test_it_stores_nothing_when_the_code_is_refused(): void
    {
        Http::fake([
            'www.strava.com/oauth/token' => Http::response(['message' => 'Bad Request'], 400),
        ]);

        $this->artisan('socials:connect strava --code=stale-code')
            ->expectsOutputToContain('Bad Request')
            ->assertFailed();

        $this->assertNull(Store::make()->get('strava.access_token'));
        $this->assertNull(Store::make()->get('strava.refresh_token'));
    }

    public function test_it_refuses_an_account_it_cannot_connect(): void
    {
        $this->artisan('socials:connect lastfm --code=whatever')
            ->expectsOutputToContain('Unknown account [lastfm]')
            ->assertFailed();
    }

    public function test_it_refuses_to_start_without_credentials(): void
    {
        config(['services.spotify.client_secret' => null]);

        $this->artisan('socials:connect spotify --code=whatever')
            ->expectsOutputToContain('SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET are missing')
            ->assertFailed();
    }

    public function test_disconnecting_forgets_the_tokens_and_what_they_fetched(): void
    {
        $store = Store::make();
        $store->put('strava.access_token', 'access', 3600);
        $store->forever('strava.refresh_token', 'refresh');
        $store->put('strava.distance.30d', 12.0, 1800);

        $this->artisan('socials:disconnect strava')
            ->expectsConfirmation('Forget the Strava tokens?', 'yes')
            ->assertSuccessful();

        $this->assertNull($store->get('strava.access_token'));
        $this->assertNull($store->get('strava.refresh_token'));
        $this->assertNull($store->get('strava.distance.30d'));
    }

    /**
     * The deploy clears the default store. Tokens are the one thing here that
     * cannot be fetched again without a browser, so they have to survive it.
     */
    public function test_a_deploys_cache_clear_leaves_the_tokens_connected(): void
    {
        $store = Store::make();
        $store->forever('strava.refresh_token', 'refresh');
        cache()->put('something.else', 'disposable', 3600);

        $this->artisan('cache:clear')->assertSuccessful();

        $this->assertSame('refresh', $store->get('strava.refresh_token'));
        $this->assertNull(cache()->get('something.else'));
    }

    public function test_status_warns_when_the_tokens_share_the_cleared_store(): void
    {
        config(['services.socials.store' => config('cache.default')]);

        Http::fake(['www.strava.com/*' => Http::response(status: 401)]);

        $this->artisan('socials:status')
            ->expectsOutputToContain('a deploy will disconnect these accounts')
            ->assertSuccessful();
    }

    public function test_status_says_what_is_connected(): void
    {
        Http::fake(['www.strava.com/*' => Http::response(status: 401)]);

        $this->artisan('socials:status')
            ->expectsOutputToContain('run socials:connect strava')
            ->assertSuccessful();
    }

    public function test_the_callback_page_only_hands_the_code_back(): void
    {
        $response = $this->get('/socials/spotify/callback?code=one-time-code');

        $response->assertOk();
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $response->assertSee('php artisan socials:connect spotify');
        $response->assertSee('one-time-code');

        $this->assertNull(Store::make()->get('spotify.access_token'));
        $this->assertNull(Store::make()->get('spotify.refresh_token'));
    }

    public function test_the_browser_can_no_longer_start_or_finish_the_flow(): void
    {
        $this->get('/socials/spotify/authorize')->assertNotFound();
        $this->get('/socials/strava/authorize')->assertNotFound();
        $this->get('/socials/lastfm/callback?token=abc')->assertNotFound();
    }

    /** @return array<string, mixed> */
    private function contributionCalendar(int $total): array
    {
        return ['data' => ['user' => ['contributionsCollection' => ['contributionCalendar' => [
            'totalContributions' => $total,
            'weeks' => [['contributionDays' => [
                ['date' => now()->toDateString(), 'contributionCount' => $total],
            ]]],
        ]]]]];
    }
}
