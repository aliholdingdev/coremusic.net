---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Component Tokens (C01-C16)"
type: tokens
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/tokens/component-tokens.md"
  source_of_truth: ".ai/ui-design/02-component-inventory.md · .ai/ui-design/tokens/design-tokens-master.md"
---

# CoreMusic — Component Tokens (C01-C16)

**Zorunlu Bağlantılar:** [[design-tokens-master]] · [[02-component-inventory]] · [[platform-tokens]]

---

## 1. Amaç

16 UI bileşeni (C01-C16) için **bileşen bazlı token'ların** tek kaynağıdır. Her bileşenin boyut, renk, boşluk ve variant token'ları burada tanımlanır.

---

## 2. C01: NavLink Token'ları

```css
:root {
  --cm-navlink-padding-x: var(--cm-space-3);
  --cm-navlink-padding-y: var(--cm-space-2);
  --cm-navlink-gap: var(--cm-space-2);
  --cm-navlink-font-size: var(--cm-text-base);
  --cm-navlink-font-weight: var(--cm-font-medium);
  --cm-navlink-icon-size: 20px;
  --cm-navlink-radius: var(--cm-radius-md);
  --cm-navlink-color: var(--cm-text-secondary);
  --cm-navlink-color-active: var(--cm-primary);
  --cm-navlink-color-hover: var(--cm-text-primary);
  --cm-navlink-bg-hover: var(--cm-hover-bg);
  --cm-navlink-bg-active: var(--cm-selected-bg);
}
```

## 3. C02: Hero Banner Token'ları

```css
:root {
  --cm-hero-padding: var(--cm-space-8);
  --cm-hero-padding-phone: var(--cm-space-4);
  --cm-hero-radius: var(--cm-radius-2xl);
  --cm-hero-min-h: 280px;
  --cm-hero-min-h-phone: 200px;
  --cm-hero-title-size: var(--cm-text-4xl);
  --cm-hero-title-weight: var(--cm-font-bold);
  --cm-hero-subtitle-size: var(--cm-text-lg);
  --cm-hero-overlay: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 100%);
}
```

## 4. C03: Card Token'ları

```css
:root {
  --cm-card-padding: var(--cm-space-4);
  --cm-card-radius: var(--cm-radius-xl);
  --cm-card-bg: var(--cm-glass-bg);
  --cm-card-border: 1px solid var(--cm-glass-border);
  --cm-card-shadow: var(--cm-shadow-sm);
  --cm-card-shadow-hover: var(--cm-shadow-lg);
  --cm-card-blur: var(--cm-glass-blur);
  --cm-card-transition: var(--cm-transition-all);
  --cm-card-gap: var(--cm-space-4);

  /* Card Sizes */
  --cm-card-sm-w: 160px;
  --cm-card-md-w: 240px;
  --cm-card-lg-w: 320px;
  --cm-card-xl-w: 400px;

  /* Card Image */
  --cm-card-img-radius: var(--cm-radius-lg);
  --cm-card-img-aspect: 1 / 1;
  --cm-card-img-aspect-wide: 16 / 9;
}
```

## 5. C04: Button Token'ları

```css
:root {
  /* Base Button */
  --cm-btn-padding-x: var(--cm-space-4);
  --cm-btn-padding-y: var(--cm-space-2);
  --cm-btn-radius: var(--cm-radius-lg);
  --cm-btn-font-size: var(--cm-text-base);
  --cm-btn-font-weight: var(--cm-font-semibold);
  --cm-btn-transition: var(--cm-transition-all);
  --cm-btn-min-h: 36px;

  /* Primary Button */
  --cm-btn-primary-bg: var(--cm-primary);
  --cm-btn-primary-color: #ffffff;
  --cm-btn-primary-bg-hover: var(--cm-primary-light);
  --cm-btn-primary-bg-active: var(--cm-primary-dark);
  --cm-btn-primary-shadow: var(--cm-shadow-primary);

  /* Secondary Button */
  --cm-btn-secondary-bg: transparent;
  --cm-btn-secondary-color: var(--cm-text-primary);
  --cm-btn-secondary-border: 1px solid var(--cm-border-default);
  --cm-btn-secondary-bg-hover: var(--cm-hover-bg);

  /* Ghost Button */
  --cm-btn-ghost-bg: transparent;
  --cm-btn-ghost-color: var(--cm-text-secondary);
  --cm-btn-ghost-bg-hover: var(--cm-hover-bg);

  /* Danger Button */
  --cm-btn-danger-bg: var(--cm-error);
  --cm-btn-danger-color: #ffffff;
  --cm-btn-danger-bg-hover: var(--cm-error-light);

  /* Button Sizes */
  --cm-btn-sm-h: 32px;
  --cm-btn-sm-font: var(--cm-text-sm);
  --cm-btn-md-h: 36px;
  --cm-btn-lg-h: 44px;
  --cm-btn-lg-font: var(--cm-text-md);
}
```

