---
title: "K016-observability — Kod Örnekleri"
node_id: K016-observability/observability-kod-ornekleri.md
node_type: subdomain
status: draft
parent_id: K016-observability/index.md
children: []
depends_on:
  - K012-services
  - K010-network
  - K014-media
  - K17-devops
evidence:
  - "shared/src/Log/LoggerFactory.php:11 · 2026-10-18"
  - "shared/src/Log/FileHandler.php:30/40/52/54/125/188/207 · 2026-10-18"
  - "shared/src/Log/Redactor.php:25/28/31/40/49/56/65 · 2026-10-18"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php:140/165 (yazım yolu maskesi) · 2026-10-18"
validation: "2026-10-18 — PHP blokları grep ile doğrulanmış satır pencereleri; diğer diller iskele (şablon)"
open_questions: "⚠️ iskelelerin production karşılığı UNKNOWN · ⚠️ metrik/tracing/health/alert kodu diskte yok (D-07…D-10)"
group: kod
---

# K016-observability — Kod Örnekleri

## §1 ELI15

Bu dosya gözlemleme katmanının **kod örneklerini** gösterir. PHP blokları diskten
gelir (dosya + satır numarasıyla): fabrikanın kurulumu, yazıcının kapıları ve
maskenin kendisi. Diğer diller (JS, TS, Python, C, C++, C#, Go, Rust, Java, SQL,
PowerShell) aynı kuralların — **seviye kapısı, maske, traceId, fail-closed** —
o dilde nasıl görüneceğinin **iskeleleridir**. İskele üretim kodu değildir; kod
yüzeyine yalnız kanıtla girer.

## §2 Kural

- Her blok dil etiketlidir, yorumludur (15–60 satır).
- PHP = disk kanıtı (satır uydurulmaz). Diğerleri = şablon (⚠️ §16).
- Maske (`[REDACTED]`) hiçbir dilde atlanmaz (D-04).

## §3 PHP 8.4 — Disk Örnekleri

### §3.1 LoggerFactory (`shared/src/Log/LoggerFactory.php:11`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Log;

/**
 * Logger kurulumunun tek kaynağı.
 * KANIT: LoggerFactory.php:11 (final class LoggerFactory)
 */
final class LoggerFactory
{
    // KANIT: 11 — fabrika yöntemi; dağıtık new yok (D-01).
    public static function make(string $logDir, string $minLevel = 'info'): FileHandler
    {
        // Tek konfigürasyon noktası: tüm çağırıcılar aynı kapıyı alır (D-01).
        return new FileHandler($logDir, $minLevel);
    }
}
```

### §3.2 FileHandler — seviye kapısı + PSR-3 (`shared/src/Log/FileHandler.php:30/40/52/125/188`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Log;

use Psr\Log\LoggerInterface;

/**
 * PSR-3 dosya yazıcısı: seviye kapısı + güvenlik akışı.
 * KANIT: FileHandler.php:30 (class) · 40/52 (seviye) · 125 (log) · 188 (logAt)
 */
final class FileHandler implements LoggerInterface  // :30 — PSR-3 (D-02)
{
    // :40 seviye sırası · :52 uyarı ailesi (D-03/D-05)
    private const LEVELS = ['debug' => 100, 'info' => 200, 'warning' => 300, 'error' => 400];
    private const WARNING_FAMILY = ['warning', 'error', 'critical', 'alert', 'emergency'];

    public function __construct(
        private string $logDir,
        private string $minLevel = 'info',   // :54 — kapı (D-03)
        private ?bool $debugMode = null,
    ) {}

    public function log($level, string|Stringable $message, array $context = []): void
    {
        // :125 — PSR-3 girişi; bilinmeyen seviye error'a düşer (test :68).
        $this->logAt((string)$level, (string)$message, $context);
    }

    private function logAt(string $level, string $message, array $context, bool $securityStream = false): void
    {
        // :188 — güvenlik akışı ayrımı (D-05); önce maske, sonra yaz (D-04).
        $line = Redactor::redact($this->format($level, $message, $context));
        $this->write($line, $securityStream);   // :207 yazım yolu
    }
}
```

### §3.3 Redactor — maske kalıpları (`shared/src/Log/Redactor.php:25/28/31/65`)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Log;

/**
 * Hassas veri maskeleyici — tek giriş noktalı.
 * KANIT: Redactor.php:25 (class) · 28 (MASK) · 31 (SENSITIVE_KEYS) · 65 (redact)
 */
final class Redactor                 // :25
{
    public const MASK = '[REDACTED]';   // :28 — evrensel maske (D-04)

    // :31 — anahtar listesi: şifre, token, secret, cookie, authorization…
    private const SENSITIVE_KEYS =
        'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';

