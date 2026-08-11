---
title: CoreMusic — Albums Page Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Albumler Page.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/01-standard-60-40]]
  - [[B-home/dashboard]]
---

# CoreMusic — Albums Page Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Albumler Page.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 1: Standard 60/40 (Card Grid + Detail Panel)
**Rota:** `/albums`

---

## 1. PLATFORM

| Property | Value |
|----------|-------|
| Resolution | 1024×600px |
| Layout | Standard 60/40 |
| Header | Ortak (60px) |
| Footer | Ortak (90px) |
| İçerik | y: 60-510 (450px) |

---

## 2. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024×600 — Pattern 1: Standard 60/40 — /albums                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← Albümler / Tüm Albümler                                    [Sanatçı Adı Ara 🔍] [≡]      │
│  "Albümler"                                                                                      │
│  Kütüphanede depolanan tüm albümler                                                             │
│                                                                                                  │
│  ┌─ GENRE TABS (C11) ───────────────────────────────────────────────────────────────────────┐  │
│  │ [Tümü] [Pop] [Arabesk] [Dans] [Oyun Havası] [Damar] [Org] [Yabancı Pop] [Kpop/Kore]   │  │
│  │ ^(aktif)                                                                                │  │
│  │ ~13 sekme, yatay scroll, pembe arka plan (aktif)                                       │  │
│  │ Tab yüksekliği: ~32px (WCAG İHLALİ: 48px olmalı)                                       │  │
│  └──────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                  │
│  ┌── CARD GRID (sol ~614px, %60) ──────────┐  ┌── DETAIL PANEL (sağ ~390px, %40) ────────┐  │
│  │                                           │  │                                            │  │
│  │  3 sütun × 3 satır = 9 kart              │  │  ┌──────────────────┐                     │  │
│  │  Gap: 8px                                 │  │  │                  │                     │  │
│  │                                           │  │  │    300×300       │                     │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐   │  │  │   圆形 Album Art  │  Nobetci Eczane     │  │
│  │  │ 140×140 │ │ 140×140 │ │ 140×140 │   │  │  │    (daire, r:50%) │  Ferhat Kasetleri   │  │
│  │  │ album   │ │ album   │ │ album   │   │  │  │                  │  Kaset              │  │
│  │  │ thumb   │ │ thumb   │ │ thumb   │   │  │  └──────────────────┘                     │  │
│  │  │─────────│ │─────────│ │─────────│   │  │                                            │  │
│  │  │ Nobetci │ │ Bergen  │ │ Civaner│   │  │  ┌────────────────────────────────────┐    │  │
│  │  │ Eczane  │ │ -Tüm    │ │ -Tüm   │   │  │  │          Hemen Çal                 │    │  │
│  │  │ Ferhat  │ │ Şarkıla │ │ Şarkıl │   │  │  │          (pembe, full-width)        │    │  │
│  │  │ Kasetle │ │ rı      │ │ ar     │   │  │  └────────────────────────────────────┘    │  │
│  │  │ ri      │ │ Bergen  │ │Civaner │   │  │  ┌────────────────────────────────────┐    │  │
│  │  │ 00:10:05│ │ 00:10:05│ │00:10:05│   │  │  │          Karışık Çal               │    │  │
│  │  └─────────┘ └─────────┘ └─────────┘   │  │  │          (sınır, full-width)        │    │  │
│  │                                           │  │  └────────────────────────────────────┘    │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐   │  │  [...] (daha fazla butonu)                  │  │
│  │  │  ...    │ │  ...    │ │  ...    │   │  │                                            │  │
│  │  └─────────┘ └─────────┘ └─────────┘   │  │  ── Metadata ──                           │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐   │  │  Kalite: 24 Bit / 48 kHz                 │  │
│  │  │  ...    │ │  ...    │ │  ...    │   │  │  Boyut: 2 GB                             │  │
│  │  └─────────┘ └─────────┘ └─────────┘   │  │  İndirme Sayısı: 2                       │  │
│  │                                           │  │  Parça Sayısı: 12                       │  │
│  │  Her kart:                               │  │  Tür: Arabesk                            │  │
│  │    thumb: 140×140px, r:8px              │  │  Yıl: Bilinmeyen Yıl                     │  │
│  │    title: 12px, 600                     │  │  Dinlenme Sayısı: 5                      │  │
│  │    artist: 10px, 400, muted            │  │  Süre: 00:30:00                          │  │
│  │    süre: 10px, 400, accent             │  │                                            │  │
│  └───────────────────────────────────────────┘  └────────────────────────────────────────────┘  │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. ZONE DETAYLARI

### 3.1 — Başlık Alanı

