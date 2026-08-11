---
title: CoreMusic — File List Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Göz At - Tıklama Clikced.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[E-filemanager/disk-browser]]
---

# CoreMusic — File List Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Göz At - Tıklama Clikced.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** 3 Sütun (değişmiş orta ve sağ panel)
**Rota:** `/browse` (disk seçildiğinde)

---

## 1. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← → Dosya Yöneticisi / Musics : Root    c:\users\Bayram Ali\Music    [Dosya Ara 🔍]         │
│                                                                                                  │
│  ┌─ SOL SIDEBAR (167px) ──────────────┐  ┌─ ORTA LİSTE (573px) ─────────────────────────┐   │
│  │ ● Tüm Şarkılar           1000      │  │ Şarkı Adı      | Albüm Adı | Sanatçı | Süre  │   │
│  │ ● Son Eklenenler          100       │  │ [♪] Pop Şarkıları Ali                        │   │
│  │ ● Son Dinlenenler          50       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:  │   │
│  │ ● Favoriler                20       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:  │   │
│  │ ● Oynatma Listeleri         5       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:  │   │
│  │ ● Sanatçılar              100       │  │                                             │   │
│  │ ● Albümler                 40       │  │                                             │   │
│  │ ● Türler                   50       │  │                                             │   │
│  │ ● Videolar                125       │  │                                             │   │
│  │ ● Podcast                  12       │  │                                             │   │
│  │                                    │  │                                             │   │
│  │ Core Tropu                       │  │                                             │   │
│  │ ▼ System Disk (C:)              │  │                                             │   │
│  │ ▼ USB Disk (E:) ← PEMBE SEÇİLİ  │  │                                             │   │
│  └────────────────────────────────────┘  └────────────────────────────────────────────┘   │
│                                                                                                  │
│  ┌─ SAĞ BİLGİ PANELİ (220px) ───────────────────────────────────────────────────────────┐   │
│  │ SSD Disk (E:)                                        [sil][düzenle][kopyala][yapış]   │   │
│  │ Sanat Güneştepe23                                                                │   │
│  │ ┌──────────┐                                                                           │   │
│  │ │Donut Chart│  65 GB — Kullanılan 49.5 GB, Boş 15.5 GB                                │   │
│  │ └──────────┘                                                                           │   │
│  │ [Göz At] (pembe)                                                                       │   │
│  │                                                                                         │   │
│  │ ── Disk Kullanım Bilgisi / Music ──                                                    │   │
│  │ ┌─ Bar Charts ──────────────────────┐                                                 │   │
│  │ │ [mavi bar] 1000 | [pembe bar]     │                                                 │   │
│  │ │ [mavi bar]  948 | [pembe bar]     │                                                 │   │
│  │ │ [mavi bar]  300 | [pembe bar]     │                                                 │   │
│  │ │ [mavi bar] 1200 | [pembe bar]     │                                                 │   │
│  │ └───────────────────────────────────┘                                                 │   │
│  │ ┌─ Genre Pie Chart ──────────────────┐                                                │   │
│  │ │ [pie chart — renkli dilimler]       │                                                │   │
│  │ │ Pop: 1000 | Arabesk: 600 | Dans:.. │                                                │   │
│  │ └───────────────────────────────────┘                                                 │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

Fark: Sol sidebar'da kategori listesi + sayılar
Sağ panel'de donut chart + bar charts + pie chart
```

---

## 2. DEĞİŞİKLİKLER (Disk Browser'a kıyasla)

| Özellik | Disk Browser | File List |
|---------|-------------|-----------|
| Sol sidebar | Disk listesi | Kategori listesi (Tüm Şarkılar, Son Eklenenler, vb.) |
| Orta liste | Boş | Dolu (şarkı listesi) |
| Sağ panel | Donut chart + butonlar | Donut chart + bar charts + pie chart + aksiyon butonları |
| üst bilgi | Dosya yolu | Dosya yolu + arama |

---

## 3. SAĞ PANEL GRAFİKLERİ

### 3.1 — Donut Chart
- Boyut: ~100×100px
- İçerik: Kullanılan/Boş disk alanı
- Renk: Mavi (kullanılan), Pembe (boş)

### 3.2 — Bar Charts
- 4-5 yatay bar
- Her biri: mavi + pembe segment
- Kategori adı + sayı

### 3.3 — Genre Pie Chart
- Çap: ~100px
- Dilimler: Renkli (Pop, Arabesk, Dans, vb.)
- Lejant: Alt kısım

---

## 4. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (sidebar) | ❌ ~21px → 48px |
| Touch target (buton) | ✅ 48px+ |
| Touch target (sil/düzenle) | ⚠️ ~20px → 44px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

---

*File List Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
