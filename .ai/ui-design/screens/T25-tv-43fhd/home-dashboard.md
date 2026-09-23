---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — TV FHD Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T25
viewport: 1920x1080
device: 43" FHD Smart TV
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T25-tv-43fhd/home-dashboard.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md"
---

# CoreMusic — TV FHD Home Dashboard (T25 1920×1080)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                                              x:1920 │
│ y:0 ┌─── HEADER (h:80) ─────────────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│     │ Core Music    Ana Sayfa   Keşfet   Albümler   Sanatçılar   Göz At   Geçmiş   Ayarlar                  👤 Bayram Ali   🔊  🌐   │ │
│ y:80 └────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                                                        │
│ y:80 ┌─── CONTENT (h:900) ────────────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│     │                                                                                                                                  │ │
│     │  ┌─── NOW PLAYING (25%, w:480) ───┐  ┌─── WELCOME (50%, w:960) ──────────────┐  ┌─── WIDGETS (25%, w:480) ─────────────────┐  │ │
│     │  │ 🎵 Album Art (200×200)         │  │ ✨ Hoş Geldin ✨                      │  │ 🔵 Hoparlörler    📅 07:00    ☁️ Hava   │  │ │
│     │  │ Göksel - Sevil Neşelenen       │  │ Bayram Ali                            │  │ 🎵 Kültür         ❤️ 💜       🔴 YT    │  │ │
│     │  │ Hayat Rüya Gibi                │  │ "Müzik, ruhun gıdası..."              │  │ 📊 İstatistikler  🎵 Popüler  📁 Son  │  │ │
│     │  │ ⏱ 00:05:00 ━━━━━━━━━━ 100%    │  │ ▶ Keyfime Başla                       │  │                                        │  │ │
│     │  │ [⏮] [▶] [⏭]   🔀 🔁          │  │                                        │  │                                        │  │ │
│     │  └────────────────────────────────┘  └────────────────────────────────────────┘  └────────────────────────────────────────┘  │ │
│     │                                                                                                                                  │ │
│     │  ┌─── EN SON DİNLENEN (33%, w:640) ──────────────────┐ ┌─── PLAYLIST'LER (33%, w:640) ─────────────────────────────────────┐  │ │
│     │  │ 🎵 Göksel - Sevil Neşelenen   00:05:00            │ │ 🎵 İLK-10 Listesi              00:55:22                           │  │ │
│     │  │ 🎵 Göksel - Kabahat Sensin    00:05:00            │ │ 🎵 Haftalık MIX                00:55:22                           │  │ │
│     │  │ 🎵 Gangsta - Çubuklar          00:07:19            │ │ 🎵 Ruh Haline Göre Mix         00:55:22                           │  │ │
│     │  └────────────────────────────────────────────────────┘ └────────────────────────────────────────────────────────────────────┘  │ │
│     │                                                                                                                                  │ │
│     └──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                                                        │
│ y:980 ┌─── FOOTER PLAYER (h:100) ─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│     │ 🎵 Göksel - Sevil Neşelenen    [⏮] [▶] [⏹] [⏭]    🔊 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ % 100  │ │
│ y:1080└───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.tv-fhd` | TV FHD layout modifier |
| `.tv-fhd__header` | Header (h:80, font:1.5x) |
| `.tv-fhd__content` | Content area |
| `.tv-fhd__now-playing` | Now Playing panel |
| `.tv-fhd__welcome` | Welcome banner |
| `.tv-fhd__widgets` | Widget panel |
| `.tv-fhd__cards` | Card list (3 sütun) |
| `.tv-fhd__footer` | Footer player (h:100) |

---

## 3. Responsive Farklar

| Özellik | T08 (Embedded) | T17 (Desktop) | T25 (TV FHD) |
|---------|----------------|---------------|--------------|
| Viewport | 1024×600 | 1920×1080 | 1920×1080 |
| Header | h:60 | h:60 | h:80 |
| Footer | h:90 | h:90 | h:100 |
| Touch Target | 48px | 24px | 80px |
| Font Scale | 1 | 1 | 1.5 |
| Navigation | Touch | Mouse+KB | D-pad |
| Focus Indicator | 2px outline | 2px outline | 4px outline, large |

---

## 4. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-radius-lg` | 24px |
| `--cm-spacing-xl` | 32px |
| `--cm-touch-target` | 80px |
| `--cm-font-scale` | 1.5 |
| `--cm-header-height` | 80px |
| `--cm-footer-height` | 100px |
| `--cm-focus-outline` | 4px solid #ff4fd8 |

---

## 5. Touch Target

| Cihaz | Min Touch |
|-------|-----------|
| T25 43" FHD TV | 80×80px (D-pad) |

---

## 6. D-pad Navigasyon

```
D-pad Navigasyon Sırası:
┌─────────────────────────────────────┐
│ Header → Content (sol üst) →       │
│ Content (sağ üst) →                │
│ Content (sol orta) →               │
│ Content (sağ orta) →               │
│ Content (sol alt) →                │
│ Content (sağ alt) →                │
│ Footer Player                      │
└─────────────────────────────────────┘

Focus Indicator: 4px solid #ff4fd8 + box-shadow
Focus Ring: 8px offset
```

---

## 7. PNG Referansı

| PNG | Viewport |
|-----|----------|
| Yok | 1920×1080 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
