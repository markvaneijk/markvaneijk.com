<?php

return [
    // Renders the image on every request instead of serving the cached one, so
    // a change to the template shows up without clearing anything first.
    'debug' => env('OG_IMAGE_DEBUG', false),

    // The card is flat colour, thin type and a one-pixel grid — everything JPEG
    // artefacts show up in. PNG keeps it sharp and still lands well under 200kB.
    'extension' => 'png',

    'width' => 1200,
    'height' => 630,

    'chrome' => [
        'path' => env('CHROME_PATH', 'chromium'),
        'flags' => [
            // The card has no UI to interact with and never scrolls: everything
            // below keeps a headless Chrome out of the way of the screenshot,
            // and lets it run as the web user in a container.
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--hide-scrollbars',
            '--mute-audio',
            '--no-sandbox',
        ],
    ],

    // Served from public/storage, so `php artisan storage:link` has to have run.
    'storage' => [
        'disk' => 'public',
        'path' => 'og-images',
    ],

    // Tags <x-og-image-tags> writes beside the image ones, mapped to the
    // attributes it was given. Empty on purpose: og:title, og:description,
    // og:type and og:url are written by templates/partials/meta.blade.php from
    // the page's own sections, and the component would only repeat them from
    // the attributes the card is drawn with.
    'metatags' => [],
];
