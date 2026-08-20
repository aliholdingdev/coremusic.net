---
title: "ADR-044: Dynamic User Theme Engine"
status: active
date: 2026-08-04
tags: [frontend, theme, gender, dynamic, css, active]
---

# ADR-044: Dynamic User Theme Engine

---

## 1. Executive Summary

CoreMusic'te **cinsiyet bazlÄ± dinamik tema** sistemi bulunur. KullanÄ±cÄ± cinsiyetine gÃ¶re tema renkleri deÄŸiÅŸir: femaleâ†’pink, maleâ†’blue, neutralâ†’default. CSS custom properties ile anÄ±nda geÃ§iÅŸ yapÄ±lÄ±r.

## 2. Decision

### Tema HaritasÄ±

| Cinsiyet | Primary Renk | Tema |
|----------|-------------|------|
| female | #ec4899 (pink) | Pink |
| male | #3b82f6 (blue) | Blue |
| neutral | #6366f1 (indigo) | Default |

### CSS Custom Properties

```css
:root {
    --theme-primary: #6366f1; /* Default */
}

[data-gender="female"] {
    --theme-primary: #ec4899;
}

[data-gender="male"] {
    --theme-primary: #3b82f6;
}
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | CSS custom properties | âœ… Zorunlu |
| 2 | data-gender attribute | âœ… Zorunlu |
| 3 | Sayfa yenileme yok | âœ… Zorunlu |
| 4 | Admin baÄŸÄ±msÄ±z tema | âœ… Zorunlu |
| 5 | user_preferences DB | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-044: Dynamic User Theme Engine v2.0.0 â€” CoreMusic Frontend*
*Authority: UI Designer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*