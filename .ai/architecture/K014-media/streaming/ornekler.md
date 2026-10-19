---
node_id: K014-media/streaming/ornekler
node_type: module
status: draft
parent_id: K014-media/streaming
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.6"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/streaming — ornekler.md

> **Not:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** illustrative design örnekleridir
> (kademe-2 design node — repo'da henüz bu alanın kodu YOK). Dayanak: media-master §9.6
> (akışla dinleme · taşıma K010 · dış kaynak K013 · offline fallback) + §22 (doğrulanmış
> protokoller: HTTP Range RFC 9110 §22.2 · HLS RFC 8216 §22.2 · DASH ISO/IEC 23009-1 §22.2 ·
> cache ETag/immutable §22.5). Kod yorumları İngilizcedir; dosya salt-okunur vault içindir.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Örnek statüsü | hepsi TASARIM ÖNERİSİ — repo'da bu alan kodu YOK |
| 2 | Dayanak | media-master §9.6 + §22 (uydurma protokol yasak) |
| 3 | Range semantiği | `Range: bytes=a-b` → 206 + `Content-Range` (RFC 9110 · master §22.2) |
| 4 | Cache | `ETag` (içerik hash) + `Cache-Control: public, max-age=31536000, immutable` (master §22.5) |
| 5 | Manifest | M3U8-style playlist = HLS (RFC 8216); DASH segment/byte-range (ISO/IEC 23009-1) |
| 6 | Sınır | taşıma K010-network · dış kaynak K013-integraion — bu dosya yalnız oturum/yanıt düzenini gösterir |

## §2 PHP

HTTP `Range` başlığı çözümleyici → 206 Partial Content + `Content-Range` + `ETag`.

```php
<?php
declare(strict_types=1);

/**
 * K014-media/streaming — HTTP Range partial-content handler (TASARIM ÖNERİSİ).
 * Grounded in K014-media/media-master.md §22.2 (RFC 9110 Range) + §22.5 (ETag/immutable).
 * No repo code yet — illustrative design example.
 */
final class RangeHandler
{
    /** @return array{status:int, headers:array<string,string>} */
    public function handle(?string $rangeHeader, int $fileSize, string $etag): array
    {
        $base = [
            'Accept-Ranges' => 'bytes',
            'ETag'          => $etag,        // içerik hash — master §22.5
            'Cache-Control' => 'public, max-age=31536000, immutable', // master §22.5
        ];

        // If-None-Match eşleşmesi -> 304 (ETag revalidation — master §22.5)
        if ($rangeHeader === null) {
            return ['status' => 200, 'headers' => $base]; // Range yok -> tam içerik
        }

        // RFC 9110: "Range: bytes=start-end" (tek aralık; çoklu aralık reddedilir)
        if (!preg_match('/^bytes=(\d*)-(\d*)$/', $rangeHeader, $m) || ($m[1] === '' && $m[2] === '')) {
            return ['status' => 416, 'headers' => $base + ['Content-Range' => "bytes */{$fileSize}"]];
        }
        $start = $m[1] === '' ? $fileSize - (int) $m[2] : (int) $m[1];
        $end   = $m[2] === '' ? $fileSize - 1 : min((int) $m[2], $fileSize - 1);

        if ($start > $end || $start >= $fileSize) {
            return ['status' => 416, 'headers' => $base + ['Content-Range' => "bytes */{$fileSize}"]];
        }

        $length = $end - $start + 1;
        return [
            'status' => 206, // Partial Content — master §22.2
            'headers' => $base + [
                'Content-Range'  => "bytes {$start}-{$end}/{$fileSize}",
                'Content-Length' => (string) $length,
                'Content-Type'   => 'audio/mpeg', // MP3 · FLAC/WAV content-type ayrıca (§22.1)
            ],
        ];
    }
}
```

## §3 JavaScript

Range-aware fetch handler (service worker stili) + 304/206/416 kararları.

```js
// K014-media/streaming — Range-aware fetch handler (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.

async function sha256Hex(buf) {
  const digest = await crypto.subtle.digest('SHA-256', buf);
  return [...new Uint8Array(digest)].map(b => b.toString(16).padStart(2, '0')).join('');
}

async function handleMediaRequest(request, file /* Blob */, etag) {
  const cacheHeaders = {
    'Accept-Ranges': 'bytes',
    'ETag': etag,
    'Cache-Control': 'public, max-age=31536000, immutable', // master §22.5
  };

  // If-None-Match eşleşmesi -> 304 (cache revalidation — master §22.5)
  if (request.headers.get('If-None-Match') === etag) {
    return new Response(null, { status: 304, headers: cacheHeaders });
  }

  const range = request.headers.get('Range');
  if (!range) {
    return new Response(file, { status: 200, headers: { ...cacheHeaders, 'Content-Length': file.size } });
  }

  const m = /^bytes=(\d*)-(\d*)$/.exec(range);
  if (!m || (m[1] === '' && m[2] === '')) {
    return new Response(null, { status: 416, headers: { ...cacheHeaders, 'Content-Range': `bytes */${file.size}` } });
  }

  const start = m[1] === '' ? file.size - Number(m[2]) : Number(m[1]);
  const end = m[2] === '' ? file.size - 1 : Math.min(Number(m[2]), file.size - 1);

  if (start > end || start >= file.size) {
    return new Response(null, { status: 416, headers: { ...cacheHeaders, 'Content-Range': `bytes */${file.size}` } });
  }

  // 206 Partial Content + Content-Range (master §22.2)
  return new Response(file.slice(start, end + 1), {
    status: 206,
    headers: {
      ...cacheHeaders,
      'Content-Range': `bytes ${start}-${end}/${file.size}`,
      'Content-Length': String(end - start + 1),
    },
  });
}
```

## §4 TypeScript

Typed `Range` çözümleyici + HLS (RFC 8216) M3U8 media playlist üretici.

```ts
// K014-media/streaming — typed Range parse + M3U8 manifest (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110 · RFC 8216 HLS).
// No repo code yet — illustrative design example.

export type RangeResult =
  | { kind: 'ok'; start: number; end: number }
  | { kind: 'unsatisfiable'; fileSize: number };

export function parseRange(header: string | null, fileSize: number): RangeResult | null {
  if (!header) return null; // Range yok -> 200 tam içerik
  const m = /^bytes=(\d*)-(\d*)$/.exec(header);
  if (!m || (m[1] === '' && m[2] === '')) return { kind: 'unsatisfiable', fileSize };
  const start = m[1] === '' ? fileSize - Number(m[2]) : Number(m[1]);
  const end = m[2] === '' ? fileSize - 1 : Math.min(Number(m[2]), fileSize - 1);
  if (start > end || start >= fileSize) return { kind: 'unsatisfiable', fileSize };
  return { kind: 'ok', start, end };
}

// HLS media playlist (RFC 8216 — master §22.2): EXTINF + segment URI + TARGETDURATION.
export function buildMediaPlaylist(segments: { uri: string; durationSec: number }[]): string {
  const maxDur = Math.max(...segments.map(s => s.durationSec));
  const lines = [
    '#EXTM3U',
    '#EXT-X-VERSION:3',
    `#EXT-X-TARGETDURATION:${Math.ceil(maxDur)}`,
    '#EXT-X-MEDIA-SEQUENCE:0',
  ];
  for (const s of segments) lines.push(`#EXTINF:${s.durationSec.toFixed(3)},`, s.uri);
  lines.push('#EXT-X-ENDLIST');
  return lines.join('\n') + '\n';
}
```

## §5 C++

DASH/HLS segment byte-range düzeni + `Range`/`Content-Range` başlık üretimi.

```cpp
// K014-media/streaming — segment byte-range reader (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110 Range · ISO/IEC 23009-1 DASH byte range).
// No repo code yet — illustrative design example.
#include <cstdint>
#include <string>

