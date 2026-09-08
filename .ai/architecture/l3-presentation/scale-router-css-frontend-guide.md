---
type: architecture
category: l3
title: "Scale, Router, CSS & Frontend Entegrasyon Rehberi"
date: 2026-09-06
updated: 2026-09-06
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Scale, Router, CSS & Frontend Entegrasyon Rehberi

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]] · [[ADR-045-multi-domain-view-mode-architecture]] · [[ADR-044-dynamic-user-theme-engine]]

---

## 1. Amaç

Bu belge, CoreMusic frontend sisteminin **tamamını** adım adım anlatır. Scale sistemi, sayfa router sistemi, CSS mimarisi ve cihaz/tema entegrasyonu nasıl çalışır, nasıl kullanılır — hepsi bu dosyadadır.

**Hedef kitle:** Sistemi anlamak ve geliştirmek isteyen mühendisler.

---

## 2. Sistem Genel Bakış

```
┌─────────────────────────────────────────────────────────────────┐
│                        TARAYICI (Browser)                        │
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐   │
│  │ DeviceLoader │  │ ScaleManager │  │     SPA Router       │   │
│  │ (device-loader│  │ (ScaleManager│  │   (Router.js +       │   │
│  │   .js)       │  │   .js)       │  │  NavigationOrch.)    │   │
│  └──────┬───────┘  └──────┬───────┘  └──────────┬───────────┘   │
│         │                 │                      │                │
│         ▼                 ▼                      ▼                │
│  ┌──────────────────────────────────────────────────────────┐    │
│  │                    CSS Sistemi (ITCSS)                    │    │
│  │  01_Abstracts → 02_Base → 03_Layout → 04_Components      │    │
│  │  → 05_Pages → 06_Utilities → 08_Devices → 09_ViewModes   │    │
│  └──────────────────────────────────────────────────────────┘    │
│         │                 │                      │                │
│         ▼                 ▼                      ▼                │
│  ┌──────────────────────────────────────────────────────────┐    │
│  │              HTML (DOM) — #main-content                   │    │
│  │  <html data-gender="female" data-mode="dark">            │    │
│  │  <body data-device="embedded">                           │    │
│  │    <header class="layout layout--embedded layout--touch">│    │
│  │    <main id="main-content" data-tier="embedded">         │    │
│  │    <footer class="footer footer--embedded">              │    │
│  └──────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
         ▲
         │  İlk yükleme: PHP HTML shell üretir
         │
┌────────┴────────────────────────────────────────────────────────┐
│                   SUNUCU (PHP Backend)                          │
│                                                                 │
│  ┌─────────────────┐  ┌────────────────┐  ┌─────────────────┐  │
│  │ PageRouterKernel │  │HtmlShellRenderer│  │ DeviceManager   │  │
│  │ (talep işler)    │  │ (HTML shell)   │  │ (cihaz tespiti) │  │
│  └────────┬────────┘  └───────┬────────┘  └────────┬────────┘  │
│           │                   │                     │            │
│           ▼                   ▼                     ▼            │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  DeviceDetector + ThemeManager + ViewModeManager          │   │
│  │  → <html data-gender="..." data-mode="..." data-device>  │   │
│  │  → <link href="d-{device}.css">                          │   │
│  │  → <link href="v-{viewMode}.css">                        │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. SCALE SİSTEMİ (Hibrit Ölçeklendirme)

### 3.1 Nedir?

Scale sistemi, farklı ekran boyutlarında (1024px RPi5'den 3840px 4K TV'ye kadar) sayfanın **orantılı** görünmesini sağlar. Üç katmanlı bir sistemdir:

| Katman | Teknoloji | Sorumluluk |
|--------|-----------|------------|
| **Layer 1** | JS `transform:scale` | Sayfa bölümlerinin genel ölçeklemesi (header, footer, home) |
| **Layer 2** | CSS custom properties | Bireysel element boyutları (font, buton, boşluk) |
| **Layer 3** | CSS `zoom` | 4K TV/Monitor için fallback (≥3840px) |

### 3.2 Layer 1: JS Transform:Scale (ScaleManager.js)

**Dosya:** `assets.coremusic.net/js/managers/ScaleManager.js`

ScaleManager, header, footer ve home bölümlerine `transform: scale()` uygular. Bu, sayfanın **tamamını** değil, sadece belirli bölümleri ölçeklendirir.

#### Scale Hedefleri

```javascript
// ScaleManager.js v7 — DOĞRULANMIŞ kurallar (kaynak: DEFAULT_SCALE_TARGETS)
// Kural tipi: {max} = üst sınır | {min,max} = aralık | {from,to} = lineer | {skip} = CSS'e devret
// Kural sırası ÖNEMLİDİR: ScaleCalculator İLK eşleşen kuralda durur.
DEFAULT_SCALE_TARGETS = [
  {
    name: 'header',
    selector: '.site-header',
    origin: 'top left',
    compensateWidth: true,
    customProp: '--scale-header',
    rules: [
      { max: 767, scale: 0.75 },                 // Phone
      { max: 1024, scale: 0.88 },                // Embedded/Tablet
      { min: 1025, max: 1919, from: 0.65, to: 1.00 }, // Laptop: lineer
      { min: 1920, max: 1920, scale: 1.00 },     // FHD tam boyut
      { min: 1921, max: 2560, scale: 1.00 },     // Desktop
      { min: 2561, skip: true },                 // ≥2561: d-4k.css (CSS zoom) yönetir
    ],
    nestedChildren: [
      { selector: '.header-widget--battery', origin: 'center right' },
      { selector: '.site-header__actions', origin: 'center right' }
    ]
  },
  {
    name: 'footer',
    selector: '.footer, .player-footer',
    origin: 'bottom left',
    compensateWidth: true,
    customProp: '--scale-footer',
    rules: [
      { max: 767, scale: 0.60 },                 // Phone
      { max: 1024, scale: 0.70 },                // Embedded/Tablet
      { min: 1025, max: 1368, maxH: 768, scale: 0.65 }, // Kısa ekran (h≤768)
      { min: 1369, max: 1520, maxH: 768, scale: 0.65 },
      { min: 1025, max: 1920, scale: 0.65 },     // Standart
      { min: 1921, max: 2560, from: 0.65, to: 1.00 }, // Desktop: lineer
      { min: 2561, skip: true },                 // ≥2561: yerel CSS (120/130px)
    ],
    nestedChildren: [
      { selector: '.c-footer__seek-slider', origin: 'top center' },
      { selector: '.footer__utility-icons', origin: 'center right' },
      { selector: '.volume-set-slider', origin: 'center right' }
    ]
  },
  {
    name: 'home',
    selector: '.page-home',
    origin: 'top left',
    compensateWidth: true,
    toScreenWidth: true,
    customProp: '--scale-home',
    rules: [
      { max: 1024, skip: true },                 // Embedded/Tablet: CSS flex/grid yönetir
      { min: 1025, max: 2560, from: 0.65, to: 1.00 }, // Laptop→Desktop: lineer
      { min: 2561, skip: true },                 // ≥2561: 4K yerel CSS
    ]
  }
];
```

#### Nasıl Çalışır?

1. **Viewport değişikliği algılanır** → `ResizeObserver` + `window.resize` event
2. **Debounce** → 16ms (RAF-based)
3. **Lineer interpolasyon** → İki breakpoint arasındaki scale değeri hesaplanır
4. **CSS uygulanır** → `element.style.transform = 'scale(0.85)'`
5. **Root'a attribute yazılır** → `<html data-scale-tier="..." data-dpr="..." data-aspect="...">`
6. **CSS token senkronizasyonu** → `--quick-controls-display` inline CSS ile güncellenir

#### 4K'da Ne Olur?

ScaleManager ≥2561px'de `skip: true` ayarlar. Ölçeklendirme CSS zoom'a devredilir:

```css
/* d-4k.css */
@media (min-width: 3840px) {
  body { zoom: 2; }                    /* 1920 × 2 = 3840 */
}
@media (min-width: 7680px) {
  body { zoom: 3; }                    /* 2560 × 3 = 7680 */
}
```

### 3.3 Layer 2: CSS Custom Properties (a-scale-hybrid.css)

**Dosya:** `assets.coremusic.net/Css/01_Abstracts/a-scale-hybrid.css`

JS genel ölçeklendirme yaparken, bu dosya **bireysel element boyutlarını** tanımlar. İkisi birbirini tamamlar, çakışmaz.

#### Modüler Ölçek (Major Third — Ratio 1.25)

```css
:root {
  --ms-0:   10px;   /* base */
  --ms-1:   12px;   /* 10 × 1.25 = 12.5 */
  --ms-2:   16px;   /* 10 × 1.25² = 15.625 */
  --ms-3:   20px;   /* 10 × 1.25³ = 19.53 */
  --ms-4:   25px;   /* 10 × 1.25⁴ = 24.41 */
  --ms-5:   31px;   /* 10 × 1.25⁵ = 30.52 */
  --ms-6:   39px;   /* 10 × 1.25⁶ = 38.15 */
  --ms-7:   49px;   /* 10 × 1.25⁷ = 47.68 */
  --ms-n1:  8px;    /* 10 / 1.25 = 8 */
  --ms-n2:  6px;    /* 10 / 1.25² = 6.4 */
  --scale-ratio: 1.25;
}
```

#### Akıcı Tipografi (Fluid Typography — clamp())

```css
:root {
  --fs-3xs:  clamp(8px,  0.7vw,  12px);   /* caption, badge */
  --fs-2xs:  clamp(9px,  0.8vw,  13px);   /* micro label */
  --fs-xs:   clamp(10px, 0.85vw, 14px);   /* footnote, meta */
  --fs-sm:   clamp(11px, 0.95vw, 16px);   /* small text */
  --fs-base: clamp(12px, 1.05vw, 18px);   /* body text */
  --fs-lg:   clamp(14px, 1.2vw,  21px);   /* subtitle */
  --fs-xl:   clamp(16px, 1.4vw,  24px);   /* section title */
  --fs-2xl:  clamp(20px, 1.7vw,  30px);   /* page title */
  --fs-3xl:  clamp(25px, 2.1vw,  38px);   /* hero title */
}
```

#### Cihaz Bazlı Bileşen Boyutları (8 Breakpoint)

| Breakpoint | --btn-size-md | --gap-md | --icon-size-lg | --quick-controls-display |
|------------|---------------|----------|----------------|--------------------------|
| Phone (≤767) | 38px | 6px | 16px | none |
| Embedded (768-1024) | 40px | 7px | 16px | none |
| Laptop (1025-1440) | 46px | 9px | 20px | flex |
| Medium (1441-1919) | 48px | 9px | 21px | flex |
| Wide (1920-2559) | 50px | 10px | 22px | flex |
| 2K (2560-3839) | 57px | 11px | 24px | flex |
| 4K TV (3840) | 69px | 13px | 30px | flex |
| 4K Monitor (≥3841) | 76px | 15px | 34px | flex |

### 3.4 Layer 3: CSS Zoom (d-4k.css)

**Dosya:** `assets.coremusic.net/Css/08_Devices/d-4k.css`

ScaleManager ≥2561px'de durur, CSS zoom devralır:

```css
/* 4K TV: zoom ×2 */
@media (min-width: 3840px) {
  body { zoom: 2; }
  /* fallback: transform: scale(2); */
}

