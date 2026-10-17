---
node_id: K014-media/artwork/ornekler
node_type: module
status: draft
parent_id: K014-media/artwork
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.3"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/artwork — ornekler.md

> **Statü:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** niteliğindedir — K014-media/artwork kademe-2 bir **design node**'dur; medya düğümü repo'da henüz kodu YOKtur. Örnekler yalnız `media-master.md` §9.3 + §22 (doğrulanmış standartlar) temeline oturur; §22'de olmayan standart/sürüm/ad yazılmaz (zero-hallucination). Konu eksenleri: CAA cover fetch (250/500/1200 px) · varyant üretimi/boyutlandırma · immutable cache + ETag · gömülü kapak çıkarma.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Statü | TASARIM ÖNERİSİ — kademe-2 design node, repo kodu YOK |
| 2 | Dayanak | yalnız `media-master.md` §9.3 + §22 (doğrulanmış standartlar) |
| 3 | Uydurma | yasak (zero-hallucination) — §22'de olmayan standart/sürüm/ad yazılmaz |
| 4 | CAA kademeleri | **250 / 500 / 1200 px** (§22.4 — resmi) |
| 5 | Varyant kademe | thumbnail ~64–128 · medium ~250–500 · large ~640–1200 px (§22.4) |
| 6 | Cache başlığı | `Cache-Control: public, max-age=31536000, immutable` + ETag (içerik hash) (§22.5) |
| 7 | Dış servis kuralı | MusicBrainz **1 istek/sn/IP** · **User-Agent zorunlu** (§22.3) |
| 8 | Hata/placeholder | kapak yoksa placeholder (§9.3 TASARIM İDDİASI) |
| 9 | Değişiklik | yok — bu dosya yalnız tasarım örneği üretir; kaynak kod okumaz/yazmaz |

## §2 PHP

CAA kademeli (250/500/1200 px) kapak fetch'i — User-Agent zorunlu (§22.3), null dönüşü placeholder (§9.3).

```php
<?php
declare(strict_types=1);

// ARTWORK — TASARIM ÖNERİSİ (K014-media/artwork · master §9.3 · §22.3 · §22.4)
// CAA resmi kademeleri: 250 / 500 / 1200 px (§22.4)

final class CoverArtFetcher
{
    /** §22.4: CAA resmi kademeleri */
    private const array CAA_TIERS = [250, 500, 1200];

    /** §22.3: MusicBrainz/CAA User-Agent ZORUNLU */
    private const string USER_AGENT = 'CoreMusic/1.0 (vault-steward@example.com)';

    /** Kademe seçili kapak getirir; bulunamazsa null → placeholder (§9.3). */
    public function fetch(string $releaseMbid, int $tierPx): ?string
    {
        if (!in_array($tierPx, self::CAA_TIERS, true)) {
            throw new InvalidArgumentException('CAA kademesi 250/500/1200 olmalı (§22.4)');
        }

        $ch = curl_init($this->caaEndpoint($releaseMbid, $tierPx));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => self::USER_AGENT, // §22.3 zorunlu
            CURLOPT_TIMEOUT        => 10,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);

        return is_string($body) && $body !== '' ? $body : null; // null → §9.3 placeholder
    }

    private function caaEndpoint(string $mbid, int $tierPx): string
    {
        // ⚠️ TASARIM ÖNERİSİ: URL kalıbı bu belgede sabitlenmez; uç nokta K013-integraion sağlar.
        return sprintf('caa://release/%s/front-%d', $mbid, $tierPx);
    }
}
```

## §3 JavaScript

Cihaz genişliğine göre kademe seçimi + ETag (içerik hash) ile 304 yeniden doğrulama (§22.5).