namespace cm::streaming {

// DASH/HLS segmenti: tek dosya üzerinde byte-range (master §22.2).
struct ByteRange {
    std::uint64_t offset;  // first byte
    std::uint64_t length;  // number of bytes
};

struct Segment {
    std::string uri;
    ByteRange   range;     // bos range -> tum dosya
    double      durationSec;
};

// "Range: bytes=offset-offset+length-1" basligi uretir (RFC 9110).
inline std::string rangeHeader(const ByteRange& r) {
    return "bytes=" + std::to_string(r.offset) + "-" +
           std::to_string(r.offset + r.length - 1);
}

// Yanit satiri: "Content-Range: bytes start-end/total" (206 Partial Content).
inline std::string contentRange(const ByteRange& r, std::uint64_t total) {
    return "bytes " + std::to_string(r.offset) + "-" +
           std::to_string(r.offset + r.length - 1) + "/" + std::to_string(total);
}

} // namespace cm::streaming
```

## §6 C#

Partial-content yanıt üreticisi — 304/416/200/206 + `ETag`/`immutable`.

```csharp
// K014-media/streaming — partial-content yanıt üreticisi (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110 Range) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.
using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace CoreMusic.Streaming;

public sealed class RangeResult
{
    public int StatusCode { get; init; }
    public Stream? Body { get; init; }
    public long ContentLength { get; init; }
    public Dictionary<string, string> Headers { get; } = new();
}

