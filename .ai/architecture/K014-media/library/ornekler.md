---
node_id: K014-media/library/ornekler
node_type: module
status: draft
parent_id: K014-media/library
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

# K014-media/library — ornekler.md

> **Not:** Bu dosyadaki her örnek **TASARIM ÖNERİSİ** niteliğindedir — K014-media
> kademe-2 bir **design node**'dur; repo'da bu düğümün kodu henüz YOKTUR. Örnekler
> illustrative tasarım örneği taşır; dayanak `media-master.md §9.1` (library kapsamı)
> + §22 (doğrulanmış format/kimlik/önbellek standartları). Kod tabanına yazılan gerçek
> uygulama; K007-data şema onayı ve ADR-097 K4 faz (C) sonrasında yapılır. Paket/kütüphane
> adı ve sürümü uydurulmaz — örnekler dile has std/yapılarla yazılmıştır (Zero-Hallucination).

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Etiket kuralı | Her örnek **TASARIM ÖNERİSİ**'dir; kaynak kodu kanıtı gibi sunulmaz |
| 2 | Dil listesi | 15 dil: PHP · JavaScript · TypeScript · C++ · C# · SQL · Go · Rust · Python · Java · Swift · Kotlin · Ruby · Elixir · Scala |
| 3 | Kanıt kuralı | Yalnız `media-master.md §9.1` + §22'de doğrulanmış standartlar kullanılır |
| 4 | Uydurma yasak | Paket/sürüm/anahtar adı yazılmaz; yalnız dil std yapısı kullanılır |
| 5 | Değişiklik | Bu dosya yalnız örnek üretir; `*.php`/`*.js`/`*.css`/`*.sql` kaynak koduna dokunulmaz |

> **library çekirdek sorumlulukları (master §9.1):** koleksiyon modeli · kitaplık sorgusu ·
> yineleme denetimi (dedup) · playlist/shelf yapısı · EMPTY durumu.

## §2 PHP

Koleksiyon modeli, içerik-hash dedup'u (ETag deseni — §22.5) ve filtreli kitaplık sorgusu (§9.1).

```php
<?php
declare(strict_types=1);

// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
final class Track
{
    public function __construct(
        public readonly string $id,          // içerik hash → ETag deseni (§22.5)
        public readonly string $title,
        public readonly string $artist,
        public readonly string $album,
        public readonly int $durationSec,
        public readonly string $format,      // "mp3" | "flac" | "wav" — §22.1 temel set
        public readonly ?float $loudnessLufs = null, // EBU R128 alanı (§22.3 — metadata malı)
    ) {}
}

final class Library
{
    /** @var array<string,Track> id => Track */
    private array $tracks = [];

    /** dedup: aynı içerik hash ikinci kez eklenmez (§22.5 ETag deseni). */
    public function add(Track $t): bool
    {
        if (isset($this->tracks[$t->id])) {
            return false;
        }
        $this->tracks[$t->id] = $t;
        return true;
    }

    /** @return list<Track> listeleme · filtreleme · sıralama (§9.1) */
    public function query(?string $artist, ?string $format, string $sort = 'title'): array
    {
        $out = array_values($this->tracks);
        if ($artist !== null) {
            $out = array_values(array_filter(
                $out,
                static fn (Track $t): bool => strcasecmp($t->artist, $artist) === 0
            ));
        }
        if ($format !== null) {
            $out = array_values(array_filter($out, static fn (Track $t): bool => $t->format === $format));
        }
        usort($out, static fn (Track $a, Track $b): int => $a->{$sort} <=> $b->{$sort});
        return $out;
    }

    /** koleksiyon boşsa EMPTY durumu (§9.1) → K011-application gösterir. */
    public function state(): string
    {
        return $this->tracks === [] ? 'EMPTY' : 'READY';
    }
}
```

## §3 JavaScript

Playlist/shelf yapısı ve filtreli sorgu — ES6+ Map tabanlı koleksiyon (§9.1).

```js
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.5)
const library = new Map(); // trackId -> track (id = içerik hash → ETag deseni §22.5)

export function addToLibrary(track) {
  if (library.has(track.id)) return { added: false, reason: 'duplicate' }; // dedup
  library.set(track.id, track);
  return { added: true };
}

export function queryTracks({ artist, format, sort = 'title' } = {}) {
  let out = [...library.values()];
  if (artist) out = out.filter((t) => t.artist.toLowerCase() === artist.toLowerCase());
  if (format) out = out.filter((t) => t.format === format); // §22.1 temel set: mp3|flac|wav
  return out.sort((a, b) => String(a[sort]).localeCompare(String(b[sort]), 'tr'));
}

// Playlist / shelf yapısı (§9.1) — sıralı parça kimlikleri
export function createPlaylist(id, name, trackIds = []) {
  const unique = [...new Set(trackIds)]; // yineleme düşme
  return { id, name, trackIds: unique };
}

export const state = () => (library.size === 0 ? 'EMPTY' : 'READY'); // §9.1 → K011
```

