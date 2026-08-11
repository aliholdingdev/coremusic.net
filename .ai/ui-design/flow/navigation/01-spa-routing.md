---
title: CoreMusic — Navigation Flow: SPA Routing (Detaylı)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/00-ascii-art-views]] §1
  - [[01-component-inventory]] C01
  - [[ADR-021-spa-router-immutable-contract]]
  - [[ADR-004-multi-domain-spa]]
---

# Navigation Flow: SPA Routing — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

> **⚠️ Mimari:** SPA Router **PHP backend** (PageRouter.php) + **JS frontend** (Router.js) kombinasyonudur.
> Backend: `PageRouter.php` → `RouteRegistry` → `SpaRoute` → `Handler` dispatch
> Frontend: `Router.js` → `#navigate` → `#patchDOM` → `#updateCsrf` → `#mount`

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       SPA ROUTING AKIŞI                                      │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Kullanıcı    │ →  │ Router.js    │ →  │ DOM Patch    │                  │
│  │ nav-link     │    │ #navigate    │    │ #patchDOM    │                  │
│  │ tıklar       │    │              │    │              │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Route   │  │ 404       │        │
│                                         │ bulundu │  │ Sayfa     │        │
│                                         └────┬────┘  └───────────┘        │
│                                              │                             │
│                                         ┌────▼────┐                        │
│                                         │ Handler │                        │
│                                         │ çağırılır│                        │
│                                         └────┬────┘                        │
│                                              │                             │
│                                         ┌────▼────┐                        │
│                                         │ Sayfa   │                        │
│                                         │ render  │                        │
│                                         └────┬────┘                        │
│                                              │                             │
│                                         ┌────▼────┐                        │
│                                         │ Header  │                        │
│                                         │ active  │                        │
│                                         │ güncelle│                        │
│                                         └─────────┘                        │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. ROUTE TABLOSU

| Route | Handler | Sayfa | Auth |
|-------|---------|-------|------|
| `/` | HomeHandler | Ana Sayfa | ✅ |
| `/albums` | AlbumsHandler | Albümler | ✅ |
| `/album/:id` | AlbumDetailHandler | Albüm Detayı | ✅ |
| `/artists` | ArtistsHandler | Sanatçılar | ✅ |
| `/artist/:id` | ArtistDetailHandler | Sanatçı Detayı | ✅ |
| `/browse` | BrowseHandler | Göz At | ✅ |
| `/playlist/:id` | PlaylistHandler | Playlist | ✅ |
| `/settings` | SettingsHandler | Ayarlar | ✅ |
| `/history` | HistoryHandler | Geçmiş | ✅ |
| `/about` | AboutHandler | Hakkımızda | ✅ |
| `/auth/login` | LoginHandler | Login | ❌ |
| `/auth/register` | RegisterHandler | Register | ❌ |
| `/auth/gender` | GenderHandler | Gender Select | ❌ |

---

## 3. NAV LINK YAPISI (C01)

