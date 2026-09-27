<?php

namespace Phattarachai\Sso;

use Override;
use Phattarachai\Sso\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SsoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('sso')
            ->hasConfigFile()
            ->hasViews('sso')
            ->hasRoute('sso')
            ->hasCommand(InstallCommand::class);
    }

    #[Override]
    public function packageRegistered(): void
    {
        $config = $this->app->make('config');

        $config->set('services.google', [
            ...$config->get('sso.google', []),
            'redirect' => '/auth/sso/callback',
            ...$config->get('services.google', []),
        ]);
    }
}
