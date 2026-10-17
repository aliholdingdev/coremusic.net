---
title: "K016-observability/audio-diagnostics — Xrun, Underrun ve Audio Teşhisi"
node_id: K016-observability/audio-diagnostics/audio-diagnostics-xrun-ve-tehis
node_type: subdomain
status: draft
parent_id: K016-observability/audio-diagnostics
children: []
depends_on:
  - K016-observability
  - K014-media
evidence:
  - "# audio-diagnostics için disk kanıtı YOK (tasarım dosyası; K003/K004 DSP çözümleri ayrı)"
validation:
  - "Bu turda audio-diagnostics için disk kanıtı YOK — tasarım/PLANNED (⚠️ VERIFICATION REQUIRED)"
open_questions:
  - "Xrun sayacı hangi katmandan okunacak (K014 driver)? sahiplik mutabakatı"
group: mimari
---


# K016-observability/audio-diagnostics — audio-diagnostics-xrun-ve-tehis

## §1 Rol (ELI15)

Ses kesildi, patladı ya da takıldı. Bunun **teşhisi** bu domaindedir: xrun/underrun sayacı, buffer doluluğu, gecikme (latency) ölçümü. **Düzeltme** burada değil — DSP algoritması K003/K004'tedir. Bu domain yalnızca "sorun ne, nerede, ne sıklıkta" sorusunu ölçer ve `alerts/`'e sinyal verir.

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Xrun/underrun sayacı | ✅ ölçüm + raporlama | ❌ DSP düzeltmesi → K003/K004 |
| Buffer doluluğu izleme | ✅ watermark ölçümü | ❌ Buffer tasarımı → K014-media |
| Audio gecikme (latency) | ✅ round-trip ölçümü | ❌ ASIO/WASAPI sürücü → K014 |
| Teşhis akışı (şiddet ayrımı) | ✅ hafif/şiddetli bozulma | ❌ Karar/aksiyon → incident-response/ |
| Tekrar üretme (repro) | ✅ teşhis verisi paketleme | ❌ Test stratejisi → K019-testing |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Xrun | xrun | Buffer taşıması/boşalması (genel) | sayaç |
| Underrun | underrun | Veri yetmezliği (kaynak tarafı) | sayaç |
| Overrun | overrun | Fazla veri (hedef tarafı) | sayaç |
| Buffer | buffer | Geçici ses deposu | doluluk % |
| Watermark | watermark | Kritik doluluk eşiği | §5 |
| Latency | latency | Gecikme (ms) | round-trip |
| Dropout | dropout | Kaçan örnek (bozulma) | §4 |
| DSP | digital signal processing | Sayısal sinyal işleme | K003/K004 |
| Callback | callback | Gerçek zamanlı geri çağırma | ölçülmaz (realtime) |
| Teşhis | diagnosis | Sorunun kaynağı | §6 |

## §4 Xrun türleri ve belirtileri

| # | Tür | Kaynak | Belirti | Şiddet |
|---|---|---|---|---|
| 1 | Underrun | kaynak besleyemiyor | kesik/tıkırtı | yüksek |
| 2 | Overrun | hedef boşaltamıyor | distorsiyon | yüksek |
| 3 | Glitch | tek seferlik örnek kaçırma | kısa çıt | orta |
| 4 | Silence gap | sessizlik boşluğu | kopuk ses | yüksek |
| 5 | Priority inversion | CPU öncelik tersi | periyodik xrun | orta |
| 6 | Thermal throttle | ısınma | artan xrun | düşük→yüksek |

## §5 Buffer doluluğu ve watermark

### §5.1 Doluluk eşikleri

| # | Doluluk | Durum | Eylem (sinyal) |
|---|---|---|---|
| 1 | %0-40 | boşalma riski | info |
| 2 | %40-60 | sağlıklı (hedef ~%50) | pass |
| 3 | %60-85 | dolma eğilimi | warn |
| 4 | %85-95 | watermark aşıldı | ticket adayı |
| 5 | %95-100 | xrun eşiği | page adayı |

**Kural:** Sağlıklı buffer ortalanmış (%50 civarı) olmalı; sürekli bir tarafa kayma (boş/dolu) darboğaz işaretidir.

### §5.2 Ölçüm örneği (somut)