## §4 TypeScript

Tip güvenli koleksiyon + format alanı (§22.1 temel set) + playlist sözleşmesi.

```ts
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1)
export type Format = 'mp3' | 'flac' | 'wav'; // §22.1 temel set (TASARIM ÖNERİSİ — Opus ileride)

export interface Track {
  readonly id: string; // içerik hash (ETag deseni — §22.5)
  readonly title: string;
  readonly artist: string;
  readonly album: string;
  readonly durationSec: number;
  readonly format: Format;
}

export interface Playlist {
  readonly id: string;
  readonly name: string;
  readonly trackIds: readonly string[]; // sıralı (§9.1)
}

export class LibraryStore {
  private readonly tracks = new Map<string, Track>();
  private readonly playlists = new Map<string, Playlist>();

  /** dedup: aynı içerik hash ikinci kez eklenmez. */
  add(t: Track): boolean {
    if (this.tracks.has(t.id)) return false;
    this.tracks.set(t.id, t);
    return true;
  }

  query(opts: { artist?: string; format?: Format } = {}): Track[] {
    let out = [...this.tracks.values()];
    if (opts.artist) out = out.filter((t) => t.artist.toLowerCase() === opts.artist!.toLowerCase());
    if (opts.format) out = out.filter((t) => t.format === opts.format);
    return out.sort((a, b) => a.title.localeCompare(b.title, 'tr'));
  }

  addPlaylist(p: Playlist): boolean {
    const known = p.trackIds.every((id) => this.tracks.has(id));
    if (!known) return false; // bilinmeyen parça playlist'e girmez
    this.playlists.set(p.id, p);
    return true;
  }

  get state(): 'EMPTY' | 'READY' {
    return this.tracks.size === 0 ? 'EMPTY' : 'READY'; // §9.1 → K011
  }
}
```

## §5 C++

Koleksiyon modeli, yineleme denetimi ve sanatçı bazlı sorgu (kaynak kodu kanıtsız — TASARIM ÖNERİSİ).

```cpp
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
#include <string>
#include <vector>
#include <unordered_map>
#include <algorithm>
#include <cctype>

struct Track {
    std::string id;      // içerik hash → ETag deseni (§22.5)
    std::string title;
    std::string artist;
    std::string album;
    double durationSec{0.0};
    std::string format;  // "mp3" | "flac" | "wav" (§22.1 temel set)
};

class Library {
public:
    bool add(const Track& t) {
        if (index_.count(t.id) != 0) return false; // dedup
        index_.emplace(t.id, t);
        return true;
    }

    std::vector<const Track*> query(const std::string& artist) const {
        std::vector<const Track*> out;
        for (const auto& [id, t] : index_)
            if (artist.empty() || iequals(t.artist, artist)) out.push_back(&t);
        std::sort(out.begin(), out.end(),
                  [](const Track* a, const Track* b) { return a->title < b->title; });
        return out;
    }

    const char* state() const { return index_.empty() ? "EMPTY" : "READY"; } // §9.1 → K011

private:
    std::unordered_map<std::string, Track> index_; // key = id → dedup

    static bool iequals(const std::string& a, const std::string& b) {
        if (a.size() != b.size()) return false;
        for (size_t i = 0; i < a.size(); ++i)
            if (std::tolower(static_cast<unsigned char>(a[i])) !=
                std::tolower(static_cast<unsigned char>(b[i]))) return false;
        return true;
    }
};
```

## §6 C#

Record model + LINQ ile sorgu/sıralama (§9.1).

```csharp
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1)
public sealed record Track(
    string Id,          // içerik hash → ETag deseni (§22.5)
    string Title,
    string Artist,
    string Album,
    double DurationSec,
    string Format);     // "mp3" | "flac" | "wav" (§22.1 temel set)

public sealed class Library
{
    private readonly Dictionary<string, Track> _tracks = new(); // key = id → dedup

    public bool Add(Track t) => _tracks.TryAdd(t.Id, t); // false = zaten vardı (dedup)

    public IReadOnlyList<Track> Query(string? artist = null, string sort = "Title")
    {
        IEnumerable<Track> q = _tracks.Values;
        if (artist is not null)
            q = q.Where(t => string.Equals(t.Artist, artist, StringComparison.OrdinalIgnoreCase));
        q = sort == "DurationSec" ? q.OrderBy(t => t.DurationSec) : q.OrderBy(t => t.Title);
        return q.ToList();
    }

    public string State => _tracks.Count == 0 ? "EMPTY" : "READY"; // §9.1 → K011
}
```

