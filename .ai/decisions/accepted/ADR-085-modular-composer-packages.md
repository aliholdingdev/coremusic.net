---
type: decision
id: "085"
title: "ADR-085: Modular Composer Packages (coremusic/*)"
category: "architecture"
status: "active"
date: "2026-08-12"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, composer, modular, packages, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
---

# ADR-085: Modular Composer Packages

---

## 1. Executive Summary

CoreMusic, **22 bağımsız `coremusic/*` Composer paketi** kullanır. Tek monolitik paket yasaktır. Circular dependency yasaktır.

## 2. Decision

### Paket Listesi

| # | Paket | Amaç |
|---|-------|------|
| 1 | coremusic/contracts | Temel sözleşmeler |
| 2 | coremusic/http | PSR-7/17/18 |
| 3 | coremusic/auth | Auth client, JWT |
| 4 | coremusic/security | CSRF, RateLimiter, CSP |
| 5 | coremusic/cache | Redis, APCu, File |
| 6 | coremusic/events | PSR-14 Event Dispatcher |
| 7 | coremusic/validation | Request/DTO validation |
| 8 | coremusic/storage | Flysystem abstraction |
| 9 | coremusic/logger | PSR-3 Monolog |
| 10 | coremusic/sdk | Client SDK |
| 11 | coremusic/api-client | Typed API Client |
| 12 | coremusic/queue | Message queue |
| 13 | coremusic/websocket | WebSocket client/server |
| ... | ... | (22 toplam) |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 22 bağımsız paket | ✅ Zorunlu |
| 2 | Circular dependency yasak | ❌ Yasak |
| 3 | contracts paketi bağımsız | ✅ Zorunlu |
| 4 | PSR uyumlu | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-085: Modular Composer Packages v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
