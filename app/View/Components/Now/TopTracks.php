<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\LastFm;
use App\Domain\Socials\Clients\Spotify;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;

class TopTracks extends Widget
{
    protected $store = 'now.top-tracks';

    /**
     * The two windows every chart covers, each named in the vocabulary of the
     * services that can answer for it — and, for the tab above it, in the
     * language the page is being read in. A third tab, the tracks played last,
     * is not a chart and is added in `render` where it is asked for.
     */
    private const WINDOWS = [
        ['label' => 'site.now.last_four_weeks', 'spotify' => 'short_term', 'lastfm' => '1month'],
        ['label' => 'site.now.all_time', 'spotify' => 'long_term', 'lastfm' => 'overall'],
    ];

    public function __construct(
        public string $locale,
        public int $limit = 10,
    ) {}

    /**
     * Both services are asked, not just the first one that answers: the widget
     * offers a tab per service, and there is nothing to switch to otherwise.
     * A service that stays silent — Spotify without a `user-top-read` token,
     * Last.fm without an API key — drops out and takes its tab with it.
     */
    public function render(): string|View
    {
        $cache = Store::make();
        $spotify = new Spotify($cache);
        $lastFm = new LastFm($cache);

        $sources = collect([
            [
                'key' => 'spotify',
                'label' => 'Spotify',
                'charts' => $this->charts(fn (array $window) => $spotify->topTracks($window['spotify'])),
                'recent' => $this->cut($spotify->recentTracks()),
            ],
            [
                'key' => 'lastfm',
                'label' => 'Last.fm',
                'charts' => $this->charts(fn (array $window) => $lastFm->topTracks($window['lastfm'])),
                'recent' => $this->cut($lastFm->recentTracks()),
            ],
        ])->filter(fn (array $source) => (bool) $source['charts'])->values();

        if ($sources->isEmpty()) {
            return '';
        }

        // One window tab set for every service, so switching service keeps the
        // window you were looking at.
        $windows = array_map(fn (array $window) => __($window['label']), self::WINDOWS);

        // What was played last is a window like any other, and the tabs above
        // it are shared — so it is offered as soon as one service can fill it.
        // One that cannot, like Spotify on a token minted before
        // `user-read-recently-played` was asked for, gets an empty list there
        // and the view says so, rather than the other service's list going
        // missing along with it.
        $offersRecent = $sources->contains(fn (array $source) => (bool) $source['recent']);

        if ($offersRecent) {
            $windows[] = __('site.now.last_tracks');
        }

        $sources = $sources->map(fn (array $source) => [
            'key' => $source['key'],
            'label' => $source['label'],
            'charts' => $offersRecent
                ? [...$source['charts'], $source['recent']]
                : $source['charts'],
        ])->all();

        return view('components.now.top-tracks', compact('sources', 'windows'));
    }

    /**
     * A chart per window, in the order the window tabs sit in. Both windows or
     * neither, so a service never fills one tab and leaves the other blank.
     *
     * @return array<int, array>
     */
    private function charts(callable $fetch): array
    {
        $charts = [];

        foreach (self::WINDOWS as $window) {
            $tracks = $fetch($window);

            if (! $tracks) {
                return [];
            }

            $charts[] = $this->cut($tracks);
        }

        return $charts;
    }

    /**
     * A list cut to the length the widget draws; a service that answered with
     * nothing leaves an empty one behind.
     *
     * @return array<int, array>
     */
    private function cut(?array $tracks): array
    {
        return array_slice($tracks ?? [], 0, $this->limit);
    }
}
