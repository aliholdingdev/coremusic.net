---
title: "CoreMusic — Api PRD (Ortak API Katmanı Gereksinim Belgesi)"
type: prd
category: shared
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/Api — PRD

**docType:** prd · **Modül:** `shared/src/Api/` · **Yüzey:** #11 (aile indeksi §5) · **Sorumlu:** Backend Architect (+ Security denetimi)

**Bağlı platform gereksinimleri:** PRD-PLT-006 · PRD-PLT-008

---

## 1. Amaç

Bu PRD, `Api/` modülünün **ortak API katmanı** gereksinimlerini tanımlar: Gateway, `ApiRequest`/`ApiRespons`e, DTO'lar, 6 API middleware (Auth, Authorization, RateLimit, Validation, ...), `ServiceRegistry` + sağlık tanımları, versiyonlama (`ApiVersion`, `VersionRegistry`, `VersionResolver`). `src/Api/Bff/` DECOMMISSIONED 2026-10-18 (ADR-084).

> **eli10 (basit):** Bu dosya, API'nin ortak iskeletinin (istek/yanıt, sürüm, servis listesi) kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü gateway tüm uçların şekil standardıdır; gereksinimi yoksa her uç
> kendi DTO/versiyonunu icat eder. Okunması, uç eklerken standardı verir.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Api/**` (Gateway, Dto, Middleware, Registry, Versioning) | Uç listesi → `config/routes.php` (#24) · api PRD (#2) |
| Servis kayıt/health tanımları | Ortağın HTTP middleware sırası → #10 |
| Versiyon çözümleme | DB şeması → #13/#25 |

- **Ölçüm notu:** `src/Api/Bff/` DECOMMISSIONED 2026-10-18 (ADR-084 — 0 dış referans); `BffInterface` dahil silindi.
- **Dokunulmaz:** frozen ADR metinleri.

> **eli10 (basit):** Kapsam API iskeleti; adresler ve DB ayrı PRD'de.
> **eli15 (detay):** Sınır, iskeletin uç listesini taşımasını engeller — iskelet mekanik, liste config'dedir.

---

## 3. Gereksinimler (PRD-API-M001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-API-M001 | Tüm uçlar ApiRequest/Response + DTO ile konuşur | Ham array/echo yok; şekil standardı |
| PRD-API-M002 | Versiyonlama `VersionResolver` üzerinden | Uç sürümü kayıt dışı değil; kırıcı sürüm yeni versiyon |
| PRD-API-M003 | Validation middleware'i ihmal edilmez | Geçersiz istek 4xx; iş koda girmeden reddedilir |
| PRD-API-M004 | ServiceRegistry sağlık tanımları güncel | Kayıtsız servis yok; health çağrısı cevap verir |

> **eli10 (basit):** Dört kural: standart şekil, sürümlü uç, doğrulanmış giriş, kayıtlı servis.
> **eli15 (detay):** Kimlikler ayrı çünkü gateway ihlali tüm API'ye yansır; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Şekil | PRD-API-M001/003 — DTO + validation |
| 2 | Sürüm | PRD-API-M002 — resolver |
| 3 | Güvenlik | Auth/Authorization middleware — Security PASS (PRD-PLT-006) |
| 4 | Test | `tests/Api` + `tests/Unit/...` yeşil |
| 5 | Kanıt | `⚠️ VERIFICATION REQUIRED` disiplini |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Bff decommission | ADR-084 (bağ) |
| Middleware sırası | ADR-020 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | DTO'suz/ doğrulamasız uç | Şekil + güvenlik ihlali | PRD-API-M001/003 |
| 2 | Sürüm atlayarak kırıcı değişiklik | İstemci kırılması | PRD-API-M002 |
| 3 | Bff'in geri eklenmesi | Ölü kod / ADR ihlali | §1 decommission kaydı + ADR-084 |

---

## 7. Sonraki Adım

1. #2 (api subdomain PRD) ile gateway↔uç sözleşmesi çapraz kontrol edilir.
2. #10 (Middleware PRD) ile API middleware sırası hizalanır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
