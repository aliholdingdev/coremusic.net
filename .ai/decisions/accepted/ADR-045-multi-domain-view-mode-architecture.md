---
type: decision
id: "045"
title: "ADR-045: Multi-Domain View Mode Architecture"
category: "frontend"
status: "active"
date: "2026-08-08"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [frontend, view-mode, multi-domain, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-004-multi-domain-spa]]"
  - "[[architecture/l3-presentation]]"
---

# ADR-045: Multi-Domain View Mode Architecture

---

## 1. Executive Summary

CoreMusic'te her panel (music, home, pro, studio) kendi **view mode**'una sahiptir. View mode'lar CSS class'ları ile yönetilir ve cihaz tipine göre otomatik seçilir.

## 2. Decision

### View Mode'lar

| Mod | CSS Class | Kullanım |
|-----|-----------|----------|
| home | .v-home | Ev medya merkezi |
| pro | .v-pro | Profesyonel |
| studio | .v-studio | Stüdyo |
| car | .v-car | Araç içi |
| admin | .v-admin | Yönetim |

### Cihaz Loader

```javascript
// device-loader.js
function detectDevice() {
    const width = window.innerWidth;
    if (width <= 480) return 'd-phone';
    if (width <= 768) return 'd-tablet';
    return 'd-desktop';
}
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | CSS class ile view mode | ✅ Zorunlu |
| 2 | Cihaz algılama | ✅ Zorunlu |
| 3 | Responsive tasarım | ✅ Zorunlu |
| 4 | View mode persistence | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-045: Multi-Domain View Mode Architecture v2.0.0 — CoreMusic Frontend*
*Authority: UI Designer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
