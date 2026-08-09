---
title: "CoreMusic — Comprehensive System Audit & Fix Plan"
date: 2026-08-05
version: 1.0.0
scope: Full System (music, auth, shared, assets)
mode: Red Team • Human Mode • Truth Mode
---

# CoreMusic — Comprehensive System Audit & Fix Plan

**Tarih:** 2026-08-05
**Kapsam:** Tüm sistem — music.coremusic.net, auth.coremusic.net, coremusic-shared, assets.coremusic.net
**Hedef:** SOLID, Clean Code, security, path, image ve dead code sorunlarını tek planda çözmek
**Sınırlama:** Theme Manager yazılmayacak, görseller hard-coded kalacak

---

## TOC (Table of Contents)

1. [Executive Summary](#1-executive-summary)
2. [Audit Findings Summary](#2-audit-findings-summary)
3. [Phase 1: CRITICAL Security Fixes](#3-phase-1-critical-security-fixes)
4. [Phase 2: Broken Path & Image Fixes](#4-phase-2-broken-path--image-fixes)
5. [Phase 3: SOLID Violations — DIP Fixes](#5-phase-3-solid-violations--dip-fixes)
6. [Phase 4: SOLID Violations — SRP Fixes](#6-phase-4-solid-violations--srp-fixes)
7. [Phase 5: SOLID Violations — DRY Fixes](#7-phase-5-solid-violations--dry-fixes)
8. [Phase 6: Clean Code — Dead Code Cleanup](#8-phase-6-clean-code--dead-code-cleanup)
9. [Phase 7: Clean Code — Inline Styles → CSS](#9-phase-7-clean-code--inline-styles--css)
10. [Phase 8: Route Fixes](#10-phase-8-route-fixes)
11. [Phase 9: Hard-coded Paths → Dynamic Config](#11-phase-9-hard-coded-paths--dynamic-config)
12. [Phase 10: Image Management (Hard-coded Theme)](#12-phase-10-image-management-hard-coded-theme)
13. [Phase 11: JS Architecture Fixes](#13-phase-11-js-architecture-fixes)
14. [Phase 12: Auth Subdomain Fixes](#14-phase-12-auth-subdomain-fixes)
15. [Phase 13: Shared Library Fixes](#15-phase-13-shared-library-fixes)
16. [Phase 14: PHP Config Cleanup](#16-phase-14-php-config-cleanup)
17. [Phase 15: Testing & Verification](#17-phase-15-testing--verification)
18. [Phase 16: Vault Sync & Log](#18-phase-16-vault-sync--log)
19. [Execution Order & Dependencies](#19-execution-order--dependencies)
20. [Risk Matrix](#20-risk-matrix)
21. [File Change Manifest](#21-file-change-manifest)
22. [Success Criteria](#22-success-criteria)

---

## 1. Executive Summary

### Toplamlar

| Kategori | Adet | Öncelik |
|----------|------|---------|
| CRITICAL Security | 2 | Hemen |
| HIGH Security | 6 | Bugün |
| Broken Image Refs | 10 | Bugün |
| Broken Routes | 5 | Bugün |
| Hard-coded Paths | 8 | Bugün |
| SOLID DIP Violations | 12 | Bu sprint |
| SOLID SRP Violations | 6 | Bu sprint |
| DRY Violations | 8 | Bu sprint |
| Dead Code | 15+ | Bu sprint |
| Inline Styles | 83 | Aşamalı |
| Missing Page Files | 5 | Bugün |
| Broken Nav Links | 7 | Bugün |

### Toplam Etkilenen Dosya Sayısı: ~60

---

## 2. Audit Findings Summary

### 2.1 CRITICAL: Credential Exposure

| Dosya | Sorun |
|-------|-------|
| `music.coremusic.net/.env:37` | `DB_PASSWORD=ali**` — plain text |
| `music.coremusic.net/.env:83` | `APP_PEPPER=c7aaaa6...` — plain text |
| `auth.coremusic.net/.env:8` | Aynı credential'lar (duplicate) |

### 2.2 HIGH: Hardcoded Windows Paths

| Dosya:Satır | Path |
|-------------|------|
| `music.coremusic.net/config/config.php:27` | `C:\www\coremusic.net\coremusic_php_errors.log` |
| `music.coremusic.net/.htaccess:11` | `C:/www/coremusic.net/coremusic_php_errors.log` |
| `music.coremusic.net/.user.ini:14` | `C:\www\coremusic.net\music.coremusic.net\cache\.opcache` |
| `coremusic-shared/src/Security/SecurityHelper.php:31` | `dirname(__DIR__, 4) . '/coremusic_php_errors.log'` |
| `coremusic-shared/src/PageRouter/PageRouterKernel.php:25` | `__DIR__ . '/../../../config/routes.php'` |
| `coremusic-shared/src/PageRouter/HtmlShellRenderer.php:52` | `dirname(__DIR__, 4) . '/assets.coremusic.net'` |
| `coremusic-shared/src/Cache/FileAdapter.php:17` | `__DIR__ . '/../../../../cache'` |
| `coremusic-shared/fix_namespaces.php` | `c:/www/coremusic.net/coremusic-shared/src` |

### 2.3 Broken Image References

| # | Kırık Referans | Kaynak Dosya |
|---|---------------|---------------|
| 1 | `Images/Users/default.png` | `music.coremusic.net/header.php:93` |
| 2 | `Images/Albums/mock1.jpg` | `music.coremusic.net/home.php:51` |
| 3 | `Images/Albums/mock2.jpg` | `music.coremusic.net/home.php:65` |
| 4 | `Images/Albums/mock3.jpg` | `music.coremusic.net/home.php:79` |
| 5 | `Images/Albums/mock4.jpg` | `music.coremusic.net/home.php:93` |
| 6 | `Images/default-album.png` | `music.coremusic.net/home.php:51,65,79,93` |
| 7 | `/Image/album-icon.png` | `assets.coremusic.net/js/core/coreplayer.controls.js:227` |
| 8 | `/Image/footer-controls/play-btn.svg` | `assets.coremusic.net/js/core/coreplayer.controls.js:228` |
| 9 | `/Image/footer-controls/pause-btn.svg` | `assets.coremusic.net/js/core/coreplayer.controls.js:229` |
| 10 | `Image/background/bkimage2.jpg` | `coremusic-shared/src/PageRouter/HtmlShellRenderer.php:281` |

### 2.4 Missing Page Files (Routes)

| Route | Eksik Dosya |
|-------|-------------|
| `album/{id}` | `pages/album.php` |
| `artist/{id}` | `pages/artist.php` |
| `playlist/{id}` | `pages/playlist.php` |
| `genre/{id}` | `pages/genre.php` |
| `track/{id}` | `pages/track.php` |

### 2.5 Broken Navigation Links

| Link | Header.php Satırı | Durum |
|------|-------------------|-------|
| `/kesfet` | 67 | Route yok |
| `/albumler` | 68 | Route yok |
| `/sanatcilar` | 69 | Route yok |
| `/goz-at` | 70 | Route yok |
| `/gecmis` | 71 | Route yok |
| `/ayarlar` | 72 | Route yok |
| `/hakkimizda` | 73 | Route yok |

---

## 3. Phase 1: CRITICAL Security Fixes

### 3.1 Credential Rotation & .env Protection

**Etkilenen Dosyalar:**
- `music.coremusic.net/.env`
- `music.coremusic.net/.gitignore`
- `auth.coremusic.net/.env`
- `auth.coremusic.net/.gitignore`

**Yapılacaklar:**

1. **`.env` dosyalarını `.gitignore`'a ekle:**
```gitignore
# Environment files (NEVER commit)
.env
.env.local
.env.production
```

2. **`.env.example` oluştur (music.coremusic.net):**
```env
APP_ENV_MODE=development
APP_NAME=CoreMusic
APP_VERSION=2.0.0
APP_TIMEZONE=Europe/Istanbul

DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=coremusic
DB_USER=root
DB_PASSWORD=CHANGE_ME
DB_PORT=3306
DB_CHARSET=utf8mb4

DB_AUTH_NAME=coremusic_auth
DB_USER_NAME=coremusic_user
DB_MUSICS_NAME=coremusic_musics
DB_ALBUMS_NAME=coremusic_albums
DB_PLAYLIST_NAME=coremusic_playlist
DB_CATALOG_NAME=coremusic_catalog
DB_LOGS_NAME=coremusic_logs
DB_MEDIA_NAME=coremusic_media
DB_SYSTEM_NAME=coremusic_system

SESSION_NAME=COREMUSIC_SESS
SESSION_LIFETIME=7200

CSRF_TOKEN_LENGTH=32
CSRF_TOKEN_LIFETIME=3600

RATE_LIMIT_MAX=60
RATE_LIMIT_WINDOW=60

CACHE_ENABLED=true
CACHE_PAGE_ENABLED=true
CACHE_PREFIX=coremusic_
CACHE_TTL_DEFAULT=3600
CACHE_TTL_PAGE=600
CACHE_TTL_DATA=1200
CACHE_TTL_ASSET=86400
CACHE_TTL_MEDIA=3600

AUTH_SERVICE_URL=http://auth.coremusic.net
```

3. **`.env.example` oluştur (auth.coremusic.net):**
```env
APP_ENV_MODE=development
APP_NAME=CoreMusic-Auth
APP_VERSION=2.0.0
APP_TIMEZONE=Europe/Istanbul

DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=coremusic_auth
DB_USER=root
DB_PASSWORD=CHANGE_ME
DB_PORT=3306
DB_CHARSET=utf8mb4

APP_PEPPER=CHANGE_ME_TO_RANDOM_64_HEX
JWT_SECRET=CHANGE_ME_TO_RANDOM_64_HEX

SESSION_NAME=COREMUSIC_AUTH_SESS
SESSION_LIFETIME=3600
```

4. **Git history'den credential'ları temizle:**
```bash
git filter-branch --force --index-filter \
  'git rm --cached --ignore-unmatch music.coremusic.net/.env auth.coremusic.net/.env' \
  --prune-empty --tag-name-filter cat -- --all
```

5. **Gerçek credential'ları değiştir** (DB şifresi, pepper, JWT secret)

**Onay:** Kullanıcıdan gerçek credential değişikliği onayı alınacak.

---

## 4. Phase 2: Broken Path & Image Fixes

### 4.1 Kırık Görsel Referansları — Hard-coded Düzeltmeler

**Theme Manager yazılmayacak. Tüm görseller mevcut `res-pink/` yapısında hard-coded kalacak.**

#### 4.1.1 `music.coremusic.net/home.php` (Legacy) — Kırık Image Düzeltmeleri

Bu dosya legacy'dir ama hala erişilebilir olabilir. Düzelt:

| Satır | Eski | Yeni |
|-------|------|------|
| 51 | `Images/Albums/mock1.jpg` | `$assetsUrl . '/Image/res-pink/cd-ico.png'` |
| 65 | `Images/Albums/mock2.jpg` | `$assetsUrl . '/Image/res-pink/music.png'` |
| 79 | `Images/Albums/mock3.jpg` | `$assetsUrl . '/Image/res-pink/cd-ico.png'` |
| 93 | `Images/Albums/mock4.jpg` | `$assetsUrl . '/Image/res-pink/music-2.png'` |
| 51,65,79,93 | `Images/default-album.png` | `$assetsUrl . '/Image/background/bkimage1.png'` |

#### 4.1.2 `music.coremusic.net/header.php:93` — Broken Avatar

| Satır | Eski | Yeni |
|-------|------|------|
| 93 | `Images/Users/default.png` | `$assetsUrl . '/Image/res-pink/users.png'` |

#### 4.1.3 `assets.coremusic.net/js/core/coreplayer.controls.js` — Player Icons

Bu dosyada 3 kırık referans var. Hard-coded düzeltme:

| Satır | Eski | Yeni |
|-------|------|------|
| 227 | `/Image/album-icon.png` | `/Image/res-pink/cd-ico.png` |
| 228 | `/Image/footer-controls/play-btn.svg` | `/Image/res-pink/media-play.png` |
| 229 | `/Image/footer-controls/pause-btn.svg` | `/Image/res-pink/media-pause.png` |

#### 4.1.4 `coremusic-shared/src/PageRouter/HtmlShellRenderer.php:281`

| Satır | Eski | Yeni |
|-------|------|------|
| 281 | `Image/background/bkimage2.jpg` | `Image/background/bkimage1.png` |

#### 4.1.5 `music.coremusic.net/pages/home.php` — Hardcoded Domain

Bu dosyada `/assets.coremusic.net/Image/...` hardcoded. Düzelt:

| Satır | Eski | Yeni |
|-------|------|------|
| 6 | `/assets.coremusic.net/Image/background/bkimage1.png` | `<?= $assetsUrl ?>/Image/background/bkimage1.png` |
| 25 | `/assets.coremusic.net/Image/res-pink/bluethoot.png` | `<?= $assetsUrl ?>/Image/res-pink/bluethoot.png` |
| 67 | `/assets.coremusic.net/Image/background/bkimage1.png` | `<?= $assetsUrl ?>/Image/background/bkimage1.png` |
| 84 | `/assets.coremusic.net/Image/background/bkimage1.png` | `<?= $assetsUrl ?>/Image/background/bkimage1.png` |

**Not:** `pages/home.php`'de `$assetsUrl` değişkeni mevcut mu kontrol et. Yoksa config'den inject et.

#### 4.1.6 `assets.coremusic.net/Css/05_Pages/p-login-view.css` — HTTP URLs

| Satır | Eski | Yeni |
|-------|------|------|
| 15 | `http://assets.coremusic.net/Image/background/login-bg-female.png` | `//assets.coremusic.net/Image/background/login-bg-female.png` |
| 16 | `http://assets.coremusic.net/Image/background/login-bg-male.png` | `//assets.coremusic.net/Image/background/login-bg-male.png` |
| 17 | `http://assets.coremusic.net/Image/background/login-bg-neutral.png` | `//assets.coremusic.net/Image/background/login-bg-neutral.png` |

### 4.2 Eksik Görseller Oluştur

#### 4.2.1 `bluethoot-connected.png.png` → `bluethoot-connected.png`

Double extension düzeltmesi:
- `assets.coremusic.net/Image/res-pink/bluethoot-connected.png.png` → yeniden adlandır: `bluethoot-connected.png`

#### 4.2.2 `assets.coremusic.net/Image/res-blue/` Dizini Oluştur

ADR-044 Theme Engine henüz implemente edilmeyecek ama `GenderSelector.js` ve `coreplayer.volume.js` `res-blue/` ve `res-default/` arıyor. Geçici çözüm:

**Seçenek A (Tavsiye):** JS kodunda fallback mekanizması ekle — `res-blue/` ve `res-default/` yoksa `res-pink/` kullan.

**Seçenek B:** `res-blue/` ve `res-default/` dizinlerini `res-pink/`'in kopyası olarak oluştur (boş/placeholder).

**Karar:** Seçenek A — JS'de fallback ekle. Dosya isimleri hard-coded kalsın.

**Etkilenen JS dosyaları:**
- `assets.coremusic.net/js/core/coreplayer.volume.js:100-103`
- `assets.coremusic.net/js/router/auth/GenderSelector.js:8,10-12`

**coreplayer.volume.js düzeltmesi:**
```javascript
// Mevcut (satır ~100-103):
const genderFolder = gender === 'female' ? 'res-pink' : 
                     gender === 'male' ? 'res-blue' : 'res-default';

// Düzeltilmiş (fallback ile):
const genderMap = { female: 'res-pink', male: 'res-pink', neutral: 'res-pink', default: 'res-pink' };
// Gelecekte res-blue/ ve res-default/ eklendiğinde güncelle
const genderFolder = genderMap[gender] || 'res-pink';
```

---

## 5. Phase 3: SOLID Violations — DIP Fixes

### 5.1 `music.coremusic.net/pages/auth_callback.php:33` — Direct Instantiation

**Mevcut:**
```php
$authClient = new AuthServiceClient(AUTH_SERVICE_URL);
```

**Not:** Bu aslında DIP compliant — `AuthServiceClient` constructor injection kullanıyor. Sorun yok. Ancak AuthContainer'dan alınmalı.

**Düzeltilmiş:**
```php
$authClient = $container->get(AuthServiceClient::class);
```

### 5.2 `coremusic-shared/src/PageRouter/PageRouterKernel.php:41-46` — Internal Dependency Creation

**Mevcut:**
```php
public function __construct(private readonly string $routesFile) {
    $this->requestNormalizer = new RequestNormalizer();
    $this->sessionInitializer = new SessionInitializer();
    $this->shellRenderer = new HtmlShellRenderer();
    $this->responseEmitter = new ResponseEmitter();
    $this->errorHandler = new ErrorHandler();
}
```

**Düzeltilmiş:**
```php
public function __construct(
    private readonly string $routesFile,
    private readonly ?RequestNormalizer $requestNormalizer = null,
    private readonly ?SessionInitializer $sessionInitializer = null,
    private readonly ?HtmlShellRenderer $shellRenderer = null,
    private readonly ?ResponseEmitter $responseEmitter = null,
    private readonly ?ErrorHandler $errorHandler = null,
) {
    $this->requestNormalizer ??= new RequestNormalizer();
    $this->sessionInitializer ??= new SessionInitializer();
    $this->shellRenderer ??= new HtmlShellRenderer();
    $this->responseEmitter ??= new ResponseEmitter();
    $this->errorHandler ??= new ErrorHandler();
}
```

### 5.3 `coremusic-shared/src/Cache/CacheManager.php` — Static State

**Mevcut:**
```php
class CacheManager {
    private static ?CacheInterface $adapter = null;
    private static CacheStats $stats;
    // Tüm method'lar static
}
```

**Düzeltilmiş:**
```php
class CacheManager {
    private ?CacheInterface $adapter = null;
    private CacheStats $stats;
    
    public function __construct() {
        $this->stats = new CacheStats();
    }
    
    public function getAdapter(): CacheInterface { ... }
    public function setAdapter(CacheInterface $adapter): void { ... }
}
```

**Etkilenen dosyalar:**
- `coremusic-shared/src/Cache/CacheManager.php` — tüm method'ları instance'a çevir
- `coremusic-shared/src/Cache/CacheBootstrap.php` — static call'ları güncelle
- Tüm `CacheManager::method()` call'larını `$container->get(CacheManager::class)->method()` yap

**Risk:** Bu geniş kapsamlı değişiklik. Aşamalı yapılacak.

### 5.4 `coremusic-shared/src/PageRouter/PageRouterHelper.php` — Direct $_SESSION Access

**LOW priority.** SessionManager zaten middleware'de yönetiyor.

### 5.5 `coremusic-shared/src/Security/ReturnUrlPolicy.php` — Global Constant Access

**Düzeltilmiş:**
```php
public function __construct(private readonly string $envMode = 'production') {}
// Sonra: if ($this->envMode === 'development') { ... }
```

### 5.6 `coremusic-shared/src/Cache/DatabaseCacheAdapter.php` — Direct $_ENV Access

**Düzeltilmiş:**
```php
public function __construct(
    private readonly PDO $pdo,
    private readonly string $prefix = 'cache_',
) {}
```

---

## 6. Phase 4: SOLID Violations — SRP Fixes

### 6.1 `music.coremusic.net/config/config.php` — God Function (172 lines)

Bu dosya 172 satırlık prosedürel bir god function. Bu sprintte sadece **hard-coded path düzeltmesi** yapılacak (Phase 9). Tam SRP split'i gelecek sprinte ertelenecek.

### 6.2 `music.coremusic.net/header.php` + `footer.php` — Duplicate Code

**Ortak kod:**
```php
// header.php:9-11 ve footer.php:8-10 — Aynı $h helper
// header.php:13 ve footer.php:12 — Aynı $assetsUrl
// header.php:54 ve footer.php:13 — Aynı $nonce
```

**Çözüm:** `include/helpers/layout.php` oluştur:

```php
<?php declare(strict_types=1);
// layout.php — Ortak layout helper'ları

function cm_escape(): callable {
    static $h = null;
    $h ??= static fn(string $v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return $h;
}

function cm_assets_url(\CoreMusic\Config\DomainConfig $domainConfig): string {
    return $domainConfig->getUrl('assets');
}

function cm_nonce(): string {
    return $_SESSION['_csp_nonce'] ?? '';
}
```

**Etkilenen dosyalar:**
- `music.coremusic.net/header.php` — `include_once __DIR__ . '/include/helpers/layout.php'`
- `music.coremusic.net/footer.php` — aynı

### 6.3 `music.coremusic.net/home.php` — Legacy Dead Code

**Durum:** Bu dosya legacy'dir. `pages/home.php` güncel versiyondur.

**Yapılacak:** `home.php`'yi sil veya `home.legacy.php` olarak yeniden adlandır.

### 6.4 `music.coremusic.net/index.php:15-21` — Duplicate Error Handling

**Düzeltilmiş:**
```php
http_response_code(500);
$message = DEBUG_MODE ? $e->getMessage() : 'An unexpected error occurred';
exit(json_encode(['error' => 'Internal Server Error', 'message' => $message]));
```

---

## 7. Phase 5: SOLID Violations — DRY Fixes

### 7.1 `music.coremusic.net/config/routes.php` — Hardcoded HTTP URLs

**Düzeltilmiş:**
```php
// routes.php en üstte:
$domainConfig = $GLOBALS['cm_domainConfig'] ?? null;
$authBase = 'http://auth.coremusic.net';
$callbackBase = $domainConfig ? $domainConfig->getUrl('music') : 'http://music.coremusic.net:81';
```

### 7.2 `coremusic-shared/src/Config/AuthRouteConfig.php` — Duplicate Arrays

**Düzeltilmiş:**
```php
public const AUTH_REDIRECT_ROUTES = self::AUTH_ROUTES;
```

### 7.3 `auth.coremusic.net/include/Controller/AuthController.php` — Duplicate Host Validation

**Düzeltilmiş:**
```php
use CoreMusic\Security\ReturnUrlPolicy;

public function __construct(
    private readonly AuthService $authService,
    private readonly UserRepository $userRepository,
    private readonly ReturnUrlPolicy $returnUrlPolicy,
) {}
```

### 7.4 `coremusic-shared/src/PageRouter/HtmlShellRenderer.php` — Duplicate Auth Routes

**Düzeltilmiş:**
```php
use CoreMusic\Config\AuthRouteConfig;
$isAuthPage = in_array($pageName, AuthRouteConfig::AUTH_ROUTES, true);
```

### 7.5 `coremusic-shared/src/PageRouter/DeviceDetector` — Two Duplicate Classes

**Silinecek:** `coremusic-shared/src/PageRouter/DeviceDetector.php`
**Kullanılacak:** `coremusic-shared/src/Device/DeviceDetector.php`

**Etkilenen dosyalar:**
- `coremusic-shared/src/PageRouter/HtmlShellRenderer.php` — import değiştir
- `coremusic-shared/src/PageRouter/PageRouterKernel.php` — import değiştir

---

## 8. Phase 6: Clean Code — Dead Code Cleanup

### 8.1 Empty Directories (music.coremusic.net)

| Dizin | Durum | Aksiyon |
|-------|-------|---------|
| `include/class/` | BOŞ | SİL |
| `include/repository/` | BOŞ | SİL |
| `include/controller/` | MEVCUT DEĞİL | Autoload'dan kaldır |
| `include/interface/` | MEVCUT DEĞİL | Autoload'dan kaldır |
| `components/layout/` | BOŞ | SİL |
| `components/ui/` | BOŞ | SİL |

### 8.2 Dead Code Files

| Dosya | Durum | Aksiyon |
|-------|-------|---------|
| `music.coremusic.net/home.php` | Legacy | SİL veya .legacy.php |
| `music.coremusic.net/config/helper.php:9-19` | Hiç çağrılmıyor | Method'u sil |
| `coremusic-shared/fix_namespaces.php` | Migration script | SİL |
| `auth.coremusic.net/include/tests/run.php` | Eski namespace'ler | SİL |
| `coremusic-shared/src/Interfaces/IAuditLogger.php` | Implementation yok | Sil |
| `coremusic-shared/src/PageRouter/RouteResult.php:179-183` | #[\Deprecated] | Sil |

### 8.3 Unused Variables

| Dosya:Satır | Değişken | Durum |
|-------------|----------|-------|
| `music.coremusic.net/header.php:53` | `$electricIcon` | Kullanılmıyor — SİL |

### 8.4 Autoload Dead References

**`music.coremusic.net/autoload.php` satır 124-128:**
```php
$scanDirectories = [
    __DIR__ . '/include/service',
    __DIR__ . '/include/modules',
];
```

---

## 9. Phase 7: Clean Code — Inline Styles → CSS

**83 inline style kullanımı tespit edildi (ADR-001 violation).**

### 9.1 Dağılım

| Dosya | Inline Style Sayısı |
|-------|---------------------|
| `home.php` (root) | 35 |
| `footer.php` | 24 |
| `pages/home.php` | 16 |
| `header.php` | 1 |
| `pages/auth_callback.php` | 1 |

### 9.2 Aşamalı Yaklaşım

**Sprint 1:** header.php ve footer.php'deki inline style'ları CSS'e taşı.
**Sprint 2:** pages/home.php
**Sprint 3:** home.php (legacy) — eğer silinmiyorsa

---

## 10. Phase 8: Route Fixes

### 10.1 Eksik Sayfa Dosyaları Oluştur

**5 eksik page dosyası oluşturulacak:**

#### `music.coremusic.net/pages/album.php`
```php
<?php declare(strict_types=1);
$id = (int)($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Albüm #<?= $id ?> — CoreMusic</title></head>
<body>
<h1>Albüm #<?= $id ?></h1>
<p>Bu sayfa yakında dolacak.</p>
</body>
</html>
```

#### `music.coremusic.net/pages/artist.php`
```php
<?php declare(strict_types=1);
$id = (int)($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Sanatçı #<?= $id ?> — CoreMusic</title></head>
<body>
<h1>Sanatçı #<?= $id ?></h1>
<p>Bu sayfa yakında dolacak.</p>
</body>
</html>
```

#### `music.coremusic.net/pages/playlist.php`
```php
<?php declare(strict_types=1);
$id = (int)($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Çalma Listesi #<?= $id ?> — CoreMusic</title></head>
<body>
<h1>Çalma Listesi #<?= $id ?></h1>
<p>Bu sayfa yakında dolacak.</p>
</body>
</html>
```

#### `music.coremusic.net/pages/genre.php`
```php
<?php declare(strict_types=1);
$id = (int)($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Tür #<?= $id ?> — CoreMusic</title></head>
<body>
<h1>Tür #<?= $id ?></h1>
<p>Bu sayfa yakında dolacak.</p>
</body>
</html>
```

#### `music.coremusic.net/pages/track.php`
```php
<?php declare(strict_types=1);
$id = (int)($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Parça #<?= $id ?> — CoreMusic</title></head>
<body>
<h1>Parça #<?= $id ?></h1>
<p>Bu sayfa yakında dolacak.</p>
</body>
</html>
```

### 10.2 Eksik Navigation Route'ları

**`config/routes.php`'ye ekle:**
```php
'kesfet'    => new SpaRoute(page: 'kesfet', requiresAuth: true, title: 'Keşfet'),
'albumler'  => new SpaRoute(page: 'albumler', requiresAuth: true, title: 'Albümler'),
'sanatcilar'=> new SpaRoute(page: 'sanatcilar', requiresAuth: true, title: 'Sanatçılar'),
'goz-at'    => new SpaRoute(page: 'goz-at', requiresAuth: true, title: 'Göz At'),
'gecmis'    => new SpaRoute(page: 'gecmis', requiresAuth: true, title: 'Geçmiş'),
'ayarlar'   => new SpaRoute(page: 'ayarlar', requiresAuth: true, title: 'Ayarlar'),
'hakkimizda'=> new SpaRoute(page: 'hakkimizda', requiresAuth: false, title: 'Hakkımızda'),
```

**Placeholder sayfaları oluştur:**
- `pages/kesfet.php`, `pages/albumler.php`, `pages/sanatcilar.php`
- `pages/goz-at.php`, `pages/gecmis.php`, `pages/ayarlar.php`, `pages/hakkimizda.php`

---

## 11. Phase 9: Hard-coded Paths → Dynamic Config

### 11.1 `music.coremusic.net/config/config.php:27`

**Düzeltilmiş:**
```php
$errorLogPath = $_ENV['ERROR_LOG_PATH'] ?? getenv('ERROR_LOG_PATH') 
    ?: dirname(__DIR__) . '/coremusic_php_errors.log';
ini_set('error_log', $errorLogPath);
```

### 11.2 `music.coremusic.net/.htaccess:11`

**Düzeltilmiş:**
```apache
ErrorLog "../../coremusic_php_errors.log"
```

### 11.3 `music.coremusic.net/.user.ini:14`

**Düzeltilmiş:**
```ini
opcache.file_cache="{DOCUMENT_ROOT}/cache/.opcache"
```

### 11.4 `coremusic-shared/src/Security/SecurityHelper.php:31`

**Düzeltilmiş:**
```php
$logPath = $_ENV['ERROR_LOG_PATH'] ?? getenv('ERROR_LOG_PATH') 
    ?: dirname(__DIR__, 4) . '/coremusic_php_errors.log';
```

### 11.5 `coremusic-shared/src/PageRouter/HtmlShellRenderer.php:52`

**Düzeltilmiş:**
```php
$assetsBase = $_ENV['ASSETS_PATH'] ?? getenv('ASSETS_PATH')
    ?: dirname(__DIR__, 4) . '/assets.coremusic.net';
```

### 11.6 `coremusic-shared/src/Cache/FileAdapter.php:17`

**Düzeltilmiş:**
```php
private string $cacheDir = '' // Constructor'dan inject et
```

---

## 12. Phase 10: Image Management (Hard-coded Theme)

### 12.1 Mevcut Görsel Yapısı

```
assets.coremusic.net/Image/
├── background/        (7 dosya: login-bg-*, bkimage1.png)
├── res-pink/          (52+ dosya: tüm UI ikonları)
│   ├── actions/       (7 dosya)
│   ├── app/           (9 dosya)
│   ├── banner/        (1 dosya)
│   ├── disk/          (8 dosya)
│   ├── logo/          (2 dosya)
│   ├── power-system/  (7 dosya)
│   ├── quick-bar/     (12 dosya)
│   ├── wifi/          (9 dosya)
│   └── (root)         (16+ dosya)
```

### 12.2 Theme Engine Durumu

**ADR-044 planlandı ama henüz implemente edilmeyecek.**

### 12.3 Hard-coded Image Path Stratejisi

Tüm image referansları mevcut yapida kalacak:
- `$assetsUrl . '/Image/res-pink/...'` — PHP tarafında
- `//assets.coremusic.net/Image/res-pink/...'` — JS/CSS tarafında

---

## 13. Phase 11: JS Architecture Fixes

### 13.1 `assets.coremusic.net/js/core/coreplayer.controls.js` — IIFE Monolith

**LOW priority.** Bu sprintte sadece kırık image referansları düzeltilecek.

### 13.2 `assets.coremusic.net/js/core/coreplayer.volume.js` — Hardcoded Asset Path

**Düzeltilmiş:**
```javascript
const GENDER_FOLDER_MAP = {
    female: 'res-pink',
    male: 'res-pink',     // gelecekte: 'res-blue'
    neutral: 'res-pink',  // gelecekte: 'res-default'
    default: 'res-pink',
};
const genderFolder = GENDER_FOLDER_MAP[gender] || 'res-pink';
```

### 13.3 `assets.coremusic.net/js/router/auth/GenderSelector.js` — Hardcoded URLs

**Durum:** `//assets.coremusic.net/Image/background/` hardcoded. Zaten protocol-relative (`//`), kabul edilebilir.

---

## 14. Phase 12: Auth Subdomain Fixes

### 14.1 `auth.coremusic.net/config/helper.php` — Dead Code

**Yapılacak:** `log_test_bypass()`'ı sil.

### 14.2 `auth.coremusic.net/include/tests/run.php` — Legacy

**Yapılacak:** PHPUnit test altyapısı zaten var. Bu dosyayı sil.

### 14.3 `coremusic-shared/src/Interfaces/IAuditLogger.php` — Dead Interface

**Tavsiye:** Sil.

---

## 15. Phase 13: Shared Library Fixes

### 15.1 DeviceDetector Consolidation

**Silinecek:** `coremusic-shared/src/PageRouter/DeviceDetector.php`
**Kullanılacak:** `coremusic-shared/src/Device/DeviceDetector.php`

### 15.2 PageRouterKernel Constructor Injection

**Düzeltme (Phase 5.2'de açıklandı)**

### 15.3 Error Handling — OCP Fix

**Düşük priority.** Mevcut yapı works.

---

## 16. Phase 14: PHP Config Cleanup

### 16.1 `define()` → Typed Config Migration

**Bu sprint yapılacak sadece:**
- Hard-coded path düzeltmeleri (Phase 9)
- AUTH_SERVICE_URL'i dynamic yap

**Gelecek sprint:** `define()`'leri `AppConfig` class'ına taşı.

### 16.2 `config/helper.php` — Global Functions

**Düzeltilmiş:**
```php
final class TestBypassHelper {
    public static function isActive(): bool { ... }
}
```

---

## 17. Phase 15: Testing & Verification

### 17.1 Çalıştırılacak Testler

```bash
cd music.coremusic.net && vendor/bin/phpunit
cd coremusic-shared && vendor/bin/phpunit
cd auth.coremusic.net && vendor/bin/phpunit
```

### 17.2 Manuel Kontroller

| # | Kontrol | Yöntem |
|---|---------|--------|
| 1 | Auth flow | `music.coremusic.net:81` → login → register → select-gender |
| 2 | Image loading | Tarayıcıda tüm sayfaları aç, image 404 kontrolü |
| 3 | Route test | `/album/1`, `/artist/1`, `/kesfet`, `/ayarlar` erişilebilirlik |
| 4 | Security | `.env` dosyası git'te görünmüyor mu? |
| 5 | Error log | Hata oluştuğunda log dosyası doğru yere yazıyor mu? |
| 6 | CSP nonce | Header'da `Content-Security-Policy` nonce var mı? |

### 17.3 Automated Smoke Tests

```bash
curl http://music.coremusic.net:81/health
curl -I http://music.coremusic.net:81/login
curl -I http://assets.coremusic.net/Image/res-pink/media-play.png
```

---

## 18. Phase 16: Vault Sync & Log

### 18.1 Log Kayıtları

Tüm değişiklikler `log.md`'ye timestamp ile eklenecek:
```
[2026-08-05 HH:MM:SS] [INFO] [backend-architect] [FIX] [Phase 1] Credential exposure fixed
[2026-08-05 HH:MM:SS] [INFO] [backend-architect] [FIX] [Phase 2] 10 broken image refs fixed
[2026-08-05 HH:MM:SS] [INFO] [backend-architect] [FIX] [Phase 3] DIP violations fixed
...
```

### 18.2 Vault Güncellemeleri

| Dosya | Güncelleme |
|-------|-----------|
| `.ai/brain.md` | Session 2026-08-05 audit fix ekle |
| `.ai/MEMORY.md` | Bu session'ın özeti |
| `.ai/log.md` | Tüm fix kayıtları |

---

## 19. Execution Order & Dependencies

```
Phase 1 (Security) ← BAŞLANGIC (kritik, bağımsız)
    │
    ├── Phase 2 (Broken Paths) ← Phase 1'den sonra
    │       │
    │       ├── Phase 3 (DIP Fixes) ← bağımsız
    │       ├── Phase 4 (SRP Fixes) ← bağımsız
    │       └── Phase 5 (DRY Fixes) ← bağımsız
    │
    ├── Phase 6 (Dead Code) ← bağımsız
    ├── Phase 8 (Routes) ← bağımsız
    ├── Phase 9 (Hard-coded Paths) ← bağımsız
    ├── Phase 12 (Auth Fixes) ← bağımsız
    └── Phase 13 (Shared Fixes) ← bağımsız
            │
            └── Phase 15 (Testing) ← TÜM PHASE'LERDEN SONRA
                    │
                    └── Phase 16 (Vault Sync) ← EN SON
```

### Paralel Çalışılabilecek Fazlar:
- Phase 3, 4, 5, 6, 8, 9, 12, 13 → Bağımsız, paralel çalışılabilir
- Phase 7 (Inline Styles) → Düşük priority, ileride
- Phase 11 (JS Architecture) → Düşük priority, ileride

---

## 20. Risk Matrix

| Değişiklik | Risk | Etki | Mitigation |
|-----------|------|------|------------|
| .env silme | YÜKSEK | Uygulama çalışmayabilir | Önce .env.example oluştur |
| Route ekleme | DÜŞÜK | Yeni sayfa, eskiyi etkilemez | Placeholder ile başla |
| DeviceDetector silme | ORTA | Import kırılabilir | Grep ile tüm referansları bul |
| CacheManager refactor | YÜKSEK | Tüm cache call'ları bozabilir | Aşamalı yap, test ile doğrula |
| Inline style temizleme | DÜŞÜK | Görsel bozulma olabilir | Visual regression test |
| Dead code silme | DÜŞÜK | Çağrılmayan kod | Grep ile doğrula |

---

## 21. File Change Manifest

### Yeni Oluşturulacak Dosyalar (~15)

| # | Dosya | Amaç |
|---|-------|------|
| 1 | `music.coremusic.net/.env.example` | Credential template |
| 2 | `auth.coremusic.net/.env.example` | Credential template |
| 3 | `music.coremusic.net/include/helpers/layout.php` | Ortak layout helper'ları |
| 4 | `music.coremusic.net/pages/album.php` | Placeholder page |
| 5 | `music.coremusic.net/pages/artist.php` | Placeholder page |
| 6 | `music.coremusic.net/pages/playlist.php` | Placeholder page |
| 7 | `music.coremusic.net/pages/genre.php` | Placeholder page |
| 8 | `music.coremusic.net/pages/track.php` | Placeholder page |
| 9 | `music.coremusic.net/pages/kesfet.php` | Placeholder page |
| 10 | `music.coremusic.net/pages/albumler.php` | Placeholder page |
| 11 | `music.coremusic.net/pages/sanatcilar.php` | Placeholder page |
| 12 | `music.coremusic.net/pages/goz-at.php` | Placeholder page |
| 13 | `music.coremusic.net/pages/gecmis.php` | Placeholder page |
| 14 | `music.coremusic.net/pages/ayarlar.php` | Placeholder page |
| 15 | `music.coremusic.net/pages/hakkimizda.php` | Placeholder page |

### Değiştirilecek Dosyalar (~25)

| # | Dosya | Değişiklik |
|---|-------|-----------|
| 1 | `music.coremusic.net/.gitignore` | .env ekle |
| 2 | `music.coremusic.net/config/config.php` | Hard-coded path fix |
| 3 | `music.coremusic.net/config/routes.php` | Dynamic auth URLs + 7 yeni route |
| 4 | `music.coremusic.net/header.php` | Broken image fix, layout helper import |
| 5 | `music.coremusic.net/footer.php` | Layout helper import |
| 6 | `music.coremusic.net/home.php` | Broken image fix (veya sil) |
| 7 | `music.coremusic.net/pages/home.php` | Hardcoded domain fix |
| 8 | `music.coremusic.net/index.php` | Duplicate error handling fix |
| 9 | `music.coremusic.net/autoload.php` | Dead directory removal |
| 10 | `music.coremusic.net/.htaccess` | Hardcoded path fix |
| 11 | `music.coremusic.net/.user.ini` | Hardcoded path fix |
| 12 | `auth.coremusic.net/.gitignore` | .env ekle |
| 13 | `auth.coremusic.net/config/helper.php` | Dead function removal |
| 14 | `coremusic-shared/src/PageRouter/PageRouterKernel.php` | DIP fix + DeviceDetector import |
| 15 | `coremusic-shared/src/PageRouter/HtmlShellRenderer.php` | Broken image fix + DeviceDetector import + DRY |
| 16 | `coremusic-shared/src/PageRouter/PageRouterHelper.php` | DIP fix (low priority) |
| 17 | `coremusic-shared/src/Cache/CacheManager.php` | Static → instance (aşamalı) |
| 18 | `coremusic-shared/src/Security/SecurityHelper.php` | Hardcoded path fix |
| 19 | `coremusic-shared/src/Security/ReturnUrlPolicy.php` | DIP fix |
| 20 | `coremusic-shared/src/Cache/DatabaseCacheAdapter.php` | DIP fix |
| 21 | `coremusic-shared/src/Config/AuthRouteConfig.php` | DRY fix |
| 22 | `coremusic-shared/src/Interfaces/IAuditLogger.php` | Dead code removal |
| 23 | `assets.coremusic.net/js/core/coreplayer.controls.js` | Broken image refs |
| 24 | `assets.coremusic.net/js/core/coreplayer.volume.js` | Fallback map |
| 25 | `assets.coremusic.net/Css/05_Pages/p-login-view.css` | HTTP → protocol-relative |

### Silinecek Dosyalar (~6)

| # | Dosya | Neden |
|---|-------|-------|
| 1 | `music.coremusic.net/home.php` | Legacy, pages/home.php güncel |
| 2 | `music.coremusic.net/include/class/` (dizin) | Boş |
| 3 | `music.coremusic.net/include/repository/` (dizin) | Boş |
| 4 | `coremusic-shared/fix_namespaces.php` | Migration script |
| 5 | `auth.coremusic.net/include/tests/run.php` | Legacy, bozuk |
| 6 | `coremusic-shared/src/PageRouter/DeviceDetector.php` | Duplicate |

---

## 22. Success Criteria

### Bu sprint sonunda:

| # | Kriter | Ölçüm |
|---|--------|-------|
| 1 | .env dosyaları git'te yok | `git ls-files .env` → boş |
| 2 | 0 kırık image referansı | Tarayıcı konsolunda 0 image 404 |
| 3 | 0 hardcoded Windows path | `grep -r "C:\\\\www" --include="*.php"` → 0 |
| 4 | 0 missing page route | Tüm route'lar erişilebilir |
| 5 | Testler geçiyor | `vendor/bin/phpunit` → 0 failure |
| 6 | Auth flow çalışıyor | Login → Register → Select-Gender → Music |
| 7 | Dead code temizlendi | Boş dizinler silindi |
| 8 | DIP violations azaltıldı | CacheManager instance-based |

### Gelecek Sprint (2. Faz):
- Inline styles → CSS (83 → 0)
- CacheManager tam instance-based
- SRP fixes (config.php split)
- JS IIFE → ES6 module
- IAuditLogger implementation

---

## Appendix A: Detailed File Analysis

### A.1 music.coremusic.net — Dosya Boyut Sıralaması

| Dosya | Satır | Kritiklik |
|-------|-------|-----------|
| config/config.php | 172 | HIGH (god function) |
| home.php (legacy) | 175 | LOW (dead code) |
| debug.php | 212 | LOW (dev-only) |
| footer.php | 146 | MEDIUM (inline styles) |
| config/routes.php | 140 | HIGH (hardcoded URLs) |
| header.php | 112 | MEDIUM (broken refs, inline) |
| autoload.php | 132 | MEDIUM (dead references) |
| pages/home.php | 95 | MEDIUM (hardcoded domain) |
| include/service/AuthServiceClient.php | 129 | GOOD (clean code) |
| include/modules/Database/MultiProviderDatabaseManager.php | 158 | LOW (OCP minor) |

### A.2 coremusic-shared — En Büyük Dosyalar

| Dosya | Satır | Sorun |
|-------|-------|-------|
| HtmlShellRenderer.php | 322 | God class, hardcoded path, duplicate auth routes |
| CacheManager.php | ~200 | Static state, DIP violation |
| PageRouterKernel.php | ~250 | Internal dependency creation, duplicate logic |
| AuthRouteConfig.php | ~120 | Duplicate arrays |
| DatabaseCacheAdapter.php | ~215 | Direct $_ENV access |

---

## Appendix B: SOLID Violation Detail

### B.1 DIP Violations Summary

| # | Dosya | Violation | Fix Complexity |
|---|-------|-----------|----------------|
| DIP-1 | CacheManager.php | Static state, no DI | ORTA |
| DIP-2 | DatabaseCacheAdapter.php | $_ENV direct access | DÜŞÜK |
| DIP-3 | ReturnUrlPolicy.php | Global constant access | DÜŞÜK |
| DIP-4 | PageRouterHelper.php | $_SESSION direct access | ORTA |
| DIP-5 | PageRouterKernel.php | Internal `new` calls | ORTA |
| DIP-6 | config/config.php | `new DomainConfig()` | DÜŞÜK |
| DIP-7 | config/config.php | `new ConfigManager()` | DÜŞÜK |
| DIP-8 | index.php | `new PageRouterKernel()` | DÜŞÜK |
| DIP-9 | debug.php | `new RouteRegistry()` | DÜŞÜK |
| DIP-10 | debug.php | `new PageRouter()` | DÜŞÜK |
| DIP-11 | auth_callback.php | `new AuthServiceClient()` | DÜŞÜK |
| DIP-12 | config.php (auth) | Inline .env parsing | DÜŞÜK |

### B.2 SRP Violations Summary

| # | Dosya | Violation | Fix Complexity |
|---|-------|-----------|----------------|
| SRP-1 | config/config.php | 172-line god function | YÜKSEK |
| SRP-2 | autoload.php | ClassMapLoader + bootstrap | ORTA |
| SRP-3 | header.php | Presentation + business logic | ORTA |
| SRP-4 | footer.php | Same as header | ORTA |
| SRP-5 | HtmlShellRenderer.php | God class (322 lines) | YÜKSEK |
| SRP-6 | PageRouterKernel.php | Multiple responsibilities | ORTA |

### B.3 DRY Violations Summary

| # | Dosyalar | Violation | Fix Complexity |
|---|----------|-----------|----------------|
| DRY-1 | header.php + footer.php | $h, $assetsUrl, $nonce duplicate | DÜŞÜK |
| DRY-2 | AuthRouteConfig.php | AUTH_ROUTES = AUTH_REDIRECT_ROUTES | DÜŞÜK |
| DRY-3 | AuthController.php + AuthValidator.php | ALLOWED_REDIRECT_HOSTS duplicate | DÜŞÜK |
| DRY-4 | HtmlShellRenderer.php (2 methods) | Auth routes array duplicate | DÜŞÜK |
| DRY-5 | config.php + EnvParser | Inline .env parsing | DÜŞÜK |
| DRY-6 | routes.php (7 entries) | Auth redirect URL pattern duplicate | ORTA |
| DRY-7 | Device/DeviceDetector + PageRouter/DeviceDetector | Two detector classes | ORTA |
| DRY-8 | home.php + pages/home.php | Two home pages | DÜŞÜK (sil) |

---

## Appendix C: Security Checklist

| # | Check | Durum | Aksiyon |
|---|-------|-------|---------|
| 1 | .env in .gitignore | ❌ | Ekle |
| 2 | DB password not in code | ❌ | .env'e taşı, .gitignore'a ekle |
| 3 | CSRF token = `csrf_token` | ✅ | ADR-010 uyumlu |
| 4 | hash_equals() for CSRF | ✅ | Timing attack koruması var |
| 5 | session_regenerate_id() | ✅ | Login sonrası var |
| 6 | Prepared statements (PDO) | ✅ | ADR-002 uyumlu |
| 7 | No `SELECT *` | ✅ | ADR-040 uyumlu |
| 8 | CSP nonce | ✅ | Middleware'de üretiliyor |
| 9 | Rate limiting | ✅ | APCu tabanlı |
| 10 | Error message leakage | ⚠️ | debug.php ve auth_callback.php'de var |
| 11 | Timing attack on debug token | ⚠️ | hash_equals() kullanılmalı |
| 12 | session_start() bypass | ⚠️ | check-cookie.php'de middleware dışı |
| 13 | Hardcoded HTTP URLs | ⚠️ | routes.php'de http:// auth URLs |
| 14 | CORS wildcard | ⚠️ | `.htaccess`'te Access-Control-Allow-Origin: * |

---

## Appendix D: Image Asset Inventory

### D.1 Kullanılan Görseller (Referanslı)

| Görsel | Kullanım | Dosya |
|--------|----------|-------|
| `res-pink/users.png` | Avatar icon | header.php |
| `res-pink/wifi-not-connection.png` | WiFi status | header.php |
| `res-pink/bluethoot.png` | Bluetooth status | header.php |
| `res-pink/battery-50%.png` | Battery icon | header.php |
| `res-pink/settings.png` | Settings icon | header.php |
| `res-pink/session-logout.png` | Logout icon | header.php |
| `res-pink/media-play.png` | Play button | footer.php, home.php |
| `res-pink/media-pause.png` | Pause button | footer.php |
| `res-pink/media-back.png` | Previous button | footer.php |
| `res-pink/media-ileri.png` | Next button | footer.php |
| `res-pink/media-stop.png` | Stop button | footer.php |
| `res-pink/music.png` | Song icon | footer.php |
| `res-pink/cd-ico.png` | Album icon | footer.php |
| `res-pink/mic-1.png` | Artist icon | footer.php |
| `res-pink/timer.png` | Time icon | footer.php |
| `res-pink/repat.png` | Repeat icon | footer.php |
| `res-pink/karistir.png` | Shuffle icon | footer.php |
| `res-pink/equalizer.png` | Equalizer icon | footer.php |
| `res-pink/full-screen.png` | Fullscreen icon | footer.php |
| `res-pink/playlist1.png` | Playlist icon | footer.php |
| `res-pink/volume-high.png` | Volume icon | footer.php, coreplayer.volume.js |
| `background/bkimage1.png` | Album art placeholder | footer.php, pages/home.php |
| `background/login-bg-female.png` | Login background | GenderSelector.js, CSS |
| `background/login-bg-male.png` | Login background | GenderSelector.js, CSS |
| `background/login-bg-neutral.png` | Login background | GenderSelector.js, CSS |
| `logo/logo-img.png` | Logo | auth pages |
| `logo/logo-text.png` | Logo text | auth pages |
| `banner/login-text.png` | Login banner | auth pages |
| `res-pink/erkek-gender-select.png` | Male gender | select-gender.php |
| `res-pink/kız-gender-select.png` | Female gender | select-gender.php, auth pages |
| `res-pink/notur-gender-select.png` | Neutral gender | select-gender.php |

### D.2 Kullanılmayan Görseller (50+)

Tüm `actions/`, `disk/`, `power-system/`, `wifi/` alt klasörleri ve root-level misc dosyaları kullanılmıyor.

**Not:** Bu görseller gelecek özellikler için hazır tutuluyor. Silinmeyecek.

---

## Appendix E: Execution Script

### E.1 Tek Komutla Çalıştırma (Sıralı)

```powershell
# CoreMusic Comprehensive Fix Script
Write-Host "=== CoreMusic System Fix — Starting ===" -ForegroundColor Cyan

# Phase 1: Security
Write-Host "`n[Phase 1] Security Fixes..." -ForegroundColor Yellow
Add-Content -Path "music.coremusic.net\.gitignore" -Value "`n# Environment files`n.env`n.env.local`n.env.production"
Add-Content -Path "auth.coremusic.net\.gitignore" -Value "`n# Environment files`n.env`n.env.local`n.env.production"
Write-Host "[Phase 1] Done" -ForegroundColor Green

# Phase 2: Broken Paths
Write-Host "`n[Phase 2] Broken Image Fixes..." -ForegroundColor Yellow
# (Dosya düzenleme komutları buraya)

# Phase 3-5: SOLID Fixes
Write-Host "`n[Phase 3-5] SOLID Fixes..." -ForegroundColor Yellow

# Phase 6: Dead Code
Write-Host "`n[Phase 6] Dead Code Cleanup..." -ForegroundColor Yellow
Remove-Item -Path "music.coremusic.net\include\class" -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item -Path "music.coremusic.net\include\repository" -Recurse -Force -ErrorAction SilentlyContinue

# Phase 8: Routes
Write-Host "`n[Phase 8] Route Fixes..." -ForegroundColor Yellow

# Phase 9: Hard-coded Paths
Write-Host "`n[Phase 9] Hard-coded Path Fixes..." -ForegroundColor Yellow

# Phase 15: Testing
Write-Host "`n[Phase 15] Running Tests..." -ForegroundColor Yellow
cd music.coremusic.net; vendor/bin/phpunit; cd ..
cd coremusic-shared; vendor/bin/phpunit; cd ..

Write-Host "`n=== CoreMusic System Fix — Complete ===" -ForegroundColor Cyan
```

---

**Plan Version:** 1.0.0
**Total Estimated Effort:** ~6-8 saat (tüm fazlar)
**Critical Path:** Phase 1 → Phase 2 → Phase 15 → Phase 16
**Parallelizable:** Phase 3,4,5,6,8,9,12,13

---

*CoreMusic Comprehensive Audit Plan v1.0.0*
*Generated: 2026-08-05*
*Mode: Red Team • Human Mode • Truth Mode*
