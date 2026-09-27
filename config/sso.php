<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Socialite driver
    |--------------------------------------------------------------------------
    |
    | "google" signs in with Google directly. A self-hosted IdP registers its
    | own Socialite driver and only this value changes.
    |
    */

    'driver' => env('SSO_DRIVER', 'google'),

    /*
    |--------------------------------------------------------------------------
    | Allowed emails
    |--------------------------------------------------------------------------
    |
    | Comma-separated. Every allowed account signs in as the same owner user
    | (see "login_as"). Any other account is rejected; no user is ever created.
    |
    */

    'allowed_emails' => env('SSO_ALLOWED_EMAILS', ''),

    /*
    |--------------------------------------------------------------------------
    | Owner account
    |--------------------------------------------------------------------------
    |
    | The email of the local user every allowed account logs in as. Defaults
    | to the first allowed email.
    |
    */

    'login_as' => env('SSO_LOGIN_AS'),

    'guard' => env('SSO_GUARD', 'web'),

    'home' => env('SSO_HOME', '/'),

    /*
    |--------------------------------------------------------------------------
    | Local owner login
    |--------------------------------------------------------------------------
    |
    | Google rejects redirect URIs on non-public TLDs such as .test, so on a
    | dev box /login signs the owner in directly. It needs APP_ENV=local *and*
    | a local host, so a misconfigured production box still goes to Google.
    |
    */

    'local_login' => env('SSO_LOCAL_LOGIN', true),

    'local_hosts' => ['localhost', '127.0.0.1', '*.test', '*.localhost'],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    ],

];
