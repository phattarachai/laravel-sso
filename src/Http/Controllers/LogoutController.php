<?php

namespace Phattarachai\Sso\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Phattarachai\Sso\Http\FullPageRedirect;
use Phattarachai\Sso\Owner;

final class LogoutController
{
    public function __invoke(Request $request, Owner $owner): RedirectResponse|Response
    {
        $owner->logOut();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return FullPageRedirect::to($request, route('sso.signed-out'));
    }
}
