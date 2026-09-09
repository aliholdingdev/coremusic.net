---
title: "CoreMusic - C13 Track List Row Component Spec"
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

# C13 — Track List Row

## BEM

```css
.track-row { }
.track-row--active { }
.track-row__thumb { }
.track-row__title { }
.track-row__duration { }
.track-row__rating { }
```

## ASCII Art

```
Normal:
┌─────────────────────────────────────────────────────────────────┐
│ [♪] Göksel - Sevil Neşelen                00:05:00  ★★★★★    │
│  ↑     ↑                                    ↑          ↑        │
│  thumb title                               duration  stars     │
│  20×20  ~12px                              ~10px     20×20px  │
└─────────────────────────────────────────────────────────────────┘
Yükseklik: ~40px → 48px olmalı

Aktif:
┌═════════════════════════════════════════════════════════════════┐
│ [♪] Göksel - Sevil Neşelen  ← PEMBE        00:05:00  ★★★★★   │
│  bg: rgba(255,79,216,0.15)                                     │
│  border-left: 3px solid var(--theme-primary)                   │
└═════════════════════════════════════════════════════════════════┘
```

## ITCSS: 04_Components
## WCAG: ❌ İHLAL — 48px olmalı
## Kullanım: Album Detail, Playlist, Göz At

---

*Component Spec C13 Track List v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
