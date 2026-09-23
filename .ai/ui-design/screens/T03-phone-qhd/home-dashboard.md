---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Phone QHD Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T03
viewport: 1440x3120
device: Samsung Galaxy S25/S26 Ultra
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T03-phone-qhd/home-dashboard.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md"
---

# CoreMusic — Phone QHD Home Dashboard (T03 1440×3120)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1440, y:0-3120)

```
┌──────────────────────────────────────────────────────────────────┐
│ x:0                                                    x:1440  │
│ y:0 ┌─── STATUS BAR (h:48) ──────────────────────────────────┐ │
│     │ 📶  🔋 100%  ⏰ 07:00              🔒  📡  🎵          │ │
│ y:48 └────────────────────────────────────────────────────────┘ │
│                                                                  │
│ y:48 ┌─── HEADER (h:64) ─────────────────────────────────────┐ │
│     │ Core Music                  🔍  👤 Bayram Ali ▼         │ │
│ y:112└────────────────────────────────────────────────────────┘ │
│                                                                  │
│ y:112 ┌─── CONTENT (h:2912) ────────────────────────────────┐ │
│     │                                                         │ │
│     │  ┌─── NOW PLAYING (w:100%) ──────────────────────────┐ │ │
│     │  │ 🎵 Album Art (400×400)                             │ │ │
│     │  │ Göksel - Sevil Neşelenen                           │ │ │
│     │  │ Hayat Rüya Gibi                                    │ │ │
│     │  │ Göksel                                             │ │ │
│     │  │ ⏱ 00:05:00 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ 100%    │ │ │
│     │  │ [⏮] [▶] [⏭]   🔀 🔁                              │ │ │
│     │  └────────────────────────────────────────────────────┘ │ │
│     │                                                         │ │
│     │  ┌─── WIDGETS (2 sütun grid) ────────────────────────┐ │ │
│     │  │ 🔵 Hoparlörler    │ 📅 07:00                      │ │ │
│     │  │ 🎵 Kültür         │ ❤️ 💜                          │ │ │
│     │  │ 📊 İstatistikler  │ 🎵 Popüler                     │ │ │
│     │  └────────────────────────────────────────────────────┘ │ │
│     │                                                         │ │
│     │  ┌─── EN SON DİNLENEN (w:100%) ──────────────────────┐ │ │
│     │  │ 🎵 Göksel - Sevil Neşelenen   00:05:00            │ │ │
│     │  │ 🎵 Göksel - Kabahat Sensin    00:05:00            │ │ │
│     │  │ 🎵 Gangsta - Çubuklar          00:07:19            │ │ │
│     │  │ 🎵 Keyifli Enstrümantal        00:05:13            │ │ │
│     │  │ 🎵 Erkin Koray - Fantastik     00:10:00            │ │ │
│     │  └────────────────────────────────────────────────────┘ │ │
│     │                                                         │ │
│     │  ┌─── PLAYLIST'LER (w:100%) ─────────────────────────┐ │ │
│     │  │ 🎵 İLK-10 Listesi       00:55:22                  │ │ │
│     │  │ 🎵 Haftalık MIX         00:55:22                  │ │ │
│     │  │ 🎵 Ruh Haline Göre      00:55:22                  │ │ │
│     │  └────────────────────────────────────────────────────┘ │ │
│     │                                                         │ │
│     └─────────────────────────────────────────────────────────┘ │
│                                                                  │
│ y:3024 ┌─── HOME INDICATOR (h:96) ───────────────────────────┐ │
│     │                    ━━━━ (gesture bar)                    │ │
│ y:3120└────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.phone-qhd` | Phone QHD layout modifier |
| `.phone-qhd__header` | Header (h:64) |
| `.phone-qhd__content` | Scrollable content |
| `.phone-qhd__now-playing` | Now Playing card (w:100%) |
| `.phone-qhd__widgets` | Widget grid (2 sütun) |
| `.phone-qhd__cards` | Card list |
| `.phone-qhd__home-indicator` | Home indicator gesture bar |

---

## 3. Responsive Farklar

| Özellik | T01 (Phone HD) | T02 (Phone FHD) | T03 (Phone QHD) |
|---------|----------------|-----------------|-----------------|
| Viewport | 720×1280 | 1290×2796 | 1440×3120 |
| DPI | 2x | 3x | 3.5x |
| Album Art | 200×200 | 300×300 | 400×400 |
| Widgets | Yok | 2 sütun | 2 sütun |
| Font Scale | 0.875 | 1 | 1 |

---

## 4. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-radius-md` | 16px |
| `--cm-spacing-md` | 24px |
| `--cm-home-indicator-h` | 96px |

---

## 5. Touch Target

| Cihaz | Min Touch |
|-------|-----------|
| T03 Galaxy S25 Ultra | 48×48px |

---

## 6. PNG Referansı

| PNG | Viewport |
|-----|----------|
| Yok | 1440×3120 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
