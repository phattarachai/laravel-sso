<?php

namespace Phattarachai\Sso\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Phattarachai\Sso\Owner;

final class InstallCommand extends Command
{
    private const array ENV_KEYS = [
        'SSO_ALLOWED_EMAILS',
        'SSO_LOGIN_AS',
        'GOOGLE_CLIENT_ID',
        'GOOGLE_CLIENT_SECRET',
    ];

    protected $signature = 'sso:install';

    protected $description = 'Add the SSO keys to .env.example, check the owner account, and print the redirect URI to register';

    public function handle(Owner $owner): int
    {
        $this->addEnvExampleKeys();

        $problems = $this->problems($owner);

        $this->line('Register this redirect URI on the Google OAuth client: '.route('sso.callback'));
        $this->line('Every allowed account signs in as: '.($owner->email() ?? '—'));

        if ($problems !== []) {
            $this->components->bulletList($problems);

            return self::FAILURE;
        }

        $this->components->info('SSO is ready.');

        return self::SUCCESS;
    }

    private function addEnvExampleKeys(): void
    {
        $path = base_path('.env.example');

        if (! File::exists($path)) {
            return;
        }

        $contents = File::get($path);
        $missing = array_filter(self::ENV_KEYS, fn (string $key): bool => ! preg_match("/^{$key}=/m", $contents));

        if ($missing === []) {
            return;
        }

        $lines = implode(PHP_EOL, array_map(fn (string $key): string => "{$key}=", $missing));
        File::append($path, PHP_EOL.$lines.PHP_EOL);
        $this->components->info('Added '.implode(', ', $missing).' to .env.example.');
    }

    /**
     * @return list<string>
     */
    private function problems(Owner $owner): array
    {
        $usesGoogle = config('sso.driver') === 'google';

        return array_values(array_filter([
            $owner->allowedEmails() === [] ? 'SSO_ALLOWED_EMAILS is empty.' : null,
            $owner->email() !== null && $owner->user() === null ? "No user row has the owner email [{$owner->email()}]." : null,
            $usesGoogle && ! config('services.google.client_id') ? 'GOOGLE_CLIENT_ID is not set.' : null,
            $usesGoogle && ! config('services.google.client_secret') ? 'GOOGLE_CLIENT_SECRET is not set.' : null,
        ]));
    }
}
