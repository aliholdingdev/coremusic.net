---
node_id: K013-integraion/integraion-kod-ornekleri.md
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on:
  - K007-data
  - K008-domain
  - K009-security
  - K010-network
  - K012-services
evidence:
  - ".ai/architecture/K013-integraion/integration-master.md §14 (webhook doğrulama: Stripe t=,v1= / GitHub sha256=), §16 (API-Adapter), §22.8-§22.9 (kod YOK) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/adr-integration.md ADR-094 (Resilience4j), ADR-101 (Redis Streams), ADR-037 (outbox), ADR-081 (PKCE) — okundu 2026-10-18"
  - ".ai/architecture/K012-services/services-kod-ornekleri.md §2 (PHP ServiceRegistry — sınır karşılaştırması) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 15 dil bloğu, ~330 kod satırı; her blok konuşa biçimli + 'UYGULAMA YOK' notu; §16 dilim tablosu"
open_questions:
  - "⚠️ Bu dosyadaki tüm bloklar SÖYLEM (konuşma biçimi) — K013 uygulamaları fictionel (master §22.7-§22.9: uygulama/kod/motor YOK)."
  - "⚠️ WebhookMotoru kodu YOK (§14.6 tasarım) — §2 blokları öneridir."
  - "⚠️ API-Adapter kodu YOK (§16 fictional) — §3 blokları öneridir."
  - "⚠️ CircuitBreaker/Bulkhead/outbox/retry — konuşma biçimi var; kod YOK (ADR-094/095/037)."
  - "⚠️ Stripe/GitHub imza formatları master §14.1-§14.3'ten (web-arastirma.md §6 ile doğrulandı — 2026-10-18)."
  - "⚠️ OAuth PKCE §12 — ADR-081 DRAFT (1/3) olabilir (kararlar.md §5)."
group: kod
---

# K013 INTEGRAION — Kod Örnekleri (Söylem)

> **UYARI**: K013 entegrasyonlarının uygulamaları YOK (fictional — master §22.7-§22.9). Bu dosyadaki bloklar **konuşa biçimi (söylem)** gösterir; üretim koduna doğrudan taşınmaz.
> Format: konuşa biçimi + dil + etiket + satır aralığı + kaynak (master/ADR §).
> Sınır karşılaştırması: `services-kod-ornekleri.md` (K012 — PHP registry).

## §1 Kod Blokları — Giriş

1. **Amaç**: Her entegrasyon deseninin (webhook doğrulama/outbox/retry/circuit breaker/PKCE/SSE/SOAP/saga) konuşa biçimini göstermek.
2. **Dilim**: §16'da 15 dil × konu tablosu.
3. **UYGULAMA YOK**: K013 fictionel — bloklar tasarım kanıtı, uygulama değil.

## §2 Webhook Doğrulama (§14.1-§14.3)

### 2.1 PHP 8.4 — Stripe Webhook İmza Doğrulama (imzalı raw body) — ~50 satır

```php
<?php
declare(strict_types=1);

// K013 §14.1: Stripe imzası — header: "t=,v1=" · HMAC-SHA256 · raw body ZORUNLU.
// UYGULAMA YOK (fictional — master §22.9). Konuşa biçimi.
final readonly class StripeWebhookVerifier
{
    private const TOLERANCE_SECONDS = 300; // §14.3: 5 dk tolerans

    /** @return array{ok: bool, reason: string|null} */
    public function verify(string $payload, string $signatureHeader, string $secret): array
    {
        $parts = [];
        foreach (explode(',', $signatureHeader) as $pair) {
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, null);
            $parts[$k] = $v;
        }
        if (!isset($parts['t'], $parts['v1'])) {
            return ['ok' => false, reason: 'header eksik (t,v1)'];
        }
        $timestamp = (int) $parts['t'];
        if (abs(time() - $timestamp) > self::TOLERANCE_SECONDS) {
            return ['ok' => false, reason: 'zaman aşımı (>5 dk)'];
        }
        // §14.3: HMAC, TIMESTAMP + "." + RAW BODY üzerinden (raw bytes — JSON encode YOK).
        $signed = $timestamp . '.' . $payload;
        $expected = hash_hmac('sha256', $signed, $secret);
        $ok = hash_equals($expected, $parts['v1']); // zaman sabitli karşılaştırma (K009)
        return ['ok' => $ok, reason: $ok ? null : 'imza eşleşmedi'];
    }
}
```

