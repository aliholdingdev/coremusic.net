---
title: CoreMusic — C14 Modal Component Spec
date: 2026-08-11
version: 2.0.0
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
