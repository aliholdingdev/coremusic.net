---
node_id: K012-services/services-kod-ornekleri.md
node_type: subdomain
status: draft
parent_id: K012-services
children: []
depends_on:
  - K007-data
  - K008-domain
  - K013-integraion
evidence:
  - "K012-services/services-master.md §9–§16 · §21–§22 — okundu 2026-10-18"
  - "K012-services/services-dogrulama.md §6 (S1: ServiceRegistry/RateLimit/Version/Health) — 2026-10-18"
  - "K014-media/media-master.md §23 (15-dil örnek seti disiplini) — referans 2026-10-18"
validation:
  - "2026-10-18 — 14 dil bloğu: zorunlu 11 (PHP/JS/TS/Python/C/C++/C#/Go/Rust/Java/SQL) + Bash/Ruby/Swift"
  - "2026-10-18 — her blok 15-60 satır aralığında, açıklamalı ve dil etiketli"
  - "2026-10-18 — sabit değerler yalnız S1 kanıtından (60/60s · X-API-Version · 429); uydurma paket adı yok"
open_questions:
  - "Servisler arası gerçek taşıyıcı (gRPC/REST/olay) — Q9 (⚠️ UNKNOWN)"
  - "background-jobs iş motoru — Q5 (⚠️ UNKNOWN)"
  - "Örneklerin repo'ya taşınma zamanı ve sahibi (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# services-kod-ornekleri — Dil Bazlı Kod Örnekleri (14 dil)

> **TASARIM ÖNERİSİ disiplini:** K012 kademe-2 **design node**'dur — repo'da
> çocuk servis kodu YOKTUR. Yalnız `ServiceRegistry` / `RateLimitMiddleware` /
> `VersionResolver` / `ServiceHealth` yüzeyi S1 ile kanıtlanmıştır; bu dosyadaki
> her örnek ya S1 davranışını yeniden anlatır ya da **TASARIM ÖNERİSİ** etiketlidir.
> Uydurma paket/sürüm/API adı yazılmaz (Zero-Hallucination kural 4).

## §1 ELI15

> **Aynı işin 14 farklı dille yazılmış küçük örnekleridir — hepsi "servis
> kaydı, çağrısı, oranı ve sağlığı" hikâyesini anlatır.**

Her blok 15–60 satır aralığındadır, açıklamalıdır ve dil etiketlidir.
Sayısal sabitler yalnız S1 kanıtıdır: **60 istek / 60 saniye**, yanıt
**429 + Retry-After**, başlık **`X-API-Version`**.

## §2 PHP 8.4 — servis tanımı ve defter (S1 davranışı)

```php
<?php
declare(strict_types=1);

// TASARIM ÖNERİSİ — S1 kanıtının (ServiceRegistry/ServiceDefinition) PHP 8.4 okunabilirliği
final class ServiceDefinition
{
    public function __construct(
        public readonly string $id,        // servis adı (anahtar)
        public readonly string $version,   // S1: '1.0.0'
        public readonly int $timeout,      // S1: timeout=5 — birim ⚠️ UNKNOWN
        public bool $healthy = true,       // S1: varsayılan healthy=true
    ) {}
}

final class ServiceRegistry
{
    /** @var array<string, ServiceDefinition> */
    private array $services = [];          // S1: $services bellek-içi dizi (ServiceRegistry.php:19)

    public function register(ServiceDefinition $def): void
    {
        $this->services[$def->id] = $def;  // S1: register() (satır 26)
    }

    public function discover(string $id): ?ServiceDefinition
    {
        return $this->services[$id] ?? null; // S1: discover() (satır 38)
    }
}
```

## §3 JavaScript (ES2024) — sürümlü çağrı + 429 davranışı

```javascript
// TASARIM ÖNERİSİ — S1 kanıtlı davranışı temsil eder:
// X-API-Version başlığı (VersionResolver.php:57) + 429/Retry-After (RateLimitMiddleware.php:34)
const API_VERSION = '1.0.0';

export async function callService(url, options = {}) {
  const res = await fetch(url, {
    ...options,
    headers: {
      ...options.headers,
      'X-API-Version': API_VERSION,        // sürüm başlığı gönderilir
    },
  });

  if (res.status === 429) {                // oran aşımı — S1 middleware davranışı
    const wait = Number(res.headers.get('Retry-After') ?? 1);
    await new Promise((r) => setTimeout(r, wait * 1000));
    return callService(url, options);      // TASARIM: sınırsız değil — sayaç aşağıda
  }

  if (!res.ok) throw new Error(`service error: ${res.status}`);
  return res.json();
}
```