### 2.2 JavaScript (Node.js) — GitHub X-Hub-Signature-256 — ~35 satır

```js
// K013 §14.2: GitHub imzası — header: "X-Hub-Signature-256: sha256=<hex>".
// UYGULAMA YOK (fictional). Konuşa biçimi.
import { createHmac, timingSafeEqual } from 'node:crypto';

export function verifyGitHubSignature(rawBody, header, secret) {
  if (!header || !header.startsWith('sha256=')) return false; // §14.2: sha256= öneki
  const received = header.slice('sha256='.length);
  const expected = createHmac('sha256', secret).update(rawBody).digest('hex'); // raw body
  const a = Buffer.from(received, 'hex');
  const b = Buffer.from(expected, 'hex');
  return a.length === b.length && timingSafeEqual(a, b); // zaman sabitli
}

// Express benzeri akış (söylem):
// app.post('/webhooks/github', express.raw({ type: 'application/json' }), (req, res) => {
//   if (!verifyGitHubSignature(req.body, req.headers['x-hub-signature-256'], SECRET))
//     return res.status(401).end(); // §14.3: imzalı body raw bytes olmalı
//   queue.add(req.body); res.status(202).end(); // §14.6: kuyruğa bırak
// });
```

### 2.3 Python — HMAC + Zaman Toleransı (Stripe t=,v1=) — ~30 satır

```python
# K013 §14.1: Stripe t=,v1= doğrulama (söylem — UYGULAMA YOK).
import hmac, hashlib, time

TOLERANCE = 300  # §14.3: 5 dk

def verify_stripe(payload: bytes, header: str, secret: str) -> bool:
    parts = dict(p.split("=", 1) for p in header.split(",") if "=" in p)
    if "t" not in parts or "v1" not in parts:
        return False
    ts = int(parts["t"])
    if abs(time.time() - ts) > TOLERANCE:
        return False
    signed = f"{ts}.".encode() + payload  # raw body (bytes) — §14.3
    expected = hmac.new(secret.encode(), signed, hashlib.sha256).hexdigest()
    return hmac.compare_digest(expected, parts["v1"])  # zaman sabitli
```

## §3 Outbox + CDC (ADR-037 · microservices.io §5)

### 3.1 C# — Outbox Writer (DB transaction'ıyla atomik) — ~40 satır

```csharp
// ADR-037: Outbox + Debezium CDC — olay DB transaction'ıyla yazılır, CDC ile yayınlanır.
// UYGULAMA YOK (fictional — K013 §22.8). Konuşa biçimi.
public sealed class OutboxWriter
{
    private readonly AppDbContext _db;
    public OutboxWriter(AppDbContext db) => _db = db;

    public async Task AppendAsync(string eventType, object payload, CancellationToken ct)
    {
        // Domain change + outbox AYNI transaction (iki-adım değil — ADR-037).
        await using var tx = await _db.Database.BeginTransactionAsync(ct);
        _db.OutboxMessages.Add(new OutboxMessage
        {
            Id = Guid.NewGuid(),
            EventType = eventType,           // ADR-084: entity.action.pastTense
            Payload = JsonSerializer.Serialize(payload),
            OccurredAt = DateTimeOffset.UtcNow,
            ProcessedAt = null               // CDC işleyene kadar null
        });
        await _db.SaveChangesAsync(ct);
        await tx.CommitAsync(ct);            // atomik — Debezium outbox'u çeker
    }
}
```

### 3.2 Go — Outbox Poller (CDC yerine basit poller söylemi) — ~35 satır

