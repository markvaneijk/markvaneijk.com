<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\X;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Followers extends Component
{
    public function render(): string|View
    {
        $followers = (new X(Store::make()))->followers();

        if ($followers === null) {
            return '';
        }

        return view('components.now.followers', compact('followers'));
    }
}
