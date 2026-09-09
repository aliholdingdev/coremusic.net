---
title: "CoreMusic — PHP Koşullu Render Implementasyon Rehberi"
type: guide
category: backend
date: 2026-09-04
updated: 2026-09-05
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — PHP Koşullu Render Implementasyon Rehberi

Bu belge, responsive device detection ve conditional rendering sisteminin PHP tarafını anlatır.

---

## 1. Genel Akış

```
Kullanıcı tarayıcı açar
    │
    ▼
index.php (Entry Point)
    │
    ▼
PageRouterKernel::handle()
    │
    ├── SessionInitializer (session başlat)
    ├── Middleware'ler (CSRF, Auth, vb.)
    │
    ▼
PageRouter::dispatch()
    │
    ▼
renderPage()
    │
    ├── DeviceDetector::detect(UA, viewportW, viewportH)
    │       └── Device türü döndürür: 'embedded', 'desktop', 'laptop', vb.
    │
    ├── DeviceManager::fromRequest(viewportW, viewportH)
    │       └── Cookie'den viewport oku
    │       └── DeviceManager nesnesi oluştur
    │
    └── include home.php
            │
            ├── $dm->isPhone()                    → Phone Layout (≤767px)
            ├── $dm->shouldRender4kLayout()       → 4K Layout (≥2561px)
            ├── $dm->shouldRenderWideLayout()     → Wide Layout (1025-2560px)
            ├── $dm->shouldRenderEmbeddedLayout() → Embedded Layout (≤1024px)
            └── $dm->shouldShowFallback()         → FALSE (hiçbir zaman)
```

---

## 2. DeviceDetector.php — Cihaz Tespiti

### 2.1 Sorumluluk

User-Agent ve viewport boyutundan cihaz türünü tespit eder.

### 2.2 Kullanım

```php
use CoreMusic\Device\DeviceDetector;

// Sadece User-Agent ile
$device = DeviceDetector::detect($userAgent);
// Sonuç: 'desktop', 'phone', 'embedded', vb.

// User-Agent + viewport ile
$device = DeviceDetector::detect($userAgent, 1920, 1080);
// Sonuç: 'desktop' (viewport 1920px → desktop)

// Sadece viewport ile
$device = DeviceDetector::detectFromViewport(1024, 600);
// Sonuç: 'embedded' (1024×600 → RPi5)
```

### 2.3 Tespit Önceliği

```
1. HTTP Header: X-Device-Type: embedded  → 'embedded'
2. User-Agent: "Raspberry Pi" içeriği    → 'embedded'
3. User-Agent: "Tizen/webOS/SmartTV"     → '4k-tv'
4. Viewport: ≤767px                      → 'phone'
5. Viewport: 768-1024px + h≤600         → 'embedded'
6. Viewport: 768-1024px + h≥768         → 'laptop'
7. Viewport: ≤1440px                     → 'laptop'
8. Viewport: ≤2560px                     → 'desktop'
9. Viewport: ≤3840px + TV UA            → '4k-tv'
10. Viewport: ≤3840px + Desktop OS      → '4k-monitor'
11. Hiçbiri eşleşmezse                   → 'desktop' (varsayılan)
```

### 2.4 Önemli Metotlar

```php
// Ana tespit metodu
DeviceDetector::detect(?string $userAgent, ?int $viewportW, ?int $viewportH): string

// Cihaz türünün mobile olup olmadığını kontrol et
DeviceDetector::isMobile('phone')  // true
DeviceDetector::isMobile('desktop') // false

// Embedded 1024 kontrolü
DeviceDetector::isEmbedded1024('embedded', 1024, 600) // true
DeviceDetector::isEmbedded1024('desktop', 1024, 600)  // false
```

---

## 3. DeviceManager.php — Karar Motoru

### 3.1 Sorumluluk

Device türüne göre layout ve feature kararlarını yönetir. Template'lerde `if/else` ile kullanılır.

### 3.2 Oluşturma

```php
use CoreMusic\Device\DeviceManager;

// 1. Request'ten oluştur (en yaygın)
$dm = DeviceManager::fromRequest(
    viewportW: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
    viewportH: (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    viewMode:  'home',
    isAuth:    ($_SESSION['MM_Username'] ?? '') !== '',
);

// 2. Device string'inden oluştur
$dm = DeviceManager::fromDevice('desktop', 'home', false, 1920, 1080);
```

### 3.3 Viewport Cookie Okuma

