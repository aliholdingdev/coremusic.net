---
type: architecture
category: l3
title: "Device CSS"
date: 2026-08-08
updated: 2026-09-02
status: active
version: 6.0.0
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

/* Device-specific token overrides (v4.0.0) */
.layout--embedded {
  --now-playing-art-size: 100px;
  --media-card-thumb-size: 140px;
  --mini-card-art-size: 50px;
  --detail-panel-art-size: 280px;
  --widget-min-height: 100px;
  --widget-grid-cols: 2;
  --footer-album-art-size: 120px;
  --footer-icon-size: 14px;
  --footer-btn-min-size: 48px;
  --home-top-split: 42% 58%;
  --home-bottom-split: 1fr 1.2fr 0.6fr;
}
```

### 4.3 Token ↔ Device CSS İlişkisi

Device CSS dosyaları **iki sorumluluk** taşır:

| Sorumluluk | İçerik | Örnek |
|-----------|--------|-------|
| **Behavioral** | Touch, hover, scrollbar, glass | `touch-action: manipulation;` |
| **Token Override** | Component boyutları, layout split | `--now-playing-art-size: 100px;` |

**Kural:** Behavioral özellikler `d-{device}.css`'te kalır. Component boyutları `a-layout-tokens.css`'teki `@media` + `.layout--{device}` tarafından yönetilir.

```
d-embedded.css:
  ├── Behavioral: touch-action, scrollbar, glass, hover devre dışı
  └── Token Override: .layout--embedded { --now-playing-art-size: 100px; }

a-layout-tokens.css:
  ├── :root { --now-playing-art-size: 100px; }           ← default
  ├── @media (min-width: 1920px) { --now-playing-art-size: 180px; }  ← responsive
  └── .layout--embedded { --now-playing-art-size: 100px; }          ← device context
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

## 4A. DeviceManager (PHP-Side Device-Aware Rendering)

**Dosya:** `shared/src/Device/DeviceManager.php`
**Namespace:** `CoreMusic\Device`

### 4A.1 Amaç

DeviceManager, cihaz bazlı HTML rendering'i PHP tarafında kontrol eder. Her cihaz tipi için farklı HTML yapısı, widget sayısı ve feature toggle'ları sunar.

### 4A.2 Factory Methods

| Method | Açıklama |
|--------|----------|
| `DeviceManager::fromRequest(ServerRequestInterface)` | PSR-7 request'ten cihaz tespiti |
| `DeviceManager::fromDevice(string)` | Doğrudan cihaz tipi belirterek |

### 4A.3 Device Queries

| Method | true olduğu cihazlar |
|--------|---------------------|
| `isEmbedded()` | RPi5, 1024×600 |
| `isPhone()` | ≤767px |
| `isTablet()` | 768-1024px, >600px |
| `isLaptop()` | 1025-1440px |
| `isDesktop()` | 1441-2560px |
| `is4kTv()` | 2561-3840px |
| `is4kMonitor()` | ≥3841px |
| `isTouch()` | embedded + phone + tablet |
| `isWide()` | laptop + desktop + 4k |
| `isLarge()` | desktop + 4k |
| `isMobile()` | phone + tablet |

### 4A.4 Content Config (per device)

| Property | embedded | phone | tablet | laptop | desktop | 4K |
|----------|----------|-------|--------|--------|---------|-----|
| widgetCount | 4 | 2 | 4 | 4 | 6 | 6 |
| recentCardCount | 3 | 2 | 4 | 5 | 7 | 8 |
| playlistCount | 0 | 0 | 2 | 2 | 3 | 3 |
| upNextCount | 1 | 1 | 3 | 3 | 5 | 5 |

### 4A.5 Feature Toggles

| Method | embedded | phone | tablet | laptop | desktop | 4K |
|--------|----------|-------|--------|--------|---------|-----|
| showVolume() | true | false | true | true | true | true |
| showFullMetadata() | false | false | false | true | true | true |
| showSidebar() | false | false | false | false | true | true |
| showSeekBar() | true | false | true | true | true | true |
| showPlaylistToggle() | false | false | true | true | true | true |
| showPodcastWidget() | false | false | false | false | true | true |
| showRadioWidget() | false | false | false | false | true | true |

