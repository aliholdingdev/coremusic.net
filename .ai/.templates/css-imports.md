# CSS Import Zinciri Şeması

> **Bu şablon AI tarafından CSS yazarken ZORUNLU okunur.**
> Kanıt (SSOT): `shared/src/Device/DeviceRenderer.php:120-130` (`headLinks()`) +
> `shared/src/Device/DeviceCssMap.php` + `Css/auth-bundled.css`.

## 1. Kim hangisini basar — `DeviceRenderer::headLinks()` (PHP)

```php
// DeviceRenderer.php:120-130 (birebir kanıt)
if ($this->isAuth) {
    return $this->link('auth-bundled.css', self::LINK_ID_AUTH_BUNDLED)   // cm-auth-bundled
         . $this->link($this->deviceCssPath(), self::LINK_ID_DEVICE);    // cm-device-css
}
return $this->link($this->deviceCssPath(), self::LINK_ID_DEVICE)         // cm-device-css
     . $this->link($this->viewCssPath(), self::LINK_ID_VIEW)             // cm-view-css
     . $this->link('03_Layout/_sidebar.css', '');                        // ID'siz
```

| Akış | `<link>` sırası |
|---|---|
| **Auth** (login/register) | `01 auth-bundled.css` → `02 08_Devices/d-auth-*.css` |
| **Main** (home vb.) | `01 08_Devices/d-*.css` → `02 09_ViewModes/v-*.css` → `03 03_Layout/_sidebar.css` |

`main.css` **YOK** (diskte 0 isabet) — main paketi `d-*.css` içindeki `@import` zinciridir.

> **ÇİFT YÜKLEME (2026-10-04 disk kanıtı — ADR bekliyor):** `v-*.css` hem device zinciri
> içinden import edilir (`d-4k.css:13`, `d-desktop.css:53`, `d-laptop.css:53` → v-home/v-pro/v-studio)
> hem de `DeviceRenderer::headLinks()` ayrı `<link>` basar (`DeviceRenderer.php:128`) — iki kez yüklenir.
## 2. auth-bundled.css zinciri (içindeki @import — sıra KORUNUR)

```
/* === AUTH TOKENS === */   01_Abstracts: a-fonts-token → a-theme-config
                                            → a-light-glass-tokens → a-login-tokens
/* === AUTH BASE === */     02_Base: b-base-core
/* === AUTH PAGES === */    05_Pages: p-select-gender → p-login-view
/* === AUTH DEVICES === */  d-auth-*.css @import EDİLMEZ (ayrı <link>, çift yüklenir)
```
Bootstrap auth'da **import edilmez** (auth-bundled.css:12-13 yorumu + 0 bootstrap `@import`; grid referansı 0).

## 3. d-*.css zinciri (main cihazları — her dosya kendi import'unu taşır)

```
08_Devices/d-<cihaz>.css
 ├─ 07_Vendors    (Bootstrap **02_Base'den ÖNCE** import edilir (reboot b-base-core'i ezerse diye bilinçli; d-4k/d-desktop/d-laptop = reboot+grid+v-bootstrap-lib, d-phone/d-tablet/d-embedded = reboot+grid, d-4k-monitor/d-4k-tv ve auth = 0 bootstrap `@import`))
 │                 a-breakpoint-tokens, a-layout-tokens-1024.css (+ a-layout-tokens-{bp}.css), a-fonts-token,
 │                 a-scale-hybrid, a-primitive-tokens, a-design-tokens, a-widget-grid-tokens)
 ├─ 07_Vendors    (bootstrap-reboot + bootstrap-grid — 02_Base'den ÖNCE: reboot b-base-core'i ezerse diye bilinçli; d-4k-monitor/d-4k-tv bootstrap import ETMEZ)
 ├─ 02_Base       (b-base-core, page-layout)
 ├─ 03_Layout     (_header, _footer)
 ├─ 04_Components (c-footer-seek, c-footer-volume, _player-info, _widget-grid, c-home-song-btn)
 ├─ 06_Utilities  (u-helpers-utility)
 └─ 05_Pages      (_home.css)
sonrası: cihaz davranışı override'ları (:root token + davranış kuralları)
```
Not: `d-4k.css` → `a-layout-tokens-1024.css` import eder (kanıt d-4k.css:9).

