---
node_id: "K015-media/catalog/catalog-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "MySQL FULLTEXT sözdizimi 8.4 dokümantasyonu ile doğrulandı (catalog-web-arastirma.md §1/1-3)"
  - "PHP 8.4 + PDO hazır; boolean operatör seti MySQL boolean sayfasından (§1/2)"
validation: "Draft — her örnek amac tablosu + kod + tasarım-id-diası uyarısı taşır; strict_types, prepared, SELECT * yasak; FULLTEXT motor seçimi KARAR DEĞİL (W-1)"
open_questions:
  - "Arama yöntemi: yerel MySQL FULLTEXT mi harici motor mu → ⚠️ UNKNOWN (media-master §12)"
group: kod
---

# K015 · catalog · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Not: Bu dosyadaki TÜM kod örnekleri **TASARIM İDDİASI**'dır, **üretim kodu DEĞİLDİR**.

---

## §1 KAYIT FORMATI

Her kayıt: **Ör-xx** numarası + Öğe/Değer tablosu (Amaç, Yüzey, Dil, Sınır) + kod bloğu + uyarı notu.
SQL sözdizimi MySQL 8.4 dokümantasyonuna dayanır (canlı doğrulanmış); **motor seçimi KARAR DEĞİL** (W-1).

---

## §2 KAYITLAR

### Ör-1 · SQL: FULLTEXT indeks + natural language arama (MySQL 8.4)

| Öğe | Değer |
|---|---|
| Amaç | İndeks oluşturma + `MATCH ... AGAINST` doğal dil sorgusu iskeleti |
| Yüzey | K015/media/catalog (arama) |
| Dil | MySQL 8.4 SQL |
| Sınır | Sözdizimi doğrulanmış (web-arastirma §1/1); **tablo alanları TASARIM İDDİASI**; şema sahibi K007 |

```sql
-- TASARIM İDDİASI — üretim şeması DEĞİLDİR; K007-data sahipliğindedir.
-- Sözdizimi: dev.mysql.com/doc/refman/8.4/en/fulltext-search.html (doğrulanmış).

-- İndeks (tek seferlik / şema ile birlikte — K007 sahipliğinde)
ALTER TABLE media_track
    ADD FULLTEXT KEY ft_media_track_search (title, artist_text, album_text);

-- Doğal dil araması — sonuçlar relevance sırasıyla; sayfalama LIMIT/OFFSET
SELECT t.id, t.title,
       MATCH(t.title, t.artist_text, t.album_text)
           AGAINST (:q IN NATURAL LANGUAGE MODE) AS score
FROM media_track AS t
WHERE MATCH(t.title, t.artist_text, t.album_text)
      AGAINST (:q IN NATURAL LANGUAGE MODE)
ORDER BY score DESC
LIMIT :lim OFFSET :off;
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. `innodb_ft_min_token_size` (default 3) kısa Türkçe kelimeleri eleyebilir — dil stratejisi W-3.

---

### Ör-2 · PHP: boolean mod sorgu + token temizleme (PDO)

| Öğe | Değer |
|---|---|
| Amaç | Kullanıcı ifadesini boolean mod için güvenli biçimde hazırlamak |
| Yüzey | K015/media/catalog (sorgu katmanı) |
| Dil | PHP 8.4 + PDO |
| Sınır | Operatör seti doğrulanmış (`+ - ~ * "` — web-arastirma §1/2); tüm değerler bound |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Catalog;

use PDO;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * Boolean mod arama iskeleti.
 * Operatörler: + - ~ * " (MySQL boolean — doğrulanmış).
 * DİKKAT: kullanıcı girdisi boolean operatörlerinden arındırılır;
 * aksi halde sorgu mantığı bozulur (SQL injection DEĞİL, arama mantığı hatası).
 */
final readonly class CatalogSearchQuery
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @return list<array{id:int,title:string,score:float}> */
    public function search(string $userInput, int $limit = 20, int $offset = 0): array
    {
        $terms = $this->sanitizeBooleanTerms($userInput);
        if ($terms === '') {
            return []; // NO_MATCH (kapsam §5/2)
        }

        $sql = 'SELECT t.id, t.title, '
             . 'MATCH(t.title, t.artist_text, t.album_text) '
             . 'AGAINST (:q IN BOOLEAN MODE) AS score '
             . 'FROM media_track AS t '
             . 'WHERE MATCH(t.title, t.artist_text, t.album_text) '
             . 'AGAINST (:q IN BOOLEAN MODE) '
             . 'ORDER BY score DESC '
             . 'LIMIT :lim OFFSET :off';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':q', $terms);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        /** @var list<array{id:int,title:string,score:float}> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    /**
     * Boolean operatör ve tırnak sembollerini temizler;
     * kelimeleri boşlukla birleştirir ("-" baştaysa korunabilir — tasarım).
     */
    private function sanitizeBooleanTerms(string $input): string
    {
        $clean = preg_replace('/[+\-*"@<>()~]/u', ' ', $input) ?? '';
        $clean = preg_replace('/\s+/u', ' ', trim($clean)) ?? '';

        return $clean;
    }
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. Temizleme kuralı (opöratör korunup korunmaması) tasarımdır; hazır kütüphane/harici motor seçimi W-1.

---

### Ör-3 · SQL: indeks güncelleme (library → catalog olayı)

| Öğe | Değer |
|---|---|
| Amaç | Ekleme/silme olayından sonra indeksin güncellendiğini doğrulamak |
| Yüzey | K015/media/catalog (SYNC_PENDING → READY) |
| Dil | MySQL 8.4 SQL |
| Sınır | InnoDB FTS otomatik güncelleme davranışı doğrulanmış bağlamda (web-arastirma §1/1-2) |

