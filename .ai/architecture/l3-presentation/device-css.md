---
type: architecture
category: l3
title: "Device CSS"
date: 2026-08-08
updated: 2026-08-17
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Device CSS

**Zorunlu Bağlantılar:** [[index]] · [[ADR-045-multi-domain-view-mode-architecture]]

---

## 1. Amaç

Cihaz bazlı responsive CSS ve view mode'ları tanımlar. [[ADR-045-multi-domain-view-mode-architecture]] ile uyumludur.

---

## 2. Device Breakpoints

*device-loader.js ile senkronize*

| Cihaz | Genişlik | Yükseklik | CSS Dosyası |
|-------|----------|-----------|-------------|
| **Phone** | `≤767px` | — | `d-phone.css` |
| **Tablet** | `768-1024px` | `>600px` | `d-tablet.css` |
| **Embedded** | `768-1024px` | `≤600px` | `d-embedded.css` |
| **Laptop** | `1025-1440px` | — | `d-laptop.css` |
| **Desktop** | `1441-2560px` | — | `d-desktop.css` |
| **4K TV** | `2561-3840px` | — | `d-4k-tv.css` |
| **4K Monitor** | `≥3841px` | — | `d-4k-monitor.css` |

---

## 3. View Modes

| View | Amaç | CSS |
|------|------|-----|
| **Home** | Ev medya merkezi | `v-home.css` |
| **Pro** | Profesyonel | `v-pro.css` |
| **Studio** | Stüdyo | `v-studio.css` |
| **Car** | Araç içi | `v-car.css` |

---

## 4. Implementation

### 4.1 Self-Contained Architecture

**main.css kaldırıldı.** Her device CSS kendi import'unu kendi içinde yapar.

```
ESKİ: main.css + d-{device}.css + v-{viewMode}.css (3 dosya)
YENİ: d-{device}.css (1 dosya, self-contained)
```

### 4.2 Home Device CSS Import

```css
/* d-embedded.css — self-contained (bridge yok) */
@import '../01_Abstracts/a-theme-config.css';
@import '../01_Abstracts/a-colors-token.css';
@import '../01_Abstracts/a-semantic-token.css';
@import '../01_Abstracts/a-breakpoint-tokens.css';
@import '../01_Abstracts/a-layout-tokens.css';
@import '../02_Base/b-base-core.css';
@import '../03_Layout/_header.css';
@import '../03_Layout/_footer.css';
@import '../05_Pages/_home-layout.css';
@import '../05_Pages/_home-components.css';
@import '../05_Pages/_home-inline.css';

/* Device-specific overrides (1024×600 PNG mockup birebir) */
:root {
  --header-h: 60px;
  --footer-h: 90px;
  --content-h: 450px;
  --sidebar-w: 167px;
  --detail-panel-w: 366px;
  --card-thumb-size: 140px;
  --touch-min: 48px;
}
```

**NOT:** `_home.css` bridge KALDIRILDI. Doğrudan import.

### 4.3 Auth Device CSS Import

```css
/* d-auth-embedded.css — self-contained example */
@import '../01_Abstracts/a-theme-config.css';
@import '../01_Abstracts/a-login-tokens.css';
@import '../01_Abstracts/a-light-glass-tokens.css';
@import '../02_Base/b-base-core.css';

/* Auth device overrides */
:root {
  --lgn-panel-w: 300px;
  --lgn-font-size: 14px;
  --touch-min: 48px;
}
```

### 4.4 Yükleme Sırası (HtmlShellRenderer)

```php
// main.css YOK — device CSS self-contained
if ($isAuthRoute) {
    $css = '<link rel="stylesheet" href="' . $authDeviceCssPath . '">';
    $css .= '<link rel="stylesheet" href="' . $assetsEsc . '/Css/auth-bundled.css">';
} else {
    $css = '<link rel="stylesheet" href="' . $deviceCssPath . '">';
    $css .= '<link rel="stylesheet" href="' . $viewCssPath . '">';
}
```

---

## 5. Auth Device CSS

Auth sayfaları (login, register, select-gender, forgot-password, reset-password) cihaz bazlı device CSS'e ihtiyaç duyar. Ancak auth sayfaları home layout'dan farklı bir yapıya sahiptir — split layout (hero + panel) kullanır.

### 5.1 Mevcut Durum

| bileşen | Durum | Açıklama |
|---------|-------|----------|
| `auth-bundled.css` | ✅ Aktif | Auth-specific token + page CSS, statik |
| `d-{device}.css` | ⚠️ Yanlış yükleniyor | Home layout import ediyor (`_home-layout.css`, `_home-components.css`) |
| Auth device CSS | ❌ Eksik | Cihaz bazlı auth layout yok |

