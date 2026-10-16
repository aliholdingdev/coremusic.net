---
node_id: "K007-data-quality-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - "shared/tests/Repository/ — Repository testleri (2026-10-15 enumerate)"
  - ".ai/.sql/CLAUDE.md §1 — proje kanunları listesi"
  - "composer.json — phpunit/phpstan bağımlılıkları"
validation: "2026-10-15 — envanter + kanun listesi diskten"
open_questions:
  - "PHPStan seviyesi (level) → okunmadı → UNKNOWN"
group: mimari
---

# data-quality — Bileşenler (K007)

## §1 Bugün çalışan katmanlar (envanter)

| # | Katman | Bileşen | Kanıt | Durum |
|---|--------|---------|-------|-------|
| 1 | Bütünlük | NOT NULL/UNIQUE/FK/CHECK | dump'larda constraint'ler | ✅ |
| 2 | Geçerlilik | hazırlama/doğrulama API'leri | ApiKeyRepository | ✅ |
| 3 | Test | SQLite in-memory + fixture | ApiKeyRepositoryTest | ✅ |
| 4 | Statik analiz | PHPStan / kod kalitesi | composer | ✅ (seviye UNKNOWN) |

## §2 Proje kanunları (veri kalitesi kuralları)

| # | Kanun | Kaynak |
|---|-------|--------|
| 1 | LIKE sorgusu tespiti — proje kanunu | .ai/.sql/CLAUDE.md §1 |
| 2 | SELECT * yasak | ADR-002 |
| 3 | Parametreli sorgu zorunlu | ADR-002 |
| 4 | ENUM kolon değerleri canlıya sabit | master §4 canlı eşleme |

## §3 Olmadığı (gösterilen) katman

| Katman | Aday | Durum |
|--------|------|-------|
| Profesyonel veri doğrulama framework'ü | Great Expectations / dbt tests | ❌ repo'da YOK → K-05 |

---

**Authority:** Bayram Ali / Vault Steward