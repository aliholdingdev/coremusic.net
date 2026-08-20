---
title: "ADR-045: Multi-Domain View Mode Architecture"
status: active
date: 2026-08-08
tags: [frontend, view-mode, multi-domain, active]
---

# ADR-045: Multi-Domain View Mode Architecture

---

## 1. Executive Summary

CoreMusic'te her panel (music, home, pro, studio) kendi **view mode**'una sahiptir. View mode'lar CSS class'larÄ± ile yÃ¶netilir ve cihaz tipine gÃ¶re otomatik seÃ§ilir.

## 2. Decision

### View Mode'lar

| Mod | CSS Class | KullanÄ±m |
|-----|-----------|----------|
| home | .v-home | Ev medya merkezi |
| pro | .v-pro | Profesyonel |
| studio | .v-studio | StÃ¼dyo |
| car | .v-car | AraÃ§ iÃ§i |
| admin | .v-admin | YÃ¶netim |

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
| 1 | CSS class ile view mode | âœ… Zorunlu |
| 2 | Cihaz algÄ±lama | âœ… Zorunlu |
| 3 | Responsive tasarÄ±m | âœ… Zorunlu |
| 4 | View mode persistence | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-045: Multi-Domain View Mode Architecture v2.0.0 â€” CoreMusic Frontend*
*Authority: UI Designer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*