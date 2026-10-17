---
title: "K016-observability/performance — Profil ve Ölçüm"
node_id: K016-observability/performance/performance-profil-ve-olcum
node_type: subdomain
status: draft
parent_id: K016-observability/performance
children: []
depends_on:
  - K016-observability
  - K014-media
evidence:
  - "# performance/profil için disk kanıtı YOK (tasarım dosyası)"
validation:
  - "Bu turda performance için disk kanıtı YOK — tasarım/PLANNED (⚠️ VERIFICATION REQUIRED)"
open_questions:
  - "Core Web Vitals hedefleri (LCP/INP/CLS) ölçüm düzeneği kurulu mu?"
group: mimari
---


# K016-observability/performance — performance-profil-ve-olcum

## §1 Rol (ELI15)

Sistem neden yavaş? Sorusunun cevabı **ölçümle** bulunur, tahminle değil. Profil, hangi kod parçasının zaman/yığın harcadığını gösterir. Web tarafında **Core Web Vitals** (LCP/INP/CLS) kullanıcı hissini ölçer. Audio tarafında **xrun/underrun** ölçüldüğünde gerçek zamanlı bozulma görülür. Kural: ölç → darboğazı bul → tek seferde değiştir → yeniden ölç.

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Profil ölçüm tekniği | ✅ hotspot, örnekleme | ❌ Kod optimizasyonu → ilgili domain |
| Core Web Vitals | ✅ LCP/INP/CLS tanımı | ❌ Frontend kodu → K011-application |
| p95/p99 gecikme | ✅ persentil hesabı | ❌ Metrik üretimi → metrics/ |
| Audio gecikme | ✅ buffer/latency ölçümü | ❌ DSP düzeltmesi → K003/K004 |
| Darboğaz kararı | ✅ ölçüm yorumu | ❌ Değişiklik uygulaması → ilgili sahip |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Profil | profile | Kodun zaman/yığın dağılımı | hotspot tablosu |
| Hotspot | hotspot | Sıcak nokta (en çok zaman) | §4 |
| Persentil | percentile | Dağılım özeti | p95, p99 |
| LCP | Largest Contentful Paint | En büyük içerik boyama | Core Web Vitals |
| INP | Interaction to Next Paint | Etkileşim gecikmesi | Core Web Vitals |
| CLS | Cumulative Layout Shift | Yerleşim kayması | Core Web Vitals |
| Xrun | xrun | Buffer taşması (audio) | audio-diagnostics/ |
| Underrun | underrun | Veri yetmezliği | audio-diagnostics/ |
| Darboğaz | bottleneck | Sınırlayıcı adım | §6 |
| Warm-up | warm-up | Isınma (ilk ölçüm yanlısı) | §5 |

## §4 Profil ölçüm teknikleri

| # | Teknik | Ne ölçer | Ne zaman | Maliyet |
|---|---|---|---|---|
| 1 | Wall-clock profil | Gerçek geçen zaman | üretim benzeri | orta |
| 2 | CPU profil | İşlemci kullanımı | darboğaz | orta |
| 3 | Bellek profil | Tahsis/çöp | sızıntı şüphesi | yüksek |
| 4 | I/O profil | Disk/ağ bekleme | yavaş I/O | düşük |
| 5 | Örnekleme | Periyodik stack örneği | üretim | düşük |
| 6 | Enstrümasyon | Belirli satır sayacı | hedefli | düşük |

**Kural:** Önce örnekleme (düşük maliyet) → darboğaz bul → sonra enstrümasyonla derinleş.

## §5 Ölçüm geçerlilik kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Warm-up atla (ilk ölçümü yoksay) | JIT/cache ısınması |
| 2 | En az 30 örnek al | tek ölçüm gürültülü |
| 3 | p50/p95/p99 ver (ortalama DEĞİL) | ortalama uçları gizler |
| 4 | Aynı ortamda ölç | ortam farkı yanıltır |
| 5 | Karşılaştırmayı aynı makinede yap | donanım farkı yanıltır |
| 6 | Regresyon eşiği belirle | %10 üstü = anlamlı |

## §6 Persentil hesabı ve yorumu

### §6.1 p95/p99 somut örnek

| # | İstek | Gecikme (ms) | p50 | p95 | p99 | Yorum |
|---|---|---|---|---|---|---|
| 1 | sayfa | 40, 45, 50, 55, 60, 200 | 52 | 200 | 200 | kuyruk var |
| 2 | API | 30, 32, 35, 36, 38, 40 | 33 | 40 | 40 | düzgün |
| 3 | media | 10, 12, 14, 15, 300, 320 | 13 | 320 | 320 | xrun riski |

