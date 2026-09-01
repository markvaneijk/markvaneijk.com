<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * markvaneijk.nl is the Dutch site and markvaneijk.com the English one. They
 * are the same application, the same routes and the same pages; the host is
 * the whole of the difference, and this is where that is decided — before the
 * static cache middleware, which keeps a file per domain, and before anything
 * renders a word.
 */
class SetLocaleFromHost
{
    public function handle(Request $request, Closure $next): Response
    {
        Locales::apply(Locales::forHost($request->getHost()));

        return $next($request);
    }
}
