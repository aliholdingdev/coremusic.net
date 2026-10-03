---
title: "CoreMusic — CSS Sayfa Şablonu (05_Pages)"
type: template
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: reference
---

# CSS Sayfa Şablonu — `05_Pages/`

**Kapsam:** Tek PHP sayfasına özel stiller · önek `p-` (sayfa) / `_` (bileşenleşmiş sayfa parçası)
**Ana şablon:** [[css-template]] · **Gate:** Mockup Before Frontend + Guardrail #16

---

## 1. Kapsam Testi

| Soru | Evet → |
|------|--------|
| Yalnız `pages/**.php` sayfasında mı kullanılıyor? | `05_Pages/` |
| Başka sayfalarda da mı tekrar ediyor? | `04_Components/c-*.css` (taşınır) |
| `--token` tanımı içeriyor mu? | `01_Abstracts/` (geri taşınır) |

**Dosya ↔ sayfa eşlemesi (disk kanıtı):**

```
p-login-view.css      → pages/auth/login-view.php
p-select-gender.css   → pages/auth/select-gender.php
p-settings.css        → pages/settings (ayarlar)
p-artists.css         → /artists
p-albums.css          → /albums
p-album-detail.css    → /albums/:id
p-playlist.css        → /playlist
_home.css             → pages/home.php  (+ _home-layout / _home-inline import eder)
_player.css           → pages/player.php
```

---

## 2. İskelet

```css
/**
 * 05_Pages/p-{{page}}.css
 * SAYFA : {{php-path}}
 * MOCKUP: {{figma-node / png-path}}
 * NOT   : tekrar eden parça 04_Components'e; token 01_Abstracts'e taşınır
 */

/* --- sayfa kabı --- */
.p-{{page}} {
  display: flex;
  flex-direction: column;
  gap: var(--section-gap);
  padding: var(--content-padding);
  min-height: var(--content-h);
}

/* --- içerik bloğu (BEM, sayfa adı = block) --- */
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

.{{page}}__grid--wide {
  --grid-cols: 4;
}

/* --- boş durum --- */
.{{page}}.is-empty .{{page}}__grid {
  display: none;
}

/* --- dar ekran: token uyarlaması (yerleşim 03_Layout'ta) --- */
@media (max-width: 767px) {
  .{{page}} {
    padding: var(--space-sm);
    gap: var(--space-md);
  }
}
```

---

## 3. Kurallar

| # | Kural |
|---|-------|
| 1 | Bu dosya **tek sayfaya** hizmetlı; genelleşirse `04_Components`'e taşınır |
| 2 | Token tanımı yok → `01_Abstracts` |
| 3 | Header/footer/sidebar stilleri yok → `03_Layout` |
| 4 | Bir sayfa = bir `p-*.css`; `_home.css` gibi çatı dosya alt dosya `@import` edebilir |
| 5 | `!important` yasak (en fazla 3 gerekçeli istisna) |
| 6 | Mevcut dosya adı/yolu değiştirilirse: tüm `@import` + PHP docblock + `DeviceCssMap` güncellenir |

---

## 4. Doğrulama

- [ ] Dosya tek PHP sayfasına karşılık geliyor
- [ ] Component/Token içeriyor mu → taşı
- [ ] BEM block = sayfa adı
- [ ] Sabit değer yok
- [ ] Tarayıcıda sayfa testi yapıldı (gerçek eleman/layout)

**Version:** 1.0.0 · **Last Updated:** 2026-10-03
