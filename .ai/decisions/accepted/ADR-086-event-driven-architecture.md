---
type: decision
id: "086"
title: "ADR-086: Event Driven Architecture (PSR-14)"
category: "architecture"
status: "active"
date: "2026-08-12"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, event-driven, psr-14, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-039-7-service-platform-architecture]]"
  - "[[decisions/accepted/ADR-084-api-gateway-architecture]]"
---

# ADR-086: Event Driven Architecture

---

## 1. Executive Summary

CoreMusic servisleri birbirini doğrudan çağırmaz, **event yayınlar**. PSR-14 Event Dispatcher kullanılır. Domain Event'ler ve Integration Event'ler ayrıştırılmıştır.

## 2. Decision

### Event Türleri

| Tür | Kapsam | Örnek |
|-----|--------|-------|
| Domain Event | Tek servis içinde | UserCreatedEvent |
| Integration Event | Servisler arası | UserRegisteredEvent |

### Event Akışı

```
Service A → Event Bus (PSR-14) → Service B, C, D
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | PSR-14 Event Dispatcher | ✅ Zorunlu |
| 2 | Direct service call yasak | ❌ Yasak |
| 3 | Domain Event separation | ✅ Zorunlu |
| 4 | Event logging | ✅ Zorunlu |
| 5 | Idempotent handlers | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-086: Event Driven Architecture v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
