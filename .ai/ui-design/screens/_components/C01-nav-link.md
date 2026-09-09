---
title: "CoreMusic - C01 Nav Link Component Spec"
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

# C01 — Navigation Link

## BEM

```css
.nav-link { }
.nav-link--active { }
```

## PNG Reference

Tüm header PNG'leri — 8 nav-link yan yana

## ASCII Art

```
┌─────────────────────────────────────────────────────────────┐
│ "Core Music" [Ana Sayfa] [Keşfet] [Albümler] [Sanatçılar] │
│                   ↑          ↑        ↑          ↑          │
│                   nav-link   nav-link nav-link   nav-link   │
│                   (active)                                 │
└─────────────────────────────────────────────────────────────┘

Tek nav-link:
┌──────────────┐
│  Ana Sayfa   │  font: Arima, 10px
│  (24×24px)   │  WCAG: 48px olmalı
└──────────────┘
```

## Ölçüler

| Token | Değer |
|-------|-------|
| Font | `--font-body` (Arima) |
| Boyut | `--text-xs` (10px) |
| Ağırlık | `--font-normal` (400) |
| Renk (default) | `rgba(255,255,255,0.85)` |
| Renk (active) | `var(--theme-primary)` |
| Padding | `--space-1` (4px) |
| Hit area | ~24×24px ❌ → 48px olmalı |

## ITCSS: 03_Layout (_header.css)
## WCAG: ❌ İHLAL — 48px olmalı
## Kullanım: Tüm app ekranları

---

*Component Spec C01 Nav Link v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
