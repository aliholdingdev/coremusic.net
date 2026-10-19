---
node_id: "K007-indexing-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - ".ai/.sql/mysql/coremusic_musics.sql — artist_credit_names CREATE TABLE (2026-10-15)"
validation: "2026-10-15 — dump'tan birebir SQL"
open_questions: []
group: kod
---

# indexing — Örnekler (K007)

## §1 Repo gerçeği: kompozit + FK indeksleri

Dump: `.ai/.sql/mysql/coremusic_musics.sql` — `artist_credit_names` (özet):

```sql
CREATE TABLE `artist_credit_names` (
  `artist_credit_id` binary(16) NOT NULL COMMENT 'UUID v7',
  `position` int NOT NULL,
  ...
  UNIQUE KEY `uk_acn_credit_pos` (`artist_credit_id`, `position`),
  CONSTRAINT `fk_acn_artist` FOREIGN KEY (`artist_credit_id`)
    REFERENCES `artist_credit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## §2 Repo gerçeği: prefix indeks

`artist_credit` tablosu:

```sql
KEY `idx_artist_credit_display` (`display_name`(100))
```

> `display_name` tam kolonu değil ilk 100 karakter indekslenir — tarama maliyeti düşer,
> `LIKE 'abc%'` ön-ek taraması desteklenir.

---

**Authority:** Bayram Ali / Vault Steward