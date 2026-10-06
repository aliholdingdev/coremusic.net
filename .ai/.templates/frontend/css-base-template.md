---
title: "CoreMusic — CSS Base Şablonu (02_Base)"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 1.0.0
status: active
authority: reference
---

# CSS Base Şablonu — `02_Base/`

**Kapsam:** Bare HTML reset, base giriş iskeleti, ana yapısal iskelet · önek `b-` / `l-` / `page-`
**Ana şablon:** [[css-template]] §3.1 · **Gate:** Guardrail #16
**YENİ DOSYA (2026-10-06) — önceki oturumda raporlanıp diske düşmemişti.**

---

## Purpose (Amaç)

Sayfa başına değil, **site geneline** ait çıplak HTML temelini yazmak: reset, typography, gövde
varsayılanları, ana yapısal iskelet. Bu katman token **tüketir**, üretmez.

## Location (Konum)

`assets.coremusic.net/Css/02_Base/`
Örnek dosyalar (2026-10-06 ölçümü — envanter iddiası değil): `b-base-core.css` ·
`l-main-structural.css` · `page-layout.css`

## Responsibility (Sorumluluk)

- Bare HTML (`html`, `body`, `h1–h6`, `p`, `a`, `img`, `ul`) reset/temel kuralları.
- `l-` = genel yapısal iskelet (main içerik kabı), `page-` = sayfa iskeleti şablonu.
- `03_Layout`'ın (header/footer/sidebar) DEĞİL, genel giriş iskeletinin sorumluluğu.

## Allowed (İzinli)

- Bare element seçiciler + element grupları (`h1, h2 { … }`).
- `var(--token)` tüketimi; `@media` ile yalnız base davranışı (ör. `html { font-size }` gerekçeli ise).
- `:focus-visible` genel kuralı, `::selection`, `:root` z-index olmayan base varsayılanlar.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | **`p-` öneki** (`p-` münhasır `05_Pages`) | C3 — Expert kararı |
| 2 | `--token` tanımı (token → `01_Abstracts`) | `css-template` §4.1 #9 |
| 3 | Bileşen stili (buton/kart yüzü) → `04_Components` | §3.1 ayrım |
| 4 | Header/footer/sidebar kuralı → `03_Layout` | §3.1 ayrım |
| 5 | Ham hex/px (01 dışında) | `Css/CLAUDE.md` §4.1 #1 |
| 6 | `!important` (cap 3 — bu katmanda varsayılan 0) | `Css/CLAUDE.md` §4.1 #2 |

## Dependencies (Bağımlılıklar)

| Bağımlılık | Not |
|------------|-----|
| `01_Abstracts/*` | token kaynağı (bu dosya import etmez; cihaz zinciri yükler) |
| `07_Vendors` reboot | **çakışma biliniyor:** Bootstrap reboot bu katmanı ezerse diye cihaz dosyasında bilinçli sıralama (d-4k/d-desktop/d-laptop reboot+grid import eder; phone/tablet/embedded reboot+grid) — ⚠️ VERIFICATION REQUIRED: cihaz bazlı bootstrap import seti `devices.config.js`/`DeviceCssMap.php` ile doğrulanır |
| Tüketen | tüm cihaz import zinciri (`08_Devices`), `auth-bundled.css` |

## Import Rules (Import Kuralları)

- Bu katman dosyaları **import etmez** (base saflığı) — kendisi `01_Abstracts`'ten sonra import edilir.
- Sıra: `01 → 02` (ör. `@import url("../02_Base/b-base-core.css?v={{v}}");` cihaz dosyasında).
- `auth-bundled.css` Grup 2 = `b-base-core.css` (reset) — grup sırası `01 → 02 → 05 → 08`.

## Naming Rules (Adlandırma)

| Önek | Kalıp | Dosya (2026-10-06 ölçümü) |
|------|-------|---------------------------|
| `b-` | `b-base-{konu}.css` | `b-base-core.css` |
| `l-` | `l-{konu}-structural.css` | `l-main-structural.css` |
| `page-` | `page-{konu}.css` | `page-layout.css` |

**`p-` bu katmanda YASAK** (C3): `p-` öneki münhasıran `05_Pages` katmanına aittir;
`page-layout.css` dosya adındaki `page-` öneki `p-` ile karıştırılmaz (tam metin `page-`).

## Device Rules (Cihaz Kuralları)

- Base dosyası cihaz-specific yazılmaz; cihaz uyarlaması `08_Devices` davranış alanında.
- Tek istisna (gerekçeli): `html { font-size }` tipi global ölçek davranışı — o da cihaz dosyasında.

## Responsive Rules (Responsive)

- Base katman breakpoint sorgusu **yazmaz**; base'den sonra yüklenen cihaz/token katmanı ölçeği yönetir.
- Taşma koruması (`overflow-x`) base'e değil cihaz davranışına aittir (`08_Devices`).

## Token Rules (Token Kuralları)

- Yalnız tüketim: `var(--space-md)`, `var(--text-base)`, `var(--touch-min)` (≥48px — C2).
- Yeni token tanımı yasak (C6 → yalnız `01_Abstracts`).

## Validation (Doğrulama)

- [ ] Dosya adı `b-` / `l-` / `page-` kalıbında; `p-` yok (C3)
- [ ] Token tanımı yok
- [ ] Bileşen/header/footer kuralı yok
- [ ] Sabit ham değer yok (gerekçeli istisna yorumda)
- [ ] `08_Devices` import zincirinde sırası `01`'den sonra
- [ ] Tarayıcıda reset etkisi doğrulandı (gerçek sayfa)

## Example Structure (Örnek Yapı)

```css
/**
 * 02_Base/b-base-{{konu}}.css
 * ROL    : bare HTML temeli — site geneli
 * TOKEN  : 01_Abstracts'ten tüketir; TANIMLAMAZ
 * YASAK  : p- öneki (C3) · bileşen yüzü · header/footer kuralı
 */

html {
  -webkit-text-size-adjust: 100%;
  scroll-behavior: smooth;
}

body {
  margin: 0;
  font-family: var(--font-sans);
  font-size: var(--text-base);
  color: var(--text-primary);
  background: var(--bg-primary);
  min-height: 100vh;
}

h1, h2, h3, h4, h5, h6 {
  margin: 0 0 var(--space-sm);
  font-weight: var(--fw-semibold);
  line-height: var(--lh-tight);
}

a {
  color: var(--color-link);
  text-decoration: none;
}

img, svg, video {
  max-width: 100%;
  height: auto;
  display: block;
}

:focus-visible {
  outline: 2px solid var(--color-accent);
  outline-offset: 2px;
}
```

```css
/**
 * 02_Base/page-{{konu}}.css — sayfa iskeleti şablonu (page- öneki; p- DEĞİL — C3)
 */
.page-shell {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
```

---

**Template Version:** 1.0.0 · **Last Updated:** 2026-10-06