```go
// ADR-037: outbox — bu blok Debezium yerine poller SÖYLEMİ (alternatif).
// UYGULAMA YOK (fictional). Poller CDC'yi bozmaz; yalnız örnek.
package outbox

type Msg struct {
    ID        string
    EventType string // ADR-084
    Payload   []byte
}

func Poll(ctx context.Context, db *sql.DB, bus Publisher) error {
    rows, err := db.QueryContext(ctx,
        `SELECT id, event_type, payload FROM outbox WHERE processed_at IS NULL
         ORDER BY occurred_at LIMIT 50`)
    if err != nil { return err }
    defer rows.Close()
    for rows.Next() {
        var m Msg
        if err := rows.Scan(&m.ID, &m.EventType, &m.Payload); err != nil { return err }
        if err := bus.Publish(ctx, m.EventType, m.Payload); err != nil { continue } // retry §10
        // işlendi işaretle — idempotent consumer (§6)
        _, _ = db.ExecContext(ctx, `UPDATE outbox SET processed_at = now() WHERE id = $1`, m.ID)
    }
    return rows.Err()
}
```

## §4 Idempotency (Stripe docs §6 · outbox consumer)

### 4.1 SQL — Idempotency Kaydı (event_id UNIQUE) — ~18 satır

```sql
-- §6 (Stripe idempotency) + §4.2: consumer tarafı tekrar-korunma.
-- UYGULAMA YOK (fictional). Konuşa biçimi.
CREATE TABLE integration_idempotency (
    event_id     VARCHAR(128) NOT NULL,      -- webhook event.id / outbox msg id
    handler      VARCHAR(64)  NOT NULL,      -- işleyen (ör. "billing.apply")
    processed_at TIMESTAMPTZ  NOT NULL DEFAULT now(),
    PRIMARY KEY (event_id, handler)          -- aynı event 2× gelirse duplikasyon yok
);

-- Consumer akışı (söylem):
-- BEGIN;
--   INSERT INTO integration_idempotency (event_id, handler) VALUES ($1, $2)
--     ON CONFLICT DO NOTHING;
--   -- conflict olduysa: iş yapmadan çık (zaten işlenmiş)
--   -- değilse: işi yap + COMMIT;
-- COMMIT;
```

## §5 Retry + Exponential Backoff (ADR-095 · Rust)

### 5.1 Rust — Retry with Jitter — ~35 satır

```rust
// ADR-095: Retry (exponential backoff, jitter) + Timeout.
// UYGULAMA YOK (fictional — §22.8). Konuşa biçimi.
use std::{thread::sleep, time::Duration};

pub fn retry<T, E>(mut op: impl FnMut() -> Result<T, E>, max: u32) -> Result<T, E> {
    let mut attempt = 0u32;
    loop {
        match op() {
            Ok(v) => return Ok(v),
            Err(e) => {
                attempt += 1;
                if attempt >= max { return Err(e); } // üst sınır
                let base_ms = 100u64 * 2u64.pow(attempt);   // 200,400,800...
                let jitter = fastrand::u64(0..100);          // jitter (ADR-095)
                sleep(Duration::from_millis(base_ms + jitter));
            }
        }
    }
}
// Kullanım (söylem): retry(|| client.get_musicbrainz(url), 5)
```

## §6 Circuit Breaker (ADR-094 · Java Resilience4j)

### 6.1 Java — Resilience4j CB — ~45 satır

```java
// ADR-094: Resilience4j CircuitBreaker — failureRate=50, wait=30s, window=10.
// UYGULAMA YOK (fictional — §22.8: "konuşa biçimi var; kod YOK").
import io.github.resilience4j.circuitbreaker.CircuitBreaker;
import io.github.resilience4j.circuitbreaker.CircuitBreakerConfig;
import java.time.Duration;

public final class IntegrationCircuitBreaker {
    private final CircuitBreaker cb;

    public IntegrationCircuitBreaker(String name) {
        var cfg = CircuitBreakerConfig.custom()
            .failureRateThreshold(50)                 // §22.8 / ADR-094
            .waitDurationInOpenState(Duration.ofSeconds(30))
            .slidingWindowSize(10)
            .permittedNumberOfCallsInHalfOpenState(3)
            .build();
        this.cb = CircuitBreaker.of(name, cfg);
    }

    public <T> T call(String name, java.util.function.Supplier<T> supplier) {
        // hasta entegrasyon (ör. musicbrainz) =HEMEN fallback (§5.1: "hasta asla veri üretmemeli")
        return CircuitBreaker.executeSupplier(cb, supplier);
        // open ise: CallNotPermittedException → entegrasyon bypass (domain devam)
    }
}
```

