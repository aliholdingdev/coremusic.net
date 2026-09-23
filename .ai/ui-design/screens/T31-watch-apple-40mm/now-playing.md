---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Apple Watch Now Playing Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T31
viewport: 396x484
device: Apple Watch SE (40mm) / Series 9 (41mm)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T31-watch-apple-40mm/now-playing.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md"
---

# CoreMusic — Apple Watch Now Playing (T31 396×484)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-396, y:0-484)

```
┌──────────────────────────────────────────────┐
│ x:0                                x:396    │
│ y:0 ┌─── STATUS (h:20) ───────────────────┐ │
│     │ 🔋 100%  ⏰ 07:00                    │ │
│ y:20 └─────────────────────────────────────┘ │
│                                              │
│ y:20 ┌─── CONTENT (h:444) ────────────────┐ │
│     │                                       │ │
│     │  ┌─── ALBUM ART (120×120, circle) ─┐ │ │
│     │  │         🎵                      │ │ │
│     │  │    ┌──────────┐                 │ │ │
│     │  │    │          │                 │ │ │
│     │  │    │  Album   │                 │ │ │
│     │  │    │   Art    │                 │ │ │
│     │  │    │          │                 │ │ │
│     │  │    └──────────┘                 │ │ │
│     │  └─────────────────────────────────┘ │ │
│     │                                       │ │
│     │  Göksel - Sevil Neşelenen            │ │
│     │  Hayat Rüya Gibi                     │ │
│     │  Göksel                              │ │
│     │                                       │ │
│     │  ⏱ 00:05:00 ━━━━━━━━━━━━ 100%      │ │
│     │                                       │ │
│     │  [⏮] [▶] [⏭]                        │ │
│     │                                       │ │
│     │  🔀  🔁  ❤️                          │ │
│     │                                       │ │
│     └───────────────────────────────────────┘ │
│                                              │
└──────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.watch` | Watch layout modifier |
| `.watch__status` | Status bar (h:20) |
| `.watch__content` | Scrollable content |
| `.watch__art` | Album art (120×120, circle) |
| `.watch__info` | Song info |
| `.watch__progress` | Progress bar |
| `.watch__controls` | Playback controls |
| `.watch__actions` | Action buttons (shuffle, repeat, favorite) |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-radius-full` | 50% (circle) |
| `--cm-spacing-sm` | 8px |
| `--cm-font-size-xs` | 10px |
| `--cm-font-size-sm` | 12px |
| `--cm-touch-target` | 44px |

---

## 4. Touch Target

| Cihaz | Min Touch |
|-------|-----------|
| T31 Apple Watch 40mm | 44×44px |

---

## 5. Watch Specific

| Özellik | Değer |
|---------|-------|
| Crown Rotation | Volume kontrolü |
| Haptic Feedback | Buton tıklamasında |
| Always-On Display | Düşük güç modu |
| OLED Power Save | Siyah arka plan |
| Swipe Left | Sonraki şarkı |
| Swipe Right | Önceki şarkı |
| Swipe Up | Volume |
| Swipe Down | Kapat |

---

## 6. PNG Referansı

| PNG | Viewport |
|-----|----------|
| Yok | 396×484 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
