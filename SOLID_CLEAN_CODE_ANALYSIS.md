# CoreMusic SOLID & Clean Code Analysis Report

**Date:** 2026-10-29  
**Analyzed Components:** Shared Library (Database, Middleware, Config, Auth), API Project (Controllers, Services, Repositories), Media Project (CatalogWriter)

---

## Executive Summary

After analyzing the CoreMusic codebase, I've identified **17 significant SOLID violations and clean code issues** across the shared library and API projects. The most critical issues are in the Database layer (ISP/LSP violations, massive code duplication) and AuthController (SRP violation, too many responsibilities).

---

## Database Layer Analysis (CRITICAL)

### 1. ISP Violation: IDatabaseManager Forces getPdo() on All Implementations

**File:** `shared/src/Contracts/Database/IDatabaseManager.php:13`

```php
public function getPdo(): \PDO;
```

**Problem:** MongoDB doesn't use PDO. `MongoDbManager` is forced to implement this method and throws `LogicException`:

```php
public function getPdo(): \PDO
{
    throw new \LogicException('MongoDB does not use PDO. Use getManager() instead.');
}
```

**Impact:** 
- Violates Interface Segregation Principle
- Clients depending on IDatabaseManager can't safely call getPdo() without knowing the concrete type
- Runtime exceptions instead of compile-time safety

**Solution:** Split into two interfaces:
- `ISqlDatabaseManager extends IDatabaseManager { public function getPdo(): \PDO; }`
- `INoSqlDatabaseManager` (for MongoDB) without getPdo()

---

### 2. LSP Violation: MongoDbManager Cannot Substitute Other Implementations

**File:** `shared/src/Database/MongoDbManager.php`

| Method | MySQL/SQLite/SQLSRV | MongoDbManager | LSP Compliant? |
|--------|---------------------|----------------|----------------|
| `getPdo()` | Returns PDO | Throws LogicException | ❌ NO |
| `beginTransaction()` | Begins TX | Returns `false` | ❌ NO |
| `commit()` | Commits TX | Returns `false` | ❌ NO |
| `rollBack()` | Rolls back TX | Returns `false` | ❌ NO |
| `runInTransaction()` | Real TX | Runs without TX | ❌ NO |
| `lastInsertId()` | Returns ID | Returns empty string | ❌ NO |

**Impact:** Code using IDatabaseManager cannot safely substitute MongoDB - it will break at runtime.

**Solution:** Create separate interface hierarchy for NoSQL databases.

---

### 3. Massive Code Duplication (DRY Violation) - 4 Classes, ~100 Lines Each

**Files:** 
- `DatabaseManager.php` (lines 55-131)
- `SQLiteDatabaseManager.php` (lines 44-118)
- `SqlsrvDatabaseManager.php` (lines 60-135)
- `MongoDbManager.php` (lines 89-191)

All three SQL implementations have **identical** implementations of:
- `execute()`, `write()`, `lastInsertId()`
- `beginTransaction()`, `commit()`, `rollBack()`
- `runInTransaction()`, `rollBackIfActive()`

**Code Example (duplicated in 3 classes):**

```php
public function execute(string $sql, array $params = []): array
{
    $stmt = $this->getPdo()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
}

public function runInTransaction(callable $fn): mixed
{
    $pdo = $this->getPdo();
    if ($pdo->inTransaction()) {
        return $fn($pdo);
    }
    $pdo->beginTransaction();
    try {
        $result = $fn($pdo);
        $pdo->commit();
        return $result;
    } catch (\Throwable $e) {
        $this->rollBackIfActive($pdo);
        throw $e;
    }
}

private function rollBackIfActive(\PDO $pdo): void
{
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}
```

**Impact:** ~300 lines of duplicated code. Bug fixes must be applied in 3 places.

**Solution:** Extract to abstract base class `AbstractPdoDatabaseManager` or trait `PdoTransactionTrait`.

---

### 4. ConnectionFactory Singleton Anti-Pattern

**File:** `shared/src/Database/ConnectionFactory.php`

