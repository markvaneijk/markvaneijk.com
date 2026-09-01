<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\X;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;

class Followers extends Widget
{
    protected $store = 'now.followers';

    public function __construct(public string $locale) {}

    public function render(): string|View
    {
        $followers = (new X(Store::make()))->followers();

        if ($followers === null) {
            return '';
        }

        return view('components.now.followers', compact('followers'));
    }
}
