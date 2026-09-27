<?php

use Illuminate\Foundation\Auth\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Phattarachai\Sso\Exceptions\OwnerNotFound;

function googleAccount(string $email, bool $verified = true): SocialiteUser
{
    return (new SocialiteUser)->setRaw(['email_verified' => $verified])->map(['email' => $email]);
}

it('logs an allowed account in as the owner with a remember cookie', function (): void {
    $owner = $this->createOwner();
    Socialite::fake('google', googleAccount('owner@gmail.com'));

    $response = $this->get(route('sso.callback'));

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($owner);
    expect(collect($response->headers->getCookies())->contains(fn ($cookie): bool => str_starts_with($cookie->getName(), 'remember_web_')))->toBeTrue();
});

it('maps every allowed email to the same owner', function (): void {
    $owner = $this->createOwner();
    Socialite::fake('google', googleAccount('WORK@example.dev'));

    $this->get(route('sso.callback'))->assertRedirect('/');

    $this->assertAuthenticatedAs($owner);
    expect(User::count())->toBe(1);
});

it('rejects an account that is not on the allowlist', function (): void {
    $this->createOwner();
    Socialite::fake('google', googleAccount('stranger@gmail.com'));

    $this->get(route('sso.callback'))
        ->assertForbidden()
        ->assertSee('stranger@gmail.com');

    $this->assertGuest();
    expect(User::count())->toBe(1);
});

it('rejects an unverified email', function (): void {
    $this->createOwner();
    Socialite::fake('google', googleAccount('owner@gmail.com', verified: false));

    $this->get(route('sso.callback'))->assertForbidden();

    $this->assertGuest();
});

it('rejects a failed provider exchange instead of erroring', function (): void {
    $this->createOwner();
    Socialite::fake('google', fn () => throw new RuntimeException('invalid_grant'));

    $this->get(route('sso.callback'))->assertForbidden();

    $this->assertGuest();
});

it('returns to the intended url', function (): void {
    $this->createOwner();
    Socialite::fake('google', googleAccount('owner@gmail.com'));

    $this->withSession(['url.intended' => 'https://notepad.phattarachai.app/notes/7'])
        ->get(route('sso.callback'))
        ->assertRedirect('https://notepad.phattarachai.app/notes/7');
});

it('fails loudly when the owner has no user row', function (): void {
    Socialite::fake('google', googleAccount('owner@gmail.com'));

    $this->withoutExceptionHandling()->get(route('sso.callback'));
})->throws(OwnerNotFound::class);
