---
node_id: K014-media/metadata/ornekler
node_type: module
status: draft
parent_id: K014-media/metadata
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.x"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/metadata — ornekler.md

> **Not:** Bu dosyadaki her örnek **TASARIM ÖNERİSİ** niteliğindedir — K014-media
> kademe-2 bir **design node**'dur; repo'da bu düğümün kodu henüz YOKTUR. Örnekler
> illustrative tasarım örneği taşır; dayanak `media-master.md §9.2` (metadata kapsamı)
> + §22 (doğrulanmış etiket şemaları · loudness · dış kaynak standartları). Gerçek
> uygulama; K007-data şema onayı ve ADR-097 K4 faz (C) sonrasında yazılır. Paket/kütüphane
> adı ve sürümü uydurulmaz — örnekler dile has std/yapılarla yazılmıştır (Zero-Hallucination).

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Etiket kuralı | Her örnek **TASARIM ÖNERİSİ**'dir; kaynak kodu kanıtı gibi sunulmaz |
| 2 | Dil listesi | 15 dil: PHP · JavaScript · TypeScript · C++ · C# · SQL · Go · Rust · Python · Java · Swift · Kotlin · Ruby · Elixir · Scala |
| 3 | Kanıt kuralı | Yalnız `media-master.md §9.2` + §22'de doğrulanmış standartlar kullanılır (ID3v2 · Vorbis Comment · ilst · LIST-INFO · EBU R128 · ReplayGain 2.0) |
| 4 | Uydurma yasak | Paket/sürüm/anahtar adı yazılmaz; yalnız dil std yapısı kullanılır |
| 5 | Değişiklik | Bu dosya yalnız örnek üretir; `*.php`/`*.js`/`*.css`/`*.sql` kaynak koduna dokunulmaz |

> **metadata çekirdek sorumlulukları (master §9.2 · §22.3):** etiket okuma/yazma ·
> etiket normalizasyonu (TYER→TDRC) · EBU R128 / ReplayGain loudness alanları ·
> dosya adı fallback'i · dış kaynak (MusicBrainz · CAA · AcoustID) K013 üzerinden.

## §2 PHP

ID3v2.3→ID3v2.4 tarih normalizasyonu (TYER→TDRC) ve loudness varsayılanları (§22.3).

```php
<?php
declare(strict_types=1);

// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
final class TagNormalizer
{
    /**
     * ID3v2.3 -> ID3v2.4 tarih kırılması: TYER (yalnız yıl) -> TDRC (tam tarih).
     * Kaynak: master §22.3 (ISO/IEC 14496-3 — ID3v2.4/MP4 ilst · §22.7).
     */
    public static function normalizeYear(array $frames): ?string
    {
        if (isset($frames['TDRC'])) {
            return (string) $frames['TDRC'];
        }
        if (!isset($frames['TYER'])) {
            return null;
        }
        $tyer = (string) $frames['TYER'];
        return preg_match('/^\d{4}$/', $tyer) === 1 ? $tyer . '-01-01' : $tyer;
    }

    /** Loudness alanları: EBU R128 (−23 LUFS yayın) · ReplayGain 2.0 (−18 LUFS) — §22.3. */
    public static function loudnessDefaults(): array
    {
        return ['r128_lufs' => -23.0, 'replaygain_lufs' => -18.0];
    }

    /** Etiket yoksa fallback: dosya adı (master §9.2 — TASARIM İDDİASI). */
    public static function fallbackTitle(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }
}
```

## §3 JavaScript

Şema bazlı çerçeve eşlemesi — ID3v2 · Vorbis Comment · MP4 `ilst` (§22.3) + fallback (§9.2).

