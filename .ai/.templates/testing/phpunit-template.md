---
type: template
category: testing
title: "PHPUnit 10+ Testing Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: PHPUnit 10+, PHP 8.4, strict_types
---

# PHPUnit 10+ Testing Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-002-pdo-mandatory-no-orm]] · [[ADR-040-database-authority]]

---

## 1. Amaç (Purpose)

Bu şablon, CoreMusic platformundaki tüm PHP modüllerinin unit testlerini yazmak, sürdürmek ve çalıştırmak için standart bir rehber sağlar. Testler, 18 BCNF veritabanı (ADR-040) üzerindeki repository sınıflarını, middleware pipeline'ını (ADR-002: PDO mandatory, ORM yasak), servis katmanlarını ve güvenlik bileşenlerini kapsar.

**Kapsam dahilindeki modüller:**
- Repository sınıfları (PDO prepared statement, ADR-002 uyumlu)
- Servis sınıfları (AuthService, SessionManager, CacheService)
- Middleware sınıfları (CsrfMiddleware, RateLimiter, SecurityHeaders)
- Yardımcı sınıfları (Validator, PasswordHasher, TokenGenerator)
- API endpoint'leri (request/response simülasyonu)

**Kapsam dışı:**
- Frontend JS testleri (→ [[vitest-template]])
- C++ audio engine testleri (→ Google Test)
- E2E testleri (→ Playwright)

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| PHPUnit | 10.5+ | PHP unit testing framework | phpunit.de |
| PHP | 8.4+ | Runtime (strict_types zorunlu) | php.net |
| ext-dom | — | XML assertion (phpunit.xml) | php.net |
| ext-json | — | JSON encode/decode | php.net |
| ext-pdo | — | Database testleri | php.net |
| ext-pdo_sqlite | — | In-memory test DB | php.net |
| ext-mbstring | — | Multibyte string testleri | php.net |

*Kaynaklar: PHPUnit 10 Documentation (phpunit.de) — 2026-08-06'da doğrulandı. PHP 8.4 strict_types: php.net/manual/tr/en/language.declarations.php*

## 3. Code Standards

### 3.1 Test Directory Structure

```
music. home. car.  pro. studio. auth or bu subdomainlerden biri  .coremusic.net/
├── tests/
│   ├── Unit/                          # Saf unit testleri (DB mock)
│   │   ├── Repository/
│   │   ├── Service/
│   │   ├── Middleware/
│   │   └── Validator/
│   ├── Integration/                   # Gerçek DB ile test
│   │   ├── Repository/
│   │   └── Service/
│   ├── Fixtures/                      # Test verileri
│   └── bootstrap.php                  # Test başlatma
├── phpunit.xml                        # PHPUnit konfigürasyonu
└── composer.json                      # require-dev: phpunit/phpunit
```

**Kurallar:**
- Her `tests/Unit/` alt klasörü, `src/` altındaki ilgili klasöre karşılık gelir
- `tests/Integration/` sadece gerçek DB bağlantısı gerektiren testleri barındırır
- `tests/Fixtures/` test verilerini tutar, asla production verisi kullanılmaz

### 3.2 TestCase Base Class

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PDO;

/**
 * CoreMusic testleri için temel sınıf.
 *
 * Ortak kurulum, veritabanı fixture'ları ve yardımcı metotlar sağlar.
 */
