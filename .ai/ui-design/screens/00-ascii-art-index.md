---
title: "CoreMusic — ASCII Art View Index"
type: reference
category: ui-design
date: 2026-08-11
updated: 2026-08-17
status: active
version: 3.0.0
authority: PNG Visual Analysis (direct pixel inspection — all 18 PNGs)
reference:
  authority: ".ai/ui-design/screens/00-ascii-art-index.md"
  source_of_truth: ".ai/ui-design/screens/"
---

# CoreMusic — ASCII Art View Index

**18 PNG'nin piksel düzeyinde ASCII art karşılıkları.** Her view, PNG'deki birebir layout'u gösterir.

> **⚠️ Bu dosya boot protokolünde okunur. Detaylı ASCII art'lar için ilgili grup dosyasına gidin.**

---

## GENEL ÖLÇÜLER (Tüm Ekranlar)

```
Ekran: 1024 × 600 px (RPi5 7" dokunmatik)
Header: y:0-60, h:60px
İçerik: y:60-510, h:450px (içerik paneli: y:71-495)
Footer: y:510-600, h:90px
Accent: #ff4fd8 (pembe)
Font body: Arima
Font logo: Bickham Script Two
Arka plan: tam kaplama fotoğraf + backdrop-filter
```

---

## Table of Contents

| # | Grup | Kapsam | Dosya |
|---|------|--------|-------|
| 1 | Home Layouts | §1-4: Home, Hoş Geldin, Albümler, Albüm Detay | [[01-home-layouts]] |
| 2 | Music Layouts | §5-7: Sanatçılar, Playlist, Video Playback | [[02-music-layouts]] |
| 3 | File Manager Layouts | §8-9: Göz At 3 Sütun, Göz At Tıklama | [[03-filemanager-layouts]] |
| 4 | Connectivity Layouts | §10-13: WiFi Modal, WiFi Bağlan, Bluetooth, Select Gender | [[04-connectivity-layouts]] |
| 5 | Auth Layouts | §14-18: Login, Register 1-3, Auth Akış Özeti | [[05-auth-layouts]] |

---

## Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Total ASCII Art Views | 18 (tüm PNG'ler) |
| Pixel Accuracy | PNG piksel ölçümü ile birebir |
| Coordinate System | x:0-1024, y:0-600 (RPi5) |
| Components Mapped | C01-C16 (tüm bileşenler) |
| WCAG Annotations | Her view'da touch target notları |
| Platform | 4 (RPi5, Desktop, Mobile, TV) |
| Theme Variants | 3 (Female, Male, Neutral) |
| CSS Code Samples | Her view için mevcut |
| JS Behavior | Her view için mevcut |

---

## Platform Bazlı Genel Bakış

| View | RPi5 (1024×600) | Desktop (1920×1080) | Mobile (375×812) | TV (3840×2160) |
|------|-----------------|---------------------|------------------|----------------|
| Home | Split 42/58 | Split 42/58 | Dikey scroll | Split 42/58 |
| Albums | Standard 60/40 | Standard 60/40 | Tam ekran grid | Standard 60/40 |
| Artists | Standard 60/40 | Standard 60/40 | Tam ekran grid | Standard 60/40 |
| Playlist | Standard 65/35 | Standard 65/35 | Tam ekran tablo | Standard 65/35 |
| Video | Fullscreen + liste | Fullscreen + liste | Fullscreen + sheet | Fullscreen + liste |
| Disk Browser | 3 sütun | 3 sütun | Tam ekran + sheet | 3 sütun |
| File List | 3 sütun | 3 sütun | Tam ekran + drawer | 3 sütun |
| WiFi Modal | Merkez modal | Merkez modal | Bottom sheet | Merkez modal |
| Bluetooth | Merkez modal | Merkez modal | Bottom sheet | Merkez modal |
| Auth (Gender) | 72/28 split | 72/28 split | Tam ekran | 72/28 split |
| Auth (Login) | 78/22 split | 77/23 split | Tam ekran | 77/23 split |
| Auth (Register) | 78/22 split | 77/23 split | Tam ekran | 77/23 split |

---

## Tema Bazlı Renk Haritası

| View | Female (Pembe) | Male (Mavi) | Neutral (Nötr) |
|------|----------------|-------------|----------------|
| Home | `#ff4fd8` accent | `#4f9fff` accent | `#a0a0b0` accent |
| Albums | Pembe tab, buton | Mavi tab, buton | Nötr tab, buton |
| Artists | Pembe tab, buton | Mavi tab, buton | Nötr tab, buton |
| Playlist | Pembe satır, seek | Mavi satır, seek | Nötr satır, seek |
| Video | Pembe seek bar | Mavi seek bar | Nötr seek bar |
| WiFi | Pembe toggle | Mavi toggle | Nötr toggle |
| Bluetooth | Pembe toggle | Mavi toggle | Nötr toggle |
| Auth | Pembe buton, input | Mavi buton, input | Nötr buton, input |

---

*ASCII Art View Reference v3.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-17*
*Mode: Red Team · Human Mode · Truth Mode*
