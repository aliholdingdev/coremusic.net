---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 01 Home Page Prompt"
type: prompt
category: ui-design
page_id: "01"
route: "/"
layout: "03-embedded-split"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 01 — Home Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/` |
| **Layout** | 03-embedded-split (42/58) |
| **Platform** | home-1024 (RPi5 7" Touch, 1024×600) |
| **Purpose** | Ana sayfa, widget grid, recent playing, playlist |

**Components Used**

| Component | Count | Location | Description |
|-----------|-------|----------|-------------|
| C01 Nav Link | 8 | Header | Ana navigasyon |
| C02 Status Widget | 3 | Header | WiFi, BT, Battery |
| C03 User Pill | 1 | Header | Avatar + dropdown |
| C09 Media Card | 8+ | Left panel | Mini card grid |
| C04 Primary Button | 1 | Now Playing | Hemen Çal |
| Glass Widgets | 4 | Right panel | Hoparlör, Hava, Takvim, Klasör |

**Interactions**

| Element | Action |
|---------|--------|
| Nav links | Navigate to page |
| Now Playing Card | Navigate to album detail |
| Media cards | Navigate to album/playlist |
| Widget panels | Open corresponding modal/page |
| Transport controls | Play/pause/prev/next |
| Seek bar | Drag to seek |
| Volume slider | Adjust volume |

### Required Inputs

- `viewport`: 1024×600 (RPi5 7" Touch) · 2 sütun · sidebar yok (kaynak: 00-device-matrix.md L111)
- `touch`: 48px minimum dokunma hedefi (kaynak: 00-device-matrix.md L111)
- `font_scale`: 1× (kaynak: 00-device-matrix.md L111)
- `navlink`: padding 12px 16px · gap 8px · icon 20px · font 14px/500 · durumlar default/hover/active/disabled (kaynak: 02-component-inventory.md L35-L39)
- `button`: `.btn--primary`/`.btn--secondary` · padding 8px 16px · min-h 36px · radius 12px · font 14px/600 · durumlar default/hover/active/disabled/loading (kaynak: 02-component-inventory.md L65-L68)
- `widget_grid`: Embedded 1024 → row1 2×2 · row2 1×5 · row3 1×5 = 12 slot (kaynak: 02-component-inventory.md L199)
- `mini_card`: Embedded 169×43px · 40px art · 8.5px title (kaynak: 02-component-inventory.md L216)

### ASCII Reference

```
┌─── HEADER (h=60px) ──────────────────────────────────────┐
│  [Logo] [Nav×8] [Status×3] [User Pill] [Settings] [Power] │
├──────────────────────┬────────────────────────────────────┤
│ LEFT (42% = 430px)   │ RIGHT (58% = 578px)               │
│                      │                                     │
│  Now Playing Card    │  Widget Grid (2×2)                 │
│  ┌──────────────┐    │  ┌─────────┬─────────┐            │
│  │ Art 100×100  │    │  │Widget 1 │Widget 2 │            │
│  │ Title        │    │  ├─────────┼─────────┤            │
│  │ Seek bar     │    │  │Widget 3 │Widget 4 │            │
│  └──────────────┘    │  └─────────┴─────────┘            │
│                      │                                     │
│  "En Son Dinlenen"   │  Mini Card (bottom)                │
│  [C09]×4             │  ┌──────────────────────┐          │
│                      │  │ [50×50] Artist Info   │          │
│  "Oynatma Listeleri" │  └──────────────────────┘          │
│  [C09]×4+            │                                     │
├──────────────────────┴────────────────────────────────────┤
│ FOOTER (h=90px) — Player Controls                         │
│ Seek bar (h=3px) | Art 120×120 | Title | Transport | Vol  │
└────────────────────────────────────────────────────────────┘
```

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```html
<div class="layout layout--embedded">
  <header class="site-header">...</header>
  <main class="layout--embedded__content">
    <div class="layout--embedded__left">
      <div class="now-playing-card"><!-- C09 variant --></div>
      <section aria-label="En Son Dinlenen">
        <h2>En Son Dinlenen</h2>
        <div class="media-card-grid"><!-- C09 cards --></div>
      </section>
      <section aria-label="Oynatma Listeleri">
        <h2>Oynatma Listeleri</h2>
        <div class="media-card-grid"><!-- C09 cards --></div>
      </section>
    </div>
    <div class="layout--embedded__right">
      <div class="widget glass-panel">🎵 Hoparlörler</div>
      <div class="widget glass-panel">☁ Hava Durumu</div>
      <div class="widget glass-panel">📅 Takvim</div>
      <div class="widget glass-panel">📂 Klasörlerim</div>
    </div>
  </main>
  <footer class="site-footer"><!-- C09 variant: player --></footer>
</div>
```

### Validation

- [ ] Dokunma hedefleri 48px proje zeminine göre ölçülü (AA SC 2.5.8 = 24px); Toggle 48×48px, Slider 48px alan, Dropdown item 48px düzeltmeleri uygulanmış (kanıt: 04-accessibility-gaps.md L27, L39, L64-L96)
- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Primary button kontrastı 3.1:1 ❌ → koyu text `#1a1a2e` veya açık pink `#ff6ee4` düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L110, L120-L123)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*01 Home Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
