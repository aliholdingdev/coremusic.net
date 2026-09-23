---
title: "CoreMusic — Screen Prompt T6 Desktop FHD"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T6-desktop
---

# T6: Desktop FHD Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T6-desktop |
| Viewport | 1920px (Full HD) |
| Cihaz | 24" masaüstü monitör, 15.6" FHD laptop |
| PPI | 93-141 |
| Input | Mouse + Klavye |
| Layout Pattern | 3-column |
| Orientation | Landscape |
| Safe Area | Yok |
| CSS Media Query | `@media (min-width: 1920px) and (max-width: 2559px)` |

---

## 2. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Interactive target min | 44×44px |
| Interactive target rec | 48×48px |
| Spacing | ≥4px |
| Font scale | 1.2× |
| Base size | 19.2px |
| Min font size | 14.4px |
| Max font size | 32px |
| Spacing scale | xs:4 sm:8 md:12 lg:16 xl:24 2xl:32 3xl:48 |
| Border radius | sm:6 md:10 lg:16 xl:24 full:9999 |
| Glass blur | `blur(20px) saturate(180%)` |
| Hover | VAR (150ms ease) |
| Max content | 1440px centered |

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
| Header | Sticky top 70px | `--header-h: 70px` |
| Sidebar | 280px, sol | `--sidebar-w: 280px` |
| Content | 3-column grid | `--grid-gap: 16px` |
| Footer Player | Fixed bottom 104px | `--footer-h: 104px` |
| Max Content | 1440px centered | `max-width: 1440px; margin: 0 auto` |

### 3.2 Auth Login

| Bileşen | Konum | Token |
|---------|-------|-------|
| Logo | Center, 96px | `--font-size-2xl: 28.8px` |
| Form | Max-width 480px, centered | `margin: 0 auto` |
| Input | Full-width, 48px | `min-height: 48px` |
| Button | Full-width, 48px | `min-height: 48px` |

### 3.3 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky, 70px | `--header-h: 70px` |
| Sidebar | 280px | `--sidebar-w: 280px` |
| Album Grid | 4 sütun | `grid-template-columns: repeat(4, 1fr)` |

### 3.4 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 104px | `--footer-h: 104px` |
| Cover Art | 80×80px | `--radius-md: 10px` |
| Seek Bar | Full-width | `--accent` |
| Controls | Play 64px, others 48px | `min-width: 44px` |
| Volume | Slider | `display: block` |
| Queue | 280px panel | Sidebar'da |

---

## 4. Code Example

```css
@media (min-width: 1920px) and (max-width: 2559px) {
  :root {
    --header-h: 70px;
    --footer-h: 104px;
    --sidebar-w: 280px;
    --grid-gap: 16px;
    --font-scale: 1.2;
  }

  .main-content {
    padding-top: var(--header-h);
    padding-bottom: var(--footer-h);
    min-height: 100dvh;
    display: grid;
    grid-template-columns: var(--sidebar-w) 1fr;
    max-width: 1440px;
    margin: 0 auto;
    padding-left: 24px;
    padding-right: 24px;
  }

  .sidebar {
    position: sticky;
    top: var(--header-h);
    height: calc(100vh - var(--header-h) - var(--footer-h));
    overflow-y: auto;
    padding: 16px;
    border-right: 1px solid var(--border-subtle);
  }

  .content-area {
    padding: 24px;
  }

  .home-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--grid-gap);
  }

  .header {
    position: sticky;
    top: 0;
    height: var(--header-h);
    display: flex;
    align-items: center;
    padding: 0 24px;
    max-width: 1440px;
    margin: 0 auto;
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
    max-width: 1440px;
    margin: 0 auto;
    background: var(--bg-elevated);
    border-top: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player__cover {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-md);
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

  .glass {
    background: var(--bg-glass);
    backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--border-subtle);
  }

  *:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
  }
}
```

---

## 5. Yasaklar

| Yasak | Doğru |
|-------|-------|
| `vw/vh` header/footer | `px` + `max-width: 1440px` |
| 5+ sütun grid | Max 4 sütun |
| Font < 14.4px | Min 14.4px (1.2×) |
| Hover olmayan eleman | Tümüne hover |
