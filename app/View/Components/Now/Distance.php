<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\Strava;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Distance extends Component
{
    public function render(): string|View
    {
        $distance = (new Strava(Store::make()))->distanceInKilometers(30);

        if ($distance === null) {
            return '';
        }

        return view('components.now.distance', compact('distance'));
    }
}