/* 8K: zoom ×3 */
@media (min-width: 7680px) {
  body { zoom: 3; }
}
```

### 3.5 Scale Kullanım Kılavuzu

#### Yeni Bir Component Eklerken

```css
/* 1. Token kullan, hardcoded boyut yazma */
.my-component {
  padding: var(--gap-md);           /* cihaza göre değişir */
  font-size: var(--fs-base);        /* akıcı tipografi */
  min-width: var(--btn-size-md);    /* modüler buton boyutu */
  border-radius: var(--radius-md);  /* kenar yuvarlaklığı */
}

/* 2. Media query ile override etme — token zaten responsive */
/* SADECE behavioral farklılıklar için media query kullan */
@media (pointer: coarse) {
  .my-component { min-width: var(--btn-size-lg); } /* touch cihazlarda daha büyük */
}
```

#### ScaleManager API

```javascript
// DOĞRULANMIŞ API — ScaleManager.js v7 (kaynak kod satırlarıyla eşleşir)

// 1) Başlatma (js/main.js v6):
//    const scaleManager = new ScaleManager(eventBus);
//    scaleManager.init();
//    app.registerModule('scale', scaleManager);

// 2) EventBus ile ölçek sonucunu dinle ('scale:applied' payload'ı):
//    payload = { sw, sh, tier, targets: { header: {scale, skip}, footer: {...}, home: {...} } }
eventBus.on('scale:applied', (payload) => {
  console.log('Tier:', payload.tier);
});

