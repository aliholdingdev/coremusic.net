---
type: decision
id: "R-009"
title: "REJECTED: Single Database Architecture"
category: "database"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, database, single-db, monolithic]
risk-level: "critical"
rejection-reason: "Güvenlik, performans, ADR-040"
rejected-by: "ADR-003, ADR-040"
references:
  - "[[decisions/accepted/ADR-003-multi-db-9-databases]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# REJECTED: Single Database Architecture

---

## 1. Executive Summary

Tek monolitik veritabanı mimarisi **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | Single point of failure | Kritik |
| 2 | Performans darboğazı | Yüksek |
| 3 | Güvenlik izolasyonu yok | Yüksek |
| 4 | Backup/restore yavaş | Orta |

## 3. Alternatif Çözüm

**Seçilen:** 18 izole BCNF veritabanı (ADR-040)
- Domain izolasyonu
- Performans optimizasyonu
- Bağımsız backup/restore
- Güvenlik izolasyonu

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
