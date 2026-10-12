<?php declare(strict_types=1);

namespace CoreMusic\Exception;

final class ValidationException extends BaseCoreMusicException
{
    /** @var array<string, string> */
    private array $errors;

    private function __construct(string $message, string $errorCode, array $errors = [])
    {
        parent::__construct($message, $errorCode, 422);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function emptyFields(): self
    {
        return new self('E-posta ve şifre zorunludur.', 'EMPTY_FIELDS');
    }

    public static function multiple(array $errors): self
    {
        $first = reset($errors) ?: 'Doğrulama hatası.';
        return new self($first, 'VALIDATION_ERRORS', $errors);
    }

    public static function invalidEmail(): self
    {
        return new self('Geçerli bir e-posta adresi girin.', 'INVALID_EMAIL');
    }

    public static function invalidToken(): self
    {
        return new self('Geçersiz token.', 'INVALID_TOKEN');
    }

    public static function tokenExpired(): self
    {
        return new self('Token süresi dolmuş.', 'TOKEN_EXPIRED');
    }

    public static function passwordTooShort(int $min): self
    {
        return new self("Şifre en az {$min} karakter olmalıdır.", 'PASSWORD_TOO_SHORT');
    }
}
