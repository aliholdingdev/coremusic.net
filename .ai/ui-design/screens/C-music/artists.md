---
title: CoreMusic — Artists Page Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux  1024 - Singer Page.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/01-standard-60-40]]
  - [[C-music/albums]]
---

# CoreMusic — Artists Page Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux  1024 - Singer Page.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 1: Standard 60/40 (Dairesel Kartlar)
**Rota:** `/artists`

---

## 1. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← Sanatçılar / Tüm Sanatçılar                                  [Sanatçı Adı Ara 🔍] [≡]   │
│  "Sanatçılar"                                                                                    │
│  Kütüphanede depolanan tüm sanatçılar                                                          │
│                                                                                                  │
│  [Tümü] [Pop] [Arabesk] [Dans] [Oyun Havası] [Damar] [Org] [Yabancı Pop] [Kpop/Kore] ...    │
│                                                                                                  │
│  ┌── CARD GRID (sol ~614px, %60) ──────────┐  ┌── DETAIL PANEL (sağ ~390px, %40) ────────┐  │
│  │                                           │  │                                            │  │
│  │  Dairesel kartlar (border-radius: 50%)   │  │  ┌──────────────────┐                     │  │
│  │                                           │  │  │                  │                     │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐   │  │  │    圆形 300×300   │                     │  │
│  │  │  ○○○○○  │ │  ○○○○○  │ │  ○○○○○  │   │  │  │   Artist Photo   │  Sibel Can          │  │
│  │  │  Sibel  │ │ Dilso'z │ │ Ankara- │   │  │  │    (r:50%)        │  Türkçe Pop         │  │
│  │  │   Can   │ │         │ │  lı Ayşe│   │  │  │                  │  1044 Şarkı          │  │
│  │  │ Türkçe  │ │ Türkçe  │ │ Oyun    │   │  │  └──────────────────┘                     │  │
│  │  │  Pop    │ │  Pop    │ │ Havası  │   │  │                                            │  │
│  │  │45 Şar.  │ │48 Şar.  │ │42 Şar.  │   │  │  ♫ 48  🎵 8  📅 1988                    │  │
│  │  └─────────┘ └─────────┘ └─────────┘   │  │                                            │  │
│  │                                           │  │  [bio metni — uzun açıklama]             │  │
│  │  ┌─────────┐ ┌─────────┐                │  │  Sibel Can, Türk müziğinin en önemli      │  │
│  │  │  ○○○○○  │ │  ○○○○○  │                │  │  isimlerinden biridir. 1988'den bu yana... │  │
│  │  │ Ankara- │ │ Bergen  │                │  │                                            │  │
│  │  │  lı Ya- │ │         │                │  │  [Hemen Çal] (C04, pembe)                  │  │
│  │  │  semin  │ │ Arabesk │                │  │  [Karışık Çal] (C05, sınır)  [...]         │  │
│  │  │42 Şar.  │ │45 Şar.  │                │  │                                            │  │
│  │  └─────────┘ └─────────┘                │  └────────────────────────────────────────────┘  │
│  │                                           │                                                   │
│  │  Her kart: ~140×200px                    │                                                   │
│  │    thumb: 140×140px,圆形, r:50%          │                                                   │
│  │    name: 12px, 600                       │                                                   │
│  │    genre: 10px, 400, muted               │                                                   │
│  │    count: 10px, 400, accent              │                                                   │
│  └───────────────────────────────────────────┘                                                   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

FARK: Kartlar DAİRESEL (border-radius: 50%), Albümler'de KARE
```

---

## 2. DETAIL PANEL — Sanatçı Bilgileri

| Alan | Değer |
|------|-------|
| Fotoğraf | 300×300px, dairesel (border-radius: 50%) |
| İsim | 16px, 600 |
| Tür | 12px, 400, muted |
| Şarkı sayısı | 12px, 400 |
| İstatistikler | ♫ 48 (şarkı) 🎵 8 (album) 📅 1988 (yıl) |
| Bio | ~3-4 satır, 11px, 400, muted |
| Butonlar | Hemen Çal, Karışık Çal, [...] |

---

## 3. BİLEŞEN KULLANIMI

| Bileşen | ID | Sayı |
|---------|-----|------|
| Genre Tabs | C11 | 1 |
| Media Card (dairesel) | C09 | ~5-10 |
| Detail Panel | C10 | 1 |
| Primary Button | C04 | 1 |
| Secondary Button | C05 | 1 |

---

## 4. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (kart) | ✅ ~140×200px |
| Touch target (tab) | ❌ ~32px |
| Touch target (buton) | ✅ 56px, 48px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

---

## 5. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[C-music/albums]] | Albümler (aynı pattern) |
| [[01-component-inventory]] | C09, C10, C11 |
| [[_layout-patterns/01-standard-60-40]] | Layout pattern |

---

*Artists Page Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