// 3) Legacy köprüler (geriye dönük uyumluluk — ScaleManager #registerGlobalBridge):
window.ScaleCoordinator.runAll();          // RAF üzerinden tüm hedefleri yeniden uygula
window.scaleHeaderForScreen();
window.scaleFooterForScreen();
window.scaleHomeForScreen();

// 4) Dinamik hedef kaydı (OCP):
//    scaleManager.registerTarget({ name, selector, origin, rules, nestedChildren });

// 5) Root metadata (ScaleManager yazar):
//    <html data-scale-tier="desktop" data-dpr="1.00" data-aspect="1.78">

// 6) Quick controls görünürlüğü (ScaleManager inline senkronize eder):
const display = getComputedStyle(document.documentElement)
  .getPropertyValue('--quick-controls-display');   // ≤1024px: none, >1024px: flex
```

> **Not:** `window.CoreMusic.ScaleManager.getScaleTier()` ve `scaletierchange` eventi kodda MEVCUT DEĞİLDİR (v1.0.0'daki bu örnek düzeltildi). Tier bilgisi `document.documentElement.getAttribute('data-scale-tier')` üzerinden okunur.

---

## 4. SAYFA ROUTER SİSTEMİ (SPA Router)

### 4.1 Nedir?

CoreMusic **hibrit SPA** (Single Page Application) kullanır. İlk yükleme PHP tarafından sunucuda yapılır, sonrasındaki navigasyonlar JavaScript tarafından istemcide yönetilir.

```
İlk Yükleme:  Browser → PHP → Tam HTML shell (header + content + footer)
Sonraki:      Browser → JS Router → Sadece #main-content içeriği değişir
```

### 4.2 Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    JS SPA ROUTER                                 │
│                                                                  │
│  RouterEventManager (event binding)                              │
│    ├── popstate → navigate(pushState=false)                      │
│    ├── click on <a> → navigate(pushState=true)                   │
│    └── mouseenter → prefetch                                     │
│                                                                  │
│  NavigationOrchestrator (durum makinesi)                         │
│    ├── GuardPipeline (auth, role, permission)                    │
│    ├── CacheLayer (LRU, TTL per route)                           │
│    ├── ContentFetcher (HTTP + AbortController)                   │
│    ├── ContentPatcher (DOMParser + TrustedTypes)                 │
│    ├── CsrfSyncManager (token senkronizasyonu)                   │
│    ├── AuthBoundaryDetector (auth durumu değişikliği)            │
│    ├── ScrollRestorer (pozisyon kaydetme/geri yükleme)          │
│    └── MemoryWatchdog (bellek sızıntısı kontrolü)               │
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐       │
│  │   Guards     │    │   Cache      │    │  Fetcher     │       │
│  │ authGuard    │    │ LRU 100max   │    │ XMLHttpRequest│       │
│  │ roleGuard    │    │ TTL per type │    │ + AbortCtrl   │       │
│  │ permGuard    │    │ Tag invalid. │    │ + CSRF header │       │
│  └──────────────┘    └──────────────┘    └──────────────┘       │
└─────────────────────────────────────────────────────────────────┘
```

### 4.3 Adım Adım Navigasyon Akışı

#### Adım 1: İlk Yükleme (PHP)

```
1. Browser → home.coremusic.net/index.php
2. PageRouterKernel::handle() çalışır
3. Middleware pipeline (10 katman) çalışır
4. PageRouter::dispatch() → route çözümlenir
5. HtmlShellRenderer::render() → tam HTML shell üretilir:
   - <html data-gender="female" data-mode="dark">
   - <link href="main.css"> (ITCSS)
   - <link href="d-embedded.css"> (device CSS)
   - <link href="v-home.css"> (view mode CSS)
   - <body data-device="embedded">
   - window.CoreMusic.RouterConfig = { ... }
   - <script src="device-loader.js">
   - <script src="main.js" type="module">
6. Browser HTML'i alır, render eder
```

#### Adım 2: SPA Navigasyon (JS)

```
1. Kullanıcı "Müzik" linkine tıklar
2. RouterEventManager click handler tetiklenir
3. Same-origin kontrolü → SPA navigasyonu başlatılır
4. NavigationOrchestrator::navigate('/kesfet') çağrılır:
   a. URL normalize edilir
   b. GuardPipeline çalışır (auth, role, permission)
   c. Cache kontrolü → cache miss ise HTTP fetch
   d. POST /api/page/kesfet → { container, route, meta, csrf_token }
   e. ContentPatcher::patchDOM():
      - lifecycle.unmount() → eski component'ler temizlenir
      - DOMParser ile yeni HTML parse edilir
      - #main-content innerHTML güncellenir
      - csrfSync.update() → CSRF token senkronize edilir
      - lifecycle.mount() → yeni component'ler başlatılır
   f. history.pushState() → URL güncellenir
   g. ScrollRestorer → sayfa pozisyonu kaydedilir
```

### 4.4 Route Tanımları

**Home routes:** `shared/config/routes.php`
**Auth routes:** `shared/config/auth-routes.php`

Her route bir `SpaRoute` nesnesidir:

```php
new SpaRoute(
    page: 'home',                    // PHP template dosyası (pages/home.php)
    path: '/home',                   // URL paterni ({param} destekli)
    requiresAuth: true,              // Auth gerektirir mi?
    title: 'Ana Sayfa',              // Sayfa başlığı
    requiredRole: null,              // RBAC rol kontrolü
    requiredPermission: null,        // RBAC yetki kontrolü
    cacheable: true,                 // Sunucu tarafı cache
    handler: null,                   // POST handler
    meta: ['ttlType' => 'user'],     // Ek metadata
    cacheTtl: null                   // Özel TTL
)
```

#### Route Çözümleme

```
/              → home (boş URI varsayılan)
/home          → home (tam eşleşme)
/kesfet/{id}   → kesfet (pattern matching, {id} parametresi)
/login         → auth.coremusic.net'a redirect
```

### 4.5 Guard Sistemi (Erişim Kontrolü)

#### Server-Side (PHP) — AuthGuard

```
1. Route auth gerektiriyor mu? → Evet + kullanıcı giriş yapmamış → /login'e redirect
2. Route rol gerektiriyor mu? → Rol uyuşmazlığı → /403
3. Route yetki gerektiriyor mu? → Yetki yok → /403
4. Auth sayfasına mı gidiliyor? → auth.coremusic.net'a redirect
5. Giriş yapmış kullanıcı auth sayfasında → /home'a redirect
6. Logout isteği → auth domain'e redirect
```

