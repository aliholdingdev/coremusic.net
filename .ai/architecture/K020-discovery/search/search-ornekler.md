---
node_id: "K020-discovery/search-ornekler"
node_type: "kod-ornekler"
status: "draft"
parent_id: "K020-discovery/search"
children: []
depends_on:
  - "K020-discovery/search/search-bilesenler.md"
  - "K020-discovery/search/search-kapsam.md"
  - "ADR-010 (auth gate)"
  - "ADR-095 (vault-only token)"
  - "ADR-101 §2.2 (fallback zorunlu)"
evidence:
  - "2026-10-17 dilekçe örnekleri (13 dil + SQL — entegrasyon DEĞİL)"
validation: "draft"
open_questions:
  - "Hangi dil K020-search uygulamasında esas alınacak (repo PHP 8.x + vanilla JS)"
group: "kod"
---

# K020-discovery/search — Kod Örnekleri (dilekçe · 13 dil + SQL)

> **Yasak ve sınır (önce oku):**
> Bunlar **dilekçe (contract illustration) örnekleridir — entegrasyon kodu DEĞİLDİR.**
> Gerçek servis entegrasyon kodu yalnız **onaydan sonra** K020 alt node'larında yazılır
> (master §6 · S-5 auth gate · README `**` yasağı).
> Token/anahtar **hiçbir yerde gerçek yazılmaz** — tümü placeholder (ADR-095 §1.2:
> secret yalnız `.env`). YTM (`⚠️ KAPALI`) için **hiçbir örnek yoktur** (kapı değişmez).

---

## 0. Ortak sözleşme (her dil aynı şeyi örnekler)

Her örnek aynı üç parçayı gösterir:

1. **Gate:** `gate_state ∈ {open, blocked, closed}` (kapalıysa sorgu ATILMAZ).
2. **Rate-aware erteleme:** `429 + Retry-After` / `402` / `503` görülürse işi erteler.
3. **Sonuç şeması:** `search-kapsam.md` §3 alanları (`source`, `query`, `result_id`,
   `score`, `rate_hint`, `gate_state`, `evidence_url`).

Placeholder'lar: `$COREMUSIC_API_KEY` · `YOUR_TOKEN` · `coremusic-api-key-here`.

---

## 1. PHP 8.4 (repo dili · strict_types)

```php
<?php

declare(strict_types=1);

/**
 * DİLEKÇE — K020-discovery/search B3+B5: rate-aware sorgu işleyici.
 * Entegrasyon DEĞİLDİR. Token: yalnız .env (ADR-095 §1.2).
 */
final readonly class SearchDispatch
{
    public const GATE_OPEN = 'open';
    public const GATE_BLOCKED = 'blocked';
    public const GATE_CLOSED = 'closed';

    public function __construct(
        private string $apiKey,          // getenv('COREMUSIC_API_KEY') — .env
        private int $maxRetries = 3,
    ) {}

    /** Kapı kapalıysa sorgu ATILMAZ (S-5 / YTM kapalı). */
    public function gate(string $service): string
    {
        return match ($service) {
            'youtube_music' => self::GATE_CLOSED, // ADR-097 §1 madde 6 — değişmez kapı
            'spotify', 'apple', 'tidal', 'discogs', 'amazon_music' => self::GATE_BLOCKED,
            default => self::GATE_OPEN,
        };
    }

    /**
     * Tek sorgu çalıştırır; 429/402/503'te Retry-After kadar erteler.
     * Uç model uyumlu sonuç döner (search-kapsam.md §3).
     */
    public function dispatch(string $service, string $query): array
    {
        if ($this->gate($service) !== self::GATE_OPEN) {
            return [
                'source' => $service,
                'query' => $query,
                'result_id' => '',
                'score' => 0.0,
                'gate_state' => $this->gate($service),
                'evidence_url' => '',
            ];
        }

        $attempt = 0;
        while ($attempt < $this->maxRetries) {
            $status = $this->callService($service, $query); // HTTP katmanı (dilekçe)

            if ($status['code'] === 200) {
                return $this->normalize($service, $query, $status['body']);
            }

            if (in_array($status['code'], [402, 429, 503], true)) {
                $wait = (int) ($status['retry_after'] ?? 1); // saniye
                sleep($wait);                                 // kota-aware erteleme
                $attempt++;
                continue;
            }

            break; // geri kalan hatalar: fallback (ADR-101 — eksik fallback yasak)
        }

        return $this->fallback($service, $query);
    }

    private function normalize(string $service, string $query, array $body): array
    {
        return [
            'source' => $service,
            'query' => $query,
            'result_id' => (string) ($body['id'] ?? 'UNKNOWN'), // uydurma yok
            'score' => (float) ($body['score'] ?? 0.0),
            'rate_hint' => ['retry_after' => null, 'remaining' => null],
            'gate_state' => self::GATE_OPEN,
            'evidence_url' => (string) ($body['url'] ?? ''),
        ];
    }

    private function fallback(string $service, string $query): array
    {
        // Fallback kuralı (ADR-101 §2.2): eksik fallback tasarımda tespit edilir.
        return [
            'source' => $service,
            'query' => $query,
            'result_id' => 'UNKNOWN',
            'score' => 0.0,
            'rate_hint' => ['fallback' => true],
            'gate_state' => self::GATE_OPEN,
            'evidence_url' => '⚠️ VERIFICATION REQUIRED',
        ];
    }

    private function callService(string $service, string $query): array
    {
        // DİLEKÇE: HTTP çağrısı burada. Header: X-Api-Key: {$_ENV['COREMUSIC_API_KEY']}
        return ['code' => 200, 'body' => [], 'retry_after' => null];
    }
}
```

---

## 2. JavaScript (ES2024 · strict · async/await)

