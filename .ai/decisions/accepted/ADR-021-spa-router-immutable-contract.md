---
type: decision
id: "021"
title: "ADR-021: SPA Router Immutable Contract"
category: "routing"
status: "frozen"
date: "2026-04-25"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [routing, spa, router, contract, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[decisions/accepted/ADR-083-spa-router]]"
---

# ADR-021: SPA Router Immutable Contract

---

## 1. Executive Summary

CoreMusic SPA Router sözleşmelesi **değiştirilemez** (immutable). Route tanımları, guard pipeline ve render akışı sabittir.

## 2. Decision

### Router Sözleşmesi

| # | Kural | Durum |
|---|-------|-------|
| 1 | History API (pushState) | ✅ Zorunlu |
| 2 | Route guard pipeline | ✅ Zorunlu |
| 3 | Partial rendering | ✅ Zorunlu |
| 4 | Backend-controlled auth | ✅ Zorunlu |
| 5 | Route contract immutable | ✅ Zorunlu |

### Route Guard Pipeline

```
Route Change → Auth Check → Permission Check → Load Page → Render
```

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-021: SPA Router Immutable Contract v2.0.0 — CoreMusic Routing*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
