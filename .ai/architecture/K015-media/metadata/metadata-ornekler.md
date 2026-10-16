---
node_id: "K015-media/metadata/metadata-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "PHP 8.4 + PDO hazır; ID3v2.3 frame kimlikleri id3.org ile doğrulandı (metadata-web-arastirma.md §1/1)"
  - "VorbisComment anahtarları xiph.org ile doğrulandı (metadata-web-arastirma.md §1/3-4)"
validation: "Draft — her örnek amac tablosu + kod + tasarım-id-diası uyarısı taşır; strict_types, SELECT * yasak; frame/anahtar adları doğrulanmış kaynaklara dayanır"
open_questions:
  - "Hangi kapsayıcılar desteklenecek → ⚠️ VERIFICATION REQUIRED (web-arastirma §4/W-1)"
group: kod
---

# K015 · metadata · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Not: Bu dosyadaki TÜM kod örnekleri **TASARIM İDDİASI**'dır, **üretim kodu DEĞİLDİR**.

---

## §1 KAYIT FORMATI

Her kayıt: **Ör-xx** numarası + Öğe/Değer tablosu (Amaç, Yüzey, Dil, Sınır) + kod bloğu + uyarı notu.
Frame/anahtar adları **kanıtlanmış** kaynaklara dayanır: ID3v2.3 (id3.org), VorbisComment (xiph.org).

---

## §2 KAYITLAR

### Ör-1 · MetadataReaderInterface (arayüz iskeleti)

| Öğe | Değer |
|---|---|
| Amaç | Kapsayıcı-bağımsız etiket okuma sözleşmesi (ID3, VorbisComment, MP4…) |
| Yüzey | K015/media/metadata (okuyucu sözleşmesi) |
| Dil | PHP 8.4 |
| Sınır | Ham bayt I/O detayı K003-audio sınırındadır; burada yalnız etiket sözleşmesi |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Metadata;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * Kapsayıcı-bağımsız etiket okuma sözleşmesi.
 * Desteklenen kapsayıcı seti ⚠️ VERIFICATION REQUIRED (W-1).
 */
interface MetadataReaderInterface
{
    /** Bu okuyucu dosyayı okuyabilir mi (uzantı + sihirbaz baytı). */
    public function supports(string $filePath): bool;

    /** Etiketleri oku; hiç etiket yoksa NO_TAG (fallback kapsayıcı boş). */
    public function read(string $filePath): MetadataResult;
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. `MetadataResult` içindeki alanlar web-arastirma §1/1-4'teki kanıtlanmış alan setine (ID3v2.3 + VorbisComment) dayanır.

---

### Ör-2 · ID3v2.3 frame çözümleme iskeleti

| Öğe | Değer |
|---|---|
| Amaç | ID3v2.3 frame kimliklerinden (TIT2/TPE1/TALB/TYER/TCON) alan çıkarmak |
| Yüzey | K015/media/metadata (ID3 okuyucu dalı) |
| Dil | PHP 8.4 |
| Sınır | Frame kimlikleri id3.org/id3v2.3.0 ile doğrulanmış (web-arastirma §1/1); 2.4 TDRC ayrımı §3'te |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Metadata\Id3;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * ID3v2.3 frame çözümleme İSKELETİ (kapsayıcı başlığı + tek kare okuma).
 * Frame kimlikleri: id3.org/id3v2.3.0 (doğrulanmış).
 */
final class Id3V23Reader
{
    /** Kanıtlanmış frame kimlikleri (id3.org/id3v2.3.0). */
    private const array FRAMES = [
        'TIT2' => 'title',      // başlık
        'TPE1' => 'artist',     // sanatçı
        'TALB' => 'album',      // albüm
        'TYER' => 'year',       // yıl (2.4'te TDRC — ayrım §3 notu)
        'TCON' => 'genre',      // tür
        'TRCK' => 'trackNumber',// parça no
    ];

