<?php

use App\Http\Controllers\AliasController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Socials\LastFmController;
use App\Http\Controllers\Socials\SpotifyController;
use App\Http\Controllers\Socials\StravaController;
use App\Http\Controllers\UsesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('uses', UsesController::class)->name('uses');
Route::get('now', NowController::class)->name('now');
Route::get('aliases', AliasController::class)->name('aliases');
Route::get('sitemap.xml', SitemapController::class);

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

Route::get('posts', PostController::class)->name('posts');
Route::get('{post}', [PostController::class, 'show'])->where('post', '.*')->name('post');
