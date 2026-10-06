---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Component Inventory (C01-C16)"
type: inventory
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 4.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/02-component-inventory.md"
  source_of_truth: ".ai/ui-design/tokens/component-tokens.md · .ai/ui-design/01-mockup-index.md"
---

# CoreMusic — Component Inventory (C01-C16)

**Zorunlu Bağlantılar:** [[01-mockup-index]] · [[tokens/component-tokens]] · [[tokens/design-tokens-master]] · [[reference/09-interaction-states]] · [[05-responsive-architecture]]

---

## 1. Amaç

CoreMusic UI bileşenlerinin **tam envanteridir**. Her bileşen için BEM sınıf adı, piksel ölçümleri, token referansları ve durumları burada tanımlanır.

---

## 2. Bileşen Envanteri

### C01: NavLink

| Özellik | Değer |
|---------|-------|
| BEM | `.nav-link`, `.nav-link--active`, `.nav-link--hover` |
| Piksel | padding: 12px 16px, gap: 8px, icon: 20px, font: 14px/500 |
| Token | `--cm-navlink-*` |
| Durumlar | default, hover, active, disabled |
| Tier | Phone: bottom tab, Laptop+: sidebar |

### C02: Hero Banner

| Özellik | Değer |
|---------|-------|
| BEM | `.hero`, `.hero__title`, `.hero__subtitle`, `.hero__overlay` |
| Piksel | padding: 32px, min-height: 280px, title: 36px/700, subtitle: 18px/400 |
| Token | `--cm-hero-*` |
| Durumlar | default, with-image, with-gradient |
| Tier | Embedded: 200px min-h, Desktop: 280px |

### C03: Card

| Özellik | Değer |
|---------|-------|
| BEM | `.card`, `.card__image`, `.card__title`, `.card__meta`, `.card--compact`, `.card--wide` |
| Piksel | padding: 16px, radius: 16px, gap: 16px, image: 1:1 aspect |
| Token | `--cm-card-*` |
| Durumlar | default, hover, active, loading, skeleton |
| Tier | Phone: 12px radius, TV: 24px radius |

### C04: Button

| Özellik | Değer |
|---------|-------|
| BEM | `.btn`, `.btn--primary`, `.btn--secondary`, `.btn--ghost`, `.btn--danger`, `.btn--sm`, `.btn--lg` |
| Piksel | padding: 8px 16px, min-h: 36px, radius: 12px, font: 14px/600 |
| Token | `--cm-btn-*` |
| Durumlar | default, hover, active, disabled, loading |
| Tier | Phone: 44px min-h, TV: 56px min-h |

### C05: Input

| Özellik | Değer |
|---------|-------|
| BEM | `.input`, `.input--error`, `.input--success`, `.input__label`, `.input__hint` |
| Piksel | padding: 10px 12px, min-h: 40px, radius: 8px, font: 14px/400 |
| Token | `--cm-input-*` |
| Durumlar | default, focus, error, success, disabled |
| Tier | Phone: 44px min-h, TV: 56px min-h |

### C06: Tab

| Özellik | Değer |
|---------|-------|
| BEM | `.tab`, `.tab--active`, `.tab__indicator`, `.tab-list` |
| Piksel | padding: 8px 16px, gap: 4px, radius: 8px, font: 13px/500 |
| Token | `--cm-tab-*` |
| Durumlar | default, hover, active, disabled |
| Tier | All tiers same structure |

### C07: Modal

| Özellik | Değer |
|---------|-------|
| BEM | `.modal`, `.modal__overlay`, `.modal__content`, `.modal__header`, `.modal__body`, `.modal__footer` |
| Piksel | padding: 24px, radius: 20px, max-w: 480px, overlay: 60% black |
| Token | `--cm-modal-*` |
| Durumlar | closed, opening, open, closing |
| Tier | Phone: full-width, Desktop: centered |

### C08: Toggle

| Özellik | Değer |
|---------|-------|
| BEM | `.toggle`, `.toggle--active`, `.toggle__knob` |
| Piksel | w: 44px, h: 24px, knob: 18px, radius: 9999px |
| Token | `--cm-toggle-*` |
| Durumlar | off, on, disabled |
| Tier | All tiers same structure |