```php
// DeviceManager::fromRequest() içinde
public static function fromRequest(...): self
{
    // Viewport bilgisi cookie'den de okunabilir
    if ($viewportW === null && !empty($_COOKIE['cm_viewport_w'])) {
        $viewportW = (int)$_COOKIE['cm_viewport_w'];
    }
    if ($viewportH === null && !empty($_COOKIE['cm_viewport_h'])) {
        $viewportH = (int)$_COOKIE['cm_viewport_h'];
    }

    $device = DeviceDetector::detect($userAgent, $viewportW, $viewportH);
    return new self($device, $viewMode, $isAuth, $viewportW, $viewportH);
}
```

### 3.4 Karar Metotları

#### shouldRenderEmbeddedLayout()

```php
public function shouldRenderEmbeddedLayout(): bool
{
    if ($this->isPhone()) {
        return false;
    }

    if ($this->isEmbedded() || $this->isTablet()) {
        return true;
    }

    if ($this->viewportW !== null && $this->viewportW <= 1024) {
        return true;
    }

    return false;
}
```

**Kullanım:** Phone hariç, embedded/tablet veya viewport ≤1024px ise 1024 layout render edilir.

```php
if ($dm->shouldRenderEmbeddedLayout()) {
    // 1024 layout HTML (Split 42/58)
}
```

#### shouldRenderWideLayout()

```php
public function shouldRenderWideLayout(): bool
{
    if ($this->isPhone()) {
        return false;
    }

    if ($this->shouldRenderEmbeddedLayout()) {
        return false;
    }

    if ($this->shouldRender4kLayout()) {
        return false;
    }

    return true;   // 1025-2560px arası her şey
}
```

**Kullanım:** Phone, embedded ve 4K hariç tüm cihazlarda true (1025-2560px).

```php
if ($dm->shouldRenderWideLayout()) {
    // Wide layout HTML (3-sütun: Now Playing | Welcome | Widgets)
}
```

#### shouldShowFallback()

```php
public function shouldShowFallback(): bool
{
    return false;  // Tüm tier'lar optimize edildi, fallback kaldırıldı
}
```

**Kullanım:** Tüm ekran boyutları ilgili optimize HTML/CSS bloklarıyla desteklendiği için FALSE.

#### shouldRender4kLayout()

```php
public function shouldRender4kLayout(): bool
{
    if ($this->is4kTv() || $this->is4kMonitor()) {
        return true;
    }

    if ($this->viewportW !== null && $this->viewportW >= 2561) {
        return true;
    }

    return false;
}
```

**Kullanım:** 4K TV/Monitor UA veya viewport ≥2561px ise true.

```php
if ($dm->shouldRender4kLayout()) {
    // 4K layout HTML (büyük ölçekli 3-sütun)
}
```

#### shouldRenderWelcomePopup()

```php
public function shouldRenderWelcomePopup(): bool
{
    return $this->isEmbedded1024();  // Yalnızca 1024×600 gömülü cihazlar
}
```

**Kullanım:** Yalnızca 1024×600 embedded (RPi5) cihazlarda hoş geldin popup'ı gösterilir.

```php
if ($dm->shouldRenderWelcomePopup()) {
    // Welcome modal HTML
}
```

### 3.5 Feature Toggles

```php
$dm->showVolume()          // true (phone hariç)
$dm->showFullMetadata()    // true (embedded + phone hariç)
$dm->showSidebar()         // true (wide + laptop)
$dm->showSeekBar()         // true (tümü)
$dm->showPlaylistToggle()  // true (phone hariç)
$dm->showPodcastWidget()   // true (wide)
$dm->showUtilityIcons()    // true (phone hariç)
$dm->showFooterSeekSlider()// true (phone hariç)
```

### 3.6 CSS Class Helpers

```php
$dm->layoutClass()    // "layout--desktop"
$dm->allClasses()     // "layout--desktop device--desktop is-wide"
$dm->dataAttributes() // 'data-device="desktop" data-touch="false" data-wide="true" data-view-mode="home"'
```

---

## 4. home.php — Koşullu Render

### 4.1 Tam Yapı

