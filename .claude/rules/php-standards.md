# PHP Standards — CoreMusic

**Authority:** ADR-002, ADR-042, ADR-051, ADR-053, ADR-054, ADR-058, ADR-059
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team • Human Mode • Truth Mode

---

## 1. Mandatory

- `declare(strict_types=1)` at top of every PHP file
- PSR-12 coding standard
- Constructor injection for all dependencies
- Interface segregation (small, focused interfaces)
- PHP 8.4+ (ADR-042/C1)

## 2. Database Access (ADR-002)

- PDO with prepared statements ONLY
- Raw SQL concatenation: ABSOLUTELY FORBIDDEN
- ORM usage: ABSOLUTELY FORBIDDEN
- Explicit column lists (SELECT * FORBIDDEN)
- Soft delete: `is_deleted = 0` pattern

```php
// ✅ DOĞRU — PDO Prepared Statement
$stmt = $this->pdo->prepare(
    'SELECT id, title, artist_id FROM songs WHERE is_deleted = 0 AND id = :id'
);
$stmt->execute([':id' => $songId]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// ❌ YANLIŞ — SQL injection + ORM yasağı
$result = $this->pdo->query("SELECT * FROM songs WHERE id = $songId");
```

## 3. Auth Subdomain Priority (ADR-058)

### Faz 1 — Öncelikli (Şimdi yapılacak)

| Subdomain | Port | Auth |
|-----------|------|------|
| home.coremusic.net | 81 (dev), 80/443 (prod) | ✅ |
| car.coremusic.net | 80 | ✅ |
| pro.coremusic.net | 81 (dev), 80/443 (prod) | ✅ |
| studio.coremusic.net | 81 (dev), 80/443 (prod) | ✅ |
| media.coremusic.net | 5000/6000 | ✅ |

### Faz 2 — Sonra yapılacak

| Subdomain | Port | Auth |
|-----------|------|------|
| music.coremusic.net | 81 (dev), 80/443 (prod) | ✅ |
| admin.coremusic.net | 80 | ✅ |
| api.coremusic.net | 81 (dev), 80/443 (prod) | ✅ |
| download.coremusic.net | 3001 | ✅ |

### Auth Flow

```
home or car or pro or studio or media .coremusic.net
        │
        ▼
auth.coremusic.net
        │
        ▼
Login → Session → JWT → Redirect
        │
        ▼
origin subdomain (.coremusic.net)
```

## 4. Development Mode (ADR-058)

| Özellik | Development | Production |
|---------|-------------|------------|
| Protocol | HTTP | HTTPS |
| Port | 81 (music), 80 (admin) | 80/443 |
| Cookie Secure | false | true |
| JWT | HS256 (test key) | RS256 (prod key) |
| Rate Limit | Devre dışı | Aktif |
| BypassAuth | `?_bypass=1` | Devre dışı |

```php
// Development auth bypass
if (APP_ENV === 'development' && isset($_GET['_bypass'])) {
    $_SESSION['MM_UserID'] = 1;
    $_SESSION['MM_Username'] = 'testuser';
}
// Production'da kesinlikle devre dışı
```

## 3. Security (ADR-022)

- Argon2id for password hashing (Memory: 64MB, Time: 4, Threads: 2)
- AES-256-GCM for credential encryption (96-bit IV, 16-byte tag)
- `hash_equals()` for CSRF token comparison (timing-safe)
- All secrets from `.env` file, never hardcoded

```php
// ✅ DOĞRU — AES-256-GCM Encryption
$ciphertext = openssl_encrypt(
    $plaintext, 'aes-256-gcm', $key,
    OPENSSL_RAW_DATA, $iv, $tag, '', 16
);
```

## 4. Middleware Pipeline Order (Frozen — ADR-010/011/012/013/022)

```php
// Değişmez sıra (array_reverse ile içeriden dışa):
$middlewares = [
    new SessionManagerMiddleware(),    // 1. Session başlat (CSP nonce üretimi)
    new BypassAuthMiddleware(),         // 2. Test bypass (prod'da devre dışı)
    new RateLimiterMiddleware(),        // 3. APCu: 60 req/60s
    new AuthMiddleware(),               // 4. Auth bilgisi inject (Hybrid: Session + JWT — ADR-052)
    new SecurityHeadersMiddleware(),    // 5. CSP strict-dynamic
    new CsrfMiddleware(),              // 6. csrf_token doğrulama (POST/PUT/DELETE)
];
```

**Order is FROZEN** (ADR-010/011/012/013/022). Never change.

**Hybrid Auth:** Auth middleware hem session cookie'yi hem JWT token'ı doğrular.
*Detay: [[ADR-052-hybrid-auth-architecture]]*

## 5. Error Handling

- Use structured logging, not var_dump
- Never expose stack traces in production
- Audit trail for all critical operations
- PSR-3 compatible logging

## 6. File Structure (ADR-051)

