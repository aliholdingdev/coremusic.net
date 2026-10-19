---
node_id: K014-media/catalog/ornekler
node_type: module
status: draft
parent_id: K014-media/catalog
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.4"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/catalog — ornekler.md

> **Statü:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** niteliğindedir — K014-media/catalog kademe-2 bir **design node**'dur; medya düğümü repo'da henüz kodu YOKtur. Örnekler yalnız `media-master.md` §9.4 + §22 (doğrulanmış standartlar) temeline oturur; §22'de olmayan standart/sürüm/ad yazılmaz (zero-hallucination). Konu eksenleri: katalog indeks/arama · içerik hash ile dedup · sıralı gezinme sorguları · ARC önbellek gerekçesi.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Statü | TASARIM ÖNERİSİ — kademe-2 design node, repo kodu YOK |
| 2 | Dayanak | yalnız `media-master.md` §9.4 + §22 (doğrulanmış standartlar) |
| 3 | Uydurma | yasak (zero-hallucination) — §22'de olmayan standart/sürüm/ad yazılmaz |
| 4 | Arama yöntemi | tam metin/etiket — **⚠️ UNKNOWN** (§9.4); örnekler alt-dize filtresi TASARIM'idır |
| 5 | Sınır | arama motoru/sorgu servisi K012/music-service ile belirsiz (§7/1) |
| 6 | Önbellek | katalog → **ARC** (scan-resistant) + `immutable`/ETag (§22.5 TASARIM ÖNERİSİ) |
| 7 | Kimlik | MusicBrainz birincil — **1 istek/sn/IP** · **User-Agent zorunlu** (§22.3) |
| 8 | İçerik hash | dedup anahtarı — ETag (içerik hash) kavramının katalog karşılığı (§22.5) |
| 9 | Değişiklik | yok — bu dosya yalnız tasarım örneği üretir; kaynak kod okumaz/yazmaz |

## §2 PHP