public static class RangeContent
{
    public static RangeResult Create(
        string? rangeHeader, string? ifNoneMatch, Stream file, long size, string etag, string contentType)
    {
        var baseHeaders = new Dictionary<string, string>
        {
            ["Accept-Ranges"] = "bytes",
            ["ETag"] = etag,                                          // master §22.5
            ["Cache-Control"] = "public, max-age=31536000, immutable" // master §22.5
        };

        // If-None-Match -> 304 (ETag revalidation — master §22.5)
        if (ifNoneMatch == etag)
            return new RangeResult { StatusCode = 304 };

        if (rangeHeader is null)
            return new RangeResult { StatusCode = 200, Body = file, ContentLength = size };

        // Tek aralık: "bytes=start-end" (RFC 9110)
        var parts = rangeHeader.Split('=', 2);
        if (parts.Length != 2 || !parts[0].Equals("bytes", System.StringComparison.Ordinal))
            return WithHeaders(new RangeResult { StatusCode = 416 }, baseHeaders, size, null, null);

        var bounds = parts[1].Split('-', 2);
        long start = bounds[0].Length > 0 ? long.Parse(bounds[0]) : size - long.Parse(bounds[1]);
        long end = bounds.Length > 1 && bounds[1].Length > 0
            ? long.Parse(bounds[1]) : size - 1;

        if (start > end || start >= size)
            return WithHeaders(new RangeResult { StatusCode = 416 }, baseHeaders, size, null, null);

        var result = new RangeResult
        {
            StatusCode = 206,                                          // master §22.2
            Body = file,                                               // design: LimitedStream(seed) olur
            ContentLength = end - start + 1
        };
        WithHeaders(result, baseHeaders, size, start, end);
        result.Headers["Content-Type"] = contentType;
        return result;
    }

    private static RangeResult WithHeaders(
        RangeResult r, Dictionary<string, string> baseHeaders, long size, long? start, long? end)
    {
        foreach (var kv in baseHeaders) r.Headers[kv.Key] = kv.Value;
        r.Headers["Content-Range"] = start.HasValue
            ? $"bytes {start}-{end}/{size}"
            : $"bytes */{size}";
        return r;
    }
}
```

## §7 SQL

Akış varlıkları + HLS/DASH segmentleri şema taslağı — NOT: kalıcı şema K007-data'ya aittir (master §2.2).

```sql
-- K014-media/streaming — akış varlıkları için şema taslağı (TASARIM ÖNERİSİ).
-- Grounded in media-master.md §22.5 (ETag/immutable) + §22.2 (segment düzeni).
-- NOT: kalıcı şema K007-data'ya aittir (master §2.2) — bu blok taslak niteliğindedir.

