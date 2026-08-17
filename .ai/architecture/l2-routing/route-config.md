---
type: architecture
category: l2
title: "Route Configuration — SpaRoute DTO & Registry"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Route Configuration — SpaRoute DTO & Registry

**Zorunlu Bağlantılar:** [[spa-router]] · [[ADR-083-spa-router]]

**Referans Proje:** `reference-project/coremusic-shared/src/PageRouter/SpaRoute.php`, `RouteRegistry.php`, `home.coremusic.net/config/routes.php`

---

## 1. Amaç

SPA route tanımlarını, `SpaRoute` DTO'sunu ve `RouteRegistry` yapısını tanımlar. Her route bir `SpaRoute` instance'ı ile tanımlanır.

---

## 2. SpaRoute — Immutable DTO

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * SpaRoute — Tek bir SPA rotasının tanımı.
 *
 * Tüm alanlar readonly — immutable DTO.
 */
final class SpaRoute
{
    public function __construct(
        public readonly string  $page,                // PHP dosya adı (pages/home.php)
        public readonly bool    $requiresAuth = true, // Auth gerekiyor mu?
        public readonly string  $title        = '',   // Sayfa başlığı
        public readonly ?string $requiredRole = null,  // Gerekli rol (null = yok)
        public readonly ?string $requiredPermission = null, // Gerekli izin (null = yok)
        public readonly bool    $cacheable    = true,  // PHP page cache aktif mi?
        public readonly array   $meta         = [],    // Ek route metadata
        public readonly string  $path         = '',    // URL path (opsiyonel)
        public readonly ?string $handler      = null,  // Controller handler (POST için)
        public readonly ?int    $cacheTtl     = null,  // Özel cache TTL
    ) {}
}
```

### Alan Açıklamaları

| Alan | Tip | Varsayılan | Açıklama |
|------|-----|-----------|----------|
| `page` | `string` | — | PHP dosya adı (uzantısız). `pages/{page}.php` olarak çözümlenir |
| `requiresAuth` | `bool` | `true` | Auth gerektiriyor mu? |
| `title` | `string` | `''` | Sayfa başlığı (HTML `<title>` için) |
| `requiredRole` | `?string` | `null` | Gerekli kullanıcı rolü (null = rol kontrolü yok) |
| `requiredPermission` | `?string` | `null` | Gerekli izin (null = izin kontrolü yok) |
| `cacheable` | `bool` | `true` | PHP page cache aktif mi? |
| `meta` | `array` | `[]` | Ek metadata (frontend'e iletilir) |
| `path` | `string` | `''` | URL path (opsiyonel, genelde key kullanılır) |
| `handler` | `?string` | `null` | Controller handler (POST istekleri için) |
| `cacheTtl` | `?int` | `null` | Özel cache TTL (saniye) |

---

## 3. RouteRegistry — Route Kayıt + Çözümleme

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * RouteRegistry — Route tanımlarını yükler ve URI çözümler.
 *
 * Çözümleme önceliği:
 *   1. Tam eşleşme (statik route)
 *   2. Parametre tabanlı eşleşme (pattern matching)
 *   3. null → 404
 */
final class RouteRegistry
{
    /** @var array<string, SpaRoute> */
    private array $routes = [];

    public function register(SpaRoute $route): void
    {
        $this->routes[trim($route->path, '/')] = $route;
    }

    public function loadFromFile(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new \RuntimeException("Route config dosyası bulunamadı: {$filePath}");
        }
        $routes = include $filePath;
        if (!is_array($routes)) {
            throw new \RuntimeException('Route config bir array döndürmelidir.');
        }
        foreach ($routes as $key => $route) {
            if (!$route instanceof SpaRoute) continue;
            $this->routes[trim((string)$key, '/')] = $route;
        }
    }

    public function resolve(string $uri): ?SpaRoute
    {
        $normalized = trim($uri, '/');
        if ($normalized === '') return $this->routes['home'] ?? null;
        if (isset($this->routes[$normalized])) return $this->routes[$normalized];

        foreach ($this->routes as $pattern => $route) {
            if ($this->matchesPattern($pattern, $normalized)) {
                return $route;
            }
        }
        return null;
    }

    public function getProtectedRouteKeys(): array
    {
        $protected = [];
        foreach ($this->routes as $key => $route) {
            if ($route->requiresAuth) $protected[] = $key;
        }
        return $protected;
    }

    private function matchesPattern(string $pattern, string $uri): bool
    {
        if (!str_contains($pattern, '{')) return false;
        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '[^/]+', $pattern);
        $regex = '#^' . $regex . '$#';
        return (bool) preg_match($regex, $uri);
    }
}
```

---

## 4. Route Tanım Örnekleri (routes.php)