### 3.1 Device token split import kuralı (`a-layout-tokens-{bp}.css`)

```
08_Devices/d-<cihaz>.css zinciri:
  … a-layout-tokens-1024.css (BASE) → a-layout-tokens-{bp}.css (breakpoint override) …
```

| Breakpoint dosyası | Import eden zincir (disk kanıtı 2026-10-04) | Durum |
|---|---|---|
| `a-layout-tokens.css` | — (hiçbir d-*.css'te import yok) | **YOK — diskte dosya yok** (01_Abstracts = 19 dosya; BASE rolü `-1024`'e geçti) |
| `a-layout-tokens-1024.css` | 8/8 d-*.css: `d-phone:9` · `d-tablet:9` · `d-embedded:9` · `d-desktop:19` · `d-laptop:19` · `d-4k:9` · `d-4k-monitor:18` · `d-4k-tv:9` | VAR — bağlı (BASE) |
| `a-layout-tokens-mobile.css` | `d-phone.css:9` | VAR — bağlı |
| `a-layout-tokens-tablet.css` | `d-tablet.css:9` | VAR — bağlı |
| `a-layout-tokens-1920.css` | `d-desktop.css:20` · `d-laptop.css:20` | VAR — bağlı |
| `a-layout-tokens-3540.css` | `d-4k-monitor.css:19` | VAR — zincirde bağlı, **ama d-4k-monitor.css YETİM** (§3.3) |
| `a-layout-tokens-3840.css` | `d-4k-tv.css:9` | VAR — zincirde bağlı, **ama d-4k-tv.css YETİM** (§3.3) |

Kural: breakpoint token dosyası BASE'den **sonra** gelir (override yönü); dosya yalnız
`:root` token taşır; import zinciri mevcut dosyadan kopyalanır, sıra değiştirilmez (§4.1).

### 3.2 10_Helpers katmanı (`h-*.css`) — disk kanıtı (2026-10-04)

```
10_Helpers/h-ellipsis.css      — VAR (tek dosya; 10 satır — yalnız kendi yorum bloğu, iskelet, içerik bekleniyor)
10_Helpers/h-motion-safe.css   — YOK (WCAG 2.3.3 iskeleti — css-helper-template.md §2)
10_Helpers/h-focus-ring.css    — YOK (WCAG 2.4.7 iskeleti)
10_Helpers/h-skip-link.css     — YOK (WCAG 2.4.1 iskeleti)
```

Wiring: `h-ellipsis.css` henüz hiçbir zincire import edilmedi (2026-10-04 grep `h-ellipsis`
CSS/PHP/JS = tek isabet, dosyanın kendisi). Yeni helper, `a-layout-tokens-1024.css`
import'undan hemen sonra `d-*.css` zincirine eklenir (onay + ADR).

### 3.3 YETİM cihaz dosyaları — `d-4k-monitor.css` / `d-4k-tv.css`

Gerçek (2026-10-04 grep, PHP + JS = 0 isabet): bu iki dosya **hiçbir yerde referans almıyor**.
`shared/src/Device/DeviceCssMap.php:14-15` ve `assets.coremusic.net/js/devices.config.js:19-20`
`4k-tv` / `4k-monitor` → **`08_Devices/d-4k.css`**'e mapler; dolayısıyla bu dosyaların import
ettiği `a-layout-tokens-3540.css` / `a-layout-tokens-3840.css` da **çalışma anında yüklenmiyor**.
Durum: ⚠️ VERIFICATION REQUIRED — harita mı, dosya mı düzeltilecek kullanıcı kararı (ADR bekliyor).

## 4. Kural özeti

1. Yeni `d-*.css` → `DeviceCssMap.php` **+** `js/devices.config.js` senkron; zincirin başına
   token/base import'larını ekleme — mevcut dosyadan kopyala, sırayı değiştirme.
2. `auth-bundled.css`'e import **ekleme/çıkarma** — yalnız grup yorumu (`/* === X === */`).
3. ViewMode `v-*.css` main akışında device'dan SONRA gelir (override yönü).
4. `_sidebar.css` bilinçli olarak zincir dışı ve IDsiz — ID'li link'lere JS dokunmaz.
5. `@import` dosyanın EN üstünde olur (import'tan sonra kural yazılır).
