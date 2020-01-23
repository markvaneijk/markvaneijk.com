<?php

Route::get('/', function () {
    return view('pages.home');
});

Route::get('{post}', 'PostController@show')->where('post', '.*');
