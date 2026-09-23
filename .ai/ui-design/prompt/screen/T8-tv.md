---
title: "CoreMusic — Screen Prompt T8 Smart TV"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
tier: T8-tv
---

# T8: Smart TV Screen Prompt

## 1. Tier Tanımı

| Özellik | Değer |
|---------|-------|
| Tier | T8-tv |
| Viewport | 3840px+ (4K UHD) |
| Cihaz | Samsung (Tizen), LG (webOS), Sony, Android TV |
| PPI | 40-67 (40-75" TV) |
| Input | Uzaktan kumanda (D-pad: ↑↓←→, OK, Back, Home) |
| Layout Pattern | Focus mode (10-foot UI) |
| Orientation | Landscape |
| Safe Area | %5 overscan (30px her kenar) |
| CSS Media Query | `@media (min-width: 3840px)` veya TV User-Agent |

---

## 2. 10-Foot UI Kuralları

| Kural | Değer | Gerekçe |
|-------|-------|---------|
| Min font | 24px | 3m mesafeden okunabilirlik |
| Min touch target | 60px | D-pad ile kolay seçim |
| Kontrast | ≥4.5:1 | WCAG AA |
| Animasyon | ≥300ms | Yorgunluğu önle |
| Focus göstergesi | Kalın çerçeve + glow | Uzaktan görünür |
| Metin yoğunluğu | Düşük | Hızlı tarama |
| Scroll bar | Gizli | D-pad ile kaydırma |

---

## 3. Genel Kurallar

| Kural | Değer |
|-------|-------|
| D-pad target min | 60×60px |
| D-pad target rec | 72×72px |
| D-pad spacing | ≥16px |
| Font scale | 1.6× |
| Base size | 25.6px |
| Min font size | 24px |
| Max font size | 48px |
| Header height | 90px + 30px overscan |
| Footer height | 138px + 30px overscan |
| Sidebar | 280px (browse) |
| Glass blur | `blur(4px)` (TV performansı) |
| Hover | YOKTUR |
| Overscan | 30px her kenar |
| Animasyon | ≥300ms ease-in-out |
| Parallax | YASAK |

---

## 4. Ekran Promptları

### 4.1 Home

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Fixed top, 90px + overscan | `--header-h: 90px; --overscan: 30px` |
| Content Grid | 4 sütun, large cards | `--grid-gap: 20px` |
| Footer Player | Fixed bottom, 138px + overscan | `--footer-h: 138px` |
| Focus State | 3px outline + glow | `box-shadow: 0 0 20px var(--accent-glow)` |

**Notlar:** D-pad ile navigasyon. ↑↓ satır, ←→ sütun. OK = seç. Back = geri.

### 4.2 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Album Grid | 5 sütun | `grid-template-columns: repeat(5, 1fr)` |
| Album Card | Large (300×300px) | `--radius-lg: 20px` |
| Focus | Scale 1.02 + glow | `transform: scale(1.02)` |

### 4.3 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 138px + overscan | `--footer-h: 138px` |
| Cover Art | 120×120px | `--radius-lg: 20px` |
| Controls | Play 80px, others 64px | `--dpad-min: 60px` |

---

## 5. Code Example

```css
@media (min-width: 3840px) {
  :root {
    --header-h: 90px;
    --footer-h: 138px;
    --sidebar-w: 280px;
    --grid-gap: 20px;
    --font-scale: 1.6;
    --overscan: 30px;
    --dpad-min: 60px;
    --dpad-target: 72px;
    --dpad-spacing: 16px;
    --transition-slow: 300ms ease-in-out;
  }

  body {
    padding: var(--overscan);
    overflow-x: hidden;
  }

  .main-content {
    padding-top: calc(var(--header-h) + var(--overscan));
    padding-bottom: calc(var(--footer-h) + var(--overscan));
    min-height: 100vh;
    max-width: 1800px;
    margin: 0 auto;
    padding-left: 40px;
    padding-right: 40px;
  }

  .home-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: var(--grid-gap);
  }

  .header {
    position: fixed;
    top: var(--overscan);
    left: var(--overscan);
    right: var(--overscan);
    height: var(--header-h);
    display: flex;
    align-items: center;
    padding: 0 40px;
    background: rgba(18, 18, 26, 0.60);
    border-bottom: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player {
    position: fixed;
    bottom: var(--overscan);
    left: var(--overscan);
    right: var(--overscan);
    height: var(--footer-h);
    display: flex;
    align-items: center;
    gap: 24px;
    padding: 0 40px;
    background: rgba(18, 18, 26, 0.60);
    border-top: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player__cover {
    width: 120px;
    height: 120px;
    border-radius: var(--radius-lg);
  }

  /* D-pad target */
  .dpad-target {
    min-width: var(--dpad-min);
    min-height: var(--dpad-min);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    transition: all var(--transition-slow);
  }

  .dpad-target:focus,
  .dpad-target:focus-visible {
    outline: 3px solid var(--accent);
    outline-offset: 4px;
    box-shadow: 0 0 20px var(--accent-glow);
    transform: scale(1.02);
  }

  .dpad-target:active {
    transform: scale(0.98);
    transition: transform 100ms ease;
  }

  /* Focus glow animasyonu */
  .dpad-target:focus {
    animation: focus-glow 2s ease-in-out infinite;
  }

  @keyframes focus-glow {
    0%, 100% { box-shadow: 0 0 20px var(--accent-glow); }
    50% { box-shadow: 0 0 30px var(--accent-glow); }
  }

  /* Hover devre dışı */
  *:hover { /* boş */ }

  /* Glass — düşük blur (TV performansı) */
  .glass {
    background: rgba(18, 18, 26, 0.60);
    backdrop-filter: blur(4px);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
  }

  /* Scroll bar gizli */
  * {
    scrollbar-width: none;
  }
  *::-webkit-scrollbar { display: none; }

  /* TV overscan koruması */
  .header, .footer-player {
    left: var(--overscan);
    right: var(--overscan);
  }

  /* Seçimi engelle */
  .tv-device {
    -webkit-user-select: none;
    user-select: none;
  }
}
```

### 5.1 D-pad Navigasyon JS

```javascript
function initDpadNavigation() {
  const focusable = document.querySelectorAll(
    'a, button, [tabindex]:not([tabindex="-1"]), [role="button"]'
  );
  let currentIndex = 0;

  document.addEventListener('keydown', (e) => {
    const cols = getGridColumns();
    switch (e.key) {
      case 'ArrowRight':
        e.preventDefault();
        currentIndex = Math.min(currentIndex + 1, focusable.length - 1);
        focusable[currentIndex].focus();
        break;
      case 'ArrowLeft':
        e.preventDefault();
        currentIndex = Math.max(currentIndex - 1, 0);
        focusable[currentIndex].focus();
        break;
      case 'ArrowDown':
        e.preventDefault();
        currentIndex = Math.min(currentIndex + cols, focusable.length - 1);
        focusable[currentIndex].focus();
        break;
      case 'ArrowUp':
        e.preventDefault();
        currentIndex = Math.max(currentIndex - cols, 0);
        focusable[currentIndex].focus();
        break;
      case 'Enter':
      case ' ':
        e.preventDefault();
        focusable[currentIndex].click();
        break;
      case 'Escape':
      case 'Backspace':
        e.preventDefault();
        window.history.back();
        break;
    }
  });
}
```

---

## 6. Yasaklar

| Yasak | Doğru |
|-------|-------|
| `blur(20px)` | `blur(4px)` TV performansı |
| `hover:` medya sorgusu | Sadece `focus` / `focus-visible` |
| `font-size < 24px` | Min 24px (10ft) |
| Animasyon < 300ms | Min 300ms |
| `cursor: pointer` | Gerekmez (fare yok) |
| Parallax efekti | Yasak |
| Scroll animasyonu | Yasak |
