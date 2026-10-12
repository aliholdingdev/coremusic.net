---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic UI Design — Master Prompt Index"
type: prompt-index
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.1.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/prompt/00-prompt-index.md"
  source_of_truth: ".ai/ui-design/prompt/ (disk glob, 2026-09-27) · .ai/ui-design/01-mockup-index.md"
---

# CoreMusic UI Design — Master Prompt Index

**Zorunlu Bağlantılar:** [[../01-mockup-index]] · [[../02-component-inventory]] · [[../03-implementation-plan]] · [[../05-responsive-architecture]]

---

## 1. Amaç

CoreMusic UI tasarımında AI ile kod üretimi için kullanılacak prompt dosyalarının **tek indeksidir**. Tüm tablolar 2026-09-27 disk glob'u ile doğrulanmıştır; dosya adları diskteki birebir adlardır.

> ⚠️ **Truth Mode notu:** Sürüm 1.0.0 bu dosyada "175 prompt (45 tier × 3 ekran = 135 screen + 14 page)" iddiası vardı — diskte karşılığı **yoktur** (screen promptu tier başına 1 adet, page promptu 12 adettir). Bu sürümde sayım glob ile yeniden alındı: **48 prompt + 2 indeks + 1 araştırma = 51 md**.

---

## 2. Prompt Kategorileri

| # | Kategori | Prompt Sayısı | Dosya Yolu |
|---|----------|:---:|------------|
| 1 | **Screen** | 10 | `prompt/screen/` |
| 2 | **Component** | 16 | `prompt/component/` |
| 3 | **Layout** | 10 | `prompt/layout/` |
| 4 | **Page** | 12 | `prompt/page/` |
| | **Prompt alt toplam** | **48** | |
| 5 | İndeks dosyaları | 2 | `prompt/00-prompt-index.md` · `prompt/screen/00-prompt-index.md` |
| 6 | Araştırma | 1 | `prompt/web-research.md` |
| | **TOPLAM (disk md)** | **51** | |

**Disk kanıtı (2026-09-27, glob):** `prompt/` altında 51 `.md` = 48 prompt + 2 indeks + 1 web-research. Kategori dışı klasör yoktur.

---

## 3. Screen Prompts (10 Tier)

Her tier; tanım, layout pattern, token overrides, code example içerir. Alt indeks: `screen/00-prompt-index.md` (v2.0.0).

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

> ⚠️ Eski sürüm "45 tier × 3 ekran = 135" satırı yanlıştı; gerçek: **10 screen prompt dosyası** (tier başına 1). Tier matrisi ayrı dosyadır: `00-device-matrix.md` (45 tier).

---

## 4. Component Prompts (16)

| # | Component | Dosya |
|---|-----------|-------|
| C01 | Nav Link | `component/C01-nav-link.md` |
| C02 | Status Widget | `component/C02-status-widget.md` |
| C03 | User Pill | `component/C03-user-pill.md` |
| C04 | Primary Button | `component/C04-primary-button.md` |
| C05 | Secondary Button | `component/C05-secondary-button.md` |
| C06 | Form Input | `component/C06-form-input.md` |
| C07 | Gender Button | `component/C07-gender-button.md` |
| C08 | Social Login | `component/C08-social-login.md` |
| C09 | Media Card | `component/C09-media-card.md` |
| C10 | Detail Panel | `component/C10-detail-panel.md` |
| C11 | Genre Tabs | `component/C11-genre-tabs.md` |
| C12 | Star Rating | `component/C12-star-rating.md` |
| C13 | Track List | `component/C13-track-list.md` |
| C14 | Modal | `component/C14-modal.md` |
| C15 | Toggle | `component/C15-toggle.md` |
| C16 | Network Row | `component/C16-network-row.md` |

> ⚠️ Eski sürüm adları (`C02-search-bar`, `C03-album-card`, `C07-player-controls` …) diskte **yok**; bu tablo 2026-09-27 glob'undan alınmıştır. Bileşen tanım SSOT'u: `02-component-inventory.md`.

---

## 5. Layout Prompts (10)

| # | Layout | Dosya |
|---|--------|-------|
| 1 | Mobile Stack | `layout/01-mobile-stack.md` |
| 2 | Tablet Grid | `layout/02-tablet-grid.md` |
| 3 | Embedded Split | `layout/03-embedded-split.md` |
| 4 | Laptop Sidebar | `layout/04-laptop-sidebar.md` |
| 5 | Desktop 3-Column | `layout/05-desktop-3col.md` |
| 6 | 4K Expanded | `layout/06-4k-expanded.md` |
| 7 | TV Focus | `layout/07-tv-focus.md` |
| 8 | Car Touch | `layout/08-car-touch.md` |
| 9 | Watch Micro | `layout/09-watch-micro.md` |
| 10 | Spatial AR | `layout/10-spatial-ar.md` |

> ⚠️ Eski sürümde dosya adları `01-pattern-mobile-stack.md` biçimindeydi — diskte bu adlar **yoktur**; gerçek adlar `NN-<kebab>.md` şeklindedir.

---

## 6. Page Prompts (12)

| # | Page | Dosya |
|---|------|-------|
| 1 | Home | `page/01-home.md` |
| 2 | Albums | `page/02-albums.md` |
| 3 | Album Detail | `page/03-album-detail.md` |
| 4 | Artists | `page/04-artists.md` |
| 5 | Playlist | `page/05-playlist.md` |
| 6 | Browse | `page/06-browse.md` |
| 7 | Settings | `page/07-settings.md` |
| 8 | Login | `page/08-login.md` |
| 9 | Register | `page/09-register.md` |
| 10 | Gender Select | `page/10-gender-select.md` |
| 11 | WiFi | `page/11-wifi.md` |
| 12 | Bluetooth | `page/12-bluetooth.md` |

> ⚠️ Eski sürüm 14 page (`02-library`, `05-player`, `06-search`, `11-auth-forgot`, `12-profile`, `13-playlist-detail`, `14-404`) iddia ediyordu — diskte **12 dosya** var; Library/Player/Search/404 vb. prompt dosyaları yoktur (kapsam dışı bırakılmış).

---

## 7. Prompt Formatı

Her prompt (`type: prompt` — şablon: `.ai/.templates/ui-design/prompt-template.md`, Kalıp C) aşağıdaki formatta yazılır:

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
| `00-prompt-index.md` | `02-component-inventory.md` | Bileşen tanımları (C01-C16) |
| `00-prompt-index.md` | `03-implementation-plan.md` | Uygulama planı |
| `00-prompt-index.md` | `screen/00-prompt-index.md` | Screen alt indeksi (v2.0.0) |
| `00-prompt-index.md` | `web-research.md` | Araştırma promptları (kapsam dışı) |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.1.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | 4 |
| Screen Prompts | 10 (T1-T10) |
| Component Prompts | 16 (C01-C16) |
| Layout Prompts | 10 (01-10) |
| Page Prompts | 12 (01-12) |
| Prompt Files | 48 |
| Index Files | 2 (`00-prompt-index.md`, `screen/00-prompt-index.md`) |
| Research Files | 1 (`web-research.md`) |
| Total md (disk) | 51 |
| Last Updated | 2026-09-27 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