```js
// ARTWORK — TASARIM ÖNERİSİ (master §22.4 · §22.5 · §9.3)
// Üç kademe: thumbnail ~64–128 px · medium ~250–500 px · large ~640–1200 px (§22.4)

const ART_TIERS = Object.freeze({
  thumbnail: 128, // §22.4 thumbnail bandı (~64–128 px)
  medium: 500,    // §22.4 CAA 500
  large: 1200,    // §22.4 CAA 1200 (resmi en büyük kademe)
});

export function pickVariant(deviceWidthPx) {
  if (deviceWidthPx < 256) return 'thumbnail';
  if (deviceWidthPx < 800) return 'medium';
  return 'large';
}

export async function fetchArtwork(mbid, tier, cachedEtag) {
  const url = `caa://release/${mbid}/front-${ART_TIERS[tier]}`; // uç nokta K013'ten (tasarım)
  const headers = cachedEtag ? { 'If-None-Match': cachedEtag } : {};
  const res = await fetch(url, { headers });

  if (res.status === 304) return { status: 'not-modified' }; // ETag isabet (§22.5)
  if (res.status >= 400 || !res.ok) return { status: 'placeholder' }; // §9.3 fallback

  return {
    status: 'ok',
    bytes: await res.arrayBuffer(),
    etag: res.headers.get('ETag'),                        // içerik hash (§22.5)
    cacheControl: 'public, max-age=31536000, immutable',  // §22.5
  };
}
```

## §4 TypeScript

Tür güvenli kademe modeli + immutable cache başlıkları (§22.4 · §22.5).

```ts
// ARTWORK — TASARIM ÖNERİSİ (master §22.4 · §22.5 · §9.3)

export type ArtTier = 'thumbnail' | 'medium' | 'large';
export type ArtStatus = 'ok' | 'not-modified' | 'placeholder';

// §22.4: thumbnail ~64–128 · medium ~250–500 · large ~640–1200 (CAA 1200 resmi)
const TIER_PX: Record<ArtTier, number> = { thumbnail: 128, medium: 500, large: 1200 };

export interface ArtworkResult {
  status: ArtStatus;
  bytes?: ArrayBuffer;
  etag?: string;           // içerik hash (§22.5)
  cacheControl?: string;   // immutable (§22.5)
}

export function tierForWidth(widthPx: number): ArtTier {
  if (widthPx < 256) return 'thumbnail';
  if (widthPx < 800) return 'medium';
  return 'large';
}

export async function loadArtwork(mbid: string, tier: ArtTier, etag?: string): Promise<ArtworkResult> {
  const res = await fetch(`caa://release/${mbid}/front-${TIER_PX[tier]}`, {
    headers: etag ? { 'If-None-Match': etag } : {},
  });

  if (res.status === 304) return { status: 'not-modified' };   // §22.5 304 akışı
  if (res.status >= 400) return { status: 'placeholder' };     // §9.3

  return {
    status: 'ok',
    bytes: await res.arrayBuffer(),
    etag: res.headers.get('ETag') ?? undefined,
    cacheControl: 'public, max-age=31536000, immutable',       // §22.5
  };
}
```

## §5 C++

Üç kademe varyant üretimi (thumbnail/medium/large) — resize arayüzü, kütüphane seçimi kademe-4 (⚠️ UNKNOWN).

```cpp
// ARTWORK — TASARIM ÖNERİSİ (master §22.4)
// Üç kademe varyant üretimi: thumbnail · medium · large

#include <array>
#include <cstdint>
#include <string>
#include <vector>