**Yorum:** Ortalama yanıltır (örnek 1 ortalaması ~75ms, p95=200ms). Kuyruk (örnek 1, 3) gerçek kullanıcı hissini bozar.

### §6.3 Ölçüm raporu örneği (somut)

| # | Servis | p50 (ms) | p95 (ms) | p99 (ms) | Hedef p95 | Durum |
|---|---|---|---|---|---|---|
| 1 | web (SSR) | 120 | 480 | 720 | 500 | ✅ eşiğin altında |
| 2 | api/playback | 45 | 380 | 900 | 500 | ⚠️ p99 eşiği aşıyor |
| 3 | media/decode | 12 | 95 | 140 | 200 | ✅ uygun |
| 4 | health endpoint | 3 | 18 | 30 | 100 | ✅ uygun |
| 5 | auth | 60 | 410 | 650 | 500 | ⚠️ izlenmeli |

**Karar:** Durum 2 ve 5'te p99 > 700ms → kuyruk analizi; alerts/ §4.1 eşiklerine (p95 > 500ms → ticket) bağlanır.

### §6.4 Regresyon değerlendirme eşiği

| # | Değişim | Karar | Aksiyon |
|---|---|---|---|
| 1 | p95 < %10 değişim | gürültü | aksiyon yok |
| 2 | p95 %10-25 artış | şüpheli | tekrar ölç |
| 3 | p95 > %25 artış | regresyon | K019 test tetikle |
| 4 | p95 > %50 artış | kritik | alerts/ page eşiği |

### §6.2 Persentil kaydırma tablosu (SQL)

```sql
-- TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
-- K016/performance — gecikme persentilleri (mysql 8 window/percentile).
SELECT
  service_name,
  COUNT(*)                                            AS samples,
  ROUND(PERCENTILE_CONT(0.50) WITHIN GROUP (ORDER BY latency_ms), 1) AS p50,
  ROUND(PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY latency_ms), 1) AS p95,
  ROUND(PERCENTILE_CONT(0.99) WITHIN GROUP (ORDER BY latency_ms), 1) AS p99,
  MAX(latency_ms)                                     AS max_ms
FROM request_latency
WHERE observed_at >= NOW() - INTERVAL 1 HOUR
GROUP BY service_name
ORDER BY p95 DESC;
```

## §7 ÇOK DİLLİ KOD ÖRNEKLERİ

> **TASARIM ÖRNEĞİ (illüstratif)** — bloklar repo'da karşılığı OLMAYAN, profil/ölçüm desenini gösteren tasarım örnekleridir. Durum: **PLANNED**.

### §7.1 Python — timeit + persentil

```python
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/performance — profil ölçümü + persentil hesabı.
from __future__ import annotations

import statistics
import time


def measure(fn, repeats: int = 30) -> list[float]:
    """Warm-up + çoklu ölçüm (ms cinsinden)."""
    fn()  # warm-up (ilk ölçüm yoksayılır)
    samples: list[float] = []
    for _ in range(repeats):
        t0 = time.perf_counter()
        fn()
        samples.append((time.perf_counter() - t0) * 1000)
    return samples


def percentile(samples: list[float], p: float) -> float:
    """p95 gibi persentil (yakalama yöntemi)."""
    ordered = sorted(samples)
    k = (len(ordered) - 1) * (p / 100)
    lo, hi = int(k), min(int(k) + 1, len(ordered) - 1)
    return ordered[lo] + (ordered[hi] - ordered[lo]) * (k - lo)


def report(name: str, samples: list[float]) -> str:
    return (
        f"{name}: p50={percentile(samples, 50):.1f}ms "
        f"p95={percentile(samples, 95):.1f}ms "
        f"p99={percentile(samples, 99):.1f}ms "
        f"max={max(samples):.1f}ms"
    )
```

### §7.2 Go — pprof + bench

```go
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/performance — net/http/pprof ile profil uçları.
package main

import (
	"net/http"
	_ "net/http/pprof"
	"time"
)

func main() {
	// /debug/pprof/ profol uçları (gözlem amaçlı, üretimde kısıtlı)
	go func() {
		_ = http.ListenAndServe("127.0.0.1:6060", nil)
	}()

	start := time.Now()
	_ = doWork()
	elapsed := time.Since(start)
	_ = elapsed // p95 hesabı metrics/ katmanında
}

func doWork() error { return nil }
```

### §7.3 JavaScript/TS — performance.now() + Web Vitals