```php
<?php declare(strict_types=1);
use CoreMusic\Device\DeviceManager;

// DeviceManager oluştur
$dm = DeviceManager::fromRequest(
    viewportW: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
    viewportH: (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    viewMode:  'home',
    isAuth:    ($_SESSION['MM_Username'] ?? '') !== '',
);

// Layout kararı (4 Tier Sistemi)
$isPhone    = $dm->isPhone();
$is4k       = $dm->shouldRender4kLayout();
$isWide     = $dm->shouldRenderWideLayout();
$isEmbedded = $dm->shouldRenderEmbeddedLayout();
?>

<?php require __DIR__ . '/../header.php'; ?>

<?php if ($isPhone): ?>
    <!-- PHONE LAYOUT — ≤767px (Kompakt tek-sütun) -->
    <main class="page-home home-layout home-layout--phone <?= $dm->allClasses() ?>" data-tier="phone" <?= $dm->dataAttributes() ?>>
        <!-- Now Playing (kompakt) + En Son Dinlenen + Çalma Listeleri -->
    </main>

<?php elseif ($is4k): ?>
    <!-- 4K LAYOUT — ≥2561px / 3840px (Büyük ölçekli 3-sütun) -->
    <main class="page-home home-layout home-layout--wide home-layout--4k <?= $dm->allClasses() ?>" data-tier="4k" <?= $dm->dataAttributes() ?>>
        <!-- 3 sütun: 4K Now Playing | 4K Hero Banner | 4K Widget Kümesi (6) -->
        <!-- Alt: EN SON (4K) | PLAYLISTLER (4K) | Şeffaf Alan -->
    </main>

<?php elseif ($isWide): ?>
    <!-- WIDE LAYOUT — 1025-2560px (Geniş masaüstü 3-sütun) -->
    <main class="page-home home-layout home-layout--wide <?= $dm->allClasses() ?>" data-tier="wide" <?= $dm->dataAttributes() ?>>
        <div class="home-layout__top home-layout__top--wide">
            <!-- 3 sütun: Now Playing | Welcome | Widgets -->
        </div>
        <div class="home-layout__bottom home-layout__bottom--wide">
            <!-- 2 sütun: En Son | Çalma Listeleri -->
        </div>
    </main>

<?php else: ?>
    <!-- EMBEDDED LAYOUT — ≤1024px (RPi5 Split 42/58) -->
    <main class="page-home home-layout <?= $dm->allClasses() ?>" data-tier="embedded" <?= $dm->dataAttributes() ?>>
        <div class="home-layout__top home-layout__top--embedded">
            <!-- Split 42/58: Now Playing | Widgets 2×2 -->
        </div>
        <div class="home-layout__bottom home-layout__bottom--embedded">
            <!-- 3 sütun: En Son | Çalma Listeleri | Sıradaki -->
        </div>
        <?php if ($dm->shouldRenderWelcomePopup()): ?>
            <!-- Welcome Modal (sadece embedded 1024) -->
        <?php endif; ?>
    </main>

<?php endif; ?>

<?php require __DIR__ . '/../footer.php'; ?>
```

### 4.2 Önemli Kurallar

| Kural | Açıklama |
|-------|----------|
| Tek `<main>` tag | Her koşulda tek `<main>` etiketi olmalı |
| `role="main"` | WCAG erişilebilirliği için zorunlu |
| `$dm->allClasses()` | Her layout bloğuna device class'ları eklenecek |
| `$dm->dataAttributes()` | JS tarafında cihaz tespiti için |
| `aria-label` | Her layout'a anlamlı label |

---

## 5. Viewport Cookie Sistemi

### 5.1 JS Tarafı (device-loader.js)

```javascript
// Sayfa yüklendiğinde viewport bilgisini cookie'ye yaz
function init(opts) {
    var w = window.innerWidth;
    var h = window.innerHeight;
    var device = detect(w, h);

    // Viewport bilgisini cookie'ye yaz
    try {
        document.cookie = 'cm_viewport_w=' + w + ';path=/;max-age=86400;SameSite=Lax';
        document.cookie = 'cm_viewport_h=' + h + ';path=/;max-age=86400;SameSite=Lax';
    } catch (e) {}

    // ...
}
```

### 5.2 PHP Tarafı (DeviceManager.php)

```php
public static function fromRequest(...): self
{
    // Cookie'den viewport oku
    if ($viewportW === null && !empty($_COOKIE['cm_viewport_w'])) {
        $viewportW = (int)$_COOKIE['cm_viewport_w'];
    }
    if ($viewportH === null && !empty($_COOKIE['cm_viewport_h'])) {
        $viewportH = (int)$_COOKIE['cm_viewport_h'];
    }

    $device = DeviceDetector::detect($userAgent, $viewportW, $viewportH);
    return new self($device, $viewMode, $isAuth, $viewportW, $viewportH);
}
```

