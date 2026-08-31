<?php

namespace Tests\Feature;

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
        ]);
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
}
