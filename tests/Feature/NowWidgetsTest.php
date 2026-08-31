<?php

namespace Tests\Feature;

use App\Domain\Socials\Store;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NowWidgetsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.x.username' => 'markvaneijk',
            'services.x.bearer_token' => 'bearer-token',
            'services.lastfm.username' => 'markve',
            'services.lastfm.api_key' => 'key',
            'services.lastfm.api_secret' => 'secret',
            'services.strava.client_id' => 1,
            'services.strava.client_secret' => 'strava-secret',
        ]);

        Store::make()->put('strava.access_token', 'strava-token', 3600);
    }

    public function test_it_shows_followers_distance_and_listening_stats(): void
    {
        Http::fake([
            'api.x.com/*' => Http::response(['data' => ['public_metrics' => ['followers_count' => 1234]]]),
            'www.strava.com/api/v3/athlete/activities*' => Http::response([
                ['distance' => 10000.0],
                ['distance' => 5250.0],
            ]),
            'ws.audioscrobbler.com/*user.getrecenttracks*' => Http::response($this->recentTracks()),
            'ws.audioscrobbler.com/*user.gettoptracks*' => Http::response($this->topTracks()),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertSee('1,234');
        $response->assertSee('Followers on X');
        $response->assertSee('15.3');
        $response->assertSee('Distance · last 30 days', false);
        $response->assertSee('Distance · last 12 months', false);
        $response->assertSee('Now playing');
        $response->assertSee('Everglow');
        $response->assertSee('Coldplay');
        $response->assertSee('Top tracks · last 30 days', false);
        $response->assertSee('Slaap');
        $response->assertSee('7 plays');

        // Each widget carries the mark of the service that answered — here
        // Last.fm, since no Spotify token is stored.
        $response->assertSee('aria-label="Strava"', false);
        $response->assertSee('aria-label="Last.fm"', false);
        $response->assertDontSee('aria-label="Spotify"', false);
    }

    public function test_a_widget_disappears_when_its_api_is_unavailable(): void
    {
        Http::fake([
            'api.x.com/*' => Http::response(status: 403),
            'www.strava.com/*' => Http::response(status: 401),
            'ws.audioscrobbler.com/*' => Http::response(['error' => 6]),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertDontSee('Followers on X');
        $response->assertDontSee('Distance · last 30 days', false);
        $response->assertDontSee('Distance · last 12 months', false);
        $response->assertDontSee('Now playing');
        $response->assertDontSee('Top tracks · last 30 days', false);
    }

    public function test_it_falls_back_to_the_last_scrobble_when_nothing_is_playing(): void
    {
        Http::fake([
            'ws.audioscrobbler.com/*user.getrecenttracks*' => Http::response(
                $this->recentTracks(nowPlaying: false)
            ),
            '*' => Http::response(status: 403),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertSee('Last played');
        $response->assertSee('Everglow');
    }

    public function test_it_adds_up_every_page_of_a_long_distance_window(): void
    {
        // A year of riding overruns Strava's 200-per-page ceiling, and a total
        // built from page one alone would quietly under-report.
        Http::fake([
            'www.strava.com/api/v3/athlete/activities*' => function ($request) {
                parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

                return Http::response(match ((int) $query['page']) {
                    1 => array_fill(0, 200, ['distance' => 1000.0]),
                    2 => [['distance' => 500.0]],
                    default => [],
                });
            },
            '*' => Http::response(status: 403),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertSee('200.5');
    }

    private function recentTracks(bool $nowPlaying = true): array
    {
        $track = [
            'name' => 'Everglow',
            'artist' => ['#text' => 'Coldplay'],
            'album' => ['#text' => 'A Head Full of Dreams'],
            'image' => [['size' => 'large', '#text' => 'https://example.com/cover.png']],
            'url' => 'https://www.last.fm/music/Coldplay/_/Everglow',
        ];

        $track += $nowPlaying
            ? ['@attr' => ['nowplaying' => 'true']]
            : ['date' => ['uts' => (string) now()->subHour()->timestamp]];

        return ['recenttracks' => ['track' => [$track]]];
    }

    private function topTracks(): array
    {
        return ['toptracks' => ['track' => [
            [
                'name' => 'Slaap',
                'artist' => ['name' => 'The Opposites'],
                'url' => 'https://www.last.fm/music/The+Opposites/_/Slaap',
                'playcount' => '7',
            ],
        ]]];
    }
}
