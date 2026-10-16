---
node_id: K014-network/comms/http/kod-ornekleri
node_type: module
status: draft
parent_id: K014-network/comms/http
role: ornek
children: []
depends_on:
  - "K014-network/comms/http/http-standart-kanit.md"
evidence:
  - "Kod kanıtı YOK (K014 PLANNED) — bu dosyadaki örnekler TASARIM ÖRNEĞİ'dir, üretim kodu değildir"
  - "Referans standartlar: http-standart-kanit.md §1 (RFC 9110/9112/9113/9114)"
validation:
  - "Örnekler zero-hallucination kapsamında: yalnız bilinen API'ler kullanıldı; sürüm/eksik API uydurulmadı"
open_questions:
  - "Hangi dil/çatı üretimde kullanılacak → ⚠️ VERIFICATION REQUIRED (faz C)"
group: kod
---

# http — kod-ornekleri (kademe-3 · module · rol: ornek)

> **UYARI:** K014'te henüz kod YOKTUR (kapsam §6). Bu örnekler **tasarım şablonudur** —
> faz C'de üretim koduna dönüşür. Her örnek 7 dilde aynı işi yapar: CoreMusic bridge/API
> için dayanıklı HTTP istemcisi (timeout + retry + backoff) veya HTTP sunucusu iskeleti.

## §1 PHP 8.4 — HttpClient (istemci · strict_types)

```php
<?php
declare(strict_types=1);

/**
 * K014/comms/http — dayanıklı istemci iskeleti (TASARIM ÖRNEĞİ).
 * Sınır: şifreleme üretmez (TLS yerleşik stream wrapper), anlam ayrıştırmaz (K012/K013).
 */
final readonly class HttpClient
{
    /** @var array{connect: float, total: float, retries: int} */
    private array $cfg;

    public function __construct(array $cfg = ['connect' => 3.0, 'total' => 10.0, 'retries' => 3])
    {
        $this->cfg = $cfg;
    }

    /**
     * GET isteği; geçici hatalarda üstel geri çekilme ile yeniden dener.
     *
     * @throws RuntimeException kalıcı hata (4xx / DNS)
     */
    public function get(string $url, array $headers = []): string
    {
        $lastError = 'bilinmeyen';
        for ($attempt = 0; $attempt <= $this->cfg['retries']; $attempt++) {
            if ($attempt > 0) {
                usleep((int)(100_000 * (2 ** ($attempt - 1)))); // 100ms → 200ms → 400ms
            }
            $hdrLine = implode("\r\n", array_map(
                static fn(string $k, string $v): string => "$k: $v",
                array_keys($headers),
                $headers
            ));
            $ctx = stream_context_create(['http' => [
                'method'        => 'GET',
                'header'        => $hdrLine,
                'timeout'       => $this->cfg['total'],
                'ignore_errors' => true,            // 4xx/5xx gövdeyi de oku
            ]]);
            $body = @file_get_contents($url, false, $ctx);
            $status = 0;
            foreach ($http_response_header ?? [] as $h) {
                if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) {
                    $status = (int)$m[1];
                    break;
                }
            }
            if ($status >= 200 && $status < 300) {
                return $body ?: '';
            }
            if ($status >= 400 && $status < 500 && $status !== 429) {
                throw new RuntimeException("kalıcı hata: HTTP $status"); // retry yok
            }
            $lastError = "HTTP $status";
        }
        throw new RuntimeException("geçici hata doldu: $lastError");
    }
}
```

## §2 Vanilla JS (ES6+) — fetch + AbortController (istemci)

