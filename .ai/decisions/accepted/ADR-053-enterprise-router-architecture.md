---
type: adr
category: routing
title: "ADR-053: Enterprise Router Architecture"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-053: Enterprise Router Architecture

**Status:** Active
**Kategorisi:** Routing
**İlgili Agent:** [[.agents/backend-architect]]
**İlgili Division:** Software Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformunda **sıfırdan** tasarlanacak Enterprise seviyesinde PHP Router'ın mimarisini, PSR uyumlu yapısını, Attribute tabanlı route tanımlamayı, Dependency Injection entegrasyonunu, Middleware desteğini, Route Group yapısını ve Route Cache mekanizmasını tanımlar.

**Mevcut router kullanılmayacaktır. Sıfırdan yazılacaktır.**

---

## 2. Bağlam

### 2.1 Mevcut Durum

Referans projede basit bir router mevcuttu. Yeni sistemde Enterprise seviyesinde bir router gereklidir.

### 2.2 Gereksinimler

| # | Gereksinim | Açıklama |
|---|------------|----------|
| R1 | PSR-15 Uyumlu | Middleware pipeline desteği |
| R2 | Attribute Destekli | PHP 8 Attribute ile route tanımlama |
| R3 | DI Container Entegre | PSR-11 uyumlu container |
| R4 | Route Group | Prefix bazlı gruplandırma |
| R5 | Route Cache | Production için route önbellek |
| R6 | Subdomain Routing | Multi-domain destek |
| R7 | Named Routes | URL oluşturma için isimlendirme |
| R8 | Parameter Extraction | Dinamik parametre çıkarma |
| R9 | HTTP Method Farklılığı | GET, POST, PUT, DELETE vb. |
| R10 | Fallback Route | 404 handler |
| R11 | SOLID | Tek sorumluluk, açık kapalılık |
| R12 | Clean Architecture | Routing katmanı bağımsız |

### 2.3 Teknoloji Seçimi

| Bileşen | Seçim | Neden |
|---------|-------|-------|
| Router Engine | `nikic/fast-route` | Performans, PHP 8.4 uyumlu, PSR-15 uyumlu |
| DI Container | `php-di/php-di` | PSR-11, PHP 8 attribute desteği |
| HTTP Message | `nyholm/psr7` | PSR-7, hafif, PHP 8.4 uyumlu |
| HTTP Handler | `laminas/laminas-httphandlerrunner` | PSR-15 uyumlu emitter |

---

## 3. Karar

CoreMusic'te **nikic/fast-route** tabanlı, PSR-15 uyumlu, Attribute destekli Enterprise Router kullanılacaktır.

### 3.1 Router Mimarisi

```
┌─────────────────────────────────────────────────────────┐
│                    HTTP KERNEL                           │
│  ServerRequest → Middleware Pipeline → Router → Response │
├─────────────────────────────────────────────────────────┤
│                    ROUTER LAYER                          │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │ Route         │  │ Route        │  │ Route        │ │
│  │ Collector     │  │ Dispatcher   │  │ Cache        │ │
│  │               │  │              │  │              │ │
│  │ • Attribute   │  │ • Matching   │  │ • APCu       │ │
│  │ • Config file │  │ • Params     │  │ • File       │ │
│  │ • Group       │  │ • Controller │  │ • Invalid    │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
├─────────────────────────────────────────────────────────┤
│                    DI CONTAINER                          │
│  PSR-11 • php-di/php-di • Constructor Injection         │
├─────────────────────────────────────────────────────────┤
│                    MIDDLEWARE                             │
│  PSR-15 • ServerMiddlewareInterface • Pipeline           │
└─────────────────────────────────────────────────────────┘
```

### 3.2 Route Tanımlama — Config File