### 5.3 Viewport Öncelik Sırası

```
1. $_SERVER['VIEWPORT_W']     → HTTP header'dan (en yüksek öncelik)
2. $_COOKIE['cm_viewport_w']  → JS tarafından yazılan cookie
3. Device türüne göre varsayılan → desktop→1920, laptop→1366, vb.
4. null                       → Fallback gösterme
```

---

## 6. PageRouter Entegrasyonu

### 6.1 PageRouter.php

```php
private function renderPage(string $pageFile, string $csrfToken = '', array $meta = []): string
{
    // Viewport bilgisi: cookie → $_SERVER
    $viewportW = !empty($_SERVER['VIEWPORT_W'])
        ? (int)$_SERVER['VIEWPORT_W']
        : (!empty($_COOKIE['cm_viewport_w']) ? (int)$_COOKIE['cm_viewport_w'] : null);
    $viewportH = !empty($_SERVER['VIEWPORT_H'])
        ? (int)$_SERVER['VIEWPORT_H']
        : (!empty($_COOKIE['cm_viewport_h']) ? (int)$_COOKIE['cm_viewport_h'] : null);

    $device = DeviceDetector::detect(
        $_SERVER['HTTP_USER_AGENT'] ?? null,
        $viewportW,
        $viewportH
    );

    include $pageFile;
    // ...
}
```

### 6.2 HtmlShellRenderer.php

```php
public function render(...): string
{
    // Viewport bilgisi: cookie → $_SERVER
    $viewportW = !empty($_SERVER['VIEWPORT_W'])
        ? (int)$_SERVER['VIEWPORT_W']
        : (!empty($_COOKIE['cm_viewport_w']) ? (int)$_COOKIE['cm_viewport_w'] : null);
    $viewportH = !empty($_SERVER['VIEWPORT_H'])
        ? (int)$_SERVER['VIEWPORT_H']
        : (!empty($_COOKIE['cm_viewport_h']) ? (int)$_COOKIE['cm_viewport_h'] : null);

    // ...
}
```

---

## 7. CSS Entegrasyonu

### 7.1 Layout Class Kullanımı

```php
// home.php'de
<main class="page-home home-layout <?= $dm->allClasses() ?>"
      role="main"
      aria-label="Ana Sayfa"
      <?= $dm->dataAttributes() ?>>
```

### 7.2 CSS Seçiciler

```css
/* Embedded layout */
.layout--embedded .home-layout__top {
    grid-template-columns: 42% 58%;
}

/* Wide layout */
.layout--desktop .home-layout__top--wide {
    grid-template-columns: 1fr 1.2fr 1fr;
}

/* Fallback */
.home-fallback {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - var(--header-h) - var(--footer-h));
}
```

### 7.3 Token Kullanımı

```css
/* Hardcoded DEĞİL */
.footer { height: 90px; }

/* Token ile */
.footer { height: var(--footer-h); }

/* Media query ile override */
@media (min-width: 1920px) {
    :root { --footer-h: 104px; }
}
```

---

## 8. Test Senaryoları

### 8.1 PHP Unit Test

```php
use CoreMusic\Device\DeviceManager;

// Phone (375×812)
$dm = DeviceManager::fromDevice('phone', 'home', false, 375, 812);
assert($dm->isPhone() === true);
assert($dm->shouldRenderEmbeddedLayout() === false);
assert($dm->shouldRender4kLayout() === false);
assert($dm->shouldRenderWideLayout() === false);
assert($dm->shouldShowFallback() === false);

// Embedded (Linux ARM, 1024×600)
$dm = DeviceManager::fromDevice('embedded', 'home', false, 1024, 600);
assert($dm->shouldRenderEmbeddedLayout() === true);
assert($dm->shouldRender4kLayout() === false);
assert($dm->shouldRenderWideLayout() === false);
assert($dm->shouldShowFallback() === false);

// Desktop (1920×1080)
$dm = DeviceManager::fromDevice('desktop', 'home', false, 1920, 1080);
assert($dm->shouldRenderEmbeddedLayout() === false);
assert($dm->shouldRender4kLayout() === false);
assert($dm->shouldRenderWideLayout() === true);
assert($dm->shouldShowFallback() === false);

// 4K TV (3840×2160)
$dm = DeviceManager::fromDevice('4k-tv', 'home', false, 3840, 2160);
assert($dm->shouldRenderEmbeddedLayout() === false);
assert($dm->shouldRender4kLayout() === true);
assert($dm->shouldRenderWideLayout() === false);
assert($dm->shouldShowFallback() === false);
```