| # | Zaman | Doluluk | Xrun (dk) | Latency (ms) | Durum |
|---|---|---|---|---|---|
| 1 | 10:00:00 | %52 | 0 | 12 | ✅ pass |
| 2 | 10:05:00 | %81 | 1 | 15 | ⚠️ warn |
| 3 | 10:10:00 | %93 | 6 | 28 | 🔴 page adayı |
| 4 | 10:15:00 | %48 | 0 | 12 | ✅ pass |

### §5.3 Xrun ↔ latency ↔ doluluk ilişkisi

| # | Doluluk | Xrun/dk | p95 latency | Teşhis | Sahip |
|---|---|---|---|---|---|
| 1 | %48-52 | 0 | 12ms | sağlıklı | — |
| 2 | %81 | 1 | 15ms | dolma eğilimi, erken uyarı | K016 (izleme) |
| 3 | %93 | 6 | 28ms | xrun eşiği aşıldı | K014 (buffer) |
| 4 | %99 | 15 | 60ms | kritik, sürekli taşma | K003/K004 (DSP) |
| 5 | %25 | 8 | 55ms | boşalma (kaynak besleyemiyor) | K014 (kaynak) |

**Model:** Doluluk iki uca kaydığında xrun artar (boş: underrun, dolu: overrun). Latency, xrun ile birlikte yükselir. Üçü birlikte okunur — tek başına yanıltır.

## §6 Teşhis akışı (adım adım)

| # | Adım | Soru | Ölçüm | Çıktı |
|---|---|---|---|---|
| 1 | Var mı? | Xrun oluşuyor mu? | sayaç | evet/hayır |
| 2 | Ne sıklıkla? | Kaç/dk? | oran | eşik karşılaştırma |
| 3 | Ne zaman? | Belirli işlemde mi? | korelasyon | tetikleyici |
| 4 | Nerede? | Kaynak/hedef/OS? | katman ayrımı | sahip (K014/K003) |
| 5 | Şiddet? | Hafif/şiddetli? | dropout sayısı | severity |
| 6 | Tekrar | Repro edilebilir mi? | teşhis paketi | K019 girdisi |

### §6.1 Somut teşhis senaryoları

| # | Belirti | Sıklık | Muhtemel kaynak | Sahip | Severity |
|---|---|---|---|---|---|
| 1 | kesik ses (underrun) | 6/dk | CPU yetersiz / öncelik | K014/K018 | page |
| 2 | distorsiyon (overrun) | 2/dk | hedef boşaltma yavaş | K014 | ticket |
| 3 | periyodik çıt | 1/dk | thermal throttle | K018 | ticket |
| 4 | sessizlik kopması | 3/dk | kaynak yeniden bağlanma | K012 | page |
| 5 | tek seferlik glitch | 0.2/dk | tek örnek kaçırma | K016 gözlem | info |

### §6.2 Teşhis veri paketi (K019 için)

| # | Alan | Değer örneği | Amaç |
|---|---|---|---|
| 1 | timestamp | 2026-10-13T10:10:00Z | korelasyon |
| 2 | xrun counts | underrun=5 overrun=1 | şiddet |
| 3 | buffer fill | 0.93 | doluluk |
| 4 | latency p95 | 28ms | gecikme |
| 5 | device | asio: Focusrite | ortam |
| 6 | version | 1.4.2 | sürüm |
| 7 | trace-id | 4bf92f3577b34da6a3ce929d0e0e4736 | korelasyon (correlation/) |

**Kural:** Teşhis paketi, K019 test tekrar üretimi için yeterli veriyi taşır (trace-id bağlantılı).

## §7 ÇOK DİLLİ KOD ÖRNEKLERİ

> **TASARIM ÖRNEĞİ (illüstratif)** — bloklar repo'da karşılığı OLMAYAN, xrun/latency ölçüm desenini gösteren tasarım örnekleridir. Durum: **PLANNED**. Realtime callback **lock-free** kuralına uyar (ölçüm, callback dışında sayaç taşır).

### §7.1 C++ — lock-free xrun sayacı (realtime güvenli)

```cpp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/audio-diagnostics — realtime callback içinde atomik sayaç (lock-free).
#pragma once
#include <atomic>
#include <cstdint>

namespace cm::obs {

class XrunCounter {
public:
    // realtime callback'te güvenli: yalnız atomik increment (lock yok).
    void onUnderrun() noexcept { underrun_.fetch_add(1, std::memory_order_relaxed); }
    void onOverrun()  noexcept { overrun_.fetch_add(1,  std::memory_order_relaxed); }

    uint64_t underrun() const noexcept { return underrun_.load(std::memory_order_relaxed); }
    uint64_t overrun()  const noexcept { return overrun_.load(std::memory_order_relaxed); }

    // callback dışındaki gözlemci okur.
    struct Snapshot { uint64_t underrun; uint64_t overrun; };
    Snapshot snapshot() const noexcept { return {underrun(), overrun()}; }

private:
    std::atomic<uint64_t> underrun_{0};
    std::atomic<uint64_t> overrun_{0};
};

} // namespace cm::obs
```