    // :65 — tek giriş: her satır (yazım yolu) buradan geçer.
    public static function redact(string $line): string
    {
        // anahtar=değer · 64-char hash (UUID korunur) · e-posta (alan adı kalır)
        $line = preg_replace(self::keyValuePattern(), self::MASK, $line);
        $line = preg_replace(self::hashPattern(), self::MASK, $line);
        return preg_replace(self::emailPattern(), self::MASK, $line);
    }
}
```

## §4 JavaScript (Node) — Merkezi logger (iskele)

```javascript
// K016 iskeleti (şablon): seviye kapısı + maske.
// Kural D-03/D-04: minLevel kapı, hassas anahtar maskelenir.
'use strict';

const LEVELS = { debug: 100, info: 200, warning: 300, error: 400 };
const MASK = '[REDACTED]';
const SENSITIVE = /^(password|token|secret|authorization|cookie)$/i;

function redactContext(ctx) {
  // D-04: maske derin — iç içe nesnelerde de uygulanır.
  const out = {};
  for (const [k, v] of Object.entries(ctx)) {
    out[k] = SENSITIVE.test(k) ? MASK : (v && typeof v === 'object' ? redactContext(v) : v);
  }
  return out;
}

function createLogger(minLevel = 'info', stream = process.stdout) {
  const gate = LEVELS[minLevel] ?? LEVELS.info;   // D-03 kapı
  return {
    log(level, msg, ctx = {}) {
      if ((LEVELS[level] ?? 0) < gate) return;    // D-03: alt seviye elenir
      stream.write(JSON.stringify({ level, msg, ...redactContext(ctx) }) + '\n');
    },
  };
}

module.exports = { createLogger, redactContext };
```

## §5 TypeScript — Tip güvenli seviye + context

```typescript
// K016 iskeleti (şablon): seviye union + traceId.
// Kural D-03/D-06: bilinmeyen seviye compile hatası, satıra traceId.

export type Level = 'debug' | 'info' | 'notice' | 'warning' | 'error' | 'critical';

const LEVEL_ORDER: Record<Level, number> = {
  debug: 100, info: 200, notice: 250, warning: 300, error: 400, critical: 500,
};

export interface LogEntry {
  ts: string;
  level: Level;
  traceId: string;        // D-06: korelasyon
  msg: string;
  ctx?: Record<string, unknown>;
}

export function shouldEmit(level: Level, minLevel: Level): boolean {
  // D-03: kapı — seviye sırasına göre (compile güvenli).
  return LEVEL_ORDER[level] >= LEVEL_ORDER[minLevel];
}

export function formatLine(entry: LogEntry): string {
  return JSON.stringify(entry);   // maske çağıran tarafta (D-04)
}
```

## §6 Python — Structured log + redaction

```python
# K016 iskeleti (şablon): yapılandırılmış log + maske.
# Kural D-04/D-06: hassas anahtar maskelenir, traceId gömülür.
import json
import re
from datetime import datetime, timezone

MASK = "[REDACTED]"
SENSITIVE_KEYS = re.compile(
    r"^(password|token|secret|authorization|cookie|api_key)$", re.I
)
LEVELS = {"debug": 100, "info": 200, "warning": 300, "error": 400}


def redact(obj):
    """D-04: sözlük içini maskeler — derinlemesine."""
    if isinstance(obj, dict):
        return {k: (MASK if SENSITIVE_KEYS.match(k) else redact(v))
                for k, v in obj.items()}
    return obj


def emit(level: str, msg: str, ctx: dict, trace_id: str, min_level: str = "info") -> str | None:
    if LEVELS.get(level, 0) < LEVELS.get(min_level, 200):   # D-03 kapı
        return None
    line = json.dumps({
        "ts": datetime.now(timezone.utc).isoformat(),
        "level": level, "trace_id": trace_id,   # D-06
        "msg": msg, **redact(ctx),              # D-04
    }, ensure_ascii=False)
    return line
```

## §7 C — Düşük seviyeli seviye kapısı

```c
/* K016 iskeleti (şablon): seviye kapısı + güvenli yazım.
 * Kural D-03: minLevel altında sessiz geç; asla taşma yok. */
#include <stdio.h>
#include <string.h>

typedef enum { L_DEBUG = 100, L_INFO = 200, L_WARNING = 300, L_ERROR = 400 } level_t;

static const char *level_name(level_t lv) {
    switch (lv) {
        case L_DEBUG:   return "debug";
        case L_INFO:    return "info";
        case L_WARNING: return "warning";
        default:        return "error";
    }
}

