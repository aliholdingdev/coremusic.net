---
node_id: K007-data/transactions
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - transactions-anayasa
  - transactions-surucu
  - transactions-ornekler
depends_on: []
evidence:
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — beginTransaction/commit/rollBack"
  - "shared/src/Database/DatabaseManager.php — InnoDB transaction sarmalayıcı"
  - ".ai/.sql/mysql/coremusic_catalog.sql — ENGINE=InnoDB (MVCC default)"
validation:
  - "2026-10-13 — index yeniden yazıldı; children disk enumerate 3/3; mojibake temiz"
open_questions:
  - "İzolasyon seviyesi repo'da açıkça ayarlanmamış → default (REPEATABLE READ)"
  - "Deadlock çözüm stratejisi repo kanıtı yok → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# transactions — Alt-Konu İndeksi (Kademe-3)

| Alan | Değer |
|---|---|
| node_id | K007-data/transactions |
| node_type | subdomain |
| parent_id | K007-data |
| Rol | Atomik yazma · ACID/MVCC · begin/commit/rollback |
| Durum | draft |

## §1 Ne işe yarar?

Bu alt konu K007'nin **atomiklik** ayağıdır: birden fazla yazmanın ya tamamının ya hiç
inin uygulanması. Repo'da `beginTransaction()` / `commit()` / `rollBack()` sarmalayıcıları
`IDatabaseManager` sözleşmesiyle tanımlıdır; motor InnoDB olduğu için MVCC varsayılan davranıştır.

## §2 İçerik dosyaları (kademe-3)

| # | Dosya | Rol | Bütçe |
|---|-------|-----|-------|
| 1 | `transactions-anayasa.md` | ACID/MVCC kuralı, izolasyon seviyeleri | ≥500 |
| 2 | `transactions-surucu.md` | begin/commit/rollback sürüş akışı, deadlock | ≥500 |
| 3 | `transactions-ornekler.md` | gerçek repo kodu + SQL transaction örnekleri | ≥500 |

## §3 Kanıt (repo)

| İddia | Kanıt |
|-------|-------|
| begin/commit/rollback sözleşmesi | `IDatabaseManager.php:8-14` |
| InnoDB (MVCC) motor | dump'larda `ENGINE=InnoDB` |
| İzolasyon seviyesi açıkça yok | repo'da `SET TRANSACTION` **görülmedi** → default |

## §4 Kapsam dışı

| Dış | Nereye |
|-----|--------|
| İş mantığı gruplama | K012-services |
| Domain kuralı | K008-domain |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