Parametreli katalog araması + sıralı gezinme (şema K007-data'ya ait — master §2.2).

```php
<?php
declare(strict_types=1);

// CATALOG — TASARIM ÖNERİSİ (K014-media/catalog · master §9.4 · §2.2)
// Arama yöntemi ⚠️ UNKNOWN (master §9.4) — bu örnek parametreli LIKE tabanlı bir TASARIM'dır.
// Şema K007-data'ya ait (master §2.2).

final class CatalogSearch
{
    public function __construct(private PDO $pdo) {}

    /**
     * Arama: koleksiyonun indekslenmiş hali üzerinde filtre (master §9.4).
     *
     * @return list<array{track_id: int, title: string, artist: string, year: int}>
     */
    public function search(string $term, int $limit = 50): array
    {
        $sql = 'SELECT c.track_id, c.title, c.artist, c.year
                FROM catalog_index AS c
                WHERE c.title LIKE :q OR c.artist LIKE :q
                ORDER BY c.artist ASC, c.year DESC -- sıralı gezinme (TASARIM ÖNERİSİ)
                LIMIT :lim';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':q', '%' . $term . '%');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

## §3 JavaScript

Sıralı gezinme (başlık/yıl/sanatçı) + indekslenmiş koleksiyonda arama (§9.4).

```js
// CATALOG — TASARIM ÖNERİSİ (master §9.4)
// Sıralı gezinme + arama: koleksiyonun indekslenmiş hali (§9.4)

export function browseSorted(entries, { by = 'title', dir = 'asc' } = {}) {
  const factor = dir === 'asc' ? 1 : -1;
  const collator = new Intl.Collator(undefined, { sensitivity: 'base' });

  return [...entries].sort((a, b) => {
    switch (by) {
      case 'year':
        return factor * (a.year - b.year);
      case 'artist':
        return factor * collator.compare(a.artist, b.artist);
      case 'title':
      default:
        return factor * collator.compare(a.title, b.title);
    }
  });
}

// Arama yöntemi ⚠️ UNKNOWN (§9.4) — alt-dize filtresi TASARIM'idır
export function searchCatalog(entries, query) {
  const q = query.trim().toLocaleLowerCase();
  return entries.filter(
    (e) => e.title.toLocaleLowerCase().includes(q) || e.artist.toLocaleLowerCase().includes(q),
  );
}
```

## §4 TypeScript

Tür güvenli katalog kaydı + içerik hash ile dedup (§9.4 · §22.5).

```ts
// CATALOG — TASARIM ÖNERİSİ (master §9.4 · §22.5)

export interface CatalogEntry {
  trackId: number;
  title: string;
  artist: string;
  year: number;
  contentHash: string; // dedup anahtarı — içerik hash (§22.5 ETag kavramı — TASARIM ÖNERİSİ)
}

export type SortKey = 'title' | 'artist' | 'year';

// İçerik hash ile mükerrer kayıt önleme (TASARIM ÖNERİSİ · hash kavramı §22.5)
export function dedupeByContentHash(entries: readonly CatalogEntry[]): CatalogEntry[] {
  const seen = new Map<string, CatalogEntry>();
  for (const e of entries) {
    if (!seen.has(e.contentHash)) seen.set(e.contentHash, e);
  }
  return [...seen.values()];
}

export function sortCatalog(entries: readonly CatalogEntry[], key: SortKey): CatalogEntry[] {
  return [...entries].sort((a, b) => {
    const av = a[key];
    const bv = b[key];
    return typeof av === 'number' && typeof bv === 'number'
      ? av - bv
      : String(av).localeCompare(String(bv));
  });
}
```

## §5 C++

İçerik hash ile dedup — ETag (içerik hash) kavramının katalog karşılığı (§22.5).

```cpp
// CATALOG — TASARIM ÖNERİSİ (master §22.5)
// İçerik hash ile dedup — ETag (içerik hash) kavramının katalog karşılığı (§22.5)

#include <cstdint>
#include <string>
#include <string_view>
#include <unordered_set>
#include <vector>

namespace coremusic::catalog {

// SHA-256 sağlayıcı kademe-4'te seçilecek (⚠️ UNKNOWN araç — tasarım arayüzü)
using ContentHasher = std::string (*)(std::string_view);

struct Entry {
    std::uint64_t track_id;
    std::string title;
    std::string content_hash; // §22.5: içerik hash (dedup anahtarı)
};

// Mükerrerleri ele; ilk kayıt kalır (TASARIM ÖNERİSİ)
[[nodiscard]] std::vector<Entry> dedupeByContentHash(
    const std::vector<Entry>& entries,
    ContentHasher /* hash_fn */)
{
    std::vector<Entry> out;
    std::unordered_set<std::string> seen;
    out.reserve(entries.size());
    for (const auto& e : entries) {
        if (seen.insert(e.content_hash).second) {
            out.push_back(e);
        }
    }
    return out;
}

} // namespace coremusic::catalog
```

## §6 C#

ARC önbellek gerekçesi + immutable/ETag katalog sunumu (§22.5).

```csharp
// CATALOG — TASARIM ÖNERİSİ (master §22.5)
// ARC: scan-resistant — tarama ile kirletilmez → katalog için tercih (§22.5)

using System.Security.Cryptography;

namespace CoreMusic.Media.Catalog;

public enum CacheStrategy { Lru, Lfu, Arc, Ttl }

public static class CatalogCachePolicy
{
    /// <summary>
    /// master §22.5 gerekçesi: LRU genel önbellek (audio stream isabet ≈ 0 — tekil dinleme) ·
    /// LFU sık dinlenenler · ARC scan-resistant (katalog tercihi) · TTL zamanlayıcı temizlik.
    /// </summary>
    public static CacheStrategy ForCatalog() => CacheStrategy.Arc;

    // Katalog sayfası sunum başlıkları — immutable + ETag (§22.5)
    public const string ImmutableDirective = "public, max-age=31536000, immutable";

    public static string ETagFor(byte[] body)
    {
        var hash = SHA256.HashData(body);
        return "\"" + Convert.ToHexStringLower(hash) + "\""; // içerik hash (§22.5)
    }
}
```

## §7 SQL

Katalog indeks tablosu — içerik hash (dedup) + sıralı gezinme anahtarı (şema K007-data'ya ait — master §2.2).

```sql
-- CATALOG — TASARIM ÖNERİSİ (master §9.4 · §22.3 · §22.5)
-- Şema K007-data'ya aittir (master §2.2) — bu DDL yalnız tasarım örneğidir.

CREATE TABLE catalog_index (
    track_id     BIGINT       NOT NULL,
    title        VARCHAR(255) NOT NULL,
    artist       VARCHAR(255) NOT NULL,
    album        VARCHAR(255) NULL,
    year         SMALLINT     NULL,
    content_hash CHAR(64)     NOT NULL,                -- dedup anahtarı (§22.5 içerik hash)
    mbid         CHAR(36)     NULL,                    -- MusicBrainz birincil kimlik (§22.3)
    PRIMARY KEY (track_id),
    UNIQUE KEY uq_catalog_hash (content_hash),         -- mükerrer kayıt yok (TASARIM ÖNERİSİ)
    KEY idx_catalog_browse (artist, album, year)       -- sıralı gezinme (TASARIM ÖNERİSİ)
) ENGINE=InnoDB;

-- Sıralı gezinme sorgusu (SELECT * yasak; parametreli limit)
SELECT c.track_id, c.title, c.artist, c.year
FROM catalog_index AS c
ORDER BY c.artist ASC, c.year DESC
LIMIT 100;
```

## §8 Go

MusicBrainz kimlik eşlemesi — 1 istek/sn/IP + zorunlu User-Agent (§22.3).

```go
// CATALOG — TASARIM ÖNERİSİ (master §22.3)
// MusicBrainz birincil kimlik: 1 istek/sn/IP + User-Agent ZORUNLU (§22.3)

package catalog

import (
	"context"
	"net/http"
	"time"
)

const (
	userAgent   = "CoreMusic/1.0 (https://coremusic.net; vault-steward@example.com)" // §22.3 zorunlu
	minInterval = 1 * time.Second                                                   // §22.3: 1 istek/sn/IP
)

type IdentitySync struct {
	client   *http.Client
	endpoint string // K013-integraion'dan gelir (tasarım; URL bu belgede sabitlenmez)
	last     time.Time
}

// lookupMBID: katalog kimliği MusicBrainz'den (§22.3 birincil kimlik).
// Sınır: dış katalog eşitlemesi K013-integraion üzerinden (master §2.2).
func (s *IdentitySync) lookupMBID(ctx context.Context, artist, title string) (string, error) {
	if wait := minInterval - time.Since(s.last); wait > 0 {
		select { // 1 istek/sn/IP kısıtı (§22.3)
		case <-ctx.Done():
			return "", ctx.Err()
		case <-time.After(wait):
		}
	}

	req, err := http.NewRequestWithContext(ctx, http.MethodGet, s.endpoint, nil)
	if err != nil {
		return "", err
	}
	q := req.URL.Query()
	q.Set("artist", artist)
	q.Set("recording", title)
	req.URL.RawQuery = q.Encode()
	req.Header.Set("User-Agent", userAgent) // §22.3 zorunlu

	// Yanıt işleme + katalog indeksine yazım TASARIM ÖNERİSİ (kademe-4).
	s.last = time.Now()
	_ = req
	return "", nil
}
```

## §9 Rust

Katalog araması + sıralı gezinme — yöntem ⚠️ UNKNOWN (§9.4), örnek alt-dize filtresi.

```rust
// CATALOG — TASARIM ÖNERİSİ (master §9.4)
// Arama yöntemi ⚠️ UNKNOWN (master §9.4) — bu örnek alt-dize filtre TASARIM'idır.

use std::cmp::Ordering;

#[derive(Debug, Clone)]
pub struct CatalogEntry {
    pub track_id: u64,
    pub title: String,
    pub artist: String,
    pub year: Option<u16>,
    pub content_hash: String, // dedup anahtarı (§22.5 içerik hash — TASARIM ÖNERİSİ)
}

/// Tam metin/etiket araması henüz ⚠️ UNKNOWN (§9.4) — burada alt-dize filtresi.
pub fn search<'a>(entries: &'a [CatalogEntry], query: &str) -> Vec<&'a CatalogEntry> {
    let q = query.to_lowercase();
    entries
        .iter()
        .filter(|e| {
            e.title.to_lowercase().contains(&q) || e.artist.to_lowercase().contains(&q)
        })
        .collect()
}

