---
title: "Animasyon Sistemi"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Animasyon Sistemi

## Genel Bakış

COREMUSIC animasyon sistemi, micro-interactions, page transitions ve loading states için standardize edilmiş animasyonları yönetir. CSS transitions ve animations kullanılır, prefers-reduced-motion tercihine saygı duyulur.

## Animasyon Kategorileri

```
animasyonlar/
├── transitions/        ← Hover, focus, active durumları
├── animations/         ← Sürekli animasyonlar
├── micro-interactions/ ← Küçük etkileşim animasyonları
├── page-transitions/   ← Sayfa geçişleri
├── loading/            ← Loading spinner, skeleton
└── reduced-motion/     ← Reduced motion fallback
```

## Transition Token'ları

```scss
// tokens/_transitions.scss

// Duration
$transition-fast: 150ms;
$transition-normal: 250ms;
$transition-slow: 350ms;
$transition-slower: 500ms;

// Easing
$ease-default: cubic-bezier(0.4, 0, 0.2, 1);
$ease-in: cubic-bezier(0.4, 0, 1, 1);
$ease-out: cubic-bezier(0, 0, 0.2, 1);
$ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
$ease-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
$ease-elastic: cubic-bezier(0.175, 0.885, 0.32, 1.275);

// CSS Custom Properties
:root {
  --transition-fast: 150ms;
  --transition-normal: 250ms;
  --transition-slow: 350ms;
  --transition-slower: 500ms;

  --ease-default: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-in: cubic-bezier(0.4, 0, 1, 1);
  --ease-out: cubic-bezier(0, 0, 0.2, 1);
  --ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
  --ease-elastic: cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
```

## Kod Örnekleri

### Temel Transition Mixin'leri

```scss
// mixins/_transitions.scss

@mixin transition($properties...) {
  transition-property: $properties;
  transition-duration: $transition-normal;
  transition-timing-function: $ease-default;
}

@mixin transition-fast($properties...) {
  transition-property: $properties;
  transition-duration: $transition-fast;
  transition-timing-function: $ease-default;
}

@mixin transition-slow($properties...) {
  transition-property: $properties;
  transition-duration: $transition-slow;
  transition-timing-function: $ease-default;
}

@mixin transition-bounce($properties...) {
  transition-property: $properties;
  transition-duration: $transition-slow;
  transition-timing-function: $ease-bounce;
}

// Transition groups
@mixin transition-colors {
  @include transition(color, background-color, border-color, box-shadow);
}

@mixin transition-transform {
  @include transition(transform);
}

@mixin transition-opacity {
  @include transition(opacity);
}

@mixin transition-all {
  @include transition(all);
}
```

### Hover & Focus Animasyonları

```scss
// components/_button.scss extend
.btn {
  // ... temel stiller ...

  // Hover animasyonu
  &:hover {
    @include transition(transform, box-shadow);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  &:active {
    transform: translateY(0);
    box-shadow: var(--shadow-sm);
  }
}

// Card hover
.card {
  @include transition(transform, box-shadow);

  &:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);

    .card__image {
      transform: scale(1.05);
    }

    .card__play {
      opacity: 1;
      transform: scale(1);
    }
  }
}

.card__image {
  @include transition(transform);
}

.card__play {
  opacity: 0;
  transform: scale(0.8);
  @include transition(opacity, transform);
}
```

### Micro-Interactions

```scss
// animations/_micro-interactions.scss

// Like/Favorite animasyonu
@keyframes like-pop {
  0% { transform: scale(1); }
  25% { transform: scale(1.3); }
  50% { transform: scale(0.9); }
  100% { transform: scale(1); }
}

.btn--like.is-active {
  animation: like-pop 0.4s $ease-bounce;
  color: var(--color-error);
}

// Ripple effect
@keyframes ripple {
  to {
    transform: scale(4);
    opacity: 0;
  }
}

.ripple {
  position: absolute;
  border-radius: 50%;
  transform: scale(0);
  background: rgba(255, 255, 255, 0.3);
  animation: ripple 0.6s linear;
  pointer-events: none;
}

// Progress bar animasyonu
@keyframes progress-indeterminate {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(400%);
  }
}

.progress-bar--loading .progress-bar__fill {
  width: 25%;
  animation: progress-indeterminate 1.5s $ease-in-out infinite;
}

// Volume slider
.volume-slider {
  @include transition(transform);

  &:hover {
    transform: scaleY(1.1);
    transform-origin: bottom;
  }
}

// Waveform animation
@keyframes waveform {
  0%, 100% { height: 4px; }
  50% { height: 20px; }
}

.waveform__bar {
  animation: waveform 1.2s $ease-in-out infinite;

  @for $i from 1 through 12 {
    &:nth-child(#{$i}) {
      animation-delay: #{$i * 0.1}s;
    }
  }
}
```

