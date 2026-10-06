---
title: "CoreMusic — CSS Konu Token Şablonu (01_Abstracts)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 2.0.0
status: active
authority: reference
---

# CSS Konu Token Şablonu — `01_Abstracts/` (a-{konu}-token)

**Kapsam:** `01_Abstracts/` içindeki **konu** token dosyaları (renk, font, semantik, breakpoint, login,
tema…) — cihaz layout token ailesi (`a-layout-tokens-*`) ayrı şablondur: [[css-device-token-template]].
**Ana şablon:** [[css-template]] · **Gate:** Guardrail #16 · **v2.0.0 sıfırdan yeniden yazım (2026-10-06)**

---

## Purpose (Amaç)

Tek dosyada tek konunun tokenlarını tanımlamak: `--token: değer`. Bu katman **token üretiminin tek
katmanıdır** (C6 katman kuralı · `Css/CLAUDE.md` §4.1 #9).

## Location (Konum)

`assets.coremusic.net/Css/01_Abstracts/a-{konu}-token(s).css`
Örnek dosya adları (2026-10-06 ölçümü — envanter iddiası değildir):
`a-colors-token.css` · `a-fonts-token.css` · `a-breakpoint-tokens.css` · `a-semantic-token.css` ·
`a-primitive-tokens.css` · `a-login-tokens.css` · `a-theme-config.css` · `a-color-mode-tokens.css` ·
`a-light-glass-tokens.css` · `a-scale-hybrid.css` · `a-design-tokens.css`

## Responsibility (Sorumluluk)

- Token **tanımı**: custom property + (gerekirse) `[data-theme]`/`[data-accent]`/`@media` sarmalayıcısı.
- Figma SSOT'tan (`.ai/ui-design/tokens/`) gelen değerin diske yazılması.

## Allowed (İzinli)

- `:root { --token: değer; }` · `[data-*] { --token: değer; }` · `@media (...) { :root { ... } }`.
- Ham `#hex` / `px` / `rem` **yalnız bu dosyada**.
- `var(--x, fallback)` ile iç içe referans (token → token).
- `notes.md` notunun dosyaya uygulanması + `✓` imzası.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | Seçici/kural yazımı (`.sel { }`) — yalnız `:root`/`[data-*]` sarmalayıcı | `css-template` §4.1 #1 |
| 2 | Bileşen/sayfa stili | `css-template` §3.1 ayrım |
| 3 | `@import` (bu şablon konu token'ı için) | katman saflığı — import zinciri `08_Devices`'te |
| 4 | Figma'da olmayan token uydurma | `⚠️ VERIFICATION REQUIRED` |
| 5 | Dosya içinde MUTLAK toplam envanter sayısı | C4 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `.ai/ui-design/tokens/*.json` | Figma SSOT — değerler buradan okunur |
| `notes.md` | uygulanan not `✓` ile imzalanır |
| Tüketenler | `02`–`11` tüm katmanlar `var(--...)` ile okur |
| [[css-device-token]] (cihaz ailesi) | bu şablonla **aynı katmanda kardeş**; çelişirse base kazanır |

## Import Rules (Import Kuralları)

- Bu dosya **import etmez** (konu token dosyası) — yalnız `08_Devices/d-*.css` ve/veya
  `auth-bundled.css` tarafından import edilir `?v={{v}}` ile.
- Sıra: konu token dosyaları `01_Abstracts` içinde alfabetik/tematik değil, **bağımlılık sırası** ile
  yüklenir (font → renk → semantik → breakpoint); ayrı dosya zincirini ilgili cihaz dosyası belirler.

## Naming Rules (Adlandırma)

| Kural | Kalıp |
|-------|-------|
| Dosya | `a-{konu}-token.css` veya `a-{konu}-tokens.css` (çoğul mevcut adlarda) |
| Token adı | `--{kategori}-{ad}` → `--text-base`, `--bg-primary`, `--touch-min` |
| Cihaz token'ı bu şablonda DEĞİL | → [[css-device-token-template]] |

## Device Rules (Cihaz Kuralları)

- Bu şablon **cihaz bağımsız** konu token'ını yazar; cihaz genişliğine özel değer
  `a-layout-tokens-{width}.css` içine gider ([[css-device-token-template]]).
- `@media` sarmalayıcı yalnız breakpoint token'ı gibi gerçekten cihaz-genişlik değerlerinde kullanılır
  (ör. `a-design-tokens.css` L191-242 — ⚠️ bu dosyadaki 44px değerleri C2'ye aykırı, rapor §R).

## Responsive Rules (Responsive)

- Breakpoint değerleri `a-breakpoint-tokens.css` içinde tek yerde tanımlanır; başka konu token dosyası
  breakpoint **tanımlamaz** (tekrar = iki gerçek / split-brain).
- `@media` içinde `var()` okunamaz (CSS kısıtı) → medya genişlikleri token değeriyle eşleştirilir.

## Token Rules (Token Kuralları)

1. Yeni token yalnız bu katmanda (C6) — `06`/`10`/`04`/`05`/`08` token **üretemez**.
2. Taban `--touch-min: 48px` (C2 — brain.md:808-809; `Css/AGENTS.md` §3.1 ≥48px).
3. Tema override yalnız `[data-theme]`/`[data-accent]` seviyesinde (ADR-044).
4. Her değer Figma/SSOT kaynağına dayanır; kaynak yoksa `⚠️ VERIFICATION REQUIRED`.
5. Token önce tanımlanır, sonra tüketilir (`css-template` §4.2).

## Validation (Doğrulama)

- [ ] Dosya `a-{konu}-token(s).css` kalıbında
- [ ] Yalnız `:root`/`[data-*]`/`@media` sarmalayıcı — sınıf seçici yok
- [ ] Ham hex/px var (bu katmanda normal) ve başka katmana sızmamış
- [ ] `--touch-min` taban 48px (C2); 44px yok
- [ ] Figma kaynağı doğrulandı / `⚠️ VERIFICATION REQUIRED` işaretli
- [ ] İlgili `d-*.css` / `auth-bundled.css` bu dosyayı `?v=` ile import ediyor
- [ ] MUTLAK dosya sayısı yazılmadı (C4)

## Example Structure (Örnek Yapı)

```css
/**
 * 01_Abstracts/a-{{konu}}-token.css
 * KAYNAK : .ai/ui-design/tokens/ (Figma SSOT)
 * ROL    : tek konunun tokenları — seçici/kural YOK
 * NOT    : notes.md notu uygulanır + ✓ imzalanır
 */

:root {
  /* === GENEL ÖLÇÜ === */
  --header-h: 60px;            /* 2026-10-06 ölçümü — Figma SSOT */
  --grid-gap: 8px;

  /* === SPACING (4px ızgara) === */
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;

  /* === TOUCH (WCAG 2.2 — C2: taban 48px) === */
  --touch-min: 48px;
  --touch-recommended: 56px;

  /* === TİPOGRAFİ === */
  --text-base: var(--fs-base, 12px);
  --text-lg:   var(--fs-lg, 14px);

  /* === Z-INDEX === */
  --z-header: 100;
  --z-modal: 300;
  --z-toast: 400;
}

/* Tema — ADR-044: yalnız token değeri değişir */
[data-theme="dark"] { --bg-primary: #0b1120; --text-primary: #f1f5f9; }
```

---

**Template Version:** 2.0.0 · **Last Updated:** 2026-10-06

**Rapor (§R):** `01_Abstracts/a-design-tokens.css` L211/221/232 → `--touch-min: 44px` (C2 ihlali,
dokunulmadı).
