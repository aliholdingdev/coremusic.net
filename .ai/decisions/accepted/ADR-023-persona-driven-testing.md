---
type: decision
id: "023"
title: "ADR-023: Persona-Driven Testing"
category: "testing"
status: "frozen"
date: "2026-05-01"
updated: "2026-08-15"
authority: "QA Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [testing, persona, driven, frozen]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[testing/strategy]]"
---

# ADR-023: Persona-Driven Testing

---

## 1. Executive Summary

CoreMusic testleri **persona bazlı** yürütülür. Her kullanıcı tipi için persona tanımları ve test senaryoları oluşturulur.

## 2. Decision

### Persona'lar

| Persona | Kullanım | Öncelik |
|---------|----------|---------|
| Music Lover | Günlük müzik dinleme | Yüksek |
| Producer | Stüdyo üretimi | Yüksek |
| DJ | Canlı performans | Orta |
| Audiophile | Yüksek kalite ses | Yüksek |
| Car User | Araç içi | Orta |
| Admin | Sistem yönetimi | Yüksek |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Persona tanımları | ✅ Zorunlu |
| 2 | Test senaryoları persona bazlı | ✅ Zorunlu |
| 3 | %80 coverage minimum | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-023: Persona-Driven Testing v2.0.0 — CoreMusic Testing*
*Authority: QA Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
