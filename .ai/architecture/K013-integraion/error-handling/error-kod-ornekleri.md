---
node_id: K013-integraion/error-handling/error-kod-ornekleri
node_type: technology-deepdive
title: "Error Kod Örnekleri — 13 Dilde Retry + Exponential Backoff + Jitter / Circuit Breaker"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/error-handling
children: []
depends_on:
  - K013-integraion/error-handling
  - K013-integraion/error-handling/error-anayasa
  - K013-integraion/error-handling/error-sozlesme
  - K013-integraion
evidence:
  - "https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html (2026-10-16)"
  - "https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/retry (2026-10-16)"
  - "https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16)"
  - "https://www.rfc-editor.org/rfc/rfc6585.html (2026-10-16)"
  - "https://cloud.google.com/storage/docs/retry-strategy (2026-10-16)"
  - "https://docs.stripe.com/rate-limits (2026-10-16)"
  - "https://learn.microsoft.com/en-us/graph/throttling (2026-10-16)"
validation:
  - "Her örnek full-jitter formülüne uyar: sleep = random(0,1) × min(cap, base×2^retry) (AWS SDK [S1])"
  - "sleep yerine clock soyutlaması kullanılmıştır — test edilebilirlik"
  - "Parametre değerleri kaynaklıdır (AWS defaultları referans) veya örnektir; CoreMusic değeri UNKNOWN"
open_questions:
  - "CoreMusic hangi dillerde retry istemcisi kullanacak → ⚠️ UNKNOWN"
  - "Örneklerdeki parametreler referans; CoreMusic politikası onay bekliyor (anayasa §4.2)"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (ADR-097 K4 faz C)"
group: kod
---

# Error Kod Örnekleri — 13 Dilde Retry + Exponential Backoff + Jitter / Circuit Breaker

> **Bu dosya ne değildir:** Production kütüphanesi veya CoreMusic modülü
> değildir. Her örnek **kavramsal ama derlenebilir-tarzda** bir şablondur;
> saat/uyku bağımlılığı soyutlanmıştır (test edilebilirlik). Parametreler
> AWS SDK defaultlarıyla **ayarlıdır** (max_attempts 3 · transient base 50 ms
> · throttling base 1000 ms · cap 20000 ms · full jitter) — CoreMusic
> politikası onaylanana kadar referans değer olarak kullanılır
> (`error-anayasa.md` §4.2).

---

## §0 ELI15 — Neden 13 dil?

Her dilin hata modeli farklıdır (exception / Result / error enum / pattern
match). Retry + jitter mantığı **aynı matematik**tir; dil sadece bunu
ifade etme biçimidir. 13 örnek, aynı formülü 13 farklı hata modelinde
gösterir. `sync-kod-ornekleri.md` ile aynı katalog genişliği korunur.

**Ortak formül (her örnekte):**

```text
backoff = min(cap, base × 2^attempt)
sleep   = random(0, 1) × backoff        ← full jitter [S1] [S2]
```

---

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `error-anayasa.md` | Sınıflandırma eksenleri, mekanizma seçimi, sınır | Kademe-3 |
| mimari | `error-sozlesme.md` | Hata sözleşmesi, Problem Details, eşleme tablosu | Kademe-3 |
| kod | `error-dogrulama.md` | Doğrulama kapıları ve test senaryoları | Kademe-3 |
| **kod** | **`error-kod-ornekleri.md`** | **13 dilde retry/circuit breaker örneği** | Kademe-3 |
| kanit | `error-web-arastirma.md` | ≥12 benzersiz resmi kaynak | Kademe-3 |

---

## §1 Dizin — 13 Dil

