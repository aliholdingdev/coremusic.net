---
title: "CoreMusic — Mockup Index (18 PNG, home-1024 + shared-1024, Linux Embedded RPi5)"
type: reference
version: 5.0.0
---

# CoreMusic — Mockup Index (v5.0.0)

**18 PNG mockup'ın kanonik indeksi.** Bundan sonra her frontend görevinin başlangıç noktası bu dosyadır.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir.

---

## Screen Categories

| # | Category | File | Screens | PNG Count |
|---|----------|------|---------|-----------|
| 1 | Auth | [[mockups/01-auth-screens]] | Select Gender, Login, Register (3 step) | 6 |
| 2 | Home | [[mockups/02-home-screens]] | Ana Sayfa, Hoş Geldin Modalı | 2 |
| 3 | Music | [[mockups/03-music-screens]] | Albums, Album Detail, Artists | 3 |
| 4 | Player | [[mockups/04-player-screens]] | Playlist, Video Playback | 2 |
| 5 | FileManager | [[mockups/05-filemanager-screens]] | Disk Browser, File List | 2 |
| 6 | Settings | [[mockups/06-settings-screens]] | WiFi, WiFi Connect, Bluetooth | 3 |
| 7 | Reference | [[mockups/07-reference-tables]] | Platform, Measurements, Matrices | — |

---

## Quick Reference

| Bilgi | Değer |
|-------|-------|
| Total PNG | 18 (12 home + 6 shared) |
| Resolution | 1024×600 (RPi5 7" dokunmatik) |
| OS | Linux Embedded |
| Themes | 3 (Female #ff4fd8, Male #4f9fff, Neutral #a0a0b0) |
| Components | 16 (C01-C16) |
| Layout Patterns | 5 (Standard 60/40, Split Home, Fullscreen, Modal, Auth 72/28) |
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
| [[mockups/02-home-screens]] | Home ekranları — Ana Sayfa, Hoş Geldin |
| [[mockups/03-music-screens]] | Music ekranları — Albums, Artists |
| [[mockups/04-player-screens]] | Player ekranları — Playlist, Video |
| [[mockups/05-filemanager-screens]] | FileManager ekranları — Disk, Dosya |
| [[mockups/06-settings-screens]] | Settings ekranları — WiFi, Bluetooth |
| [[mockups/07-reference-tables]] | Referans tabloları — Platform, Ölçüler, Matrisler |
| `01-component-inventory.md` | C01-C16 bileşen envanteri |
| `02-implementation-plan.md` | 15 adımlık CSS uygulama planı |
| `03-accessibility-gaps.md` | WCAG 2.2 AA gap analizi |
| `04-vault-registration.md` | Vault kalıcı kayıt durumu |

---

*Mockup Index v5.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