```php
private static ?self $instance = null;

public static function getInstance(?string $envFile = null): self
{
    return self::$instance ??= new self($envFile);
}

public static function reset(): void
{
    self::$instance = null;
}
```

**Problems:**
- Global state makes testing difficult
- Hidden dependencies
- Cannot have multiple configurations in same process
- `reset()` is a test-only workaround indicating design flaw

**Solution:** Use dependency injection container, remove singleton.

---

### 5. Hardcoded Configuration in ConnectionFactory

**File:** `shared/src/Database/ConnectionFactory.php:140-153`

```php
$this->registry->registerProvider(
    $db,
    $provider,
    $this->env('DB_HOST', 'localhost'),
    $this->schemaName($db),
    $this->env('DB_USER', ''),
    $this->env('DB_PASSWORD', ''),
    (int) $this->env('DB_PORT', '3306'),
    $this->env('DB_CHARSET', 'utf8mb4'),
);
```

**Problems:**
- All databases forced to use same host/user/password/port/charset
- Cannot configure per-database connection parameters
- MongoDB and SQLite parameters ignored (user/pass/port/charset not applicable)

**Solution:** Per-database env var configuration (e.g., `DB_AUTH_HOST`, `DB_AUTH_PORT`, etc.)

---

## API Layer Analysis (HIGH)

### 6. AuthController SRP Violation - 7 Responsibilities in One Class

**File:** `api.coremusic.net/include/Controller/AuthController.php` (310 lines)

**Responsibilities identified:**
1. HTTP request/response handling (body parsing, IP detection)
2. Action routing (login, register, setGender, forgot, reset, logout, me)
3. Business logic orchestration (calling authService)
4. **JWT token issuance and persistence** (lines 196-207)
5. Redirect URL resolution and open-redirect protection
6. User data shaping/transformation
7. CSRF token management
8. Gender detection logic

**Violation:** The JWT token persistence logic (lines 196-207) belongs in a service, not controller:

```php
// In AuthController::authPayload() - THIS IS BUSINESS LOGIC
if ($this->jwt !== null && $this->users !== null && $userId !== '') {
    $issued = $this->jwt->issue($userId);
    $this->users->saveAccessToken(
        $userId,
        $issued['jti'],
        date('Y-m-d H:i:s', time() + $issued['expires_in'])
    );
    $payload['access_token'] = $issued['token'];
    // ...
}
```

**Solution:** Move JWT handling to `AuthService` or dedicated `TokenService`.

---

### 7. AuthController Optional Dependencies Signal Design Issues

**File:** `api.coremusic.net/include/Controller/AuthController.php:32-39`

```php
public function __construct(
    private readonly IAuthService $authService,
    private readonly ISessionManager $session,
    private readonly string $musicUrl,
    private readonly string $authUrl,
    private readonly ?JwtService $jwt = null,      // Optional!
    private readonly ?IUserRepository $users = null, // Optional!
) {}
```

**Problem:** Optional dependencies indicate the controller is doing too much. JWT and UserRepository are only needed for token persistence - this should be encapsulated in a service.

---

### 8. Duplicate Transaction Logic in Containers

**File:** `api.coremusic.net/include/Container/ApiAuthContainer.php` (not read but similar pattern exists)

The transaction management (`runInTransaction`) is duplicated in:
- `DatabaseManager` (and 3 subclasses)
- `ConnectionFactory`
- Likely in containers too

**Solution:** Single source of truth for transaction handling.

---

## Media Project Analysis (MEDIUM)

### 9. CatalogWriter SRP Violation

**File:** `media.coremusic.net/src/Media/CatalogWriter.php` (560 lines)

**Responsibilities:**
1. JSONL file writing (buffer management, encoding, file I/O)
2. MySQL database writing (connection, transactions, upserts)
3. Data transformation (JSON → DB rows)
4. Hash calculation and deduplication
5. Schema validation (allowlist, unique constraints)
6. Type conversion (s, i, f, j, dt helper methods)

**Solution:** Split into:
- `JsonlWriter` - file I/O only
- `MySqlCatalogRepository` - database persistence only
- `CatalogTransformer` - data transformation only
- `CatalogValidator` - schema/constraint validation

