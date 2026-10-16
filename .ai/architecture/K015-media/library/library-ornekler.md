---
node_id: "K015-media/library/library-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "PHP 8.4 + PDO hazır; kod blokları yalnız tasarım iskeletidir (üretim kodu değildir)"
  - "SQL eskizleri K007-data sahipliğindedir; K015 şema YAZMAZ (media-master §2.2)"
validation: "Draft — her örnek amac tablosu + kod + tasarım-id-diası uyarısı taşır; strict_types, prepared statement, SELECT * yasak (registry §16 Data/Backend standartları)"
open_questions:
  - "Repository implementasyonunun gerçek şema alan adları → K007 şemasına bağlı ⚠️ UNKNOWN"
group: kod
---

# K015 · library · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Not: Bu dosyadaki TÜM kod örnekleri **TASARIM İDDİASI**'dır, **üretim kodu DEĞİLDİR**.

---

## §1 KAYIT FORMATI

Her kayıt: **Ör-xx** numarası + Öğe/Değer tablosu (Amaç, Yüzey, Dil, Sınır) + kod bloğu + uyarı notu.
Kod kuralları: `declare(strict_types=1)` · PDO prepared statement · `SELECT *` YASAK · parameter binding.

---

## §2 KAYITLAR

### Ör-1 · LibraryRepositoryInterface (arayüz iskeleti)

| Öğe | Değer |
|---|---|
| Amaç | Kitaplık CRUD/sorgu sözleşmesini dil ve şemadan bağımsız tanımlamak |
| Yüzey | K015/media/library (repository sözleşmesi) |
| Dil | PHP 8.4 |
| Sınır | Kalıcı şema K007-data'ya aittir; arayüz K015'in tasarım iddiasıdır |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Library;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * K015/media/library repository sözleşmesi iskeleti.
 * Implementasyon K007-data şemasına bağlıdır (alan adları ⚠️ UNKNOWN).
 */
interface LibraryRepositoryInterface
{
    /** Sayfalı parça listesi — filtre opsiyonel. */
    public function findTracks(
        LibraryFilter $filter,
        int $offset,
        int $limit,
    ): array;

    /** Tek parça — bulunamazsa null. */
    public function findTrackById(int $trackId): ?TrackRecord;

    /** Dosya yolu/parmak izi ile parça — tarama tekilleştirmesi için. */
    public function findByPathHash(string $pathHash): ?TrackRecord;

    /** Yeni parça kaydı. */
    public function insertTrack(TrackRecord $track): int;

    /** Dosyası kaybolan parçaları işaretle (MISSING_FILE). */
    public function markMissing(int $trackId): void;

    /** Eksik parça sayısı (K011 uyarısı için). */
    public function countMissing(): int;
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. Alan adları ve tipler K007 şeması kesinleştikçe revize edilir.

---

### Ör-2 · PDO repository iskeleti (sayfalı listeleme + eksik işaretleme)

| Öğe | Değer |
|---|---|
| Amaç | Parametreli, `SELECT *` içermeyen listeleme sorgusu iskeleti |
| Yüzey | K015/media/library (altyapı iskeleti) |
| Dil | PHP 8.4 + PDO |
| Sınır | Tablo/alan adları TASARIM İDDİASI; gerçek şema K007'de |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Library\Infrastructure;

use CoreMusic\Media\Library\LibraryRepositoryInterface;
use CoreMusic\Media\Library\LibraryFilter;
use CoreMusic\Media\Library\TrackRecord;
use PDO;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * K007 şeması kesinleşmeden alan adları tahminidir (⚠️ UNKNOWN).
 */
final readonly class PdoLibraryRepository implements LibraryRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findTracks(
        LibraryFilter $filter,
        int $offset,
        int $limit,
    ): array {
        $sql = 'SELECT t.id, t.title, t.duration_ms, t.path_hash, '
             . 't.is_missing, t.added_at '
             . 'FROM media_track t '
             . 'WHERE t.is_missing = :missing '
             . 'ORDER BY t.added_at DESC '
             . 'LIMIT :lim OFFSET :off';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':missing', $filter->onlyMissing ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn (array $row): TrackRecord => TrackRecord::fromRow($row),
            $rows,
        );
    }

    public function markMissing(int $trackId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE media_track SET is_missing = 1, missing_since = NOW() '
            . 'WHERE id = :id',
        );
        $stmt->bindValue(':id', $trackId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function countMissing(): int
    {
        $stmt = $this->pdo->query(
            'SELECT COUNT(*) AS c FROM media_track WHERE is_missing = 1',
        );
        $row = $stmt?->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['c'] ?? 0);
    }
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. `SELECT *` yok, tüm değerler bound. Tablo/alan adları (`media_track` vb.) uydurmadır — K007 şeması SSOT'tur.

---

### Ör-3 · SQL şema eskizi (salt-okunur referans)