| # | Dil | Rol | Örnek tipi |
|---|---|---|---|
| 1 | PHP | Sunucu tarafı dış API istemcisi (cURL) | Retry + full jitter + Retry-After |
| 2 | JavaScript | Tarayıcı/Node istemci | Retry + full jitter |
| 3 | TypeScript | Tip güvenli HTTP istemci sarmalayıcı | Retry + sınıf eşleme |
| 4 | Python | Servis/boru hattı istemcisi | Retry + Retry-After + jitter |
| 5 | Go | Explicit error + timeout bütçesi | Retry + context timeout |
| 6 | Java | Kurumsal istemci | Exponential backoff + jitter |
| 7 | C# | .NET istemci (#nullable enable) | Polly tarzı politika + jitter |
| 8 | Rust | Result-tabanlı | Retry + ? operator |
| 9 | C++ | Gerçek zamanlı olmayan yardımcı | Retry + std::variant hata |
| 10 | Ruby | Otomasyon betiği | Retry + jitter |
| 11 | Kotlin | Coroutine istemci | Retry + sealed class |
| 12 | Swift | iOS istemci | throws + retry |
| 13 | Elixir | Hataya-dayanıklı servis | GenServer + backoff |

---

## §2 PHP

```php
<?php
# Dil: PHP 8.4 — Rol: Sunucu tarafı dış API istemcisi (PDO/cURL)
declare(strict_types=1);

/**
 * Retry + exponential backoff + FULL JITTER.
 * Formül: sleep = random(0,1) × min(cap, base × 2^attempt)  [AWS SDK — S1]
 * Clock soyutlaması: usleep yerine $clock çağrısı (test edilebilir).
 */
final class RetryPolicy
{
    /** @var callable(int):void  milisaniye kadar bekle */
    private $sleep;

    public function __construct(
        private readonly int $maxAttempts = 3,        // AWS default [S1]
        private readonly int $baseDelayMs = 1000,
        private readonly int $capMs = 20000,          // AWS cap [S1]
        ?callable $sleep = null,
    ) {
        // Test'te $sleep sahte fonksiyonla beslenir; production'da usleep.
        $this->sleep = $sleep ?? static function (int $ms): void {
            usleep($ms * 1000);
        };
    }

    /**
     * @template T
     * @param callable(int):T $operation  attempt (0-tabanlı) alır
     * @param callable(\Throwable):bool $isTransient
     * @return T
     * @throws \Throwable son hata yükseltilir
     */
    public function run(callable $operation, callable $isTransient): mixed
    {
        $attempt = 0;
        while (true) {
            try {
                return $operation($attempt);
            } catch (\Throwable $e) {
                $attempt++;
                if ($attempt >= $this->maxAttempts || !$isTransient($e)) {
                    throw $e;                     // cap doldu veya kalıcı
                }
                // Full jitter [S1]
                $backoff = min($this->capMs, $this->baseDelayMs * (2 ** $attempt));
                $jittered = (int) random_int(0, $backoff);   // random(0,1)×backoff
                ($this->sleep)($jittered);
            }
        }
    }
}

/**
 * 429 + Retry-After: sunucu kuralı jitter'dan ÖNCEDİR.
 * RFC 6585 §4 / RFC 9110 §10.2.3 [S3][S4] · Graph örneği Retry-After:10 [S9]
 */
function retryAfterDelayMs(array $headers): ?int
{
    if (!isset($headers['Retry-After'])) {
        return null;
    }
    $v = $headers['Retry-After'];
    return ctype_digit($v) ? (int) $v * 1000 : null;  // delay-seconds
}
```

**Not:** `random_int` kriptografik rastgelelik kullanır; jitter için fazladan
entropi zarar vermez. Idempotency: `operation` non-idempotent ise bu politika
**kullanılmaz** (RFC 9110 §9.2.2 [S5]).

---

## §3 JavaScript