CREATE TABLE media_asset (
    asset_id     BIGINT      NOT NULL PRIMARY KEY,
    content_type VARCHAR(64) NOT NULL,        -- audio/mpeg · audio/flac · audio/wav (§22.1)
    size_bytes   BIGINT      NOT NULL,
    etag         CHAR(64)    NOT NULL,        -- içerik hash — master §22.5
    UNIQUE KEY uq_asset_etag (etag)
);

-- HLS/DASH segmentleri (RFC 8216 / ISO/IEC 23009-1 — master §22.2)
CREATE TABLE media_segment (
    segment_id   BIGINT       NOT NULL PRIMARY KEY,
    asset_id     BIGINT       NOT NULL,
    seq_no       INT          NOT NULL,        -- EXT-X-MEDIA-SEQUENCE karşılığı
    byte_offset  BIGINT       NULL,            -- byte-range segment (DASH)
    byte_length  BIGINT       NULL,
    duration_sec DECIMAL(8,3) NOT NULL,
    uri          VARCHAR(255) NOT NULL,
    UNIQUE KEY uq_segment (asset_id, seq_no),
    CONSTRAINT fk_segment_asset FOREIGN KEY (asset_id) REFERENCES media_asset (asset_id)
) ENGINE=InnoDB;
```

## §8 Go

`net/http` Range handler — `ETag`/`immutable` + 304 + `http.ServeContent` (RFC 9110 semantiği).

```go
// K014-media/streaming — HTTP Range handler (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.
package streaming

import (
	"errors"
	"fmt"
	"io"
	"net/http"
	"time"
)

var errUnsatisfiable = errors.New("range not satisfiable") // -> 416

func ServeMedia(w http.ResponseWriter, r *http.Request, f io.ReadSeeker, etag string) {
	w.Header().Set("Accept-Ranges", "bytes")
	w.Header().Set("ETag", etag)                                          // master §22.5
	w.Header().Set("Cache-Control", "public, max-age=31536000, immutable") // master §22.5

	// If-None-Match -> 304 (ETag revalidation — master §22.5)
	if r.Header.Get("If-None-Match") == etag {
		w.WriteHeader(http.StatusNotModified)
		return
	}

	// http.ServeContent RFC 9110 Range semantiğini uygular (206 + Content-Range — master §22.2).
	http.ServeContent(w, r, "track", time.Time{}, f)
}

// parseRangeManual alternatif akış (eğitim amaçlı): "bytes=a-b" -> (start, end).
func parseRangeManual(header string, size int64) (int64, int64, error) {
	var a, b int64 = -1, -1
	if _, err := fmt.Sscanf(header, "bytes=%d-%d", &a, &b); err != nil {
		return 0, 0, err
	}
	if a < 0 { a = size - b; b = size - 1 }
	if b < 0 || b >= size { b = size - 1 }
	if a > b || a >= size { return 0, 0, errUnsatisfiable } // 416
	return a, b, nil
}
```

## §9 Rust

`Range` çözümleyici — 200/206/416 kararı + `Content-Range` başlık üretimi.

```rust
// K014-media/streaming — Range parse + 206 response (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.

#[derive(Debug, PartialEq)]
pub enum RangeParse<'a> {
    Full,                                    // Range yok -> 200
    Partial { start: u64, end: u64 },       // 206 + Content-Range
    Unsatisfiable,                           // 416: bytes */size
    Ignored(&'a str),                        // sozlesmeye aykiri -> yok say (RFC 9110)
}

