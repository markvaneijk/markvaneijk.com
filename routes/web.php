<?php

use App\Http\Controllers\AliasController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UsesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('uses', UsesController::class)->name('uses');
Route::get('now', NowController::class)->name('now');
Route::get('aliases', AliasController::class)->name('aliases');
Route::get('sitemap.xml', SitemapController::class);

Route::get('posts', PostController::class)->name('post.index');
Route::get('{post}', [PostController::class, 'show'])->where('post', '.*')->name('post.show');
