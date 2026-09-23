---
title: "CoreMusic — Screen Prompt T1 Phone"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T1-phone
---

# T1: Phone Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T1-phone |
| Viewport | max-width: 767px |
| Cihaz | iPhone SE → iPhone 15 Pro Max, Samsung Galaxy |
| PPI | 326-460 |
| Input | Touch-first (parmak) |
| Layout Pattern | Stack (dikey scroll) |
| Orientation | Portrait (varsayılan), Landscape (destekli) |
| Safe Area | Dynamic Island, notch, home indicator |
| CSS Media Query | `@media (max-width: 767px)` |

---

## 2. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Touch target min | 48×48px |
| Touch target rec | 56×56px |
| Touch spacing | ≥8px |
| Font scale | 1× (base 16px) |
| Min font size | 12px (badge) |
| Max font size | 22px (heading) |
| Spacing scale | xs:4 sm:8 md:12 lg:16 xl:24 2xl:32 |
| Border radius | sm:6 md:10 lg:16 xl:24 full:9999 |
| Scroll bar | Gizli (`scrollbar-width: none`) |
| Hover | Yok (touch-only) |
| Glass blur | Yok (backdrop-filter performans için devre dışı) |
| Safe area | `env(safe-area-inset-*)` |

### Renk Paleti (Dark Mode Varsayılan)

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
| Welcome Section | Full-width hero | `--space-lg: 16px` padding |
| Widget Grid | 2 sütun, 8px gap | `--grid-gap: 8px` |
| Recent Cards | Horizontal scroll row | `scroll-snap-type: x mandatory` |
| Mini Player | Bottom fixed 72px | `--footer-h: 72px` |
| Bottom Tab Bar | 5 ikon, fixed bottom | Tab.active = accent renk |

**Notlar:** Phone'da sidebar yok. Bottom tab bar ana navigasyon. Swipe gesture ile sayfa geçişi.

### 3.2 Auth Login

| Bileşen | Konum | Token |
|---------|-------|-------|
| Logo | Center top, 64px | `--font-size-2xl: 22px` |
| Form Input | Full-width, 48px yükseklik | `min-height: 48px` |
| Primary Button | Full-width, 48px | `--touch-min: 48px` |
| Social Login Buttons | Row, 48px each | `--space-md: 12px` gap |
| Footer Text | Center, muted | `--text-muted` |

**Notlar:** Mobilde klavye açılınca `padding-bottom` artmalı. `env(keyboard-inset-height)` kullanılabilir.

### 3.3 Albums / Library

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky, "Kütüphane" başlığı | `--header-h: 56px` |
| Genre Tabs | Horizontal scroll, chip format | `--radius-full` |
| Album Grid | 2 sütun, 8px gap | `grid-template-columns: repeat(2, 1fr)` |
| Album Card | Cover (1:1) + başlık + sanatçı | `--radius-md: 10px` |
| Bottom Tab | 5 ikon aktif | `--footer-h: 72px` |

### 3.4 Player (Mini → Full)

| Bileşen | Konum | Token |
|---------|-------|-------|
| Mini Player | Above tab bar, 64px | `position: fixed; bottom: 72px` |
| Full Player | Swipe up → fullscreen | `transition: transform 300ms ease` |
| Cover Art | 280×280px, centered | `--radius-lg: 16px` |
| Seek Bar | Full-width, 4px height | `--accent` renk |
| Controls | Play/Pause 64px, others 48px | `--touch-min: 48px` |
| Volume | Slider,隐藏 on phone | `display: none` (phone) |

---

## 4. Code Example

### 4.1 Phone Layout CSS

```css
@media (max-width: 767px) {
  :root {
    --header-h: 56px;
    --footer-h: 72px;
    --grid-gap: 8px;
    --touch-min: 48px;
    --safe-top: env(safe-area-inset-top, 0px);
    --safe-bottom: env(safe-area-inset-bottom, 0px);
  }

  .main-content {
    padding-top: calc(var(--header-h) + var(--safe-top));
    padding-bottom: calc(var(--footer-h) + var(--safe-bottom));
    min-height: 100dvh;
    padding-left: 12px;
    padding-right: 12px;
  }

  .home-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--grid-gap);
  }

  .scroll-row {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    gap: var(--grid-gap);
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }

  .scroll-row::-webkit-scrollbar { display: none; }
  .scroll-row > * { scroll-snap-align: start; flex-shrink: 0; }

  .bottom-tab-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--footer-h);
    padding-bottom: var(--safe-bottom);
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .tab-item {
    min-width: var(--touch-min);
    min-height: var(--touch-min);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    color: var(--text-muted);
    font-size: 11px;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
  }

  .tab-item.active {
    color: var(--accent);
  }

  .mini-player {
    position: fixed;
    bottom: var(--footer-h);
    left: 0;
    right: 0;
    height: 64px;
    padding: 0 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--bg-elevated);
    border-top: 1px solid var(--border-subtle);
    z-index: 999;
  }

  .album-card {
    border-radius: var(--radius-md);
    overflow: hidden;
    background: var(--bg-secondary);
  }

  .album-card__cover {
    aspect-ratio: 1;
    width: 100%;
    object-fit: cover;
  }

  .album-card__info {
    padding: 8px;
  }

  .album-card__title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .album-card__artist {
    font-size: 12px;
    color: var(--text-secondary);
  }

  .touch-target {
    min-width: var(--touch-min);
    min-height: var(--touch-min);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
  }

  .touch-target:active {
    transform: scale(0.95);
    transition: transform 100ms ease;
  }
}
```

### 4.2 Phone Bottom Tab HTML

```html
<nav class="bottom-tab-bar" role="tablist">
  <a href="/home" class="tab-item active" role="tab" aria-selected="true">
    <svg width="24" height="24"><use href="#icon-home"/></svg>
    <span>Ana Sayfa</span>
  </a>
  <a href="/search" class="tab-item" role="tab" aria-selected="false">
    <svg width="24" height="24"><use href="#icon-search"/></svg>
    <span>Ara</span>
  </a>
  <a href="/library" class="tab-item" role="tab" aria-selected="false">
    <svg width="24" height="24"><use href="#icon-library"/></svg>
    <span>Kütüphane</span>
  </a>
  <a href="/settings" class="tab-item" role="tab" aria-selected="false">
    <svg width="24" height="24"><use href="#icon-settings"/></svg>
    <span>Ayarlar</span>
  </a>
</nav>
```

---

## 5. Yasaklar

| Yasak | Doğru |
|-------|-------|
| `backdrop-filter: blur()` | Solid/gradient arka plan |
| `hover:` medya sorgusu | Sadece `:active` touch state |
| `cursor: pointer` (zorunlu değil) | Touch cihaz |
| Sidebar (fixed) | Bottom tab bar |
| Scroll bar görünür | `scrollbar-width: none` |
| Font < 11px | Min 11px (badge) |
| `vw/vh` header/footer | `px` + `env()` safe area |
