<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\GitHub;
use App\Domain\Socials\Store;
use Illuminate\Contracts\View\View;

class Contributions extends Widget
{
    protected $store = 'now.contributions';

    public function __construct(public int $days = 30) {}

    public function render(): string|View
    {
        $github = new GitHub(Store::make());
        $contributions = $github->contributions($this->days);

        if (! $contributions) {
            return '';
        }

        return view('components.now.contributions', [
            'label' => "Commits · last {$this->days} days",
            'total' => $contributions['total'],
            'levels' => $this->levels($contributions['per_day']),
            'merged' => $github->mergedPullRequests($this->days),
            'profileUrl' => $github->profileUrl(),
        ]);
    }

    /**
     * One shade per day, scaled to the busiest day in the window: an average
     * week should still read as a bar, not as an empty row.
     *
     * @param  array<string, int>  $perDay
     * @return array<int, int>
     */
    private function levels(array $perDay): array
    {
        $peak = max($perDay) ?: 1;

        return array_map(
            fn (int $count) => $count === 0 ? 0 : (int) max(1, ceil($count / $peak * 4)),
            array_values($perDay)
        );
    }
}
