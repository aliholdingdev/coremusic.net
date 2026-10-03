---
title: "CoreMusic — CSS Utility Şablonu (06_Utilities)"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CSS Utility Şablonu — `06_Utilities/`

**Kapsam:** Tek amaclı, sayfa bağımsız, **sıfır mantık** sınıfı · önek `u-`
**Ana şablon:** [[css-template]] §3.8 · **İlgili:** [[css-helper-template]]

---

## 1. Ayrım (06 vs 10)

| | `06_Utilities/` | `10_Helpers/` |
|--|-----------------|---------------|
| Ne | tek satır işlev | tekrarlanabilir yardımcı desen |
| Örnek | `.is-hidden`, `.u-truncate`, `.sr-only` | odak halkası, 2 satır kırpma, motion-safe sarmalayıcı |
| Bağımlılık | başka sınıf bilmez | kendi içinde kural barındırır |
| Token | tüketir | tüketir |
| **Token üretmez — token üretimi yalnız `01_Abstracts`.** | | |

---

## 2. İskelet

```css
/**
 * 06_Utilities/u-{{name}}.css
 * AMACI : tek işlev · başka sınıf/selector bilmez
 * TOKEN : 01_Abstracts'ten okur, tanımlamaz
 */

/* görünürlük — footer play/pause, koşullu bloklar */
.is-hidden {
  display: none !important;   /* gerekçe: katman sırasını kırmak için tek istisna */
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

/* ızgara yardımcıları — layout token'ı tüketir */
.u-flex { display: flex; }
.u-flex-center { display: flex; align-items: center; justify-content: center; }
.u-gap-sm { gap: var(--space-sm); }
.u-mt-md { margin-top: var(--space-md); }
```

---

## 3. Kurallar

| # | Kural |
|---|-------|
| 1 | **Bir sınıf = bir işlev**; koşul/alt mantık yok |
| 2 | Başka sınıf bilinmez (`.a .b` yazım yasağı) |
| 3 | Bileşen stili yazılmaz → `04_Components` |
| 4 | Token tanımı yok |
| 5 | `!important` en fazla 1 sınıf (görünürlük), gerekçesi yorumda |
| 6 | Bootstrap utility **kopyalanmaz** → `07_Vendors/bootstrap-utilities.css` zaten var |

---

## 4. Doğrulama

- [ ] Tek işlev mi? (değilse component/helper)
- [ ] Başka sınıf referansı var mı? (varsa kural)
- [ ] Token tanımı yok
- [ ] Bootstrap'te karşılığı var mı? (varsa tekrar ekleme)

**Version:** 1.0.0 · **Last Updated:** 2026-10-03
