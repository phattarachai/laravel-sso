<?php

namespace Phattarachai\Sso\Http;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class FullPageRedirect
{
    public static function to(Request $request, string $url): RedirectResponse|Response
    {
        if ($request->hasHeader('X-Inertia')) {
            return new Response('', 409, ['X-Inertia-Location' => $url]);
        }

        return redirect()->to($url);
    }
}
