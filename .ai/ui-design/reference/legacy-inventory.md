# Legacy Inventory — ui-design (Eski Referans + Mevcut Durum)

**Tarih:** 2026-09-24 · **Kaynak:** salt-okunur tarama · **Durum:** .ai copy\ui-design diskte YOK

## Eski referans (.ai copy\ui-design)

**Sonuç: KLASÖR MEVCUT DEĞİL.** Kanıt: (1) `read C:\www\coremusic.net\.ai copy` → "File not found"; (2) kök dizin listesi yalnızca `.ai\` içeriyor; (3) repo geneli grep `\.ai copy` → tek eşleşme; (4) `**/qr-*.md` glob → 0 dosya.

| Dosya yolu | Rol | Yapı taşı |
|---|---|---|
| `C:\www\coremusic.net\.ai copy\ui-design\` | Eski hızlı-referans seti (silinmiş/hiç var olmamış) | Tek kanıt: `.ai/ui-design/01-mockup-index.md:147` → "> Eski `.ai copy/ui-design/screens/qr-*.md` dosyalarından türetilmiştir." — **17 adet `qr-*.md` (max 10KB/ekran) diskte yok** |
| `assets.coremusic.net\Css copy\` | Yan etki: "copy" sadece CSS tarafında var (`03_Layout\_footer copy.css`) | ui-design ile ilgisi yok |

**Not:** Görevde flow/ prompt/ reference/ screens/ tokens/ için istenen dosya rolleri bu klasörde değil, **yalnızca `C:\www\coremusic.net\.ai\ui-design\` altında** yaşayabildi — tam envanter aşağıdaki "Mevcut" bölümünde.

## Mevcut .ai\ui-design

**Kök:** 5 klasör (flow, prompt, reference, screens, tokens) + 6 md = 11 giriş. (Görevde "6 klasör" denmişti; disk gerçeği **5**.) Toplam: **114 md + 2 json = 116 dosya.**

### Kök md dosyaları (6/6 DOLU — boş/yarı dosya yok)

| Dosya | Satır | Durum | Başlık yapısı (`##`) |
|---|---:|---|---|
| `00-device-matrix.md` | 315 | DOLU (45 tier tabloları, tespit önceliği, Quality Report) | 1. Amaç · 2. Device Tier Sistemi · 3. Tüm Tiers · 3A. Viewport Bazlı Özet Tablosu · 4. Cihaz Tespit Önceliği · 5. Quality Report |
| `01-mockup-index.md` | 183 | DOLU (19 PNG tablosu, screen spec eşleme, qr- sistemi) | 1. Amaç · 2. PNG Dizin Yapısı · 3. Mockup Kategorileri (3.1-3.3) · 4. Screen Spec Eşleştirmesi · 5. Referans Sıralaması · 6. Kullanım Protokolü · 7. Quick Reference (qr-) Sistemi · 8. Quality Report |
| `02-component-inventory.md` | 239 | DOLU (C01-C19 tek tek BEM/piksel/token tabloları) | 1. Amaç · 2. Bileşen Envanteri · 3. Quality Report |
| `03-implementation-plan.md` | 184 | DOLU (15 adım + bağımlılık grafik + 31 saat) | 1. Amaç · 2. 15 Adımlık CSS Planı · 3. Bağımlılık Grafisi · 4. Toplam Süre Tahmini · 5. Quality Report |
| `04-accessibility-gaps.md` | 210 | DOLU (12 gap + CSS düzeltme blokları) | 1. Amaç · 2. Touch Target Analizi · 3. Contrast Analizi · 4. Keyboard Navigation · 5. Screen Reader Desteği · 6. Reduced Motion · 7. Focus Management · 8. Quality Report |
| `05-responsive-architecture.md` | 198 | DOLU (token hiyerarşisi, CSS dosya ağacı, yasak örüntüler) | 1. Amaç · 2. Temel İlkeler · 3. Token Hiyerarşisi · 4. CSS Dosya Yapısı · 5. Token-First CSS Örneği · 6. Media Query Stratejisi · 7. Yasak Örüntüleri · 8. Quality Report |

**Tutarsızlık (Truth Mode):** `00-device-matrix.md` frontmatter `version: 5.0.0` ama §5 Quality Report `Version 6.0.0`; satır 131'de eski mojibake vardı (2026-09-24'te onarıldı). `05-responsive-architecture.md` §1-§8 arası — AGENTS.md:231 ve CLAUDE.md:536'nın bağladığı **§7.4 ve §12 bu dosyada YOK**.

