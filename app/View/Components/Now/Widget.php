<?php

namespace App\View\Components\Now;

use App\Support\Locales;
use Backstage\PermanentCache\Laravel\CachedComponent;
use Backstage\PermanentCache\Laravel\Scheduled;
use Illuminate\Console\Scheduling\CallbackEvent;

/**
 * A widget on /now, drawn by the scheduler rather than by whoever asked for the
 * page. What each one renders is kept forever and handed out as it stands, so
 * the only thing that ever waits on Spotify, Strava, GitHub, X or Last.fm is a
 * background run. A cold start — the first hit after a deploy cleared the
 * static cache — then costs a Blade render instead of a walk around five APIs.
 *
 * Every subclass has to be registered in AppServiceProvider with the arguments
 * the page draws it with. A combination nobody registered still renders, but it
 * renders in front of a visitor and, once cached, has nothing scheduled to
 * update it again — NowWidgetsTest holds the two lists against each other.
 */
abstract class Widget extends CachedComponent implements Scheduled
{
    /**
     * The language this widget is drawn in — Dutch on markvaneijk.nl, English
     * on markvaneijk.com.
     *
     * Every subclass promotes it into its own constructor rather than
     * inheriting it from here: only the properties a widget declares itself
     * are part of the key it caches under, and a widget that kept one
     * language's HTML for both domains would hand Dutch to markvaneijk.com.
     */
    public string $locale;

    /**
     * A request already knows which language it is being read in; the
     * scheduler does not. It draws every widget outside any request, hours
     * before a visitor arrives, and the locale it was registered with is the
     * only thing there that says which language to draw it in — so it is
     * applied here, where both the widget and the view it returns are still
     * ahead of us.
     */
    public function resolveView()
    {
        Locales::apply($this->locale);

        return parent::resolveView();
    }

    /**
     * Often enough to follow the response cache behind it: the clients keep a
     * TTL per API and that is what decides when a call really goes out, so a
     * run that finds one warm is a Blade render and nothing more.
     *
     * @param  CallbackEvent  $callback
     */
    public static function schedule($callback)
    {
        // No client sets an HTTP timeout, so a hung API would otherwise stack
        // another run on top of the first every time the cron fires.
        $callback->everyFiveMinutes()->withoutOverlapping(expiresAt: 10);
    }
}
