<?php

namespace App\Providers;

use App\Support\Locales;
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
     *
     * The language is one of those arguments: /now is served in Dutch on
     * markvaneijk.nl and in English on markvaneijk.com, and the two keep their
     * own drawn HTML. So every widget below is registered once per language,
     * and the scheduler keeps both warm.
     */
    private function registerNowWidgets(): void
    {
        foreach (array_keys(Locales::all()) as $locale) {
            PermanentCache::caches([
                [NowPlaying::class => ['locale' => $locale]],
                [LatestPush::class => ['locale' => $locale]],
                [Contributions::class => ['locale' => $locale, 'days' => 30]],
                [Stars::class => ['locale' => $locale, 'limit' => 3]],
                [Distance::class => ['locale' => $locale, 'days' => 30]],
                [Distance::class => ['locale' => $locale, 'days' => 365]],
                [Followers::class => ['locale' => $locale]],
                [TopTracks::class => ['locale' => $locale, 'limit' => 10]],
            ]);
        }
    }
}