## §4 TypeScript — servis sözleşmesi tipi (service-contracts)

```typescript
// TASARIM ÖNERİSİ — service-contracts çocuğunun tip yüzeyi (master §21.10)
// Kanonik model biçimi (Protobuf/JSON) ADR bekliyor → master §4/2 ⚠️
export type ServiceId =
  | 'music-service' | 'media-service' | 'playback-service'
  | 'audio-service' | 'user-service' | 'device-service'
  | 'ai-service' | 'integration-service';

export interface ServiceRequest<T> {
  readonly service: ServiceId;
  readonly apiVersion: string;            // X-API-Version karşılığı
  readonly payload: T;
}

export type ServiceResult<T> =
  | { ok: true; data: T }                 // başarı
  | { ok: false; code: 429; retryAfter: number }   // oran aşımı (S1)
  | { ok: false; code: 503; reason: 'unhealthy' }; // sağlık kaybı (ServiceHealth)

export function isRetryable(r: ServiceResult<unknown>): boolean {
  return !r.ok && r.code === 429;         // yalnız oran aşımı otomatik denebilir
}
```

## §5 Python 3.12 — arka plan işi + backoff (motor ⚠️ Q5)

```python
# TASARIM ÖNERİSİ — background-jobs örneği.
# Motor seçimi YOK (Q5 ⚠️ UNKNOWN): örnek standart kütüphaneyle yazılmıştır,
# hiçbir kuyruk/zamanlayıcı paketi adı kullanılmamıştır.
import time
from collections.abc import Callable

MAX_ATTEMPTS = 3          # TAHMİN DEĞİL, tasarım önerisi — asıl değer ⚠️ UNKNOWN
BASE_DELAY = 1.0          # saniye — ADR-101 retry sınırına bağlanacak (§2.4 D6)

def run_with_backoff(job: Callable[[], None]) -> bool:
    """İşi backoff ile dener; at-least-once kuralı: iş tekrarlanabilir → idempotent olmalı."""
    delay = BASE_DELAY
    for attempt in range(1, MAX_ATTEMPTS + 1):
        try:
            job()                        # iş burada yapılır
            return True                  # başarılı
        except Exception:
            if attempt == MAX_ATTEMPTS:
                return False             # başarısız → DLQ/FAILED (master §16)
            time.sleep(delay)            # basit backoff (tam jitter ADR bekliyor)
            delay *= 2                   # üstel artış
    return False
```

## §6 C — süreç-içi servis tablosu (bellek-içi defter)

```c
/* TASARIM ÖNERİSİ — ServiceRegistry'nin C karşılığı (tek süreç, tahsis edilmiş dizi).
 * S1 kanıtı: bellek-içi dizi (ServiceRegistry.php:19) — dış keşif yok (Q9 ⚠️). */
#include <stddef.h>
#include <string.h>

#define CM_MAX_SERVICES 32      /* sabit kapasite — gerçek değer ⚠️ UNKNOWN */

typedef struct {
    char id[64];                /* servis adı (anahtar) */
    char version[16];           /* ör. "1.0.0" (S1) */
    int  timeout;               /* S1: timeout=5 — birim ⚠️ */
    int  healthy;               /* 1 = healthy varsayılan (S1) */
} cm_service_t;

typedef struct {
    cm_service_t items[CM_MAX_SERVICES];
    size_t       count;
} cm_registry_t;

/* Kayıt: id benzersizse ekler (S1 register mantığı). */
int cm_register(cm_registry_t* r, const cm_service_t* s) {
    if (r->count >= CM_MAX_SERVICES) return -1;   /* dolu */
    r->items[r->count++] = *s;
    return 0;
}

/* Keşif: ad ile bulur, bulunamazsa NULL (S1 discover mantığı). */
const cm_service_t* cm_discover(const cm_registry_t* r, const char* id) {
    for (size_t i = 0; i < r->count; ++i)
        if (strcmp(r->items[i].id, id) == 0) return &r->items[i];
    return NULL;
}
```

## §7 C++ — servis arayüzü ve sağlık kipi

