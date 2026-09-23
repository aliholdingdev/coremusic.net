---
title: "CoreMusic — 01 Home Page Prompt"
type: prompt
category: ui-design
page_id: "01"
route: "/"
layout: "03-embedded-split"
date: 2026-09-20
version: 2.0.0
status: active
---

# 01 — Home Page

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | `/` |
| **Layout** | 03-embedded-split (42/58) |
| **Platform** | home-1024 (RPi5 7" Touch, 1024×600) |
| **Purpose** | Ana sayfa, widget grid, recent playing, playlist |

## 2. Components Used

| Component | Count | Location | Description |
|-----------|-------|----------|-------------|
| C01 Nav Link | 8 | Header | Ana navigasyon |
| C02 Status Widget | 3 | Header | WiFi, BT, Battery |
| C03 User Pill | 1 | Header | Avatar + dropdown |
| C09 Media Card | 8+ | Left panel | Mini card grid |
| C04 Primary Button | 1 | Now Playing | Hemen Çal |
| Glass Widgets | 4 | Right panel | Hoparlör, Hava, Takvim, Klasör |

## 3. Layout Structure

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

## 4. Interactions

| Element | Action |
|---------|--------|
| Nav links | Navigate to page |
| Now Playing Card | Navigate to album detail |
| Media cards | Navigate to album/playlist |
| Widget panels | Open corresponding modal/page |
| Transport controls | Play/pause/prev/next |
| Seek bar | Drag to seek |
| Volume slider | Adjust volume |

## 5. Code Example

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

---

*01 Home Page v2.0.0 — CoreMusic UI Design System*
