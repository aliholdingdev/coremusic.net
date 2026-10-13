---
node_id: K007-data
node_type: domain
status: draft
parent_id: null
children:
  - schema
  - migrations
  - repositories
  - transactions
  - indexing
  - cache
  - search
  - synchronization
  - backup
  - data-quality
depends_on:
  - K008-domain
  - K009-security
evidence:
  - "shared/src/Database/DatabaseManager.php:14-27 — PDO MySQL DSN + 4 attribute (2026-10-13 okundu)"
  - ".ai/.sql/mysql/ — 18 BCNF coremusic_* veritabanı / 20 SQL dump (2026-10-07)"
validation:
  - "2026-10-13 — children listesi Get-ChildItem ile diskten enumerate edildi: 10/10"
open_questions:
  - "search (K007) ile K020-discovery/search/ sahipliği sınırı → VERIFICATION REQUIRED"
group: mimari
---

# K007-data — Domain Künyesi

> **ELI15:** Veritabanı — şarkılar, kullanıcılar, listeler, yedekler.

Bu oda CoreMusic'in **uzun süreli hafızasıdır**: yazılan her şey (şarkı künyesi, kullanıcı
kaydı, playlist, cihaz eşlemesi) burada şekillenir, indekslenir, önbelleklenir, aranır,
yedeklenir ve kalite denetiminden geçirilir. Kod içermez — bu vault **plan, sınır ve kayıt**
tutar; gerçek kod `shared/` ve `.ai/.sql/` altındadır.

## §1 Alt-Konu Haritası (kademe-2 → kademe-3)

| # | Alt konu | Rol | Odak |
|---|----------|-----|------|
| 1 | `schema` | Şema tasarımı | ER/ilişkisel, BCNF, PK/FK/UNIQUE |
| 2 | `migrations` | Şema sürümleme | Geri alınabilir DDL, online DDL |
| 3 | `repositories` | Erişim katmanı | PDO/prepared, repository sözleşmesi |
| 4 | `transactions` | İşlem bütünlüğü | ACID, MVCC, izolasyon, deadlock |
| 5 | `indexing` | İndeks stratejisi | B-Tree, prefix, composite |
| 6 | `cache` | Önbellek | APCu, page cache, invalidation |
| 7 | `search` | Arama | Full-text, inverted index, novasearch |
| 8 | `synchronization` | Eşitleme | Çift kayıt sınırı (K013 overlap) |
| 9 | `backup` | Yedekleme | Tam/yedim, kurtarma, replication |
| 10 | `data-quality` | Veri kalitesi | Dürüstlük, saklama politikası, ölçüm |

## §2 Sınır (kapsam dışı komşular)

| Komşu | İlişki | Not |
|-------|--------|-----|
| K008-domain | Veri modelinin sahibi | Entity/ValueObject/domain event → K008 |
| K009-security | Şifreleme/anahtar | key_hash, cipher sınırı |
| K013-integraion | synchronization çift kayıt | Sınır → VERIFICATION REQUIRED |
| K020-recomenataon | Arama keşfi | search sahipliği sınırı → VERIFICATION REQUIRED |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
