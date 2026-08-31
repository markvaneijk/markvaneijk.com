<?php

namespace App\View\Components\Now;

use App\Domain\Socials\Clients\GitHub;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Stars extends Component
{
    public function __construct(public int $limit = 3) {}

    public function render(): string|View
    {
        $github = new GitHub;
        $stars = $github->stars();

        if ($stars === null) {
            return '';
        }

        return view('components.now.stars', [
            'stars' => $stars,
            'popular' => $github->popularRepositories($this->limit) ?? [],
            'profileUrl' => $github->profileUrl(),
        ]);
    }
}