```js
// Dil: JavaScript (ES2022+) — Rol: Tarayıcı/Node istemci retry
// Formül: sleep = random() × min(cap, base × 2^attempt)  [full jitter — S1][S2]

export class RetryPolicy {
  /**
   * @param {object} opts
   * @param {number} [opts.maxAttempts=3]     AWS default [S1]
   * @param {number} [opts.baseDelayMs=1000]
   * @param {number} [opts.capMs=20000]       AWS cap [S1]
   * @param {(ms:number)=>Promise<void>} [opts.sleep]  clock soyutlaması
   */
  constructor({ maxAttempts = 3, baseDelayMs = 1000, capMs = 20000, sleep } = {}) {
    this.maxAttempts = maxAttempts;
    this.baseDelayMs = baseDelayMs;
    this.capMs = capMs;
    // Test: sahte sleep; prod: setTimeout sarmalayıcısı
    this.sleep = sleep ?? ((ms) => new Promise((r) => setTimeout(r, ms)));
  }

  /** @param {(attempt:number)=>Promise<T} operation @param {(e:unknown)=>boolean} isTransient */
  async run(operation, isTransient) {
    let attempt = 0;
    for (;;) {
      try {
        return await operation(attempt);
      } catch (e) {
        attempt += 1;
        if (attempt >= this.maxAttempts || !isTransient(e)) throw e;
        const backoff = Math.min(this.capMs, this.baseDelayMs * 2 ** attempt);
        const jittered = Math.random() * backoff;   // full jitter
        await this.sleep(Math.round(jittered));
      }
    }
  }
}

// 429 durum kodu geçici kabul edilir (RFC 6585 [S4]); 4xx çoğu kalıcı.
export const isTransientStatus = (status) =>
  status === 408 || status === 429 || status >= 500;
```

---

## §4 TypeScript

```ts
// Dil: TypeScript — Rol: Tip güvenli HTTP istemci sarmalayıcı + sınıf eşleme
// Eşleme: error-sozlesme.md §3.2 (HTTP status → transient/permanent)

type ErrorClass = "transient" | "permanent" | "subtle";

interface ClassifiedError {
  class: ErrorClass;
  retryable: boolean;
  httpStatus: number;
  retryAfterMs?: number;   // 429/503'te sunucu değeri korunur
  cause: unknown;
}

// RFC 9110 §15 durum kodları + Google retry listesi [S7]
function classify(status: number): ErrorClass {
  if (status === 408 || status === 429) return "transient";
  if (status >= 500) return "transient";
  return "permanent";                      // 4xx çoğu
}

interface RetryOptions {
  maxAttempts: number;                     // AWS default 3 [S1]
  baseDelayMs: number;
  capMs: number;                           // AWS cap 20000 [S1]
  sleep: (ms: number) => Promise<void>;    // clock soyutlaması
}

async function withRetry<T>(
  op: (attempt: number) => Promise<T>,
  opts: RetryOptions,
  readRetryAfterMs?: (e: unknown) => number | undefined,
): Promise<T> {
  let attempt = 0;
  for (;;) {
    try {
      return await op(attempt);
    } catch (e) {
      const status = (e as { status?: number }).status ?? 0;
      const cls = classify(status);
      attempt += 1;
      if (cls === "permanent" || attempt >= opts.maxAttempts) throw e;

      // Retry-After sunucu kuralıdır — jitter'ı ezer (error-anayasa §11.3) [S9]
      const serverDelay = readRetryAfterMs?.(e);
      if (serverDelay !== undefined) {
        await opts.sleep(serverDelay);
        continue;
      }
      const backoff = Math.min(opts.capMs, opts.baseDelayMs * 2 ** attempt);
      await opts.sleep(Math.round(Math.random() * backoff));  // full jitter
    }
  }
}
```

---

## §5 Python

```python
# Dil: Python 3.12 — Rol: Servis/boru hattı istemcisi
# Formül: sleep = random() × min(cap, base × 2^attempt)  [full jitter — S1][S2]
from __future__ import annotations

import random
import time
from collections.abc import Callable
from dataclasses import dataclass
from typing import TypeVar

T = TypeVar("T")


@dataclass(frozen=True, slots=True)
class RetryPolicy:
    max_attempts: int = 3          # AWS default [S1]
    base_delay_ms: int = 1000
    cap_ms: int = 20_000           # AWS cap [S1]
    # Clock soyutlaması: production'da time.sleep; test'te kaydedici
    sleep: Callable[[float], None] = time.sleep

    def run(self, op: Callable[[int], T], is_transient: Callable[[BaseException], bool]) -> T:
        attempt = 0
        while True:
            try:
                return op(attempt)
            except BaseException as exc:
                attempt += 1
                if attempt >= self.max_attempts or not is_transient(exc):
                    raise                      # son hata yükseltilir
                backoff = min(self.cap_ms, self.base_delay_ms * (2 ** attempt))
                jittered = random.random() * backoff / 1000.0   # full jitter → saniye
                self.sleep(jittered)


# 429 + Retry-After: Graph örneği Retry-After: 10 [S9] · RFC 6585 [S4]
def _retry_after_seconds(headers: dict[str, str]) -> float | None:
    raw = headers.get("Retry-After")
    return float(raw) if raw is not None and raw.isdigit() else None
```

