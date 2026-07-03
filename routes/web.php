<?php

use App\Http\Controllers\AliasController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Socials\LastFmController;
use App\Http\Controllers\Socials\SpotifyController;
use App\Http\Controllers\Socials\StravaController;
use App\Http\Controllers\UsesController;
use Backstage\Static\Laravel\Middleware\StaticResponse;
use Illuminate\Support\Facades\Route;

Route::middleware(StaticResponse::class)->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('uses', UsesController::class)->name('uses');
    Route::get('now', NowController::class)->name('now');
    Route::get('aliases', AliasController::class)->name('aliases');
});

Route::get('sitemap.xml', SitemapController::class);
Route::get('feed', FeedController::class)->name('feed');

Route::group(['prefix' => 'socials'], function () {
    Route::get('lastfm/authorize', [LastFmController::class, 'authorize'])->name('socials.lastfm.authorize');
    Route::get('lastfm/callback', [LastFmController::class, 'callback'])->name('socials.lastfm.callback_url');
    Route::get('lastfm/playing', [LastFmController::class, 'activities'])->name('socials.lastfm.activities');

    Route::get('spotify/authorize', [SpotifyController::class, 'authorize'])->name('socials.spotify.authorize');
    Route::get('spotify/callback', [SpotifyController::class, 'callback'])->name('socials.spotify.callback_url');
    Route::get('spotify/now-playing', [SpotifyController::class, 'nowPlaying'])->name('socials.spotify.activities');

    Route::get('strava/authorize', [StravaController::class, 'authorize'])->name('socials.strava.authorize');
    Route::get('strava/callback', [StravaController::class, 'callback'])->name('socials.strava.callback_url');
    Route::get('strava/activities', [StravaController::class, 'activities'])->name('socials.strava.activities');
});

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