#### Client-Side (JS) — Guards

```javascript
// guards.js
export function authGuard(route, user) {
  if (route.requiresAuth && !user) return { redirect: '/login' };
  return { allowed: true };
}

export function roleGuard(route, user) {
  if (route.requiredRole && user.role !== route.requiredRole)
    return { redirect: '/403' };
  return { allowed: true };
}

export function permissionGuard(route, user) {
  if (route.requiredPermission && !user.permissions.includes(route.requiredPermission))
    return { redirect: '/403' };
  return { allowed: true };
}
```

### 4.6 Cache Sistemi

**CacheLayer** — LRU (Least Recently Used) cache:

```javascript
// TTL değerleri
const TTL = {
  static:  3600,  // Statik içerik: 1 saat
  user:     120,  // Kullanıcı içerik: 2 dakika
  dynamic:   60,  // Dinamik içerik: 1 dakika
  default:  600   // Varsayılan: 10 dakika
};

// Maksimum 100 entry, fazlası LRU eviction
// Tag bazlı invalidation desteği
router.invalidateCacheTag('music'); // tüm music tag'li cache'leri temizle
```

### 4.7 CSRF Senkronizasyonu

```javascript
// CsrfSyncManager
// 1. Token'ı [name="csrf_token"] input'tan okur
// 2. DOM patch sonrası tüm CSRF input'larını günceller
// 3. Fetch isteklerine X-CSRF-Token header'ı ekler
```

### 4.8 Router Kullanım Kılavuzu

#### Yeni Route Eklerken

```php
// shared/config/routes.php
'blog' => new SpaRoute(
    page: 'blog',
    path: '/blog',
    requiresAuth: false,
    title: 'Blog',
    cacheable: true
),
```

```php
// pages/blog.php (template)
<div class="page-blog">
    <h1><?= h($config['title']) ?></h1>
    <!-- İçerik -->
</div>
```

#### JS Tarafından Navigasyon

```javascript
// Programatik navigasyon
await window.CoreMusic.Router.navigate('/kesfet');

// Cache temizleme
window.CoreMusic.Router.invalidateCacheTag('kesfet');

// Mevcut URL
console.log(window.CoreMusic.Router.currentUrl);
```

#### Yeni Guard Eklerken

```javascript
// main.js
const customGuard = (route, user) => {
  // Özel kontrol
  if (route.meta?.requiresPremium && user.role !== 'premium') {
    return { redirect: '/upgrade' };
  }
  return { allowed: true };
};

const router = new Router({
  ...config,
  guardFunctions: [authGuard, roleGuard, permissionGuard, customGuard]
});
```

---

## 5. CSS SİSTEMİ (ITCSS Mimarisi)

### 5.1 Nedir?

ITCSS (Inverted Triangle CSS), CSS dosyalarının **öncelik sırasına** göre düzenlendiği bir mimari katmanlama sistemidir. En geniş kapsamlı kurallar üste, en spesifik kurallar alttadır.

```
            ┌─────────────┐
            │  Utilities   │  ← En yüksek öncelik (override eder)
            ├─────────────┤
            │   Vendors    │  ← Üçüncü parti
            ├─────────────┤
            │   Devices    │  ← Cihaz bazlı davranış
            ├─────────────┤
            │  ViewModes   │  ← Görünüm modu
            ├─────────────┤
            │    Pages     │  ← Sayfa bazlı
            ├─────────────┤
            │ Components   │  ← Bileşen stilleri
            ├─────────────┤
            │   Layout     │  ← Header, footer, sidebar
            ├─────────────┤
            │    Base      │  ← Reset, temel elementler
            ├─────────────┤
            │  Abstracts   │  ← Token'lar, değişkenler (en geniş)
            └─────────────┘
```

### 5.2 Katman Yapısı

| #   | Katman         | Dizin            | Amaç                                       | Dosya Sayısı |
| --- | -------------- | ---------------- | ------------------------------------------ | ------------ |
| 01  | **Abstracts**  | `01_Abstracts/`  | Token'lar, CSS değişkenleri, hiç kural yok | 10           |
| 02  | **Base**       | `02_Base/`       | Reset, temel element stilleri              | 2            |
| 03  | **Layout**     | `03_Layout/`     | Header, footer, sidebar                    | 3            |
| 04  | **Components** | `04_Components/` | Bileşen stilleri (button, card, modal)     | 3            |
| 05  | **Pages**      | `05_Pages/`      | Sayfa bazlı stiller                        | 7            |
| 06  | **Utilities**  | `06_Utilities/`  | Helper sınıfları                           | 1            |
| 07  | **Vendors**    | `07_Vendors/`    | Üçüncü parti (Bootstrap)                   | 1            |
| 08  | **Devices**    | `08_Devices/`    | Cihaz bazlı davranışsal override'lar       | 12           |
| 09  | **ViewModes**  | `09_ViewModes/`  | Görünüm modu stilleri                      | 4            |
| 11  | **OAuth**      | `11_OAuth/`      | OAuth sayfa stilleri                       | 1            |

### 5.3 Ana Stylesheet (main.css)

```css
/* main.css — Tüm import'lar sıralı */
@import url("./01_Abstracts/a-fonts-token.css");
@import url("./01_Abstracts/a-colors-token.css");
@import url("./01_Abstracts/a-semantic-token.css");
@import url("./01_Abstracts/a-color-mode-tokens.css");
@import url("./01_Abstracts/a-theme-config.css");
@import url("./01_Abstracts/a-light-glass-tokens.css");
@import url("./01_Abstracts/a-login-tokens.css");
@import url("./01_Abstracts/a-breakpoint-tokens.css");
@import url("./01_Abstracts/a-scale-hybrid.css");
@import url("./01_Abstracts/a-layout-tokens.css");

@import url("./02_Base/b-base-core.css");
@import url("./02_Base/l-main-structural.css");

@import url("./03_Layout/_header.css");
@import url("./03_Layout/_footer.css");

@import url("./04_Components/c-scrollbar-accent.css");
@import url("./04_Components/c-footer-seek.css");
@import url("./04_Components/c-footer-volume.css");

@import url("./05_Pages/_home.css");
@import url("./05_Pages/_player.css");

@import url("./06_Utilities/u-helpers-utility.css");

@import url("./07_Vendors/v-bootstrap-lib.css");

@import url("./09_ViewModes/v-home.css");
@import url("./09_ViewModes/v-pro.css");
@import url("./09_ViewModes/v-studio.css");
```

