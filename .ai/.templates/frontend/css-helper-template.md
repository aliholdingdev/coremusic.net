---
title: "CoreMusic — CSS Helper Şablonu (10_Helpers)"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CSS Helper Şablonu — `10_Helpers/`

**Kapsam:** Tekrarlanabilir yardımcı desen / makro · önek `h-`
**Durum:** katman diskte **henüz yok** — ilk dosya bu şablonla oluşturulur
**Ana şablon:** [[css-template]] §3.8 · **İlgili:** [[css-utility-template]]

---

## 1. Ayrım Testi

| Soru | Evet → |
|------|--------|
| Tek satırlık işlev mi (görünürlük/boşluk)? | `06_Utilities/` |
| Kendi içinde kural/durum barındırıyor mu? | `10_Helpers/` |
| Görsel parça mı (kart/buton)? | `04_Components/` |
| `--token` tanımı mı? | `01_Abstracts/` |

---

## 2. İskelet

```css
/**
 * 10_Helpers/h-{{name}}.css
 * AMACI : tekrar kullanılabilir yardımcı desen (kural barındırır)
 * TOKEN : 01_Abstracts'ten okur — token TANIMLAMAZ
 * IMPORT: ilgili d-*.css / auth-bundled.css zincirine eklenir
 */

/* --- çok satırlı metin kırpma (2 satır) --- */
.h-ellipsis-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* --- klavye odak halkası (WCAG 2.4.7) --- */
.h-focus-ring:focus-visible {
  outline: 2px solid var(--color-accent);
  outline-offset: 2px;
  border-radius: var(--radius-sm);
}

/* --- hareket azaltma sarmalayıcı (WCAG 2.3.3) --- */
.h-motion-safe {
  transition-duration: var(--transition-normal);
}

@media (prefers-reduced-motion: reduce) {
  .h-motion-safe {
    transition-duration: 1ms !important;
    animation-duration: 1ms !important;
    animation-iteration-count: 1 !important;
    scroll-behavior: auto !important;
  }
}

/* --- klavye atlama bağlantısı (WCAG 2.4.1) --- */
.h-skip-link {
  position: absolute;
  left: var(--space-sm);
  top: -100px;
  z-index: var(--z-toast);
  padding: var(--space-sm) var(--space-md);
  background: var(--bg-primary);
  color: var(--text-primary);
  border-radius: var(--radius-md);
  transition: top var(--transition-fast);
}

.h-skip-link:focus {
  top: var(--space-sm);
}
```

---

## 3. Kurallar

| # | Kural |
|---|-------|
| 1 | Yardımcı desen = **çok yerde** tekrar eden kural; tek kullanım `06`/`04` |
| 2 | Token tanımı yok |
| 3 | BEM block gibi davranmaz (yardımcı öneki `h-`) |
| 4 | Bootstrap'te karşılığı varsa **üretilmez** → `07_Vendors` |
| 5 | Yeni dosya import zincirine eklenir (`d-*.css` veya `auth-bundled.css`) |
| 6 | `.ai/.templates/index.md`'ye kayıt eklenir (Guardrail #16) |

---

## 4. Doğrulama

- [ ] En az 2 kullanım yeri var mı?
- [ ] `06`/`04` katmanı değil mi? (doğrulandı)
- [ ] Token tanımı yok
- [ ] Import zincirine eklendi
- [ ] Registry (`index.md`) güncellendi

**Version:** 1.0.0 · **Last Updated:** 2026-10-03
