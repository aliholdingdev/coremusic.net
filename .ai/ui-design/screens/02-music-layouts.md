---
title: "CoreMusic — Music Layouts (§5-7)"
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

# CoreMusic — Music Layouts (§5-7)

> **§5 SANATÇILAR · §6 PLAYLIST · §7 VIDEO PLAYBACK**

---

## 5. SANATÇILAR — PNG LAYOUT

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 y:71 ← Sanatçılar / Tüm Sanatçılar                        [Sanatçı Adı Ara 🔍] [≡]    │
│ y:120 [Tümü][Pop][Arabesk][Dans][Oyun Havası][Damar][Org][Yabancı Pop][Kpop/Kore]...          │
│                                                                                                 │
│ y:160 ┌── CARD GRID (sol) ─────────────────┐  ┌── DETAIL PANEL (sağ) ─────────────────────┐  │
│        │ ┌───────┐┌───────┐┌───────┐       │  │  ┌────────────┐                             │  │
│        │ │ ○○○○○ ││ ○○○○○ ││ ○○○○○ │       │  │  │ 圆形 300×300│  Sibel Can               │  │
│        │ │ Sibel ││Dilso'z││Ankara-│       │  │  │ Artist Photo│  Türkçe Pop               │  │
│        │ │  Can  ││       ││lı Ayşe│       │  │  │ (r:50%)     │  1044 Şarkı               │  │
│        │ │Türkçe ││Türkçe ││Oyun   │       │  │  └────────────┘                             │  │
│        │ │ Pop   ││ Pop   ││Havası │       │  │  ♫ 48  🎵 8  📅 1988                       │  │
│        │ │45 Şar.││48 Şar.││42 Şar.│       │  │  [bio metni — 3-4 satır]                    │  │
│        │ └───────┘└───────┘└───────┘       │  │  [Hemen Çal] (pembe)                        │  │
│        │ ┌───────┐┌───────┐               │  │  [Karışık Çal] (sınır) [...]                │  │
│        │ │ ○○○○○ ││ ○○○○○ │               │  │                                              │  │
│        │ │Ankara-││Bergen │               │  │                                              │  │
│        │ │lı Ya- ││       │               │  │                                              │  │
│        │ │semin  ││Arabesk│               │  │                                              │  │
│        │ └───────┘└───────┘               │  │                                              │  │
│ y:495  └────────────────────────────────────┘  └──────────────────────────────────────────────┘  │
│                                                                                                 │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

FARK: Kartlar DAİRESEL (border-radius: 50%), Albümler'de KARE
```

---

## 6. PLAYLIST — PNG LAYOUT

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 y:71 ← Şimdi Oynatılıyor                                  [Şarkı Ara 🔍]               │
│                                                                                                 │
│ y:100 ┌── TABLO (x:16-680) ────────────────────────────────────────────────────────────────┐  │
│        │ / | Şarkı Adı         | Albüm Adı        | Sanatçı | Süre    | Favori Yıldızı    │  │
│        │───│───────────────────│──────────────────│─────────│─────────│──────────────────│  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★☆☆☆           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ PEMBE VURGU     │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│ y:350  └────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ y:100 ┌── SAĞ PANEL (x:700-1008) ─────────────────────────────────────────────────────────┐   │
│        │ [圆形 Artist Photo 100×100]                                                        │   │
│        │ Göksel - Sevil Neşelen                                                            │   │
│        │ Göksel, Hayat Rüya Gibi                                                           │   │
│        │ [♫][♥][▼][⋯]                                                                      │   │
│        │                                                                                    │   │
│        │ Önerilen Sanatçılar          Takip Edilen Sanatçılar                              │   │
│        │ [thumb×4]                    [thumb×4]                                             │   │
│        │                                                                                    │   │
│        │ Son Öneriler                 Tüm Sanatçılar                                       │   │
│        │ [thumb×4]                    [thumb×4]                                             │   │
│ y:495  └────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                 │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 7. VIDEO PLAYBACK — PNG LAYOUT

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ ← Göksel - Sevil Neşelen (x:16, y:16, geri ok 44×44px)                                       │
│                                                                                                 │
│ y:60 ┌── VİDEO (x:0-717, w:717px) ──────────┐  ┌── LİSTE (x:717-1024, w:307px) ──────────┐ │
│        │                                       │  │ Şarkı Adı                    Süre         │ │
│        │   [Tam kaplama video/image]            │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │   background-size: cover               │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │   background-position: center          │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │                                        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│ y:510  └────────────────────────────────────────┘  └──────────────────────────────────────────┘ │
│                                                                                                 │
│ y:430 ┌─ Mini Player (sol alt, ~300×100px) ─────────────────────────────────────────────────┐  │
│        │ [50×50] Göksel - Sevil Neşelen                                                    │  │
│        │           Hayat Rüya Gibi                                                          │  │
│        │           Göksel                                                                    │  │
│        │           00:00:00 / 00:05:00                                                      │  │
│        │           [seek bar — full-width, h:3px]                                           │  │
│ y:530  └────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ y:580 m3p3 ★★★★★                                                                              │
│                                                                                                 │
│ ARKA PLAN: Tam kaplama sanatçı fotoğrafı                                                      │
│ Header: YOK (sadece geri ok)                                                                  │
│ Footer: YOK (mini player ile değiştirildi)                                                   │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```
