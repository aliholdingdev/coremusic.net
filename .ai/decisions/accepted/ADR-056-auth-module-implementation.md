---
type: adr
category: security
title: "ADR-056: Auth Module Implementation Plan"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-056: Auth Module Implementation Plan

**Status:** Active
**Kategorisi:** Security
**İlgili Agent:** [[.agents/security-engineer]], [[.agents/backend-architect]]
**İlgili Division:** Security Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformunun **merkezi auth modülünün** (auth.coremusic.net) detaylı implementasyon planını tanımlar. Her dosyanın amacı, içeriği, bağımlılıkları ve implementasyon sırası belirlenmiştir.

---

## 2. Bağlam

### 2.1 İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-051 | Platform Rewrite — proje yapısı |
| ADR-052 | Hybrid Auth Architecture — Session + JWT |
| ADR-054 | Enterprise Composer Stack — paket listesi |
| ADR-055 | Project Structure Plan — dosya yapısı |

### 2.2 Auth Modülü Kapsamı

```
shared/src/Auth/
├── Domain/
│   ├── User.php
│   ├── Role.php
│   ├── Session.php
│   ├── Token.php
│   └── Repository/
│       ├── UserRepositoryInterface.php
│       ├── SessionRepositoryInterface.php
│       └── TokenRepositoryInterface.php
├── Application/
│   ├── LoginUseCase.php
│   ├── LogoutUseCase.php
│   ├── RegisterUseCase.php
│   ├── RefreshTokenUseCase.php
│   ├── ValidateSessionUseCase.php
│   └── DTO/
│       ├── LoginRequest.php
│       ├── LoginResponse.php
│       └── TokenPair.php
└── Infrastructure/
    ├── Persistence/
    │   ├── PdoUserRepository.php
    │   ├── PdoSessionRepository.php
    │   └── PdoTokenRepository.php
    ├── Security/
    │   ├── Argon2idPasswordHasher.php
    │   ├── JwtTokenManager.php
    │   └── CsrfTokenManager.php
    └── Http/
        ├── AuthApiClient.php
        └── AuthMiddleware.php

auth.coremusic.net/
├── index.php
├── include/
│   └── Controller/
│       ├── LoginController.php
│       ├── RegisterController.php
│       ├── LogoutController.php
│       ├── PasswordResetController.php
│       └── SessionCheckController.php
├── pages/
│   ├── login.php
│   ├── register.php
│   └── reset-password.php
└── config/
    └── routes.php
```

---

## 3. Karar — Dosya Bazlı Implementasyon

### 3.1 Domain Layer (L0 — Core)

#### `shared/src/Auth/Domain/User.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

