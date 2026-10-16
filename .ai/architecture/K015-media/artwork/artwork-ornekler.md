---
node_id: "K015-media/artwork/artwork-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "PHP 8.4 + PDO hazır; varyant/kaynak öncelik fikirleri navidrome artwork sayfasıyla örtüşür (artwork-web-arastirma.md §1/8)"
  - "Placeholder davranışı media-master §9.3 ile hizalı"
validation: "Draft — her örnek amac tablosu + kod + tasarım-id-diası uyarısı taşır; strict_types, prepared, SELECT * yasak; boyut ölçüleri KARAR DEĞİL (§3a web-arastirma)"
open_questions:
  - "Varyant boyutları ve format seti → ⚠️ UNKNOWN (media-master §12/7)"
group: kod
---

# K015 · artwork · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Not: Bu dosyadaki TÜM kod örnekleri **TASARIM İDDİASI**'dır, **üretim kodu DEĞİLDİR**.

---

## §1 KAYIT FORMATI

Her kayıt: **Ör-xx** numarası + Öğe/Değer tablosu (Amaç, Yüzey, Dil, Sınır) + kod bloğu + uyarı notu.
Boyut değerleri **örnek referans**tır (CAA 250/500/1200 · Deezer 56/250/500/1000 · Navidrome WebP+Q75 — web-arastirma §3a); **seçim değildir**.

---

## §2 KAYITLAR

### Ör-1 · ArtworkSourcePriority (kaynak öncelik zinciri iskeleti)

| Öğe | Değer |
|---|---|
| Amaç | Kapak kaynağını öncelik sırasıyla dolaşmak: embedded → yan dosya → dış servis → placeholder |
| Yüzey | K015/media/artwork (kaynak çözümleme) |
| Dil | PHP 8.4 |
| Sınır | Öncelik sırası tasarım iddiası (Navidrome fikriyle örtüşür — web-arastirma §1/8); CoreMusic kararı W-4 |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Artwork;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * Kapak kaynak öncelik zinciri iskeleti.
 * Sıra: embedded → yan dosya → dış servis → placeholder
 * (Navidrome CoverArtPriority ile örtüşür; CoreMusic kararı W-4).
 */
final class ArtworkResolver
{
    public function __construct(
        private EmbeddedArtworkReader $embedded,
        private SidecarArtworkLocator $sidecar,
        private ExternalArtworkClient $external, // K013 üzerinden
        private ArtworkCache $cache,
    ) {
    }

    /** En yüksek öncelikli kaynağı dener; hiçbiri yoksa PLACEHOLDER. */
    public function resolve(int $trackId, string $filePath, string $variant): ArtworkResult
    {
        $cached = $this->cache->find($trackId, $variant);
        if ($cached !== null) {
            return ArtworkResult::fromCache($cached);
        }

        // 1) Embedded (ID3 APIC / Vorbis METADATA_BLOCK_PICTURE — kardeş metadata-ornekler)
        $image = $this->embedded->extract($filePath);
        if ($image !== null) {
            return $this->store($trackId, 'embedded', $image, $variant);
        }

        // 2) Yan dosya (ad listesi ⚠️ UNKNOWN — Navidrome: cover.*/folder.*/front.*)
        $image = $this->sidecar->find(dirname($filePath));
        if ($image !== null) {
            return $this->store($trackId, 'sidecar', $image, $variant);
        }

        // 3) Dış servis (K013 üzerinden; rate-limit K013'ün)
        $image = $this->external->fetch($trackId);
        if ($image !== null) {
            return $this->store($trackId, 'external', $image, $variant);
        }

        // 4) PLACEHOLDER (media-master §9.3)
        return ArtworkResult::placeholder($trackId);
    }