### C09: Slider

| Özellik | Değer |
|---------|-------|
| BEM | `.slider`, `.slider__track`, `.slider__fill`, `.slider__thumb` |
| Piksel | track-h: 4px, thumb: 14px, radius: 9999px |
| Token | `--cm-slider-*` |
| Durumlar | default, dragging, disabled |
| Tier | Phone: 14px thumb, TV: 20px thumb |

### C10: Badge

| Özellik | Değer |
|---------|-------|
| BEM | `.badge`, `.badge--primary`, `.badge--success`, `.badge--error` |
| Piksel | padding: 2px 8px, radius: 9999px, font: 12px/600 |
| Token | `--cm-badge-*` |
| Durumlar | default, with-count |
| Tier | All tiers same structure |

### C11: Avatar

| Özellik | Değer |
|---------|-------|
| BEM | `.avatar`, `.avatar--sm`, `.avatar--md`, `.avatar--lg`, `.avatar--xl` |
| Piksel | sm: 24px, md: 32px, lg: 40px, xl: 56px, radius: 9999px |
| Token | `--cm-avatar-*` |
| Durumlar | default, with-image, with-initials, online, offline |
| Tier | All tiers same structure |

### C12: Tooltip

| Özellik | Değer |
|---------|-------|
| BEM | `.tooltip`, `.tooltip--top`, `.tooltip--bottom`, `.tooltip__arrow` |
| Piksel | padding: 8px 12px, radius: 8px, max-w: 200px, font: 12px/400 |
| Token | `--cm-tooltip-*` |
| Durumlar | hidden, visible |
| Tier | TV: larger text |

### C13: Skeleton

| Özellik | Değer |
|---------|-------|
| BEM | `.skeleton`, `.skeleton--text`, `.skeleton--circle`, `.skeleton--rect` |
| Piksel | radius: 8px, animation: 1.5s ease-in-out infinite |
| Token | `--cm-skeleton-*` |
| Durumlar | loading, loaded |
| Tier | All tiers same structure |

### C14: Dropdown

| Özellik | Değer |
|---------|-------|
| BEM | `.dropdown`, `.dropdown__menu`, `.dropdown__item`, `.dropdown__item--active` |
| Piksel | padding: 4px, item-padding: 8px 12px, radius: 12px, shadow: xl |
| Token | `--cm-dropdown-*` |
| Durumars | closed, open, item-hover, item-active |
| Tier | Phone: full-width, Desktop: positioned |

### C15: Progress

| Özellik | Değer |
|---------|-------|
| BEM | `.progress`, `.progress__bar`, `.progress__fill` |
| Piksel | h: 4px, radius: 9999px, animation: 300ms ease-out |
| Token | `--cm-progress-*` |
| Durumlar | determinate, indeterminate, error |
| Tier | All tiers same structure |

### C16: Toast

| Özellik | Değer |
|---------|-------|
| BEM | `.toast`, `.toast--success`, `.toast--error`, `.toast--info`, `.toast__icon`, `.toast__message` |
| Piksel | padding: 12px 16px, radius: 12px, max-w: 360px |
| Token | `--cm-toast-*` |
| Durumlar | showing, hiding, success, error, info |
| Tier | Phone: bottom-sheet, Desktop: top-right |

### C17: Widget Area

| Özellik | Değer |
|---------|-------|
| BEM | `.home-widget-area`, `.home-widget-area__row`, `.home-widget-panel`, `.home-widget-panel__icon`, `.home-widget-panel__text`, `.home-widget-panel__title`, `.home-widget-panel__subtitle` |
| Piksel | Embedded: 109×40px panel, 20px icon, 9px text; Desktop: 220×80px panel |
| Token | `--cm-widget-area-*`, `--cm-widget-panel-*` |
| Durumlar | default, with-icon, with-text |
| Tier | **Grid kuralı (kullanıcı direktifi — kanonik kayıt `05-responsive-architecture` §13):** Embedded 1024 → row1 **2×2**, row2 **1×5**, row3 **1×5** (12 slot); Desktop 1920 → row1 **4×4**, row2 **1×8**, row3 **1×8** (20 slot). API `layoutGrids` bu kuralı **çürütmüyor, tamamlıyor** (§3 kanıtı); C18 quick-apps row2/row3 slotlarını doldurur |

