---
title: "CoreMusic - C14 Modal Component Spec"
type: component-spec
category: component-spec
date: 2026-08-11
updated: 2026-09-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
platform: home-1024
---

# C14 — Modal / Popup

## BEM

```css
.modal { }
.modal__overlay { }
.modal__content { }
.modal__header { }
.modal__body { }
.modal__footer { }
.modal__close { }
```

## ASCII Art

```
┌── OVERLAY (tam ekran) ──────────────────────────────────┐
│  backdrop-filter: blur(4px)                              │
│  rgba(0,0,0,0.5)                                        │
│                                                          │
│    ┌── MODAL ──────────────────────────────────────┐    │
│    │ [✕ kapat 44×44px]                             │    │
│    │ [Başlık]                                       │    │
│    │ [İçerik]                                       │    │
│    │ [Aksiyonlar]                                   │    │
│    │ r:16px, glass, border:1px solid rgba(255,...,0.1)│   │
│    └────────────────────────────────────────────────┘    │
└──────────────────────────────────────────────────────────┘
```

## ITCSS: 04_Components
## WCAG: ✅ UYGUN (close 44px)
## Kullanım: WiFi, BT, Welcome, EQ

---

*Component Spec C14 Modal v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
