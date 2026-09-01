<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObject;

/**
 * Email ValueObject — Domain'de e-posta temsili.
 *
 * Geçerlilik kontrolü constructor'da yapılır.
 * İmmutable — değiştirilemez.
 */
final class Email
{
    private function __construct(
        public readonly string $value,
    ) {}

    /**
     * Email oluştur. Geçersizse ValidationException fırlatır.
     */
    public static function create(string $email): self
    {
        $email = trim(strtolower($email));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw \CoreMusic\Exception\ValidationException::invalidEmail();
        }

        return new self($email);
    }

    /**
     * Doğrudan oluştur (validation olmadan — DB'den okuma için).
     */
    public static function unsafe(string $email): self
    {
        return new self(trim(strtolower($email)));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