/// Sıralı gezinme (TASARIM ÖNERİSİ): sanatçı → yıl (yeni önce)
pub fn browse_sorted(entries: &mut [CatalogEntry]) {
    entries.sort_by(|a, b| match a.artist.cmp(&b.artist) {
        Ordering::Equal => b.year.cmp(&a.year),
        other => other,
    });
}
```

## §10 Python

ARC gerekçesi + sıralı gezinme sayfalama + immutable/ETag başlıkları (§22.5).

```python
# CATALOG — TASARIM ÖNERİSİ (master §22.5 · §9.4)

import hashlib
from dataclasses import dataclass

# §22.5 strateji gerekçesi:
#   LRU — genel önbellek; audio stream isabet ≈ 0 (tekil dinleme), scan'e dayanıksız
#   LFU — sık dinlenenler
#   ARC — scan-resistant: tarama ile kirletilmez → KATALOG için tercih (TASARIM ÖNERİSİ)
#   TTL — zamanlayıcı temizlik
CACHE_STRATEGY = {"catalog": "ARC", "artwork": "ARC", "stream": "LRU"}

IMMUTABLE = "public, max-age=31536000, immutable"  # §22.5


@dataclass(frozen=True)
class CatalogPage:
    items: list[dict]
    offset: int
    limit: int


