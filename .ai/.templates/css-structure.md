# CSS Structure — 01→11 Klasör Kuralı

> **Bu şablon AI tarafından CSS yazarken ZORUNLU okunur.**
> SSOT kanıt: `assets.coremusic.net/Css/` disk listesi (2026-10-03) ·
> version: **1.0.1** · updated: **2026-10-03** ·
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
| `a-layout-tokens.css` | **YOK — diskte yok** (git track yok; 5 cihaz dosyası bu yolu import ediyordu → kırık import, 2026-10-03 onarıldı) | kırık import hedefi — BASE rolü `a-layout-tokens-1024.css`'e geçti |
| `a-layout-tokens-1024.css` | **BASE — `:root` (medyasız tek `:root`, `@media` YOK)** (2026-10-04 doğrulandı) | RPi5 1024×600 referans default'ları + `--footer-text-size` + `--cm-player-*` bloğu — tüm cihaz dosyalarının fallback'i |
| `a-layout-tokens-mobile.css` | VAR — `@media (max-width: 767px)` (2026-10-03 dolduruldu) | phone token override'ları (d-phone.css `.layout--phone`'dan extract: split, kart, footer 72, header 56, spacing, font, grid) |
| `a-layout-tokens-tablet.css` | VAR — `@media (min-width: 768px) and (max-width: 1023px)` (2026-10-03 dolduruldu) | tablet token override'ları (d-tablet.css `.layout--tablet`'ten extract: split 45/55, kart 160, footer 95, header 62) |
| `a-layout-tokens-1920.css` | VAR — `:root` (medyasız tek `:root`; `@media` YOK) (2026-10-04 doğrulandı) | wide desktop override (header 60 · footer 90 · sidebar 280 · `--cm-player-info-*` · welcome-banner · home-song-card) |
| `a-layout-tokens-3540.css` | VAR — `@media (min-width: 3540px) and (max-width: 3839px)` (2026-10-03 dolduruldu) | 4K monitor (d-4k-monitor.css `:root`'tan extract: header 90 · footer 180 · sidebar 400 · touch 64/72/80) |
| `a-layout-tokens-3840.css` | VAR — `@media (min-width: 3840px)` (2026-10-03 dolduruldu) | 4K TV / 10ft (d-4k-tv.css `:root`'tan extract: header 80 · footer 160 · sidebar 340 · touch 56/64/72) |

Kurallar:
1. Her dosya **yalnız** `:root` token override taşır; kural/seçici yazılmaz.
2. Yükleyen: ilgili `08_Devices/d-*.css` zinciri — **base (`a-layout-tokens-1024.css`) ÖNCE, cihaz override SONRA**
   (kanıt 2026-10-04: `d-embedded.css:9` → `-1024` · `d-phone.css:9` → `-1024` + `-mobile` ·
   `d-tablet.css:9` → `-1024` + `-tablet` · `d-desktop.css:19-20` / `d-laptop.css:19-20` → `-1024` + `-1920` ·
   `d-4k.css:9` → `-1024` · `d-4k-monitor.css:18-19` → `-1024` + `-3540` · `d-4k-tv.css:9` → `-1024` + `-3840`
   ⚠️ son iki dosya YETİM: PHP/JS'te 0 referans, `4k-tv`/`4k-monitor` → `d-4k.css`'e mapli
   (DeviceCssMap.php:14-15 · devices.config.js:19-20) → `-3540`/`-3840` çalışma anında yüklenmiyor).
3. **Wiring tamamlandı (2026-10-03):** mobile · tablet · 3540 · 3840 import'ları ilgili
   `d-*.css` zincirine eklendi (kırık `a-layout-tokens.css` hedefi yerine); 1920 zaten bağlıydı.
   Yeni cihaz token dosyası ekleme → `js/devices.config.js` + `DeviceCssMap.php` eşzamanlı (AGENTS.md §10).
4. Ayırma (split) yalnız onay + ADR ile yapılır; mevcut dosya taşıma/silme YOK (AGENTS.md §10).

## 3. Dosya adlandırma deseni (GREP — mevcut dosyalardan çıkarılmış, uydurma değil)

| Önek | Klasör | Örnek (gerçek dosya) |
|------|--------|----------------------|
| `a-` | 01_Abstracts | `a-layout-tokens-{mobile,tablet,1024,1920,3540,3840}.css`, `a-colors-token.css`, `a-breakpoint-tokens.css`, `a-primitive-tokens.css`, `a-scale-hybrid.css` (`a-layout-tokens.css` **YOK** · kopya artığı **YOK** — 01_Abstracts = 19 dosya, 2026-10-04 sayımı) |
| `b-` | 02_Base | `b-base-core.css` |
| `l-` | 02_Base | `l-main-structural.css` |
| `_` | 03/04/05 partial | `_header.css`, `_footer.css`, `_sidebar.css`, `_widget-grid.css`, `_player-info.css`, `_home*.css`, `_player.css` |
| `c-` | 04_Components | `c-buttons.css`, `c-forms.css`, `c-card.css`, `c-modal.css`, `c-toggle.css`, `c-toast.css` (12 dosya) |
| `p-` | 05_Pages | `p-albums.css`, `p-artists.css`, `p-login-view.css`, `p-settings.css` (7 dosya) |
| `u-` | 06_Utilities | `u-helpers-utility.css` |
| `v-` | 07_Vendors · 09_ViewModes | `v-bootstrap-lib.css` · `v-home.css`, `v-pro.css`, `v-studio.css`, `v-car.css` |
| `d-` | 08_Devices | `d-desktop.css`, `d-phone.css`, `d-4k.css`… + auth: `d-auth-desktop.css`, `d-auth-4k-tv.css` (7 auth) |
| `h-` | 10_Helpers | `h-ellipsis.css` (yalnız bu — 2026-10-03 disk ölçümü; `h-motion-safe` / `h-focus-ring` / `h-skip-link` **diskte YOK**) |
| (yok) | 11_OAuth | `oauth.css` |

## 4. Bilinen sapmalar (TAŞMA YOK — raporlanır, düzeltilmez)

1. `02_Base/page-layout.css` — `p-` öneki taşır ama 02_Base'de (ad != katman).
2. `02_Base/l-main-structural.css` — `l-` (layout) öneki 03_Layout'a ait görünür.
3. `03_Layout/*`, `04_Components/_*`, `05_Pages/_*` — `_` partial, önekli ad yok.
4. `main.css` YOK (AGENTS.md envanterinde geçiyor, diskte 0 isabet) — main yüklemesi
   `DeviceRenderer::headLinks()` + `device-loader.js` ile yapılır.
5. `Css copy 2/` dizini — **diskte YOK** (2026-10-04: `Css/` altında yalnız 01–11 katman klasörleri); eski kopya artığı artık yok — okunmaz/üretilmez.
6. `01_Abstracts/a-layout-tokens.css` **YOK** (disk + `git ls-files` 0 isabet) — 5 cihaz dosyası
   (`d-phone`, `d-tablet`, `d-embedded`, `d-4k-monitor`, `d-4k-tv`) bu yolu import ediyordu → kırık import;
   2026-10-03 BASE `a-layout-tokens-1024.css` olarak bağlandı, zincir `base ÖNCE → cihaz override SONRA`.
7. `01_Abstracts/a-layout-tokens copy*.css` — **diskte YOK** (2026-10-04: 01_Abstracts = 19 dosya,
   `*copy*` 0 isabet). Geriye yalnız `assets.coremusic.net/Css/CONTEXT.md` içinde 6 metin
   referansı kaldı → dosya zaten yok; temizlik kullanıcı onayı bekler (kural değişikliği değil).