```js
'use strict';

/**
 * DİLEKÇE — B3 sorgu kuyruğu (rate-aware). Entegrasyon DEĞİLDİR.
 * Token: process.env.COREMUSIC_API_KEY (.env) — koda gömülmez.
 */

const GATE = Object.freeze({ OPEN: 'open', BLOCKED: 'blocked', CLOSED: 'closed' });
const GATE_MAP = Object.freeze({
  youtube_music: GATE.CLOSED,            // ADR-097 §1 madde 6 — kalıcı kapı
  spotify: GATE.BLOCKED,                 // auth gate ADR'si yok — onay bekliyor
  apple: GATE.BLOCKED,
  tidal: GATE.BLOCKED,
  discogs: GATE.BLOCKED,
  amazon_music: GATE.BLOCKED,            // kapalı beta
});

export function gateState(service) {
  return GATE_MAP[service] ?? GATE.OPEN;
}

/** 429/402/503 → Retry-After kadar ertele; sonuç dilbilgisine indir. */
export async function dispatch(service, query, { call, maxRetries = 3 } = {}) {
  const gate = gateState(service);
  if (gate !== GATE.OPEN) {
    return { source: service, query, result_id: '', score: 0, gate_state: gate, evidence_url: '' };
  }

  for (let attempt = 0; attempt < maxRetries; attempt++) {
    const res = await call(query); // HTTP katmanı (dilekçe)

    if (res.status === 200) {
      return normalize(service, query, res.body);
    }

    if (res.status === 402 || res.status === 429 || res.status === 503) {
      const waitSec = Number(res.headers.get('retry-after') ?? 1);
      await new Promise((r) => setTimeout(r, waitSec * 1000)); // kota-aware
      continue;
    }

    break;
  }

  return fallback(service, query);
}

function normalize(service, query, body) {
  return {
    source: service,
    query,
    result_id: typeof body.id === 'string' ? body.id : 'UNKNOWN',
    external_ids: body.mbid ? { mbid: body.mbid } : {},
    score: Number.isFinite(body.score) ? body.score : 0,
    rate_hint: { retry_after: null, remaining: null },
    gate_state: GATE.OPEN,
    evidence_url: typeof body.url === 'string' ? body.url : '',
  };
}

function fallback(service, query) {
  // ADR-101: eksik fallback tespit edilir (oracle modu) — sessizce geçilmez.
  return {
    source: service,
    query,
    result_id: 'UNKNOWN',
    score: 0,
    rate_hint: { fallback: true },
    gate_state: GATE.OPEN,
    evidence_url: '⚠️ VERIFICATION REQUIRED',
  };
}
```

---

## 3. TypeScript (strict · no any)

```ts
'use strict';

/** DİLEKÇE — B5 federatör: uç model uyumlu sonuç tipi. */

export type GateState = 'open' | 'blocked' | 'closed';

export interface RateHint {
  readonly retryAfterSec?: number | null;
  readonly remaining?: number | null;
  readonly fallback?: boolean;
}

export interface SearchResult {
  readonly source: string;
  readonly query: string;
  readonly result_id: string;
  readonly external_ids?: Readonly<Record<string, string>>;
  readonly score: number;
  readonly rate_hint?: RateHint;
  readonly gate_state: GateState;
  readonly evidence_url: string;
}

export type HttpCall = (query: string) => Promise<{
  status: number;
  body?: Record<string, unknown>;
  retryAfterSec?: number;
}>;

const GATE_MAP: Readonly<Record<string, GateState>> = Object.freeze({
  youtube_music: 'closed', // ADR-097 §1 madde 6
  spotify: 'blocked',      // auth gate ADR'si yok
  apple: 'blocked',
  tidal: 'blocked',
  discogs: 'blocked',
  amazon_music: 'blocked',
});

export function gateState(service: string): GateState {
  return GATE_MAP[service] ?? 'open';
}

function blocked(service: string, query: string): SearchResult {
  return {
    source: service,
    query,
    result_id: '',
    score: 0,
    gate_state: gateState(service),
    evidence_url: '',
  };
}

function normalize(service: string, query: string, body: Record<string, unknown>): SearchResult {
  const id = typeof body.id === 'string' ? body.id : 'UNKNOWN'; // uydurma yok
  const mbid = typeof body.mbid === 'string' ? body.mbid : undefined;
  return {
    source: service,
    query,
    result_id: id,
    external_ids: mbid ? { mbid } : undefined,
    score: typeof body.score === 'number' ? body.score : 0,
    rate_hint: { retryAfterSec: null, remaining: null },
    gate_state: 'open',
    evidence_url: typeof body.url === 'string' ? body.url : '',
  };
}

export async function dispatch(
  service: string,
  query: string,
  call: HttpCall,
  maxRetries = 3,
): Promise<SearchResult> {
  if (gateState(service) !== 'open') return blocked(service, query);

  for (let attempt = 0; attempt < maxRetries; attempt++) {
    const res = await call(query);
    if (res.status === 200) return normalize(service, query, res.body ?? {});

    if (res.status === 402 || res.status === 429 || res.status === 503) {
      await new Promise<void>((r) => setTimeout(r, (res.retryAfterSec ?? 1) * 1000));
      continue;
    }
    break; // ADR-101 §2.2: diğer hatalar fallback'e düşer, döngü KOPARMAZ
  }

  return {
    source: service,
    query,
    result_id: 'UNKNOWN',
    score: 0,
    rate_hint: { fallback: true },
    gate_state: 'open',
    evidence_url: '⚠️ VERIFICATION REQUIRED',
  };
}
```

---

## 4. Python (3.12 · asyncio · type hints)

