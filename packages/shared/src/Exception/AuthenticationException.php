<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Exception;

use RuntimeException;

class AuthenticationException extends RuntimeException
{
    public static function invalidCredentials(): self
    {
        return new self('E-posta veya sifre hatali.', 401);
    }

    public static function tokenExpired(): self
    {
        return new self('Token suresi dolmus.', 401);
    }

    public static function tokenInvalid(): self
    {
        return new self('Gecersiz token.', 401);
    }
}