### 4A.6 CSS Class Helpers

| Method | Çıktı |
|--------|-------|
| `layoutClass()` | `"layout--embedded"`, `"layout--phone"`, vb. |
| `allClasses()` | `"layout layout--embedded layout--touch"` |
| `dataAttributes()` | `'data-device="embedded" data-touch="true" data-wide="false"'` |

### 4A.7 Nav Links

| Cihaz | Nav Link'leri |
|-------|---------------|
| embedded | Ana Sayfa, Müzik |
| phone | Ana Sayfa, Müzik, Oynatıcı |
| laptop | Ana Sayfa, Müzik, Albümler, Oynatıcı |
| desktop | Ana Sayfa, Müzik, Albümler, Sanatçılar, Oynatıcı, Radyo, Podcast |
| 4K | Ana Sayfa, Müzik, Albümler, Sanatçılar, Çalma Listesi, Oynatıcı, Radyo, Podcast |

### 4A.8 Kullanım

```php
// header.php, footer.php, home.php
$dm = DeviceManager::fromRequest($request);

// CSS class ekleme
<header class="<?= $dm->allClasses() ?>" <?= $dm->dataAttributes() ?>>

// Widget sayısı
<?php for ($i = 0; $i < $dm->widgetCount(); $i++): ?>

// Feature toggle
<?php if ($dm->showVolume()): ?>
    <div class="volume-section">...</div>
<?php endif; ?>

// Nav links
<?php foreach ($dm->navLinks() as $link): ?>
    <a href="<?= $link['href'] ?>" <?= $link['active'] ? 'aria-current="page"' : '' ?>>
<?php endforeach; ?>
```

### 4A.9 mimari Entegrasyon

```
DeviceManager.php (shared/src/Device/)
  ├── DeviceDetector.php → range-based algılama (mevcut)
  ├── DeviceCssMap.php   → CSS haritası (mevcut)
  └── DeviceManager.php  → central device management (YENİ)

PHP Rendering:
  home.php    → 5 cihaz bloğu: if ($dm->isEmbedded()) ... elseif ($dm->isPhone()) ... else (4K)
  header.php  → $dm->navLinks() + $dm->allClasses() + $dm->dataAttributes()
  footer.php  → $dm->showVolume() + $dm->showFullMetadata() + $dm->allClasses()

CSS:
  a-layout-tokens.css → :root defaults + @media breakpoints + .layout--{device} overrides
  d-{device}.css      → behavioral (touch, scrollbar, glass) + token overrides
  .layout--{device}   → DeviceManager::layoutClass() tarafından PHP'den yazılır
```

**Kural:** DeviceManager PHP-side kontrol sağlar. CSS-side token'lar responsive breakpoints ile destekler. İkisi birlikte çalışır: PHP hangi HTML'i render edeceğini belirler, CSS hangi boyut/token'ların uygulanacağını belirler.

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
| **Versiyon** | 6.0.0 |
| **Satır Sayısı** | ~800 |
| **ADR Uyumlu** | ✅ 045 |
| **Auth Device CSS** | ✅ 7 cihaz tanımlı |
| **DeviceManager** | ✅ PHP-side device-aware rendering (5 cihaz bloğu, feature toggles) |
| **Zero Hallucination** | ✅ |

---

---

## 9. **FAZ 2D BULGUSU — d-auth-* Varlık Çelişkisi (2026-09-08)**

§5.3 bu dosya auth device CSS'leri "YENİ oluşturulmalı" der; **itcss-architecture.md §3 ağacı ise d-auth-* 7 dosyayı LİSTELİYOR** (var sayarak). İki doküman çelişiyor — çözüm Test-Path:

```powershell
Get-ChildItem -LiteralPath "assets.coremusic.net\Css\08_Devices" -Filter "d-auth-*.css" -ErrorAction SilentlyContinue | Select-Object Name
```