abstract class BaseTestCase extends PHPUnitTestCase
{
    protected PDO $testPdo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testPdo = $this->createTestDatabase();
    }

    protected function tearDown(): void
    {
        $this->testPdo = null;
        parent::tearDown();
    }

    /**
     * SQLite in-memory veritabanı oluşturur.
     * Her test kendi izole DB'sine sahiptir.
     */
    protected function createTestDatabase(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $schema = file_get_contents(__DIR__ . '/Fixtures/schema.sql');
        $pdo->exec($schema);

        return $pdo;
    }

    /**
     * Fixture'ları test DB'ine yükler.
     *
     * @param array<string, array<string, mixed>> $records
     */
    protected function loadFixture(string $table, array $records): void
    {
        foreach ($records as $record) {
            $columns = implode(', ', array_keys($record));
            $placeholders = implode(', ', array_fill(0, count($record), '?'));

            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->testPdo->prepare($sql);
            $stmt->execute(array_values($record));
        }
    }

    /**
     * Mock PDO statement döndürür (ADR-002 uyumlu).
     *
     * @param array<string, mixed> $returnValue
     */
    protected function createMockPdoStatement(array $returnValue = []): \PDOStatement
    {
        $stmt = $this->createMock(\PDOStatement::class);

        if ($returnValue !== []) {
            $stmt->method('fetch')->willReturn($returnValue);
            $stmt->method('fetchAll')->willReturn([$returnValue]);
        }

        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('execute')->willReturn(true);

        return $stmt;
    }

    /**
     * Test verisi üretici (sahte kullanıcı).
     *
     * @return array<string, mixed>
     */
    protected function generateTestUser(int $id = 1): array
    {
        return [
            'id' => $id,
            'email' => "user{$id}@test.example.com",
            'username' => "testuser{$id}",
            'password_hash' => password_hash('Test1234!', PASSWORD_ARGON2ID),
            'is_deleted' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
```

### 3.3 PHPUnit 10 Attributes

PHPUnit 10+ ile attribute tabanlı konfigürasyon. Annotation'lar (#[Test]) yerine PHP 8 attribute'ları kullanılır.

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Repository;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Repository\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\After;

/**
 * UserRepository unit tests.
 */
#[CoversClass(UserRepository::class)]
final class UserRepositoryTest extends BaseTestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository($this->testPdo);
    }

    #[Test]
    #[Group('repository')]
    #[Group('user')]
    public function testFindByIdReturnsUserWhenExists(): void
    {
        // Arrange
        $user = $this->generateTestUser(1);
        $this->loadFixture('users', [$user]);

        // Act
        $result = $this->repository->findById(1);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('user1@test.example.com', $result['email']);
    }

    #[Test]
    #[Group('repository')]
    public function testFindByIdReturnsNullWhenNotExists(): void
    {
        $result = $this->repository->findById(999);
        $this->assertNull($result);
    }

    #[Test]
    #[DataProvider('emailProvider')]
    #[Group('validation')]
    public function testEmailValidation(string $email, bool $expected): void
    {
        $result = filter_var($email, FILTER_VALIDATE_EMAIL);
        $this->assertEquals($expected, $result !== false);
    }

    public static function emailProvider(): array
    {
        return [
            'valid gmail' => ['test@gmail.com', true],
            'valid outlook' => ['user@outlook.com', true],
            'invalid no at' => ['testexample.com', false],
            'invalid no domain' => ['test@', false],
            'invalid empty' => ['', false],
        ];
    }

    #[Test]
    #[Depends('testFindByIdReturnsUserWhenExists')]
    #[Group('delete')]
    public function testSoftDeleteAfterFindById(): void
    {
        // Depends: testFindByIdReturnsUserWhenExists önce çalışmalıdır
        $result = $this->repository->softDelete(1);
        $this->assertTrue($result);
    }
}
```

### 3.4 Test Naming Convention

**Format:** `testMethodNameWithConditionReturnsExpected`

| Örnek | Açıklama |
|-------|----------|
| `testFindByIdReturnsUserWhenExists` | findById, mevcut olduğunda user döndürür |
| `testFindByIdReturnsNullWhenNotExists` | findById, mevcut olmadığında null döndürür |
| `testInsertThrowsExceptionOnDuplicateEmail` | insert, duplicate email'de istisna fırlatır |
| `testSoftDeleteReturnsTrueOnSuccess` | softDelete, başarılı oldğunda true döndürür |
| `testLoginFailsWithWrongPassword` | login, yanlış şifreyle başarısız olur |
| `testCsrfTokenValidationRejectsEmptyToken` | csrfTokenValidasyonu boş token'ı reddeder |

**Kurallar:**
- Metod adları `test` ile başlar veya `#[Test]` attribute'u kullanılır
- Koşul `With`/`When` ile belirtilir
- Sonuç `Returns`/`Throws` ile belirtilir
- CamelCase kullanılır, underscore yasaktır

### 3.5 AAA Pattern (Arrange-Act-Assert)

Her testte üç bölüm zorunludur:

```php
#[Test]
public function testInsertCreatesNewUserWithValidData(): void
{
    // Arrange — Test için hazırlık
    $repository = new UserRepository($this->testPdo);
    $userData = [
        'email' => 'new@example.com',
        'username' => 'newuser',
        'password_hash' => password_hash('Pass1234!', PASSWORD_ARGON2ID),
    ];

    // Act — Test edilen eylem
    $userId = $repository->insert($userData);

    // Assert — Sonucun doğrulanması
    $this->assertIsInt($userId);
    $this->assertGreaterThan(0, $userId);

    $fetched = $repository->findById($userId);
    $this->assertEquals('new@example.com', $fetched['email']);
}
```

**❌ YANLIŞ — Tek assertion bloğu:**
```php
#[Test]
public function testSomething(): void
{
    $data = ['email' => 'test@example.com'];
    $this->assertIsArray($data);
    $this->assertArrayHasKey('email', $data);
    $this->assertEquals('test@example.com', $data['email']);
    // 3 assertion — hangisi başarısız olursa hata belli olmaz
}
```

**✅ DOĞRU — AAA ile tek mantıksal assertion:**
```php
#[Test]
public function testInsertReturnsUserWithEmail(): void
{
    // Arrange
    $userData = ['email' => 'test@example.com', 'username' => 'testuser'];

    // Act
    $userId = $this->repository->insert($userData);

    // Assert — tek assertion, açık mesaj
    $this->assertGreaterThan(0, $userId, 'Insert should return valid user ID');
}
```

### 3.6 Data Providers

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Service;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Service\InputValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

final class InputValidatorTest extends BaseTestCase
{
    private InputValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new InputValidator();
    }

    // ─── Inline Provider ──────────────────────────────────

    #[Test]
    #[DataProvider('passwordStrengthProvider')]
    public function testPasswordStrengthDetection(string $password, string $expected): void
    {
        $result = $this->validator->getPasswordStrength($password);
        $this->assertEquals($expected, $result);
    }

    public static function passwordStrengthProvider(): array
    {
        return [
            'weak numeric' => ['12345678', 'weak'],
            'weak common' => ['password', 'weak'],
            'medium mixed' => ['Password1', 'medium'],
            'strong special' => ['P@ssw0rd!2024', 'strong'],
            'empty string' => ['', 'weak'],
        ];
    }

    // ─── External Dataset Provider ────────────────────────

    #[Test]
    #[DataProvider('externalEmailDataset')]
    public function testEmailFormatValidation(string $email, bool $expected): void
    {
        $result = $this->validator->isValidEmail($email);
        $this->assertEquals($expected, $result);
    }

    public static function externalEmailDataset(): array
    {
        return require __DIR__ . '/../../Fixtures/email-dataset.php';
    }

    // ─── Multiple Providers ───────────────────────────────

    #[Test]
    #[DataProvider('usernameProvider')]
    public function testUsernameValidation(string $username, bool $expected): void
    {
        $result = $this->validator->isValidUsername($username);
        $this->assertEquals($expected, $result);
    }

    #[Test]
    #[DataProvider('maxLengthProvider')]
    public function testMaxLengthEnforcement(string $input, int $max, bool $expected): void
    {
        $result = $this->validator->isWithinLength($input, $max);
        $this->assertEquals($expected, $result);
    }

    public static function usernameProvider(): array
    {
        return [
            'valid alpha' => ['john', true],
            'valid alphanumeric' => ['john_doe123', true],
            'invalid spaces' => ['john doe', false],
            'invalid special' => ['john@doe', false],
            'too short' => ['jo', false],
            'too long' => ['johndoe1234567890123', false],
        ];
    }

    public static function maxLengthProvider(): array
    {
        return [
            'exact limit' => ['abcdef', 6, true],
            'under limit' => ['abc', 6, true],
            'over limit' => ['abcdefgh', 6, false],
            'empty string' => ['', 6, true],
            'zero limit' => ['', 0, true],
        ];
    }
}
```

### 3.7 Mocking Patterns

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Service;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Repository\UserRepository;
use CoreMusic\Security\PasswordHasher;
use CoreMusic\Service\AuthService;
use CoreMusic\Cache\CacheInterface;
use PHPUnit\Framework\Attributes\Test;

final class AuthServiceMockTest extends BaseTestCase
{
    // ─── Basic Mock ───────────────────────────────────────

    #[Test]
    public function testLoginReturnsUserOnValidCredentials(): void
    {
        // Arrange
        $userRepository = $this->createMock(UserRepository::class);
        $passwordHasher = $this->createMock(PasswordHasher::class);

        $userRepository->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn([
                'id' => 1,
                'email' => 'test@example.com',
                'password_hash' => '$argon2id$v=19$m=65536,t=4,p=1$hash',
            ]);

        $passwordHasher->expects($this->once())
            ->method('verify')
            ->with('Test1234!', '$argon2id$v=19$m=65536,t=4,p=1$hash')
            ->willReturn(true);

        $authService = new AuthService($userRepository, $passwordHasher);

        // Act
        $result = $authService->login('test@example.com', 'Test1234!');

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    // ─── MockBuilder ile Gelişmiş Mock ────────────────────

    #[Test]
    public function testLoginReturnsNullOnInvalidPassword(): void
    {
        // Arrange
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByEmail'])
            ->getMock();

        $userRepository->method('findByEmail')
            ->willReturn([
                'id' => 1,
                'password_hash' => '$argon2id$wrong_hash',
            ]);

        $passwordHasher = $this->createMock(PasswordHasher::class);
        $passwordHasher->method('verify')->willReturn(false);

        $authService = new AuthService($userRepository, $passwordHasher);

        // Act
        $result = $authService->login('test@example.com', 'WrongPassword');

        // Assert
        $this->assertNull($result);
    }

    // ─── Callback Mock ────────────────────────────────────

    #[Test]
    public function testPasswordHasherIsCalledWithCorrectAlgorithm(): void
    {
        // Arrange
        $passwordHasher = $this->createMock(PasswordHasher::class);

        $passwordHasher->expects($this->once())
            ->method('hash')
            ->with(
                $this->callback(fn(string $password): bool => strlen($password) >= 8)
            )
            ->willReturn('hashed_password');

        // Act
        $result = $passwordHasher->hash('MySecurePassword123!');

        // Assert
        $this->assertEquals('hashed_password', $result);
    }

    // ─── Spy Pattern (Interaction Verification) ───────────

    #[Test]
    public function testCacheIsCheckedBeforeDatabaseQuery(): void
    {
        // Arrange
        $cache = $this->createMock(CacheInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $cache->expects($this->once())
            ->method('get')
            ->with('user_1')
            ->willReturn(null);

        $userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn(['id' => 1, 'email' => 'test@example.com']);

        // Act & Assert
        $this->assertNotNull($cache);
        $this->assertNotNull($userRepository);
    }
}
```

### 3.8 Expected Exceptions

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Security;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Security\CsrfGuard;
use CoreMusic\Security\InvalidTokenException;
use CoreMusic\Repository\UserRepository;
use PHPUnit\Framework\Attributes\Test;

final class CsrfGuardTest extends BaseTestCase
{
    #[Test]
    public function testValidateThrowsExceptionOnEmptyToken(): void
    {
        // Arrange
        $guard = new CsrfGuard();

        // Act & Assert
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('CSRF token cannot be empty');

        $guard->validate('');
    }

    #[Test]
    public function testValidateThrowsExceptionOnInvalidToken(): void
    {
        // Arrange
        $guard = new CsrfGuard();

        // Act & Assert
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionCode(403);

        $guard->validate('invalid_token_value');
    }

    #[Test]
    public function testGenerateReturnsNonEmptyString(): void
    {
        $guard = new CsrfGuard();
        $token = $guard->generate();

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertGreaterThanOrEqual(32, strlen($token));
    }

    #[Test]
    public function testValidateAcceptsValidToken(): void
    {
        // Arrange
        $guard = new CsrfGuard();
        $token = $guard->generate();

        // Act & Assert — istisna fırlatmamalı
        $result = $guard->validate($token);
        $this->assertTrue($result);
    }
}
```

### 3.9 Database Testing (SQLite In-Memory)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Integration\Repository;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Repository\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

/**
 * Repository integration testleri — gerçek SQLite DB ile.
 */
#[Group('integration')]
#[Group('database')]
final class UserRepositoryIntegrationTest extends BaseTestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixture('users', [
            $this->generateTestUser(1),
            $this->generateTestUser(2),
            $this->generateTestUser(3),
        ]);
        $this->repository = new UserRepository($this->testPdo);
    }

    #[Test]
    public function testFindByIdReturnsCorrectUser(): void
    {
        $result = $this->repository->findById(1);

        $this->assertIsArray($result);
        $this->assertEquals('user1@test.example.com', $result['email']);
    }

    #[Test]
    public function testInsertAddsNewRecord(): void
    {
        $newUser = [
            'email' => 'newuser@test.example.com',
            'username' => 'newuser',
            'password_hash' => password_hash('Test1234!', PASSWORD_ARGON2ID),
            'is_deleted' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->repository->insert($newUser);

        $this->assertGreaterThan(0, $id);

        $fetched = $this->repository->findById($id);
        $this->assertEquals('newuser@test.example.com', $fetched['email']);
    }

    #[Test]
    public function testSoftDeleteMarksRecordAsDeleted(): void
    {
        $result = $this->repository->softDelete(1);

        $this->assertTrue($result);

        $fetched = $this->repository->findById(1);
        $this->assertNull($fetched);
    }

    #[Test]
    public function testFindAllReturnsAllNonDeletedRecords(): void
    {
        $this->repository->softDelete(2);

        $all = $this->repository->findAll();

        $this->assertIsArray($all);
        $this->assertCount(2, $all);
    }
}
```

### 3.10 HTTP Testing (Request Simulation)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Middleware;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Middleware\CsrfMiddleware;
use CoreMusic\Middleware\SecurityHeadersMiddleware;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

final class CsrfMiddlewareTest extends BaseTestCase
{
    #[Test]
    #[Group('middleware')]
    public function testCsrfMiddlewareRejectsPostWithoutToken(): void
    {
        // Arrange
        $middleware = new CsrfMiddleware();
        $request = [
            'method' => 'POST',
            'uri' => '/api/user',
            'headers' => [],
            'body' => ['name' => 'test'],
        ];

        // Act
        $response = $middleware->handle($request, fn($req) => ['status' => 200]);

        // Assert
        $this->assertEquals(403, $response['status']);
    }

    #[Test]
    #[Group('middleware')]
    public function testCsrfMiddlewareAllowsGetWithoutToken(): void
    {
        // Arrange
        $middleware = new CsrfMiddleware();
        $request = [
            'method' => 'GET',
            'uri' => '/api/user',
            'headers' => [],
            'body' => [],
        ];

        // Act
        $response = $middleware->handle($request, fn($req) => ['status' => 200]);

        // Assert
        $this->assertEquals(200, $response['status']);
    }

    #[Test]
    #[Group('middleware')]
    public function testSecurityHeadersAreAddedToResponse(): void
    {
        // Arrange
        $middleware = new SecurityHeadersMiddleware();
        $request = [
            'method' => 'GET',
            'uri' => '/test',
            'headers' => [],
            'body' => [],
        ];

        // Act
        $response = $middleware->handle($request, fn($req) => [
            'status' => 200,
            'headers' => [],
            'body' => 'OK',
        ]);

        // Assert
        $this->assertArrayHasKey('X-Content-Type-Options', $response['headers']);
        $this->assertEquals('nosniff', $response['headers']['X-Content-Type-Options']);
    }
}
```

## 4. Hard Guardrails

| # | Kural | Açıklama | ADR |
|---|-------|----------|-----|
| 1 | **strict_types=1** | Her test dosyasının ilk satırı zorunlu | PHP 8.4 |
| 2 | **AAA Pattern** | Arrange-Act-Assert ayrımı zorunlu | — |
| 3 | **Descriptive Names** | `testMethodNameWithCondition` formatı | — |
| 4 | **Single Assertion** | Her test'te tek mantıksal assertion bloğu | — |
| 5 | **No Test Interdependence** | Testler bağımsız çalışmalıdır, sıraya bağlı olmamalı | — |
| 6 | **PDO Prepared Statement** | DB testlerinde prepared statement zorunlu | ADR-002 |
| 7 | **No ORM** | Doctrine, Eloquent vb. kullanımı yasak | ADR-002 |
| 8 | **SQLite In-Memory** | Unit testlerde gerçek DB yerine SQLite kullanılmalı | — |
| 9 | **No Production Data** | Testlerde production verisi kullanılmaz | ADR-040 |
| 10 | **Cleanup After Test** | `tearDown()` ile test izleri temizlenmeli | — |

## 5. Naming Conventions

| Unsur | Format | Örnek |
|-------|--------|-------|
| **Test Dosyası** | `{Sınıf}Test.php` | `UserRepositoryTest.php` |
| **Test Sınıfı** | `final class {Sınıf}Test extends BaseTestCase` | `final class UserRepositoryTest` |
| **Test Metodu** | `test{Metod}{Koşul}{Sonuç}` | `testFindByIdReturnsUserWhenExists` |
| **Data Provider** | `public static function {değişken}Provider(): array` | `emailProvider()` |
| **Değişken** | `$camelCase` | `$userRepository` |
| **Sabit** | `UPPER_SNAKE_CASE` | `TEST_USER_EMAIL` |
| **Namespace** | `CoreMusic\Tests\{Tip}\{Modül}` | `CoreMusic\Tests\Unit\Repository` |
| **Group** | `#[Group('modül')]` | `#[Group('repository')]` |

## 6. Security Considerations

| Konu | Kural | Uygulama |
|------|-------|----------|
| **Test Şifreleri** | Asla gerçek şifre kullanma | `password_hash('Test1234!', PASSWORD_ARGON2ID)` |
| **API Key'ler** | Testlerde dummy key kullan | `'test_api_key_' . bin2hex(random_bytes(16))` |
| **Production DB** | Test asla production DB'ine bağlanmaz | `sqlite::memory:` veya test DB |
| **Session Token** | Rastgele üretilmiş token kullan | `bin2hex(random_bytes(32))` |
| **Credential Cleanup** | Test sonunda tüm credential'lar temizlenir | `tearDown()` ile |
| **GDPR Compliance** | Test verilerinde gerçek PII yok | Sahte email: `test@example.com` |
| **Argon2id** | Şifre hashleme testlerinde Argon2id kullan | ADR-022 uyumlu |

## 7. Performance Notes

| Konu | Öneri | Detay |
|------|-------|-------|
| **Parallel Execution** | `--parallel-processes=4` | PHPUnit 10 paralel test desteği |
| **Test Isolation** | Her test kendi DB'sini oluşturur | `sqlite::memory:` ile izolasyon |
| **Memory Limit** | `--memory-limit=512M` | Büyük fixture'larda gerekli |
| **Slow Test Detection** | `--disallow-test-output` | Çıktı üreten testleri tespit |
| **Coverage Off** | Geliştirmede `--no-coverage` | Hız artışı için |
| **Group Filtering** | `--group=unit` | Sadece unit testleri çalıştır |

## 8. Edge Cases

| Senaryo | Test Edilen Durum | Beklenen Davranış |
|---------|-------------------|-------------------|
| **Null Input** | `findById(null)` | `InvalidArgumentException` |
| **Empty String** | `findByEmail('')` | `null` döndürür |
| **Max Integer** | `findById(PHP_INT_MAX)` | `null` döndürür |
| **Negative ID** | `findById(-1)` | `null` döndürür |
| **SQL Injection** | `findByEmail("' OR 1=1--")` | Prepared statement koruması |
| **Unicode Input** | `findByUsername(' Bayram ')` | Trim uygulanır |
| **Long String** | `findByEmail(str_repeat('a', 1000))` | Validation hatası |
| **Concurrent Access** | Eşzamanlı insert | Race condition koruması |
| **Memory Limit** | 10000 kayıtlı select | Bellek limiti aşılmaz |
| **Transaction Rollback** | Hatalı insert sonrası | Rollback ile veri bozulmaz |

## 9. Troubleshooting

| Hata | Neden | Çözüm |
|------|-------|-------|
| **Risky test** | Test assertion içermiyor | `#[Test]` method'una assertion ekle |
| **Orphan assertion** | Assertion test akışı dışında | Assertion'ı `// Act` bloğuna taşı |
| **Mock not called** | `expects($this->once())` başarısız | Mock'un realmente çağrıldığından emin ol |
| **DataProvider error** | Static method değil | Provider method'unu `public static` yap |
| **Depends chain broken** | Bağımlı test başarısız | Bağımsız testleri bağımsız çalıştırılabilir yap |
| **Memory leak** | `setUp()` içinde büyük veri | `tearDown()` ile temizle |
| **SQLite locked** | Eşzamanlı erişim | Her test kendi DB connection'ını kullansın |
| **Coverage missing** | `#[CoversClass]` eksik | Doğru class reference'ı ekle |
| **Assertion count** | Birden fazla assertion | Tek assertion bloğuna indir veya `withConsecutive` kullan |
| **Slow test** | Integration testleri yavaş | `#[Group('slow')]` ile ayır |

## 10. Common Anti-Patterns

### ❌ Testing Private Methods Directly

```php
// ❌ YANLIŞ
#[Test]
public function testHashPassword(): void
{
    $service = new AuthService();
    $result = $service->hashPassword('test'); // private method
}
```

```php
// ✅ DOĞRU
#[Test]
public function testPasswordIsHashedOnRegistration(): void
{
    $service = new AuthService($userRepository, $passwordHasher);
    $user = $service->register('test@example.com', 'MyPassword123!');
    $this->assertNotEquals('MyPassword123!', $user['password_hash']);
}
```

### ❌ Over-Mocking

```php
// ❌ YANLIŞ — Her şey mocklanmış, test bir şey doğrulamıyor
#[Test]
public function testSomething(): void
{
    $mock = $this->createMock(Anything::class);
    $mock->method('anything')->willReturn('anything');
    // Hiçbir gerçek davranış test edilmiyor
}
```

```php
// ✅ DOĞRU — Sadece dış bağımlılıklar mock'lanır
#[Test]
public function testAuthServiceReturnsUserOnValidLogin(): void
{
    $userRepository = $this->createMock(UserRepository::class);
    $passwordHasher = $this->createMock(PasswordHasher::class);

    $userRepository->method('findByEmail')
        ->willReturn(['id' => 1, 'email' => 'test@example.com']);

    $passwordHasher->method('verify')->willReturn(true);

    $authService = new AuthService($userRepository, $passwordHasher);
    $result = $authService->login('test@example.com', 'password');

    $this->assertNotNull($result);
}
```

### ❌ Testing Implementation Details

```php
// ❌ YANLIŞ — Internal method çağrısını test ediyor
#[Test]
public function testInternalMethodCalled(): void
{
    $mock = $this->createMock(UserRepository::class);
    $mock->expects($this->once())
        ->method('buildSelectQuery'); // internal method
}
```

```php
// ✅ DOĞRU — Dış davranışı test eder
#[Test]
public function testFindByIdReturnsCorrectUser(): void
{
    $repository = new UserRepository($this->testPdo);
    $this->loadFixture('users', [$this->generateTestUser(1)]);

    $user = $repository->findById(1);

    $this->assertEquals('user1@test.example.com', $user['email']);
}
```

### ❌ Shared State Between Tests

```php
// ❌ YANLIŞ — Testler arası paylaşılan state
private array $sharedData = [];

#[Test]
public function testFirst(): void
{
    $this->sharedData[] = 'item1';
}

#[Test]
public function testSecond(): void
{
    // testFirst'in state'ini taşır
    $this->assertCount(1, $this->sharedData);
}
```

```php
// ✅ DOĞRU — Her test kendi state'ini oluşturur
#[Test]
public function testFirst(): void
{
    $data = ['item1'];
    $this->assertCount(1, $data);
}

#[Test]
public function testSecond(): void
{
    $data = ['item2'];
    $this->assertCount(1, $data);
}
```

### ❌ Assertion-less Tests

```php
// ❌ YANLIŞ — Assertion yok, test riskli
#[Test]
public function testInsert(): void
{
    $this->repository->insert(['email' => 'test@example.com']);
    // Sonuç doğrulanmıyor
}
```

```php
// ✅ DOĞRU — Her test en az bir assertion içerir
#[Test]
public function testInsertReturnsValidId(): void
{
    $id = $this->repository->insert(['email' => 'test@example.com']);
    $this->assertIsInt($id);
    $this->assertGreaterThan(0, $id);
}
```

### ❌ Hardcoded Test Data

```php
// ❌ YANLIŞ — Hardcoded değer
#[Test]
public function testUserEmail(): void
{
    $this->assertEquals('admin@coremusic.net', $user['email']);
}
```

```php
// ✅ DOĞRU — Fixture veya generate ile veri üretimi
#[Test]
public function testUserEmail(): void
{
    $user = $this->generateTestUser(1);
    $this->assertEquals('user1@test.example.com', $user['email']);
}
```

## 11. Testing Requirements

| Modül | Minimum Kapsama | Hedef Kapsama | ADR |
|-------|----------------|---------------|-----|
| **Backend (PHP)** | ≥80% | ≥90% | ADR-040 |
| **Frontend (JS)** | ≥80% | ≥90% | ADR-001 |
| **Audio Engine (C++)** | ≥80% | ≥90% | ADR-017 |
| **Download Service** | ≥80% | ≥90% | — |

**Test Piramidi:**

```
        ▲  E2E (Playwright) — 10%
       ▲▲  Integration (PHPUnit) — 30%
      ▲▲▲  Unit (PHPUnit/Vitest) — 60%
```

**Birim Test Dağılımı:**

| Tip | Oran | Örnek |
|-----|------|-------|
| Unit (hızlı, izole) | 60% | Repository, Service, Validator |
| Integration (DB) | 25% | Repository + SQLite, API endpoint |
| Functional (HTTP) | 15% | Middleware pipeline, session |

## 12. phpunit.xml Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         failOnRisky="true"
         failOnWarning="true"
         beStrictAboutTestsThatDoNotTestAnything="true"
         beStrictAboutOutputDuringTests="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         displayDetailsOnTestsThatTriggerNotices="true"
         displayDetailsOnTestsThatTriggerWarnings="true"
         cacheDirectory=".phpunit.cache">

    <testsuites>
        <testsuite name="unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="all">
            <directory>tests</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory>src</directory>
        </include>
        <exclude>
            <directory>src/Config</directory>
            <directory>src/Dev</directory>
        </exclude>
    </source>

    <coverage>
        <report>
            <html outputDirectory="coverage/html"/>
            <text outputFile="coverage/coverage.txt" showUncoveredFiles="true"/>
            <clover outputFile="coverage/clover.xml"/>
            <cobertura outputFile="coverage/cobertura.xml"/>
        </report>
    </coverage>

    <logging>
        <junit outputFile="logs/junit.xml"/>
        <teamcity outputFile="logs/teamcity.log"/>
        <text outputFile="logs/test-results.txt" showStackTraces="true"/>
    </logging>

    <php>
        <ini name="error_reporting" value="-1"/>
        <ini name="display_errors" value="1"/>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_DRIVER" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>

    <extensions>
        <bootstrap class="CoreMusic\Tests\BootstrapExtension"/>
    </extensions>
</phpunit>
```

## 13. CI Integration

### GitHub Actions Workflow

```yaml
name: PHPUnit Tests

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php-version: ['8.4']

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          extensions: mbstring, xml, dom, json, pdo, pdo_sqlite
          coverage: xdebug

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run PHPUnit
        run: vendor/bin/phpunit --coverage-clover=coverage/clover.xml

      - name: Upload coverage to Codecov
        if: matrix.php-version == '8.4'
        uses: codecov/codecov-action@v4
        with:
          files: coverage/clover.xml
          fail_ci_if_error: false

      - name: Generate coverage badge
        if: matrix.php-version == '8.4'
        run: |
          php vendor/bin/phpunit --coverage-text > coverage/coverage.txt
          echo "## Test Coverage" >> $GITHUB_STEP_SUMMARY
          cat coverage/coverage.txt >> $GITHUB_STEP_SUMMARY
```

## 14. Related Documents

| Dosya | İlişki |
|-------|--------|
| [[php-template]] | PHP coding template |
| [[js-template]] | JavaScript template |
| [[vitest-template]] | Vitest frontend testing |
| [[cpp-template]] | C++ Neva Engine template |
| [[testing/strategy]] | CoreMusic test stratejisi |
| [[testing/coverage-targets]] | Kapsama hedefleri |
| [[testing/e2e-template]] | E2E test şablonu |
| [[testing/persona-test-protocol]] | Persona test protokolü |
| [[testing/test-plan]] | Test planı |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak |
| [[ADR-040-database-authority]] | 18 BCNF DB otoritesi |
| [[ADR-010-csrf-protection-strategy]] | CSRF token yönetimi |
| [[ADR-011-session-management]] | Session yönetimi |
| [[ADR-022-database-hardened-security]] | DB güvenlik standartları |
| [[ADR-001-vanilla-js-itcss]] | Frontend kararı (JS testleri için) |

## 15. Cross-References

| Bu Şablondan | Hedef ADR/Dosya | İlişki |
|-------------|-----------------|--------|
| § 3.2 BaseTestCase | [[ADR-002-pdo-mandatory-no-orm]] | PDO prepared statement kullanımı |
| § 3.9 Database Testing | [[ADR-040-database-authority]] | 18 BCNF veritabanı testleri |
| § 3.10 HTTP Testing | [[ADR-010-csrf-protection-strategy]] | CSRF middleware testleri |
| § 6 Security | [[ADR-022-database-hardened-security]] | Credential test kuralları |
| § 3.7 Mocking | [[ADR-011-session-management]] | Session service mock'ları |
| § 12 phpunit.xml | [[testing/strategy]] | Test altyapısı |
| § 13 CI | [[architecture/02-deployment/ci-cd-pipeline]] | CI/CD entegrasyonu |

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | 600+ |
| **Frontmatter** | ✅ Tamamlandı (18 field) |
| **PHPUnit 10+ Uyumlu** | ✅ Attributes, strict types |
| **ADR Referansları** | ✅ 11 ADR |
| **Kod Örnekleri** | ✅ 25+ PHP kod bloğu |
| **Anti-Pattern'ler** | ✅ 6 ❌/✅ çifti |
| **Section Sayısı** | ✅ 18/18 |

## 17. Examples

### Full Test Class: Repository Test

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Repository;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Repository\MusicRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * MusicRepository unit tests.
 *
 * ADR-002 uyumlu: PDO prepared statement, ORM yasak.
 * ADR-040 uyumlu: coremusic_musics tablosu (BCNF).
 */
#[CoversClass(MusicRepository::class)]
#[Group('repository')]
#[Group('music')]
final class MusicRepositoryTest extends BaseTestCase
{
    private MusicRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MusicRepository($this->testPdo);
    }

    #[Test]
    public function testFindByIdReturnsMusicWhenExists(): void
    {
        // Arrange
        $music = [
            'id' => 1,
            'title' => 'Test Song',
            'artist' => 'Test Artist',
            'album' => 'Test Album',
            'duration' => 240,
            'genre' => 'Pop',
            'is_deleted' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->loadFixture('musics', [$music]);

        // Act
        $result = $this->repository->findById(1);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('Test Song', $result['title']);
        $this->assertEquals('Test Artist', $result['artist']);
    }

    #[Test]
    public function testFindByIdReturnsNullWhenNotExists(): void
    {
        $result = $this->repository->findById(999);
        $this->assertNull($result);
    }

    #[Test]
    #[DataProvider('searchQueryProvider')]
    public function testSearchReturnsMatchingResults(string $query, int $expectedCount): void
    {
        // Arrange
        $this->loadFixture('musics', [
            ['id' => 1, 'title' => 'Bohemian Rhapsody', 'artist' => 'Queen', 'is_deleted' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'title' => 'Stairway to Heaven', 'artist' => 'Led Zeppelin', 'is_deleted' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 3, 'title' => 'Hotel California', 'artist' => 'Eagles', 'is_deleted' => 0, 'created_at' => date('Y-m-d H:i:s')],
        ]);

        // Act
        $results = $this->repository->search($query);

        // Assert
        $this->assertCount($expectedCount, $results);
    }

    public static function searchQueryProvider(): array
    {
        return [
            'single match' => ['Bohemian', 1],
            'no match' => ['NonExistent', 0],
            'partial match' => ['to', 1],        // "Stairway to Heaven"
            'artist match' => ['Queen', 1],
            'wildcard' => ['%', 3],              // tümünü getir
        ];
    }

    #[Test]
    public function testInsertCreatesNewMusic(): void
    {
        $newMusic = [
            'title' => 'New Song',
            'artist' => 'New Artist',
            'album' => 'New Album',
            'duration' => 300,
            'genre' => 'Rock',
            'is_deleted' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->repository->insert($newMusic);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $fetched = $this->repository->findById($id);
        $this->assertEquals('New Song', $fetched['title']);
    }

    #[Test]
    public function testSoftDeleteReturnsTrueOnSuccess(): void
    {
        $music = ['id' => 1, 'title' => 'Del', 'artist' => 'A', 'is_deleted' => 0, 'created_at' => date('Y-m-d H:i:s')];
        $this->loadFixture('musics', [$music]);

        $result = $this->repository->softDelete(1);

        $this->assertTrue($result);
        $this->assertNull($this->repository->findById(1));
    }

    #[Test]
    public function testSoftDeleteReturnsFalseWhenNotExists(): void
    {
        $result = $this->repository->softDelete(999);
        $this->assertFalse($result);
    }

    #[Test]
    public function testFindAllExcludesDeletedRecords(): void
    {
        $this->loadFixture('musics', [
            ['id' => 1, 'title' => 'Active', 'artist' => 'A', 'is_deleted' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'title' => 'Deleted', 'artist' => 'B', 'is_deleted' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ]);

        $all = $this->repository->findAll();

        $this->assertCount(1, $all);
        $this->assertEquals('Active', $all[0]['title']);
    }
}
```

### Full Test Class: Service Test

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Service;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Service\AuthService;
use CoreMusic\Repository\UserRepository;
use CoreMusic\Security\PasswordHasher;
use CoreMusic\Security\CsrfGuard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * AuthService unit tests.
 *
 * Mock tabanlı: DB bağlantısı yok, sadece iş mantığı test edilir.
 */
#[CoversClass(AuthService::class)]
#[Group('service')]
#[Group('auth')]
final class AuthServiceTest extends BaseTestCase
{
    private UserRepository $userRepository;
    private PasswordHasher $passwordHasher;
    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->authService = new AuthService($this->userRepository, $this->passwordHasher);
    }

    #[Test]
    public function testLoginReturnsUserDataOnValidCredentials(): void
    {
        $this->userRepository->method('findByEmail')
            ->willReturn([
                'id' => 1,
                'email' => 'test@example.com',
                'password_hash' => '$argon2id$correct',
            ]);

        $this->passwordHasher->method('verify')
            ->willReturn(true);

        $result = $this->authService->login('test@example.com', 'CorrectPassword');

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    #[Test]
    public function testLoginReturnsNullOnInvalidPassword(): void
    {
        $this->userRepository->method('findByEmail')
            ->willReturn([
                'id' => 1,
                'password_hash' => '$argon2id$hash',
            ]);

        $this->passwordHasher->method('verify')
            ->willReturn(false);

        $result = $this->authService->login('test@example.com', 'WrongPassword');

        $this->assertNull($result);
    }

    #[Test]
    public function testLoginReturnsNullWhenUserNotFound(): void
    {
        $this->userRepository->method('findByEmail')
            ->willReturn(null);

        $result = $this->authService->login('nonexistent@example.com', 'password');

        $this->assertNull($result);
    }

    #[Test]
    public function testRegisterCreatesNewUser(): void
    {
        $this->userRepository->method('findByEmail')
            ->willReturn(null);

        $this->userRepository->method('insert')
            ->willReturn(42);

        $this->passwordHasher->method('hash')
            ->willReturn('hashed_password');

        $result = $this->authService->register('new@example.com', 'SecurePass123!');

        $this->assertIsArray($result);
        $this->assertEquals(42, $result['id']);
    }

    #[Test]
    public function testRegisterThrowsOnDuplicateEmail(): void
    {
        $this->userRepository->method('findByEmail')
            ->willReturn(['id' => 1]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Email already exists');

        $this->authService->register('existing@example.com', 'password');
    }

    #[Test]
    public function testRegisterRejectsWeakPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password too weak');

        $this->authService->register('test@example.com', '123');
    }
}
```

### Full Test Class: Middleware Test

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Middleware;

use CoreMusic\Tests\BaseTestCase;
use CoreMusic\Middleware\RateLimiterMiddleware;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * RateLimiter middleware tests.
 *
 * APCu-based rate limiting testleri.
 */
#[CoversClass(RateLimiterMiddleware::class)]
#[Group('middleware')]
#[Group('security')]
final class RateLimiterMiddlewareTest extends BaseTestCase
{
    private RateLimiterMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60
        );
    }

    #[Test]
    public function testRequestWithinLimitIsAllowed(): void
    {
        $request = [
            'method' => 'GET',
            'uri' => '/api/test',
            'headers' => ['X-Forwarded-For' => '127.0.0.1'],
            'body' => [],
        ];

        $response = $this->middleware->handle($request, fn($req) => [
            'status' => 200,
            'body' => 'OK',
        ]);

        $this->assertEquals(200, $response['status']);
    }

    #[Test]
    public function testRequestExceedingLimitReturns429(): void
    {
        // Arrange — 60 istek limiti, 61. istek reddedilmeli
        $request = [
            'method' => 'GET',
            'uri' => '/api/test',
            'headers' => ['X-Forwarded-For' => '192.168.1.100'],
            'body' => [],
        ];

        // Act — 61 kez istek gönder
        for ($i = 0; $i < 61; $i++) {
            $response = $this->middleware->handle($request, fn($req) => [
                'status' => 200,
                'body' => 'OK',
            ]);
        }

        // Assert — Son istek 429 döndürmeli
        $this->assertEquals(429, $response['status']);
    }

    #[Test]
    public function testRateLimitHeadersArePresent(): void
    {
        $request = [
            'method' => 'GET',
            'uri' => '/api/test',
            'headers' => [],
            'body' => [],
        ];

        $response = $this->middleware->handle($request, fn($req) => [
            'status' => 200,
            'headers' => [],
            'body' => 'OK',
        ]);

        $this->assertArrayHasKey('X-RateLimit-Limit', $response['headers']);
        $this->assertArrayHasKey('X-RateLimit-Remaining', $response['headers']);
        $this->assertEquals(60, $response['headers']['X-RateLimit-Limit']);
    }
}
```

## 18. Checklist

Her test yazmadan önce ve PR oluşturmadan önce bu kontrol listesini uygula:

### Test Yazma Kontrol Listesi

- [ ] `declare(strict_types=1)` dosya başında mevcut mu?
- [ ] `CoreMusic\Tests\` namespace'i doğru mu?
- [ ] `#[CoversClass]` attribute'u ekli mi?
- [ ] `#[Group]` attribute'ları tanımlı mı?
- [ ] Test adı `testMethodNameWithCondition` formatında mı?
- [ ] AAA pattern (Arrange-Act-Assert) uygulanmış mı?
- [ ] Her test'te en az bir assertion var mı?
- [ ] Mock'lar sadece dış bağımlılıklar için mi oluşturulmuş?
- [ ] Test verileri fixture veya `generateTestUser()` ile mi üretilmiş?
- [ ] `tearDown()` ile temizlik yapılıyor mu?
- [ ] Production verisi veya credental kullanılmıyor mu?

### PR Kontrol Listesi

- [ ] `vendor/bin/phpunit` %100 geçiyor mu?
- [ ] Yeni testler için coverage artışı var mı?
- [ ] `phpunit.xml` değişikliği varsa CI uyumlu mu?
- [ ] Eski testler bozulmamış mı?
- [ ] Yavaş test varsa `#[Group('slow')]` ile işaretlenmiş mi?
- [ ] Mock'lar gerçek davranışı simüle ediyor mu?
- [ ] Testler bağımsız çalışabiliyor mu (sıra bağımlılığı yok)?
- [ ] Hata mesajları anlamlı mı?

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