/* D-03: kapı — level < min ise yazma. Çıkış buffer'a snprintf ile (taşma yok). */
int log_emit(FILE *out, level_t level, level_t min_level, const char *trace_id, const char *msg) {
    char line[512];
    if (level < min_level) {
        return 0;   /* elendi */
    }
    int n = snprintf(line, sizeof line, "[%s] trace=%s %s\n",
                     level_name(level), trace_id, msg);
    if (n < 0 || (size_t)n >= sizeof line) {
        return -1;  /* taşma engellendi (fail-closed) */
    }
    fputs(line, out);
    return 1;
}
```

## §8 C++ — RAII logger (stream guard)

```cpp
// K016 iskeleti (şablon): RAII ile log satırı garantisi.
// Kural D-05/D-06: güvenlik akışı + traceId; scope çıkınca satır yazılır.
#include <fstream>
#include <string>
#include <utility>

class ScopedLog {
public:
    ScopedLog(std::ofstream& out, std::string level, std::string traceId)
        : out_(out), level_(std::move(level)), trace_(std::move(traceId)) {}

    ~ScopedLog() noexcept {
        // RAII: hata yutsa da satır (trace + seviye) çıkar (D-06).
        try {
            out_ << "[" << level_ << "] trace=" << trace_ << " " << msg_ << "\n";
            out_.flush();
        } catch (...) {
            // log yazımı asla çağıranı çökertmez (yutma + işaretleme).
        }
    }

    ScopedLog& operator<<(const std::string& m) { msg_ += m; return *this; }

    ScopedLog(const ScopedLog&) = delete;
    ScopedLog& operator=(const ScopedLog&) = delete;

private:
    std::ofstream& out_;
    std::string level_, trace_, msg_;
};
```

## §9 C# — ILogger + maske (DI)

```csharp
// K016 iskeleti (şablon): maskeli logger sarmalayıcı.
// Kural D-04: hassas alanlar yazılmadan önce REDACTED olur.
using System;
using System.Collections.Generic;

namespace CoreMusic.Observability;

public sealed class MaskedLogger
{
    private static readonly HashSet<string> Sensitive = new(StringComparer.OrdinalIgnoreCase)
        { "password", "token", "secret", "authorization", "cookie" };

    private readonly ILogger _inner;

    public MaskedLogger(ILogger inner) => _inner = inner;

    public void Log(Level level, string message, IReadOnlyDictionary<string, object>? ctx = null)
    {
        // D-04: maske — sadece hassas anahtarlar değiştirilir.
        var safe = new Dictionary<string, object>();
        if (ctx is not null)
            foreach (var kv in ctx)
                safe[kv.Key] = Sensitive.Contains(kv.Key) ? "[REDACTED]" : kv.Value;

        _inner.Write(level, message, safe);   // D-01/D-02: DI üzerinden merkezi logger
    }
}
```

## §10 Go — logrus benzeri + traceId

```go
// K016 iskeleti (şablon): seviye + traceId + maske.
// Kural D-03/D-06: min seviye altı yazılmaz, satır trace taşır.
package observability

import (
	"encoding/json"
	"os"
	"time"
)

var levelOrder = map[string]int{"debug": 100, "info": 200, "warning": 300, "error": 400}

type Entry struct {
	TS      string `json:"ts"`
	Level   string `json:"level"`
	TraceID string `json:"trace_id"`
	Msg     string `json:"msg"`
}

// Emit: minLevel altında nil döner (D-03); traceId satıra gömülür (D-06).
func Emit(level, minLevel, traceID, msg string) ([]byte, error) {
	if levelOrder[level] < levelOrder[minLevel] {
		return nil, nil
	}
	b, err := json.Marshal(Entry{
		TS: time.Now().UTC().Format(time.RFC3339),
		Level: level, TraceID: traceID, Msg: msg,
	})
	if err != nil {
		return nil, err // fail-closed: hata yutulmaz
	}
	b = append(b, '\n')
	_, _ = os.Stdout.Write(b)
	return b, nil
}
```

## §11 Rust — maske tipi (ownership güvenli)

```rust
// K016 iskeleti (şablon): maskeleme — &str alır, String döner.
// Kural D-04: hassas anahtar değeri asla ham yazılmaz.
use std::collections::HashMap;

pub const MASK: &str = "[REDACTED]";

fn sensitive(key: &str) -> bool {
    // D-04: anahtar kontrolü (case-insensitive).
    matches!(key.to_ascii_lowercase().as_str(),
        "password" | "token" | "secret" | "authorization" | "cookie")
}

pub fn redact_fields(fields: &HashMap<String, String>) -> HashMap<String, String> {
    fields
        .iter()
        .map(|(k, v)| {
            let val = if sensitive(k) { MASK.to_string() } else { v.clone() };
            (k.clone(), val)
        })
        .collect()
}

