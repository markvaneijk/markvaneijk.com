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
     * language the page is being read in.
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
            ],
            [
                'key' => 'lastfm',
                'label' => 'Last.fm',
                'charts' => $this->charts(fn (array $window) => $lastFm->topTracks($window['lastfm'])),
            ],
        ])->filter(fn (array $source) => (bool) $source['charts'])->values()->all();

        if (! $sources) {
            return '';
        }

        // One window tab set for every service, so switching service keeps the
        // window you were looking at.
        $windows = array_map(fn (array $window) => __($window['label']), self::WINDOWS);

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

            $charts[] = array_slice($tracks, 0, $this->limit);
        }

        return $charts;
    }
}
