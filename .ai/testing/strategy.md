---
type: testing
category: strategy
title: "Test Strategy"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Test Strategy

**İlgili ADR:** [[decisions/accepted/ADR-023-persona-driven-testing]]

## 1. Amaç

CoreMusic test stratejisi ve kapsam hedefleri.

## 2. Test Piramidi

```text
      E2E (%10)
     /        \
Integration (%20)
     \        /
      Unit (%70)
```

## 3. Kapsam Hedefleri

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

## 4. Test Framework'leri

| Katman | Framework |
|--------|-----------|
| Backend | PHPUnit 11 |
| Frontend | Vitest |
| E2E | Playwright 1.40 |
| Audio | Google Test |

## 5. Persona Testleri

| Persona | Amaç |
|---------|------|
| Casual User | Günlük müzik dinleme |
| Professional | Stüdyo üretimi |
| DJ | Canlı performans |
| Admin | Sistem yönetimi |

## 6. Test Senaryoları

| Senaryo | Öncelik |
|---------|---------|
| Auth flow | CRITICAL |
| Music playback | HIGH |
| Download | HIGH |
| EQ adjustment | MEDIUM |
| Theme change | LOW |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
