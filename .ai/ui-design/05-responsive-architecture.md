---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Responsive Architecture (45-Tier Token-First)"
type: architecture
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/05-responsive-architecture.md"
  source_of_truth: ".ai/CLAUDE.md §18A · .ai/ui-design/tokens/platform-tokens.md"
---

# CoreMusic — Responsive Architecture (45-Tier Token-First)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[tokens/platform-tokens]] · [[tokens/design-tokens-master]] · [[03-implementation-plan]]

---

## 1. Amaç

CoreMusic responsive tasarımının **mimari temel noktalarıdır**. Token-first yaklaşım, 45-tier cihaz matrisi ve CSS media query stratejisi burada tanımlanır.

---

## 2. Temel İlkeler

| İlke | Açıklama | Guardrail |
|------|----------|-----------|
| Tek Bileşen | Tek HTML dosyası, CSS ile cihaz farkları | #17 |
| Token-First | CSS custom properties ile tüm değerler | #17 |
| Media Query | `@media` ile breakpoint yönetimi | #17 |
| Device CSS | Sadece behavioral override | #17 |
| PHP'de Sunum Yok | PHP'de margin/padding/kodlanamaz | #18C |

---

## 3. Token Hiyerarşisi

```
:root (Default = 1024px embedded)
  ↓
@media (max-width: 480px)     → Phone override
@media (max-width: 767px)     → Phone HD override
@media (max-width: 1024px)    → Tablet/Embedded override
@media (min-width: 1366px)    → Laptop override
@media (min-width: 1920px)    → Desktop override
@media (min-width: 2560px)    → Desktop HD override
@media (min-width: 3840px)    → 4K override
@media (pointer: coarse)      → TV/Touch override
```

---

## 4. CSS Dosya Yapısı

```
assets.coremusic.net/Css/
├── 01_Abstracts/
│   ├── a-layout-tokens.css      ← Tüm token tanımları + media query
│   ├── a-fonts-token.css        ← Font tanımları
│   ├── a-scale-hybrid.css       ← Ölçek motoru
│   ├── _glass.css               ← Cam efektleri
│   └── _animations.css          ← Keyframe animasyonlar
├── 02_Base/
│   ├── _reset.css               ← CSS reset
│   └── _base.css                ← Body, typography base
├── 03_Layout/
│   ├── _header.css              ← Header + nav
│   └── _footer.css              ← Footer player
├── 04_Components/
│   ├── _card.css                ← C03 Card
│   ├── _buttons.css             ← C04 Button
│   ├── _forms.css               ← C05 Input
│   ├── _modal.css               ← C07 Modal
│   ├── _toast.css               ← C16 Toast
│   └── _scrollbar.css           ← Scrollbar
├── 05_Pages/
│   ├── _home-layout.css         ← Home grid layout
│   └── _home-components.css     ← Home widget'lar
├── 06_Utilities/
│   └── _helpers.css             ← Utility classes
├── 08_Devices/
│   ├── d-embedded.css           ← RPi5 behavioral
│   ├── d-desktop.css            ← Desktop behavioral
│   ├── d-phone.css              ← Phone behavioral
│   ├── d-tablet.css             ← Tablet behavioral
│   ├── d-4k-tv.css              ← 4K/TV behavioral
│   ├── d-car.css                ← Car behavioral
│   └── d-watch.css              ← Watch behavioral
└── 09_ViewModes/
    ├── v-home.css               ← Home view mode
    ├── v-pro.css                ← Pro view mode
    ├── v-studio.css             ← Studio view mode
    └── v-car.css                ← Car view mode
```

---

## 5. Token-First CSS Örneği

```css
/* ❌ YANLIŞ: Hardcoded değer */
.header { height: 70px; padding: 0 24px; }

/* ✅ DOĞRU: Token kullanımı */
.header {
  height: var(--cm-header-h);
  padding: 0 var(--cm-content-padding);
}
```

---

## 6. Media Query Stratejisi

```css
/* Default: 1024×600 embedded */
:root {
  --cm-header-h: 60px;
  --cm-footer-h: 90px;
  --cm-sidebar-w: 0;
  --cm-touch-target: 48px;
  --cm-font-scale: 1;
}

/* Phone */
@media (max-width: 767px) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 80px;
    --cm-touch-target: 48px;
    --cm-font-scale: 0.875;
  }
}

/* Desktop */
@media (min-width: 1920px) {
  :root {
    --cm-header-h: 70px;
    --cm-footer-h: 104px;
    --cm-sidebar-w: 240px;
    --cm-touch-target: 32px;
    --cm-font-scale: 1;
  }
}

/* 4K */
@media (min-width: 3840px) {
  :root {
    --cm-header-h: 80px;
    --cm-footer-h: 120px;
    --cm-sidebar-w: 300px;
    --cm-touch-target: 24px;
    --cm-font-scale: 1.25;
  }
}
```

---

## 7. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `home-1024.html, home-desktop.html` | Tek HTML + responsive CSS |
| `if (screenWidth === 1024)` | CSS media query + var() |
| `device-loader.js` ile CSS swap | CSS media query ile token override |
| Hardcoded `height: 90px` | `height: var(--cm-footer-h)` |
| Hardcoded `width: 280px` | `width: var(--cm-sidebar-w)` |
| PHP'de `margin: 16px` | CSS'de `margin: var(--cm-space-4)` |

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 5.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Architecture Principle | Token-First + Figma Pixel-Perfect |
| CSS Files | 25+ |
| Device CSS | 7 |
| View Mode CSS | 4 |
| Media Query Breakpoints | 10 |
| New Components | Widget Area, Quick Apps, Mini Card |
| Figma Sources | 1024×600 + 1920×1080 |
| Cross References | 4 |
| Last Updated | 2026-09-22 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-22
**Mode:** Red Team · Human Mode · Truth Mode
