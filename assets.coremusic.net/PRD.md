---
title: "CoreMusic — assets.coremusic.net PRD (Statik Asset Servisi Gereksinim Belgesi)"
type: prd
category: frontend
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] (envanter) · [[AGENTS.md]] (roller)"
updated: 2026-10-23
---

# assets.coremusic.net — PRD

**docType:** prd · **Klasör:** `assets.coremusic.net/` · **Yüzey:** #3 (aile indeksi §5) · **Sorumlu:** UI Designer (+ accessibility denetimi)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-009 · PRD-PLT-010

---

## 1. Amaç

Bu PRD, `assets.coremusic.net/` yüzeyinin **statik asset servisi** gereksinimlerini tanımlar: tüm subdomain'lerin CSS/JS/font/görsel varlıklarının üretimi, ITCSS + BEM + `--cm-*` token disiplini, Vanilla JS (framework yasağı), CSP uyumu ve cihaz (device) CSS katmanı. Bu klasör ADR-001'in **tek uygulama noktasıdır**; PHP sayfa sunumu yapmaz.

> **eli10 (basit):** Bu dosya, sitenin görünüm ve davranış dosyalarının kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü tüm subdomain'ler aynı CSS/JS'i paylaştığından buradaki hata
> sistemin tamamına yayılır. Okunması, token/katman/CSP sınırını üretim öncesi verir. Yazılması,
> "hangi katmana yazılır" sorusunu kapatır. Olmazsa her ekip kendi CSS'ini üretir, tema dağılır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `Css/**` (ITCSS 11 katman — şablon: `.ai/.templates/frontend/css-template.md` v4.0.0) | PHP sayfa üretimi (home/api PRD'leri) |
| `js/**` (giriş noktaları, router, features, managers) — alt PRD'ler #18–#20 | Token **sözlüğü** metni → `.ai/ui-design/tokens/**` (#26) |
| `Fonts/**`, `Image/**` (varlık envanteri) | Görsel tasarım kararı → mockup (`[[../.ai/ui-design/01-mockup-index.md]]`) |
| Statik servis `.htaccess` / `web.config` (cache, MIME) | Şema/migration → `.ai/.sql/**` (#25) |

- **Ölçüm (disk kanıtı, 2026-10-12):** **465 dosya** (`node_modules/` ve `test-results/` hariç); üst girdi **15 = 8 dosya + 7 dizin** — sayı `[[CONTEXT.md]]` §1'dedir, burada tekrarlanmaz.
- **Ön koşul:** Mockup gate — UI kodundan önce `[[../.ai/ui-design/01-mockup-index.md]]` + ilgili PNG okunur (Guardrail #11); CSS görevi öncesi `css-template.md` (v4.0.0) okunur (kök `AGENTS.md` §10).
- **Dokunulmaz:** `Css/07_Vendors/**` (salt okunur) · `Css/main.css` diskte YOK (2026-09-30 silindi — yeniden üretilmez) · `.ai/.png/**` · frozen ADR metinleri.

> **eli10 (basit):** Kapsam CSS/JS/font/görsel; tasarım kararı ve PHP dışarıda.
> **eli15 (detay):** Sınır, tasarım dosyası ile kod dosyasının birbirini ezmesini engeller — tasarım
> önce, kod sonra akar. Sınır yoksa token ham değer olarak katmana sızar ve tema kırılır.

---

## 3. Gereksinimler (PRD-AST-001..006)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-AST-001 | Sabit değer yalnız `01_Abstracts` içinde `--cm-*` token'ı olur | Katman dosyasında ham hex/px yok; `@media` içinde `var()` **yok** (şablon kuralı) |
| PRD-AST-002 | ITCSS katman sırası + BEM adlandırması korunur | Sıra: 01→11; format `block__element--modifier`; taşıma/silme onaysız YOK |
| PRD-AST-003 | Vanilla JS: `var`/`eval`/`innerHTML` yasak | ES6+; DOMParser + TrustedTypes; CSP nonce uyumlu inline üretim YOK |
| PRD-AST-004 | Device CSS ekleme iki kayıtla senkron kalır | `js/devices.config.js` + `DeviceCssMap.php` (shared) ikisi de güncel |
| PRD-AST-005 | WCAG 2.2 AA + touch ≥24×24px + görünür focus 3px | Component State Matrix (8 durum); `accessibility-tester` PASS |
| PRD-AST-006 | Yeni UI kodu mockup'a sadıktır | İlgili PNG okunmadıysa DUR (Guardrail #11); mockupta olmayan öge eklenmez |

> **eli10 (basit):** Altı kural: token tek yerden, katman sırası sabit, saf JS, senkron cihaz kaydı, erişilebilirlik, mockup'a bağlılık.
> **eli15 (detay):** Kimlikler ayrı çünkü CSS/JS ihlali görsel ve erişilebilirlik kusuru üretir; denetlenebilir
> olmalıdır. Okunması, dosya yazım öncesi kontrol verir. Kimliksiz kural "stil tercihi"ne döner ve
> `accessibility-tester` hedefi olmaz.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Şablon | `css-template.md` v4.0.0 okundu (11 katman) — Guardrail #16 |
| 2 | Mockup | `01-mockup-index` + PNG okundu (Guardrail #11) |
| 3 | Token | PRD-AST-001 — ham değer yalnız `01_Abstracts` |
| 4 | Erişilebilirlik | PRD-AST-005 — `accessibility-tester` PASS (PRD-PLT-010) |
| 5 | Kanıt | Ölçülmeyen breakpoint/token iddiası `⚠️ VERIFICATION REQUIRED` (PRD-PLT-011); 3840/TV token dosyaları **boş** → `⚠️` taşınır |

> **eli10 (basit):** Beş kapı: şablon, mockup, token, erişilebilirlik, kanıt.
> **eli15 (detay):** Kapılar üretimden ayrıdır; denetim üretime karışmaz. Okunması, G8 raporu için
> checklist verir. Kapı yoksa "uyumlu" iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| Vanilla JS + ITCSS (framework yasağı) | ADR-001 | PRD-AST-002/003 |
| CSP/nonce uyumu | ADR-012 | PRD-AST-003 |
| SPA router deseni (js/router) | ADR-021 / ADR-083 | Alt PRD #19 |
| Token/ekran SSOT | `[[../.ai/ui-design/01-mockup-index.md]]` + tokens (#26) | PRD-AST-001 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Katman/taşma değişikliğinde |

> **eli10 (basit):** Mimari kararlar ADR'de; bu PRD yalnız bağlar.
> **eli15 (detay):** Bağ, frozen ADR metninin tek sürüm kalmasını sağlar; kopya eskir ve çelişir.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Ham hex/px'in katmana sızması | Token drift, tema kırılması | PRD-AST-001 + şablon kapısı |
| 2 | Device CSS ↔ `DeviceCssMap.php` drift'i | Cihazda yanlış layout | PRD-AST-004 — iki kayıt senkronu |
| 3 | Mockupsız kod | Tasarım sapması | PRD-AST-006 + Guardrail #11 DUR |
| 4 | `main.css`'in "eski yerde" varsayılması | Kırık import zinciri | Dokunulmazlık: dosya YOK; tek giriş `08_Devices` + `auth-bundled.css` |
| 5 | Boş 3840/TV token'ından tahmini değer üretimi | Uydurma token (PRD-PLT-011 ihlali) | `⚠️ VERIFICATION REQUIRED` + türetme user kuralı/`05-responsive-architecture` üzerinden |

> **eli10 (basit):** En büyük tehlike: kural dışı yerde değer üretmek ve olmayan dosyayı aramak.
> **eli15 (detay):** Risklerin azaltması farklı kapıya bağlıdır; kapılar atlanırsa risk geri döner.

---

## 7. Sonraki Adım

1. #18–#20 (js/coreplayer · js/router · js çekirdek) ve #21 (Css) alt PRD'leri bu dosyaya bağlanır.
2. Token/ekran gereksinimleri #26 (`.ai/ui-design` PRD) ile çapraz kontrol edilir.
3. Erişilebilirlik denetimi `accessibility-tester` → PASS kaydı bu PRD §4 kapısına eklenir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
