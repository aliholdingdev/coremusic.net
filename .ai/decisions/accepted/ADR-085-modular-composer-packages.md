---
type: decision
id: "085"
title: "ADR-085: Shared Library Architecture (Hybrid)"
category: "architecture"
status: "active"
date: "2026-08-12"
updated: "2026-08-15"
authority: "Vault Steward"
governance: "Red Team · Human Mode · Truth Mode"
version: 3.0.0
tags: [architecture, composer, shared, hybrid, modular-namespace]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
---

# ADR-085: Shared Library Architecture (Hybrid)

---

## 1. Executive Summary

CoreMusic, **tek `shared/` dizini** ile **modüler namespace yapısı** kullanır. 22 ayrı paket yerine, PSR-4 namespace ile ayrılmış tek bir Composer paketi tercih edilir. Bu karar Vault Steward (Bayram Ali) tarafından onaylanmıştır.

## 2. Decision

### Yapı: Hybrid — Tek Dizin + Modüler Namespace

```
shared/
├── composer.json              ← Tek paket: coremusic/shared
├── bootstrap.php              ← Autoloader + env
├── config/                    ← Config dosyaları
│   ├── database.php           ← 18 BCNF DB
│   ├── middleware.php          ← Frozen pipeline
│   ├── routes.php             ← Route tanımları
│   └── cors.php               ← CORS whitelist
├── src/
│   ├── Router/                ← L2: SPA Router
│   │   ├── Contracts/         ← RouterInterface, RouteDefinitionInterface
│   │   ├── Attributes/        ← #[Route], #[Middleware], #[Guard]
│   │   └── Cache/             ← RouteCache
│   ├── Security/              ← L1: Middleware Pipeline
│   │   ├── Middleware/         ← 10 middleware (frozen sıra)
│   │   └── Service/            ← CspNonceGenerator, RateLimiter
│   ├── Auth/                  ← L1/L4: Auth Domain
│   │   ├── Domain/             ← Entity, ValueObject, Repository, Event
│   │   ├── Application/        ← Command, Query, DTO, Service
│   │   └── Infrastructure/     ← Repository implementations
│   ├── Http/                  ← PSR-7/17
│   ├── Cache/                 ← PSR-6
│   ├── Events/                ← PSR-14
│   ├── Validation/            ← Request validation
│   └── Logger/                ← PSR-3
└── tests/
    └── Unit/
```

### Namespace Haritası

| Namespace | Kullanım Alanı |
|-----------|---------------|
| `CoreMusic\Router\*` | SPA Router (L2) |
| `CoreMusic\Security\*` | Middleware Pipeline (L1) |
| `CoreMusic\Auth\*` | Auth Domain (L1/L4) |
| `CoreMusic\Http\*` | PSR-7 HTTP |
| `CoreMusic\Cache\*` | PSR-6 Cache |
| `CoreMusic\Events\*` | PSR-14 Events |
| `CoreMusic\Validation\*` | Request Validation |
| `CoreMusic\Logger\*` | PSR-3 Logging |

### Subdomain Bağımlılığı

Her alt domain `shared/`'e bağımlıdır:

```
auth.coremusic.net   → require shared/bootstrap.php
music.coremusic.net  → require shared/bootstrap.php
api.coremusic.net    → require shared/bootstrap.php
admin.coremusic.net  → require shared/bootstrap.php
... (tüm subdomain'ler)
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Tek `shared/` dizini | ✅ Zorunlu |
| 2 | PSR-4 namespace ile modüler ayrılama | ✅ Zorunlu |
| 3 | Subdomain'ler shared'e bağımlı | ✅ Zorunlu |
| 4 | Circular dependency yasak | ❌ Yasak |
| 5 | PSR uyumlu paketler | ✅ Zorunlu |
| 6 | Tek `composer.json` | ✅ Zorunlu |

### Neden 22 Paket Değil?

| 22 Paket (Reddedilen) | Shared Hybrid (Kabul Edilen) |
|------------------------|------------------------------|
| Her paket ayrı `composer.json` | Tek `composer.json` |
| Paketler arası bağımlılık yönetimi karmaşık | Namespace ile basit ayrım |
|版本 güncelleme karmaşık | Tek versiyon |
| CI/CD karmaşık | Tek pipeline |
| Büyük ekip için ideal | Bireysel geliştirici için ideal |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Status** | Active — User Approved (2026-08-15) |
| **Yapı** | Hybrid: Tek shared/ + PSR-4 namespace |
| **Paket** | Tek composer.json (coremusic/shared) |
| **Namespace** | 8 modüler namespace (Router, Security, Auth, Http, Cache, Events, Validation, Logger) |

---

*ADR-085: Shared Library Architecture v3.0.0 — CoreMusic Architecture*
*Authority: Vault Steward · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
*User Approval: Bayram Ali — "shared yapısı olsun, packages değil"*
