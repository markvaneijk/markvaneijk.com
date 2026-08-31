<?php

namespace App\View\Components\Now;

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
