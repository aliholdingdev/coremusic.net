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

## 2. auth-bundled.css zinciri (içindeki @import — sıra KORUNUR)

```
/* === AUTH TOKENS === */   01_Abstracts: a-fonts-token → a-theme-config
                                            → a-light-glass-tokens → a-login-tokens
/* === AUTH BASE === */     02_Base: b-base-core
/* === AUTH PAGES === */    05_Pages: p-select-gender → p-login-view
/* === AUTH DEVICES === */  d-auth-*.css @import EDİLMEZ (ayrı <link>, çift yüklenir)
```
Bootstrap auth'da **import edilmez** (auth-bundled.css:9-10 kanıtı — grid referansı 0).

## 3. d-*.css zinciri (main cihazları — her dosya kendi import'unu taşır)

```
08_Devices/d-<cihaz>.css
 ├─ 01_Abstracts  (a-theme-config, a-colors-token, a-semantic-token, a-color-mode-tokens,
 │                 a-breakpoint-tokens, a-layout-tokens[.css|-1024.css], a-fonts-token,
 │                 a-scale-hybrid, a-primitive-tokens, a-design-tokens, a-widget-grid-tokens)
 ├─ 07_Vendors    (bootstrap — 02_Base'den ÖNCE: reboot b-base-core'i ezerse diye bilinçli)
 ├─ 02_Base       (b-base-core, page-layout)
 ├─ 03_Layout     (_header, _footer)
 ├─ 04_Components (c-footer-seek, c-footer-volume, _player-info, _widget-grid, c-home-song-btn)
 ├─ 06_Utilities  (u-helpers-utility)
 └─ 05_Pages      (_home.css)
sonrası: cihaz davranışı override'ları (:root token + davranış kuralları)
```
Not: `d-4k.css` → `a-layout-tokens-1024.css` import eder (kanıt d-4k.css:2).

### 3.1 Device token split import kuralı (`a-layout-tokens-{bp}.css`)

```
08_Devices/d-<cihaz>.css zinciri:
  … a-layout-tokens.css (BASE) → a-layout-tokens-{bp}.css (breakpoint override) …
```

| Breakpoint dosyası | Import eden zincir | Durum (2026-10-03) |
|---|---|---|
| `a-layout-tokens.css` (BASE) | tüm d-*.css (`d-phone`, `d-tablet`, `d-embedded`, `d-desktop`, `d-laptop`, `d-4k`, `d-4k-monitor`, `d-4k-tv`) | VAR |
| `a-layout-tokens-1024.css` | `d-4k.css` (grep: tek isabet, `?v=5.1.0`) | VAR — notes.md bloğu yazıldı |
| `a-layout-tokens-mobile.css` | (atanmadı) | VAR — diskte hazır, d-phone.css zincirine import **onay + ADR** ile eklenecek |
| `a-layout-tokens-tablet.css` | (atanmadı) | VAR — diskte hazır, d-tablet.css zincirine import **onay + ADR** ile eklenecek |
| `a-layout-tokens-1920.css` | (atanmadı) | VAR — diskte hazır, d-desktop.css / d-laptop.css zincirine import **onay + ADR** ile eklenecek |
| `a-layout-tokens-3540.css` | (atanmadı) | VAR — diskte hazır, d-4k-monitor.css zincirine import **onay + ADR** ile eklenecek |
| `a-layout-tokens-3840.css` | (atanmadı) | VAR — diskte hazır, d-4k-tv.css zincirine import **onay + ADR** ile eklenecek |

Kural: breakpoint token dosyası BASE'den **sonra** gelir (override yönü); dosya yalnız
`:root` token taşır; import zinciri mevcut dosyadan kopyalanır, sıra değiştirilmez (§4.1).

### 3.2 10_Helpers katmanı (`h-*.css`) — disk kanıtı (2026-10-03)

```
10_Helpers/h-motion-safe.css   — WCAG 2.3.3 (prefers-reduced-motion)
10_Helpers/h-focus-ring.css    — WCAG 2.4.7 (focus-visible halkası)
10_Helpers/h-skip-link.css     — WCAG 2.4.1 (atlama bağlantısı)
```

Wiring: helper dosyaları d-*.css zincirine eklenir (`a-layout-tokens.css` import'undan
hemen sonra). auth-bundled.css'e de eklenebilir (auth akışı için atlama bağlantısı
özellikle yararlı).

## 4. Kural özeti

1. Yeni `d-*.css` → `DeviceCssMap.php` **+** `js/devices.config.js` senkron; zincirin başına
   token/base import'larını ekleme — mevcut dosyadan kopyala, sırayı değiştirme.
2. `auth-bundled.css`'e import **ekleme/çıkarma** — yalnız grup yorumu (`/* === X === */`).
3. ViewMode `v-*.css` main akışında device'dan SONRA gelir (override yönü).
4. `_sidebar.css` bilinçli olarak zincir dışı ve IDsiz — ID'li link'lere JS dokunmaz.
5. `@import` dosyanın EN üstünde olur (import'tan sonra kural yazılır).