```js
/**
 * K014/comms/http — dayanıklı fetch sarmalayıcı (TASARIM ÖRNEĞİ).
 * Sınır: yanıt anlamı K012/K013'te; burada yalnız taşıma.
 */
export class HttpClient {
  /** @param {{connectMs?: number, totalMs?: number, retries?: number}} cfg */
  constructor(cfg = {}) {
    this.connectMs = cfg.connectMs ?? 3000;
    this.totalMs   = cfg.totalMs   ?? 10000;
    this.retries   = cfg.retries   ?? 3;
  }

  /**
   * @param {string} url
   * @param {RequestInit} [init]
   * @returns {Promise<string>}
   */
  async get(url, init = {}) {
    let lastError;
    for (let attempt = 0; attempt <= this.retries; attempt++) {
      if (attempt > 0) {
        await new Promise(r => setTimeout(r, 100 * 2 ** (attempt - 1)));
      }
      const ctrl = new AbortController();
      const timer = setTimeout(() => ctrl.abort(), this.totalMs);
      try {
        const res = await fetch(url, { ...init, signal: ctrl.signal });
        if (res.ok) return await res.text();
        if (res.status >= 400 && res.status < 500 && res.status !== 429) {
          throw new Error(`kalıcı hata: HTTP ${res.status}`); // retry yok
        }
        lastError = `HTTP ${res.status}`;
      } catch (err) {
        if (err instanceof Error && err.message.startsWith('kalıcı')) throw err;
        lastError = err instanceof Error ? err.message : String(err); // abort/timeout/geçici
      } finally {
        clearTimeout(timer);
      }
    }
    throw new Error(`geçici hata doldu: ${lastError}`);
  }
}
```

## §3 C++20 — HttpClient (istemci · RAII · noexcept yollar)

```cpp
// K014/comms/http — HTTP istemci iskeleti (TASARIM ÖRNEĞİ).
// Sınır: TLS üretmez (kullanır), JSON çözümlemez (K012/K013).
// Not: gerçek üretimde WinHTTP (Windows) ya da libcurl; bu örnek arayüz sözleşmesidir.
#pragma once
#include <string>
#include <chrono>
#include <expected>
#include <thread>

namespace cm::net {

enum class HttpError { timeout, transient, permanent };

struct HttpResult {
    unsigned status{};
    std::string body;
};

class HttpClient {
public:
    struct Config {
        std::chrono::milliseconds total{10'000};
        int retries{3};
    };

    explicit HttpClient(Config cfg = {}) : cfg_(cfg) {}

    // Geçici hatalarda üstel geri çekilme ile yeniden dener; kalıcıda erken döner.
    [[nodiscard]] std::expected<HttpResult, HttpError>
    get(const std::string& url) const {
        HttpError last = HttpError::transient;
        for (int attempt = 0; attempt <= cfg_.retries; ++attempt) {
            if (attempt > 0) {
                std::this_thread::sleep_for(std::chrono::milliseconds{100} << (attempt - 1));
            }
            auto r = doRequest(url); // platform katmanı (WinHTTP/curl) — faz C
            if (!r) { last = *r.error(); continue; }
            if (r->status >= 200 && r->status < 300) return r;
            if (r->status >= 400 && r->status < 500 && r->status != 429)
                return std::unexpected{HttpError::permanent};
            last = HttpError::transient;
        }
        return std::unexpected{last};
    }

private:
    Config cfg_;
    [[nodiscard]] std::expected<HttpResult, HttpError>
    doRequest(const std::string&) const; // faz C'de platform implementasyonu
};

} // namespace cm::net
```

## §4 C# — HttpClient (istemci · nullable · ConfigureAwait(false))

```csharp
// K014/comms/http — dayanıklı istemci (TASARIM ÖRNEĞİ).
// Sınır: şifreleme üretmez (SocketsHttpHandler yerleşik TLS), anlam ayrıştırmaz.
#nullable enable
using System.Net;

namespace CoreMusic.Network.Http;

public sealed record HttpConfig(double ConnectSeconds = 3, double TotalSeconds = 10, int Retries = 3);

public sealed class HttpClientWrapper(HttpConfig? cfg = null, HttpMessageHandler? handler = null)
    : IDisposable
{
    private readonly HttpConfig _cfg = cfg ?? new HttpConfig();
    private readonly HttpClient _client = handler is null ? new HttpClient() : new HttpClient(handler);

    /// <summary>GET; geçici hatalarda üstel geri çekilme ile yeniden dener.</summary>
    /// <exception cref="InvalidOperationException">kalıcı hata (4xx, 429 hariç)</exception>
    public async Task<string> GetAsync(string url, CancellationToken ct = default)
    {
        Exception? last = null;
        for (var attempt = 0; attempt <= _cfg.Retries; attempt++)
        {
            if (attempt > 0)
                await Task.Delay(TimeSpan.FromMilliseconds(100 * Math.Pow(2, attempt - 1)), ct)
                          .ConfigureAwait(false);
            using var cts = CancellationTokenSource.CreateLinkedTokenSource(ct);
            cts.CancelAfter(TimeSpan.FromSeconds(_cfg.TotalSeconds));
            try
            {
                using var req = new HttpRequestMessage(HttpMethod.Get, url);
                using var res = await _client.SendAsync(req, cts.Token).ConfigureAwait(false);
                if (res.IsSuccessStatusCode)
                    return await res.Content.ReadAsStringAsync(cts.Token).ConfigureAwait(false);
                var code = (int)res.StatusCode;
                if (code >= 400 && code < 500 && code != 429)
                    throw new InvalidOperationException($"kalıcı hata: HTTP {code}");
                last = new HttpRequestException($"HTTP {code}");
            }
            catch (OperationCanceledException) when (!ct.IsCancellationRequested)
            {
                last = new TimeoutException("toplam süre doldu");
            }
        }
        throw last ?? new HttpRequestException("istek başarısız");
    }

    public void Dispose() => _client.Dispose();
}
```

