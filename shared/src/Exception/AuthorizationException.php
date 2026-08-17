<?php declare(strict_types=1);

namespace CoreMusic\Exception;

final class AuthorizationException extends BaseCoreMusicException
{
    private function __construct(string $message, string $errorCode)
    {
        parent::__construct($message, $errorCode, 403);
    }

    public static function forbidden(): self
    {
        return new self('Bu kaynağa erişim yetkiniz yok.', 'FORBIDDEN');
    }

    public static function insufficientRole(string $role): self
    {
        return new self("Bu işlem için {$role} rolü gerekli.", 'INSUFFICIENT_ROLE');
    }
}