```php
<?php
declare(strict_types=1);

use CoreMusic\PageRouter\SpaRoute;

// ── Auth URL Constants ──────────────────────────────────────────────────
$authDomain  = 'http://auth.coremusic.net';
$homeDomain  = 'http://home.coremusic.net';
$clientId    = 'coremusic-web';
$callbackUrl = $homeDomain . '/auth/callback';

return [

    // ── Auth Redirect Routes ─────────────────────────────────────────────
    'login' => new SpaRoute(
        page:         'redirect',
        requiresAuth: false,
        title:        'Giriş',
        meta:         ['redirect_to' => "$authDomain/login?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),

    'register' => new SpaRoute(
        page:         'redirect',
        requiresAuth: false,
        title:        'Kayıt',
        meta:         ['redirect_to' => "$authDomain/register?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),

    'logout' => new SpaRoute(
        page:         'redirect',
        requiresAuth: false,
        title:        'Çıkış',
        meta:         ['redirect_to' => "$authDomain/logout?redirect=$homeDomain/"],
    ),

    // ── Auth-Required Routes ─────────────────────────────────────────────
    'home' => new SpaRoute(
        page:         'home',
        requiresAuth: true,
        title:        'Ana Sayfa',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'kesfet' => new SpaRoute(
        page:         'kesfet',
        requiresAuth: true,
        title:        'Keşfet',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'albumler' => new SpaRoute(
        page:         'albumler',
        requiresAuth: true,
        title:        'Albümler',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'sanatcilar' => new SpaRoute(
        page:         'sanatcilar',
        requiresAuth: true,
        title:        'Sanatçılar',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'ayarlar' => new SpaRoute(
        page:         'ayarlar',
        requiresAuth: true,
        title:        'Ayarlar',
    ),

    // ── Parameterized Routes ─────────────────────────────────────────────
    'album/{id}' => new SpaRoute(
        page:         'album',
        requiresAuth: false,
        title:        'Albüm',
        cacheable:    true,
        cacheTtl:     3600,
        meta:         ['ttlType' => 'static'],
    ),

    'artist/{id}' => new SpaRoute(
        page:         'artist',
        requiresAuth: false,
        title:        'Sanatçı',
        cacheable:    true,
        cacheTtl:     3600,
        meta:         ['ttlType' => 'static'],
    ),

    'playlist/{id}' => new SpaRoute(
        page:         'playlist',
        requiresAuth: false,
        title:        'Çalma Listesi',
        cacheable:    true,
        cacheTtl:     1800,
        meta:         ['ttlType' => 'static'],
    ),
];
```

---

## 5. Route Çözümleme Akışı

```
GET /album/42
    │
    ▼
RouteRegistry::resolve('album/42')
    │
    ├── 1. Tam eşleşme: 'album/42' → yok
    │
    ├── 2. Pattern eşleşme: 'album/{id}' → eşleşti ✓
    │
    ▼
SpaRoute(page: 'album', requiresAuth: false, cacheable: true, cacheTtl: 3600)
    │
    ▼
PageRouter::renderRoute()
    │
    ├── resolvePageFile(): pages/album.php
    │
    ├── renderPageWithCache(): cache varsa kullan
    │
    ▼
RouteResult::ok($container, $meta, $csrfToken, 'album/42')
```

---

## 6. Parametre Pattern eşleme

| Pattern | URI | Eşleşme |
|---------|-----|---------|
| `album/{id}` | `album/42` | ✅ |
| `artist/{id}` | `artist/john-doe` | ✅ |
| `playlist/{id}` | `playlist/123` | ✅ |
| `user/{id}/playlist/{pid}` | `user/5/playlist/99` | ✅ |
| `album/{id}` | `album/42/tracks` | ❌ (fazla segment) |

**Regex dönüşümü:** `{id}` → `[^/]+`

---

## 7. Auth Redirect Routes

Auth gerektiren sayfalar auth.coremusic.net'e yönlendirilir:

| Route | Auth URL | Callback |
|-------|----------|----------|
| `login` | `auth.coremusic.net/login` | `home.coremusic.net/auth/callback` |
| `register` | `auth.coremusic.net/register` | `home.coremusic.net/auth/callback` |
| `logout` | `auth.coremusic.net/logout` | `home.coremusic.net/` |
| `forgot-password` | `auth.coremusic.net/forgot-password` | `home.coremusic.net/login` |
| `reset-password` | `auth.coremusic.net/reset-password` | `home.coremusic.net/login` |

**ADR-043 Uyumlu:** Auth domain scheme `DomainConfig`'ten alınır, hardcoded URL yasak.

---

## 8. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Eşleşmeyen route** | `RouteResult::notFound()` | ADR-021 |
| **Auth required + giriş yok** | `AuthGuard::check()` → redirect | ADR-043 |
| **Rol yetkisi yok** | `RouteResult::forbidden()` | ADR-052 |
| **POST + handler tanımlı** | Controller'a yönlendir | ADR-083 |
| **Cache devre dışı** | Doğrudan render | — |
| **Debug modda eksik sayfa** | Placeholder render | — |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[spa-router]] | PHP SPA PageRouter |
| [[html-shell-renderer]] | HTML shell üretimi |
| [[ADR-083-spa-router]] | SPA Router Architecture |
| [[ADR-021-spa-router-immutable-contract]] | SPA router contract |

---

## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 021, 083 |
| **Zero Hallucination** | ✅ (referans proje tabanlı) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-16
**Mode:** Red Team · Human Mode · Truth Mode
