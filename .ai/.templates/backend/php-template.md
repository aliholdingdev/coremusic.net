---
type: template
category: backend
title: "PHP 8.4 Backend Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: PHP 8.4, PDO, strict_types
---

# PHP 8.4 Backend Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-002-pdo-mandatory-no-orm]] · [[ADR-010-csrf-protection-strategy]]

## 1. Amaç

CoreMusic backend geliştirme için PHP 8.4 şablonu. strict_types, PDO prepared statements, middleware pipeline ve OWASP uyumlu güvenlik standartları dahil.

**Kapsam:** API endpoint'leri, controller'lar, service'ler, repository'ler, middleware'ler, CLI script'leri.
**Kapsam Dışı:** Frontend JS/CSS (→ [[js-template]], [[css-template]]), Database migration (→ [[migration-template]]).

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| PHP | 8.4+ | Backend runtime | php.net |
| PDO | — | Database abstraction | php.net/pdo |
| MySQL | 9+ | Veritabanı | mysql.com |
| APCu | — | In-memory cache | pecl.php.net |
| OpenSSL | 3.0+ | Şifreleme | openssl.org |

*Kaynak: PHP 8.4 Manual (php.net) — 2026-08-06'da doğrulandı*

### 2.1 Zorunlu Bağımlılıklar

| Bağımlılık | Amaç | Zorunlu mu? |
|------------|------|-------------|
| `ext-pdo` | Database erişimi | ✅ Evet |
| `ext-pdo_mysql` | MySQL driver | ✅ Evet |
| `ext-openssl` | AES-256-GCM şifreleme | ✅ Evet |
| `ext-json` | JSON işleme | ✅ Evet |
| `ext-mbstring` | String işlemleri | ✅ Evet |
| `ext-apcu` | Cache (rate limiting) | ⚠️ Önerilir |

## 3. Code Standards

### 3.1 Dosya Yapısı : music. home. car.  pro. studio. auth or bu subdomainlerden biri 

```text
├─**projects-folder**
├── config/
├── include/                     # Ana uygulama kaynakları
│   │
│   ├── Class/                   # Özel sınıflar
│   │   │
│   │   ├── Exception/           # Custom Exception sınıfları
│   │   ├── Middleware/          # HTTP Middleware katmanı
│   │   └── Helpers/             # Yardımcı fonksiyonlar
│   │
│   ├── Controllers/             # HTTP Request Handler
│   ├── Services/                # Business Logic
│   ├── Repository/              # Database erişim katmanı
│   └── Interfaces/              # Contract tanımları
│
├── pages/                       # Frontend sayfaları
├── test/                        # Test dosyaları
│   ├── Unit/
│   ├── Feature/
│   └── Integration/
│
├── vendor/                      # Composer paketleri
│
├── index.php                    # Entry Point
├── header.php                   # Global Header
├── footer.php                   # Global Footer
├── autoload.php                 # Composer autoload
├── .env                         # Environment değişkenleri
├── .env.example                 # Örnek environment
├── .user.ini                    # PHP runtime ayarları
├── web.config                   # IIS yapılandırması
├── favicon.ico
├── composer.json                # PHP dependency yönetimi
├── composer.lock
└── README.md                    # Proje dokümantasyonu
```

### 3.2 Dosya Yapısı : coremusic-shared

```
shared/
│
├── composer.json
├── composer.lock
├── phpunit.xml
├── .phpunit.result.cache
│
├── src/
│   │
│   ├── Bootstrap/
│   ├── CacheManager/
│   ├── ConfigManager/
│   ├── DatabaseManager/
│   ├── DeviceManager/
│   ├── Exception/
│   ├── Interfaces/
│   ├── Middleware/
│   ├── PageRouter/
│   └── Security/
│
├── tests/
└── vendor/
```

### 3.2 Dosya Başlangıç Kalıbı

```php
<?php
declare(strict_types=1);

/**
 * {DOSYA_AÇIKLAMASI}
 *
 * @file {dosya_adi}.php
 * @version 3.0.0
 * @see ADR-{ilgili_adr}
 */

namespace CoreMusic\{Domain};

// Zorunlu: Hiçbir external import yoksa bile bu satır olmalı
```

### 3.3 Class Design

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Repository;

use PDO;
use PDOException;
use CoreMusic\Exception\DatabaseException;

/**
 * User repository — database erişim katmanı.
 *
 * @see ADR-002-pdo-mandatory-no-orm
 * @see ADR-040-database-authority
 */
class UserRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    /**
     * Kullanıcıyı ID ile getir.
     *
     * @param int $id Kullanıcı ID
     * @return array{int, string, string, string}|null
     * @throws DatabaseException
     */
    public function findById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, email, username, created_at
                 FROM coremusic_auth.users
                 WHERE id = :id AND is_deleted = 0'
            );
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                "User fetch failed: {$e->getMessage()}",
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Email ile kullanıcı getir.
     *
     * @param string $email Kullanıcı email
     * @return array{int, string, string, string}|null
     * @throws DatabaseException
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, email, username, password_hash, created_at
                 FROM coremusic_auth.users
                 WHERE email = :email AND is_deleted = 0'
            );
            $stmt->execute([':email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                "User fetch by email failed: {$e->getMessage()}",
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Yeni kullanıcı oluştur.
     *
     * @param array{email: string, username: string, password_hash: string} $data
     * @return int Yeni kullanıcı ID
     * @throws DatabaseException
     */
    public function create(array $data): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO coremusic_auth.users (email, username, password_hash, created_at)
                 VALUES (:email, :username, :password_hash, NOW())'
            );
            $stmt->execute([
                ':email' => $data['email'],
                ':username' => $data['username'],
                ':password_hash' => $data['password_hash'],
            ]);

            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new DatabaseException(
                "User creation failed: {$e->getMessage()}",
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Kullanıcıyı soft-delete yap.
     *
     * @param int $id Kullanıcı ID
     * @return bool Başarılı mı
     * @throws DatabaseException
     */
    public function softDelete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE coremusic_auth.users
                 SET is_deleted = 1, updated_at = NOW()
                 WHERE id = :id AND is_deleted = 0'
            );
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new DatabaseException(
                "User soft delete failed: {$e->getMessage()}",
                (int) $e->getCode(),
                $e
            );
        }
    }
}
```

### 3.4 Service Layer

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Service;

use CoreMusic\Repository\UserRepository;
use CoreMusic\Exception\ValidationException;
use CoreMusic\Exception\AuthException;

/**
 * User service — business logic katmanı.
 *
 * @see ADR-022-database-hardened-security
 */
class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /**
     * Kayıt ol.
     *
     * @param string $email
     * @param string $username
     * @param string $password
     * @return int Yeni kullanıcı ID
     * @throws ValidationException
     */
    public function register(
        string $email,
        string $username,
        string $password
    ): int {
        // Input validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Geçersiz email adresi');
        }

        if (strlen($username) < 3 || strlen($username) > 32) {
            throw new ValidationException('Kullanıcı adı 3-32 karakter olmalı');
        }

        if (strlen($password) < 8) {
            throw new ValidationException('Şifre en az 8 karakter olmalı');
        }

        // Email uniqueness check
        $existing = $this->userRepository->findByEmail($email);
        if ($existing !== null) {
            throw new ValidationException('Bu email zaten kayıtlı');
        }

        // Password hashing (Argon2id — ADR-022)
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,  // 64MB
            'time_cost' => 4,
            'threads' => 2,
        ]);

        return $this->userRepository->create([
            'email' => $email,
            'username' => $username,
            'password_hash' => $passwordHash,
        ]);
    }

    /**
     * Giriş yap.
     *
     * @param string $email
     * @param string $password
     * @return array{int, string, string} [id, email, username]
     * @throws AuthException
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            // Timing-safe comparison — user enumeration önleme
            password_verify($password, '$argon2id$v=19$m=65536,t=4,p=2$fake$hash');
            throw new AuthException('Geçersiz email veya şifre');
        }

        if (!password_verify($password, $user['password_hash'])) {
            throw new AuthException('Geçersiz email veya şifre');
        }

        // Password hash upgrade check
        if (password_needs_rehash($user['password_hash'], PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 2,
        ])) {
            $newHash = password_hash($password, PASSWORD_ARGON2ID, [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 2,
            ]);
            // Hash upgrade — silent update
            $this->userRepository->updatePasswordHash($user['id'], $newHash);
        }

        return [
            $user['id'],
            $user['email'],
            $user['username'],
        ];
    }
}
```

### 3.5 Error Handling

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Exception;

/**
 * Base exception for CoreMusic.
 */
class CoreMusicException extends \RuntimeException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Database exception.
 */
class DatabaseException extends CoreMusicException {}

/**
 * Validation exception.
 */