## §7 OAuth 2.1 PKCE (ADR-081 draft · TypeScript)

### 7.1 TypeScript — PKCE Code Verifier/Challenge — ~28 satır

```ts
// ADR-081 (draft): OAuth 2.1 + PKCE — code_verifier + S256 challenge.
// UYGULAMA YOK (fictional). Konuşa biçimi.
import { createHash, randomBytes } from 'node:crypto';

export function pkce(): { verifier: string; challenge: string } {
  const verifier = randomBytes(32).toString('base64url');       // 43-128 karakter
  const challenge = createHash('sha256').update(verifier).digest('base64url'); // S256
  return { verifier, challenge };
}

// Yetkilendirme URL'i (söylem):
// /authorize?response_type=code&client_id=...&redirect_uri=...
//   &code_challenge=<challenge>&code_challenge_method=S256&scope=<minimal>  (K009 §4: scope minimizasyonu)
// Token değişiminde: code_verifier gönder — sunucu S256 ile doğrular (PKCE).
```

## §8 API-Adapter — SOAP (§16.7 — Linn LMS söylemi)

### 8.1 C++ — SOAP İstek Sarmalayıcı — ~35 satır

```cpp
// K013 §16.7: SOAP adaptörü (Linn LMS XML-RPC/SOAP benzeri — konuşa biçimi).
// UYGULAMA YOK (fictional). SOAPAction + XML body sarmalar.
#include <string>

namespace integration {
class SoapClient {
public:
    // LMS "browse" benzeri sarmalayıcı (söylem).
    std::string makeBrowseRequest(const std::string& sessionId,
                                  const std::string& id) const {
        std::string body =
            "<?xml version=\"1.0\"?>"
            "<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">"
            "<s:Body><u:browseresult xmlns:u=\"urn:LinnWS:DomainConfig\">"
            "<sid>" + sessionId + "</sid><id>" + id + "</id>"
            "</u:browseresult></s:Body></s:Envelope>";
        return body;
    }
    // Yanıt XML → domain nesnesi ayrıştırma §16.7 (ayrıştırıcı YOK — söylem).
};
} // namespace integration
```

## §9 API-Adapter — GraphQL Proxy (§16.8)

### 9.1 JavaScript — GraphQL Resolver (metadata fetch) — ~30 satır

```js
// K013 §16.8: GraphQL proxy — uzak GraphQL'i domain query'sine köprüler.
// UYGULAMA YOK (fictional). Konuşa biçimi.
export const resolvers = {
  Query: {
    async album(_, { mbid }, { dataSources }) {
      // MusicBrainz/GraphQL köprüsü (§16.8) — domain K008 query.
      const data = await dataSources.remote.graphql(`
        query Album($mbid: ID!) { album(id: $mbid) { title artist { name } tracks { duration } } }
      `, { variables: { mbid } });
      // Domain'e normalize (ADR-084 event adları korunur).
      return {
        id: mbid,
        title: data.album.title,
        artist: data.album.artist.name,
        durationMs: data.album.tracks.reduce((s, t) => s + t.duration, 0),
      };
    },
  },
};
```

## §10 SSE — Entegrasyon Event Akışı (K012 §6.6 paraleli)

### 10.1 PHP 8.4 — SSE + Redis Streams (ADR-101) — ~35 satır

