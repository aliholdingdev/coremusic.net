---
type: decision
id: "014"
title: "ADR-014: Multi-DB Migration Strategy"
category: "database"
status: "frozen"
date: "2026-03-25"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [database, migration, multi-db, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-014: Multi-DB Migration Strategy

---

## 1. Executive Summary

CoreMusic veritabanı migration'ları **forward-only** stratejisi ile yönetilir. Geri dönüş (rollback) yoktur. Her migration versiyonlu ve loglanır. 18 BCNF veritabanı için bağımsız migration'lar uygulanır.

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
| 1 | Forward-only migration | ✅ Zorunlu |
| 2 | Rollback yasak | ❌ Yasak |
| 3 | Versioned migration | ✅ Zorunlu |
| 4 | Migration log | ✅ Zorunlu |
| 5 | Backup önce | ✅ Zorunlu |
| 6 | Test ortamında önce | ✅ Zorunlu |
| 7 | BCNF validation sonra | ✅ Zorunlu |

### Migration Akışı

```
Backup → Test Ortamı → BCNF Validation → Production → Log
```

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-014: Multi-DB Migration Strategy v2.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