namespace coremusic::artwork {

struct ImageBuffer {
    std::vector<std::uint8_t> pixels; // ham RGB (tasarım varsayımı)
    int width = 0;
    int height = 0;
};

// §22.4 doğrulanmış CAA kademeleri: 250 / 500 / 1200 px
inline constexpr std::array<int, 3> kTierPx{250, 500, 1200};

// Resize arayüzü — görsel kütüphanesi kademe-4'te seçilecek (⚠️ UNKNOWN araç)
using ResizeFn = ImageBuffer (*)(const ImageBuffer&, int targetPx);

struct ArtworkVariants {
    ImageBuffer thumbnail; // ~128 px (§22.4 thumbnail bandı ~64–128)
    ImageBuffer medium;    // 250 px (§22.4 CAA 250)
    ImageBuffer large;     // 1200 px (§22.4 CAA 1200 — resmi en büyük kademe)
};

[[nodiscard]] ArtworkVariants makeVariants(const ImageBuffer& source, ResizeFn resize) {
    ArtworkVariants out{};
    out.thumbnail = resize(source, 128);
    out.medium    = resize(source, kTierPx[0]); // 250
    out.large     = resize(source, kTierPx[2]); // 1200
    return out;
}

} // namespace coremusic::artwork
```

## §6 C#

`immutable` + ETag (içerik hash) HTTP cache istemcisi — 304 akışı (§22.5).

```csharp
// ARTWORK — TASARIM ÖNERİSİ (master §22.5)
// Cache-Control: public, max-age=31536000, immutable + ETag (içerik hash)

using System.Net;
using System.Net.Http.Headers;

namespace CoreMusic.Media.Artwork;

public sealed record ArtworkCacheResult(bool FromCache, byte[]? Body, string? ETag);

public sealed class ArtworkCacheClient(HttpClient http)
{
    // §22.5: immutable + ETag — değişmeyen içerik için uzun ömür
    private const string ImmutableDirective = "public, max-age=31536000, immutable";

    public async Task<ArtworkCacheResult> GetAsync(string mbid, int tierPx, string? etag, CancellationToken ct)
    {
        var request = new HttpRequestMessage(HttpMethod.Get, $"caa://release/{mbid}/front-{tierPx}");
        if (etag is not null)
            request.Headers.TryAddWithoutValidation("If-None-Match", etag);

        using var response = await http.SendAsync(request, ct);

        if (response.StatusCode == HttpStatusCode.NotModified)
            return new ArtworkCacheResult(true, null, etag); // ETag isabet (§22.5)

        response.Headers.CacheControl = CacheControlHeaderValue.Parse(ImmutableDirective); // §22.5
        var body = await response.Content.ReadAsByteArrayAsync(ct);
        return new ArtworkCacheResult(false, body, response.Headers.ETag?.Tag); // ETag = içerik hash
    }
}
```

## §7 SQL

Varyant tablosu + içerik hash (dedup) + kademe sorgusu — şema K007-data'ya ait (master §2.2), DDL yalnız tasarım örneği.

```sql
-- ARTWORK — TASARIM ÖNERİSİ (master §9.3 · §22.4 · §22.5)
-- Şema K007-data'ya aittir (master §2.2) — bu DDL yalnız tasarım örneğidir.

CREATE TABLE artwork_variant (
    artwork_id   BIGINT      NOT NULL,
    tier         ENUM('thumbnail', 'medium', 'large') NOT NULL, -- §22.4 üç kademe
    width_px     SMALLINT    NOT NULL,                          -- 128 / 250 / 1200
    content_hash CHAR(64)    NOT NULL,                          -- ETag kaynağı (§22.5 içerik hash)
    byte_size    INT         NOT NULL,
    created_at   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (artwork_id, tier),
    -- içerik hash: aynı görsel ikinci kez indirilmez (dedup — TASARIM ÖNERİSİ)
    UNIQUE KEY uq_artwork_hash (content_hash)
) ENGINE=InnoDB;

-- Katalog kartı sorgusu: medium kademe (parametreli; SELECT * yasak)
SELECT a.artwork_id, a.tier, a.width_px, a.content_hash
FROM artwork_variant AS a
WHERE a.artwork_id = :artwork_id
  AND a.tier = 'medium';