### §7.2 Rust — atomik sayaç + doluluk

```rust
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/audio-diagnostics — atomik xrun sayacı + buffer doluluk durumu.
use std::sync::atomic::{AtomicU64, Ordering};

pub struct BufferHealth {
    underruns: AtomicU64,
    overruns: AtomicU64,
}

impl BufferHealth {
    pub const fn new() -> Self {
        Self { underruns: AtomicU64::new(0), overruns: AtomicU64::new(0) }
    }

    /// realtime callback'te güvenli (relaxed atomic, kilitsiz).
    pub fn on_underrun(&self) {
        self.underruns.fetch_add(1, Ordering::Relaxed);
    }

    pub fn doluluk_durumu(&self, fill_ratio: f64) -> &'static str {
        match fill_ratio {
            r if r >= 0.95 => "page",
            r if r >= 0.85 => "ticket",
            r if r >= 0.60 => "warn",
            r if r >= 0.40 => "pass",
            _ => "info",
        }
    }
}
```

### §7.3 Go — latency round-trip ölçümü

```go
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/audio-diagnostics — round-trip latency ölçümü (callback dışı).
package audiodiag

import "time"

type LatencyStats struct {
	Samples []time.Duration
}

func (l *LatencyStats) Add(d time.Duration) {
	l.Samples = append(l.Samples, d)
}

// P95 enstrümante round-trip gecikme.
func (l *LatencyStats) P95() time.Duration {
	if len(l.Samples) == 0 {
		return 0
	}
	sorted := make([]time.Duration, len(l.Samples))
	copy(sorted, l.Samples)
	// basit kabuk sıralama (küçük örneklemler için yeterli)
	for i := 0; i < len(sorted); i++ {
		for j := i + 1; j < len(sorted); j++ {
			if sorted[j] < sorted[i] {
				sorted[i], sorted[j] = sorted[j], sorted[i]
			}
		}
	}
	idx := int(float64(len(sorted)-1) * 0.95)
	return sorted[idx]
}
```

### §7.4 Python — xrun analizi (log çözümleme)

```python
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/audio-diagnostics — xrun loglarından teşhis raporu.
from __future__ import annotations

import re
from collections import Counter

XRUN_RE = re.compile(r"(?P<kind>underrun|overrun)\s+x=(?P<count>\d+)")


def tehis(lines: list[str]) -> dict:
    """xrun log satırlarından teşhis özeti üretir."""
    kinds: Counter[str] = Counter()
    total = 0
    for line in lines:
        m = XRUN_RE.search(line)
        if not m:
            continue
        n = int(m.group("count"))
        kinds[m.group("kind")] += n
        total += n

    return {
        "toplam_xrun": total,
        "underrun": kinds["underrun"],
        "overrun": kinds["overrun"],
        "siddet": "page" if total > 5 else ("ticket" if total > 0 else "pass"),
        "ornek_satir": next((l for l in lines if XRUN_RE.search(l)), ""),
    }


def doluluk_durumu(fill_ratio: float) -> str:
    if fill_ratio >= 0.95:
        return "page"
    if fill_ratio >= 0.85:
        return "ticket"
    if fill_ratio >= 0.60:
        return "warn"
    if fill_ratio >= 0.40:
        return "pass"
    return "info"
```

### §7.5 C# — ses kartı gecikme ölçümü

```csharp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/audio-diagnostics — round-trip latency ölçümü (callback dışı).
#nullable enable
using System.Diagnostics;

namespace CoreMusic.Observability.Audio;

public sealed class LatencyProbe
{
    private readonly List<TimeSpan> _samples = new();

    public void MeasureRoundTrip(Action duplexRoundTrip, int repeats = 20)
    {
        var sw = new Stopwatch();
        for (var i = 0; i < repeats; i++)
        {
            sw.Restart();
            duplexRoundTrip();
            sw.Stop();
            _samples.Add(sw.Elapsed);
        }
    }

    public double P95Ms()
    {
        var sorted = _samples.OrderBy(x => x).ToList();
        var idx = (int)((sorted.Count - 1) * 0.95);
        return sorted[idx].TotalMilliseconds;
    }

    public static string DolulukDurumu(double fillRatio)
        => fillRatio >= 0.95 ? "page"
         : fillRatio >= 0.85 ? "ticket"
         : fillRatio >= 0.60 ? "warn"
         : fillRatio >= 0.40 ? "pass"
         : "info";
}
```

