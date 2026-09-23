---
title: "CoreMusic — Screen Prompt T4 Embedded RPi5"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T4-embedded
---

# T4: Embedded RPi5 Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T4-embedded |
| Viewport | 1024×600 (sabit) |
| Cihaz | Raspberry Pi 5 + 7" dokunmatik LCD |
| OS | Debian Bookworm / Linux ARM64 |
| PPI | 163 |
| Input | Kapasitif dokunmatik (5 nokta) |
| Tarayıcı | Chromium (kiosk mode) |
| Layout Pattern | Split 42/58 |
| Safe Area | 0px |
| CSS Media Query | `@media (max-width: 1024px) and (max-height: 600px)` veya sabit class |

---

## 2. Genel Kurallar

| Kural | Değer |
|-------|-------|
| Touch target min | 48×48px |
| Touch target rec | 56×56px |
| Touch spacing | ≥8px |
| Font scale | 1× (base 16px) |
| Min font size | 14px |
| Max font size | 24px |
| Header height | 60px sabit |
| Footer height | 90px sabit |
| Sidebar | 167px (sadece browse) |
| Grid max columns | 3 |
| Grid min width | 280px |
| Glass blur | `blur(20px) saturate(180%)` |
| Hover | YOKTUR |
| Welcome popup | SADECE bu tier'da |

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
  --border-subtle: rgba(255,255,255,0.08);
  --border-default: rgba(255,255,255,0.12);
  --header-height: 60px;
  --footer-height: 90px;
  --sidebar-width: 167px;
}
```

---

## 3. Ekran Promptları

### 3.1 Home

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky top 60px | `--header-h: 60px` |
| Split Layout | 42% sol / 58% sağ | `grid-template-columns: 42% 58%` |
| Widget Grid | 2×2 grid, sağ taraf | `--grid-gap: 12px` |
| Footer Player | Fixed bottom 90px | `--footer-h: 90px` |
| Welcome Popup | Centered modal | Sadece embedded |

**Notlar:** 1024×600 tek boyutlu platform. Responsive breakpoint yok.

### 3.2 Auth Login

| Bileşen | Konum | Token |
|---------|-------|-------|
| Logo | Center top, 64px | `--font-size-2xl: 24px` |
| Form | Max-width 360px, centered | `margin: 0 auto` |
| Input | Full-width, 48px | `min-height: 48px` |
| Button | Full-width, 48px | `--touch-min: 48px` |

### 3.3 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky, 60px | `--header-h: 60px` |
| Sidebar | 167px (browse) | `--sidebar-w: 167px` |
| Album Grid | 3 sütun | `grid-template-columns: repeat(3, 1fr)` |

### 3.4 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 90px | `--footer-h: 90px` |
| Cover Art | 64×64px | `--radius-md: 10px` |
| Seek Bar | Full-width, 4px | `--accent` |
| Controls | Play 56px, others 48px | `--touch-min: 48px` |
| Volume | Slider | `display: block` |

---

## 4. Code Example

```css
.embedded-device {
  --header-h: 60px;
  --footer-h: 90px;
  --sidebar-w: 167px;
  --grid-gap: 12px;
  --touch-min: 48px;
}

.embedded-device .main-content {
  padding-top: var(--header-h);
  padding-bottom: var(--footer-h);
  min-height: 100dvh;
}

.embedded-device .home-layout {
  display: grid;
  grid-template-columns: 42% 58%;
  gap: var(--grid-gap);
  padding: 0 16px;
}

.embedded-device .widget-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: var(--grid-gap);
}

.embedded-device .browse-layout {
  display: grid;
  grid-template-columns: var(--sidebar-w) 1fr;
  gap: var(--grid-gap);
}

.embedded-device .sidebar {
  position: fixed;
  left: 0;
  top: var(--header-h);
  bottom: var(--footer-h);
  width: var(--sidebar-w);
  overflow-y: auto;
  padding: 12px;
  border-right: 1px solid var(--border-subtle);
  scrollbar-width: thin;
}

.embedded-device .header {
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

.embedded-device .footer-player {
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

.embedded-device .glass {
  background: var(--bg-glass);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
}

.embedded-device *:hover {
  /* Boş — hover yok */
}

.embedded-device *:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 2px;
  border-radius: var(--radius-sm);
  animation: focus-pulse 1.5s ease-in-out infinite;
}

@keyframes focus-pulse {
  0%, 100% { outline-color: var(--accent); }
  50% { outline-color: var(--accent-hover); }
}

.embedded-device .touch-target {
  min-width: var(--touch-min);
  min-height: var(--touch-min);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
}

.embedded-device .touch-target:active {
  transform: scale(0.97);
  transition: transform 100ms ease;
}

/* Welcome Popup — SADECE embedded */
.embedded-device .welcome-popup {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1200;
}

.embedded-device .welcome-popup__card {
  background: var(--bg-elevated);
  border-radius: var(--radius-xl);
  padding: 32px;
  max-width: 400px;
  text-align: center;
}
```

---

## 5. Yasaklar

| Yasak | Doğru |
|-------|-------|
| `hover:` medya sorgusu | Sadece `focus-visible` |
| `vw/vh` birimleri | `px` veya `dvh` |
| `z-index > 2000` | Max 1200 |
| Font < 14px | Min 14px |
| `touch-action: none` | `touch-action: manipulation` |
| Responsive breakpoint | Tek boyut (1024×600) |
| Welcome popup (diğer tier'lar) | Sadece embedded |
