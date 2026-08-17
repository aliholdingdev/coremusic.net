<?php declare(strict_types=1);

namespace CoreMusic\Exception;

final class AuthenticationException extends BaseCoreMusicException
{
    private function __construct(string $message, string $errorCode)
    {
        parent::__construct($message, $errorCode, 401);
    }

    public static function invalidCredentials(): self
    {
        return new self('Geçersiz kullanıcı adı veya şifre.', 'INVALID_CREDENTIALS');
    }

    public static function banned(): self
    {
        return new self('Hesabınız askıya alınmıştır.', 'ACCOUNT_BANNED');
    }

    public static function genderMismatch(string $expected): self
    {
        return new self("Cinsiyet uyuşmazlığı: {$expected}", 'GENDER_MISMATCH');
    }

    public static function invalidSessionKey(): self
    {
        return new self('Geçersiz oturum anahtarı.', 'INVALID_SESSION_KEY');
    }
}