```python
"""DİLEKÇE — B3 sorgu kuyruğu (rate-aware). Entegrasyon DEĞİLDİR.

Token: os.environ["COREMUSIC_API_KEY"] (.env) — koda asla gömülmez (ADR-095 §1.2).
"""

from __future__ import annotations

import asyncio
import os
from dataclasses import dataclass, field
from typing import Any, Awaitable, Callable

GATE_OPEN = "open"
GATE_BLOCKED = "blocked"
GATE_CLOSED = "closed"

GATE_MAP: dict[str, str] = {
    "youtube_music": GATE_CLOSED,  # ADR-097 §1 madde 6 — kalıcı kapı
    "spotify": GATE_BLOCKED,       # auth gate ADR'si yok — onay bekliyor
    "apple": GATE_BLOCKED,
    "tidal": GATE_BLOCKED,
    "discogs": GATE_BLOCKED,
    "amazon_music": GATE_BLOCKED,  # kapalı beta
}


@dataclass(frozen=True, slots=True)
class SearchResult:
    source: str
    query: str
    result_id: str
    score: float
    gate_state: str
    evidence_url: str
    external_ids: dict[str, str] = field(default_factory=dict)
    rate_hint: dict[str, Any] = field(default_factory=dict)


def gate_state(service: str) -> str:
    return GATE_MAP.get(service, GATE_OPEN)


def _blocked(service: str, query: str) -> SearchResult:
    return SearchResult(
        source=service, query=query, result_id="", score=0.0,
        gate_state=gate_state(service), evidence_url="",
    )


def _normalize(service: str, query: str, body: dict[str, Any]) -> SearchResult:
    result_id = body.get("id") if isinstance(body.get("id"), str) else "UNKNOWN"
    external = {"mbid": body["mbid"]} if isinstance(body.get("mbid"), str) else {}
    url = body.get("url") if isinstance(body.get("url"), str) else ""
    return SearchResult(
        source=service, query=query, result_id=result_id,
        score=float(body.get("score", 0.0)), gate_state=GATE_OPEN,
        evidence_url=url, external_ids=external,
        rate_hint={"retry_after": None, "remaining": None},
    )


def _fallback(service: str, query: str) -> SearchResult:
    # ADR-101: eksik fallback tespit edilir (oracle modu) — sessizce geçilmez.
    return SearchResult(
        source=service, query=query, result_id="UNKNOWN", score=0.0,
        gate_state=GATE_OPEN, evidence_url="⚠️ VERIFICATION REQUIRED",
        rate_hint={"fallback": True},
    )


async def dispatch(
    service: str,
    query: str,
    call: Callable[[str], Awaitable[dict[str, Any]]],
    max_retries: int = 3,
) -> SearchResult:
    if gate_state(service) != GATE_OPEN:
        return _blocked(service, query)

    for attempt in range(max_retries):
        res = await call(query)  # HTTP katmanı (dilekçe): {"status": 200, ...}
        status = int(res.get("status", 0))

        if status == 200:
            return _normalize(service, query, res.get("body", {}))

        if status in (402, 429, 503):
            wait_sec = int(res.get("retry_after", 1))  # Retry-After
            await asyncio.sleep(wait_sec)              # kota-aware erteleme
            continue

        break  # geri kalan hatalar → fallback

    return _fallback(service, query)


async def _example() -> None:
    api_key = os.environ.get("COREMUSIC_API_KEY", "")  # .env kaynağı
    assert api_key, "token yalnız .env'den gelir"

    async def call(query: str) -> dict[str, Any]:
        return {"status": 200, "body": {"id": "mb:1", "score": 0.9, "url": ""}}

    result = await dispatch("musicbrainz", "sakir ses", call)
    assert result.gate_state == GATE_OPEN
```

---

## 5. Go (1.22+ · context · error değerleri)

```go
// DİLEKÇE — B5 federatör (rate-aware). Entegrasyon DEĞİLDİR.
// Token: os.Getenv("COREMUSIC_API_KEY") (.env) — koda gömülmez (ADR-095 §1.2).
package search

import (
	"context"
	"fmt"
	"os"
	"time"
)

type GateState string

const (
	GateOpen    GateState = "open"
	GateBlocked GateState = "blocked"
	GateClosed  GateState = "closed"
)

var gateMap = map[string]GateState{
	"youtube_music": GateClosed, // ADR-097 §1 madde 6 — kalıcı kapı
	"spotify":       GateBlocked, // auth gate ADR'si yok
	"apple":         GateBlocked,
	"tidal":         GateBlocked,
	"discogs":       GateBlocked,
	"amazon_music":  GateBlocked, // kapalı beta
}

type RateHint struct {
	RetryAfterSec *int
	Remaining     *int
	Fallback      bool
}

type SearchResult struct {
	Source       string
	Query        string
	ResultID     string
	Score        float64
	RateHint     RateHint
	GateState    GateState
	EvidenceURL  string
}

type HTTPResult struct {
	Status       int
	RetryAfter   int
	BodyID       string
	BodyScore    float64
	BodyURL      string
}

type CallFunc func(ctx context.Context, query string) (HTTPResult, error)

func GateState(service string) GateState {
	if s, ok := gateMap[service]; ok {
		return s
	}
	return GateOpen
}

func blocked(service, query string) SearchResult {
	return SearchResult{
		Source: service, Query: query, GateState: GateState(service),
	}
}

func normalize(service, query string, r HTTPResult) SearchResult {
	id := r.BodyID
	if id == "" {
		id = "UNKNOWN" // uydurma yok
	}
	return SearchResult{
		Source: service, Query: query, ResultID: id,
		Score: r.BodyScore, GateState: GateOpen, EvidenceURL: r.BodyURL,
		RateHint: RateHint{},
	}
}

// Dispatch: 402/429/503 → Retry-After kadar erteler; kapalı kapıda sorgu ATMAZ.
func Dispatch(ctx context.Context, service, query string, call CallFunc, maxRetries int) (SearchResult, error) {
	if GateState(service) != GateOpen {
		return blocked(service, query), nil
	}

	var last HTTPResult
	for attempt := 0; attempt < maxRetries; attempt++ {
		res, err := call(ctx, query)
		if err != nil {
			return fallback(service, query), fmt.Errorf("call: %w", err)
		}
		last = res

		switch {
		case res.Status == 200:
			return normalize(service, query, res), nil
		case res.Status == 402 || res.Status == 429 || res.Status == 503:
			wait := time.Duration(res.RetryAfter) * time.Second
			select { // kota-aware erteleme
			case <-ctx.Done():
				return fallback(service, query), ctx.Err()
			case <-time.After(wait):
			}
			continue
		default:
			// ADR-101 §2.2: döngü KOPARILMAZ — fallback'e düşülür.
		}
	}
	_ = last
	return fallback(service, query), nil
}

func fallback(service, query string) SearchResult {
	return SearchResult{
		Source: service, Query: query, ResultID: "UNKNOWN",
		GateState: GateOpen, EvidenceURL: "⚠️ VERIFICATION REQUIRED",
		RateHint: RateHint{Fallback: true},
	}
}

func ExampleDispatch() {
	_ = os.Getenv("COREMUSIC_API_KEY") // .env kaynağı
}
```

---

## 6. Rust (2024 edition · Result · no unsafe)

