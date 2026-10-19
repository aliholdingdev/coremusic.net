---
node_id: "K007-search-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/indexing"
evidence:
  - ".ai/.sql/mysql/novasearch.sql — 7 tablo (cache_entries, channels, playlists… 2026-10-15 okundu)"
  - "dev.mysql.com/doc/refman/8.4/en/fulltext-search.html — HTTP 200 (2026-10-15)"
validation: "2026-10-15 — kapsam yazıldı; novasearch dump + MySQL FULLTEXT URL"
open_questions:
  - "S-1 — arama sahipliği K007 mi K020-discovery mi → SINIR AÇIK"
  - "MATCH…AGAINST repo kodunda kullanımı → tarama yapılmadı (VERIFICATION REQUIRED)"
group: mimari
---

# search — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | MySQL FULLTEXT (natural/boolean/query expansion) | MySQL 8.4 §14.9 (2026-10-15) |
| 2 | `novasearch` şemasının veri-yüzü | `.ai/.sql/mysql/novasearch.sql` |
| 3 | Dış motor değerlendirmesi (Meilisearch/Solr/ES) | K-06 (UNKNOWN) |
| 4 | InnoDB FULLTEXT davranışı (commit-time, DOC_ID) | MySQL 8.4 §17.6.2.4 |
| 5 | Türkçe tokenizasyon (ngram) gereksinimi | MySQL 8.4 §14.9.8 |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| Keşif/katalog kuralları (ne aranır) | K020-discovery | S-1 sahiplik sınırı |
| İndeks hızlandırması (B-tree) | indexing | FULLTEXT ayrı dil |
| Dış motor kurulumu/operasyonu | K17-devops | süreç |
| Öneri/vektör arama modeli | K006-yapay-zeka | model eğitimi |

## §3 Sınır cümlesi

search, "kullanıcı bir metin yazdığında hangi satır bulunur?" sorusunun **veri-yüzü**dür —
motor seçimi (MySQL mi dış motor mu) K-06 kararıyla askıdadır.

---

**Authority:** Bayram Ali / Vault Steward