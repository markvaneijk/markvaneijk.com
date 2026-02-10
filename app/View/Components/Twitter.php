<?php

namespace App\View\Components;

use App\Domain\Socials\Clients\X;
use Illuminate\View\Component;

class X extends Component
{
    protected $client;

    public function __construct()
    {
        $this->client = new X;
    }

    public function render()
    {
        $me = $this->client->me();

        return view('components.x', compact('me'));
    }
}
