---
type: decision
id: "046"
title: "ADR-046: Cross-View State Preservation"
category: "frontend"
status: "active"
date: "2026-08-08"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [frontend, state, preservation, cross-view, active]
risk-level: "low"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]]"
  - "[[architecture/l3-presentation]]"
---

# ADR-046: Cross-View State Preservation

---

## 1. Executive Summary

View mode'lar arası geçişlerde **state korunur**. Kullanıcı pro modundan home moduna geçtiğinde, scroll pozisyonu, seçimler ve geçici veriler korunur.

## 2. Decision

### State Koruma Alanları

| Alan | Korunma | Yöntem |
|------|---------|--------|
| Scroll pozisyonu | ✅ | sessionStorage |
| Seçili öğe | ✅ | Client-side state |
| Arama filtresi | ✅ | URL params |
| Player durumu | ✅ | Global state |
| Form verileri | ✅ | sessionStorage |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Scroll state korunur | ✅ Zorunlu |
| 2 | Player state korunur | ✅ Zorunlu |
| 3 | URL params ile state | ✅ Zorunlu |
| 4 | sessionStorage kullanımı | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-046: Cross-View State Preservation v2.0.0 — CoreMusic Frontend*
*Authority: UI Designer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