### Page Transitions

```scss
// animations/_page-transitions.scss

// Fade in
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

// Slide up
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

// Slide in from right
@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

// Scale in
@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

// Stagger children animation
.stagger-children > * {
  animation: slideUp 0.5s $ease-out both;

  @for $i from 1 through 12 {
    &:nth-child(#{$i}) {
      animation-delay: #{$i * 0.05}s;
    }
  }
}

// Page enter
.page-enter {
  animation: fadeIn 0.3s $ease-out;
}

// Page leave
.page-leave {
  animation: fadeIn 0.2s $ease-in reverse;
}
```

### Loading States

```scss
// animations/_loading.scss

// Spinner
@keyframes spin {
  to { transform: rotate(360deg); }
}

.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid var(--color-border);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;

  &--sm {
    width: 16px;
    height: 16px;
    border-width: 2px;
  }

  &--lg {
    width: 40px;
    height: 40px;
    border-width: 4px;
  }
}

// Pulse
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.skeleton {
  background: linear-gradient(
    90deg,
    var(--color-bg-subtle) 25%,
    var(--color-border) 50%,
    var(--color-bg-subtle) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: $radius-md;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

// Skeleton blocks
.skeleton--text {
  height: 1em;
  width: 100%;
  margin-bottom: 0.5em;
}

.skeleton--title {
  height: 1.5em;
  width: 60%;
  margin-bottom: 0.5em;
}

.skeleton--avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.skeleton--image {
  width: 100%;
  aspect-ratio: 1;
}

// Dots loading
@keyframes dots {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1); }
}

.loading-dots {
  display: flex;
  gap: 4px;

  &__dot {
    width: 8px;
    height: 8px;
    background: var(--color-primary);
    border-radius: 50%;
    animation: dots 1.4s infinite ease-in-out both;

    &:nth-child(1) { animation-delay: -0.32s; }
    &:nth-child(2) { animation-delay: -0.16s; }
    &:nth-child(3) { animation-delay: 0; }
  }
}
```

### Reduced Motion

```scss
// mixins/_reduced-motion.scss

@mixin reduced-motion {
  @media (prefers-reduced-motion: reduce) {
    @content;
  }
}

// Global reduced motion
@include reduced-motion {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}

// Component-level reduced motion
.btn {
  &:hover {
    transform: none;  // Default
    box-shadow: var(--shadow-md);

    @include reduced-motion {
      box-shadow: none;
    }
  }
}

.card {
  &:hover {
    transform: none;  // Default

    @include reduced-motion {
      transform: none;
    }
  }
}
```

### GSAP Entegrasyonu (Opsiyonel)

```javascript
// animations/gsap-setup.js
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Respect reduced motion
const prefersReducedMotion = window.matchMedia(
  '(prefers-reduced-motion: reduce)'
).matches;

if (prefersReducedMotion) {
  gsap.globalTimeline.timeScale(100); // Animasyonları atla
}

// Stagger animation helper
export function staggerReveal(selector, options = {}) {
  const defaults = {
    y: 30,
    opacity: 0,
    duration: 0.6,
    stagger: 0.1,
    ease: 'power2.out'
  };

  const config = { ...defaults, ...options };

  if (prefersReducedMotion) {
    config.duration = 0;
    config.stagger = 0;
  }

  gsap.from(selector, config);
}

// Parallax helper
export function parallax(selector, options = {}) {
  if (prefersReducedMotion) return;

  const defaults = { speed: 0.5 };
  const config = { ...defaults, ...options };

  gsap.to(selector, {
    yPercent: -50 * config.speed,
    ease: 'none',
    scrollTrigger: {
      trigger: selector,
      start: 'top bottom',
      end: 'bottom top',
      scrub: true
    }
  });
}
```

## Animasyon Kullanım Kuralları

| Kural | Açıklama |
|---|---|
| Purposeful | Animasyon bir amaca hizmet etmeli |
| Brief | 200-500ms arası optimal |
| Smooth | 60fps hedefle |
| Respectful | prefers-reduced-motion'e saygı |
| Consistent | Aynı tür aynı animasyon |
| Non-blocking | Animasyon UI'ı bloklamamalı |

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| GSAP | 3.12+ | Complex animations (opsiyonel) |
| @gsap/scrolltrigger | 3.12+ | Scroll animations (opsiyonel) |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Orta (Polish feature)
