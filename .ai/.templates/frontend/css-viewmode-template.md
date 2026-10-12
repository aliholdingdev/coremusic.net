---
title: "CoreMusic — CSS Görünüm Modu Şablonu (09_ViewModes)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 1.0.0
status: active
authority: reference
---

# CSS Görünüm Modu Şablonu — `09_ViewModes/`

**Kapsam:** Görünüm modu (home / pro / studio / car) override'ı · önek `v-`
**Ana şablon:** [[css-template]] §3.1/§3.5 · **Gate:** Mockup Before Frontend + Guardrail #16
**YENİ DOSYA (2026-10-06) — önceki oturumda raporlanıp diske düşmemişti.**

---

## Purpose (Amaç)

Seçili görünüm modunun (theme/mode) token davranışı override'ını tek dosyada tutmak. Mod, PHP
tarafından eşlenir (`ViewModeManager`) ve CSS'e `v-{{mode}}.css` olarak yansıtılır; mod için **yerleşim
yeniden yazılmaz**, değer/override değişir.

## Location (Konum)

`assets.coremusic.net/Css/09_ViewModes/v-{{mode}}.css`
Örnek dosyalar (2026-10-06 ölçümü — envanter iddiası değil; sayı/envanter yalnız `Css/CONTEXT.md §3.1`'de — C4):
`v-home.css` · `v-pro.css` · `v-studio.css` · `v-car.css`

**YOK — uydurulmaz:** bu 4 mod dışındaki `v-*` adları (eşleme PHP'de kanıtlanmadan üretilmez).

## Responsibility (Sorumluluk)

- Mod bazlı **override**: `[data-view-mode]`/mod seçicisiyle token değeri veya kural override'ı.
- Mod ekleme/değişikliğinde PHP eşlemesinin senkronu (aşağıda Dependencies).

## Allowed (İzinli)

- `var(--token)` tüketimi; mod override'ı `:root`/mod seçicisi altında **mevcut** token adıyla değer (C6).
- `@media` içinde yalnız token uyarlaması.
- Cihaz zincirinde `06`'dan sonra, `10_Helpers`'tan önce import.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Yeni token adı (mod override mevcut adla yapılır) | `Css/CLAUDE.md` §4.1 #9 (C6) |
| 2 | Yerleşim kuralı (grid/kolon düzeni) | §4.1 #5 → `02_Base`/`03_Layout` |
| 3 | Ham hex/px | §4.1 #1 |
| 4 | `!important` (cap 3 — bu iskelette 0) | §4.1 #2 · C5 |
| 5 | PHP eşlemesiz yeni mod dosyası | `ViewModeManager`/`DeviceCssMap` senkronu |
| 6 | Dosya sayısı/envanter iddiası | C4 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Kanıt / Not |
|------------|-------------|
| `shared/src/ViewMode/ViewModeManager.php` L16-19 | `'home'→v-home.css, 'pro'→v-pro.css, 'studio'→v-studio.css, 'car'→v-car.css` (disk kanıtı) |
| `shared/src/Device/DeviceCssMap.php` L29-32 | aynı 4 eşleme (ikinci senkron noktası) |
| `shared/src/Device/DeviceRenderer.php` L120-128 | `headLinks()` mod CSS'i ayrıca `<link>` basar → **çift yükleme (import zinciri + link); ADR bekliyor — ⚠️ VERIFICATION REQUIRED** |
| `01_Abstracts/*` | token kaynağı (override mevcut adlarla) |
| `.ai/ui-design/` | mod mockup'ı — PNG okunmadan CSS yazılmaz |

## Import Rules (Import Kuralları)

- Bu dosya **import etmez**; cihaz zincirinde `06_Utilities`'ten sonra:
  `@import url("../09_ViewModes/v-{{mode}}.css?v={{v}}");`
- `d-4k.css` gibi bazı cihaz dosyaları birden fazla `v-*.css` import eder (disk kanıtı 2026-10-06) —
  son yazan kazanır; mod ayrımı PHP `headLinks()` ile netleşir.
- `auth-bundled.css`'e **konmaz** (auth'da mod yok — `[VERIFY REQUIRED]`: auth mod eşlemesi kanıtsız).

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `v-{{mode}}.css` — `{{mode}}` PHP eşleme anahtarıyla birebir (`home`, `pro`, `studio`, `car`) |
| Seçici | mevcut mod seçici (diskte hangisi kullanılırsa — `[VERIFY REQUIRED]`, mockup/PHP kanıtıyla) |
| Token | mevcut adın override'ı (yeni ad yasak — C6) |

## Device Rules (Cihaz Kuralları)

- Cihaz davranışı bu katmanda DEĞİL → `08_Devices`.
- Mod × cihaz kesişimi: mod override token ile yapılır; cihaz override'ı `a-layout-tokens-{width}.css`
  (çakışma durumunda sıradaki katman/`08_Devices` kazanır — `css-template` §3.5).

## Responsive Rules (Responsive)

- Breakpoint değerleri `a-breakpoint-tokens.css` ile eşleşmeli; `@media` içinde `var()` okunamaz.
- Modun responsive çözümü token override ile; kolon düzeni `05_Pages`/`03_Layout`.

## Token Rules (Token Kuralları)

1. Yeni `--token` yalnız `01_Abstracts/` (C6).
2. Mod override yalnız mevcut adın değerini değiştirir (ör. `[data-view-mode="pro"] { --bg-primary: …; }`).
3. `--touch-min` taban 48px (C2) — mod onu düşürmez.
4. Figma'da olmayan değer uydurulmaz → `⚠️ VERIFICATION REQUIRED`.

## Validation (Doğrulama)

- [ ] Dosya adı PHP eşleme anahtarıyla birebir (`ViewModeManager` L16-19 / `DeviceCssMap` L29-32)
- [ ] Yeni token adı yok (C6) · ham değer yok (§4.1 #1) · `!important` 0
- [ ] Yerleşim kuralı yok (§4.1 #5)
- [ ] Mockup okundu (PNG > ASCII > Inventory)
- [ ] Gerçek sayfada mod değiştirilerek tarayıcı testi (≥2 mod)
- [ ] `headLinks()` çift yükleme davranışı biliniyor/ADR (⚠️ VERIFICATION REQUIRED)

## Example Structure (Örnek Yapı)

```css
/**
 * 09_ViewModes/v-{{mode}}.css
 * MOD   : {{mode}} (ViewModeManager eşlemesi ile birebir)
 * MOCKUP: {{png-path}}
 * KURAL : mevcut token'ın DEĞER override'ı — yeni token adı YOK (C6) · yerleşim YOK
 */

{{mod-selector}} {
  /* mevcut token adları — değerler mockup/token dosyasından okunur,
     adlar UYDURULMAZ (örnek adlar değil, diskte tanımlı olanlar kullanılır) */
  --bg-primary: {{value-from-tokens}};
  --color-accent: {{value-from-tokens}};
}

/* mod davranışı — yalnız token tüketimi */
{{mod-selector}} .main-content {
  gap: var(--grid-gap);
}

/* dar ekran — breakpoint token ile eşleşir */
@media (max-width: 767px) {
  {{mod-selector}} .main-content {
    gap: var(--space-sm);
  }
}
```

> `{{mod-selector}}` = gerçek mod seçicisi — diskteki `v-*.css` + PHP render'ından doğrulanır
> (`[VERIFY REQUIRED]`: DOM attribute/seçici adı kanıtlanmadan yazılmaz).

---

**Template Version:** 1.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R — dokunulmayan ikincil düzeltmeler):**

| Hedef | Sorun |
|-------|-------|
| `shared/src/Device/DeviceRenderer.php:120-128` | mod CSS hem import zincirinde hem ayrı `<link>` → çift yükleme; ADR bekliyor ⚠️ VERIFICATION REQUIRED |
| `assets.coremusic.net/AGENTS.md` §2 | "09_ViewModes: iskelet dosyalar" ifadesi — diskte 4 dolu/iskelet ayrımı CONTEXT.md §3.1'e ait (C4) |