    /**
     * İSKELET: kapsayıcı başlığını okur; tam parse (synchsafe uzunluk,
     * encoding ayrımı, unsynchronisation) ÜRETİMDE ayrıca yazılmalıdır.
     */
    public function readHeader(string $filePath): ?Id3Header
    {
        $fh = fopen($filePath, 'rb');
        if ($fh === false) {
            return null; // UNREADABLE_FILE (kapsam §5/3)
        }

        try {
            $magic = fread($fh, 3);
            if ($magic !== 'ID3') {
                return null; // ID3 yok — NO_TAG (kapsam §3/1)
            }

            $versionBytes = fread($fh, 2); // major.minor (ör. \x03\x00 = 2.3)
            $flags        = ord((string) fread($fh, 1));
            $sizeBytes    = fread($fh, 4);
            $size = $this->synchsafeToInt($sizeBytes);

            return new Id3Header(
                majorVersion: ord($versionBytes[0]),
                revision:     ord($versionBytes[1]),
                flags:        $flags,
                tagSize:      $size,
            );
        } finally {
            fclose($fh);
        }
    }

    /** Synchsafe tam sayı (ID3v2 uzunluk kodlaması). */
    private function synchsafeToInt(string $bytes): int
    {
        return (ord($bytes[0]) << 21)
             | (ord($bytes[1]) << 14)
             | (ord($bytes[2]) << 7)
             |  ord($bytes[3]);
    }
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. Tam frame parse (metin encoding, unsynchronisation, extended header) üretimde ayrıca yazılacak ya da doğrulanmış bir kütüphane değerlendirilecektir (W-2).

---

### Ör-3 · SQL eskizi: metadata kaydı (K007 sahipliğinde)

| Öğe | Değer |
|---|---|
| Amaç | Etiket alanlarının kaydedilme gözünü göstermek |
| Yüzey | K015 veri sahibi — **şema sahibi K007-data** |
| Dil | MySQL 8.x eskiz |
| Sınır | Şema YAZMAZ; K007 BCNF şeması SSOT'tur |

```sql
-- TASARIM İDDİASI — üretim şeması DEĞİLDİR; K007-data sahipliğindedir.
-- Alanlar doğrulanmış format setinden türetildi:
--   ID3v2.3: TIT2/TPE1/TALB/TYER/TCON/TRCK · VorbisComment: TITLE/ARTIST/ALBUM/DATE/GENRE/TRACKNUMBER

CREATE TABLE media_metadata (
    track_id     BIGINT UNSIGNED NOT NULL,          -- media_track FK (K007)
    title        VARCHAR(512)    NULL,
    artist       VARCHAR(512)    NULL,
    album        VARCHAR(512)    NULL,
    album_artist VARCHAR(512)    NULL,
    year         SMALLINT UNSIGNED NULL,
    genre        VARCHAR(128)    NULL,
    track_number SMALLINT UNSIGNED NULL,
    source       ENUM('local_tag','enriched','fallback') NOT NULL DEFAULT 'local_tag',
    updated_at   DATETIME        NOT NULL,
    PRIMARY KEY (track_id),
    KEY ix_media_metadata_artist (artist(191)),
    KEY ix_media_metadata_album (album(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

> **UYARI:** Bu eskiz TASARIM İDDİASI'dır, üretim şeması değildir. `source` alanı kapsamdaki durum makinesini (NO_TAG/PARTIAL/FULL/ENRICHING) yansıtır; ENUM değerleri tasarımdır.

---

### Ör-4 · Fallback + normalizasyon (dosya adı) + Vanilla JS gösterim

| Öğe | Değer |
|---|---|
| Amaç | Etiket eksikse dosya adı fallback'i ve istemci gösterimi |
| Yüzey | K015/media/metadata → K011 |
| Dil | PHP 8.4 (fallback) + Vanilla JS (gösterim) |
| Sınır | Fallback kuralı media-master §9.2 ile hizalı |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Metadata;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * Etiket eksikse dosya adı fallback'i (media-master §9.2).
 */
final class MetadataFallback
{
    /** NO_TAG/PARTIAL durumunda başlık fallback'i. */
    public function titleFallback(string $filePath, ?string $tagTitle): string
    {
        $trimmed = trim((string) $tagTitle);
        if ($trimmed !== '') {
            return $trimmed;
        }

        $base = pathinfo($filePath, PATHINFO_FILENAME);

        return $base !== '' ? $base : 'Bilinmeyen parça';
    }

    /** Sanatçı normalizasyonu — "feat."/"ft." varyantlarını birleştirir (tasarım). */
    public function normalizeArtist(?string $artist): string
    {
        $value = trim((string) $artist);
        if ($value === '') {
            return 'Bilinmeyen sanatçı';
        }

        $value = preg_replace('/\s+(feat\.|ft\.|featuring)\s+/iu', ' ', $value) ?? $value;

        return trim($value);
    }
}
```

```javascript
// TASARIM İDDİASI — üretim kodu değildir.
// Metadata gösterimi: fallback başlık + eksik alan rozeti.

'use strict';

/**
 * @param {{title:string,artist?:string,album?:string,source:string}} meta
 * @param {HTMLElement} container
 */
function renderTrackMeta(meta, container) {
  container.replaceChildren();

  const title = document.createElement('span');
  title.className = 'cm-meta__title';
  title.textContent = meta.title; // sunucu tarafında fallback uygulanmış

  const artist = document.createElement('span');
  artist.className = 'cm-meta__artist';
  artist.textContent = meta.artist ?? 'Bilinmeyen sanatçı';

  container.append(title, artist);

  if (meta.source === 'fallback') { // PARTIAL/NO_TAG rozeti (kapsam §5/1)
    const badge = document.createElement('span');
    badge.className = 'cm-meta__badge cm-meta__badge--partial';
    badge.textContent = 'bilgi eksik';
    container.append(badge);
  }
}
```

> **UYARI:** Bu iskeletler TASARIM İDDİASI'dır, üretim kodu değildir. CSS sınıfları tasarım iddiasıdır; gerçek adlandırma `.ai/.templates/frontend/` şablonuna uyar.

---

## §3 ÖRNEK ÖZETİ

| # | Örnek | Dil | Kanıt dayanağı | Sınır |
|---|---|---|---|---|
| Ör-1 | MetadataReaderInterface | PHP 8.4 | kapsayıcı seti ⚠️ W-1 | Sözleşme iskeleti |
| Ör-2 | Id3V23Reader iskeleti | PHP 8.4 | id3.org/id3v2.3.0 ✓ | Tam parse üretimde |
| Ör-3 | media_metadata SQL eskizi | MySQL 8.x | ID3 + VorbisComment alanları ✓ | Şema sahibi K007 |
| Ör-4 | Fallback + JS gösterim | PHP + JS | media-master §9.2 | K011 gösterimi |

---

## §4 OKUMA KAYDI

| # | Not |
|---|---|
| a | Dört örnek de `TASARIM İDDİASI — üretim kodu değildir` uyarısı taşır. |
| b | Frame kimlikleri (TIT2/TPE1/TALB/TYER/TCON/TRCK) ve VorbisComment anahtarları **canlı doğrulanmış** kaynaklara dayanır (web-arastirma §1/1-4). |
| c | ID3v2.4 TDRC ayrımı Ör-2'de KASTEN yazılmadı (2.3 odaklı iskelet); 2.4 desteği kararı W-3. |
| d | SQL eskizi K007 sahipliğindedir; ENUM kaynak değerleri tasarım iddiasıdır. |

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `metadata-kapsam.md` | Ör-4 FALLBACK §5/1 · durum makinesi §3 ile hizalı |
| `metadata-web-arastirma.md` | ID3/VorbisComment/Picard kanıtları |
| `../artwork/artwork-ornekler.md` | Embedded artwork (ID3 APIC) kardeş örnekte |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
