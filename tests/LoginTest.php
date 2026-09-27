<?php

use Laravel\Socialite\Facades\Socialite;

it('sends a guest to the provider', function (): void {
    Socialite::fake('google');

    $this->get('/login')->assertRedirect('https://socialite.fake/google/authorize');
});

it('turns an Inertia visit into a full page visit', function (): void {
    $this->get('https://notepad.phattarachai.app/login', ['X-Inertia' => 'true'])
        ->assertStatus(409)
        ->assertHeader('X-Inertia-Location', 'https://notepad.phattarachai.app/login');
});

it('logs the owner straight in on a local host in the local env', function (): void {
    $owner = $this->createOwner();
    $this->app['env'] = 'local';

    $this->get('https://notepad.test/login')->assertRedirect('/');

    $this->assertAuthenticatedAs($owner);
});

it('still goes to the provider when a public host runs with APP_ENV=local', function (): void {
    $this->createOwner();
    $this->app['env'] = 'local';
    Socialite::fake('google');

    $this->get('https://notepad.phattarachai.app/login')->assertRedirect('https://socialite.fake/google/authorize');

    $this->assertGuest();
});

it('does not log in locally outside the local env', function (): void {
    $this->createOwner();
    Socialite::fake('google');

    $this->get('https://notepad.test/login')->assertRedirect('https://socialite.fake/google/authorize');

    $this->assertGuest();
});

it('logs out to the signed-out page', function (): void {
    $this->actingAs($this->createOwner());

    $this->post('/logout')->assertRedirect(route('sso.signed-out'));

    $this->assertGuest();
    $this->get(route('sso.signed-out'))->assertOk()->assertSee('signed out');
});
