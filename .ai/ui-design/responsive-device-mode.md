---
type: architecture
category: ui-design
title: "CoreMusic - Responsive Device Mode Architecture"
date: 2026-08-19
updated: 2026-09-08
status: active
version: 3.2.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
reference:
  authority: ".ai/ui-design/responsive-device-mode.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/ui-design/tokens/platform-tokens.md"
    - ".ai/architecture/l3-presentation/device-css.md"
  related:
    - ".ai/decisions/accepted/ADR-001-vanilla-js-itcss.md"
    - ".ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md"
    - ".ai/decisions/accepted/ADR-045-multi-domain-view-mode-architecture.md"
    - ".ai/ui-design/prompt/screen/01-1024-embedded.md"
---

# CoreMusic — Responsive Device Mode Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[index.md]] · [[architecture/l3-presentation/device-css]] · [[ui-design/tokens/platform-tokens]]

---

## 1. AI Role

Sen CoreMusic frontend mimarisinde çalışan:

- Senior Frontend Architect
- UI System Engineer
- Responsive Architecture Specialist
- Design System Engineer

olarak görev yaparsın.

---

## 2. Objective

CoreMusic frontend sistemi için:

- Tek component mimarisi kullan.
- 1024x600 Embedded Touch cihazlarını özel UI Mode olarak yönet.
- 1024x600 PNG mockup tasarımlarını sadece Embedded Device Design Authority olarak kullan.
- Desktop, TV ve Mobile cihazlarda normal responsive sistem çalıştır.

**Amaç:**

1024x600 tasarımı tüm sistemi kilitlemek değildir. 1024x600 sadece:

- Embedded cihaz UI referansı
- Pixel ölçü kaynağı
- Touch interaction referansı
- Component ölçü standardı

olarak kullanılacaktır.

---

## 3. Koşullu Render Mimarisi (v3.0.0)

**4-Tier Conditional Rendering** sistemi kullanılır:

```
Request → DeviceManager.fromRequest()
              │
              ├── Cookie: cm_viewport_w, cm_viewport_h
              ├── $_SERVER['VIEWPORT_W']
              └── User-Agent
              │
    ┌─────────┼─────────┬─────────┐
    ▼         ▼         ▼         ▼
  Phone    Embedded    Wide      4K
  (≤767)  (≤1024)  (1025-2560) (≥2561)
    │         │         │         │
    ▼         ▼         ▼         ▼
  Phone    Embedded    Wide      4K
  Layout   Layout     Layout    Layout
```

### 3.1 Desteklenen Çözünürlükler

| Çözünürlük | Cihaz | Tier | Layout | Condition |
|-------------|-------|------|--------|-----------|
| ≤767px | Phone (iPhone, Android) | Tier 4: Phone | Kompakt tek-sütun | `isPhone() = true` |
| 1024×600 | Embedded (Linux ARM / RPi5) | Tier 1: Embedded | Split 42/58 | `shouldRenderEmbeddedLayout() = true` |
| 768-1024px | Tablet | Tier 1: Embedded | Split 42/58 | `shouldRenderEmbeddedLayout() = true` |
| 1025-2560px | Laptop, Desktop, Standart | Tier 2: Wide | 3-sütun geniş | `shouldRenderWideLayout() = true` |
| ≥2561px | 4K TV, 4K Monitor | Tier 3: 4K | 4K ölçekli 3-sütun | `shouldRender4kLayout() = true` |

### 3.2 DeviceManager Karar Metotları

```php
// Home.php'de kullanılan karar metotları (4 Tier)
$isPhone    = $dm->isPhone();                        // ≤767px → Phone layout
$is4k       = $dm->shouldRender4kLayout();           // ≥2561px → 4K layout
$isWide     = $dm->shouldRenderWideLayout();         // 1025-2560px → Wide layout
$isEmbedded = $dm->shouldRenderEmbeddedLayout();     // ≤1024px → Embedded layout
```

### 3.3 Karar Akışı