```rust
// DİLEKÇE — B3 sorgu işleyici (rate-aware). Entegrasyon DEĞİLDİR.
// Token: std::env::var("COREMUSIC_API_KEY") (.env) — koda gömülmez (ADR-095 §1.2).

use std::time::Duration;

#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum GateState {
    Open,
    Blocked,
    Closed,
}

impl GateState {
    pub fn as_str(&self) -> &'static str {
        match self {
            GateState::Open => "open",
            GateState::Blocked => "blocked",
            GateState::Closed => "closed",
        }
    }
}

pub fn gate_state(service: &str) -> GateState {
    match service {
        // ADR-097 §1 madde 6 — kalıcı kapı (YTM)
        "youtube_music" => GateState::Closed,
        // auth gate ADR'si yok — onay bekliyor
        "spotify" | "apple" | "tidal" | "discogs" | "amazon_music" => GateState::Blocked,
        _ => GateState::Open,
    }
}

#[derive(Debug, Clone)]
pub struct SearchResult {
    pub source: String,
    pub query: String,
    pub result_id: String,
    pub score: f64,
    pub fallback: bool,
    pub gate_state: GateState,
    pub evidence_url: String,
}

pub struct HttpResult {
    pub status: u16,
    pub retry_after: u64,
    pub id: String,
    pub score: f64,
    pub url: String,
}

pub type CallFn = Box<dyn Fn(&str) -> Result<HttpResult, String>>;

fn blocked(service: &str, query: &str) -> SearchResult {
    SearchResult {
        source: service.to_string(),
        query: query.to_string(),
        result_id: String::new(),
        score: 0.0,
        fallback: false,
        gate_state: gate_state(service),
        evidence_url: String::new(),
    }
}

fn normalize(service: &str, query: &str, r: &HttpResult) -> SearchResult {
    let id = if r.id.is_empty() { "UNKNOWN" } else { &r.id }; // uydurma yok
    SearchResult {
        source: service.to_string(),
        query: query.to_string(),
        result_id: id.to_string(),
        score: r.score,
        fallback: false,
        gate_state: GateState::Open,
        evidence_url: r.url.clone(),
    }
}

fn fallback(service: &str, query: &str) -> SearchResult {
    // ADR-101: eksik fallback tespit edilir (oracle modu).
    SearchResult {
        source: service.to_string(),
        query: query.to_string(),
        result_id: "UNKNOWN".to_string(),
        score: 0.0,
        fallback: true,
        gate_state: GateState::Open,
        evidence_url: "⚠️ VERIFICATION REQUIRED".to_string(),
    }
}

/// Kapalı kapıda sorgu ATMZ; 402/429/503 → Retry-After kadar bekle.
pub fn dispatch(service: &str, query: &str, call: &CallFn, max_retries: u32) -> SearchResult {
    if gate_state(service) != GateState::Open {
        return blocked(service, query);
    }

    let mut attempt = 0;
    while attempt < max_retries {
        match call(query) {
            Ok(r) if r.status == 200 => return normalize(service, query, &r),
            Ok(r) if r.status == 402 || r.status == 429 || r.status == 503 => {
                std::thread::sleep(Duration::from_secs(r.retry_after.max(1)));
                attempt += 1;
            }
            Ok(_) => break,
            Err(_) => break,
        }
    }
    fallback(service, query)
}
```

---

## 7. C (C11 · kodlanmış dönüş · başarısızlık gizlenmez)

```c
/* DİLEKÇE — B5 federatör (rate-aware). Entegrasyon DEĞİLDİR.
 * Token: getenv("COREMUSIC_API_KEY") (.env) — koda gömülmez (ADR-095 §1.2). */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#define GATE_OPEN    0
#define GATE_BLOCKED 1
#define GATE_CLOSED  2

#define SR_OK        200
#define SR_QUOTA     402
#define SR_RATE      429
#define SR_UNAVAIL   503

typedef struct {
    char  source[32];
    char  query[128];
    char  result_id[64];
    double score;
    int   gate_state;
    int   fallback;
    char  evidence_url[128];
} search_result_t;

typedef struct {
    int    status;
    int    retry_after;
    char   id[64];
    double score;
    char   url[128];
} http_result_t;

static int gate_state(const char *service) {
    if (strcmp(service, "youtube_music") == 0) return GATE_CLOSED; /* ADR-097 §1 madde 6 */
    if (strcmp(service, "spotify") == 0 || strcmp(service, "apple") == 0 ||
        strcmp(service, "tidal") == 0 || strcmp(service, "discogs") == 0 ||
        strcmp(service, "amazon_music") == 0) {
        return GATE_BLOCKED; /* auth gate ADR'si yok */
    }
    return GATE_OPEN;
}

/* HTTP katmanı dilekçesi (dışarıdan sağlanır). */
typedef int (*call_fn_t)(const char *query, http_result_t *out);

static void result_blocked(search_result_t *r, const char *service, const char *query) {
    snprintf(r->source, sizeof r->source, "%s", service);
    snprintf(r->query, sizeof r->query, "%s", query);
    r->result_id[0] = '\0';
    r->score = 0.0;
    r->gate_state = gate_state(service);
    r->fallback = 0;
    r->evidence_url[0] = '\0';
}

static void result_fallback(search_result_t *r, const char *service, const char *query) {
    /* ADR-101: eksik fallback tespit edilir (oracle modu) — sessizce geçilmez. */
    snprintf(r->source, sizeof r->source, "%s", service);
    snprintf(r->query, sizeof r->query, "%s", query);
    snprintf(r->result_id, sizeof r->result_id, "UNKNOWN");
    r->score = 0.0;
    r->gate_state = GATE_OPEN;
    r->fallback = 1;
    snprintf(r->evidence_url, sizeof r->evidence_url, "%s", "⚠️ VERIFICATION REQUIRED");
}

/* Kapalı kapıda sorgu ATMZ; 402/429/503 → Retry-After kadar erteler. */
void search_dispatch(const char *service, const char *query,
                     call_fn_t call, int max_retries, search_result_t *out) {
    if (gate_state(service) != GATE_OPEN) {
        result_blocked(out, service, query);
        return;
    }

    for (int attempt = 0; attempt < max_retries; ++attempt) {
        http_result_t res;
        memset(&res, 0, sizeof res);
        if (call(query, &res) != 0) {
            result_fallback(out, service, query);
            return;
        }
        if (res.status == SR_OK) {
            snprintf(out->source, sizeof out->source, "%s", service);
            snprintf(out->query, sizeof out->query, "%s", query);
            snprintf(out->result_id, sizeof out->result_id, "%s",
                     res.id[0] ? res.id : "UNKNOWN"); /* uydurma yok */
            out->score = res.score;
            out->gate_state = GATE_OPEN;
            out->fallback = 0;
            snprintf(out->evidence_url, sizeof out->evidence_url, "%s", res.url);
            return;
        }
        if (res.status == SR_QUOTA || res.status == SR_RATE || res.status == SR_UNAVAIL) {
            int wait = res.retry_after > 0 ? res.retry_after : 1;
            struct timespec ts = { .tv_sec = wait, .tv_nsec = 0 };
            nanosleep(&ts, NULL); /* kota-aware erteleme */
            continue;
        }
        break; /* diğer hatalar → fallback (ADR-101) */
    }
    result_fallback(out, service, query);
}
```

