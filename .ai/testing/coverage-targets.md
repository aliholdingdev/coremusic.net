---
type: testing
category: coverage-targets
title: "Coverage Targets"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Coverage Targets

## 1. Amaç

Test kapsam hedefleri ve ölçümleri.

## 2. Kapsam Hedefleri

| Modül | Minimum | Hedef | Framework |
|-------|---------|-------|-----------|
| Backend (PHP) | ≥80% | ≥90% | PHPUnit 11 |
| Frontend (JS) | ≥80% | ≥90% | Vitest |
| Audio Engine (C++) | ≥80% | ≥90% | Google Test |
| Download Service | ≥80% | ≥90% | Vitest |

## 3. Kapsam Metrikleri

| Metrik | Açıklama |
|--------|----------|
| Line | Satır kapsamı |
| Branch | Dal kapsamı |
| Function | Fonksiyon kapsamı |
| Class | Sınıf kapsamı |

## 4. Kapsam Raporlama

| Format | Kullanım |
|--------|----------|
| Clover | PHP |
| LCOV | JS |
| Cobertura | C++ |

## 5. Kapsam Kontrolü

| Komut | Amaç |
|-------|------|
| `phpunit --coverage-html` | PHP kapsamı |
| `vitest --coverage` | JS kapsamı |
| `gtest --gtest_print_time` | C++ test |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