## 6. C05: Input Token'ları

```css
:root {
  --cm-input-padding-x: var(--cm-space-3);
  --cm-input-padding-y: var(--cm-space-2-5);
  --cm-input-radius: var(--cm-radius-md);
  --cm-input-bg: var(--cm-bg-surface);
  --cm-input-border: 1px solid var(--cm-border-default);
  --cm-input-border-focus: 1px solid var(--cm-primary);
  --cm-input-border-error: 1px solid var(--cm-error);
  --cm-input-shadow-focus: var(--cm-shadow-focus);
  --cm-input-font-size: var(--cm-text-base);
  --cm-input-color: var(--cm-text-primary);
  --cm-input-placeholder: var(--cm-text-tertiary);
  --cm-input-min-h: 40px;
  --cm-input-transition: var(--cm-transition-colors);
}
```

## 7. C06: Tab Token'ları

```css
:root {
  --cm-tab-padding-x: var(--cm-space-4);
  --cm-tab-padding-y: var(--cm-space-2);
  --cm-tab-gap: var(--cm-space-1);
  --cm-tab-radius: var(--cm-radius-md);
  --cm-tab-font-size: var(--cm-text-sm);
  --cm-tab-font-weight: var(--cm-font-medium);
  --cm-tab-color: var(--cm-text-secondary);
  --cm-tab-color-active: var(--cm-primary);
  --cm-tab-bg-active: var(--cm-selected-bg);
  --cm-tab-border-active: 2px solid var(--cm-primary);
  --cm-tab-transition: var(--cm-transition-colors);
}
```

## 8. C07: Modal Token'ları

```css
:root {
  --cm-modal-radius: var(--cm-radius-2xl);
  --cm-modal-bg: var(--cm-bg-elevated);
  --cm-modal-border: 1px solid var(--cm-border-default);
  --cm-modal-shadow: var(--cm-shadow-2xl);
  --cm-modal-blur: var(--cm-glass-blur-lg);
  --cm-modal-padding: var(--cm-space-6);
  --cm-modal-max-w: 480px;
  --cm-modal-max-w-lg: 640px;
  --cm-modal-max-w-xl: 800px;
  --cm-modal-overlay-bg: var(--cm-bg-overlay);
  --cm-modal-transition: var(--cm-scale-in);
}
```

## 9. C08: Toggle Token'ları

```css
:root {
  --cm-toggle-w: 44px;
  --cm-toggle-h: 24px;
  --cm-toggle-radius: var(--cm-radius-full);
  --cm-toggle-bg: var(--cm-gray-700);
  --cm-toggle-bg-active: var(--cm-primary);
  --cm-toggle-knob-size: 18px;
  --cm-toggle-knob-color: #ffffff;
  --cm-toggle-knob-shadow: var(--cm-shadow-sm);
  --cm-toggle-transition: var(--cm-transition-all);
}
```

## 10. C09: Slider Token'ları

```css
:root {
  --cm-slider-track-h: 4px;
  --cm-slider-track-bg: var(--cm-gray-700);
  --cm-slider-track-bg-active: var(--cm-primary);
  --cm-slider-thumb-size: 14px;
  --cm-slider-thumb-bg: var(--cm-text-primary);
  --cm-slider-thumb-shadow: var(--cm-shadow-sm);
  --cm-slider-thumb-scale-hover: 1.2;
}
```

## 11. C10: Badge Token'ları

