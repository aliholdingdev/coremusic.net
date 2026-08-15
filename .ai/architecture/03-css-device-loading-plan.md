---
type: architecture
category: css-loading
title: "CoreMusic — CSS Device & View Mode Loading Plan"
date: 2026-08-05
updated: 2026-08-05
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---

# CoreMusic — CSS Device & View Mode Loading Plan

## 1. Amaç

RPi5 (1024x600) → 4K arası tüm ekranlar için CSS yükleme stratejisi.
Mobil hariç (ayrı uygulama olacak).

## 2. Mevcut Durum

| Katman | Dosya Sayısı | Satır | Durum |
|--------|-------------|-------|-------|
| 01_Abstracts | 8 | ~2,100 | ✅ Dolu |
| 02_Base | 2 | ~211 | ✅ Dolu |
| 03_Layout | 2 | ~959 | ✅ Dolu (header/footer responsive) |
| 04_Components | 3 | ~129 | ✅ Dolu |
| 05_Pages | 3 aktif | ~442 | ✅ Dolu |
| 06_Utilities | 1 | ~71 | ✅ Dolu |
| 07_Vendors | 1 | ~85 | ✅ Dolu |
| 08_Devices | 7 | ~657 | ✅ 5 dolu, 2 boş (phone/tablet — mobil hariç) |
| 09_ViewModes | 3 | ~60+ | ✅ Dolu |

**Toplam aktif CSS:** ~4,600+ satır
**Durum:** ✅ Tüm katmanlar aktif

## 3. Device Breakpoint Haritası

| Dosya | Cihaz | Genişlik | Yükseklik | Kullanım | Durum |
|-------|-------|----------|-----------|----------|-------|
| `d-embedded.css` | **RPi5** | ≤1024px | 600px | 10ft UI, touch-first | **✅ 197 satır** |
| `d-laptop.css` | Laptop | 1025-1440px | 768-900px | Standart laptop | **✅ 65 satır** |
| `d-desktop.css` | Masaüstü | 1441-2560px | 1080p | Normal monitör | **✅ 48 satır (varsayılan)** |
| `d-4k-tv.css` | 4K TV | 2561-3840px | 2160p | 10ft UI, büyük butonlar | **✅ 167 satır** |
| `d-4k-monitor.css` | 4K Monitor | ≥3841px | 4K+ | Detaylı, yüksek yoğunluk | **✅ 180 satır** |
| `d-phone.css` | Telefon | ≤767px | — | Mobil | **❌ Boş (mobil ayrı uygulama)** |
| `d-tablet.css` | Tablet | 768-1024px | — | Mobil | **❌ Boş (mobil ayrı uygulama)** |

## 4. Import Zinciri

Her device CSS (`d-*.css`) kendi kendine yetecek — kendi import'unu kendi içinde yapacak:

```
d-embedded.css
  ├── 01_Abstracts/ (token'lar)
  ├── 02_Base/ (reset)
  ├── 03_Layout/ (header/footer)
  ├── 05_Pages/ (sayfa CSS'leri)
  └── Device-specific overrides (media query)
```

**device-loader.js** tarafından dinamik yüklenir:
```html
<link rel="stylesheet" href="08_Devices/d-embedded.css" data-device="true">
```

**main.css** artık `08_Devices/` import etmez — her cihaz bağımsızdır.

## 5. Yeni Token Dosyaları

| Dosya | Konum | İçerik |
|-------|-------|--------|
| `a-breakpoint-tokens.css` | `01_Abstracts/` | `--bp-embedded: 1024`, `--bp-laptop: 1440`, `--bp-desktop: 2560`, `--bp-4k-tv: 3840`, `--bp-4k-monitor: 3841` |
| `a-layout-tokens.css` | `01_Abstracts/` | `--header-h: 70px`, `--header-h-compact: 60px`, `--footer-h: 138px`, `--footer-h-compact: 104px`, `--sidebar-w: 280px` |

