---
node_id: K014-network/multi-room/kod-ornekleri
node_type: module
status: draft
parent_id: K014-network/multi-room
role: ornek
children: []
depends_on:
  - "K014-network/multi-room/multi-room-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans standartlar: multi-room-standart-kanit.md §1–§2 (RFC 5905 NTP · RFC 3550 · Performance.now)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "Zaman hizalama yöntemi (§7/8) + tolerans eşiği → ⚠️ UNKNOWN (faz C)"
group: kod
---

# multi-room — kod-ornekleri (kademe-2 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: **NTP tarzı offset
> tahmini** — 4 damgalı ping/pong ile `offset = ((t2−t1) + (t3−t4)) / 2`, en düşük
> RTT numunesi tercih edilir (RFC 5905 modelinden basitleştirilmiş TASARIM; gerçek
> NTP algoritması/kütüphanesi seçimi §7/8'de faz C'dedir). Zaman birimi: milisaniye.

## §1 PHP 8.4 — ClockOffsetEstimator (min-RTT numunesi · strict_types)

```php
<?php
declare(strict_types=1);

/**
 * K014/multi-room — NTP tarzı offset tahmini (TASARIM ÖRNEĞİ).
 * Model: RFC 5905 offset denklemi (basitleştirilmiş) — multi-room-standart-kanit.md §1/1.
 * Sınır: yöntem §7/8'de seçilmedi; bu sınıf yalnız tahmin mekaniği.
 */
final class ClockOffsetEstimator
{
    /** @var array{offset: float, rtt: float}|null */
    private ?array $best = null; // min-RTT numunesi tercih edilir

    /**
     * 4 damga: t1 (istemci gönderi), t2 (sunucu alım), t3 (sunucu gönderi), t4 (istemci alım).
     *
     * @return float milisaniye cinsinden offset (sunucu saati − istemci saati)
     */
    public function sample(float $t1, float $t2, float $t3, float $t4): float
    {
        $offset = (($t2 - $t1) + ($t3 - $t4)) / 2.0;
        $rtt = ($t4 - $t1) - ($t3 - $t2);
        if ($this->best === null || $rtt < $this->best['rtt']) {
            $this->best = ['offset' => $offset, 'rtt' => $rtt];
        }
        return $offset;
    }

    /** En iyi (min-RTT) offset; numune yoksa null. */
    public function bestOffset(): ?float
    {
        return $this->best['offset'] ?? null;
    }

    public function bestRtt(): ?float
    {
        return $this->best['rtt'] ?? null;
    }
}
```

## §2 Vanilla JS (ES6+) — Performance.now() ile offset tahmini

```js
/**
 * K014/multi-room — tarayıcı offset tahmini (TASARIM ÖRNEĞİ).
 * Zaman kaynağı: performance.now() (monotonik — multi-room-standart-kanit.md §1/2);
 * RFC 5905 offset denklemi basitleştirilmiş (§1/1). Yöntem seçimi §7/8 faz C.
 */
export class ClockOffsetEstimator {
  #best = null; // {offset, rtt} — min-RTT numunesi

  /**
   * WS üzerinden tek bir ping/pong ile 4 damga toplayan örnekleme.
   * @param {(msg: object) => Promise<object>} rpc sunucuya round-trip (comms/websocket)
   */
  async sampleVia(rpc) {
    const t1 = performance.now();
    const res = await rpc({ cmd: 'time-pong', t1 }); // sunucu t2/t3'ü gömer
    const t4 = performance.now();
    const { t2, t3 } = res;
    const offset = ((t2 - t1) + (t3 - t4)) / 2;
    const rtt = (t4 - t1) - (t3 - t2);
    if (!this.#best || rtt < this.#best.rtt) this.#best = { offset, rtt };
    return offset;
  }

  bestOffset() { return this.#best?.offset ?? null; }
  bestRtt() { return this.#best?.rtt ?? null; }
}
```

## §3 C++20 — ClockOffsetEstimator (Neva Engine · chrono · noexcept okuma)

