<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Link extends Component
{
    public string $href;

    public bool $external = false;

    public function __construct($href)
    {
        $this->href = $href;

        if (
            ! str_starts_with($this->href, config('app.url')) &&
            ! str_starts_with($this->href, '/')
        ) {
            $this->external = true;
        }
    }

    public function render()
    {
        return view('components.link');
    }
}