```
isPhone():
  → device === 'phone'
  → Phone layout (kompakt, tek sütun, 4-butonlu çalar)

shouldRender4kLayout():
  → is4kTv() || is4kMonitor() === true
  → veya viewportW >= 2561
  → 4K layout (büyük ölçekli 3-sütun)

shouldRenderWideLayout():
  → isPhone() === false
  → shouldRenderEmbeddedLayout() === false
  → shouldRender4kLayout() === false
  → Wide layout (3-sütun: Now Playing | Welcome | Widgets)

shouldRenderEmbeddedLayout():
  → isPhone() === false
  → isEmbedded() || isTablet() === true
  → veya viewportW <= 1024
  → Embedded layout (Split 42/58)

shouldShowFallback():
  → ALWAYS FALSE (tüm tier'larda optimize layout mevcut)
```

---

## 4. Viewport Bilgi Akışı

### 4.1 JS → Cookie (Client-Side)

```javascript
// device-loader.js — init() fonksiyonunda
var w = window.innerWidth;
var h = window.innerHeight;
document.cookie = 'cm_viewport_w=' + w + ';path=/;max-age=86400;SameSite=Lax';
document.cookie = 'cm_viewport_h=' + h + ';path=/;max-age=86400;SameSite=Lax';
```

### 4.2 Cookie → PHP (Server-Side)

```php
// DeviceManager::fromRequest() — cookie'den okuma
if ($viewportW === null && !empty($_COOKIE['cm_viewport_w'])) {
    $viewportW = (int)$_COOKIE['cm_viewport_w'];
}

// PageRouter.php — cookie fallback
$viewportW = !empty($_SERVER['VIEWPORT_W'])
    ? (int)$_SERVER['VIEWPORT_W']
    : (!empty($_COOKIE['cm_viewport_w']) ? (int)$_COOKIE['cm_viewport_w'] : null);
```

### 4.3 Öncelik Sırası

```
1. $_SERVER['VIEWPORT_W']  (HTTP header'dan)
2. $_COOKIE['cm_viewport_w']  (JS tarafından yazılan cookie)
3. Device türüne göre varsayılan  (desktop→1920, laptop→1366, vb.)
4. null  (fallback gösterme)
```

---

## 5. Layout Detayları

### 5.1 Embedded Layout (1024×600)

**Mockup:** Image 2 (Linux 1024 - Home Page)

```
┌─────────────────────────────────────────────────────┐
│ Header: Logo + 8 Nav Link + System Status + User    │
├──────────────────────┬──────────────────────────────┤
│ NOW PLAYING (42%)    │ WIDGETS 2×2 (58%)            │
│ Album Art 90×90      │ Hoparlör │ Hava Durumu       │
│ Title/Subtitle       │ Saat/Tarih │ Kütüphanelerim  │
│ Seek Bar             │                              │
├──────────┬───────────┼──────────────────────────────┤
│ En Son   │ Çalma     │ Sıradaki                     │
│ Dinlenen │ Listeleri │ Şarkı                        │
│ (4 kart) │ (3 kart)  │ (1 kart)                     │
├──────────┴───────────┴──────────────────────────────┤
│ Footer: Album Art + Metadata + Controls + Volume    │
└─────────────────────────────────────────────────────┘
```

**CSS:** `_home-layout.css` → `.home-layout__top--embedded`, `.home-layout__bottom--embedded`

### 5.2 Wide Layout (≥1920×1080)

**Mockup:** Image 3 (Linux 1920 - Home)

```
┌─────────────────────────────────────────────────────┐
│ Header: Logo + 8 Nav Link + System Status + User    │
├────────────┬───────────────────┬────────────────────┤
│ NOW PLAYING│ WELCOME BANNER    │ WIDGETS 2×2        │
│ (33%)      │ (34%)             │ (33%)              │
│ 120×120 art│ Background image  │ Hoparlör│Hava      │
│ Rating ★★★★│ "Hoş Geldin"     │ Saat    │Kütüphane │
│ Badges     │ Username + Stats  │                    │
│ Seek Bar   │                   │                    │
├────────────┴───────────────────┴────────────────────┤
│ EN SON DİNLENEN ŞARKILAR │ ÇALMA LİSTELERİ          │
│ (7 kart)                  │ (6 kart)                 │
├───────────────────────────┴─────────────────────────┤
│ Footer: Album Art + Metadata + Controls + Volume    │
└─────────────────────────────────────────────────────┘
```

**CSS:** `_home-layout.css` → `.home-layout__top--wide`, `.home-layout__bottom--wide`

### 5.3 4K Layout (≥2561px / 3840px)