## §5 Python 3.12+ — httpx tabanlı dayanıklı istemci (async)

```python
# K014/comms/http — async dayanıklı istemci (TASARIM ÖRNEĞİ).
# Sınır: TLS yerleşik (üretim yok), yanıt anlamı K012/K013.
from __future__ import annotations

import asyncio
from dataclasses import dataclass


@dataclass(frozen=True, slots=True)
class HttpConfig:
    connect_timeout: float = 3.0
    total_timeout: float = 10.0
    retries: int = 3


class TransientError(Exception):
    """Geçici hata — yeniden denenebilir (timeout, 5xx, 429)."""


class PermanentError(Exception):
    """Kalıcı hata — retry yok (4xx)."""


class HttpClient:
    def __init__(self, cfg: HttpConfig | None = None, session=None) -> None:
        self._cfg = cfg or HttpConfig()
        self._session = session  # aiohttp.ClientSession / httpx.AsyncClient — faz C

    async def get(self, url: str) -> str:
        last: Exception = TransientError("bilinmeyen")
        for attempt in range(self._cfg.retries + 1):
            if attempt:
                await asyncio.sleep(0.1 * 2 ** (attempt - 1))  # 100ms → 200ms → …
            try:
                async with asyncio.timeout(self._cfg.total_timeout):
                    status, body = await self._request(url)
            except asyncio.TimeoutError as exc:
                last = TransientError(str(exc))
                continue
            if 200 <= status < 300:
                return body
            if 400 <= status < 500 and status != 429:
                raise PermanentError(f"kalıcı hata: HTTP {status}")
            last = TransientError(f"HTTP {status}")
        raise last

    async def _request(self, url: str) -> tuple[int, str]:
        ...  # faz C: seçilen kütüphane ile doldurulur
```

## §6 Go — http.Client sarmalayıcı (istemci)

```go
// K014/comms/http — dayanıklı istemci (TASARIM ÖRNEĞİ).
// Sınır: TLS yerleşik (üretim yok), anlam ayrıştırma K012/K013'te.
package nethttp

import (
	"context"
	"errors"
	"fmt"
	"io"
	"net/http"
	"time"
)

type Config struct {
	Total   time.Duration
	Retries int
}

func DefaultConfig() Config { return Config{Total: 10 * time.Second, Retries: 3} }

type Client struct {
	http *http.Client
	cfg  Config
}

func New(cfg Config, transport http.RoundTripper) *Client {
	if transport == nil {
		transport = http.DefaultTransport // TLS yerleşik
	}
	return &Client{http: &http.Client{Transport: transport, Timeout: cfg.Total}, cfg: cfg}
}

// ErrPermanent kalıcı hatadır (4xx, 429 hariç); yeniden denenmez.
var ErrPermanent = errors.New("kalıcı HTTP hatası")

func (c *Client) Get(ctx context.Context, url string) (string, error) {
	var lastErr error
	for attempt := 0; attempt <= c.cfg.Retries; attempt++ {
		if attempt > 0 {
			select {
			case <-time.After(time.Duration(100<<uint(attempt-1)) * time.Millisecond):
			case <-ctx.Done():
				return "", ctx.Err()
			}
		}
		req, err := http.NewRequestWithContext(ctx, http.MethodGet, url, nil)
		if err != nil {
			return "", fmt.Errorf("istek kurulamadı: %w", err) // kalıcı
		}
		res, err := c.http.Do(req)
		if err != nil {
			lastErr = err // timeout/bağlantı — geçici
			continue
		}
		body, rerr := io.ReadAll(res.Body)
		_ = res.Body.Close()
		if rerr != nil {
			lastErr = rerr
			continue
		}
		switch {
		case res.StatusCode >= 200 && res.StatusCode < 300:
			return string(body), nil
		case res.StatusCode >= 400 && res.StatusCode < 500 && res.StatusCode != http.StatusTooManyRequests:
			return "", fmt.Errorf("%w: HTTP %d", ErrPermanent, res.StatusCode)
		default:
			lastErr = fmt.Errorf("HTTP %d", res.StatusCode)
		}
	}
	return "", lastErr
}
```