**Not:** Google Cloud Storage Python istemcisi varsayılan olarak exponential
backoff kullanır ve 408/429/5xx'i retry eder [S7] — bu örnek aynı sınıfa uyar.

---

## §6 Go

```go
// Dil: Go — Rol: Explicit error + timeout bütçesi (context)
// Formül: sleep = rand × min(cap, base × 2^attempt)  [full jitter — S1]

package retry

import (
	"context"
	"math/rand"
	"time"
)

type Policy struct {
	MaxAttempts int           // AWS default 3 [S1]
	BaseDelay   time.Duration // referans: 1s (Google Go default — S7)
	Cap         time.Duration // referans: 30s (Google Go max delay — S7)
	Sleep       func(ctx context.Context, d time.Duration) error // clock soyutlaması
}

func (p Policy) Run(
	ctx context.Context,
	op func(ctx context.Context, attempt int) error,
	isTransient func(error) bool,
) error {
	attempt := 0
	for {
		err := op(ctx, attempt)
		if err == nil {
			return nil
		}
		attempt++
		if attempt >= p.MaxAttempts || !isTransient(err) {
			return err // son hata yükseltilir
		}
		backoff := p.BaseDelay << uint(attempt) // base × 2^attempt
		if backoff > p.Cap || backoff <= 0 {
			backoff = p.Cap // cap [S1]
		}
		jittered := time.Duration(rand.Int63n(int64(backoff))) // full jitter
		if serr := p.Sleep(ctx, jittered); serr != nil {
			return serr // timeout bütçesi doldu → fail-fast (Azure nested retry [S5])
		}
	}
}

// Bağlantı hataları geçicidir (Google Go: connection refused/reset/EOF — S7)
func IsTransient(err error) bool { /* ... */ return false }
```

**Not:** `context.Context` timeout bütçesi taşır — üst katmandan gelen
deadline `Sleep` tarafından da saygı görür (error-anayasa §6).

---

## §7 Java

```java
// Dil: Java 21 — Rol: Kurumsal istemci (exponential backoff + jitter)
// Formül: sleep = random × min(cap, base × 2^attempt)  [full jitter — S1][S2]

import java.util.concurrent.ThreadLocalRandom;
import java.util.function.LongConsumer;

public final class RetryPolicy {
    private final int maxAttempts;      // AWS default 3 [S1]
    private final long baseDelayMs;
    private final long capMs;           // AWS cap 20000 [S1]
    private final LongConsumer sleep;   // clock soyutlaması (ms)

    public RetryPolicy(int maxAttempts, long baseDelayMs, long capMs, LongConsumer sleep) {
        this.maxAttempts = maxAttempts;
        this.baseDelayMs = baseDelayMs;
        this.capMs = capMs;
        this.sleep = sleep;
    }

    @FunctionalInterface
    public interface Op<T> {
        T apply(int attempt) throws Exception;
    }

    public <T> T run(Op<T> op, java.util.function.Predicate<Exception> isTransient) throws Exception {
        int attempt = 0;
        while (true) {
            try {
                return op.apply(attempt);
            } catch (Exception e) {
                attempt++;
                if (attempt >= maxAttempts || !isTransient.test(e)) {
                    throw e;                                  // son hata yükseltilir
                }
                long backoff = Math.min(capMs, baseDelayMs * (1L << attempt));
                long jittered = (long) (ThreadLocalRandom.current().nextDouble() * backoff);
                sleep.accept(jittered);                       // full jitter
            }
        }
    }
}
```

---

## §8 C\#

