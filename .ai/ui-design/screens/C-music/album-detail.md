---
title: CoreMusic — Album Detail Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux  1024 - Albumler Details Detay Page.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/01-standard-60-40]]
  - [[C-music/albums]]
---

# CoreMusic — Album Detail Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux  1024 - Albumler Details Detay Page.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 1: Standard 60/40 (Track List + Detail Panel)
**Rota:** `/album/:id`

---

## 1. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← Album Detayları                                              [Şarkı Ara 🔍]              │
│                                                                                                  │
│  Albüm : Göksel - Hayat Rüya Gibi    /    Şarkı Adı              Süre    Favori Yıldızı       │
│  ┌──────────────────────────────────────────────────────────────────────────────────────────┐   │
│  │ [♪] Göksel - Sevil Neşelen                                    00:05:00  ★★★★★          │   │
│  │ [♪] Göksel - Kabahat Senin                                    00:00:00  ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen                                    00:00:00  ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen                                    00:00:00  ★★★★☆          │   │
│  │ [♪] Göksel - Sevil Neşelen  ← PEMBE vurgu (aktif satır)       00:00:00  ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen                                    00:00:00  ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen                                    00:00:00  ★★★★★          │   │
│  └──────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│  ┌─ Sol Panel (~70%) ──────────────────────┐  ┌─ Sağ Panel (~30%) ────────────────────────┐   │
│  │                                          │  │  ┌────────────┐                            │   │
│  │  C13 Track list                          │  │  │  圆形 300×300│  Hayat Rüya Gibi         │   │
│  │  Satır: thumb(20×20) + title + duration  │  │  │ Album Art   │  Göksel                  │   │
│  │        + stars (C12)                     │  │  └────────────┘                            │   │
│  │                                          │  │                                            │   │
│  │  Başlık: "Şarkı Adı" | "Süre" | "Favori"│  │  [Hemen Çal] (C04, pembe)                  │   │
│  │  Tablo başlığı: sabit, sıralanabilir     │  │  [Karışık Çal] (C05, sınır)               │   │
│  │                                          │  │  [...]                                     │   │
│  │  Aktif satır: pembe arka plan            │  │                                            │   │
│  │  background: rgba(255,79,216,0.15)       │  │  ── Metadata ──                           │   │
│  │  border-left: 3px solid #ff4fd8          │  │  Kalite: 24 Bit / 48 kHz                 │   │
│  │                                          │  │  Boyut: 3 GB                             │   │
│  └──────────────────────────────────────────┘  │  İndirme: 2 | Parça: 11                  │   │
│                                                  │  Tür: Arabesk | Yıl: Bilinmeyen         │   │
│  ┌─ Sol Alt (album metadata) ──────────────┐  │  Dinlenme: 10 | Süre: 00:30:00           │   │
│  │ [圆形 Album Art, küçük]                  │  │                                            │   │
│  │ Hayat Rüya Gibi                          │  └────────────────────────────────────────────┘   │
│  │ Göksel ★★★★★                             │                                                   │
│  │ 350 Kbps (gizli)                         │                                                   │
│  │ 2024 · 12 Şarkı · 00:30:00 · Pop        │                                                   │
│  └──────────────────────────────────────────┘                                                   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. TRACK LIST DETAY (C13)

### 2.1 — Tablo Yapısı

| Sütun | Genişlik | İçerik |
|-------|----------|--------|
| # | ~30px | Sıra numarası veya ♪ ikonu |
| Şarkı Adı | ~%50 | Şarkı başlığı |
| Albüm Adı | ~%20 | Albüm adı (opsiyonel) |
| Sanatçı | ~%15 | Sanatçı adı |
| Süre | ~60px | 00:00:00 formatında |
| Favori Yıldızı | ~100px | 5 yıldız (C12) |

### 2.2 — Satır Yüksekliği

| Özellik | Değer | Token |
|---------|-------|-------|
| Satır yüksekliği | ~40px | `--row-h` (48px olmalı) |
| Padding | 8px 12px | `--space-2` `--space-3` |
| Aktif bg | `rgba(255,79,216,0.15)` | — |
| Aktif border-left | 3px solid `var(--theme-primary)` | — |
| Thumb | 20×20px | — |
| Title | 12px, 500 | `--text-sm` `--font-medium` |
| Duration | 10px, 400 | `--text-xs` |
| Stars | 20×20px per star | `--star-size` |

### 2.3 — Aktif Satır

```
┌═════════════════════════════════════════════════════════════════════════════════════════════┐
│ [♪] Göksel - Sevil Neşelen  ← PEMBE VURGU        00:00:00  ★★★★★                       │
│  background: rgba(255,79,216,0.15)                                                          │
│  border-left: 3px solid var(--theme-primary)                                                │
│  text color: var(--theme-primary) (başlık için)                                             │
└═════════════════════════════════════════════════════════════════════════════════════════════┘
```

---

## 3. STAR RATING (C12)

| Özellik | Değer |
|---------|-------|
| Yıldız boyutu | 20×20px (WCAG: 48px olmalı) |
| Gap | 2px |
| Dolu renk | `#FFD700` (altın) |
| Boş renk | `rgba(255,255,255,0.3)` |
| Toplam genişlik | ~106px |
| Etkileşim | Tek yıldız tıklama veya tam satır |

**Öneri:** Tam satırı tıklanabilir yap (tüm 5 yıldız tek hit area → 48px yükseklik)

---

## 4. DETAIL PANEL (Sağ Panel)

| Özellik | Değer |
|---------|-------|
| Genişlik | ~30% (~300px) |
| Art boyutu | 300×300px,圆形 |
| Başlık | 16px, 600 |
| Alt başlık | 12px, 400, muted |
| Butonlar | Hemen Çal (C04), Karışık Çal (C05), [...] |
| Metadata | 11px, 400, muted |

---

## 5. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (satır) | ❌ ~40px → 48px |
| Touch target (yıldız) | ❌ ~20px → 48px |
| Touch target (buton) | ✅ 56px, 48px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

---

## 6. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[C-music/albums]] | Albümler listesi |
| [[01-component-inventory]] | C12, C13 detayları |
| [[_layout-patterns/01-standard-60-40]] | Layout pattern |

---

*Album Detail Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
