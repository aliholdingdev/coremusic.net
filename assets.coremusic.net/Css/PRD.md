---
title: "CoreMusic — Css PRD (ITCSS Stil Katmanları Gereksinim Belgesi)"
type: prd
category: frontend
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../PRD.md]] (assets) · şablon: css-template.md v4.0.0"
updated: 2026-10-23
---

# assets.coremusic.net/Css — PRD

**docType:** prd · **Klasör:** `Css/` · **Yüzey:** #21 (aile indeksi §5) · **Sorumlu:** UI Designer (+ accessibility denetimi)

**Bağlı platform gereksinimleri:** PRD-PLT-009 · PRD-PLT-010

---

## 1. Amaç

Bu PRD, `Css/` katmanının gereksinimlerini tanımlar: ITCSS 11 katman (01_Abstracts → 11_OAuth), `--cm-*` token disiplini, BEM adlandırması, cihaz CSS'i (08_Devices) ve view-mode stilleri. ADR-001'in stil yüzü; **tek giriş noktaları** `Css/08_Devices/` + `Css/auth-bundled.css`'tir (`Css/main.css` YOK — 2026-09-30 silindi).

> **eli10 (basit):** Bu dosya, sitenin görünüm dosyalarının katman ve token kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü CSS tek dağılım katmanıdır; gereksinimi yoksa ham renk/ölçü her dosyaya serpilir, tema değiştirilemez olur (PRD-PLT-009 ihlali).

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `Css/01_Abstracts` … `11_OAuth` (11 katman) | Token **sözlüğü** metni → `.ai/ui-design/tokens/**` (#26) |
| `01_Abstracts` token dosyaları (19 — BASE: `a-layout-tokens-1024.css`) | Font/görsel varlıkları → assets envanteri (#3) |
| `08_Devices` (15 cihaz CSS) + `auth-bundled.css` | JS davranış → #18–#20 |

- **Ölçüm (disk kanıtı, assets AGENTS.md envanteri):** 04_Components 15 dosya · 05_Pages 12 dosya · 08_Devices 15 dosya · 09_ViewModes 4 dosya · 07_Vendors salt okunur · 2 kopya artığı (`a-layout-tokens copy*.css` — okunmaz).
- **Ön koşul:** Şablon okuması zorunlu — `.ai/.templates/frontend/css-template.md` **v4.0.0** (11 katman) + kök `AGENTS.md` §10; mockup gate (Guardrail #11).
- **Dokunulmaz:** `07_Vendors/**` · `Css/main.css` (YOK — yeniden üretilmez) · `a-layout-tokens.css` (YOK — BASE `a-layout-tokens-1024.css`) · taşma/silme onaysız yasak · frozen ADR metinleri.

> **eli10 (basit):** Kapsam 11 CSS katmanı; token metni ve JS ayrı.
> **eli15 (detay):** Sınır, yanlış katmana yazımı engeller — taşıma onaysız yasak, yanlış yerleşim raporlanır.

---

## 3. Gereksinimler (PRD-CSS-001..005)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-CSS-001 | Sabit değer yalnız `01_Abstracts` içinde `--cm-*` | Katmanda ham hex/px yok; `@media` içinde `var()` **yok** (şablon kuralı) |
| PRD-CSS-002 | ITCSS sırası + BEM korunur | 01→11 sırası; `block__element--modifier` |
| PRD-CSS-003 | Cihaz CSS'i iki kayıtla senkron | `devices.config.js` + `DeviceCssMap.php` (#18/#3) |
| PRD-CSS-004 | WCAG 2.2 AA + touch ≥24×24px + focus 3px (PRD-PLT-010) | State Matrix 8 durum; `accessibility-tester` PASS |
| PRD-CSS-005 | Giriş noktaları korunur | `08_Devices/` + `auth-bundled.css` tek giriş; `main.css` geri eklenmez |

> **eli10 (basit):** Beş kural: token tek yerden, katman sırası, senkron cihaz, erişilebilirlik, tek giriş.
> **eli15 (detay):** Kimlikler ayrı çünkü CSS ihlali görsel/erişilebilirlik kusuru üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Şablon | css-template v4.0.0 okundu (Guardrail #16) |
| 2 | Mockup | `01-mockup-index` + PNG okundu (Guardrail #11) |
| 3 | Token | PRD-CSS-001 |
| 4 | Erişilebilirlik | PRD-CSS-004 — accessibility PASS |
| 5 | Kanıt | 3840/TV token dosyaları **boş** → `⚠️ VERIFICATION REQUIRED`; uydurma token yasak |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Vanilla JS + ITCSS | ADR-001 |
| CSP (inline style yasağı) | ADR-012 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Ham hex/px sızması | Token drift | PRD-CSS-001 + şablon |
| 2 | `main.css`'in geri eklenmesi | Çifte giriş zinciri | PRD-CSS-005 + dokunulmazlık |
| 3 | Boş token'dan tahmin üretimi | Uydurma tasarım | PRD-CSS-005 — `⚠️` + user kuralı/`05-responsive-architecture` |
| 4 | Katmansız yeni dosya | ITCSS bozulması | PRD-CSS-002 + yanlış yerleşim raporu ( taşıma yasak) |

---

## 7. Sonraki Adım

1. #26 (ui-design PRD) ile token/ekran spec çapraz kontrol edilir.
2. 3840/TV katmanları: token boş → tasarım gelene kadar `status: planlanmış` + `⚠️`.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