```js
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
const FRAME_MAP = {
  id3v2: { TIT2: 'title', TPE1: 'artist', TALB: 'album', TDRC: 'year' }, // v2.4 (v2.3 TYER → TDRC §22.3)
  vorbis: { TITLE: 'title', ARTIST: 'artist', ALBUM: 'album', DATE: 'year' }, // Vorbis Comment (§22.3)
  mp4ilst: { '©nam': 'title', '©ART': 'artist', '©alb': 'album', covr: 'artwork' }, // ilst kutuları (§22.3)
  listinfo: { INAM: 'title', IART: 'artist', IPRD: 'album' }, // WAV LIST-INFO (§22.3)
};

export function normalizeTags(schema, frames, filePath) {
  const map = FRAME_MAP[schema];
  const tags = { source: schema };
  for (const [key, field] of Object.entries(map)) {
    if (frames[key] !== undefined) tags[field] = frames[key];
  }
  // Etiket eksikse fallback: dosya adı (master §9.2 — TASARIM İDDİASI)
  tags.title ??= filePath.split(/[\\/]/).pop().replace(/\.[^.]+$/, '');
  // Loudness alanları: EBU R128 (−23 LUFS) · ReplayGain 2.0 (−18 LUFS) — §22.3
  tags.loudness ??= { r128Lufs: -23, replaygainLufs: -18 };
  return tags;
}
```

## §4 TypeScript

Tip güvenli Tag sözleşmesi — beş etiket şeması ve loudness alanları (§22.3).

```ts
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
export type TagSchema = 'id3v2.3' | 'id3v2.4' | 'vorbis-comment' | 'mp4-ilst' | 'list-info'; // §22.3

export interface Loudness {
  readonly r128Lufs: -23;        // EBU R128 — yayın (§22.3)
  readonly replaygainLufs: -18;  // ReplayGain 2.0 (§22.3)
}

export interface NormalizedTags {
  readonly schema: TagSchema;
  title: string;
  artist?: string;
  album?: string;
  year?: string;
  readonly loudness: Loudness;
}

// ID3v2.3 TYER → ID3v2.4 TDRC kırılması (§22.3) — tek yönlü normalizasyon
export function normalizeYear(frames: Record<string, string>): string | undefined {
  const raw = frames.TDRC ?? frames.TYER;
  if (!raw) return undefined;
  return /^\d{4}$/.test(raw) ? `${raw}-01-01` : raw;
}
```

## §5 C++

Vorbis Comment (FLAC/Ogg) yazıcı ve dosya adı fallback'i (§22.3 · §9.2).

```cpp
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
#include <string>
#include <vector>

// Vorbis Comment bloğu (FLAC/Ogg) — "ALAN=değer" çiftleri (§22.3)
struct VorbisComment {
    std::vector<std::string> fields; // ör: "ARTIST=...", "TITLE=...", "R128_LOUDNESS=-23"

    void set(const std::string& name, const std::string& value) {
        const std::string prefix = name + "=";
        for (auto& f : fields)
            if (f.rfind(prefix, 0) == 0) { f = prefix + value; return; } // overwrite
        fields.push_back(prefix + value);
    }
};

// Loudness alanları: EBU R128 (−23 LUFS yayın) · ReplayGain 2.0 (−18 LUFS) — §22.3
struct Loudness {
    double r128Lufs = -23.0;
    double replaygainLufs = -18.0;
};

// Etiket yoksa fallback: dosya adı (master §9.2 — TASARIM İDDİASI)
std::string fallbackTitle(const std::string& path) {
    auto slash = path.find_last_of("/\\");
    std::string name = (slash == std::string::npos) ? path : path.substr(slash + 1);
    auto dot = name.find_last_of('.');
    return dot == std::string::npos ? name : name.substr(0, dot);
}
```

## §6 C#

MP4 `ilst` kutu anahtarları ve tarih normalizasyonu (§22.3).

```csharp
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
using System.Collections.Generic;
using System.IO;
using System.Linq;

// MP4/M4A 'ilst' kutu anahtarları (§22.3): ©nam · ©ART · covr
public static class IlstKeys
{
    public const string Title = "©nam";
    public const string Artist = "©ART";
    public const string Album = "©alb";
    public const string Artwork = "covr";
}

public sealed record Loudness(
    double R128Lufs = -23.0,       // EBU R128 — yayın (§22.3)
    double ReplayGainLufs = -18.0  // ReplayGain 2.0 (§22.3)
);

public static class TagNormalizer
{
    // ID3v2.3 TYER → ID3v2.4 TDRC (§22.3 — uyumluluk kırılması)
    public static string? NormalizeYear(IReadOnlyDictionary<string, string> frames)
    {
        if (frames.TryGetValue("TDRC", out var tdrc)) return tdrc;
        if (!frames.TryGetValue("TYER", out var tyer)) return null;
        return tyer.Length == 4 && tyer.All(char.IsDigit) ? $"{tyer}-01-01" : tyer;
    }

    // Etiket yoksa fallback: dosya adı (master §9.2)
    public static string FallbackTitle(string path) =>
        Path.GetFileNameWithoutExtension(path);
}
```

