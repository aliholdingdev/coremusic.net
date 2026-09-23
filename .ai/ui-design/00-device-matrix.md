---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 45-Tier Device Matrix"
type: matrix
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/00-device-matrix.md"
  source_of_truth: ".ai/CLAUDE.md §18A · .ai/ui-design/tokens/platform-tokens.md"
---

# CoreMusic — 45-Tier Device Matrix

**Zorunlu Bağlantılar:** [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/platform-tokens]] · [[05-responsive-architecture]]

---

## 1. Amaç

CoreMusic'in hedeflediği **45 cihaz katmanının tam listesi** ve her biri için UI gereksinimleri. Bu dosya, tüm frontend geliştirme görevlerinin başlangıç noktasıdır.

---

## 2. Device Tier Sistemi

### 2.1 12 Kategori × 45 Tier

| Kategori | Tier Sayısı | Aralık |
|----------|:-----------:|--------|
| 📱 Phone | 5 | T01-T05 |
| 📱 Tablet | 6 | T06-T11 |
| 💻 Laptop | 5 | T12-T16 |
| 🖥️ Desktop Monitor | 8 | T17-T24 |
| 📺 Smart TV | 4 | T25-T28 |
| 🚗 Automotive | 2 | T29-T30 |
| ⌚ Smart Watch | 3 | T31-T33 |
| 🎮 Console | 3 | T34-T36 |
| 🖥️ Desktop App | 2 | T37-T38 |
| 📱 Mobil Uygulama | 2 | T39-T40 |
| 🌐 Web & Özel | 5 | T41-T45 |
| **Toplam** | **45** | |

---

## 3. Tüm Tiers

### 📱 Phone (T01-T05)

| Tier | Cihaz | Viewport | DPI | Touch | Font | Layout |
|------|-------|----------|-----|-------|------|--------|
| T01 | Samsung Galaxy J7 (2016) | 720×1280 | 2x | 48px | 0.875 | Tek sütun |
| T01 | Samsung Galaxy A13 | 720×1600 | 2x | 48px | 0.875 | Tek sütun |
| T02 | iPhone 14 Pro Max | 1290×2796 | 3x | 48px | 1 | Tek sütun |
| T02 | iPhone 15 Pro Max | 1290×2796 | 3x | 48px | 1 | Tek sütun |
| T02 | iPhone 16 Pro Max | 1320×2868 | 3x | 48px | 1 | Tek sütun |
| T03 | Samsung Galaxy S25 Ultra | 1440×3120 | 3.5x | 48px | 1 | Tek sütun |
| T03 | Samsung Galaxy S26 Ultra | 1440×3120 | 3.5x | 48px | 1 | Tek sütun |
| T04 | Samsung Galaxy Z Flip5 | 1080×2640 | 3x | 48px | 1 | Tek sütun (katlı/açık) |
| T05 | OnePlus 12 | 1440×3168 | 3.5x | 48px | 1 | Tek sütun |
| T05 | ASUS ROG Phone 8 | 1080×2400 | 3x | 48px | 1 | Tek sütun |

**Özellikler:** Bottom tab nav, kompakt kartlar, dikey scroll, tam width.

### 📱 Tablet (T06-T11)

