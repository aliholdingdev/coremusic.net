---
type: architecture
category: css-loading
title: "CoreMusic — CSS Device & View Mode Loading Plan"
date: 2026-08-05
updated: 2026-08-17
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---

# CoreMusic — CSS Device & View Mode Loading Plan

## 1. Amaç

RPi5 (1024x600) → 4K arası tüm ekranlar için CSS yükleme stratejisi.
Mobil hariç (ayrı uygulama olacak).

**Mimari Karar:** `main.css` kaldırıldı. Her device CSS (`d-*.css`, `d-auth-*.css`) **self-contained** — kendi import'unu kendi içinde yapar.

## 2. Mimari Değişiklik (v2.0.0)

### 2.1 ESKİ (v1.0.0) — main.css merkezli

```
main.css ← her şeyi import eder
  ├── 01_Abstracts/*
  ├── 02_Base/*
  ├── 03_Layout/*
  ├── 04_Components/*
  ├── 05_Pages/*
  ├── 06_Utilities/*
  ├── 07_Vendors/*
  └── 09_ViewModes/*

HtmlShellRenderer:
  1. main.css ← tokens, base, vendors
  2. d-{device}.css ← device overrides
  3. v-{viewMode}.css ← view mode overrides
  4. auth-bundled.css ← auth only
```

### 2.2 YENİ (v2.0.0) — Device CSS merkezli

```
main.css YOK — kaldırıldı

Her device CSS self-contained:
  d-{device}.css ← kendi import'unu kendi yapar
    ├── 01_Abstracts/*
    ├── 02_Base/*
    ├── 03_Layout/*
    ├── 05_Pages/*
    └── Device-specific overrides

HtmlShellRenderer:
  Home:  d-{device}.css (tek dosya, self-contained)
  Auth:  d-auth-{device}.css (tek dosya, self-contained) + auth-bundled.css
```

## 3. Mevcut Durum

| Katman | Dosya Sayısı | Satır | Durum |
|--------|-------------|-------|-------|
| 01_Abstracts | 8 | ~2,100 | ✅ Dolu |
| 02_Base | 2 | ~211 | ✅ Dolu |
| 03_Layout | 2 | ~959 | ✅ Dolu (header/footer responsive) |
| 04_Components | 3 | ~129 | ✅ Dolu |
| 05_Pages | 3 aktif | ~442 | ✅ Dolu |
| 06_Utilities | 1 | ~71 | ✅ Dolu |
| 07_Vendors | 1 | ~85 | ✅ Dolu |
| 08_Devices | 7 home + 7 auth = 14 | ~1,300+ | ✅ Home dolu, auth planlandı |
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

### 4.1 Home Device CSS (Self-Contained)

Her `d-{device}.css` kendi kendine yeter — kendi import'unu kendi içinde yapar:

```
d-embedded.css (self-contained)
  ├── 01_Abstracts/a-theme-config.css
  ├── 01_Abstracts/a-colors-token.css
  ├── 01_Abstracts/a-semantic-token.css
  ├── 01_Abstracts/a-breakpoint-tokens.css
  ├── 01_Abstracts/a-layout-tokens.css
  ├── 02_Base/b-base-core.css
  ├── 03_Layout/_header.css
  ├── 03_Layout/_footer.css
  ├── 05_Pages/_home-layout.css
  ├── 05_Pages/_home-components.css
  ├── 05_Pages/_home-inline.css
  └── Device-specific overrides (token values, grid, layout)
```

### 4.2 Auth Device CSS (Self-Contained)

Her `d-auth-{device}.css` kendi kendine yeter:

```
d-auth-embedded.css (self-contained)
  ├── 01_Abstracts/a-theme-config.css
  ├── 01_Abstracts/a-login-tokens.css
  ├── 01_Abstracts/a-light-glass-tokens.css
  ├── 02_Base/b-base-core.css
  └── Auth device overrides (panel width, font size, layout)
```

**Home layout import ETMEZ:**
- ❌ `_home-layout.css`
- ❌ `_home-components.css`
- ❌ `_home-inline.css`
- ❌ `_header.css`
- ❌ `_footer.css`

### 4.3 main.css KALDIRILDI

`main.css` artık kullanılmaz. Tüm import'lar device CSS'lerin içindedir.

**Eski:** `main.css` + `d-{device}.css` + `v-{viewMode}.css` (3 dosya)
**Yeni:** `d-{device}.css` (1 dosya, self-contained)

### 4.4 Yükleme Sırası