pub fn parse_range(header: Option<&str>, size: u64) -> RangeParse<'_> {
    let Some(h) = header else { return RangeParse::Full };
    let Some(spec) = h.strip_prefix("bytes=") else { return RangeParse::Ignored(h) };
    // Tek aralik: "start-end" (coklu aralik sozlesmeye aykiri -> Ignored)
    let Some((s, e)) = spec.split_once('-') else { return RangeParse::Ignored(h) };
    match (s.parse::<u64>().ok(), e.parse::<u64>().ok()) {
        (Some(start), Some(end)) if start <= end && end < size =>
            RangeParse::Partial { start, end },
        (Some(start), None) if start < size =>
            RangeParse::Partial { start, end: size - 1 },
        (None, Some(tail)) if tail > 0 && tail <= size =>
            RangeParse::Partial { start: size - tail, end: size - 1 },
        _ => RangeParse::Unsatisfiable,
    }
}

pub fn content_range(start: u64, end: u64, size: u64) -> String {
    format!("bytes {start}-{end}/{size}") // 206 yanit basligi (master §22.2)
}
```

## §10 Python

Range işleyici (206/416/304) + HLS M3U8 manifest üretici (RFC 8216).

```python
# K014-media/streaming — Range işleyici + M3U8 manifest (TASARIM ÖNERİSİ).
# Grounded in media-master.md §22.2 (RFC 9110 · RFC 8216) + §22.5 (ETag).
# Repo'da bu modülün kodu YOK — illustrative design example.

import re

RANGE_RE = re.compile(r"^bytes=(\d*)-(\d*)$")

def build_range_response(headers: dict, file_size: int, etag: str) -> tuple[int, dict]:
    """206/416/304/200 kararını verir — gövde okunmadan önce çağrılır."""
    base = {
        "Accept-Ranges": "bytes",
        "ETag": etag,  # master §22.5
        "Cache-Control": "public, max-age=31536000, immutable",  # master §22.5
    }
    if headers.get("If-None-Match") == etag:
        return 304, base  # ETag revalidation (master §22.5)

    raw = headers.get("Range")
    if not raw:
        return 200, base  # Range yok -> tam içerik

    m = RANGE_RE.match(raw)
    if not m or (m[1] == "" and m[2] == ""):
        return 416, {**base, "Content-Range": f"bytes */{file_size}"}

    start = file_size - int(m[2]) if m[1] == "" else int(m[1])
    end = file_size - 1 if m[2] == "" else min(int(m[2]), file_size - 1)
    if start > end or start >= file_size:
        return 416, {**base, "Content-Range": f"bytes */{file_size}"}

    return 206, {**base, "Content-Range": f"bytes {start}-{end}/{file_size}",
                 "Content-Length": str(end - start + 1)}

def build_m3u8(segments: list[tuple[str, float]]) -> str:
    """HLS media playlist (RFC 8216 — master §22.2)."""
    max_dur = max(d for _, d in segments)
    lines = ["#EXTM3U", "#EXT-X-VERSION:3",
             f"#EXT-X-TARGETDURATION:{int(max_dur) + 1}",
             "#EXT-X-MEDIA-SEQUENCE:0"]
    for uri, dur in segments:
        lines += [f"#EXTINF:{dur:.3f},", uri]
    lines.append("#EXT-X-ENDLIST")
    return "\n".join(lines) + "\n"
```

## §11 Java

`Range` çözümleyici — `ByteRange` kaydı + `Content-Range` yazımı + immutable cache sabiti.

```java
// K014-media/streaming — Range yanıt mantığı (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.
package com.coremusic.streaming;

import java.util.Optional;

public final class RangeSupport {
    public record ByteRange(long start, long end) {
        public long length() { return end - start + 1; }
        public String contentRange(long size) {
            return "bytes " + start + "-" + end + "/" + size; // 206 (master §22.2)
        }
    }

