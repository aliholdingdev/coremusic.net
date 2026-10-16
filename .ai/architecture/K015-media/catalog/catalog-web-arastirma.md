---
node_id: "K015-media/catalog/catalog-web-arastirma"
node_type: "subdomain"
status: "verify-required"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "8 kaynak URL'si 2026-10-16'da canlı doğrulandı (hepsi HTTP 200)"
  - "https://dev.mysql.com/doc/refman/8.4/en/fulltext-search.html (yerel tam metin çözümü)"
validation: "8/8 URL canlı; her ana iddia ≥2 kaynakla cross-check edildi; ⚠️ 0 uydurma URL/ürün/sürüm"
open_questions:
  - "W-1: Arama yöntemi (yerel FULLTEXT mi harici motor mu) → ⚠️ UNKNOWN (media-master §12)"
group: kanit
---

# K015 · catalog · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Konu: tam metin arama yöntemleri — MySQL FULLTEXT (yerel), harici arama motorları, SQLite FTS5, BM25 sıralama

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-16)

> Kural: bu dosya **yalnızca** aşağıdaki 8 URL'yi taşır; kardeş web-arastirma dosyalarındaki URL'ler tekrarlanmaz.

| # | İddia (kaynak ne diyor) | URL | Tarih: 2026-10-16 | Durum |
|---|---|---|---|---|
| 1 | MySQL 8.4 Full-Text Search Functions: InnoDB ve MyISAM'da tam metin indeksi; `MATCH ... AGAINST` sözdizimi; üç mod — **natural language**, **boolean**, **query expansion**; ngram ve MeCab parser seçenekleri | https://dev.mysql.com/doc/refman/8.4/en/fulltext-search.html | Tarih: 2026-10-16 | HTTP 200 |
| 2 | MySQL 8.4 Boolean Full-Text Search: operatörler `+ - ~ * " < >` (@distance dahil); InnoDB FTS tasarımının **Sphinx** modellendiği; rank = **TF·IDF·IDF** bileşenli; InnoDB `innodb_ft_min_token_size` (min token uzunluğu, default 3) | https://dev.mysql.com/doc/refman/8.4/en/fulltext-boolean.html | Tarih: 2026-10-16 | HTTP 200 |
| 3 | MySQL 8.4 ngram parser: CJK dilleri için; `ngram_token_size` 1-10 arası, **default 2**; `WITH PARSER ngram` zorunlu; **stopword'i içeren** token'lar da elenir (stopword tam eşleşmesi değil, içerik kuralı) | https://dev.mysql.com/doc/refman/8.4/en/fulltext-search-ngram.html | Tarih: 2026-10-16 | HTTP 200 |
| 4 | Meilisearch: **<50ms** yanıt iddiası; **7 sıralama kuralı** (words, typo, proximity, attributeRank, sort, wordPosition, exactness); memory-mapped indeks; tek binary + REST API | https://www.meilisearch.com/docs | Tarih: 2026-10-16 | HTTP 200 |
| 5 | SQLite FTS5: `MATCH` sözdizimi, `bm25()` ve `rank` sıralama fonksiyonları, `NEAR`, prefix (`*`), initial-token `^`; tokenizer'lar: unicode61, ascii, porter (stemming), **trigram**; external content tabloları (ayrı depolama) | https://sqlite.org/fts5.html | Tarih: 2026-10-16 | HTTP 200 |
| 6 | Sphinx 2.2.11 dokümantasyonu: **GPL v2**, SphinxQL (MySQL benzeri sorgu dili), **BM25** sıralama, MySQL/PostgreSQL/ODBC kaynaklar, **RT (real-time) indeksler** | https://sphinxsearch.com/docs/current.html | Tarih: 2026-10-16 | HTTP 200 |
| 7 | Okapi BM25 (Wikipedia): sıralama fonksiyonu — term frequency'nin doygunlaştırılmış hali; parametre **k1** (terim sıklığı doygunluğu, yaygın [1.2, 2.0]) ve **b** (alan uzunluğu normalizasyonu, yaygın **0.75**); BM25F/BM25+ genişletmeleri | https://en.wikipedia.org/wiki/Okapi_BM25 | Tarih: 2026-10-16 | HTTP 200 |
| 8 | Typesense dokümantasyonu: açık kaynak arama motoru — typo-tolerant fuzzy search, facet, geo; REST API; tipleri indeksleyen şema tanımı | https://typesense.org/docs | Tarih: 2026-10-16 | HTTP 200 |

---

## §2 KOD XREF (kaynak → CoreMusic yüzeyi)

