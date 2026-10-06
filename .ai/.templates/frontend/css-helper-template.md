---
title: "CoreMusic — CSS Helper Şablonu (10_Helpers)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 2.0.0
status: active
authority: reference
---

# CSS Helper Şablonu — `10_Helpers/`

**Kapsam:** Tekrarlanabilir yardımcı desen / makro (kural barındırır) · önek `h-` · **`!important`siz iskelet**
**Ana şablon:** [[css-template]] §3.8 · **İlgili:** [[css-utility-template]] · **Gate:** Guardrail #16
**v2.0.0 sıfırdan yeniden yazım (2026-10-06) — C5: iskelet 4→0 `!important`; toplam iskelet 2/3 (06:1 + 04:1).**

---

## Purpose (Amaç)

Yerlerde tekrar eden, **kural barındıran** yardımcı deseni tek dosyada tutmak (çok satırlı kırpma,
odak halkası, klavye atlama bağlantısı). Tek satırlık sıfır-mantık sınıf buraya DEĞİL → `06_Utilities`.

## Location (Konum)

`assets.coremusic.net/Css/10_Helpers/h-{{name}}.css`
Örnek dosya (2026-10-06 ölçümü — envanter iddiası değil; sayı/envanter yalnız `Css/CONTEXT.md §3.1`'de — C4):
`h-ellipsis.css` (iskelet — içerik bekliyor).

## Responsibility (Sorumluluk)

- Tekrarlanabilir **kural** deseni: durum/selector içerir ama başka sınıf bilmez.
- WCAG yardımcıları: odak halkası (2.4.7), skip-link (2.4.1), hareket azaltma (2.3.3) — `!important`siz.
- `06` ile ayrım: `06` = koşulsuz tek işlev; `10` = kural barındıran desen.

## Allowed (İzinli)

- BEM dışı yardımcı önek seçicileri: `.h-{{name}}`, `.h-{{name}}:focus`, `.h-{{name}}.is-*`.
- `var(--token)` tüketimi; `@media (prefers-reduced-motion: reduce)` **normal öncelikle** (C5: `!important` YOK).
- Cihaz zincirine `?v={{v}}` ile import.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | `!important` — iskelette 0 (kod geneli cap 3: 06:1 + 04:1 hakları kullanılmış) | `Css/CLAUDE.md` §4.1 #2 · C5 |
| 2 | Token tanımı (`--x: …`) | §4.1 #9 (C6) |
| 3 | Ham hex/px | §4.1 #1 |
| 4 | Başka sınıf bilme (`.a .b` yazımı — blok istisnası `:focus`/`.is-*`) | BEM (§4.1 #6) |
| 5 | Bileşen yüzeyi (kart/buton görünümü) → `04_Components` | §3.1 ayrım |
| 6 | Bootstrap'te karşılığı olan desen → `07_Vendors` | §4.1 #10 |
| 7 | Dosya sayısı/envanter iddiası | C4 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `01_Abstracts/*` | `--space-*`, `--color-accent`, `--radius-*`, `--transition-*`, `--z-*` |
| `06_Utilities` | ayrım ortağı (tek işlev vs kural deseni) |
| `08_Devices/d-*.css` · `auth-bundled.css` | import eden girişler |
| `.ai/.templates/index` | yeni şablon dosyası kaydı (Guardrail #16) |

## Import Rules (Import Kuralları)

- Bu dosya **import etmez**; cihaz zincirinde `09`'dan sonra eklenir:
  `@import url("../10_Helpers/h-{{name}}.css?v={{v}}");`
- `css-template` §Import Rules iskeletinde `10_Helpers` satırı **zorunlu** (C5).
- `auth-bundled.css`'e yalnız auth sayfası ihtiyacı varsa eklenir (şişirmez).

## Naming Rules (Adlandırma)

| Kural | Kalıp | Örnek (disk: `h-ellipsis.css`) |
|-------|-------|-------------------------------|
| Dosya | `h-{{name}}.css` | `h-ellipsis.css` |
| Sınıf | `.h-{{name}}` | `.h-ellipsis-2`, `.h-focus-ring`, `.h-skip-link` |
| BEM block yasak | yardımcı önek `h-` blok gibi davranmaz | `.player__x` buraya yazılmaz |

## Device Rules (Cihaz Kuralları)

- Cihaz davranışı yazmaz; helper cihazdan bağımsızdır. Cihaz uyarlaması → `08_Devices`.
- `@media` yalnız **WCAG ayarı** (reduced-motion) — cihaz kırılımı değil.

## Responsive Rules (Responsive)

- Breakpoint `@media` **yazmaz** (helper koşulsuz desendir). Çözüm token veya `08_Devices` üzerinden.

## Token Rules (Token Kuralları)

- Yalnız tüketim: `var(--color-accent)`, `var(--space-sm)`, `var(--transition-fast)`, `var(--z-toast)`.
- Yeni token tanımı yasak (C6 → yalnız `01_Abstracts`).
- `--touch-min` kullanımında taban 48px (C2).

## Validation (Doğrulama)

- [ ] En az 2 kullanım yeri var (tek kullanım → `04`/`06`)
- [ ] `!important` **0** (C5 — iskelet toplamı 2/3: 06:1 + 04:1)
- [ ] Token tanımı yok · ham değer yok
- [ ] Başka sınıf bilmiyor (yalnız `:focus*` / `.is-*`)
- [ ] Import zincirine eklendi (`d-*.css` veya `auth-bundled.css`, `10_Helpers` satırı)
- [ ] `.ai/.templates/index` kaydı güncellendi (Guardrail #16)

## Example Structure (Örnek Yapı)

```css
/**
 * 10_Helpers/h-{{name}}.css
 * AMACI : tekrarlanabilir kural deseni — !important YOK (C5: iskelet 0/3)
 * TOKEN : 01_Abstracts'ten okur, TANIMLAMAZ (C6)
 * IMPORT: cihaz zincirinde 09'dan sonra (`css-template` §Import Rules)
 */

/* --- çok satırlı metin kırpma --- */
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

/* --- hareket azaltma (WCAG 2.3.3) — normal öncelik, !important YOK (C5) ---
   Bilinçli sınır: bu desen yüksek öncelikli bloklarda geri planlanabilir;
   öncelik çatışması çıkarsa çözüm `!important` değil, yüklenme sırasıdır. */
@media (prefers-reduced-motion: reduce) {
  .h-motion-safe {
    transition-duration: 1ms;
    animation-duration: 1ms;
    animation-iteration-count: 1;
    scroll-behavior: auto;
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

**Template Version:** 2.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R — dokunulmayan ikincil düzeltmeler):**

| Hedef | Sorun |
|-------|-------|
| `10_Helpers/h-ellipsis.css` | 2026-10-03 iskeleti (10 satır yorum) — içerik boş; ayrıca v1 şablonundaki 6 `!important` iskeleti bu sürümle geçersiz |
| `assets.coremusic.net/Css/CLAUDE.md` §4.1 #2 | kod geneli `!important` 47+ satır ihlal (vendor hariç) — hedef: iskelet 2/3, kod ≤3 gerekçeli |
