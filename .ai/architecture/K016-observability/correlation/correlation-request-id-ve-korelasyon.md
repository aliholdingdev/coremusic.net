---
title: "K016-observability/correlation — Request-ID ve Korelasyon"
node_id: K016-observability/correlation/correlation-request-id-ve-korelasyon
node_type: subdomain
status: draft
parent_id: K016-observability/correlation
children: []
depends_on:
  - K016-observability
  - K012-services
evidence:
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 116 — testTraceId_isStampedOnLines (trace-id damgası)"
  - "shared/src/Log/FileHandler.php · Satır 125 — public function log($level, string|Stringable $message, array $context = []): void"
validation:
  - "Bu turda correlation/ için disk kanıtı YOK — tasarım dosyası, uygulama PLANNED (⚠️ VERIFICATION REQUIRED)"
open_questions:
  - "correlation/ için disk kanıtı sonraki turda"
group: mimari
---


# K016-observability/correlation — correlation-request-id-ve-korelasyon

## §1 Rol (ELI15)

Bir istek (sayfa yükleme, API çağrısı) uygulama boyunca birden çok katmandan geçer. Her katman kendi logunu yazar. **Correlation**, tek bir isteğe ait tüm log satırlarını **aynı kimlikle** bağlar: "bu 500 hatasının tüm izleri nerede?" Sorusunun cevabı. Kimliği **tek noktada** üretiriz (dağıtırsak her servis kendi kimliğini üretir, bağlantı kopar).

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Request-id/trace-id üretimi | ✅ Tek nokta factory | ❌ İstek yönlendirme → K011-application |
| Kimliğin log satırına damgası | ✅ `[trace=4bf92f3577b34da6a3ce929d0e0e4736]` formatı | ❌ Log yazım/redaction → logging/ |
| Kimliğin servisler arası taşınması | ✅ W3C Trace Context şeması | ❌ Servis üretimi mantığı → K012-services |
| Korelasyon kimliği doğrulama | ✅ 32 hex format kontrolü | ❌ Uygulama iş mantığı → K011/K012 |
| Vault audit trail (.ai/log.md) | ❌ Farklı sistem (ajan izi) → §8 | ❌ Vault depolama → K000-vault |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Request-id | request id | Tek isteğin benzersiz kimliği | `CorrelationId::generate()` |
| Trace-id | trace id | Uçtan uca iz kimliği (W3C: 32 hex) | `[trace=4bf92f3577b34da6a3ce929d0e0e4736]` damgası |
| Parent-id | parent span id | Üst span kimliği (W3C: 16 hex) | `parent_id` alanı |
| Korelasyon | correlation | Kimlikle bağlama | satır eşleme |
| Bağlam | context | İstek boyunca taşınan veri | PHP `$context`, Go `context.Context` |
| Propagator | propagator | Kimliği servisler arası taşıyan | W3C Trace Context |
| Damga | stamp | Satıra kimlik yazma | `testTraceId_isStampedOnLines` |
| Traceparent | traceparent | HTTP header (W3C) | `traceparent: 00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-01` |
| Seri | log stream | Kimliğe göre gruplanan satırlar | grep `[trace=4bf92f3577b34da6a3ce929d0e0e4736]` |
| Tek nokta | single source | Tek factory üretimi | LoggerFactory deseni |

## §4 Request-id nedir, neden tek nokta

### §4.1 Tek nokta üretimin gerekçesi

| # | Dağıtık üretim (yanlış) | Tek nokta (doğru) | Sonuç |
|---|---|---|---|
| 1 | Her servis kendi id'sini üretir | Factory bir kez üretir, taşır | Aynı istek = aynı kimlik |
| 2 | Format bozulur (farklı uzunluk) | Format tek yerde sabit | 32 hex tutarlı |
| 3 | Eksilme riski (üretim unutulur) | Zorunlu çağrı | Boş kimlik yok |
| 4 | Güvenlik: dışarıdan girilen id kabul edilir | Üretim + doğrulama | Spoofing önlenir |