def browse_page(entries: list[dict], offset: int = 0, limit: int = 50) -> CatalogPage:
    """Sıralı gezinme sayfası — sanatçı/albüm/yıl (TASARIM ÖNERİSİ)."""
    ordered = sorted(entries, key=lambda e: (e["artist"], e["album"], -e["year"]))
    return CatalogPage(items=ordered[offset : offset + limit], offset=offset, limit=limit)


def cache_headers_for_catalog(body: bytes) -> dict[str, str]:
    """Katalog sayfası immutable sunumu — ETag = içerik hash (§22.5)."""
    etag = '"' + hashlib.sha256(body).hexdigest() + '"'
    return {"Cache-Control": IMMUTABLE, "ETag": etag}
```

## §11 Java

Katalog önbelleği ARC — scan-resistant gerekçesi (§22.5 birebir).

```java
// CATALOG — TASARIM ÖNERİSİ (master §22.5)
// Katalog önbelleği ARC — scan-resistant (§22.5 TASARIM ÖNERİSİ)

package coremusic.media.catalog;

import java.util.EnumMap;
import java.util.Map;

enum CacheContent { CATALOG, ARTWORK, AUDIO_STREAM }

enum CacheStrategy { LRU, LFU, ARC, TTL }

final class CatalogCacheRationale {

    // master §22.5 satır satır gerekçe
    private static final Map<CacheContent, CacheStrategy> POLICY =
            new EnumMap<>(Map.of(
                    CacheContent.CATALOG, CacheStrategy.ARC,      // scan-resistant (§22.5)
                    CacheContent.ARTWORK, CacheStrategy.ARC,      // scan-resistant (§22.5)
                    CacheContent.AUDIO_STREAM, CacheStrategy.LRU  // isabet ≈ 0 (§22.5)
            ));

    static CacheStrategy forContent(CacheContent content) {
        return POLICY.getOrDefault(content, CacheStrategy.TTL); // TTL: zamanlayıcı temizlik (§22.5)
    }

    private CatalogCacheRationale() {}
}
```

## §12 Swift

Katalog arama/gezinme + içerik hash ile dedup (§9.4 · §22.5).

```swift
// CATALOG — TASARIM ÖNERİSİ (master §9.4 · §22.5)
// Arama yöntemi ⚠️ UNKNOWN (master §9.4) — alt-dize filtresi örneği.

import Foundation

struct CatalogEntry: Identifiable, Hashable {
    let trackId: Int
    let title: String
    let artist: String
    let year: Int
    let contentHash: String // dedup anahtarı (§22.5 içerik hash — TASARIM ÖNERİSİ)
}

enum CatalogQuery {
    // Sıralı gezinme: başlık (yerel duyarlı karşılaştırma — TASARIM ÖNERİSİ)
    static func browse(_ entries: [CatalogEntry]) -> [CatalogEntry] {
        entries.sorted { $0.title.localizedCaseInsensitiveCompare($1.title) == .orderedAscending }
    }

