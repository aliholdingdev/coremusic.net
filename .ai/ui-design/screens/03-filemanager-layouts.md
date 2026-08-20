---
title: "CoreMusic — File Manager Layouts (§8-9)"
type: reference
date: 2026-08-11
updated: 2026-08-17
status: active
version: 3.0.0
authority: PNG Visual Analysis (direct pixel inspection — all 18 PNGs)
reference:
  authority: ".ai/ui-design/screens/00-ascii-art-index.md"
  source_of_truth: ".ai/ui-design/screens/"
---

# CoreMusic — File Manager Layouts (§8-9)

> **§8 GÖZ AT (3 Sütun) · §9 GÖZ AT TIKLAMA**

---

## 8. GÖZ AT (3 Sütun) — PNG LAYOUT

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 y:71 ← Dosya Yöneticisi / Disk                              [c:\users\...\Music] 🔍    │
│                                                                                                 │
│ y:100 ┌─ SOL (x:16-183, w:167px) ──┐  ┌─ ORTA (x:186-759, w:573px) ──────────────────────┐  │
│        │ Sistem Diskleri             │  │ Şarkı Adı      | Albüm Adı | Sanatçı | Süre       │  │
│        │  ● System Disk              │  │ [♪] Pop Şarkıları Ali                             │  │
│        │  ● NAS Drive                │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:00:00  │  │
│        │                             │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:00:00  │  │
│        │ Harici / Taşınabilir        │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:00:00  │  │
│        │  ● HDD Drive                │  │                                                   │  │
│        │  ● SSD Nvme 2 Drive         │  │                                                   │  │
│        │  ● SSD Drive                │  │                                                   │  │
│        │                             │  │                                                   │  │
│        │ Çıkarılabilir               │  │                                                   │  │
│        │  ● USB Drive                │  │                                                   │  │
│        │  ● USB Drive                │  │                                                   │  │
│        │  ● CD DVD Drive             │  │                                                   │  │
│ y:495  └─────────────────────────────┘  └───────────────────────────────────────────────────┘  │
│                                                                                                 │
│ y:100 ┌─ SAĞ (x:784-1008, w:224px) ────────────────────────────────────────────────────────┐  │
│        │ System Disk                                                                        │  │
│        │ Hard Disk · Dahili Disk                                                            │  │
│        │ ┌──────────┐                                                                       │  │
│        │ │Donut Chart│ 32 GB — 16 GB Kullanılabilir %50                                     │  │
│        │ └──────────┘                                                                       │  │
│        │ [Göz At] (pembe)                                                                   │  │
│        │ [Bütün Şarkıları Çal]                                                             │  │
│        │ [Şarkıları Göz At]                                                                │  │
│        │ [Şarkılarını Göz At]                                                              │  │
│        │ [Videoları Göz At]                                                                │  │
│ y:495  └────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 9. GÖZ AT TIKLAMA — PNG LAYOUT

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 ← → Dosya Yöneticisi / Musics : Root  c:\users\Bayram Ali\Music  [Dosya Ara 🔍]         │
│                                                                                                 │
│ y:100 ┌─ SOL (167px) ───────────────┐  ┌─ ORTA (573px) ───────────────────────────────────┐  │
│        │ ● Tüm Şarkılar      1000    │  │ Şarkı Adı      | Albüm Adı | Sanatçı | Süre     │  │
│        │ ● Son Eklenenler     100     │  │ [♪] Pop Şarkıları Ali                           │  │
│        │ ● Son Dinlenenler     50     │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:00   │  │
│        │ ● Favoriler           20     │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:00   │  │
│        │ ● Oynatma List.        5     │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:00   │  │
│        │ ● Sanatçılar         100     │  │                                                 │  │
│        │ ● Albümler            40     │  │                                                 │  │
│        │ ● Türler              50     │  │                                                 │  │
│        │ ● Videolar           125     │  │                                                 │  │
│        │ ● Podcast             12     │  │                                                 │  │
│        │                           │  │                                                 │  │
│        │ Core Tropu               │  │                                                 │  │
│        │ ▼ System Disk (C:)      │  │                                                 │  │
│        │ ▼ USB Disk (E:) ← PEMBE │  │                                                 │  │
│ y:495  └───────────────────────────┘  └───────────────────────────────────────────────────┘  │
│                                                                                                 │
│ y:100 ┌─ SAĞ (224px) ──────────────────────────────────────────────────────────────────────┐  │
│        │ SSD Disk (E:)                                   [sil][düzenle][kopyala][yapış]    │  │
│        │ Sanat Güneştepe23                                                               │  │
│        │ ┌──────────┐                                                                       │  │
│        │ │Donut Chart│ 65 GB — Kullanılan 49.5 GB, Boş 15.5 GB                             │  │
│        │ └──────────┘                                                                       │  │
│        │ [Göz At] (pembe)                                                                   │  │
│        │ ── Disk Kullanım Bilgisi ──                                                        │  │
│        │ ┌─ Bar Charts ──┐  ┌─ Genre Pie ──┐                                               │  │
│        │ │ [mavi][pembe]  │  │ [pie chart]   │                                               │  │
│        │ │ [mavi][pembe]  │  │ Pop:1000      │                                               │  │
│        │ │ [mavi][pembe]  │  │ Arabesk:600   │                                               │  │
│        │ └────────────────┘  └───────────────┘                                               │  │
│ y:495  └────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```
