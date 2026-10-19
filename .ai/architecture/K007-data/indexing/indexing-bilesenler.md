---
node_id: "K007-indexing-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - ".ai/.sql/mysql/*.sql — 20 dump'ta KEY/UNIQUE KEY/envanter (2026-10-15 tarandı)"
validation: "2026-10-15 — dump envanteri 4 türde örneklendi"
open_questions:
  - "Kullanılmayan indeks envanteri (drop adayı) → UNKNOWN"
group: mimari
---

# indexing — Bileşenler (K007)

## §1 Repo indeks türleri (dump envanteri)

| # | Tür | Repo örneği | Amaç |
|---|-----|-------------|------|
| 1 | UNIQUE KEY | `uk_album_types_name(type_name)` | tekilleştirme |
| 2 | Kompozit UNIQUE | `uk_acn_credit_pos(artist_credit_id, position)` | sıra tekilliği |
| 3 | Normal KEY | `idx_album_types_sort_order(sort_order)` | sıralama/tarama |
| 4 | Prefix KEY | `idx_artist_credit_display(display_name(100))` | uzun varchar |

## §2 Karar matrisi

| Durum | Karar | Dayanak |
|-------|-------|---------|
| Sık WHERE + JOIN kolonu | indeksle | tarama maliyeti |
| Uzun metin | prefix (100) | tam kolon pahalı |
| Düşük kardinalite | tek başına indeksleme; plana bırak | seçicilik |
| `SELECT *` | **yasak** (ADR-002) | transfer israfı |
| Tablo yükleme + indeks | önce yükle, sonra `CREATE INDEX` | MySQL §10.5.7 (online) |

---

**Authority:** Bayram Ali / Vault Steward