<?php

namespace Phattarachai\Sso\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Phattarachai\Sso\Http\FullPageRedirect;
use Phattarachai\Sso\LocalLogin;
use Phattarachai\Sso\Owner;
use Phattarachai\Sso\SsoDriver;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

final class LoginController
{
    public function __invoke(Request $request, SsoDriver $driver, Owner $owner, LocalLogin $localLogin): RedirectResponse|Response|SymfonyRedirectResponse
    {
        if ($request->hasHeader('X-Inertia')) {
            return FullPageRedirect::to($request, $request->fullUrl());
        }

        if ($localLogin->allows($request)) {
            return $this->logInLocally($request, $owner);
        }

        return $driver->redirect(selectAccount: $request->boolean('select'));
    }

    private function logInLocally(Request $request, Owner $owner): RedirectResponse
    {
        $owner->logIn();
        $request->session()->regenerate();

        return redirect()->intended(config('sso.home'));
    }
}