**ÖNEMLİ:** `08_Devices/` dosyaları main.css'te **YOKTUR**. Device CSS dosyaları `device-loader.js` tarafından dinamik olarak yüklenir.

### 5.4 Abstracts Katmanı (Token Sistemi)

| Dosya | Amaç | Token Örnekleri |
|-------|------|-----------------|
| `a-fonts-token.css` | Font-face + font token'ları | `--font-family-body`, `--fw-bold` |
| `a-colors-token.css` | Ham renk paleti | `--color-pink-500`, `--color-blue-300` |
| `a-semantic-token.css` | Anlamsal token'lar | `--bg-base`, `--text-primary`, `--accent` |
| `a-color-mode-tokens.css` | Dark/light mode | `html[data-mode="light"] { --bg-base: #fff; }` |
| `a-theme-config.css` | Tema yapılandırması | `--glass-blur`, `--z-modal`, transition'lar |
| `a-light-glass-tokens.css` | Cam efekti + gender | `--theme-f-primary`, `--theme-m-primary` |
| `a-breakpoint-tokens.css` | Breakpoint sabitleri | `--bp-device-phone: 767px` |
| `a-scale-hybrid.css` | Hibrit ölçeklendirme | `--fs-base`, `--btn-size-md`, `--gap-md` |
| `a-layout-tokens.css` | Layout boyutları | `--header-h`, `--footer-h`, `--sidebar-w` |
| `a-design-tokens.css` | Master design tokens | 250+ token (C01-C16 component) |

### 5.5 Token Kullanım Kılavuzu

#### Renk Token'ları

```css
/* ❌ YANLIŞ — hardcoded renk */
color: #ff4fd8;
background: rgba(0,0,0,0.5);

/* ✅ DOĞRU — token kullan */
color: var(--accent);
background: var(--glass-bg);
```

#### Boşluk Token'ları

```css
/* ❌ YANLIŞ — hardcoded piksel */
padding: 12px;
margin: 16px 24px;

/* ✅ DOĞRU — token kullan */
padding: var(--gap-md);
margin: var(--gap-lg) var(--gap-2xl);
```

#### Font Token'ları

```css
/* ❌ YANLIŞ — hardcoded font boyutu */
font-size: 14px;

/* ✅ DOĞRU — akıcı tipografi */
font-size: var(--fs-base);    /* clamp(12px, 1.05vw, 18px) */
font-size: var(--fs-lg);      /* clamp(14px, 1.2vw, 21px) */
```

#### Breakpoint Token'ları

```css
/* ❌ YANLIŞ — hardcoded breakpoint */
@media (min-width: 1024px) { ... }

/* ✅ DOĞRU — token kullan (eğer mümkünse) */
/* Not: Media query'lerde doğrudan px kullanmak da kabul edilebilir */
/* Ama layout token'ları zaten responsive */
:root {
  --header-h: 60px;  /* default: embedded */
}
@media (min-width: 1920px) {
  :root { --header-h: 70px; }  /* desktop override */
}
```

### 5.6 Device CSS (08_Devices/)

Device CSS dosyaları **kendi kendine yeterli** (self-contained). Her dosya kendi import'unu yapar:

```css
/* d-desktop.css — Self-contained */
@import '../01_Abstracts/a-theme-config.css';
@import '../01_Abstracts/a-colors-token.css';
@import '../01_Abstracts/a-semantic-token.css';
@import '../01_Abstracts/a-breakpoint-tokens.css';
@import '../01_Abstracts/a-layout-tokens.css';
@import '../02_Base/b-base-core.css';
@import '../03_Layout/_header.css';
@import '../03_Layout/_footer.css';
@import '../05_Pages/_home.css';

/* Sadece behavioral override'lar */
.nav-link:hover {
  color: var(--accent);
  background: rgba(255,79,216,0.08);
}

.layout--desktop {
  /* Token override yok — a-layout-tokens'tan gelir */
}
```

#### Device CSS Dosya Haritası

| Dosya | Cihaz | Import Ettiği |
|-------|-------|---------------|
| `d-embedded.css` | RPi5 (1024×600) | abstracts + base + header + footer + home |
| `d-phone.css` | Telefon (≤767) | abstracts + base + header + footer + home |
| `d-tablet.css` | Tablet (768-1024) | abstracts + base + header + footer + home |
| `d-laptop.css` | Laptop (1025-1440) | abstracts + base + header + footer + home |
| `d-desktop.css` | Masaüstü (1441-2560) | abstracts + base + header + footer + home |
| `d-4k.css` | 4K TV/Monitor (≥2561) | abstracts + base + header + footer + home |
| `d-auth-*.css` | Auth sayfaları | abstracts + base (home import ETMEZ) |

### 5.7 ViewMode CSS (09_ViewModes/)

Her görünüm modu için ayrı CSS:

| Dosya | Mod | Özellik |
|-------|-----|---------|
| `v-home.css` | Ev medya merkezi | Gender bazlı arka plan, glass overlay |
| `v-pro.css` | Profesyonel | Geniş sidebar, EQ/DSP panelleri |
| `v-studio.css` | Stüdyo | Koyu tema, geniş sidebar |
| `v-car.css` | Araç içi | Büyük touch hedefleri, minimal UI |

---

## 6. CİHAZ VE TEMA SİSTEMİ

### 6.1 Cihaz Tespiti (Dual-Layer: PHP + JS)

#### PHP (Sunucu tarafı)

```php
// shared/src/Device/DeviceManager.php
$dm = DeviceManager::fromRequest($request);

// Cihaz tipi
$dm->isEmbedded()   // RPi5
$dm->isPhone()      // ≤767px
$dm->isTablet()     // 768-1024px
$dm->isLaptop()     // 1025-1440px
$dm->isDesktop()    // 1441-2560px
$dm->is4kTv()       // 2561-3840px
$dm->is4kMonitor()  // ≥3841px

// Karma sorgular
$dm->isTouch()      // embedded + phone + tablet
$dm->isWide()       // laptop + desktop + 4k
$dm->isLarge()      // desktop + 4k
$dm->isMobile()     // phone + tablet

// CSS class helper'ları
$dm->layoutClass()   // "layout--embedded"
$dm->allClasses()    // "layout layout--embedded layout--touch"
$dm->dataAttributes() // 'data-device="embedded" data-touch="true"'
```

#### JS (İstemci tarafı)