```csharp
// Dil: C# (.NET 8) — Rol: Tip güvenli istemci (#nullable enable)
// Polly tarzı politika; formül: sleep = rand × min(cap, base × 2^attempt)

#nullable enable
using System;
using System.Net;
using System.Threading.Tasks;

public sealed class RetryPolicy
{
    private readonly int _maxAttempts;                 // AWS default 3 [S1]
    private readonly TimeSpan _baseDelay;              // referans 1s
    private readonly TimeSpan _cap;                    // AWS cap 20s [S1]
    private readonly Func<TimeSpan, Task> _sleep;      // clock soyutlaması

    public RetryPolicy(int maxAttempts, TimeSpan baseDelay, TimeSpan cap,
                       Func<TimeSpan, Task>? sleep = null)
    {
        _maxAttempts = maxAttempts;
        _baseDelay = baseDelay;
        _cap = cap;
        _sleep = sleep ?? Task.Delay;                  // prod: Task.Delay
    }

    public async Task<T> RunAsync<T>(
        Func<int, Task<T>> operation,
        Func<Exception, bool> isTransient)
    {
        var attempt = 0;
        while (true)
        {
            try { return await operation(attempt).ConfigureAwait(false); }
            catch (Exception e)
            {
                attempt++;
                if (attempt >= _maxAttempts || !isTransient(e)) throw;

                var backoff = TimeSpan.FromMilliseconds(
                    Math.Min(_cap.TotalMilliseconds,
                             _baseDelay.TotalMilliseconds * Math.Pow(2, attempt)));
                var jittered = TimeSpan.FromMilliseconds(
                    Random.Shared.NextDouble() * backoff.TotalMilliseconds); // full jitter
                await _sleep(jittered).ConfigureAwait(false);
            }
        }
    }

    // 429/408/5xx geçici (RFC 9110 §15 + Google listesi [S6])
    public static bool IsTransient(Exception e) =>
        e is HttpRequestException { StatusCode:
            HttpStatusCode.RequestTimeout or HttpStatusCode.TooManyRequests }
        || (int?)((System.Net.Http.HttpRequestException)e).StatusCode is >= 500;
}
```

**Not:** 503 + Retry-After durumunda sunucu değeri jitter'ı ezer —
`Retry-After` okunup `_sleep` ona göre çağrılmalıdır (error-sozlesme §5).

---

## §9 Rust

```rust
// Dil: Rust — Rol: Result-tabanlı güvenli retry (? operator ile yayılım)
// Formül: sleep = rand × min(cap, base × 2^attempt)  [full jitter — S1]

use rand::Rng;
use std::time::Duration;

pub struct RetryPolicy {
    pub max_attempts: u32,     // AWS default 3 [S1]
    pub base_delay: Duration,  // referans 1s
    pub cap: Duration,         // AWS cap 20s [S1]
}

impl RetryPolicy {
    /// Clock soyutlaması: `sleep` fonksiyonu çağrıcıdan gelir (test: kayıt).
    pub fn run<T, E, F, S, G>(
        &self,
        mut op: F,
        is_transient: G,
        mut sleep: S,
    ) -> Result<T, E>
    where
        F: FnMut(u32) -> Result<T, E>,
        G: Fn(&E) -> bool,
        S: FnMut(Duration),
    {
        let mut attempt: u32 = 0;
        loop {
            match op(attempt) {
                Ok(v) => return Ok(v),
                Err(e) => {
                    attempt += 1;
                    if attempt >= self.max_attempts || !is_transient(&e) {
                        return Err(e);              // son hata yükseltilir
                    }
                    let backoff_ms = (self.base_delay.as_millis() as u64)
                        .saturating_mul(1u64 << attempt)
                        .min(self.cap.as_millis() as u64);
                    let jittered =
                        rand::thread_rng().gen_range(0..=backoff_ms);  // full jitter
                    sleep(Duration::from_millis(jittered));
                }
            }
        }
    }
}
```

---

## §10 C++