**Kanıt (karar):** Trace-id tek noktada üretilir, dağıtılmaz — Psr3LoggerTest.php Satır 116 (`testTraceId_isStampedOnLines`). Bu, master §C/19 kuralının kaynağıdır.

### §4.2 W3C Trace Context formatı

| Alan | Uzunluk | İçerik | Not |
|---|---|---|---|
| trace-id | 32 hex (128 bit) | `00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-01` | Benzersiz iz |
| parent-id | 16 hex (64 bit) | `00f067aa0ba902b7` | Üst span |
| version | 2 hex | `00` | Şema sürümü |
| flags | 2 hex | `01` (sampled) | Örnekleme bayrağı |

**Kural:** trace-id `00000000000000000000000000000000` (all-zero) geçersizdir (W3C yasağı).

## §5 Trace-id üretimi kuralları

| # | Kural | Kaynak |
|---|---|---|
| 1 | trace-id 32 hex, all-zero olamaz | W3C Trace Context |
| 2 | Üretim crypto-random (random_bytes / OsRng) | güvenlik |
| 3 | Dışarıdan gelen traceparent doğrulanır, yoksa üretilir | spoofing önleme |
| 4 | Bozuk traceparent → yeni üretim + uyarı | fail-safe |
| 5 | Kimlik log satırına `[trace=4bf92f3577b34da6a3ce929d0e0e4736]` damgasıyla yazılır | Psr3LoggerTest Satır 116 |
| 6 | Kimlik hassas veri değildir, redaction'dan geçmez | Redactor scope |

## §6 Log satırına damgalama formatı

### §6.1 Damga şeması

| Öğe | Format | Örnek |
|---|---|---|
| Damga | `[trace=4bf92f3577b34da6a3ce929d0e0e4736]` | `2026-10-13 10:00:00 [ERROR] [trace=4bf92f3577b34da6a3ce929d0e0e4736] baglanti koptu` |
| Yerleşim | Satır başında, seviye etiketinden sonra | `2026-10-13 10:00:00 [ERROR] [trace=4bf92f3577b34da6a3ce929d0e0e4736] baglanti koptu` |
| Zorunluluk | Tüm istek kaynaklı satırlar | Vault/ajan logu hariç |

### §6.2 Korelasyon akışı

| # | Adım | Katman | Girdi | Çıktı |
|---|---|---|---|---|
| 1 | Üretim | giriş middleware | yok / header | 32 hex trace-id |
| 2 | Bağlama | context | trace-id | istek boyu taşıma |
| 3 | Damgalama | logger | context | `[trace=4bf92f3577b34da6a3ce929d0e0e4736]` satırı |
| 4 | Taşıma | HTTP client | trace-id | `traceparent` header |
| 5 | Sorgu | log arama | trace-id | eşleşen satırlar |

## §7 ÇOK DİLLİ KOD ÖRNEKLERİ

> **TASARIM ÖRNEĞİ (illüstratif)** — aşağıdaki bloklar repo'da karşılığı OLMAYAN, W3C Trace Context desenini gösteren tasarım örnekleridir. Durum: **PLANNED**. Mevcut tek IMPLEMENTED yüzey logging/ (`shared/src/Log/`)tir.

### §7.1 PHP — TraceId factory + log damgası

```php
<?php
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/correlation — trace-id tek nokta üretimi + PSR-3 context damgalama.
declare(strict_types=1);

namespace CoreMusic\Observability\Correlation;

final class TraceId
{
    private const HEX = '0123456789abcdef';
    private const ALL_ZERO = '00000000000000000000000000000000';

    /** W3C uyumlu 128-bit trace-id (32 hex, all-zero değil). */
    public static function generate(): string
    {
        $raw = random_bytes(16);           // crypto-random
        $id = bin2hex($raw);               // 32 hex
        if ($id === self::ALL_ZERO) {      // W3C yasağı (çok nadir)
            return self::generate();
        }
        return $id;
    }

    /** Doğrulama: dışarıdan gelen traceparent güvenli mi? */
    public static function isValid(string $id): bool
    {
        return strlen($id) === 32
            && ctype_xdigit($id)
            && $id !== self::ALL_ZERO;
    }

    /** Log satırına damga: "[trace=4bf92f3577b34da6a3ce929d0e0e4736]" (Psr3LoggerTest Satır 116 deseni). */
    public static function stamp(string $id, string $message): string
    {
        return sprintf('[trace=%s] %s', $id, $message);
    }
}
```

