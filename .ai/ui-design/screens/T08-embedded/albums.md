---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Albums Page Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/albums.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Albumler Page.png"
---

# CoreMusic — Albums Page (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                              x:1024    │
│ y:0 ┌─── HEADER (h:60) ────────────────────────────────────────────────────────────────────────────────┐  │
│     │ ← Geri   Albümler / Tüm Albümler                          🔍 Sanatçı Adı Ara                     │  │
│ y:60 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:60 ┌─── CONTENT (h:450) ──────────────────────────────────────────────────────────────────────────────┐  │
│     │                                                                                                    │  │
│     │  ┌─── LEFT PANEL (60%, w:614) ──────────────────┐  ┌─── RIGHT PANEL (40%, w:410) ────────────┐   │  │
│     │  │                                                │  │                                          │   │  │
│     │  │  Albümler                                      │  │  🎵 Albüm Kapak Görseli (300×300)       │   │  │
│     │  │  Kütüphanede depolananharici Albümler  │  │  ┌──────────────────────────────┐       │   │  │
│     │  │                                                │  │  │                              │       │   │  │
│     │  │  [Tümü] [Pop] [Arabesk] [Dans] [Oyun Havaası]│  │  │                              │       │   │  │
│     │  │  [Damard] [Org] [Yabancı Pop] [Kpop-Kore]    │  │  │                              │       │   │  │
│     │  │                                                │  │  └──────────────────────────────┘       │   │  │
│     │  │  ┌─── Album Grid (4×N) ─────────────────────┐ │  │                                          │   │  │
│     │  │  │ 🎵 Nobetçi Eczane    │ 🎵 Bergen - Tam  │ │  │  Nobetçi Eczane Ferhat                  │   │  │
│     │  │  │    Ferhat Kasetleri  │    Şarkılar      │ │  │  Kasetleri                              │   │  │
│     │  │  │    Bergen            │    Bergen         │ │  │                                          │   │  │
│     │  │  ├──────────────────────┼──────────────────┤ │  │  [▶ Hemen Çal]  [Karışık Çal] [⋯]      │   │  │
│     │  │  │ 🎵 Bergen - Tam      │ 🎵 Bergen - Tam  │ │  │                                          │   │  │
│     │  │  │    Şarkılar         │    Şarkılar      │ │  │  Kalite: 24 Bit / 48 kHz   Boyut: 2 GB  │   │  │
│     │  │  │    Bergen            │    Bergen         │ │  │  İndirme Sayısı: 2    Parça Sayısı: 12  │   │  │
│     │  │  ├──────────────────────┼──────────────────┤ │  │  Tür: Arabesk   Yıl: Bilinmeyen Yıl     │   │  │
│     │  │  │ 🎵 Bergen - Tam      │ 🎵 Bergen - Tam  │ │  │  Dinlenme Sayısı: 5    Süre: 00:30:00   │   │  │
│     │  │  │    Şarkılar         │    Şarkılar      │ │  │                                          │   │  │
│     │  │  │    Bergen            │    Bergen         │ │  │                                          │   │  │
│     │  │  └──────────────────────────────────────────┘ │  │                                          │   │  │
│     │  │                                                │  │                                          │   │  │
│     │  └────────────────────────────────────────────────┘  └──────────────────────────────────────────┘   │  │
│     │                                                                                                    │  │
│     └────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:510 ┌─── FOOTER PLAYER (h:90) ───────────────────────────────────────────────────────────────────────┐  │
│     │ 🎵 Şarkı Adı: Göksel - Sevil Neşelenen    [⏮] [▶] [⏹] [⏭]    🔊 ━━━━━━━━━━━━━━━━━━━━━━━━ % 100 │  │
│ y:600 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.albums-page` | Ana sayfa container |
| `.albums-page__header` | Başlık + geri butonu + arama |
| `.albums-page__filters` | Tür filtreleri (chip/scroll) |
| `.albums-page__grid` | Albüm kartları grid'i (4 sütun) |
| `.albums-page__detail` | Sağ panel: Albüm detay |
| `.album-card` | Albüm kartı (glass) |
| `.album-card__image` | Albüm kapak görseli (1:1) |
| `.album-card__title` | Albüm adı |
| `.album-card__artist` | Sanatçı adı |
| `.album-card__duration` | Süre |
| `.album-detail__cover` | Büyük kapak (300×300) |
| `.album-detail__title` | Albüm adı |
| `.album-detail__artist` | Sanatçı adı |
| `.album-detail__actions` | Hemen Çal, Karışık Çal |
| `.album-detail__meta` | Kalite, Boyut, Parça, Yıl |
| `.filter-chip` | Tür filtre butonu |
| `.filter-chip--active` | Aktif filtre |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-glass` | rgba(255,255,255,0.15) |
| `--cm-radius-md` | 12px |
| `--cm-radius-lg` | 16px |
| `--cm-spacing-md` | 16px |
| `--cm-grid-cols-albums` | 4 |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux 1024 - Albumler Page.png` | 1024×600 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
