---
type: architecture
category: auth
title: "Enterprise Auth — Infrastructure Layer"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Infrastructure Layer

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Auth infrastructure katmanındaki persistence, security ve HTTP bileşenlerini tanımlar. Infrastructure katmanı, Domain ve Application katmanlarının teknik detaylarını içerir.

## 2. Persistence (PDO)

### 2.1 PdoUserRepository

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Persistence;

use CoreMusic\Auth\Domain\User;
use CoreMusic\Auth\Domain\ValueObjects\Email;
use CoreMusic\Auth\Domain\ValueObjects\UserId;
use CoreMusic\Auth\Domain\ValueObjects\Password;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use PDO;

class PdoUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly PDO $pdo) {}

    public function findById(string $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, password_hash, is_active, email_verified_at, created_at, updated_at 
             FROM users WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false) {
            return null;
        }
        
        return $this->mapToEntity($row);
    }

    public function findByEmail(Email $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, password_hash, is_active, email_verified_at, created_at, updated_at 
             FROM users WHERE email = :email'
        );
        $stmt->execute(['email' => $email->toString()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false) {
            return null;
        }
        
        return $this->mapToEntity($row);
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (id, email, password_hash, is_active, email_verified_at, created_at, updated_at)
             VALUES (:id, :email, :password_hash, :is_active, :email_verified_at, :created_at, :updated_at)
             ON DUPLICATE KEY UPDATE
             is_active = :is_active,
             email_verified_at = :email_verified_at,
             updated_at = :updated_at'
        );
        
        $stmt->execute([
            'id' => $user->getId()->toString(),
            'email' => $user->getEmail()->toString(),
            'password_hash' => $user->getPassword()->getHash(),
            'is_active' => $user->isActive() ? 1 : 0,
            'email_verified_at' => $user->getEmailVerifiedAt(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function mapToEntity(array $row): User
    {
        return new User(
            id: new UserId($row['id']),
            email: new Email($row['email']),
            password: Password::fromHash($row['password_hash']),
            createdAt: new \DateTimeImmutable($row['created_at']),
            updatedAt: new \DateTimeImmutable($row['updated_at']),
            isActive: (bool) $row['is_active'],
            emailVerifiedAt: $row['email_verified_at'],
        );
    }
}
```

### 2.2 PdoSessionRepository

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Persistence;

use CoreMusic\Auth\Domain\Session;
use CoreMusic\Auth\Domain\ValueObjects\UserId;
use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;
use PDO;

class PdoSessionRepository implements SessionRepositoryInterface
{
    public function __construct(private readonly PDO $pdo) {}

    public function findById(string $id): ?Session
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, created_at, last_activity, ip_address, user_agent, is_valid 
             FROM sessions WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false) {
            return null;
        }
        
        return $this->mapToEntity($row);
    }

    public function findByUserId(string $userId): ?Session
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, created_at, last_activity, ip_address, user_agent, is_valid 
             FROM sessions WHERE user_id = :user_id AND is_valid = 1
             ORDER BY last_activity DESC LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row === false) {
            return null;
        }
        
        return $this->mapToEntity($row);
    }

    public function save(Session $session): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sessions (id, user_id, created_at, last_activity, ip_address, user_agent, is_valid)
             VALUES (:id, :user_id, :created_at, :last_activity, :ip_address, :user_agent, :is_valid)
             ON DUPLICATE KEY UPDATE
             last_activity = :last_activity,
             is_valid = :is_valid'
        );
        
        $stmt->execute([
            'id' => $session->getId(),
            'user_id' => $session->getUserId()->toString(),
            'created_at' => $session->getCreatedAt()->format('Y-m-d H:i:s'),
            'last_activity' => $session->getLastActivity()->format('Y-m-d H:i:s'),
            'ip_address' => $session->getIpAddress(),
            'user_agent' => $session->getUserAgent(),
            'is_valid' => $session->isValid() ? 1 : 0,
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function deleteByUserId(string $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }

    private function mapToEntity(array $row): Session
    {
        return new Session(
            id: $row['id'],
            userId: new UserId($row['user_id']),
            createdAt: new \DateTimeImmutable($row['created_at']),
            lastActivity: new \DateTimeImmutable($row['last_activity']),
            ipAddress: $row['ip_address'],
            userAgent: $row['user_agent'],
            isValid: (bool) $row['is_valid'],
        );
    }
}
```

## 3. Security

### 3.1 Argon2idPasswordHasher

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Security;

use CoreMusic\Auth\Application\PasswordHasherInterface;

class Argon2idPasswordHasher implements PasswordHasherInterface
{
    private const OPTIONS = [
        'memory_cost' => 65536,  // 64MB
        'time_cost' => 4,
        'threads' => 2,
    ];

    public function hash(string $plain): string
    {
        return password_hash($plain, PASSWORD_ARGON2ID, self::OPTIONS);
    }

    public function verify(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, self::OPTIONS);
    }
}
```

### 3.2 JwtTokenManager

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Security;

use CoreMusic\Auth\Domain\Token;
use CoreMusic\Auth\Domain\ValueObjects\UserId;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class JwtTokenManager
{
    private Configuration $config;

    public function __construct(string $privateKeyPath, string $publicKeyPath)
    {
        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($privateKeyPath),
            InMemory::file($publicKeyPath),
        );
    }

    public function create(UserId $userId, array $claims = []): Token
    {
        $now = new \DateTimeImmutable();
        $expiresAt = $now->modify('+1 hour');

        $token = $this->config->builder()
            ->issuedBy('auth.coremusic.net')
            ->issuedAt($now)
            ->expiresAt($expiresAt)
            ->withClaim('user_id', $userId->toString())
            ->withClaim('roles', $claims['roles'] ?? [])
            ->getToken($this->config->signer(), $this->config->signingKey());

        return new Token(
            accessToken: $token->toString(),
            refreshToken: $this->createRefreshToken($userId),
            expiresAt: $expiresAt,
            userId: $userId,
        );
    }

    public function validate(string $tokenString): ?array
    {
        try {
            $token = $this->config->parser()->parse($tokenString);
            
            $this->config->setValidationConstraints(
                new SignedWith($this->config->signer(), $this->config->verificationKey()),
            );
            
            $constraints = $this->config->getValidationConstraints();
            $constraints->validate($token);
            
            return [
                'user_id' => $token->claims()->get('user_id'),
                'roles' => $token->claims()->get('roles'),
                'exp' => $token->claims()->get('exp'),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function createRefreshToken(UserId $userId): string
    {
        $now = new \DateTimeImmutable();
        $expiresAt = $now->modify('+30 days');

        $token = $this->config->builder()
            ->issuedBy('auth.coremusic.net')
            ->issuedAt($now)
            ->expiresAt($expiresAt)
            ->withClaim('user_id', $userId->toString())
            ->withClaim('type', 'refresh')
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }
}
```

### 3.3 CsrfTokenManager

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Security;

class CsrfTokenManager
{
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function validate(string $token, string $sessionToken): bool
    {
        return hash_equals($sessionToken, $token);
    }
}
```

### 3.4 SessionManager

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Security;

class SessionManager
{
    private const SESSION_NAME = 'COREMUSIC_SESS';
    private const IDLE_TIMEOUT = 3600;
    private const ABSOLUTE_TIMEOUT = 86400;
    private const COOKIE_DOMAIN = '.coremusic.net';

    public function start(): void
    {
        session_name(self::SESSION_NAME);
        
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.cookie_domain', self::COOKIE_DOMAIN);
        ini_set('session.cookie_path', '/');
        ini_set('session.gc_maxlifetime', (string) self::ABSOLUTE_TIMEOUT);
        
        session_start();
    }

    public function create(\CoreMusic\Auth\Domain\User $user): string
    {
        $sessionId = bin2hex(random_bytes(32));
        
        $_SESSION['session_id'] = $sessionId;
        $_SESSION['user_id'] = $user->getId()->toString();
        $_SESSION['email'] = $user->getEmail()->toString();
        $_SESSION['roles'] = $user->getRoles();
        $_SESSION['last_activity'] = time();
        $_SESSION['created_at'] = time();
        
        return $sessionId;
    }

    public function destroy(): void
    {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function isValid(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            if ($elapsed > self::IDLE_TIMEOUT) {
                $this->destroy();
                return false;
            }
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
}
```

## 4. HTTP

### 4.1 AuthApiClient

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Auth\Infrastructure\Http;

use GuzzleHttp\Client;

class AuthApiClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://auth.coremusic.net',
            'timeout' => 3,
            'verify' => true,
        ]);
    }

    public function checkSession(string $sessionId): ?array
    {
        try {
            $response = $this->client->get('/api/session/check', [
                'headers' => [
                    'Cookie' => 'COREMUSIC_SESS=' . $sessionId,
                ],
            ]);
            
            $body = json_decode($response->getBody()->getContents(), true);
            
            return $body['valid'] ? $body : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
```

## 5. Infrastructure Rules

| Kural | Açıklama |
|-------|----------|
| PDO prepared statement | SQL injection koruması |
| Argon2id | OWASP uyumlu şifreleme |
| JWT RS256 | Asimetrik imza |
| CSRF hash_equals | Timing attack koruması |
| Session HTTPOnly | XSS koruması |

## 6. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Persistence | 2 (PdoUserRepository, PdoSessionRepository) |
| Security | 4 (Argon2id, Jwt, Csrf, Session) |
| HTTP | 1 (AuthApiClient) |
| Clean Architecture | ✅ Infrastructure bağımlılıkları |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
