---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Album Detail Screen Specification"
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
  authority: ".ai/ui-design/screens/T08-embedded/album-detail.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Albumler Details Detay Page.png"
---

# CoreMusic — Album Detail (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─── HEADER (h:60) ────────────────────────────────────────────────────────────────────────────────┐  │
│     │ ← Geri   Albüm Detayları                              🔍 Şarkı Ara                               │  │
│ y:60 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:60 ┌─── CONTENT (h:450) ──────────────────────────────────────────────────────────────────────────────┐  │
│     │                                                                                                    │  │
│     │  ┌─── LEFT PANEL (60%, w:614) ──────────────────┐  ┌─── RIGHT PANEL (40%, w:410) ────────────┐   │  │
│     │  │ Albüm: Göksel : Hayat Rüya Gibi / Şarkı Adı │  │  🎵 Kapak (300×300)                      │   │  │
│     │  │                                               │  │  ┌────────────────────────────┐          │   │  │
│     │  │  🎵 Kapak (200×200)  │ Göksel - Sevil N.    │  │  │                            │          │   │  │
│     │  │  ┌──────────┐         │ ⏱ 00:05:59  ★★★★★  │  │  │                            │          │   │  │
│     │  │  │          │  Göksel - Kabahat Sensin      │  │  │                            │          │   │  │
│     │  │  │  Linet   │  ⏱ 00:00:00  ★★★★★           │  │  └────────────────────────────┘          │   │  │
│     │  │  │          │  Göksel - Sevil Neşelenen     │  │                                          │   │  │
│     │  │  └──────────┘  ⏱ 00:00:00  ★★★★★           │  │  Hayat Rüya Gibi                         │   │  │
│     │  │               Göksel - Sevil Neşelenen     │  │  Göksel                                  │   │  │
│     │  │  Hayat Rüya Gibi  ⏱ 00:00:00  ★★★★★       │  │                                          │   │  │
│     │  │  Göksel              Göksel - Sevil N.     │  │  [▶ Hemen Çal]  [Karışık Çal] [⋯]       │   │  │
│     │  │  ★★★★★               ⏱ 00:00:00  ★★★★★    │  │                                          │   │  │
│     │  │  350 Kbps · 🔊       Göksel - Sevil N.     │  │  Kalite: 24 Bit / 48 kHz   Boyut: 3 GB   │   │  │
│     │  │  2024 · 12 Şarkı     ⏱ 00:00:00  ★★★★★    │  │  İndirme Sayısı: 2    Parça Sayısı: 11   │   │  │
│     │  │  · 00:30:00 · Pop                           │  │  Tür: Arabesk   Yıl: Bilinmeyen Yıl      │   │  │
│     │  │                                              │  │  Dinlenme Sayısı: 10   Süre: 00:00:00    │   │  │
│     │  └──────────────────────────────────────────────┘  └──────────────────────────────────────────┘   │  │
│     │                                                                                                    │  │
│     └────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│ y:510 ┌─── FOOTER PLAYER (h:90) ───────────────────────────────────────────────────────────────────────┐  │
│ y:600 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.album-detail` | Ana sayfa container |
| `.album-detail__header` | Başlık + geri + arama |
| `.album-detail__tracklist` | Sol panel: Şarkı listesi |
| `.album-detail__sidebar` | Sağ panel: Albüm bilgisi |
| `.track-item` | Şarkı satırı |
| `.track-item__number` | Sıra numarası |
| `.track-item__title` | Şarkı adı |
| `.track-item__duration` | Süre |
| `.track-item__rating` | Yıldız puanı |
| `.track-item--active` | Çalınan şarkı (highlight) |
| `.album-sidebar__cover` | Büyük kapak (300×300) |
| `.album-sidebar__title` | Albüm adı |
| `.album-sidebar__artist` | Sanatçı |
| `.album-sidebar__actions` | Hemen Çal, Karışık Çal |
| `.album-sidebar__meta` | Kalite, Boyut, Parça, Yıl |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-active` | rgba(255,79,216,0.2) |
| `--cm-star-filled` | #ffd700 |
| `--cm-star-empty` | #666666 |
| `--cm-radius-md` | 12px |
| `--cm-spacing-sm` | 8px |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux 1024 - Albumler Details Detay Page.png` | 1024×600 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