    // Arama: koleksiyonun indekslenmiş hali üzerinde filtre (§9.4)
    static func search(_ entries: [CatalogEntry], matching term: String) -> [CatalogEntry] {
        entries.filter {
            $0.title.localizedCaseInsensitiveContains(term) ||
            $0.artist.localizedCaseInsensitiveContains(term)
        }
    }

    // İçerik hash ile dedup (TASARIM ÖNERİSİ · hash kavramı §22.5)
    static func dedupe(_ entries: [CatalogEntry]) -> [CatalogEntry] {
        var seen = Set<String>()
        return entries.filter { seen.insert($0.contentHash).inserted }
    }
}
```

## §13 Kotlin

İçerik hash ile dedup + sıralı gezinme + ETag kaynağı (§22.5).

```kotlin
// CATALOG — TASARIM ÖNERİSİ (master §22.5 · §9.4)
// İçerik hash ile dedup — ETag (içerik hash) kavramı (§22.5)

package coremusic.media.catalog

import java.security.MessageDigest

data class CatalogEntry(
    val trackId: Long,
    val title: String,
    val artist: String,
    val year: Int,
    val contentHash: String, // dedup anahtarı (§22.5)
)

object CatalogIndex {

    // Mükerrerleri ele; ilk kayıt kalır (TASARIM ÖNERİSİ)
    fun dedupeByContentHash(entries: List<CatalogEntry>): List<CatalogEntry> {
        val seen = HashSet<String>(entries.size)
        return entries.filter { seen.add(it.contentHash) }
    }

    // Sıralı gezinme (TASARIM ÖNERİSİ): sanatçı → yıl (yeni önce)
    fun browseSorted(entries: List<CatalogEntry>): List<CatalogEntry> =
        entries.sortedWith(compareBy({ it.artist }, { -it.year }))

    // Dosya gövdesinden içerik hash — ETag kaynağı (§22.5)
    fun contentHash(bytes: ByteArray): String =
        MessageDigest.getInstance("SHA-256").digest(bytes)
            .joinToString("") { "%02x".format(it) }
}
```

## §14 Ruby

Sıralı gezinme + arama + immutable/ETag katalog sunumu (§9.4 · §22.5).

```ruby
# CATALOG — TASARIM ÖNERİSİ (master §9.4 · §22.5)

require 'digest'

module CoreMusic
  module Catalog
    Entry = Struct.new(:track_id, :title, :artist, :year, :content_hash, keyword_init: true)

    module_function

    # Sıralı gezinme: sanatçı → yıl (yeni önce) — TASARIM ÖNERİSİ
    def browse_sorted(entries)
      entries.sort_by { |e| [e.artist, -e.year] }
    end

    # Arama: koleksiyonun indekslenmiş hali (master §9.4 — yöntem ⚠️ UNKNOWN)
    def search(entries, term)
      q = term.downcase
      entries.select { |e| e.title.downcase.include?(q) || e.artist.downcase.include?(q) }
    end

    # İçerik hash ile dedup (§22.5 ETag kavramı — TASARIM ÖNERİSİ)
    def dedupe_by_content_hash(entries)
      entries.uniq(&:content_hash)
    end

    # Katalog sayfası immutable sunumu (§22.5)
    def cache_headers(body)
      {
        'Cache-Control' => 'public, max-age=31536000, immutable',
        'ETag' => %("#{Digest::SHA256.hexdigest(body)}") # içerik hash (§22.5)
      }
    end
  end
end
```

## §15 Elixir

Katalog cache strateji gerekçesi (ARC) + sıralı gezinme/arama (§22.5 · §9.4).

```elixir
# CATALOG — TASARIM ÖNERİSİ (master §22.5 · §9.4)