```php
<?php
// config/routes.php
declare(strict_types=1);

/** @var \CoreMusic\Router\RouteCollector $routes */

// ===== AUTH SERVICE (auth.coremusic.net) =====
$routes->group('/auth', function ($routes) {
    $routes->get('/login', [LoginController::class, 'showForm']);
    $routes->post('/login', [LoginController::class, 'login']);
    $routes->get('/register', [RegisterController::class, 'showForm']);
    $routes->post('/register', [RegisterController::class, 'register']);
    $routes->post('/logout', [LogoutController::class, 'logout']);
    $routes->get('/password/forgot', [PasswordResetController::class, 'showForgotForm']);
    $routes->get('/password/reset', [PasswordResetController::class, 'showResetForm']);
    $routes->post('/password/reset', [PasswordResetController::class, 'reset']);
})->middleware('rate-limit:login');

// Auth API
$routes->group('/api/session', function ($routes) {
    $routes->get('/check', [SessionCheckController::class, 'check']);
    $routes->post('/refresh', [TokenRefreshController::class, 'refresh']);
    $routes->post('/revoke', [TokenRevokeController::class, 'revoke']);
})->middleware('auth');

// ===== MUSIC SERVICE (music.coremusic.net) =====
$routes->group('/', function ($routes) {
    $routes->get('/', [MusicController::class, 'home']);
    $routes->get('/kesfet', [MusicController::class, 'kesfet']);
    $routes->get('/albumler', [MusicController::class, 'albumler']);
    $routes->get('/sanatcilar', [MusicController::class, 'sanatcilar']);
    $routes->get('/goz-at', [MusicController::class, 'gozAt']);
    $routes->get('/gecmis', [MusicController::class, 'gecmis']);
    $routes->get('/ayarlar', [MusicController::class, 'ayarlar']);
    $routes->get('/hakkimizda', [MusicController::class, 'hakkimizda']);
})->middleware('auth');

// Music API
$routes->group('/api', function ($routes) {
    $routes->get('/songs', [SongApiController::class, 'index']);
    $routes->get('/songs/{id}', [SongApiController::class, 'show']);
    $routes->get('/playlists', [PlaylistApiController::class, 'index']);
    $routes->post('/playlists', [PlaylistApiController::class, 'create']);
    $routes->put('/playlists/{id}', [PlaylistApiController::class, 'update']);
    $routes->delete('/playlists/{id}', [PlaylistApiController::class, 'delete']);
})->middleware('auth')->middleware('rate-limit:api');
```

### 3.3 Route Tanımlama — Attribute (PHP 8)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Controller;

use CoreMusic\Router\Attributes\Route;
use CoreMusic\Router\Attributes\Middleware;

#[Route('/auth')]
class AuthController
{
    #[Route('/login', methods: ['GET'])]
    #[Middleware('rate-limit:login')]
    public function showForm(): string
    {
        // Login formu göster
    }

    #[Route('/login', methods: ['POST'])]
    #[Middleware('csrf')]
    #[Middleware('rate-limit:login')]
    public function login(ServerRequestInterface $request): ResponseInterface
    {
        // Login işlemi
    }

    #[Route('/register', methods: ['GET'])]
    public function showRegisterForm(): string
    {
        // Kayıt formu göster
    }

    #[Route('/register', methods: ['POST'])]
    #[Middleware('csrf')]
    #[Middleware('rate-limit:register')]
    public function register(ServerRequestInterface $request): ResponseInterface
    {
        // Kayıt işlemi
    }

    #[Route('/logout', methods: ['POST'])]
    #[Middleware('auth')]
    #[Middleware('csrf')]
    public function logout(): ResponseInterface
    {
        // Logout işlemi
    }
}
```

### 3.4 Route Group

```php
<?php
declare(strict_types=1);

// Auth routes — prefix: /auth
$routes->group('/auth', function ($routes) {
    $routes->get('/login', [AuthController::class, 'showForm']);
    $routes->post('/login', [AuthController::class, 'login']);
    $routes->get('/register', [AuthController::class, 'showRegisterForm']);
    $routes->post('/register', [AuthController::class, 'register']);
    $routes->post('/logout', [AuthController::class, 'logout']);
})->middleware('rate-limit:login');

// API routes — prefix: /api
$routes->group('/api', function ($routes) {
    $routes->get('/songs', [SongController::class, 'index']);
    $routes->get('/songs/{id}', [SongController::class, 'show']);
    $routes->post('/songs', [SongController::class, 'create']);
    $routes->put('/songs/{id}', [SongController::class, 'update']);
    $routes->delete('/songs/{id}', [SongController::class, 'delete']);
})->middleware('auth')->middleware('rate-limit:api');