## §7 SQL

Metadata saklama tasarımı — loudness kolonları ve fallback; **şema biçimi ⚠️ UNKNOWN → K007'ye ait** (master §12/2 · §22.6).

```sql
-- TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
-- DİKKAT: kalıcı şema biçimi ⚠️ UNKNOWN → K007-data'ya ait (master §12/2 · §22.6).
-- Bu blok yalnız tasarım önerisidir; nihai şema K007 onayı ister. SELECT * yasak.

CREATE TABLE media_tag (
    track_id        BIGINT       NOT NULL PRIMARY KEY,
    schema          ENUM('id3v2.3','id3v2.4','vorbis-comment','mp4-ilst','list-info') NOT NULL, -- §22.3
    file_stem       VARCHAR(255) NOT NULL,                       -- fallback kaynağı (§9.2)
    title           VARCHAR(255) NULL,                           -- etiketten; NULL ise file_stem
    artist          VARCHAR(255) NULL,
    album           VARCHAR(255) NULL,
    year            CHAR(10)     NULL,                           -- TDRC biçimi (TYER→TDRC §22.3)
    r128_lufs       DECIMAL(6,2) NOT NULL DEFAULT -23.00,        -- EBU R128 (§22.3)
    replaygain_lufs DECIMAL(6,2) NOT NULL DEFAULT -18.00,        -- ReplayGain 2.0 (§22.3)
    source          ENUM('file','musicbrainz','acoustid') NOT NULL DEFAULT 'file' -- §22.3
);

-- Etiket eksikse dosya adı fallback'i (master §9.2) — parametreli
SELECT m.track_id,
       COALESCE(m.title, m.file_stem) AS display_title,
       m.r128_lufs
FROM media_tag m
WHERE m.track_id = :track_id;
```

## §8 Go

Şema/tipi modeli ve TYER→TDRC normalizasyonu (§22.3).

```go
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
package metadata

import "strings"

// TagSchema — §22.3'te doğrulanan beş etiket şeması.
type TagSchema string

const (
	SchemaID3v23   TagSchema = "id3v2.3"        // TYER (yalnız yıl)
	SchemaID3v24   TagSchema = "id3v2.4"        // TDRC (tam tarih) — §22.3 kırılması
	SchemaVorbis   TagSchema = "vorbis-comment" // FLAC/Ogg — §22.3
	SchemaMP4Ilst  TagSchema = "mp4-ilst"       // ©nam · ©ART · covr — §22.3
	SchemaListInfo TagSchema = "list-info"      // WAV — §22.3
)

// Tags — loudness alanları EBU R128 (−23 LUFS) · ReplayGain 2.0 (−18 LUFS) (§22.3)
type Tags struct {
	Schema         TagSchema
	Title          string
	Artist         string
	Album          string
	Year           string
	R128LUFS       float64 // -23.0
	ReplayGainLUFS float64 // -18.0
}

// NormalizeYear: ID3v2.3 TYER -> ID3v2.4 TDRC (yalnız yıl -> YYYY-01-01).
func NormalizeYear(frames map[string]string) string {
	if v, ok := frames["TDRC"]; ok {
		return v
	}
	tyer := frames["TYER"]
	if len(tyer) == 4 {
		return tyer + "-01-01"
	}
	return tyer
}

// Fallback: etiket yoksa dosya adı (master §9.2 — TASARIM İDDİASI).
func FallbackTitle(path string) string {
	name := path
	if i := strings.LastIndexAny(path, `/\`); i >= 0 {
		name = path[i+1:]
	}
	if j := strings.LastIndex(name, "."); j > 0 {
		name = name[:j]
	}
	return name
}
```