## §7 Rust — reqwest tabanlı istemci (async · taşınan tokio task)

```rust
// K014/comms/http — dayanıklı istemci (TASARIM ÖRNEĞİ).
// Sınır: TLS yerleşik (rustls/native-tls — üretim yok), anlam ayrıştırma K012/K013.
use std::time::Duration;

#[derive(Clone)]
pub struct HttpConfig {
    pub total: Duration,
    pub retries: u32,
}

impl Default for HttpConfig {
    fn default() -> Self {
        Self { total: Duration::from_secs(10), retries: 3 }
    }
}

#[derive(Debug, thiserror::Error)]
pub enum HttpError {
    #[error("kalıcı hata: HTTP {0}")]
    Permanent(u16),
    #[error("geçici hata: {0}")]
    Transient(String),
}

pub struct HttpClient {
    inner: reqwest::Client, // faz C: bağımlılık seçimi
    cfg: HttpConfig,
}

impl HttpClient {
    pub fn new(cfg: HttpConfig) -> Result<Self, HttpError> {
        let inner = reqwest::Client::builder()
            .timeout(cfg.total)
            .build()
            .map_err(|e| HttpError::Transient(e.to_string()))?;
        Ok(Self { inner, cfg })
    }

    pub async fn get(&self, url: &str) -> Result<String, HttpError> {
        let mut last = HttpError::Transient("bilinmeyen".into());
        for attempt in 0..=self.cfg.retries {
            if attempt > 0 {
                tokio::time::sleep(Duration::from_millis(100 << (attempt - 1))).await;
            }
            match self.inner.get(url).send().await {
                Ok(res) => {
                    let status = res.status().as_u16();
                    if res.status().is_success() {
                        return res
                            .text()
                            .await
                            .map_err(|e| HttpError::Transient(e.to_string()));
                    }
                    if (400..500).contains(&status) && status != 429 {
                        return Err(HttpError::Permanent(status));
                    }
                    last = HttpError::Transient(format!("HTTP {status}"));
                }
                Err(e) => last = HttpError::Transient(e.to_string()), // timeout/bağlantı
            }
        }
        Err(last)
    }
}
```

## §8 Dil Matrisi Özeti

| # | Dil | Rol | Örnek tipi | Sınır notu |
|---|---|---|---|---|
| 1 | PHP 8.4 | backend bridge | istemci | stream wrapper; şifreleme yok |
| 2 | JS ES6+ | Web UI | istemci (fetch) | tarayıcı yerleşik TLS |
| 3 | C++20 | Neva Engine | istemci arayüzü | platform API (WinHTTP/curl) faz C |
| 4 | C# | Windows kolu | istemci | SocketsHttpHandler yerleşik |
| 5 | Python | ağ testi/araç | istemci (async) | kütüphane seçimi faz C |
| 6 | Go | sunucu/tünel | istemci | net/http yerleşik |
| 7 | Rust | sistem | istemci | reqwest — bağımlılık seçimi faz C |

**Kapı:** 7/7 dil örneği üretildi; hiçbiri "verified üretim kodu" değildir — faz C'de
kütüphane seçimi sonrası güncellenir (standart-kanit.md §4 UNKNOWN satırı).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
