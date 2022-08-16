<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UsesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('uses', [UsesController::class, 'index'])->name('uses');
Route::get('now', [NowController::class, 'index'])->name('now');

Route::get('posts', [PostController::class, 'index'])->name('post.index');
Route::get('{post}', [PostController::class, 'show'])->where('post', '.*')->name('post.show');
