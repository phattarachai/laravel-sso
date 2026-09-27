<?php

namespace Phattarachai\Sso\Http\Controllers;

use Illuminate\Contracts\View\View;

final class SignedOutController
{
    public function __invoke(): View
    {
        return view('sso::signed-out');
    }
}