### C18: Quick Apps Row

| Özellik | Değer |
|---------|-------|
| BEM | `.home-quick-apps`, `.home-quick-app`, `.home-quick-app__icon`, `.home-quick-app__label` |
| Piksel | Embedded: 129×40px button, 45px icon, 8.5px text; Desktop: 200×60px button |
| Token | `--cm-quick-app-*` |
| Durumlar | default, hover, active |
| Tier | **Grid kuralı ile hizalı** (kanonik: `05-responsive-architecture` §13): Embedded 1024 → row2 ve row3'te **1×5** (5+5 slot); Desktop 1920 → row2 ve row3'te **1×8** (8+8 slot) — row1 slotu C17 widget grid'inin 2×2 / 4×4'üdür |

### C19: Mini Card

| Özellik | Değer |
|---------|-------|
| BEM | `.home-mini-card`, `.home-mini-card__art`, `.home-mini-card__info`, `.home-mini-card__title`, `.home-mini-card__subtitle`, `.home-mini-card__duration` |
| Piksel | Embedded: 169×43px, 40px art, 8.5px title; Desktop: 200×48px, 44px art |
| Token | `--cm-mini-card-*` |
| Durumlar | default, hover, with-art, with-duration |
| Tier | All tiers: horizontal card with art + info + duration |

---

## 3. Figma Bileşen Kanıtı (Design-System-XD)

> Kaynak: `.ai/ui-design/reference/figma/raw/page-18-2907.json` (sayfa `18:2907`, breakpoint `system`, 2026-09-29 tam çekim). Amaç: C01-C19 envanterinin Figma bileşen kütüphanesiyle **çakışmadığını** kanıtlamak — Figma set adları BEM sınıf adlarımızla aynı değildir; sayılar grep ile diskten alındı.

| Ölçü | Değer | Yöntem |
|------|-------|--------|
| Sayfa toplam node | 21.767 | `_extraction-notes.md` tam çekim tablosu |
| `COMPONENT` node | 140 | grep `"name":"…","type":"COMPONENT"` |
| `COMPONENT_SET` node | 23 | grep `"name":"…","type":"COMPONENT_SET"` |
| `layoutGrids` tanımlı node | 21 | grep `"layoutGrids":` (grid kuralı kanıtı → `05-responsive-architecture` §13) |

**COMPONENT_SET adları (23/23 — dosyadaki değerler):** `İnput` · `Şarkılar Item Btn` · `Albumler btn` · `Albumler List Item` · `Battery` · `Button` · `Button Flat` · `conneted bağlı olan bt` · `conneted bağlı olan ağ` · `Disk Buttons` · `Footer` · `Navbar` · `Navigate Div` · `Player İnfо Div` · `Player İnfо Div Mini` · `Playlist Items` (×2) · `Playlist Items Mini` · `Playlist Items Mini history` · `Playlist Status Div` · `Romantic_Background_03 (2)` · `Settings Btn Items` · `Switch`

> **⚠️ VERIFICATION REQUIRED — Figma set ↔ C-bileşeni eşlemesi:** Figma adları ile C01-C19 arası birebir eşleme **yapılmadı** (uydurma eşleme Guardrail #3 ihlali olurdu). Eşleme, bileşen bazlı ekran spec'leri tamamlandığında üretilecek; bu dosyada yalnız kanıt (ad + adet) tutulur.

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 4.1.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Component Count | 19 (C01-C19) |
| BEM Classes | 80+ |
| State Variants | 60+ |
| Figma Component Sets | 23 (Design-System-XD `18:2907`, 21.767 node) |
| Grid Kuralı Sahibi | `05-responsive-architecture` §13 (1024 → 12 slot · 1920 → 20 slot) |
| Cross References | 5 |
| Last Updated | 2026-09-29 |
| Version Notu (Truth Mode) | Önceki sürümde frontmatter `4.0.1` ↔ Quality Report `5.0.0` uyuşmazlığı vardı; bu sürümde tek değerde birleştirildi (**4.1.0**), iki sayı da düzeltildi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
