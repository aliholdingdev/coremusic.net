---
node_id: K007-data/transactions
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - transactions-anayasa
  - transactions-surucu
  - transactions-ornekler
  - transactions-kapsam
  - transactions-web-arastirma
depends_on:
  - K007-data/schema
  - K007-data/repositories
evidence:
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — begin/commit/rollBack"
  - "master §6 satır 18 — InnoDB + MVCC + atomik UPDATE"
  - "dev.mysql.com 8.4 InnoDB — 4 URL (2026-10-15)"
validation:
  - "2026-10-15 — 6 dosyaya tamamlandı (4→6); children disk enumerate 5/5"
open_questions:
  - "Çok-DB transaction kullanımı → UNKNOWN"
  - "İzolasyon seviyesi config değeri → UNKNOWN"
group: mimari
---

# transactions — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | K007-data/transactions |
| parent_id | K007-data |
| Durum | draft |

## Ne işe yarar?

Tek DB atomikliği: begin → commit / rollback. InnoDB MVCC + REPEATABLE READ
(variablesayılan) + deadlock otomatik çözümü resmi olarak doğrulanmıştır.

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [transactions-anayasa.md](transactions-anayasa.md) | anayasa | mimari |
| 2 | [transactions-surucu.md](transactions-surucu.md) | uygulama mekanizması | mimari |
| 3 | [transactions-ornekler.md](transactions-ornekler.md) | repo örnekleri | kod |
| 4 | [transactions-kapsam.md](transactions-kapsam.md) | sınır | mimari |
| 5 | [transactions-web-arastirma.md](transactions-web-arastirma.md) | InnoDB/PDO taraması | kanit |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | index.md | Künye | Kademe-2 |
| mimari | transactions-anayasa.md | Anayasa | Kademe-2 |
| mimari | transactions-surucu.md | Sürücü | Kademe-2 |
| kod | transactions-ornekler.md | Örnekler | Kademe-2 |
| mimari | transactions-kapsam.md | Kapsam | Kademe-2 |
| kanit | transactions-web-arastirma.md | Kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward