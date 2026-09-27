<?php

namespace Phattarachai\Sso\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Contracts\User;
use Phattarachai\Sso\Owner;
use Phattarachai\Sso\SsoDriver;
use Throwable;

final class CallbackController
{
    public function __invoke(Request $request, SsoDriver $driver, Owner $owner): RedirectResponse|Response
    {
        $account = $this->signedInAccount($driver);
        $email = $account?->getEmail();

        if (! $this->isVerified($account) || ! $owner->allows($email)) {
            return $this->reject($email);
        }

        $owner->logIn();
        $request->session()->regenerate();

        return redirect()->intended(config('sso.home'));
    }

    private function signedInAccount(SsoDriver $driver): ?User
    {
        try {
            return $driver->user();
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    private function isVerified(?User $account): bool
    {
        $claims = $account instanceof AbstractUser ? $account->getRaw() : [];

        return $account !== null && data_get($claims, 'email_verified', true) !== false;
    }

    private function reject(?string $email): Response
    {
        /** @var View $view */
        $view = view('sso::rejected', ['email' => $email]);

        return new Response($view, 403);
    }
}
