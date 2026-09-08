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

# UI Components (C01–C16 Kanonik Sistem)

**Zorunlu Bağlantılar:** [[index]] · [[ui-design/00-mockup-index]] · [[ui-design/01-component-inventory]] · [[ADR-001-vanilla-js-itcss]] · [[ADR-018-footer-player-vaporwave]]

---

## 1. Amaç

CoreMusic platformunun kanonik UI bileşen mimarisini tanımlar. **Tek Doğruluk Kaynağı (SSOT) [[ui-design/01-component-inventory]] (C01–C16) belgesidir.** Tüm frontend bileşenleri, 18 PNG mockup'tan ölçülen BEM standartlarına ve ITCSS 9-layer katmanlarına tam uyumlu olmak zorundadır.

---

## 2. Kanonik Bileşen Listesi (C01–C16 — SSOT)

| ID | Bileşen Adı | BEM Sınıfı | ITCSS Katmanı | Dosya Konumu | Touch Target |
|----|-------------|------------|---------------|--------------|--------------|
| **C01** | Navigation Link | `.nav-link` | 03_Layout | `_header.css` | ⚠️ ~24×24px (min 48px hedef) |
| **C02** | Status Widget | `.header-widget` | 03_Layout | `_header.css` | ~38×24px pill |
| **C03** | User Pill | `.header-user` | 03_Layout | `_header.css` | ~85×26px pill |
| **C04** | Butonlar (Primary/Sec) | `.btn-primary`, `.btn-secondary` | 04_Components | `c-buttons.css` | 44×44px / 48×48px |
| **C05** | Icon Button | `.icon-btn`, `.play-ctrl-btn` | 04_Components | `c-buttons.css` | 44×44px / 48×48px |
| **C06** | Form Input | `.form-input` | 04_Components | `c-forms.css` | 44px input h |
| **C07** | Gender Button | `.gender-card`, `.gender-btn` | 05_Pages | `p-select-gender.css` | 140×180px kart |
| **C08** | Social Login Button | `.social-login-btn` | 05_Pages | `p-login-view.css` | 48×48px daire |
| **C09** | Media Card | `.media-card` | 04_Components | `c-cards.css` | 120×140px / 160×180px |
| **C10** | Content Panel | `.content-panel`, `.split-panel` | 03_Layout | `_home.css` | 42/58 Split (h:450px) |
| **C11** | Navigation Tab Bar | `.tab-bar`, `.sub-nav` | 04_Components | `c-navigation.css` | 48px touch target |
| **C12** | Star Rating | `.star-rating` | 04_Components | `c-rating.css` | 24×24px star |
| **C13** | Media List Item | `.media-list-item` | 04_Components | `c-lists.css` | 48px row height |
| **C14** | WiFi Quick Modal | `.wifi-modal` | 04_Components | `c-modals.css` | Pattern 4 Modal Overlay |
| **C15** | Bluetooth Modal | `.bluetooth-modal` | 04_Components | `c-modals.css` | Pattern 4 Modal Overlay |
| **C16** | Welcome Modal | `.welcome-modal` | 04_Components | `c-modals.css` | Modal (Home ilk giriş) |

> **Detaylı Ölçümler ve BEM Standartları:** Bknz: [[ui-design/01-component-inventory]]

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
| [[ui-design/00-mockup-index]] | 18 PNG Mockup İndeksi (Kanonik UI Tasarım SSOT) |
| [[ui-design/01-component-inventory]] | C01–C16 Kanonik Bileşen Envanteri |
| [[ui-design/02-implementation-plan]] | 15 Adımlık CSS Uygulama Planı |
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
