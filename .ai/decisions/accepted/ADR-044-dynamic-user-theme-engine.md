---
type: decision
id: "044"
title: "ADR-044: Dynamic User Theme Engine"
category: "frontend"
status: "active"
date: "2026-08-04"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [frontend, theme, gender, dynamic, css, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[architecture/l3-presentation]]"
---

# ADR-044: Dynamic User Theme Engine

---

## 1. Executive Summary

CoreMusic'te **cinsiyet bazlı dinamik tema** sistemi bulunur. Kullanıcı cinsiyetine göre tema renkleri değişir: female→pink, male→blue, neutral→default. CSS custom properties ile anında geçiş yapılır.

## 2. Decision

### Tema Haritası

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
| 1 | CSS custom properties | ✅ Zorunlu |
| 2 | data-gender attribute | ✅ Zorunlu |
| 3 | Sayfa yenileme yok | ✅ Zorunlu |
| 4 | Admin bağımsız tema | ✅ Zorunlu |
| 5 | user_preferences DB | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-044: Dynamic User Theme Engine v2.0.0 — CoreMusic Frontend*
*Authority: UI Designer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
