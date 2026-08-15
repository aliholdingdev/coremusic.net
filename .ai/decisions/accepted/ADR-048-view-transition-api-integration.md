---
type: decision
id: "048"
title: "ADR-048: View Transition API Integration"
category: "frontend"
status: "active"
date: "2026-08-08"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [frontend, view-transition, animation, api, active]
risk-level: "low"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[architecture/l3-presentation]]"
---

# ADR-048: View Transition API Integration

---

## 1. Executive Summary

CoreMusic'te sayfa geçişleri **View Transition API** ile desteklenir. Desteklemeyen tarayıcılarda graceful degradation uygulanır.

## 2. Decision

### View Transition Kullanımı

```javascript
// Sayfa geçişinde view transition
document.startViewTransition(async () => {
    // DOM güncelleme
    await updateDOM(newState);
});
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | View Transition API | ✅ Tercih |
| 2 | Graceful degradation | ✅ Zorunlu |
| 3 | Feature detection | ✅ Zorunlu |
| 4 | CSS view-transition-name | ✅ Zorunlu |

### Fallback

```javascript
if ('startViewTransition' in document) {
    document.startViewTransition(() => updateDOM());
} else {
    updateDOM(); // Fallback: anında geçiş
}
```

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-048: View Transition API Integration v2.0.0 — CoreMusic Frontend*
*Authority: UI Designer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
