---
title: "CoreMusic — Testing Architecture"
type: architecture
category: testing
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Testing Architecture

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[testing/strategy]]

---

## 1. Amaç

Test altyapısını ve stratejilerini tanımlar. Unit, integration, E2E ve coverage hedefleri.

---

## 2. Test Pyramid

```
         ┌─────────┐
         │  E2E    │  %10
         │(Playwright)│
         ├─────────┤
         │Integration│  %20
         │(PHPUnit) │
         ├─────────┤
         │  Unit    │  %70
         │(PHPUnit) │
         └─────────┘
```

---

## 3. Coverage Targets

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

---

## 4. Test Frameworks

| Dil | Framework | Kullanım |
|-----|-----------|----------|
| PHP | PHPUnit 11 | Backend test |
| JS | Vitest | Frontend test |
| C++ | Google Test | Audio engine |
| E2E | Playwright | Browser test |

---

## 5. Test Types

| Tip | Kapsam | Hız |
|-----|--------|-----|
| Unit | Tek fonksiyon | <1ms |
| Integration | Modül arası | <100ms |
| E2E | Tam akış | <5s |
| Performance | Load test | <30s |

---

## 6. Test Data Management

| Stratejisi | Kullanım |
|------------|----------|
| Factory | Test verisi üretimi |
| Fixture | DB seed data |
| Mock | Dış bağımlılıklar |
| Stub | API yanıtları |

---

## 7. CI Integration

```
Push → Lint → Unit Test → Integration Test → E2E → Coverage → Report
 ↓        ↓        ↓              ↓              ↓         ↓         ↓
Git    PHP lint  PHPUnit      PHPUnit       Playwright  coverage  Summary
```

---

## 8. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Pyramid | [[testing/strategy]] | Test stratejisi |
| § 3 Coverage | [[testing/coverage-targets]] | Kapsama hedefleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
