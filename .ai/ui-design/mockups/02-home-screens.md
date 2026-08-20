---
title: "CoreMusic — Home Screen Mockups"
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
  - "PNG #1 — Ana Sayfa"
  - "PNG #2 — Hoş Geldin Modalı"
png_source: ".ai/.png/home-1024/"
---

# Home Screen Mockups

**2 PNG — home-1024/ dizininde.** Ana sayfa ve hoş geldin modalı.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz.

---

## Home Ekran Envteri

| # | Ekran | PNG Dosyası | Rota | Layout Pattern | CSS Hedefi |
|---|-------|-------------|------|---------------|------------|
| 1 | **Ana Sayfa** | `Linux  1024 - Home Page.png` | `/` | Pattern 2: Split Home (42/58) | `05_Pages/_home-*.css` |
| 2 | **Hoş Geldin Modalı** | `Linux  1024 - Home Page Welcome Popup.png` | `/` (ilk giriş) | Pattern 4: Modal (600×308) | `05_Pages/_home-*.css` |

---

## ASCII Art — Home Ekranları

### HOME PAGE (Ana Sayfa) — PNG #1

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                     x:1024│
│ y:0 ┌─────────────────────────────────────────────────────────────────────────────────────────┐ │
│     │"Core Music"  Ana Sayfa  Keşfet  Albümler  Sanatçılar  Göz At  Geçmiş  Ayarlar  Hakk..│ │
│     │ y:15    (Bickham)  (nav-link × 8, gap:2-4px, Arima 10px)           [Bayram Ali ▾]    │ │
│     │                                                         [📶✳ pill 65×37] [🔋 pill 100]│ │
│     │ y:35                                                              [⚙] [⏻]             │ │
│ y:60├─────────────────────────────────────────────────────────────────────────────────────────┤ │
│     │                                                                                         │ │
│     │ x:16                                                                                    │ │
│     │ y:71 ┌──────────────────────────────────────┐   ┌──────────────────────────────────────┐│ │
│     │       │  ┌────────┐ Göksel - Sevil Neşelen  │   │ 🎵 Hoparlörler                      ││ │
│     │       │  │100×100 │ Hayat Rüya Gibi          │   │    Core Music - Hoparlör             ││ │
│     │       │  │album   │ Göksel                   │   │    ┌──────────────────────────────┐  ││ │
│     │       │  │art     │                          │   │    │ glass panel ~250×100          │  ││ │
│     │       │  └────────┘ 00:05:00 ═══════ 00:05:00│   │    │ (backdrop-filter: blur(8px))  │  ││ │
│     │       │              ▲ pembe seek bar h:3px   │   │    └──────────────────────────────┘  ││ │
│     │       │                                     │   │                                      ││ │
│     │       │  "En Son Dinlenen" başlığı          │   │ ☁ Hava Durumu                        ││ │
│     │       │  ┌──────┐┌──────┐┌──────┐┌──────┐  │   │    İzmir, TR                         ││ │
│     │       │  │140×  ││140×  ││140×  ││140×  │  │   │    ┌──────────────────────────────┐  ││ │
│     │       │  │140   ││140   ││140   ││140   │  │   │    │ glass panel ~250×100          │  ││ │
│     │       │  │C09   ││C09   ││C09   ││C09   │  │   │    └──────────────────────────────┘  ││ │
│     │       │  │kart  ││kart  ││kart  ││kart  │  │   │                                      ││ │
│     │       │  └──────┘└──────┘└──────┘└──────┘  │   │ 📅 07:00 — 5 Ağustos 2026           ││ │
│     │       │                                     │   │    ┌──────────────────────────────┐  ││ │
│     │       │  "Oynatma Listeleri" başlığı        │   │    │ glass panel ~250×100          │  ││ │
│     │       │  ┌──────┐┌──────┐┌──────┐┌──────┐  │   │    └──────────────────────────────┘  ││ │
│     │       │  │140×  ││140×  ││140×  ││140×  │  │   │                                      ││ │
│     │       │  │140   ││140   ││140   ││140   │  │   │ 📂 Klasörlerim                       ││ │
│     │       │  │kart  ││kart  ││kart  ││kart  │  │   │    [▶][YT][♥][♫][♥]                  ││ │
│     │       │  └──────┘└──────┘└──────┘└──────┘  │   │    ┌──────────────────────────────┐  ││ │
│     │       │  ☐ "Oynatma listesini göster"       │   │    │ glass panel ~250×100          │  ││ │
│     │       │                                     │   │    └──────────────────────────────┘  ││ │
│     │       │  "Sıradaki Şarkılar"                │   └──────────────────────────────────────┘│ │
│     │       │                                     │                                           │ │
│     │       │                       ┌─ Mini Card ──────────────────────┐                       │ │
│     │       │                       │ [50×50] Göksel                    │                       │ │
│     │       │                       │          Sevil Neşelen             │                       │ │
│     │       │                       │          Hayat Rüya Gibi           │                       │ │
│     │ y:495 │                       └───────────────────────────────────┘                       │ │
│     └─────────────────────────────────────────────────────────────────────────────────────────┘ │
│ y:510├─────────────────────────────────────────────────────────────────────────────────────────┤ │
│     │▲ pembe ilerleme çubuğu h:3px, full-width, y=0                                            │ │
│     │ y:513┌────────┐ ♪ Şarkı Adı  : Göksel - Sevil Neşelen                                   │ │
│     │       │120×120 │ ● Albümüm   : Hayat Rüya Gibi                                           │ │
│     │       │album   │ 🎤 Sanatçı  : Göksel                                                   │ │
│     │       │art     │                                                                         │ │
│     │ y:550└────────┘    [⏮]  [▶]  [⏹]  [⏭]     Süre: 09:00:00 / 00:05:00                   │ │
│     │                       ◯    ◉    ◯    ◯      Bit rate : 320 kbps                         │ │
│     │ y:566                   (33px çap daireler)    [🔊 ═══▲═══ ] % 100                       │ │
│ y:600└─────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘

Bileşenler: C01×8, C02, C03, C09×~14, Now Playing Card, Widget Area (4×glass), Mini Card, Footer Player
Layout: Split — Sol %42 (Now Playing + kartlar) / Sağ %58 (Widget'lar)
Sidebar: YOK (global sidebar token'ı kullanılmaz)
Arka plan: Tam kaplama kadın fotoğrafı, sunset tonları, pembe/mor
```

---

### HOŞ GELDİN MODALI — PNG #2

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — arka plan bulanık, backdrop-filter: blur(4px)]                                       │
│ [ANA SAYFA — arka plan bulanık, rgba(0,0,0,0.5)]                                              │
│                                                                                                 │
│     x:212                                                                                       │
│ y:145 ┌─────────────────────────────────────────────────────────────────────────────────────┐   │
│       │                                                                                     │   │
│       │                [CoreMusic Logo — Bickham Script Two, ~60×40px, orta hizalı]        │   │
│       │                Hoş geldin                                                           │   │
│       │ y:190                                                                              │   │
│       │                *İsminizi Girin Buraya*                                              │   │
│       │                (Bickham Script Two, italik, pembe, ~16px)                           │   │
│       │ y:220                                                                              │   │
│       │    Sana özel seçimler, müzik deneyimlerini ve sunumları                            │   │
│       │    tamamen sana özel hale getirir. CoreMusic ile rüyalarındaki                     │   │
│       │    müziğin Keyfine dal ♡                                                           │   │
│       │ y:280                                                                              │   │
│       │                ┌─────────────┐                                                      │   │
│       │                │   Başla     │  105×25px (WCAG İHLALİ: 48px olmalı)                │   │
│       │                │  #ff4fd8    │  pembe arka plan, beyaz text                         │   │
│       │ y:310          └─────────────┘                                                      │   │
│       │                                                                                     │   │
│ y:453 └─────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                 │
│ Modal: 600×308px, glass efekti, backdrop-filter: blur(20px) saturate(180%)                     │
│ Modal border: 1px solid rgba(255,255,255,0.1)                                                 │
│ Modal border-radius: 16px                                                                       │
│ Modal arka plan: Tam kaplama okyanus/sunset fotoğrafı + pembe tonları                          │
│ Overlay: rgba(0,0,0,0.5) + backdrop-filter: blur(4px)                                         │
│ Merkez: x=512, y=299.5                                                                         │
│ Kapat: backdrop click                                                                           │
│ Modal içinde: Kadın fotoğrafı (sağ üst), logolar, dekoratif text                              │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | Ana indeks |
| [[01-auth-screens]] | Auth ekranları |
| [[03-music-screens]] | Music ekranları |
| [[04-player-screens]] | Player ekranları |
| [[05-filemanager-screens]] | FileManager ekranları |
| [[06-settings-screens]] | Settings ekranları |
| [[07-reference-tables]] | Referans tabloları |

---

*Home Screen Mockups v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