    /** "bytes=a-b" -> Optional.empty() (416) veya aralık. Tek aralık desteklenir (RFC 9110). */
    public static Optional<ByteRange> parse(String header, long size) {
        if (header == null || !header.startsWith("bytes=")) return Optional.empty();
        String[] parts = header.substring(6).split("-", 2);
        try {
            long start = parts[0].isEmpty()
                    ? size - Long.parseLong(parts[1])
                    : Long.parseLong(parts[0]);
            long end = parts.length < 2 || parts[1].isEmpty()
                    ? size - 1
                    : Long.parseLong(parts[1]);
            if (start > end || start >= size) return Optional.empty();
            return Optional.of(new ByteRange(start, end));
        } catch (NumberFormatException e) {
            return Optional.empty();
        }
    }

    /** ETag immutable cache başlıkları (master §22.5). */
    public static String cacheControl() { return "public, max-age=31536000, immutable"; }
}
```

## §12 Swift

`URLSession` ile parçalı okuma isteği + `Content-Range` başlığı çözümleme.

```swift
// K014-media/streaming — URLSession ile parçalı okuma (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110 Range) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.
import Foundation

struct RangeSegmentRequest {
    let url: URL
    let start: Int64
    let end: Int64        // dahil

    var urlRequest: URLRequest {
        var req = URLRequest(url: url)
        req.setValue("bytes=\(start)-\(end)", forHTTPHeaderField: "Range") // RFC 9110
        return req
    }
}

enum RangeHeader {
    // "Content-Range: bytes start-end/total" -> (start...end, total)
    static func parse(_ value: String) -> (ClosedRange<Int64>, total: Int64)? {
        guard value.hasPrefix("bytes ") else { return nil }
        let spec = String(value.dropFirst(6))
        let parts = spec.split(separator: "/")
        guard parts.count == 2,
              let total = Int64(parts[1]),
              let se = parts[0].split(separator: "-").first,
              let start = Int64(se) else { return nil }
        let end = Int64(parts[0].split(separator: "-")[1]) ?? total - 1
        return (start...end, total)
    }
}

// İstemci akışı: 206 geldiğinde gövde parçasını decode pipeline'ına ver.
// ETag/cache: Cache-Control immutable + ETag (master §22.5) URLSession cache'ine bırakılır.
```

## §13 Kotlin

Ktor-style `Range` route mantığı + M3U8 manifest üretici.

```kotlin
// K014-media/streaming — Ktor-style Range route (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110 · RFC 8216) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.
package com.coremusic.streaming

data class RangeSpec(val start: Long, val end: Long) {
    fun contentRange(size: Long) = "bytes $start-$end/$size" // 206 (master §22.2)
}

fun parseRange(header: String?, size: Long): RangeSpec? = when {
    header == null -> null                                   // Range yok -> 200
    !header.startsWith("bytes=") -> null                     // yok say (RFC 9110)
    else -> {
        val parts = header.removePrefix("bytes=").split("-", limit = 2)
        val start = parts[0].toLongOrNull()
            ?: (size - (parts.getOrNull(1)?.toLongOrNull() ?: return null))
        val end = parts.getOrNull(1)?.toLongOrNull() ?: (size - 1)
        if (start > end || start >= size) null else RangeSpec(start, end.coerceAtMost(size - 1))
    }
}

// Yanıt başlıkları (master §22.5)
val CACHE_CONTROL = "public, max-age=31536000, immutable"

fun buildM3U8(segments: List<Pair<String, Double>>): String = buildString { // RFC 8216
    appendLine("#EXTM3U")
    appendLine("#EXT-X-VERSION:3")
    appendLine("#EXT-X-TARGETDURATION:${(segments.maxOf { it.second }).toInt() + 1}")
    appendLine("#EXT-X-MEDIA-SEQUENCE:0")
    segments.forEach { (uri, dur) ->
        appendLine("#EXTINF:${"%.3f".format(dur)},")
        appendLine(uri)
    }
    appendLine("#EXT-X-ENDLIST")
}
```

## §14 Ruby

Rack middleware — `Range` + `ETag`/`immutable` + Enumerator ile gövde dilimleme.

```ruby
# K014-media/streaming — Rack middleware: Range + ETag (TASARIM ÖNERİSİ).
# Grounded in media-master.md §22.2 (RFC 9110) + §22.5 (ETag/immutable).
# Repo'da bu dosyanın kodu YOK — illustrative design example.

