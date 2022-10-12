<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'instagram' => [
        'username' => env('INSTAGRAM_USERNAME'),
        'password' => env('INSTAGRAM_PASSWORD'),
    ],

    'lastfm' => [
        'username' => env('LASTFM_USERNAME'),
        'api_key' => env('LASTFM_KEY'),
        'api_secret' => env('LASTFM_SECRET'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'spotify' => [
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
    ],

    'strava' => [
        'client_id' => env('STRAVA_CLIENT_ID'),
        'client_secret' => env('STRAVA_CLIENT_SECRET'),
    ],

    'twitter' => [
        'api_key' => env('TWITTER_API_KEY'),
        'api_secret' => env('TWITTER_API_SECRET'),
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
    ],

];