```
Request → HtmlShellRenderer
  │
  ├── Auth route mu? ($isAuthRoute)
  │   ├── EVET:
  │   │   1. d-auth-{device}.css (self-contained, auth tokens + layout)
  │   │   2. auth-bundled.css (auth page styles, ek override'lar)
  │   │
  │   └── HAYIR:
  │       1. d-{device}.css (self-contained, home tokens + layout)
  │       2. v-{viewMode}.css (view mode overrides)
  │
  └── main.css YOK — kaldırıldı
```

### 4.5 Device Loader (JS)

```javascript
// device-loader.js — Auth ve Home için ayrı mapping
class DeviceLoader {
    static loadCSS(device, isAuth) {
        const prefix = isAuth ? 'd-auth-' : 'd-';
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = `/Css/08_Devices/${prefix}${device}.css`;
        document.head.appendChild(link);
    }
}
```

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

## 7. Auth CSS Loading Plan

Auth sayfaları (login, register, select-gender, forgot-password, reset-password) için cihaz bazlı CSS yükleme stratejisi.

### 7.1 Mevcut Auth CSS Yapısı

| Dosya | Amaç | Durum |
|-------|------|-------|
| `auth-bundled.css` | Auth token + page CSS | ✅ Aktif |
| `d-{device}.css` | Home device CSS | ⚠️ Auth'da gereksiz yükleniyor |
| `d-auth-{device}.css` | Auth device CSS | ❌ Eksik |

### 7.2 Auth Device CSS Dosyaları (7 Yeni Dosya)

| Dosya | Cihaz | Breakpoint | Amaç |
|-------|-------|------------|------|
| `d-auth-embedded.css` | RPi5 | ≤1024px, 600px | Auth split layout, compact panel |
| `d-auth-phone.css` | Telefon | ≤767px | Auth stack layout, full-width panel |
| `d-auth-tablet.css` | Tablet | 768-1024px | Auth stack layout, narrow panel |
| `d-auth-laptop.css` | Laptop | 1025-1440px | Auth split layout, standard panel |
| `d-auth-desktop.css` | Masaüstü | 1441-2560px | Auth split layout, default panel |
| `d-auth-4k-tv.css` | 4K TV | 2561-3840px | Auth split layout, large panel |
| `d-auth-4k-monitor.css` | 4K Monitor | ≥3841px | Auth split layout, widest panel |

### 7.3 Auth Device CSS Import Zinciri

```
d-auth-{device}.css
  ├── 01_Abstracts/a-theme-config.css      ← Theme tokens
  ├── 01_Abstracts/a-login-tokens.css      ← Auth-specific tokens
  ├── 01_Abstracts/a-light-glass-tokens.css ← Glass effect tokens
  └── Auth device overrides                ← Panel width, font, layout
```

**Home layout import ETMEZ:**
- ❌ `_home-layout.css`
- ❌ `_home-components.css`
- ❌ `_home-inline.css`
- ❌ `_header.css`
- ❌ `_footer.css`

### 7.4 Auth Device Token Haritası

| Cihaz | `--lgn-panel-w` | `--lgn-font-size` | `--touch-min` | Layout |
|-------|-----------------|-------------------|---------------|--------|
| Embedded | `300px` | `14px` | `48px` | Split (flex) |
| Phone | `100%` | `14px` | `48px` | Stack (column-reverse) |
| Tablet | `380px` | `14px` | `48px` | Stack (column-reverse) |
| Laptop | `400px` | `14px` | — | Split (flex) |
| Desktop | `440px` | `16px` | — | Split (flex) |
| 4K TV | `500px` | `18px` | `56px` | Split (flex) |
| 4K Monitor | `560px` | `20px` | `64px` | Split (flex) |

### 7.5 DeviceCssMap.php Auth Mapping

```php
private const AUTH_DEVICE_CSS = [
    'embedded'   => '08_Devices/d-auth-embedded.css',
    'phone'      => '08_Devices/d-auth-phone.css',
    'tablet'     => '08_Devices/d-auth-tablet.css',
    'laptop'     => '08_Devices/d-auth-laptop.css',
    'desktop'    => '08_Devices/d-auth-desktop.css',
    '4k-tv'      => '08_Devices/d-auth-4k-tv.css',
    '4k-monitor' => '08_Devices/d-auth-4k-monitor.css',
];

public static function authToCssPath(string $deviceType): string
{
    return self::AUTH_DEVICE_CSS[$deviceType] ?? self::AUTH_DEVICE_CSS['desktop'];
}
```

### 7.6 HtmlShellRenderer.php Auth CSS Loading