## §9 Rust

Şema enumu, loudness alanları ve tarih normalizasyonu (§22.3).

```rust
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
use std::collections::HashMap;

#[derive(Debug, Clone, Copy, PartialEq)]
pub enum TagSchema {
    Id3v23,    // TYER (yalnız yıl)
    Id3v24,    // TDRC (tam tarih) — §22.3 kırılması
    Vorbis,    // FLAC/Ogg — Vorbis Comment (§22.3)
    Mp4Ilst,   // ©nam · ©ART · covr (§22.3)
    ListInfo,  // WAV — LIST-INFO (§22.3)
}

#[derive(Debug, Clone)]
pub struct Tags {
    pub schema: TagSchema,
    pub title: String,
    pub artist: Option<String>,
    pub album: Option<String>,
    pub year: Option<String>,
    pub r128_lufs: f32,       // EBU R128: −23 LUFS (§22.3)
    pub replaygain_lufs: f32, // ReplayGain 2.0: −18 LUFS (§22.3)
}

impl Default for Tags {
    fn default() -> Self {
        Tags {
            schema: TagSchema::Id3v24,
            title: String::new(),
            artist: None,
            album: None,
            year: None,
            r128_lufs: -23.0,
            replaygain_lufs: -18.0,
        }
    }
}

/// ID3v2.3 TYER -> ID3v2.4 TDRC normalizasyonu (yalnız yıl -> YYYY-01-01).
pub fn normalize_year(frames: &HashMap<&str, &str>) -> Option<String> {
    if let Some(tdrc) = frames.get("TDRC") {
        return Some(tdrc.to_string());
    }
    let tyer = frames.get("TYER")?;
    Some(if tyer.len() == 4 { format!("{tyer}-01-01") } else { tyer.to_string() })
}
```

## §10 Python

Sabitler + dataclass Tag modeli + TYER→TDRC + fallback (§22.3 · §9.2).

```python
# TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
from dataclasses import dataclass
from pathlib import Path

# §22.3 — doğrulanan etiket şemaları
SCHEMAS = ("id3v2.3", "id3v2.4", "vorbis-comment", "mp4-ilst", "list-info")

R128_LUFS = -23.0        # EBU R128 — yayın (§22.3)
REPLAYGAIN_LUFS = -18.0  # ReplayGain 2.0 (§22.3)

# MP4/M4A ilst kutu anahtarları (§22.3)
ILST_KEYS = {"©nam": "title", "©ART": "artist", "©alb": "album", "covr": "artwork"}


@dataclass
class Tags:
    schema: str
    title: str
    artist: str | None = None
    album: str | None = None
    year: str | None = None
    r128_lufs: float = R128_LUFS
    replaygain_lufs: float = REPLAYGAIN_LUFS


def normalize_year(frames: dict[str, str]) -> str | None:
    """ID3v2.3 TYER -> ID3v2.4 TDRC (yalnız yıl -> YYYY-01-01) — §22.3."""
    raw = frames.get("TDRC") or frames.get("TYER")
    if not raw:
        return None
    if len(raw) == 4 and raw.isdigit():
        return f"{raw}-01-01"
    return raw


def fallback_title(path: str) -> str:
    """Etiket yoksa dosya adı (master §9.2 — TASARIM İDDİASI)."""
    return Path(path).stem
```

## §11 Java

`ilst` anahtar eşlemesi ve tarih normalizasyonu (§22.3).

