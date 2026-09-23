---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Phone FHD Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T02
viewport: 1290x2796
device: iPhone 14/15/16 Pro Max
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T02-phone-fhd/home-dashboard.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md"
---

# CoreMusic — Phone FHD Home Dashboard (T02 1290×2796)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1290, y:0-2796)

```
┌──────────────────────────────────────────────────────────────┐
│ x:0                                                x:1290  │
│ y:0 ┌─── STATUS BAR (h:54) ──────────────────────────────┐ │
│     │ 📶  🔋 100%  ⏰ 07:00                     🔒  📡   │ │
│ y:54 └────────────────────────────────────────────────────┘ │
│                                                              │
│ y:54 ┌─── DYNAMIC ISLAND (h:120) ────────────────────────┐ │
│     │              ◉ (notch area)                         │ │
│ y:174└────────────────────────────────────────────────────┘ │
│                                                              │
│ y:174 ┌─── HEADER (h:56) ───────────────────────────────┐ │
│     │ Core Music              🔍  👤 Bayram Ali ▼        │ │
│ y:230 └──────────────────────────────────────────────────┘ │
│                                                              │
│ y:230 ┌─── CONTENT (h:2470) ────────────────────────────┐ │
│     │                                                     │ │
│     │  ┌─── NOW PLAYING (w:100%) ──────────────────────┐ │ │
│     │  │ 🎵 Album Art (300×300)                         │ │ │
│     │  │ Göksel - Sevil Neşelenen                       │ │ │
│     │  │ Hayat Rüya Gibi                                │ │ │
│     │  │ Göksel                                         │ │ │
│     │  │ ⏱ 00:05:00 ━━━━━━━━━━━━━━━━━━━━━━━━━ 100%    │ │ │
│     │  │ [⏮] [▶] [⏭]   🔀 🔁                          │ │ │
│     │  └────────────────────────────────────────────────┘ │ │
│     │                                                     │ │
│     │  ┌─── WIDGETS (2 sütun grid) ────────────────────┐ │ │
│     │  │ 🔵 Hoparlörler    │ 📅 07:00                  │ │ │
│     │  │ 🎵 Kültür         │ ❤️ 💜                      │ │ │
│     │  └────────────────────────────────────────────────┘ │ │
│     │                                                     │ │
│     │  ┌─── EN SON DİNLENEN (w:100%) ──────────────────┐ │ │
│     │  │ 🎵 Göksel - Sevil Neşelenen   00:05:00        │ │ │
│     │  │ 🎵 Göksel - Kabahat Sensin    00:05:00        │ │ │
│     │  │ 🎵 Gangsta - Çubuklar          00:07:19        │ │ │
│     │  │ 🎵 Keyifli Enstrümantal        00:05:13        │ │ │
│     │  └────────────────────────────────────────────────┘ │ │
│     │                                                     │ │
│     │  ┌─── PLAYLIST'LER (w:100%) ─────────────────────┐ │ │
│     │  │ 🎵 İLK-10 Listesi       00:55:22              │ │ │
│     │  │ 🎵 Haftalık MIX         00:55:22              │ │ │
│     │  └────────────────────────────────────────────────┘ │ │
│     │                                                     │ │
│     └─────────────────────────────────────────────────────┘ │
│                                                              │
│ y:2700 ┌─── HOME INDICATOR (h:96) ───────────────────────┐ │
│     │                    ━━━━ (gesture bar)                │ │
│ y:2796└────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.phone-fhd` | Phone FHD layout modifier |
| `.phone-fhd__dynamic-island` | Dynamic Island area (h:120) |
| `.phone-fhd__header` | Header (h:56) |
| `.phone-fhd__content` | Scrollable content |
| `.phone-fhd__now-playing` | Now Playing card (w:100%) |
| `.phone-fhd__widgets` | Widget grid (2 sütun) |
| `.phone-fhd__cards` | Card list |
| `.phone-fhd__home-indicator` | Home indicator gesture bar |

---

## 3. Responsive Farklar

| Özellik | T01 (Phone HD 720) | T02 (Phone FHD 1290) |
|---------|---------------------|----------------------|
| Viewport | 720×1280 | 1290×2796 |
| DPI | 2x | 3x |
| Dynamic Island | Yok | Var (h:120) |
| Album Art | 200×200 | 300×300 |
| Widgets | Yok | 2 sütun grid |
| Font Scale | 0.875 | 1 |
| Home Indicator | Yok | Var (gesture bar) |

---

## 4. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-glass` | rgba(255,255,255,0.15) |
| `--cm-radius-md` | 16px |
| `--cm-spacing-md` | 20px |
| `--cm-dynamic-island-h` | 120px |
| `--cm-home-indicator-h` | 96px |

---

## 5. Touch Target

| Cihaz | Min Touch |
|-------|-----------|
| T02 iPhone Pro Max | 48×48px |

---

## 6. PNG Referansı

| PNG | Viewport |
|-----|----------|
| Yok (referans: T08 PNG'lerinden türetildi) | 1290×2796 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
