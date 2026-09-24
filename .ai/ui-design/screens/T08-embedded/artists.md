---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Artists Page Screen Specification"
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
  authority: ".ai/ui-design/screens/T08-embedded/artists.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Singer Page.png"
---

# CoreMusic — Artists Page (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─── HEADER (h:60) ────────────────────────────────────────────────────────────────────────────────┐  │
│     │ ← Geri   Sanatçılar / Tüm Sanatçılar                   🔍 Sanatçı Adı Ara                        │  │
│ y:60 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:60 ┌─── CONTENT (h:450) ──────────────────────────────────────────────────────────────────────────────┐  │
│     │                                                                                                    │  │
│     │  ┌─── LEFT PANEL (60%, w:614) ──────────────────┐  ┌─── RIGHT PANEL (40%, w:410) ────────────┐   │  │
│     │  │ Sanatçılar                                     │  │  🎤 Sanatçı Foto (120×120, circle)      │   │  │
│     │  │ Kütüphanede depolananharici Sanatçılar │  │  ┌────────────────────────┐              │   │  │
│     │  │                                                │  │  │                        │              │   │  │
│     │  │  [Tümü] [Pop] [Arabesk] [Dans] [Oyun Havaası]│  │  └────────────────────────┘              │   │  │
│     │  │  [Damard] [Org] [Yabancı Pop] [Kpop-Kore]    │  │                                          │   │  │
│     │  │                                                │  │  Sibel Can                                │   │  │
│     │  │  ┌─── Artist Grid (5×2) ─────────────────────┐│  │  Türkçe Pop                               │   │  │
│     │  │  │ 🎤 Sibel Can  │ 🎤 Dişo'z  │ 🎤 Ankaralı││  │  144 Şarkı                                │   │  │
│     │  │  │  (circle)     │  (circle)  │  Ayşe (cir) ││  │  48 Albüm   8 Sanatçı  1988               │   │  │
│     │  │  ├──────────────┼────────────┼─────────────┤│  │                                          │   │  │
│     │  │  │ 🎤 Ankaralı   │ 🎤 Bergen  │            ││  │  [▶ Hemen Çal]  [Karışık Çal] [⋯]       │   │  │
│     │  │  │  Yasemin      │            │            ││  │                                          │   │  │
│     │  │  │  (circle)     │  (circle)  │            ││  │  "Sibel Can, Türkiye'nin en çok…         │   │  │
│     │  │  └──────────────────────────────────────────┘│  │   satellite ile	inline	yapıldı…"         │   │  │
│     │  │                                                │  │                                          │   │  │
│     │  └────────────────────────────────────────────────┘  └──────────────────────────────────────────┘   │  │
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
| `.artists-page` | Ana sayfa container |
| `.artists-page__filters` | Tür filtreleri (chip/scroll) |
| `.artists-page__grid` | Sanatçı kartları grid'i (5×2) |
| `.artists-page__detail` | Sağ panel: Sanatçı detay |
| `.artist-card` | Sanatçı kartı (glass) |
| `.artist-card__image` | Dairesel fotoğraf (100×100, circle) |
| `.artist-card__name` | Sanatçı adı |
| `.artist-card__genre` | Tür bilgisi |
| `.artist-card__count` | Şarkı sayısı |
| `.artist-detail__photo` | Büyük fotoğraf (120×120, circle) |
| `.artist-detail__name` | Sanatçı adı |
| `.artist-detail__genre` | Tür |
| `.artist-detail__stats` | Albüm, Sanatçı, Yıl istatistikleri |
| `.artist-detail__actions` | Hemen Çal, Karışık Çal |
| `.artist-detail__bio` | Biyografi |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-glass` | rgba(255,255,255,0.15) |
| `--cm-radius-full` | 50% (circle) |
| `--cm-radius-md` | 12px |
| `--cm-spacing-md` | 16px |
| `--cm-grid-cols-artists` | 5 |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux 1024 - Singer Page.png` | 1024×600 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