pub fn should_emit(level: &str, min: &str, order: &[&str]) -> bool {
    let idx = order.iter().position(|l| l == &level);
    let midx = order.iter().position(|l| l == &min);
    matches!((idx, midx), (Some(i), Some(m)) if i >= m) // D-03 kapı
}
```

## §12 Java — SLF4J bağlam maskesi

```java
// K016 iskeleti (şablon): MDC (traceId) + bağlam maskesi.
// Kural D-06/D-04: traceId MDC'ye, hassas alan maskelenir.
package com.coremusic.observability;

import java.util.HashMap;
import java.util.Map;

public final class LogContext {

    private static final String MASK = "[REDACTED]";
    private static final Map<String, Boolean> SENSITIVE = Map.of(
        "password", true, "token", true, "secret", true, "authorization", true
    );

    private final String traceId;

    public LogContext(String traceId) { this.traceId = traceId; }   // D-06

    public Map<String, Object> safe(Map<String, Object> ctx) {
        Map<String, Object> out = new HashMap<>();
        ctx.forEach((k, v) -> out.put(k, SENSITIVE.getOrDefault(k, false) ? MASK : v));  // D-04
        out.put("trace_id", traceId);
        return out;
    }
}
```

## §13 SQL — Log olayı sorgusu (K007-data sınırı)

```sql
-- K016 iskeleti (şablon): gözlemleme sorgusu.
-- Kural: parametreli sorgu zorunlu, SELECT * yasak (yalnız gerekli sütunlar).
-- Örnek: verilen trace_id için olay dizisi (korelasyon, D-06).
SELECT
    e.id,
    e.trace_id,
    e.level,
    e.message,
    e.created_at
FROM app_log_event AS e
WHERE e.trace_id = ?            -- parametreli (SQL kuralı)
  AND e.level IN ('warning', 'error', 'critical')   -- seviye ailesi (D-05)
ORDER BY e.created_at ASC
LIMIT 200;

-- İndeks: (trace_id, level, created_at) — gözlem sorgusu sıralı okur.
```

## §14 PowerShell — CI log kontrolü (11. dil, K17 kesişimi)

```powershell
# K016 iskeleti (şablon): CI'da log sağlığı.
# Kural D-04: hata logunda maske sızıntısı var mı? (fail-closed rapor)
param(
    [Parameter(Mandatory = $true)][string]$LogPath,
    [string[]]$SecretPattern = @('password=', 'token=', 'secret=')
)

$hits = 0
foreach ($line in Get-Content -Path $LogPath) {
    foreach ($p in $SecretPattern) {
        if ($line -match [regex]::Escape($p)) {
            # Sızıntı yakalanırsa: geçiştirmeden say ve çalışmayı durdur (D-04).
            Write-Host "UNMASKED SECRET in log: $p"
            $hits++
        }
    }
}
if ($hits -gt 0) { exit 1 }   # CI gate: maskesiz log = başarısız
exit 0
```

## §15 Diller ve Kural Eşlemesi

| # | Dil | Blok | Kural |
|---|-----|------|-------|
| 1 | PHP 8.4 | §3 (3 blok) | disk: factory/kapı/maske (D-01…D-05) |
| 2 | JavaScript | §4 | derin maske (D-04) |
| 3 | TypeScript | §5 | tip güvenli kapı + traceId (D-03/D-06) |
| 4 | Python | §6 | yapılandırılmış log + maske (D-04) |
| 5 | C | §7 | seviye kapısı + taşma yok (D-03) |
| 6 | C++ | §8 | RAII satır garantisi (D-05/D-06) |
| 7 | C# | §9 | maskeli logger (D-04) |
| 8 | Go | §10 | traceId + kapı (D-03/D-06) |
| 9 | Rust | §11 | maske tipi (D-04) |
| 10 | Java | §12 | MDC traceId + maske (D-06/D-04) |
| 11 | SQL | §13 | korelasyon sorgusu (D-06) |
| 12 | PowerShell | §14 | CI mask gate (D-04, K17) |

## §16 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

- ⚠️ §4–§14 **iskele/şablondur** — diskte production karşılığı **UNKNOWN**;
  taşınma kararı D-07…D-10 (teknoloji ADR) kapatılmadan verilmez.
- ⚠️ §13 `app_log_event` tablosu **iskele** — gerçek şema diskte doğrulanmadı
  (UNKNOWN; uydurma şema production'dır denmez).
- ⚠️ §3 PHP pencereleri 2026-10-18 grep'iyle doğrulandı; dosya değişirse
  güncellenir.

## §17 İlgili Dosyalar

- `observability-bilesenler.md` — C-01…C-03 eşlemesi
- `observability-kararlar.md` — D-01…D-11 kararları
- `observability-test-stratejisi.md` — örneklerin doğrulanması
- `../K17-devops/` — §14 CI kapısı (ci.yml)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