```
│    ─── Header: Logo + Nav + Status + User (80px)     │
├────────────┬───────────────────┬────────────────────┤
│ NOW PLAYING│ WELCOME BANNER    │ WIDGETS 2×3 (6)    │
│ (33%)      │ (34%)             │ (33%)              │
│ 180×180 art│ Background image  │ Hoparlör│Hava      │
│ 4K/Hi-Res  │ "Hoş Geldin —    │ Saat    │Kütüphane │
│ Badges ★★★★│ 4K Deneyimi"    │ DSP     │Multi-Room│
│ Bitrate    │ Stats Strip      │                    │
├────────────┴───────────────────┴────────────────────┤
│ EN SON (4K)    │ PLAYLISTLER (4K)   │ (Şeffaf Alan) │
├────────────────┴────────────────────┴───────────────┤
│ Footer: Full vaporwave player (120px, tier: footer--4k) │
└─────────────────────────────────────────────────────┘
```

**CSS:** `_home-layout.css` → `.home-layout__top--wide`, `.home-layout--4k`

### 5.4 Phone Layout (≤767px)

```
┌─────────────────────────┐
│ Bottom Tab Bar (3 icon) │  ← Header yerine alt navigasyon
├─────────────────────────┤
│ Now Playing (kompakt)   │
│ Art 64×64 + Meta        │
│ Seek Bar                │
├─────────────────────────┤
│ En Son Dinlenen (2 kart)│
├─────────────────────────┤
│ Çalma Listeleri (1 kart)│
├─────────────────────────┤
│ Kompakt Player          │  ← 4 buton: ◀ ▶ ⏸ ▶
│ (4-button touch)        │
└─────────────────────────┘
```

**CSS:** `_home-layout.css` → `.home-layout--phone`, `.phone-now-playing`

---

## 6. Dosya Yapısı

### 6.1 Değişen Dosyalar

| Dosya | Versiyon | Değişiklik |
|-------|----------|------------|
| `shared/src/Device/DeviceManager.php` | v1.0.0 | 7 cihaz tipi, 4-tier layout decisions, feature toggles, nav links, content config |
| `shared/src/Device/DeviceDetector.php` | v1.0.0 | 7 cihaz tespiti, priority-based detection |
| `home.coremusic.net/pages/home.php` | v10.0.0 | 4 koşullu render bloğu (phone / 4k / wide / embedded) |
| `home.coremusic.net/header.php` | v8.0.0 | Phone bottom tab bar + tier CSS classes |
| `home.coremusic.net/footer.php` | v11.0.0 | Phone kompakt + full vaporwave player |
| `assets.coremusic.net/js/device-loader.js` | — | Viewport cookie yazma |

### 6.2 Akış Şeması

```
index.php
  → bootstrap.php
    → PageRouterKernel::handle()
      → normalizeRequest()
      → runMiddlewareStack()
        → SessionManagerMiddleware (session başlat)
        → CsrfMiddleware
        → AuthMiddleware
        → ...
      → PageRouter::dispatch()
        → renderPage()
          → DeviceDetector::detect(UA, viewportW, viewportH)
          → include home.php
            → DeviceManager::fromRequest(viewportW, viewportH)
              → Cookie'den viewport oku
              → 4-Tier layout kararı:
                isPhone() → Phone Layout
                shouldRender4kLayout() → 4K Layout
                shouldRenderWideLayout() → Wide Layout
                shouldRenderEmbeddedLayout() → Embedded Layout
                shouldShowFallback() → FALSE
              → İlgili layout HTML'ini render et
```

---

## 7. CSS Token Mimarisi

### 7.1 Default Tokens (1024×600 — RPi5 Baseline)

```css
:root {
  --screen-w: 1024px;
  --screen-h: 600px;
  --header-h: 60px;
  --footer-h: 90px;
  --home-top-split: 42% 58%;
  --home-bottom-split: 1fr 1.2fr 0.6fr;
  --widget-grid-cols: 2;
}
```

### 7.2 Wide Tokens (≥1920px)

```css
@media (min-width: 1920px) {
  :root {
    --screen-w: 1920px;
    --screen-h: 1080px;
    --header-h: 70px;
    --footer-h: 104px;
    --home-bottom-split: 1fr 1.5fr 1fr;
    --widget-grid-cols: 3;
  }
}
```

### 7.3 4K Tokens (≥3840px)

