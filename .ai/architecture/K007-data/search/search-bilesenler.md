---
node_id: "K007-search-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - ".ai/.sql/mysql/novasearch.sql:20-35 — cache_entries (UNIQUE cache_key + idx_cache_key)"
  - ".ai/.sql/mysql/novasearch.sql:37-65 — channels (idx_youtube_id, idx_name)"
  - ".ai/.sql/CLAUDE.md §3 — novasearch 7 tablo (canlı)"
validation: "2026-10-15 — 2 tablo örneği dump'tan okundu"
open_questions:
  - "novasearch tablolarında FULLTEXT indeks var mı → dump'ta FULLTEXT görmedi → UNKNOWN"
group: mimari
---

# search — Bileşenler (K007)

## §1 novasearch envanteri (dump · 2026-10-15)

| # | Tablo | Rol | İndeks (dump) |
|---|-------|-----|---------------|
| 1 | `cache_entries` | arama önbelleği | UNIQUE cache_key · idx_cache_key |
| 2 | `channels` | YouTube kanal künyesi | UNIQUE youtube_id · idx_name |
| 3 | `playlists` | playlist künyesi | youtube_id · channel_id |
| 4 | `knex_migrations` (+lock) | migration kaydı (knex) | PK |

> 7 tablonun tamamı `.ai/.sql/mysql/novasearch.sql` içindedir; burada 4'ü örneklendi.
> **Dürüstlük notu:** dump'ta `FULLTEXT` tanımı **görülmedi** — arama indeksli mi, LIKE mi? UNKNOWN.

## §2 Motor seçenek matrisi (K-06)

| Aday | Veri tipi | Repo durumu |
|------|-----------|-------------|
| MySQL FULLTEXT | natural/boolean/QE | şema var, kullanım kanıtsız |
| Meilisearch | hibrit + typo | referans YOK |
| Solr / Elasticsearch | enterprise ölçek | referans YOK |

---

**Authority:** Bayram Ali / Vault Steward