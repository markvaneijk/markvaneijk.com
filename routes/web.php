<?php

use App\Domain\Socials\Connections;
use App\Http\Controllers\AliasController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Socials\CallbackController;
use App\Http\Controllers\UsesController;
use Backstage\Static\Laravel\Middleware\StaticResponse;
use Illuminate\Support\Facades\Route;

// Pages that only change when something is deployed, so a file on disk says
// as much as a request would.
Route::middleware(StaticResponse::class)->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('uses', UsesController::class)->name('uses');
    Route::get('aliases', AliasController::class)->name('aliases');
});

// The one page that is never done changing, and the only one deliberately
// left out of the static cache: its widgets are redrawn by the scheduler every
// minute or five, and a static file would hold the first of those until the
// next deploy. Nothing here waits on an API either — the widgets are handed
// out as the scheduler last rendered them — so what a visitor pays for is a
// Blade render of cached HTML.
Route::get('now', NowController::class)->name('now');

Route::get('sitemap.xml', SitemapController::class);
Route::get('feed', FeedController::class)->name('feed');

// The only trace the OAuth flow leaves on the site. Connecting an account is
// `php artisan socials:connect`; all this page does is show the code the
// provider redirected back with, so it can be pasted into that command.
Route::get('socials/{service}/callback', CallbackController::class)
    ->whereIn('service', Connections::names())
    ->name('socials.callback');

Route::get('posts', PostController::class)->name('posts')->middleware(StaticResponse::class);

// Legacy blog posts that still earn backlinks (Laracasts, SitePoint, Habr, …)
// but were removed years ago — 301 them to /posts to preserve link equity.
foreach ([
    'use-hhvm-to-speed-up-composer',
    'whats-new-and-upcoming-in-laravel-4-1',
    'caching-routes-using-filters-in-laravel-4',
    'why-you-should-use-mariadb-instead-of-mysql',
    'the-fastest-way-to-install-laravel',
    'minify-the-html-output-in-laravel-4',
    'quick-tip-caching-eloquent-in-laravel-4',
    'how-to-automatically-protect-your-forms-in-laravel-against-csrf',
    'laravel-4-all-the-new-good-stuff',
    'route-patterns-in-laravel',
] as $legacySlug) {
    Route::redirect($legacySlug, '/posts', 301);
}

Route::get('{post}', [PostController::class, 'show'])->where('post', '.*')->name('post')->middleware(StaticResponse::class);