---

## 8. C++ (C++20 · RAII · std::optional · std::expected benzeri dönüş)

```cpp
// DİLEKÇE — B3 sorgu işleyici (rate-aware). Entegrasyon DEĞİLDİR.
// Token: std::getenv("COREMUSIC_API_KEY") (.env) — koda gömülmez (ADR-095 §1.2).
#include <chrono>
#include <optional>
#include <string>
#include <string_view>
#include <thread>
#include <vector>

namespace cmsearch {

enum class GateState { Open, Blocked, Closed };

struct RateHint {
    std::optional<int> retry_after;
    std::optional<int> remaining;
    bool fallback = false;
};

struct SearchResult {
    std::string source;
    std::string query;
    std::string result_id;
    double score = 0.0;
    RateHint rate_hint{};
    GateState gate_state = GateState::Open;
    std::string evidence_url;
};

struct HttpResult {
    int status = 0;
    int retry_after = 0;
    std::string id;
    double score = 0.0;
    std::string url;
};

using CallFn = HttpResult (*)(std::string_view query);

GateState gate_state(std::string_view service) {
    if (service == "youtube_music") return GateState::Closed;  // ADR-097 §1 madde 6
    if (service == "spotify" || service == "apple" || service == "tidal" ||
        service == "discogs" || service == "amazon_music") {
        return GateState::Blocked;                              // auth gate ADR'si yok
    }
    return GateState::Open;
}

SearchResult blocked(std::string_view service, std::string_view query) {
    return SearchResult{
        .source = std::string(service),
        .query = std::string(query),
        .result_id = {},
        .score = 0.0,
        .rate_hint = {},
        .gate_state = gate_state(service),
        .evidence_url = {},
    };
}

SearchResult normalize(std::string_view service, std::string_view query,
                       const HttpResult& r) {
    SearchResult out;
    out.source = std::string(service);
    out.query = std::string(query);
    out.result_id = r.id.empty() ? "UNKNOWN" : r.id;  // uydurma yok
    out.score = r.score;
    out.gate_state = GateState::Open;
    out.evidence_url = r.url;
    return out;
}

SearchResult fallback(std::string_view service, std::string_view query) {
    // ADR-101: eksik fallback tespit edilir (oracle modu).
    SearchResult out;
    out.source = std::string(service);
    out.query = std::string(query);
    out.result_id = "UNKNOWN";
    out.rate_hint.fallback = true;
    out.gate_state = GateState::Open;
    out.evidence_url = "⚠️ VERIFICATION REQUIRED";
    return out;
}

// Kapalı kapıda sorgu ATMZ; 402/429/503 → Retry-After kadar erteler.
SearchResult dispatch(std::string_view service, std::string_view query,
                      CallFn call, int max_retries = 3) {
    if (gate_state(service) != GateState::Open) {
        return blocked(service, query);
    }
    for (int attempt = 0; attempt < max_retries; ++attempt) {
        HttpResult r = call(query);
        if (r.status == 200) return normalize(service, query, r);
        if (r.status == 402 || r.status == 429 || r.status == 503) {
            const int wait = r.retry_after > 0 ? r.retry_after : 1;
            std::this_thread::sleep_for(std::chrono::seconds(wait));
            continue;
        }
        break;
    }
    return fallback(service, query);
}

}  // namespace cmsearch
```

---

## 9. C# (.NET 8+ · nullable enable · record DTO · ConfigureAwait(false))

```csharp
// DİLEKÇE — B5 federatör (rate-aware). Entegrasyon DEĞİLDİR.
// Token: Environment.GetEnvironmentVariable("COREMUSIC_API_KEY") (.env) — koda gömülmez.
#nullable enable

namespace CoreMusic.K020.Discovery.Search;

public enum GateState { Open, Blocked, Closed }

public sealed record RateHint(int? RetryAfterSec = null, int? Remaining = null, bool Fallback = false);

public sealed record SearchResult(
    string Source,
    string Query,
    string ResultId,
    double Score,
    GateState GateState,
    string EvidenceUrl,
    RateHint? RateHint = null,
    IReadOnlyDictionary<string, string>? ExternalIds = null);

public sealed record HttpResult(int Status, int RetryAfterSec = 0, string? Id = null,
    double Score = 0.0, string? Url = null);

public delegate Task<HttpResult> CallFn(string query, CancellationToken ct);

public static class SearchDispatch
{
    private static readonly IReadOnlyDictionary<string, GateState> GateMap =
        new Dictionary<string, GateState>
        {
            ["youtube_music"] = GateState.Closed, // ADR-097 §1 madde 6 — kalıcı kapı
            ["spotify"] = GateState.Blocked,      // auth gate ADR'si yok
            ["apple"] = GateState.Blocked,
            ["tidal"] = GateState.Blocked,
            ["discogs"] = GateState.Blocked,
            ["amazon_music"] = GateState.Blocked, // kapalı beta
        };

    public static GateState GetGate(string service) =>
        GateMap.TryGetValue(service, out var g) ? g : GateState.Open;

    public static async Task<SearchResult> DispatchAsync(
        string service, string query, CallFn call, int maxRetries = 3,
        CancellationToken ct = default)
    {
        if (GetGate(service) != GateState.Open)
            return new SearchResult(service, query, string.Empty, 0.0,
                GetGate(service), EvidenceUrl: string.Empty);

        for (var attempt = 0; attempt < maxRetries; attempt++)
        {
            var res = await call(query, ct).ConfigureAwait(false);

            if (res.Status == 200)
            {
                return new SearchResult(
                    service, query,
                    string.IsNullOrWhiteSpace(res.Id) ? "UNKNOWN" : res.Id, // uydurma yok
                    res.Score, GateState.Open,
                    res.Url ?? string.Empty,
                    new RateHint());
            }

            if (res.Status is 402 or 429 or 503)
            {
                var wait = TimeSpan.FromSeconds(res.RetryAfterSec > 0 ? res.RetryAfterSec : 1);
                await Task.Delay(wait, ct).ConfigureAwait(false); // kota-aware erteleme
                continue;
            }
            break;
        }

        // ADR-101: eksik fallback tespit edilir (oracle modu) — sessizce geçilmez.
        return new SearchResult(service, query, "UNKNOWN", 0.0, GateState.Open,
            "⚠️ VERIFICATION REQUIRED", new RateHint(Fallback: true));
    }
}
```

