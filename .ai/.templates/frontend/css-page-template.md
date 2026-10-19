---
title: "CoreMusic — CSS Sayfa Şablonu (05_Pages)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 2.0.0
status: active
authority: reference
---

# CSS Sayfa Şablonu — `05_Pages/`

**Kapsam:** Tek PHP sayfasına özel stiller · önek `p-` (münhasır) / `_` (çatı sayfa dosyası)
**Ana şablon:** [[css-template]] §3.1 · **Gate:** Mockup Before Frontend + Guardrail #16
**v2.0.0 sıfırdan yeniden yazım (2026-10-06) — C3 (`p-` münhasır), C4 (sayı yok).**

---

## Purpose (Amaç)

Tek bir `pages/**.php` sayfasına özgü stili tutmak. `p-` öneki **bu katmana münhasırdır** (C3) —
`02_Base` dahil hiçbir başka katman `p-` kullanamaz.

## Location (Konum)

`assets.coremusic.net/Css/05_Pages/p-{{page}}.css` · çatı: `_{{page}}.css` (alt dosya `@import` eder)
Örnek dosyalar (2026-10-06 ölçümü): `p-login-view.css` · `p-select-gender.css` · `p-settings.css` ·
`p-artists.css` · `p-albums.css` · `p-album-detail.css` · `p-playlist.css` ·
`_home.css` · `_home-layout.css` · `_home-inline.css` · `_player.css` · `_welcome.css`

## Responsibility (Sorumluluk)

- Sayfa kabı + sayfa özel blokları (BEM block = sayfa adı).
- Boş durum / sayfa durumu (`.is-empty`).

## Allowed (İzinli)

- BEM seçiciler (sayfa adı = block); `p-` dosya/sayfa kabı seçicisi.
- `var(--token)` tüketimi; `@media` = token uyarlaması.
- Çatı dosyanın kendi iç `@import`'i (`_home.css` → `_home-layout.css`).

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | **≥2 sayfada tekrar eden parça** (→ `04_Components`) | §3.1 ayrım · §4.1 #8 |
| 2 | Token tanımı (→ `01_Abstracts`) | §4.1 #9 (C6) |
| 3 | Header/footer/sidebar düzeni (→ `03_Layout`) | §3.1 |
| 4 | Ham hex/px | §4.1 #1 |
| 5 | `!important` (cap 3, gerekçeli) | §4.1 #2 |
| 6 | `p-` önekinin bu katman dışına taşınması (ve burada `c-` dosyası) | C3 · §4.1 #8 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `01_Abstracts/*` | token kaynağı (`a-login-tokens.css` ↔ `p-login-view.css` gibi) |
| `04_Components` | sayfa, bileşeni **tüketir** |
| `auth-bundled.css` | auth sayfaları (`p-login-view`, `p-select-gender`) Grup 3'te |
| `DeviceCssMap.php` + PHP docblock | dosya adı değişirse senkron |

## Import Rules (Import Kuralları)

- İçe import: yalnız çatı dosyalar (`_home.css` → `_home-layout.css`, `_home-inline.css`) `?v=` ile.
- Dışa: cihaz zincirinde `04`'ten sonra:
  `@import url("../05_Pages/p-{{page}}.css?v={{v}}");`
- `auth-bundled.css` Grup 3 = auth sayfaları (`01 → 02 → 05 → 08` sırası).

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `p-{{page}}.css` · çatı `_{{page}}.css` |
| BEM block | sayfa adı (`.{{page}}__header`) |
| Dosya ↔ sayfa | `p-login-view.css` → `pages/auth/login-view.php` (eşleme kanıtlanır, uydurulmaz) |

## Device Rules (Cihaz Kuralları)

- Cihaz davranışı bu dosyada DEĞİL → `08_Devices` / `d-auth-*`.
- `p-login-view` gibi dosyalar auth cihaz varyantları (`d-auth-*`) tarafından davranış görür.

## Responsive Rules (Responsive)

- `@media` yalnız token uyarlaması (padding/gap); breakpoint değeri
  `a-breakpoint-tokens.css` ile eşleşmeli.
- Sayfa grid kolonu `var(--grid-cols)` gibi token ile; sabit kolon sayısını cihaz token'ı yönetir.

## Token Rules (Token Kuralları)

- Tüketim: `var(--content-padding)`, `var(--section-gap)`, `var(--grid-gap)`, `var(--text-*)`.
- Yeni token tanımı yasak (C6) → `01_Abstracts`.
- ⚠️ Bilinen disk ihlali: `_home-layout.css` L367-424 `--touch-min` **tanımlıyor** (05'te token üretimi
  + 44px) → rapor §R.

## Validation (Doğrulama)

- [ ] Dosya tek PHP sayfasına karşılık (eşleme kanıtlı)
- [ ] Tekrar eden parça / token var mı → taşı (`04` / `01`)
- [ ] BEM block = sayfa adı; `p-` yalnız bu katmanda
- [ ] Ham değer yok · `!important` ≤3 gerekçeli
- [ ] Dosya adı değiştiyse `@import` + docblock + `DeviceCssMap` senkron
- [ ] Gerçek sayfada tarayıcı testi yapıldı

## Example Structure (Örnek Yapı)

```css
/**
 * 05_Pages/p-{{page}}.css
 * SAYFA : {{php-path}}
 * MOCKUP: {{png-path}}
 * NOT   : tekrar eden parça → 04_Components · token → 01_Abstracts (C6)
 */

.p-{{page}} {
  display: flex;
  flex-direction: column;
  gap: var(--section-gap);
  padding: var(--content-padding);
  min-height: var(--content-h);
}

.{{page}}__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-md);
}

.{{page}}__title {
  margin: 0;
  font-size: var(--text-xl);
  color: var(--text-primary);
}

.{{page}}__grid {
  display: grid;
  grid-template-columns: repeat(var(--grid-cols, 3), minmax(0, 1fr));
  gap: var(--grid-gap);
}

.{{page}}__grid--wide { --grid-cols: 4; }

.{{page}}.is-empty .{{page}}__grid { display: none; }

/* dar ekran — yalnız token uyarlaması (breakpoint token ile eşleşir) */
@media (max-width: 767px) {
  .p-{{page}} { padding: var(--space-sm); gap: var(--space-md); }
}
```

---

**Template Version:** 2.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R):** `_home-layout.css` L367/386/405/424 → token tanımı (`--touch-min`) + 44px —
C6 + C2 ihlali (dokunulmadı).
