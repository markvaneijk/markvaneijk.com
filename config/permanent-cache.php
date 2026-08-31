<?php

return [
    /**
     * Where the rendered /now widgets live. Its own store on purpose: the point
     * of caching them permanently is that nobody ever waits on the APIs behind
     * them, and a deploy that runs `cache:clear` would hand the next visitor
     * exactly that bill. See the `permanent` store in config/cache.php.
     */
    'store' => env('PERMANENT_CACHE_STORE', 'permanent'),

    'components' => [
        /**
         * The HTML comments the package can wrap a cached component in to say
         * which cache drew it. Off here: every page goes out through the HTML
         * minifier, which strips comments, so they would cost a render and
         * never reach the browser.
         */
        'markers' => [
            'enabled' => env('PERMANENT_CACHE_MARKERS_ENABLED', false),
            'hash' => env('PERMANENT_CACHE_MARKERS_HASH', env('APP_ENV') === 'production'),
        ],
    ],
];
