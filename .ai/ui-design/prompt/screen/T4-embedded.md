---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Screen Prompt T4 Embedded RPi5"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
tier: T4-embedded
---

# T4: Embedded RPi5 Screen Prompt

## AI Code Generation Prompt

### Context

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

### Required Inputs

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

### ASCII Reference

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                x:1024 │
│ y:0   ┌── HEADER (h:60) ───────────────────────────────────────────────────────────────────────────────┐  │
│       │  Navbar (16,17) w:993 h:27 · .site-header__logo + .nav-link×N [aria-current] → C01           │  │
│ y:60  └───────────────────────────────────────────────────────────────────────────────────────────────┘  │
│ y:60  ┌── CONTENT (h:450) ─────────────────────────────────────────────────────────────────────────────┐ │
│       │  ┌ (32,84) Player Info w:392 h:131 ───────┐ ┌ (627,84) Hoparlör w:173 h:51 ┐ (820,84) Hava     │ │
│       │  │ .now-playing__meta-value + C19 mini 2×2│ └──────────────────────────────┘ w:172 h:51         │ │
│       │  │ Playlist Status Div → §5 ÇELİŞKİ-2     │ ┌ (627,155) ROW2 w:365 h:40 · C17 widget ×5 ──────┐ │ │
│       │  │ y:84 ──────────────────────▶ y:215     │ │ Tarih/Saat 109 · EQ 42 · ×5 → bitiş x:992      │ │ │
│       │  └────────────────────────────────────────┘ └─────────────────────────────────────────────────┘ │ │
│       │                                      ┌ (627,215) ROW3 w:365 h:40 ─────────────────────────────┐  │
│       │                                      │ Kütüphanelerim 129×40 + .home-app-btn ×4 → C18 kanıtı  │  │
│       │                                      └─────────────────────────────────────────────────────────┘  │
│       │  ┌ (32,316.5) En Son Dinlenen w:354 h:138.5 ┐┌ (419,317) Playlistler w:353 h:138 ┐┌ (805,317)    │
│       │  │ C19 .home-mini-card ×4 (169×43) 2×2      ││ .playlist-list-card ×3 + button  ││ Sıradaki      │
│       │  └──────────────────────────────────────────┘└───────────────────────────────────┘│ title w:186   │
│       │                                                                                     │ (805,344)     │
│       │                                                                                     │ .up-next-panel│
│       │                                                                                     │ h:109 (1 mini)│
│ y:510 └─────────────────────────────────────────────────────────────────────────────────────┴─────────────┘ │
│ y:510 ┌── FOOTER (h:90) ──────────────────────────────────────────────────────────────────────────────────┐ │
│       │  .footer + .footer-player__inner/__controls/__volume · role=contentinfo aria-label="Oynatıcı"     │ │
│ y:600 └────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

> Kaynak: [[screens/T07-embedded/home-dashboard]] — L28-L52

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

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

### Validation

- [ ] Dokunma hedefleri 48px proje zeminine göre ölçülü (AA SC 2.5.8 = 24px); Toggle 48×48px, Slider 48px alan, Dropdown item 48px düzeltmeleri uygulanmış (kanıt: 04-accessibility-gaps.md L27, L39, L64-L96)
- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] Modal focus trap korunmuş (Tab döngüsü ve içerik odaklaması); Dropdown ok tuşları çalışır (kanıt: 04-accessibility-gaps.md L135-L136, L154-L160)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

### Ekran Promptları

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

### Yasaklar

| Yasak | Doğru |
|-------|-------|
| `hover:` medya sorgusu | Sadece `focus-visible` |
| `vw/vh` birimleri | `px` veya `dvh` |
| `z-index > 2000` | Max 1200 |
| Font < 14px | Min 14px |
| `touch-action: none` | `touch-action: manipulation` |
| Responsive breakpoint | Tek boyut (1024×600) |
| Welcome popup (diğer tier'lar) | Sadece embedded |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
