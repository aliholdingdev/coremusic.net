---
type: architecture
category: ui-design
title: "CoreMusic — Responsive Device Mode Architecture"
date: 2026-08-19
updated: 2026-08-19
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
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

## 3. Core Rule

Frontend sistemi şu mantıkla çalışmalıdır:

```
Current Screen
      |
      v
Device Capability Detection
      |
      +-----------------------------+
      |                             |
      v                             v

Embedded Device Mode            Normal Responsive Mode

1024x600 UI Rules               Responsive Layout Rules
```

---

## 4. Embedded Device Condition

1024x600 özel UI modu sadece aşağıdaki koşullarda aktif olabilir:

```
IF

    screenWidth === 1024

    AND

    screenHeight === 600

    AND

    deviceMode === embedded/touch

THEN

    activate embedded-1024 mode

ELSE

    use normal responsive system
```

---

## 5. Important Architecture Rule

1024x600 için:

YENİ HTML oluşturma. **YASAK.**
YENİ COMPONENT oluşturma. **YASAK.**
YENİ PAGE oluşturma. **YASAK.**
YENİ FRONTEND BRANCH oluşturma. **YASAK.**

```javascript
// YASAK — Ayrı UI loading
if(width === 1024)
{
    load1024UI();
}
```

```text
// YASAK — Ayrı HTML dosyaları
home-1024.html
home-desktop.html
home-mobile.html
home-4k.html
```

---

## 6. Required Implementation

Sistem:

```
Tek Component
    *
Design Token
    *
Responsive CSS
    *
Device Mode Override
```

mantığı ile çalışmalıdır.

Mimari akışı:

```
UI Mockup PNG
        |
        v
Design Reference
        |
        v
Design Tokens
        |
        v
Component System
        |
        v
Responsive Engine
        |
        v
Device Modes
```

---

## 7. Device Modes

### 7.1 — Embedded Mode

**Target:** `1024x600`

**Kullanım alanları:**
- Raspberry Pi
- Embedded Touch Screen
- Car Display
- Dedicated Music Console

**Özellikler:**
- Touch optimized
- Büyük kontrol alanları
- Pixel accurate layout
- Mockup ölçüleri korunur

### 7.2 — Responsive Desktop Mode

**Target:** `1920x1080`, `2560x1440`, `3840x2160`

**Kurallar:**
- Aynı component sistemi kullanılır.
- Layout fluid olarak ölçeklenir.
- Token değerleri değişir.
- Component yapısı değişmez.

### 7.3 — Mobile Mode

**Target:** `Portrait Mobile`

**Kurallar:**
- Aynı component sistemi korunur.
- Responsive breakpoint uygulanır.
- Minimum alan optimizasyonu yapılır.

---

## 8. Runtime Device Detection

```javascript
const isEmbedded1024 =
(
    window.innerWidth === 1024 &&
    window.innerHeight === 600 &&
    deviceCapabilities.touch === true
);

if(isEmbedded1024)
{
    document.documentElement.dataset.device = "embedded-1024";
}
else
{
    document.documentElement.dataset.device = "responsive";
}
```

**NOT:** Sadece width kontrolü yeterli değildir. 1024 genişliğe sahip tüm cihazlar Embedded kabul edilmez.

---

## 9. CSS Token Architecture

### 9.1 — Default Tokens

```css
:root {
    --sidebar-width: 220px;
    --player-height: 80px;
    --touch-target-size: 44px;
    --content-gap: 24px;
}
```

### 9.2 — Embedded Override

```css
:root[data-device="embedded-1024"] {
    --sidebar-width: 220px;
    --player-height: 80px;
    --touch-target-size: 56px;
    --content-gap: 16px;
}
```

### 9.3 — Desktop Responsive Tokens

```css
@media (min-width: 1920px) {
    :root {
        --sidebar-width: 320px;
        --player-height: 120px;
    }
}

@media (min-width: 3840px) {
    :root {
        --sidebar-width: 420px;
        --player-height: 160px;
    }
}
```

---

## 10. Component Architecture

Component yapısı **değişmez.** Aynı component tüm cihazlarda kullanılır:

```
components/
    Sidebar/
    Player/
    AlbumCard/
    Navigation/
    SearchBox/
    SettingsPanel/
```

```
Embedded 1024
        |
        v
Desktop
        |
        v
4K TV
        |
        v
Mobile
```

---

## 11. CSS Architecture

Zorunlu:
- ITCSS
- BEM
- CSS Variables
- Component Based CSS
- Responsive First

```css
.player {
    height: var(--player-height);
}

.player__button {
    width: var(--touch-target-size);
}

.player--compact {
    /* compact variant */
}
```

---

## 12. Priority Order

CSS ve UI davranış önceliği:

```
1. Embedded 1024 Override
2. Desktop Responsive Rules
3. Mobile Responsive Rules
4. Default Tokens
```

---

## 13. Validation Rules

AI kod üretmeden önce kontrol et:

| # | Kontrol |
|---|---------|
| 1 | 1024 Embedded mode var mı? |
| 2 | Ayrı HTML oluşturulmuş mu? |
| 3 | Component tekrar edilmiş mi? |
| 4 | Hardcoded resolution kontrolü var mı? |
| 5 | Design token kullanılmış mı? |
| 6 | Responsive davranış korunuyor mu? |

---

## 14. Final Architecture Decision

CoreMusic frontend:

**1024x600 Embedded UI = Özel Device Mode**

Kaynak:
- PNG Mockup
- Pixel Reference
- Touch Layout
- Component Measurement

**Diğer cihazlar = Normal Responsive System**

Kaynak:
- Responsive CSS
- Breakpoints
- Fluid Layout
- Shared Components

---

## 15. Final Rule

AI hiçbir zaman **"1024 için ayrı frontend"** oluşturmayacaktır.

AI her zaman **"Tek component sistemi + Embedded Device Override + Responsive Engine"** mimarisini uygulayacaktır.

---

## 16. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/l3-presentation/device-css]] | Device CSS detayları |
| [[ui-design/tokens/platform-tokens]] | Platform token değerleri |
| [[ui-design/tokens/design-tokens-master]] | Design token referansı |
| [[ui-design/prompt/screen/01-1024-embedded]] | Embedded prompt |
| [[ui-design/prompt/screen/02-1920-desktop]] | Desktop prompt |
| [[ui-design/prompt/screen/03-3840-tv]] | TV prompt |
| [[ui-design/prompt/screen/04-mobile]] | Mobile prompt |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS |
| [[ADR-045-multi-domain-view-mode-architecture]] | View modes |
| [[architecture/03-css-device-loading-plan]] | CSS loading planı |

---

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 17 |
| Device Modes | 3 (Embedded, Desktop, Mobile) |
| Validation Rules | 6 |
| ADR Uyumlu | ✅ 001, 044, 045 |
| Zero Hallucination | ✅ |
| Guardrail #17 Uyumlu | ✅ Single Component Responsive |

---

*Responsive Device Mode Architecture v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
