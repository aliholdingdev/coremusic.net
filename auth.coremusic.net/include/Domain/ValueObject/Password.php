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
    /** Asgari şifre uzunluğu (AuthService::MIN_PASSWORD_LENGTH ile aynı değer). */
    private const MIN_PASSWORD_LENGTH = 8;

    private function __construct(
        private readonly string $raw,
    ) {}

    /**
     * Ham şifreden Password oluştur.
     *
     * Faz 1b: asgari uzunluk 8 (kullanıcı kararı; AuthService ile aynı kural).
     * argon2id parametreleri değişmez (memory 65536 / time 4 / threads 2).
     */
    public static function create(string $rawPassword): self
    {
        if (strlen($rawPassword) < self::MIN_PASSWORD_LENGTH) {
            throw \CoreMusic\Exception\ValidationException::passwordTooShort(self::MIN_PASSWORD_LENGTH);
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