### §7.6 PHP — teşhis raporu üretimi

```php
<?php
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/audio-diagnostics — teşhis raporu (sayım + şiddet).
declare(strict_types=1);

namespace CoreMusic\Observability\Audio;

final class AudioDiagnostics
{
    /** @param array<int,array{kind:string,count:int}> $events */
    public function report(array $events): array
    {
        $underrun = 0;
        $overrun = 0;
        foreach ($events as $e) {
            if ($e['kind'] === 'underrun') { $underrun += $e['count']; }
            elseif ($e['kind'] === 'overrun') { $overrun += $e['count']; }
        }
        $total = $underrun + $overrun;

        return [
            'toplam_xrun' => $total,
            'underrun'    => $underrun,
            'overrun'     => $overrun,
            'siddet'      => $total > 5 ? 'page' : ($total > 0 ? 'ticket' : 'pass'),
            'sahip'       => 'K014-media', // düzeltme sahibi (bu dosya DEĞİL)
        ];
    }
}
```

### §7.7 JavaScript/TS — Web Audio xrun takibi

```typescript
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/audio-diagnostics — Web Audio buffer izleme (tarayıcı gözlemi).
interface BufferSample {
  fillRatio: number;   // 0..1
  xrunDelta: number;
  at: number;          // epoch ms
}

const PAGE = 0.95;
const TICKET = 0.85;

export function durum(s: BufferSample): "page" | "ticket" | "warn" | "pass" | "info" {
  if (s.fillRatio >= PAGE) return "page";
  if (s.fillRatio >= TICKET) return "ticket";
  if (s.fillRatio >= 0.6) return "warn";
  if (s.fillRatio >= 0.4) return "pass";
  return "info";
}

export function xrunOrani(samples: BufferSample[], windowMs: number): number {
  const now = samples[samples.length - 1]?.at ?? 0;
  const inWindow = samples.filter((s) => now - s.at <= windowMs);
  const total = inWindow.reduce((acc, s) => acc + s.xrunDelta, 0);
  return total / (windowMs / 60_000); // dakikada xrun
}
```

### §7.8 Java — xrun sayacı

```java
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/audio-diagnostics — atomik xrun sayacı.
package com.coremusic.audio;

import java.util.concurrent.atomic.AtomicLong;

public final class XrunCounter {
    private final AtomicLong underrun = new AtomicLong();
    private final AtomicLong overrun = new AtomicLong();

    public void onUnderrun() { underrun.incrementAndGet(); }
    public void onOverrun()  { overrun.incrementAndGet(); }

    public long total() { return underrun.get() + overrun.get(); }

    public String severity() {
        long t = total();
        return t > 5 ? "page" : (t > 0 ? "ticket" : "pass");
    }
}
```

### §7.9 YAML — teşhis eşik yapılandırması

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/audio-diagnostics — teşhis eşikleri (alerts/ bağlantı noktası).
audio_diagnostics:
  xrun:
    warn_per_minute: 1
    ticket_per_minute: 3
    page_per_minute: 5
  buffer_fill:
    page: 0.95
    ticket: 0.85
    warn: 0.60
    pass_min: 0.40
  latency_ms:
    pass: 20
    warn: 40
    page: 80
  owners:
    detection: K016-observability   # ölçüm (bu domain)
    fix_dsp: K003/K004              # DSP düzeltmesi
    fix_driver: K014-media          # sürücü/buffer
```

### §7.10 Bash — xrun log taraması (grep + sayaç)

```bash
#!/usr/bin/env bash
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/audio-diagnostics — xrun loglarından dakikalık oran hesabı.
set -euo pipefail

LOG="${1:?kullanım: xrun-scan.sh <log-dosyasi>}"
WINDOW_MIN="${2:-1}"

under=$(grep -ci 'underrun' "$LOG" || true)
over=$(grep -ci 'overrun' "$LOG" || true)
total=$((under + over))

