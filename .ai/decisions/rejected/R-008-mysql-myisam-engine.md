---
type: decision
id: "R-008"
title: "REJECTED: MySQL MyISAM Engine"
category: "database"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, database, myisam, engine]
risk-level: "high"
rejection-reason: "Transaction eksikliği, InnoDB zorunlu"
rejected-by: "ADR-040"
references:
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# REJECTED: MySQL MyISAM Engine

---

## 1. Executive Summary

MyISAM storage engine kullanımı **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | Transaction desteği yok | Kritik |
| 2 | Row-level locking yok | Yüksek |
| 3 | ACID uyumsuz | Yüksek |
| 4 | Crash recovery zayıf | Yüksek |

## 3. Alternatif Çözüm

**Seçilen:** InnoDB storage engine
- Full ACID support
- Row-level locking
- Crash recovery
- Foreign key support

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
