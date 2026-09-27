<?php

namespace Phattarachai\Sso\Tests;

use Illuminate\Foundation\Auth\User;
use Laravel\Socialite\SocialiteServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Override;
use Phattarachai\Sso\SsoServiceProvider;

abstract class TestCase extends BaseTestCase
{
    #[Override]
    protected function getPackageProviders($app): array
    {
        return [SocialiteServiceProvider::class, SsoServiceProvider::class];
    }

    #[Override]
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.url', 'https://notepad.phattarachai.app');
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('sso.allowed_emails', 'Owner@gmail.com, work@example.dev');
        $app['config']->set('services.google.client_id', 'client-id');
        $app['config']->set('services.google.client_secret', 'client-secret');
    }

    #[Override]
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }

    protected function createOwner(string $email = 'owner@gmail.com'): User
    {
        return User::forceCreate(['name' => 'Owner', 'email' => $email, 'password' => 'unused']);
    }
}
