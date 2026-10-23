---
title: "CoreMusic — Cache PRD (Cache Adapter Katmanı Gereksinim Belgesi)"
type: prd
category: shared
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/Cache — PRD

**docType:** prd · **Modül:** `shared/src/Cache/` · **Yüzey:** #16 (aile indeksi §5) · **Sorumlu:** Backend Architect (+ performance denetimi)

**Bağlı platform gereksinimleri:** PRD-PLT-005

---

## 1. Amaç

Bu PRD, `Cache/` modülünün gereksinimlerini tanımlar: `Apcu`, `Memory`, `PageCache` adaptörleri (ADR-007). Amaç, ortak cache katmanının **tek arayüzle**, TTL ve fail-safe davranışla çalışmasıdır (ör. RateLimit sayaçları PRD-SEC-004'e bağlıdır).

> **eli10 (basit):** Bu dosya, geçici bellek kutularının (cache) kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü cache tutarsızlığı sessiz veri bozulması üretir; gereksinimi yoksa herkes kendi cache'ini açar.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Cache/**` (3 adaptör) | Cache'lenen verinin kaynağı → ilgili modül |
| PSR cache arayüzü kullanımı | PageCache'in sayfa üretimiyle ilişkisi → #9 (PageRouter) |

- **Dokunulmaz:** frozen ADR metinleri.

> **eli10 (basit):** Kapsam cache adaptörleri; neyin cache'leneceği başka yerde.
> **eli15 (detay):** Sınır, kaynağın adaptöre gömülmesini engeller — adaptör genel, kaynak özeldir.

---

## 3. Gereksinimler (PRD-CAC-001..003)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-CAC-001 | Tüm adaptörler ortak arayüz üzerinden kullanılır | Adaptöre özel çağrı kod içinde parçalanmaz |
| PRD-CAC-002 | TTL zorunlu; sonsuz cache yok | Her yazmada/expiry kararı |
| PRD-CAC-003 | Cache kaybı/çökmesi fail-safe (PRD-SEC-004 ile uyum) | Kaynak çağrısına düşer; fail-open kapı yok |

> **eli10 (basit):** Üç kural: ortak arayüz, süreli kayıt, güvenli düşüş.
> **eli15 (detay):** Kimlikler ayrı çünkü cache ihlali veri tutarsızlığı üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Arayüz | PRD-CAC-001 — ortak kullanım |
| 2 | TTL/fail-safe | PRD-CAC-002/003 |
| 3 | Test | `tests/Unit/...` cache testleri yeşil |
| 4 | Kanıt | `⚠️ VERIFICATION REQUIRED` disiplini |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Cache stratejisi | ADR-007 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Sonsuz TTL | Bayat veri | PRD-CAC-002 |
| 2 | Fail-open kapı (rate limit) | Brute-force | PRD-CAC-003 + PRD-SEC-004 |

---

## 7. Sonraki Adım

1. #12 (Security PRD) ile CacheRateLimiter kullanımı çapraz kontrol edilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
