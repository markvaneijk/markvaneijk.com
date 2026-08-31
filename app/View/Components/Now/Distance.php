<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\Strava;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Distance extends Component
{
    public function __construct(
        public int $days = 30,
        public string $label = 'Distance · last 30 days',
    ) {}

    public function render(): string|View
    {
        $distance = (new Strava(Store::make()))->distanceInKilometers($this->days);

        if ($distance === null) {
            return '';
        }

        return view('components.now.distance', compact('distance'));
    }
}