### §7.2 Python 3.12+ — uuid4 + structlog contextvar

```python
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/correlation — istek boyu trace-id bağlam taşıma (contextvar).
from __future__ import annotations

import uuid
from contextvars import ContextVar

_trace_id: ContextVar[str] = ContextVar("trace_id", default="")


def generate_trace_id() -> str:
    """uuid4 → 32 hex (W3C 128-bit)."""
    return uuid.uuid4().hex


def begin_request(trace_id: str | None = None) -> str:
    tid = trace_id if trace_id and _is_valid(trace_id) else generate_trace_id()
    _trace_id.set(tid)
    return tid


def current_trace_id() -> str:
    return _trace_id.get()


def stamp(message: str) -> str:
    """Log satırına damga (W3C formatı korunur)."""
    return f"[trace={current_trace_id()}] {message}"


def _is_valid(trace_id: str) -> bool:
    return len(trace_id) == 32 and trace_id != "0" * 32
```

### §7.3 Go — crypto/rand + context.Context

```go
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/correlation — context tabanlı trace-id taşıma.
package correlation

import (
	"context"
	"crypto/rand"
	"encoding/hex"
	"fmt"
)

type ctxKey struct{}

// Generate 128-bit (32 hex) W3C trace-id üretir.
func Generate() (string, error) {
	b := make([]byte, 16)
	if _, err := rand.Read(b); err != nil {
		return "", err
	}
	id := hex.EncodeToString(b)
	if id == "00000000000000000000000000000000" {
		return Generate() // W3C all-zero yasağı
	}
	return id, nil
}

// WithTraceID context'e trace-id ekler.
func WithTraceID(ctx context.Context, id string) context.Context {
	return context.WithValue(ctx, ctxKey{}, id)
}

// FromContext context'ten trace-id okur (yoksa boş).
func FromContext(ctx context.Context) string {
	if v, ok := ctx.Value(ctxKey{}).(string); ok {
		return v
	}
	return ""
}

// Stamp log satırına damga ekler.
func Stamp(id, msg string) string {
	return fmt.Sprintf("[trace=%s] %s", id, msg)
}
```

### §7.4 C# — Guid + ActivitySource

```csharp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/correlation — trace-id üretimi + log damgası.
#nullable enable
using System.Diagnostics;

namespace CoreMusic.Observability.Correlation;

public static class TraceId
{
    /// <summary>W3C 128-bit trace-id (32 hex, D harfi yok).</summary>
    public static string Generate()
        => Guid.NewGuid().ToString("N"); // 32 hex

    public static bool IsValid(string id)
        => id.Length == 32 && id != new string('0', 32)
           && id.All(Uri.IsHexDigit);

    public static string Stamp(string id, string message)
        => $"[trace={id}] {message}";
}
```

### §7.5 Rust — OsRng + hex + tracing field

```rust
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/correlation — realtime-dışı trace-id üretimi + tracing field.
use rand::RngCore;

pub fn generate_trace_id() -> String {
    let mut buf = [0u8; 16];
    rand::rngs::OsRng.fill_bytes(&mut buf);
    let id: String = buf.iter().map(|b| format!("{b:02x}")).collect();
    if id == "00000000000000000000000000000000" {
        return generate_trace_id(); // W3C all-zero yasağı
    }
    id
}

pub fn stamp(id: &str, msg: &str) -> String {
    format!("[trace={id}] {msg}")
}
```

