---
title: "CoreMusic — 06 4K Expanded Layout Prompt"
type: prompt
category: ui-design
layout_id: "06"
date: 2026-09-20
version: 2.0.0
status: active
---

# 06 — 4K Expanded Layout

## 1. Layout Definition

| Property | Value |
|----------|-------|
| **ID** | `06-4k-expanded` |
| **Target Tiers** | 4K TV/Monitor (≥2561px) |
| **Usage** | Tüm sayfalar |
| **Type** | Expanded sidebar + 3-column content, scaled fonts |

## 2. ASCII Wireframe

```
┌─── 4K (3840×2160) ────────────────────────────────────────┐
│  HEADER: h=80px (büyük font, geniş logo)                   │
├────────────┬──────────────────────────┬───────────────────┤
│            │                          │                   │
│  SIDEBAR   │  CONTENT (55%)           │  PANEL (30%)      │
│  w=340px   │  3-column card grid      │  Detail / Info    │
│  large     │                          │                   │
│  items     │  ┌────┐┌────┐┌────┐     │  ┌──────────┐    │
│            │  │    ││    ││    │     │  │ Album    │    │
│ 🏠 Ana Say │  └────┘└────┘└────┘     │  │ Art      │    │
│ 🎵 Keşfet  │  ┌────┐┌────┐┌────┐     │  │ 400×400  │    │
│ 💿 Albümler│  │    ││    ││    │     │  └──────────┘    │
│ 👤 Sanatçı │  └────┘└────┘└────┘     │                   │
│ 📁 Göz At  │                          │  Title 24px      │
│ 📜 Geçmiş  │                          │  Meta 16px       │
│ ⚙ Ayarlar │                          │  Actions         │
│            │                          │                   │
├────────────┴──────────────────────────┴───────────────────┤
│  FOOTER: h=120px (büyük kontrol butonları)                 │
└────────────────────────────────────────────────────────────┘
```

## 3. Grid Structure

```css
/* 4K Expanded Layout */
.layout--4k {
  display: grid;
  grid-template-rows: 80px 1fr 120px;
  grid-template-columns: 340px 1fr;
  min-height: 100vh;
}

.layout--4k__header { grid-column: 1 / -1; }
.layout--4k__sidebar { grid-row: 2; }
.layout--4k__content {
  grid-row: 2;
  display: grid;
  grid-template-columns: 55% 30%;
  gap: 24px;
  padding: 24px 32px;
}
.layout--4k__footer { grid-column: 1 / -1; }
```

## 4. Token Overrides

| Token | Desktop | 4K |
|-------|---------|-----|
| `--header-h` | 70px | 80px |
| `--footer-h` | 104px | 120px |
| `--sidebar-w` | 260px | 340px |
| `--font-size-base` | 16px | 18px |
| `--spacing-scale` | 1 | 1.5 |

## 5. Code Example

```css
@media (min-width: 2561px) {
  .layout--4k {
    display: grid;
    grid-template-rows: 80px 1fr 120px;
    grid-template-columns: 340px 1fr;
    min-height: 100vh;
    --spacing-scale: 1.5;
  }

  .layout--4k__header { grid-column: 1 / -1; }
  .layout--4k__sidebar { grid-row: 2; padding: 20px 0; border-right: 1px solid var(--border-subtle); }
  .layout--4k__content { grid-row: 2; display: grid; grid-template-columns: 55% 30%; gap: 24px; padding: 24px 32px; overflow-y: auto; }
  .layout--4k__footer { grid-column: 1 / -1; }

  /* Scaled components */
  .layout--4k .nav-link { font-size: 14px; padding: 16px 12px; }
  .layout--4k .media-card { width: 200px; }
  .layout--4k .media-card__thumb { width: 200px; height: 200px; }
  .layout--4k .btn-primary { height: 64px; font-size: 18px; }
}
```

---

*06 4K Expanded Layout v2.0.0 — CoreMusic UI Design System*