class MediaRangeMiddleware
  RANGE_RE = /\Abytes=(\d*)-(\d*)\z/.freeze
  CACHE_CONTROL = 'public, max-age=31536000, immutable'.freeze # master §22.5

  def initialize(app, &body_source)
    @app = app
    @body_source = body_source # -> { size:, io:, etag: }
  end

  def call(env)
    asset = @body_source.call
    size = asset[:size]
    etag = asset[:etag] # icerik hash — master §22.5

    headers = { 'Accept-Ranges' => 'bytes', 'ETag' => etag, 'Cache-Control' => CACHE_CONTROL }

    return [304, headers, []] if env['HTTP_IF_NONE_MATCH'] == etag # ETag revalidation

    raw = env['HTTP_RANGE']
    return @app.call(env).then { |res| [res[0], res[1].merge(headers), res[2]] } if raw.nil? # 200

    m = RANGE_RE.match(raw)
    return [416, headers.merge('Content-Range' => "bytes */#{size}"), []] if m.nil?

    start = m[1].empty? ? size - m[2].to_i : m[1].to_i
    en = m[2].empty? ? size - 1 : [m[2].to_i, size - 1].min
    return [416, headers.merge('Content-Range' => "bytes */#{size}"), []] if start > en || start >= size

    length = en - start + 1
    body = Enumerator.new do |y| # govde uzerinde seek + dilimleme (TASARIM)
      asset[:io].seek(start)
      y << asset[:io].read(length)
    end
    [206, headers.merge('Content-Range' => "bytes #{start}-#{en}/#{size}",
                        'Content-Length' => length.to_s), body]
  end
end
```

## §15 Elixir

Plug pipeline — `accept-ranges`/`etag`/`cache-control` başlıkları + 206/416/304 kararı.

```elixir
# K014-media/streaming — Plug pipeline: Range + ETag (TASARIM ÖNERİSİ).
# Grounded in media-master.md §22.2 (RFC 9110) + §22.5 (ETag/immutable).
# Repo'da bu plug'un kodu YOK — illustrative design example.

defmodule CoreMusic.Streaming.RangePlug do
  import Plug.Conn

  @cache_control "public, max-age=31536000, immutable" # master §22.5

  def init(opts), do: opts

  def call(conn, _opts) do
    size = conn.assigns.media_size
    etag = conn.assigns.media_etag # icerik hash — master §22.5

    conn =
      put_resp_header(conn, "accept-ranges", "bytes")
      |> put_resp_header("etag", etag)
      |> put_resp_header("cache-control", @cache_control)

    # ETag revalidation -> 304 (master §22.5)
    if {"if-none-match", etag} in conn.req_headers do
      send_resp(conn, 304, "")
    else
      case parse_range(get_req_header(conn, "range"), size) do
        :full ->
          send_resp(conn, 200, "")

        {:partial, start, len} ->
          stop = start + len - 1
          # 206 Partial Content — Content-Range (RFC 9110 · master §22.2)
          conn
          |> put_resp_header("content-range", "bytes #{start}-#{stop}/#{size}")
          |> put_resp_header("content-length", Integer.to_string(len))
          |> send_resp(206, "")

        :unsatisfiable ->
          conn
          |> put_resp_header("content-range", "bytes */#{size}")
          |> send_resp(416, "")
      end
    end
  end

  defp parse_range([], _size), do: :full

  defp parse_range([<<"bytes=", spec::binary>>], size) do
    case String.split(spec, "-", parts: 2) do
      ["", tail] ->
        start = size - String.to_integer(tail)
        {:partial, start, size - start}

      [head, ""] ->
        start = String.to_integer(head)
        {:partial, start, size - start}

      [h, t] ->
        start = String.to_integer(h)
        stop = String.to_integer(t)
        if start > stop or start >= size,
          do: :unsatisfiable,
          else: {:partial, start, stop - start + 1}
    end
  end

  defp parse_range(_, _), do: :full # sozlesmeye aykiri baslik yok sayilir (RFC 9110)
