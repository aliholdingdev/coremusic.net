---
title: "CoreMusic — CSS Auth Cihaz Şablonu (08_Devices/d-auth-*)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 2.0.0
status: active
authority: reference
---

# CSS Auth Cihaz Şablonu — `08_Devices/d-auth-*.css`

**Kapsam:** auth. subdomain cihaz varyantı · önek `d-auth-` · ana giriş `auth-bundled.css`
**Ana şablon:** [[css-template]] §3.5–§3.6 · **İlgili:** [[css-device-template]] · [[css-page-template]]
**v2.0.0 sıfırdan yeniden yazım (2026-10-06) — C2 (48px), C4 (sayı yok), C5 (`!important` 0), C6 (yeni token adı yasak).**

---

## Purpose (Amaç)

auth. subdomain için **cihaz davranışı** yazmak; ana paket çoğaltılmaz. `auth-bundled.css` zaten
token → base → pages zincirini kurar; bu dosya yalnız o cihazın auth'a özgü uyarlamasını ekler.

## Location (Konum)

`assets.coremusic.net/Css/08_Devices/d-auth-{{device}}.css` · giriş: kök `auth-bundled.css`
Örnek dosyalar (2026-10-06 ölçümü — envanter iddiası değil; sayı/envanter yalnız `Css/CONTEXT.md §3.1`'de — C4):
`d-auth-phone.css` · `d-auth-tablet.css` · `d-auth-laptop.css` · `d-auth-desktop.css` ·
`d-auth-embedded.css` · `d-auth-4k-monitor.css` · `d-auth-4k-tv.css`

**YOK — uydurulmaz:** `d-auth-4k.css` (yalnız `-4k-monitor` / `-4k-tv`) · `main.css`.

## Responsibility (Sorumluluk)

- Auth sayfalarına (login, select-gender) cihaz davranışı: `100dvh`, padding daraltma, input
  dokunma hedefi, iOS zoom önleme.
- `auth-bundled.css` grup düzeninin korunması (aşağıda §Import Rules).
- Auth'a özel token değişikliği **değil** (token `01_Abstracts/a-login-tokens.css`'te — C6).

## Allowed (İzinli)

- `@media` içinde cihaz davranışı (davranış kuralları; yerleşim değil).
- Mevcut token'ın `:root` **değer** override'ı (ör. `--touch-min`) — yeni ad DEĞİL (C6).
- `var(--token)` tüketimi (`--space-*`, `--touch-min`, `--lgn-*` gibi — adlar diskte tanımlı olmalı).
- `auth-bundled.css`'e grup sırasına uygun `@import` ekleme.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | `07_Vendors` (Bootstrap) import'u — auth paketinde yasak | `css-template` §3.6 · `Css/CLAUDE.md` §4.1 #10 |
| 2 | Yeni token adı (auth token'ı `01_Abstracts`'te tanımlanır) | §4.1 #9 (C6) |
| 3 | Yerleşim kuralı (grid/kolon) | §4.1 #5 |
| 4 | `!important` (cap 3 — bu iskelette 0; gerekçesiz kalan her istisna revert) | §4.1 #2 · C5 |
| 5 | `d-auth-4k.css` / `main.css` uydurma | § YOK listesi |
| 6 | Dosya sayısı/envanter iddiası ("7 dosya" vb.) | C4 |
| 7 | Ham `44px` dokunma hedefi | C2 (≥48px taban) |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `auth-bundled.css` | auth tek giriş — gruplar: `01 → 02 → 05 → 08` |
| `01_Abstracts/a-login-tokens.css` | auth token kaynağı (bu dosya token TANIMLAMAZ) |
| `shared/src/Device/DeviceCssMap.php` | cihaz varyantı eşlemesi — dosya adı değişirse senkron |
| `js/devices.config.js` | ana domain senkronu (auth varyantı için gerekliliği ⚠️ VERIFICATION REQUIRED) |
| `04_Components` | büyüyen sayfa düzeltmesi buraya taşınır (Grup 5 geçici) |

## Import Rules (Import Kuralları)