```cpp
// Dil: C++20 — Rol: Yardımcı servis istemcisi (std::variant hata, clock soyutlama)
// Formül: sleep = rand01 × min(cap, base × 2^attempt)  [full jitter — S1]

#include <algorithm>
#include <cstdint>
#include <functional>
#include <random>
#include <variant>

struct TransientError { int status; };     // 408/429/5xx (Google listesi [S6])
struct PermanentError { int status; };     // 4xx çoğu
using CallResult = std::variant<TransientError, PermanentError>;

struct RetryPolicy {
    std::uint32_t max_attempts = 3;        // AWS default [S1]
    std::uint64_t base_delay_ms = 1000;
    std::uint64_t cap_ms = 20000;          // AWS cap [S1]

    // Clock soyutlaması: çağrıcı sleep fonksiyonu verir (test edilebilir)
    template <class Op, class Sleep>
    CallResult run(Op&& op, Sleep&& sleep) const {
        std::mt19937_64 rng{std::random_device{}()};
        std::uint32_t attempt = 0;
        for (;;) {
            CallResult r = op(attempt);
            if (std::holds_alternative<PermanentError>(r)) {
                return r;                                    // kalıcı: retry yok
            }
            ++attempt;
            if (attempt >= max_attempts) return r;           // cap doldu
            const std::uint64_t backoff =
                std::min(cap_ms, base_delay_ms << attempt);  // base × 2^attempt
            std::uniform_int_distribution<std::uint64_t> dist(0, backoff);
            sleep(dist(rng));                                // full jitter
        }
    }
};
```

**Not:** Google C++ istemcisi varsayılan: initial 1s, multiplier 2, max wait
5 dk, max süre 15 dk; 408/429/500/502/503/504 retry'lenebilir [S6].

---

## §11 Ruby

```ruby
# Dil: Ruby — Rol: Otomasyon/ayar-betiği istemcisi
# Formül: sleep = rand × min(cap, base × 2^attempt)  [full jitter — S1][S2]
# Ruby örneği: Stripe SDK max_network_retries = 2 [S8]

class RetryPolicy
  def initialize(max_attempts: 3, base_delay_ms: 1000, cap_ms: 20_000, &sleeper)
    @max_attempts = max_attempts       # AWS default [S1]
    @base_delay_ms = base_delay_ms
    @cap_ms = cap_ms                   # AWS cap [S1]
    # Clock soyutlaması: test'te ->(ms) { kaydet }, prod'ta Kernel.sleep
    @sleeper = sleeper || ->(ms) { sleep(ms / 1000.0) }
  end

  def call
    attempt = 0
    begin
      yield attempt
    rescue StandardError => e
      attempt += 1
      raise if attempt >= @max_attempts || !transient?(e)
      backoff = [@cap_ms, @base_delay_ms * (2**attempt)].min
      @sleeper.call(rand * backoff)          # full jitter
      retry
    end
  end

  private

  def transient?(error)
    status = error.respond_to?(:http_status) ? error.http_status : 0
    [408, 429].include?(status) || status >= 500   # RFC 9110 §15 + Google [S7]
  end
end
```

---

## §12 Kotlin

```kotlin
// Dil: Kotlin — Rol: Coroutine istemci (sealed class hata modeli)
// Formül: sleep = random × min(cap, base × 2^attempt)  [full jitter — S1]

import kotlin.random.Random
import kotlinx.coroutines.delay

sealed class CallError {
    data class Transient(val status: Int, val retryAfterMs: Long? = null) : CallError()
    data class Permanent(val status: Int) : CallError()
}

class RetryPolicy(
    private val maxAttempts: Int = 3,        // AWS default [S1]
    private val baseDelayMs: Long = 1_000,
    private val capMs: Long = 20_000,        // AWS cap [S1]
    private val sleep: suspend (Long) -> Unit = { delay(it) },  // clock soyutlaması
) {
    suspend fun <T> run(
        op: suspend (Int) -> Result<T>,
    ): Result<T> {
        var attempt = 0
        while (true) {
            val r = op(attempt)
            val err = r.exceptionOrNull()
            if (err == null) return r
            val cls = classify(err)
            attempt++
            if (cls is CallError.Permanent || attempt >= maxAttempts) {
                return Result.failure(err)                  // son hata yükseltilir
            }
            // Retry-After varsa sunucu kuralı ezer (error-anayasa §11.3)
            val serverDelay = (cls as? CallError.Transient)?.retryAfterMs
            if (serverDelay != null) { sleep(serverDelay); continue }
            val backoff = minOf(capMs, baseDelayMs shl attempt)
            sleep(Random.nextLong(backoff + 1))             // full jitter
        }
    }

    private fun classify(e: Throwable): CallError { /* status okuma */ TODO() }
}
```

---

