---
type: decision
id: "083"
title: "ADR-083: SPA Router Architecture (PHP+JS Hybrid)"
category: "architecture"
status: "active"
date: "2026-08-12"
updated: "2026-08-16"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 3.0.0
tags: [architecture, spa, router, php, js, hybrid, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[decisions/accepted/ADR-004-multi-domain-spa]]"
  - "[[decisions/accepted/ADR-021-spa-router-immutable-contract]]"
  - "[[architecture/l2-routing]]"
  - "[[architecture/l2-routing/spa-router]]"
  - "[[architecture/l2-routing/js-router]]"
  - "[[architecture/l2-routing/route-config]]"
  - "[[architecture/l2-routing/html-shell-renderer]]"
  - "[[architecture/l2-routing/guard-pipeline]]"
---

# ADR-083: SPA Router Architecture (PHP+JS Hybrid)

---

## 1. Executive Summary

CoreMusic SPA Router, **PHP + JS hybrid** mimarisi ile çalışır. PHP tarafı ilk yüklemede HTML shell üretir, JS tarafı subsequent navigation'larda client-side routing'i yönetir. History API (pushState/popstate) kullanılır. DOMParser ile innerHTML yasak.

**Referans Proje:** `reference-project/coremusic-shared/src/PageRouter/` (14 PHP modülü) + `reference-project/assets.coremusic.net/js/router/` (21+ JS modülü).

---

## 2. Decision

### Hybrid Mimari Akışı

```
İlk Yükleme (Full Page):
  Browser → PHP PageRouterKernel → Middleware Pipeline → PageRouter → HtmlShellRenderer → Tam HTML

Sonraki Navigasyon (SPA):
  Browser → JS Router → Fetch (X-Requested-With: XMLHttpRequest) → PHP PageRouterKernel
    → Middleware Pipeline → PageRouter → RouteResult (JSON) → JS DomPatcher → DOM güncelleme
```

### PHP Tarafı — 18 Modül (SRP Decomposition, 78.33 KB)

| # | Modül | Boyut | Sorumluluk |
|---|-------|-------|------------|
| 1 | `HtmlShellRenderer` | 12.14 KB | SPA HTML shell üretimi |
| 2 | `PageRouterKernel` | 11.97 KB | Ana orkestratör (middleware + dispatch) |
| 3 | `PageRouter` | 6.86 KB | Route çözümleme + rendering |
| 4 | `RequestNormalizer` | 6.49 KB | $_SERVER normalization |
| 5 | `SessionInitializer` | 5.78 KB | Session lifecycle |
| 6 | `RouteResult` | 5.47 KB | Response factory (JSON/HTML/Redirect) |
| 7 | `ResponseEmitter` | 4.50 KB | HTTP response emission |
| 8 | `RouteRegistry` | 4.20 KB | Route kayıt + çözümleme |
| 9 | `AuthGuard` | 4.13 KB | Auth guard mantığı (6 kontrol) |
| 10 | `AuthUrlBuilder` | 3.66 KB | Auth URL oluşturma |
| 11 | `ErrorHandler` | 3.54 KB | Hata sayfası üretimi |
| 12 | `StructuredLogger` | 3.53 KB | JSON loglama |
| 13 | `PageRouterHelper` | 3.50 KB | Auth helper (lazy session) |
| 14 | `SpaRoute` | 1.16 KB | Route DTO (immutable) |
| 15 | `SessionProviderInterface` | 509 B | Session arayüzü (DIP) |
| 16 | `StaticSessionProvider` | 520 B | Test için statik session |
| 17 | `SessionProvider` | 423 B | Session erişimi |
| 18 | `templates/inline-script` | — | Inline JS template |

### JS Tarafı — 31 Modül (26 Router + 5 Auth)

| # | Modül | Sorumluluk |
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
| 14-26 | Diğer router modülleri | Core routing |
| — | **auth/** | **Auth form'lar (8 ayrı dosya, router içinde değil)** |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | History API (pushState/popstate) | ✅ Zorunlu |
| 2 | PHP + JS hybrid | ✅ Zorunlu |
| 3 | DOMParser (innerHTML yasak) | ✅ Zorunlu |
| 4 | TrustedTypes policy | ✅ Zorunlu |
| 5 | Client-side state | ✅ Zorunlu |
| 6 | Backend-controlled auth | ✅ Zorunlu |
| 7 | SRP decomposition (18 PHP + 31 JS modül) | ✅ Zorunlu |
| 8 | Immutable SpaRoute DTO | ✅ Zorunlu |
| 9 | Guard pipeline (PHP 6 + JS 3 kontrol) | ✅ Zorunlu |

---

## 3. Vault Dosyaları

| Dosya | Amaç |
|-------|------|
| [[architecture/l2-routing/spa-router]] | PHP SPA PageRouter (14 modül) |
| [[architecture/l2-routing/js-router]] | JS SPA Router (21+ modül) |
| [[architecture/l2-routing/route-config]] | Route yapısı + SpaRoute DTO |
| [[architecture/l2-routing/html-shell-renderer]] | HTML shell üretimi |
| [[architecture/l2-routing/guard-pipeline]] | Guard pipeline (PHP + JS) |
| [[architecture/l2-routing/middleware-pipeline]] | Middleware pipeline |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.2.0 |
| **Status** | Active |
| **PHP Modül Sayısı** | 18 (78.33 KB) |
| **JS Modül Sayısı** | 31 (26 Router + 5 Auth form) |
| **Middleware** | 7 PHP dosyası |
| **Vault Dosyası** | 6 |
| **Referans Proje** | ✅ Uyumlu |

---

*ADR-083: SPA Router Architecture v3.2.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-16*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
