---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Phone HD Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T01
viewport: 720x1280
device: Samsung Galaxy J7 (2016) / Galaxy A13
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T01-phone-hd/home-dashboard.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md"
---

# CoreMusic — Phone HD Home Dashboard (T01 720×1280)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-720, y:0-1280)

```
┌────────────────────────────────────────────────────┐
│ x:0                                        x:720  │
│ y:0 ┌─── STATUS BAR (h:24) ─────────────────────┐ │
│     │ 📶  🔋 100%  ⏰ 07:00                     │ │
│ y:24 └───────────────────────────────────────────┘ │
│                                                    │
│ y:24 ┌─── HEADER (h:56) ────────────────────────┐ │
│     │ Core Music          🔍  👤                 │ │
│ y:80 └───────────────────────────────────────────┘ │
│                                                    │
│ y:80 ┌─── CONTENT (h:1100) ─────────────────────┐ │
│     │                                             │ │
│     │  ┌─── NOW PLAYING (w:100%) ──────────────┐ │ │
│     │  │ 🎵 Album Art (200×200)                 │ │ │
│     │  │ Göksel - Sevil Neşelenen               │ │ │
│     │  │ Hayat Rüya Gibi                        │ │ │
│     │  │ Göksel                                 │ │ │
│     │  │ ⏱ 00:05:00 ━━━━━━━━━━━━━━━ 100%      │ │ │
│     │  │ [⏮] [▶] [⏭]   🔀 🔁                  │ │ │
│     │  └────────────────────────────────────────┘ │ │
│     │                                             │ │
│     │  ┌─── EN SON DİNLENEN (w:100%) ──────────┐ │ │
│     │  │ 🎵 Göksel - Sevil Neşelenen   00:05:00│ │ │
│     │  │ 🎵 Göksel - Kabahat Sensin    00:05:00│ │ │
│     │  │ 🎵 Gangsta - Çubuklar          00:07:19│ │ │
│     │  │ 🎵 Keyifli Enstrümantal        00:05:13│ │ │
│     │  │ 🎵 Erkin Koray - Fantastik     00:10:00│ │ │
│     │  └────────────────────────────────────────┘ │ │
│     │                                             │ │
│     │  ┌─── PLAYLIST'LER (w:100%) ─────────────┐ │ │
│     │  │ 🎵 İLK-10 Listesi       00:55:22      │ │ │
│     │  │ 🎵 Haftalık MIX         00:55:22      │ │ │
│     │  │ 🎵 Ruh Haline Göre      00:55:22      │ │ │
│     │  └────────────────────────────────────────┘ │ │
│     │                                             │ │
│     └─────────────────────────────────────────────┘ │
│                                                    │
│ y:1200 ┌─── BOTTOM TAB NAV (h:80) ──────────────┐ │
│     │ 🏠 Ana Sayfa │ 📚 Kütüphane │ ⚙️ Ayarlar  │ │
│ y:1280└───────────────────────────────────────────┘ │
└────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.phone-hd` | Phone HD layout modifier |
| `.phone-hd__status` | Status bar (h:24) |
| `.phone-hd__header` | Header (h:56) |
| `.phone-hd__content` | Scrollable content |
| `.phone-hd__now-playing` | Now Playing card (w:100%) |
| `.phone-hd__cards` | Card list (w:100%) |
| `.phone-hd__tab-nav` | Bottom tab navigation (h:80) |
| `.tab-nav__item` | Tab item |
| `.tab-nav__item--active` | Active tab |
| `.tab-nav__icon` | Tab icon (24×24) |
| `.tab-nav__label` | Tab label (10px) |

---

## 3. Responsive Farklar (Embedded vs Phone)

| Özellik | T08 (Embedded 1024) | T01 (Phone HD 720) |
|---------|---------------------|---------------------|
| Layout | Split 42/58 | Tek sütun |
| Now Playing | w:42% | w:100% |
| Widgets | w:58% | Yok (kartlarda) |
| Sidebar | Yok | Yok |
| Bottom Tab | Yok | h:80, 3 tab |
| Touch Target | 48px | 48px |
| Font Scale | 1 | 0.875 |

---

## 4. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-glass` | rgba(255,255,255,0.15) |
| `--cm-radius-md` | 12px |
| `--cm-spacing-md` | 16px |
| `--cm-tab-height` | 80px |
| `--cm-status-height` | 24px |
| `--cm-header-height` | 56px |

---

## 5. Touch Target

| Cihaz | Min Touch |
|-------|-----------|
| T01 Phone HD | 48×48px |

---

## 6. WCAG Uyumu

| Kontrol | Durum |
|---------|:-----:|
| Kontrast 4.5:1 | ✅ |
| Keyboard Tab Order | ✅ |
| Focus Visible | ✅ |
| Screen Reader | ✅ |
| Touch Target | ✅ 48px |

---

## 7. PNG Referansı

| PNG | Viewport |
|-----|----------|
| Yok (referans: T08 PNG'lerinden türetildi) | 720×1280 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
