<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\Strava;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;

class Distance extends Widget
{
    /** The window is part of the key: the page draws two. */
    protected $store = 'now.distance';

    public function __construct(
        public string $locale,
        public int $days = 30,
    ) {}

    public function render(): string|View
    {
        $distance = (new Strava(Store::make()))->distanceInKilometers($this->days);

        if ($distance === null) {
            return '';
        }

        return view('components.now.distance', [
            'distance' => $distance,
            'label' => $this->label(),
        ]);
    }

    /**
     * A year of riding reads as twelve months rather than 365 days; anything
     * shorter is counted in the days it was asked for.
     */
    private function label(): string
    {
        return $this->days < 365
            ? __('site.now.distance_last_days', ['days' => $this->days])
            : __('site.now.distance_last_months', ['months' => intdiv($this->days, 30)]);
    }
}
