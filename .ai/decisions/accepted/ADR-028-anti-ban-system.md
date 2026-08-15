---
type: decision
id: "028"
title: "ADR-028: Anti-Ban System"
category: "download"
status: "frozen"
date: "2026-05-25"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [download, anti-ban, rate-limit, proxy, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-026-download-service-architecture]]"
---

# ADR-028: Anti-Ban System

---

## 1. Executive Summary

CoreMusic download servisi **anti-ban** sistemi ile korunur. Rate limiting, proxy rotasyonu ve ARL token yönetimi ile platform ban'ları engellenir.

## 2. Decision

### Anti-Ban Stratejileri

| # | Strateji | Durum |
|---|----------|-------|
| 1 | Rate limiting (platform bazlı) | ✅ Zorunlu |
| 2 | Proxy rotasyonu | ✅ Zorunlu |
| 3 | ARL token rotasyonu | ✅ Zorunlu |
| 4 | User-Agent çeşitliliği | ✅ Zorunlu |
| 5 | Random delay | ✅ Zorunlu |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Anti-ban aktif | ✅ Zorunlu |
| 2 | Proxy rotation | ✅ Zorunlu |
| 3 | ARL token management | ✅ Zorunlu |
| 4 | Graceful degradation | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-028: Anti-Ban System v2.0.0 — CoreMusic Download*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