| # | Kaynak kanıtı | CoreMusic yüzeyi | Bağlantı (tasarım iddiası) |
|---|---|---|---|
| 1 | MySQL FULLTEXT üç mod (natural/boolean/query expansion) + InnoDB — MySQL 8.4 docs ×2 | K015/media/catalog (arama yöntemi) | **Yerel çözüm adayı**: K007 MySQL olduğu için (media-master §2.2) arama yerel FULLTEXT ile yapılabilir — KARAR Vault Steward'da (§4/W-1) |
| 2 | MySQL boolean operatörleri + `innodb_ft_min_token_size` — boolean sayfası | K015/media/catalog (sorgu ayrıştırma) | Sorgu API tasarımı (ornekler Ör-4 iskeleti) bu operatör setine referanstır |
| 3 | ngram parser + `ngram_token_size` — ngram sayfası | K015/media/catalog | Türkçe/CJK gibi boşluksuz token sorunu varsa parser seçeneği; CoreMusic dili Türkçe → ⚠️ UNKNOWN (W-3) |
| 4 | Meilisearch <50ms + 7 sıralama kuralı + tek binary — meilisearch docs | K015/media/catalog (harici alternatif) | Harici arama motoru alternatifi referansı (media-master §12 "Arama yöntemi UNKNOWN" için) |
| 5 | FTS5 bm25() + trigram/porter tokenizer — sqlite.org ×2 | K015/media/catalog | SQLite referans ürün bağlamında (Navidrome/Jellyfin SQLite kullanır — kardeş library-web-arastirma.md) |
| 6 | Sphinx BM25 + SphinxQL + RT indeks — sphinxsearch.com | K015/media/catalog | Tarihsel referans; MySQL FTS "Sphinx modellenir" (MySQL boolean sayfası ×2 cross-check) |
| 7 | BM25 k1∈[1.2,2.0], b=0.75 — Wikipedia ×3 | K015/media/catalog (sıralama) | Sıralama fonksiyonu parametreleri için sektör standardı referansı; MySQL FTS rank = TF·IDF·IDF ile karşılaştırma |

> **Sınır notu:** Bu dosyadaki hiçbir motor/ürün seçimi DEĞİL. CoreMusic arama yöntemi media-master §12'de ⚠️ UNKNOWN'dur.

---

## §3 NOTLAR (erişim kaydı)

| # | Not |
|---|---|
| a | Tüm erişimler 2026-10-16'da HTTP 200 ile doğrulandı (8/8). |
| b | Cross-check 1: MySQL boolean sayfası FTS'in Sphinx modellendiğini ve rank'ın TF·IDF·IDF bileşenli olduğunu doğrudan yazar → Sphinx × MySQL bağlantısı ×2 kaynak. |
| c | Cross-check 2: FTS5 `bm25()` fonksiyonu ile Wikipedia BM25 tanımı (k1/b parametreleri) örtüşüyor → BM25 iddiası ×2 kaynak. |
| d | Cross-check 3: MySQL FULLTEXT ana sayfası hem boolean hem ngram sayfalarını referanslar → üç MySQL sayfası aynı dokümantasyon ailesinden çapraz doğrulandı. |
| e | Başarısız erişim (dosyaya alınmadı): manticoresearch.com/docs/ (404) — Manticore iddiası bu yüzden yazılmadı. |
| f | Meilisearch <50ms iddiası vendor beyanıdır (bağımsız ölçüm YOK) — pazarlama iddiası olarak değerlendirilmelidir. |
| g | Typesense sayfası erişildi; içerik detayı için ayrıca `/docs` alt sayfaları incelenmedi (yalnız ana doküman okundu). |

---

## §4 AÇIK KONULAR (W-xx)

| # | Konu | Etiket |
|---|---|---|
| W-1 | Arama yöntemi: yerel MySQL FULLTEXT mi, harici motor (Meilisearch/Typesense/Sphinx) mi? | ⚠️ UNKNOWN (media-master §12) |
| W-2 | İndeks nerede tutulur: K007 şeması içinde mi, ayrı yapı mı? | ⚠️ UNKNOWN (kapsam §6/3 ile hizalı) |
| W-3 | Türkçe dil desteği için tokenization stratejisi (ngram mı, stemming mi, standart parser yeterli mi)? | ⚠️ UNKNOWN |
| W-4 | Sıralama: MySQL rank (TF·IDF·IDF) yeterli mi, BM25 benzeri işlevsellik gerekir mi? | ⚠️ VERIFICATION REQUIRED |

---

## §5 KARDEŞ DOSYA BAĞI

| Kardeş | İlişki |
|---|---|
| `catalog-kapsam.md` | §3 durum makinesi + §6/2 (arama yöntemi) bu dosyanın W-1'i ile hizalı |
| `catalog-ornekler.md` | FULLTEXT sorgu iskeleti MySQL 8.4 sözdizimine (§1/1-3) dayanır |
| `../library/library-web-arastirma.md` | Navidrome/Jellyfin SQLite kullanımı FTS5 referans bağlamını verir |

---

## §6 YENİDEN ERİŞİM KURALI

1. Bu dosyadaki 8 URL **tekrar tekrar** doğrulanmaz; yeniden erişim yalnız bir üstteki iddia değiştiğinde yapılır.
2. Yeni kanıt = yeni satır (§1'e ek) + tarih; eski satır silinmez (append-only).
3. URL bulunamazsa satır `⚠️ VERIFICATION REQUIRED` olarak işaretlenir — **URL/ürün/sürüm asla uydurulmaz**.

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `.ai/architecture/K015-media/media-master.md` §9.4 · §12 · §7/1 | Arama yöntemi UNKNOWN + K012/music-service sınırı bu dosyanın kanıtlarıyla beslenir |
| `catalog-kapsam.md` · `catalog-ornekler.md` | Kardeş dosyalar (aynı çocuk) |
| `.ai/architecture/K015-media/index.md` | Üst katalog |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
