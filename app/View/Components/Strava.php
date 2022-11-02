<?php

namespace App\View\Components;

use App\Domain\Socials\Clients\Strava as StravaClient;
use Illuminate\View\Component;

class Strava extends Component
{
    protected $cache;
    protected $client;

    public function __construct()
    {
        $this->cache = cache()->driver('file');

        $this->client = new StravaClient($this->cache);
    }

    public function render()
    {
        $activities = $this->client->activities(after: now()->subMonth()->timestamp);
        $distance = number_format($activities->sum('distance') / 1000, 1, ',', '.');
        $prs = $activities->sum('pr_count');

        if(! (int) $distance) {
            return;
        }

        return view('components.strava', compact('distance', 'prs'));
    }
}
