---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Responsive Architecture (45-Tier Token-First)"
type: architecture
category: ui-design
date: 2026-09-20
updated: 2026-09-27
status: active
version: 5.1.0
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

### 7.4 4K / High-DPI — Bileşen Ortalamama (No-Center)

**Bağlayıcı referanslar:** `.ai/CLAUDE.md` L536 · `.ai/AGENTS.md` L243 (§7.2 pre-flight) · `.ai/WORKFLOW.md` L760 · `00-device-matrix.md` §3 ("NO-CENTER (4K)") · `.templates/ui-design/flow-template.md` §3.7 (T18 satırı).

| # | Kural | ✅ Doğru | ❌ Yasak | Kaynak |
|---|-------|---------|----------|--------|
| 1 | **No-Center** | 3840px ve üzerinde layout **tam genişlikte akar**; grid sütunları tier token'ı ile genişler | 1920 genişliğinde sabit bloğun `width: 1920px; margin: 0 auto` ile ortalanması | `00-device-matrix.md` (NO-CENTER) · CLAUDE.md L536 |
| 2 | **2× ölçek + max-width** | 3840 = 2 × 1920 → 1920 tier'ından türeyen **layout/konteyner ölçüsü 2 katıdır**; `max-width` ile üst sınır verilir, ölçek token'ı ile uygulanır | Ölçüleri iki breakpoint arasında **ortalayarak** (uzlaşı değere düşürerek) sabitlemek | Görev kuralı · §3 breakpoint 3840 |
| 3 | **Tipografi ayrı ölçek** | `--cm-font-scale` tier bloğundan okunur (4K: 1.25) | Font ölçeğini layout ölçeğiyle karıştırmak | `tokens/platform-tokens.md` T12 · `tokens/component-tokens.md` §18 |
| 4 | **High-DPI (DPR ≥ 2)** | CSS piksel değeri DPR ile çarpılmaz; büyüme yalnızca viewport/token ölçeğiyle | `devicePixelRatio` ile px çarpmak | §2 Token-First |

```css
/* ✅ 4K No-Center — max-width + ölçek (3840 = 1920 × 2) */
@media (min-width: 3840px) {
  :root {
    --cm-grid-cols: 4;                 /* platform-tokens T12 */
    --cm-widget-grid-cols: 4;
    --cm-font-scale: 1.25;             /* tipografi: ayrı ölçek */
    --cm-border-radius-scale: 1.25;
  }
}
.layout {
  max-width: 100%;                     /* sınır: max-width — akış korunur */
  padding-inline: var(--cm-content-padding);
}

/* ❌ 4K'da ortalamama (YASAK) */
/* .layout { width: 1920px; margin: 0 auto; } */
```

> ⚠️ **Truth Mode — açık çelişki (vault steward kararı bekliyor):** Kural 2 "1920 × 2" der; buna karşılık `tokens/platform-tokens.md` T12 (4K) bloğu ölçüleri 1920'ye oranla **~1.25×** verir (font 1.25 · radius 1.25 · grid 3→4). Bu dosya **2× kuralını layout/konteyner ölçeği için bağlayıcı** yazar; tipografi/spacing değerleri T12 token bloğundan okunmaya devam eder. İki ölçeği tek değere indirgemek §7.4 ihlalidir.

---

## 12. Geriye Dönük Uyumluluk (Fallback ZORUNLU)

**Bağlayıcı referanslar:** `.ai/AGENTS.md` L243 (fallback eksikse → **RED**) · `.ai/CLAUDE.md` L536 · `.templates/ui-design/screen-spec-template.md` §3.11 (spec fallback zinciri) · `decisions/accepted/ADR-006-performance-targets.md` (fallback → CLS/LCP ölçüm bağlamı).

> **Numaralandırma notu:** Bu bölümün numarası **12**'dir — vault çapraz referansları (AGENTS.md §7.2, CLAUDE.md L536, engine.md) adı sabitler. `Quality Report`, Kalıp A §3.3-4 gereği dosyanın **son H2'si** olarak §8'de kalır; §9-§11 bu sürümde açılmamıştır (boş başlık yasak — Kalıp A §6.1 #5).

| # | Senaryo | Fallback zinciri (zorunlu) | Kaynak |
|---|---------|---------------------------|--------|
| 1 | Tier'a ait screen spec yoksa | En yakın tier spec'i (önce bir üst, sonra bir alt viewport) → `:root` default (1024×600 embedded) | `screens/00-ascii-art-index` · Kalıp D §3.11 |
| 2 | Media query eşleşmezse | `:root` default token'ları her viewport'ta geçerlidir — ekran asla **stilssiz** açılmaz | §3 · §6 |
| 3 | Token tanımsızsa | `var(--token, <yedek-değer>)` — her token'ın gömülü yedeği vardır | §5 Token-First |
| 4 | `backdrop-filter` desteklenmiyorsa | Solid `rgba()` arka plan (`background` fallback satırı zorunlu) | Kalıp D §3.9.2 |
| 5 | Boyut sıçraması (CLS) | Fallback değeri sabit boyut/token'dan gelir; `auto → px` sıçraması yasak (CLS gate) | ADR-006 (ölçüm bağlamı) |
| 6 | Fallback hiç yoksa | **RED** — AGENTS.md §7.2 pre-flight kapısı | AGENTS.md L243 |

```css
/* ✅ Fallback zinciri — her katmanda yedek değer */
.header {
  height: var(--cm-header-h, 60px);     /* token yoksa gömülü yedek */
  background: rgba(0, 0, 0, .7);        /* glass desteklemeyen tarayıcı: solid */
}
@supports (backdrop-filter: blur(1px)) {
  .header { backdrop-filter: blur(20px); }
}

/* ❌ Fallback'siz — token tanımsızsa layout kırılır (boş ekran / CLS) */
/* .header { height: var(--cm-header-h); } */
```

**Kapı:** Responsive uyum kontrolünde bu dosyanın **§7.4 ve §12** birlikte okunur; fallback eksikse görev RED ile döner.

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 5.1.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 9 H2 (§1-§8 + §12) + 1 H3 (§7.4) — §9-§11 açılmadı (boş başlık yasak) |
| Architecture Principle | Token-First + Figma Pixel-Perfect + 4K No-Center (§7.4) |
| CSS Files | 25+ |
| Device CSS | 7 |
| View Mode CSS | 4 |
| Media Query Breakpoints | 10 |
| New Components | Widget Area, Quick Apps, Mini Card |
| Figma Sources | 1024×600 + 1920×1080 |
| Cross References | 4 |
| Last Updated | 2026-09-27 |
| Version Notu (Truth Mode) | Frontmatter 4.0.0 ↔ Quality Report 5.0.0 uyuşmazlığı bu sürümde tek değerde birleştirildi (5.1.0); kaynak: bu dosya L9 / L182 (önceki sürüm) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-27
**Mode:** Red Team · Human Mode · Truth Mode
