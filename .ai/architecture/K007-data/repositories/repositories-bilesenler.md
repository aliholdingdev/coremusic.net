---
node_id: "K007-repositories-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - "shared/src/Repository/ — 1 dosya: ApiKeyRepository.php (2026-10-15 disk enumerate)"
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — execute/write/lastInsertId/beginTransaction/commit/rollBack"
  - "shared/src/Contracts/Database/IDatabaseRegistry.php:8-12 — registerMySql/get/has"
validation: "2026-10-15 — envanter disk enumerate; envanter ≠ plan farkı işaretli"
open_questions:
  - "171 tablo vs 1 repository → kalan tabloların erişim yolu UNKNOWN"
group: mimari
---

# repositories — Bileşenler (K007)

## §1 Repo gerçeği (envanter)

| # | Dosya | Tablo | Durum |
|---|-------|-------|-------|
| 1 | `ApiKeyRepository.php` | `coremusic_auth.api_keys` | ✅ tek repository |

## §2 Sözleşme yüzeyi (altında yatan katman)

| # | Sözleşme | Metotlar | Kanıt |
|---|----------|----------|-------|
| 1 | `IDatabaseManager` | execute · write · lastInsertId · beginTransaction · commit · rollBack | :8-14 |
| 2 | `IDatabaseRegistry` | registerMySql(key,…) · get · has | :8-12 |

## §3 Olgunluk matrisi (gerçek vs plan)

| Yüzey | Durum | Not |
|-------|-------|-----|
| API keys | ✅ tam repository (şema docblock + SELECT_COLUMNS + hash-only) | örnek model |
| 171 tablonun erişimi | ❓ | repository YOK — doğrudan `DatabaseManager::execute` çağrılıyor mu? **UNKNOWN** |
| Test | ✅ SQLite in-memory | `ApiKeyRepositoryTest.php` |

> **Kademe-3 ipucu:** 171 tablo tek tek repository'ye mi düşer, query-object mi kurulur —
> ADR-097 K4 kararı.

---

**Authority:** Bayram Ali / Vault Steward