```

## §8 Go

MusicBrainz/CAA rate-limit (1 istek/sn/IP) + zorunlu User-Agent'li fetch (§22.3 · §22.4).

```go
// ARTWORK — TASARIM ÖNERİSİ (master §22.3 · §22.4)
// MusicBrainz politikası: 1 istek/sn/IP + User-Agent ZORUNLU (§22.3)

package artwork

import (
	"context"
	"fmt"
	"io"
	"net/http"
	"time"
)

const (
	userAgent  = "CoreMusic/1.0 (https://coremusic.net; vault-steward@example.com)" // §22.3 zorunlu
	minInterval = 1 * time.Second // §22.3: 1 istek/sn/IP
)

type Fetcher struct {
	client *http.Client
	last   time.Time
}

// fetchTier: CAA resmi kademeleri 250/500/1200 px (§22.4)
func (f *Fetcher) fetchTier(ctx context.Context, mbid string, tierPx int) ([]byte, error) {
	if tierPx != 250 && tierPx != 500 && tierPx != 1200 {
		return nil, fmt.Errorf("CAA kademesi 250/500/1200 olmalı (master §22.4)")
	}

	if wait := minInterval - time.Since(f.last); wait > 0 {
		select { // 1 istek/sn/IP kısıtı (§22.3)
		case <-ctx.Done():
			return nil, ctx.Err()
		case <-time.After(wait):
		}
	}

	// Uç nokta K013-integraion'dan gelir (tasarım; URL kalıbı bu belgede sabitlenmez)
	req, err := http.NewRequestWithContext(ctx, http.MethodGet,
		fmt.Sprintf("caa://release/%s/front-%d", mbid, tierPx), nil)
	if err != nil {
		return nil, err
	}
	req.Header.Set("User-Agent", userAgent) // §22.3 zorunlu

	res, err := f.client.Do(req)
	if err != nil {
		return nil, err
	}
	defer res.Body.Close()
	f.last = time.Now()
	return io.ReadAll(res.Body)
}
```

## §9 Rust

Gömülü kapak çıkarma — etiket şeması format bazında (§22.3: ID3v2 · Vorbis Comment · `covr` · LIST-INFO).

```rust
// ARTWORK — TASARIM ÖNERİSİ (master §22.3)
// Gömülü kapak çıkarma: etiket şeması format bazında

/// MP3 → ID3v2 (v2.3 vs v2.4: tarih alanı TYER→TDRC kırılması — §22.3)
/// FLAC/Ogg → Vorbis Comment (§22.3 · FLAC standardı RFC 9639 §22.1)
/// MP4/M4A → `ilst` kutuları: `©nam` · `©ART` · `covr` (§22.3)
/// WAV → LIST-INFO (§22.3)
#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum ContainerFormat {
    Mp3,
    Flac,
    Mp4,
    Wav,
}

pub struct EmbeddedArt {
    pub mime: String, // örn. image/jpeg — görsel lisansı ayrıdır (§22.3)
    pub data: Vec<u8>,
}

pub enum ArtError {
    NoEmbeddedArt,
    Unsupported(ContainerFormat),
}

/// TASARIM ÖNERİSİ: çıkarım kütüphanesi kademe-4'te seçilecek (⚠️ UNKNOWN araç).
pub fn extract_embedded_art(
    format: ContainerFormat,
    tag_block: &[u8],
) -> Result<EmbeddedArt, ArtError> {
    match format {
        ContainerFormat::Mp3 => extract_id3v2_art(tag_block),
        ContainerFormat::Flac => extract_vorbis_comment_art(tag_block),
        ContainerFormat::Mp4 => extract_ilst_covr_art(tag_block), // `covr` kutusu (§22.3)
        ContainerFormat::Wav => Err(ArtError::Unsupported(ContainerFormat::Wav)), // LIST-INFO'da kapak alanı sınırlı
    }
}

