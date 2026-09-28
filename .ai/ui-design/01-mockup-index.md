---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Mockup Index (19 PNG)"
type: index
category: ui-design
date: 2026-09-20
updated: 2026-09-28
status: active
version: 6.1.0
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

## 4. Screen Spec Eşleştirmesi (20 spec — 4b9ef53 senkronu)

> **Kaynak (disk gerçeği):** `screens/00-ascii-art-index.md` §3-5 (2026-09-28). v5.0.0'daki `screens/T07-embedded/*` yolları tarihte hiç var olmadı → eski adların haritası §7'de.

### 4.1 T08 Embedded — `screens/T08-embedded/` (1024×600, 12 spec)

| # | Screen Spec Dosyası | Figma node (kök) | Kaynak PNG (`source_of_truth`) | Status |
|---|---------------------|------------------|-------------------------------|:------:|
| 1 | `screens/T08-embedded/home-dashboard.md` | `1639:10160` (COMPONENT) | `.ai/.png/home-1024/Linux  1024 - Home Page.png` | active |
| 2 | `screens/T08-embedded/welcome-popup.md` | `2831:10265` | `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png` | active |
| 3 | `screens/T08-embedded/albums.md` | `2831:9176` | `.ai/.png/home-1024/Linux  1024 - Albumler Page.png` | active |
| 4 | `screens/T08-embedded/album-detail.md` | `2831:10086` (h=612 ⚠️) | `.ai/.png/home-1024/Linux  1024 - Albumler Details Detay Page.png` | active |
| 5 | `screens/T08-embedded/singer.md` | `2831:9273` | `.ai/.png/home-1024/Linux  1024 - Singer Page.png` | active |
| 6 | `screens/T08-embedded/playlist.md` | `2831:9443` | `.ai/.png/home-1024/Linux  1024 - Playlist Page.png` | active |
| 7 | `screens/T08-embedded/playlist-video.md` | `2831:9710` | `.ai/.png/home-1024/Linux  1024 - Playlist Page - Video Played.png` | active |
| 8 | `screens/T08-embedded/browse.md` | `2831:9555` | `.ai/.png/home-1024/Linux  1024 - Göz At Page.png` | active |
| 9 | `screens/T08-embedded/browse-clicked.md` | `2831:10282` (v5) + 4 varyant | `.ai/.png/home-1024/Linux  1024 - Göz At - Tıklama Clicked.png` | active |
| 10 | `screens/T08-embedded/wifi-quick.md` | `2831:9665` | `.ai/.png/home-1024/Linux  1024 - Wifi Quick Page Base.png` | active |
| 11 | `screens/T08-embedded/wifi-connect-light.md` | `2831:9644` | `.ai/.png/home-1024/Linux  1024 - Wifi Connect Light.png` | active |
| 12 | `screens/T08-embedded/bluetooth-quick.md` | `2831:9687` | `.ai/.png/home-1024/Linux  1024 - Bluetooth Quick Page Base.png` | active |

### 4.2 Shared / Auth — `screens/shared/` (1024×600, 6 spec, frontmatter `tier: T07`)

| # | Screen Spec Dosyası | Figma node (kök) | Kaynak PNG (`source_of_truth`) | Status |
|---|---------------------|------------------|-------------------------------|:------:|
| 13 | `screens/shared/login.md` | `2831:9826` | `.ai/.png/shared-1024/Linux  1024 - Login Girl.png` | active |
| 14 | `screens/shared/register-step1.md` | `2831:9894` | `.ai/.png/shared-1024/Linux  1024 - Register Girl.png` | active |
| 15 | `screens/shared/register-step2.md` | `2831:9957` | `.ai/.png/shared-1024/Linux  1024 - Register Girl step 2.png` | active |
| 16 | `screens/shared/register-step3.md` | `2831:10020` | `.ai/.png/shared-1024/Linux  1024 - Register Girl step 3.png` | active |
| 17 | `screens/shared/select-gender.md` | `2831:9748` | `.ai/.png/shared-1024/Linux  1024 - Select Gender.png` | active |
| 18 | `screens/shared/select-gender-selected.md` | `2831:9787` | `.ai/.png/shared-1024/Linux  1024 - Select Gender - selected.png` | active |

### 4.3 T17 Desktop Monitor — `screens/T17-monitor-22fhd/` (1920×1080, 2 spec)

| # | Screen Spec Dosyası | Figma node (kök) | Kaynak PNG (`source_of_truth`) | Status |
|---|---------------------|------------------|-------------------------------|:------:|
| 19 | `screens/T17-monitor-22fhd/home-dashboard.md` | `2831:13747` | `.ai/.png/home-1920/Linux - 1920 - Home.png` | active |
| 20 | `screens/T17-monitor-22fhd/welcome-popup.md` | `2876:6439` (modal 600×308) | `⚠️ VERIFICATION REQUIRED — 1920 popup PNG'si yok` | **draft** |

> **Tier notu (çelişki — taşınmadı):** dizin `T08-embedded` adını taşıyor ama viewport 1024×600 = `00-device-matrix` L92'de **T07** (L93: T08 = 1280×800); `shared/*` spec'leri zaten `tier: T07`. Öneri (taşıma owner onayına bağlı): `screens/00-ascii-art-index.md` §7 madde 1.

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

