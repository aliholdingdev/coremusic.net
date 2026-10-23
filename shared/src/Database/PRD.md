---
title: "CoreMusic — Database PRD (DB Erişim Katmanı Gereksinim Belgesi)"
type: prd
category: data
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/Database — PRD

**docType:** prd · **Modül:** `shared/src/Database/` · **Yüzey:** #13 (aile indeksi §5) · **Sorumlu:** Data Engineer (+ Backend Architect)

**Bağlı platform gereksinimleri:** PRD-PLT-007

---

## 1. Amaç

Bu PRD, `Database/` modülünün **DB erişim katmanı** gereksinimlerini tanımlar: `DatabaseManager`, `DatabaseRegistry` (ADR-003/ADR-022). Amaç, tüm sistemin **tek DB kapısı** üzerinden, BCNF veri bütünlüğü (PRD-PLT-007) ve PDO prepared statement (ADR-002) disipliniyle çalışmasıdır.

> **eli10 (basit):** Bu dosya, veritabanına giden tek kapının kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü ikinci bağlantı açmak transaction/isolation bütünlüğünü kırar; gereksinimi yoksa herkes kendi PDO'sunu açar.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Database/**` (Manager + Registry) | Şema metinleri → `.ai/.sql/**` (#25) · migrations → #23 |
| Bağlentı havuzu + registry eşlemesi | Repository implementasyonları → `src/Repository/` |
| `SELECT *` / ORM yasağı denetimi | Subdomain sorguları → ilgili subdomain PRD |

- **Dokunulmaz:** ORM yasak (ADR-002) · frozen ADR metinleri.

> **eli10 (basit):** Kapsam bağlantı kapısı; şema ve migration ayrı PRD'de.
> **eli15 (detay):** Sınır, şemanın kod içine gömülmesini engeller — şema kasada, erişim kapıdadır.

---

## 3. Gereksinimler (PRD-DB-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-DB-001 | DB bağlantısı yalnız `DatabaseManager` üzerinden açılır | Modül dışı `new PDO` 0 |
| PRD-DB-002 | Prepared statement zorunlu; `SELECT *` yasak | Ham string sorgu yok |
| PRD-DB-003 | Registry eşlemesi (DB adı → bağlantı) eksiksiz | Kayıtsız DB erişilemez (ADR-003/022) |
| PRD-DB-004 | Migration dışı şema değişikliği yasak (PRD-PLT-007) | Şema değişikliği migration ile |

> **eli10 (basit):** Dört kural: tek kapı, hazır statement, kayıtlı DB, migration'lı şema.
> **eli15 (detay):** Kimlikler ayrı çünkü DB ihlali veri kaybı/injection üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | BCNF | PRD-PLT-007 — şema/kod tutarlılığı |
| 2 | Erişim | PRD-DB-001..003 — tek kapı + prepared |
| 3 | Migration | PRD-DB-004 — şema değişikliği migration'dan geçer |
| 4 | Test | `tests/Repository` + `tests/Unit/...` yeşil |
| 5 | Kanıt | `⚠️ VERIFICATION REQUIRED` disiplini |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| ORM yasak / PDO | ADR-002 |
| DatabaseManager/Registry | ADR-003 · ADR-022 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | İkinci bağlantı / doğrudan PDO | Transaction izolasyonu kırığı | PRD-DB-001 |
| 2 | `SELECT *` / string sorgu | Injection + kırılgan şema | PRD-DB-002 |
| 3 | Koda gömülü ALTER | BCNF drift'i | PRD-DB-004 |

---

## 7. Sonraki Adım

1. #23 (migrations) ve #25 (`.sql` kasası) PRD'leri ile şema sözleşmesi derinleştirilir.
2. #14 (OAuth) ve #17 (Session) PRD'leri DB kullanımlarını çapraz kontrol eder.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