    private function store(
        int $trackId,
        string $source,
        BinaryImage $image,
        string $variant,
    ): ArtworkResult {
        $variantImage = $this->cache->makeVariant($image, $variant);
        $this->cache->put($trackId, $variant, $variantImage, $source);

        return new ArtworkResult($trackId, $source, $variantImage, isPlaceholder: false);
    }
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. Yan dosya adları ve dış servis seçimi belirsizdir (W-3/W-4).

---

### Ör-2 · Varyant üretimi iskeleti (ölçüler referans, seçim değil)

| Öğe | Değer |
|---|---|
| Amaç | Orijinal görselden tanımlı boyutlu varyant üretmek |
| Yüzey | K015/media/artwork (varyant servisi) |
| Dil | PHP 8.4 (GD/Imagick çağıracak) |
| Sınır | Ölçüler **örnek**tır — KARAR Vault Steward (§12/7) |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Media\Artwork;

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * Varyant ölçüleri ÖRNEK referanstır (CAA 250/500/1200px — web-arastirma §1/3),
 * KARAR DEĞİL. CoreMusic politikası ⚠️ UNKNOWN (§12/7).
 */
final class ArtworkVariantSpec
{
    /** @var array<string, int> örnek varyant seti (karar değil) */
    private const array VARIANTS = [
        'thumbnail' => 250,
        'medium'    => 500,
        'large'     => 1200,
    ];

    public function __construct(
        private ImageResizer $resizer,
        private ArtworkCache $cache,
    ) {
    }