```typescript
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/performance — performans ölçümü + LCP/INP/CLS gönderimi.
interface Vital {
  name: "LCP" | "INP" | "CLS";
  value: number;
  id: string;
}

export function measureLcp(): void {
  new PerformanceObserver((list) => {
    const entries = list.getEntries();
    const last = entries[entries.length - 1] as PerformanceEntry;
    report({ name: "LCP", value: last.startTime, id: crypto.randomUUID() });
  }).observe({ type: "largest-contentful-paint", buffered: true });
}

export function measureCls(): void {
  let cls = 0;
  new PerformanceObserver((list) => {
    for (const entry of list.getEntries() as Array<LayoutShift>) {
      if (!entry.hadRecentInput) cls += entry.value;
    }
    report({ name: "CLS", value: cls, id: crypto.randomUUID() });
  }).observe({ type: "layout-shift", buffered: true });
}

function report(vital: Vital): void {
  // gözlem katmanına gönderim (K016/metrics)
  void fetch("/vitals", {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify(vital),
  });
}
```

### §7.4 C# — Stopwatch + persentil

```csharp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/performance — Stopwatch profil ölçümü + p95.
#nullable enable
using System.Diagnostics;

namespace CoreMusic.Observability.Performance;

public sealed class Profiler
{
    private readonly List<double> _samples = new();

    public double Measure(Action action, int repeats = 30)
    {
        action(); // warm-up
        _samples.Clear();
        var sw = new Stopwatch();
        for (var i = 0; i < repeats; i++)
        {
            sw.Restart();
            action();
            sw.Stop();
            _samples.Add(sw.Elapsed.TotalMilliseconds);
        }
        return Percentile(_samples, 95);
    }

    public double Percentile(double p)
        => Percentile(_samples, p);

    private static double Percentile(List<double> data, double p)
    {
        var sorted = data.OrderBy(x => x).ToList();
        var k = (sorted.Count - 1) * (p / 100.0);
        var lo = (int)Math.Floor(k);
        var hi = Math.Min(lo + 1, sorted.Count - 1);
        return sorted[lo] + (sorted[hi] - sorted[lo]) * (k - lo);
    }
}
```

### §7.5 Rust — Instant + örnekleme

```rust
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/performance — std::time::Instant ile ölçüm (realtime callback dışında).
use std::time::Instant;

pub struct Samples {
    values: Vec<f64>,
}

impl Samples {
    pub fn new() -> Self {
        Self { values: Vec::with_capacity(64) }
    }

    pub fn measure<F: FnMut()>(&mut self, mut f: F) {
        f(); // warm-up
        let t0 = Instant::now();
        f();
        self.values.push(t0.elapsed().as_secs_f64() * 1000.0);
    }

    pub fn p95(&self) -> f64 {
        let mut sorted = self.values.clone();
        sorted.sort_by(|a, b| a.partial_cmp(b).unwrap());
        let idx = ((sorted.len() - 1) as f64 * 0.95) as usize;
        sorted[idx]
    }
}
```

### §7.6 C++ — chrono profil

```cpp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/performance — std::chrono profil ölçümü (realtime callback dışında).
#pragma once
#include <chrono>
#include <vector>
#include <algorithm>

namespace cm::obs {

class Profiler {
public:
    template <typename Fn>
    double measure(Fn&& fn, int repeats = 30) {
        fn(); // warm-up
        samples_.clear();
        for (int i = 0; i < repeats; ++i) {
            auto t0 = std::chrono::steady_clock::now();
            fn();
            auto t1 = std::chrono::steady_clock::now();
            samples_.push_back(
                std::chrono::duration<double, std::milli>(t1 - t0).count());
        }
        return percentile(95.0);
    }

    double percentile(double p) const {
        auto v = samples_;
        std::sort(v.begin(), v.end());
        auto idx = static_cast<size_t>((v.size() - 1) * (p / 100.0));
        return v[idx];
    }

private:
    std::vector<double> samples_;
};

} // namespace cm::obs
```

### §7.7 PHP — microtime + opcache durumu