```css
:root {
  --cm-badge-padding-x: var(--cm-space-2);
  --cm-badge-padding-y: var(--cm-space-0-5);
  --cm-badge-radius: var(--cm-radius-full);
  --cm-badge-font-size: var(--cm-text-xs);
  --cm-badge-font-weight: var(--cm-font-semibold);
  --cm-badge-bg: var(--cm-primary-20);
  --cm-badge-color: var(--cm-primary);
}
```

## 12. C11: Avatar Token'ları

```css
:root {
  --cm-avatar-radius: var(--cm-radius-full);
  --cm-avatar-border: 2px solid var(--cm-border-default);
  --cm-avatar-sm: 24px;
  --cm-avatar-md: 32px;
  --cm-avatar-lg: 40px;
  --cm-avatar-xl: 56px;
  --cm-avatar-2xl: 80px;
}
```

## 13. C12: Tooltip Token'ları

```css
:root {
  --cm-tooltip-bg: var(--cm-bg-surface);
  --cm-tooltip-color: var(--cm-text-primary);
  --cm-tooltip-radius: var(--cm-radius-md);
  --cm-tooltip-padding: var(--cm-space-2) var(--cm-space-3);
  --cm-tooltip-font-size: var(--cm-text-xs);
  --cm-tooltip-shadow: var(--cm-shadow-md);
  --cm-tooltip-max-w: 200px;
  --cm-tooltip-arrow-size: 6px;
}
```

## 14. C13: Skeleton Token'ları

```css
:root {
  --cm-skeleton-bg: var(--cm-bg-surface);
  --cm-skeleton-shine: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.06) 50%, transparent 100%);
  --cm-skeleton-radius: var(--cm-radius-md);
  --cm-skeleton-duration: 1.5s;
}
```

## 15. C14: Dropdown Token'ları

```css
:root {
  --cm-dropdown-bg: var(--cm-bg-elevated);
  --cm-dropdown-border: 1px solid var(--cm-border-default);
  --cm-dropdown-shadow: var(--cm-shadow-xl);
  --cm-dropdown-radius: var(--cm-radius-lg);
  --cm-dropdown-padding: var(--cm-space-1);
  --cm-dropdown-item-padding: var(--cm-space-2) var(--cm-space-3);
  --cm-dropdown-item-radius: var(--cm-radius-md);
  --cm-dropdown-item-hover-bg: var(--cm-hover-bg);
  --cm-dropdown-item-font-size: var(--cm-text-sm);
}
```

## 16. C15: Progress Token'ları

```css
:root {
  --cm-progress-h: 4px;
  --cm-progress-radius: var(--cm-radius-full);
  --cm-progress-bg: var(--cm-gray-700);
  --cm-progress-fill: var(--cm-primary);
  --cm-progress-fill-gradient: var(--cm-primary-gradient);
  --cm-progress-transition: width var(--cm-duration-slow) var(--cm-ease-out);
}
```

## 17. C16: Toast Token'ları

```css
:root {
  --cm-toast-radius: var(--cm-radius-lg);
  --cm-toast-bg: var(--cm-bg-elevated);
  --cm-toast-border: 1px solid var(--cm-border-default);
  --cm-toast-shadow: var(--cm-shadow-xl);
  --cm-toast-padding: var(--cm-space-3) var(--cm-space-4);
  --cm-toast-max-w: 360px;
  --cm-toast-transition: var(--cm-slide-up);
}
```

---

## 18. Tier Override Matrisi

| Bileşen | Phone | Embedded | Laptop | Desktop | 4K | TV |
|---------|-------|----------|--------|---------|-----|-----|
| Card padding | 12px | 12px | 16px | 20px | 28px | 24px |
| Card radius | 10px | 12px | 12px | 12px | 15px | 18px |
| Button min-h | 44px | 44px | 36px | 36px | 36px | 56px |
| Input min-h | 44px | 44px | 40px | 40px | 40px | 56px |
| Touch target | 48px | 48px | 32px | 32px | 24px | 80px |
| Font scale | 0.875 | 1 | 1 | 1 | 1.25 | 1.5 |

---

## 19. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Component Count | 16 (C01-C16) |
| Token Categories | 6 (Size, Color, Spacing, Radius, Shadow, Transition) |
| Total Component Tokens | 160+ |
| Tier Override Rows | 6 |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
