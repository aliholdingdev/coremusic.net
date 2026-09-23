---
title: "CoreMusic — Screen Prompt T10 Smart Watch"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T10-watch
---

# T10: Smart Watch Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T10-watch |
| Viewport | ≤400px (genellikle 198×242 — 502×410) |
| Cihaz | Apple Watch (40-49mm), Samsung Galaxy Watch |
| PPI | 326-410 |
| Input | Crown/Digital Crown + Touch |
| Layout Pattern | Micro UI |
| Orientation | Portrait (varsayılan), Square (Apple Watch) |
| Safe Area | Rounded corners (watchOS) |
| CSS Media Query | `@media (max-width: 400px)` |

---

## 2. Micro UI Kuralları

| Kural | Değer | Gerekçe |
|-------|-------|---------|
| Max visible item | 3-4 | Küçük ekran |
| Min touch target | 44×44px | Crown + touch |
| Font scale | 1× (ama круп) | Okunabilirlik |
| Animation | ≥200ms | Hızlı geçiş |
| Color | Yüksek kontrast | Dış mekan |
| Text | Minimal | Hızlı bilgi |
| Navigation | Crown scroll | Dikey kaydırma |

---

## 3. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Touch target min | 44×44px |
| Touch target rec | 48×48px |
| Touch spacing | ≥8px |
| Font scale | 1× |
| Base size | 16px |
| Min font size | 12px |
| Max font size | 20px |
| Header height | 32px (minimal) |
| Footer height | Yok |
| Grid | Tek sütun |
| Glass blur | Yok |
| Hover | YOKTUR |
| Always-on display | Destekli |
| Background | Siyah (OLED power save) |

---

## 4. Ekran Promptları

### 4.1 Now Playing

| Bileşen | Konum | Token |
|---------|-------|-------|
| Cover Art | 80×80px, center | `--radius-full` |
| Track Title | 16px bold, center | Truncate |
| Artist | 14px muted, center | Truncate |
| Controls | Row: prev, play/pause, next | 44×44px each |
| Seek | Minimal bar | `--accent` |

**Notlar:** Crown ile seek. Touch ile play/pause.

### 4.2 Library (List)

| Bileşen | Konum | Token |
|---------|-------|-------|
| List Items | 44px height, single line | `--touch-min: 44px` |
| Scroll | Crown ile dikey | `overscroll-behavior: contain` |
| Active Item | Accent background | `--accent` |
| Max Items Visible | 4-5 | Overflow scroll |

### 4.3 Controls

| Bileşen | Konum | Token |
|---------|-------|-------|
| Volume | Vertical slider | Crown ile ayarlama |
| Source | Toggle | 44×44px button |
| EQ | Quick toggle | 44×44px button |

---

## 5. Code Example

```css
@media (max-width: 400px) {
  :root {
    --touch-min: 44px;
    --grid-gap: 8px;
    --header-h: 32px;
  }

  body {
    background: #000000;
    color: #ffffff;
    font-family: 'Arima', sans-serif;
    font-size: 16px;
    line-height: 1.4;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
  }

  .main-content {
    padding: 8px;
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    gap: var(--grid-gap);
  }

  /* Now Playing */
  .watch-now-playing {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    padding: 16px 8px;
  }

  .watch-cover {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
  }

  .watch-title {
    font-size: 16px;
    font-weight: 700;
    text-align: center;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .watch-artist {
    font-size: 14px;
    color: #a0a0b0;
    text-align: center;
  }

  .watch-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-top: 8px;
  }

  .watch-btn {
    min-width: var(--touch-min);
    min-height: var(--touch-min);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #1a1a24;
    color: #ffffff;
    touch-action: manipulation;
  }

  .watch-btn--play {
    min-width: 56px;
    min-height: 56px;
    background: var(--accent);
  }

  .watch-btn:active {
    transform: scale(0.9);
    transition: transform 100ms ease;
  }

  /* Seek bar */
  .watch-seek {
    width: 100%;
    height: 4px;
    border-radius: 2px;
    background: #333;
    appearance: none;
    margin-top: 8px;
  }

  .watch-seek::-webkit-slider-thumb {
    appearance: none;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--accent);
  }

  /* Library list */
  .watch-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .watch-list-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 8px;
    min-height: var(--touch-min);
    border-radius: 12px;
    touch-action: manipulation;
  }

  .watch-list-item:active {
    background: rgba(124, 92, 255, 0.2);
  }

  .watch-list-item__icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    flex-shrink: 0;
  }

  .watch-list-item__text {
    flex: 1;
    min-width: 0;
  }

  .watch-list-item__title {
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .watch-list-item__subtitle {
    font-size: 12px;
    color: #a0a0b0;
  }

  /* Crown scroll */
  .watch-scroll {
    overflow-y: auto;
    overscroll-behavior: contain;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }

  .watch-scroll::-webkit-scrollbar { display: none; }

  /* Always-on display */
  @media (prefers-reduced-motion: reduce) {
    * { animation: none !important; transition-duration: 0ms !important; }
  }

  /* OLED power save */
  .watch-aod {
    background: #000000;
    opacity: 0.6;
  }

  .watch-aod * {
    animation: none;
  }

  /* Hover devre dışı */
  *:hover { /* boş */ }

  /* Rounded corners (watchOS) */
  .watch-container {
    border-radius: 40px;
    overflow: hidden;
  }
}
```

---

## 6. Crown Navigasyon JS

```javascript
// Digital Crown handler
function initCrownNavigation(scrollElement) {
  let lastAngle = 0;

  if ('onwheel' in window) {
    scrollElement.addEventListener('wheel', (e) => {
      e.preventDefault();
      const delta = e.deltaY;
      scrollElement.scrollTop += delta;
    }, { passive: false });
  }

  // watchOS crown via WKWebView
  if (window.webkit?.messageHandlers?.crown) {
    window.webkit.messageHandlers.crown.addEventListener('message', (msg) => {
      const rotation = msg.data.rotation;
      scrollElement.scrollTop -= rotation * 2;
    });
  }
}
```

---

## 7. Yasaklar

| Yasak | Doğru |
|-------|-------|
| 3+ sütun grid | Tek sütun |
| Kompleks layout | Micro UI |
| Fazla metin | Minimal, large font |
| Glass blur | Yok |
| Hover | Yok |
| Derin menü | Max 2 seviye |
| Animated transitions | ≥200ms, basit |
| Beyaz arka plan | Siyah (OLED) |
