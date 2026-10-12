<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Entity;

/**
 * User Entity — Domain katmanının temel nesnesi.
 *
 * Auth servisi içindeki tüm iş mantığı bu entity üzerinden çalışır.
 * Array yerine typed property kullanılır.
 */
final class User
{
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly string $email,
        public readonly string $passwordHash,
        public readonly string $displayName,
        public readonly string $gender,
        public readonly string $avatarUrl,
        public readonly string $accountType,
        public readonly bool $isActive,
        public readonly bool $isBanned,
        public readonly ?string $lastLoginAt,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    /**
     * DBC'den dönen array'den User entity'si oluştur.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: $row['id'],
            username: $row['username'],
            email: $row['email'],
            passwordHash: $row['password_hash'],
            displayName: $row['display_name'] ?? $row['username'],
            gender: $row['gender'] ?? 'neutral',
            avatarUrl: $row['avatar_url'] ?? '',
            accountType: $row['account_type'] ?? 'free',
            isActive: (bool)($row['is_active'] ?? true),
            isBanned: (bool)($row['is_banned'] ?? false),
            lastLoginAt: $row['last_login_at'] ?? null,
            createdAt: $row['created_at'] ?? '',
            updatedAt: $row['updated_at'] ?? '',
        );
    }

    /**
     * Hassas verileri (password_hash) çıkararak dizi döndür.
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'username'     => $this->username,
            'email'        => $this->email,
            'display_name' => $this->displayName,
            'gender'       => $this->gender,
            'avatar_url'   => $this->avatarUrl,
            'account_type' => $this->accountType,
        ];
    }

    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
