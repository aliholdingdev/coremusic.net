---
type: architecture
category: l3
title: "Yeni Cihaz ve Çözünürlük Breakpoint Ekleme Rehberi"
date: 2026-09-06
updated: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Yeni Cihaz ve Çözünürlük Breakpoint Ekleme Rehberi

**Zorunlu Bağlantılar:** [[scale-router-css-frontend-guide]] · [[device-css]] · [[itcss-architecture]] · [[ADR-001-vanilla-js-itcss]] · [[00-mockup-index]]

---

## 1. Amaç

Bu belge, CoreMusic sistemine **yeni bir cihaz türü** (örn. `car-display`, `8k-tv`) veya mevcut cihazlara **yeni bir çözünürlük breakpoint'i** eklemenin kanonik adımlarını tanımlar. Tüm değerler kaynak koddan doğrulanmıştır (Zero Hallucination).

> **Kritik Kural:** Cihaz sistemi **13 noktalı senkron zinciridir**. Tek bir dosyayı atlamak tier-sync reload döngüsü, CSS yükleme hatası veya HTML/JS uyumsuzluğu üretir.

---

## 2. SSOT Zinciri (Doğrulanmış Akış)

```
                    ┌─────────────────────────────┐
                    │  Breakpoint Değerleri (px)   │
                    │  TEK GERÇEK: sayısal sınırlar│
                    └──────────┬──────────────────┘
                               │ senkron
        ┌──────────────────────┼──────────────────────────┐
        ▼                      ▼                          ▼
┌───────────────┐    ┌─────────────────┐    ┌──────────────────────┐
│ a-breakpoint- │    │ device-loader.js│    │ DeviceManager.js (JS)│
│ tokens.css    │    │ BP + detect()   │    │ BREAKPOINTS+#detect()│
│ (CSS sabitleri│    │ + getTier()     │    │ (EventBus bridge)    │
│  + JS int)    │    └────────┬────────┘    └──────────┬───────────┘
└───────────────┘             │                        │
                              ▼                        │
                   ┌─────────────────────┐            │
                   │ devices.config.js   │◄───────────┘
                   │ (CSS dosya SSOT)    │  devices.config.js okur
                   └──────────┬──────────┘
                              │ senkron
                   ┌──────────▼──────────┐
                   │ DeviceCssMap.php    │  ← PHP tarafı birebir aynısı
                   └──────────┬──────────┘
                              │
              ┌───────────────┼────────────────────┐
              ▼               ▼                    ▼
   ┌─────────────────┐ ┌───────────────┐ ┌──────────────────────┐
   │ DeviceDetector  │ │DeviceManager  │ │ device-layout-       │
   │ .php (tespit)   │ │.php (NAV_LINKS│ │ updater.js (DOM +    │
   │                 │ │ + davranış)   │ │ DEVICE_LAYOUT)       │
   └─────────────────┘ └───────────────┘ └──────────────────────┘
```

**Tier kavramı (doğrulanmış):** `getTier()` → `'phone' | 'embedded' | 'wide'`.
- `device-loader.js`: 4k-tv / 4k-monitor → `'wide'` (tier-sync reload döngüsü önlenir)
- `device-layout-updater.js`: aynı 3 tier; 4K için `'wide'` + `home-layout--4k`
- Sunucu: `main[data-tier]` attribute'u; uyuşmazlıkta max 2 deneme ile `Router.navigate(pathname)` veya `location.reload()` (sessionStorage `cm_tier_sync_count`)

**Mevcut cihaz sınırları (doğrulanmış):**

| Cihaz | Aralık | Tier |
|-------|--------|------|
| phone | ≤767px | phone |
| tablet | 768–1024px (h > 600) | embedded |
| embedded | ≤1024px (h ≤ 600, RPi5) | embedded |
| laptop | 1025–1440px | wide |
| desktop | 1441–2560px | wide |
| 4k-tv | 2561–3840px | wide |
| 4k-monitor | ≥3841px | wide |

---

## 3. Zorunlu Senkronizasyon Tablosu (13 Nokta)

