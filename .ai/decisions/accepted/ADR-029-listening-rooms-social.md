---
type: decision
id: "029"
title: "ADR-029: Listening Rooms Social"
category: "social"
status: "frozen"
date: "2026-05-30"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [social, listening-rooms, realtime, frozen]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-039-7-service-platform-architecture]]"
---

# ADR-029: Listening Rooms Social

---

## 1. Executive Summary

CoreMusic'te **dinleme odaları** özelliği bulunur. Kullanıcılar aynı anda müzik dinleyebilir, yorum yapabilir ve etkileşimde bulunabilir.

## 2. Decision

### Odözellikleri

| Özellik | Değer |
|---------|-------|
| Max katılımcı | 50 |
| Real-time senkronizasyon | ✅ |
| Chat desteği | ✅ |
| Moderasyon | ✅ |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Real-time senkronizasyon | ✅ Zorunlu |
| 2 | Chat desteği | ✅ Zorunlu |
| 3 | Moderasyon | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-029: Listening Rooms Social v2.0.0 — CoreMusic Social*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
