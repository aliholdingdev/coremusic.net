---
title: "CoreMusic — FileManager Screen Mockups"
type: reference
category: ui-design/mockups
date: 2026-08-19
updated: 2026-08-19
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
parent: "[[00-mockup-index]]"
screens:
  - "PNG #8 — Göz At (Disk)"
  - "PNG #9 — Göz At (Tıklama)"
png_source: ".ai/.png/home-1024/"
---

# FileManager Screen Mockups

**2 PNG — home-1024/ dizininde.** Dosya yöneticisi ve disk tarama ekranları.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz.

---

## FileManager Ekran Envteri

| # | Ekran | PNG Dosyası | Rota | Layout Pattern | CSS Hedefi |
|---|-------|-------------|------|---------------|------------|
| 8 | **Göz At (Disk)** | `Linux  1024 - Göz At Page.png` | `/browse` | Pattern 1: 3 Sütun (167+573+220) | `05_Pages/_home-*.css` |
| 9 | **Göz At (Tıklama)** | `Linux  1024 - Göz At - Tıklama Clikced.png` | `/browse` | Pattern 1: 3 Sütun | `05_Pages/_home-*.css` |

---

## ASCII Art — FileManager Ekranları

### GÖZ AT (Disk) — PNG #8

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 y:71 ← (geri ok) Dosya Yöneticisi / Disk                    [c:\users\...\Music] 🔍    │
│                                                                                                 │
│ y:100 ┌─ SOL ALAN (x:16-240, ~224px) ───────┐  ┌─ SAĞ BİLGİ (x:784-1008, w:224px) ────────┐  │
│        │ Sistem Diskleri                       │  │ System Disk                                │  │
│        │  ● System Disk (pink progress bar)    │  │ Hard Disk · Dahili Disk                    │  │
│        │  ● NAS Drive (pink progress bar)      │  │                                            │  │
│        │                                       │  │ ┌──────────┐                               │  │
│        │ Harici / Taşınabilir Diskler          │  │ │Donut Chart│ 32 GB                       │  │
│        │  ● HDD Drive (pink progress bar)      │  │ │ (glass)   │ 16 GB Kullanılabilir %50    │  │
│        │  ● SSD Nvme 2 Drive (pink bar)        │  │ └──────────┘                               │  │
│        │  ● SSD Drive (pink bar)               │  │                                            │  │
│        │                                       │  │ [Göz At] (C04, pembe, full-width)          │  │
│        │ Çıkarılabilir Diskler                 │  │ [Bütün Şarkıları Çal] (C05, sınır)        │  │
│        │  ● USB Drive (pink bar)               │  │ [Şarkıları Göz At] (C05, sınır)           │  │
│        │  ● USB Drive (pink bar)               │  │ [Şarkılarını Göz At] (C05, sınır)         │  │
│        │  ● CD DVD Drive (pink bar)            │  │ [Videoları Göz At] (C05, sınır)           │  │
│ y:495  └───────────────────────────────────────┘  └────────────────────────────────────────────┘  │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

Layout: 2 sütun — Sol 224px (disk listesi) + Sağ 224px (bilgi paneli)
Orta alan YOK (sadece sol ve sağ)
Her disk: pembe progress bar (kullanım oranı)
Detail panel: Donut chart (glass), aksiyon butonları
Sidebar: SADECE BU SAYFAYA ÖZEL (global değil)
```

---

### GÖZ AT TIKLAMA — PNG #9

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 ← → (geri/ileri okları, pembe daire) Dosya Yöneticisi / Musics : Root                    │
│        c:\users\Bayram Ali\Music                         [Dosya Ara 🔍]                       │
│                                                                                                 │
│ y:100 ┌─ SOL SIDEBAR (x:16-183, w:167px) ──┐  ┌─ ORTA LİSTE (x:186-759, w:573px) ────────┐  │
│        │ ● Tüm Şarkılar        1000         │  │ Şarkı Adı      | Albüm Adı | Sanatçı | Süre│  │
│        │ ● Son Eklenenler        100         │  │ [♪] Pop Şarkıları Ali                     │  │
│        │ ● Son Dinlenenler        50         │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | ...│  │
│        │ ● Favoriler              20         │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | ...│  │
│        │ ● Oynatma List.           5         │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | ...│  │
│        │ ● Sanatçılar            100         │  │                                             │  │
│        │ ● Albümler               40         │  │                                             │  │
│        │ ● Türler                 50         │  │                                             │  │
│        │ ● Videolar              125         │  │                                             │  │
│        │ ● Podcast                12         │  │                                             │  │
│        │                                   │  │                                             │  │
│        │ Core Tropu                         │  │                                             │  │
│        │ ▼ System Disk (C:)                 │  │                                             │  │
│        │ ▼ USB Disk (E:) ← PEMBE SEÇİLİ     │  │                                             │  │
│ y:495  └─────────────────────────────────────┘  └───────────────────────────────────────────┘  │
│                                                                                                 │
│ y:100 ┌─ SAĞ BİLGİ (x:784-1008, w:224px) ────────────────────────────────────────────────┐   │
│        │ SSD Disk (E:)                                   [sil][düzenle][kopyala][yapış]    │   │
│        │ Sanat Güneştepe23                                                               │   │
│        │ ┌──────────┐                                                                       │   │
│        │ │Donut Chart│ 65 GB — Kullanılan 49.5 GB, Boş 15.5 GB                             │   │
│        │ └──────────┘                                                                       │   │
│        │ [Göz At] (C04, pembe)                                                              │   │
│        │ ── Disk Kullanım Bilgisi ──                                                        │   │
│        │ ┌─ Bar Charts ──┐  ┌─ Genre Pie ──┐                                               │   │
│        │ │ [mavi][pembe]  │  │ [pie chart]   │                                               │   │
│        │ │ [mavi][pembe]  │  │ Pop:1000      │                                               │   │
│        │ │ [mavi][pembe]  │  │ Arabesk:600   │                                               │   │
│        │ └────────────────┘  └───────────────┘                                               │   │
│ y:495  └────────────────────────────────────────────────────────────────────────────────────┘   │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

Layout: 3 Sütun — Sol 167px (disk listesi) + Orta 573px (dosya listesi) + Sağ 224px (bilgi)
Sidebar: SADECE BU SAYFAYA ÖZEL (global değil)
Sol sidebar: Kategori listesi + sayılar + ağack yapısı
Orta: Şarkı listesi tablosu
Sağ: Donut chart + bar charts + pie chart + aksiyon butonları (glass panels)
```

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | Ana indeks |
| [[01-auth-screens]] | Auth ekranları |
| [[02-home-screens]] | Home ekranları |
| [[03-music-screens]] | Music ekranları |
| [[04-player-screens]] | Player ekranları |
| [[06-settings-screens]] | Settings ekranları |
| [[07-reference-tables]] | Referans tabloları |

---

*FileManager Screen Mockups v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
