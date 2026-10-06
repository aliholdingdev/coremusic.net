---
title: "CoreMusic — CSS Utility Şablonu (06_Utilities)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 2.0.0
status: active
authority: reference
---

# CSS Utility Şablonu — `06_Utilities/`

**Kapsam:** Tek amaclı, sayfa bağımsız, **sıfır mantık** sınıf · önek `u-` (durum sınıfı `.is-*` hariç)
**Ana şablon:** [[css-template]] §3.8 · **İlgili:** [[css-helper-template]]
**v2.0.0 sıfırdan yeniden yazım (2026-10-06) — C5 (iskelet 1 `!important`/3), C4.**

---

## Purpose (Amaç)

Tek satırlık, koşulsuz, başka sınıf tanımayan işlevsel sınıf sağlamak (görünürlük, kırpma, boşluk).

## Location (Konum)

`assets.coremusic.net/Css/06_Utilities/u-{{ad}}.css`
Örnek dosya (2026-10-06 ölçümü): `u-helpers-utility.css` (bu katmanın tek dosyası — sayı envanter
`Css/CONTEXT.md §3.1`'dedir, bu şablonda iddia edilmez).

## Responsibility (Sorumluluk)

- `.is-hidden` · `.u-sr-only` · `.u-truncate` · `.u-flex` · boşluk/margin yardımcıları.
- Bootstrap utility ile **çakışan** işlevi yeniden üretmemek (`07_Vendors/bootstrap-utilities.css` var).

## Allowed (İzinli)

- Tek seçici + tek/az sayıda bildirim (declaration) — **koşul yok, iç içe seçici yok**.
- `var(--token)` tüketimi.
- **Tek `!important` istisnası**: `.is-hidden` (C5 — bu dosyanın 1/3 hakkı; gerekçe: katman sırasını
  kırmadan görünürlüğü garantiye almak, yorumda yazılı).

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Başka sınıf bilme (`.a .b` yazımı) | `css-template` §3.8 · BEM |
| 2 | Koşul/durum mantığı (→ `04` / `10`) | ayrım |
| 3 | Token tanımı | §4.1 #9 (C6) |
| 4 | Bileşen yüzeyi | §3.1 |
| 5 | `.is-hidden` dışında `!important` (cap 3 genel; iskelet 1/3) | §4.1 #2 · C5 |
| 6 | Bootstrap utility kopyası | §4.1 #10 · tekrar |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `01_Abstracts/*` | `--space-*` token kaynağı |
| `07_Vendors/bootstrap-utilities.css` | karşılığı varsa **üretilmez** |
| `10_Helpers` | kural barındıran desen oraya |

## Import Rules (Import Kuralları)

- Import **etmez**; tüm cihaz zincirlerinde `05`'ten sonra tek satır:
  `@import url("../06_Utilities/u-helpers-utility.css?v={{v}}");`
- `auth-bundled.css`'e yalnız auth sayfası ihtiyacı varsa eklenir (şişirmez).

## Naming Rules (Adlandırma)

| Tür | Kalıp | Örnek |
|-----|-------|-------|
| Dosya | `u-{{ad}}.css` | `u-helpers-utility.css` |
| Sınıf | `u-{{ad}}` | `.u-truncate`, `.u-sr-only` |
| Durum sınıfı (istisna) | `is-{{state}}` | `.is-hidden` (görünürlük).

## Device Rules (Cihaz Kuralları)

- Cihaz davranışı yok; utility cihazdan bağımsızdır. Cihaz'a özgü davranış → `08_Devices`.

## Responsive Rules (Responsive)

- `@media` **yok** (utility koşulsuz). Responsive çözüm token veya `08_Devices` üzerindendir.

## Token Rules (Token Kuralları)

- Tüketim: `var(--space-*)`, `var(--text-*)`.
- Yeni token tanımı yasak (C6).

## Validation (Doğrulama)

- [ ] Tek işlev mi? (değilse → `04` bileşen / `10` helper)
- [ ] Başka sınıf referansı yok
- [ ] Token tanımı yok · ham değer yok
- [ ] Bootstrap'te karşılığı var mı? (varsa tekrar ekleme)
- [ ] `!important` yalnız `.is-hidden` (1/3 — C5), gerekçe yorumda

## Example Structure (Örnek Yapı)

```css
/**
 * 06_Utilities/u-{{ad}}.css
 * AMACI : tek işlev · başka sınıf/selector bilmez · koşul yok
 * TOKEN : 01_Abstracts'ten okur, TANIMLAMAZ (C6)
 */

/* görünürlük — footer play/pause, koşullu bloklar
   gerekçe: katman sırasını kırmadan garanti altına alınır (C5 — bu dosyanın 1/3 !important'i) */
.is-hidden {
  display: none !important;
}

/* tek satır kırpma */
.u-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* yalnızca ekran okuyucu */
.u-sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* ızgara yardımcıları — token tüketir */
.u-flex { display: flex; }
.u-flex-center { display: flex; align-items: center; justify-content: center; }
.u-gap-sm { gap: var(--space-sm); }
.u-mt-md { margin-top: var(--space-md); }
```

---

**Template Version:** 2.0.0 · **Last Updated:** 2026-10-06
