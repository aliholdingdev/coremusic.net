---
title: "CoreMusic — CSS Layout Şablonu (03_Layout)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 1.0.0
status: active
authority: reference
---

# CSS Layout Şablonu — `03_Layout/`

**Kapsam:** Sayfa düzeni — header, footer, sidebar, widget grid · dosya öneki `_`
**Ana şablon:** [[css-template]] §3.1 · **Gate:** Mockup Before Frontend + Guardrail #16
**YENİ DOSYA (2026-10-06) — önceki oturumda raporlanıp diske düşmemişti.**

---

## Purpose (Amaç)

Sayfayı **düzenleyen** yapısal blokları yazmak: nerede header, nerede sidebar, grid nasıl akar.
Düzen (yerleşim) bu katmanın tekelindedir — `08_Devices`'e yerleşim yazmak yasaktır
(`Css/CLAUDE.md` §4.1 #5).

## Location (Konum)

`assets.coremusic.net/Css/03_Layout/_{{konu}}.css`
Örnek dosyalar (2026-10-06 ölçümü): `_header.css` · `_footer.css` · `_sidebar.css` · `_widget-grid.css`

## Responsibility (Sorumluluk)

- Grid/flex **düzeni** (kolon yapısı, sidebar konumu, sticky header).
- Layout token tüketimi: `--header-h`, `--sidebar-w`, `--grid-gap`, `--z-header` …
- Bileşen **yüzeyi** burada DEĞİL → `04_Components`; sayfa özel akış → `05_Pages`.

## Allowed (İzinli)

- Düzen seçicileri (`.site-header`, `.layout`, `.sidebar`, `.widget-grid`).
- `display: grid|flex`, `position`, `gap`, `grid-template-*`, `z-index` (token ile).
- `var(--token)` tüketimi; `@media` ile düzen kırılımı (değerler token'dan; breakpoint
  `a-breakpoint-tokens.css` ile eşleşmeli).

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Bileşen yüzeyi (buton/badge/modal görünümü) | `css-template` §3.1 |
| 2 | Token tanımı (`--x: …`) | §4.1 #9 (C6) |
| 3 | Ham hex/px | §4.1 #1 |
| 4 | `p-` öneki (bu katman `_` kullanır; `p-` münhasır 05) | C3 |
| 5 | Figma mockupsuz ölçü | Mockup Before Frontend |
| 6 | `!important` (cap 3 — varsayılan 0) | §4.1 #2 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `01_Abstracts/*` | `--header-h`, `--sidebar-w`, `--z-*` token kaynağı |
| `02_Base` | base'den sonra yüklenir (`01 → 02 → 03`) |
| `04_Components` / `05_Pages` | bu katman onlardan önce gelir; çelişirse **önceki katman kazanmaz** — sıradaki katman override eder |
| `.ai/ui-design/` | header/footer/sidebar mockup ölçüleri (PNG > ASCII > Inventory) |

## Import Rules (Import Kuralları)

- Bu dosyalar import **etmez**; cihaz zincirinde `02`'den sonra yüklenir:
  `@import url("../03_Layout/_header.css?v={{v}}");`
- `_widget-grid.css` token kardeşi `a-widget-grid-tokens.css` ile birlikte düşünülür (token 01'de).

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `_{{konu}}.css` (alt çizgi + konu) |
| Seçici | BEM: `.site-header`, `.site-header__nav`, `.site-header--stuck` |
| Yasak önek | `p-` (05'e ait), `c-` (04'e ait) |

## Device Rules (Cihaz Kuralları)

- Yerleşim değişikliği (sidebar'ı gizle, header'ı küçült) → `08_Devices` **davranış** alanı veya
  layout token override (`01_Abstracts/a-layout-tokens-{width}.css`) — bu dosyada `@media` yığmak yerine
  token tüketimi tercih edilir.
- `08_Devices`'e yerleşim yazıldıysa: revert + bu katmana taşı (§4.3 #2).

## Responsive Rules (Responsive)

- Düzen kırılımı mümkün olduğunca **token ile** çözülür (`--sidebar-w` cihaz dosyasında değişir →
  düzen kendiliğinden uyar).
- Gerekli `@media` burada: breakpoint değeri `a-breakpoint-tokens.css` ile eşleşmeli (tahmin yasak).

## Token Rules (Token Kuralları)

- Tüketim: `var(--header-h)`, `var(--footer-h)`, `var(--sidebar-w)`, `var(--grid-gap)`,
  `var(--z-header)` …
- Yeni token tanımı yasak (C6) → `01_Abstracts`.

## Validation (Doğrulama)

- [ ] Dosya `_{{konu}}.css` kalıbında
- [ ] Yalnız düzen (grid/flex/position) — bileşen yüzeyi yok
- [ ] Token tanımı yok; ham değer yok
- [ ] Mockup ölçüsü ile karşılaştırıldı (PNG > ASCII)
- [ ] Cihaz testi: ≥2 genişlikte tarayıcı doğrulaması
- [ ] `08_Devices`'e yerleşim sızmadı (§4.1 #5)

## Example Structure (Örnek Yapı)

```css
/**
 * 03_Layout/_{{konu}}.css
 * ROL    : sayfa düzeni (grid/position) — bileşen yüzeyi DEĞİL
 * MOCKUP : {{png-path}} · TOKEN: 01_Abstracts (ham hex/px YOK)
 */

.{{konu}} {
  display: grid;
  grid-template-columns: var(--sidebar-w) minmax(0, 1fr);
  grid-template-rows: var(--header-h) minmax(0, 1fr) var(--footer-h);
  grid-template-areas:
    "header header"
    "sidebar main"
    "footer footer";
  min-height: 100vh;
}

.{{konu}}__header { grid-area: header; z-index: var(--z-header); }
.{{konu}}__sidebar { grid-area: sidebar; }
.{{konu}}__main { grid-area: main; padding: var(--content-padding); }
.{{konu}}__footer { grid-area: footer; z-index: var(--z-footer); }

/* düzen kırılımı — breakpoint token ile eşleşir */
@media (max-width: 767px) {
  .{{konu}} {
    grid-template-columns: minmax(0, 1fr);
    grid-template-areas: "header" "main" "footer";
  }
}
```

---

**Template Version:** 1.0.0 · **Last Updated:** 2026-10-06
