---
title: "CoreMusic — Mockup Index (19 PNG, home-1024 + home-1920 + shared-1024, Linux)"
type: reference
version: 6.1.0
updated: 2026-09-04
---

# CoreMusic — Mockup Index (v6.1.0)

**19 PNG mockup'ın kanonik indeksi.** Bundan sonra her frontend görevinin başlangıç noktası bu dosyadır.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir.

---

## Screen Categories

| # | Category | File | Screens | PNG Count |
|---|----------|------|---------|-----------|
| 1 | Auth | [[mockups/01-auth-screens]] | Select Gender, Login, Register (3 step) | 6 |
| 2 | Home (1024) | [[mockups/02-home-screens]] | Ana Sayfa, Hoş Geldin Modalı | 2 |
| 3 | Home (1920) | [[mockups/02-home-screens-1920]] | Desktop Ana Sayfa | 1 |
| 4 | Music | [[mockups/03-music-screens]] | Albums, Album Detail, Artists | 3 |
| 5 | Player | [[mockups/04-player-screens]] | Playlist, Video Playback | 2 |
| 6 | FileManager | [[mockups/05-filemanager-screens]] | Disk Browser, File List | 2 |
| 7 | Settings | [[mockups/06-settings-screens]] | WiFi, WiFi Connect, Bluetooth | 3 |
| 8 | Reference | [[mockups/07-reference-tables]] | Platform, Measurements, Matrices | — |

---

## Quick Reference (qr-) Sistemi

> **17 compact qr-*.md dosyasi** — her ekran icin max 10KB hizli referans.
> OpenCode intent router'i "ascii art", "wireframe", "layout spec" keyword'lerini otomatik algilar.
> `ascii-art-viewer` skill'i ile tam ekran haritasi ve okuma protokolu mevcut.

| Dosya Grubu | Quick Reference Dosyalari |
|-------------|--------------------------|
| Home | `screens/qr-home-1024.md`, `screens/qr-welcome-popup.md`, `screens/qr-home-1920.md` |
| Music | `screens/qr-albums.md`, `screens/qr-album-detail.md`, `screens/qr-artists.md` |
| Player | `screens/qr-playlist.md`, `screens/qr-video-playback.md` |
| FileManager | `screens/qr-disk-browser.md`, `screens/qr-file-list.md` |
| Settings | `screens/qr-wifi.md`, `screens/qr-wifi-connect.md`, `screens/qr-bluetooth.md` |
| Auth | `screens/qr-gender-select.md`, `screens/qr-login.md`, `screens/qr-register-step1.md`, `screens/qr-register-step2-3.md` |

---

## Quick Reference

| Bilgi | Değer |
|-------|-------|
| Total PNG | 19 (12 home-1024 + 1 home-1920 + 6 shared) |
| Resolution | 1024×600 (RPi5 7" dokunmatik) + 1920×1080 (Desktop) |
| OS | Linux Embedded + Linux Desktop |
| Themes | 3 (Female #ff4fd8, Male #4f9fff, Neutral #a0a0b0) |
| Components | 16 (C01-C16) |
| Layout Patterns | 6 (Standard 60/40, Split Home 1024, Split Home 1920, Fullscreen, Modal, Auth 72/28) |
| Auth Flow | Select Gender → Login → Register (3 adım) |
| ADR Uyumlu | ADR-001 (Vanilla JS), ADR-044 (Theme Engine) |

---

## PNG Dizin Yapısı

```
.ai/.png/
├── home-1024/          ← 12 PNG (Ana sayfa, albümler, sanatçılar, playlist, video, göz at, WiFi, Bluetooth)
│   ├── Linux  1024 - Home Page.png
│   ├── Linux  1024 - Home Page Welcome Popup.png
│   ├── Linux  1024 - Albumler Page.png
│   ├── Linux  1024 - Albumler Details Detay Page.png
│   ├── Linux  1024 - Singer Page.png
│   ├── Linux  1024 - Playlist Page.png
│   ├── Linux  1024 - Playlist Page - Video Played.png
│   ├── Linux  1024 - Göz At Page.png
│   ├── Linux  1024 - Göz At - Tıklama Clikced.png
│   ├── Linux  1024 - Wifi Qucik Page Base.png
│   ├── Linux  1024 - Wifi Coonect Light.png
│   └── Linux  1024 - Bluethoot Qucik Page Base.png
├── home-1920/          ← 1 PNG (Desktop ana sayfa)
│   └── Linux - 1920 - Home.png
└── shared-1024/        ← 6 PNG (Auth ekranları)
    ├── Linux  1024 - Select Gender.png
    ├── Linux  1024 - Select Gender - selected.png
    ├── Linux  1024 - Login Girl.png
    ├── Linux  1024 - Register Girl.png
    ├── Linux  1024 - Register Girl step 2.png
    └── Linux  1024 - Register Girl step 3.png
```

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[mockups/01-auth-screens]] | Auth ekranları — Select Gender, Login, Register |
| [[mockups/02-home-screens]] | Home ekranları (1024) — Ana Sayfa, Hoş Geldin |
| [[mockups/02-home-screens-1920]] | Home ekranları (1920) — Desktop Ana Sayfa |
| [[mockups/03-music-screens]] | Music ekranları — Albums, Artists |
| [[mockups/04-player-screens]] | Player ekranları — Playlist, Video |
| [[mockups/05-filemanager-screens]] | FileManager ekranları — Disk, Dosya |
| [[mockups/06-settings-screens]] | Settings ekranları — WiFi, Bluetooth |
| [[mockups/07-reference-tables]] | Referans tabloları — Platform, Ölçüler, Matrisler |
| `01-component-inventory.md` | C01-C16 bileşen envanteri |
| `02-implementation-plan.md` | 15 adımlık CSS uygulama planı |
| `03-accessibility-gaps.md` | WCAG 2.2 AA gap analizi |
| `04-vault-registration.md` | Vault kalıcı kayıt durumu |
| `screens/B-home/dashboard-1920.md` | Desktop 1920 ASCII view (PNG doğrulamalı, 2026-09-06) |
| `responsive-device-mode.md` | Responsive tier kuralları — 4K No-Center §7.4 + Backward-Compat §12 |

---

*Mockup Index v6.1.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-04*
*Mode: Red Team · Human Mode · Truth Mode*
