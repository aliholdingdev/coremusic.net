---
type: decision
id: "015"
title: "ADR-015: Env Parser Strategy"
category: "infrastructure"
status: "frozen"
date: "2026-03-30"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [infrastructure, env, config, frozen]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-034-credential-vault-normalization]]"
---

# ADR-015: Env Parser Strategy

---

## 1. Executive Summary

CoreMusic ortam değişkenleri `.env` dosyasından okunur. `vlucas/phpdotenv` kullanılır. Hassas bilgiler credential vault'ta AES-256-GCM ile şifrelenir.

## 2. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | .env dosyası | ✅ Zorunlu |
| 2 | phpdotenv kullanımı | ✅ Zorunlu |
| 3 | Hassas bilgi credential vault | ✅ Zorunlu |
| 4 | .gitignore'da .env | ✅ Zorunlu |
| 5 | Environment-specific configs | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-015: Env Parser Strategy v2.0.0 — CoreMusic Infrastructure*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
