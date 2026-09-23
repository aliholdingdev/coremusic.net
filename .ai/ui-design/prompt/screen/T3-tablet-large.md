---
title: "CoreMusic — Screen Prompt T3 Tablet Large"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T3-tablet-large
---

# T3: Tablet Large Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T3-tablet-large |
| Viewport | 1024-1279px |
| Cihaz | iPad Air, iPad Pro 11", Samsung Galaxy Tab S |
| PPI | 264-326 |
| Input | Touch + Stylus (Apple Pencil, S Pen) |
| Layout Pattern | 2-column split |
| Orientation | Landscape tercih edilir |
| Safe Area | Normal |
| CSS Media Query | `@media (min-width: 1024px) and (max-width: 1279px)` |

---

## 2. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Touch target min | 48×48px |
| Touch target rec | 52×52px |
| Touch spacing | ≥12px |
| Font scale | 1× (base 16px) |
| Min font size | 13px |
| Max font size | 28px |
| Spacing scale | xs:4 sm:8 md:12 lg:16 xl:24 2xl:32 3xl:48 |
| Border radius | sm:6 md:10 lg:16 xl:24 full:9999 |
| Glass blur | `blur(12px)` |

### Renk Paleti

```css
:root {
  --bg-primary: #0a0a0f;
  --bg-secondary: #12121a;
  --bg-elevated: #1a1a24;
  --text-primary: #f0f0f5;
  --text-secondary: #a0a0b0;
  --accent: #7c5cff;
  --border-subtle: rgba(255,255,255,0.08);
}
```

---

## 3. Ekran Promptları

### 3.1 Home

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky top 60px | `--header-h: 60px` |
| Sidebar (Left) | 220px, browse sayfasında | `--sidebar-w: 220px` |
| Content Grid | 2 sütun, 16px gap | `--grid-gap: 16px` |
| Footer Player | Fixed bottom 88px | `--footer-h: 88px` |

**Notlar:** Tablet-large'da landscape modda sidebar görünebilir. Portrait'da header tab'ları.

### 3.2 Auth Login

| Bileşen | Konum | Token |
|---------|-------|-------|
| Logo | Center, 96px | `--font-size-2xl: 28px` |
| Form | Max-width 440px, centered | `margin: 0 auto` |
| Input | Full-width, 48px | `min-height: 48px` |

### 3.3 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky, tabs | `--header-h: 60px` |
| Sidebar | 220px (browse) | `--sidebar-w: 220px` |
| Album Grid | 2-3 sütun | `grid-template-columns: repeat(auto-fill, minmax(200px, 1fr))` |

### 3.4 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 88px | `--footer-h: 88px` |
| Cover Art | 64×64px | `--radius-md: 10px` |
| Seek Bar | Full-width | `--accent` |
| Controls | Play 56px, others 48px | `--touch-min: 48px` |
| Volume | Slider görünür | `display: block` |

---

## 4. Code Example

```css
@media (min-width: 1024px) and (max-width: 1279px) {
  :root {
    --header-h: 60px;
    --footer-h: 88px;
    --sidebar-w: 220px;
    --grid-gap: 16px;
    --touch-min: 48px;
  }

  .main-content {
    padding-top: var(--header-h);
    padding-bottom: var(--footer-h);
    min-height: 100dvh;
    padding-left: 24px;
    padding-right: 24px;
  }

  .home-layout {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--grid-gap);
  }

  .browse-layout {
    display: grid;
    grid-template-columns: var(--sidebar-w) 1fr;
    gap: var(--grid-gap);
  }

  .sidebar {
    position: sticky;
    top: var(--header-h);
    height: calc(100vh - var(--header-h) - var(--footer-h));
    overflow-y: auto;
    padding: var(--space-lg);
    border-right: 1px solid var(--border-subtle);
    scrollbar-width: thin;
  }

  .header {
    position: sticky;
    top: 0;
    height: var(--header-h);
    display: flex;
    align-items: center;
    padding: 0 24px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--footer-h);
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 0 24px;
    background: var(--bg-elevated);
    border-top: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player__cover {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-md);
    object-fit: cover;
  }

  .footer-player__seek {
    flex: 1;
    height: 4px;
    border-radius: 2px;
    background: var(--border-default);
    appearance: none;
    cursor: pointer;
  }

  .footer-player__seek::-webkit-slider-thumb {
    appearance: none;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--accent);
    cursor: pointer;
  }

  .glass {
    background: rgba(18, 18, 26, 0.72);
    backdrop-filter: blur(12px) saturate(180%);
    border: 1px solid var(--border-subtle);
  }
}
```

---

## 5. Yasaklar

| Yasak | Doğru |
|-------|-------|
| 4+ sütun grid | Max 3 sütun |
| `backdrop-filter: blur(20px)` | `blur(12px)` |
| Font < 13px | Min 13px |
| Fixed sidebar (tüm sayfalarda) | Sadece browse |