```javascript
// device-loader.js
DeviceLoader.init({
  assetsUrl: '/assets',
  isAuth: false,
  viewMode: 'home',
  serverDevice: 'embedded'
});

// Cihaz değişikliğini dinle
window.addEventListener('devicechange', (e) => {
  console.log('Yeni cihaz:', e.detail.device);
  console.log('Önceki:', e.detail.previous);
});

// Mevcut cihazı al
const device = window.CoreMusic.DeviceLoader.getDevice();
```

### 6.2 Cihaz Bazlı HTML (DeviceManager PHP)

DeviceManager, her cihaz için farklı HTML yapısı render eder:

| Özellik | Embedded | Phone | Tablet | Laptop | Desktop | 4K |
|---------|----------|-------|--------|--------|---------|-----|
| Widget sayısı | 4 | 2 | 4 | 4 | 6 | 6 |
| Recent kart sayısı | 3 | 2 | 4 | 5 | 7 | 8 |
| Playlist sayısı | 0 | 0 | 2 | 2 | 3 | 3 |
| Volume göster | ✅ | ❌ | ✅ | ✅ | ✅ | ✅ |
| Sidebar göster | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Nav link sayısı | 2 | 3 | 4 | 6 | 7 | 8 |

```php
// header.php
<header class="<?= $dm->allClasses() ?>" <?= $dm->dataAttributes() ?>>
    <?php foreach ($dm->navLinks() as $link): ?>
        <a href="<?= $link['href'] ?>" <?= $link['active'] ? 'aria-current="page"' : '' ?>>
            <?= h($link['label']) ?>
        </a>
    <?php endforeach; ?>
</header>

// home.php
<?php for ($i = 0; $i < $dm->widgetCount(); $i++): ?>
    <div class="home-widget">...</div>
<?php endfor; ?>

<?php if ($dm->showVolume()): ?>
    <div class="volume-section">...</div>
<?php endif; ?>
```

### 6.3 Tema Sistemi (Gender + Color Mode)

#### Gender Teması (3 varyant)

`data-gender` attribute'u `<html>` elementinde ayarlanır:

```css
/* a-semantic-token.css */
html[data-gender="female"] {
  --theme-primary: #e8b4b8;      /* Rose */
  --theme-secondary: #d496a0;
  --accent: #ff4fd8;             /* Pembe */
}

html[data-gender="male"] {
  --theme-primary: #5b8fb9;      /* Blue */
  --theme-secondary: #89c4d9;
  --accent: #4f8fff;             /* Mavi */
}

html[data-gender="neutral"] {
  --theme-primary: #b8a9c9;      /* Lavender */
  --theme-secondary: #9b8BB4;
  --accent: #a855f7;             /* Mor */
}
```

#### Dark/Light Mode

`data-mode` attribute'u `<html>` elementinde ayarlanır:

```css
/* a-color-mode-tokens.css */
/* Dark mode = varsayılan */
:root {
  --bg-base: #0a0a0f;
  --text-primary: #ffffff;
}

/* Light mode */
html[data-mode="light"] {
  --bg-base: #f5f0ff;
  --text-primary: #1a1025;
}

/* Light + Gender kombinasyonları */
html[data-mode="light"][data-gender="female"] {
  --accent: #d946a8;
}
```

#### CSS Kaskad Önceliği

```
1. :root (dark varsayılan)
2. [data-gender] (gender override)
3. @media prefers-color-scheme (OS varsayılanı)
4. html[data-mode] (kullanıcı override — EN YÜKSEK)
```

#### Tema Kullanım Kılavuzu

```php
// PHP tarafı
$theme = ThemeManager::detect();        // "female", "male", "neutral"
$mode = ThemeManager::detectMode();     // "dark", "light", null

// HTML attributes
ThemeManager::injectAttributes($theme, $mode);
// → <html lang="tr" data-gender="female" data-mode="dark">
```

```javascript
// JS tarafı — anında tema değişikliği (sayfa yenileme yok)
window.CoreMusic.ThemeManager.setGender('male');
window.CoreMusic.ThemeManager.setMode('light');

// Event dinleme
window.addEventListener('themechange', (e) => {
  console.log('Yeni tema:', e.detail.gender);
});

window.addEventListener('colormodechange', (e) => {
  console.log('Yeni mod:', e.detail.mode);
});
```

---

## 7. CSS YÜKLEME SIRASI (Tam Akış)

### 7.1 İlk Yükleme (PHP + JS)

```
1. PHP: HtmlShellRenderer → <link href="main.css"> (ITCSS 9-layer)
2. PHP: HtmlShellRenderer → <link href="d-{device}.css"> (device behavioral)
3. PHP: HtmlShellRenderer → <link href="v-{viewMode}.css"> (view mode)
4. JS: device-loader.js →动态加载/device CSS (eğer resize olursa)
5. JS: ScaleManager.js → transform:scale uygula
```

### 7.2 Resize Sırasında

```
1. device-loader.js: 300ms debounce
2. Yeni cihaz tespit et
3. Tier değiştiyse → Router.navigate() veya location.reload()
4. Tier aynıysa → Sadece device CSS değiştir (fade transition)
5. ScaleManager: yeniden hesapla ve uygula
```

### 7.3 Auth Sayfalarında

```
1. main.css YÜKLENMEZ
2. auth-bundled.css yüklenir
3. d-auth-{device}.css yüklenir (home import ETMEZ)
4. v-{viewMode}.css YÜKLENMEZ
5. auth-theme.js + auth-gender-bg.js yüklenir
```

---

## 8. BİLEŞEN SİSTEMİ (C01-C16)

### 8.1 Kanonik Bileşenler

| ID | Bileşen | BEM Sınıfı | ITCSS Katmanı | Touch Target |
|----|---------|------------|---------------|--------------|
| C01 | Navigation Link | `.nav-link` | 03_Layout | ~24×24px |
| C02 | Status Widget | `.header-widget` | 03_Layout | ~38×24px |
| C03 | User Pill | `.header-user` | 03_Layout | ~85×26px |
| C04 | Buttons | `.btn-primary`, `.btn-secondary` | 04_Components | 44-48px |
| C05 | Icon Button | `.icon-btn`, `.play-ctrl-btn` | 04_Components | 44-48px |
| C06 | Form Input | `.form-input` | 04_Components | 44px |
| C07 | Gender Button | `.gender-card`, `.gender-btn` | 05_Pages | 140×180px |
| C08 | Social Login | `.social-login-btn` | 05_Pages | 48×48px |
| C09 | Media Card | `.media-card` | 04_Components | 120-160px |
| C10 | Content Panel | `.content-panel`, `.split-panel` | 03_Layout | 42/58 Split |
| C11 | Tab Bar | `.tab-bar`, `.sub-nav` | 04_Components | 48px |
| C12 | Star Rating | `.star-rating` | 04_Components | 24×24px |
| C13 | Media List Item | `.media-list-item` | 04_Components | 48px row |
| C14 | WiFi Modal | `.wifi-modal` | 04_Components | Modal Overlay |
| C15 | Bluetooth Modal | `.bluetooth-modal` | 04_Components | Modal Overlay |
| C16 | Welcome Modal | `.welcome-modal` | 04_Components | Modal |