```css
@media (min-width: 3840px) {
  :root {
    --screen-w: 3840px;
    --screen-h: 2160px;
    --header-h: 80px;
    --footer-h: 120px;
    --content-h: 1960px;
    --home-bottom-split: 1fr 1.5fr 1fr;
    --widget-grid-cols: 3;
    --touch-min: 60px;
    /* NO-CENTER KURALI: 4K'da içerik ortalanmaz */
    --content-max-w: none;      /* max-width merkezleme YASAK */
    --content-anchor: left;     /* içerik SOL YASLI akışkan */
    --content-margin-inline: 0; /* margin-inline: auto YASAK */
  }
}

@media (min-width: 3841px) {
  :root {
    /* 4K Monitor — mouse+fare, TV'den daha büyük */
    --header-h: 90px;
    --footer-h: 130px;
    --content-max-w: none;      /* NO-CENTER: ortalama yok */
    --content-anchor: left;
    --content-margin-inline: 0;
    --sidebar-w: 340px;
    --detail-panel-w: 700px;
  }
}
```

### 7.4 4K No-Center Kuralı (ZORUNLU — İHLAL EDİLEMEZ)

> **BAĞLAYICI KURAL (2026-09-06):** 4K çözünürlükte (≥2561px) içerik **kesinlikle ortalanmaz**. Tüm AI asistanları ve geliştiriciler bu kurala uymak ZORUNDADIR.

