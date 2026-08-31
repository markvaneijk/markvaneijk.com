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
            'services.github.username' => 'markvaneijk',
            'services.github.token' => 'github-token',
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
            'api.github.com/users/*/events/public*' => Http::response($this->events()),
            'api.github.com/repos/*/commits/*' => Http::response($this->commit()),
            'api.github.com/users/*/repos*' => Http::response($this->repos()),
            'api.github.com/user/orgs*' => Http::response($this->orgs()),
            'api.github.com/orgs/*/repos*' => Http::response($this->orgRepos()),
            'api.github.com/search/issues*' => Http::response(['total_count' => 3]),
            'api.github.com/graphql' => Http::response($this->contributionCalendar()),
            'ws.audioscrobbler.com/*user.getrecenttracks*' => Http::response($this->recentTracks()),
            'ws.audioscrobbler.com/*user.gettoptracks*' => Http::response($this->topTracks()),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertSee('1,234');
        $response->assertSee('Followers on X');
        $response->assertSee('15.3');
        $response->assertSee('Distance · last 30 days', false);
        $response->assertSee('Now playing');
        $response->assertSee('Everglow');
        $response->assertSee('Coldplay');
        $response->assertSee('Top tracks · last 30 days', false);
        $response->assertSee('Slaap');
        $response->assertSee('7 plays');
        $response->assertSee('Last push');
        $response->assertSee('markvaneijk/markvaneijk.com');
        $response->assertSee('Add /now widgets for X, Strava and listening stats');
        $response->assertSee('Contributions · last 30 days', false);
        $response->assertSee('215');
        $response->assertSee('pull requests merged');
        $response->assertDontSee('And a body nobody asked for');
        $response->assertSee('Stars on GitHub');
        // 1 owned + 760 across the organization, with the fork and the
        // private repository left out.
        $response->assertSee('761');
        $response->assertSee('laravel-seo-scanner');
        $response->assertSee('272');
        $response->assertSee('laravel-mails');
        $response->assertSee('252');
        $response->assertSee('164');
        // Fourth by stars, so it falls outside the top three.
        $response->assertDontSee('laravel-og-image');
    }

    /**
     * The organizations hold public and private repositories alike, and this
     * page is public. Nothing private may reach it — not a name, not a star.
     */
    public function test_the_star_widget_never_names_a_private_or_forked_repository(): void
    {
        Http::fake([
            'api.github.com/users/*/repos*' => Http::response($this->repos()),
            'api.github.com/user/orgs*' => Http::response($this->orgs()),
            'api.github.com/orgs/*/repos*' => Http::response($this->orgRepos()),
            '*' => Http::response(status: 403),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertDontSee('secret-thing');
        $response->assertDontSee('somebody-elses-work');

        // Asking for anything but the public ones is the mistake to catch at
        // the source: the default on that endpoint is every repository.
        Http::assertSent(fn ($request) => ! str_contains($request->url(), '/orgs/')
            || str_contains($request->url(), 'type=public'));
    }

    public function test_the_star_widget_falls_back_to_public_memberships_without_a_token(): void
    {
        config(['services.github.token' => '']);

        Http::fake([
            'api.github.com/users/*/repos*' => Http::response($this->repos()),
            'api.github.com/users/*/orgs*' => Http::response($this->orgs()),
            'api.github.com/orgs/*/repos*' => Http::response($this->orgRepos()),
            '*' => Http::response(status: 403),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertSee('laravel-seo-scanner');
        Http::assertNotSent(fn ($request) => str_starts_with($request->url(), 'https://api.github.com/user/orgs'));
    }

    public function test_the_contributions_widget_drops_the_pull_request_line_when_none_were_merged(): void
    {
        Http::fake([
            'api.github.com/graphql' => Http::response($this->contributionCalendar()),
            'api.github.com/search/issues*' => Http::response(['total_count' => 0]),
            '*' => Http::response(status: 403),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertSee('Contributions · last 30 days', false);
        $response->assertDontSee('merged');
    }

    public function test_the_contributions_widget_needs_a_token(): void
    {
        config(['services.github.token' => '']);

        Http::fake([
            'api.github.com/search/issues*' => Http::response(['total_count' => 3]),
            '*' => Http::response(status: 403),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertDontSee('Contributions · last 30 days', false);
        Http::assertNotSent(fn ($request) => $request->url() === 'https://api.github.com/graphql');
    }

    /**
     * The timeline hands back pushes made seconds apart in the wrong order,
     * and only ever carries the head SHA — never the message.
     */
    public function test_the_latest_push_is_the_newest_one_the_timeline_mentions(): void
    {
        Http::fake([
            'api.github.com/users/*/events/public*' => Http::response([
                $this->pushEvent(minutesAgo: 90, repo: 'backstagephp/shop-support', sha: 'older'),
                $this->pushEvent(minutesAgo: 20, repo: 'markvaneijk/markvaneijk.com', sha: 'newest'),
                $this->pushEvent(minutesAgo: 45, repo: 'backstagephp/laravel-ai', sha: 'middle'),
            ]),
            'api.github.com/repos/*/commits/newest' => Http::response($this->commit()),
            '*' => Http::response(status: 403),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertSee('markvaneijk/markvaneijk.com');
        $response->assertSee('Add /now widgets for X, Strava and listening stats');
        $response->assertDontSee('backstagephp');
    }

    public function test_a_widget_disappears_when_its_api_is_unavailable(): void
    {
        Http::fake([
            'api.x.com/*' => Http::response(status: 403),
            'api.github.com/*' => Http::response(status: 403),
            'www.strava.com/*' => Http::response(status: 401),
            'ws.audioscrobbler.com/*' => Http::response(['error' => 6]),
        ]);

        $response = $this->get('/now');

        $response->assertOk();
        $response->assertDontSee('Followers on X');
        $response->assertDontSee('Distance · last 30 days', false);
        $response->assertDontSee('Now playing');
        $response->assertDontSee('Top tracks · last 30 days', false);
        $response->assertDontSee('Last push');
        $response->assertDontSee('Contributions · last 30 days', false);
        $response->assertDontSee('Stars on GitHub');
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

    /** A public timeline the way GitHub serves it: a head SHA, no message. */
    private function events(): array
    {
        return [
            $this->pushEvent(minutesAgo: 30, repo: 'markvaneijk/markvaneijk.com', sha: 'bbb2222'),
            ['type' => 'WatchEvent', 'created_at' => now()->subHours(2)->toIso8601ZuluString(), 'repo' => ['name' => 'laravel/framework'], 'payload' => []],
            $this->pushEvent(minutesAgo: 8000, repo: 'backstagephp/shop-support', sha: 'ccc3333'),
        ];
    }

    private function pushEvent(int $minutesAgo, string $repo, string $sha): array
    {
        return [
            'type' => 'PushEvent',
            'created_at' => now()->subMinutes($minutesAgo)->toIso8601ZuluString(),
            'repo' => ['name' => $repo],
            'payload' => ['push_id' => 41743251855, 'ref' => 'refs/heads/main', 'head' => $sha, 'before' => 'aaa1111'],
        ];
    }

    private function commit(): array
    {
        return ['commit' => ['message' => "Add /now widgets for X, Strava and listening stats\n\nAnd a body nobody asked for."]];
    }

    /** Thirty days of contributions, chunked into weeks the way GraphQL does. */
    private function contributionCalendar(): array
    {
        $days = collect(range(29, 0))->map(fn (int $ago) => [
            'date' => now()->subDays($ago)->toDateString(),
            'contributionCount' => [0, 3, 12, 0, 7, 21][$ago % 6],
        ]);

        return ['data' => ['user' => ['contributionsCollection' => ['contributionCalendar' => [
            'totalContributions' => $days->sum('contributionCount'),
            'weeks' => $days->chunk(7)
                ->map(fn ($week) => ['contributionDays' => $week->values()->all()])
                ->values()
                ->all(),
        ]]]]];
    }

    private function orgs(): array
    {
        return [['login' => 'backstagephp']];
    }

    private function repos(): array
    {
        return [
            $this->repo('markvaneijk/markvaneijk.com', 1),
            $this->repo('markvaneijk/dotfiles', 0),
            $this->repo('markvaneijk/somebody-elses-work', 900, fork: true),
        ];
    }

    private function orgRepos(): array
    {
        return [
            $this->repo('backstagephp/laravel-mails', 252),
            $this->repo('backstagephp/mails', 164),
            $this->repo('backstagephp/laravel-seo-scanner', 272),
            $this->repo('backstagephp/laravel-og-image', 72),
            $this->repo('backstagephp/secret-thing', 500, private: true),
        ];
    }

    private function repo(string $fullName, int $stars, bool $fork = false, bool $private = false): array
    {
        return [
            'name' => explode('/', $fullName)[1],
            'full_name' => $fullName,
            'html_url' => "https://github.com/{$fullName}",
            'stargazers_count' => $stars,
            'fork' => $fork,
            'private' => $private,
        ];
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
