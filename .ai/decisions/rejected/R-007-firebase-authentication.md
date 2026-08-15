---
type: decision
id: "R-007"
title: "REJECTED: Firebase Authentication"
category: "security"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, security, auth, firebase]
risk-level: "high"
rejection-reason: "Harici bağımlılık, merkezi auth gerekli"
rejected-by: "ADR-043"
references:
  - "[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]"
---

# REJECTED: Firebase Authentication

---

## 1. Executive Summary

Firebase Authentication kullanımı **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | Harici bağımlılık | Yüksek |
| 2 | ADR-043: Merkezi auth gerekli | Yüksek |
| 3 | Vendor lock-in riski | Yüksek |
| 4 | Veri gizliliği | Orta |

## 3. Alternatif Çözüm

**Seçilen:** auth.coremusic.net merkezi auth (ADR-043)
- Kendi auth sistemimiz
- JWT + Session hybrid
- Harici bağımlılık yok
- Tam kontrol

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
