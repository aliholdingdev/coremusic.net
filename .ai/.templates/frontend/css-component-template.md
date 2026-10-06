---
title: "CoreMusic — CSS Bileşen Şablonu (04_Components)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 2.0.0
status: active
authority: reference
---

# CSS Bileşen Şablonu — `04_Components/`

**Kapsam:** Tekrar eden görsel parça (buton, form, menü, logo, kart…) · BEM · önek `c-` (yeni) / `_` (mevcut)
**Ana şablon:** [[css-template]] §3.4 · **Gate:** Mockup Before Frontend + Guardrail #16
**v2.0.0 sıfırdan yeniden yazım (2026-10-06) — C2 (48px), C4 (sayı yok), C5 (`!important` 1/3).**

---

## Purpose (Amaç)

Birden çok sayfada tekrar eden görsel parçanın stilini tek yerde tutmak (Figma component karşılığı).

## Location (Konum)

`assets.coremusic.net/Css/04_Components/c-{{block}}.css` (yeni) · `_{{block}}.css` (mevcut — yeniden
adlandırılmaz). Örnek dosyalar (2026-10-06 ölçümü): `c-buttons.css` · `c-forms.css` · `c-card.css` ·
`c-modal.css` · `c-badge.css` · `c-toggle.css` · `c-toast.css` · `c-progress.css` ·
`c-scrollbar-accent.css` · `c-footer-seek.css` · `c-footer-volume.css` · `c-home-song-btn.css` ·
`_home-components.css` · `_welcome-banner.css` · `_player-info.css`

## Responsibility (Sorumluluk)

- Bileşen yüzeyi: renk, boşluk, tipografi, durum (hover/focus/is-*), modifikatör.
- WCAG: dokunma hedefi `--touch-min` (≥48px — C2), `:focus-visible` odak halkası.

## Allowed (İzinli)

- BEM seçiciler (`.block`, `.block__el`, `.block--mod`, `.block.is-*`).
- `var(--token)` tüketimi; `@media` = yalnız token uyarlaması (padding/min-height).
- **Tek `!important`**: `@media (prefers-reduced-motion: reduce)` bloğu içi (C5 — iskelet 1/3;
  gerekçe: hareket azaltma WCAG 2.3.3, kullanıcı ayarı her şeyi geçmeli).

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Ham hex/px | §4.1 #1 |
| 2 | Token tanımı (`--x: …`) | §4.1 #9 (C6) |
| 3 | Grid/sayfa düzeni (header/footer/sidebar/grid) | §3.1 → `03_Layout` |
| 4 | Tek sayfaya özel stil | §3.1 → `05_Pages` |
| 5 | Cihaz davranışı (`08_Devices`) | §4.1 #5 |
| 6 | `!important` (bu iskelette tek istisna; kod geneli cap 3) | §4.1 #2 · C5 |
| 7 | Klasör/şablon envanter sayısı iddia etme | C4 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `01_Abstracts/*` | `--space-*`, `--bg-*`, `--text-*`, `--touch-min`, `--radius-*`, `--shadow-*` |
| `.ai/ui-design/` (mockup + `02-component-inventory`) | bileşen karşılığı + ölçü |
| `07_Vendors` | Bootstrap'te karşılığı varsa **üretilmez** (§4.1 #10) |
| `08_Devices` | cihaz davranışı burada değil, orada |

## Import Rules (Import Kuralları)

- Import **etmez**; cihaz zincirinde `03`'ten sonra:
  `@import url("../04_Components/c-{{block}}.css?v={{v}}");`

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `c-{{block}}.css` (yeni) · `{{_block}}.css` (mevcut korunur) |
| Block | Figma component adı ile eşleşen kelime (`.{{block}}`) |
| Element | `.{{block}}__{{element}}` |
| Modifikatör | `.{{block}}--{{modifier}}` |
| Durum | `.{{block}}.is-{{state}}` (BEM dışı state eki yasak: `.player.loading`) |

## Device Rules (Cihaz Kuralları)

- Bu dosya cihaz **dosyası** yazmaz; yalnız `var()` ile cihazdan etkilenir.
- Cihaz davranışı (hover yok, tap area, scroll) → `08_Devices` ([[css-device-template]]).

## Responsive Rules (Responsive)

- `@media` burada **yalnız token tüketimi** (min-height, padding, gap); yeni breakpoint değeri
  tanımlanmaz (değer `a-breakpoint-tokens.css` ile eşleşmeli).
- Touch-first cihazlarda `min-width/min-height: var(--touch-min)` zorunlu.

## Token Rules (Token Kuralları)

- Tüketim: `var(--space-*)`, `var(--touch-min)`, `var(--bg-*)`, `var(--text-*)`, `var(--radius-*)`,
  `var(--shadow-*)`, `var(--transition-*)`.
- Yeni token tanımı yasak (C6) → `01_Abstracts`.

## Validation (Doğrulama)

- [ ] ≥2 sayfada kullanılıyor (değilse → `05_Pages`)
- [ ] BEM 3'lüsü tam (block/element/modifikatör) + `.is-*` durum
- [ ] Ham değer yok · token tanımı yok
- [ ] `--touch-min` ≥ 48px hedef (C2) + `:focus-visible` var
- [ ] `!important` ≤1 (iskelet) / ≤3 (kod, gerekçeli) — C5
- [ ] Mockup ölçüsü eşleşti (PNG > ASCII > Inventory)
- [ ] Gerçek sayfada tarayıcı testi yapıldı

## Example Structure (Örnek Yapı)

```css
/**
 * 04_Components/c-{{block}}.css
 * BEM   : .{{block}}__element--modifier · durum: .{{block}}.is-*
 * MOCKUP: {{png-path}}
 * TOKEN : 01_Abstracts — ham hex/px YAZILMAZ (§4.1 #1)
 */

.{{block}} {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  min-height: var(--touch-min);
  padding: var(--space-sm) var(--space-md);
  background: var(--bg-secondary);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  transition: box-shadow var(--transition-fast);
}

.{{block}}__element {
  font-size: var(--text-base);
  color: var(--text-primary);
}

.{{block}}__button {
  min-width: var(--touch-min);      /* C2 — ≥48px */
  min-height: var(--touch-min);
  border: 0;
  border-radius: var(--radius-full);
  background: var(--bg-tertiary);
  color: var(--text-primary);
  cursor: pointer;
}

.{{block}}__button:focus-visible {
  outline: 2px solid var(--color-accent);
  outline-offset: 2px;
}

/* --- modifikatör --- */
.{{block}}--compact { gap: var(--space-xs); }

/* --- durum --- */
.{{block}}.is-active { box-shadow: var(--shadow-md); }

.{{block}}.is-loading .{{block}}__element {
  opacity: 0.5;
  pointer-events: none;
}

/* --- responsive: yalnız token uyarlaması --- */
@media (max-width: 767px) {
  .{{block}} { padding: var(--space-xs); }
}

/* --- hareket azaltma (WCAG 2.3.3) — bu dosyanın TEK !important'i (C5: 1/3) --- */
@media (prefers-reduced-motion: reduce) {
  .{{block}} { transition-duration: 1ms !important; } /* gerekçe: kullanıcı ayarı öncelikli */
}
```

---

**Template Version:** 2.0.0 · **Last Updated:** 2026-10-06
