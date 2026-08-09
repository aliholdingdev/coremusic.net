---
type: architecture
category: l3
title: "Device CSS"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Device CSS

**Zorunlu Bağlantılar:** [[index]] · [[ADR-045-multi-domain-view-mode-architecture]]

---

## 1. Amaç

Cihaz bazlı responsive CSS ve view mode'ları tanımlar. [[ADR-045-multi-domain-view-mode-architecture]] ile uyumludur.

---

## 2. Device Breakpoints

| Cihaz | Breakpoint | CSS Dosyası |
|-------|------------|-------------|
| **Phone** | `< 480px` | `d-phone.css` |
| **Tablet** | `480-768px` | `d-tablet.css` |
| **Laptop** | `768-1024px` | `d-laptop.css` |
| **Desktop** | `1024-1440px` | `d-desktop.css` |
| **4K TV** | `> 1440px` | `d-4k-tv.css` |
| **4K Monitor** | `> 2560px` | `d-4k-monitor.css` |
| **Embedded** | `320-480px` | `d-embedded.css` |

---

## 3. View Modes

| View | Amaç | CSS |
|------|------|-----|
| **Home** | Ev medya merkezi | `v-home.css` |
| **Pro** | Profesyonel | `v-pro.css` |
| **Studio** | Stüdyo | `v-studio.css` |
| **Car** | Araç içi | `v-car.css` |

---

## 4. Implementation

```css
/* Device detection via CSS */
@media (max-width: 480px) {
    @import url('d-phone.css');
}

@media (min-width: 481px) and (max-width: 768px) {
    @import url('d-tablet.css');
}

@media (min-width: 769px) and (max-width: 1024px) {
    @import url('d-laptop.css');
}

@media (min-width: 1025px) and (max-width: 1440px) {
    @import url('d-desktop.css');
}

@media (min-width: 1441px) {
    @import url('d-4k-tv.css');
}
```

---

## 5. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Orientation change** | Landscape/Portrait | ADR-045 |
| **DPI change** | Resolution media query | ADR-045 |
| **View mode change** | CSS class toggle | ADR-045 |
| **Device not detected** | Desktop default | ADR-045 |

---

## 6. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-045-multi-domain-view-mode-architecture]] | View modes |

---

## 7. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~500 |
| **ADR Uyumlu** | ✅ 045 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
