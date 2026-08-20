---
title: "ADR-083: SPA Router Architecture (PHP+JS Hybrid)"
status: active
date: 2026-08-12
tags: [architecture, spa, router, php, js, hybrid, active]
---

# ADR-083: SPA Router Architecture (PHP+JS Hybrid)

---

## 1. Executive Summary

CoreMusic SPA Router, **PHP + JS hybrid** mimarisi ile Ã§alÄ±ÅŸÄ±r. PHP tarafÄ± ilk yÃ¼klemede HTML shell Ã¼retir, JS tarafÄ± subsequent navigation'larda client-side routing'i yÃ¶netir. History API (pushState/popstate) kullanÄ±lÄ±r. DOMParser ile innerHTML yasak.

**Referans Proje:** `reference-project/coremusic-shared/src/PageRouter/` (14 PHP modÃ¼lÃ¼) + `reference-project/assets.coremusic.net/js/router/` (21+ JS modÃ¼lÃ¼).

---

## 2. Decision

### Hybrid Mimari AkÄ±ÅŸÄ±

```
Ä°lk YÃ¼kleme (Full Page):
  Browser â†’ PHP PageRouterKernel â†’ Middleware Pipeline â†’ PageRouter â†’ HtmlShellRenderer â†’ Tam HTML

Sonraki Navigasyon (SPA):
  Browser â†’ JS Router â†’ Fetch (X-Requested-With: XMLHttpRequest) â†’ PHP PageRouterKernel
    â†’ Middleware Pipeline â†’ PageRouter â†’ RouteResult (JSON) â†’ JS DomPatcher â†’ DOM gÃ¼ncelleme
```

### PHP TarafÄ± â€” 18 ModÃ¼l (SRP Decomposition, 78.33 KB)

| # | ModÃ¼l | Boyut | Sorumluluk |
|---|-------|-------|------------|
| 1 | `HtmlShellRenderer` | 12.14 KB | SPA HTML shell Ã¼retimi |
| 2 | `PageRouterKernel` | 11.97 KB | Ana orkestratÃ¶r (middleware + dispatch) |
| 3 | `PageRouter` | 6.86 KB | Route Ã§Ã¶zÃ¼mleme + rendering |
| 4 | `RequestNormalizer` | 6.49 KB | $_SERVER normalization |
| 5 | `SessionInitializer` | 5.78 KB | Session lifecycle |
| 6 | `RouteResult` | 5.47 KB | Response factory (JSON/HTML/Redirect) |
| 7 | `ResponseEmitter` | 4.50 KB | HTTP response emission |
| 8 | `RouteRegistry` | 4.20 KB | Route kayÄ±t + Ã§Ã¶zÃ¼mleme |
| 9 | `AuthGuard` | 4.13 KB | Auth guard mantÄ±ÄŸÄ± (6 kontrol) |
| 10 | `AuthUrlBuilder` | 3.66 KB | Auth URL oluÅŸturma |
| 11 | `ErrorHandler` | 3.54 KB | Hata sayfasÄ± Ã¼retimi |
| 12 | `StructuredLogger` | 3.53 KB | JSON loglama |
| 13 | `PageRouterHelper` | 3.50 KB | Auth helper (lazy session) |
| 14 | `SpaRoute` | 1.16 KB | Route DTO (immutable) |
| 15 | `SessionProviderInterface` | 509 B | Session arayÃ¼zÃ¼ (DIP) |
| 16 | `StaticSessionProvider` | 520 B | Test iÃ§in statik session |
| 17 | `SessionProvider` | 423 B | Session eriÅŸimi |
| 18 | `templates/inline-script` | â€” | Inline JS template |

### JS TarafÄ± â€” 31 ModÃ¼l (26 Router + 5 Auth)

| # | ModÃ¼l | Sorumluluk |
|---|-------|------------|
| 1 | `Router.js` | Ana SPA router |
| 2 | `main.js` | Entry point |
| 3 | `GuardPipeline.js` | Client-side guard'lar |
| 4 | `CacheLayer.js` | Route content caching |
| 5 | `DomPatcher.js` | DOM patching (DOMParser) |
| 6 | `ContentPatcher.js` | HTML content update |
| 7 | `CsrfSyncManager.js` | CSRF token sync |
| 8 | `FetchWrapper.js` | HTTP fetch wrapper |
| 9 | `NavigationOrchestrator.js` | Navigasyon orkestrasyonu |
| 10 | `AuthBoundaryDetector.js` | Auth state detection |
| 11 | `ScrollRestorer.js` | Scroll restoration |
| 12 | `MemoryWatchdog.js` | Memory leak prevention |
| 13 | `RouterEventManager.js` | Event management |
| 14-26 | DiÄŸer router modÃ¼lleri | Core routing |
| â€” | **auth/** | **Auth form'lar (8 ayrÄ± dosya, router iÃ§inde deÄŸil)** |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | History API (pushState/popstate) | âœ… Zorunlu |
| 2 | PHP + JS hybrid | âœ… Zorunlu |
| 3 | DOMParser (innerHTML yasak) | âœ… Zorunlu |
| 4 | TrustedTypes policy | âœ… Zorunlu |
| 5 | Client-side state | âœ… Zorunlu |
| 6 | Backend-controlled auth | âœ… Zorunlu |
| 7 | SRP decomposition (18 PHP + 31 JS modÃ¼l) | âœ… Zorunlu |
| 8 | Immutable SpaRoute DTO | âœ… Zorunlu |
| 9 | Guard pipeline (PHP 6 + JS 3 kontrol) | âœ… Zorunlu |

---

## 3. Vault DosyalarÄ±

| Dosya | AmaÃ§ |
|-------|------|
| [[architecture/l2-routing/spa-router]] | PHP SPA PageRouter (14 modÃ¼l) |
| [[architecture/l2-routing/js-router]] | JS SPA Router (21+ modÃ¼l) |
| [[architecture/l2-routing/route-config]] | Route yapÄ±sÄ± + SpaRoute DTO |
| [[architecture/l2-routing/html-shell-renderer]] | HTML shell Ã¼retimi |
| [[architecture/l2-routing/guard-pipeline]] | Guard pipeline (PHP + JS) |
| [[architecture/l2-routing/middleware-pipeline]] | Middleware pipeline |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 3.2.0 |
| **Status** | Active |
| **PHP ModÃ¼l SayÄ±sÄ±** | 18 (78.33 KB) |
| **JS ModÃ¼l SayÄ±sÄ±** | 31 (26 Router + 5 Auth form) |
| **Middleware** | 7 PHP dosyasÄ± |
| **Vault DosyasÄ±** | 6 |
| **Referans Proje** | âœ… Uyumlu |

---

*ADR-083: SPA Router Architecture v3.2.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-16*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*