```
shared/src/
  Auth/
    Domain/           — Entities, Repository interfaces
    Application/      — Use cases, DTOs
    Infrastructure/   — PDO repositories, Security, HTTP
  Security/
    Middleware/        — 6-layer middleware pipeline
    Service/           — CSP, Rate Limiter, Security Headers
  Http/
    Kernel.php         — HTTP kernel
    Request/           — PSR-7 request factory
    Response/          — PSR-7 response emitter
  Router/
    Router.php         — Enterprise router (nikic/fast-route)
    RouteCollector.php — Route collection
    RouteDispatcher.php — Route dispatching
    Attributes/        — PHP 8 attributes (Route, Middleware)
    Cache/             — Route cache (APCu/File)
  Container/
    ContainerFactory.php — DI container (PSR-11)
  Event/
    EventDispatcherFactory.php — Event dispatcher (PSR-14)

{subdomain}/
  public/
    index.php          — Entry point
  src/
    Controller/        — Request handlers
    Pages/             — PHP templates
    config/
      routes.php       — Route definitions
  tests/
    Unit/
    Integration/
```

*Detay: [[ADR-051-platform-rewrite-from-scratch]], [[ADR-053-enterprise-router-architecture]]*

## 7. Forbidden

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| ORM (Eloquent, Doctrine) | Raw PDO |
| `SELECT *` | Explicit columns |
| Raw SQL concatenation | Prepared statements |
| Hardcoded secrets | `.env` file |
| `var_dump()` | Structured logging |
| Stack traces in prod | Error pages |
| `require_once` in classes | Autoloading |

## 8. Auth Subdomain (ADR-043 + ADR-052)

All auth code is consolidated in `auth.coremusic.net`:
- `LoginController.php` — login form + login handler
- `RegisterController.php` — register form + register handler
- `LogoutController.php` — logout handler
- `PasswordResetController.php` — forgot + reset password
- `SessionCheckController.php` — session/JWT validation API
- `TokenRefreshController.php` — JWT refresh token rotation

**Hybrid Auth (ADR-052):**
- Session cookie: `COREMUSIC_SESS` (HttpOnly, Secure, SameSite=Lax)
- Access JWT: 15 min TTL, RS256
- Refresh JWT: 7 days TTL, RS256
- Key rotation: 90 days

*Detay: [[ADR-052-hybrid-auth-architecture]]*

## 9. Enterprise Router (ADR-053)

- Engine: `nikic/fast-route`
- DI: `php-di/php-di` (PSR-11)
- HTTP: `nyholm/psr7` (PSR-7)
- Middleware: PSR-15
- Features: Attribute routes, Route groups, Route cache, Named routes, Subdomain routing

*Detay: [[ADR-053-enterprise-router-architecture]]*

## 10. Composer Stack (ADR-054)

Minimum Enterprise: 25 packages (require) + 5 (require-dev)
Password: PHP native `password_hash()` — no extra package
Encryption: `paragonie/halite` + `paragonie/sodium_compat`
JWT: `firebase/php-jwt`

*Detay: [[ADR-054-enterprise-composer-stack]]*

## 11. API Architecture (Enterprise)

### Contract First (Zorunlu)

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

Kod sözleşmeden önce **ASLA** yazılmaz.

### Response Format (Zorunlu)

```json
{
  "success": true,
  "data": {},
  "meta": { "timestamp": "...", "request_id": "..." }
}
```

### URL Kuralları

| Kural | Doğru | Yanlış |
|-------|-------|--------|
| lowercase | `/api/v1/songs` | `/api/v1/Songs` |
| plural | `/api/v1/songs` | `/api/v1/song` |
| kebab-case | `/api/v1/songs/{id}/cover-art` | `/api/v1/songs/{id}/coverArt` |
| no verbs | `GET /api/v1/songs` | `POST /api/v1/getSongs` |

### Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| URL | kebab-case | `/api/v1/songs/{id}/cover-art` |
| JSON Key | snake_case | `created_at` |
| PHP Class | PascalCase | `SongRepository` |
| PHP Method | camelCase | `findById()` |
| DB Column | snake_case | `created_at` |
| Error Code | UPPER_SNAKE | `SONG_NOT_FOUND` |

### Forbidden (API)

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| SPA → PDO | SPA → ApiClient → Gateway |
| Controller → Repository | Controller → Use Case → Repository |
| `POST /getSongs` | `GET /api/v1/songs` |
| Tek monolitik shared | Modüler `coremusic/*` paketler |

*Detay: [[architecture/03-contracts/api-architecture-master]], [[architecture/03-contracts/api-design-rules]], [[architecture/03-contracts/api-design-rules]]*

## 12. Modüler Shared Library

```
coremusic/contracts      ← DTO, Enums, ValueObjects
coremusic/http           ← HttpClient, ApiClient
coremusic/auth           ← Auth Client, JWT
coremusic/security       ← CSRF, RateLimiter
coremusic/cache          ← Cache Interface
coremusic/events         ← Event Dispatcher (PSR-14)
coremusic/validation     ← Request Validation
coremusic/storage        ← Storage Interface
coremusic/logger         ← PSR-3 Logger
coremusic/monitoring     ← Metrics, Health Check
coremusic/websocket      ← WebSocket Client/Server
coremusic/sdk            ← Client SDK
coremusic/api-client     ← Typed API Client
```

**Kural:** Tek monolitik `shared/` paketi yasak. Her modül bağımsız Composer paketi olarak yayınlanır.

*Detay: [[architecture/03-contracts/shared-library]]*

---

*PHP Standards v3.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-09*
