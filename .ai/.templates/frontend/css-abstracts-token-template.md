---
title: "CoreMusic — CSS Token Şablonu (01_Abstracts)"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CSS Token Şablonu — `01_Abstracts/`

**Kapsam:** Yalnızca `--token: değer` · seçici/kural **yazılmaz** · önek `a-`
**Ana şablon:** [[css-template]] · **Gate:** Guardrail #16

---

## 1. Dosya Adı Kuralı

```
a-layout-tokens.css            → BASE (fallback)
a-layout-tokens-mobile.css     → ≤767px
a-layout-tokens-tablet.css     → 768–1023px
a-layout-tokens-1024.css       → RPi5 1024×600
a-layout-tokens-1920.css       → wide desktop
a-layout-tokens-3540.css       → 4K monitor
a-layout-tokens-3840.css       → 4K TV
```

> **Cihaz, dosya adında görünür** — hangi token'ın hangi cihaz için olduğu bakışta anlaşılır.
> Konu önekleri: `a-colors-token`, `a-fonts-token`, `a-breakpoint-tokens`, `a-semantic-token`, `a-primitive-tokens`, `a-login-tokens`, `a-theme-config`, `a-scale-hybrid`.

---

## 2. İskelet

```css
/**
 * 01_Abstracts/a-layout-tokens-{{width}}.css
 * CİHAZ : {{device}}
 * ARALIK: {{min}}px – {{max}}px
 * KAYNAK: .ai/ui-design/tokens/ (Figma SSOT)
 * NOT    : notes.md notları buraya uygulanır ve ✓ ile imzalanır
 */

@media (min-width: {{min}}px) and (max-width: {{max}}px) {
  :root {
    /* === GENEL ÖLÇÜ === */
    --header-h: 60px;
    --footer-h: 90px;
    --sidebar-w: 280px;
    --content-padding: 16px;

    /* === SPACING (4px ızgara) === */
    --space-xs: 4px;
    --space-sm: 8px;
    --space-md: 16px;
    --space-lg: 24px;
    --space-xl: 32px;
    --grid-gap: 8px;
    --card-padding: 12px;

    /* === TOUCH (WCAG 2.2) === */
    --touch-min: 48px;
    --touch-recommended: 56px;
    --touch-large: 56px;

    /* === COMPONENT TOKEN === */
    --now-playing-art-size: 100px;
    --card-thumb-size: 140px;
    --album-art-size: 300px;
    --widget-min-height: 100px;
    --widget-grid-cols: 2;
    --footer-album-art-size: 120px;
    --footer-icon: 13px;
    --footer-btn-min-size: 48px;

    /* === TİPOGRAFİ (a-scale-hybrid akışı) === */
    --text-xs: var(--fs-xs, 10px);
    --text-sm: var(--fs-sm, 11px);
    --text-base: var(--fs-base, 12px);
    --text-lg: var(--fs-lg, 14px);
    --text-xl: var(--fs-xl, 16px);

    /* === Z-INDEX === */
    --z-sidebar: 50;
    --z-header: 100;
    --z-footer: 100;
    --z-dropdown: 200;
    --z-modal: 300;
    --z-toast: 400;
  }
}
```

---

## 3. Kurallar

| # | Kural |
|---|-------|
| 1 | Dosya içinde **seçici yok** (yalnız `:root` / `[data-*]` / `@media` sarmalayıcı) |
| 2 | Ham `#hex` / `px` **yalnız bu katmanda** yazılır; diğer katmanlarda `var(--...)` |
| 3 | Token önce tanımlanır, sonra tüketilir (`css-template` §4.2) |
| 4 | Figma'da olmayan token **uydurulmaz** → `⚠️ VERIFICATION REQUIRED` |
| 5 | Sıralama: base → mobile → tablet → 1024 → 1920 → 3540 → 3840 |
| 6 | Tema override yalnız `[data-theme]` / `[data-accent]` ile (ADR-044) |

---

## 4. Doğrulama

- [ ] Dosya adında cihaz/konu geçiyor
- [ ] Seçici yok · kural yok · `@media` dışında kural yok
- [ ] `var(--...)` fallback'i var
- [ ] `notes.md` notu uygulandı mı?
- [ ] İlgili `d-*.css` bu token dosyasını import ediyor

**Version:** 1.0.0 · **Last Updated:** 2026-10-03
