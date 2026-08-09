---
type: architecture
category: auth
title: "Enterprise Auth — Domain Entities"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Domain Entities

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Auth domain'indeki entity'leri, value object'leri ve repository contract'larını tanımlar. Clean Architecture'ın **Domain katmanı** tamamen bağımsızdır — hiçbir framework veya altyapı bağımlılığı yoktur.

## 2. Entity Tanımları

### 2.1 User Entity

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

class User
{
    public function __construct(
        private readonly UserId $id,
        private readonly Email $email,
        private readonly Password $password,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
        private bool $isActive = true,
        private ?string $emailVerifiedAt = null,
    ) {}

    public function getId(): UserId { return $this->id; }
    public function getEmail(): Email { return $this->email; }
    public function getPassword(): Password { return $this->password; }
    public function isActive(): bool { return $this->isActive; }
    public function isEmailVerified(): bool { return $this->emailVerifiedAt !== null; }
    
    public function activate(): void { $this->isActive = true; $this->updatedAt = new \DateTimeImmutable(); }
    public function deactivate(): void { $this->isActive = false; $this->updatedAt = new \DateTimeImmutable(); }
    public function verifyEmail(): void { $this->emailVerifiedAt = (new \DateTimeImmutable())->format('Y-m-d H:i:s'); }
}
```

### 2.2 Role Entity

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

class Role
{
    public const STANDARD = 'standard';
    public const PREMIUM = 'premium';
    public const STUDIO = 'studio';
    public const CAR = 'car';
    public const ADMIN = 'admin';
    public const SYSTEM = 'system';

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $description,
        private readonly int $level,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getLevel(): int { return $this->level; }
    
    public function hasLevel(int $requiredLevel): bool
    {
        return $this->level >= $requiredLevel;
    }
}
```

### 2.3 Permission Entity

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

class Permission
{
    public const MUSIC_READ = 'music:read';
    public const MUSIC_WRITE = 'music:write';
    public const MEDIA_READ = 'media:read';
    public const MEDIA_WRITE = 'media:write';
    public const ADMIN_READ = 'admin:read';
    public const ADMIN_WRITE = 'admin:write';
    public const SYSTEM_READ = 'system:read';
    public const SYSTEM_WRITE = 'system:write';

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $resource,
        private readonly string $action,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getResource(): string { return $this->resource; }
    public function getAction(): string { return $this->action; }
}
```

### 2.4 Session Entity

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

class Session
{
    private const IDLE_TIMEOUT = 3600;
    private const ABSOLUTE_TIMEOUT = 86400;

    public function __construct(
        private readonly string $id,
        private readonly UserId $userId,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $lastActivity,
        private readonly string $ipAddress,
        private readonly string $userAgent,
        private bool $isValid = true,
    ) {}

    public function getId(): string { return $this->id; }
    public function getUserId(): UserId { return $this->userId; }
    public function isValid(): bool { return $this->isValid; }
    
    public function isExpired(): bool
    {
        $elapsed = time() - $this->lastActivity->getTimestamp();
        return $elapsed > self::IDLE_TIMEOUT;
    }
    
    public function isAbsoluteExpired(): bool
    {
        $elapsed = time() - $this->createdAt->getTimestamp();
        return $elapsed > self::ABSOLUTE_TIMEOUT;
    }
    
    public function refresh(): void
    {
        $this->lastActivity = new \DateTimeImmutable();
    }
    
    public function invalidate(): void
    {
        $this->isValid = false;
    }
}
```

### 2.5 Token Entity

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

class Token
{
    public function __construct(
        private readonly string $accessToken,
        private readonly string $refreshToken,
        private readonly \DateTimeImmutable $expiresAt,
        private readonly UserId $userId,
    ) {}

    public function getAccessToken(): string { return $this->accessToken; }
    public function getRefreshToken(): string { return $this->refreshToken; }
    public function getUserId(): UserId { return $this->userId; }
    
    public function isExpired(): bool
    {
        return new \DateTimeImmutable() >= $this->expiresAt;
    }
}
```

## 3. Value Objects

### 3.1 Email

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObjects;

class Email
{
    public function __construct(private readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email: {$value}");
        }
    }

    public function toString(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
}
```

### 3.2 Password

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObjects;

class Password
{
    private function __construct(private readonly string $hash)
    {
        if (empty($hash)) {
            throw new \InvalidArgumentException("Password hash cannot be empty");
        }
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public static function fromPlain(string $plain): self
    {
        $hash = password_hash($plain, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,  // 64MB
            'time_cost' => 4,
            'threads' => 2,
        ]);
        return new self($hash);
    }

    public function verify(string $plain): bool
    {
        return password_verify($plain, $this->hash);
    }

    public function getHash(): string { return $this->hash; }
}
```

### 3.3 UserId

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObjects;

class UserId
{
    public function __construct(private readonly string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException("UserId cannot be empty");
        }
    }

    public static function generate(): self
    {
        return new self(\Ramsey\Uuid\Uuid::uuid4()->toString());
    }

    public function toString(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
}
```

## 4. Repository Contracts

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Repository;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function save(User $user): void;
    public function delete(string $id): void;
}

interface SessionRepositoryInterface
{
    public function findById(string $id): ?Session;
    public function findByUserId(string $userId): ?Session;
    public function save(Session $session): void;
    public function delete(string $id): void;
    public function deleteByUserId(string $userId): void;
}

interface RoleRepositoryInterface
{
    public function findById(int $id): ?Role;
    public function findByName(string $name): ?Role;
    public function findAll(): array;
}

interface PermissionRepositoryInterface
{
    public function findById(int $id): ?Permission;
    public function findByRoleId(int $roleId): array;
    public function findAll(): array;
}
```

## 5. Domain Rules

| Kural | Açıklama |
|-------|----------|
| User entity bağımsız | Hiçbir framework'e bağımlı değil |
| Value Object immutable | Değiştirilemez nesneler |
| Repository contract | Interface-based dependency inversion |
| Domain events | Gelecek için hazır |

## 6. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Entities | 5 (User, Role, Permission, Session, Token) |
| Value Objects | 3 (Email, Password, UserId) |
| Repository Contracts | 4 |
| Clean Architecture | ✅ Domain bağımsız |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
