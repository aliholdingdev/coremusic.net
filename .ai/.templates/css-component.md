# CSS Component Şablonu (04_Components)

> **Bu şablon AI tarafından CSS yazarken ZORUNLU okunur.**
> SSOT: `Css/04_Components/` (c-*.css ×12, partial `_*.css` ×3) · BEM: `block__element--modifier`

## 1. Adım adım

1. Ayrım testi (css-structure.md §2): tekrar eden mi, sayfaya özel mi → component ise 04.
2. Önekli ad seç: `c-<block>.css` (yeni dosya) veya mevcut `c-*.css` içine ekle.
3. Token kullan: sabit değer yazma → `var(--cm-*, var(--fallback))`.
4. Durumları ekle: `:hover` / `:focus-visible` / `:disabled` / `.is-*` / `--mod`.

## 2. İsimlendirme deseni (gerçek dosyalardan)

```
c-buttons.css      → .btn, .btn--play, .btn__icon
c-card.css         → .card, .card__thumb, .card--selected
c-forms.css        → .lgn-form__field, .lgn-form__input--check   (gerçek: auth-bundled.css)
_footer ile ortak  → .footer__album-art, .player-btn, .player-btn--play
partial            → _widget-grid.css, _player-info.css, _welcome-banner.css (underscore = import parçası)
```

Kural: `block` (tek kelime) `__` element `--` modifier. Layout kökü: `layout--tablet` (gerçek: d-tablet.css).

## 3. Şablon

```css
/* 04_Components/c-<block>.css — BEM: block__element--modifier */
.<block> {
  /* token ile — ham px/hex YAZMA (css-token.md §1) */
  display: flex;
  gap: var(--cm-<block>-gap, var(--grid-gap, 8px));
  min-height: var(--touch-min, 48px);      /* WCAG 2.2 AA — ≥48px hedef */
}

.<block>__<element> {
  font-size: var(--text-base, var(--fs-base, 12px));
}

/* durum: hover — dokunmatik cihazlarda etkisiz (--hover-display: none) */
.<block>:hover { opacity: 0.92; }

/* durum: focus — klavye görünür (WCAG 2.4.7) */
.<block>:focus-visible {
  outline: var(--focus-ring-width, 2px) solid var(--cm-accent, #ff4fd8);
  outline-offset: 2px;
}

/* durum: disabled */
.<block>:disabled,
.<block>[aria-disabled="true"] {
  opacity: 0.5;
  pointer-events: none;
}

/* durum: modifier (seçili / yükleniyor) */
.<block>--active { /* ... */ }

/* breakpoint — bant seri, css-token.md §3 (uydurma değer yok) */
@media (max-width: 767px) { .<block> { /* phone */ } }
@media (min-width: 3840px) { .<block> { /* 4K */ } }
```

## 4. Yasaklar

- `innerHTML`/inline style üretmek (CSP) · `var` · framework sınıfı (btn-primary vb. bootstrap).
- `07_Vendors/` dışına vendor kod.
- `:root` içinde token tanımlamak (token → 01_Abstracts).
- 1-2 kullanım yeri olan seçiciyi component'e taşımak (→ 05_Pages).