## §13 Swift

```swift
// Dil: Swift 5.9 — Rol: iOS istemci (throws, struct politika)
// Formül: sleep = Double.random × min(cap, base × 2^attempt)  [full jitter — S1]

import Foundation

struct RetryPolicy {
    let maxAttempts: Int        // AWS default 3 [S1]
    let baseDelayMs: Double     // referans 1s
    let capMs: Double           // AWS cap 20s [S1]
    // Clock soyutlaması: production'da Task.sleep; test'te sahte
    let sleep: (Double) async throws -> Void

    init(maxAttempts: Int = 3, baseDelayMs: Double = 1000, capMs: Double = 20000,
         sleep: @escaping (Double) async throws -> Void = { ms in
             try await Task.sleep(nanoseconds: UInt64(ms * 1_000_000))
         }) {
        self.maxAttempts = maxAttempts
        self.baseDelayMs = baseDelayMs
        self.capMs = capMs
        self.sleep = sleep
    }

    func run<T>(_ op: (Int) async throws -> T,
                isTransient: (Error) -> Bool) async throws -> T {
        var attempt = 0
        while true {
            do { return try await op(attempt) }
            catch {
                attempt += 1
                if attempt >= maxAttempts || !isTransient(error) { throw error }
                let backoff = min(capMs, baseDelayMs * pow(2, Double(attempt)))
                let jittered = Double.random(in: 0...backoff)   // full jitter
                try await sleep(jittered)
            }
        }
    }
}
```

---

## §14 Elixir

```elixir
# Dil: Elixir — Rol: Hataya-dayanıklı servis (GenServer + backoff)
# Formül: sleep = rand × min(cap, base × 2^attempt)  [full jitter — S1][S2]

defmodule RetryPolicy do
  @moduledoc """
  Full jitter: `min(cap, base * 2^attempt)` üstüne `:rand.uniform/1` ile bölünür.
  Clock soyutlaması: `:timer.sleep/1` çağrı yerine enjekte edilebilir (test).
  """

  @max_attempts 3   # AWS default [S1]
  @base_ms 1_000
  @cap_ms 20_000    # AWS cap [S1]

  def run(op, is_transient, sleeper \\ &(:timer.sleep/1)) do
    do_run(op, is_transient, sleeper, 0)
  end

  defp do_run(op, is_transient, sleeper, attempt) do
    case op.(attempt) do
      {:ok, value} ->
        {:ok, value}

      {:error, reason} = err ->
        cond do
          attempt + 1 >= @max_attempts -> err           # cap doldu → son hata
          not is_transient.(reason) -> err              # kalıcı → retry yok
          true ->
            backoff = min(@cap_ms, @base_ms * Integer.pow(2, attempt + 1))
            jittered = :rand.uniform(backoff)           # full jitter (1..backoff)
            sleeper.(jittered)
            do_run(op, is_transient, sleeper, attempt + 1)
        end
    end
  end
end

# 429 + Retry-After: sunucu değeri jitter'ı ezer (error-anayasa §11.3)
# Graph örneği: Retry-After: 10 [S9]
```

**Not:** OTP'de circuit breaker ayrıca `:breaker`/`:fuse` gibi kütüphanelerle
ya da GenServer state'i ile kurulur; bu dosyada bağımlılık eklenmez (YAGNI).

---

## §15 Dil-Karşılaştırması — Aynı Formülün Karşılığı

| Kavram | PHP | JS | TS | Py | Go | Java | C# | Rust | C++ | Ruby | Kotlin | Swift | Elixir |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Hata modeli | Throwable | Error | unknown | Exception | error return | Exception | Exception | Result | variant | StandardError | sealed | throws | tagged tuple |
| Clock soyutlaması | callable | Promise | Promise fn | Callable | func field | LongConsumer | Func | closure | template | lambda/proc | suspend fn | closure | function arg |
| Rastgelelik | random_int | Math.random | Math.random | random | math/rand | ThreadLocalRandom | Random.Shared | rand | mt19937 | Kernel#rand | Random | Double.random | :rand |
| Cap uygulaması | min() | Math.min | Math.min | min() | if | Math.min | Math.Min | saturating+min | std::min | Array#min | minOf | min() | min/2 |
| Retry-After ezer | header oku | header oku | readRetryAfter | header oku | header oku | header oku | header oku | header oku | header oku | header oku | retryAfterMs | header oku | header oku |

