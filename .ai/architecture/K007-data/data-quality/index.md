---
node_id: data-quality
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - data-quality-kapsam
  - data-quality-bilesenler
  - data-quality-dogrulama
  - data-quality-ornekler
  - data-quality-web-arastirma
depends_on:
  - K007-data/schema
  - K007-data/repositories
evidence:
  - "v2 §2.7 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 287"
  - ".ai/.sql/CLAUDE.md §1 — proje kanunları"
  - "shared/tests/Repository/ApiKeyRepositoryTest.php — in-memory fixture"
validation:
  - "2026-10-15 — 5 içerik MD'si yazıldı; children disk enumerate 5/5"
open_questions:
  - "K-05 (Great Expectations/dbt tests) → UNKNOWN"
  - "Accuracy/completeness eşik değerleri → UNKNOWN"
group: mimari
---

# data-quality — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | data-quality |
| parent_id | K007-data |
| Kaynak | v2 §2.7 (satır 287) |
| Durum | draft |

## Ne işe yarar?

Verinin bütünlüğü, geçerliliği ve tekrarlanabilirliği: constraint'ler (bütünlük),
yazma doğrulaması (geçerlilik), test fixture'ı (tekrar). Çerçeve kararı K-05.

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [data-quality-kapsam.md](data-quality-kapsam.md) | sınır | mimari |
| 2 | [data-quality-bilesenler.md](data-quality-bilesenler.md) | katman envanteri + kanunlar | mimari |
| 3 | [data-quality-dogrulama.md](data-quality-dogrulama.md) | D-Q1…D-Q6 | kanit |
| 4 | [data-quality-ornekler.md](data-quality-ornekler.md) | test fixture | kod |
| 5 | [data-quality-web-arastirma.md](data-quality-web-arastirma.md) | GE/GDPR/PDO | kanit |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | index.md | Künye | Kademe-2 |
| mimari | data-quality-kapsam.md | Kapsam | Kademe-2 |
| mimari | data-quality-bilesenler.md | Bileşenler | Kademe-2 |
| kanit | data-quality-dogrulama.md | İddia defteri | Kademe-2 |
| kod | data-quality-ornekler.md | Test örneği | Kademe-2 |
| kanit | data-quality-web-arastirma.md | Kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward