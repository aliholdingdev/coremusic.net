---
node_id: "K007-data-quality-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/repositories"
evidence:
  - ".ai/.sql/CLAUDE.md §1 — LIKE sorgusu tespiti · SELECT * yasağı · proje kanunları (2026-10-07)"
  - "shared/tests/Repository/ApiKeyRepositoryTest.php:6-9,23-30 — SQLite in-memory fixture (2026-10-12)"
  - "greatexpectations.io — HTTP 200 (2026-10-12)"
validation: "2026-10-15 — kapsam yazıldı; 2 repo kanıtı + 1 test altyapısı"
open_questions:
  - "Beklenen doğruluk (accuracy) eşiği → UNKNOWN"
  - "Veri okuma devamlılık (completeness) ölçümü → UNKNOWN"
group: mimari
---

# data-quality — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Şema düzeyi kısıt (NOT NULL/UNIQUE/FK/CHECK) | .ai/.sql dump'ları |
| 2 | Yazma doğrulama katmanı (hash-only, ENUM, JSON) | ApiKeyRepository docblock |
| 3 | Test güvencesi (SQLite in-memory + fixtura) | ApiKeyRepositoryTest.php |
| 4 | Proje kanunları (LIKE / SELECT * / LIMIT) | .ai/.sql/CLAUDE.md §1 |
| 5 | Profesyonel framework değerlendirmesi (Great Expectations vb.) | greatexpectations.io |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| Entegre test işletimi | K019-testing | burada yalnız veri güvencesi |
| PII/şifreleme doğruluğu | K009-security | güvenlik ≠ kalite |
| Ölçüm metrik yayını | K017-observability | yayım akışı |
| Veri üretimi/etiketleme | K006-yapay-zeka | model verisi |

## §3 Sınır cümlesi

data-quality, "veri beklendiği gibi mi?" sorusunun sahibidir: bütünlük (constraint),
geçerlilik (validation) ve tekrarlanabilirlik (test fixture) — üç merdiven.

---

**Authority:** Bayram Ali / Vault Steward