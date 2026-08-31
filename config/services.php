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

    // Never the default store: `cache:clear` on a deploy would take the tokens
    // with it. See the `socials` store in config/cache.php.
    'socials' => [
        'store' => env('SOCIALS_STORE', 'socials'),
    ],

    // Most of this reads without credentials. The token is what buys the
    // contribution graph — GraphQL refuses anonymous callers — and with it the
    // private work, which is the larger half.
    'github' => [
        'username' => env('GITHUB_USERNAME', 'markvaneijk'),
        'token' => env('GITHUB_TOKEN'),
    ],

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

    // Both redirect URIs default to this app's own callback page. Set them
    // explicitly to run `socials:connect` from a machine whose APP_URL is not
    // the one registered with the provider — a laptop against production, say.
    'spotify' => [
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        'redirect_uri' => env('SPOTIFY_REDIRECT_URI'),
    ],

    'strava' => [
        'client_id' => env('STRAVA_CLIENT_ID'),
        'client_secret' => env('STRAVA_CLIENT_SECRET'),
        'redirect_uri' => env('STRAVA_REDIRECT_URI'),
        'athlete_id' => env('STRAVA_ATHLETE_ID', '9775556'),
    ],

    // X still issues credentials under the Twitter name, so both spellings of
    // the environment variables are accepted.
    'x' => [
        'username' => env('X_USERNAME', 'markvaneijk'),
        'api_key' => env('X_API_KEY', env('TWITTER_API_KEY')),
        'api_secret' => env('X_API_SECRET', env('TWITTER_API_SECRET')),
        'bearer_token' => env('X_BEARER_TOKEN', env('TWITTER_BEARER_TOKEN')),
        'access_token' => env('X_ACCESS_TOKEN', env('TWITTER_ACCESS_TOKEN')),
        'access_token_secret' => env('X_ACCESS_TOKEN_SECRET', env('TWITTER_ACCESS_TOKEN_SECRET')),
    ],

];