---

## 10. Java (21 · sealed/record · virtual threads opsiyonel)

```java
// DİLEKÇE — B3 sorgu işleyici (rate-aware). Entegrasyon DEĞİLDİR.
// Token: System.getenv("COREMUSIC_API_KEY") (.env) — koda gömülmez (ADR-095 §1.2).
package net.coremusic.k020.discovery.search;

import java.time.Duration;
import java.util.Map;

public final class SearchDispatch {

    public enum GateState { OPEN, BLOCKED, CLOSED }

    public record RateHint(Integer retryAfterSec, Integer remaining, boolean fallback) {
        public static RateHint none() { return new RateHint(null, null, false); }
    }

    public record SearchResult(String source, String query, String resultId, double score,
                               GateState gateState, String evidenceUrl, RateHint rateHint) {}

    public record HttpResult(int status, int retryAfterSec, String id, double score, String url) {}

    @FunctionalInterface
    public interface CallFn { HttpResult call(String query) throws Exception; }

    private static final Map<String, GateState> GATE_MAP = Map.of(
        "youtube_music", GateState.CLOSED,  // ADR-097 §1 madde 6 — kalıcı kapı
        "spotify",       GateState.BLOCKED, // auth gate ADR'si yok
        "apple",         GateState.BLOCKED,
        "tidal",         GateState.BLOCKED,
        "discogs",       GateState.BLOCKED,
        "amazon_music",  GateState.BLOCKED  // kapalı beta
    );

    static GateState gateState(String service) {
        return GATE_MAP.getOrDefault(service, GateState.OPEN);
    }

    private static SearchResult blocked(String service, String query) {
        return new SearchResult(service, query, "", 0.0, gateState(service), "", RateHint.none());
    }

    private static SearchResult normalize(String service, String query, HttpResult r) {
        String id = (r.id() == null || r.id().isBlank()) ? "UNKNOWN" : r.id(); // uydurma yok
        String url = r.url() == null ? "" : r.url();
        return new SearchResult(service, query, id, r.score(), GateState.OPEN, url, RateHint.none());
    }

    private static SearchResult fallback(String service, String query) {
        // ADR-101: eksik fallback tespit edilir (oracle modu).
        return new SearchResult(service, query, "UNKNOWN", 0.0, GateState.OPEN,
            "⚠️ VERIFICATION REQUIRED", new RateHint(null, null, true));
    }

    /** Kapalı kapıda sorgu ATMZ; 402/429/503 → Retry-After kadar erteler. */
    public static SearchResult dispatch(String service, String query, CallFn call, int maxRetries) {
        if (gateState(service) != GateState.OPEN) {
            return blocked(service, query);
        }
        for (int attempt = 0; attempt < maxRetries; attempt++) {
            HttpResult res;
            try {
                res = call.call(query);
            } catch (Exception e) {
                return fallback(service, query);
            }
            if (res.status() == 200) {
                return normalize(service, query, res);
            }
            if (res.status() == 402 || res.status() == 429 || res.status() == 503) {
                long wait = res.retryAfterSec() > 0 ? res.retryAfterSec() : 1;
                try {
                    Thread.sleep(Duration.ofSeconds(wait).toMillis()); // kota-aware erteleme
                } catch (InterruptedException ie) {
                    Thread.currentThread().interrupt();
                    return fallback(service, query);
                }
                continue;
            }
            break;
        }
        return fallback(service, query);
    }

    private SearchDispatch() {}
}
```

---

## 11. Kotlin (2 · data class · Result · coroutines)

