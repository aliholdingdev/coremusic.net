---
title: "ADR-085: Shared Library Architecture (Hybrid)"
status: active
date: 2026-08-12
tags: [architecture, composer, shared, hybrid, modular-namespace]
---

# ADR-085: Shared Library Architecture (Hybrid)

---

## 1. Executive Summary

CoreMusic, **tek `shared/` dizini** ile **modÃ¼ler namespace yapÄ±sÄ±** kullanÄ±r. 22 ayrÄ± paket yerine, PSR-4 namespace ile ayrÄ±lmÄ±ÅŸ tek bir Composer paketi tercih edilir. Bu karar Vault Steward (Bayram Ali) tarafÄ±ndan onaylanmÄ±ÅŸtÄ±r.

## 2. Decision

### YapÄ±: Hybrid â€” Tek Dizin + ModÃ¼ler Namespace

```
shared/
â”œâ”€â”€ composer.json              â† Tek paket: coremusic/shared
â”œâ”€â”€ bootstrap.php              â† Autoloader + env
â”œâ”€â”€ config/                    â† Config dosyalarÄ±
â”‚   â”œâ”€â”€ database.php           â† 18 BCNF DB
â”‚   â”œâ”€â”€ middleware.php          â† Frozen pipeline
â”‚   â”œâ”€â”€ routes.php             â† Route tanÄ±mlarÄ±
â”‚   â””â”€â”€ cors.php               â† CORS whitelist
â”œâ”€â”€ src/
â”‚   â”œâ”€â”€ Router/                â† L2: SPA Router
â”‚   â”‚   â”œâ”€â”€ Contracts/         â† RouterInterface, RouteDefinitionInterface
â”‚   â”‚   â”œâ”€â”€ Attributes/        â† #[Route], #[Middleware], #[Guard]
â”‚   â”‚   â””â”€â”€ Cache/             â† RouteCache
â”‚   â”œâ”€â”€ Security/              â† L1: Middleware Pipeline
â”‚   â”‚   â”œâ”€â”€ Middleware/         â† 10 middleware (frozen sÄ±ra)
â”‚   â”‚   â””â”€â”€ Service/            â† CspNonceGenerator, RateLimiter
â”‚   â”œâ”€â”€ Auth/                  â† L1/L4: Auth Domain
â”‚   â”‚   â”œâ”€â”€ Domain/             â† Entity, ValueObject, Repository, Event
â”‚   â”‚   â”œâ”€â”€ Application/        â† Command, Query, DTO, Service
â”‚   â”‚   â””â”€â”€ Infrastructure/     â† Repository implementations
â”‚   â”œâ”€â”€ Http/                  â† PSR-7/17
â”‚   â”œâ”€â”€ Cache/                 â† PSR-6
â”‚   â”œâ”€â”€ Events/                â† PSR-14
â”‚   â”œâ”€â”€ Validation/            â† Request validation
â”‚   â””â”€â”€ Logger/                â† PSR-3
â””â”€â”€ tests/
    â””â”€â”€ Unit/
```

### Namespace HaritasÄ±

| Namespace | KullanÄ±m AlanÄ± |
|-----------|---------------|
| `CoreMusic\Router\*` | SPA Router (L2) |
| `CoreMusic\Security\*` | Middleware Pipeline (L1) |
| `CoreMusic\Auth\*` | Auth Domain (L1/L4) |
| `CoreMusic\Http\*` | PSR-7 HTTP |
| `CoreMusic\Cache\*` | PSR-6 Cache |
| `CoreMusic\Events\*` | PSR-14 Events |
| `CoreMusic\Validation\*` | Request Validation |
| `CoreMusic\Logger\*` | PSR-3 Logging |

### Subdomain BaÄŸÄ±mlÄ±lÄ±ÄŸÄ±

Her alt domain `shared/`'e baÄŸÄ±mlÄ±dÄ±r:

```
auth.coremusic.net   â†’ require shared/bootstrap.php
music.coremusic.net  â†’ require shared/bootstrap.php
api.coremusic.net    â†’ require shared/bootstrap.php
admin.coremusic.net  â†’ require shared/bootstrap.php
... (tÃ¼m subdomain'ler)
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Tek `shared/` dizini | âœ… Zorunlu |
| 2 | PSR-4 namespace ile modÃ¼ler ayrÄ±lama | âœ… Zorunlu |
| 3 | Subdomain'ler shared'e baÄŸÄ±mlÄ± | âœ… Zorunlu |
| 4 | Circular dependency yasak | âŒ Yasak |
| 5 | PSR uyumlu paketler | âœ… Zorunlu |
| 6 | Tek `composer.json` | âœ… Zorunlu |

### Neden 22 Paket DeÄŸil?

| 22 Paket (Reddedilen) | Shared Hybrid (Kabul Edilen) |
|------------------------|------------------------------|
| Her paket ayrÄ± `composer.json` | Tek `composer.json` |
| Paketler arasÄ± baÄŸÄ±mlÄ±lÄ±k yÃ¶netimi karmaÅŸÄ±k | Namespace ile basit ayrÄ±m |
|ç‰ˆæœ¬ gÃ¼ncelleme karmaÅŸÄ±k | Tek versiyon |
| CI/CD karmaÅŸÄ±k | Tek pipeline |
| BÃ¼yÃ¼k ekip iÃ§in ideal | Bireysel geliÅŸtirici iÃ§in ideal |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Status** | Active â€” User Approved (2026-08-15) |
| **YapÄ±** | Hybrid: Tek shared/ + PSR-4 namespace |
| **Paket** | Tek composer.json (coremusic/shared) |
| **Namespace** | 8 modÃ¼ler namespace (Router, Security, Auth, Http, Cache, Events, Validation, Logger) |

---

*ADR-085: Shared Library Architecture v3.0.0 â€” CoreMusic Architecture*
*Authority: Vault Steward Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*
*User Approval: Bayram Ali â€” "shared yapÄ±sÄ± olsun, packages deÄŸil"*