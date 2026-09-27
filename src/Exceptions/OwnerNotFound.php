<?php

namespace Phattarachai\Sso\Exceptions;

use RuntimeException;

final class OwnerNotFound extends RuntimeException
{
    public static function for(?string $email): self
    {
        return new self($email === null
            ? 'SSO has no owner: set SSO_ALLOWED_EMAILS (and optionally SSO_LOGIN_AS).'
            : "SSO owner [{$email}] has no user row: set SSO_LOGIN_AS to an existing user's email.");
    }
}
