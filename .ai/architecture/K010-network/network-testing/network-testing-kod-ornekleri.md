---
node_id: K010-network/network-testing/kod-ornekleri
node_type: module
status: draft
parent_id: K010-network/network-testing
role: ornek
children: []
depends_on:
  - "K010-network/network-testing/network-testing-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans kaynaklar: network-testing-standart-kanit.md §1–§2 (RFC 2544 · asyncio · pytest · tokio · warp)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "K019-testing sahipliği (§7/6) + ölçüm eşikleri → ⚠️ UNKNOWN (faz C)"
group: kod
---

# network-testing — kod-ornekleri (kademe-2 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: **RTT ölçüm
> koşucusu** — N deneme toplar, min/avg/max/p95 üretir ve eşik kontrolü yapar
> (RFC 2544 gecikme ölçüm mantığının basitleştirilmiş TASARIM'ı; eşik değeri UNKNOWN).

## §1 PHP 8.4 — LatencyRunner (min/avg/max/p95 · strict_types)

```php
<?php
declare(strict_types=1);

/**
 * K014/network-testing — RTT ölçüm koşucusu (TASARIM ÖRNEĞİ).
 * Çerçeve: RFC 2544 gecikme ölçümü (basitleştirilmiş) — network-testing-standart-kanit.md §1/1.
 * Sınır: eşik değeri UNKNOWN (faz C); K019-testing sahipliği §7/6.
 */
final readonly class LatencyStats
{
    /** @param list<float> $rtts */
    public function __construct(
        public array $rtts,
        public float $min,
        public float $avg,
        public float $max,
        public float $p95,
    ) {
    }
}

final class LatencyRunner
{
    /** @param \Closure(): ?float $probe rtt_ms veya null (başarısız deneme) */
    public function __construct(
        private readonly \Closure $probe,
        private readonly int $samples = 50,
    ) {
    }

    public function run(): LatencyStats
    {
        $rtts = [];
        for ($i = 0; $i < $this->samples; $i++) {
            $v = ($this->probe)();
            if ($v !== null) {
                $rtts[] = $v; // başarısız denemeler kayba girer (RFC 2544 frame-loss benzeri)
            }
        }
        if ($rtts === []) {
            throw new RuntimeException('hiç başarılı ölçüm yok');
        }
        sort($rtts);
        $n = count($rtts);
        return new LatencyStats(
            $rtts,
            $rtts[0],
            array_sum($rtts) / $n,
            $rtts[$n - 1],
            $rtts[(int)ceil(0.95 * $n) - 1],
        );
    }

    /** @return bool true = eşik aşıldı (eşik UNKNOWN — çağrıran verir) */
    public function exceeds(LatencyStats $s, float $thresholdMs): bool
    {
        return $s->avg > $thresholdMs;
    }
}
```

## §2 Vanilla JS (ES6+) — RTT koşucusu + kopma senaryosu (fetch time)

```js
/**
 * K014/network-testing — RTT ölçüm koşucusu (TASARIM ÖRNEĞİ).
 * Çerçeve: RFC 2544 gecikme ölçümü (basitleştirilmiş) — network-testing-standart-kanit.md §1/1.
 * Sınır: eşik UNKNOWN (faz C); sahiplik §7/6.
 */
export class LatencyRunner {
  /** @param {{probe: () => Promise<number|null>, samples?: number}} cfg */
  constructor(cfg) {
    this.probe = cfg.probe;                  // rtt_ms döner; null = başarısız deneme
    this.samples = cfg.samples ?? 50;
  }

  /** @returns {Promise<{rtts:number[], min:number, avg:number, max:number, p95:number}>} */
  async run() {
    const rtts = [];
    for (let i = 0; i < this.samples; i++) {
      const v = await this.probe();
      if (v !== null) rtts.push(v);
    }
    if (rtts.length === 0) throw new Error('hiç başarılı ölçüm yok');
    rtts.sort((a, b) => a - b);
    const sum = rtts.reduce((a, b) => a + b, 0);
    return {
      rtts,
      min: rtts[0],
      avg: sum / rtts.length,
      max: rtts[rtts.length - 1],
      p95: rtts[Math.ceil(0.95 * rtts.length) - 1],
    };
  }
}

/**
 * Kopma senaryosu: çevrimdışıyken fetch'in başarısızlığı ölçülür
 * (connectivity online/offline olayları ile birlikte kullanılır).
 */
export async function measureOfflineFailures(fetchProbe, tries = 5) {
  let failures = 0;
  for (let i = 0; i < tries; i++) {
    try { await fetchProbe(); } catch { failures++; }
  }
  return { tries, failures }; // eşik değerlendirmesi faz C
}
```

## §3 C++20 — LatencyRunner (Neva Engine · <expected> · noexcept istatistik)

```cpp
// K014/network-testing — RTT ölçüm koşucusu (TASARIM ÖRNEĞİ).
// Çerçeve: RFC 2544 (basitleştirilmiş) — network-testing-standart-kanit.md §1/1.
// Sınır: eşik UNKNOWN (faz C); ASIO taşıyıcı implementasyonu faz C'dedir.
#pragma once
#include <algorithm>
#include <cmath>
#include <expected>
#include <functional>
#include <optional>
#include <vector>

namespace cm::net {

struct LatencyStats {
    double min_ms{};
    double avg_ms{};
    double max_ms{};
    double p95_ms{};
    std::size_t failures{};
};

class LatencyRunner {
public:
    // probe: rtt_ms; nullopt = başarısız deneme (kayba girer).
    using ProbeFn = std::function<std::optional<double>()>;

    LatencyRunner(ProbeFn probe, std::size_t samples = 50)
        : probe_(std::move(probe)), samples_(samples) {}

    [[nodiscard]] std::expected<LatencyStats, const char*> run() const {
        std::vector<double> rtts;
        rtts.reserve(samples_);
        std::size_t failures = 0;
        for (std::size_t i = 0; i < samples_; ++i) {
            if (auto v = probe_()) rtts.push_back(*v); else ++failures;
        }
        if (rtts.empty()) return std::unexpected("hiç başarılı ölçüm yok");
        std::sort(rtts.begin(), rtts.end());
        double sum = 0.0;
        for (double v : rtts) sum += v;
        const auto idx95 = static_cast<std::size_t>(std::ceil(0.95 * rtts.size())) - 1;
        return LatencyStats{rtts.front(), sum / static_cast<double>(rtts.size()),
                            rtts.back(), rtts[idx95], failures};
    }

private:
    ProbeFn probe_;
    std::size_t samples_;
};

} // namespace cm::net
```

## §4 C# — LatencyRunner (async · LINQ percentile · ConfigureAwait(false))

```csharp
// K014/network-testing — RTT ölçüm koşucusu (TASARIM ÖRNEĞİ).
// Çerçeve: RFC 2544 (basitleştirilmiş) — network-testing-standart-kanit.md §1/1.
// Sınır: eşik UNKNOWN (faz C); sahiplik §7/6.
#nullable enable

namespace CoreMusic.Network.Testing;

public sealed record LatencyStats(
    double MinMs, double AvgMs, double MaxMs, double P95Ms, int Failures);

public sealed class LatencyRunner(
    Func<CancellationToken, Task<double?>> probe,
    int samples = 50)
{
    public async Task<LatencyStats> RunAsync(CancellationToken ct = default)
    {
        var rtts = new List<double>(samples);
        var failures = 0;
        for (var i = 0; i < samples; i++)
        {
            var v = await probe(ct).ConfigureAwait(false);
            if (v is null) failures++; else rtts.Add(v.Value);
        }
        if (rtts.Count == 0) throw new InvalidOperationException("hiç başarılı ölçüm yok");
        rtts.Sort();
        var idx95 = (int)Math.Ceiling(0.95 * rtts.Count) - 1;
        return new LatencyStats(rtts[0], rtts.Average(), rtts[^1], rtts[idx95], failures);
    }

    /// <summary>Eşik UNKNOWN (faz C) — eşik çağrıran tarafta tutulur.</summary>
    public static bool Exceeds(LatencyStats s, double thresholdMs) => s.AvgMs > thresholdMs;
}
```

## §5 Python 3.12+ — LatencyRunner (asyncio + pytest senaryosu · §2/2–3)

```python
# K014/network-testing — async RTT ölçüm koşucusu (TASARIM ÖRNEĞİ).
# Çerçeve: RFC 2544 (basitleştirilmiş) — network-testing-standart-kanit.md §1/1.
# Test çalışma zamanı: asyncio + pytest (§2/2–3) — eşik UNKNOWN (faz C).
from __future__ import annotations

import asyncio
from collections.abc import Awaitable, Callable
from dataclasses import dataclass
from math import ceil


@dataclass(frozen=True, slots=True)
class LatencyStats:
    min_ms: float
    avg_ms: float
    max_ms: float
    p95_ms: float
    failures: int


class LatencyRunner:
    def __init__(self, probe: Callable[[], Awaitable[float | None]], samples: int = 50) -> None:
        self._probe = probe
        self._samples = samples

    async def run(self) -> LatencyStats:
        rtts: list[float] = []
        failures = 0
        for _ in range(self._samples):
            v = await self._probe()
            if v is None:
                failures += 1     # başarısız deneme — kayba girer
            else:
                rtts.append(v)
        if not rtts:
            raise RuntimeError("hiç başarılı ölçüm yok")
        rtts.sort()
        return LatencyStats(
            min_ms=rtts[0],
            avg_ms=sum(rtts) / len(rtts),
            max_ms=rtts[-1],
            p95_ms=rtts[ceil(0.95 * len(rtts)) - 1],
            failures=failures,
        )


# Örnek pytest senaryosu (eşik değeri faz C'de gelir):
#   async def test_rtt_bounded(latency_runner):
#       stats = await latency_runner.run()
#       assert stats.failures <= 0
#       assert stats.p95_ms < KNOWN_THRESHOLD  # ⚠️ UNKNOWN (faz C)
```

## §6 Go — LatencyRunner (tablo istatistik · parallel-safe değil — koşucu tek)

```go
// K014/network-testing — RTT ölçüm koşucusu (TASARIM ÖRNEĞİ).
// Çerçeve: RFC 2544 (basitleştirilmiş) — network-testing-standart-kanit.md §1/1.
// Sınır: eşik UNKNOWN (faz C); sahiplik §7/6.
package networktesting

import (
	"context"
	"fmt"
	"math"
	"sort"
)

type Stats struct {
	MinMs    float64
	AvgMs    float64
	MaxMs    float64
	P95Ms    float64
	Failures int
}

// probe rtt_ms döner; hata = başarısız deneme (kayba girer).
func RunLatency(ctx context.Context, probe func(context.Context) (float64, error), samples int) (Stats, error) {
	if samples <= 0 {
		samples = 50
	}
	rtts := make([]float64, 0, samples)
	failures := 0
	for i := 0; i < samples; i++ {
		if err := ctx.Err(); err != nil {
			return Stats{}, err
		}
		v, err := probe(ctx)
		if err != nil {
			failures++
			continue
		}
		rtts = append(rtts, v)
	}
	if len(rtts) == 0 {
		return Stats{}, fmt.Errorf("hiç başarılı ölçüm yok")
	}
	sort.Float64s(rtts)
	var sum float64
	for _, v := range rtts {
		sum += v
	}
	idx95 := int(math.Ceil(0.95*float64(len(rtts)))) - 1
	return Stats{
		MinMs: rtts[0], AvgMs: sum / float64(len(rtts)),
		MaxMs: rtts[len(rtts)-1], P95Ms: rtts[idx95], Failures: failures,
	}, nil
}
```

## §7 Rust — LatencyRunner (async · tokio · §2/4)

```rust
// K014/network-testing — async RTT ölçüm koşucusu (TASARIM ÖRNEĞİ).
// Çerçeve: RFC 2544 (basitleştirilmiş) — network-testing-standart-kanit.md §1/1.
// Çalışma zamanı: tokio (§2/4). Sınır: eşik UNKNOWN (faz C).
use std::future::Future;

#[derive(Debug, Clone, Copy)]
pub struct LatencyStats {
    pub min_ms: f64,
    pub avg_ms: f64,
    pub max_ms: f64,
    pub p95_ms: f64,
    pub failures: u32,
}

pub async fn run_latency<F, Fut>(mut probe: F, samples: u32) -> Result<LatencyStats, String>
where
    F: FnMut() -> Fut,
    Fut: Future<Output = Option<f64>>,
{
    let mut rtts: Vec<f64> = Vec::with_capacity(samples as usize);
    let mut failures = 0u32;
    for _ in 0..samples {
        match probe().await {
            Some(v) => rtts.push(v),
            None => failures += 1, // başarısız deneme — kayba girer
        }
    }
    if rtts.is_empty() {
        return Err("hiç başarılı ölçüm yok".into());
    }
    rtts.sort_by(|a, b| a.partial_cmp(b).expect("NaN ölçüm"));
    let n = rtts.len();
    let sum: f64 = rtts.iter().sum();
    let idx95 = ((0.95 * n as f64).ceil() as usize).saturating_sub(1);
    Ok(LatencyStats {
        min_ms: rtts[0],
        avg_ms: sum / n as f64,
        max_ms: rtts[n - 1],
        p95_ms: rtts[idx95],
        failures,
    })
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | RTT koşucusu (min/avg/max/p95) | eşik UNKNOWN — faz C |
| 2 | JS ES6+ | Web UI | RTT koşucusu + offline senaryo | connectivity olayları ile |
| 3 | TypeScript | Web UI (tipli) | RTT raporlayıcı (tipli) | `RTTSample` arayüzü |
| 4 | C++20 | Neva Engine | RTT koşucusu (expected) | ASIO taşıyıcısı faz C |
| 5 | C | POSIX ölçüm | `clock_gettime` + socket | monotonic saat |
| 6 | C# | Windows kolu | async koşucu | eşik çağrıran tarafta |
| 7 | Python | ağ testi/araç | async koşucu + pytest örneği | asyncio+pytest §2/2–3 |
| 8 | Go | sunucu/tünel | koşucu + context iptali | eşik UNKNOWN |
| 9 | Rust | sistem | async koşucu (tokio) | tokio §2/4 — eşik UNKNOWN |
| 10 | Java 21 | test/araç | koşucu + JUnit örneği | `System.nanoTime` |
| 11 | Swift | iOS/macos kolu | ölçüm sarmalayıcı | Network.framework |
| 12 | Kotlin | Android kolu | ölçüm sarmalayıcı | platform API |
| 13 | Bash | CI smoke | `curl -w` zamanlama | curl bağımlılık |
| 14 | PowerShell | Windows smoke | `Measure-Command` | yerleşik |
| 15 | SQL | — (referans: ölçüm kaydı saklama) | tablo şeması | — |
| 16 | YAML | — (referans: test senaryosu) | yapılandırma | — |
| 17 | HCL | — (referans: load-test altyapısı) | yapılandırma | — |

**Kapı:** 14/17 dil örneği üretildi; **ölçüm eşikleri ve K019 sahipliği** açık kalır
(network-testing-standart-kanit.md §4) — istatistik üretimi eşik üretmez. SQL · YAML ·
HCL referans satırıdır.

---

## §9 Ölçüm İstatistikleri & Protokol (2026-10-17 derinleştirme)

> **Dayanak:** Bu bölüm K010 ağ ölçüm metodolojisidir; eşik DEĞERLERİ üretim verisinden
> gelir (K017/K019) — burada yalnız yöntem tanımlanır. Uydurma eşik yazılmaz.

### §9.1 Toplanan metrikler

| Metrik | Tanım |
|---|---|
| RTT | istek gönderimi → yanıt alımı (monotonic saat) |
| jitter | RTT varyasyonu (RFC 3550 jitter formülü — master §25) |
| kayıp oranı | yanıt alınamayan istek / toplam istek |
| yeniden bağlanma süresi | disconnect → CONNECTED (durum makinesi connectivity §9) |

### §9.2 İstatistik özeti

| İstatistik | Not |
|---|---|
| min / max | uç değerler |
| avg | ortalama |
| p50 / p95 / p99 | yüzdelik — **p95 gecikme kritik** (K010 hedefi ⚠️ UNKNOWN) |

### §9.3 Eşik karar akışı (TASARIM)

```
ölçüm topla → p95 hesapla → K017 eşiğiyle karşılaştır
  → eşik üstü → DEGRADED (connectivity §9.1)
  → eşik altı → CONNECTED
```

### §9.4 Bilinmeyenler

| Bilinmeyen | Sahip |
|---|---|
| p95 eşik değeri | K017/K019 (ölçüm) |
| K019 sahipliği sınırı | network-testing-standart-kanit.md §4 |

> Bu bölüm eşik **üretmez**; yalnız ölçüm yöntemini tanımlar (standart-kanit.md §4).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