fn extract_id3v2_art(_b: &[u8]) -> Result<EmbeddedArt, ArtError> {
    Err(ArtError::NoEmbeddedArt)
}
fn extract_vorbis_comment_art(_b: &[u8]) -> Result<EmbeddedArt, ArtError> {
    Err(ArtError::NoEmbeddedArt)
}
fn extract_ilst_covr_art(_b: &[u8]) -> Result<EmbeddedArt, ArtError> {
    Err(ArtError::NoEmbeddedArt)
}
```

## §10 Python

ETag = içerik hash (SHA-256) + immutable cache başlıkları (§22.5).

```python
# ARTWORK — TASARIM ÖNERİSİ (master §22.5)
# ETag = içerik hash; Cache-Control immutable

import hashlib

IMMUTABLE_CACHE_CONTROL = "public, max-age=31536000, immutable"  # §22.5


def content_etag(body: bytes) -> str:
    """İçerik hash'i ETag üretir (§22.5) — aynı içerik, aynı ETag."""
    digest = hashlib.sha256(body).hexdigest()
    return f'"{digest}"'


def cache_headers(body: bytes) -> dict[str, str]:
    """Kapak varyantı sunum başlıkları (§22.5)."""
    return {
        "Cache-Control": IMMUTABLE_CACHE_CONTROL,
        "ETag": content_etag(body),
        "Content-Type": "image/jpeg",  # TASARIM ÖNERİSİ — görsel tipi
    }


def needs_refetch(cached_etag: str | None, new_body: bytes) -> bool:
    """304 akışı: istemci If-None-Match ile ETag getirir (§22.5)."""
    return cached_etag != content_etag(new_body)
```

## §11 Java

Önbellek stratejisi seçimi — ARC scan-resistant → artwork (§22.5 gerekçesi birebir).

```java
// ARTWORK — TASARIM ÖNERİSİ (master §22.5)
// Önbellek stratejisi seçimi: LRU / LFU / ARC / TTL

package coremusic.media.artwork;

enum CacheStrategy { LRU, LFU, ARC, TTL }

final class CachePolicy {

    /**
     * master §22.5 gerekçesi:
     *  - LRU: genel önbellek; audio stream'de isabet ≈ 0 (tekil dinleme), scan'e dayanıksız
     *  - LFU: sık dinlenenler
     *  - ARC: scan-resistant — tarama ile kirletilmez → ARTWORK/KATALOG için tercih
     *  - TTL: zamanlayıcı temizlik
     */
    static CacheStrategy forContent(String contentType) {
        if (contentType.startsWith("image/")) {
            return CacheStrategy.ARC; // artwork → ARC (§22.5 TASARIM ÖNERİSİ)
        }
        if ("audio/stream".equals(contentType)) {
            return CacheStrategy.LRU; // stream isabet ≈ 0 → basit LRU (§22.5)
        }
        return CacheStrategy.TTL; // zamanlayıcı temizlik (§22.5)
    }

    static String immutableDirective() {
        return "public, max-age=31536000, immutable"; // §22.5
    }

    private CachePolicy() {}
}
```

## §12 Swift

Kademe seçili CAA fetch'i — User-Agent zorunlu (§22.3), kapak yoksa placeholder (§9.3).

```swift
// ARTWORK — TASARIM ÖNERİSİ (master §22.3 · §22.4 · §9.3)

import Foundation

enum ArtTier: Int, CaseIterable {
    case thumbnail = 128  // §22.4: ~64–128 px bandı
    case medium    = 500  // §22.4: CAA 500
    case large     = 1200 // §22.4: CAA 1200 (resmi en büyük kademe)
}

enum ArtworkResult {
    case cover(Data, etag: String?)
    case placeholder // §9.3: kapak yoksa placeholder
}

struct CoverArtClient {
    let session: URLSession

