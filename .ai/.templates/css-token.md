# CSS Token Yazma Kuralı

> **Bu şablon AI tarafından CSS yazarken ZORUNLU okunur.**
> SSOT: `assets.coremusic.net/Css/01_Abstracts/` (19 dosya — 2026-10-04 sayımı) · `.ai/.templates/frontend/css-abstracts-token-template.md`

## 1. Temel kural

1. Sabit değer → `--cm-*` (yeni token) veya mevcut token adı kullanılır; katman
   dosyasına (03-09) ham `px/hex/rem` YAZILMAZ.
2. Token tanımı **yalnız** `01_Abstracts/a-*.css` içinde, `:root` bloğunda.
3. `@media` **içinde** `var(--x)` ile değer okumak YASAK — media query'de yalnız
   `--x: <sabit değer>` **override** edilir.
4. Device override, `:root` bloğu içinde yapılır (kanıt: `d-4k-tv.css` `:root` bloğu).
5. `--cm-` öneki mevcut kodda yalnız yeni nesil token'larda (`--cm-player-*`);
   eski taban `--header-h`, `--footer-h`, `--touch-min`… korunur (yeni ad verirken
   `--cm-*` kullan, eskiyi yeniden adlandırma).

## 2. Dosya seçimi

| Değer nerede değişir | Dosya |
|---|---|
| Genel (tüm cihazlar) | `a-layout-tokens-1024.css` (BASE / fallback — tek `:root`, `@media` YOK; eski `a-layout-tokens.css` diskte YOK) |
| ≤767px (mobile) | `a-layout-tokens-mobile.css` (2026-10-03 diskte VAR — d-phone.css'ten extract) |
| 768–1023px (tablet) | `a-layout-tokens-tablet.css` (2026-10-03 diskte VAR — d-tablet.css'ten extract) |
| 1024×600 RPi5'e özel | `a-layout-tokens-1024.css` (diskte medyasız tek `:root` — `@media` YOK, 2026-10-04 doğrulandı) |
| 1920×1080 wide desktop | `a-layout-tokens-1920.css` (2026-10-03 diskte VAR) |
| 3540–3839px 4K monitor | `a-layout-tokens-3540.css` (2026-10-03 diskte VAR — d-4k-monitor.css'ten extract) |
| ≥3840px 4K TV (10ft) | `a-layout-tokens-3840.css` (2026-10-03 diskte VAR — d-4k-tv.css'ten extract) |
| Renk / tipografi / scale | `a-colors-token` · `a-fonts-token` · `a-scale-hybrid` · `a-primitive-tokens` |
| Breakpoint token'ı | `a-breakpoint-tokens.css` |
| Cihaz davranışı (davranış, token değil) | `08_Devices/d-*.css` |

> ✅ **Device token dosyası durumu (2026-10-04):** 6 dosya diskte —
> BASE `a-layout-tokens.css` **YOK** (01_Abstracts = 19 dosya), BASE rolü
> `a-layout-tokens-1024.css` + 5 bp-specific (mobile, tablet, 1920, 3540, 3840).
> Her biri disk kanıtı (mevcut d-*.css :root veya .layout--* override'larından) ile dolduruldu.
> **Wiring TAMAMLANDI (2026-10-04):** `d-phone:9` · `d-tablet:9` · `d-desktop:20` ·
> `d-laptop:20` · `d-4k-monitor:19` · `d-4k-tv:9` — BASE `-1024` ayrıca 8/8 cihaz zincirinde.
> `3540` artık VERIFICATION REQUIRED DEĞİL — değeri d-4k-monitor.css'ten taşındı.
> ⚠️ `d-4k-monitor` / `d-4k-tv` YETİM (DeviceCssMap: 4k-* → `d-4k.css`) — bkz. `css-imports.md` §3.3.

## 3. Breakpoint listesi (grep — gerçek değerler, vendor hariç)

| Değer | Kaynak (örnek dosya) |
|---|---|
| 480, 640 | `05_Pages/p-login-view.css` |
| **767** (max) / **768** (min) | `a-scale-hybrid.css`, `c-buttons.css`, `_sidebar.css` |
| 900 (max) / 901 (min) | `p-login-view.css` |
| 990 | `_header.css` |
| **1024** (max) / **1025** (min) | `_header.css`, `_footer.css`, `l-main-structural.css` |
| 1200 | `l-main-structural.css` |
| **1440** (max) / **1441** (min) | `a-design-tokens.css`, `a-scale-hybrid.css` |
| 1919 (max) / **1920** (min) | `a-design-tokens.css`, `p-albums.css` |
| 2559 / **2560** (min) / 2561 | `a-scale-hybrid.css`, `_footer.css`, `_home-components.css` |
| 3839 / **3840** (min) / 3841 | `a-scale-hybrid.css`, `c-card.css`, `_sidebar.css` |
| 3540 | **VAR** — `a-layout-tokens-3540.css:15` `@media (min-width: 3540px) and (max-width: 3839px)` (import: d-4k-monitor.css:19) |
| (576, 992, 1400) | yalnız bootstrap vendor — yerel kodda KULLANMA |

Ana bant (ana seri): `≤767 · 768–1024 · 1025–1440 · 1441–1919 · 1920–2559 · 2560–3839 · ≥3840`.

> **Cihaz-seri notu (disk, 2026-10-04):** `a-breakpoint-tokens.css:17` yorumu cihaz serisini
> `desktop 1441-2560px` (varsayılan) verir — `--bp-device-desktop: 2560px` (:32); `:31`'deki
> `--bp-device-medium: 1919px` ise medium alt bandıdır (`1441-1919px`). Yukarıdaki ana seri
> media-query bantlarıdır, cihaz-seri aralığı ayrı tanımdır — ikisi de diskteki değerlerdir,
> hiçbiri değiştirilmedi.

## 4. Cihaz import eşlemesi (SSOT: `shared/src/Device/DeviceCssMap.php`)

```
embedded→d-embedded · phone→d-phone · tablet→d-tablet · laptop→d-laptop
desktop→d-desktop · 4k-tv→d-4k · 4k-monitor→d-4k        (main)
auth: 4k-tv→d-auth-4k-tv · 4k-monitor→d-auth-4k-monitor · diğerleri d-auth-<ad>
```
Yeni device CSS → `js/devices.config.js` + `DeviceCssMap.php` **ikisi** senkron (AGENTS.md assets §4).