| # | Dosya | Ne Eklenir | SSOT Tipi |
|---|-------|-----------|-----------|
| 1 | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | `--bp-device-{ad}: {N}px` + `--bp-{ad}: {N}` | CSS sabit |
| 2 | `assets.coremusic.net/js/device-loader.js` | `BP` sabiti + `detect()` kuralı + `getTier()` kuralı | JS tespit |
| 3 | `assets.coremusic.net/js/devices.config.js` | `HOME_CSS` + `AUTH_CSS` + `ALL` dizisi | CSS dosya haritası (JS SSOT) |
| 4 | `shared/src/Device/DeviceCssMap.php` | `DEVICE_CSS` + `AUTH_DEVICE_CSS` | CSS dosya haritası (PHP SSOT) |
| 5 | `shared/src/Device/DeviceDetector.php` | Tespit kuralı (viewport/UA) | PHP tespit |
| 6 | `shared/src/Device/DeviceManager.php` | Sabit + `ALL_DEVICES` + `NAV_LINKS` + davranış metotları | PHP davranış |
| 7 | `assets.coremusic.net/js/device-layout-updater.js` | `DEVICE_LAYOUT` + `NAV_LINKS` + tier dalları + art boyutu | JS DOM davranış |
| 8 | `assets.coremusic.net/js/managers/DeviceManager.js` | `BREAKPOINTS` + `#detect()` | JS bridge |
| 9 | `assets.coremusic.net/Css/01_Abstracts/a-layout-tokens.css` | `@media` token override bloğu | CSS token |
| 10 | `assets.coremusic.net/Css/01_Abstracts/a-scale-hybrid.css` | `@media` bileşen boyutları (`--btn-size-*`, `--gap-*`) | CSS token |
| 11 | `assets.coremusic.net/js/managers/ScaleManager.js` | `DEFAULT_SCALE_TARGETS` kuralları veya bilinçli `skip: true` | JS ölçek |
| 12 | `assets.coremusic.net/Css/08_Devices/d-{ad}.css` | YENİ dosya (self-contained import) | CSS cihaz |
| 13 | `assets.coremusic.net/Css/08_Devices/d-auth-{ad}.css` | YENİ dosya (home import ETMEZ) | CSS cihaz-auth |

**Ek doğrulama noktaları:** `DeviceDetectorTest.php` (PHPUnit profil testleri), DevTools canlı test.

---

## 4. Adım Adım Yeni Cihaz Ekleme (Kod Şablonlarıyla)

> Örnek: `8k-tv` cihazı, ≥7681px, tier `wide`. Tüm şablonlarda `{AD}` yerine cihaz adını, `{N}` yerine üst sınırı yazın.

### Adım 1 — `a-breakpoint-tokens.css`

```css
/* DEVICE BREAKPOINTS (px cinsinden — media query için) */
--bp-device-8k-tv:      7680px;   /* ÜST SINIR */

/* DEVICE BREAKPOINTS (integer — JS hesaplama için) */
--bp-8k-tv:      7680;
```

### Adım 2 — `device-loader.js`

```javascript
/* BREAKPOINT CONSTANTS — a-breakpoint-tokens.css ile senkronize */
const BP = {
    /* ... mevcut satırlar KORUNUR ... */
    EIGHT_K_TV_MAX: 7680,          // YENİ
};

/* detect() — mevcut 4k-monitor kontrolünün ÖNCESİNE ekle
   (küçükten büyüğe sıralı kontrol zinciri korunmalı) */
if (w <= BP.EIGHT_K_TV_MAX) {
    if (/Tizen|Web0S|webOS|SmartTV|BRAVIA|NetCast|AppleTV|Android TV|GoogleTV|HbbTV|Roku/i.test(ua)) {
        return '8k-tv';
    }
    return '8k-tv';
}

/* getTier() — 4k-tv/4k-monitor satırına ekle (wide'a map):
   if (device === '4k-tv' || device === '4k-monitor' || device === '8k-tv' || (w && w > BP.DESKTOP_MAX)) return 'wide';
   ⚠️ 8K için ölçek d-4k.css Kademe 3 (zoom ×3, ≥7680px) zaten mevcut.
   Yeni cihaz d-4k.css paylaşacaksa zoom şablonu eklemeyin. */
```