```php
<?php
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/performance — PHP profil ölçümü + opcache durumu.
declare(strict_types=1);

namespace CoreMusic\Observability\Performance;

final class Profiler
{
    /** @var list<float> */
    private array $samples = [];

    public function measure(callable $fn, int $repeats = 30): float
    {
        $fn(); // warm-up
        $this->samples = [];
        for ($i = 0; $i < $repeats; $i++) {
            $t0 = hrtime(true);
            $fn();
            $this->samples[] = (hrtime(true) - $t0) / 1e6; // ms
        }
        return $this->percentile(95.0);
    }

    public function percentile(float $p): float
    {
        $sorted = $this->samples;
        sort($sorted);
        $k = (count($sorted) - 1) * ($p / 100);
        $lo = (int) floor($k);
        $hi = min($lo + 1, count($sorted) - 1);
        return $sorted[$lo] + ($sorted[$hi] - $sorted[$lo]) * ($k - $lo);
    }

    /** opcache durumu (gözlem için). */
    public function opcacheStatus(): array
    {
        if (!function_exists('opcache_get_status')) {
            return ['enabled' => false];
        }
        $s = opcache_get_status(false);
        return [
            'enabled'   => (bool) ($s['opcache_enabled'] ?? false),
            'hits'      => (int) ($s['opcache_statistics']['hits'] ?? 0),
            'misses'    => (int) ($s['opcache_statistics']['misses'] ?? 0),
        ];
    }
}
```

### §7.8 Java — JFR / System.nanoTime

```java
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/performance — nanoTime ölçüm + persentil.
package com.coremusic.perf;

import java.util.Arrays;
import java.util.List;
import java.util.ArrayList;

public final class Profiler {
    private final List<Double> samples = new ArrayList<>();

    public double measure(Runnable fn, int repeats) {
        fn.run(); // warm-up
        samples.clear();
        for (int i = 0; i < repeats; i++) {
            long t0 = System.nanoTime();
            fn.run();
            samples.add((System.nanoTime() - t0) / 1_000_000.0);
        }
        return percentile(95.0);
    }

    public double percentile(double p) {
        double[] sorted = samples.stream().mapToDouble(Double::doubleValue).sorted().toArray();
        int k = (int) ((sorted.length - 1) * (p / 100.0));
        return sorted[k];
    }
}
```

### §7.9 YAML — Kubernetes resource göstergeleri

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/performance — K8s resource request/limit (ölçüm karşılaştırma hedefi).
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-web
spec:
  template:
    spec:
      containers:
        - name: web
          image: coremusic/web:v1.4.2
          resources:
            requests:
              cpu: 250m
              memory: 256Mi
            limits:
              cpu: 1000m
              memory: 512Mi
          env:
            - name: OTEL_RESOURCE_ATTRIBUTES
              value: "service.name=coremusic-web,deployment.environment=prod"
```

## §8 Komşu sınır

| # | Komşu | Sınır | Yön | Kaynak |
|---|---|---|---|---|
| 1 | metrics/ | Ölçüm üretimi metrics'te; bu dosya ölçüm tekniği/yorumu | iç | §6 |
| 2 | audio-diagnostics/ | Audio gecikme/xrun özelinde derinleşme | iç | §3 |
| 3 | K014-media | Buffer/latency kaynağı media; düzeltme K003/K004 | K016 → K014 | master §C/4 |
| 4 | K011-application | Frontend Core Web Vitals ölçümü uygulama katmanı | K016 ↔ K011 | §2 |
| 5 | K019-testing | Performans regresyon testi | K016 → K019 | §5 satır 6 |

## §9 Açık sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etki |
|---|---|---|
| 1 | Core Web Vitals ölçüm düzeneği (RUM) kurulu mu? | web ölçümü |
| 2 | p95 hedefi (< 500ms) onaylandı mı? | alerts/ eşiği |
| 3 | Profil aracı seçimi (JFR/pprof/opcache) netleşti mi? | araç zinciri |
| 4 | Realtime callback dış sınırı (ölçüm DEĞİL) kesinleşti mi? | audio güvenliği |

## §F Web tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| web.dev Core Web Vitals — LCP/INP/CLS tanımları ve eşikleri | https://web.dev/articles/vitals | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |
| Go net/http/pprof — profil uçları (CPU/heap/goroutine) | https://pkg.go.dev/net/http/pprof | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §G Revizyon doğrulama kaydı

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 507 |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§9 + §F + §G | ✓ |
| 5 | Çok-dilli kod | 10 blok (SQL · Go · TS · C# · Rust · C++ · PHP · Java · YAML) · her biri TASARIM ÖRNEĞİ işaretli | ✓ |
| 6 | Web tablosu | 2 satır · envanter dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | boş hücre · üç nokta dolgu · gelecek-sözü · mojibake | ✓ 0 |
| 8 | Kanıt dürüstlüğü | performance disk kanıtı YOK → tasarım/PLANNED | ✓ |
| 9 | Git commit | atılmadı | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
