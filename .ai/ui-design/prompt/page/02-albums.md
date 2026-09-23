---
title: "CoreMusic — 02 Albums Page Prompt"
type: prompt
category: ui-design
page_id: "02"
route: "/albums"
layout: "05-desktop-3col"
date: 2026-09-20
version: 2.0.0
status: active
---

# 02 — Albums Page

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | `/albums` |
| **Layout** | 05-desktop-3col (60/40 split) |
| **Purpose** | Albüm listesi, card grid, detail panel |

## 2. Components Used

| Component | Count | Location |
|-----------|-------|----------|
| C01 Nav Link | 8 | Header |
| C11 Genre Tabs | 1 | Content top |
| C09 Media Card | 9+ | Content left (60%) |
| C10 Detail Panel | 1 | Content right (40%) |

## 3. Layout

```
LEFT (60%): Genre Tabs → Card Grid (3×3, 140px cards)
RIGHT (40%): Detail Panel (album art + metadata + actions)
```

## 4. Interactions

| Element | Action |
|---------|--------|
| Genre tabs | Filter albums by genre |
| Card click | Select album, show detail |
| Play button | Start playing album |
| Shuffle button | Shuffle play |

## 5. Code Example

```html
<div class="layout layout--desktop">
  <header class="site-header">...</header>
  <aside class="layout--desktop__sidebar"><!-- C01 nav --></aside>
  <main class="layout--desktop__content">
    <section class="content-left">
      <div class="genre-tabs" role="tablist"><!-- C11 --></div>
      <div class="media-card-grid"><!-- C09 cards --></div>
    </section>
    <aside class="content-right">
      <div class="detail-panel"><!-- C10 --></div>
    </aside>
  </main>
  <footer class="site-footer">...</footer>
</div>
```

---

*02 Albums Page v2.0.0 — CoreMusic UI Design System*
