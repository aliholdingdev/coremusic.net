---
title: "ADR-086: Event Driven Architecture (PSR-14)"
status: active
date: 2026-08-12
tags: [architecture, event-driven, psr-14, active]
---

# ADR-086: Event Driven Architecture

---

## 1. Executive Summary

CoreMusic servisleri birbirini doÄŸrudan Ã§aÄŸÄ±rmaz, **event yayÄ±nlar**. PSR-14 Event Dispatcher kullanÄ±lÄ±r. Domain Event'ler ve Integration Event'ler ayrÄ±ÅŸtÄ±rÄ±lmÄ±ÅŸtÄ±r.

## 2. Decision

### Event TÃ¼rleri

| TÃ¼r | Kapsam | Ã–rnek |
|-----|--------|-------|
| Domain Event | Tek servis iÃ§inde | UserCreatedEvent |
| Integration Event | Servisler arasÄ± | UserRegisteredEvent |

### Event AkÄ±ÅŸÄ±

```
Service A â†’ Event Bus (PSR-14) â†’ Service B, C, D
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | PSR-14 Event Dispatcher | âœ… Zorunlu |
| 2 | Direct service call yasak | âŒ Yasak |
| 3 | Domain Event separation | âœ… Zorunlu |
| 4 | Event logging | âœ… Zorunlu |
| 5 | Idempotent handlers | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-086: Event Driven Architecture v2.0.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*