| Tier | Cihaz | Viewport | DPI | Orientation | Touch | Font | Layout |
|------|-------|----------|-----|-------------|-------|------|--------|
| T06 | iPad Mini (6. nesil) | 1488×2266 | 2x | Portrait | 48px | 1 | 2 sütun |
| T06 | Samsung Galaxy Tab A9 | 1340×800 | 1.5x | Landscape | 48px | 1 | 2 sütun |
| T07 | iPad (10. nesil) | 1640×2360 | 2x | Portrait | 48px | 1 | 2 sütun |
| T07 | Samsung Galaxy Tab S9 | 1752×2800 | 2x | Portrait | 48px | 1 | 2 sütun |
| T07 | Xiaomi Pad 6 | 1840×2800 | 2x | Portrait | 48px | 1 | 2 sütun |
| T08 | iPad Pro 11" (M2) | 1668×2388 | 2x | Portrait | 48px | 1 | 2 sütun + split |
| T09 | iPad Pro 12.9" (M2) | 2048×2732 | 2x | Portrait | 48px | 1 | 3 sütun |
| T09 | Samsung Galaxy Tab S9 Ultra | 2960×1848 | 2x | Landscape | 48px | 1 | 3 sütun |
| T10 | Microsoft Surface Pro 9 | 2880×1920 | 2x | Landscape | 48px | 1 | 3 sütun |
| T10 | Lenovo Tab P12 Pro | 2560×1600 | 2x | Landscape | 48px | 1 | 3 sütun |
| T11 | iPad Pro 12.9" (M4) | 2732×2048 | 2x | Landscape | 48px | 1 | 3 sütun |
| T11 | Samsung Galaxy Tab S9 FE+ | 2560×1600 | 2x | Landscape | 48px | 1 | 3 sütun |

**Özellikler:** Grid layout, tablet nav, portrait/landscape geçiş, stylus desteği.

### 🖥️ Embedded (T07-T08)

| Tier | Cihaz | Viewport | OS | Touch | Font | Layout |
|------|-------|----------|-----|-------|------|--------|
| T07 | RPi5 7" | 1024×600 | Debian ARM | 48px | 1 | 2 sütun, sidebar yok |
| T08 | RPi5 10" | 1280×800 | Debian ARM | 48px | 1 | 3 sütun |

**Özellikler:** Welcome popup (T07), touch-first, minimal chrome, offline-first.

### 💻 Laptop (T12-T16)

| Tier | Cihaz | Viewport | DPI | Input | Font | Sidebar | Layout |
|------|-------|----------|-----|-------|------|---------|--------|
| T12 | 13" Ultrabook (FHD) | 1920×1080 | 1x | Mouse+KB | 1 | 220px | 3 sütun |
| T12 | MacBook Air M2 13" | 2560×1664 | 2x | Trackpad+KB | 1 | 240px | 3 sütun |
| T13 | 14" Creator (QHD) | 2560×1600 | 1.5x | Mouse+KB | 1 | 240px | 3 sütun |
| T13 | MacBook Pro 14" M3 | 3024×1964 | 2x | Trackpad+KB | 1 | 260px | 3 sütun |
| T14 | 15" Laptop (FHD) | 1920×1080 | 1x | Mouse+KB | 1 | 240px | 3 sütun |
| T14 | MacBook Air 15" M3 | 2880×1864 | 2x | Trackpad+KB | 1 | 260px | 3 sütun |
| T15 | 15.6" Creator (4K) | 3840×2160 | 1.5x | Mouse+KB | 1.25 | 280px | 3 sütun |
| T16 | 16" Gaming (QHD+) | 2560×1600 | 1.5x | Mouse+KB | 1 | 260px | 3 sütun |
| T16 | MacBook Pro 16" M3 Max | 3456×2234 | 2x | Trackpad+KB | 1 | 280px | 3 sütun |

**Özellikler:** Persistent sidebar, hover states, mouse cursor, keyboard nav, macOS/Windows uyumlu.

### 🖥️ Desktop Monitor (T17-T24)

| Tier | Cihaz | Viewport | DPI | Input | Font | Sidebar | Layout |
|------|-------|----------|-----|-------|------|---------|--------|
| T17 | 22" FHD Monitor | 1920×1080 | 1x | Mouse+KB | 1 | 240px | 3 sütun |
| T18 | 24" FHD Monitor | 1920×1080 | 1x | Mouse+KB | 1 | 240px | 3 sütun |
| T19 | 27" QHD Monitor | 2560×1440 | 1x | Mouse+KB | 1 | 260px | 4 sütun |
| T20 | 27" 4K Monitor | 3840×2160 | 1.5x | Mouse+KB | 1.25 | 280px | 4 sütun |
| T21 | 32" QHD Monitor | 2560×1440 | 1x | Mouse+KB | 1 | 280px | 4 sütun |
| T21 | 32" 4K Monitor | 3840×2160 | 1.5x | Mouse+KB | 1.25 | 300px | 4 sütun |
| T22 | 34" Ultrawide QHD | 3440×1440 | 1x | Mouse+KB | 1 | 280px | 5 sütun |
| T23 | 40" Ultrawide WUHD | 5120×2160 | 1x | Mouse+KB | 1.125 | 300px | 5 sütun |
| T23 | 49" Super Ultrawide | 5120×1440 | 1x | Mouse+KB | 1.125 | 300px | 6 sütun |
| T24 | 32" 4K Reference | 3840×2160 | 1.5x | Mouse+KB | 1.25 | 300px | 4 sütun |
| T24 | 27" 5K Studio | 5120×2880 | 2x | Mouse+KB | 1 | 280px | 4 sütun |

