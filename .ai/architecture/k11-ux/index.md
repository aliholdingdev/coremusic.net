---
title: "K11 Kullanıcı Deneyimi Katmanı"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# K11 Kullanıcı Deneyimi Katmanı

## Genel Bakış

K11, COREMUSIC'in ön yüz kullanıcı deneyimini yöneten katmandır. ITCSS mimarisi üzerine inşa edilen bu katman, tasarım tokenları, tema motoru, erişilebilirlik standartları ve yeniden kullanılabilir bileşen kütüphanesini kapsar. PWA desteği ile masaüstü ve mobil platformlarda tutarlı bir deneyim sunar.

## Mimari Yapı

```
K11-UX/
├── index.md                 ← Bu dosya (genel bakış)
├── itcss-9-layer.md         ← ITCSS 9 katmanlı CSS mimarisi
├── bem-naming.md            ← BEM isimlendirme kuralı
├── design-tokens.md         ← Tasarım tokenları
├── theme-engine.md          ← Tema motoru, CSS değişkenleri
├── pwa-features.md          ← PWA özellikleri, service worker
├── accessibility-wcag.md    ← WCAG 2.2 AA erişilebilirlik
├── responsive-design.md     ← Responsive breakpoints
├── ui-components.md         ← Bileşen kütüphanesi
├── animation-system.md      ← Animasyon sistemi
├── icon-library.md          ← İkon kütüphanesi
├── typography-scale.md      ← Tipografi ölçeği
├── color-system.md          ← Renk sistemi
└── spacing-system.md        ← Boşluk sistemi
```

## Katman Sorumlulukları

| Sorumluluk | Açıklama |
|---|---|
| CSS Mimarisi | ITCSS 9 katman, BEM isimlendirme |
| Tasarım Sistemi | Token, renk, tipografi, boşluk |
| Tema Yönetimi | Dark/light toggle, CSS değişkenleri |
| Erişilebilirlik | WCAG 2.2 AA, ARIA, focus yönetimi |
| Responsive | Mobile-first breakpoints |
| Bileşenler | Reusable UI component library |
| Animasyonlar | Transitions, micro-interactions |
| İkonlar | SVG sprite, icon font |
| PWA | Service worker, manifest, offline |

## Teknik Detaylar

### Technology Stack

- **CSS**: SCSS (Sass) + CSS Custom Properties
- **JS**: Vanilla JS / Alpine.js (hafif reaktivite)
- **Build**: PostCSS + Autoprefixer + CSSNano
- **PWA**: Workbox (Google service worker kütüphanesi)
- **Test**: axe-core (erişilebilirlik), Lighthouse (performans)

### Design Token Pipeline

```
design-tokens.json
    ↓ Style Dictionary
    ↓
├── _variables.scss      (SCSS değişkenleri)
├── _tokens.css          (CSS custom properties)
├── tokens.android.json  (Android resource)
├── tokens.ios.json      (iOS asset)
└── tokens.svg           (SVG renk paleti)
```

### Component Architecture

Her bileşen şu yapıya sahiptir:

```scss
// Bileşen yapısı
.component-name {
  // 1. Custom properties (tema desteği)
  // 2. Layout (flex/grid)
  // 3. Spacing (token-based)
  // 4. Typography (token-based)
  // 5. Color (semantic tokens)
  // 6. States (hover, focus, active)
  // 7. Animations (transitions)
  // 8. Responsive (breakpoint overrides)
  // 9. Accessibility (focus-visible, sr-only)
}
```

### Performance Budget

| Metrik | Hedef | Kritik |
|---|---|---|
| FCP | < 1.5s | < 0.8s |
| LCP | < 2.5s | < 1.8s |
| CLS | < 0.1 | < 0.05 |
| INP | < 200ms | < 100ms |
| CSS Bundle | < 50KB | < 30KB |
| JS Bundle | < 100KB | < 60KB |

### Browser Support

| Browser | Minimum Version | Desteğe Göre |
|---|---|---|
| Chrome | 90+ | Tam |
| Firefox | 88+ | Tam |
| Safari | 14+ | Tam |
| Edge | 90+ | Tam |
| iOS Safari | 14+ | Tam |
| Chrome Android | 90+ | Tam |

## Kod Örnekleri

### Temel Import Yapısı

```scss
// style.scss - Ana giriş noktası
@import 'itcss/settings/variables';
@import 'itcss/settings/tokens';

@import 'itcss/tools/mixins';
@import 'itcss/tools/functions';

@import 'itcss/generic/reset';
@import 'itcss/generic/normalize';

@import 'itcss/elements/base';
@import 'itcss/elements/typography';

@import 'itcss/objects/layout';
@import 'itcss/objects/grid';

@import 'itcss/components/buttons';
@import 'itcss/components/cards';
@import 'itcss/components/forms';

@import 'itcss/utilities/spacing';
@import 'itcss/utilities/visibility';

@import 'itcss/trumps/overrides';
```

### Temel Bileşen Kullanımı

```html
<!-- Temel bileşen yapısı -->
<div class="card card--dark">
  <img class="card__image" src="cover.webp" alt="Albüm kapağı" />
  <div class="card__body">
    <h3 class="card__title">Album Name</h3>
    <p class="card__meta">Artist · 2026</p>
    <button class="btn btn--primary btn--sm">
      Dinle
    </button>
  </div>
</div>
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| Sass (Dart Sass) | 1.77+ | CSS preprocessing |
| PostCSS | 8.4+ | CSS transformation |
| Autoprefixer | 10.4+ | Vendor prefix |
| CSSNano | 6.0+ | CSS minification |
| Workbox | 7.0+ | Service worker |
| axe-core | 4.8+ | Accessibility testing |
| Lighthouse | 11.0+ | Performance audit |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Kullanıcı deneyimi temel katmanı)
