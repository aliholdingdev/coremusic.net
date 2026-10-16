---
node_id: "K007-data/transactions-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data/transactions"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/repositories"
evidence:
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — beginTransaction/commit/rollBack (2026-10-12 okundu)"
  - "master §6 satır 18 — InnoDB + MVCC + atomik UPDATE (2026-10-12)"
  - "dev.mysql.com/doc/refman/8.4/en/innodb-transaction-isolation-levels.html — HTTP 200 (2026-10-15)"
validation: "2026-10-15 — kapsam yazıldı; sözleşme satırı + MySQL resmi URL"
open_questions:
  - "Çok-DB transaction CoreMusic'te kullanılıyor mu → UNKNOWN (18 DB topolojisi)"
  - "Deadlock sayacı/izleme → repo'da YOK"
group: mimari
---

# transactions — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | ACID / begin-commit-rollback sözleşmesi | IDatabaseManager.php:8-14 |
| 2 | İzolasyon seviyesi (REPEATABLE READ varsayılan) | MySQL 8.4 §17.7.2.1 (2026-10-15) |
| 3 | Deadlock (çift kilit) davranışı | MySQL 8.4 §13.7.1 |
| 4 | Tek DB atomik güncelleme (CHECK/UNIQUE/FK) | master §6 satır 18 |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| Şema tasarımı | schema | kısıt orada tasarlanır |
| Çok DB senkronizasyonu | synchronization | transaction sınırı tek DB |
| DB kurulumu/innoDB yapılandırması | K018-devops / K000 | runtime yapılandırma |
| Lock timeout politikası değeri | → UNKNOWN | ölçüm yok |

## §3 Sınır cümlesi

transactions, "tek adımda hepsi ya hiç" sorusunun sahibidir — tek DB'yi korur;
DB'ler arası koordinasyon synchronization'ın konusudur (sınır açık, S-3/S-9).

---

**Authority:** Bayram Ali / Vault Steward