// Admin routes — prefix: /admin
$routes->group('/admin', function ($routes) {
    $routes->get('/', [AdminController::class, 'dashboard']);
    $routes->get('/users', [AdminController::class, 'users']);
    $routes->get('/settings', [AdminController::class, 'settings']);
})->middleware('auth')->middleware('role:admin');
```

### 3.5 DI Container Entegrasyonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Container;

use DI\ContainerBuilder;

class ContainerFactory
{
    public static function create(): \Psr\Container\ContainerInterface
    {
        $builder = new ContainerBuilder();

        // Config dosyalarını yükle
        $builder->addDefinitions(__DIR__ . '/../config/services.php');

        // Attribute tabanlı controller'ları tara
        $builder->addDefinitions(
            (new \CoreMusic\Router\AttributeScanner())->scan()
        );

        return $builder->build();
    }
}
```

**services.php:**
```php
<?php
declare(strict_types=1);

use CoreMusic\Auth\Infrastructure\Persistence\PdoUserRepository;
use CoreMusic\Auth\Infrastructure\Persistence\PdoSessionRepository;
use CoreMusic\Auth\Infrastructure\Security\Argon2idPasswordHasher;
use CoreMusic\Auth\Infrastructure\Security\JwtTokenManager;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;

return [
    // Interfaces → Implementations
    UserRepositoryInterface::class => \DI\autowire(PdoUserRepository::class),
    SessionRepositoryInterface::class => \DI\autowire(PdoSessionRepository::class),

    // Security
    Argon2idPasswordHasher::class => \DI\autowire(Argon2idPasswordHasher::class),
    JwtTokenManager::class => \DI\autowire(JwtTokenManager::class),

    // PDO
    \PDO::class => function (\Psr\Container\ContainerInterface $c) {
        return new \PDO(
            getenv('DB_DSN'),
            getenv('DB_USER'),
            getenv('DB_PASS'),
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    },
];
```

### 3.6 Route Cache

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Router\Cache;

class RouteCache
{
    private string $cacheDir;
    private ?array $cachedRoutes = null;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * Production'da route'ları cache'le.
     */
    public function getRoutes(): ?array
    {
        if ($this->cachedRoutes !== null) {
            return $this->cachedRoutes;
        }

        $cacheFile = $this->cacheDir . '/routes.cache';

        if (file_exists($cacheFile)) {
            $this->cachedRoutes = require $cacheFile;
            return $this->cachedRoutes;
        }

        return null;
    }

    /**
     * Route'ları cache'e yaz.
     */
    public function setRoutes(array $routes): void
    {
        $cacheFile = $this->cacheDir . '/routes.cache';
        $content = '<?php return ' . var_export($routes, true) . ';';
        file_put_contents($cacheFile, $content);
    }

    /**
     * Cache'i temizle.
     */
    public function clear(): void
    {
        $cacheFile = $this->cacheDir . '/routes.cache';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
        $this->cachedRoutes = null;
    }
}
```

### 3.7 Subdomain Routing

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Router;

class SubdomainRouter
{
    private array $subdomainMap = [
        'music'    => 'music.coremusic.net',
        'admin'    => 'admin.coremusic.net',
        'auth'     => 'auth.coremusic.net',
        'api'      => 'api.coremusic.net',
        'media'    => 'media.coremusic.net',
        'download' => 'download.coremusic.net',
        'home'     => 'home.coremusic.net',
        'studio'   => 'studio.coremusic.net',
        'pro'      => 'pro.coremusic.net',
        'car'      => 'car.coremusic.net',
    ];

    public function detect(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $parts = explode('.', $host);
        return $parts[0] ?? '';
    }

    public function getSubdomainConfig(string $subdomain): ?string
    {
        return $this->subdomainMap[$subdomain] ?? null;
    }
}
```

### 3.8 Named Routes

```php
<?php
declare(strict_types=1);

// Route tanımlarken isim verme
$routes->get('/login', [AuthController::class, 'showForm'])->name('auth.login');
$routes->post('/login', [AuthController::class, 'login'])->name('auth.login.post');
$routes->get('/kesfet', [MusicController::class, 'kesfet'])->name('music.kesfet');

// URL oluştururken isim kullanma
$url = $router->url('auth.login');           // /login
$url = $router->url('music.kesfet');        // /kesfet
$url = $router->url('song.show', ['id' => 5]); // /songs/5
```

### 3.9 Route Dispatcher Flow

```
HTTP Request
  │
  ▼
Subdomain Detection (music / admin / auth / ...)
  │
  ▼
Route Matching (nikic/fast-route)
  │
  ├── Match found → Extract params
  │     │
  │     ▼
  │   DI Container → Resolve controller
  │     │
  │     ▼
  │   Middleware Pipeline (PSR-15)
  │     │
  │     ▼
  │   Controller → handle()
  │     │
  │     ▼
  │   Response (PSR-7)
  │
  └── No match → 404 Handler
```

