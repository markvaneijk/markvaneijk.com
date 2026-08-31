<?php

namespace App\Domain\Socials\Clients;

use App\Domain\Socials\CachesResponses;
use App\Domain\Socials\ConnectsThroughOAuth;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * What the account shipped lately. The latest push and the star count read
 * without credentials; the contribution graph is the one thing GitHub will
 * only answer for a token, and it answers with the private work too.
 */
class GitHub implements ConnectsThroughOAuth
{
    use CachesResponses;

    /** What the contribution graph and the organization packages need. */
    public const SCOPES = 'read:user,read:org';

    private const API_URL = 'https://api.github.com';

    private const GRAPHQL_URL = 'https://api.github.com/graphql';

    private const AUTHORIZE_URL = 'https://github.com/login/oauth/authorize';

    private const TOKEN_URL = 'https://github.com/login/oauth/access_token';

    /** The window every widget reports on. */
    private const DAYS = 30;

    private const CACHE_KEY_TOKEN = 'github.access_token';

    /** The answers the /now widgets ask for; stale once the token changes. */
    private const RESPONSE_KEYS = [
        'github.latest_push',
        'github.repositories',
        'github.contributions.30d',
        'github.merged_prs.30d',
    ];

    protected string $username;

    protected Repository $cache;

    public function __construct(Repository $cache)
    {
        $this->username = (string) config('services.github.username');
        $this->cache = $cache;
    }

    /**
     * The token every call goes out with: whichever `socials:connect github`
     * stored, or the personal access token in the environment. Empty when
     * there is neither, which still reads — anonymously, and without the
     * private half of the work.
     */
    public function token(): string
    {
        return (string) ($this->cache->get(self::CACHE_KEY_TOKEN)
            ?: config('services.github.token'));
    }

    public function client()
    {
        // GitHub answers 403 to a request without a User-Agent, and Guzzle's
        // default one says nothing about who is calling.
        $client = Http::withHeaders([
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => (string) config('app.name'),
        ]);

        $token = $this->token();

        return $token === '' ? $client : $client->withToken($token);
    }

    public function get(string $endpoint, array $query = []): ?array
    {
        $response = $this->client()->get(self::API_URL.'/'.ltrim($endpoint, '/'), $query);

        return $response->successful() ? $response->json() : null;
    }

    public function profileUrl(): string
    {
        return 'https://github.com/'.$this->username;
    }

    public function isConfigured(): bool
    {
        return (string) config('services.github.client_id') !== ''
            && (string) config('services.github.client_secret') !== '';
    }

    public function redirectUri(): string
    {
        return (string) config('services.github.redirect_uri')
            ?: route('socials.callback', 'github');
    }

