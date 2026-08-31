<?php

namespace App\Http\Controllers\Socials;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Where Spotify and Strava drop the browser once the app is approved. It reads
 * nothing and stores nothing: it prints the authorization code so it can be
 * pasted back into `php artisan socials:connect`, which does the exchange.
 * That keeps the only step that can mint a token behind a shell.
 */
class CallbackController
{
    public function __invoke(Request $request, string $service): Response
    {
        $body = match (true) {
            $request->filled('error') => ucfirst($service).' declined the request: '.$request->string('error'),
            $request->filled('code') => "Paste this into `php artisan socials:connect {$service}`:\n\n"
                .$request->string('code'),
            default => "Nothing to copy. Start at `php artisan socials:connect {$service}`.",
        };

        return response($body, headers: [
            'Content-Type' => 'text/plain; charset=utf-8',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
