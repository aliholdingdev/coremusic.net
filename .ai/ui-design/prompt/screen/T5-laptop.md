---
title: "CoreMusic — Screen Prompt T5 Laptop"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T5-laptop
---

# T5: Laptop Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T5-laptop |
| Viewport | 1280-1919px |
| Cihaz | 13"-16" laptoplar (MacBook Air, ThinkPad, Dell XPS) |
| PPI | 113-227 |
| Input | Mouse + Klavye (trackpad) |
| Layout Pattern | Sidebar + Content |
| Orientation | Landscape (varsayılan) |
| Safe Area | Yok |
| CSS Media Query | `@media (min-width: 1280px) and (max-width: 1919px)` |

---

## 2. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Interactive target min | 44×44px |
| Interactive target rec | 48×48px |
| Spacing | ≥4px |
| Font scale | 1.1× |
| Base size | 17.6px |
| Min font size | 14px |
| Max font size | 30px |
| Spacing scale | xs:4 sm:8 md:12 lg:16 xl:24 2xl:32 3xl:48 |
| Border radius | sm:6 md:10 lg:16 xl:24 full:9999 |
| Glass blur | `blur(16px) saturate(180%)` |
| Hover | VAR (150ms ease) |
| Cursor | `pointer` tüm interaktif elemanlarda |

### Renk Paleti

```css
:root {
  --bg-primary: #0a0a0f;
  --bg-secondary: #12121a;
  --bg-elevated: #1a1a24;
  --bg-glass: rgba(18, 18, 26, 0.72);
  --text-primary: #f0f0f5;
  --text-secondary: #a0a0b0;
  --text-muted: #606070;
  --accent: #7c5cff;
  --accent-hover: #9070ff;
  --border-subtle: rgba(255,255,255,0.08);
  --border-default: rgba(255,255,255,0.12);
}
```

---

## 3. Ekran Promptları

### 3.1 Home

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky top 64px | `--header-h: 64px` |
| Sidebar | 240px, sol taraf | `--sidebar-w: 240px` |
| Content | Grid 3-4 sütun | `--grid-gap: 16px` |
| Footer Player | Fixed bottom 96px | `--footer-h: 96px` |

**Notlar:** Laptop'da sidebar her zaman görünür (1280px+). Mouse hover aktif.

### 3.2 Auth Login

| Bileşen | Konum | Token |
|---------|-------|-------|
| Logo | Center, 96px | `--font-size-2xl: 28px` |
| Form | Max-width 440px, centered | `margin: 0 auto` |
| Input | Full-width, 44px | `min-height: 44px` |
| Button | Full-width, 44px | `min-height: 44px` |

### 3.3 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky, 64px | `--header-h: 64px` |
| Sidebar | 240px | `--sidebar-w: 240px` |
| Album Grid | 3-4 sütun | `grid-template-columns: repeat(auto-fill, minmax(200px, 1fr))` |

### 3.4 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 96px | `--footer-h: 96px` |
| Cover Art | 72×72px | `--radius-md: 10px` |
| Seek Bar | Full-width | `--accent` |
| Controls | Play 56px, others 48px | `min-width: 44px` |
| Volume | Slider görünür | `display: block` |
| Queue | Mini queue panel | Sidebar içi |

---

## 4. Code Example

```css
@media (min-width: 1280px) and (max-width: 1919px) {
  :root {
    --header-h: 64px;
    --footer-h: 96px;
    --sidebar-w: 240px;
    --grid-gap: 16px;
    --font-scale: 1.1;
  }

  .main-content {
    padding-top: var(--header-h);
    padding-bottom: var(--footer-h);
    min-height: 100dvh;
    display: grid;
    grid-template-columns: var(--sidebar-w) 1fr;
  }

  .sidebar {
    position: sticky;
    top: var(--header-h);
    height: calc(100vh - var(--header-h) - var(--footer-h));
    overflow-y: auto;
    padding: 16px;
    border-right: 1px solid var(--border-subtle);
    scrollbar-width: thin;
  }

  .sidebar__nav {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .sidebar__link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    font-size: 14px;
    text-decoration: none;
    transition: all 150ms ease;
    cursor: pointer;
  }

  .sidebar__link:hover {
    background: var(--bg-elevated);
    color: var(--text-primary);
  }

  .sidebar__link.active {
    background: var(--accent);
    color: #fff;
  }

  .content-area {
    padding: 24px;
    overflow-y: auto;
  }

  .home-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--grid-gap);
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
    width: 72px;
    height: 72px;
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

  .interactive:hover {
    background: var(--bg-elevated);
    border-color: var(--border-strong);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
    transition: all 150ms ease;
    cursor: pointer;
  }

  .card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: var(--accent);
  }

  a:hover {
    color: var(--accent-hover);
    text-decoration: underline;
    text-underline-offset: 3px;
  }

  *:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
    border-radius: var(--radius-sm);
  }

  .glass {
    background: var(--bg-glass);
    backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid var(--border-subtle);
  }
}
```

---

## 5. Yasaklar

| Yasak | Doğru |
|-------|-------|
| `vw/vh` header/footer | `px` birimleri |
| `z-index > 2000` | Max 1200 |
| Font < 14px | Min 14px (1.1× scale) |
| 5+ sütun grid | Max 4 sütun |
| Hover olmayan interaktif eleman | Tümüne hover |