## §7 SQL

Koleksiyon + playlist şeması — parametreli sorgu; **şema biçimi ⚠️ UNKNOWN → K007'ye ait** (master §12/2 · §22.6).

```sql
-- TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.5)
-- DİKKAT: kalıcı şema biçimi ⚠️ UNKNOWN → K007-data'ya ait (master §12/2 · §22.6).
-- Bu blok yalnız tasarım önerisidir; nihai şema K007 onayı ister. SELECT * yasak.

CREATE TABLE track (
    id            BIGINT       NOT NULL PRIMARY KEY,
    content_hash  CHAR(64)     NOT NULL,                          -- içerik hash → ETag deseni (§22.5)
    title         VARCHAR(255) NOT NULL,
    artist        VARCHAR(255) NOT NULL,
    album         VARCHAR(255) NOT NULL,
    duration_sec  INT          NOT NULL,
    format        ENUM('mp3','flac','wav') NOT NULL,              -- §22.1 temel set (TASARIM ÖNERİSİ)
    UNIQUE KEY uq_track_hash (content_hash)                       -- dedup
);

CREATE TABLE playlist (
    id   BIGINT       NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE playlist_item (
    playlist_id BIGINT NOT NULL,
    position    INT    NOT NULL,                                  -- sıralı yapı (§9.1)
    track_id    BIGINT NOT NULL,
    PRIMARY KEY (playlist_id, position),
    CONSTRAINT fk_pitem_track FOREIGN KEY (track_id) REFERENCES track(id)
);

-- Kitaplık sorgusu: listeleme · filtreleme · sıralama (§9.1) — parametreli
SELECT t.id, t.title, t.artist, t.album, t.duration_sec, t.format
FROM track t
WHERE t.artist = :artist
ORDER BY t.title ASC;
```

## §8 Go

Map tabanlı koleksiyon, dedup ve EMPTY/READY durumu (§9.1).

```go
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
package library

import (
	"sort"
	"strings"
)

// Track — format alanı §22.1 temel setine kilitli.
type Track struct {
	ID          string // içerik hash → ETag deseni (§22.5)
	Title       string
	Artist      string
	Album       string
	DurationSec int
	Format      string // "mp3" | "flac" | "wav" (§22.1)
}

type Library struct {
	tracks map[string]Track // key = id → dedup
}

func New() *Library { return &Library{tracks: map[string]Track{}} }

func (l *Library) Add(t Track) bool {
	if _, ok := l.tracks[t.ID]; ok {
		return false // dedup
	}
	l.tracks[t.ID] = t
	return true
}

func (l *Library) Query(artist, format string) []Track {
	var out []Track
	for _, t := range l.tracks {
		if artist != "" && !strings.EqualFold(t.Artist, artist) {
			continue
		}
		if format != "" && t.Format != format {
			continue
		}
		out = append(out, t)
	}
	sort.Slice(out, func(i, j int) bool { return out[i].Title < out[j].Title })
	return out
}

func (l *Library) State() string {
	if len(l.tracks) == 0 {
		return "EMPTY" // §9.1 → K011
	}
	return "READY"
}
```

## §9 Rust

Sahiplikli koleksiyon modeli, Result tabanlı dedup ve sorgu (§9.1).

```rust
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
use std::collections::HashMap;

#[derive(Debug, Clone)]
pub struct Track {
    pub id: String,    // içerik hash → ETag deseni (§22.5)
    pub title: String,
    pub artist: String,
    pub album: String,
    pub duration_sec: u32,
    pub format: String, // "mp3" | "flac" | "wav" (§22.1 temel set)
}

pub struct Library {
    tracks: HashMap<String, Track>, // key = id → dedup
}

impl Library {
    pub fn new() -> Self { Self { tracks: HashMap::new() } }

    /// Aynı içerik (hash) ikinci kez eklenmez — dedup.
    pub fn add(&mut self, t: Track) -> Result<(), &'static str> {
        if self.tracks.contains_key(&t.id) {
            return Err("duplicate");
        }
        self.tracks.insert(t.id.clone(), t);
        Ok(())
    }

    pub fn query(&self, artist: Option<&str>, format: Option<&str>) -> Vec<&Track> {
        let mut out: Vec<&Track> = self
            .tracks
            .values()
            .filter(|t| artist.map_or(true, |a| t.artist.eq_ignore_ascii_case(a)))
            .filter(|t| format.map_or(true, |f| t.format == f))
            .collect();
        out.sort_by(|a, b| a.title.to_lowercase().cmp(&b.title.to_lowercase()));
        out
    }

    pub fn state(&self) -> &'static str {
        if self.tracks.is_empty() { "EMPTY" } else { "READY" } // §9.1 → K011
    }
}
```

