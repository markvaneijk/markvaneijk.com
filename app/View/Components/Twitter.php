<?php

namespace App\View\Components;

use App\Domain\Socials\Clients\Twitter as TwitterClient;
use Illuminate\View\Component;

class Twitter extends Component
{
    protected $client;

    public function __construct()
    {
        $this->client = new TwitterClient();
    }

    public function render()
    {
        $me = $this->client->me();

        return view('components.twitter', compact('me'));
    }
}
