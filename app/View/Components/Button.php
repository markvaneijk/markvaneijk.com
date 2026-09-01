<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public string $href;

    public bool $external = false;

    public function __construct($href)
    {
        $this->href = $href;

        // Against the site being served, not config('app.url'): the same page
        // is served on two domains, and route() writes whichever one asked —
        // so a link home would count as somebody else's and open a tab.
        if (
            ! str_starts_with($this->href, url('/')) &&
            ! str_starts_with($this->href, '/')
        ) {
            $this->external = true;
        }
    }

    public function render()
    {
        return view('components.button');
    }
}