class ValidationException extends CoreMusicException {}

/**
 * Auth exception.
 */
class AuthException extends CoreMusicException {}

/**
 * Rate limit exception.
 */
class RateLimitException extends CoreMusicException
{
    public static function registerRateLimited(): static
    {
        return new static('Kayıt rate limit aşıldı. Lütfen 60 saniye bekleyin.', 429);
    }

    public static function loginRateLimited(): static
    {
        return new static('Giriş deneme limiti aşıldı. Lütfen 300 saniye bekleyin.', 429);
    }
}
```

### 3.6 Logging

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Service;

/**
 * Structured logging service.
 *
 * Log seviyeleri: DEBUG, INFO, WARN, ERROR, CRITICAL
 * Format: [TIMESTAMP] [LEVEL] [MODULE] MESSAGE
 */
class LogService
{
    private string $logPath;

    public function __construct(string $logPath = '/var/log/coremusic/app.log')
    {
        $this->logPath = $logPath;
    }

    public function info(string $module, string $message): void
    {
        $this->log('INFO', $module, $message);
    }

    public function error(string $module, string $message, ?\Throwable $e = null): void
    {
        $detail = $e !== null ? " | {$e->getFile()}:{$e->getLine()} | {$e->getMessage()}" : '';
        $this->log('ERROR', $module, $message . $detail);
    }

    public function critical(string $module, string $message, ?\Throwable $e = null): void
    {
        $detail = $e !== null ? " | {$e->getFile()}:{$e->getLine()} | {$e->getMessage()}" : '';
        $this->log('CRITICAL', $module, $message . $detail);
    }

    private function log(string $level, string $module, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $entry = "[{$timestamp}] [{$level}] [{$module}] {$message}" . PHP_EOL;
        file_put_contents($this->logPath, $entry, FILE_APPEND | LOCK_EX);
    }
}
```

## 4. Security Considerations

### 4.1 Input Validation

| Girdi Tipi | Validation | ADR |
|------------|-----------|-----|
| Email | `filter_var(FILTER_VALIDATE_EMAIL)` | ADR-022 |
| Username | strlen 3-32, alphanumeric + _ | ADR-022 |
| Password | min 8 char, Argon2id hash | ADR-022 |
| ID | `intval()`, positive integer | ADR-040 |
| Sort order | Whitelist: `['ASC', 'DESC']` | ADR-040 |

```php
// ✅ DOĞRU: Prepared statement + explicit columns
$stmt = $this->pdo->prepare(
    'SELECT id, email, username FROM coremusic_auth.users WHERE id = :id AND is_deleted = 0'
);
$stmt->execute([':id' => intval($id)]);

// ❌ YANLIŞ: SQL injection risk
$stmt = $this->pdo->query("SELECT * FROM users WHERE id = $id");
```

### 4.2 CSRF Protection

| Kural | Detay | ADR |
|-------|-------|-----|
| Token key | `csrf_token` (kesinlikle `_csrf_token` değil) | ADR-010 |
| Üretim | `random_bytes(32)` → `bin2hex()` | ADR-010 |
| Doğrulama | `hash_equals()` timing-safe | ADR-010 |
| Scope | POST, PUT, DELETE istekleri | ADR-010 |
| Session-bound | Token session'a bağlı, multi-tab safe | ADR-010 |

```php
// CSRF token üretimi
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// CSRF token doğrulama
function verifyCsrfToken(string $token): bool
{
    $stored = $_SESSION['csrf_token'] ?? '';
    return hash_equals($stored, $token);
}
```

### 4.3 Session Management

| Kural | Değer | ADR |
|-------|-------|-----|
| Session name | `COREMUSIC_SESS` | ADR-011 |
| Idle timeout | 3600s (1 saat) | ADR-011 |
| Absolute timeout | 86400s (24 saat) | ADR-011 |
| Cookie params | HttpOnly, SameSite=Lax, Path=/ | ADR-011 |
| Regenerate | Login sonrası session regenerate | ADR-011 |

```php
// Session başlatma
function initSession(): void
{
    session_name('COREMUSIC_SESS');
    session_set_cookie_params([
        'lifetime' => 0,          // Browser session
        'path' => '/',
        'domain' => '',
        'secure' => true,         // HTTPS only
        'httponly' => true,       // JS erişimi yok
        'samesite' => 'Lax',     // CSRF koruması
    ]);
    session_start();

    // Idle timeout kontrolü
    $lastActivity = $_SESSION['last_activity'] ?? 0;
    if (time() - $lastActivity > 3600) {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
    $_SESSION['last_activity'] = time();
}
```

