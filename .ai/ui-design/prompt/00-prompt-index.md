---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic UI Design — Master Prompt Index"
type: prompt-index
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
reference:
  authority: ".ai/ui-design/prompt/00-prompt-index.md"
  source_of_truth: ".ai/ui-design/01-mockup-index.md · .ai/CLAUDE.md"
---

# CoreMusic UI Design — Master Prompt Index

**Zorunlu Bağlantılar:** [[../01-mockup-index]] · [[../02-component-inventory]] · [[../03-implementation-plan]]

---

## 1. Amaç

CoreMusic UI tasarımında AI ile kod üretimi için kullanılacak prompt şablonlarının merkezi indeksidir. Her prompt; JSON prompt formatı, gerekli girdiler ve beklenen çıktı formatını içerir.

---

## 2. Prompt Kategorileri

| # | Kategori | Prompt Sayısı | Dosya Yolu |
|---|----------|---------------|------------|
| 1 | **Screen** | 45 tier × 3 ekran = 135 | `prompt/screen/` |
| 2 | **Component** | 16 | `prompt/component/` |
| 3 | **Layout** | 10 | `prompt/layout/` |
| 4 | **Page** | 14 | `prompt/page/` |
| | **TOPLAM** | **175** | |

---

## 3. Screen Prompts (10 Tier)

Her tier; tanım, layout pattern, token overrides, code example içerir.

| # | Tier | Viewport | Dosya |
|---|------|----------|-------|
| 1 | T1-phone | max-width: 767px | `screen/T1-phone.md` |
| 2 | T2-tablet-small | 768-1023px | `screen/T2-tablet-small.md` |
| 3 | T3-tablet-large | 1024-1279px | `screen/T3-tablet-large.md` |
| 4 | T4-embedded | 1024×600 | `screen/T4-embedded.md` |
| 5 | T5-laptop | 1280-1919px | `screen/T5-laptop.md` |
| 6 | T6-desktop | 1920px (FHD) | `screen/T6-desktop.md` |
| 7 | T7-desktop-4k | 2560-3839px | `screen/T7-desktop-4k.md` |
| 8 | T8-tv | 3840px+ | `screen/T8-tv.md` |
| 9 | T9-car | Değişken | `screen/T9-car.md` |
| 10 | T10-watch | ≤400px | `screen/T10-watch.md` |

---

## 4. Component Prompts (16)

| # | Component | Dosya |
|---|-----------|-------|
| C01 | Nav Link | `component/C01-nav-link.md` |
| C02 | Search Bar | `component/C02-search-bar.md` |
| C03 | Album Card | `component/C03-album-card.md` |
| C04 | Artist Circle | `component/C04-artist-circle.md` |
| C05 | Track Row | `component/C05-track-row.md` |
| C06 | Widget Card | `component/C06-widget-card.md` |
| C07 | Player Controls | `component/C07-player-controls.md` |
| C08 | Seek Bar | `component/C08-seek-bar.md` |
| C09 | Volume Slider | `component/C09-volume-slider.md` |
| C10 | Playlist Item | `component/C10-playlist-item.md` |
| C11 | Genre Chip | `component/C11-genre-chip.md` |
| C12 | Notification Badge | `component/C12-notification-badge.md` |
| C13 | Modal Overlay | `component/C13-modal-overlay.md` |
| C14 | Toast Notification | `component/C14-toast-notification.md` |
| C15 | Loading Spinner | `component/C15-loading-spinner.md` |
| C16 | Empty State | `component/C16-empty-state.md` |

---

## 5. Layout Prompts (10)

| # | Layout | Dosya |
|---|--------|-------|
| 1 | Phone Stack | `layout/01-pattern-mobile-stack.md` |
| 2 | Tablet Grid | `layout/02-pattern-tablet-grid.md` |
| 3 | Embedded Split | `layout/03-pattern-embedded-split.md` |
| 4 | Laptop Sidebar | `layout/04-pattern-laptop-sidebar.md` |
| 5 | Desktop 3-Column | `layout/05-pattern-desktop-3col.md` |
| 6 | 4K Expanded | `layout/06-pattern-4k-expanded.md` |
| 7 | TV Focus | `layout/07-pattern-tv-focus.md` |
| 8 | Car Simplified | `layout/08-pattern-car-simplified.md` |
| 9 | Watch Micro | `layout/09-pattern-watch-micro.md` |
| 10 | Spatial AR/VR | `layout/10-pattern-spatial.md` |

---

## 6. Page Prompts (14)

| # | Page | Dosya |
|---|------|-------|
| 1 | Home | `page/01-home.md` |
| 2 | Library | `page/02-library.md` |
| 3 | Albums | `page/03-albums.md` |
| 4 | Artists | `page/04-artists.md` |
| 5 | Player | `page/05-player.md` |
| 6 | Search | `page/06-search.md` |
| 7 | Settings | `page/07-settings.md` |
| 8 | Auth Login | `page/08-auth-login.md` |
| 9 | Auth Register | `page/09-auth-register.md` |
| 10 | Auth Gender | `page/10-auth-gender.md` |
| 11 | Auth Forgot | `page/11-auth-forgot.md` |
| 12 | Profile | `page/12-profile.md` |
| 13 | Playlist Detail | `page/13-playlist-detail.md` |
| 14 | 404 | `page/14-404.md` |

---

## 7. Prompt Formatı

Her prompt aşağıdaki formatta yazılır:

```markdown
---
title: "Component X Prompt"
type: prompt
tier: embedded
component: C01
---

## AI Code Generation Prompt

### Context
[Proje bağlamı]

### Required Inputs
- token: CSS token adı
- tier: Cihaz tier'ı

### Prompt Template
[JSON prompt]

### Expected Output
[HTML + CSS çıktısı]

### Validation
[Doğrulama kuralları]
```

---

## 8. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| `00-prompt-index.md` | `01-mockup-index.md` | PNG referansları |
| `00-prompt-index.md` | `02-component-inventory.md` | Bileşen tanımları |
| `00-prompt-index.md` | `03-implementation-plan.md` | Uygulama planı |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Screen Prompts | 135 (45 tier × 3) |
| Component Prompts | 16 |
| Layout Prompts | 10 |
| Page Prompts | 14 |
| Total Prompts | 175 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
