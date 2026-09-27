<?php

namespace Phattarachai\Sso;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class LocalLogin
{
    public function __construct(
        private Application $app,
        private Repository $config,
    ) {}

    public function allows(Request $request): bool
    {
        return $this->app->isLocal()
            && (bool) $this->config->get('sso.local_login')
            && Str::is($this->config->get('sso.local_hosts', []), $request->getHost());
    }
}