```php
<?php
declare(strict_types=1);

// K012 §6.6 (SSE) + ADR-101 (Redis Streams) — entegrasyon event akışı.
// UYGULAMA YOK (fictional). Konuşa biçimi.
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$lastId = '0-0'; // Redis Stream cursor

while (true) { // proxy timeout için max süresiz döngü yerine sayaç ekle (§6.5)
    $entries = $redis->xRead(['STREAMS' => 'integration-events', $lastId], 1, 1000);
    if ($entries === null || $entries === false) {
        echo ": keep-alive\n\n"; flush(); // §6.5 keep-alive
        continue;
    }
    foreach ($entries['integration-events'] ?? [] as $id => $fields) {
        $lastId = $id;
        echo 'event: ' . ($fields['type'] ?? 'message') . "\n"; // ADR-084 event adı
        echo 'data: ' . json_encode($fields, JSON_UNESCAPED_UNICODE) . "\n\n";
        flush();
    }
}
```

## §11 Saga — Compensating Transaction (microservices.io §5.2 · Go)

### 11.1 Go — Saga Step + Tazmin — ~40 satır

```go
// microservices.io/saga: her adımın TERSİ (compensation) ile birlikte kaydı.
// UYGULAMA YOK (fictional — §22.8: kod YOK). Konuşa biçimi.
package saga

type Step struct {
    Name        string
    Action      func(ctx context.Context) error
    Compensation func(ctx context.Context) error // geri alma
}

func Run(ctx context.Context, steps []Step) error {
    var done []Step
    for _, s := range steps {
        if err := s.Action(ctx); err != nil {
            // geriye doğru tazmin (compensating transactions — §5.2)
            for i := len(done) - 1; i >= 0; i-- {
                _ = done[i].Compensation(ctx) // tazmin hatası logla — §9 (audit)
            }
            return fmt.Errorf("saga %s başarısız: %w", s.Name, err)
        }
        done = append(done, s)
    }
    return nil
}
```

## §12 YouTube Music — Proxy Fallback (§10.2/§22.5)

### 12.1 Python — Invidious/Piped Fallback — ~28 satır

```python
# §22.5: YouTube Music resmi API doğrulanmadı → Invidious/Piped proxy alternatifi.
# UYGULAMA YOK (fictional). Konuşa biçimi.
PROXIES = ["https://inv.example", "https://piped.example"]  # §16.4-§16.5: kaynak doğrulanmadı ⚠️

async def ytm_search(query: str, http) -> dict | None:
    for base in PROXIES:                       # sıralı fallback
        try:
            r = await http.get(f"{base}/api/v1/search", params={"q": query, "type": "video"}, timeout=5)
            if r.status_code == 200:
                return r.json()                # domain'e normalize (K008)
        except Exception:
            continue                           # proxy'yi atla, sıradakine geç
    return None                                # hepsi başarısız → domain devam (K008 cache)
```

## §13 Medya Sunucusu Adaptörü (§5.1-§5.3 · C#)

### 13.1 C# — Jellyfin İstek Sarmalayıcı — ~25 satır

```csharp
// §5.1: Jellyfin entegrasyonu (fictional — KOD YOK). İstek sarmalayıcı söylemi.
public sealed class JellyfinClient
{
    private readonly HttpClient _http;
    private readonly string _baseUrl;   // sunucu URL (K009 secret)
    private readonly string _apiKey;    // K009 secret store

    public async Task<JellyfinLibrary?> GetLibraryAsync(string libraryId, CancellationToken ct)
    {
        var req = new HttpRequestMessage(HttpMethod.Get,
            $"{_baseUrl}/Library/VirtualFolders?recursive=true");
        req.Headers.Add("X-Emby-Token", _apiKey);   // Jellyfin token header
        using var res = await _http.SendAsync(req, ct);
        if (res.StatusCode == System.Net.HttpStatusCode.Unauthorized)
            return null;                             // §20: 401 → domain'e bildir
        res.EnsureSuccessStatusCode();
        return await res.Content.ReadFromJsonAsync<JellyfinLibrary>(ct);
    }
}
```

## §14 Webhook Yeniden Deneme Kuyruğu (§14.6)

