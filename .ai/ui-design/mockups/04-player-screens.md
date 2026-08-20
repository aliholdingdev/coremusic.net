---
title: "CoreMusic — Player Screen Mockups"
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
  - "PNG #6 — Playlist"
  - "PNG #7 — Video Playback (Fullscreen)"
png_source: ".ai/.png/home-1024/"
---

# Player Screen Mockups

**2 PNG — home-1024/ dizininde.** Playlist ve video oynatma ekranları.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz.

---

## Player Ekran Envteri

| # | Ekran | PNG Dosyası | Rota | Layout Pattern | CSS Hedefi |
|---|-------|-------------|------|---------------|------------|
| 6 | **Playlist** | `Linux  1024 - Playlist Page.png` | `/playlist/:id` | Pattern 1: Standard 60/40 | `05_Pages/_home-*.css` |
| 7 | **Video Playback** | `Linux  1024 - Playlist Page - Video Played.png` | `/playlist/:id` (video) | Pattern 3: Fullscreen | `05_Pages/_home-*.css` |

---

## ASCII Art — Player Ekranları

### PLAYLIST — PNG #6

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER]                                                                                        │
├─────────────────────────────────────────────────────────────────────────────────────────────────┤
│ x:16 y:71 ← (geri ok) Şimdi Oynatılıyor                        [Şarkı Ara 🔍]               │
│                                                                                                 │
│ y:100 ┌── TABLO (x:16-680, ~65%) ──────────────────────────────────────────────────────────┐  │
│        │ / | Şarkı Adı         | Albüm Adı        | Sanatçı | Süre    | Favori Yıldızı    │  │
│        │───│───────────────────│──────────────────│─────────│─────────│──────────────────│  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★☆☆☆           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ PEMBE VURGU     │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│        │[♪]│Göksel-Sevil Neş.  │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★           │  │
│ y:495  └────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ y:100 ┌── SAĞ PANEL (x:700-1008, ~300px) ────────────────────────────────────────────────┐    │
│        │ [圆形 Artist Photo 100×100]                                                        │    │
│        │ Göksel - Sevil Neşelen                                                            │    │
│        │ Göksel, Hayat Rüya Gibi                                                           │    │
│        │                                                                                    │    │
│        │ [♫][♥][▼][⋯]  (aksiyon ikonları, 44×44px hit area)                               │    │
│        │                                                                                    │    │
│        │ Önerilen Sanatçılar          Takip Edilen Sanatçılar                              │    │
│        │ [thumb×4 grid]               [thumb×4 grid]                                        │    │
│        │                                                                                    │    │
│        │ Son Öneriler                 Tüm Sanatçılar                                       │    │
│        │ [thumb×4 grid]               [thumb×4 grid]                                        │    │
│ y:495  └────────────────────────────────────────────────────────────────────────────────────┘   │
│ [FOOTER]                                                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

Layout: Standard 60/40 — Sol ~65% (tablo) / Sağ ~300px (detail panel)
Aktif satır: pembe arka plan
Tablo başlığı: sabit üstte, sıralanabilir
```

---

### VIDEO PLAYBACK (Fullscreen) — PNG #7

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ ← Göksel - Sevil Neşelen (x:16, y:16, geri ok pembe daire 44×44px)                           │
│ Header: YOK (sadece geri ok)                                                                   │
│                                                                                                 │
│ y:60 ┌── VİDEO (x:0-717, w:717px) ──────────┐  ┌── LİSTE (x:717-1024, w:307px) ──────────┐ │
│        │                                       │  │ Şarkı Adı                    Süre         │ │
│        │   [Tam kaplama video/image]            │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │   background-size: cover               │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │   background-position: center          │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
│        │   Sanatçı fotoğrafı tam kaplama        │  │ [thumb] Göksel - Sevil Neş.  00:00:00    │ │
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
│        │ [50×50 thumb] Göksel - Sevil Neşelen                                              │  │
│        │                Hayat Rüya Gibi                                                     │  │
│        │                Göksel                                                               │  │
│        │                00:00:00 / 00:05:00                                                 │  │
│        │                [seek bar — full-width, h:3px]                                      │  │
│ y:530  └────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ y:580 m3p3 ★★★★★                                                                              │
│ Footer: YOK (mini player ile değiştirildi)                                                     │
│ ARKA PLAN: Tam kaplama sanatçı fotoğrafı                                                      │
│ Sağ panel: Yarı saydam, glass efekti                                                          │
│ Mini Player: Glass panel, backdrop-filter: blur(20px)                                         │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | Ana indeks |
| [[01-auth-screens]] | Auth ekranları |
| [[02-home-screens]] | Home ekranları |
| [[03-music-screens]] | Music ekranları |
| [[05-filemanager-screens]] | FileManager ekranları |
| [[06-settings-screens]] | Settings ekranları |
| [[07-reference-tables]] | Referans tabloları |

---

*Player Screen Mockups v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
