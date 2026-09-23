---
title: "CoreMusic — Screen Prompt T7 Desktop 4K"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T7-desktop-4k
---

# T7: Desktop 4K Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T7-desktop-4k |
| Viewport | 2560-3839px (QHD / 4K) |
| Cihaz | 27" QHD, 32" 4K monitör |
| PPI | 109-163 |
| Input | Mouse + Klavye |
| Layout Pattern | Expanded 3-column |
| Orientation | Landscape |
| Safe Area | Yok |
| CSS Media Query | `@media (min-width: 2560px) and (max-width: 3839px)` |

---

## 2. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Interactive target min | 44×44px |
| Font scale | 1.4× |
| Base size | 22.4px |
| Min font size | 16px |
| Max font size | 40px |
| Sidebar width | 320px |
| Header height | 80px |
| Footer height | 120px |
| Max content | 1800px centered |
| Glass blur | `blur(20px) saturate(180%)` |
| Hover | VAR (150ms ease) |
| Spacing scale | xs:8 sm:12 md:16 lg:24 xl:32 2xl:48 3xl:64 |

### Renk Paleti (Yüksek Kontrast)

```css
:root {
  --bg-primary: #08080d;
  --bg-secondary: #101018;
  --bg-elevated: #181822;
  --text-primary: #ffffff;
  --text-secondary: #b0b0c0;
  --text-muted: #707080;
  --accent: #7c5cff;
  --accent-hover: #9070ff;
  --border-subtle: rgba(255,255,255,0.10);
  --border-default: rgba(255,255,255,0.15);
}
```

---

## 3. Ekran Promptları

### 3.1 Home

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky top 80px | `--header-h: 80px` |
| Sidebar | 320px | `--sidebar-w: 320px` |
| Content | 4-column grid | `--grid-gap: 24px` |
| Footer Player | Fixed bottom 120px | `--footer-h: 120px` |
| Max Content | 1800px | `max-width: 1800px; margin: 0 auto` |

### 3.2 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Album Grid | 5 sütun | `grid-template-columns: repeat(5, 1fr)` |
| Album Card | Cover (1:1) + bilgi | `--radius-lg: 16px` |

### 3.3 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 120px | `--footer-h: 120px` |
| Cover Art | 96×96px | `--radius-md: 10px` |
| Seek Bar | Full-width, 6px | `--accent` |
| Controls | Play 72px, others 56px | `min-width: 44px` |

---

## 4. Code Example

```css
@media (min-width: 2560px) and (max-width: 3839px) {
  :root {
    --header-h: 80px;
    --footer-h: 120px;
    --sidebar-w: 320px;
    --grid-gap: 24px;
    --font-scale: 1.4;
  }

  .main-content {
    padding-top: var(--header-h);
    padding-bottom: var(--footer-h);
    min-height: 100dvh;
    display: grid;
    grid-template-columns: var(--sidebar-w) 1fr;
    max-width: 1800px;
    margin: 0 auto;
    padding-left: 32px;
    padding-right: 32px;
  }

  .home-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: var(--grid-gap);
  }

  .header {
    position: sticky;
    top: 0;
    height: var(--header-h);
    display: flex;
    align-items: center;
    padding: 0 32px;
    max-width: 1800px;
    margin: 0 auto;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .header__logo {
    height: 48px;
  }

  .footer-player {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--footer-h);
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 0 32px;
    max-width: 1800px;
    margin: 0 auto;
    background: var(--bg-elevated);
    border-top: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player__cover {
    width: 96px;
    height: 96px;
    border-radius: var(--radius-lg);
  }

  .sidebar__link {
    padding: 14px 16px;
    font-size: 18px;
    gap: 16px;
  }

  .glass {
    background: var(--bg-glass);
    backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--border-subtle);
  }

  *:focus-visible {
    outline: 3px solid var(--accent);
    outline-offset: 3px;
  }
}
```

---

## 5. Yasaklar

| Yasak | Doğru |
|-------|-------|
| Font < 16px | Min 16px (1.4×) |
| 4K'da ortalamama | `max-width` + `margin: 0 auto` |
| Sidebar < 280px | Min 320px |