```
Header'da 8 nav-link:
┌─────────────────────────────────────────────────────────────────────────────┐
│ "Core Music" [Ana Sayfa] [Keşfet] [Albümler] [Sanatçılar] [Göz At] [Geçmiş] [Ayarlar] [Hakkımızda] │
│                 ↑ active                                                                    │
│                                                                                             │
│ Her link: ~24×24px hit area (WCAG: 48px olmalı)                                            │
│ Font: Arima, 10px                                                                           │
│ Renk default: rgba(255,255,255,0.85)                                                       │
│ Renk active: var(--theme-primary)                                                           │
│ Padding: 2px 4px                                                                            │
│ Gap: 4px                                                                                    │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. DAVRANIŞ DETAYLARI

### 4.1 — Mimari Katmanları

```
┌── Frontend (JS) ──────────────────────────────────────────┐
│ Router.js → #navigate → #patchDOM → #updateCsrf → #mount  │
│   ↓ (AJAX request)                                        │
├── Backend (PHP) ──────────────────────────────────────────┤
│ PageRouter.php → RouteRegistry → SpaRoute → Handler        │
│   ↓ (middleware pipeline)                                  │
├── Security (PHP) ─────────────────────────────────────────┤
│ SessionManager → BypassAuth → RateLimiter → Auth →        │
│ SecurityHeaders → Csrf                                    │
│   ↓ (DB/cache)                                            │
├── Infrastructure (PHP) ───────────────────────────────────┤
│ CacheManager, DatabaseManager, Logger                     │
└───────────────────────────────────────────────────────────┘
```

### 4.2 — Link Tıklama

```
Kullanıcı nav-link'e tıklar
  → JS: Event.preventDefault() (sayfa yenilenmez)
  → JS: Router.js #navigate(path) çağırılır
  → JS: history.pushState ile URL güncellenir
  → AJAX: Backend'e istek gönderilir
  → PHP: PageRouter.php → RouteRegistry → eşleşme
  → PHP: Handler çağırılır (middleware pipeline çalışır)
  → PHP: JSON response döner
  → JS: #patchDOM ile DOM güncellenir
  → JS: Header'daki active link güncellenir
  → JS: Scroll pozisyonu sıfırlanır
```

### 4.3 — DOM Patching (Frontend)

```
Backend'den JSON response geldiğinde
  → JS: #patchDOM(targetElement, newContent)
  → JS: DOMParser ile HTML parse et (innerHTML YASAK)
  → JS: Mevcut içerik temizlenir
  → JS: Yeni içerik eklenir (appendChild)
  → JS: #updateCsrf ile CSRF token güncellenir
  → JS: #mount ile event listener'lar bağlanır
  → JS: Footer player korunur (sayfa değişikliğinde durmaz)
```

### 4.4 — Backend Handler Yapısı

```php
// PageRouter.php
RouteRegistry::register('/albums', AlbumsHandler::class);
RouteRegistry::register('/album/{id}', AlbumDetailHandler::class);
RouteRegistry::register('/artists', ArtistsHandler::class);
// ...

// Her handler:
class AlbumsHandler implements SpaRoute {
    public function handle(Request $request): Response {
        // Middleware pipeline çalışır
        // DB'den veri çekilir
        // JSON response döner
        return new JsonResponse($data);
    }
}
```

### 4.5 — Back/Forward

```
Kullanıcı tarayıcı back butonuna basar
  → JS: popstate event tetiklenir
  → JS: Router.js mevcut URL'i okur
  → AJAX: Backend'e istek gönderilir
  → PHP: Handler çalışır
  → JS: DOM güncellenir
```

### 4.6 — Auth Guard

```
Kullanıcı korumalı bir sayfaya gitmeye çalışır
  → JS: GuardPipeline.js çalışır
    → authGuard: Session var mı? (cookie kontrolü)
    → roleGuard: Yeterli yetki var mı?
    → permissionGuard: İzin var mı?
  → JS: AJAX ile backend'e istek
  → PHP: AuthMiddleware session doğrulama
  → PHP: RBAC kontrolü
  → Başarılı → JSON response → Sayfa gösterilir
  → Başarısız → 401 → Login sayfasına yönlendirme
```

---

## 5. PERFORMANCE NOTLARI

| Metrik | Hedef |
|--------|-------|
| Route geçiş süresi | <100ms |
| DOM patching | <50ms |
| Lazy loading | İlk yükleme için |
| Preloading | Sık kullanılan sayfalar |
| Skeleton screen | Yükleme sırasında |

---

## 6. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/00-ascii-art-views]] §1 | Header ASCII art |
| [[01-component-inventory]] C01 | Nav Link |
| [[ADR-021-spa-router-immutable-contract]] | SPA router |
| [[ADR-004-multi-domain-spa]] | Multi-domain |
| [[flow/navigation/02-header-nav]] | Header nav |
| [[flow/navigation/03-footer-player]] | Footer player |

---

*SPA Routing Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