minutes=$(awk -v w="$WINDOW_MIN" 'BEGIN { print (w > 0 ? w : 1) }')
rate=$(awk -v t="$total" -v m="$minutes" 'BEGIN { printf "%.1f", t / m }')

severity="pass"
[ "$total" -gt 0 ] && severity="ticket"
[ "$(awk -v r="$rate" 'BEGIN { print (r > 5) }')" -eq 1 ] && severity="page"

echo "toplam=${total} underrun=${under} overrun=${over} oran=${rate}/dk siddet=${severity}"
```

### §7.11 PowerShell — teşhis raporu (Windows gözlem)

```powershell
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/audio-diagnostics — Windows ses teşhis özeti (olay günlüğü taraması).
param(
    [int]$Dakika = 5,
    [string]$Kaynak = 'CoreMusic-Audio'
)

$kesit = Get-WinEvent -FilterHashtable @{
    LogName   = 'Application'
    ProviderName = $Kaynak
    StartTime = (Get-Date).AddMinutes(-$Dakika)
} -ErrorAction SilentlyContinue

$xrun = @($kesit | Where-Object { $_.Message -match 'xrun|underrun|overrun' })
$toplam = ($xrun | Measure-Object).Count
$oran = [math]::Round($toplam / [math]::Max($Dakika, 1), 1)
$siddet = if ($oran -gt 5) { 'page' } elseif ($toplam -gt 0) { 'ticket' } else { 'pass' }

[pscustomobject]@{
    Zaman   = (Get-Date).ToString('yyyy-MM-ddTHH:mm:ssZ')
    Toplam  = $toplam
    OranDk  = $oran
    Siddet  = $siddet
    Sahip   = 'K014-media'   # tespit: K016, düzeltme: K014
} | ConvertTo-Json -Compress
```

## §8 Komşu sınır

| # | Komşu | Sınır | Yön | Kaynak |
|---|---|---|---|---|
| 1 | K003/K004 (DSP) | Xrun DÜZELTMESİ DSP'de; teşhis burada | K016 → K003/K004 | master §C/4 |
| 2 | K014-media | Buffer/sürücü kaynağı; sayaç verisi | K016 → K014 | §2 |
| 3 | alerts/ | Xrun > 5/dk → page adayı | iç | §5, §7.9 |
| 4 | performance/ | Gecikme ölçümü genel teknik; audio özel | iç | master §D.2/6 |
| 5 | incident-response/ | Teşhis verisi runbook girdisi | iç | §6 |

## §9 Açık sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etki |
|---|---|---|
| 1 | Xrun sayacı hangi katmandan okunacak (K014 sürücüsü)? | sahiplik mutabakatı |
| 2 | Latency hedefi (< 20ms) donanıma göre netleşti mi? | eşikler |
| 3 | Teşhis verisi toplama izni (K009) alındı mı? | gizlilik |
| 4 | Realtime lock-free sınırı kesinleşti mi (callback atomik)? | güvenlik |
| 5 | Hangi audio backends (ASIO/WASAPI/CoreAudio) kapsanacak? | kapsam |
| 6 | Teşhis paketi otomatik mi manuel mi toplanacak? | K019 girdisi |
| 7 | xrun eşiği (5/dk → page) donanıma göre uyarlanacak mı? | alerts/ eşikleri |

## §F Web tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| JUCE — AudioIODevice buffer size / xrun (underrun) kavramı | https://juce.com/doc/classspec/IODevice/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |
| PortAudio — xrun/overflow/underflow callback kavramı | http://portaudio.com/docs/v19-doxydocs/group__pa__stream__flags.html | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §G Revizyon doğrulama kaydı

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 541 |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§9 + §F + §G | ✓ |
| 5 | Çok-dilli kod | 11 blok (C++ · Rust · Go · Python · C# · PHP · TS · Java · YAML · Bash · PowerShell) · her biri TASARIM ÖRNEĞİ işaretli | ✓ |
| 6 | Realtime kural | lock-free atomik sayaç (C++/Rust) | ✓ |
| 7 | Web tablosu | 2 satır · envanter dışı · HTTP 200 | ✓ |
| 8 | Yasaklı yer-tutucu | boş hücre · üç nokta dolgu · gelecek-sözü · mojibake | ✓ 0 |
| 9 | Kanıt dürüstlüğü | audio-diagnostics disk kanıtı YOK → tasarım/PLANNED | ✓ |
| 10 | Git commit | atılmadı | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
