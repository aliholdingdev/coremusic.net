<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Exception;

use RuntimeException;

class AuthorizationException extends RuntimeException
{
    public static function forbidden(string $message = 'Yetersiz yetki.'): self
    {
        return new self($message, 403);
    }

    public static function insufficientRole(string $requiredRole): self
    {
        return new self("Bu islem icin {$requiredRole} yetkisi gereklidir.", 403);
    }
}
