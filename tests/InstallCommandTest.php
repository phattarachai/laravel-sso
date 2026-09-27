<?php

use Illuminate\Support\Facades\File;

it('adds the SSO keys to .env.example and passes with an owner row', function (): void {
    $this->createOwner();
    File::put(base_path('.env.example'), "APP_NAME=Notepad\nGOOGLE_CLIENT_ID=\n");

    $this->artisan('sso:install')
        ->expectsOutputToContain('/auth/sso/callback')
        ->assertSuccessful();

    expect(File::get(base_path('.env.example')))
        ->toContain('SSO_ALLOWED_EMAILS=')
        ->toContain('GOOGLE_CLIENT_SECRET=')
        ->and(substr_count(File::get(base_path('.env.example')), 'GOOGLE_CLIENT_ID='))->toBe(1);
})->after(fn () => File::delete(base_path('.env.example')));

it('fails when the owner has no user row', function (): void {
    $this->artisan('sso:install')
        ->expectsOutputToContain('No user row has the owner email [owner@gmail.com]')
        ->assertFailed();
});