    // §22.3: MusicBrainz/CAA User-Agent ZORUNLU
    func fetch(mbid: String, tier: ArtTier) async -> ArtworkResult {
        // Uç nokta K013-integraion'dan gelir (tasarım)
        let url = URL(string: "caa://release/\(mbid)/front-\(tier.rawValue)")!
        var request = URLRequest(url: url)
        request.setValue("CoreMusic/1.0 (vault-steward@example.com)", forHTTPHeaderField: "User-Agent")

        guard let (data, response) = try? await session.data(for: request),
              let http = response as? HTTPURLResponse, http.statusCode == 200
        else {
            return .placeholder // §9.3
        }

        let etag = http.value(forHTTPHeaderField: "ETag") // içerik hash (§22.5)
        return .cover(data, etag: etag)
    }
}
```

## §13 Kotlin

Üç kademe varyant üretimi + içerik hash (ETag kaynağı) — §22.4 · §22.5.

```kotlin
// ARTWORK — TASARIM ÖNERİSİ (master §22.4 · §22.5)

package coremusic.media.artwork

import java.security.MessageDigest

// §22.4 doğrulanmış kademeler: thumbnail ~64–128 · medium ~250–500 · large ~640–1200
enum class ArtTier(val widthPx: Int) {
    THUMBNAIL(128),
    MEDIUM(500), // CAA 250/500 bandı
    LARGE(1200), // CAA 1200 (resmi en büyük kademe)
}

data class ArtVariant(val tier: ArtTier, val bytes: ByteArray, val contentHash: String)

// Görsel işleme arayüzü — araç seçimi kademe-4 (⚠️ UNKNOWN)
interface ImageResizer {
    fun resize(source: ByteArray, targetPx: Int): ByteArray
}

class VariantGenerator(private val resizer: ImageResizer) {

    fun generate(source: ByteArray): List<ArtVariant> =
        ArtTier.entries.map { tier ->
            val resized = resizer.resize(source, tier.widthPx)
            // içerik hash → ETag kaynağı (§22.5)
            ArtVariant(tier = tier, bytes = resized, contentHash = sha256Hex(resized))
        }

    private fun sha256Hex(bytes: ByteArray): String =
        MessageDigest.getInstance("SHA-256").digest(bytes).joinToString("") { "%02x".format(it) }
}
```

## §14 Ruby

immutable + ETag (içerik hash) sunum başlıkları ve 304 tazeliği (§22.5).

```ruby
# ARTWORK — TASARIM ÖNERİSİ (master §22.5)
# immutable + ETag (içerik hash) sunum

require 'digest'

module CoreMusic
  module Artwork
    IMMUTABLE = 'public, max-age=31536000, immutable'.freeze # §22.5

    module_function

    # ETag = içerik hash (§22.5)
    def content_etag(body)
      %("#{Digest::SHA256.hexdigest(body)}")
    end

    # Kapak varyantı yanıt başlıkları (§22.5)
    def cache_headers(body)
      {
        'Cache-Control' => IMMUTABLE,
        'ETag' => content_etag(body)
      }
    end

    # 304 akışı: If-None-Match eşleşirse gövde gönderilmez (§22.5)
    def fresh?(request_etag, body)
      !request_etag.nil? && request_etag == content_etag(body)
    end
  end
end
```

## §15 Elixir

Plug tarzı yanıt başlıkları + strateji gerekçesi (ARC artwork için — §22.5).

```elixir
# ARTWORK — TASARIM ÖNERİSİ (master §22.5)

