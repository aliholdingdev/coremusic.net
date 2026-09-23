---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Mockup Index (19 PNG)"
type: index
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/01-mockup-index.md"
  source_of_truth: ".ai/.png/home-1024/ · .ai/.png/home-1920/ · .ai/.png/shared-1024/"
---

# CoreMusic — Mockup Index (19 PNG)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[02-component-inventory]] · [[05-responsive-architecture]]

---

## 1. Amaç

CoreMusic UI tasarımının **görsel referanslarının tek indeksidir**. 19 PNG mockup dosyası, tüm frontend geliştirme görevlerinde tartışmasız başlangıç noktasıdır.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir.

---

## 2. PNG Dizin Yapısı

```
.ai/.png/
├── home-1024/          ← 12 PNG (RPi5 1024×600 Embedded)
│   ├── Linux  1024 - Home Page.png
│   ├── Linux  1024 - Home Page Welcome Popup.png
│   ├── Linux  1024 - Albumler Page.png
│   ├── Linux  1024 - Albumler Details Detay Page.png
│   ├── Linux  1024 - Singer Page.png
│   ├── Linux  1024 - Playlist Page.png
│   ├── Linux  1024 - Playlist Page - Video Played.png
│   ├── Linux  1024 - Göz At Page.png
│   ├── Linux  1024 - Göz At - Tıklama Clicked.png
│   ├── Linux  1024 - Wifi Quick Page Base.png
│   ├── Linux  1024 - Wifi Connect Light.png
│   └── Linux  1024 - Bluetooth Quick Page Base.png
├── home-1920/          ← 1 PNG (Desktop 1920×1080)
│   └── Linux - 1920 - Home.png
└── shared-1024/        ← 6 PNG (Auth ekranları 1024×600)
    ├── Linux  1024 - Select Gender.png
    ├── Linux  1024 - Select Gender - selected.png
    ├── Linux  1024 - Login Girl.png
    ├── Linux  1024 - Register Girl.png
    ├── Linux  1024 - Register Girl step 2.png
    └── Linux  1024 - Register Girl step 3.png
```

---

## 3. Mockup Kategorileri

### 3.1 Home Dashboard — Embedded (12 PNG)

| # | Dosya Adı | Viewport | Cihaz | İçerik |
|---|-----------|----------|-------|--------|
| 1 | `Linux  1024 - Home Page.png` | 1024×600 | RPi5 7" Touch | Ana sayfa: Now Playing (42%), Widget 2×2 (58%), alt satır 3 sütun, footer player |
| 2 | `Linux  1024 - Home Page Welcome Popup.png` | 1024×600 | RPi5 7" Touch | Karşılama modalı: 600×308px, manzara fotoğrafı + hoş geldin |
| 3 | `Linux  1024 - Albumler Page.png` | 1024×600 | RPi5 7" Touch | Albümler: Sol%60 grid (4×N kart), Sağ%40 detay paneli |
| 4 | `Linux  1024 - Albumler Details Detay Page.png` | 1024×600 | RPi5 7" Touch | Albüm detay: 300×300 art, parça listesi, metadata |
| 5 | `Linux  1024 - Singer Page.png` | 1024×600 | RPi5 7" Touch | Sanatçılar: Dairesel kartlar (border-radius: 50%), detay paneli |
| 6 | `Linux  1024 - Playlist Page.png` | 1024×600 | RPi5 7" Touch | Çalma listeleri: Sol liste, sağ parça listesi |
| 7 | `Linux  1024 - Playlist Page - Video Played.png` | 1024×600 | RPi5 7" Touch | Video oynatma: Playlist + video player |
| 8 | `Linux  1024 - Göz At Page.png` | 1024×600 | RPi5 7" Touch | Dosya yöneticisi: Disk tarayıcı |
| 9 | `Linux  1024 - Göz At - Tıklama Clicked.png` | 1024×600 | RPi5 7" Touch | Dosya yöneticisi: Tıklama sonrası görünüm |
| 10 | `Linux  1024 - Wifi Quick Page Base.png` | 1024×600 | RPi5 7" Touch | WiFi modal: Ağ listesi, toggle, sinyal badge'leri |
| 11 | `Linux  1024 - Wifi Connect Light.png` | 1024×600 | RPi5 7" Touch | WiFi bağlama: Şifre formu modal |
| 12 | `Linux  1024 - Bluetooth Quick Page Base.png` | 1024×600 | RPi5 7" Touch | Bluetooth modal: Cihaz listesi, eşleşme |

### 3.2 Home Dashboard — Desktop (1 PNG)

| # | Dosya Adı | Viewport | Cihaz | İçerik |
|---|-----------|----------|-------|--------|
| 13 | `Linux - 1920 - Home.png` | 1920×1080 | Desktop FHD | Ana sayfa: 3 sütun (Now Playing 33%, Welcome 34%, Widgets 33%), alt satır 2 sütun (7+6 kart), footer player |

### 3.3 Auth Ekranları — Shared (6 PNG)