| Öğe | Değer |
|---|---|
| Amaç | Kapsamdaki varlıkların (parça, eksiklik, tarama) şema gözünü göstermek |
| Yüzey | K015 veri sahibi (koleksiyon) — **şema sahibi K007-data** |
| Dil | MySQL 8.x eskiz |
| Sınır | **Bu eskiz K007'ye aittir; K015 şema YAZMAZ** (media-master §2.2). Migration kararı ADR-014'tedir. |

```sql
-- TASARIM İDDİASI — üretim şeması DEĞİLDİR; K007-data sahipliğindedir.
-- Alan adları tahminidir (⚠️ UNKNOWN); K007 BCNF şeması kesinleştirir.

CREATE TABLE media_track (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title         VARCHAR(512)    NOT NULL,
    duration_ms   INT UNSIGNED    NOT NULL DEFAULT 0,
    path_hash     CHAR(64)        NOT NULL,          -- dosya parmak izi (algo ⚠️ UNKNOWN)
    is_missing    TINYINT(1)      NOT NULL DEFAULT 0,
    missing_since DATETIME        NULL,
    added_at      DATETIME        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_media_track_path_hash (path_hash),
    KEY ix_media_track_is_missing (is_missing)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

> **UYARI:** Bu eskiz TASARIM İDDİASI'dır, üretim şeması değildir. Oluşturma/çalıştırma yetkisi K007-data'nındır (Data Engineer).

---

### Ör-4 · Vanilla JS: kitaplık listesi + eksik dosya rozeti

| Öğe | Değer |
|---|---|
| Amaç | Listeleme + MISSING_FILE rozetinin istemci görünümü iskeleti |
| Yüzey | K015/media/library → K011 (gösterim) |
| Dil | Vanilla JS (ES6+) |
| Sınır | Veri sunucusu K015 repository'sinden gelir; istemci yalnız gösterir |

```javascript
// TASARIM İDDİASI — üretim kodu değildir.
// Kitaplık listesi iskeleti: sayfalı veri + eksik dosya rozeti.

'use strict';

/**
 * @param {Array<{id:number,title:string,duration_ms:number,is_missing:boolean}>} tracks
 * @param {HTMLElement} container
 */
function renderTrackList(tracks, container) {
  container.replaceChildren();

  if (tracks.length === 0) {
    const empty = document.createElement('p');
    empty.className = 'cm-library__empty';
    empty.textContent = 'Kitaplık boş — bir klasör ekleyin.'; // EMPTY durumu (kapsam §5/1)
    container.append(empty);
    return;
  }

  const list = document.createElement('ul');
  list.className = 'cm-library__list';

  for (const track of tracks) {
    const item = document.createElement('li');
    item.className = 'cm-library__item';
    if (track.is_missing) {
      item.classList.add('cm-library__item--missing'); // MISSING_FILE rozeti
    }

    const title = document.createElement('span');
    title.className = 'cm-library__title';
    title.textContent = track.title;

    const duration = document.createElement('span');
    duration.className = 'cm-library__duration';
    duration.textContent = formatDuration(track.duration_ms);

    item.append(title, duration);
    list.append(item);
  }

  container.append(list);
}

/** @param {number} ms */
function formatDuration(ms) {
  const totalSec = Math.floor(ms / 1000);
  const min = Math.floor(totalSec / 60);
  const sec = totalSec % 60;
  return `${min}:${String(sec).padStart(2, '0')}`;
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. CSS sınıfları (`cm-library__*`) tasarım iddiasıdır; gerçek BEM/ITCSS adlandırması `.ai/.templates/frontend/` şablonuna uyar.

---

## §3 ÖRNEK ÖZETİ

| # | Örnek | Dil | Yüzey | Sınır |
|---|---|---|---|---|
| Ör-1 | LibraryRepositoryInterface | PHP 8.4 | library | K007 şemasına bağımlı |
| Ör-2 | PdoLibraryRepository iskeleti | PHP 8.4 + PDO | library | SELECT * yok · prepared |
| Ör-3 | media_track SQL eskizi | MySQL 8.x | K007-data | Şema sahibi K007 |
| Ör-4 | Liste + eksik rozeti | Vanilla JS | K011 gösterimi | BEM şablonuna uyar |

---

## §4 OKUMA KAYDI

| # | Not |
|---|---|
| a | Dört örnek de `TASARIM İDDİASI — üretim kodu değildir` uyarısı taşır. |
| b | SQL eskizi (Ör-3) K007 sahipliğindedir; K015 bu dosyada şema YAZMAZ. |
| c | Eksik dosya davranışı library-web-arastirma.md §1/7 (Navidrome Missing Files) kanıtıyla örtüşür. |

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `library-kapsam.md` | Ör-4 EMPTY/MISSING_FILE §5/1-2 ile hizalı |
| `library-web-arastirma.md` | Eksik dosya + çoklu model kanıtları |
| `.ai/architecture/K009-security/…` | Format referansı (K009 threat-model üçlüsü) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