## §10 Python

dataclass modeli, dedup ve filtreli sorgu — okunabilirlik odaklı (§9.1).

```python
# TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
from dataclasses import dataclass


@dataclass(frozen=True)
class Track:
    id: str            # içerik hash → ETag deseni (§22.5)
    title: str
    artist: str
    album: str
    duration_sec: int
    format: str        # "mp3" | "flac" | "wav" (§22.1 temel set)


class Library:
    def __init__(self) -> None:
        self._tracks: dict[str, Track] = {}

    def add(self, track: Track) -> bool:
        """dedup: aynı içerik hash ikinci kez eklenmez."""
        if track.id in self._tracks:
            return False
        self._tracks[track.id] = track
        return True

    def query(self, artist: str | None = None, album: str | None = None) -> list[Track]:
        """listeleme · filtreleme · sıralama (§9.1)."""
        out = list(self._tracks.values())
        if artist is not None:
            out = [t for t in out if t.artist.casefold() == artist.casefold()]
        if album is not None:
            out = [t for t in out if t.album.casefold() == album.casefold()]
        return sorted(out, key=lambda t: t.title.casefold())

    @property
    def state(self) -> str:
        return "EMPTY" if not self._tracks else "READY"  # §9.1 → K011
```

## §11 Java

Record model + Stream sorgusu + dedup (§9.1).

```java
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1)
import java.util.*;

record Track(String id, String title, String artist, String album,
             int durationSec, String format) {} // format: mp3|flac|wav (§22.1 temel set)

public final class Library {
    private final Map<String, Track> tracks = new LinkedHashMap<>(); // key = id → dedup (§22.5)

    public boolean add(Track t) {
        if (tracks.containsKey(t.id())) return false; // dedup
        tracks.put(t.id(), t);
        return true;
    }

    public List<Track> query(String artist, String format) {
        return tracks.values().stream()
                .filter(t -> artist == null || t.artist().equalsIgnoreCase(artist))
                .filter(t -> format == null || t.format().equals(format))
                .sorted(Comparator.comparing(Track::title))
                .toList();
    }

    public String state() {
        return tracks.isEmpty() ? "EMPTY" : "READY"; // §9.1 → K011
    }
}
```

## §12 Swift

Dictionary tabanlı koleksiyon + sıralı playlist (§9.1).

```swift
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1)
import Foundation

enum Format: String {
    case mp3, flac, wav // §22.1 temel set (TASARIM ÖNERİSİ)
}

struct Track: Hashable {
    let id: String          // içerik hash → ETag deseni (§22.5)
    let title: String
    let artist: String
    let album: String
    let durationSec: Int
    let format: Format
}

struct Playlist {
    let id: String
    let name: String
    let trackIds: [String]  // sıralı yapı (§9.1)
}

final class Library {
    private var tracks: [String: Track] = [:] // key = id → dedup

    @discardableResult
    func add(_ track: Track) -> Bool {
        guard tracks[track.id] == nil else { return false } // dedup
        tracks[track.id] = track
        return true
    }

    func query(artist: String? = nil) -> [Track] {
        var out = tracks.values.sorted { $0.title.localizedCompare($1.title) == .orderedAscending }
        if let artist {
            out = out.filter { $0.artist.caseInsensitiveCompare(artist) == .orderedSame }
        }
        return out
    }

    var state: String { tracks.isEmpty ? "EMPTY" : "READY" } // §9.1 → K011
}
```

## §13 Kotlin

data class koleksiyonu, putIfAbsent dedup'u ve filtreli sorgu (§9.1).

```kotlin
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
data class Track(
    val id: String,        // içerik hash → ETag deseni (§22.5)
    val title: String,
    val artist: String,
    val album: String,
    val durationSec: Int,
    val format: String     // "mp3" | "flac" | "wav" (§22.1 temel set)
)

data class Playlist(val id: String, val name: String, val trackIds: List<String>) // sıralı (§9.1)

class Library {
    private val tracks = LinkedHashMap<String, Track>() // key = id → dedup

    fun add(track: Track): Boolean = tracks.putIfAbsent(track.id, track) == null

    fun query(artist: String? = null, format: String? = null): List<Track> = tracks.values
        .filter { artist == null || it.artist.equals(artist, ignoreCase = true) }
        .filter { format == null || it.format == format }
        .sortedBy { it.title.lowercase() }

    val state: String get() = if (tracks.isEmpty()) "EMPTY" else "READY" // §9.1 → K011
}
```

