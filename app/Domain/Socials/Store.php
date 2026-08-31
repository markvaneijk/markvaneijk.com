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
        return Cache::driver(self::name());
    }

    /** Which cache store the tokens and the responses behind them live in. */
    public static function name(): string
    {
        return (string) config('services.socials.store');
    }

    /**
     * True when that is the store a deploy empties. Tokens do not survive it,
     * and getting them back means another browser round trip, so the answer
     * should stay false — see the `socials` store in config/cache.php.
     */
    public static function sharesDefaultStore(): bool
    {
        return self::name() === (string) config('cache.default');
    }
}