**Grup sırası (değiştirilemez):** `01_Abstracts → 02_Base → 05_Pages → 08_Devices`.
`07_Vendors` **yok** (grid kullanılmıyor; reboot `b-base-core`'i ezer — `css-template` §3.6).

```css
/* CoreMusic Auth Pages Bundled CSS — auth. subdomain TEK giriş
 * sıra: token → base → pages → (cihaz ayrı d-auth-*.css, DeviceCssMap ile dinamik)
 * Bootstrap import EDİLMEZ. */

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

/* 5 · sayfa içi düzeltme (geçici — büyüyünce 04_Components'e taşınır) */
```

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `d-auth-{{device}}.css` — `{{device}}` ana domaindeki ad ile aynı (`4k-monitor`, `phone` …) |
| BEM | `.lgn-*`, `.oauth-platform-card` gibi mevcut blok adları korunur |
| Token | `--{kategori}-{ad}` — `01_Abstracts`'te tanımlı (yeni ad yasak) |

## Device Rules (Cihaz Kuralları)

1. Bu dosya **yalnız cihaz davranışı** yazar; ana pakette davranış tekrar edilmez.
2. Cihaz token'ı override gerekirse `01_Abstracts/a-layout-tokens-{width}.css` (BASE `-1024` — C1);
   buradaki override yalnız mevcut adın değeri (C6).
3. Cihaz davranışı ana pakete kopyalanırsa → revert + bu dosyaya taşı.
4. `d-auth-4k-monitor` / `d-auth-4k-tv` 4K davranışı ayrı yazılır (tek `d-auth-4k` YOK).

## Responsive Rules (Responsive)

- Breakpoint genişlikleri `a-breakpoint-tokens.css` ile eşleşmeli; `@media` içinde `var()` okunamaz.
- Auth sayfaları dar ekranda tek kolon akar; kolon/düzen kuralı `05_Pages`/`02_Base`'de.

## Token Rules (Token Kuralları)

1. Token tanımı yalnız `01_Abstracts/` (C6); auth token'ı `a-login-tokens.css`.
2. Dokunma hedefi `var(--touch-min)` — taban **48px** (C2; `Css/AGENTS.md` §3.1 ≥48px).
3. Input `font-size`: iOS zoom önlemi ≥16px gerektirir — değerin token'da karşılığı
   `[VERIFY REQUIRED]` (token dosyası okunmadan sabit `16px` yazılmaz; §4.1 #1).
4. Figma/mockup'ta olmayan değer uydurulmaz → `⚠️ VERIFICATION REQUIRED`.

## Validation (Doğrulama)

- [ ] Dosya adı disk örneği ile (`d-auth-` + envanter adı) — `d-auth-4k.css` yok
- [ ] Bootstrap/`07_Vendors` import'u yok
- [ ] Grup sırası `01 → 02 → 05 → 08` · `?v={{v}}` tek değer
- [ ] Yeni token adı yok (C6) · `!important` 0 (C5) · ham `44px` yok (C2)
- [ ] `auth-bundled.css` yeni import ile şişmedi
- [ ] auth. subdomain tarayıcı testi (login + gender select, ≥2 cihaz genişliği)

## Example Structure (Örnek Yapı)

```css
/**
 * 08_Devices/d-auth-{{device}}.css
 * CİHAZ : {{device}} · SAYFA: auth (login / select-gender)
 * GÖREV : auth'a özel cihaz DAVRANIŞI — ana paket çoğaltılmaz, token tanımlanmaz (C6)
 * YASAK : 07_Vendors · yerleşim · yeni token adı · !important (C5: 0)
 */

/* Auth token'ı auth-bundled'da (01_Abstracts); buraya TEKRAR import edilmez. */
@media (max-width: 767px) {
  .lgn-page {
    min-height: 100dvh;
    padding: var(--space-md);
  }

  .lgn-form__input {
    min-height: var(--touch-min);   /* C2 — taban 48px */
    font-size: var(--text-input);   /* iOS zoom önlemi ≥16px — token değeri [VERIFY REQUIRED] */
  }

  /* mevcut token değeri override (C6 — yeni ad yazılmaz) */
  :root { --touch-min: 48px; }
}
```

---

**Template Version:** 2.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R — dokunulmayan ikincil düzeltmeler):**

| Hedef | Sorun |
|-------|-------|
| `08_Devices/d-auth-*.css` `:root` | `--lgn-panel-w` / `--lgn-font-size` tanımlanıyor — `01_Abstracts`'te karşılığı yok (C6 ihlali) |
| `Css/CLAUDE.md` §4.1 #2 | auth device minified dosyalarında satır içi çoklu `!important` → kesin sayı **UNKNOWN** (raporlanan 47+ satır ihlalin parçası) |
| `assets.coremusic.net/AGENTS.md` §2 | "04_Components 13 dosya" ↔ sütunda 15 ad yazılı (çelişki) · "01 20 token dosyası" ↔ disk 19 |