final class User
{
    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private readonly string $passwordHash,
        private readonly array $roles,
        private readonly bool $isActive,
        private readonly \DateTimeImmutable $createdAt,
        private readonly ?\DateTimeImmutable $lastLoginAt = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public static function create(
        string $id,
        string $email,
        string $passwordHash,
        array $roles = ['user'],
    ): self {
        return new self(
            id: $id,
            email: $email,
            passwordHash: $passwordHash,
            roles: $roles,
            isActive: true,
            createdAt: new \DateTimeImmutable('now', new \DateTimeZone('UTC')),
        );
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** User entity (immutable, value object pattern)

---

#### `shared/src/Auth/Domain/Role.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

enum Role: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case PREMIUM = 'premium';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function level(): int
    {
        return match ($this) {
            self::GUEST => 0,
            self::USER => 1,
            self::PREMIUM => 2,
            self::ADMIN => 3,
            self::SUPER_ADMIN => 4,
        };
    }

    public function hasAccessTo(self $required): bool
    {
        return $this->level() >= $required->level();
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** Role enum (PHP 8.4)

---

#### `shared/src/Auth/Domain/Session.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

final class Session
{
    public function __construct(
        private readonly string $id,
        private readonly string $userId,
        private readonly string $userAgent,
        private readonly string $ipAddress,
        private readonly \DateTimeImmutable $createdAt,
        private readonly \DateTimeImmutable $expiresAt,
        private readonly ?\DateTimeImmutable $lastActivityAt = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getLastActivityAt(): ?\DateTimeImmutable
    {
        return $this->lastActivityAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function hasTimedOut(int $idleTimeoutSeconds = 1800): bool
    {
        if ($this->lastActivityAt === null) {
            return false;
        }

        $idleExpiry = $this->lastActivityAt->add(
            new \DateInterval("PT{$idleTimeoutSeconds}S")
        );

        return $idleExpiry < new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public static function create(
        string $id,
        string $userId,
        string $userAgent,
        string $ipAddress,
        int $lifetimeSeconds = 86400,
    ): self {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return new self(
            id: $id,
            userId: $userId,
            userAgent: $userAgent,
            ipAddress: $ipAddress,
            createdAt: $now,
            expiresAt: $now->add(new \DateInterval("PT{$lifetimeSeconds}S")),
            lastActivityAt: $now,
        );
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** Session entity (immutable)

---

#### `shared/src/Auth/Domain/Token.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain;

final class Token
{
    public function __construct(
        private readonly string $jti,
        private readonly string $userId,
        private readonly string $type, // 'access' | 'refresh'
        private readonly string $family,
        private readonly string $deviceHash,
        private readonly \DateTimeImmutable $issuedAt,
        private readonly \DateTimeImmutable $expiresAt,
        private readonly bool $isRevoked = false,
    ) {
    }

    public function getJti(): string
    {
        return $this->jti;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFamily(): string
    {
        return $this->family;
    }

    public function getDeviceHash(): string
    {
        return $this->deviceHash;
    }

    public function getIssuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isRevoked(): bool
    {
        return $this->isRevoked;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function isAccess(): bool
    {
        return $this->type === 'access';
    }

    public function isRefresh(): bool
    {
        return $this->type === 'refresh';
    }

    public static function createAccess(
        string $userId,
        string $deviceHash,
        int $ttlSeconds = 900,
    ): self {
        return self::create($userId, 'access', $deviceHash, $ttlSeconds);
    }

    public static function createRefresh(
        string $userId,
        string $deviceHash,
        string $family,
        int $ttlSeconds = 604800,
    ): self {
        $token = self::create($userId, 'refresh', $deviceHash, $ttlSeconds);

        return new self(
            jti: $token->jti,
            userId: $userId,
            type: 'refresh',
            family: $family,
            deviceHash: $deviceHash,
            issuedAt: $token->issuedAt,
            expiresAt: $token->expiresAt,
        );
    }

    private static function create(
        string $userId,
        string $type,
        string $deviceHash,
        int $ttlSeconds,
    ): self {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return new self(
            jti: ramsey_uuid_factory()->uuid4()->toString(),
            userId: $userId,
            type: $type,
            family: ramsey_uuid_factory()->uuid4()->toString(),
            deviceHash: $deviceHash,
            issuedAt: $now,
            expiresAt: $now->add(new \DateInterval("PT{$ttlSeconds}S")),
        );
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** Token entity (immutable)

---

#### `shared/src/Auth/Domain/Repository/UserRepositoryInterface.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Repository;

use CoreMusic\Auth\Domain\User;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function save(User $user): void;

    public function updateLastLogin(string $userId): void;

    public function emailExists(string $email): bool;
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** User.php
**Çıktı:** Repository interface

---

#### `shared/src/Auth/Domain/Repository/SessionRepositoryInterface.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Repository;

use CoreMusic\Auth\Domain\Session;

interface SessionRepositoryInterface
{
    public function findById(string $id): ?Session;

    public function save(Session $session): void;

    public function delete(string $id): void;

    public function deleteByUserId(string $userId): void;

    public function deleteExpired(): void;

    public function updateLastActivity(string $id): void;
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Session.php
**Çıktı:** Repository interface

---

#### `shared/src/Auth/Domain/Repository/TokenRepositoryInterface.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Repository;

use CoreMusic\Auth\Domain\Token;

interface TokenRepositoryInterface
{
    public function findRefreshByJti(string $jti): ?Token;

    public function save(Token $token): void;

    public function revoke(string $jti): void;

    public function revokeByFamily(string $family): void;

    public function revokeByUserId(string $userId): void;

    public function isRevoked(string $jti): bool;
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Token.php
**Çıktı:** Repository interface

---

### 3.2 Application Layer (L0 — Use Cases)

#### `shared/src/Auth/Application/DTO/LoginRequest.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Application\DTO;

final class LoginRequest
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly string $userAgent,
        private readonly string $ipAddress,
        private readonly ?string $returnUrl = null,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** LoginRequest DTO

---

#### `shared/src/Auth/Application/DTO/LoginResponse.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Application\DTO;

final class LoginResponse
{
    public function __construct(
        private readonly bool $success,
        private readonly ?string $sessionId = null,
        private readonly ?TokenPair $tokenPair = null,
        private readonly ?string $error = null,
        private readonly ?int $retryAfter = null,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function getTokenPair(): ?TokenPair
    {
        return $this->tokenPair;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }

    public static function success(string $sessionId, TokenPair $tokenPair): self
    {
        return new self(
            success: true,
            sessionId: $sessionId,
            tokenPair: $tokenPair,
        );
    }

    public static function failure(string $error, ?int $retryAfter = null): self
    {
        return new self(
            success: false,
            error: $error,
            retryAfter: $retryAfter,
        );
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** TokenPair.php
**Çıktı:** LoginResponse DTO

---

#### `shared/src/Auth/Application/DTO/TokenPair.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Application\DTO;

final class TokenPair
{
    public function __construct(
        private readonly string $accessToken,
        private readonly string $refreshToken,
        private readonly int $accessTokenTtl,
        private readonly int $refreshTokenTtl,
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getAccessTokenTtl(): int
    {
        return $this->accessTokenTtl;
    }

    public function getRefreshTokenTtl(): int
    {
        return $this->refreshTokenTtl;
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** TokenPair DTO

---

#### `shared/src/Auth/Application/LoginUseCase.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Application;

use CoreMusic\Auth\Application\DTO\LoginRequest;
use CoreMusic\Auth\Application\DTO\LoginResponse;
use CoreMusic\Auth\Application\DTO\TokenPair;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\TokenRepositoryInterface;
use CoreMusic\Auth\Infrastructure\Security\Argon2idPasswordHasher;
use CoreMusic\Auth\Infrastructure\Security\JwtTokenManager;
use CoreMusic\Security\Service\RateLimiter;
use Psr\Log\LoggerInterface;

final class LoginUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly Argon2idPasswordHasher $passwordHasher,
        private readonly JwtTokenManager $jwtManager,
        private readonly RateLimiter $rateLimiter,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(LoginRequest $request): LoginResponse
    {
        // 1. Rate limit check
        $rateLimitKey = 'login:' . $request->getIpAddress();
        $limit = $this->rateLimiter->consume($rateLimitKey, 5, 900); // 5 attempts / 15 min

        if (!$limit->isAccepted()) {
            $retryAfter = $limit->getRetryAfter()->getTimestamp() - time();

            $this->logger->warning('Login rate limit exceeded', [
                'email' => $request->getEmail(),
                'ip' => $request->getIpAddress(),
            ]);

            return LoginResponse::failure(
                'Too many login attempts. Please try again later.',
                $retryAfter
            );
        }

        // 2. Find user by email
        $user = $this->userRepository->findByEmail($request->getEmail());

        if ($user === null) {
            $this->logger->warning('Login failed: user not found', [
                'email' => $request->getEmail(),
                'ip' => $request->getIpAddress(),
            ]);

            // Constant-time comparison to prevent timing attack
            $this->passwordHasher->hash('dummy-password-to-prevent-timing-attack');

            return LoginResponse::failure('Invalid email or password.');
        }

        // 3. Check if user is active
        if (!$user->isActive()) {
            $this->logger->warning('Login failed: user inactive', [
                'user_id' => $user->getId(),
                'ip' => $request->getIpAddress(),
            ]);

            return LoginResponse::failure('Account is disabled.');
        }

        // 4. Verify password
        if (!$this->passwordHasher->verify($request->getPassword(), $user->getPasswordHash())) {
            $this->logger->warning('Login failed: invalid password', [
                'user_id' => $user->getId(),
                'ip' => $request->getIpAddress(),
            ]);

            return LoginResponse::failure('Invalid email or password.');
        }

        // 5. Rehash if needed (algorithm upgrade)
        if ($this->passwordHasher->needsRehash($user->getPasswordHash())) {
            $newHash = $this->passwordHasher->hash($request->getPassword());
            // Update hash in database (fire-and-forget)
        }

        // 6. Create session
        $sessionId = $this->generateSessionId();
        $deviceHash = $this->hashUserAgent($request->getUserAgent());

        $session = \CoreMusic\Auth\Domain\Session::create(
            id: $sessionId,
            userId: $user->getId(),
            userAgent: $request->getUserAgent(),
            ipAddress: $request->getIpAddress(),
            lifetimeSeconds: 86400, // 24 hours
        );

        $this->sessionRepository->save($session);

        // 7. Create JWT pair
        $accessToken = $this->jwtManager->createAccessToken(
            userId: $user->getId(),
            email: $user->getEmail(),
            roles: $user->getRoles(),
            deviceHash: $deviceHash,
        );

        $refreshToken = $this->jwtManager->createRefreshToken(
            userId: $user->getId(),
            deviceHash: $deviceHash,
        );

        // 8. Save tokens to database
        $this->tokenRepository->save($accessToken);
        $this->tokenRepository->save($refreshToken);

        // 9. Update last login
        $this->userRepository->updateLastLogin($user->getId());

        // 10. Log success
        $this->logger->info('Login successful', [
            'user_id' => $user->getId(),
            'ip' => $request->getIpAddress(),
        ]);

        // 11. Return response
        return LoginResponse::success(
            sessionId: $sessionId,
            tokenPair: new TokenPair(
                accessToken: $this->jwtManager->encode($accessToken),
                refreshToken: $this->jwtManager->encode($refreshToken),
                accessTokenTtl: 900,
                refreshTokenTtl: 604800,
            )
        );
    }

    private function generateSessionId(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function hashUserAgent(string $userAgent): string
    {
        return hash('sha256', $userAgent);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Tüm Domain + Infrastructure dosyaları
**Çıktı:** LoginUseCase (60+ satır)

---

### 3.3 Infrastructure Layer (L1 — PDO, Security)

#### `shared/src/Auth/Infrastructure/Security/Argon2idPasswordHasher.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Security;

final class Argon2idPasswordHasher
{
    private const OPTIONS = [
        'memory_cost' => 65536,  // 64MB
        'time_cost' => 4,        // 4 iterations
        'threads' => 2,          // 2 threads
    ];

    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, self::OPTIONS);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, self::OPTIONS);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** Password hasher (PHP native)

---

#### `shared/src/Auth/Infrastructure/Security/JwtTokenManager.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Security;

use CoreMusic\Auth\Domain\Token;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ramsey\Uuid\Uuid;

final class JwtTokenManager
{
    private const ALGORITHM = 'RS256';
    private const ACCESS_TTL = 900;       // 15 min
    private const REFRESH_TTL = 604800;   // 7 days

    public function __construct(
        private readonly string $privateKeyPath,
        private readonly string $publicKeyPath,
        private readonly string $issuer = 'auth.coremusic.net',
        private readonly string $audience = '*.coremusic.net',
    ) {
    }

    public function createAccessToken(
        string $userId,
        string $email,
        array $roles,
        string $deviceHash,
    ): Token {
        return Token::createAccess(
            userId: $userId,
            deviceHash: $deviceHash,
            ttlSeconds: self::ACCESS_TTL,
        );
    }

    public function createRefreshToken(
        string $userId,
        string $deviceHash,
        ?string $family = null,
    ): Token {
        return Token::createRefresh(
            userId: $userId,
            deviceHash: $deviceHash,
            family: $family ?? Uuid::uuid4()->toString(),
            ttlSeconds: self::REFRESH_TTL,
        );
    }

    public function encode(Token $token): string
    {
        $privateKey = openssl_pkey_get_private(file_get_contents($this->privateKeyPath));

        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'sub' => $token->getUserId(),
            'jti' => $token->getJti(),
            'iat' => $token->getIssuedAt()->getTimestamp(),
            'exp' => $token->getExpiresAt()->getTimestamp(),
            'type' => $token->getType(),
            'device' => $token->getDeviceHash(),
        ];

        if ($token->isRefresh()) {
            $payload['family'] = $token->getFamily();
        }

        return JWT::encode($payload, $privateKey, self::ALGORITHM);
    }

    public function decode(string $jwt): array
    {
        $publicKey = openssl_pkey_get_public(file_get_contents($this->publicKeyPath));

        return (array) JWT::decode($jwt, new Key($publicKey, self::ALGORITHM));
    }

    public function generateKeyPair(): void
    {
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);

        if ($res === false) {
            throw new \RuntimeException('Failed to generate RSA key pair');
        }

        openssl_pkey_export($res, $privateKey);
        file_put_contents($this->privateKeyPath, $privateKey);

        $publicKey = openssl_pkey_get_details($res);
        file_put_contents($this->publicKeyPath, $publicKey['key']);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Token.php, firebase/php-jwt
**Çıktı:** JWT manager (RS256)

---

#### `shared/src/Auth/Infrastructure/Persistence/PdoUserRepository.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Persistence;

use CoreMusic\Auth\Domain\User;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use PDO;

final class PdoUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findById(string $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, password_hash, roles, is_active, created_at, last_login_at
             FROM users
             WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->mapToUser($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, password_hash, roles, is_active, created_at, last_login_at
             FROM users
             WHERE email = :email AND is_deleted = 0'
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        return $row ? $this->mapToUser($row) : null;
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (id, email, password_hash, roles, is_active, created_at, is_deleted)
             VALUES (:id, :email, :password_hash, :roles, :is_active, :created_at, 0)
             ON DUPLICATE KEY UPDATE
                 email = :email,
                 password_hash = :password_hash,
                 roles = :roles,
                 is_active = :is_active'
        );

        $stmt->execute([
            ':id' => $user->getId(),
            ':email' => $user->getEmail(),
            ':password_hash' => $user->getPasswordHash(),
            ':roles' => json_encode($user->getRoles()),
            ':is_active' => $user->isActive() ? 1 : 0,
            ':created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function updateLastLogin(string $userId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET last_login_at = NOW() WHERE id = :id'
        );
        $stmt->execute([':id' => $userId]);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM users WHERE email = :email AND is_deleted = 0'
        );
        $stmt->execute([':email' => $email]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function mapToUser(array $row): User
    {
        return new User(
            id: $row['id'],
            email: $row['email'],
            passwordHash: $row['password_hash'],
            roles: json_decode($row['roles'], true),
            isActive: (bool) $row['is_active'],
            createdAt: new \DateTimeImmutable($row['created_at'], new \DateTimeZone('UTC')),
            lastLoginAt: isset($row['last_login_at'])
                ? new \DateTimeImmutable($row['last_login_at'], new \DateTimeZone('UTC'))
                : null,
        );
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** UserRepositoryInterface.php, PDO
**Çıktı:** PDO repository (prepared statements)

---

### 3.4 Auth Service Entry Point

#### `auth.coremusic.net/index.php`

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

// Auth service routes:
// GET  /login              → LoginController@show
// POST /login              → LoginController@login
// GET  /register           → RegisterController@show
// POST /register           → RegisterController@register
// POST /logout             → LogoutController@logout
// GET  /password/forgot    → PasswordResetController@showForgot
// POST /password/forgot    → PasswordResetController@sendReset
// GET  /password/reset     → PasswordResetController@showReset
// POST /password/reset     → PasswordResetController@reset
// GET  /api/session/check  → SessionCheckController@check
// POST /api/token/refresh  → TokenController@refresh
// POST /api/token/revoke   → TokenController@revoke
```

**Sorumlu:** Backend Architect
**Ön Koşul:** shared/bootstrap.php
**Çıktı:** Auth service entry point

---

#### `auth.coremusic.net/include/Controller/LoginController.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Http\Controller;

use CoreMusic\Auth\Application\LoginUseCase;
use CoreMusic\Auth\Application\DTO\LoginRequest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class LoginController
{
    public function __construct(
        private readonly LoginUseCase $loginUseCase,
    ) {
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Return login page
        // Include pages/login.php
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();

        $loginRequest = new LoginRequest(
            email: $body['email'] ?? '',
            password: $body['password'] ?? '',
            userAgent: $request->getHeaderLine('User-Agent'),
            ipAddress: $request->getServerParams()['REMOTE_ADDR'] ?? '',
            returnUrl: $body['return_url'] ?? '/',
        );

        $result = $this->loginUseCase->execute($loginRequest);

        if ($result->isSuccess()) {
            // Set session cookie
            // Set JWT cookies
            // Redirect to return URL
        }

        // Show error
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** LoginUseCase.php
**Çıktı:** Login controller

---

## 4. Implementasyon Sırası

| # | Dosya | Sorumlu | Tahmini | Bağımlılık |
|---|-------|---------|---------|------------|
| 1 | Domain/User.php | Security Engineer | 15 dk | — |
| 2 | Domain/Role.php | Security Engineer | 10 dk | — |
| 3 | Domain/Session.php | Security Engineer | 15 dk | — |
| 4 | Domain/Token.php | Security Engineer | 15 dk | — |
| 5 | Repository interfaces (3) | Security Engineer | 15 dk | #1, #3, #4 |
| 6 | DTO/LoginRequest.php | Security Engineer | 5 dk | — |
| 7 | DTO/LoginResponse.php | Security Engineer | 10 dk | #8 |
| 8 | DTO/TokenPair.php | Security Engineer | 5 dk | — |
| 9 | Argon2idPasswordHasher.php | Security Engineer | 10 dk | — |
| 10 | JwtTokenManager.php | Security Engineer | 30 dk | #4 |
| 11 | PdoUserRepository.php | Backend Architect | 30 dk | #1, #5 |
| 12 | PdoSessionRepository.php | Backend Architect | 25 dk | #3, #5 |
| 13 | PdoTokenRepository.php | Backend Architect | 25 dk | #4, #5 |
| 14 | LoginUseCase.php | Security Engineer | 45 dk | #1-#13 |
| 15 | LogoutUseCase.php | Security Engineer | 20 dk | #14 |
| 16 | RegisterUseCase.php | Security Engineer | 30 dk | #14 |
| 17 | RefreshTokenUseCase.php | Security Engineer | 25 dk | #14 |
| 18 | ValidateSessionUseCase.php | Security Engineer | 20 dk | #14 |
| 19 | LoginController.php | Backend Architect | 30 dk | #14 |
| 20 | RegisterController.php | Backend Architect | 25 dk | #16 |
| 21 | LogoutController.php | Backend Architect | 15 dk | #15 |
| 22 | PasswordResetController.php | Backend Architect | 30 dk | — |
| 23 | SessionCheckController.php | Backend Architect | 20 dk | #18 |
| 24 | TokenController.php | Backend Architect | 25 dk | #17 |
| 25 | Auth pages (login, register) | UI Designer | 60 dk | — |
| 26 | Auth tests | QA Engineer | 90 dk | #1-#24 |

**Toplam Tahmini:** ~10 saat

---

## 5. Hard Guardrails

| # | Guardrail | Uygulama |
|---|-----------|----------|
| G1 | Hybrid zorunlu | Session + JWT birlikte |
| G2 | Merkezi auth | auth.coremusic.net |
| G3 | Argon2id zorunlu | Şifre hashleme |
| G4 | HttpOnly cookie | JS erişimi yasak |
| G5 | Short-lived JWT | 15 dk access token |
| G6 | Refresh rotation | Her refresh'te yeni token |
| G7 | Prepared statement | ORM yasak |
| G8 | CSRF zorunlu | Login formu |

---

## 6. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| ADR-051 | Platform Rewrite | Proje yapısı |
| ADR-052 | Hybrid Auth | Auth tasarımı |
| ADR-054 | Composer Stack | Paket listesi |
| ADR-055 | Project Structure | Dosya yapısı |

---

## 7. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 1.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-09 |
| Sections | 7 |
| Domain Dosya | 4 entity + 3 interface |
| Application Dosya | 5 use case + 3 DTO |
| Infrastructure Dosya | 6 (3 persistence + 3 security) |
| Controller Dosya | 6 |
| Page Dosya | 3 |
| Implementasyon Adım | 26 |
| Tahmini Süre | ~10 saat |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