### §7.6 JavaScript/TS — AsyncLocalStorage bağlam

```typescript
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/correlation — Node.js istek boyu bağlam (AsyncLocalStorage).
import { AsyncLocalStorage } from "node:async_hooks";
import { randomBytes } from "node:crypto";

interface Ctx { traceId: string }
const store = new AsyncLocalStorage<Ctx>();

export function generateTraceId(): string {
  const id = randomBytes(16).toString("hex");
  return id === "0".repeat(32) ? generateTraceId() : id;
}

export function runWithTrace<T>(traceId: string, fn: () => T): T {
  return store.run({ traceId }, fn);
}

export function currentTraceId(): string {
  return store.getStore()?.traceId ?? "";
}

export function stamp(msg: string): string {
  return `[trace=${currentTraceId()}] ${msg}`;
}
```

### §7.7 Java — W3C propagator + MDC

```java
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/correlation — MDC tabanlı trace-id damgalama (SLF4J).
package com.coremusic.observability;

import io.opentelemetry.api.GlobalOpenTelemetry;
import io.opentelemetry.context.propagation.TextMapGetter;
import io.opentelemetry.context.propagation.TextMapSetter;

public final class TraceContext {
    private TraceContext() {}

    public static String current() {
        return org.slf4j.MDC.get("trace_id");
    }

    public static void stamp(String message) {
        org.slf4j.MDC.put("trace_id", generate());
    }

    private static String generate() {
        return java.util.UUID.randomUUID().toString().replace("-", "");
    }
}
```

### §7.8 C++ — random_device + scoped logger

```cpp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/correlation — trace-id üretimi (realtime callback dışında).
#pragma once
#include <random>
#include <string>
#include <sstream>
#include <iomanip>

namespace cm::obs {

class TraceId {
public:
    static std::string generate() {
        std::random_device rd;
        std::mt19937_64 gen(rd());
        std::uniform_int_distribution<uint64_t> dist;
        uint64_t hi = dist(gen), lo = dist(gen);
        std::ostringstream os;
        os << std::hex << std::setfill('0')
           << std::setw(16) << hi << std::setw(16) << lo;
        return os.str(); // 32 hex
    }

    static std::string stamp(const std::string& id, const std::string& msg) {
        return "[trace=" + id + "] " + msg;
    }
};

} // namespace cm::obs
```

### §7.9 YAML — W3C traceparent header şeması

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# W3C Trace Context — HTTP header şeması (propagasyon).
traceparent: "00-4bf92f3577b34da6a3ce929d0e0e4736-00f067aa0ba902b7-01"
#   |   |              |                                |          |
#   |   |              |                                |          └─ flags (01 = sampled)
#   |   |              |                                └─ parent-id (16 hex)
#   |   |              └─ trace-id (32 hex, all-zero YASAK)
#   |   └─ sürüm (00)
#   └─ ayraç
headers:
  tracestate: ""          # opsiyonel vendor listesi
  x-request-id: ""        # fallback (W3C yoksa)
```

### §7.9 Bash/curl — traceparent propagasyonu (CLI gözlem)

```bash
#!/usr/bin/env bash
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/correlation — CLI çağrısında W3C traceparent üretimi ve propagasyonu.
set -euo pipefail

TRACE_ID="$(od -An -N16 -tx1 /dev/urandom | tr -d ' \n')"   # 32 hex, crypto-random
PARENT_ID="$(od -An -N8 -tx1 /dev/urandom | tr -d ' \n')"    # 16 hex
TRACEPARENT="00-${TRACE_ID}-${PARENT_ID}-01"                 # flags=01 (sampled)

echo "[trace=${TRACE_ID}] istek baslatildi"

curl -sS -X POST 'https://api.example.test/v1/playback' \
  -H "traceparent: ${TRACEPARENT}" \
  -H 'content-type: application/json' \
  -d '{"track_id":"42"}' \
  | tee /dev/stderr \
  | grep -q '"status":"ok"' && echo "[trace=${TRACE_ID}] basarili" \
  || echo "[trace=${TRACE_ID}] hata"