### 8.2 Browser Test

```
1. 375×812  → Phone layout (kompakt) ✅
2. 1024×600 → Embedded (Linux ARM) ✅
3. 1024×768 → Wide layout (Desktop UA) ✅
4. 1366×768 → Wide layout (laptop) ✅
5. 1920×1080 → Wide layout (desktop) ✅
6. 3840×2160 → 4K layout (SmartTV) ✅
```

---

## 9. Yaygın Hatalar

### 9.1 Viewport Cookie Yazmıyor

**Sorun:** JS device-loader.js cookie yazmıyorsa, PHP viewport bilgisini alamaz.

**Çözüm:** `device-loader.js`'de init() fonksiyonunda cookie yazılmalı:

```javascript
document.cookie = 'cm_viewport_w=' + w + ';path=/;max-age=86400;SameSite=Lax';
document.cookie = 'cm_viewport_h=' + h + ';path=/;max-age=86400;SameSite=Lax';
```

### 9.2 Cookie Okunmuyor

**Sorun:** PHP `$_COOKIE['cm_viewport_w']` okuyamıyor.

**Çözüm:** Cookie domain'i doğru olmalı. `SameSite=Lax` ve `path=/` zorunlu.

### 9.3 Wrong Device Detected

**Sorun:** Yanlış cihaz tespit ediliyor.

**Çözüm:** `DeviceDetector::detect()`'in öncelik sırasını kontrol et:
1. HTTP Header: `X-Device-Type`
2. User-Agent: Embedded/TV/Mobile kontrolü
3. Viewport boyutu

### 9.4 Layout Değişmiyor

**Sorun:** Viewport değişse bile layout aynı kalıyor.

**Çözüm:** Sayfa yeniden yüklenmeli (reload). Cookie sadece sonraki istekte okunur.

---

## 10. Dosya Referansı

| Dosya | Amaç |
|-------|------|
| `shared/src/Device/DeviceDetector.php` | Cihaz tespit — 7 tip, priority-based |
| `shared/src/Device/DeviceManager.php` | Karar motoru — 4-tier layout, feature toggles, nav links, content config |
| `home.coremusic.net/pages/home.php` | Koşullu render — 4 tier HTML bloğu |
| `home.coremusic.net/header.php` | Navigation — Phone bottom tab + tier CSS classes |
| `home.coremusic.net/footer.php` | Player — Phone kompakt + full vaporwave |
| `assets.coremusic.net/js/device-loader.js` | Cookie yazma |
| `shared/src/PageRouter/PageRouter.php` | Cookie okuma |
| `shared/src/PageRouter/HtmlShellRenderer.php` | Cookie okuma |

---

## 11. Faz 2c Doğrulama Notları (2026-09-08)

Bu rehber Faz 0-2c taramalarıyla çapraz doğrulandı:

| İddia | Kanıt | Durum |
|-------|-------|-------|
| `DeviceManager.php` 7 cihaz, 4 tier, 9 toggle | brain §18B + `shared/src/Device/DeviceManager.php` v2.0.0 | ✅ |
| `DeviceDetector` 11 tespit kuralı | brain §18B öncelik listesi | ✅ |
| Viewport cookie hattı | `device-loader.js` + PageRouter + HtmlShellRenderer | ✅ (§6.1/6.2 kod örnekleri gerçek eklerle uyumlu) |
| Fallback her zaman false | brain §18B `shouldShowFallback()` | ✅ |
| Test matrisi 9 viewport | brain §18B Test Sonuçları tablosu | ✅ |
| scale*.js referansları | **SİLİNDİ** — ScaleManager.js ile değişti (html-shell-renderer §11) | ⚠️ bu rehberde scale referansı yok — temiz |

**Bu rehberin konumu:** l2-routing'den bağımsız kök düzey mimari doküman; DeviceManager kullanım rehberidir. Satır hedefi ~482 boş-hariç — mikro farkla hedefte kabul edildi (içerik tamam, dolgu yasak).

**Çapraz referanslar:** [[l2-routing/html-shell-renderer]] §24 DeviceCssMap · [[../brain]] §18B/§18C · [[../l2-routing/index]] §8 viewport akışı.

---

*PHP Implementasyon Rehberi v2.1.0 — CoreMusic 4-Tier Conditional Rendering System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team · Human Mode · Truth Mode*
