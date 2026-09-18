---
title: "K11 — Kullanıcı Deneyimi Katmanı (UX Layer)"
type: architecture
category: layer-definition
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k11-ux-layer.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K11
  component_count: 40
  adr:
    - "[[ADR-001-vanilla-js-itcss]]"
    - "[[ADR-044-dynamic-user-theme-engine]]"
    - "[[ADR-045-multi-domain-view-mode-architecture]]"
  github:
    - name: "ITCSS"
      url: "https://github.com/itcss-navitcss/itcss"
    - name: "BEM"
      url: "https://github.com/bem/bem"
    - name: "Open Props"
      url: "https://github.com/argyleink/open-props"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K11 — Kullanıcı Deneyimi Katmanı (UX Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic ekosistemi için CSS mimarisi (ITCSS 9-layer), BEM naming, design tokens, cihaz yönetimi, tema motoru, erişilebilirlik ve PWA. 40 bileşen.

---

## 1. Genel Bakış

K11 katmanı, CoreMusic'in tüm frontend UI katmanını tanımlar. ITCSS 9-layer yapısı, BEM naming convention, CSS custom properties ile cihaz bazlı responsive tasarım.

### 1.1 UX Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                    K11 — UX LAYER (40)                              │
├──────────────┬──────────────┬──────────────┬────────────────────────┤
│  ITCSS (9)   │  BEM (3)     │  TOKENS (4)  │  DEVICE (4)            │
│              │              │              │                        │
│  01_Settings │  Block       │  Color       │  Device Manager PHP    │
│  02_Tools    │  Element     │  Spacing     │  Device CSS            │
│  03_Generic  │  Modifier    │  Typography  │  Layout Tokens         │
│  04_Elements │              │  Z-Index     │                        │
│  05_Objects  │              │              │                        │
│  06_Components│             │              │                        │
│  07_Utilities│             │              │                        │
│  08_State    │             │              │                        │
│  09_Theme    │             │              │                        │
├──────────────┴──────────────┴──────────────┴────────────────────────┤
│  THEME (3)   │  A11Y (4)    │  PWA (3)     │  COMPONENTS (5)        │
│              │              │              │                        │
│  Theme Eng.  │  ARIA        │  Manifest    │  Toast                 │
│  Theme JS    │  Keyboard    │  SW          │  Modal                 │
│  Theme Vars  │  Focus       │  Offline     │  Dropdown              │
│              │  Contrast    │              │  Animation             │
│              │              │              │  View Transition       │
└──────────────┴─────────────┴──────────────┴────────────────────────┘
```

---

## 2. ITCSS 9-Layer (9 Bileşen)

Inverted Triangle CSS — specificity sırasına göre katmanlama.

| # | Katman | Dosya | İçerik |
|---|--------|-------|--------|
| 1 | 01_Settings | `a-settings.css` | Global değişkenler, design tokens |
| 2 | 02_Tools | `a-tools.css` | Mixin'ler, fonksiyonlar (Sass) |
| 3 | 03_Generic | `b-reset.css`, `b-normalize.css` | Reset/normalize stilleri |
| 4 | 04_Elements | `c-elements.css` | HTML element stilleri (h1, p, a) |
| 5 | 05_Objects | `d-objects.css` | Layout objeleri (container, grid) |
| 6 | 06_Components | `e-components.css` | UI bileşenleri (button, card) |
| 7 | 07_Utilities | `f-utilities.css` | Yardımcı sınıflar (text-center) |
| 8 | 08_State | `g-state.css` | Durum stilleri (.is-active, .is-hidden) |
| 9 | 09_Theme | `h-theme.css` | Tema değişkenleri (dark, light) |

### 2.1 ITCSS Kullanım Kuralları

| Kural | Açıklama |
|-------|----------|
| Specificity artışı | 01 → 09 specificity artar |
| Override yasağı | Alt katman üst katmanı override edemez |
| Utils son | 07_Utilities her zaman son kullanılır |
| State izole | 08_State sadece `!important` ile |

---

## 3. BEM Naming Convention (3 Bileşen)

Block Element Modifier — CSS sınıf isimlendirme standardı.

| # | Bileşen | Tanım | Örnek |
|---|---------|-------|-------|
| 10 | BEM Block | Bağımsız bileşen | `.player`, `.card`, `.nav` |
| 11 | BEM Element | Block içindeki alt bileşen | `.player__controls`, `.card__title` |
| 12 | BEM Modifier | Varyasyon | `.player--active`, `.card--large` |

### 3.1 BEM Örnekleri

```css
/* Block */
.player { }

/* Element */
.player__controls { }
.player__progress { }
.player__volume { }

/* Modifier */
.player--active { }
.player--compact { }
.player__progress--full { }

/* State */
.is-playing { }
.is-paused { }
.is-loading { }
```

---

## 4. Design Tokens (4 Bileşen)

CSS custom properties ile merkezi token yönetimi.

| # | Token | Dosya | Değerler |
|---|-------|-------|----------|
| 13 | Color Tokens | `a-color-tokens.css` | Primary, Secondary, Accent, Neutral |
| 14 | Spacing Tokens | `a-spacing-tokens.css` | 4px base, scale: 4, 8, 12, 16, 24, 32, 48, 64 |
| 15 | Typography Tokens | `a-typography-tokens.css` | Font family, size scale, line-height, weight |
| 16 | Z-Index Tokens | `a-zindex-tokens.css` | Dropdown: 1000, Sticky: 1100, Modal: 1200, Toast: 1300 |

### 4.1 Token Değerleri

```css
:root {
  /* Colors */
  --color-primary: #ff4fd8;
  --color-secondary: #4f9fff;
  --color-accent: #a0a0b0;
  --color-bg: #0a0a0f;
  --color-surface: #1a1a2e;
  --color-text: #ffffff;

  /* Spacing */
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 12px;
  --space-lg: 16px;
  --space-xl: 24px;
  --space-2xl: 32px;
  --space-3xl: 48px;
  --space-4xl: 64px;

  /* Typography */
  --font-family: 'Inter', sans-serif;
  --font-size-xs: 12px;
  --font-size-sm: 14px;
  --font-size-base: 16px;
  --font-size-lg: 18px;
  --font-size-xl: 24px;
  --font-size-2xl: 32px;
  --font-size-3xl: 48px;
  --line-height: 1.5;
  --font-weight-normal: 400;
  --font-weight-medium: 500;
  --font-weight-bold: 700;

  /* Z-Index */
  --z-dropdown: 1000;
  --z-sticky: 1100;
  --z-modal: 1200;
  --z-toast: 1300;
}
```

---

## 5. Device Management (4 Bileşen)

Cihaz bazlı CSS ve layout yönetimi.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 17 | Device Manager PHP | Cihaz tespiti ve yapılandırma | PHP 8.4 |
| 18 | Device CSS | Cihaz bazlı behavioral override (7 dosya) | CSS |
| 19 | Layout Tokens | Cihaz bazlı layout token'ları | CSS Custom Properties |
| 20 | Device Loader JS | Client-side cihaz tespiti ve cookie yazma | Vanilla JS |

### 5.1 4-Tier Conditional Rendering

| Tier | Cihazlar | Viewport | Layout |
|------|----------|----------|--------|
| Tier 1: Phone | PHONE | ≤767px | Tek sütun, dikey scroll |
| Tier 2: Embedded | EMBEDDED, TABLET | ≤1024px | 42/58 split, 2×2 widget |
| Tier 3: Wide | LAPTOP, DESKTOP | 1025-2560px | 3-sütun, tam widget |
| Tier 4: 4K | FOUR_K_TV, FOUR_K_MON | ≥2561px | 4K ölçekli |

### 5.2 Responsive Breakpoints

```css
/* Default: 1024px (RPi5 embedded) */
:root {
  --header-h: 60px;
  --footer-h: 90px;
  --content-h: 450px;
}

/* Tablet: 768-1024px */
@media (min-width: 768px) and (max-width: 1024px) { }

/* Desktop: ≥1920px */
@media (min-width: 1920px) {
  --header-h: 70px;
  --footer-h: 104px;
  --content-h: 906px;
}

/* 4K TV: ≥3840px */
@media (min-width: 3840px) {
  --header-h: 80px;
  --footer-h: 120px;
  --content-h: 1960px;
}

/* Mobile: ≤767px */
@media (max-width: 767px) {
  --footer-h: 80px;
}
```

---

## 6. Theme Engine (3 Bileşen)

Dinamik tema yönetimi (cinsiyet bazlı, ADR-044).

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 21 | Theme Engine PHP | DB + user gender çözümleme | PHP 8.4 |
| 22 | Theme Manager JS | CSS custom properties ile anında geçiş | Vanilla JS |
| 23 | Theme Variables | Tema bazlı CSS değişkenleri | CSS Custom Properties |

### 6.1 Tema Değerleri

| Cinsiyet | Ana Renk | İkincil Renk | Varsayılan |
|----------|----------|--------------|------------|
| female | #ff4fd8 (pembe) | #ff8fe8 | — |
| male | #4f9fff (mavi) | #8fc4ff | — |
| neutral | #a0a0b0 (gri) | #c0c0d0 | ✅ |

### 6.2 Tema Değişiklik Akışı

```
Kullanıcı → Tema Seçimi
  → ThemeManager JS (#22)
    → data-gender attribute güncelle
    → CSS custom property güncelle
    → Anında geçiş (sayfa yenileme yok)
  → Theme Engine PHP (#21)
    → DB'ye kaydet (user_preferences)
```

---

## 7. Accessibility (4 Bileşen)

WCAG 2.2 AA uyumluluk bileşenleri.

| # | Bileşen | Tanım | Standart |
|---|---------|-------|----------|
| 24 | ARIA | Semantic HTML ve ARIA attributes | WCAG 4.1.2 |
| 25 | Keyboard | Keyboard navigasyonu ve shortcut'lar | WCAG 2.1.1 |
| 26 | Focus | Focus visible ve focus management | WCAG 2.4.7 |
| 27 | Contrast | Renk kontrastı (min 4.5:1) | WCAG 1.4.3 |

### 7.1 WCAG 2.2 AA Kuralları

| Kural | Gereksinim | Uygulama |
|-------|------------|----------|
| Touch Target | Min 48×48px (Phone/Embedded) | `min-width: 48px; min-height: 48px` |
| Focus Visible | `:focus-visible` outline | `outline: 2px solid currentColor` |
| Contrast | Min 4.5:1 normal text | Token-based color selection |
| Keyboard | Tüm interaktif elementler keyboard accessible | `tabindex`, `keydown` |
| Skip Link | Ana içeriğe atlama linki | `<a href="#main" class="skip-link">` |
| Reduced Motion | `prefers-reduced-motion` desteği | `@media (prefers-reduced-motion: reduce)` |

```css
/* Skip Link */
.skip-link {
  position: absolute;
  top: -40px;
  left: 0;
  background: var(--color-primary);
  color: var(--color-text);
  padding: var(--space-sm) var(--space-md);
  z-index: var(--z-toast);
  transition: top 0.3s;
}
.skip-link:focus {
  top: 0;
}

/* Focus Visible */
:focus-visible {
  outline: 2px solid currentColor;
  outline-offset: 2px;
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## 8. PWA (3 Bileşen)

Progressive Web App desteği.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 28 | PWA Manifest | Uygulama manifest (name, icons, theme) | `manifest.json` |
| 29 | Service Worker | Offline caching ve background sync | Service Worker API |
| 30 | Offline Support | Çevrimdışı medya erişimi | Cache API |

---

## 9. UI Components (5 Bileşen)

Paylaşılan UI bileşenleri.

| # | Bileşen | Tanım | CSS Sınıfı |
|---|---------|-------|------------|
| 31 | Toast | Bildirim toast'ları (success, error, info) | `.toast`, `.toast--success` |
| 32 | Modal | Modal dialog (onay, form, bilgi) | `.modal`, `.modal__overlay` |
| 33 | Dropdown | Dropdown menü (seçim, filtre) | `.dropdown`, `.dropdown__menu` |
| 34 | Animation CSS | Animasyon tanımları (fade, slide, scale) | `.animate-fade-in` |
| 35 | View Transition | View Transition API entegrasyonu (ADR-048) | `::view-transition-*` |

---

## 10. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | Sebep |
|----------|----------|-------|
| React / Vue / Angular | Vanilla JS (ADR-001) | Framework yasak |
| `innerHTML` | `DOMParser` + `TrustedTypes` | XSS riski |
| `var` | `const` / `let` | Scope sorunu |
| Hardcoded `height: 90px` | `height: var(--footer-h)` | Token kullanımı |
| Hardcoded `width: 280px` | `width: var(--sidebar-w)` | Token kullanımı |
| `home-1024.html` | Tek HTML + responsive CSS | Guardrail #17 |
| `if (screenWidth === 1024)` | CSS media query + var() | Tek bileşen ilkesi |

---

## 11. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| K11 UX Layer | K10 Application | Sayfa bileşenleri |
| K11 UX Layer | [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS |
| K11 UX Layer | [[ADR-044-dynamic-user-theme-engine]] | Tema motoru |
| K11 UX Layer | [[ADR-045-multi-domain-view-mode-architecture]] | View mode |
| K11 → ui-design/00-mockup-index | Mockup | 19 PNG mockup |
| K11 → ui-design/01-component-inventory | C01-C16 | Bileşen envanteri |
| K11 → ui-design/tokens/design-tokens-master | Tokens | Master design tokens |
| K11 → ITCSS GitHub | Referans | ITCSS standardı |
| K11 → BEM GitHub | Referans | BEM standardı |
| K11 → Open Props | Referans | CSS props |

---

## 12. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | draft |
| Total Components | 40 |
| ITCSS Layers | 9 |
| BEM Components | 3 |
| Design Tokens | 4 |
| Device Components | 4 |
| Theme Components | 3 |
| Accessibility | 4 |
| PWA Components | 3 |
| UI Components | 5 |
| ADR Coverage | 3 ADR referansı |
| GitHub References | ITCSS, BEM, Open Props |
| WCAG Level | AA |

---

## Class AB UX Entegrasyonu

- [[electronics/amplifier-classab-circuit]] — Amplifikatör kontrol UI'u
- [[electronics/power-supply-classab]] — Güç durumu UI bileşeni
- [[electronics/thermal-design-classab]] — Sıcaklık göstergesi UI

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