```kotlin
// DİLEKÇE — B5 federatör (rate-aware). Entegrasyon DEĞİLDİR.
// Token: System.getenv("COREMUSIC_API_KEY") (.env) — koda gömülmez (ADR-095 §1.2).
package net.coremusic.k020.discovery.search

import kotlinx.coroutines.delay

enum class GateState { OPEN, BLOCKED, CLOSED }

data class RateHint(
    val retryAfterSec: Int? = null,
    val remaining: Int? = null,
    val fallback: Boolean = false,
)

data class SearchResult(
    val source: String,
    val query: String,
    val resultId: String,
    val score: Double,
    val gateState: GateState,
    val evidenceUrl: String,
    val rateHint: RateHint = RateHint(),
    val externalIds: Map<String, String> = emptyMap(),
)

data class HttpResult(
    val status: Int,
    val retryAfterSec: Int = 0,
    val id: String? = null,
    val score: Double = 0.0,
    val url: String? = null,
)

typealias CallFn = suspend (String) -> HttpResult

private val GATE_MAP: Map<String, GateState> = mapOf(
    "youtube_music" to GateState.CLOSED,  // ADR-097 §1 madde 6 — kalıcı kapı
    "spotify" to GateState.BLOCKED,       // auth gate ADR'si yok
    "apple" to GateState.BLOCKED,
    "tidal" to GateState.BLOCKED,
    "discogs" to GateState.BLOCKED,
    "amazon_music" to GateState.BLOCKED,  // kapalı beta
)

fun gateState(service: String): GateState = GATE_MAP[service] ?: GateState.OPEN

private fun blocked(service: String, query: String) = SearchResult(
    source = service, query = query, resultId = "", score = 0.0,
    gateState = gateState(service), evidenceUrl = "",
)

private fun normalize(service: String, query: String, r: HttpResult) = SearchResult(
    source = service, query = query,
    resultId = r.id?.takeIf { it.isNotBlank() } ?: "UNKNOWN", // uydurma yok
    score = r.score, gateState = GateState.OPEN, evidenceUrl = r.url.orEmpty(),
)

private fun fallback(service: String, query: String) = SearchResult(
    // ADR-101: eksik fallback tespit edilir (oracle modu).
    source = service, query = query, resultId = "UNKNOWN", score = 0.0,
    gateState = GateState.OPEN, evidenceUrl = "⚠️ VERIFICATION REQUIRED",
    rateHint = RateHint(fallback = true),
)

/** Kapalı kapıda sorgu ATMZ; 402/429/503 → Retry-After kadar erteler. */
suspend fun dispatch(
    service: String,
    query: String,
    call: CallFn,
    maxRetries: Int = 3,
): SearchResult {
    if (gateState(service) != GateState.OPEN) return blocked(service, query)

    repeat(maxRetries) { attempt ->
        val res = runCatching { call(query) }.getOrElse { return fallback(service, query) }
        when (res.status) {
            200 -> return normalize(service, query, res)
            402, 429, 503 -> delay((res.retryAfterSec.coerceAtLeast(1)) * 1000L) // kota-aware
            else -> return@repeat // döngüyü kopar (ADR-101 fallback)
        }
        if (res.status == 402 || res.status == 429 || res.status == 503) return@repeat
    }
    return fallback(service, query)
}
```

---

## 12. Swift (6 · struct + enum · async/await · guard)

```swift
// DİLEKÇE — B3 sorgu işleyici (rate-aware). Entegrasyon DEĞİLDİR.
// Token: ProcessInfo.processInfo.environment["COREMUSIC_API_KEY"] (.env) — koda gömülmez.
import Foundation

enum GateState: String {
    case open = "open"
    case blocked = "blocked"
    case closed = "closed"
}

struct RateHint: Sendable {
    var retryAfterSec: Int?
    var remaining: Int?
    var fallback: Bool
    init(retryAfterSec: Int? = nil, remaining: Int? = nil, fallback: Bool = false) {
        self.retryAfterSec = retryAfterSec
        self.remaining = remaining
        self.fallback = fallback
    }
}

struct SearchResult: Sendable {
    let source: String
    let query: String
    let resultId: String
    let score: Double
    let gateState: GateState
    let evidenceUrl: String
    var rateHint: RateHint = RateHint()
    var externalIds: [String: String] = [:]
}

struct HttpResult: Sendable {
    let status: Int
    var retryAfterSec: Int = 0
    var id: String?
    var score: Double = 0.0
    var url: String?
}

typealias CallFn = @Sendable (String) async throws -> HttpResult

enum SearchDispatch {
    static func gateState(_ service: String) -> GateState {
        switch service {
        case "youtube_music": return .closed // ADR-097 §1 madde 6 — kalıcı kapı
        case "spotify", "apple", "tidal", "discogs", "amazon_music":
            return .blocked                  // auth gate ADR'si yok / kapalı beta
        default: return .open
        }
    }

    private static func blocked(_ service: String, _ query: String) -> SearchResult {
        SearchResult(source: service, query: query, resultId: "", score: 0,
                     gateState: gateState(service), evidenceUrl: "")
    }

    private static func normalize(_ service: String, _ query: String,
                                  _ r: HttpResult) -> SearchResult {
        // uydurma yok: boş kimlik UNKNOWN kalır
        let id = (r.id?.isEmpty == false) ? r.id! : "UNKNOWN"
        return SearchResult(source: service, query: query, resultId: id, score: r.score,
                            gateState: .open, evidenceUrl: r.url ?? "")
    }

    private static func fallback(_ service: String, _ query: String) -> SearchResult {
        // ADR-101: eksik fallback tespit edilir (oracle modu).
        SearchResult(source: service, query: query, resultId: "UNKNOWN", score: 0,
                     gateState: .open, evidenceUrl: "⚠️ VERIFICATION REQUIRED",
                     rateHint: RateHint(fallback: true))
    }

    /// Kapalı kapıda sorgu ATMZ; 402/429/503 → Retry-After kadar erteler.
    static func dispatch(_ service: String, _ query: String, call: CallFn,
                         maxRetries: Int = 3) async -> SearchResult {
        guard gateState(service) == .open else { return blocked(service, query) }

        for _ in 0..<maxRetries {
            let res: HttpResult
            do {
                res = try await call(query)
            } catch {
                return fallback(service, query)
            }
            if res.status == 200 {
                return normalize(service, query, res)
            }
            if res.status == 402 || res.status == 429 || res.status == 503 {
                let wait = UInt64(max(res.retryAfterSec, 1))
                try? await Task.sleep(nanoseconds: wait * 1_000_000_000) // kota-aware
                continue
            }
            break
        }
        return fallback(service, query)
    }
}
```

---

## 13. Ruby (3.3 · frozen_string_literal · idiomatic)

