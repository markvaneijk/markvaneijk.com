<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\GitHub;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LatestPush extends Component
{
    public function render(): string|View
    {
        $push = (new GitHub)->latestPush();

        if (! $push) {
            return '';
        }

        return view('components.now.latest-push', compact('push'));
    }
}