## §14 Ruby

Struct modeli, hash tabanlı dedup ve sorgu (§9.1).

```ruby
# TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
Track = Struct.new(:id, :title, :artist, :album, :duration_sec, :format, keyword_init: true)

class Library
  def initialize
    @tracks = {} # key = içerik hash → dedup (ETag deseni §22.5)
  end

  def add(track)
    return false if @tracks.key?(track.id) # dedup
    @tracks[track.id] = track
    true
  end

  # listeleme · filtreleme · sıralama (§9.1)
  def query(artist: nil, format: nil)
    out = @tracks.values
    out = out.select { |t| t.artist.casecmp(artist).zero? } if artist
    out = out.select { |t| t.format == format } if format # §22.1 temel set: mp3|flac|wav
    out.sort_by { |t| t.title.downcase }
  end

  def state
    @tracks.empty? ? 'EMPTY' : 'READY' # §9.1 → K011
  end
end
```

## §15 Elixir

Map tabanlı koleksiyon, {:error, :duplicate} dedup ve durum (§9.1).

```elixir
# TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
defmodule Library do
  @moduledoc "Koleksiyon kümesi — dedup içerik hash ile (§22.5 ETag deseni)."

  defmodule Track do
    @enforce_keys [:id, :title, :artist, :album]
    defstruct [:id, :title, :artist, :album, duration_sec: 0, format: "mp3"]
    # format: "mp3" | "flac" | "wav" (§22.1 temel set)
  end

  def new, do: %{} # %{id => %Track{}} — key = id → dedup

  def add(tracks, %Track{id: id} = track) do
    if Map.has_key?(tracks, id) do
      {:error, :duplicate} # dedup
    else
      {:ok, Map.put(tracks, id, track)}
    end
  end

  # listeleme · filtreleme · sıralama (§9.1)
  def query(tracks, artist \\ nil, format \\ nil) do
    tracks
    |> Map.values()
    |> Enum.filter(fn t -> is_nil(artist) or String.downcase(t.artist) == String.downcase(artist) end)
    |> Enum.filter(fn t -> is_nil(format) or t.format == format end)
    |> Enum.sort_by(&String.downcase(&1.title))
  end

  def state(tracks) when map_size(tracks) == 0, do: "EMPTY" # §9.1 → K011
  def state(_tracks), do: "READY"
end
```

## §16 Scala

case class koleksiyonu, immutable Map ve sorgu (§9.1).

```scala
// TASARIM ÖNERİSİ — K014-media/library (master §9.1 · §22.1 · §22.5)
final case class Track(
    id: String,          // içerik hash → ETag deseni (§22.5)
    title: String,
    artist: String,
    album: String,
    durationSec: Int,
    format: String       // "mp3" | "flac" | "wav" (§22.1 temel set)
)

final case class Playlist(id: String, name: String, trackIds: List[String]) // sıralı (§9.1)

final class Library:
  private var tracks = Map.empty[String, Track] // key = id → dedup

  def add(t: Track): Boolean =
    if tracks.contains(t.id) then false // dedup
    else { tracks += (t.id -> t); true }

  def query(artist: Option[String] = None, format: Option[String] = None): List[Track] =
    tracks.values.toList
      .filter(t => artist.forall(_.equalsIgnoreCase(t.artist)))
      .filter(t => format.forall(_ == t.format))
      .sortBy(_.title.toLowerCase)

  def state: String = if (tracks.isEmpty) then "EMPTY" else "READY" // §9.1 → K011
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | library rolü/kapsam/sınır | K014-media/media-master.md §3/1 · §9.1 | model · sorgu · EMPTY · playlist |
| 2 | Format seti (MP3 · FLAC · WAV · Opus) | K014-media/media-master.md §22.1 | track.format alanı |
| 3 | Kimlik/dedup (MusicBrainz · AcoustID) | K014-media/media-master.md §22.3 | dedup tasarımı |
| 4 | ETag/içerik hash · ARC/LRU/TTL | K014-media/media-master.md §22.5 | dedup + önbellek deseni |
| 5 | Çocuk künyesi + açık sorular | K014-media/library/index.md §A Kapılar | şema K007 bekliyor |
| 6 | Örnek formatı referansı | K015-AI/ai-ornekler.md §1 | kılavuz şeması |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