---

## §16 Kaynak / Dosya-Satır Ayrımı

| Konu | Dosya | Satır |
|---|---|---|
| Formül + AWS parametreleri | `error-anayasa.md` §4 | §4.1–§4.2 |
| Sınıflandırma (transient/permanent/subtle) | `error-anayasa.md` §2–§3 | — |
| Eşleme tablosu | `error-sozlesme.md` §3 | §3.2–§3.4 |
| Kapılar/testler | `error-dogrulama.md` §2–§10 | D-1..D-9 |
| Kaynak fetch | `error-web-arastirma.md` §3 | [S#] |

---

## §17 Kaynak Tablosu

| # | Kaynak | URL | Erişim | Durum |
|---|---|---|---|---|
| S1 | AWS SDK retry behavior (max_attempts 3; base 50ms/1000ms; cap 20000ms; full jitter) | https://docs.aws.amazon.com/sdkref/latest/guide/feature-retry-behavior.html | 2026-10-16 | ✅ çekildi |
| S2 | AWS Architecture Blog — Exponential Backoff And Jitter (Full/Equal/Decorrelated) | https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/ | 2026-10-16 | ✅ çekildi |
| S3 | RFC 9110 (§9.2.2 idempotent; §15 status kodları) | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ çekildi |
| S4 | RFC 6585 §4 (429 Too Many Requests) | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ✅ çekildi |
| S5 | Azure Retry pattern (idempotency; nested retry; fail-fast) | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | 2026-10-16 | ✅ çekildi |
| S6 | Google Cloud Storage retry strategy (C++/Go defaultları; retry'lenebilir kodlar) | https://cloud.google.com/storage/docs/retry-strategy | 2026-10-16 | ✅ çekildi |
| S7 | Google Cloud Storage retry strategy (Python/Java notları — aynı sayfa) | https://cloud.google.com/storage/docs/retry-strategy | 2026-10-16 | ✅ çekildi (S6 ile aynı URL) |
| S8 | Stripe rate limits (Ruby SDK max_network_retries=2; jitter tavsiyesi) | https://docs.stripe.com/rate-limits | 2026-10-16 | ✅ çekildi |
| S9 | Microsoft Graph throttling (Retry-After:10 örneği) | https://learn.microsoft.com/en-us/graph/throttling | 2026-10-16 | ✅ çekildi |
| S10 | Azure Circuit Breaker pattern (durum makinesi — §14 Elixir notu) | https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker | 2026-10-16 | ✅ çekildi |

> Not: S6 ve S7 aynı URL'dir (farklı bölümler) — bu dosyada iki kez
> listelenir çünkü iki farklı iddia (C++ defaultları / Python-Java notu)
> taşır; benzersiz URL sayısı §A'da sayılırken birleştirilir.

---

## §18 Red Listesi — Bu Dosyada Yazılmayanlar

| Madde | Neden yazılmadı |
|---|---|
| CoreMusic retry max_attempts değeri | ⚠️ UNKNOWN — politika onayı yok (anayasa §4.2) |
| Circuit breaker tam implementasyonu (13 dilde) | Kapsam: retry+jitter birincil; circuit breaker kavramı anayasa §5 + doğrulama D-7'de |
| Kütüphane seçimi (Polly vs Failsafe vs resilience4j) | YAGNI + karar yok; örnekler bağımsız yazıldı |
| Uydurma API/metrik adları | Zero-hallucination |
| Gözleme/telemetri kodu (metrik gönderimi) | K017 sınırı (anayasa §9) |

---

## §A Kapılar (bu dosya)

| # | Kapı | Durum |
|---|---|---|
| 1 | CoreMusic dil kapsamı (hangi diller implement edilecek) → ⚠️ UNKNOWN | FM open_questions |
| 2 | Parametreler referans (AWS/Google defaultları); CoreMusic politikası onay bekliyor | FM open_questions |
| 3 | Örnekler production kütüphanesi değildir — şablondur | §0 |
| 4 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
