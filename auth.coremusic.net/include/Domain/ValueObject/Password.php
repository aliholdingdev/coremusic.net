<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObject;

/**
 * Password ValueObject — Domain'de şifre temsili.
 *
 * Ham şifre ile oluşturulur, hash'lenmiş hali saklanır.
 * Ham şifre asla entity'de saklanmaz.
 */
final class Password
{
    private function __construct(
        private readonly string $raw,
    ) {}

    /**
     * Ham şifreden Password oluştur.
     */
    public static function create(string $rawPassword): self
    {
        if (strlen($rawPassword) < 8) {
            throw \CoreMusic\Exception\ValidationException::passwordTooShort(8);
        }

        return new self($rawPassword);
    }

    /**
     * Pepper uygulanmış şifreyi hash'le.
     */
    public function hashWithPepper(string $pepper): string
    {
        if ($pepper === '') {
            throw \CoreMusic\Exception\ServerException::configError('APP_PEPPER');
        }
        $peppered = hash_hmac('sha256', $this->raw, $pepper);
        return password_hash($peppered, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost'   => 4,
            'threads'     => 2,
        ]);
    }

    /**
     * Ham şifreyi doğrula.
     */
    public function verify(string $hash, string $pepper): bool
    {
        if ($pepper === '') {
            return false;
        }
        $peppered = hash_hmac('sha256', $this->raw, $pepper);
        return password_verify($peppered, $hash);
    }

    public function raw(): string
    {
        return $this->raw;
    }
}