```

### §7.10 YAML — OpenTelemetry collector korelasyon yapılandırması

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/correlation — collector'da trace-id korelasyonu (traceparent işlenir).
receivers:
  otlp:
    protocols:
      grpc:
        endpoint: 0.0.0.0:4317
      http:
        endpoint: 0.0.0.0:4318

processors:
  batch:
    timeout: 5s
  resource:
    attributes:
      - key: service.name
        value: coremusic-web
        action: upsert

exporters:
  logging:
    loglevel: info
    sampling_initial: 100
    sampling_thereafter: 100

service:
  pipelines:
    traces:
      receivers: [otlp]
      processors: [resource, batch]
      exporters: [logging]
```

## §8 Komşu sınır (K012 / K011 / K015-AI / K000)

| # | Komşu | Sınır kuralı | Yön | Kaynak |
|---|---|---|---|---|
| 1 | K012-services | Servis trace-id üretmez, taşır; üretim correlation/ tek nokta | K016 → K012 | Psr3LoggerTest Satır 116 |
| 2 | K011-application | İstek yönlendirme K011'de; correlation yalnız kimlik verir | K016 ↔ K011 | §2 sınır |
| 3 | K000-vault | Vault `.ai/log.md` ajan izi, ürün korelasyonu DEĞİL — ayrılır | ayrım | master §2 sınır notu |
| 4 | K015-AI | `.ai/log.md` girdileri K015-AI otomasyonunun girdisidir | K016 → K015-AI | .ai/log.md Satır 17-20 |
| 5 | logging/ | Damga logging katmanına context olarak verilir, ayrı sistem değil | iç | master §B/16 |

### §8.1 Vault audit trail ≠ ürün korelasyonu

| Yüzey | Kimlik | Amaç | Sahip |
|---|---|---|---|
| Ürün logu (`shared/src/Log`) | `[trace=4bf92f3577b34da6a3ce929d0e0e4736]` | İstek korelasyonu | K016 (bu dosya) |
| Vault (`.ai/log.md`) | tarih+aktör | Ajan/vault izi | K000-vault + K015-AI |

**Kural:** İkisi karıştırılmaz; `.ai/log.md`'ye trace-id damgası yazılmaz (ajan zinciri ürün logu değildir).

## §9 Açık sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etki |
|---|---|---|
| 1 | correlation/ için disk kanıtı var mı (üretim kodu)? | IMPLEMENTED etiketi |
| 2 | traceparent header'ı hangi middleware'de işlenecek? (K011/K016 sınırı) | K011 mutabakatı |
| 3 | Örnekleme oranı (sampling) ne olacak? | maliyet/iz dengesi |
| 4 | Vault/ürün logu sınırı kademe-3'te tescillenecek mi? | master §7/4 |

## §F Web tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OpenTelemetry PHP Traces **STABLE**; PHP 8.0+; W3C Trace Context uyumlu; `OTEL_PHP_AUTOLOAD_ENABLED=true` | https://opentelemetry.io/docs/languages/php/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |
| OpenTelemetry sinyalleri (traces · metrics · logs · baggage) gözlem yapı taşlarıdır | https://opentelemetry.io/docs/concepts/signals/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §G Revizyon doğrulama kaydı

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 504 |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§9 + §F + §G | ✓ |
| 5 | Çok-dilli kod | 11 blok (PHP · Python · Go · C# · Rust · TS · Java · C++ · YAML · Bash · YAML) · her biri TASARIM ÖRNEĞİ işaretli | ✓ |
| 6 | Web tablosu | 2 satır · envanter dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | boş hücre · üç nokta dizgesi · gelecek-sözü · mojibake · angle-bracket yer tutucu | ✓ 0 |
| 8 | Kanıt dürüstlüğü | correlation/ disk kanıtı YOK → tasarım/PLANNED olarak işaretli | ✓ |
| 9 | Git commit | atılmadı | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