### flow/ — 21 md (6 kategori)

| Dosya | Rol (H1) |
|---|---|
| `flow/00-flow-index.md` | Master Flow Index — 4 kategori/17 flow iddiası, ASCII box-arrow stil kuralı (§8), tier tablosu (§7) |
| `flow/auth/01-login.md` | Auth Login Flow — decision flow + ekran akışı + hata + tier varyasyon + BEM + adımlar |
| `flow/auth/02-register.md` | Register Flow (3 adımlı wizard) |
| `flow/auth/03-forgot-password.md` | Forgot Password Flow |
| `flow/auth/04-select-gender.md` | Select Gender Flow (ilk adım) |
| `flow/auth/05-logout.md` | Logout Flow |
| `flow/music/01-playback.md` | Playback Flow (STOPPED/PLAYING/PAUSED state machine) |
| `flow/music/02-playlist-queue.md` | Playlist & Queue Flow |
| `flow/music/03-album-browse.md` | Album Browse Flow |
| `flow/music/04-artist-browse.md` | Artist Browse Flow |
| `flow/music/05-search.md` | Search Flow |
| `flow/navigation/01-spa-routing.md` | SPA Routing Flow (route match → guard → DOM patch) |
| `flow/navigation/02-header-nav.md` | Header Navigation Flow |
| `flow/navigation/03-footer-player.md` | Footer Player Flow |
| `flow/settings/01-wifi-connect.md` | WiFi Connect Flow (state machine) |
| `flow/settings/02-bluetooth-connect.md` | Bluetooth Connect Flow (state machine) |
| `flow/settings/03-equalizer.md` | Equalizer Flow |
| `flow/settings/04-general.md` | General Settings Flow |
| `flow/automotive/01-android-auto-layout.md` | Android Auto Layout Flow |
| `flow/automotive/02-carplay-layout.md` | Apple CarPlay Layout Flow |
| `flow/watch/01-now-playing.md` | Apple Watch Now Playing Flow |

**Tutarsızlık:** `00-flow-index.md` "17 flow / 4 kategori" diyor; diskte **20 flow / 6 kategori** (automotive 2 + watch 1 indekste yok).

### prompt/ — 50 md (4 alt kategori)