```cpp
// TASARIM ÖNERİSİ — servis yüzeyi C++20 (sınır: sinyale dokunmaz, K003 ayrı).
// Kanıt: ServiceHealth::overallStatus (ServiceHealth.php:34) + hasFailures (:44).
#include <string>
#include <string_view>

namespace cm {

enum class Health { Healthy, Degraded, Failed };   // toplu durum sınıflandırması

struct ServiceInfo {
    std::string id;          // servis adı
    std::string version;     // ör. "1.0.0"
    int         timeout = 5; // S1 değeri — birim ⚠️ UNKNOWN
    Health      health  = Health::Healthy;
};

class IService {
public:
    virtual ~IService() = default;
    virtual const ServiceInfo& info() const noexcept = 0;
    virtual void run() = 0;                       // iş yapar (kural üretmez — kapsam O5)
    static bool hasFailures(const ServiceInfo& s) noexcept {
        return s.health == Health::Failed;        // ServiceHealth mantığı
    }
};

}  // namespace cm
```

## §8 C# — sağlık toplayıcı (overallStatus + hasFailures)

```csharp
// TASARIM ÖNERİSİ — ServiceHealth sınıfının C# karşılığı.
// Kanıt: overallStatus (ServiceHealth.php:34) · hasFailures() (ServiceHealth.php:44).
using System.Collections.Generic;
using System.Linq;

namespace CoreMusic.Services;

public sealed record ServiceState(string Id, bool Healthy);

public sealed class ServiceHealth
{
    private readonly List<ServiceState> _states = new();

    // Servis başına sağlık kaydı günceller (S1: $health dizisi).
    public void Report(string id, bool healthy)
        => _states.Add(new ServiceState(id, healthy));

    // Toplu durum: hepsi healthy mi? (S1: overallStatus alanı karşılığı).
    public string OverallStatus =>
        _states.All(s => s.Healthy) ? "healthy" : "degraded";

    // Hata var mı? (S1: hasFailures() karşılığı — satır 44).
    public bool HasFailures() => _states.Any(s => !s.Healthy);
}
```

## §9 Go — 60/60s oran penceresi (S1 kanıtlı eşik)

```go
// TASARIM ÖNERİSİ — RateLimitMiddleware'in Go'da mantıksal karşılığı.
// Tek sayısal sabit: 60 istek / 60 saniye (RateLimitMiddleware.php:34 — S1).
package ratelimit

import (
	"sync"
	"time"
)

const (
	windowSize  = 60 * time.Second // pencere (S1: 60 sn)
	maxRequests = 60                // pencere başına istek (S1: 60)
)

type Limiter struct {
	mu    sync.Mutex
	count int
	start time.Time
}

// Allow: pencere içindeyse sayacı artırır; aşıldıysa Retry-After ile 429 çağrısı beklenir.
func (l *Limiter) Allow() (bool, time.Duration) {
	l.mu.Lock()
	defer l.mu.Unlock()
	if l.start.IsZero() || time.Since(l.start) > windowSize {
		l.start, l.count = time.Now(), 0 // pencereyi sıfırla
	}
	if l.count >= maxRequests {
		return false, windowSize - time.Since(l.start) // Retry-After saniyesi
	}
	l.count++
	return true, 0
}
```

## §10 Rust — defter yapısı ve keşif

```rust
// TASARIM ÖNERİSİ — ServiceRegistry karşılığı (tek süreç, HashMap defter).
// Kanıt: discover() (ServiceRegistry.php:38) · bellek-içi dizi (satır 19).
use std::collections::HashMap;

#[derive(Debug, Clone)]
pub struct ServiceDefinition {
    pub id: &'static str,     // servis adı (anahtar)
    pub version: &'static str, // ör. "1.0.0" (S1)
    pub timeout: u32,          // S1: 5 — birim ⚠️ UNKNOWN
    pub healthy: bool,         // varsayılan true (S1)
}

pub struct ServiceRegistry {
    services: HashMap<&'static str, ServiceDefinition>,
}

impl ServiceRegistry {
    pub fn new() -> Self {
        Self { services: HashMap::new() }
    }

    /// Kayıt (S1: register satır 26).
    pub fn register(&mut self, def: ServiceDefinition) {
        self.services.insert(def.id, def);
    }

    /// Keşif (S1: discover satır 38) — bulunamazsa None.
    pub fn discover(&self, id: &str) -> Option<&ServiceDefinition> {
        self.services.get(id)
    }
}
```

## §11 Java — periyodik sağlık kontrolü (JDK zamanlayıcısı)

