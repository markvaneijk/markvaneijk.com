<?php

Route::get('/', 'HomeController@index')->name('home');
Route::get('uses', 'UsesController@index')->name('uses');
Route::get('now', 'NowController@index')->name('now');

Route::get('posts', 'PostController@index')->name('posts');
Route::get('{post}', 'PostController@show')->where('post', '.*')->name('post');