| Dosya grubu | Adet | Rol (H1 deseni) |
|---|---:|---|
| `prompt/00-prompt-index.md` | 1 | Master Prompt Index — kategori tablosu + **§7 Prompt Formatı** (Context/Required Inputs/Prompt Template/Expected Output/Validation) |
| `prompt/page/01-home … 12-bluetooth` | 12 | Sayfa bazlı üretim promptu: `01-home, 02-albums, 03-album-detail, 04-artists, 05-playlist, 06-browse, 07-settings, 08-login, 09-register, 10-gender-select, 11-wifi, 12-bluetooth` → H1 `# NN — <Page> Page` |
| `prompt/screen/00-prompt-index.md` + `T1-phone … T10-watch` | 11 | Tier bazlı ekran promptu → H1 `T<N>: <Device> Screen Prompt` (T1 phone, T2 tablet-small, T3 tablet-large, T4 embedded, T5 laptop, T6 desktop, T7 desktop-4k, T8 tv, T9 car, T10 watch) |
| `prompt/layout/01-mobile-stack … 10-spatial-ar` | 10 | Layout pattern promptu (viewport'lu H1): mobile-stack 720×1280, tablet-grid, embedded-split 1024×600, laptop-sidebar, desktop-3col 1920×1080, 4k-expanded, tv-focus, car-touch 1280×720, watch-micro 396×484, spatial-ar |
| `prompt/component/C01-nav-link … C16-network-row` | 16 | Bileşen promptu → H1 `<Name> Component Prompt (Cxx)`: C01 nav-link, C02 status-widget, C03 user-pill, C04 primary-button, C05 secondary-button, C06 form-input, C07 gender-button, C08 social-login, C09 media-card, C10 detail-panel, C11 genre-tabs, C12 star-rating, C13 track-list, C14 modal, C15 toggle, C16 network-row |

**Tutarsızlık:** `prompt/00-prompt-index.md` diskteki adları yansıtmıyor — `C02-search-bar.md` (diskte `C02-status-widget.md`), `01-pattern-mobile-stack.md` (diskte `01-mobile-stack.md`), `page/02-library.md` (diskte `02-albums.md`), "14 page" iddiası (diskte 12), "175 prompt" iddiası (diskte 49 prompt + 1 indeks).

### reference/ — 10 md + 2 json

| Dosya | Rol (H1) |
|---|---|
| `reference/01-php-source-architecture.md` | PHP Source Architecture Reference |
| `reference/02-text-strings.md` | Text Strings (80+ Turkish UI) |
| `reference/03-icon-asset-catalog.md` | Icon Asset Catalog (50+ Icons) |
| `reference/04-verification.md` | UI Verification Protocol (tier bazlı doğrulama — WORKFLOW §4.6 kapısı) |
| `reference/05-backend-reference.md` | Backend Reference |
| `reference/06-frontend-reference.md` | Frontend Reference (ITCSS, Vanilla JS, BEM) |
| `reference/07-session-notes.md` | Session Notes |
| `reference/08-css-design-tokens.md` | CSS Design Tokens Quick Reference |
| `reference/09-interaction-states.md` | Interaction States (mouse vs touch, hover capability media query) |
| `reference/10-device-specific-guidelines.md` | Device-Specific Guidelines (10 Categories) — AGENTS.md §7.2 45-tier kapısı |
| `reference/figma_1920_main.json` | Figma 1920 ana dışa aktarımı |
| `reference/figma/raw/nodes-1024-1920.json` | Figma ham node verisi (1024 + 1920) |

### screens/ — 23 md

| Dosya | Rol (H1) |
|---|---|
| `screens/00-ascii-art-index.md` | Screen Specification Index — completed/planned tabloları, 14 H2 (ASCII Art Layout Reference, Glassmorphism, BEM Naming, Reading Protocol, File Naming Convention) |
| `screens/T01-phone-hd/home-dashboard.md` · `auth-login.md` | Phone HD 720×1280 screen spec |
| `screens/T02-phone-fhd/home-dashboard.md` | Phone FHD 1290×2796 screen spec |
| `screens/T03-phone-qhd/home-dashboard.md` | Phone QHD 1440×3120 screen spec |
| `screens/T08-embedded/` (9) | `home-dashboard, welcome-popup, albums, album-detail, artists, now-playing, file-browser, wifi-modal, bluetooth-modal` — 1024×600 RPi5 screen spec (ASCII layout + BEM + token + WCAG + glass CSS + PNG ref + responsive + state) |
| `screens/T17-monitor-22fhd/home-dashboard.md` | Desktop 1920×1080 screen spec |
| `screens/T25-tv-43fhd/home-dashboard.md` | TV FHD 1920×1080 screen spec |
| `screens/T29-car-android-auto/home-dashboard.md` | Android Auto 1280×720 screen spec |
| `screens/T31-watch-apple-40mm/now-playing.md` | Apple Watch 396×484 screen spec |
| `screens/shared/` (5) | `login, select-gender, register-step1, register-step2, register-step3` — 1024×600 auth screen spec |

### tokens/ — 4 md

| Dosya | Rol (H1) |
|---|---|
| `tokens/color-palettes.md` | Color Palettes — 3 tema (#ff4fd8/#4f9fff/#a0a0b0), semantik 4×10, gri 50-950, 15 glass renk |
| `tokens/component-tokens.md` | Component Tokens (C01-C16) |
| `tokens/design-tokens-master.md` | Master Design Tokens (CLAUDE.md guardrail #11 zorunlu okuma) |
| `tokens/platform-tokens.md` | Platform Tokens (45-Tier) |

## PNG envanteri

**`.png\AGENTS.md` YOK.** Toplam 19 PNG + 4 CLAUDE.md.

**home-1024/ (12 PNG):** `Linux  1024 - Albumler Details Detay Page.png` · `Linux  1024 - Albumler Page.png` · `Linux  1024 - Bluetooth Quick Page Base.png` · `Linux  1024 - Göz At - Tıklama Clicked.png` · `Linux  1024 - Göz At Page.png` · `Linux  1024 - Home Page Welcome Popup.png` · `Linux  1024 - Home Page.png` · `Linux  1024 - Playlist Page - Video Played.png` · `Linux  1024 - Playlist Page.png` · `Linux  1024 - Singer Page.png` · `Linux  1024 - Wifi Connect Light.png` · `Linux  1024 - Wifi Quick Page Base.png`

**home-1920/ (1 PNG):** `Linux - 1920 - Home.png`

**shared-1024/ (6 PNG):** `Linux  1024 - Login Girl.png` · `Linux  1024 - Register Girl.png` · `Linux  1024 - Register Girl step 2.png` · `Linux  1024 - Register Girl step 3.png` · `Linux  1024 - Select Gender.png` · `Linux  1024 - Select Gender - selected.png`

**CLAUDE.md özeti:**
- `.png/CLAUDE.md` — Guardrail #11 kaynaklı bağlam: 19 PNG (12+1+6), sıralama `PNG > ASCII art > Component Inventory > Tokens > Implementation Plan`, "PNG okunamıyorsa DUR". **Eski ad veriyor:** indeks `ui-design/00-mockup-index.md` (gerçek: `01-mockup-index.md`).
- `.png/home-1024/CLAUDE.md` — 12 PNG, kanonik 1024×600 referans seti; append-only protokol.
- `.png/home-1920/CLAUDE.md` — 1 PNG, desktop setinin başlangıcı.
- `.png/shared-1024/CLAUDE.md` — 6 PNG, auth ekranları SSOT; auth.coremusic.net kodu buradan doğrulanır.

## Kayıt noktaları

### `.ai/AGENTS.md` (v22.0.1)
- L117 — §13.4 istisna: `.ai/ui-design/screens/**`, `.ai/.png/**`
- L119 — §13 Mockup Before Frontend: `.ai/ui-design/` görseli okunmadan kod YAZILAMAZ
- L189 — §6 keyword routing: `ui-design, c01-c16, 45-tier, device-matrix, screen-spec, token` → UI Designer
- L229 — §7.2 `[[ui-design/01-mockup-index]]` (19 PNG) + C01-C16; sıra: PNG > ASCII > Inventory > Tokens > Reference(01-10)
- L230 — §7.2 `[[ui-design/reference/10-device-specific-guidelines]]`
- L231 — §7.2 `[[ui-design/05-responsive-architecture]] §7.4 + §12` ← **bu bölümler dosyada yok**
- L478 — §24.3 Frontend zorunlu okuma: `ui-design/01-mockup-index.md`, `02-component-inventory.md`
- L482 — §24.3 QA zorunlu okuma: `ui-design/04-accessibility-gaps.md`, `ui-design/screens/**/*.md`
- L515 — §16 UI kalite standardı: "ui-design C01-C16 uyumu"
- L557 — §25.1 Faz 4: "ui-design çekirdek + tokens + flow — Pending"

### `.ai/CLAUDE.md`
- L519 — Guardrail #11: `01-mockup-index` + `02-component-inventory` + `tokens/design-tokens-master` + `reference/01-10`
- L524 — Guardrail #17: `[[ui-design/screens/00-ascii-art-index]]` (Header 60px y:0-60, İçerik 450px, Footer 90px)
- L532-536 — Frontend okuma listesi 1-5 (01, 02, tokens/design-tokens-master, screens/00-ascii-art-index, 05-responsive-architecture §7.4/§12 ← **olmayan bölümler**)
- L560 — Vault-First: Frontend görevlerinde `[[ui-design/01-mockup-index]]` + `[[ui-design/02-component-inventory]]` ZORUNLU
- L874 — §12A UI Design → `[[ui-design/01-mockup-index]]`

### `.ai/WORKFLOW.md` (10 kayıt)
- L403 §4.5 UI Design Gate · L404 §4.6 `[[ui-design/reference/04-verification]]`
- L491 §2.5 HARD GATE (Guardrail #11): 01-mockup-index + 02-component-inventory
- L504-506 HARD GATE: reference/10-device-specific-guidelines → 01-mockup-index → tokens/design-tokens-master
- L508 reference/04-verification · L511 reference/09-interaction-states
- L757 UI Design → 01-mockup-index · L759 Responsive → 05-responsive-architecture

### `.ai/workflow*` klasörü
- **YOK.** `.ai/` altında `workflow*` eşleşmesi boş; yalnız `.ai/WORKFLOW.md` (yukarıda). Ayrıca kök `.workflows/` (8 dosya) ve `.ai/.workflows` yok.

### `.opencode/.workflows` — ui-design ile ilgili workflow **YOK**
- `vault-sync.md`, `session-init.md`, `security-audit.md` → üçü de okundu, `ui-design` geçmiyor. (session-init boot listesinde ui-design dosyası yok.)

### Diğer kayıtlar (tarama yan etkileri)
- `.ai/.agents/AGENTS.md` L99, L120, L286, L290, L302, L374, L409, L458 — alt registry; L374 "03-accessibility → 04-accessibility düzeltmesi üst görevde"
- `.ai/.agents/ui-designer.md` — domain tekel + §8.2 dizin envanteri (L81, L126, L187, L249-255, L264, L311-313, L335-339, L383, L394)
- `.ai/brain.md` L460, L481-484, L728, L1046 · `.ai/engine.md` L63, L65, L268, L391, L485, L518, L520, L536, L567-569
- `.ai/.templates/index.md` L18, L22 ("115 dosya"), L298 · `frontend/js-template.md` L46, L606, L628, L642 · `frontend/css-template.md` L16, L39, L44, L488, L522
- `assets.coremusic.net/AGENTS.md` L15, L59, L75, L76 · `assets.coremusic.net/CLAUDE.md` L14, L19 — **eski adlar**: `00-mockup-index.md`, `01-component-inventory.md`
- `home.coremusic.net/AGENTS.md` L65 · `home.coremusic.net/CLAUDE.md` L135-139, L163-167 — **diskte olmayan**: `responsive-device-mode.md`
- `assets.coremusic.net/Css copy/CLAUDE.md` L24 — **diskte olmayan**: `.ai/ui-design/tokens/CLAUDE.md`
- `.claude/CLAUDE.md` L250, L256, L264-268, L286 — tamamı **eski adlar** (00-mockup-index / 01-component-inventory / responsive-device-mode)

## Şablon kalıpları (yeni .ai/.templates için)

Eski referansta 4 tekrar eden kalıp var. 114 md'nin 72'sinde `reference_doc:` frontmatter; ~26 dosya tam iskeleti uyguluyor.

### Kalıp A — Reference/Spec/Token dokümanı (kök 6 + tokens 4 + reference 10 + indexler)

```yaml
---
reference_doc: CoreMusic UI Design System   # index dosyalarında: Freelancer Technical Documentation v1.0
title: "CoreMusic — <Başlık>"
type: matrix|index|inventory|plan|analysis|architecture|tokens|reference|spec
category: ui-design
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: active
version: X.Y.Z
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/<göreli yol>"
  source_of_truth: "<yol> · <yol>"
---
# CoreMusic — <Başlık>
**Zorunlu Bağlantılar:** [[link]] · [[link]]      # alt klasörde ../ öneki
---
## 1. Amaç
---
## 2. <İçerik (tablo/kod bloğu)>
---
## N. Quality Report
| Metrik | Değer |
| Version | X.Y.Z |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | n |
| Last Updated | YYYY-MM-DD |
---
**Authority:** Bayram Ali / Vault Steward
**Last Updated:** YYYY-MM-DD
**Mode:** Red Team · Human Mode · Truth Mode
```

### Kalıp B — Flow dosyası (`flow/<kategori>/NN-*.md`)
H1 + `## 1. Akış Diyagramı (Decision Flow)` (ASCII box-arrow) → `## 2. Ekran Akışı` → `## 3. Hata Senaryoları` (tablo: Hata/Tetikleyici/Çözüm/Max Retry) → `## 4. Tier-Bazlı Varyasyonlar` (7 tier) → `## 5. BEM Sınıfları` → `## 6. Adımlar` → Authority footer. ASCII karakter seti `flow/00-flow-index.md §8` ile sabit: `─ │ ┌ ┐ └ ┘ ┬ ┤ ├ ┴ ▼`.

### Kalıp C — Component prompt (`prompt/component/Cxx-*.md`)
Frontmatter'da `type: prompt` + `component: Cxx` (governance alanı BAZEN yok). H1 → `## AI Code Generation Prompt` → `### Context` → `### Required Inputs` → `### ASCII Reference` → `### Prompt Template` (```json: task/component/bem/states/tokens) → `### Expected Output` (```html + ```css) → `### Validation` (`- [ ]` checklist). İdari tanımda `prompt/00-prompt-index.md §7`: **Context / Required Inputs / Prompt Template / Expected Output / Validation**.

### Kalıp D — Screen spec (`screens/<tier>/*.md`)
Frontmatter + `reference.source_of_truth` → birebir PNG dosya adı. H1 `CoreMusic — <Screen> (<Tier> <viewport>)` + Zorunlu Bağlantılar → `## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)` → `## 2. BEM Sınıfları` → `## 3. Token Referansları` → `## 4. Touch Target` → `## 5. WCAG Uyumu` → `## 6. Glassmorphism Stili` → `## 7. PNG Referansı` → `## 8. Responsive Davranış` → `## 9. State Durumları` → Authority footer.