```sql
-- TASARIM İDDİASI — üretim kodu DEĞİLDİR.
-- InnoDB FULLTEXT ekleme/silmede indeksi otomatik günceller (dahili FTS dizini);
-- "SON DAKİKA" senkronizasyonu ayrı kuyruk KARARIDIR (W-2, UNKNOWN).

-- Yeni parça eklendi → dahili FTS dizini otomatik güncellenir
INSERT INTO media_track (title, artist_text, album_text, path_hash, added_at)
VALUES (:title, :artist, :album, :pathHash, NOW());

-- Parça silindi → FTS kaydı da düşer
DELETE FROM media_track WHERE id = :id;

-- Doğrulama sorgusu: indeks taze mi? (operasyonel kontrol)
SELECT COUNT(*) AS indexed_rows
FROM media_track AS t
WHERE MATCH(t.title, t.artist_text, t.album_text)
      AGAINST ('*' IN BOOLEAN MODE);
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. `AGAINST ('*')` kontrolü yaklaşık sayı verir; tam doğrulama stratejisi tasarımdır (kapsam §5/5 GUNCELLEME_GECIKMESI ile hizalı).

---

### Ör-4 · Vanilla JS: arama kutusu + debounce + sonuç listesi

| Öğe | Değer |
|---|---|
| Amaç | Type-ahead arama istemci iskeleti (debounce + sayfalama düğmesi) |
| Yüzey | K015/media/catalog → K011 |
| Dil | Vanilla JS (ES6+) |
| Sınır | Sunucu uç noktası K015 repository'sidir; ağ detayı K014 |

```javascript
// TASARIM İDDİASI — üretim kodu değildir.
// Arama kutusu iskeleti: 250ms debounce + sonuç listesi.

'use strict';

/**
 * @param {HTMLInputElement} input
 * @param {HTMLElement} resultsEl
 * @param {(q:string)=>Promise<Array<{id:number,title:string,artist?:string}>>} searchFn
 */
function mountCatalogSearch(input, resultsEl, searchFn) {
  let timer = 0;
  let seq = 0; // yarış durumu: eski yanıtın yazmasını engeller

  input.addEventListener('input', () => {
    window.clearTimeout(timer);
    timer = window.setTimeout(async () => {
      const q = input.value.trim();
      if (q === '') { // NO_MATCH/EMPTY ayrımı (kapsam §5/1-2)
        resultsEl.replaceChildren();
        return;
      }

      const mySeq = ++seq;
      const results = await searchFn(q);
      if (mySeq !== seq) return; // eski yanıt — yoksay

      renderResults(results, resultsEl);
    }, 250);
  });
}

/** @param {Array<{id:number,title:string,artist?:string}>} results */
function renderResults(results, container) {
  container.replaceChildren();

  if (results.length === 0) {
    const none = document.createElement('p');
    none.className = 'cm-catalog__empty';
    none.textContent = 'Sonuç yok.';
    container.append(none);
    return;
  }

  const list = document.createElement('ul');
  list.className = 'cm-catalog__results';

  for (const item of results) {
    const li = document.createElement('li');
    li.className = 'cm-catalog__result';
    li.dataset.trackId = String(item.id);
    li.textContent = item.artist ? `${item.artist} — ${item.title}` : item.title;
    list.append(li);
  }

  container.append(list);
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. 250ms debounce değeri tasarımıdır; rate-limit/dış motor seçimi W-1'e bağlı değişir.

---

## §3 ÖRNEK ÖZETİ

| # | Örnek | Dil | Kanıt dayanağı | Sınır |
|---|---|---|---|---|
| Ör-1 | FULLTEXT + natural mode | MySQL 8.4 | refman 8.4 ✓ | Şema sahibi K007 |
| Ör-2 | Boolean mod + PDO | PHP 8.4 | boolean operatörleri ✓ | Motor seçimi W-1 |
| Ör-3 | İndeks güncelleme/doğrulama | MySQL 8.4 | InnoDB FTS davranışı ✓ | Olay kuyruğu UNKNOWN |
| Ör-4 | Arama kutusu (JS) | Vanilla JS | — | K011 gösterimi |

---

## §4 OKUMA KAYDI

| # | Not |
|---|---|
| a | Dört örnek de `TASARIM İDDİASI — üretim kodu değildir` uyarısı taşır. |
| b | MySQL sözdizimi (MATCH AGAINST, natural/boolean, operatörler, FULLTEXT KEY) 8.4 dokümantasyonundan doğrulanmıştır; `artist_text/album_text` alan adları TASARIM İDDİASI'dır (gerçek alanlar K007'de). |
| c | InnoDB FTS ekleme/silmede dahili dizini otomatik günceller; ayrı senkronizasyon kuyruğu var mı sorusu W-2'de (UNKNOWN) kayıt altındadır. |
| d | Türkçe tokenizasyon (W-3): `innodb_ft_min_token_size` + ngram parser opsiyonu web-arastirma §1/3'te kanıtlandı; bu örnekte default (standart parser) varsayılmıştır. |
| e | Harici motor (Meilisearch/Typesense/Sphinx) örneği **bilerek yazılmadı** — motor seçimi KARAR öncesi iskelet üretmek yanıltıcı olurdu. |

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `catalog-kapsam.md` | Ör-3 SYNC_PENDING §3 · Ör-4 NO_MATCH §5/2 ile hizalı |
| `catalog-web-arastirma.md` | MySQL/ngram/BM25 kanıtları (§1/1-3, §1/7) |
| `../library/library-ornekler.md` | media_track şema iskeleti (örnekler arası tutarlılık) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