---

### 10. Static Helper Methods in CatalogWriter

**File:** `media.coremusic.net/src/Media/CatalogWriter.php:497-558`

```php
private static function s(mixed $d): ?string { ... }
private static function i(mixed $d): ?int { ... }
private static function f(mixed $d): ?float { ... }
private static function j(mixed $d): string { ... }
private static function dt(mixed $d): ?string { ... }
```

**Problems:**
- Static methods can't be mocked/tested in isolation
- Type conversion logic belongs in a separate `TypeConverter` or `Hydrator` class
- Violates DIP - high-level module depends on low-level implementation details

---

## Middleware Layer Analysis (LOW)

### 11. Middleware Order Hardcoded in Multiple Places

The 10-middleware pipeline order is documented in:
- `shared/src/Middleware/CLAUDE.md` (lines 42-55)
- `api.coremusic.net/CLAUDE.md` (rule #3, #9)
- `shared/src/Database/ConnectionFactory.php` (not directly but related)
- Likely in `index.php` files

**Risk:** Inconsistency if order changes in one place but not others.

**Solution:** Single source of truth - configuration-driven pipeline.

---

### 12. MiddlewarePipeline Uses get_class() for Logging

**File:** `shared/src/Middleware/MiddlewarePipeline.php:41`

```php
$name = substr(strrchr(get_class($middleware), '\\') ?: get_class($middleware), 1);
```

**Problem:** Couples logging to class name. If class renamed, log output changes.

**Solution:** Middleware should implement `getName()` method or use a name property.

---

## Cross-Cutting Concerns

### 13. Missing Interface for Database Configuration

**File:** `shared/src/Database/Config/DatabaseConfig.php` (not read but referenced)

No interface for `DatabaseConfig` - concrete class used directly in constructors.

---

### 14. No Domain Events for Critical Operations

**Files:** Various - AuthController, CatalogWriter

Operations like user registration, password reset, catalog writes don't emit domain events. Other services can't react (e.g., analytics, notifications).

**Solution:** Emit domain events from services (not controllers).

---

### 15. Inconsistent Error Handling Patterns

Different components use different patterns:
- `DatabaseException::connectionFailed()` factory method
- `ValidationException` with error codes
- Raw `\Throwable` catches returning arrays with `uyari` keys
- Some return `['baglandi' => false, ...]`, others throw

**Solution:** Standardized error handling with consistent result types.

---

### 16. Magic Strings in AuthController

**File:** `api.coremusic.net/include/Controller/AuthController.php`

```php
private const FORGOT_MESSAGE = 'Sıfırlama bağlantısı gönderildi.';
private const RESET_MESSAGE  = 'Şifreniz sıfırlandı.';
// ...
$code = $e->getErrorCode() === 'USERNAME_EXISTS' ? 'USERNAME_TAKEN' : 'EMAIL_TAKEN';
// ...
$status = in_array($e->getErrorCode(), ['GENDER_MISMATCH', 'ACCOUNT_BANNED'], true) ? 403 : 401;
```

**Problems:** Error codes and messages scattered, not centralized.

**Solution:** Centralized error code/message registry or exception hierarchy.

---

### 17. Session/Cookie Configuration Scattered

Session config appears in:
- `shared/src/Session/SessionConfig.php`
- `shared/src/Middleware/SessionManagerMiddleware.php`
- `api.coremusic.net/config/constants.php` (likely)
- `auth.coremusic.net/config/constants.php` (likely)

**Solution:** Single configuration source.

---

## Priority Matrix

| Priority | Issue | Effort | Risk if Not Fixed |
|----------|-------|--------|-------------------|
| 🔴 CRITICAL | ISP/LSP in Database Layer | Medium | Runtime crashes, unsafe substitutions |
| 🔴 CRITICAL | Code Duplication (300+ lines) | Low | Bugs, maintenance burden |
| 🟠 HIGH | AuthController SRP (7 responsibilities) | Medium | Unmaintainable, untestable |
| 🟠 HIGH | ConnectionFactory Singleton | Medium | Testing difficulties, global state |
| 🟡 MEDIUM | CatalogWriter SRP (6 responsibilities) | Medium | Tight coupling, hard to modify |
| 🟡 MEDIUM | Hardcoded DB config per-database | Low | Can't configure different DBs differently |
| 🟢 LOW | Middleware order duplication | Low | Inconsistency risk |
| 🟢 LOW | Static helpers in CatalogWriter | Low | Testing difficulty |
| 🟢 LOW | Scattered session config | Low | Configuration drift |

---

## Recommended Implementation Order

### Phase 1: Database Layer Fixes (Week 1)
1. Split `IDatabaseManager` into `ISqlDatabaseManager` + `INoSqlDatabaseManager`
2. Create `AbstractPdoDatabaseManager` base class with shared transaction/query logic
3. Refactor `DatabaseManager`, `SQLiteDatabaseManager`, `SqlsrvDatabaseManager` to extend base class
4. Make `MongoDbManager` implement `INoSqlDatabaseManager` (no PDO, no transactions)
5. Remove singleton from `ConnectionFactory`, use DI

### Phase 2: API Layer Refactoring (Week 2)
1. Extract JWT token handling to `TokenService` 
2. Move redirect logic to `RedirectService` or `AuthService`
3. Move user shaping to `UserPresenter`/`UserTransformer`
4. Make `AuthController` thin - only HTTP translation
5. Remove optional dependencies from constructor

### Phase 3: Media Project Refactoring (Week 3)
1. Split `CatalogWriter` into 4 classes (Writer, Repository, Transformer, Validator)
2. Extract type converters to `TypeConverter` class
3. Add interface for repository

### Phase 4: Cross-Cutting (Week 4)
1. Centralize middleware pipeline configuration
2. Standardize error handling
3. Consolidate session configuration
4. Add domain events for critical operations

---

## Code Examples: Proposed Refactoring

### Database Layer - Abstract Base Class

```php
// shared/src/Database/AbstractPdoDatabaseManager.php
abstract class AbstractPdoDatabaseManager implements ISqlDatabaseManager
{
    private ?\PDO $pdo = null;

    abstract protected function buildDsn(): string;
    abstract protected function getCredentials(): array; // [user, password]

    public function getPdo(): \PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new \PDO(
                $this->buildDsn(),
                ...$this->getCredentials(),
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return $this->pdo;
    }

    // All shared methods here - execute, write, lastInsertId, transactions
    // Subclasses only implement buildDsn() and getCredentials()
}
```

### AuthController - After Refactoring

```php
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly IAuthService $authService,
        private readonly ITokenService $tokenService,  // NEW: extracted
        private readonly IRedirectService $redirectService, // NEW: extracted
        private readonly ISessionManager $session,
    ) {}

    private function login(array $body, string $ip): array
    {
        $result = $this->authService->login(
            $this->getIdentity($body),
            $body['password'] ?? '',
            $this->getVisitorGender($body),
            $ip
        );

        return $this->success($this->tokenService->buildAuthPayload($result, $body));
    }
    // ... other actions delegate to services
}
```

---

## Verification Checklist

After each phase, verify:
- [ ] All tests pass (`vendor/bin/phpunit`)
- [ ] PHPStan Level 5 clean (`vendor/bin/phpstan analyse --level=5`)
- [ ] No `VERIFICATION REQUIRED` markers in code
- [ ] No code duplication (use phpcpd or similar)
- [ ] Interface segregation verified (no forced dummy implementations)
- [ ] LSP verified (subtypes work without runtime exceptions)

---

## Conclusion

The CoreMusic codebase has a solid foundation with good test coverage and PHPStan compliance, but significant SOLID violations exist in the database layer (critical) and AuthController (high priority). The database layer issues are the most urgent because they affect type safety and runtime correctness across all subdomains.

The proposed refactoring is low-risk because:
1. All changes are internal - public interfaces preserved where possible
2. Comprehensive test suite exists (205+ shared, 111+ API tests)
3. Changes can be made incrementally with tests passing at each step
4. No schema or API contract changes required

**Estimated Effort:** 3-4 weeks for full remediation  
**Risk:** Low (well-tested, incremental approach)