defmodule CoreMusic.Artwork.Cache do
  @moduledoc """
  Kapak sunum önbelleği — master §22.5:
  * HTTP: `Cache-Control: public, max-age=31536000, immutable` + ETag (içerik hash)
  * Strateji: artwork/katalog → ARC (scan-resistant) — TASARIM ÖNERİSİ
  * LRU: audio stream'de isabet ≈ 0 (tekil dinleme) — §22.5
  """

  @immutable "public, max-age=31536000, immutable" # §22.5

  # ETag = içerik hash (§22.5)
  def etag(body) when is_binary(body) do
    "\"" <> Base.encode16(:crypto.hash(:sha256, body), case: :lower) <> "\""
  end

  def response_headers(body) do
    [{"cache-control", @immutable}, {"etag", etag(body)}]
  end

  # 304: If-None-Match eşleşirse gövde yok (§22.5)
  def not_modified?(incoming_etag, body) when is_binary(incoming_etag) do
    incoming_etag == etag(body)
  end

  def not_modified?(_incoming, _body), do: false

  # ARC gerekçesi (§22.5): scan-resistant → tarama kirletmez
  def strategy_for(:artwork), do: :arc
  def strategy_for(:catalog), do: :arc
  def strategy_for(:stream), do: :lru
end
```

## §16 Scala

Kademe varyant modeli + ARC strateji gerekçesi (§22.4 · §22.5).

```scala
// ARTWORK — TASARIM ÖNERİSİ (master §22.4 · §22.5)

package coremusic.media.artwork

import java.security.MessageDigest

// §22.4: doğrulanmış CAA kademeleri 250/500/1200 px + thumbnail bandı ~64–128
sealed trait ArtTier { def widthPx: Int }

object ArtTier {
  case object Thumbnail extends ArtTier { val widthPx = 128 }  // ~64–128 bandı (§22.4)
  case object Medium    extends ArtTier { val widthPx = 500 }  // CAA 500
  case object Large     extends ArtTier { val widthPx = 1200 } // CAA 1200 (resmi en büyük)
  val all: List[ArtTier] = List(Thumbnail, Medium, Large)
}

final case class ArtVariant(tier: ArtTier, bytes: Vector[Byte], contentHash: String)

object VariantFactory {

  // resize: görsel kütüphanesi kademe-4'te seçilecek (⚠️ UNKNOWN)
  def generate(source: Vector[Byte])(resize: (Vector[Byte], Int) => Vector[Byte]): List[ArtVariant] =
    ArtTier.all.map { tier =>
      val resized = resize(source, tier.widthPx)
      ArtVariant(tier, resized, contentHash(resized)) // içerik hash → ETag (§22.5)
    }

  private def contentHash(bytes: Vector[Byte]): String =
    MessageDigest.getInstance("SHA-256")
      .digest(bytes.toArray)
      .map("%02x".format(_))
      .mkString
}

// §22.5 strateji gerekçesi: ARC scan-resistant → artwork için tercih
object CacheStrategy {
  sealed trait Strategy
  case object Arc extends Strategy // scan-resistant (§22.5 TASARIM ÖNERİSİ)
  case object Lru extends Strategy // stream isabet ≈ 0 (§22.5)

  def forArtwork(): Strategy = Arc
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | media-master §9.3 | `K014-media/media-master.md` §9.3 | artwork rol · sınır · placeholder (§9.3 TASARIM İDDİASI) |
| 2 | media-master §22.3 | `K014-media/media-master.md` §22.3 | CAA 250/500/1200 · MusicBrainz 1 istek/sn + User-Agent · etiket şeması (ID3v2 · Vorbis Comment · `covr` · LIST-INFO) |
| 3 | media-master §22.4 | `K014-media/media-master.md` §22.4 | varyant kademeleri: thumbnail ~64–128 · medium ~250–500 · large ~640–1200 |
| 4 | media-master §22.5 | `K014-media/media-master.md` §22.5 | `Cache-Control: immutable` + ETag (içerik hash) · ARC/LRU/LFU/TTL |
| 5 | media-master §22.1/§22.7 | `K014-media/media-master.md` §22.1 · §22.7 | RFC 9639 (FLAC) · ISO/IEC 14496-3 (ID3v2.4/MP4 ilst) · satır kanıtları `media-dogrulama.md` §3a/§3b |
| 6 | Format referansı | `K015-AI/ai-ornekler.md` | ornekler.md şablon/formatı |

**Authority:** Bayram Ali / Vault Steward
