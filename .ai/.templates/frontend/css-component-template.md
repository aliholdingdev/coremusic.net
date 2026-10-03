---
title: "CoreMusic — CSS Bileşen Şablonu (04_Components)"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CSS Bileşen Şablonu — `04_Components/`

**Kapsam:** Tekrar eden görsel parça (buton, form, menü, logo, kart…) · BEM · önek `c-` (veya mevcut `_`)
**Ana şablon:** [[css-template]] · **Gate:** Mockup Before Frontend + Guardrail #16

---

## 1. Bu Katmana MI, `05_Pages`'e Mİ?

| Soru | Evet → |
|------|--------|
| Birden çok PHP sayfasında kullanılıyor mu? | `04_Components/` |
| Figma'da **component** olarak mı tanımlı? | `04_Components/` |
| Tek bir `pages/**.php` sayfasına mı özel? | `05_Pages/` |
| Grid/header/footer/sidebar düzeni mi? | `03_Layout/` |

---

## 2. Dosya Adı

```
c-{{block}}.css      → yeni bileşen (tercih edilen)
_{{block}}.css       → mevcut bileşen (korunur, yeniden adlandırılmaz)
```

---

## 3. İskelet

```css
/**
 * 04_Components/c-{{block}}.css
 * BEM: .{{block}}__element--modifier · durum: .{{block}}.is-*
 * MOCKUP: {{figma-node / png-path}}
 * TOKEN : 01_Abstracts/ — burada ham hex/px YAZILMAZ
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
  min-width: var(--touch-min);
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

/* --- modifier --- */
.{{block}}--compact {
  gap: var(--space-xs);
  padding: var(--space-xs) var(--space-sm);
}

/* --- durum --- */
.{{block}}.is-active {
  box-shadow: var(--shadow-md);
}

.{{block}}.is-loading .{{block}}__element {
  opacity: 0.5;
  pointer-events: none;
}

/* --- responsive: yalnız token tüketimi --- */
@media (max-width: 767px) {
  .{{block}} {
    min-height: var(--touch-min);
    padding: var(--space-xs);
  }
}

/* --- hareket azaltma (WCAG 2.3.3) --- */
@media (prefers-reduced-motion: reduce) {
  .{{block}} { transition-duration: 1ms !important; }
}
```

---

## 4. Kurallar

| # | Kural |
|---|-------|
| 1 | BEM zorunlu: `.block__element--modifier` + `.is-*` |
| 2 | Yerleşim (grid/sayfa düzeni) buraya yazılmaz → `03_Layout` |
| 3 | Token tüketilir, token üretilmez |
| 4 | `--touch-min` altı hedef yok (WCAG 2.2 AA) |
| 5 | Cihaz davranışı `08_Devices/`'e ait; burada yalnız dar ekran token uyumu |
| 6 | Mockup okunmadan kod yazılmaz |

---

## 5. Doğrulama

- [ ] En az 2 sayfada kullanılıyor (değilse `05_Pages`)
- [ ] BEM 3'ü de: block / element / modifier
- [ ] Sabit değer yok (§4 #1 guardrail)
- [ ] Focus-visible + reduced-motion var
- [ ] Mockup ile ölçü eşleşiyor

**Version:** 1.0.0 · **Last Updated:** 2026-10-03
