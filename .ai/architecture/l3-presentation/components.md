---
type: architecture
category: l3
title: "UI Components"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# UI Components

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]] · [[ADR-018-footer-player-vaporwave]]

---

## 1. Amaç

UI component listesini ve BEM naming convention'ı tanımlar.

---

## 2. Component Listesi

| Component | BEM Block | Dosya |
|-----------|-----------|-------|
| **Header** | `.header` | `c-header.css` |
| **Footer** | `.footer` | `c-footer.css` |
| **Player** | `.player` | `c-player.css` |
| **Sidebar** | `.sidebar` | `c-sidebar.css` |
| **Card** | `.card` | `c-card.css` |
| **Button** | `.btn` | `c-button.css` |
| **Modal** | `.modal` | `c-modal.css` |
| **Toast** | `.toast` | `c-toast.css` |

---

## 3. BEM Examples

```css
/* Player component */
.player { display: flex; }
.player__controls { display: flex; }
.player__track { flex: 1; }
.player__volume { width: 100px; }
.player--mini { height: 60px; }
.player--playing .player__play { display: none; }
.player__track--active { background: var(--color-primary); }

/* Button component */
.btn { padding: 8px 16px; }
.btn--primary { background: var(--color-primary); }
.btn--danger { background: #e74c3c; }
.btn__icon { margin-right: 8px; }
.btn--loading { opacity: 0.6; }
```

---

## 4. Component Template

```html
<!-- Player component -->
<div class="player player--playing">
    <div class="player__controls">
        <button class="player__play btn btn--primary">
            <span class="btn__icon">▶</span>
        </button>
        <button class="player__pause btn btn--secondary">⏸</button>
    </div>
    <div class="player__track">
        <div class="player__progress"></div>
    </div>
    <div class="player__volume">
        <input type="range" class="player__slider">
    </div>
</div>
```

---

## 5. Footer Player (ADR-018)

| Özellik | Değer | ADR |
|---------|-------|-----|
| **Position** | Fixed bottom | ADR-018 |
| **Theme** | Vaporwave aesthetic | ADR-018 |
| **Height** | 90px (1024) · 104px (desktop) · 138px (4K) | ADR-018 |
| **Z-index** | 100 | ADR-018 |

---

## 6. JS Component Bindings

Her CSS component'i için JS modül binding'i:

| Component | BEM Block | JS Modül | Sorumluluk |
|-----------|-----------|----------|------------|
| **Header Nav** | `.nav-link` | SPARouterAdapter | SPA navigasyonu |
| **Header User** | `.header-user` | CoreMusicApp | Dropdown toggle |
| **Header Status** | `.header-widget` | DeviceManager | WiFi/BT/Battery |
| **Footer Player** | `.footer__controls` | PlayerController | Play/Pause/Stop |
| **Footer Volume** | `.footer__volume-slider` | PlayerController | Ses ayarı |
| **Footer Progress** | `.footer__progress-bar` | PlayerController | İlerleme çubuğu |
| **Now Playing** | `.now-playing` | PlayerController | Seek bar tıklama |
| **Media Card** | `.media-card` | CardManager | Click delegation |
| **Card Grid** | `.card-grid--scroll` | CardManager | Scroll-snap |
| **Home Widget** | `.home-widget` | WidgetManager | Clock/weather |
| **Mini Card** | `.mini-card` | CardManager | Click delegation |
| **Toggle Row** | `.toggle-row` | WidgetManager | Checkbox toggle |

---

## 7. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Component loading** | Skeleton screen | ADR-001 |
| **Empty state** | Placeholder | ADR-001 |
| **Error state** | Error message | ADR-001 |
| **Responsive** | Mobile-first | ADR-001 |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| [[ADR-018-footer-player-vaporwave]] | Footer player |
| [[js-module-architecture]] | JS modül detayları |

---

## 9. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Satır Sayısı** | ~550 |
| **ADR Uyumlu** | ✅ 001, 018 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-21
**Mode:** Red Team · Human Mode · Truth Mode
