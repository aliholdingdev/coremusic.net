---
node_id: K015-media/catalog
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "https://dev.mysql.com/doc/refman/8.4/en/fulltext-search.html — MySQL 8.4 FULLTEXT (natural/boolean/query expansion, InnoDB/MyISAM, 2026-10-16 doğrulandı)"
  - "https://sqlite.org/fts5.html — FTS5 (bm25()/rank, NEAR, tokenizer'lar; Navidrome/Jellyfin SQLite bağlamı, 2026-10-16 doğrulandı)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "8/8 web kaynağı 2026-10-16'da HTTP 200 ile doğrulandı (catalog-web-arastirma.md §1)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "catalog ↔ K012/music-service sınırı → ⚠️ VERIFICATION REQUIRED"
  - "Arama yöntemi: yerel MySQL FULLTEXT mi harici motor (Meilisearch/Typesense/Sphinx) mu → ⚠️ UNKNOWN (media-master §12, web-arastirma W-1)"
  - "İndeks nerede tutulur: K007 şeması içinde mi ayrı yapı mı → ⚠️ UNKNOWN (W-2)"
  - "Türkçe tokenizasyon stratejisi (ngram mı stemming mi) → ⚠️ UNKNOWN (W-3)"
group: mimari
---


# catalog — K015-media (kademe-2 · subdomain)

**ELI15:** Listeleme/dizin verisi: hangi görünümde ne listelenir, arama kaynağı.

**Sınır (ne DEĞİL):** Arama motoru/katalog sorgusu K012/music-service ile sınır belirsiz.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `catalog-kapsam.md` | Kapsam içi/dışı · durum makinesi · veri akışı · hata durumları | Kademe-2 |
| kanit | `catalog-web-arastirma.md` | 8 canlı doğrulanmış kaynak (MySQL FULLTEXT, FTS5, BM25, Meilisearch, Sphinx) | Kademe-2 |
| kod | `catalog-ornekler.md` | FULLTEXT sorgu iskeleti + boolean/PDO + indeks güncelleme + JS arama | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | catalog ↔ K012/music-service sınırı → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Arama yöntemi (yerel FULLTEXT vs harici motor) → ⚠️ UNKNOWN | FM open_questions (W-1) |
| 5 | İndeks depolama yeri (K007 içi mi ayrı mı) → ⚠️ UNKNOWN | FM open_questions (W-2) |
| 6 | Türkçe tokenizasyon stratejisi → ⚠️ UNKNOWN | FM open_questions (W-3) |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