```cpp
// K014/multi-room — NTP tarzı offset tahmini (TASARIM ÖRNEĞİ).
// Model: RFC 5905 (basitleştirilmiş) — multi-room-standart-kanit.md §1/1.
// Sınır: yöntem seçimi §7/8 faz C; ses üretim senkronu K003 sınırındadır.
#pragma once
#include <optional>

namespace cm::net {

class ClockOffsetEstimator {
public:
    struct Sample {
        double offset_ms; // sunucu − istemci
        double rtt_ms;
    };

    // 4 damga (milisaniye): t1 istemci gönderi · t2 sunucu alım · t3 sunucu gönderi · t4 istemci alım.
    [[nodiscard]] double sample(double t1, double t2, double t3, double t4) {
        const double offset = ((t2 - t1) + (t3 - t4)) / 2.0;
        const double rtt = (t4 - t1) - (t3 - t2);
        if (!best_ || rtt < best_->rtt_ms)
            best_ = Sample{offset, rtt};
        return offset;
    }

    [[nodiscard]] std::optional<double> best_offset() const noexcept {
        if (!best_) return std::nullopt;
        return best_->offset_ms;
    }

    [[nodiscard]] std::optional<double> best_rtt() const noexcept {
        if (!best_) return std::nullopt;
        return best_->rtt_ms;
    }

private:
    std::optional<Sample> best_; // min-RTT numunesi tercih edilir
};

} // namespace cm::net
```

## §4 C# — ClockOffsetEstimator (record · nullable · ConfigureAwait(false))

```csharp
// K014/multi-room — NTP tarzı offset tahmini (TASARIM ÖRNEĞİ).
// Model: RFC 5905 (basitleştirilmiş) — multi-room-standart-kanit.md §1/1.
// Sınır: yöntem seçimi §7/8 faz C.
#nullable enable

namespace CoreMusic.Network.MultiRoom;

public sealed record OffsetSample(double OffsetMs, double RttMs);

public sealed class ClockOffsetEstimator
{
    private OffsetSample? _best; // min-RTT numunesi

    /// <summary>4 damga (ms): t1 istemci gönderi · t2 sunucu alım · t3 sunucu gönderi · t4 istemci alım.</summary>
    public double Sample(double t1, double t2, double t3, double t4)
    {
        var offset = ((t2 - t1) + (t3 - t4)) / 2.0;
        var rtt = (t4 - t1) - (t3 - t2);
        if (_best is null || rtt < _best.RttMs)
            _best = new OffsetSample(offset, rtt);
        return offset;
    }

    public double? BestOffset() => _best?.OffsetMs;
    public double? BestRtt() => _best?.RttMs;
}
```

## §5 Python 3.12+ — ClockOffsetEstimator (dataclass · min-RTT)

```python
# K014/multi-room — NTP tarzı offset tahmini (TASARIM ÖRNEĞİ).
# Model: RFC 5905 (basitleştirilmiş) — multi-room-standart-kanit.md §1/1.
# Sınır: yöntem seçimi §7/8 faz C; ses senkronu K003 sınırında.
from __future__ import annotations

from dataclasses import dataclass


@dataclass(frozen=True, slots=True)
class OffsetSample:
    offset_ms: float
    rtt_ms: float


class ClockOffsetEstimator:
    def __init__(self) -> None:
        self._best: OffsetSample | None = None   # min-RTT numunesi

    def sample(self, t1: float, t2: float, t3: float, t4: float) -> float:
        """4 damga (ms): t1 istemci gönderi · t2 sunucu alım · t3 sunucu gönderi · t4 istemci alım."""
        offset = ((t2 - t1) + (t3 - t4)) / 2.0
        rtt = (t4 - t1) - (t3 - t2)
        if self._best is None or rtt < self._best.rtt_ms:
            self._best = OffsetSample(offset, rtt)
        return offset

    def best_offset(self) -> float | None:
        return self._best.offset_ms if self._best else None

    def best_rtt(self) -> float | None:
        return self._best.rtt_ms if self._best else None
```

## §6 Go — ClockOffsetEstimator (mutex · min-RTT)