    /**
     * İstenen varyant varsa önbellekten, yoksa orijinalden üretir.
     * Orijinal yoksa null (PLACEHOLDER zinciri Ör-1'e döner).
     */
    public function makeVariant(int $trackId, BinaryImage $original, string $variant): ?BinaryImage
    {
        if (! isset(self::VARIANTS[$variant])) {
            return null; // bilinmeyen varyant — hata değil, PLACEHOLDER zinciri
        }

        $width = self::VARIANTS[$variant];

        $resized = $this->resizer->resize(
            $original,
            maxWidth: $width,
            maxHeight: $width,          // kare kırpma politikası ⚠️ UNKNOWN
            format:   $original->format, // JPEG/PNG/WebP seti ⚠️ UNKNOWN (W-2)
        );

        return $resized;
    }
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. Ölçü/fmt/kırpma politikaları kararı Vault Steward'dadır (web-arastirma §4 W-1/W-2).

---

### Ör-3 · SQL eskizi: artwork kaydı (K007 sahipliğinde)

| Öğe | Değer |
|---|---|
| Amaç | Görsel kaydı + kaynağı + varyant önbellek metasının gözünü göstermek |
| Yüzey | K015 veri sahibi — **şema sahibi K007-data** |
| Dil | MySQL 8.x eskiz |
| Sınır | Şema YAZMAZ; önbellek politikası media-cache (§9.9) ile sınır |

```sql
-- TASARIM İDDİASI — üretim şeması DEĞİLDİR; K007-data sahipliğindedir.
-- source ENUM'u kapsamdaki durum makinesiyle hizalı (EMBEDDED/SIDECAR/FETCHING/PLACEHOLDER).

CREATE TABLE media_artwork (
    track_id    BIGINT UNSIGNED NOT NULL,           -- media_track FK (K007)
    variant     VARCHAR(32)     NOT NULL,           -- thumbnail/medium/large (tasarım)
    source      ENUM('embedded','sidecar','external','placeholder')
                                  NOT NULL,
    file_path   VARCHAR(1024)   NULL,               -- önbellek dosyası (media-cache)
    width_px    SMALLINT UNSIGNED NULL,
    height_px   SMALLINT UNSIGNED NULL,
    fetched_at  DATETIME        NULL,               -- dış servis getirme zamanı
    updated_at  DATETIME        NOT NULL,
    PRIMARY KEY (track_id, variant),
    KEY ix_media_artwork_source (source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

> **UYARI:** Bu eskiz TASARIM İDDİASI'dır, üretim şeması değildir. `variant` adları ve `source` ENUM tasarımdır; K007 BCNF şeması kesinleştirir.

---

### Ör-4 · Vanilla JS: kapak yükleme + PLACEHOLDER zinciri

| Öğe | Değer |
|---|---|
| Amaç | İstenen boyutta kapak sunumu; hata/boş durumda placeholder gösterimi |
| Yüzey | K015/media/artwork → K011 |
| Dil | Vanilla JS (ES6+) |
| Sınır | Varyant seçimi istemcide (srcset benzeri) veya sunucuda (tasarım) |

```javascript
// TASARIM İDDİASI — üretim kodu değildir.
// Kapak yükleme iskeleti: varyant URL + PLACEHOLDER fallback.

'use strict';

const PLACEHOLDER_SRC = '/assets/img/cm-artwork-placeholder.svg'; // tasarım iddiası

/**
 * @param {{trackId:number, variantUrl:string|null}} artwork
 * @param {HTMLImageElement} img
 */
function mountArtwork(artwork, img) {
  img.addEventListener('error', () => {
    // BOZUK_GORSEL / SERVIS_HATASI → PLACEHOLDER (kapsam §5/1,5)
    if (img.src !== PLACEHOLDER_SRC) {
      img.src = PLACEHOLDER_SRC;
      img.dataset.source = 'placeholder';
    }
  }, { once: true });

  if (artwork.variantUrl === null) {
    // sunucu PLACEHOLDER döndü (NO_COVER)
    img.src = PLACEHOLDER_SRC;
    img.dataset.source = 'placeholder';
    return;
  }

  img.src = artwork.variantUrl;
  img.dataset.source = 'resolved';
  img.alt = ''; // dekoratif kapak — WCAG: alt boş, metin yanındaki başlıktan
}
```

> **UYARI:** Bu iskelet TASARIM İDDİASI'dır, üretim kodu değildir. Placeholder yolu ve `dataset.source` tasarımıdır; gerçek asset yolu `.ai/.templates/frontend/` şablonuna uyar.

---

## §3 ÖRNEK ÖZETİ

| # | Örnek | Dil | Kanıt dayanağı | Sınır |
|---|---|---|---|---|
| Ör-1 | ArtworkResolver (öncelik zinciri) | PHP 8.4 | Navidrome zinciri ✓ (fikir) | Sıra kararı W-4 |
| Ör-2 | ArtworkVariantSpec | PHP 8.4 | CAA 250/500/1200 ✓ (referans) | Ölçü kararı §12/7 |
| Ör-3 | media_artwork SQL eskizi | MySQL 8.x | Durum makinesi §3 | Şema sahibi K007 |
| Ör-4 | mountArtwork (JS) | Vanilla JS | placeholder §9.3 ✓ | K011 gösterimi |

---

## §4 OKUMA KAYDI

| # | Not |
|---|---|
| a | Dört örnek de `TASARIM İDDİASI — üretim kodu değildir` uyarısı taşır. |
| b | Varyant ölçüleri (250/500/1200) **yalnız CAA referansıdır**; CoreMusic politikası UNKNOWN'dur ve bu örnekler KARAR olarak kullanılamaz. |
| c | Embedded çıkarma (APIC) kardeş metadata-ornekler.md Ör-2 ile sınırdaş; burada yalnızca çağırıcı tarafı vardır. |
| d | Dış servis getirme `ExternalArtworkClient` — taşıma K013/K014'tedir; bu örnekte arayüz varsayılmıştır. |

---

## § BAĞLANTILAR

| Hedef | İlişki |
|---|---|
| `artwork-kapsam.md` | Ör-1 zinciri §3 durum makinesi · Ör-4 §5 hata durumları ile hizalı |
| `artwork-web-arastirma.md` | Varyant/kaynak kanıtları (§1/3, §1/7, §1/8) |
| `../metadata/metadata-ornekler.md` | ID3 okuyucu iskeleti (embedded kaynağı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
