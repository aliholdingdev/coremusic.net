---
type: decision
id: "033"
title: "ADR-033: SQL Normalization Strategy"
category: "database"
status: "frozen"
date: "2026-06-15"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [database, sql, normalization, bcnf, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-033: SQL Normalization Strategy

---

## 1. Executive Summary

CoreMusic veritabanı şemaları **BCNF (Boyce-Codd Normal Form)** standardında normalize edilir. 3NF minimum gereksinimdir, BCNF hedeftir. Her tablo aday anahtar ile tam bağımlı olmalıdır.

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Risk Seviyesi** | high |

## 3. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | 3NF minimum | ✅ Zorunlu |
| 3 | Transitive bağımlılık yasak | ❌ Yasak |
| 4 | Partial bağımlılık yasak | ❌ Yasak |
| 5 | snake_case tablo/sütun adı | ✅ Zorunlu |
| 6 | Soft delete (is_deleted) | ✅ Zorunlu |
| 7 | Timestamp (created_at, updated_at) | ✅ Zorunlu |

### BCNF Kontrol Listesi

| # | Kontrol | Açıklama |
|---|---------|----------|
| 1 | Her tabloda PK var mı? | ✅ Zorunlu |
| 2 | Non-key sütunlar PK'ya tam bağımlı mı? | ✅ Zorunlu |
| 3 | Aday anahtarlar bağımsız mı? | ✅ Zorunlu |
| 4 | Transitive bağımlılık yok mu? | ✅ Zorunlu |
| 5 | NULL değer minimal mi? | ⚠️ Tercih |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-033: SQL Normalization Strategy v2.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
