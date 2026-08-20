---
title: "CoreMusic — Music Screen Mockups"
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
  - "PNG #3 — Albümler"
  - "PNG #4 — Albüm Detayı"
  - "PNG #5 — Sanatçılar"
png_source: ".ai/.png/home-1024/"
---

# Music Screen Mockups

**3 PNG — home-1024/ dizininde.** Albümler, albüm detayı ve sanatçılar ekranları.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz.

---

## Music Ekran Envteri

| # | Ekran | PNG Dosyası | Rota | Layout Pattern | CSS Hedefi |
|---|-------|-------------|------|---------------|------------|
| 3 | **Albümler** | `Linux  1024 - Albumler Page.png` | `/albums` | Pattern 1: Standard 60/40 | `05_Pages/_home-*.css` |
| 4 | **Albüm Detayı** | `Linux  1024 - Albumler Details Detay Page.png` | `/album/:id` | Pattern 1: Standard 60/40 | `05_Pages/_home-*.css` |
| 5 | **Sanatçılar** | `Linux  1024 - Singer Page.png` | `/artists` | Pattern 1: Standard 60/40 | `05_Pages/_home-*.css` |

---

## ASCII Art — Music Ekranları

### ALBÜMLER PAGE — PNG #3

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak, y:0-60]                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                 │
│ x:16 y:71 ← (geri ok, pembe daire) Albümler / Tüm Albümler    [Sanatçı Adı Ara 🔍] [≡]       │
│ y:91 "Albümler"                                                                                │
│ y:105 Kütüphanede depolanan tüm albümler                                                       │
│                                                                                                 │
│ y:120 [Tümü][Pop][Arabesk][Dans][Oyun Havası][Damar][Org][Yabancı Pop][Kpop/Kore]...          │
│        ^^^ C11 Genre Tabs — ~13 sekme, h:32px (WCAG: 48px), gap:4px, yatay scroll             │
│        Aktif: pembe arka plan (#ff4fd8), border-radius: 20px (pill)                            │
│                                                                                                 │
│ y:160 ┌── CARD GRID (x:16-630, w:614px) ──┐  ┌── DETAIL PANEL (x:642-1008, w:366px) ───────┐│
│        │ ┌───────┐┌───────┐┌───────┐┌────┐  │  │          ┌──────────┐                        ││
│        │ │140×140││140×140││140×140││140×│  │  │          │ 圆形 300  │  Nobetci Eczane       ││
│        │ │ album ││ album ││ album ││140 │  │  │          │ ×300      │  Ferhat Kasetleri     ││
│        │ │ thumb ││ thumb ││ thumb ││    │  │  │          │ (r:50%)   │  Kaset                ││
│        │ │───────││───────││───────││────│  │  │          └──────────┘                        ││
│        │ │Nobetci││Bergen ││Civaner││Alb.│  │  │                                               ││
│        │ │Eczane ││-Tüm   ││-Tüm   ││... │  │  │  ┌──────────────────────────────────────┐   ││
│        │ │Ferhat ││Şarkıla││Şarkıl ││    │  │  │  │     Hemen Çal (C04, pembe, full-w)   │   ││
│        │ │Kasetle││rí     ││ar     ││    │  │  │  └──────────────────────────────────────┘   ││
│        │ │ri     ││00:10: ││00:10: ││    │  │  │  ┌──────────────────────────────────────┐   ││
│        │ │00:10: ││05     ││05     ││    │  │  │  │     Karışık Çal (C05, sınır, full-w) │   ││
│        │ │05     ││       ││       ││    │  │  │  └──────────────────────────────────────┘   ││
│        │ └───────┘└───────┘└───────┘└────┘  │  │  [...] (daha fazla)                          ││
│        │ 4 sütun × 3 satır = 12 kart        │  │                                               ││
│        │ gap: 8px                           │  │  Kalite: 24 Bit / 48 kHz                    ││
│        │                                    │  │  Boyut: 2 GB | İndirme: 2                    ││
│        │                                    │  │  Parça: 12 | Tür: Arabesk                    ││
│        │                                    │  │  Yıl: Bilinmeyen Yıl                        ││
│        │                                    │  │  Dinlenme Sayısı: 5                         ││
│ y:495  └────────────────────────────────────┘  │  Süre: 00:30:00                             ││
│                                                └────────────────────────────────────────────────┘│
│ [FOOTER — ortak, y:510-600]                                                                    │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

Layout: Standard 60/40 — Sol 614px (card grid) + Sağ 366px (detail panel)
Grid: 4 sütun, gap:8px, kart boyutu ~140×180px (thumb 140×140 + text altı)
Detail panel: 300×300 daire album art + butonlar + metadata
```

---

### ALBÜM DETAY — PNG #4

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 y:71 ← (geri ok) Album Detayları                          [Şarkı Ara 🔍]               │
│                                                                                                 │
│ Albüm : Göksel - Hayat Rüya Gibi    /    Şarkı Adı       Süre     Favori Yıldızı              │
│                                                                                                 │
│ y:120 ┌─────────────────────────────────────────────────────────────────────────────────────┐   │
│        │ [♪] Göksel - Sevil Neşelen                           00:05:00  ★★★★★             │   │
│        │ [♪] Göksel - Kabahat Senin                           00:00:00  ★★★★★             │   │
│        │ [♪] Göksel - Sevil Neşelen                           00:00:00  ★★★★★             │   │
│        │ [♪] Göksel - Sevil Neşelen                           00:00:00  ★★★★☆             │   │
│        │ [♪] Göksel - Sevil Neşelen ← PEMBE VURGU             00:00:00  ★★★★★             │   │
│        │ [♪] Göksel - Sevil Neşelen                           00:00:00  ★★★★★             │   │
│        │ [♪] Göksel - Sevil Neşelen                           00:00:00  ★★★★★             │   │
│ y:350  └─────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                 │
│ y:360 ┌─ Sol Alt (~70%) ───────────────────┐  ┌─ Sağ Panel (x:784-1008, ~30%) ────────────┐   │
│        │ [圆形 Album Art]                    │  │  ┌────────────┐                             │   │
│        │ Hayat Rüya Gibi                     │  │  │ 圆形 300×300│  Hayat Rüya Gibi          │   │
│        │ Göksel ★★★★★                        │  │  │ Album Art   │  Göksel                   │   │
│        │ 350 Kbps (gizli)                    │  │  └────────────┘                             │   │
│        │ 2024 · 12 Şarkı · 00:30:00 · Pop   │  │  [Hemen Çal] (C04, pembe)                  │   │
│        │                                     │  │  [Karışık Çal] (C05, sınır) [...]           │   │
│        │                                     │  │  Kalite: 24 Bit / 48 kHz                   │   │
│        │                                     │  │  Boyut: 3 GB | İndirme: 2                   │   │
│        │                                     │  │  Parça: 11 | Tür: Arabesk                   │   │
│        │                                     │  │  Yıl: Bilinmeyen | Dinlenme: 10             │   │
│ y:495  └─────────────────────────────────────┘  │  Süre: 00:30:00                             │   │
│                                                  └──────────────────────────────────────────────┘   │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

Layout: Standard 60/40 — Sol ~70% (track list + metadata) / Sağ ~30% (detail panel)
Aktif satır: pembe arka plan rgba(255,79,216,0.15)
Tablo başlığı: Albüm Adı, Şarkı Adı, Süre, Favori Yıldızı
```

---

### SANATÇILAR — PNG #5

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 y:71 ← (geri ok) Sanatçılar / Tüm Sanatçılar                [Sanatçı Adı Ara 🔍] [≡]    │
│ y:91 "Sanatçılar"                                                                              │
│ y:105 Kütüphanede depolanan tüm sanatçılar                                                     │
│                                                                                                 │
│ y:120 [Tümü][Pop][Arabesk][Dans][Oyun Havası][Damar][Org][Yabancı Pop][Kpop/Kore]...          │
│                                                                                                 │
│ y:160 ┌── CARD GRID (sol ~60%) ──────────────┐  ┌── DETAIL PANEL (sağ ~40%) ──────────────┐  │
│        │                                       │  │  ┌────────────┐                          │  │
│        │  Dairesel kartlar (border-radius: 50%)│  │  │ 圆形 300×300│  Sibel Can             │  │
│        │                                       │  │  │ Artist Photo│  Türkçe Pop             │  │
│        │  ┌────────┐ ┌────────┐ ┌────────┐   │  │  │ (r:50%)     │  1044 Şarkı             │  │
│        │  │ ○○○○○  │ │ ○○○○○  │ │ ○○○○○  │   │  │  └────────────┘                          │  │
│        │  │ Sibel  │ │Dilso'z │ │Ankara- │   │  │  ♫ 48  🎵 8  📅 1988                    │  │
│        │  │  Can   │ │        │ │lı Ayşe │   │  │  [bio metni — 3-4 satır açıklama]        │  │
│        │  │Türkçe  │ │Türkçe  │ │Oyun    │   │  │                                          │  │
│        │  │ Pop    │ │ Pop    │ │Havası  │   │  │  [Hemen Çal] (C04, pembe)                 │  │
│        │  │45 Şar. │ │48 Şar. │ │42 Şar. │   │  │  [Karışık Çal] (C05, sınır) [...]        │  │
│        │  └────────┘ └────────┘ └────────┘   │  │                                          │  │
│        │  ┌────────┐ ┌────────┐              │  │                                          │  │
│        │  │ ○○○○○  │ │ ○○○○○  │              │  │                                          │  │
│        │  │Ankara- │ │Bergen  │              │  │                                          │  │
│        │  │lı Ya-  │ │        │              │  │                                          │  │
│        │  │semin   │ │Arabesk │              │  │                                          │  │
│        │  └────────┘ └────────┘              │  │                                          │  │
│ y:495  └──────────────────────────────────────┘  └──────────────────────────────────────────┘  │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

FARK: Kartlar DAİRESEL (border-radius: 50%), Albums'de KARE
Layout: Standard 60/40
Detail panel: Artist photo (300×圆形) + bio + istatistikler (♫, 🎵, 📅 ikonları)
```

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | Ana indeks |
| [[01-auth-screens]] | Auth ekranları |
| [[02-home-screens]] | Home ekranları |
| [[04-player-screens]] | Player ekranları |
| [[05-filemanager-screens]] | FileManager ekranları |
| [[06-settings-screens]] | Settings ekranları |
| [[07-reference-tables]] | Referans tabloları |

---

*Music Screen Mockups v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
