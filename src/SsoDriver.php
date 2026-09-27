<?php

namespace Phattarachai\Sso;

use Illuminate\Contracts\Config\Repository;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Two\AbstractProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;

final readonly class SsoDriver
{
    public function __construct(
        private Socialite $socialite,
        private Repository $config,
    ) {}

    public function redirect(bool $selectAccount = false): RedirectResponse
    {
        /** @var AbstractProvider $provider */
        $provider = $this->provider();

        return $provider
            ->with($selectAccount ? ['prompt' => 'select_account'] : [])
            ->redirect();
    }

    public function user(): User
    {
        return $this->provider()->user();
    }

    private function provider(): Provider
    {
        /** @var AbstractProvider $provider */
        $provider = $this->socialite->driver($this->config->get('sso.driver'));

        return $provider->redirectUrl(route('sso.callback'));
    }
}
