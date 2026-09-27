<?php

namespace Phattarachai\Sso;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use Phattarachai\Sso\Exceptions\OwnerNotFound;

final readonly class Owner
{
    public function __construct(
        private AuthFactory $auth,
        private Repository $config,
    ) {}

    public function allows(?string $email): bool
    {
        return $email !== null && in_array(Str::lower(trim($email)), $this->allowedEmails(), true);
    }

    /**
     * @return list<string>
     */
    public function allowedEmails(): array
    {
        return Str::of((string) $this->config->get('sso.allowed_emails'))
            ->lower()
            ->explode(',')
            ->map(fn (string $email): string => trim($email))
            ->filter()
            ->values()
            ->all();
    }

    public function email(): ?string
    {
        return $this->config->get('sso.login_as') ?: ($this->allowedEmails()[0] ?? null);
    }

    public function user(): ?Authenticatable
    {
        $email = $this->email();

        if ($email === null) {
            return null;
        }

        return $this->guard()->getProvider()->retrieveByCredentials(['email' => $email]);
    }

    public function logIn(): Authenticatable
    {
        $user = $this->user() ?? throw OwnerNotFound::for($this->email());

        $this->guard()->login($user, remember: true);

        return $user;
    }

    public function logOut(): void
    {
        $this->guard()->logout();
    }

    private function guard(): SessionGuard
    {
        /** @var SessionGuard */
        return $this->auth->guard($this->config->get('sso.guard'));
    }
}
