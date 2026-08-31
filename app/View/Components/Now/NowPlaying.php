<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\LastFm;
use App\Domain\Socials\Clients\Spotify;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NowPlaying extends Component
{
    /**
     * Spotify knows what is playing this second; Last.fm keeps the scrobble
     * history, so it can still name the last track once playback stops.
     */
    public function render(): string|View
    {
        $cache = Store::make();

        $track = (new Spotify($cache))->nowPlaying();
        $source = $track ? 'spotify' : 'lastfm';

        $track ??= (new LastFm($cache))->nowPlaying();

        if (! $track) {
            return '';
        }

        return view('components.now.now-playing', compact('track', 'source'));
    }
}