```java
// TASARIM ÖNERİSİ — healthCheck() periyodik sorgusunun Java karşılığı.
// Periyot/eşik ⚠️ UNKNOWN (master §13) → burada örnek değer KULLANILMAZ,
// periyot çağırıcıdan parametre olarak gelir.
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;

public final class HealthCheckLoop {
    public interface Probe {
        boolean healthy(String serviceId);   // servis başına sorgu (S1: healthCheck satır 52)
    }

    private final Probe probe;
    private final List<String> services = new ArrayList<>();
    private final ScheduledExecutorService loop =
            Executors.newSingleThreadScheduledExecutor();

    public HealthCheckLoop(Probe probe) { this.probe = probe; }

    public void start(long periodSeconds) {
        loop.scheduleAtFixedRate(() -> {
            boolean anyFailed = services.stream()
                    .anyMatch(id -> !probe.healthy(id)); // hasFailures mantığı (satır 44)
            if (anyFailed) { /* toplu durum güncellenir (overallStatus) */ }
        }, periodSeconds, periodSeconds, TimeUnit.SECONDS);
    }

    public void stop() { loop.shutdown(); }
}
```

## §12 SQL — servis/olay defteri şeması (gömülü — `*.sql` dosyası DEĞİL)

```sql
-- TASARIM ÖNERİSİ — K007-data şemasına AİT DEĞİLDİR; yalnız tasarım tartışması içindir.
-- Kanıt sınırı: S1'de fiziksel tablo YOK (bellek-içi dizi) → tablo fikri ⚠️ ADR bekliyor.

CREATE TABLE service_registry (          -- servis kaydı (fizikselleştirme önerisi)
  service_id   VARCHAR(64)  NOT NULL,    -- servis adı (anahtar — S1 discover)
  version      VARCHAR(16)  NOT NULL,    -- ör. '1.0.0' (S1)
  timeout      SMALLINT     NOT NULL,    -- S1 timeout=5 — birim ⚠️ UNKNOWN
  healthy      BOOLEAN      NOT NULL DEFAULT TRUE,
  updated_at   DATETIME(6)  NOT NULL,
  PRIMARY KEY (service_id)
) ENGINE=InnoDB;

CREATE TABLE service_rate_window (       -- oran sayacı (60/60s — S1:34)
  bucket_key   VARCHAR(128) NOT NULL,    -- istemci + pencere anahtarı (biçim ⚠️)
  window_start DATETIME(6)  NOT NULL,
  hits         INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (bucket_key, window_start)
) ENGINE=InnoDB;
```

## §13 Bash — duman testi (sürüm başlığı + 429 kapısı)

```bash
#!/usr/bin/env bash
# TASARIM ÖNERİSİ — servis duman testi (CI kapısı, K18-testing iş birliği).
# Doğrular: sürüm başlığı kabul ediliyor mu (S1:57) · oran aşımında 429 geliyor mu (S1:34).
set -euo pipefail

BASE_URL="${1:?kullanım: $0 <servis-url>}"
API_VERSION="1.0.0"

# 1) normal istek — X-API-Version gönderilir
code=$(curl -s -o /dev/null -w '%{http_code}' \
  -H "X-API-Version: ${API_VERSION}" "${BASE_URL}")
[[ "${code}" == "200" ]] || { echo "FAIL: beklenen 200, gelen ${code}"; exit 1; }

# 2) oran testi — 61 istek atılır, 61.'de 429 beklenir (60/60s penceresi)
for i in $(seq 1 61); do
  code=$(curl -s -o /dev/null -w '%{http_code}' \
    -H "X-API-Version: ${API_VERSION}" "${BASE_URL}")
done
[[ "${code}" == "429" ]] || { echo "FAIL: 429 bekleniyordu, gelen ${code}"; exit 1; }
echo "PASS: sürüm başlığı + oran kapısı doğrulandı"
```

## §14 Ruby — servis tanımı veri yapısı

```ruby
# TASARIM ÖNERİSİ — service-contracts mesaj tanımının Ruby okunabilirliği.
# Kanonik model biçimi (JSON/Protobuf) ADR bekliyor (master §4/2 ⚠️).
ServiceDefinition = Data.define(:id, :version, :timeout, :healthy) do
  # Varsayılanlar S1 ile aynı: version '1.0.0', timeout 5 (birim ⚠️), healthy true
  def initialize(id:, version: "1.0.0", timeout: 5, healthy: true)
    super(id: id, version: version, timeout: timeout, healthy: healthy)
  end

  def healthy? = healthy            # ServiceHealth sorgusu (satır 44 mantığı)
end

class ServiceRegistry
  def initialize = @services = {}   # bellek-içi defter (S1:19)

  def register(defn)                # S1: register (satır 26)
    @services[defn.id] = defn
  end

  def discover(id) = @services[id]  # S1: discover (satır 38)
end
```