```php
// YENİ mimari: main.css YOK, device CSS self-contained

$deviceCssPath = DeviceCssMap::toCssPath($deviceType);

if ($isAuthRoute) {
    // Auth: self-contained auth device CSS + auth-bundled
    $authDeviceCssPath = DeviceCssMap::authToCssPath($deviceType);
    $css = '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $authDeviceCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . '>';
    $css .= '<link rel="stylesheet" href="' . $assetsEsc . '/Css/auth-bundled.css?v=' . $cacheBuster . '"' . $nonceAttr . '>';
} else {
    // Home: self-contained device CSS + view mode
    $css = '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $deviceCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . '>';
    $css .= '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $viewCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . '>';
}
```

### 7.7 Auth CSS Loading Sırası

```
Request → HtmlShellRenderer
  → Auth route mu? ($isAuthRoute)
    → EVET:
      1. d-auth-{device}.css (self-contained: tokens + base + auth layout)
      2. auth-bundled.css (auth page styles, checkbox, body overrides)
    → HAYIR:
      1. d-{device}.css (self-contained: tokens + base + home layout)
      2. v-{viewMode}.css (view mode overrides)

  main.css YOK — kaldırıldı
```

### 7.8 Auth Device CSS İçerik Yapısı

Her `d-auth-{device}.css` dosyası şunları içerir:

```css
/* 1. Import: Auth-specific tokens */
@import '../01_Abstracts/a-theme-config.css';
@import '../01_Abstracts/a-login-tokens.css';
@import '../01_Abstracts/a-light-glass-tokens.css';

/* 2. Device token overrides */
:root {
  --lgn-panel-w: 440px;    /* Device-specific */
  --lgn-font-size: 16px;   /* Device-specific */
  --touch-min: 48px;       /* Device-specific */
}

/* 3. Auth layout overrides */
.lgn-page { /* Split or stack layout */ }
.lgn-panel { /* Panel width override */ }
.lgn-hero { /* Hero padding override */ }

/* 4. Auth component overrides */
.lgn-form__input { /* Input height override */ }
.lgn-btn { /* Button height override */ }
.lgn-social__btn { /* Social button size override */ }
```

### 7.9 Uygulama Sırası

| Sıra | Dosya | Aksiyon | Süre |
|------|-------|---------|------|
| 1 | `d-auth-embedded.css` | Oluştur: RPi5 auth layout | 20dk |
| 2 | `d-auth-phone.css` | Oluştur: Phone auth stack | 15dk |
| 3 | `d-auth-tablet.css` | Oluştur: Tablet auth stack | 15dk |
| 4 | `d-auth-laptop.css` | Oluştur: Laptop auth split | 15dk |
| 5 | `d-auth-desktop.css` | Oluştur: Desktop auth split | 15dk |
| 6 | `d-auth-4k-tv.css` | Oluştur: 4K TV auth split | 15dk |
| 7 | `d-auth-4k-monitor.css` | Oluştur: 4K Monitor auth split | 15dk |
| 8 | `DeviceCssMap.php` | Auth mapping ekle | 10dk |
| 9 | `HtmlShellRenderer.php` | Auth CSS loading güncelle | 15dk |

**Toplam Tahmini Süre:** ~2.5 saat

## 8. Uygulama Sırası (15 Adım)

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

## 10. Kritik Kontrol Noktaları

1. **main.css Kaldırıldı:** Artık kullanılmaz, tüm import'lar device CSS'lerin içindedir
2. **Header/Footer Override:** Home device CSS'lerde header/footer override edilmeli
3. **Inline Styles:** `footer.php` ve `header.php`'deki `style=""` attribute'ları CSS'e taşınacak
4. **Touch Targets:** RPi5'de minimum 44px (WCAG 2.2)
5. **Token Çakışması:** Yeni token dosyaları mevcut `--sp-*` ile çakışmamalı
6. **Auth Device CSS:** 7 yeni dosya oluşturulmalı (§7.2)
7. **Self-Contained:** Her device CSS kendi import'unu kendi yapmalı

## 11. İlgili ADR'ler

- **ADR-001:** Vanilla JS, ITCSS mimarisi
- **ADR-044:** Dynamic theme engine (cinsiyet bazlı tema)

## 12. Cross References

- [[decisions/accepted/ADR-001-vanilla-js-itcss]] — Frontend kararı
- [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] — Vault restructure
- [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] — Theme engine
- [[architecture/00-overview/architecture-master]] — L0-L6 mimari
- [[architecture/l3-presentation]] — CSS mimarisi
- [[architecture/l3-presentation/device-css]] — Device CSS detayları
- [[keys.md]] — Keyword navigasyon haritası

---
**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-17
**Mode:** Red Team • Human Mode • Truth Mode
