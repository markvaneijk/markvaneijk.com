<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\LastFm;
use App\Domain\Socials\Clients\Spotify;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopTracks extends Component
{
    public function __construct(public int $limit = 3) {}

    /**
     * Spotify's own chart when the stored token carries `user-top-read`,
     * otherwise Last.fm's — both cover roughly the last four weeks.
     */
    public function render(): string|View
    {
        $cache = Store::make();

        $tracks = (new Spotify($cache))->topTracks($this->limit)
            ?? (new LastFm($cache))->topTracks($this->limit);

        if (! $tracks) {
            return '';
        }

        return view('components.now.top-tracks', compact('tracks'));
    }
}