defmodule CoreMusic.Catalog.Cache do
  @moduledoc """
  Katalog önbelleği — master §22.5:
  * ARC: scan-resistant — tarama ile kirletilmez → katalog için tercih (TASARIM ÖNERİSİ)
  * LRU: audio stream'de isabet ≈ 0 (tekil dinleme)
  * immutable + ETag (içerik hash) HTTP sunumu
  """

  @immutable "public, max-age=31536000, immutable" # §22.5

  # §22.5 strateji seçimi
  def strategy_for(:catalog), do: :arc
  def strategy_for(:artwork), do: :arc
  def strategy_for(:stream), do: :lru

  # ETag = içerik hash (§22.5)
  def etag(body) when is_binary(body) do
    "\"" <> Base.encode16(:crypto.hash(:sha256, body), case: :lower) <> "\""
  end

  def response_headers(body) do
    [{"cache-control", @immutable}, {"etag", etag(body)}]
  end

  # Sıralı gezinme (TASARIM ÖNERİSİ): sanatçı → yıl (yeni önce)
  def browse_sorted(entries) do
    Enum.sort_by(entries, &{&1.artist, -&1.year})
  end

  # Arama (master §9.4 — yöntem ⚠️ UNKNOWN; alt-dize filtresi örneği)
  def search(entries, term) when is_binary(term) do
    q = String.downcase(term)

    Enum.filter(entries, fn e ->
      String.contains?(String.downcase(e.title), q) or
        String.contains?(String.downcase(e.artist), q)
    end)
  end
end
```

## §16 Scala

Katalog arama/gezinme + içerik hash dedup + ARC strateji gerekçesi (§9.4 · §22.5).

```scala
// CATALOG — TASARIM ÖNERİSİ (master §9.4 · §22.5)

package coremusic.media.catalog

import java.security.MessageDigest

final case class CatalogEntry(
    trackId: Long,
    title: String,
    artist: String,
    year: Int,
    contentHash: String // dedup anahtarı (§22.5 içerik hash — TASARIM ÖNERİSİ)
)

object CatalogIndex {

  // Sıralı gezinme (TASARIM ÖNERİSİ): sanatçı → yıl (yeni önce)
  def browseSorted(entries: List[CatalogEntry]): List[CatalogEntry] =
    entries.sortBy(e => (e.artist, -e.year))

  // Arama (master §9.4 — yöntem ⚠️ UNKNOWN; alt-dize filtresi örneği)
  def search(entries: List[CatalogEntry], term: String): List[CatalogEntry] = {
    val q = term.toLowerCase
    entries.filter(e => e.title.toLowerCase.contains(q) || e.artist.toLowerCase.contains(q))
  }

  // İçerik hash ile dedup (TASARIM ÖNERİSİ · hash kavramı §22.5)
  def dedupeByContentHash(entries: List[CatalogEntry]): List[CatalogEntry] = {
    val seen = scala.collection.mutable.Set.empty[String]
    entries.filter(e => seen.add(e.contentHash))
  }

  // Dosya gövdesinden içerik hash — ETag kaynağı (§22.5)
  def contentHash(bytes: Array[Byte]): String =
    MessageDigest.getInstance("SHA-256").digest(bytes).map("%02x".format(_)).mkString

  // §22.5: katalog → ARC (scan-resistant); stream → LRU (isabet ≈ 0)
  def cacheStrategyFor(domain: String): String =
    if (domain == "stream") "LRU" else "ARC"
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | media-master §9.4 | `K014-media/media-master.md` §9.4 | catalog rol · K012/music-service sınırı · içerik: indekslenmiş koleksiyon · arama yöntemi ⚠️ UNKNOWN |
| 2 | media-master §3/4 | `K014-media/media-master.md` §3 | "Dizin/liste verisi — arama kaynağı olabilir" (v2 §2.15 birebir) |
| 3 | media-master §22.3 | `K014-media/media-master.md` §22.3 | MusicBrainz 1 istek/sn + User-Agent · CAA 250/500/1200 · etiket şeması (indeks alanları) |
| 4 | media-master §22.5 | `K014-media/media-master.md` §22.5 | ARC (scan-resistant) · LRU/LFU/TTL · `immutable` + ETag (içerik hash) |
| 5 | media-master §22.7 | `K014-media/media-master.md` §22.7 | standart kaynakları özeti — satır kanıtları `media-dogrulama.md` §3a/§3b |
| 6 | Format referansı | `K015-AI/ai-ornekler.md` | ornekler.md şablon/formatı |

**Authority:** Bayram Ali / Vault Steward