```java
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
import java.util.Map;

public final class TagNormalizer {
    public static final double R128_LUFS = -23.0;        // EBU R128 — yayın (§22.3)
    public static final double REPLAYGAIN_LUFS = -18.0;  // ReplayGain 2.0 (§22.3)

    private TagNormalizer() {}

    // MP4/M4A ilst anahtarları (§22.3): ©nam · ©ART · covr
    public static String ilstField(String key) {
        return switch (key) {
            case "©nam" -> "title";
            case "©ART" -> "artist";
            case "©alb" -> "album";
            case "covr" -> "artwork";
            default -> null;
        };
    }

    // ID3v2.3 TYER -> ID3v2.4 TDRC (§22.3 — uyumluluk kırılması)
    public static String normalizeYear(Map<String, String> frames) {
        String tdrc = frames.get("TDRC");
        if (tdrc != null) {
            return tdrc;
        }
        String tyer = frames.get("TYER");
        if (tyer == null) {
            return null;
        }
        return tyer.matches("\\d{4}") ? tyer + "-01-01" : tyer;
    }
}
```

## §12 Swift

Şema enumu + Loudness + dosya adı fallback'i (§22.3 · §9.2).

```swift
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
import Foundation

enum TagSchema: String {
    case id3v23 = "id3v2.3"        // TYER (yalnız yıl)
    case id3v24 = "id3v2.4"        // TDRC (tam tarih) — §22.3 kırılması
    case vorbis = "vorbis-comment" // FLAC/Ogg — §22.3
    case mp4Ilst = "mp4-ilst"      // ©nam · ©ART · covr — §22.3
    case listInfo = "list-info"    // WAV — §22.3
}

struct Loudness {
    let r128Lufs: Double = -23.0       // EBU R128 (§22.3)
    let replaygainLufs: Double = -18.0 // ReplayGain 2.0 (§22.3)
}

struct Tags {
    let schema: TagSchema
    var title: String
    var artist: String?
    var album: String?
    var year: String?
    let loudness = Loudness()
}

// Etiket yoksa fallback: dosya adı (master §9.2 — TASARIM İDDİASI)
func fallbackTitle(path: String) -> String {
    (path as NSString).deletingPathExtension
}
```

## §13 Kotlin

`ilst` anahtar haritası + TYER→TDRC normalizasyonu (§22.3).

```kotlin
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)

const val R128_LUFS = -23.0        // EBU R128 — yayın (§22.3)
const val REPLAYGAIN_LUFS = -18.0  // ReplayGain 2.0 (§22.3)

// MP4/M4A 'ilst' kutu anahtarları (§22.3): ©nam · ©ART · covr
val ILST_KEYS: Map<String, String> = mapOf(
    "©nam" to "title",
    "©ART" to "artist",
    "©alb" to "album",
    "covr" to "artwork"
)

// ID3v2.3 TYER -> ID3v2.4 TDRC normalizasyonu (§22.3)
fun normalizeYear(frames: Map<String, String>): String? {
    frames["TDRC"]?.let { return it }
    val tyer = frames["TYER"] ?: return null
    return if (tyer.matches(Regex("\\d{4}"))) "$tyer-01-01" else tyer
}
```

## §14 Ruby

Sabitler + module fonksiyonları — normalizasyon, fallback ve loudness (§22.3 · §9.2).

```ruby
# TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
module Metadata
  # §22.3 — doğrulanan etiket şemaları
  SCHEMAS = %w[id3v2.3 id3v2.4 vorbis-comment mp4-ilst list-info].freeze

  R128_LUFS       = -23.0 # EBU R128 — yayın (§22.3)
  REPLAYGAIN_LUFS = -18.0 # ReplayGain 2.0 (§22.3)

  # MP4/M4A ilst kutu anahtarları (§22.3)
  ILST = { '©nam' => :title, '©ART' => :artist, '©alb' => :album, 'covr' => :artwork }.freeze

  # ID3v2.3 TYER -> ID3v2.4 TDRC (yalnız yıl -> YYYY-01-01) — §22.3
  def self.normalize_year(frames)
    raw = frames['TDRC'] || frames['TYER']
    return nil if raw.nil? || raw.empty?
    raw.match?(/\A\d{4}\z/) ? "#{raw}-01-01" : raw
  end

  # Etiket yoksa fallback: dosya adı (master §9.2)
  def self.fallback_title(path)
    File.basename(path, '.*')
  end
end
```

## §15 Elixir

Tag map'i, normalize_year kural kafesi ve fallback (§22.3 · §9.2).

