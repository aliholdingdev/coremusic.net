<?php declare(strict_types=1);

namespace CoreMusic\Exception;

final class ConflictException extends BaseCoreMusicException
{
    private function __construct(string $message, string $errorCode)
    {
        parent::__construct($message, $errorCode, 409);
    }

    public static function usernameAlreadyExists(): self
    {
        return new self('Bu kullanıcı adı zaten alınmış.', 'USERNAME_EXISTS');
    }

    public static function emailAlreadyExists(): self
    {
        return new self('Bu e-posta adresi zaten kayıtlı.', 'EMAIL_EXISTS');
    }
}