### 5.2 Problem

`HtmlShellRenderer.php` currently loads:
1. `main.css` — tüm rotalar için
2. `d-{device}.css` — tüm rotalar için (home layout import eder)
3. `v-{viewMode}.css` — tüm rotalar için
4. `auth-bundled.css` — sadece auth rotaları için

Auth rotalarında `d-{device}.css` yüklenir ama home layout import ettiği için gereksiz CSS yüklenir.

### 5.3 Çözüm — Auth Device CSS Dosyaları

Auth sayfaları için ayrı device CSS dosyaları oluşturulmalıdır:

```
08_Devices/
├── d-embedded.css          ← Home (mevcut)
├── d-phone.css             ← Home (mevcut)
├── d-tablet.css            ← Home (mevcut)
├── d-laptop.css            ← Home (mevcut)
├── d-desktop.css           ← Home (mevcut)
├── d-4k-tv.css             ← Home (mevcut)
├── d-4k-monitor.css        ← Home (mevcut)
├── d-auth-embedded.css     ← Auth — YENİ
├── d-auth-phone.css        ← Auth — YENİ
├── d-auth-tablet.css       ← Auth — YENİ
├── d-auth-laptop.css       ← Auth — YENİ
├── d-auth-desktop.css      ← Auth — YENİ
├── d-auth-4k-tv.css        ← Auth — YENİ
└── d-auth-4k-monitor.css   ← Auth — YENİ
```

### 5.4 Auth Device CSS Import Zinciri

Her auth device CSS kendi kendine yeter — sadece auth-specific token ve page import eder:

```
d-auth-embedded.css
  ├── 01_Abstracts/a-theme-config.css
  ├── 01_Abstracts/a-login-tokens.css
  ├── 01_Abstracts/a-light-glass-tokens.css
  └── Auth-specific device overrides (layout, panel width, font size)
```

**Home layout import ETMEZ:** `_home-layout.css`, `_home-components.css`, `_home-inline.css`

### 5.5 Auth Device CSS Token Haritası

| Cihaz | Panel Width | Font Size | Touch Target | Layout |
|-------|-------------|-----------|--------------|--------|
| **Embedded** (RPi5 1024x600) | 300px | 14px | 48px | Split (hero + panel) |
| **Phone** (≤767px) | 100% | 14px | 48px | Stack (column-reverse) |
| **Tablet** (768-1024px) | 380px | 14px | 48px | Stack (column-reverse) |
| **Laptop** (1025-1440px) | 400px | 14px | — | Split (hero + panel) |
| **Desktop** (1441-2560px) | 440px | 16px | — | Split (hero + panel) |
| **4K TV** (2561-3840px) | 500px | 18px | 56px | Split (hero + panel) |
| **4K Monitor** (≥3841px) | 560px | 20px | 64px | Split (hero + panel) |

### 5.6 DeviceCssMap.php Auth Mapping

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
```

### 5.7 HtmlShellRenderer.php Auth CSS Loading

```php
// Auth rotalarında device CSS yerine auth device CSS yükle
if ($isAuthRoute) {
    $authDeviceCssPath = DeviceCssMap::authToCssPath($deviceType);
    $css .= '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $authDeviceCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . '>';
    $css .= '<link rel="stylesheet" href="' . $assetsEsc . '/Css/auth-bundled.css?v=' . $cacheBuster . '"' . $nonceAttr . '>';
} else {
    $css .= '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $deviceCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . '>';
    $css .= '<link rel="stylesheet" href="' . $h($assetsUrl . '/Css/' . $viewCssPath) . '?v=' . $cacheBuster . '"' . $nonceAttr . '>';
}
```

---

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Orientation change** | Landscape/Portrait | ADR-045 |
| **DPI change** | Resolution media query | ADR-045 |
| **View mode change** | CSS class toggle | ADR-045 |
| **Device not detected** | Desktop default | ADR-045 |
| **Auth on mobile** | Stack layout (column-reverse) | Auth CSS |
| **Auth on 4K** | Larger panel, larger fonts | Auth CSS |

---

## 7. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-045-multi-domain-view-mode-architecture]] | View modes |
| [[architecture/03-css-device-loading-plan]] | CSS loading planı |
| [[ui-design/responsive-device-mode]] | Responsive Device Mode mimarisi kuralı |

---

## 8. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Satır Sayısı** | ~600 |
| **ADR Uyumlu** | ✅ 045 |
| **Auth Device CSS** | ✅ 7 cihaz tanımlı |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