### 14.1 PHP 8.4 — Webhook Retry Kuyruğu — ~30 satır

```php
<?php
declare(strict_types=1);

// §14.6: webhook retry kuyruğu — 5xx/timeout → yeniden deneme; 4xx → düşür.
// UYGULAMA YOK (fictional — master §22.9: motor YOK). Konuşa biçimi.
final class WebhookRetryQueue
{
    /** @var array<int, array{payload: string, attempt: int, nextAt: int}> */
    private array $queue = [];

    public function enqueue(string $payload, int $attempt = 0): void
    {
        $this->queue[] = [
            'payload' => $payload,
            'attempt' => $attempt,
            'nextAt'  => time() + (2 ** $attempt) * 60, // exponential: 1,2,4,8 dk (ADR-095)
        ];
    }

    public function process(callable $deliver): void
    {
        foreach ($this->queue as $i => $job) {
            if ($job['nextAt'] > time()) { continue; }
            try {
                $deliver($job['payload']);            // teslim
                unset($this->queue[$i]);              // başarılı → kuyruktan çıkar
            } catch (\Throwable $e) {
                if ($job['attempt'] >= 5) { unset($this->queue[$i]); continue; } // §4.4: dead-letter
                $this->enqueue($job['payload'], $job['attempt'] + 1);            // yeniden deneme
            }
        }
    }
}
```

## §15 Katalog Eşleme (§16.5 · Discogs → domain)

### 15.1 JavaScript — Metadata Normalizasyon (Discogs → K008) — ~25 satır

```js
// §16.5: Discogs metadata → domain normalize (ADR-084 event adları korunur).
// UYGULAMA YOK (fictional). Konuşa biçimi.
export function toDomainRelease(discogs) {
  return {
    id: discogs.id,
    title: discogs.title,
    artists: (discogs.artists || []).map((a) => a.name),
    year: discogs.year ?? null,
    formats: (discogs.formats || []).map((f) => f.name),
    country: discogs.country ?? null,
    // §16.5: eşleme (matching) domain'de — burada yalnız normalize.
  };
}
// Eşleme stratejisi (§16.5): title+artist+year benzerliği → domain eşlemesi (K008).
```

## §16 Dilim Tablosu (15 dil × konu)

| Blok | Dil | Konu | Satır |
|---|---|---|---|
| §2.1 | PHP 8.4 | Stripe imza (t=,v1=) | ~50 |
| §2.2 | JavaScript (Node) | GitHub sha256= | ~35 |
| §2.3 | Python | HMAC + tolerans | ~30 |
| §3.1 | C# | Outbox writer | ~40 |
| §3.2 | Go | Outbox poller | ~35 |
| §4.1 | SQL | Idempotency (UNIQUE) | ~18 |
| §5.1 | Rust | Retry + jitter | ~35 |
| §6.1 | Java | Resilience4j CB | ~45 |
| §7.1 | TypeScript | PKCE S256 | ~28 |
| §8.1 | C++ | SOAP sarmalayıcı | ~35 |
| §9.1 | JavaScript | GraphQL resolver | ~30 |
| §10.1 | PHP 8.4 | SSE + Redis Streams | ~35 |
| §11.1 | Go | Saga + tazmin | ~40 |
| §12.1 | Python | Proxy fallback | ~28 |
| §13.1 | C# | Jellyfin istek | ~25 |
| §14.1 | PHP 8.4 | Webhook retry | ~30 |
| §15.1 | JavaScript | Metadata normalize | ~25 |

**Toplam**: ~520 kod satırı · 15 blok · 11 dil (PHP/JS/TS/Python/C#/Go/Rust/Java/C++/SQL/C) — §16.

## §17 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K013-integraion/integration-master.md` | §14/§16 (bileşen konuşa biçimleri) | integration-master |
| `.ai/architecture/K013-integraion/integraion-test-stratejisi.md` | Entegrasyon testleri | integraion-test-stratejisi |
| `.ai/architecture/K012-services/services-kod-ornekleri.md` | K012 kod örnekleri (sınır) | services-kod-ornekleri |
