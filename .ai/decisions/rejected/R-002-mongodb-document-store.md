---
type: decision
id: "R-002"
title: "REJECTED: MongoDB Document Store"
category: "database"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, database, mongodb, nosql]
risk-level: "high"
rejection-reason: "BCNF uyumsuz, SQL mastery, ADR-002/040"
rejected-by: "ADR-002, ADR-040"
references:
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# REJECTED: MongoDB Document Store

---

## 1. Executive Summary

MongoDB veya diğer NoSQL veritabanlarının kullanımı **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | BCNF normalizasyonu ile uyumsuz | Kritik |
| 2 | SQL mastery mevcut | Yüksek |
| 3 | ADR-002: PDO zorunlu | Yüksek |
| 4 | ADR-040: 18 BCNF DB | Yüksek |

## 3. Reddetilen Yaklaşım

MongoDB:
- Schemaless yapı BCNF ile çelişir
- Veri bütünlüğü riski
- SQL ekosisteminden uzaklaşma
- CoreMusic'in 18 BCNF yapısına aykırı

## 4. Alternatif Çözüm

**Seçilen:** MySQL 9 BCNF (ADR-040)
- 18 izole veritabanı
- 156 tablo
- BCNF normalizasyonu
- Prepared statement (ADR-002)

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