**Not (doğrulanmış):** `d-4k.css` ≥7680px için `zoom: var(--cm-scale-step-3)` içerir. 8K cihazı d-4k.css ile paylaşmak tier-sync döngüsünü önler (4K ssot birleşiminin aynısı).

### Adım 3 — `devices.config.js`

```javascript
HOME_CSS: Object.freeze({
    /* ... mevcut satırlar KORUNUR ... */
    '8k-tv':      '08_Devices/d-4k.css',   // ÖNERİ: d-4k.css paylaş
    // VEYA tamamen yeni dosya: '08_Devices/d-8k-tv.css'
}),
AUTH_CSS: Object.freeze({
    /* ... */
    '8k-tv':      '08_Devices/d-auth-8k-tv.css',   // Auth için ayrı dosya ZORUNLU
}),
ALL: Object.freeze(['embedded', 'phone', 'tablet', 'laptop', 'desktop', '4k-tv', '4k-monitor', '8k-tv']),
```

**Kural:** `Object.freeze` kırılmaz; dizi sırası `device-loader.js` `ALL_DEVICES.indexOf(opts.serverDevice)` kontrolünde kullanılır.

### Adım 4 — `DeviceCssMap.php`

```php
private const DEVICE_CSS = [
    /* ... */
    '8k-tv'      => '08_Devices/d-4k.css',
];

private const AUTH_DEVICE_CSS = [
    /* ... */
    '8k-tv'      => '08_Devices/d-auth-8k-tv.css',
];
```

**Kural:** `devices.config.js` ve `DeviceCssMap.php` birebir aynı yolaları işaret etmeli (çift SSOT, tek gerçek değer).

### Adım 5 — `DeviceDetector.php`

Tespit kuralını viewport/UA mantığına ekleyin. Mevcut imza (doğrulanmış): `DeviceDetector::detect(?string $userAgent, ?int $viewportW, ?int $viewportH): string`.
**VERIFICATION REQUIRED:** Bu dosyanın içeriği bu oturumda satır satır okunmadı; ekleme yapmadan önce dosyayı okuyun ve mevcut aralık sırasını bozmayın.

### Adım 6 — `DeviceManager.php`

```php
/* SABİTLER */
public const EIGHT_K_TV = '8k-tv';

/* ALL_DEVICES dizisine ekle */
private const ALL_DEVICES = [
    self::EMBEDDED, self::PHONE, self::TABLET,
    self::LAPTOP, self::DESKTOP, self::FOUR_K_TV, self::FOUR_K_MON,
    self::EIGHT_K_TV,                       // YENİ
];

/* NAV_LINKS — 4k-monitor kopyası (8 link) */
self::EIGHT_K_TV => [
    ['href' => '/home',       'label' => 'Ana Sayfa',  'active' => true],
    ['href' => '/kesfet',     'label' => 'Keşfet',     'active' => false],
    ['href' => '/albumler',   'label' => 'Albümler',   'active' => false],
    ['href' => '/sanatcilar', 'label' => 'Sanatçılar', 'active' => false],
    ['href' => '/goz-at',     'label' => 'Göz At',     'active' => false],
    ['href' => '/gecmis',     'label' => 'Geçmiş',     'active' => false],
    ['href' => '/ayarlar',    'label' => 'Ayarlar',    'active' => false],
    ['href' => '/hakkimizda', 'label' => 'Hakkımızda', 'active' => false],
],

/* Davranış metotları: is8kTv(), isTv() güncellemesi,
   widgetCount()/recentCardCount() vb. için profil ekle.
   ⚠️ PHP'de margin/padding/width/height/font-size KESİNLİKLE YASAK
   (brain.md §18C) — sadece davranışsal konfigürasyon (sayı/bool). */
```

