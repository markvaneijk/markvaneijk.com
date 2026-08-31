<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\LastFm;
use App\Domain\Socials\Clients\Spotify;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopTracks extends Component
{
    /**
     * The two charts the widget puts behind its tabs, each window named in the
     * vocabulary of whichever service ends up answering for it.
     */
    private const CHARTS = [
        ['label' => 'Last 4 weeks', 'spotify' => 'short_term', 'lastfm' => '1month'],
        ['label' => 'All time', 'spotify' => 'long_term', 'lastfm' => 'overall'],
    ];

    /** Fifty is as far as Spotify counts in one call, so it is the whole chart. */
    public function __construct(public int $limit = 50) {}

    /**
     * Spotify's own charts when the stored token carries `user-top-read`,
     * otherwise Last.fm's. Both windows render; a radio picks which one shows.
     */
    public function render(): string|View
    {
        $cache = Store::make();

        $spotify = new Spotify($cache);
        $charts = $this->chartsFrom(fn (array $chart) => $spotify->topTracks($chart['spotify']));
        $source = 'spotify';

        if (! $charts) {
            $lastFm = new LastFm($cache);
            $charts = $this->chartsFrom(fn (array $chart) => $lastFm->topTracks($chart['lastfm']));
            $source = 'lastfm';
        }

        if (! $charts) {
            return '';
        }

        return view('components.now.top-tracks', compact('charts', 'source'));
    }

    /**
     * Both charts or neither: one service answers for the whole widget, so the
     * two tabs never end up counting by different rules.
     *
     * @return array<int, array{label: string, tracks: array}>
     */
    private function chartsFrom(callable $fetch): array
    {
        $charts = [];

        foreach (self::CHARTS as $chart) {
            $tracks = $fetch($chart);

            if (! $tracks) {
                return [];
            }

            $charts[] = [
                'label' => $chart['label'],
                'tracks' => array_slice($tracks, 0, $this->limit),
            ];
        }

        return $charts;
    }
}
