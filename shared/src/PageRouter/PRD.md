---
title: "CoreMusic — PageRouter PRD (SPA Router Çekirdeği Gereksinim Belgesi)"
type: prd
category: shared
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/PageRouter — PRD

**docType:** prd · **Modül:** `shared/src/PageRouter/` · **Yüzey:** #9 (aile indeksi §5) · **Sorumlu:** Backend Architect

**Bağlı platform gereksinimleri:** PRD-PLT-008

---

## 1. Amaç

Bu PRD, `PageRouter/` modülünün **SPA sayfa yönlendirme çekirdeği** gereksinimlerini tanımlar: `Kernel`, `HtmlShellRenderer`, `AuthGuard`, `RouteRegistry`, `RequestNormalizer`, `ResponseEmitter` (14 dosya). Tüm panellerin tek router deseni bu modülden geçer (ADR-021/ADR-083); hedef, tek ve öngörülebilir route sözleşmesidir.

> **eli10 (basit):** Bu dosya, sitenin "hangi adres hangi sayfa" haritasının çekirdek kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü router tüm subdomain'lerin sayfa motorudur; gereksinimi yoksa
> her panel kendi router'ını icat eder (PRD-PLT-008 ihlali). Okunması, route eklerken sözleşmeyi verir.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/PageRouter/**` (14 dosya — Kernel, AuthGuard, RouteRegistry, ...) | Subdomain route tanımları → `config/routes.php` (#24) |
| Route sözleşmesi + shell render + response emit | Frontend router (`assets/js/router`) → #19 |
| AuthGuard entegrasyonu (auth devri) | Auth akışı içeriği → auth PRD (#4/#22) |

- **Ön koşul:** ADR-021 / ADR-083 (SPA router deseni); AuthGuard `HomeAuthBridge`/auth köprüsüyle konuşur.
- **Dokunulmaz:** frozen ADR metinleri.

> **eli10 (basit):** Kapsam çekirdek router kodu; adres listesi ve JS tarafı dışarıda.
> **eli15 (detay):** Sınır, çekirdeğin uygulama adres listesini taşımasını engeller — çekirdek mekanik, liste config'dedir.

---

## 3. Gereksinimler (PRD-PRT-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-PRT-001 | Tüm paneller tek router deseni kullanır (PRD-PLT-008) | Yeni sayfa `RouteRegistry` üzerinden; ikinci mekanizma yok |
| PRD-PRT-002 | Kernel → RequestNormalizer → AuthGuard → ResponseEmitter zinciri korunur | Sıra değişikliği yalnız ADR ile |
| PRD-PRT-003 | AuthGuard'sız korumalı route render edilmez | Korumalı route erişiminde auth devri çalışır |
| PRD-PRT-004 | Shell render (HtmlShellRenderer) token'a sadık | `--cm-*` token/asset çağrısı assets'ten; inline üretim yok |

> **eli10 (basit):** Dört kural: tek desen, sabit sıra, korumasız render yok, shell asset çağrısı doğru yerden.
> **eli15 (detay):** Kimlikler ayrı çünkü router ihlali tüm panellere yansır; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Sözleşme | PRD-PRT-001/002 — desen + sıra |
| 2 | Güvenlik | PRD-PRT-003 — AuthGuard; Security denetimi PASS |
| 3 | Test | Modül testleri (`tests/Unit/PageRouter`) yeşil |
| 4 | Kanıt | Ölçülmeyen davranış iddiası `⚠️ VERIFICATION REQUIRED` |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| SPA router deseni | ADR-021 · ADR-083 |
| Auth bypass kapsamı | ADR-008 (AuthGuard sınırı) |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Panelin kendi router mekanizması | Sürüm dağılması | PRD-PRT-001 |
| 2 | Guard sırasının değişmesi | Korumasız render | PRD-PRT-002/003 |

---

## 7. Sonraki Adım

1. #19 (js/router alt PRD) ile frontend çekirdeği çapraz kontrol edilir.
2. #24 (config PRD) ile `routes.php` sözleşmesi doğrulanır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