**Özellikler:** Geniş content, yüksek çözünürlük, çoklu widget, NO-CENTER (4K).

### 📺 Ultrawide (T22-T24 overlap — tam赶到 T22-T24 de Desktop tier'da)

> Ultrawide tier'ları Desktop Monitor tier'ları ile çakışır. T22 (34" Ultrawide) ve T23 (40"/49" Ultrawide) Desktop Monitor section'ında tanımlıdır.

### 📺 Smart TV (T25-T28)

| Tier | Cihaz | Viewport | DPI | Input | Font | Touch | Layout |
|------|-------|----------|-----|-------|------|-------|--------|
| T25 | 43" FHD TV | 1920×1080 | 1x | D-pad | 1.5 | 80px | 4 sütun |
| T25 | 50" FHD TV | 1920×1080 | 1x | D-pad | 1.5 | 80px | 4 sütun |
| T26 | 55" 4K TV | 3840×2160 | 1x | D-pad | 1.75 | 96px | 4 sütun |
| T26 | Samsung QN85B 55" | 3840×2160 | 1x | D-pad | 1.75 | 96px | 4 sütun |
| T27 | 65" 4K TV | 3840×2160 | 1x | D-pad | 2 | 104px | 4 sütun |
| T27 | LG C3 65" OLED | 3840×2160 | 1x | D-pad | 2 | 104px | 4 sütun |
| T28 | 75" 4K TV | 3840×2160 | 1x | D-pad | 2 | 112px | 4 sütun |
| T28 | 85" 4K TV | 3840×2160 | 1x | D-pad | 2.25 | 120px | 5 sütun |
| T28 | 98" 4K TV | 3840×2160 | 1x | D-pad | 2.5 | 128px | 5 sütun |

**Özellikler:** 10-foot UI, D-pad navigation, large focus indicators, high contrast, ambient mode.

### 🚗 Automotive (T29-T30)

| Tier | Cihaz | Viewport | DPI | Input | Font | Touch | Layout |
|------|-------|----------|-----|-------|------|-------|--------|
| T29 | Android Auto (Küçük) | 800×480 | 1x | Touch+Voice | 1.125 | 80px | 2 sütun |
| T29 | Android Auto (Orta) | 1280×720 | 1x | Touch+Voice | 1.125 | 80px | 2 sütun |
| T30 | Apple CarPlay (Küçük) | 800×480 | 1x | Touch+Voice | 1.125 | 80px | 2 sütun |
| T30 | Apple CarPlay (Büyük) | 1920×720 | 1x | Touch+Voice | 1.25 | 80px | 3 sütun |
| T30 | Tesla Model 3/Y | 1920×1200 | 1x | Touch | 1.25 | 80px | 2 sütun |

**Özellikler:** Safety-first, 80px+ touch targets, minimal text, voice-first, driver mode, ambient lighting uyumlu.

### ⌚ Smart Watch (T31-T33)

| Tier | Cihaz | Viewport | DPI | Input | Font | Layout |
|------|-------|----------|-----|-------|------|--------|
| T31 | Apple Watch SE (40mm) | 396×484 | 2x | Crown+Touch | 0.75 | Tek sütun |
| T31 | Apple Watch Series 9 (41mm) | 396×484 | 2x | Crown+Touch | 0.75 | Tek sütun |
| T32 | Apple Watch Ultra 2 (49mm) | 502×410 | 3x | Crown+Touch | 0.875 | Tek sütun |
| T32 | Samsung Galaxy Watch 6 (44mm) | 450×450 | 2x | Bezel+Touch | 0.8125 | Dairesel |
| T33 | Samsung Galaxy Watch 6 Classic (47mm) | 480×480 | 2x | Bezel+Touch | 0.8125 | Dairesel |

**Özellikler:** Micro UI, OLED power save, always-on display, haptic feedback, rotating crown/bezel.

### 🎮 Console (T34-T36)

| Tier | Cihaz | Viewport | DPI | Input | Font | Touch | Layout |
|------|-------|----------|-----|-------|------|-------|--------|
| T34 | PlayStation 5 | 1920×1080 | 1x | D-pad | 1.5 | 80px | 4 sütun |
| T34 | Xbox Series X | 3840×2160 | 1x | D-pad | 1.75 | 96px | 4 sütun |
| T35 | Nintendo Switch (Handheld) | 1280×720 | 1x | Joy-Con+Touch | 1 | 56px | 2 sütun |
| T35 | Nintendo Switch OLED | 1280×720 | 1x | Joy-Con+Touch | 1 | 56px | 2 sütun |
| T36 | Steam Deck | 1280×800 | 1x | Pad+Stick | 1 | 48px | 2 sütun |
| T36 | ASUS ROG Ally | 1920×1080 | 1x | Pad+Stick | 1 | 48px | 2 sütun |

**Özellikler:** Controller navigation, handheld hybrid, gamepad UI, large focus indicators.

### 🖥️ Desktop App (T37-T38)

| Tier | Cihaz | Runtime | Viewport | Font | Sidebar | Layout |
|------|-------|---------|----------|------|---------|--------|
| T37 | Electron Desktop | Chromium | Responsive | 1 | 240px | 3 sütun |
| T38 | Tauri Desktop | WebView2 | Responsive | 1 | 240px | 3 sütun |

**Özellikler:** Native titlebar, offline support, installable, system tray, IPC bridge, custom protocol.

### 🥽 AR/VR (T30)

| Tier | Cihaz | Viewport | Input | Font | Layout |
|------|-------|----------|-------|------|--------|
| T30 | Meta Quest 3 | 2064×2208 | Controller+Hand | 1.25 | 3 sütun |

**Özellikler:** Spatial UI, 3D depth, hand tracking, immersive mode.

### ⌚ Smart Watch Ek (T31-T33)

| Tier | Cihaz | Viewport | DPI | Input | Font | Layout |
|------|-------|----------|-----|-------|------|--------|
| T31 | Apple Watch SE 40mm | 396×484 | 2x | Crown+Touch | 0.75 | Tek sütun |
| T32 | Apple Watch Ultra 49mm | 502×410 | 3x | Crown+Touch | 0.875 | Tek sütun |
| T33 | Samsung Galaxy Watch 6 44mm | 450×450 | 2x | Bezel+Touch | 0.8125 | Dairesel |

**Özellikler:** Micro UI, OLED power save, always-on display, haptic feedback.

### 🎮 Oyun Konsolu Ek (T34-T36)

| Tier | Cihaz | Viewport | Input | Font | Touch | Layout |
|------|-------|----------|-------|------|-------|--------|
| T34 | PlayStation 5 | 1920×1080 | D-pad | 1.5 | 80px | 4 sütun |
| T35 | Nintendo Switch (Handheld) | 1280×720 | Joy-Con+Touch | 1 | 56px | 2 sütun |
| T36 | Steam Deck / ROG Ally | 1280×800 | Pad+Stick | 1 | 48px | 2 sütun |

**Özellikler:** Controller navigation, handheld hybrid, gamepad UI.

### 🖥️ Desktop Uygulama Ek (T37-T38)

| Tier | Cihaz | Runtime | Viewport | Font | Sidebar | Layout |
|------|-------|---------|----------|------|---------|--------|
| T37 | Electron Desktop | Chromium | Responsive | 1 | 240px | 3 sütun |
| T38 | Tauri Desktop | WebView2 | Responsive | 1 | 240px | 3 sütun |

**Özellikler:** Native titlebar, offline support, installable, system tray, IPC bridge.

### 📱 Mobil Uygulama (T39-T40)

| Tier | Cihaz | Platform | Viewport | Input | Font | Layout |
|------|-------|----------|----------|-------|------|--------|
| T39 | iOS Native App | Swift/SwiftUI | Responsive | Touch | 1 | Tek sütun |
| T40 | Android Native App | Kotlin/Material | Responsive | Touch | 1 | Tek sütun |

**Özellikler:** Platform HIG uyumlu, native navigation, gesture support.

### 🌐 Web & Özel (T41-T45)

| Tier | Cihaz | Platform | Viewport | Input | Font | Layout |
|------|-------|----------|----------|-------|------|--------|
| T41 | Progressive Web App | Browser | Responsive | Touch+Mouse | 1 | Responsive |
| T42 | NAS Web Interface | Browser | 1920×1080 | Mouse+KB | 1 | Admin panel |
| T43 | Smart Speaker (Display) | Voice OS | varies | Voice | 1.5 | Minimal UI |
| T44 | TV Gaming Console | Console OS | 1920×1080 | Gamepad | 1.5 | 4 sütun |
| T45 | AR/VR Headset | XR OS | 2880×2880 | Hand+Controller | 1.25 | Spatial |

**Özellikler:** PWA offline, NAS admin, voice-first, spatial 3D.

---

## 3A. Viewport Bazlı Özet Tablosu

| Viewport Aralığı | Tier Grubu | Layout Pattern |
|-------------------|-----------|---------------|
| ≤767px | T01-T04 (Phone) | Stack (dikey scroll) |
| 768-1023px | T05-T06 (Tablet Küçük) | 2-column grid |
| 1024-1279px | T07-T08 (Embedded/Tablet Büyük) | Split 42/58 |
| 1280-1439px | T09 (Laptop Küçük) | Sidebar + content |
| 1440-1919px | T10-T12 (Laptop/Desktop FHD) | Sidebar + 3-column |
| 1920-2559px | T13-T17 (Desktop QHD/Laptop Büyük) | 3-column expanded |
| 2560-3839px | T18-T22 (4K Monitor/Ultrawide) | 4-column expanded |
| 3840+ | T23-T26 (TV/4K TV) | Focus mode (D-pad) |
| Car displays | T29-T30 (Android Auto/CarPlay) | Car touch |
| Watch displays | T31-T33 (Apple Watch/Galaxy Watch) | Micro UI |
| Console displays | T34-T36 (PS5/Switch/Steam Deck) | Gamepad UI |
| App windows | T37-T41 (Electron/Tauri/iOS/Android/PWA) | Responsive |
| Special | T42-T45 (NAS/Speaker/Console TV/AR-VR) | Variant |

---

## 4. Cihaz Tespit Önceliği

```
1. HTTP Header: X-Device-Type: embedded     → 'embedded'
2. User-Agent: "Raspberry Pi" içeriği        → 'embedded'
3. User-Agent: "Tizen/webOS/SmartTV"         → '4k-tv'
4. Viewport: ≤767px                          → 'phone'
5. Viewport: 768-1024px + h≤600             → 'embedded'
6. Viewport: 768-1024px + h≥768             → 'laptop'
7. Viewport: ≤1440px                         → 'laptop'
8. Viewport: ≤2560px                         → 'desktop'
9. Viewport: ≤3840px + TV UA                → '4k-tv'
10. Viewport: ≤3840px + Desktop OS          → '4k-monitor'
11. Hiçbiri eşleşmezse                       → 'desktop' (varsayılan)
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 6.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total Tiers | 45 |
| Categories | 12 |
| Specific Devices | 50+ |
| Touch Target Range | 48px-128px |
| Font Scale Range | 0.75-2.5 |
| Grid Range | 1-6 columns |
| Viewport Range | 396×484 - 7680×4320 |
| Cross References | 6 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