---

## 4. Teknik Detaylar

### 4.1 Route Parametreleri

```php
// Basit parametre
$routes->get('/songs/{id}', [SongController::class, 'show']);

// Regex kısıtlaması
$routes->get('/songs/{id:\d+}', [SongController::class, 'show']);

//_opsiyonel parametre
$routes->get('/songs/{id?}', [SongController::class, 'index']);

// Çoklu parametre
$routes->get('/artists/{artistId}/songs/{songId}', [ArtistSongController::class, 'show']);
```

### 4.2 HTTP Method Desteği

```php
$routes->get('/songs', [SongController::class, 'index']);      // GET
$routes->post('/songs', [SongController::class, 'create']);    // POST
$routes->put('/songs/{id}', [SongController::class, 'update']); // PUT
$routes->delete('/songs/{id}', [SongController::class, 'delete']); // DELETE
$routes->patch('/songs/{id}', [SongController::class, 'patch']); // PATCH
```

### 4.3 Fallback Route

```php
// Tanımsız route'lar için
$routes->fallback(function () {
    http_response_code(404);
    return '404 - Sayfa Bulunamadı';
});
```

### 4.4 Route Listesi (Debug)

```php
// Development'ta tüm route'ları listele
if (getenv('APP_ENV') === 'development') {
    $routes->get('/debug/routes', function () use ($router) {
        header('Content-Type: application/json');
        return json_encode($router->getRouteList(), JSON_PRETTY_PRINT);
    });
}
```

---

## 5. Yasak Örüntüleri

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | Mevcut router kullanma | Sıfırdan yazım | ADR-051 |
| 2 | Hardcoded URL'ler | Named routes | Bu ADR |
| 3 | Hash-based routing | pushState | ADR-021 |
| 4 | Sync route loading | Lazy loading | ADR-021 |
| 5 | Controller'da iş mantığı | Service layer | Clean Architecture |
| 6 | Controller'da DB erişimi | Repository pattern | SOLID |
| 7 | Global fonksiyonlar | Class-based | PSR-12 |
| 8 | `require_once` | Autoloading (Composer) | PSR-4 |

---

## 6. Edge Cases

| # | Edge Case | Çözüm | ADR |
|---|-----------|-------|-----|
| 1 | Route cache bozulması | Cache invalidation + rebuild | Bu ADR |
| 2 | Subdomain yok | Default route fallback | Bu ADR |
| 3 | Method not allowed | 405 Method Not Allowed | Bu ADR |
| 4 | Circular redirect | Max redirect limit (5) | Bu ADR |
| 5 | Large route list | Route cache (APCu) | Bu ADR |
| 6 | Concurrent route modification | File locking | Bu ADR |
| 7 | Invalid controller | DI container error | Bu ADR |
| 8 | Middleware chain break | Short-circuit response | Bu ADR |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | PSR-15 Middleware | Tüm middleware PSR-15 | Uyumsuzluk |
| G2 | Named routes | URL oluşturma için zorunlu | Bakım zorluğu |
| G3 | Route cache | Production'da zorunlu | Performans düşüşü |
| G4 | DI Container | Controller resolver zorunlu | Bağımlılık enjeksiyonu |
| G5 | Subdomain routing | Multi-domain destek zorunlu | Dağınık yapı |
| G6 | Fallback route | 404 handler zorunlu | Kullanıcı kaybı |
| G7 | SOLID | Controller SRP | Karmaşıklık |
| G8 | Clean Architecture | Routing katmanı bağımsız | Katman ihlali |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Subdomain routing |
| [[ADR-009-clean-url-redirect]] | Clean URL | URL normalizasyonu |
| [[ADR-021-spa-router-immutable-contract]] | SPA router | Client-side routing |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | PHP 8.4 |
| [[ADR-051-platform-rewrite-from-scratch]] | Platform rewrite | Sıfırdan yazım |
| [[ADR-054-enterprise-composer-stack]] | Composer stack | Paket seçimi |

---

## 9. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 1.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-09 |
| Sections | 9 |
| Route Features | 12 |
| Composer Paket | 4 |
| Yasak Örüntü | 8 |
| Edge Cases | 8 |
| Hard Guardrails | 8 |
| İlgili ADR | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
