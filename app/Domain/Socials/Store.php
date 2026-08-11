<?php

namespace App\Domain\Socials;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

/**
 * Everything the /now widgets keep between requests lives here: the OAuth
 * tokens handed out once through a browser redirect, and the API responses
 * behind them. Both need a store that outlives the request, whichever cache
 * driver the rest of the app happens to run on.
 */
class Store
{
    public static function make(): Repository
    {
        return Cache::driver(config('services.socials.store'));
    }
}