| # | Dosya Adı | Viewport | Cihaz | İçerik |
|---|-----------|----------|-------|--------|
| 14 | `Linux  1024 - Select Gender.png` | 1024×600 | RPi5 7" Touch | Cinsiyet seçimi: 3 buton (Kız/Erkek/Diğer), sol%72 manzara, sağ%28 form |
| 15 | `Linux  1024 - Select Gender - selected.png` | 1024×600 | RPi5 7" Touch | Cinsiyet seçimi (seçili): Pembe vurgu, border change |
| 16 | `Linux  1024 - Login Girl.png` | 1024×600 | RPi5 7" Touch | Giriş: E-posta/şifre formu, sosyal giriş (7 buton), sol%72 manzara |
| 17 | `Linux  1024 - Register Girl.png` | 1024×600 | RPi5 7" Touch | Kayıt adım 1: Kullanıcı adı, e-posta |
| 18 | `Linux  1024 - Register Girl step 2.png` | 1024×600 | RPi5 7" Touch | Kayıt adım 2: Şifre, şifre tekrar |
| 19 | `Linux  1024 - Register Girl step 3.png` | 1024×600 | RPi5 7" Touch | Kayıt adım 3: KVKK onay, kayıt tamamla |

---

## 4. Screen Spec Eşleştirmesi

| PNG Dosyası | Screen Spec Dosyası | Tier |
|-------------|--------------------|------|
| `Home Page.png` | `screens/T07-embedded/home-dashboard.md` | T07 |
| `Home Page Welcome Popup.png` | `screens/T07-embedded/welcome-popup.md` | T07 |
| `Albumler Page.png` | `screens/T07-embedded/albums.md` | T07 |
| `Albumler Details Detay Page.png` | `screens/T07-embedded/album-detail.md` | T07 |
| `Singer Page.png` | `screens/T07-embedded/artists.md` | T07 |
| `Playlist Page.png` | `screens/T07-embedded/playlist.md` | T07 |
| `Playlist Page - Video Played.png` | `screens/T07-embedded/video-playback.md` | T07 |
| `Göz At Page.png` | `screens/T07-embedded/browse.md` | T07 |
| `Göz At - Tıklama Clicked.png` | `screens/T07-embedded/browse-clicked.md` | T07 |
| `Wifi Quick Page Base.png` | `screens/T07-embedded/wifi-modal.md` | T07 |
| `Wifi Connect Light.png` | `screens/T07-embedded/wifi-connect.md` | T07 |
| `Bluetooth Quick Page Base.png` | `screens/T07-embedded/bluetooth-modal.md` | T07 |
| `Linux - 1920 - Home.png` | `screens/T17-monitor-22fhd/home-dashboard.md` | T17 |
| `Select Gender.png` | `screens/T07-embedded/auth-gender.md` | T07 |
| `Select Gender - selected.png` | `screens/T07-embedded/auth-gender.md` | T07 |
| `Login Girl.png` | `screens/T07-embedded/auth-login.md` | T07 |
| `Register Girl.png` | `screens/T07-embedded/auth-register.md` | T07 |
| `Register Girl step 2.png` | `screens/T07-embedded/auth-register.md` | T07 |
| `Register Girl step 3.png` | `screens/T07-embedded/auth-register.md` | T07 |

---

## 5. Referans Sıralaması (Çelişki Durumunda)

```
PNG Mockup > ASCII Art > Component Inventory > Tokens > Implementation Plan
```

---

## 6. Kullanım Protokolü

1. Frontend görevi başlamadan önce bu dosya okunur
2. İlgili PNG referansı bulunur (§3 tabloları)
3. PNG'den piksel ölçümleri çıkartılır
4. Screen spec ile eşleştirilir (§4)
5. Component inventory ile doğrulanır
6. Token'lar ile CSS'e dönüştürülür

---

## 7. Quick Reference (qr-) Sistemi

> **17 compact qr-*.md dosyası** — her ekran için max 10KB hızlı referans.
> Eski `.ai copy/ui-design/screens/qr-*.md` dosyalarından türetilmiştir.

| Dosya Grubu | Quick Reference Dosyaları |
|-------------|--------------------------|
| Home | `screens/T07-embedded/home-dashboard.md`, `welcome-popup.md` |
| Music | `screens/T07-embedded/albums.md`, `album-detail.md`, `artists.md` |
| Player | `screens/T07-embedded/playlist.md`, `video-playback.md` |
| FileManager | `screens/T07-embedded/browse.md`, `browse-clicked.md` |
| Settings | `screens/T07-embedded/wifi-modal.md`, `wifi-connect.md`, `bluetooth-modal.md` |
| Auth | `screens/T07-embedded/auth-gender.md`, `auth-login.md`, `auth-register.md` |

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 6.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total PNG | 19 (12 home-1024 + 1 home-1920 + 6 shared-1024) |
| Viewports | 2 (1024×600, 1920×1080) |
| OS | Linux Embedded + Linux Desktop |
| Themes | 3 (Female #ff4fd8, Male #4f9fff, Neutral #a0a0b0) |
| Components | 19 (C01-C16 + C17 Widget Area + C18 Quick Apps + C19 Mini Card) |
| Layout Patterns | 6 (Standard 60/40, Split Home 1024, Split Home 1920, Fullscreen, Modal, Auth 72/28) |
| Auth Flow | Select Gender → Login → Register (3 adım) |
| Screen Specs | 19 PNG ↔ 19 screen spec eşleşmesi |
| Figma Sources | 1024×600 (Embedded) + 1920×1080 (Desktop) pixel-perfect |
| ADR Uyumlu | ADR-001 (Vanilla JS), ADR-044 (Theme Engine) |
| Cross References | 3 |
| Last Updated | 2026-09-22 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-22
**Mode:** Red Team · Human Mode · Truth Mode
