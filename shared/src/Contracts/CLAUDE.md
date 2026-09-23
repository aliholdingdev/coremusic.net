---
title: "CoreMusic — shared/src/Contracts Bağlam"
type: context
folder: "shared/src/Contracts"
category: layer4-domain
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Contracts — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]]

---

## 1. Bağlam

Sözleşme arayüzleri. API ve Events için interface tanımları.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Alt klasör | 2 (Contracts/Api/, Contracts/Events/) |

| Klasör | İçerik |
|--------|--------|
| `Contracts/Api/` | API sözleşme arayüzleri |
| `Contracts/Events/` | Event sözleşme arayüzleri (DomainEventInterface) |

---

## 3. Sözleşme Kuralları

| Kural | Detay |
|-------|-------|
| Interface segregation | Her interface tek sorumluluk |
| Dependency inversion | High-level modül low-level'e bağımlı değil |
| Contract-first | Önce sözleşme, sonra implementasyon |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