### 8.2 BEM Kullanım Örnekleri

```css
/* Block */
.player { display: flex; }

/* Element */
.player__controls { display: flex; }
.player__track { flex: 1; }
.player__volume { width: 100px; }

/* Modifier */
.player--mini { height: 60px; }
.player--playing .player__play { display: none; }

/* Durum */
.player__track--active { background: var(--color-primary); }
```

---

## 9. HATA AYIKLAMA KILAVUZU

### 9.1 Scale Çalışmıyor

```
1. ScaleManager yüklü mü? → konsolda window.CoreMusic.ScaleManager kontrol et
2. Data attribute'lar yazıldı mı? → <html data-scale-tier="..." data-dpr="...">
3. CSS token'lar tanımlı mı? → getComputedStyle(document.documentElement).getPropertyValue('--fs-base')
4. 4K'da mısınız? → ScaleManager ≥2561px'de skip eder, CSS zoom devralır
```

### 9.2 Router Çalışmıyor

```
1. SPA enabled mi? → window.CoreMusic.RouterConfig.enabled === true
2. history.pushState destekleniyor mu? → typeof history.pushState === 'function'
3. Guard'lar engelliyor mu? → Konsolda hata var mı?
4. CSRF token doğru mu? → [name="csrf_token"] input'u mevcut mu?
5. İlk yükleme mi? → SPA sadece sonraki navigasyonları yönetir
```

### 9.3 CSS Yüklenmiyor

```
1. Device-loader.js çalışıyor mu? → window.CoreMusic.DeviceLoader mevcut mu?
2. CSS dosyası var mı? → /Css/08_Devices/d-{device}.css mevcut mu?
3. CSP nonce doğru mu? → <link> tag'inde nonce var mı?
4. Console'da CSP hatası var mı? → blocked by Content Security Policy
```

### 9.4 Tema Değişmiyor

```
1. data-gender attribute'u değişti mi? → <html data-gender="...">
2. CSS token'lar tanımlı mı? → --theme-primary, --accent
3. JS manager çalışıyor mu? → window.CoreMusic.ThemeManager
4. Sayfa yenilendi mi? → Tema değişikliği anında olmalı
```

---

## 10. İLGİLİ DOSYALAR

| Sistem | Ana Dosya |
|--------|-----------|
| Scale (JS) | `assets.coremusic.net/js/managers/ScaleManager.js` |
| Scale (CSS) | `assets.coremusic.net/Css/01_Abstracts/a-scale-hybrid.css` |
| Scale (Layout) | `assets.coremusic.net/Css/01_Abstracts/a-layout-tokens.css` |
| Scale (4K Zoom) | `assets.coremusic.net/Css/08_Devices/d-4k.css` |
| ITCSS Master | `assets.coremusic.net/Css/main.css` |
| Device Loader | `assets.coremusic.net/js/device-loader.js` |
| Router (JS) | `assets.coremusic.net/js/router/Router.js` |
| Router Entry | `assets.coremusic.net/js/router/main.js` |
| Nav Orchestrator | `assets.coremusic.net/js/router/NavigationOrchestrator.js` |
| Content Patcher | `assets.coremusic.net/js/router/ContentPatcher.js` |
| PHP Router | `shared/src/PageRouter/PageRouter.php` |
| PHP Router Kernel | `shared/src/PageRouter/PageRouterKernel.php` |
| HTML Shell | `shared/src/PageRouter/HtmlShellRenderer.php` |
| Device Manager (PHP) | `shared/src/Device/DeviceManager.php` |
| Device Detector | `shared/src/Device/DeviceDetector.php` |
| Device CSS Map | `shared/src/Device/DeviceCssMap.php` |
| Theme Manager (PHP) | `shared/src/Theme/ThemeManager.php` |
| View Mode Manager | `shared/src/ViewMode/ViewModeManager.php` |
| Home Routes | `shared/config/routes.php` |
| Auth Routes | `shared/config/auth-routes.php` |

---

## 11. KOD ŞABLONLARI VE AKIŞ KURALLARI (v1.1.0)

### 11.1 Yeni Ölçek Hedefi (Scale Target)

```javascript
// ScaleManager.js'e registerTarget() ile eklenir — çekirdek koda dokunulmaz (OCP)
scaleManager.registerTarget({
  name: 'sidebar',
  selector: '.sidebar',
  origin: 'top left',
  compensateWidth: true,
  customProp: '--scale-sidebar',
  rules: [
    { max: 1024, skip: true },                    // Embedded/Tablet: CSS yönetir
    { min: 1025, max: 2560, from: 0.80, to: 1.00 }, // Lineer enterpolasyon
    { min: 2561, skip: true },                    // ≥2561: yerel CSS
  ],
  nestedChildren: [
    { selector: '.sidebar__toggle', origin: 'center center', inverseScale: true },
  ],
});
```

**Kurallar:** (1) Kural sırası önemli — ilk eşleşen kazanır. (2) `{maxH}/{minH}` ile yükseklik kısıtı eklenebilir. (3) `skip: true` = `TransformApplier.reset()` — inline transform temizlenir. (4) Her hedef `--scale-{ad}` custom prop'u yazar.

### 11.2 Yeni Bileşen CSS (C17+ Şablonu)

```css
/**
 * CoreMusic — {Bileşen Adı}
 * ITCSS Layer: 04_Components
 * Version: 1.0.0 — {TARİH}
 * PNG Kaynak: .ai/.png/home-1024/{ilgili-png}.png
 */
.c-{bileşen} {                                   /* Block — BEM */
  padding: var(--gap-md);                        /* token zorunlu */
  font-size: var(--fs-base);                     /* akıcı tipografi */
  background: var(--glass-bg-subtle);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: var(--radius-md);
  min-height: var(--touch-min);                  /* WCAG 2.2 AA ≥48px */
}

.c-{bileşen}__{element} { }                       /* Element */
.c-{bileşen}--{modifier} { }                      /* Modifier */

.c-{bileşen}:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 2px;
}

@media (hover: hover) and (pointer: fine) {       /* Dokunmatikte hover YOK */
  .c-{bileşen}:hover { background: var(--glass-bg-hover); }
}

@media (prefers-reduced-motion: reduce) {
  .c-{bileşen} { transition-duration: 0.01ms; }
}
```