### 4.4 Password Hashing

| Parametre | Değer | Not |
|-----------|-------|-----|
| Algoritma | Argon2id | ADR-022 zorunlu |
| Memory | 64MB (65536 KB) | ADR-022 |
| Time | 4 iterasyon | ADR-022 |
| Threads | 2 | ADR-022 |
| Rehash | `password_needs_rehash()` | Otomatik upgrade |

```php
// ✅ DOĞRU: Argon2id — ADR-022 uyumlu
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 2,
]);

// ❌ YANLIŞ: MD5/SHA1 — ASLA kullanılmaz
$hash = md5($password); // ❌ GÜVENLİK AÇIĞI
```

### 4.5 SQL Injection Prevention

| Kural | Açıklama | ADR |
|-------|----------|-----|
| `SELECT *` yasak | Explicit column listesi zorunlu | ADR-002, ADR-040 |
| Prepared Statement | Parameter binding zorunlu | ADR-002 |
| ORM yasak | PDO direct kullanım | ADR-002 |
| Hard delete yasak | Soft delete (`is_deleted = 1`) | ADR-040 |
| BCNF | 18 BCNF veritabanı normal formu | ADR-040 |

### 4.6 Rate Limiting

```php
// APCu-based rate limiting — ADR-013
function checkRateLimit(string $key, int $maxRequests, int $windowSeconds): bool
{
    $now = time();
    $windowStart = $now - $windowSeconds;

    // Eski istekleri temizle
    $cacheKey = "rate_limit:{$key}";
    $requests = apcu_fetch($cacheKey) ?: [];

    // Pencere dışındaki istekleri kaldır
    $requests = array_filter($requests, fn($ts) => $ts > $windowStart);

    if (count($requests) >= $maxRequests) {
        return false; // Rate limit aşıldı
    }

    $requests[] = $now;
    apcu_store($cacheKey, $requests, $windowSeconds);

    return true; // İzin verildi
}

// Kullanım:
// Login: 5 deneme / 300 saniye
// Register: 3 deneme / 60 saniye
// API: 60 istek / 60 saniye
```

## 5. Performance Notes

### 5.1 Caching Strategies

| Strateji | TTL | Kullanım |
|----------|-----|----------|
| **Page Cache** | 600s | Statik sayfa içerikleri |
| **Data Cache** | 1200s | API yanıtları |
| **Session Cache** | 0 (session) | Oturum verileri |
| **Opcode Cache** | — | OPcache (PHP built-in) |

```php
// Cache-First stratejisi
function getCachedData(string $key, int $ttl, callable $fetcher): mixed
{
    $cached = apcu_fetch($key);
    if ($cached !== false) {
        return $cached;
    }

    $data = $fetcher();
    apcu_store($key, $data, $ttl);

    return $data;
}
```

### 5.2 Database Optimization

| Optimizasyon | Detay |
|--------------|-------|
| Prepared statements | Query plan cache |
| Index usage | WHERE columns index'li |
| Connection pooling | PDO persistent connections |
| Query limit | Pagination zorunlu |
| N+1 prevention | JOIN veya batch loading |

### 5.3 Memory Management

| Kural | Değer |
|-------|-------|
| Memory limit | 128M (PHP default) |
| Max execution | 30s |
| Max input vars | 3000 |
| OPcache | Enabled, 128MB |

## 6. Common Patterns

### 6.1 Repository Pattern

```php
// Interface — SOLID DIP
interface UserRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function create(array $data): int;
    public function softDelete(int $id): bool;
}

// Implementation
class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly PDO $pdo) {}
    // ...
}
```

### 6.2 Service Layer Pattern

```php
// Service interface
interface UserServiceInterface
{
    public function register(string $email, string $username, string $password): int;
    public function login(string $email, string $password): array;
}

// Service implementation
class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}
    // ...
}
```

### 6.3 Middleware Pattern

```php
// Middleware interface
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}

// Middleware pipeline
$middlewares = [
    new SessionManagerMiddleware(),
    new BypassAuthMiddleware(),
    new RateLimiterMiddleware(),
    new AuthMiddleware(),
    new SecurityHeadersMiddleware(),
    new CsrfMiddleware(),
];

$pipeline = array_reduce(
    array_reverse($middlewares),
    fn($next, $mw) => fn($req) => $mw->handle($req, $next),
    fn($req) => $controller->handle($req)
);
```