end
```

## §16 Scala

Sealed trait `RangeResult` + regex çözümleyici + HLS master playlist notu.

```scala
// K014-media/streaming — Range parse + yanıt üretimi (TASARIM ÖNERİSİ).
// Grounded in media-master.md §22.2 (RFC 9110 · RFC 8216) + §22.5 (ETag/immutable).
// No repo code yet — illustrative design example.
package com.coremusic.streaming

sealed trait RangeResult
object RangeResult {
  case object Full extends RangeResult                               // 200
  case class Partial(start: Long, end: Long) extends RangeResult     // 206
  case object Unsatisfiable extends RangeResult                      // 416
}

object RangeHeader {
  private val Pattern = raw"bytes=(\d*)-(\d*)".r

  def parse(header: Option[String], size: Long): RangeResult = header match {
    case None => RangeResult.Full
    case Some(Pattern("", "")) => RangeResult.Unsatisfiable
    case Some(Pattern("", tail)) =>
      val start = size - tail.toLong
      if (start >= size) RangeResult.Unsatisfiable else RangeResult.Partial(start, size - 1)
    case Some(Pattern(head, "")) =>
      val start = head.toLong
      if (start >= size) RangeResult.Unsatisfiable else RangeResult.Partial(start, size - 1)
    case Some(Pattern(head, tail)) =>
      val (start, end) = (head.toLong, math.min(tail.toLong, size - 1))
      if (start > end || start >= size) RangeResult.Unsatisfiable else RangeResult.Partial(start, end)
    case _ => RangeResult.Full // sozlesmeye aykiri baslik yok sayilir (RFC 9110)
  }

  def contentRange(r: RangeResult.Partial, size: Long): String =
    s"bytes ${r.start}-${r.end}/$size" // 206 yanit basligi (master §22.2)

  val CacheControl: String = "public, max-age=31536000, immutable" // master §22.5

  // HLS master playlist ornegi (RFC 8216 — master §22.2):
  // #EXTM3U / #EXT-X-STREAMINF:BANDWIDTH=128000,CODECS="mp4a.40.2" / variant URI listesi
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | K014-media/media-master.md §9.6 | vault | streaming rol/sınır (taşıma K010 · dış kaynak K013 · offline fallback) |
| 2 | K014-media/media-master.md §22.2 | vault | HTTP Range · HLS · DASH protokol kararları |
| 3 | K014-media/media-master.md §22.5 | vault | `ETag` + `Cache-Control immutable` cache başlıkları |
| 4 | RFC 9110 (STD 97) | master §22.7 kaynak listesi | Range/206/`Content-Range` semantiği |
| 5 | RFC 8216 (HLS) | master §22.7 kaynak listesi | M3U8 manifest yapısı |
| 6 | ISO/IEC 23009-1:2026 (DASH) | master §22.7 kaynak listesi | segment/byte-range düzeni |
| 7 | RFC 6716 (Opus) | master §22.1 · §22.7 | gelecek ağ codec adayı (TASARIM ÖNERİSİ) |
| 8 | K014-media/streaming/index.md | vault | bu modülün künyesi (§ROL/§KAPSAM/§BİLEŞENLER/§STANDARTLAR) |

> Tüm kod blokları TASARIM ÖNERİSİ illustrative design örnekleridir (kademe-2 design node —
> repo'da henüz kodu YOK); dayanak: media-master §9.6 + §22.

**Authority:** Bayram Ali / Vault Steward
