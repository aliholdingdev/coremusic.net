---
type: reference
category: ui-design
title: "CoreMusic — Prompt Index (UI Design Templates)"
date: 2026-08-11
version: 2.0.0
---

# Prompt Index (v2.0.0)

## ⚠️ KRİTİK KURAL: Hover Davranışı

**RPi5 dokunmatik cihazlarda hover yoktur.** Tüm component promptlarındaki hover state'ler `@media (hover: hover)` sorgusuna sarılmalıdır:

```css
/* Sadece fare olan cihazlarda hover aktif */
@media (hover: hover) {
  .btn-primary:hover {
    background: var(--theme-primary-hover);
  }
}
```

Bu kural tüm component promptları için geçerlidir.

---

## Screen Prompts (4 dosya)
| # | Dosya | Platform |
|---|-------|----------|
| 1 | screen/01-1024-embedded.md | RPi5 7" touch |
| 2 | screen/02-1920-desktop.md | Desktop |
| 3 | screen/03-3840-tv.md | 4K TV |
| 4 | screen/04-mobile.md | Mobile |

## Component Prompts (16 dosya)
| # | Dosya | BEM |
|---|-------|-----|
| 1-16 | component/C01-C16-*.md | See 01-component-inventory.md |

## Layout Prompts (5 dosya)
| # | Dosya | Pattern |
|---|-------|---------|
| 1 | layout/01-pattern-standard-60-40.md | 60/40 Split |
| 2 | layout/02-pattern-split-home.md | 42/58 Split |
| 3 | layout/03-pattern-fullscreen.md | Fullscreen |
| 4 | layout/04-pattern-modal.md | Modal |
| 5 | layout/05-pattern-auth-screen.md | Auth 72/28 |

## Page Prompts (14 dosya)
| # | Dosya | Route |
|---|-------|-------|
| 1 | page/01-home.md | / |
| 2 | page/02-albums.md | /albums |
| 3 | page/03-album-detail.md | /album/:id |
| 4 | page/04-artists.md | /artists |
| 5 | page/05-playlist.md | /playlist/:id |
| 6 | page/06-video-playback.md | /playlist/:id (video) |
| 7 | page/07-browse.md | /browse |
| 8 | page/08-browse-clicked.md | /browse |
| 9 | page/09-wifi.md | overlay |
| 10 | page/10-bluetooth.md | overlay |
| 11 | page/11-login.md | /login |
| 12 | page/12-register.md | /register |
| 13 | page/13-gender-select.md | /gender-select |
| 14 | page/14-welcome-popup.md | / (ilk giriş) |
