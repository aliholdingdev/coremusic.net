---
title: "CoreMusic — Screen Prompt T2 Tablet Small"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T2-tablet-small
---

# T2: Tablet Small Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T2-tablet-small |
| Viewport | 768-1023px |
| Cihaz | iPad Mini, Samsung Galaxy Tab A, 8" tabletler |
| PPI | 163-326 |
| Input | Touch + Stylus |
| Layout Pattern | 2-column grid |
| Orientation | Portrait (varsayılan), Landscape |
| Safe Area | Normal (notch yok) |
| CSS Media Query | `@media (min-width: 768px) and (max-width: 1023px)` |

---

## 2. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Touch target min | 48×48px |
| Touch target rec | 52×52px |
| Touch spacing | ≥8px |
| Font scale | 1× (base 16px) |
| Min font size | 12px |
| Max font size | 24px |
| Spacing scale | xs:4 sm:8 md:12 lg:16 xl:24 2xl:32 3xl:48 |
| Border radius | sm:6 md:10 lg:16 xl:24 full:9999 |
| Scroll bar | Gizli |
| Hover | Kısmi (tablet hover algılanabilir) |
| Glass blur | `blur(8px)` (orta performans) |

### Renk Paleti

```css
:root {
  --bg-primary: #0a0a0f;
  --bg-secondary: #12121a;
  --bg-elevated: #1a1a24;
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
| Header | Sticky top 56px | `--header-h: 56px` |
| Welcome Section | Full-width | `--space-xl: 24px` padding |
| Widget Grid | 2 sütun, 12px gap | `--grid-gap: 12px` |
| Recent Cards | Horizontal scroll | `scroll-snap-type: x mandatory` |
| Footer Player | Fixed bottom 80px | `--footer-h: 80px` |

**Notlar:** Tablet-small'da top header, bottom'da player bar. Tab bar header içinde.

### 3.2 Auth Login

| Bileşen | Konum | Token |
|---------|-------|-------|
| Logo | Center, 80px | `--font-size-2xl: 24px` |
| Form Container | Max-width 400px, centered | `margin: 0 auto` |
| Input | Full-width, 48px | `min-height: 48px` |
| Button | Full-width, 48px | `--touch-min: 48px` |

### 3.3 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky, tab'lar | `--header-h: 56px` |
| Genre Tabs | Horizontal scroll | `--radius-full` |
| Album Grid | 2 sütun, 12px gap | `grid-template-columns: repeat(2, 1fr)` |
| Album Card | Cover (1:1) + info | `--radius-md: 10px` |

### 3.4 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 80px | `--footer-h: 80px` |
| Cover Art | 56×56px mini | `--radius-sm: 6px` |
| Seek Bar | Full-width | `--accent` |
| Controls | Play 56px, others 44px | `--touch-min: 44px` |

---

## 4. Code Example

```css
@media (min-width: 768px) and (max-width: 1023px) {
  :root {
    --header-h: 56px;
    --footer-h: 80px;
    --grid-gap: 12px;
    --touch-min: 48px;
  }

  .main-content {
    padding-top: var(--header-h);
    padding-bottom: var(--footer-h);
    min-height: 100dvh;
    padding-left: 16px;
    padding-right: 16px;
  }

  .home-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--grid-gap);
  }

  .album-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--grid-gap);
  }

  .header {
    position: sticky;
    top: 0;
    height: var(--header-h);
    display: flex;
    align-items: center;
    padding: 0 16px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .header__tabs {
    display: flex;
    gap: var(--space-lg);
    margin-left: auto;
  }

  .header__tab {
    padding: 8px 16px;
    border-radius: var(--radius-full);
    font-size: 14px;
    color: var(--text-secondary);
    touch-action: manipulation;
  }

  .header__tab.active {
    background: var(--accent);
    color: #fff;
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
    padding: 0 16px;
    background: var(--bg-elevated);
    border-top: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player__cover {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-sm);
    object-fit: cover;
  }

  .footer-player__controls {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-left: auto;
  }

  .glass {
    background: rgba(18, 18, 26, 0.72);
    backdrop-filter: blur(8px) saturate(180%);
    -webkit-backdrop-filter: blur(8px) saturate(180%);
    border: 1px solid var(--border-subtle);
  }
}
```

---

## 5. Yasaklar

| Yasak | Doğru |
|-------|-------|
| `backdrop-filter: blur(20px)` | `blur(8px)` orta performans |
| 3+ sütun grid | Max 2 sütun |
| Sidebar (fixed) | Header tab'ları |
| Font < 12px | Min 12px |
| `vw/vh` header/footer | `px` birimleri |
