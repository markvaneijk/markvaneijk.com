<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\GitHub;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;

class LatestPush extends Widget
{
    protected $store = 'now.latest-push';

    public function render(): string|View
    {
        $push = (new GitHub(Store::make()))->latestPush();

        if (! $push) {
            return '';
        }

        return view('components.now.latest-push', compact('push'));
    }
}
