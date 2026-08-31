<?php

namespace App\Providers;

use App\View\Components\Now\Contributions;
use App\View\Components\Now\Distance;
use App\View\Components\Now\Followers;
use App\View\Components\Now\LatestPush;
use App\View\Components\Now\NowPlaying;
use App\View\Components\Now\Stars;
use App\View\Components\Now\TopTracks;
use Backstage\PermanentCache\Laravel\Facades\PermanentCache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerNowWidgets();
    }

    /**
     * The widgets on /now, in the arguments resources/views/pages/now.blade.php
     * draws them with. Registering a widget is what gives it a scheduled run,
     * and the arguments are part of its cache key — so a widget the page draws
     * with anything but the arguments below has no background run behind it and
     * would go stale the moment it was first rendered. That is the pairing
     * NowWidgetsTest checks; keep the two in step.
     */
    private function registerNowWidgets(): void
    {
        PermanentCache::caches([
            [NowPlaying::class => []],
            [LatestPush::class => []],
            [Contributions::class => ['days' => 30]],
            [Stars::class => ['limit' => 3]],
            [Distance::class => ['days' => 30, 'label' => 'Distance · last 30 days']],
            [Distance::class => ['days' => 365, 'label' => 'Distance · last 12 months']],
            [Followers::class => []],
            [TopTracks::class => ['limit' => 10]],
        ]);
    }
}