    public function authorizeUrl(string $state = ''): string
    {
        return self::AUTHORIZE_URL.'?'.http_build_query([
            'client_id' => (string) config('services.github.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'scope' => self::SCOPES,
            'state' => $state,
        ]);
    }

    public function connect(string $code): void
    {
        // GitHub answers a refused code with a 200 and an error in the body,
        // so the status line proves nothing here.
        $response = Http::asForm()->acceptJson()->post(self::TOKEN_URL, [
            'client_id' => (string) config('services.github.client_id'),
            'client_secret' => (string) config('services.github.client_secret'),
            'redirect_uri' => $this->redirectUri(),
            'code' => $code,
        ])->json();

        $response = is_array($response) ? $response : [];

        if (empty($response['access_token'])) {
            throw new RuntimeException('GitHub refused the authorization code: '.(
                $response['error_description'] ?? $response['error'] ?? 'no token returned'
            ).'.');
        }

        // An OAuth app token does not expire and comes with nothing to refresh
        // it from, so it is kept until it is replaced or revoked.
        $this->cache->forever(self::CACHE_KEY_TOKEN, $response['access_token']);

        $this->forgetCachedResponses();
    }

    public function isConnected(): bool
    {
        return $this->cache->has(self::CACHE_KEY_TOKEN);
    }

    public function disconnect(): void
    {
        $this->cache->forget(self::CACHE_KEY_TOKEN);

        $this->forgetCachedResponses();
    }

    /**
     * The contribution graph, which is the read that needs the token — and so
     * the one that proves the granted scopes cover more than anonymous access.
     */
    public function summarize(): ?string
    {
        $contributions = $this->contributions();

        return $contributions === null
            ? null
            : number_format($contributions['total']).' commits over the last 30 days';
    }

    /**
     * The newest public push: which repository, its head commit and when it
     * landed. The timeline carries the SHA but no longer the message, so the
     * subject line costs one more call.
     */
    public function latestPush(): ?array
    {
        if ($this->username === '') {
            return null;
        }

        $push = $this->remember('github.latest_push', 900, function () {
            $events = $this->get("users/{$this->username}/events/public", ['per_page' => 100]);

            if ($events === null) {
                return null;
            }

            // The timeline runs newest first, but not strictly: pushes made
            // within seconds of each other come back out of order.
            return collect($events)
                ->filter(fn (array $event) => ($event['type'] ?? null) === 'PushEvent'
                    && ! empty($event['repo']['name'])
                    && ! empty($event['payload']['head']))
                ->sortByDesc('created_at')
                ->map(fn (array $event) => [
                    'repo' => $event['repo']['name'],
                    'sha' => $event['payload']['head'],
                    'at' => Carbon::parse($event['created_at'])->timestamp,
                ])
                ->first();
        });

        return $push === null ? null : [
            'repo' => $push['repo'],
            'message' => $this->commitSubject($push['repo'], $push['sha']),
            'url' => "https://github.com/{$push['repo']}/commit/{$push['sha']}",
            'pushed_at' => now()->createFromTimestamp($push['at']),
        ];
    }

    /**
     * The contribution calendar over the window: a total and a count per day,
     * oldest day first, so the widget can draw the rhythm as well as count it.
     *
     * This is the one call that needs a token — and the only place the private
     * work shows up, which for this account is most of it. Without one there
     * is no honest number to print, so the widget stays away.
     *
     * @return array{total: int, per_day: array<string, int>}|null
     */
    public function contributions(int $days = self::DAYS): ?array
    {
        if ($this->username === '' || $this->token() === '') {
            return null;
        }

        return $this->remember("github.contributions.{$days}d", 900, function () use ($days) {
            $response = $this->client()->post(self::GRAPHQL_URL, [
                'query' => <<<'GRAPHQL'
                    query ($login: String!, $from: DateTime!, $to: DateTime!) {
                        user(login: $login) {
                            contributionsCollection(from: $from, to: $to) {
                                contributionCalendar {
                                    totalContributions
                                    weeks { contributionDays { date contributionCount } }
                                }
                            }
                        }
                    }
                    GRAPHQL,
                'variables' => [
                    'login' => $this->username,
                    'from' => now()->subDays($days - 1)->startOfDay()->toIso8601ZuluString(),
                    'to' => now()->endOfDay()->toIso8601ZuluString(),
                ],
            ]);

            // GraphQL reports its own failures with a 200 and a null user.
            $calendar = $response->successful()
                ? $response->json('data.user.contributionsCollection.contributionCalendar')
                : null;

            if ($calendar === null) {
                return null;
            }

            $total = (int) ($calendar['totalContributions'] ?? 0);

            return $total === 0 ? null : [
                'total' => $total,
                'per_day' => collect($calendar['weeks'] ?? [])
                    ->flatMap(fn (array $week) => $week['contributionDays'] ?? [])
                    ->mapWithKeys(fn (array $day) => [$day['date'] => (int) $day['contributionCount']])
                    ->all(),
            ];
        });
    }

    /**
     * Pull requests this account authored that were merged inside the window.
     *
     * Counted through search rather than the event timeline, which only sees a
     * merge when this account is the one who pressed the button — someone else
     * merging your work would otherwise go missing. Search sees public
     * repositories, plus whatever private ones the token can read.
     */
    public function mergedPullRequests(int $days = self::DAYS): ?int
    {
        if ($this->username === '') {
            return null;
        }

        return $this->remember("github.merged_prs.{$days}d", 900, function () use ($days) {
            $result = $this->get('search/issues', [
                'q' => "author:{$this->username} type:pr is:merged merged:>=".now()->subDays($days)->toDateString(),
                'per_page' => 1,
                'advanced_search' => 'true',
            ]);

            return $result === null ? null : (int) ($result['total_count'] ?? 0);
        });
    }

    /** Stars across every public repository this account has a hand in. */
    public function stars(): ?int
    {
        $repositories = $this->repositories();

        return $repositories === null ? null : (int) collect($repositories)->sum('stars');
    }

    /**
     * The best known of those repositories, most stars first. Anything nobody
     * has starred is left out — a zero is not a popular package.
     *
     * @return array<int, array{name: string, full_name: string, url: string, stars: int}>|null
     */
    public function popularRepositories(int $limit = 3): ?array
    {
        $repositories = $this->repositories();

        if ($repositories === null) {
            return null;
        }

        $popular = array_slice(
            array_values(array_filter($repositories, fn (array $repo) => $repo['stars'] > 0)),
            0,
            $limit
        );

        return $popular ?: null;
    }

    /**
     * Every public repository the account owns or shares an organization
     * with, most stars first.
     *
     * The organizations are where the packages actually live, and the ones
     * worth showing are not always a public membership — see organizations().
     *
     * @return array<int, array{name: string, full_name: string, url: string, stars: int}>|null
     */
    private function repositories(): ?array
    {
        if ($this->username === '') {
            return null;
        }

        return $this->remember('github.repositories', 6 * 3600, function () {
            // One page covers each of these; a hundred-and-first repository
            // would be the first one to go uncounted.
            $owned = $this->get("users/{$this->username}/repos", [
                'per_page' => 100,
                'type' => 'owner',
                'sort' => 'pushed',
            ]);

            if ($owned === null) {
                return null;
            }

            $repositories = collect($owned);

            foreach ($this->organizations() as $organization) {
                $repositories = $repositories->concat($this->get("orgs/{$organization}/repos", [
                    'per_page' => 100,
                    // Not the default: with a token that would be every
                    // repository the organization has, and the name of a
                    // private one has no business on a public page.
                    'type' => 'public',
                ]) ?? []);
            }

            return $repositories
                ->reject(fn (array $repo) => ($repo['fork'] ?? false) || ($repo['private'] ?? false))
                ->unique('full_name')
                ->map(fn (array $repo) => [
                    'name' => (string) ($repo['name'] ?? ''),
                    'full_name' => (string) ($repo['full_name'] ?? ''),
                    'url' => (string) ($repo['html_url'] ?? ''),
                    'stars' => (int) ($repo['stargazers_count'] ?? 0),
                ])
                ->sortByDesc('stars')
                ->values()
                ->all() ?: null;
        });
    }

    /**
     * The organizations to count alongside the account's own repositories.
     *
     * A token names the ones whose membership is not public — which is where
     * the packages tend to be — so long as it carries `read:org`. Without one
     * only the memberships shown on the profile are visible.
     *
     * @return array<int, string>
     */
    private function organizations(): array
    {
        $organizations = $this->token() === ''
            ? $this->get("users/{$this->username}/orgs", ['per_page' => 100])
            : $this->get('user/orgs', ['per_page' => 100]);

        return collect($organizations ?? [])->pluck('login')->filter()->values()->all();
    }

    /** The first line of a commit message, looked up by SHA. */
    private function commitSubject(string $repo, string $sha): ?string
    {
        return $this->remember("github.commit.{$sha}", 6 * 3600, function () use ($repo, $sha) {
            $commit = $this->get("repos/{$repo}/commits/{$sha}");

            if ($commit === null) {
                return null;
            }

            return Str::of($commit['commit']['message'] ?? '')->before("\n")->trim()->toString() ?: null;
        });
    }

    private function forgetCachedResponses(): void
    {
        foreach (self::RESPONSE_KEYS as $key) {
            $this->cache->forget($key);
        }
    }
}