**Not:** `a-theme-config.css`'teki mevcut `--sp-*` spacing token'ları yeterli, yeni spacing dosyası gerekmez.

## 6. Home Sayfası Grid Yapısı

```
┌──────────────────────────────────────────────┐
│ HEADER (70px / 60px compact)                 │
├──────────────────────────────────────────────┤
│ ┌─────────┬────────────────────────────────┐ │
│ │ SIDEBAR │ MAIN CONTENT                   │ │
│ │ 280px   │ Now Playing, Widgets, Grid     │ │
│ └─────────┴────────────────────────────────┘ │
├──────────────────────────────────────────────┤
│ FOOTER (138px / 104px compact)               │
└──────────────────────────────────────────────┘
```

## 7. Uygulama Sırası (15 Adım)

| Sıra | Dosya | Aksiyon | Süre |
|------|-------|---------|------|
| 1 | `a-breakpoint-tokens.css` | Yeni oluştur | 15dk |
| 2 | `a-layout-tokens.css` | Yeni oluştur | 15dk |
| 3 | `_home.css` | Oluştur (kırık import düzelt) | 20dk |
| 4 | `_home-layout.css` | Doldur: grid yapısı | 30dk |
| 5 | `_home-components.css` | Doldur: bileşen stilleri | 45dk |
| 6 | `_home-inline.css` | Doldur: inline→class | 30dk |
| 7 | `d-embedded.css` | Doldur: RPi5 1024x600 | 45dk |
| 8 | `d-laptop.css` | Doldur: 1025-1440 | 20dk |
| 9 | `d-desktop.css` | Doldur: 1441-2560 | 20dk |
| 10 | `d-4k-tv.css` | Doldur: 2561-3840 | 20dk |
| 11 | `d-4k-monitor.css` | Doldur: ≥3841 | 15dk |
| 12 | `v-home.css` | Doldur: home görünümü | 20dk |
| 13 | `v-pro.css` | Doldur: pro görünümü | 15dk |
| 14 | `v-studio.css` | Doldur: studio görünümü | 15dk |
| 15 | `main.css` | Güncelle: import ekle | 5dk |

**Toplam Tahmini Süre:** ~5.5 saat

## 8. Sorumluluk Matrisi

| Ajan | Sorumluluk | Dosyalar |
|------|-----------|----------|
| UI Designer | Token tasarımı, device CSS, viewmode CSS | `01_Abstracts/*`, `08_Devices/*`, `09_ViewModes/*` |
| Frontend Specialist | Home layout, component CSS | `05_Pages/_home-*.css` |
| Backend Architect | main.css import güncelleme | `main.css` |

## 9. Kritik Kontrol Noktaları

1. **Kırık Import:** `main.css`'teki `_home.css` import'u düzeltilmeli (şu an 404)
2. **Header/Footer Override:** Device CSS'lerde header/footer override edilmeli (mevcut responsive korunur)
3. **Inline Styles:** `footer.php` ve `header.php`'deki `style=""` attribute'ları CSS'e taşınacak
4. **Touch Targets:** RPi5'de minimum 44px (WCAG 2.2)
5. **Token Çakışması:** Yeni token dosyaları mevcut `--sp-*` ile çakışmamalı

## 10. İlgili ADR'ler

- **ADR-001:** Vanilla JS, ITCSS mimarisi
- **ADR-044:** Dynamic theme engine (cinsiyet bazlı tema)

## 11. Cross References

- [[decisions/accepted/ADR-001-vanilla-js-itcss]] — Frontend kararı
- [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] — Vault restructure
- [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] — Theme engine
- [[architecture/00-overview/architecture-master]] — L0-L6 mimari
- [[architecture/l3-presentation]] — CSS mimarisi
- [[keys.md]] — Keyword navigasyon haritası

---
**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-05
**Mode:** Red Team • Human Mode • Truth Mode