```elixir
# TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)
defmodule Metadata do
  @r128_lufs -23.0        # EBU R128 — yayın (§22.3)
  @replaygain_lufs -18.0  # ReplayGain 2.0 (§22.3)

  # MP4/M4A ilst kutu anahtarları (§22.3)
  @ilst %{"©nam" => :title, "©ART" => :artist, "©alb" => :album, "covr" => :artwork}

  def new(schema, fields, path) do
    %{
      schema: schema,
      title: Map.get(fields, "TITLE") || fallback_title(path), # §9.2 fallback
      artist: Map.get(fields, "ARTIST"),
      album: Map.get(fields, "ALBUM"),
      year: normalize_year(fields),
      r128_lufs: @r128_lufs,
      replaygain_lufs: @replaygain_lufs
    }
  end

  # ID3v2.3 TYER -> ID3v2.4 TDRC (yalnız yıl -> YYYY-01-01) — §22.3
  def normalize_year(%{"TDRC" => v}), do: v
  def normalize_year(%{"TYER" => <<y::binary-size(4)>> = v}), do: "#{y}-01-01"
  def normalize_year(%{"TYER" => v}), do: v
  def normalize_year(_), do: nil

  defp fallback_title(path), do: path |> Path.basename() |> Path.rootname()
end
```

## §16 Scala

enum şema + case class Tag + normalizeYear (§22.3).

```scala
// TASARIM ÖNERİSİ — K014-media/metadata (master §9.2 · §22.3)

enum TagSchema(val id: String):
  case Id3v23   extends TagSchema("id3v2.3")          // TYER (yalnız yıl)
  case Id3v24   extends TagSchema("id3v2.4")          // TDRC — §22.3 kırılması
  case Vorbis   extends TagSchema("vorbis-comment")   // FLAC/Ogg — §22.3
  case Mp4Ilst  extends TagSchema("mp4-ilst")         // ©nam · ©ART · covr — §22.3
  case ListInfo extends TagSchema("list-info")        // WAV — §22.3

final case class Loudness(r128Lufs: Double = -23.0, replaygainLufs: Double = -18.0) // §22.3

final case class Tags(
    schema: TagSchema,
    title: String,
    artist: Option[String] = None,
    album: Option[String] = None,
    year: Option[String] = None,
    loudness: Loudness = Loudness()
)

object TagNormalizer:
  // ID3v2.3 TYER -> ID3v2.4 TDRC (yalnız yıl -> YYYY-01-01) — §22.3
  def normalizeYear(frames: Map[String, String]): Option[String] =
    frames
      .get("TDRC")
      .orElse(frames.get("TYER").map(y => if y.matches("\\d{4}") then s"$y-01-01" else y))
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | metadata rolü/kapsam/sınır | K014-media/media-master.md §3/2 · §9.2 | etiket · fallback · kalıcılık |
| 2 | Etiket şemaları (ID3v2.3/2.4 · Vorbis Comment · ilst · LIST-INFO) + TYER→TDRC | K014-media/media-master.md §22.3 | her dilde normalizasyon |
| 3 | Loudness (EBU R128 −23 LUFS · ReplayGain 2.0 −18 LUFS) | K014-media/media-master.md §22.3 | loudness alanları |
| 4 | Dış kaynaklar (MusicBrainz 1/sn · CAA 250/500/1200 px · AcoustID ≤3/sn · Last.fm ⚠️) | K014-media/media-master.md §22.3 · §22.4 | kaynak/sınır tabloları |
| 5 | Format seti (MP3 · FLAC · WAV) | K014-media/media-master.md §22.1 | etiket okunacak dosya kapsamı |
| 6 | Kaynak listesi (ISO/IEC 14496-3 · EBU R128 · ReplayGain 2.0 · MusicBrainz · CAA · AcoustID) | K014-media/media-master.md §22.7 | standart kanıtları |
| 7 | Çocuk künyesi + açık sorular | K014-media/metadata/index.md §A Kapılar | kaynak sorusu §7/8 açık |
| 8 | Örnek formatı referansı | K015-AI/ai-ornekler.md §1 | kılavuz şeması |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
