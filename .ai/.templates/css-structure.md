# CSS Structure — 01→11 Klasör Kuralı

> **Bu şablon AI tarafından CSS yazarken ZORUNLU okunur.**
> SSOT kanıt: `assets.coremusic.net/Css/` disk listesi (2026-10-03) ·
> ilgili: `.ai/.templates/frontend/css-template.md` (detaylı iskelet).

## 1. Klasör sırası ve içerik

| # | Klasör | İçeriği | Girmez |
|---|--------|---------|--------|
| 01 | `01_Abstracts/` | SADECE token (`--*` custom property) — kural/seçici YAZILMAZ | seçici, media query içi kural (yalnız token override serbest) |
| 02 | `02_Base/` | reset, tipografi, genel yapı (`b-*`, `l-main-structural`) | component, sayfa |
| 03 | `03_Layout/` | header / footer / sidebar / grid (`_header`, `_footer`, `_sidebar`) | component içi (button, form) |
| 04 | `04_Components/` | tekrar eden bileşen (`c-*`) — Figma component karşılığı | sayfa seçicisi |
| 05 | `05_Pages/` | tek sayfaya özgü CSS (`p-*`) — PHP sayfası + partial (`_home*`, `_player`) | component (varsa 04) |
| 06 | `06_Utilities/` | tek amaçlı yardımcı sınıf (`u-*`) | component kuralı |
| 07 | `07_Vendors/` | 3. parti (bootstrap*) + `v-bootstrap-lib` | yerel kod |
| 08 | `08_Devices/` | cihaz override (`d-*`, `d-auth-*`) | token tanımı (→ 01) |
| 09 | `09_ViewModes/` | görünüm modu (`v-home/pro/studio/car`) | device breakpoint |
| 10 | `10_Helpers/` | tekrarlanabilir yardımcı desen / makro (`h-*`) | — |
| 11 | `11_OAuth/` | OAuth akışı (`oauth.css`) | login sayfası (→ 05 `p-login-view`) |

## 2. Ayrım kuralı (tek satır testi)

- **PHP sayfası** → `05_Pages/p-<sayfa>.css`
- **Figma component** (birçok yerde tekrar) → `04_Components/c-<ad>.css`
- **Sabit değer** → `01_Abstracts/a-<konu>-token(s).css`
- **Cihaz davranışı** → `08_Devices/d-<cihaz>.css`
- **Klasör kısmi import'u** → `_` önekli (`_header.css`, `_home.css`)

## 2.1 Device token split kuralı (`a-layout-tokens-{bp}.css`)

Kanonik desen: `a-layout-tokens-{mobile,tablet,1024,1920,3540,3840}.css` (01_Abstracts).

| Dosya | Durum (disk kanıtı 2026-10-03) | İçerik |
|---|---|---|
| `a-layout-tokens.css` | VAR — BASE (tek `:root`, media query YOK) | 1024 RPi5 referans default'ları + value-bound `--cm-*` sabitleri + `--footer-text-size` |
| `a-layout-tokens-mobile.css` | VAR — `@media (max-width: 767px)` (2026-10-03) | phone token override'ları (d-phone.css .layout--phone'dan extract) |
| `a-layout-tokens-tablet.css` | VAR — `@media (min-width: 768px) and (max-width: 1023px)` (2026-10-03) | tablet token override'ları (d-tablet.css .layout--tablet'ten extract) |
| `a-layout-tokens-1024.css` | VAR — `@media (min-width: 1024px) and (max-width: 1919px)` (2026-10-03 sarmalayıcı eklendi) | notes.md token bloğu (component-specific, kart, detail-panel, home split, embedded/wide, touch, spacing, font scale, glass, hover, z-index, `--cm-player-*`) |
| `a-layout-tokens-1920.css` | VAR — `@media (min-width: 1920px) and (max-width: 2559px)` (2026-10-03) | wide desktop (BASE ile 4K arası geçiş; hover açık) |
| `a-layout-tokens-3540.css` | VAR — `@media (min-width: 3540px) and (max-width: 3839px)` (2026-10-03) | 4K monitor (d-4k-monitor.css :root'tan extract) |
| `a-layout-tokens-3840.css` | VAR — `@media (min-width: 3840px)` (2026-10-03) | 4K TV / 10ft (d-4k-tv.css :root'tan extract) |

Kurallar:
1. Her dosya **yalnız** `:root` token override taşır; kural/seçici yazılmaz.
2. Yükleyen: ilgili `08_Devices/d-*.css` zinciri — `a-layout-tokens.css`'ten **SONRA** import edilir
   (kanıt: `d-4k.css:2` → `a-layout-tokens-1024.css?v=5.1.0`; grep: bu import tek isabet).
3. **Wiring:** yeni eklenen 5 dosya (mobile/tablet/1920/3540/3840) diskte hazır; ilgili
   `d-*.css` zincirine import ekleme işlemi **admin onayı + ADR** ile yapılacak (AGENTS.md §10).
4. Ayırma (split) yalnız onay + ADR ile yapılır; mevcut dosya taşıma/silme YOK (AGENTS.md §10).

## 3. Dosya adlandırma deseni (GREP — mevcut dosyalardan çıkarılmış, uydurma değil)

| Önek | Klasör | Örnek (gerçek dosya) |
|------|--------|----------------------|
| `a-` | 01_Abstracts | `a-layout-tokens.css`, `a-layout-tokens-{mobile,tablet,1024,1920,3540,3840}.css`, `a-colors-token.css`, `a-breakpoint-tokens.css`, `a-primitive-tokens.css`, `a-scale-hybrid.css` (20 dosya) |
| `b-` | 02_Base | `b-base-core.css` |
| `l-` | 02_Base | `l-main-structural.css` |
| `_` | 03/04/05 partial | `_header.css`, `_footer.css`, `_sidebar.css`, `_widget-grid.css`, `_player-info.css`, `_home*.css`, `_player.css` |
| `c-` | 04_Components | `c-buttons.css`, `c-forms.css`, `c-card.css`, `c-modal.css`, `c-toggle.css`, `c-toast.css` (12 dosya) |
| `p-` | 05_Pages | `p-albums.css`, `p-artists.css`, `p-login-view.css`, `p-settings.css` (7 dosya) |
| `u-` | 06_Utilities | `u-helpers-utility.css` |
| `v-` | 07_Vendors · 09_ViewModes | `v-bootstrap-lib.css` · `v-home.css`, `v-pro.css`, `v-studio.css`, `v-car.css` |
| `d-` | 08_Devices | `d-desktop.css`, `d-phone.css`, `d-4k.css`… + auth: `d-auth-desktop.css`, `d-auth-4k-tv.css` (7 auth) |
| `h-` | 10_Helpers | `h-motion-safe.css`, `h-focus-ring.css`, `h-skip-link.css` (2026-10-03 diskte VAR) |
| (yok) | 11_OAuth | `oauth.css` |

## 4. Bilinen sapmalar (TAŞMA YOK — raporlanır, düzeltilmez)

1. `02_Base/page-layout.css` — `p-` öneki taşır ama 02_Base'de (ad != katman).
2. `02_Base/l-main-structural.css` — `l-` (layout) öneki 03_Layout'a ait görünür.
3. `03_Layout/*`, `04_Components/_*`, `05_Pages/_*` — `_` partial, önekli ad yok.
4. `main.css` YOK (AGENTS.md envanterinde geçiyor, diskte 0 isabet) — main yüklemesi
   `DeviceRenderer::headLinks()` + `device-loader.js` ile yapılır.
5. `Css copy 2/` dizini kopya artığı — okunmaz/üretilmez.