| Özellik | Değer |
|---------|-------|
| Geri oku | ← (sol üst, 44×44px touch target) |
| Başlık | "Albümler / Tüm Albümler" (16px, 600) |
| Alt başlık | "Kütüphanede depolanan tüm albümler" (12px, 400, muted) |
| Arama | "Sanatçı Adı Ara 🔍" (input, sağ üst) |
| Sıralama | ≌ (sağ üst, dropdown) |

### 3.2 — Genre Tabs (C11)

| Özellik | Değer |
|---------|-------|
| Sayısı | ~13 sekme |
| Aktif | "Tümü" (pembe arka plan) |
| Scroll | Yatay, `overflow-x: auto` |
| Yükseklik | ~32px (WCAG İHLALİ: 48px olmalı) |
| Gap | 4px |
| Font | 11px, 500 |

### 3.3 — Card Grid (Sol %60)

| Özellik | Değer |
|---------|-------|
| Genişlik | ~614px |
| Sütun | 3 |
| Satır | 3 |
| Kart boyutu | ~190×220px (thumb 140×140 + text) |
| Gap | 8px |
| Padding | 12px |
| Scroll | Dikey, `overflow-y: auto` |

**Her kart (C09):**
```
┌─────────────────┐
│  ┌───────────┐  │
│  │  140×140  │  │  ← album art (kare, r:8px)
│  │  thumb    │  │
│  └───────────┘  │
│  Album Title     │  ← 12px, 600, max 2 satır
│  Artist Name     │  ← 10px, 400, muted
│  00:10:05        │  ← 10px, 400, accent
└─────────────────┘
```

### 3.4 — Detail Panel (Sağ %40)

| Özellik | Değer |
|---------|-------|
| Genişlik | ~390px |
| Art | 300×300px,圆形 (border-radius: 50%) |
| Başlık | Album adı (16px, 600) |
| Alt başlık | Sanatçı adı (12px, 400, muted) |
| Butonlar | Hemen Çal (C04), Karışık Çal (C05), [...] |
| Metadata | Kalite, Boyut, İndirme, Parça, Tür, Yıl, Dinlenme, Süre |
| Padding | 16px |
| Glass | `backdrop-filter: blur(8px)` |

---

## 4. BİLEŞEN KULLANIM MATRİSİ

| Bileşen | ID | Sayı | Konum |
|---------|-----|------|-------|
| Nav Link | C01 | 8 | Header |
| Status Widget | C02 | 1 | Header |
| User Pill | C03 | 1 | Header |
| Primary Button | C04 | 1 | Detail Panel |
| Secondary Button | C05 | 1 | Detail Panel |
| Genre Tabs | C11 | 1 | Üst kısım |
| Media Card | C09 | 9 | Card Grid |
| Detail Panel | C10 | 1 | Sağ panel |
| Footer Player | — | 1 | Footer |

---

## 5. ETKİLEŞİM

| Eylem | Sonuç |
|-------|-------|
| Kart tıklama | Detail paneli güncellenir |
| Genre sekme tıklama | Kart grid'i filtrelenir |
| "Hemen Çal" tıklama | Seçili albüm çalmaya başlar |
| "Karışık Çal" tıklama | Rastgele sırayla çalma |
| Geri ok tıklama | Ana sayfaya dönüş |
| Arama yazma | Kartlar filtrelenir |

---

## 6. WCAG DURUMU

| Kriter | Durum | Not |
|--------|-------|-----|
| Touch target (kart) | ✅ UYGUN | ~190×220px |
| Touch target (tab) | ❌ İHLAL | ~32px yükseklik |
| Touch target (buton) | ✅ UYGUN | 56px, 48px |
| Focus indicator | ✅ UYGUN | outline visible |
| Keyboard nav | ✅ UYGUN | Tab ile gezinme |
| ARIA labels | ⚠️ EKSİK | Kartlara `role="button"` ekle |
| Renk kontrastı | ✅ UYGUN | Beyaz/açık text |
| Scroll | ✅ UYGUN | Dikey ve yatay |

---

## 7. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | PNG master kataloğu |
| [[01-component-inventory]] | C09, C10, C11 detayları |
| [[_layout-patterns/01-standard-60-40]] | Layout pattern |
| [[C-music/album-detail]] | Albüm detay sayfası |
| [[C-music/artists]] | Sanatçılar sayfası |

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Layout | Standard 60/40 |
| Grid | 3×3 (9 kart max) |
| Components | 9 (C01-C05, C09-C11, C14) |
| WCAG Gaps | 2 (tab height, ARIA) |
| ADR Uyumlu | ✅ ADR-001, ADR-044 |

---

*Albums Page Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