**Kurallar:** Bileşen dosyası `04_Components/c-{ad}.css`'e yazılır ve `main.css`'e import edilir. Ölçüler `[[01-component-inventory]]` ile eşleşir. Hover yalnızca `(hover:hover) and (pointer:fine)` içinde tanımlanır (RPi5 dokunmatik cihaz).

### 11.3 Yeni Route (PHP + Template)

```php
// shared/config/routes.php
'blog' => new SpaRoute(
    page: 'blog',                // pages/blog.php
    path: '/blog',
    requiresAuth: false,
    title: 'Blog',
    cacheable: true,
    meta: ['ttlType' => 'static']
),
```

```php
<!-- pages/blog.php — SADECE #main-content içeriği; header/footer shell'dedir -->
<div class="page-blog">
  <h1 class="page-blog__title"><?= h($config['title']) ?></h1>
</div>
```

**Kurallar:** (1) Template PHP'de inline style YASAK (brain.md §18C). (2) `h()` ile escape zorunlu. (3) SPA etkinse sayfa PHP shell + JS patch ile açılır; `data-no-spa` attribute'u SPA dışına çıkarır.

### 11.4 Yeni Guard (JS)

```javascript
// main.js — Router constructor'ına guardFunctions ile enjekte edilir
const premiumGuard = (route, user) => {
  if (route.meta?.requiresPremium && user?.role !== 'premium') {
    return { redirect: '/403' };
  }
  return { allowed: true };
};

const router = new Router({ ...config, guardFunctions: [authGuard, roleGuard, permissionGuard, premiumGuard] });
```

### 11.5 Akış Kuralları (Özet — Hepsi Doğrulanmış)

1. **İlk yükleme PHP'dedir:** Shell + `main.css` + `d-{device}.css` + `v-{view}.css`; SPA yalnızca sonraki navigasyonları yönetir.
2. **Tier sınırı aşıldıysa CSS yetmez:** `device-loader.js` tier değişiminde `Router.navigate(pathname)` → koşullu HTML bloklarının sunucudan gelmesi gerekir (max 2 deneme, `cm_tier_sync_count`).
3. **Tier aynıysa sadece CSS değişir:** `loadDeviceOnly()` + fade transition (`cm-device-transitioning`).
4. **4K'da çift ölçek YASAK:** ScaleManager ≥2561 skip; ölçeği `d-4k.css` zoom üstlenir (Kademe 1/2/3: ×1/×2/×3).
5. **Viewport cookie sözleşmesi:** `cm_viewport_w`/`cm_viewport_h` (SameSite=Lax, 86400s) — PHP `DeviceManager::fromRequest()` okur.
6. **Event sırası:** resize → detect → (tier değişimi? navigate : device CSS değiştir) → `devicechange` → `DeviceLayoutUpdater.updateAll()` → ScaleManager `requestApply()` → `scale:applied`.
7. **Auth CSS akışı:** `auth-bundled.css` ÖNCE, `d-auth-{device}.css` SONRA (override); `_home.css` asla import edilmez.
8. **Geriye dönük uyumluluk:** cihaz adları/aralıkları sabittir; `Object.freeze` yapıları yalnızca genişletilir; legacy köprüler ve cookie adları korunur. Detay: [[device-breakpoint-guide]] §6.

---

## 12. IFRAME RENDER (DEVICE) DESNİ

> **Durum (doğrulanmış):** Kod tabanında iframe tabanlı bir render cihazı MEVCUT DEĞİLDİR. `router/DomPatcher.js` `DANGEROUS_ELEMENTS` listesinde `iframe, object, embed, applet` tanımlıdır ve SPA DOM patch'i sırasında bu elementleri temizler. Aşağıdaki desen bu nedenle **ÇIKARIM/ÖNERİ**'dir; mevcut sistem davranışı olarak sunulmaz.

### 12.1 Neden Kontrollü Olmalı?

1. **CSP (ADR-012):** `frame-src` / `frame-ancestors` direktifleri olmadan iframe yüklemesi CSP tarafından engellenir.
2. **DomPatcher:** SPA patch edilen içerikteki iframe'ler güvenlik nedeniyle silinir.
3. **XSS:** `srcdoc`/`data:` URI ile kullanıcı verisi enjekte etmek yasak seviyededir.

### 12.2 Önerilen Desen (ÇIKARIM)

```html
<!-- Yalnız sunucu kontrollü, allow-list'li kaynak -->
<iframe
  src="https://embed.example.com/player/{id}"
  title="Müzik çalar"                       <!-- a11y zorunlu -->
  loading="lazy"
  sandbox="allow-scripts allow-same-origin" <!-- minimum izin -->
  referrerpolicy="strict-origin-when-cross-origin"
  allow="encrypted-media"></iframe>
```

```javascript
// postMessage sözleşmesi (ÇIKARIM — implementasyon yoktur)
window.addEventListener('message', (e) => {
  if (e.origin !== 'https://embed.example.com') return;  // origin kontrolü zorunlu
  if (typeof e.data?.type !== 'string') return;
  // eventBus.emit('iframe:' + e.data.type, e.data.payload);
});
```

### 12.3 Etkinleştirme Önkoşulları

1. CSP header'ına `frame-src https://embed.example.com;` eklenir (Security Engineer onayı — L1).
2. `DomPatcher` allow-list'e bu origin eklenir (şu an tüm iframe'leri temizler).
3. Çapraz cihaz testi: embedded (RPi5) iframe'i desteklemezse fallback bloğu `DeviceManager::isEmbedded()` ile koşullanır.

---

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.1.0 |
| **Bölüm Sayısı** | 13 |
| **Kapsanan Sistem** | Scale, Router, CSS, Device, Theme, Iframe (öneri) |
| **ADR Uyumlu** | ✅ 001, 044, 045 |
| **Zero Hallucination** | ✅ (kod tabanlı; ScaleManager kuralları ve API v1.1.0'da kaynak kodla eşitlendi; §12 ÇIKARIM etiketli) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
**Mode:** Red Team · Human Mode · Truth Mode
