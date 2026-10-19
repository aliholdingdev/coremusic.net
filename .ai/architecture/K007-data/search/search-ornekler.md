---
node_id: "K007-search-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - ".ai/.sql/mysql/novasearch.sql:20-35 — cache_entries (2026-10-15 okundu)"
  - "dev.mysql.com/doc/refman/8.4/en/fulltext-boolean.html — operatör seti (2026-10-15)"
validation: "2026-10-15 — 1 repo SQL + 1 resmi kalıp"
open_questions: []
group: kod
---

# search — Örnekler (K007)

## §1 Repo gerçeği: novasearch cache tablosu

`.ai/.sql/mysql/novasearch.sql` (satır 20-35, özet):

```sql
CREATE TABLE `cache_entries` (
  `id` varchar(36) NOT NULL DEFAULT (uuid()),
  `cache_key` varchar(255) NOT NULL,
  `cache_value` text NOT NULL,
  `cache_type` varchar(50) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cache_key` (`cache_key`)
) ENGINE=InnoDB ...;
```

> Arama sonuçlarının TTL'li cache'i — `expires_at` zorunlu, hard TTL.

## §2 Resmi kalıp: MySQL FULLTEXT (§14.9 · 2026-10-15)

```sql
-- doğal dil (varsayılan):
SELECT * FROM tracks WHERE MATCH(title, artist) AGAINST ('ceza' IN NATURAL LANGUAGE MODE);
-- boolean (InnoDB BM25/TF-IDF):
SELECT * FROM tracks WHERE MATCH(title, artist) AGAINST ('+ceza -frekans' IN BOOLEAN MODE);
```

> InnoDB: DML commit'te indekslenir; `%` joker desteklenmez; min token 3 karakter.

---

**Authority:** Bayram Ali / Vault Steward