### 6.4 Factory Pattern

```php
class DatabaseFactory
{
    public static function create(string $dsn, string $user, string $password): PDO
    {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, // Real prepared statements
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ]);

        return $pdo;
    }
}
```

## 7. Edge Cases

### 7.1 Race Conditions

| Senaryo | Risk | Çözüm |
|---------|------|-------|
| Duplicate registration | Email uniqueness | DB unique index + transaction |
| Concurrent session | Session overwrite | Session ID regeneration |
| Rate limit bypass | Multi-tab | APCu atomic increment |

### 7.2 Error Recovery

| Hata Tipi | Kurtarma |
|-----------|----------|
| DB connection lost | Retry 3x, exponential backoff |
| Cache failure | Fallback to DB query |
| Session corruption | Destroy + recreate |
| Rate limit hit | 429 response + Retry-After header |

### 7.3 Fallback Strategies

```php
// Cache fallback
function getDataWithFallback(int $id): array
{
    try {
        $cached = apcu_fetch("user:{$id}");
        if ($cached !== false) {
            return $cached;
        }
    } catch (\Exception) {
        // Cache down — continue to DB
    }

    return $this->userRepository->findById($id);
}
```

## 8. Testing Requirements

### 8.1 PHPUnit Structure

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Repository;

use PHPUnit\Framework\TestCase;
use PDO;
use CoreMusic\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->repository = new UserRepository($this->pdo);
    }

    public function testFindByIdReturnsUser(): void
    {
        // Arrange
        $this->pdo->exec(
            'CREATE TABLE users (id INTEGER PRIMARY KEY, email TEXT, username TEXT, created_at TEXT, is_deleted INTEGER DEFAULT 0)'
        );
        $this->pdo->exec(
            "INSERT INTO users (id, email, username, created_at) VALUES (1, 'test@example.com', 'testuser', '2026-01-01')"
        );

        // Act
        $result = $this->repository->findById(1);

        // Assert
        $this->assertNotNull($result);
        $this->assertSame('test@example.com', $result['email']);
    }

    public function testFindByIdReturnsNullForDeleted(): void
    {
        // Arrange
        $this->pdo->exec(
            'CREATE TABLE users (id INTEGER PRIMARY KEY, email TEXT, username TEXT, created_at TEXT, is_deleted INTEGER DEFAULT 0)'
        );
        $this->pdo->exec(
            "INSERT INTO users (id, email, username, created_at, is_deleted) VALUES (1, 'test@example.com', 'testuser', '2026-01-01', 1)"
        );

        // Act
        $result = $this->repository->findById(1);

        // Assert
        $this->assertNull($result);
    }
}
```

### 8.2 Mocking

```php
// Mock repository for service tests
$mockRepo = $this->createMock(UserRepositoryInterface::class);
$mockRepo->method('findByEmail')->willReturn(null);

$service = new UserService($mockRepo);
```

### 8.3 Coverage Targets

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Repository | ≥80% | ≥90% |
| Service | ≥80% | ≥90% |
| Middleware | ≥80% | ≥90% |
| Controller | ≥70% | ≥80% |

## 9. Troubleshooting

### 9.1 Sıkça Görülen Hatalar

| Hata | Neden | Çözüm |
|------|-------|-------|
| `PDOException: SQLSTATE[HY000]` | DB bağlantı hatası | DSN, user, password kontrol |
| `PDOException: Column not found` | SELECT * yasak | Explicit column listesi |
| `Warning: Cannot modify header` | Output gönderilmiş | `ob_start()` kullan |
| `Session start failed` | Cookie zaten gönderilmiş | Session'dan önce başlat |
| `Argon2id memory error` | memory_cost çok yüksek | 65536 (64MB) kullan |

### 9.2 Debug Komutları

```bash
# PHP syntax kontrolü
php -l src/Controller/AuthController.php

# PHPUnit çalıştır
vendor/bin/phpunit --filter UserRepositoryTest

# OPcache durumu
php -r "print_r(opcache_get_status());"

# APCu durumu
php -r "print_r(apcu_cache_info());"

# Session debugging
php -r "session_start(); print_r($_SESSION);"
```

### 9.3 Log Analizi

```bash
# Son 50 hata
tail -50 /var/log/coremusic/app.log | grep ERROR

# Rate limit ihlalleri
grep "rate_limit" /var/log/coremusic/app.log | tail -20

# Auth hataları
grep "AuthException" /var/log/coremusic/app.log
```

## 10. Hard Guardrails

| # | Kural | Açıklama | İlgili ADR |
|---|-------|----------|------------|
| 1 | **strict_types=1** | Her PHP dosyasında zorunlu | PHP 8.4 |
| 2 | **PDO Mandatory** | ORM yasak, PDO prepared statement | ADR-002 |
| 3 | **SELECT * Yasak** | Explicit column listesi zorunlu | ADR-040 |
| 4 | **Soft Delete** | Hard delete yasak | ADR-040 |
| 5 | **Argon2id** | Password hashing — MD5/SHA yasak | ADR-022 |
| 6 | **csrf_token** | Token key adı değişmez | ADR-010 |
| 7 | **CSRF Timing** | DOM patch sonrası token güncelle | ADR-010 |
| 8 | **Session COREMUSIC_SESS** | Session adı değişmez | ADR-011 |
| 9 | **Layer Violation Yasak** | L0→L3 import yok | ADR-008 |
| 10 | **Zero Code Before Plan** | Plan onayı olmadan kod yok | ADR-007 |

## 11. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| **Dosya** | PascalCase.php | `UserRepository.php` |
| **Class** | PascalCase | `UserRepository` |
| **Method** | camelCase | `findById()` |
| **Variable** | camelCase | `$userRepository` |
| **Constant** | UPPER_SNAKE_CASE | `MAX_RETRY_COUNT` |
| **Interface** | I + PascalCase | `UserRepositoryInterface` |
| **Namespace** | PascalCase | `CoreMusic\Repository` |
| **Table** | coremusic_{domain} | `coremusic_auth.users` |
| **Column** | snake_case | `created_at` |
| **Parameter** | :param | `:user_id` |

## 12. Common Anti-Patterns

| Anti-Pattern | Neden Yasak | Doğru Kullanım |
|-------------|-------------|----------------|
| `SELECT *` | Performance + security | Explicit columns |
| ORM | ADR-002 yasaklıyor | PDO prepared |
| `md5($pass)` | Güvenli değil | Argon2id |
| `mysql_*` | Deprecated | PDO |
| Hard delete | Data loss risk | Soft delete |
| `$_GET` direct | Injection risk | Prepared statement |
| Magic numbers | Maintainability | Named constants |
| Nested callbacks | Readability | Early return + guard clause |

## 13. Related Documents

- [[php-template]] — Bu dosya (PHP 8.4)
- [[js-template]] — JavaScript ES6+ template
- [[css-template]] — CSS ITCSS template
- [[cpp-template]] — C++20 template
- [[phpunit-template]] — PHPUnit test template
- [[Query-Template]] — SQL query template
- [[migration-template]] — Database migration template
- [[ADR-002-pdo-mandatory-no-orm]] — PDO mandatory, ORM yasak
- [[ADR-010-csrf-protection-strategy]] — CSRF koruma stratejisi
- [[ADR-011-session-management]] — Session yönetimi
- [[ADR-012-csp-nonce-strict-dynamic]] — CSP nonce
- [[ADR-013-rate-limiting-apcu]] — Rate limiting
- [[ADR-022-database-hardened-security]] — DB güvenlik
- [[ADR-040-database-authority]] — 18 BCNF DB otoritesi

## 14. Cross-References

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § 3.3 Class Design | [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory |
| § 4.1 Input Validation | [[ADR-022-database-hardened-security]] | Security |
| § 4.2 CSRF | [[ADR-010-csrf-protection-strategy]] | CSRF token |
| § 4.3 Session | [[ADR-011-session-management]] | Session lifecycle |
| § 4.5 SQL Injection | [[ADR-040-database-authority]] | DB authority |
| § 8 Testing | [[phpunit-template]] | Test standards |

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~550+ |
| **Frontmatter** | ✅ Tamamlandı |
| **PHP 8.4** | ✅ Uyumlu |
| **strict_types** | ✅ Zorunlu |
| **ADR Uyumlu** | ✅ 002, 010, 011, 012, 013, 022, 040 |
| **Security Sections** | ✅ 6 bölüm |
| **Performance Sections** | ✅ 3 bölüm |
| **Edge Cases** | ✅ 3 bölüm |
| **Troubleshooting** | ✅ 3 bölüm |
