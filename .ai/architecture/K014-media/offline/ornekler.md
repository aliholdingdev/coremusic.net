---
node_id: K014-media/offline/ornekler
node_type: module
status: draft
parent_id: K014-media/offline
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.8"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/offline — ornekler.md

> **Not:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** illustrative design örnekleridir
> (kademe-2 design node — repo'da henüz bu alanın kodu YOK). Dayanak: media-master §9.8
> (çevrimdışı kullanılabilirlik · indirilen/önbelleğe alınan parçalar) + §22.5 (ARC/LRU/TTL
> eviction + `Cache-Control: immutable`/ETag) + §9.6 (bağlantı koparsa offline fallback).
> Kod yorumları İngilizcedir; dosya salt-okunur vault içindir, kaynak koda dokunmaz.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Örnek statüsü | hepsi TASARIM ÖNERİSİ — repo'da bu alan kodu YOK |
| 2 | Dayanak | media-master §9.8 + §22.5 (uydurma standart yasak) |
| 3 | Dil listesi | 15 dil: PHP · JavaScript · TypeScript · C++ · C# · SQL · Go · Rust · Python · Java · Swift · Kotlin · Ruby · Elixir · Scala (§2-§16) |
| 4 | Hazırlık | parça başına `is_offline_ready` = dosya mevcut + ETag/SHA-256 checksum eşleşiyor |
| 5 | Kota/eviction | kota aşılınca LRU/TTL/ARC ile silme (§22.5) — eşik ⚠️ UNKNOWN (Vault Steward §16/3) |
| 6 | Fallback | parça çevrimdışı hazır değilse akışa nazik düşme (master §9.6) |
| 7 | Değişiklik | yok — bu dosya yalnız tasarımı gösterir |

## §2 PHP

Offline hazırlık işaretçisi + kota denetimi + LRU eviction + streaming fallback.

```php
<?php
declare(strict_types=1);

/**
 * K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
 * Grounded in media-master.md §9.8 (çevrimdışı kullanılabilirlik)
 * + §22.5 (LRU/TTL eviction + ETag = içerik hash) + §9.6 (fallback).
 * Repo'da bu sınıfın kodu YOK — illustrative design example.
 */
final class OfflineAvailability
{
    public const READY   = 'READY';
    public const PENDING = 'PENDING';
    public const MISSING = 'MISSING';

    /** @var array<string,array{path:string,size:int,hash:string,last:int}> */
    private array $tracks = [];
    private int $quotaBytes; // eşik ⚠️ UNKNOWN — Vault Steward §16/3

    public function __construct(int $quotaBytes)
    {
        $this->quotaBytes = $quotaBytes;
    }

    // Parça bazında hazırlık: dosya var mı + ETag/SHA-256 içerik hash eşleşiyor mu?
    public function isOfflineReady(string $trackId): bool
    {
        if (!isset($this->tracks[$trackId])) {
            return false;
        }
        $entry = $this->tracks[$trackId];
        if (!is_file($entry['path'])) {
            return false; // dosya silinmiş/eksik — fallback (master §9.6)
        }
        return hash_equals($entry['hash'], (string) hash_file('sha256', $entry['path']));
    }

    /** @return string READY | PENDING | MISSING */
    public function mark(string $trackId, string $path, string $hash, int $lastAccess): string
    {
        if (!is_file($path)) {
            return self::MISSING;
        }
        $this->tracks[$trackId] = [
            'path' => $path,
            'size' => (int) filesize($path),
            'hash' => $hash,
            'last' => $lastAccess, // LRU sırası (master §22.5)
        ];
        return $this->usedBytes() <= $this->quotaBytes ? self::READY : self::PENDING;
    }

    public function usedBytes(): int
    {
        return array_sum(array_column($this->tracks, 'size'));
    }

    // LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim önce silinir.
    public function pickEvictionVictim(): ?string
    {
        if ($this->usedBytes() <= $this->quotaBytes || $this->tracks === []) {
            return null;
        }
        $last = array_column($this->tracks, 'last');
        array_multisort($last, SORT_ASC, array_keys($this->tracks), SORT_STRING);
        return (string) array_key_first($this->tracks);
    }

    // Hazır değilse akışa nazik düşme (master §9.6 fallback).
    public function resolve(string $trackId): array
    {
        if ($this->isOfflineReady($trackId)) {
            return ['mode' => 'OFFLINE', 'path' => $this->tracks[$trackId]['path']];
        }
        return ['mode' => 'STREAMING', 'reason' => 'not offline ready'];
    }
}
```

## §3 JavaScript

`is_offline_ready` kontrolü + kota/eviction + fallback — asenkron dosya portu ile.

```js
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 (çevrimdışı) + §22.5 (LRU/TTL eviction + ETag)
// + §9.6 (streaming fallback).
// No repo code yet — illustrative design example.

const READY = 'READY', PENDING = 'PENDING', MISSING = 'MISSING';

function createOfflineStore({ quotaBytes, fs, now = Date.now }) {
  const tracks = new Map(); // id -> { path, size, expectedHash, lastAccess }

  // Parça bazında hazırlık: dosya var mı + ETag/SHA-256 içerik hash eşleşiyor mu.
  async function isOfflineReady(id) {
    const t = tracks.get(id);
    if (!t || !(await fs.exists(t.path))) return false; // eksikse fallback (master §9.6)
    const actual = await fs.sha256(t.path);             // içerik hash (master §22.5)
    return actual === t.expectedHash;
  }

  async function mark(id, path, expectedHash) {
    if (!(await fs.exists(path))) return MISSING;
    tracks.set(id, { path, size: await fs.size(path), expectedHash, lastAccess: now() });
    return usedBytes() <= quotaBytes ? READY : PENDING; // kota aşıldı → eviction bekler
  }

  const usedBytes = () => [...tracks.values()].reduce((n, t) => n + t.size, 0);

  // LRU eviction (master §22.5 TASARIM ÖNERİSİ): en eski erişim önce silinir.
  function pickEvictionVictim() {
    if (usedBytes() <= quotaBytes || tracks.size === 0) return null;
    let victim = null, oldest = Infinity;
    for (const [id, t] of tracks) {
      if (t.lastAccess < oldest) { oldest = t.lastAccess; victim = id; }
    }
    return victim;
  }

  // Hazır değilse akışa nazik düşme (master §9.6 fallback).
  async function resolve(id) {
    return (await isOfflineReady(id))
      ? { mode: 'OFFLINE', path: tracks.get(id).path }
      : { mode: 'STREAMING', reason: 'not offline ready' };
  }

  return { isOfflineReady, mark, usedBytes, pickEvictionVictim, resolve };
}
```

## §4 TypeScript

Tip güvenli offline state union + port arayüzü + LRU eviction sözleşmesi.

```ts
// K014-media/offline — typed offline state + quota eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (ARC/LRU/TTL + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.

export type OfflineState = 'READY' | 'PENDING' | 'MISSING';

export interface TrackEntry {
  id: string;
  path: string;
  sizeBytes: number;
  expectedHash: string;  // indirme sırasında kaydedilen içerik hash (ETag — §22.5)
  lastAccess: number;    // LRU sırası (epoch — §22.5)
}

export type ResolveResult =
  | { mode: 'OFFLINE'; path: string }
  | { mode: 'STREAMING'; reason: string }; // fallback (master §9.6)

export interface FileSystemPort {
  exists(path: string): Promise<boolean>;
  sha256(path: string): Promise<string>;
}

export class OfflineStore {
  private readonly tracks = new Map<string, TrackEntry>();

  constructor(private readonly quotaBytes: number,   // eşik ⚠️ UNKNOWN — Vault Steward §16/3
              private readonly fs: FileSystemPort) {}

  /** Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği. */
  async isOfflineReady(id: string): Promise<boolean> {
    const t = this.tracks.get(id);
    if (!t || !(await this.fs.exists(t.path))) return false;
    return (await this.fs.sha256(t.path)) === t.expectedHash;
  }

  async mark(entry: TrackEntry): Promise<OfflineState> {
    if (!(await this.fs.exists(entry.path))) return 'MISSING';
    this.tracks.set(entry.id, entry);
    return this.usedBytes() <= this.quotaBytes ? 'READY' : 'PENDING';
  }

  usedBytes(): number {
    return [...this.tracks.values()].reduce((n, t) => n + t.sizeBytes, 0);
  }

  /** LRU eviction (§22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim. */
  pickEvictionVictim(): string | null {
    if (this.usedBytes() <= this.quotaBytes || this.tracks.size === 0) return null;
    let victim: string | null = null;
    let oldest = Infinity;
    for (const [id, t] of this.tracks) {
      if (t.lastAccess < oldest) { oldest = t.lastAccess; victim = id; }
    }
    return victim;
  }

  /** Hazır değilse akışa nazik düşme (master §9.6 fallback). */
  async resolve(id: string): Promise<ResolveResult> {
    const t = this.tracks.get(id);
    if (t && (await this.isOfflineReady(id))) return { mode: 'OFFLINE', path: t.path };
    return { mode: 'STREAMING', reason: 'not offline ready' };
  }
}
```

## §5 C++

`std::filesystem` ile hazırlık kontrolü + kota/LRU eviction + fallback kararı.

```cpp
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL eviction + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.
#include <cstdint>
#include <filesystem>
#include <optional>
#include <string>
#include <unordered_map>
#include <vector>

namespace cm::offline {

enum class OfflineState : std::uint8_t { Ready, Pending, Missing };

struct TrackEntry {
    std::string path;
    std::uint64_t sizeBytes = 0;
    std::string expectedHash;    // indirme sırasında kaydedilen içerik hash (ETag — §22.5)
    std::int64_t lastAccess = 0; // LRU sırası (epoch — §22.5)
};

class OfflineStore {
public:
    explicit OfflineStore(std::uint64_t quotaBytes) noexcept
        : quota_(quotaBytes) {} // eşik ⚠️ UNKNOWN — Vault Steward §16/3

    // Parça bazında hazırlık: dosya var mı + hash eşleşiyor mu (SHA-256/ETag).
    // Hazır değilse streaming fallback tetiklenir (master §9.6).
    bool isOfflineReady(const std::string& id, const std::string& actualHash) const noexcept {
        auto it = tracks_.find(id);
        if (it == tracks_.end()) return false;
        std::error_code ec;
        return std::filesystem::is_regular_file(it->second.path, ec)
            && !ec && actualHash == it->second.expectedHash;
    }

    OfflineState mark(const std::string& id, TrackEntry entry) noexcept {
        std::error_code ec;
        if (!std::filesystem::is_regular_file(entry.path, ec) || ec) return OfflineState::Missing;
        tracks_[id] = std::move(entry);
        return usedBytes() <= quota_ ? OfflineState::Ready : OfflineState::Pending;
    }

    std::uint64_t usedBytes() const noexcept {
        std::uint64_t total = 0;
        for (const auto& kv : tracks_) total += kv.second.sizeBytes;
        return total;
    }

    // LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim.
    std::optional<std::string> pickEvictionVictim() const noexcept {
        if (usedBytes() <= quota_ || tracks_.empty()) return std::nullopt;
        std::optional<std::string> victim;
        std::int64_t oldest = INT64_MAX;
        for (const auto& kv : tracks_) {
            if (kv.second.lastAccess < oldest) { oldest = kv.second.lastAccess; victim = kv.first; }
        }
        return victim;
    }

    // Hazır değilse akışa nazik düşme (master §9.6 fallback).
    const char* resolveMode(const std::string& id, const std::string& actualHash) const noexcept {
        return isOfflineReady(id, actualHash) ? "OFFLINE" : "STREAMING";
    }

private:
    std::uint64_t quota_;
    std::unordered_map<std::string, TrackEntry> tracks_;
};

} // namespace cm::offline
```

## §6 C#

`SHA256` checksum ile `IsOfflineReady`, kota/LRU eviction ve fallback resolve.

```csharp
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Security.Cryptography;
using System.Threading.Tasks;

namespace CoreMusic.Offline;

public enum OfflineState { Ready, Pending, Missing }

public sealed record TrackEntry(string Id, string Path, long SizeBytes,
                                string ExpectedHash, DateTimeOffset LastAccess);

public sealed class OfflineStore
{
    private readonly Dictionary<string, TrackEntry> _tracks = new();
    private readonly long _quotaBytes; // eşik ⚠️ UNKNOWN — Vault Steward §16/3

    public OfflineStore(long quotaBytes) => _quotaBytes = quotaBytes;

    // Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 içerik hash eşitliği.
    // Hazır değilse streaming fallback (master §9.6) tetiklenir.
    public async Task<bool> IsOfflineReadyAsync(string trackId)
    {
        if (!_tracks.TryGetValue(trackId, out var t) || !File.Exists(t.Path)) return false;
        await using var stream = File.OpenRead(t.Path);
        var actual = Convert.ToHexString(await SHA256.HashDataAsync(stream));
        return string.Equals(actual, t.ExpectedHash, StringComparison.OrdinalIgnoreCase);
    }

    public OfflineState Mark(TrackEntry entry)
    {
        if (!File.Exists(entry.Path)) return OfflineState.Missing;
        _tracks[entry.Id] = entry;
        return UsedBytes() <= _quotaBytes ? OfflineState.Ready : OfflineState.Pending;
    }

    public long UsedBytes() => _tracks.Values.Sum(t => t.SizeBytes);

    // LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim önce silinir.
    public string? PickEvictionVictim()
    {
        if (UsedBytes() <= _quotaBytes || _tracks.Count == 0) return null;
        return _tracks.Values.OrderBy(t => t.LastAccess).First().Id;
    }

    // Hazır değilse akışa nazik düşme (master §9.6 fallback).
    public async Task<(string Mode, string? Path)> ResolveAsync(string trackId) =>
        await IsOfflineReadyAsync(trackId)
            ? ("OFFLINE", _tracks[trackId].Path)
            : ("STREAMING", null);
}
```

## §7 SQL

Çevrimdışı durum/kota şema taslağı — NOT: kalıcı şema K007-data'ya aittir (master §2.2); bu blok taslaktır.

```sql
-- K014-media/offline — çevrimdışı durum/kota şema taslağı (TASARIM ÖNERİSİ).
-- Grounded in media-master.md §9.8 (çevrimdışı kullanılabilirlik)
-- + §22.5 (LRU/TTL eviction + ETag) + §2.2 (şema sahibi K007-data).
-- NOT: kalıcı şema K007-data'ya aittir — bu blok taslak/örnek niteliğindedir.

CREATE TABLE offline_state (
    track_id         BIGINT       NOT NULL,
    is_offline_ready BOOLEAN      NOT NULL DEFAULT FALSE, -- parça bazında hazırlık
    local_path       VARCHAR(1024) NULL,                  -- yerel dosya yolu
    size_bytes       BIGINT       NOT NULL DEFAULT 0,     -- kota hesabı
    content_hash     CHAR(64)     NULL,                   -- SHA-256 (ETag = içerik hash — §22.5)
    last_access_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP, -- LRU sırası (§22.5)
    expires_at       TIMESTAMP    NULL,                   -- TTL (§22.5)
    state            ENUM('READY','PENDING','MISSING') NOT NULL DEFAULT 'MISSING',
    PRIMARY KEY (track_id),
    KEY idx_lru (last_access_at),
    KEY idx_ttl (expires_at)
) ENGINE=InnoDB;

-- Kota denetimi: toplam çevrimdışı bayt (eşik ⚠️ UNKNOWN — Vault Steward §16/3)
SELECT SUM(size_bytes) AS used_bytes
  FROM offline_state
 WHERE is_offline_ready = TRUE;

-- Eviction adayı: en eski erişim + süresi dolanlar (LRU + TTL — master §22.5)
SELECT track_id, size_bytes, last_access_at
  FROM offline_state
 WHERE is_offline_ready = TRUE
 ORDER BY last_access_at ASC
 LIMIT 50;

-- Fallback kontrolü: hazır olmayan parçalar akışa düşer (master §9.6)
SELECT COUNT(*) AS not_ready
  FROM offline_state
 WHERE is_offline_ready = FALSE;
```

## §8 Go

Mutex-guarded store + SHA-256 hazırlık kontrolü + LRU eviction + fallback.

```go
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL eviction + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.
package offline

import (
	"crypto/sha256"
	"encoding/hex"
	"os"
	"sort"
	"sync"
	"time"
)

type State string

const (
	StateReady   State = "READY"
	StatePending State = "PENDING"
	StateMissing State = "MISSING"
)

type Track struct {
	Path         string
	SizeBytes    int64
	ExpectedHash string    // indirme sırasında kaydedilen içerik hash (ETag — §22.5)
	LastAccess   time.Time // LRU sırası (§22.5)
}

type Store struct {
	mu         sync.Mutex
	quotaBytes int64 // eşik ⚠️ UNKNOWN — Vault Steward §16/3
	tracks     map[string]Track
}

func NewStore(quotaBytes int64) *Store {
	return &Store{quotaBytes: quotaBytes, tracks: map[string]Track{}}
}

// IsOfflineReady: dosya mevcut + SHA-256 içerik hash eşitliği.
// Hazır değilse streaming fallback tetiklenir (master §9.6).
func (s *Store) IsOfflineReady(id string, actualHash string) bool {
	s.mu.Lock()
	defer s.mu.Unlock()
	t, ok := s.tracks[id]
	if !ok {
		return false
	}
	if _, err := os.Stat(t.Path); err != nil {
		return false
	}
	return t.ExpectedHash == actualHash
}

func (s *Store) Mark(id string, t Track) State {
	s.mu.Lock()
	defer s.mu.Unlock()
	if _, err := os.Stat(t.Path); err != nil {
		return StateMissing
	}
	s.tracks[id] = t
	if s.usedBytesLocked() <= s.quotaBytes {
		return StateReady
	}
	return StatePending
}

func (s *Store) usedBytesLocked() int64 {
	var total int64
	for _, t := range s.tracks {
		total += t.SizeBytes
	}
	return total
}

func (s *Store) UsedBytes() int64 {
	s.mu.Lock()
	defer s.mu.Unlock()
	return s.usedBytesLocked()
}

// PickEvictionVictim: kota aşılırsa en eski erişimli parça (LRU — §22.5 TASARIM ÖNERİSİ).
func (s *Store) PickEvictionVictim() (string, bool) {
	s.mu.Lock()
	defer s.mu.Unlock()
	if s.usedBytesLocked() <= s.quotaBytes || len(s.tracks) == 0 {
		return "", false
	}
	ids := make([]string, 0, len(s.tracks))
	for id := range s.tracks {
		ids = append(ids, id)
	}
	sort.Slice(ids, func(i, j int) bool {
		return s.tracks[ids[i]].LastAccess.Before(s.tracks[ids[j]].LastAccess)
	})
	return ids[0], true
}

// ResolveMode: hazır değilse streaming (master §9.6 fallback).
func (s *Store) ResolveMode(id, actualHash string) string {
	if s.IsOfflineReady(id, actualHash) {
		return "OFFLINE"
	}
	return "STREAMING"
}

func HashBytes(b []byte) string {
	sum := sha256.Sum256(b)
	return hex.EncodeToString(sum[:])
}
```

## §9 Rust

`Result`/`Option` tabanlı eviction + hazırlık kontrolü — panic yok.

```rust
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL eviction + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.

use std::collections::HashMap;
use std::path::Path;

#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum OfflineState { Ready, Pending, Missing }

#[derive(Debug, Clone)]
pub struct TrackEntry {
    pub path: String,
    pub size_bytes: u64,
    pub expected_hash: String, // indirme sırasında kaydedilen içerik hash (ETag — §22.5)
    pub last_access: u64,      // LRU sırası (epoch — §22.5)
}

pub struct OfflineStore {
    quota_bytes: u64,          // eşik ⚠️ UNKNOWN — Vault Steward §16/3
    tracks: HashMap<String, TrackEntry>,
}

impl OfflineStore {
    pub fn new(quota_bytes: u64) -> Self {
        Self { quota_bytes, tracks: HashMap::new() }
    }

    /// Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
    /// Hazır değilse streaming fallback (master §9.6).
    pub fn is_offline_ready(&self, id: &str, actual_hash: &str) -> bool {
        self.tracks.get(id)
            .filter(|t| Path::new(&t.path).is_file())
            .is_some_and(|t| t.expected_hash == actual_hash)
    }

    pub fn mark(&mut self, id: &str, entry: TrackEntry) -> OfflineState {
        if !Path::new(&entry.path).is_file() {
            return OfflineState::Missing;
        }
        let state = if self.used_bytes() <= self.quota_bytes {
            OfflineState::Ready
        } else {
            OfflineState::Pending // kota aşıldı → eviction bekliyor
        };
        self.tracks.insert(id.to_string(), entry);
        state
    }

    pub fn used_bytes(&self) -> u64 {
        self.tracks.values().map(|t| t.size_bytes).sum()
    }

    /// LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim.
    pub fn pick_eviction_victim(&self) -> Option<&str> {
        if self.used_bytes() <= self.quota_bytes {
            return None;
        }
        self.tracks.iter()
            .min_by_key(|(_, t)| t.last_access)
            .map(|(id, _)| id.as_str())
    }

    /// Hazır değilse akışa nazik düşme (master §9.6 fallback).
    pub fn resolve_mode(&self, id: &str, actual_hash: &str) -> &'static str {
        if self.is_offline_ready(id, actual_hash) { "OFFLINE" } else { "STREAMING" }
    }
}
```

## §10 Python

Dataclass store + `is_offline_ready` + LRU eviction + fallback.

```python
# K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.8 (çevrimdışı) + §22.5 (LRU/TTL eviction + ETag)
# + §9.6 (fallback).
# Repo'da bu modülün kodu YOK — illustrative design example.

from __future__ import annotations

import hashlib
import os
from dataclasses import dataclass

READY, PENDING, MISSING = "READY", "PENDING", "MISSING"


@dataclass
class TrackEntry:
    path: str
    size_bytes: int
    expected_hash: str  # indirme sırasında kaydedilen içerik hash (ETag — §22.5)
    last_access: float  # LRU sırası (epoch — §22.5)


def file_sha256(path: str) -> str:
    h = hashlib.sha256()
    with open(path, "rb") as f:
        for block in iter(lambda: f.read(65536), b""):
            h.update(block)
    return h.hexdigest()  # içerik hash (ETag karşılığı — master §22.5)


class OfflineStore:
    def __init__(self, quota_bytes: int) -> None:
        self.quota_bytes = quota_bytes  # eşik ⚠️ UNKNOWN — Vault Steward §16/3
        self.tracks: dict[str, TrackEntry] = {}

    def is_offline_ready(self, track_id: str, actual_hash: str | None = None) -> bool:
        """Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
        Hazır değilse streaming fallback (master §9.6) tetiklenir."""
        entry = self.tracks.get(track_id)
        if entry is None or not os.path.isfile(entry.path):
            return False
        actual = actual_hash if actual_hash is not None else file_sha256(entry.path)
        return actual == entry.expected_hash

    def mark(self, track_id: str, entry: TrackEntry) -> str:
        if not os.path.isfile(entry.path):
            return MISSING
        self.tracks[track_id] = entry
        return READY if self.used_bytes() <= self.quota_bytes else PENDING

    def used_bytes(self) -> int:
        return sum(t.size_bytes for t in self.tracks.values())

    def pick_eviction_victim(self) -> str | None:
        """LRU eviction (master §22.5 TASARIM ÖNERİSİ): en eski erişim önce silinir."""
        if self.used_bytes() <= self.quota_bytes or not self.tracks:
            return None
        return min(self.tracks, key=lambda k: self.tracks[k].last_access)

    def resolve(self, track_id: str) -> dict:
        """Hazır değilse akışa nazik düşme (master §9.6 fallback)."""
        entry = self.tracks.get(track_id)
        if entry is not None and self.is_offline_ready(track_id):
            return {"mode": "OFFLINE", "path": entry.path}
        return {"mode": "STREAMING", "reason": "not offline ready"}
```

## §11 Java

`MessageDigest` SHA-256 hazırlık kontrolü + `Comparator` LRU eviction + fallback.

```java
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL eviction + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.
package com.coremusic.offline;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.security.MessageDigest;
import java.util.Comparator;
import java.util.HashMap;
import java.util.Map;
import java.util.Optional;

public final class OfflineStore {
    public enum State { READY, PENDING, MISSING }

    record TrackEntry(Path path, long sizeBytes, String expectedHash, long lastAccess) {}

    private final long quotaBytes; // eşik ⚠️ UNKNOWN — Vault Steward §16/3
    private final Map<String, TrackEntry> tracks = new HashMap<>();

    public OfflineStore(long quotaBytes) { this.quotaBytes = quotaBytes; }

    /** Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
     *  Hazır değilse streaming fallback (master §9.6). */
    public boolean isOfflineReady(String trackId) {
        var t = tracks.get(trackId);
        if (t == null || !Files.isRegularFile(t.path())) return false;
        try {
            byte[] digest = MessageDigest.getInstance("SHA-256").digest(Files.readAllBytes(t.path()));
            return hex(digest).equalsIgnoreCase(t.expectedHash()); // içerik hash (§22.5)
        } catch (IOException | NoSuchAlgorithmException e) {
            return false;
        }
    }

    public State mark(String trackId, TrackEntry entry) {
        if (!Files.isRegularFile(entry.path())) return State.MISSING;
        tracks.put(trackId, entry);
        return usedBytes() <= quotaBytes ? State.READY : State.PENDING;
    }

    public long usedBytes() {
        return tracks.values().stream().mapToLong(TrackEntry::sizeBytes).sum();
    }

    /** LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim. */
    public Optional<String> pickEvictionVictim() {
        if (usedBytes() <= quotaBytes || tracks.isEmpty()) return Optional.empty();
        return tracks.entrySet().stream()
                .min(Comparator.comparingLong(e -> e.getValue().lastAccess()))
                .map(Map.Entry::getKey);
    }

    /** Hazır değilse akışa nazik düşme (master §9.6 fallback). */
    public String resolveMode(String trackId) {
        return isOfflineReady(trackId) ? "OFFLINE" : "STREAMING";
    }

    private static String hex(byte[] bytes) {
        var sb = new StringBuilder();
        for (byte b : bytes) sb.append(String.format("%02x", b & 0xff));
        return sb.toString();
    }
}
```

## §12 Swift

`CryptoKit` SHA-256 hazırlık kontrolü + value-type store + LRU eviction + fallback.

```swift
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL eviction + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.
import Foundation
import CryptoKit // SHA-256 — dile yerleşik

enum OfflineState: String { case ready = "READY", pending = "PENDING", missing = "MISSING" }

struct TrackEntry {
    let id: String
    let path: URL
    let sizeBytes: Int64
    let expectedHash: String // indirme sırasında kaydedilen içerik hash (ETag — §22.5)
    var lastAccess: Date      // LRU sırası (§22.5)
}

struct OfflineStore {
    let quotaBytes: Int64 // eşik ⚠️ UNKNOWN — Vault Steward §16/3
    private(set) var tracks: [String: TrackEntry] = [:]

    /// Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
    /// Hazır değilse streaming fallback (master §9.6).
    func isOfflineReady(_ id: String) -> Bool {
        guard let t = tracks[id], let data = try? Data(contentsOf: t.path) else { return false }
        let digest = SHA256.hash(data: data).map { String(format: "%02x", $0) }.joined()
        return digest == t.expectedHash
    }

    mutating func mark(_ entry: TrackEntry) -> OfflineState {
        guard FileManager.default.fileExists(atPath: entry.path.path) else { return .missing }
        tracks[entry.id] = entry
        return usedBytes() <= quotaBytes ? .ready : .pending // kota aşıldı → eviction bekler
    }

    func usedBytes() -> Int64 { tracks.values.reduce(0) { $0 + $1.sizeBytes } }

    /// LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim.
    func pickEvictionVictim() -> String? {
        guard usedBytes() > quotaBytes, !tracks.isEmpty,
              let oldest = tracks.values.min(by: { $0.lastAccess < $1.lastAccess })
        else { return nil }
        return oldest.id
    }

    /// Hazır değilse akışa nazik düşme (master §9.6 fallback).
    func resolve(_ id: String) -> (mode: String, path: URL?) {
        guard isOfflineReady(id), let t = tracks[id] else { return ("STREAMING", nil) }
        return ("OFFLINE", t.path)
    }
}
```

## §13 Kotlin

Sealed class durum + `MessageDigest` hazırlık kontrolü + LRU eviction + fallback.

```kotlin
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL eviction + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.
package com.coremusic.offline

import java.io.File
import java.security.MessageDigest

enum class OfflineState { READY, PENDING, MISSING }

data class TrackEntry(
    val path: String,
    val sizeBytes: Long,
    val expectedHash: String, // indirme sırasında kaydedilen içerik hash (ETag — §22.5)
    val lastAccess: Long,     // LRU sırası (epoch — §22.5)
)

class OfflineStore(private val quotaBytes: Long) { // eşik ⚠️ UNKNOWN — Vault Steward §16/3
    private val tracks = LinkedHashMap<String, TrackEntry>()

    /** Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
     *  Hazır değilse streaming fallback (master §9.6). */
    fun isOfflineReady(trackId: String): Boolean {
        val t = tracks[trackId] ?: return false
        val file = File(t.path)
        if (!file.isFile) return false
        return sha256Hex(file.readBytes()).equals(t.expectedHash, ignoreCase = true)
    }

    fun mark(trackId: String, entry: TrackEntry): OfflineState {
        if (!File(entry.path).isFile) return OfflineState.MISSING
        tracks[trackId] = entry
        return if (usedBytes() <= quotaBytes) OfflineState.READY else OfflineState.PENDING
    }

    fun usedBytes(): Long = tracks.values.sumOf { it.sizeBytes }

    /** LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim. */
    fun pickEvictionVictim(): String? {
        if (usedBytes() <= quotaBytes || tracks.isEmpty()) return null
        return tracks.entries.minByOrNull { it.value.lastAccess }?.key
    }

    /** Hazır değilse akışa nazik düşme (master §9.6 fallback). */
    fun resolve(trackId: String): String =
        if (isOfflineReady(trackId)) "OFFLINE" else "STREAMING"

    private fun sha256Hex(bytes: ByteArray): String =
        MessageDigest.getInstance("SHA-256").digest(bytes)
            .joinToString("") { "%02x".format(it.toInt() and 0xff) }
}
```

## §14 Ruby

Struct entry + `Digest::SHA256` hazırlık kontrolü + LRU eviction + fallback.

```ruby
# K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.8 (çevrimdışı) + §22.5 (LRU/TTL eviction + ETag)
# + §9.6 (fallback).
# Repo'da bu sınıfın kodu YOK — illustrative design example.

require 'digest'

class OfflineStore
  READY   = 'READY'
  PENDING = 'PENDING'
  MISSING = 'MISSING'

  TrackEntry = Struct.new(:path, :size_bytes, :expected_hash, :last_access, keyword_init: true)

  # eşik ⚠️ UNKNOWN — Vault Steward §16/3
  def initialize(quota_bytes:)
    @quota_bytes = quota_bytes
    @tracks = {} # id -> TrackEntry
  end

  # Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
  # Hazır değilse streaming fallback (master §9.6) tetiklenir.
  def offline_ready?(track_id)
    entry = @tracks[track_id]
    return false if entry.nil? || !File.file?(entry.path)
    Digest::SHA256.file(entry.path).hexdigest == entry.expected_hash # içerik hash (§22.5)
  end

  def mark(track_id, entry)
    return MISSING unless File.file?(entry.path)
    @tracks[track_id] = entry
    used_bytes <= @quota_bytes ? READY : PENDING # kota aşıldı → eviction bekler
  end

  def used_bytes
    @tracks.values.sum(&:size_bytes)
  end

  # LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim önce silinir.
  def pick_eviction_victim
    return nil if used_bytes <= @quota_bytes || @tracks.empty?
    @tracks.min_by { |_id, entry| entry.last_access }.first
  end

  # Hazır değilse akışa nazik düşme (master §9.6 fallback).
  def resolve(track_id)
    if offline_ready?(track_id)
      { mode: 'OFFLINE', path: @tracks[track_id].path }
    else
      { mode: 'STREAMING', reason: 'not offline ready' }
    end
  end
end
```

## §15 Elixir

Map tabanlı store — `File.regular?` + `:crypto.hash(:sha256, ...)` + LRU eviction + fallback.

```elixir
# K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.8 (çevrimdışı) + §22.5 (LRU/TTL eviction + ETag)
# + §9.6 (fallback).
# Repo'da bu modülün kodu YOK — illustrative design example.

defmodule CoreMusic.Offline.Store do
  @moduledoc false

  # State: %{quota_bytes:, tracks: %{id => %{path:, size_bytes:, expected_hash:, last_access:}}}
  # expected_hash = indirme sırasında kaydedilen içerik hash (ETag — master §22.5)

  def new(quota_bytes) do
    # eşik ⚠️ UNKNOWN — Vault Steward §16/3
    %{quota_bytes: quota_bytes, tracks: %{}}
  end

  # Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
  # Hazır değilse streaming fallback (master §9.6) tetiklenir.
  def offline_ready?(state, track_id, actual_hash \\ nil) do
    case state.tracks[track_id] do
      nil ->
        false

      %{path: path, expected_hash: expected} ->
        if File.regular?(path) do
          hash = actual_hash || (path |> File.read!() |> sha256_hex())
          hash == expected
        else
          false
        end
    end
  end

  def mark(state, track_id, entry) do
    if File.regular?(entry.path) do
      tracks = Map.put(state.tracks, track_id, entry)
      state = %{state | tracks: tracks}
      if used_bytes(state) <= state.quota_bytes, do: :ready, else: :pending
    else
      :missing
    end
  end

  def used_bytes(state) do
    state.tracks |> Map.values() |> Enum.map(& &1.size_bytes) |> Enum.sum()
  end

  # LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim önce silinir.
  def pick_eviction_victim(%{tracks: tracks}) when map_size(tracks) == 0, do: nil

  def pick_eviction_victim(state) do
    if used_bytes(state) > state.quota_bytes do
      state.tracks
      |> Enum.min_by(fn {_id, entry} -> entry.last_access end)
      |> elem(0)
    else
      nil
    end
  end

  # Hazır değilse akışa nazik düşme (master §9.6 fallback).
  def resolve(state, track_id) do
    if offline_ready?(state, track_id) do
      %{mode: :offline, path: state.tracks[track_id].path}
    else
      %{mode: :streaming, reason: :not_offline_ready}
    end
  end

  defp sha256_hex(bin), do: bin |> then(&:crypto.hash(:sha256, &1)) |> Base.encode16(case: :lower)
end
```

## §16 Scala

`MessageDigest` hazırlık kontrolü + `Either`-sız state + minBy LRU eviction + fallback.

```scala
// K014-media/offline — çevrimdışı hazırlık + kota/eviction (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.8 + §22.5 (LRU/TTL eviction + ETag) + §9.6 (fallback).
// No repo code yet — illustrative design example.
package com.coremusic.offline

import java.nio.file.{Files, Path}
import java.security.MessageDigest

sealed trait OfflineState
object OfflineState {
  case object Ready extends OfflineState
  case object Pending extends OfflineState
  case object Missing extends OfflineState
}

final case class TrackEntry(
  path: Path,
  sizeBytes: Long,
  expectedHash: String, // indirme sırasında kaydedilen içerik hash (ETag — §22.5)
  lastAccess: Long      // LRU sırası (epoch — §22.5)
)

final class OfflineStore(quotaBytes: Long) { // eşik ⚠️ UNKNOWN — Vault Steward §16/3
  private var tracks = Map.empty[String, TrackEntry]

  /** Parça bazında hazırlık: dosya mevcut + ETag/SHA-256 eşitliği.
   *  Hazır değilse streaming fallback (master §9.6). */
  def isOfflineReady(id: String): Boolean =
    tracks.get(id).exists { t =>
      Files.isRegularFile(t.path) && sha256Hex(Files.readAllBytes(t.path)).equalsIgnoreCase(t.expectedHash)
    }

  def mark(id: String, entry: TrackEntry): OfflineState =
    if (!Files.isRegularFile(entry.path)) OfflineState.Missing
    else {
      tracks += id -> entry
      if (usedBytes() <= quotaBytes) OfflineState.Ready else OfflineState.Pending
    }

  def usedBytes(): Long = tracks.values.map(_.sizeBytes).sum

  /** LRU eviction (master §22.5 TASARIM ÖNERİSİ): kota aşılırsa en eski erişim. */
  def pickEvictionVictim(): Option[String] =
    if (usedBytes() <= quotaBytes || tracks.isEmpty) None
    else Some(tracks.minBy { case (_, t) => t.lastAccess }._1)

  /** Hazır değilse akışa nazik düşme (master §9.6 fallback). */
  def resolve(id: String): String = if (isOfflineReady(id)) "OFFLINE" else "STREAMING"

  private def sha256Hex(bytes: Array[Byte]): String =
    MessageDigest.getInstance("SHA-256").digest(bytes).map(b => f"${b & 0xff}%02x").mkString
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | K014-media/media-master.md §9.8 | vault | offline rol/sınır/iddata (çevrimdışı kullanılabilirlik) |
| 2 | K014-media/media-master.md §22.5 | vault | ARC/LRU/TTL eviction + `Cache-Control: immutable` + ETag |
| 3 | K014-media/media-master.md §9.6 | vault | bağlantı koparsa fallback (akış ↔ çevrimdışı yönü) |
| 4 | K014-media/media-master.md §10.1 | vault | parça yaşam döngüsü — aşama 5 "çevrimdışı" |
| 5 | K014-media/media-master.md §13/3 | vault | çevrimdışı erişim test senaryosu |
| 6 | K014-media/media-master.md §7/2 | vault | sahiplik açık sorusu (⚠️ VERIFICATION REQUIRED) |
| 7 | K014-media/media-master.md §22.2 | vault | eksik yerel parçanın Range ile tamamlanması |
| 8 | K014-media/offline/index.md | vault | bu modülün künyesi (§ROL/§KAPSAM/§BİLEŞENLER/§STANDARTLAR) |

> Tüm kod blokları TASARIM ÖNERİSİ illustrative design örnekleridir (kademe-2 design node —
> repo'da henüz kodu YOK); dayanak: media-master §9.8 + §22.5 (+ §9.6 fallback).

**Authority:** Bayram Ali / Vault Steward