### Adım 7 — `device-layout-updater.js`

```javascript
/* DEVICE_LAYOUT — desktop değerlerini kopyala, cihaza göre ayarla */
'8k-tv': {
    navLinks: 8, widgets: 6, recentCards: 8, playlists: 6, upNext: 8,
    showVolume: true, showFullMeta: true, showBattery: true,
    showSettingsBtn: true, showLogoutBtn: true,
},

/* NAV_LINKS — DeviceManager.php NAV_LINKS ile BİREBİR aynı */
'8k-tv': [ /* yukarıdaki 8 link ile aynı href/label */ ],

/* getTier(): if (device === '8k-tv') return 'wide'; satırı ekle */

/* updateLayoutClasses(): regex'e cihaz adını ekle
   site-header--(…|4k-monitor|8k-tv|…) ve footer--(…) regex'leri güncelle */

/* updateFooter(): album art boyutu (doğrulanmış tabloya ekle)
   embedded: 80 | 4k-tv: 140 | desktop: 120 | diğer: 100 */
```

### Adım 8 — `managers/DeviceManager.js`

```javascript
static BREAKPOINTS = {
    /* ... */
    EIGHT_K_TV_MAX: 7680,        // YENİ
};

/* #detect() — 4k-monitor dönüşünden ÖNCE:
   if (w <= BP.EIGHT_K_TV_MAX) return '8k-tv';
   return '4k-monitor'; */
```

### Adım 9 — `a-layout-tokens.css`

```css
/* 8K — ≥7681px (d-4k.css zoom ×3 ile birlikte çalışır) */
@media (min-width: 7681px) {
  :root {
    /* d-4k.css Kademe 3 zoom'u layout'u zaten ×3 ölçekler.
       Sadece zoom'un kapsamadığı token'lar override edilir. */
    --touch-min: 60px;
    --touch-recommended: 64px;
    --spacing-scale: 1.8;
  }
}
```

**Kural:** Mevcut `:root` default'ları (RPi5 1024×600) ASLA değiştirilmez; sadece yeni `@media` bloğu eklenir (geriye dönük uyumluluk).

### Adım 10 — `a-scale-hybrid.css`

```css
/* 8K TV — ≥7681px */
@media (min-width: 7681px) {
  :root {
    --btn-size-xs: 63px;  --btn-size-sm: 71px;
    --btn-size-md: 83px;  --btn-size-lg: 95px;
    --icon-size-md: 28px; --icon-size-lg: 36px;
    --gap-md: 16px;       --gap-lg: 24px;
    --quick-controls-display: flex;
  }
}
```

### Adım 11 — `ScaleManager.js`

```javascript
/* YÖNTEM A (önerilen): d-4k.css zoom ile paylaş
   — header/footer/home kurallarında skip aralığını genişlet:
{ min: 2561, skip: true },   // ≥2561px: yerel CSS (d-4k.css Kademe 2/3) yönetir

/* YÖNTEM B: bağımsız ölçek gerekliyse — YENİ kural, eski kurallara dokunmadan:
   { min: 7681, max: 9999, from: 1.00, to: 1.30 },   // lineer enterpolasyon örneği
   Kural sırası ÖNEMLİDİR: ScaleCalculator İLK eşleşen kuralda durur. */
```

### Adım 12 — `d-{ad}.css` / `d-auth-{ad}.css` oluşturma

```css
/**
 * CoreMusic — {AD} Device CSS
 * ITCSS Layer: 08_Devices — SELF-CONTAINED (tüm zinciri import eder)
 * Version: 1.0.0 — {TARİH}
 * Sözleşme zinciri: device-loader.js → devices.config.js → bu dosya
 */
@import '../01_Abstracts/a-theme-config.css';
@import '../01_Abstracts/a-colors-token.css';
@import '../01_Abstracts/a-semantic-token.css';
@import '../01_Abstracts/a-breakpoint-tokens.css';
@import '../01_Abstracts/a-layout-tokens.css';
@import '../01_Abstracts/a-fonts-token.css';
@import '../01_Abstracts/a-scale-hybrid.css';
@import '../02_Base/b-base-core.css';
@import '../03_Layout/_header.css';
@import '../03_Layout/_footer.css';
@import '../05_Pages/_home.css';

/* SADECE behavioral override'lar (hover, touch, scrollbar).
   Token/sunum kararları 01_Abstracts media query'lerindedir. */
```