```ruby
# frozen_string_literal: true

# DİLEKÇE — B5 federatör (rate-aware). Entegrasyon DEĞİLDİR.
# Token: ENV["COREMUSIC_API_KEY"] (.env) — koda gömülmez (ADR-095 §1.2).

module K020
  module Discovery
    module Search
      GATE_OPEN    = 'open'
      GATE_BLOCKED = 'blocked'
      GATE_CLOSED  = 'closed'

      GATE_MAP = {
        'youtube_music' => GATE_CLOSED, # ADR-097 §1 madde 6 — kalıcı kapı
        'spotify'       => GATE_BLOCKED, # auth gate ADR'si yok
        'apple'         => GATE_BLOCKED,
        'tidal'         => GATE_BLOCKED,
        'discogs'       => GATE_BLOCKED,
        'amazon_music'  => GATE_BLOCKED  # kapalı beta
      }.freeze

      RATE_CODES = [402, 429, 503].freeze

      Result = Struct.new(:source, :query, :result_id, :score, :rate_hint,
                          :gate_state, :evidence_url, keyword_init: true)

      HttpResult = Struct.new(:status, :retry_after, :id, :score, :url,
                              keyword_init: true)

      module_function

      def gate_state(service)
        GATE_MAP.fetch(service, GATE_OPEN)
      end

      def blocked(service, query)
        Result.new(source: service, query: query, result_id: '', score: 0.0,
                   rate_hint: {}, gate_state: gate_state(service), evidence_url: '')
      end

      def normalize(service, query, r)
        Result.new(
          source: service, query: query,
          result_id: r.id.to_s.empty? ? 'UNKNOWN' : r.id.to_s, # uydurma yok
          score: r.score.to_f,
          rate_hint: { retry_after: nil, remaining: nil },
          gate_state: GATE_OPEN,
          evidence_url: r.url.to_s
        )
      end

      def fallback(service, query)
        # ADR-101: eksik fallback tespit edilir (oracle modu).
        Result.new(source: service, query: query, result_id: 'UNKNOWN', score: 0.0,
                   rate_hint: { fallback: true }, gate_state: GATE_OPEN,
                   evidence_url: '⚠️ VERIFICATION REQUIRED')
      end

      # Kapalı kapıda sorgu ATMZ; 402/429/503 → Retry-After kadar erteler.
      def dispatch(service, query, max_retries: 3, &call)
        return blocked(service, query) unless gate_state(service) == GATE_OPEN

        max_retries.times do
          res = call.call(query)
          case res.status
          when 200
            return normalize(service, query, res)
          when *RATE_CODES
            wait = res.retry_after.to_i.positive? ? res.retry_after.to_i : 1
            sleep(wait) # kota-aware erteleme
            next
          else
            break
          end
        end
        fallback(service, query)
      end
    end
  end
end
```

---

## 14. SQL (MySQL 8 · dilekçe — keşif sorgu günlüğü şeması)

```sql
-- DİLEKÇE — K020-discovery/search: keşif sorgu günlüğü + kota durumu.
-- Şema normalizasyonu K007-data (S-1 sahiplik sınırı) yetkisindedir; bu dosya yalın dilekçedir.
-- Parametreli sorgu zorunlu; SELECT * yasak (AGENTS.md §5).

CREATE TABLE discovery_query_log (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  service          VARCHAR(32)     NOT NULL,        -- spotify|apple|musicbrainz|...
  query_text       VARCHAR(255)    NOT NULL,
  gate_state       ENUM('open','blocked','closed') NOT NULL,
  http_status      SMALLINT UNSIGNED NULL,
  retry_after_sec  SMALLINT UNSIGNED NULL,
  rate_remaining   INT NULL,
  result_id        VARCHAR(64)     NULL,            -- UNKNOWN bırakılabilir (uydurma yok)
  score            DECIMAL(4,3)    NULL,
  evidence_url     VARCHAR(255)    NULL,
  created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_service_time (service, created_at),
  KEY idx_gate (gate_state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kapalı/bloke kapıların gerçekten sorguya dönüşmediğinin denetimi (kanıt sorgusu).
SELECT
  g.gate_state,
  COUNT(q.id) AS query_count
FROM (SELECT 'open' AS gate_state UNION ALL SELECT 'blocked' UNION ALL SELECT 'closed') g
LEFT JOIN discovery_query_log q
  ON q.gate_state = g.gate_state
 AND q.created_at >= :since_ts
GROUP BY g.gate_state
ORDER BY g.gate_state;

-- Kota erken uyarı: kalan kota düşen servisler (429 öncesi nabız).
SELECT
  service,
  MAX(rate_remaining)          AS remaining_max,
  COUNT(CASE WHEN http_status = 429 THEN 1 END) AS rate_hits
FROM discovery_query_log
WHERE created_at >= :since_ts
GROUP BY service
HAVING COUNT(CASE WHEN http_status = 429 THEN 1 END) > 0
ORDER BY rate_hits DESC;
```

---

## 15. Özet ve İlgili dosyalar

### 15.1 Dilekçe özeti

| # | Dil | Gösterilen parça | Gate vurgusu |
|---|---|---|---|
| 1 | PHP 8.4 | dispatch + normalize + fallback | `strict_types` · `.env` |
| 2 | JavaScript | async dispatch + gate map | YTM `closed` hard-block |
| 3 | TypeScript | uç model tipleri (`SearchResult`) | `GateState` union |
| 4 | Python | asyncio kuyruk | `asyncio.sleep` kota-aware |
| 5 | Go | Dispatch + ctx iptali | `Retry-After` select |
| 6 | Rust | `dispatch` + `Result` | loop güvenliği (borrow) |
| 7 | C | `search_dispatch` + struct | `nanosleep` kota |
| 8 | C++ | `namespace cmsearch` + RAII | `optional` rate hint |
| 9 | C# | `record` + `ConfigureAwait(false)` | `is 402 or 429 or 503` |
| 10 | Java | `record` + `enum` | `Thread.sleep` guard |
| 11 | Kotlin | `suspend` + `data class` | `delay` coroutine |
| 12 | Swift | `actor`sız async | `guard gate` ilk satır |
| 13 | Ruby | `Struct` + `module_function` | `frozen_string_literal` |
| 14 | SQL | sorgu günlüğü + denetim | `gate_state` sayımı |

### 15.2 Doğrulama

- [x] 13 dil + SQL; her örnek **gate + erteleme + sonuç şeması** içeriyor.
- [x] Gerçek secret **yok** (tümü placeholder); `.env` kuralı her blokta anıldı.
- [x] YTM için **örnek yok** (kapı `closed` olarak modelleniyor, çağrı yok).
- [x] `// ...`/`# ...` dolgu **yok**; her fonksiyon tam gövde.
- [x] Frontmatter 9 alan + `group: kod` + Authority footer.
- [ ] `status: draft` — dilekçeler uygulama öncesi **onay** bekler (S-5).

### 15.3 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `search/search-bilesenler.md` | örneklenen B3/B5 bileşenleri |
| `search/search-kapsam.md` | sonuç dilbilgisi (§3) |
| `search/search-web-arastirma.md` | hangi servis hangi auth (2026-10-17) |
| `search/search-dogrulama.md` | 13 dil denetim tablosu (§2) |

---

**Authority:** Bayram Ali / Vault Steward