```go
// K014/multi-room — NTP tarzı offset tahmini (TASARIM ÖRNEĞİ).
// Model: RFC 5905 (basitleştirilmiş) — multi-room-standart-kanit.md §1/1.
// Sınır: yöntem seçimi §7/8 faz C; ses senkronu K003 sınırında.
package multiroom

import "sync"

type Sample struct {
	OffsetMs float64
	RttMs    float64
}

type Estimator struct {
	mu   sync.Mutex
	best *Sample // min-RTT numunesi tercih edilir
}

// Sample 4 damga (ms): t1 istemci gönderi · t2 sunucu alım · t3 sunucu gönderi · t4 istemci alım.
func (e *Estimator) Sample(t1, t2, t3, t4 float64) float64 {
	offset := ((t2 - t1) + (t3 - t4)) / 2.0
	rtt := (t4 - t1) - (t3 - t2)
	e.mu.Lock()
	defer e.mu.Unlock()
	if e.best == nil || rtt < e.best.RttMs {
		e.best = &Sample{OffsetMs: offset, RttMs: rtt}
	}
	return offset
}

func (e *Estimator) BestOffset() (float64, bool) {
	e.mu.Lock()
	defer e.mu.Unlock()
	if e.best == nil {
		return 0, false
	}
	return e.best.OffsetMs, true
}

func (e *Estimator) BestRtt() (float64, bool) {
	e.mu.Lock()
	defer e.mu.Unlock()
	if e.best == nil {
		return 0, false
	}
	return e.best.RttMs, true
}
```

## §7 Rust — ClockOffsetEstimator (Mutex<Option<Sample>> · min-RTT)

```rust
// K014/multi-room — NTP tarzı offset tahmini (TASARIM ÖRNEĞİ).
// Model: RFC 5905 (basitleştirilmiş) — multi-room-standart-kanit.md §1/1.
// Sınır: yöntem seçimi §7/8 faz C; ses senkronu K003 sınırında.
use std::sync::Mutex;

#[derive(Clone, Copy, Debug)]
pub struct OffsetSample {
    pub offset_ms: f64,
    pub rtt_ms: f64,
}

#[derive(Default)]
pub struct ClockOffsetEstimator {
    best: Mutex<Option<OffsetSample>>, // min-RTT numunesi
}

impl ClockOffsetEstimator {
    /// 4 damga (ms): t1 istemci gönderi · t2 sunucu alım · t3 sunucu gönderi · t4 istemci alım.
    pub fn sample(&self, t1: f64, t2: f64, t3: f64, t4: f64) -> f64 {
        let offset = ((t2 - t1) + (t3 - t4)) / 2.0;
        let rtt = (t4 - t1) - (t3 - t2);
        let mut best = self.best.lock().expect("kilit zehirlenmesi");
        if best.map_or(true, |b| rtt < b.rtt_ms) {
            *best = Some(OffsetSample { offset_ms: offset, rtt_ms: rtt });
        }
        offset
    }

    pub fn best_offset(&self) -> Option<f64> {
        self.best.lock().expect("kilit zehirlenmesi").map(|b| b.offset_ms)
    }

    pub fn best_rtt(&self) -> Option<f64> {
        self.best.lock().expect("kilit zehirlenmesi").map(|b| b.rtt_ms)
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | offset tahmini (min-RTT) | NTP kütüphanesi değil — basitleştirme |
| 2 | JS ES6+ | Web UI | performance.now() + WS round-trip | monotonik saat §1/2 |
| 3 | C++20 | Neva Engine | offset tahmini | gerçek NTP/PPH seçimi §7/8 |
| 4 | C# | Windows kolu | offset tahmini | yöntem seçimi §7/8 |
| 5 | Python | ağ testi/araç | offset tahmini | ölçüm aracı — test network-testing'te |
| 6 | Go | sunucu/tünel | offset tahmini | sunucu tarafı t2/t3 kaynağı |
| 7 | Rust | sistem | offset tahmini | yöntem seçimi §7/8 |

**Kapı:** 7/7 dil örneği üretildi; bu bir **NTP algoritması değil**, RFC 5905 offset
denkleminin basitleştirilmiş tasarımdır — yöntem ve tolerans §7/8'de açık kalır
(multi-room-standart-kanit.md §4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
