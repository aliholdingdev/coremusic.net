---
node_id: K014-media/playback/ornekler
node_type: module
status: draft
parent_id: K014-media/playback
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.5"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/playback — ornekler.md

> **Not:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** illustrative design örnekleridir
> (kademe-2 design node — repo'da henüz bu alanın kodu YOK). Dayanak: media-master §9.5
> (çalma işlemi · durum · pozisyon takibi) + §22 (doğrulanmış codec/seek standartları:
> MP3/FLAC/WAV §22.1 · Xing/LAME + FLAC `SEEKTABLE` §22.2 · etiket şemaları §22.3).
> Kod yorumları İngilizcedir; dosya salt-okunur vault içindir, kaynak koda dokunmaz.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Örnek statüsü | hepsi TASARIM ÖNERİSİ — repo'da bu alan kodu YOK |
| 2 | Dayanak | media-master §9.5 + §22 (uydurma standart yasak) |
| 3 | Durum modeli | IDLE · PLAYING · PAUSED · SEEKING · ENDED (§STANDARTLAR TASARIM ÖNERİSİ) |
| 4 | Seek | MP3 → Xing/LAME tag · FLAC → `SEEKTABLE` · WAV → lineer byte offset (§22.2) |
| 5 | Codec izni | yalnız MP3 · FLAC · WAV (§22.1 TASARIM ÖNERİSİ) |
| 6 | Değişiklik | yok — bu dosya yalnız tasarımı gösterir |

## §2 PHP

Player state machine + geçiş doğrulama + seek giriş noktası (fail-safe: illegal geçiş reddedilir).

```php
<?php
declare(strict_types=1);

/**
 * K014-media/playback — player state machine (TASARIM ÖNERİSİ).
 * Grounded in K014-media/media-master.md §9.5 + §22.2 (gapless seek).
 * Repo'da bu sınıfın kodu YOK — illustrative design example.
 */
final class PlayerStateMachine
{
    public const IDLE    = 'IDLE';
    public const PLAYING = 'PLAYING';
    public const PAUSED  = 'PAUSED';
    public const SEEKING = 'SEEKING';
    public const ENDED   = 'ENDED';

    /** @var array<string, list<string>> */
    private const TRANSITIONS = [
        self::IDLE    => [self::PLAYING],
        self::PLAYING => [self::PAUSED, self::SEEKING, self::ENDED],
        self::PAUSED  => [self::PLAYING, self::SEEKING],
        self::SEEKING => [self::PLAYING, self::PAUSED],
        self::ENDED   => [self::IDLE, self::PLAYING], // playlist advance restarts
    ];

    private string $state = self::IDLE;

    public function state(): string
    {
        return $this->state;
    }

    public function transition(string $to): bool
    {
        if (!in_array($to, self::TRANSITIONS[$this->state] ?? [], true)) {
            return false; // fail-safe: illegal transition rejected
        }
        $this->state = $to;
        return true;
    }

    // Gapless seek entry point (master §22.2):
    // MP3 -> Xing/LAME header, FLAC -> SEEKTABLE, WAV -> linear byte offset.
    public function seek(float $positionSeconds): bool
    {
        if (!$this->transition(self::SEEKING)) {
            return false;
        }
        // resolveByteOffset(track, positionSeconds) — design placeholder
        return true;
    }
}
```

## §3 JavaScript

Player controller + parça bitince playlist ilerlemesi (master §9.5 "ileri").

```js
// K014-media/playback — player controller + playlist advance (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 (durum + pozisyon takibi).
// No repo code yet — illustrative design example.

const States = Object.freeze({
  IDLE: 'IDLE', PLAYING: 'PLAYING', PAUSED: 'PAUSED',
  SEEKING: 'SEEKING', ENDED: 'ENDED',
});

function createPlayer(playlist) {
  let state = States.IDLE;
  let index = 0;
  let positionSec = 0;

  const advance = () => {
    // Parça bitince sıradaki parça (master §9.5 "ileri").
    if (index + 1 < playlist.length) {
      index += 1;
      positionSec = 0;
      state = States.PLAYING;
      return playlist[index];
    }
    state = States.IDLE; // kuyruk bitti
    return null;
  };

  return {
    get state() { return state; },
    get position() { return positionSec; },
    play()  { if (state === States.IDLE || state === States.PAUSED) state = States.PLAYING; },
    pause() { if (state === States.PLAYING) state = States.PAUSED; },
    seek(sec) {
      if (state === States.PLAYING || state === States.PAUSED) {
        state = States.SEEKING;
        positionSec = Math.max(0, sec);
        state = States.PLAYING; // offset çözüldükten sonra
      }
    },
    onEnded: advance,
  };
}
```

## §4 TypeScript

Discriminated union ile tip güvenli durum geçişi.

```ts
// K014-media/playback — type-safe state machine (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 + §22.2 (Xing/LAME · SEEKTABLE).
// No repo code yet — illustrative design example.

export type PlayerState =
  | { tag: 'IDLE' }
  | { tag: 'PLAYING'; positionSec: number }
  | { tag: 'PAUSED'; positionSec: number }
  | { tag: 'SEEKING'; targetSec: number }
  | { tag: 'ENDED' };

export type PlayerEvent = 'PLAY' | 'PAUSE' | 'SEEK' | 'END' | 'RESUME';

export function transition(s: PlayerState, ev: PlayerEvent, positionSec = 0): PlayerState {
  switch (s.tag) {
    case 'IDLE':
      return ev === 'PLAY' ? { tag: 'PLAYING', positionSec: 0 } : s;
    case 'PLAYING':
      if (ev === 'PAUSE') return { tag: 'PAUSED', positionSec };
      if (ev === 'SEEK') return { tag: 'SEEKING', targetSec: positionSec };
      if (ev === 'END') return { tag: 'ENDED' };
      return s;
    case 'PAUSED':
      if (ev === 'RESUME' || ev === 'PLAY') return { tag: 'PLAYING', positionSec };
      if (ev === 'SEEK') return { tag: 'SEEKING', targetSec: positionSec };
      return s;
    case 'SEEKING':
      return ev === 'RESUME' ? { tag: 'PLAYING', positionSec: s.targetSec } : s;
    case 'ENDED':
      return ev === 'PLAY' ? { tag: 'PLAYING', positionSec: 0 } : s; // playlist advance
  }
}
```

## §5 C++

Realtime-safe atomic durum + FLAC `SEEKTABLE` seek-point karşılığı (RFC 9639 container — master §22.2).

```cpp
// K014-media/playback — realtime-safe player state (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 + §22.2 (gapless: Xing/LAME · FLAC SEEKTABLE).
// No repo code yet — illustrative design example.
#include <atomic>
#include <cstdint>

namespace cm::playback {

enum class PlayerState : std::uint8_t { Idle, Playing, Paused, Seeking, Ended };

// FLAC SEEKTABLE satir karsiligi (master §22.2).
struct SeekPoint {
    std::uint64_t sampleNumber;  // first sample of the seek point
    std::uint64_t byteOffset;    // offset from first frame
    std::uint16_t frameSamples;  // samples in this frame (0 = placeholder)
};

// Audio callback'te kullanilir: lock-free okuma, allocation YOK.
class Player {
public:
    void play()  noexcept { state_.store(PlayerState::Playing, std::memory_order_release); }
    void pause() noexcept { state_.store(PlayerState::Paused,  std::memory_order_release); }
    PlayerState state() const noexcept { return state_.load(std::memory_order_acquire); }

    // MP3: Xing/LAME header'dan frame offset; FLAC: SEEKTABLE binary search.
    // WAV: byteOffset = pcmOffset * bytesPerFrame (linear).
    std::uint64_t resolveByteOffset(std::uint64_t positionMs) const noexcept;

private:
    std::atomic<PlayerState> state_{PlayerState::Idle};
};

} // namespace cm::playback
```

## §6 C#

Record snapshot + izin tablosu + K007 kalıcılık seam'i (şema sahibi K007-data — master §2.2).

```csharp
// K014-media/playback — state machine + position persistence seam (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 (oynat/duraklat/ileri/geri · pozisyon takibi).
// No repo code yet — illustrative design example.
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;

namespace CoreMusic.Playback;

public enum PlayerStatus { Idle, Playing, Paused, Seeking, Ended }

public sealed record PlayerSnapshot(PlayerStatus Status, double PositionSeconds, int TrackId);

public interface IPositionStore // persistence seam — şema K007-data'ya ait (master §2.2)
{
    Task SaveAsync(PlayerSnapshot snapshot, CancellationToken ct = default);
}

public sealed class PlayerStateMachine
{
    private static readonly Dictionary<PlayerStatus, PlayerStatus[]> Allowed = new()
    {
        [PlayerStatus.Idle]    = new[] { PlayerStatus.Playing },
        [PlayerStatus.Playing] = new[] { PlayerStatus.Paused, PlayerStatus.Seeking, PlayerStatus.Ended },
        [PlayerStatus.Paused]  = new[] { PlayerStatus.Playing, PlayerStatus.Seeking },
        [PlayerStatus.Seeking] = new[] { PlayerStatus.Playing, PlayerStatus.Paused },
        [PlayerStatus.Ended]   = new[] { PlayerStatus.Idle, PlayerStatus.Playing }, // playlist advance
    };

    public PlayerSnapshot Current { get; private set; } = new(PlayerStatus.Idle, 0, 0);

    public bool TryTransition(PlayerStatus to, double positionSec)
    {
        if (!Allowed[Current.Status].Contains(to)) return false; // fail-safe
        Current = Current with { Status = to, PositionSeconds = Math.Max(0, positionSec) };
        return true;
    }
}
```

## §7 SQL

Pozisyon kalıcılığı şema taslağı — NOT: kalıcı şema K007-data'ya aittir (master §2.2); bu blok taslaktır.

```sql
-- K014-media/playback — pozisyon kalıcılığı şema taslağı (TASARIM ÖNERİSİ).
-- Grounded in media-master.md §9.5 (pozisyon takibi) + §2.2 (şema sahibi K007-data).
-- NOT: kalıcı şema K007-data'ya aittir — bu blok taslak/örnek niteliğindedir.

CREATE TABLE playback_position (
    track_id         BIGINT       NOT NULL,
    device_id        BIGINT       NOT NULL,
    position_seconds DECIMAL(10,3) NOT NULL DEFAULT 0,
    state            ENUM('IDLE','PLAYING','PAUSED','SEEKING','ENDED')
                     NOT NULL DEFAULT 'IDLE',
    updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                     ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (track_id, device_id),
    CONSTRAINT chk_position_nonneg CHECK (position_seconds >= 0)
) ENGINE=InnoDB;

-- Son dinlenen parçalar (kuyruk ilerlemesi için hızlı okuma)
CREATE INDEX idx_position_updated ON playback_position (device_id, updated_at DESC);
```

## §8 Go

Mutex-guarded player + playlist ilerleme.

```go
// K014-media/playback — mutex-guarded player + playlist advance (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 (oynat/duraklat · pozisyon takibi).
// No repo code yet — illustrative design example.
package playback

import "sync"

type State string

const (
	StateIdle    State = "IDLE"
	StatePlaying State = "PLAYING"
	StatePaused  State = "PAUSED"
	StateSeeking State = "SEEKING"
	StateEnded   State = "ENDED"
)

var allowed = map[State][]State{
	StateIdle:    {StatePlaying},
	StatePlaying: {StatePaused, StateSeeking, StateEnded},
	StatePaused:  {StatePlaying, StateSeeking},
	StateSeeking: {StatePlaying, StatePaused},
	StateEnded:   {StateIdle, StatePlaying}, // playlist advance
}

type Player struct {
	mu       sync.Mutex
	state    State
	position float64 // seconds — master §9.5
	tracks   []string
	index    int
}

func (p *Player) Transition(to State, pos float64) bool {
	p.mu.Lock()
	defer p.mu.Unlock()
	for _, s := range allowed[p.state] {
		if s == to {
			p.state, p.position = to, max(pos, 0)
			return true
		}
	}
	return false // fail-safe: illegal transition rejected
}
```

## §9 Rust

Result tabanlı geçiş — panic yok, illegal geçiş `Err` döner.

```rust
// K014-media/playback — Result-based transitions, no panic (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 + §22.2 (gapless seek: Xing/LAME · SEEKTABLE).
// No repo code yet — illustrative design example.

#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum PlayerState { Idle, Playing, Paused, Seeking, Ended }

#[derive(Debug, PartialEq, Eq)]
pub enum TransitionError { Illegal { from: PlayerState, to: PlayerState } }

pub struct Player {
    state: PlayerState,
    position_ms: u64,
}

#[derive(Clone, Copy)]
pub struct SeekPoint { pub sample_number: u64, pub byte_offset: u64 }

impl Player {
    pub fn transition(&mut self, to: PlayerState) -> Result<(), TransitionError> {
        let legal = matches!(
            (self.state, to),
            (PlayerState::Idle, PlayerState::Playing)
                | (PlayerState::Playing, PlayerState::Paused | PlayerState::Seeking | PlayerState::Ended)
                | (PlayerState::Paused, PlayerState::Playing | PlayerState::Seeking)
                | (PlayerState::Seeking, PlayerState::Playing | PlayerState::Paused)
                | (PlayerState::Ended, PlayerState::Idle | PlayerState::Playing) // playlist advance
        );
        if legal { self.state = to; Ok(()) }
        else { Err(TransitionError::Illegal { from: self.state, to }) }
    }

    // MP3: Xing/LAME header; FLAC: SEEKTABLE binary search; WAV: linear PCM math (master §22.2).
    pub fn byte_offset_for(&self, table: &[SeekPoint], position_ms: u64) -> Option<u64> {
        table.iter()
            .filter(|p| p.sample_number <= position_ms) // design simplification
            .max_by_key(|p| p.sample_number)
            .map(|p| p.byte_offset)
    }
}
```

## §10 Python

Dataclass tabanlı player + `on_ended` playlist ilerlemesi.

```python
# K014-media/playback — durum makinesi + pozisyon kaydı (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.5 (oynat/duraklat/ileri/geri · pozisyon takibi).
# Repo'da bu modülün kodu YOK — illustrative design example.

from dataclasses import dataclass, field

IDLE, PLAYING, PAUSED, SEEKING, ENDED = "IDLE", "PLAYING", "PAUSED", "SEEKING", "ENDED"

TRANSITIONS = {
    IDLE:    {PLAYING},
    PLAYING: {PAUSED, SEEKING, ENDED},
    PAUSED:  {PLAYING, SEEKING},
    SEEKING: {PLAYING, PAUSED},
    ENDED:   {IDLE, PLAYING},  # playlist advance
}

@dataclass
class Player:
    state: str = IDLE
    position_sec: float = 0.0
    playlist: list[str] = field(default_factory=list)
    index: int = 0

    def transition(self, to: str) -> bool:
        if to not in TRANSITIONS[self.state]:
            return False  # fail-safe
        self.state = to
        return True

    def on_ended(self) -> None:
        # Parça bitince sıradaki parça (master §9.5 "ileri").
        if self.index + 1 < len(self.playlist):
            self.index += 1
            self.position_sec = 0.0
            self.state = PLAYING
        else:
            self.state = IDLE
```

## §11 Java

EnumSet tabanlı geçiş tablosu.

```java
// K014-media/playback — EnumSet tabanlı geçiş tablosu (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 (durum + pozisyon takibi).
// No repo code yet — illustrative design example.
package com.coremusic.playback;

import java.util.EnumMap;
import java.util.EnumSet;
import java.util.Map;

public enum PlayerStatus {
    IDLE, PLAYING, PAUSED, SEEKING, ENDED;

    private static final Map<PlayerStatus, EnumSet<PlayerStatus>> ALLOWED =
            new EnumMap<>(PlayerStatus.class);

    static {
        ALLOWED.put(IDLE,    EnumSet.of(PLAYING));
        ALLOWED.put(PLAYING, EnumSet.of(PAUSED, SEEKING, ENDED));
        ALLOWED.put(PAUSED,  EnumSet.of(PLAYING, SEEKING));
        ALLOWED.put(SEEKING, EnumSet.of(PLAYING, PAUSED));
        ALLOWED.put(ENDED,   EnumSet.of(IDLE, PLAYING)); // playlist advance
    }

    public boolean canMoveTo(PlayerStatus to) {
        return ALLOWED.get(this).contains(to); // fail-safe: illegal transition rejected
    }
}
```

## §12 Swift

Value-type player + mutating geçişler + pozisyon kaydı seam'i.

```swift
// K014-media/playback — value-type player, mutating transitions (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 (oynat/duraklat · pozisyon takibi).
// No repo code yet — illustrative design example.
import Foundation

enum PlayerState: Equatable {
    case idle, playing(positionSec: Double), paused(positionSec: Double)
    case seeking(targetSec: Double), ended
}

struct Player {
    private(set) var state: PlayerState = .idle
    var onPositionSaved: ((Double) -> Void)? // K007-data persistence seam

    mutating func play() {
        switch state {
        case .idle, .paused, .ended:
            state = .playing(positionSec: currentPosition)
        default: break // fail-safe: illegal transition rejected
        }
    }

    mutating func pause() {
        if case .playing(let pos) = state { state = .paused(positionSec: pos) }
    }

    // MP3: Xing/LAME header; FLAC: SEEKTABLE (master §22.2).
    mutating func seek(to seconds: Double) {
        switch state {
        case .playing, .paused:
            let clamped = max(0, seconds)
            state = .playing(positionSec: clamped)
            onPositionSaved?(clamped)
        default: break
        }
    }

    var currentPosition: Double {
        switch state {
        case .playing(let p), .paused(let p): return p
        case .seeking(let t): return t
        default: return 0
        }
    }
}
```

## §13 Kotlin

Sealed class + saf `reduce` fonksiyonu (test edilebilir).

```kotlin
// K014-media/playback — sealed class + when reducer (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 + §22.2 (gapless seek).
// No repo code yet — illustrative design example.
package com.coremusic.playback

sealed class PlayerState {
    data object Idle : PlayerState()
    data class Playing(val positionSec: Double) : PlayerState()
    data class Paused(val positionSec: Double) : PlayerState()
    data class Seeking(val targetSec: Double) : PlayerState()
    data object Ended : PlayerState()
}

sealed class PlayerEvent {
    data object Play : PlayerEvent()
    data object Pause : PlayerEvent()
    data class Seek(val targetSec: Double) : PlayerEvent()
    data object End : PlayerEvent()
}

// Reducer: saf fonksiyon — yan etki yok, test edilebilir.
fun reduce(state: PlayerState, event: PlayerEvent): PlayerState = when (state) {
    is PlayerState.Idle -> if (event is PlayerEvent.Play) PlayerState.Playing(0.0) else state
    is PlayerState.Playing -> when (event) {
        PlayerEvent.Pause -> PlayerState.Paused(state.positionSec)
        is PlayerEvent.Seek -> PlayerState.Seeking(event.targetSec)
        PlayerEvent.End -> PlayerState.Ended // playlist advance tetikleyicisi
        else -> state
    }
    is PlayerState.Paused -> when (event) {
        PlayerEvent.Play -> PlayerState.Playing(state.positionSec)
        is PlayerEvent.Seek -> PlayerState.Seeking(event.targetSec)
        else -> state
    }
    is PlayerState.Seeking -> PlayerState.Playing(state.targetSec)
    is PlayerState.Ended -> if (event is PlayerEvent.Play) PlayerState.Playing(0.0) else state
}
```

## §14 Ruby

Hash tabanlı geçiş tablosu + `on_ended` ilerleme.

```ruby
# K014-media/playback — hash tabanlı geçiş tablosu (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.5 (oynat/duraklat · pozisyon takibi).
# Repo'da bu sınıfın kodu YOK — illustrative design example.

class Player
  STATES = %i[IDLE PLAYING PAUSED SEEKING ENDED].freeze

  TRANSITIONS = {
    IDLE:    %i[PLAYING],
    PLAYING: %i[PAUSED SEEKING ENDED],
    PAUSED:  %i[PLAYING SEEKING],
    SEEKING: %i[PLAYING PAUSED],
    ENDED:   %i[IDLE PLAYING] # playlist advance
  }.freeze

  attr_reader :state, :position_sec, :playlist, :index

  def initialize(playlist: [])
    @state = :IDLE
    @position_sec = 0.0
    @playlist = playlist
    @index = 0
  end

  def transition(to)
    return false unless TRANSITIONS[@state].include?(to) # fail-safe
    @state = to
    true
  end

  def on_ended
    if @index + 1 < @playlist.size
      @index += 1
      @position_sec = 0.0
      @state = :PLAYING
    else
      @state = :IDLE
    end
  end
end
```

## §15 Elixir

GenServer deseni — state tuple, `handle_call`/`handle_info` ile playlist ilerleme.

```elixir
# K014-media/playback — GenServer deseni (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.5 (oynat/duraklat · pozisyon takibi).
# Repo'da bu modülün kodu YOK — illustrative design example.

defmodule CoreMusic.Playback.Player do
  use GenServer

  @transitions %{
    IDLE: [:PLAYING],
    PLAYING: [:PAUSED, :SEEKING, :ENDED],
    PAUSED: [:PLAYING, :SEEKING],
    SEEKING: [:PLAYING, :PAUSED],
    ENDED: [:IDLE, :PLAYING] # playlist advance
  }

  def init(playlist), do: {:ok, %{state: :IDLE, position: 0.0, playlist: playlist, index: 0}}

  def handle_call({:transition, to}, _from, %{state: from} = s) do
    if to in Map.fetch!(@transitions, from) do
      {:reply, :ok, %{s | state: to}}
    else
      {:reply, {:error, {:illegal, from, to}}, s} # fail-safe
    end
  end

  # Parça bitince sıradaki parça (master §9.5 "ileri").
  def handle_info(:track_ended, %{playlist: pl, index: i} = s) do
    if i + 1 < length(pl) do
      {:noreply, %{s | index: i + 1, position: 0.0, state: :PLAYING}}
    else
      {:noreply, %{s | state: :IDLE}}
    end
  end
end
```

## §16 Scala

Sealed trait + `Either` tabanlı geçiş — hata tipi ayrılmıştır.

```scala
// K014-media/playback — sealed trait + Either tabanlı geçiş (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.5 + §22.2 (gapless seek: Xing/LAME · SEEKTABLE).
// No repo code yet — illustrative design example.
package com.coremusic.playback

sealed trait PlayerState
object PlayerState {
  case object Idle extends PlayerState
  case class Playing(positionSec: Double) extends PlayerState
  case class Paused(positionSec: Double) extends PlayerState
  case class Seeking(targetSec: Double) extends PlayerState
  case object Ended extends PlayerState
}

sealed trait TransitionError
case class IllegalTransition(from: PlayerState, to: PlayerState) extends TransitionError

object PlayerTransitions {
  import PlayerState._

  def transition(from: PlayerState, to: PlayerState): Either[TransitionError, PlayerState] = {
    val legal = (from, to) match {
      case (Idle, Playing(_)) => true
      case (Playing(_), Paused(_)) | (Playing(_), Seeking(_)) | (Playing(_), Ended) => true
      case (Paused(_), Playing(_)) | (Paused(_), Seeking(_)) => true
      case (Seeking(_), Playing(_)) | (Seeking(_), Paused(_)) => true
      case (Ended, Idle) | (Ended, Playing(_)) => true // playlist advance
      case _ => false
    }
    if (legal) Right(to) else Left(IllegalTransition(from, to))
  }
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | K014-media/media-master.md §9.5 | vault | playback rol/sınır/iddata |
| 2 | K014-media/media-master.md §22.1 | vault | codec seti MP3/FLAC/WAV (patent durumu doğrulandı) |
| 3 | K014-media/media-master.md §22.2 | vault | gapless/seek: Xing/LAME + FLAC `SEEKTABLE` + HTTP Range |
| 4 | K014-media/media-master.md §22.3 | vault | etiket şemaları (ID3v2.3/v2.4 · Vorbis Comment · LIST-INFO) |
| 5 | RFC 9639 (FLAC) | master §22.7 kaynak listesi | SEEKTABLE container |
| 6 | RFC 9110 (STD 97) | master §22.7 kaynak listesi | Range temeli (seek zinciri) |
| 7 | K014-media/playback/index.md | vault | bu modülün künyesi (§ROL/§KAPSAM/§BİLEŞENLER/§STANDARTLAR) |

> Tüm kod blokları TASARIM ÖNERİSİ illustrative design örnekleridir (kademe-2 design node —
> repo'da henüz kodu YOK); dayanak: media-master §9.5 + §22.

**Authority:** Bayram Ali / Vault Steward
