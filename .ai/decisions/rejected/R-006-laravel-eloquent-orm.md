---
type: decision
id: "R-006"
title: "REJECTED: Laravel Eloquent ORM"
category: "database"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, database, orm, eloquent, laravel]
risk-level: "critical"
rejection-reason: "ORM yasak (ADR-002), framework yasak"
rejected-by: "ADR-002, ADR-001"
references:
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
---

# REJECTED: Laravel Eloquent ORM

---

## 1. Executive Summary

Laravel Eloquent ORM kullanımı **kesinlikle reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | ADR-002: ORM yasak | Kritik |
| 2 | ADR-001: Framework yasak | Kritik |
| 3 | SQL injection riski | Yüksek |
| 4 | Overhead | Yüksek |
| 5 | Debug zorluğu | Orta |

## 3. Alternatif Çözüm

**Seçilen:** PDO prepared statement (ADR-002)
- Doğrudan SQL kontrolü
- Minimal overhead
- Kolay debug
- Güvenli prepared statement

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
