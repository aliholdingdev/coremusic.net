---
type: decision
id: "050"
title: "ADR-050: Multi-DB Sync Strategy"
category: "database"
status: "active"
date: "2026-08-08"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [database, sync, multi-db, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-050: Multi-DB Sync Strategy

---

## 1. Executive Summary

18 BCNF veritabanı arasındaki senkronizasyon **event-driven** strateji ile yönetilir. Cross-db query yasak olduğu için veri paylaşımı **integration events** ile yapılır.

## 2. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Cross-db query yasak | ❌ Yasak |
| 2 | Event-driven sync | ✅ Zorunlu |
| 3 | Integration events | ✅ Zorunlu |
| 4 | eventual consistency | ✅ Kabul |
| 5 | Event log | ✅ Zorunlu |

### Sync Akışı

```
Service A (coremusic_auth)
    │
    ├──► UserCreatedEvent
    │
    └──► Event Bus (PSR-14)
            │
            ├──► coremusic_user (profile oluştur)
            ├──► coremusic_social (social profile oluştur)
            └──► coremusic_ai (AI profil oluştur)
```

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-050: Multi-DB Sync Strategy v2.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