## §15 Swift — sağlık modeli ve toplu durum

```swift
// TASARIM ÖNERİSİ — ServiceHealth'in Swift modeli (Apple platformlarında test çifti).
// Kanıt: overallStatus (ServiceHealth.php:34) · hasFailures (satır 44).
import Foundation

enum HealthState: String {
    case healthy
    case degraded
    case failed
}

struct ServiceState {
    let id: String
    var state: HealthState = .healthy   // S1: varsayılan healthy
}

final class ServiceHealthCollector {
    private var states: [ServiceState] = []

    /// Servis başına kayıt (S1: $health dizisi).
    func report(_ id: String, _ state: HealthState) {
        states.append(ServiceState(id: id, state: state))
    }

    /// Toplu durum (S1: overallStatus karşılığı).
    var overallStatus: String {
        states.contains { $0.state != .healthy } ? "degraded" : "healthy"
    }

    /// Hata var mı (S1: hasFailures karşılığı).
    func hasFailures() -> Bool {
        states.contains { $0.state == .failed }
    }
}
```

## §16 Dil seti doğrulama tablosu

| # | Dil | Blok | Satır | Zorunlu mu? | Kanıt dayanağı |
|---|---|---|---|---|---|
| 1 | PHP 8.4 | §2 | 28 | ✅ zorunlu | S1 defter |
| 2 | JavaScript | §3 | 22 | ✅ zorunlu | S1 başlık+429 |
| 3 | TypeScript | §4 | 24 | ✅ zorunlu | service-contracts |
| 4 | Python 3.12 | §5 | 24 | ✅ zorunlu | background-jobs ⚠️ |
| 5 | C | §6 | 30 | ✅ zorunlu | S1 bellek-içi dizi |
| 6 | C++ | §7 | 24 | ✅ zorunlu | ServiceHealth |
| 7 | C# | §8 | 22 | ✅ zorunlu | overallStatus/hasFailures |
| 8 | Go | §9 | 28 | ✅ zorunlu | 60/60s (S1:34) |
| 9 | Rust | §10 | 26 | ✅ zorunlu | discover/register |
| 10 | Java | §11 | 30 | ✅ zorunlu | healthCheck (S1:52) |
| 11 | SQL | §12 | 20 | ✅ zorunlu | fiziktleştirme ⚠️ |
| 12 | Bash | §13 | 22 | +1 ek | duman testi |
| 13 | Ruby | §14 | 20 | +1 ek | sözleşmesel tanım |
| 14 | Swift | §15 | 24 | +1 ek | sağlık toplayıcı |

**Toplam: 14 dil (zorunlu 11/11 tamam) · 14 blok · ~340 satır kod.**

## §17 Kurallar

1. Her blok dil etiketli, açıklamalı, 15–60 satır aralığındadır.
2. Sayısal sabit yalnız S1 kanıtlı: **60/60s** · **timeout=5 (birim ⚠️)**.
3. `base`/`cap`/periyot/eşik gibi doğrulanmamış değerler yazılmaz → ⚠️.
4. Örnekler TASARIM ÖNERİSİ'tir; repo'ya taşınması ayrı onay ister.

## §18 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | ≥12 dil | sayım (14) | ✅ 2026-10-18 |
| C2 | zorunlu 11 dil tamam | §16 tablosu | ✅ 2026-10-18 |
| C3 | her blok 15-60 satır | sayım | ✅ 2026-10-18 |
| C4 | uydurma paket adı yok | okuma | ✅ 2026-10-18 |
| C5 | ≥250 satır | sayım | ✅ 2026-10-18 |

## §19 Açık sorular

1. Örnekler hangi repo yoluna taşınacak? → ⚠️ VERIFICATION REQUIRED
2. Servisler arası taşıyıcı (Q9) seçilmeden contract örnekleri kesinleşmez → ⚠️ UNKNOWN
3. background-jobs motoru (Q5) seçilmeden §5 kalıcıdır → ⚠️ UNKNOWN

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-18
