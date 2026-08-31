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

    // Most of this reads without credentials. A token is what buys the
    // contribution graph — GraphQL refuses anonymous callers — and with it the
    // private work, which is the larger half. Either connect the account with
    // `socials:connect github` or drop a personal access token in GITHUB_TOKEN;
    // the connected one wins when both are there.
    'github' => [
        'username' => env('GITHUB_USERNAME', 'markvaneijk'),
        'token' => env('GITHUB_TOKEN'),
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect_uri' => env('GITHUB_REDIRECT_URI'),
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

    // The OAuth 2.0 client from the developer portal, not the API key and secret
    // sitting next to it: the follower count reads through `users/me`, and that
    // endpoint refuses app-only tokens. The username only builds the profile
    // link; the count comes from whoever authorized the app.
    'x' => [
        'username' => env('X_USERNAME', 'markvaneijk'),
        'client_id' => env('X_CLIENT_ID'),
        'client_secret' => env('X_CLIENT_SECRET'),
        'redirect_uri' => env('X_REDIRECT_URI'),
    ],

];
