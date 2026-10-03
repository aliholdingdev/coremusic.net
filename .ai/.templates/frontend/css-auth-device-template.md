---
title: "CoreMusic — CSS Auth Cihaz Şablonu (08_Devices/d-auth-*)"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CSS Auth Cihaz Şablonu — `08_Devices/d-auth-*.css`

**Kapsam:** auth. subdomain cihaz varyantı · önek `d-auth-` · ana giriş `auth-bundled.css`
**Ana şablon:** [[css-template]] §3.5–§3.6 · **İlgili:** [[css-device-template]]

---

## 1. Gruplama (auth-bundled.css)

```
auth-bundled.css            ← auth subdomain TEK giriş
├── Grup 1  01_Abstracts    fonts · theme-config · light-glass · login-tokens
├── Grup 2  02_Base         b-base-core (reset)
├── Grup 3  05_Pages        p-select-gender · p-login-view
├── Grup 4  08_Devices      d-auth-* (cihaz davranışı — DeviceCssMap ile dinamik)
└── Grup 5  düzeltmeler     büyüyen parçalar 04_Components'e taşınır
```

**Yasak:** `07_Vendors` (Bootstrap) auth paketinde **import edilmez** — auth sayfalarında grid kullanılmıyor, reboot `b-base-core`'i ezer.

**Dosya envanteri (7 — `d-auth-4k.css` YOKTUR, uydurulmaz):**
`d-auth-phone` · `d-auth-tablet` · `d-auth-laptop` · `d-auth-desktop` · `d-auth-embedded` · `d-auth-4k-monitor` · `d-auth-4k-tv`

---

## 2. İskelet — `d-auth-{{device}}.css`

```css
/**
 * 08_Devices/d-auth-{{device}}.css
 * CİHAZ : {{device}} · SAYFA: auth (login / select-gender)
 * GÖREV : auth'a özel cihaz davranışı — ana paket çoğaltılmaz
 */

/* Auth token'ı zaten auth-bundled'da; buraya TEKRAR import edilmez.
   Sadece cihaz davranışı eklenir. */
@media (max-width: 767px) {
  .lgn-page {
    min-height: 100dvh;
    padding: var(--space-md);
  }

  .lgn-form__input {
    min-height: var(--touch-min);   /* WCAG 2.2 — 44px+ */
    font-size: 16px;                /* iOS zoom önleme */
  }
}
```

## 3. İskelet — `auth-bundled.css` grup düzeni

```css
/* CoreMusic Auth Pages Bundled CSS
 * Giriş: auth. subdomain · sıra: token → base → pages → (cihaz ayrı dosyada)
 * Bootstrap import EDİLMEZ (2026-09-29 notu — grid kullanılmıyor). */

/* 1 · 01_Abstracts — token */
@import url("./01_Abstracts/a-fonts-token.css?v={{v}}");
@import url("./01_Abstracts/a-theme-config.css?v={{v}}");
@import url("./01_Abstracts/a-light-glass-tokens.css?v={{v}}");
@import url("./01_Abstracts/a-login-tokens.css?v={{v}}");

/* 2 · 02_Base — reset */
@import url("./02_Base/b-base-core.css?v={{v}}");

/* 3 · 05_Pages — auth sayfaları */
@import url("./05_Pages/p-select-gender.css?v={{v}}");
@import url("./05_Pages/p-login-view.css?v={{v}}");

/* 4 · 08_Devices — cihaz varyantı (DeviceCssMap, dinamik) */

/* 5 · sayfa içi düzeltme (geçici — büyüyünce 04_Components'e) */
```

---

## 4. Kurallar

| # | Kural |
|---|-------|
| 1 | Auth paketi `07_Vendors` **içermez** |
| 2 | Import grubu sırası: `01 → 02 → 05 → 08` |
| 3 | Cihaz varyantı **ana pakette tekrar edilmez**; ayrı `d-auth-*.css` |
| 4 | `d-auth-4k.css` uydurulmaz |
| 5 | `body.auth-page` override `!important` gerekçesi yorumda yazılır |
| 6 | Login formu dokunma hedefi `--touch-min` · input `font-size: 16px` (iOS zoom) |

---

## 5. Doğrulama

- [ ] Envantere uygun dosya adı (7'lü liste)
- [ ] Bootstrap import yok
- [ ] Grup sırası `01 → 02 → 05 → 08`
- [ ] `auth-bundled.css` yeni import ile şişmedi
- [ ] auth. subdomain tarayıcı testi (login + gender select)

**Version:** 1.0.0 · **Last Updated:** 2026-10-03
