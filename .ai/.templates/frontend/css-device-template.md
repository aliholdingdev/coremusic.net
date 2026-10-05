---
title: "CoreMusic — CSS Cihaz Şablonu (08_Devices)"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CSS Cihaz Şablonu — `08_Devices/`

**Kapsam:** Cihaz için **import zinciri** + davranış override · önek `d-` · layout **YAZILMAZ**
**Ana şablon:** [[css-template]] §3.5 · **Eşzamanlı:** `js/devices.config.js` + `DeviceCssMap.php`

---

## 1. Dosya Envanteri (disk kanıtı — 8 normal + 7 auth)

```
d-phone.css          ≤767px              d-auth-phone.css
d-tablet.css         768–1023px          d-auth-tablet.css
d-laptop.css         laptop              d-auth-laptop.css
d-desktop.css        1920 wide           d-auth-desktop.css
d-embedded.css       RPi5 1024×600       d-auth-embedded.css
d-4k.css             4K genel            (d-auth-4k YOKTUR — uydurulmaz)
d-4k-monitor.css     4K monitor          d-auth-4k-monitor.css
d-4k-tv.css          4K TV (10ft)        d-auth-4k-tv.css
```

---

## 2. İskelet

```css
/**
 * 08_Devices/d-{{device}}.css
 * CİHAZ : {{device}} · ARALIK: {{min}}–{{max}}px
 * GÖREV : (1) import zinciri  (2) davranış override
 * YASAK : yerleşim (grid/boyut kuralı) → 02_Base / 03_Layout
 * SENKRO: js/devices.config.js + DeviceCssMap.php (ikisi birden)
 */

/* ---- 1) IMPORT (sıra: Abstracts → Base → Layout → Components → Pages → Utilities → ViewModes) ---- */
@import url("../01_Abstracts/a-layout-tokens-{{width}}.css?v={{v}}");
@import url("../01_Abstracts/a-breakpoint-tokens.css?v={{v}}");
@import url("../02_Base/b-base-core.css?v={{v}}");
@import url("../02_Base/page-layout.css?v={{v}}");
@import url("../03_Layout/_header.css?v={{v}}");
@import url("../03_Layout/_footer.css?v={{v}}");
@import url("../04_Components/c-footer-seek.css?v={{v}}");
@import url("../04_Components/c-footer-volume.css?v={{v}}");
@import url("../05_Pages/_home.css?v={{v}}");
@import url("../06_Utilities/u-helpers-utility.css?v={{v}}");
@import url("../09_ViewModes/v-home.css?v={{v}}");

/* ⚠️ ÇİFT YÜKLEME (2026-10-04 disk kanıtı): v-*.css ayrıca DeviceRenderer::headLinks()
 * ayrı <link> ile basılır (DeviceRenderer.php:128) — iki kez yüklenir, ADR bekliyor. */
/* ---- 2) DAVRANIŞ ---- */
@media (max-width: 767px) {
  html { font-size: 14px; }

  body {
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
  }

  * { -webkit-tap-highlight-color: transparent; }

  /* isteğe bağlı gizleme — yerleşim değil davranış */
  .header-widget { display: none; }
}
```

---

## 3. Cihaz Notları

| Cihaz | Davranış |
|-------|----------|
| phone / tablet / embedded | hover **yok** (`--hover-display: none`), touch-first, thin scrollbar |
| embedded (RPi5) | `html{font-size:14px}` · `*:hover` boş · `touch-action: manipulation` |
| 4K TV (10ft) | `--touch-*` 56–72px · `html{font-size:18px}` |
| 4K monitor / 4K | ortalama (`margin-inline: auto`) + fallback zorunlu |
| desktop / laptop | tam import zinciri (tüm bileşen + sayfa + viewmode) |

---

## 4. Kurallar

| # | Kural |
|---|-------|
| 1 | **Import + davranış** — ikisi dışında bir şey yazılmaz |
| 2 | Token yazımı `01_Abstracts/a-layout-tokens-*.css`'e; burada yalnız `:root` override gerekirse |
| 3 | Yerleşim `02_Base`/`03_Layout`'ta kalır (§2 iskeletteki "yasak") |
| 4 | Bootstrap **02_Base'den ÖNCE** import edilir (reboot b-base-core'i ezerse diye bilinçli; d-4k/d-desktop/d-laptop = reboot+grid+v-bootstrap-lib, d-phone/d-tablet/d-embedded = reboot+grid, d-4k-monitor/d-4k-tv ve auth = 0 bootstrap `@import`) — `css-imports.md` §3 ile aynı cümle |
| 5 | Yeni cihaz → `devices.config.js` **+** `DeviceCssMap.php` senkronu zorunlu |
| 6 | `main.css` **yoktur** — giriş bu dosyalardır |

---

## 5. Doğrulama

- [ ] Dosya adı envanterde var (§1) — uydurma cihaz yok
- [ ] Import zinciri sırası doğru
- [ ] Layout kuralı yok
- [ ] `devices.config.js` + `DeviceCssMap.php` güncellendi
- [ ] Gerçek cihaz genişliğinde tarayıcı testi yapıldı

**Version:** 1.0.0 · **Last Updated:** 2026-10-03
