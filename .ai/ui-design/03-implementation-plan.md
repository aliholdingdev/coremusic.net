---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — CSS Implementation Plan (15-Step)"
type: plan
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/03-implementation-plan.md"
  source_of_truth: ".ai/ui-design/tokens/design-tokens-master.md · .ai/ui-design/02-component-inventory.md"
---

# CoreMusic — CSS Implementation Plan (15-Step)

**Zorunlu Bağlantılar:** [[02-component-inventory]] · [[tokens/design-tokens-master]] · [[05-responsive-architecture]]

---

## 1. Amaç

CoreMusic UI CSS implementasyonunun **15 adımlık yol haritasıdır**. Her adımda hangi dosyanın ne zaman oluşturulacağı ve bağımlılıkları tanımlanır.

---

## 2. 15 Adımlık CSS Planı

### Adım 1: Token Tanımları (T=0)

**Dosya:** `01_Abstracts/a-layout-tokens.css`
**İçerik:** Tüm CSS custom properties (300+ token)
**Bağımlılık:** Yok (ilk dosya)
**Süre:** 2 saat

### Adım 2: Reset & Base (T=0)

**Dosya:** `02_Base/_reset.css`, `02_Base/_base.css`
**İçerik:** CSS reset, body, typography base
**Bağımlılık:** Adım 1
**Süre:** 1 saat

### Adım 3: Layout Tokens Media Query (T=1)

**Dosya:** `01_Abstracts/a-layout-tokens.css` (extend)
**İçerik:** 45-tier media query breakpoint'leri
**Bağımlılık:** Adım 1
**Süre:** 3 saat

### Adım 4: Header (T=1)

**Dosya:** `03_Layout/_header.css`
**İçerik:** Site header, nav links, mobile bottom nav
**Bağımlılık:** Adım 1, Adım 2
**Süre:** 2 saat

### Adım 5: Footer (T=1)

**Dosya:** `03_Layout/_footer.css`
**İçerik:** Player footer, seek bar, volume, controls
**Bağımlılık:** Adım 1, Adım 2
**Süre:** 2 saat

### Adım 6: Home Layout (T=2)

**Dosya:** `05_Pages/_home-layout.css`
**İçerik:** Grid template, split layout, responsive columns
**Bağımlılık:** Adım 3
**Süre:** 3 saat

### Adım 7: Home Components (T=2)

**Dosya:** `05_Pages/_home-components.css`
**İçerik:** Widget grid (kullanıcı grid kuralı — `reference/figma/grid-rules.md`): 1024 → row1 2×2, row2 1×5, row3 1×5; 1920 → row1 4×4, row2 1×8, row3 1×8 + recent cards, playlist, up-next
**Bağımlılık:** Adım 6
**Süre:** 3 saat

### Adım 8: Card Component (T=3)

**Dosya:** `04_Components/_card.css`
**İçerik:** Card base, variants, hover states
**Bağımlılık:** Adım 1
**Süre:** 2 saat

### Adım 9: Button & Input (T=3)

**Dosya:** `04_Components/_buttons.css`, `04_Components/_forms.css`
**İçerik:** Button variants, input fields, validation states
**Bağımlılık:** Adım 1
**Süre:** 2 saat

### Adım 10: Modal & Toast (T=4)

**Dosya:** `04_Components/_modal.css`, `04_Components/_toast.css`
**İçerik:** Modal overlay, animations, toast notifications
**Bağımlılık:** Adım 1
**Süre:** 2 saat

### Adım 11: Glass Effects (T=4)

**Dosya:** `01_Abstracts/_glass.css`
**İçerik:** Glassmorphism utilities, blur, gradient
**Bağımlılık:** Adım 1
**Süre:** 1 saat

### Adım 12: Device CSS Overrides (T=5)

**Dosya:** `08_Devices/d-embedded.css`, `d-desktop.css`, `d-phone.css`, `d-tablet.css`, `d-4k-tv.css`, `d-car.css`, `d-watch.css`
**İçerik:** Behavioral overrides per device
**Bağımlılık:** Adım 3
**Süre:** 4 saat

### Adım 13: View Mode CSS (T=5)

**Dosya:** `09_ViewModes/v-home.css`, `v-pro.css`, `v-studio.css`, `v-car.css`
**İçerik:** View mode specific styles
**Bağımlılık:** Adım 6
**Süre:** 2 saat

### Adım 14: Utilities (T=6)

**Dosya:** `06_Utilities/_helpers.css`
**İçerik:** Display, spacing, text, visibility helpers
**Bağımlılık:** Adım 1
**Süre:** 1 saat

### Adım 15: Animation Keyframes (T=6)

**Dosya:** `01_Abstracts/_animations.css`
**İçerik:** All keyframe animations, transitions
**Bağımlılık:** Adım 1
**Süre:** 1 saat

---

## 3. Bağımlılık Grafisi

```
Adım 1 (Tokens) ──┬──> Adım 2 (Base) ──> Adım 4 (Header)
                   ├──> Adım 3 (Media) ──> Adım 6 (Home Layout) ──> Adım 7
                   ├──> Adım 8 (Card)
                   ├──> Adım 9 (Button/Input)
                   ├──> Adım 10 (Modal/Toast)
                   ├──> Adım 11 (Glass)
                   ├──> Adım 12 (Device CSS)
                   ├──> Adım 14 (Utilities)
                   └──> Adım 15 (Animations)
```

---

## 4. Toplam Süre Tahmini

| Adım | Süre |
|------|------|
| Adım 1-3 | 6 saat |
| Adım 4-5 | 4 saat |
| Adım 6-7 | 6 saat |
| Adım 8-10 | 6 saat |
| Adım 11-13 | 7 saat |
| Adım 14-15 | 2 saat |
| **Toplam** | **31 saat** |

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total Steps | 15 |
| Total Estimated Hours | 31 |
| CSS Files | 25+ |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