**Auth varyantı:** `_home.css` import EDİLMEZ; `auth-bundled.css` sonrası override olarak yüklenir (doğrulanmış akış: `device-loader.js` → `loadCSS(base + 'auth-bundled.css')` SONRA `AUTH_CSS[device]`).

### Adım 13 — Doğrulama (ZORUNLU)

1. `php -l` → tüm değişen PHP dosyaları.
2. PHPUnit → `DeviceDetectorTest.php` (yeni profil testi ekleyin; mevcut 13 test/49 assertion tabanını bozmayın).
3. DevTools canlı test matrisi (min.): `{N}px`, `{N}±1px`, komşu cihaz sınırı, tier değişimi (devicechange event + `data-device` + `data-tier`).
4. Console: 0 hata / 0 uyarı; tier-sync reload döngüsü yok (`cm_tier_sync_count` kontrolü).
5. CSS: `#cm-device-css` link `href` doğru dosyayı işaret ediyor mu.

---

## 5. Sadece Yeni Breakpoint Ekleme (Yeni Cihaz YOK)

Mevcut bir cihazın içine ince ayar breakpoint'i eklerken **1, 9, 10. adımlar yeterli**; JS cihaz tespiti DEĞİŞMEZ:

```css
/* Örnek: laptop içi ince ayar — 1280-1366px (a-layout-tokens.css) */
@media (min-width: 1280px) and (max-width: 1366px) {
  :root {
    --card-thumb-size: 165px;
    --grid-gap: 10px;
  }
}
```

**Kurallar:**
- `device-loader.js` `BP` sabitleri yalnızca CİHAZ sınırları içindir; aradaki ince ayarlar JS'e girmez.
- Yeni `@media` bloğu mevcut bloklardan SONRA yer alır (CSS kaskad: en son kazanır).
- `:root` default bloğu hiçbir zaman düzenlenmez.

---

## 6. Geriye Dönük Uyumluluk Kuralları (ZORUNLU)

1. **Mevcut cihaz adları ve aralıkları değiştirilemez** — yalnızca ekleme yapılır (`ALL` dizisi sona eklenir).
2. **`Object.freeze` yapıları değiştirilmez**, yalnızca içerik genişletilir.
3. **Eski `--header-h-4k` vb. backward-compat token'ları silinmez** (`a-layout-tokens.css` v3.1.0'da "backward compat" işaretliler).
4. **`window.scaleHeaderForScreen` / `scaleFooterForScreen` / `scaleHomeForScreen` / `window.ScaleCoordinator` legacy köprüleri korunur** (ScaleManager v7 `#registerGlobalBridge`).
5. **`cm_viewport_w/h` cookie adları korunur** — PHP `DeviceManager::fromRequest()` bu cookie'leri okur.
6. **Tier kümesi (phone|embedded|wide) değiştirilmez** — sunucu `data-tier` sözleşmesi buna bağlıdır.
7. **4K deseni:** yeni üst-tier cihazlar önce `d-4k.css` paylaşmayı denemelidir (ayrı dosya yalnızca davranış farkı kanıtlandığında).

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Senkron Noktası** | 13 |
| **Doğrulanmış Kaynak** | device-loader.js, devices.config.js, DeviceCssMap.php, DeviceManager.php (PHP+JS), device-layout-updater.js, a-breakpoint-tokens.css, a-layout-tokens.css, a-scale-hybrid.css, ScaleManager.js, d-4k.css |
| **Zero Hallucination** | ✅ (Adım 5 hariç — VERIFICATION REQUIRED işaretli) |
| **ADR Uyumlu** | ✅ ADR-001 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
**Mode:** Red Team · Human Mode · Truth Mode