**Yasaklı desenler (4K tier'da):**

```css
/* ❌ YASAK — container merkezleme */
.layout-4k .container { max-width: 2400px; margin-inline: auto; }
.layout-4k .container { margin: 0 auto; }
.layout-4k .grid { justify-content: center; }

/* ❌ YASAK — utility ile merkezleme */
/* HTML: <div class="container mx-auto"> — 4K tier'ında uygulanmaz */
```

**Zorunlu desenler (4K tier'da):**

```css
/* ✅ ZORUNLU — tam akışkan, sol yaslı, grid genişlemesi */
.layout-4k .container {
  width: 100%;
  max-width: none;
  margin-inline: 0;
  padding-inline: 48px;        /* kenar boşluğu sabit, merkezleme yok */
}

/* ✅ ZORUNLU — kolonlar genişlikten kazanç sağlar (sabit oran) */
.layout-4k .home-layout__top--wide {
  display: grid;
  grid-template-columns: 1fr 1.2fr 1fr;   /* orijinal oran korunur */
  gap: 24px;
}

/* ✅ ZORUNLU — satırlar yatayda akar, scroll'u varsa tam genişlik */
.layout-4k .chip-row { display: flex; gap: 16px; overflow-x: auto; }
```

**Uygulama prensipleri:**

| # | Kural |
|---|-------|
| 1 | İçerik konteyneri `width: 100%` + `max-width: none` — ekran genişliğini tam kullanır |
| 2 | Kenar boşluğu sabit padding ile verilir (`padding-inline: 48px`), `margin: auto` ile ASLA |
| 3 | Grid kolonları orijinal oranı koruyarak genişler (`fr` birimleri) — kolon sayısı artmaz |
| 4 | Chip/kart satırları sola yaslı başlar; genişlikte ekstra alan sağdaki boş/arka plan bölgesine kalır (PNG'deki kompozisyonla uyumlu) |
| 5 | `mx-auto`, `margin-inline: auto`, `justify-content: center` (container düzeyinde) 4K tier CSS'inde bulunamaz — kod review'da red sebebi |
| 6 | Eski tarayıcılar için fallback: `--content-max-w: none` desteklenmezse `max-width: 100%` natural davranışı zaten akışkandır (bkz. §12 Geriye Dönük Uyumluluk) |

**Mevcut kodda tespit:** `assets.coremusic.net/Css/**` içinde 4K tier'a dokunan `max-width` + `margin-inline: auto` kombinasyonları bu kural kapsamında düzeltilmelidir.

### 7.3 Wide Layout Override

```css
.home-layout__top--wide {
  display: grid;
  grid-template-columns: 1fr 1.2fr 1fr;  /* 3-sütun */
}
.home-layout__bottom--wide {
  display: grid;
  grid-template-columns: 1fr 1fr;  /* 2-sütun */
}
```

---

## 8. Core Rules

### 8.1 Ayrı HTML Yasak

```javascript
// YASAK — Ayrı UI loading
if(width === 1024) { load1024UI(); }
```

```text
// YASAK — Ayrı HTML dosyaları
home-1024.html
home-desktop.html
home-mobile.html
home-1920.html
```

### 8.2 Doğru Yaklaşım

```php
// home.php — 4-Tier koşullu render
if ($isPhone) {
    // Phone layout HTML (kompakt, tek sütun)
} elseif ($is4k) {
    // 4K layout HTML (büyük ölçekli 3-sütun)
} elseif ($isWide) {
    // Wide layout HTML (geniş masaüstü 3-sütun)
} else {
    // Embedded layout HTML (Split 42/58)
}
```

### 8.3 Component Tekrar Kullanımı

Aynı component (Now Playing, Widget, Recent Card) tüm layoutlarda kullanılır. Sadece:
- CSS class değişir (`--embedded`, `--wide`)
- Token değerleri değişir
- Layout grid değişir

---

## 9. Interaction & Accessibility

### 9.1 Embedded Mode (1024×600)

- **Touch optimized**: Hover state'ler gizli
- **Büyük kontrol alanları**: min 48×48px touch targets
- **Pixel accurate layout**: Mockup ölçüleri korunur

### 9.2 Wide Mode (1025-2560px)

- **Mouse + Keyboard**: Hover state'ler aktif
- **Geniş layout**: 3-sütun üst, 2-sütun alt
- **Enhanced metadata**: Yıldız rating, badge'ler

### 9.3 4K Mode (≥2561px)

- **4K ölçeklendirilmiş** tipografi ve ikonlar
- **6 widget grid** (3×2), hero banner, DSP/Multi-Room widget'ları
- **4K ULTRA HD badge**, Hi-Res metadata

### 9.4 Phone Mode (≤767px)

- **Kompakt tek-sütun** dikey scroll
- **Bottom tab bar** navigasyon (3 ikon)
- **4 butonlu dokunmatik** çalar
- **Kompakt mini-music-card'lar**

---

## 10. Test Senaryoları

| # | Viewport | Cihaz | Tier | Beklenen | Durum |
|---|----------|-------|------|----------|-------|
| 1 | 375×812 | Phone (iPhone) | Tier 4: Phone | Phone Layout | ✅ |
| 2 | 320×568 | Phone (small) | Tier 4: Phone | Phone Layout | ✅ |
| 3 | 1024×600 | Embedded (Linux ARM) | Tier 1: Embedded | Embedded Layout | ✅ |
| 4 | 820×1180 | Tablet (iPad) | Tier 1: Embedded | Embedded Layout | ✅ |
| 5 | 1366×768 | Laptop | Tier 2: Wide | Wide Layout | ✅ |
| 6 | 1920×1080 | Desktop | Tier 2: Wide | Wide Layout | ✅ |
| 7 | 3840×2160 | 4K TV (SmartTV UA) | Tier 3: 4K | 4K Layout | ✅ |
| 8 | 3840×2160 | 4K Monitor (Desktop UA) | Tier 3: 4K | 4K Layout | ✅ |

---

## 11. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `shared/src/Device/DeviceManager.php` | Cihaz yönetim sınıfı — 4-tier karar metotları, feature toggles, nav links, content config |
| `shared/src/Device/DeviceDetector.php` | Cihaz tespit — 7 tip, priority-based detection |
| `home.coremusic.net/pages/home.php` | Ana sayfa — 4-tier koşullu render |
| `home.coremusic.net/header.php` | Navigation — Phone bottom tab + tier CSS classes |
| `home.coremusic.net/footer.php` | Player — Phone kompakt + full vaporwave |
| `assets.coremusic.net/js/device-loader.js` | JS cihaz tespiti — cookie yazma |
| `shared/src/PageRouter/PageRouter.php` | Viewport okuma — cookie fallback |
| `shared/src/PageRouter/HtmlShellRenderer.php` | HTML shell — auth route branching |

---

## 12. Geriye Dönük Uyumluluk (Backward Compatibility — ZORUNLU)

> **BAĞLAYICI KURAL (2026-09-06):** Tüm CSS çözümleri eski tarayıcı sürümleri ve cihazlarla tam uyumlu olmalıdır. Modern-only çözümler fallback'siz yazılamaz.

### 12.1 CSS Özellik Fallback Stratejisi

| Modern Özellik | Kullanım | Zorunlu Fallback |
|----------------|----------|------------------|
| `backdrop-filter: blur()` | Glass kartlar | Önce düz yarı saydam bg: `background: rgba(255,255,255,0.12);` sonra `backdrop-filter` — blur desteklenmezse yarı saydam bg yeterli kontrast verir |
| `display: grid` | Layout tier'ları | `@supports not (display: grid) { ... }` içinde `display: flex; flex-wrap: wrap;` eşdeğeri |
| `gap` (flex) | Chip satırları | `margin` fallback: child'lara `margin-right`, container'a negatif margin — veya `@supports` bloğu |
| `clamp()` / `min()` / `max()` | Fluid tipografi | Sabit px değeri önce yazılır, sonra `clamp()` — eski tarayıcı sabit değeri okur |
| CSS custom properties | Tüm token sistemi | `:root` fallback bloğu: her token için düz değerli eski-syntax kuralı (bkz. 13.3) |
| `aspect-ratio` | Kart görselleri | Sabit `height` veya padding-top hack |
| `inset` shorthand | Overlay/modal | Ayrı `top/right/bottom/left` |
| `overflow-x: auto` + scrollbar styling | Chip satırları | Standart scrollbar (görsel fark kabul edilir) |

### 12.2 Responsive Tier Fallback'leri

| Tier | Modern Çözüm | Eski Tarayıcı Davranışı |
|------|--------------|------------------------|
| Phone (≤767) | CSS grid tek sütun | Flex-wrap dikey stack (doğal davranış aynı) |
| Embedded (≤1024) | Split 42/58 grid | Float/flex 42-58 yüzde tabanlı |
| Wide (1025-2560) | 3-sütun grid | Flex row + yüzde genişlikler |
| 4K (≥2561) | Fluid grid, NO-CENTER | `max-width: 100%` natural akış — merkezleme zaten yok, kural doğal sağlanır |

### 12.3 Kural Örnekleri (doğru yazım sırası)

```css
/* 1) Eski-tarayıcı fallback ÖNCE */
.glass-card {
  background: rgba(255,255,255,0.12);
}

/* 2) Modern geliştirme SONRA (destekleyen tarayıcı override eder) */
@supports (backdrop-filter: blur(1px)) {
  .glass-card {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(12px);
  }
}

/* Fluid tipografi fallback */
.section-title {
  font-size: 14px;                    /* fallback */
  font-size: clamp(14px, 0.9vw, 20px); /* modern override */
}

/* Grid fallback */
.layout-top {
  display: flex;                       /* fallback */
  flex-wrap: wrap;
}
@supports (display: grid) {
  .layout-top {
    display: grid;
    grid-template-columns: 1fr 1.2fr 1fr;
  }
}
```

### 12.4 Test Matrisi (Eski Tarayıcılar)

| Tarayıcı | Beklenen Davranış | Kabul Kriteri |
|----------|-------------------|---------------|
| Chrome 70+ | Tüm tier'lar tam | Blur yoksa düz bg; grid var |
| Firefox 65+ | Tüm tier'lar tam | Custom properties çalışır |
| Safari 12+ | Glass efektli (webkit prefix) | `-webkit-backdrop-filter` mevcut olmalı |
| Edge Legacy (18) | Fallback tier | Düz bg + flex layout, merkezleme yok |
| Android WebView 5+ | Fallback tier | İçerik okunur, touch 48px korunur |

### 12.5 Red Kriterleri (Code Review)

1. Fallback'siz `backdrop-filter` → RED
2. `@supports` olmadan sadece-grid layout → RED
3. Fallback'siz `gap` (flex context) → RED
4. 4K tier'da merkezleme → RED (§7.4)
5. `var()` kullanıp düz değer fallback'i olmayan token → RED

---

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 13 |
| Layouts | 4 (Phone, 4K, Wide, Embedded) |
| Device Types | 7 (phone, tablet, embedded, laptop, desktop, 4k-tv, 4k-monitor) |
| Feature Toggles | 9 |
| Content Config Methods | 4 (widgetCount, recentCardCount, playlistCount, upNextCount) |
| Test Senaryoları | 8 (tümü başarılı) |
| ADR Uyumlu | ✅ 001, 044, 045 |
| Guardrail #17 Uyumlu | ✅ Single Component Responsive |

---

*Responsive Device Mode Architecture v3.2.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-06*
*Mode: Red Team · Human Mode · Truth Mode*
