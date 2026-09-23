---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Home Dashboard Desktop Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T17
viewport: 1920x1080
device: 22" FHD Monitor (Desktop)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T17-monitor-22fhd/home-dashboard.md"
  source_of_truth: ".ai/.png/home-1920/Linux - 1920 - Home.png"
---

# CoreMusic — Home Dashboard Desktop (T17 1920×1080)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                                                              x:1920    │
│ y:0 ┌─── HEADER (h:60) ──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│     │ Core Music │ Ana Sayfa │ Keşfet │ Albümler │ Sanatçılar │ Göz At │ Geçmiş │ Ayarlar │ Hakkımızda │     👤 Bayram Ali ▼   🔊  🌐  🔍  ⚙️          │  │
│ y:60 └─────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                                                                                  │
│ y:60 ┌─── CONTENT (h:930) ──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│     │                                                                                                                                                          │  │
│     │  ┌─── NOW PLAYING (25%, w:480) ───┐  ┌─── WELCOME BANNER (50%, w:960) ──────────────────┐  ┌─── WIDGETS (25%, w:480) ─────────────────────────────┐  │  │
│     │  │                                 │  │                                                  │  │                                                        │  │  │
│     │  │  🎵 Album Art (160×160)         │  │  ✨ Hoş Geldin ✨                                │  │  ┌─── Widget Row 1 ─────────────────────────────────┐  │  │  │
│     │  │  ┌──────────┐                   │  │  Bayram Ali                                      │  │  │ 🔵 Hoparlörler    │ 📅 07:00    │ ☁️ Hava      │  │  │  │
│     │  │  │          │  Şarkı: Göksel - │  │                                                  │  │  │    Core Music     │ 5 Haziran   │ Güneşli 13°  │  │  │  │
│     │  │  │  JRŞAT   │  Sevil Neşelenen │  │  "Müzik, ruhun gıdası her notada, bir nefeste    │  │  │    Hoparlör       │ 2026        │ İstanbul    │  │  │  │
│     │  │  │  GÜÜR    │                   │  │   gelen bir ruh hali yaratır."                   │  │  └────────────────────────────────────────────────┘  │  │  │
│     │  │  │          │  🎵 Hayat Rüya    │  │                                                  │  │                                                        │  │  │
│     │  │  └──────────┘  Gibi             │  │  ✨ Core Music ✨                                │  │  ┌─── Widget Row 2 ─────────────────────────────────┐  │  │  │
│     │  │                 🎤 Göksel        │  │                                                  │  │  │ 🎵 Kültür페스티벌 │ ❤️ 💜  │  🔴 YT  │ 🟣 SP │  │  │  │
│     │  │                                 │  │  2.450  156  87  42  1.250                       │  │  │    2.104 Item     │          │  📺      │ 🎵    │  │  │  │
│     │  │  ⭐⭐⭐⭐⭐ (5 yıldız)          │  │                                                  │  │  └────────────────────────────────────────────────┘  │  │  │
│     │  │                                 │  │  🔀 ▶ Keyfime Başla                              │  │                                                        │  │  │
│     │  │  Bit rate: 350 kbps   🔊 ♪ ♫   │  │                                                  │  │  ┌─── Widget Row 3 ─────────────────────────────────┐  │  │  │
│     │  │  ⏱ 00:05:00 ━━━━━━━━━━━ 100%  │  │                                                  │  │  │ 📊 İstatistikler  │ 🎵 Popüler  │ 📁 Son      │  │  │  │
│     │  │                                 │  │                                                  │  │  │    156 Şarkı      │  87 Dinlenme│  Eklenenler │  │  │  │
│     │  │  [⏮] [▶] [⏭]   🔀 🔁          │  │                                                  │  │  │    42 Albüm       │  42 Sanatçı │  1.250 Item │  │  │  │
│     │  └─────────────────────────────────┘  └──────────────────────────────────────────────────┘  └────────────────────────────────────────────────────────┘  │  │
│     │                                                                                                                                                          │  │
│     │  ┌─── EN SON DİNLENEN ŞARKILAR (33%, w:640) ──────────────────┐ ┌─── PLAYLIST'LER (33%, w:640) ──────────────────────────────────────────────────────┐  │  │
│     │  │ 🎵 Göksel - Sevil Neşelenen   00:05:00                    │ │ 🎵 İLK-10 Listesi              00:55:22                                 │  │  │  │
│     │  │ 🎵 Göksel - Kabahat Sensin    00:05:00                    │ │ 🎵 Haftalık MIX                00:55:22                                 │  │  │  │
│     │  │ 🎵 Gangsta - Çubuklar          00:07:19                    │ │ 🎵 Ruh Haline Göre Mix         00:55:22                                 │  │  │  │
│     │  │ 🎵 Keyifli Enstrümantal        00:05:13                    │ │ 🎵 Sabah Pop Listesi           00:55:16                                 │  │  │  │
│     │  │ 🎵 Erkin Koray - Fantastik     00:10:00                    │ │ 🎵 Akşam Jazz Mix              00:45:30                                 │  │  │  │
│     │  │ 🎵 Barış Manço - Dönence       00:08:45                    │ │ ▶️ Playlist listesini göster                                               │  │  │  │
│     │  └──────────────────────────────────────────────────────────────┘ └─────────────────────────────────────────────────────────────────────────────────────┘  │  │
│     │                                                                                                                                                          │  │
│     └──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                                                                                  │
│ y:990 ┌─── FOOTER PLAYER (h:90) ───────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│     │ 🎵 Şarkı Adı: Göksel - Sevil Neşelenen    [⏮] [▶] [⏹] [⏭]    🔀 🔁    🔊 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ % 100          │  │
│     │ 💿 Albüm: Hayat Rüya Gibi                  ● ● ● ● ●                                                                              │  │
│     │ 🎤 Sanatçı: Göksel                         ⏱ 00:00:00 / 00:05:00  |  Bit rate: 350 kbps                                          │  │
│ y:1080└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.home-page--desktop` | Desktop layout modifier |
| `.home-content--desktop` | Desktop content grid |
| `.home-now-playing--wide` | Geniş Now Playing paneli |
| `.home-welcome--wide` | Geniş hoş geldin banner'ı |
| `.home-widgets--wide` | Geniş widget paneli |
| `.home-cards--scroll` | Yatay scroll kart listesi |

---

## 3. Responsive Farklar (Embedded vs Desktop)

| Özellik | T08 (Embedded 1024) | T17 (Desktop 1920) |
|---------|---------------------|---------------------|
| Now Playing | w:42% | w:25% |
| Welcome Banner | Yok | w:50% |
| Widgets | w:58% | w:25% |
| Card Grid | 3 sütun dikey | Yatay scroll |
| Footer | h:90 | h:90 |
| Sidebar | Yok | 240px |
| Touch Target | 48px | 24px |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux - 1920 - Home.png` | 1920×1080 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