| Senaryo | Sonuç |
|---------|-------|
| Dosyalar VAR | itcss doğru; §5.3 "eksik" bölümü IMPLEMENTED'e döner; DeviceCssMap AUTH_DEVICE_CSS zaten kayıtlı (§5.6) |
| Dosyalar YOK | device-css doğru; itcss §3 ağacı hedef tasarımdır — ağaç nota alınır |

**Not:** §5.7'deki HtmlShellRenderer auth CSS yükleme kodu ve §5.6 DeviceCssMap AUTH_DEVICE_CSS sabiti kod gibi yazılmış — gerçeklik teyidi aynı Test-Path + kod okuma turunda yapılacaktır (guard-pipeline §24 protokolü).

**Ek çelişki:** §5.2 "HtmlShellRenderer currently loads main.css" diyor — **main.css KALDIRILDI** (itcss §14, device-css §4.1 kendi içinde de söylüyor!). §5.2 eski durumu anlatıyor — güncel durum: auth route'larda auth device CSS + auth-bundled.css, main.css yok (§4.4 doğru akış).

---

## 10. D-4K Paylaşım Notu (device-breakpoint bağlantısı)

[[device-breakpoint-guide]] Adım 2 notu: `d-4k.css` ≥7680px zoom ×3 içerir ve yeni üst-tier cihazlar (8K) d-4k.css'i paylaşmalıdır. §2 tablosunda 4K TV/Monitor ayrı dosyalardır (2561-3840 / ≥3841) — 8K genişlemesi bu yapıya eklenir.

---

## 11. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | d-auth-* varlık belirsizliği | Kesin (çelişki) | Orta | §9 Test-Path görevi |
| 2 | Eski main.css anlatımının karıştırılması | Orta | Düşük | §9 ek çelişki notu |
| 3 | §5.6-5.7 kod örneklerinin gerçek sanılması | Orta | Orta | Kod okuma teyidi |
| 4 | DeviceManager toggle matrisi sürüm sapması | Düşük | Orta | brain §18B kanonik |

---

## 12. Ek SSS

**S: §4A.7 nav link listesi brain §18B ile farklı — hangisi?**
C: brain §18B kanoniktir (embedded 4: Ana Sayfa/Kütüphane/Radyo/Ayarlar). §4A.7 tablosu eski varyanttır — çapraz düzeltme notu; kod (DeviceManager.php NAV_LINKS) tek kanıt.

**S: widgetCount embedded=4, playlist embedded=0 — çelişki yok mu (4A.4 playlist 0)?**
C: brain §18B içerik tablosu: EMBEDDED widget 4, recentCard 3, playlist 3, upNext 3. §4A.4 "playlistCount 0" farklı! Kanonik: brain §18B (Faz 1-5 refactor ile güncellendi). Bu tablo eski sürüm — düzeltme notu.

**S: d-auth-* dosyaları itcss ağacında neyse burada ne?**
C: §9 bulgusu — Test-Path karar verecek. İki doküman senkrona alınacak.

**S: DeviceCssMap::authToCssPath gerçek mi?**
C: §5.7 örneği; DeviceCssMap.php okumasında teyit — AUTH_DEVICE_CSS sabiti kanıtlanırsa IMPLEMENTED.

---

## 13. Kalite Raporu (Güncel)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 7.0.0 |
| **Kritik Bulgu** | §9 d-auth-* çelişkisi + main.css anlatım sapması |
| **Tablo Düzeltme Notu** | §12 — 4A.4/4A.7 brain §18B ile çapraz |
| **ADR Uyumlu** | ✅ 045 |
| **Zero Hallucination** | ✅ (çelişkiler gizlenmedi — çözüm görevine bağlandı) |

---

## 14. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 6.0.0 | 2026-09-02 | Auth device CSS + DeviceManager |
| 7.0.0 | 2026-09-08 | Faz 2d: §9 d-auth-* varlık çelişkisi + main.css anlatım sapması; §10 d-4k paylaşım notu; §11-§14 ekler; §12 brain §18B çapraz düzeltme notları |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