> **20 screen spec dosyası** (T08 12 + shared 6 + T17 2) — her ekran için hızlı referans.
> Tarihçe: 17 adet `screens/qr-*.md` 2026-09-19'da (commit 8ce113f) silindi; eski `.ai copy/ui-design/screens/qr-*.md` türevi de kaldırıldı.

| Dosya Grubu | Quick Reference Dosyaları |
|-------------|--------------------------|
| Home | `screens/T08-embedded/home-dashboard.md`, `screens/T08-embedded/welcome-popup.md` |
| Music | `screens/T08-embedded/albums.md`, `screens/T08-embedded/album-detail.md`, `screens/T08-embedded/singer.md` |
| Player | `screens/T08-embedded/playlist.md`, `screens/T08-embedded/playlist-video.md` |
| FileManager | `screens/T08-embedded/browse.md`, `screens/T08-embedded/browse-clicked.md` |
| Settings | `screens/T08-embedded/wifi-quick.md`, `screens/T08-embedded/wifi-connect-light.md`, `screens/T08-embedded/bluetooth-quick.md` |
| Auth | `screens/shared/select-gender.md`, `screens/shared/select-gender-selected.md`, `screens/shared/login.md`, `screens/shared/register-step1.md`, `screens/shared/register-step2.md`, `screens/shared/register-step3.md` |
| Desktop (T17) | `screens/T17-monitor-22fhd/home-dashboard.md`, `screens/T17-monitor-22fhd/welcome-popup.md` |

**Eski ad haritası (v5.0.0 listesi → disk gerçeği; 4b9ef53 = 2026-09-27):**

| Eski ad (bu indeks v5.0.0) | Git durumu | Yeni karşılık |
|----------------------------|------------|---------------|
| `screens/T07-embedded/artists.md` | ⚠️ silindi (4b9ef53 — `T08-embedded/artists.md`) | `screens/T08-embedded/singer.md` |
| `screens/T07-embedded/video-playback.md` | ⚠️ bu adda dosya hiç yok (`qr-video-playback.md` 8ce113f'te silindi) | `screens/T08-embedded/playlist-video.md` |
| `screens/T07-embedded/wifi-modal.md` | ⚠️ silindi (4b9ef53 — `T08-embedded/wifi-modal.md`) | `screens/T08-embedded/wifi-quick.md` |
| `screens/T07-embedded/wifi-connect.md` | ⚠️ bu adda dosya hiç yok (`qr-wifi-connect.md` 8ce113f'te silindi) | `screens/T08-embedded/wifi-connect-light.md` |
| `screens/T07-embedded/bluetooth-modal.md` | ⚠️ silindi (4b9ef53 — `T08-embedded/bluetooth-modal.md`) | `screens/T08-embedded/bluetooth-quick.md` |
| `screens/T07-embedded/auth-gender.md` | ⚠️ bu adda dosya hiç yok | `screens/shared/select-gender.md` + `screens/shared/select-gender-selected.md` |
| `screens/T07-embedded/auth-login.md` | ⚠️ silindi (4b9ef53 — `T01-phone-hd/auth-login.md`) | `screens/shared/login.md` |
| `screens/T07-embedded/auth-register.md` | ⚠️ bu adda dosya hiç yok | `screens/shared/register-step1.md` · `register-step2.md` · `register-step3.md` |
| `screens/T07-embedded/{home-dashboard,welcome-popup,albums,album-detail,playlist,browse,browse-clicked}.md` | ⚠️ `T07-embedded/` yolu hiç var olmadı (git log 0 kayıt) | `screens/T08-embedded/` aynı adlarla |

> 4b9ef53'te ayrıca (bu indekste hiç yer almayan) silinenler: `T08-embedded/file-browser.md`, `T08-embedded/now-playing.md` (yeni sette karşılığı yok), `T01-phone-hd/`, `T02-phone-fhd/`, `T03-phone-qhd/`, `T25-tv-43fhd/`, `T29-car-android-auto/`, `T31-watch-apple-40mm/` dosyaları.

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 6.1.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total PNG | 19 (12 home-1024 + 1 home-1920 + 6 shared-1024) |
| Viewports | 2 (1024×600, 1920×1080) |
| OS | Linux Embedded + Linux Desktop |
| Themes | 3 (Female #ff4fd8, Male #4f9fff, Neutral #a0a0b0) |
| Components | 19 (C01-C16 + C17 Widget Area + C18 Quick Apps + C19 Mini Card) |
| Layout Patterns | 6 (Standard 60/40, Split Home 1024, Split Home 1920, Fullscreen, Modal, Auth 72/28) |
| Auth Flow | Select Gender → Login → Register (3 adım) |
| Screen Specs | 20 spec dosya (19 active · 1 draft) ↔ 19 PNG |
| Spec Senkronu | 4b9ef53 (2026-09-27) → §4/§7 2026-09-28 tarihinde 20 dosyayla eşitlendi |
| Figma Sources | 1024×600 (Embedded) + 1920×1080 (Desktop) pixel-perfect |
| ADR Uyumlu | ADR-001 (Vanilla JS), ADR-044 (Theme Engine) |
| Cross References | 3 |
| Last Updated | 2026-09-28 |

---

**Authority:** Bayram Ali / Vault Steward
**Version:** 6.1.0 — §4/§7 4b9ef53 senkronu (20 spec) + Quality Report güncellendi
**Last Updated:** 2026-09-28
**Mode:** Red Team · Human Mode · Truth Mode
