---
title: "CoreMusic — 06 Browse Page Prompt"
type: prompt
category: ui-design
page_id: "06"
route: "/browse"
layout: "04-laptop-sidebar"
date: 2026-09-20
version: 2.0.0
status: active
---

# 06 — Browse Page (Disk)

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | `/browse` |
| **Layout** | 04-laptop-sidebar + 3-column content |
| **Purpose** | Dosya tarayıcı, disk/kategori listesi |

## 2. Components Used

| Component | Count | Location |
|-----------|-------|----------|
| Disk List | 1 | Sidebar (240px) |
| File Browser | 1 | Content left |
| Info Panel | 1 | Content right (220px) |

## 3. Layout

```
SIDEBAR: Disk/kategori listesi (scrollable)
CONTENT: 3-column (Disk List | File Browser | Info Panel)
```

## 4. Code Example

```html
<main class="layout--laptop__content" style="display:grid;grid-template-columns:167px 1fr 220px;gap:16px">
  <nav class="disk-list" aria-label="Diskler"><!-- disk items --></nav>
  <section class="file-browser">
    <nav aria-label="Breadcrumb"><!-- breadcrumb --></nav>
    <div class="file-list" role="list"><!-- file rows --></div>
  </section>
  <aside class="info-panel"><!-- selected file info --></aside>
</main>
```

---

*06 Browse Page v2.0.0 — CoreMusic UI Design System*
