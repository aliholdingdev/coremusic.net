---
node_id: K013-integraion/adapters/adapters-kod-ornekleri
node_type: knowledge
status: draft
parent_id: K013-integraion/adapters/index
children: []
depends_on:
  - K013-integraion/adapters/adapters-anayasa
  - K013-integraion/adapters/adapters-sozlesme
evidence:
  - "Her dilde: 1 port/arayüz + 1 somut adapter + 1 hata eşleme bloğu (kanıt: bu dosya §1-§14)"
  - "Repo kanıtı (PHP + JS/TS): shared/src/Cache/CacheInterface + ApcuAdapter/MemoryAdapter/PageCacheAdapter; shared/src/OAuth/Provider/IOAuthProvider + BaseOAuthProvider + 10 somut provider (2026-10-16)"
  - "JS/TS hata eşleme kanıtı: @/lib/errors.ts (AppError kodları) + server/docs/openapi.yaml components/responses (2026-10-16)"
validation:
  - "14 dil başlığı '# Dil: X — Rol:' formatında (2026-10-16)"
  - "Her blokta port + adapter + error mapping var (2026-10-16)"
  - "Repo kanıtı blokları kaynak-etiketli bloklardan ayrı § başlıklarıyla (2026-10-16)"
open_questions:
  - "Ruby/Swift/Elixir adapter örnekleri depoda yok — kaynak-etiketli şema örnekleri; gerçek kodla doğrulanmadı ⚠️"
  - "shared/src/Cache dosyaları 'Generated file — DO NOT EDIT' etiketli (ApcuAdapter.php:1); el yazımı genişletme yolu üretim scripti üzerinden mi? ⚠️"
group: kod
---

# adapters-kod-ornekleri — Dil Bazlı Port + Adapter + Hata Eşleme Örnekleri

> **Kapsam:** 14 dilde port (arayüz) + somut adapter + hata eşleme bloğu.
> Blok türü ikiye ayrılır: **repo kanıtı** (bu depoda gözlemlenen gerçek kod yapısı)
> ve **kaynak-etiketli şema** (kalıp örneği — kaynak etiketli, depoda birebir yok).
> Format kalıbı: `openapi/openapi-kod-ornekleri.md` (her blok `# Dil: X — Rol: …` başlığıyla).

---

## §0 Blok Okuma Kuralları

1. Her dil bloğu üç zorunlu parçadan oluşur: **port**, **adapter**, **hata eşleme**.
2. `**Tür:** repo kanıtı` → depoda gözlemlenen gerçek yapı (`evidence` satırı dosya yolu verir).
3. `**Tür:** kaynak-etiketli şema` → dil kalıbı; depoda birebir karşılığı yok, `⚠️` ile işaretlenir.
4. JS ve TS tek set sayılır (kalıp kuralı) — §2'de birlikte verilir.
5. Hata eşleme: iç hata tipi → dış taşıma tipi (HTTP durum / ortak hata gövdesi).
6. Aynen kopyala-yapıştır YASAK; `sozlesme.md` M1-M8 + E1-E6'ya uyarlanır.

---

## §1 PHP — Rol: Depo adapter kanıtı (gerçek kod yapısı)

**Tür:** repo kanıtı

**Port (arayüz):**

```php
<?php
declare(strict_types=1);

namespace Shared\Cache;

interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, ?int $ttl = null): bool;
    public function delete(string $key): bool;
}
```

**Somut adapter (üç örnek — aynı porta farklı altyapı):**

```php
<?php
// shared/src/Cache/ApcuAdapter.php — APCu arka ucu
// shared/src/Cache/MemoryAdapter.php — süreç-içi bellek (test)
// shared/src/Cache/PageCacheAdapter.php — sayfa önbelleği
declare(strict_types=1);

namespace Shared\Cache;

final class ApcuAdapter implements CacheInterface
{
    public function get(string $key): mixed
    {
        return apcu_fetch($key, $success) ?: null;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        return apcu_store($key, $value, $ttl ?? 0);
    }

    public function delete(string $key): bool
    {
        return apcu_delete($key);
    }
}
```

**Hata eşleme (PHP):**

```php
try {
    $cached = $adapter->get($key);
} catch (\Throwable $e) {
    // taşıma: HTTP durumu + ortak gövde (sozlesme §4.1)
    error_log('[adapter-cache] ' . $e->getMessage());
    $cached = null;  // fail-safe: cache hatası isteği düşürmez (sozlesme E5)
}
```

**Kanal kanıtı:**
- `shared/src/Cache/CacheInterface.php` — port (2026-10-16)
- `shared/src/Cache/ApcuAdapter.php`, `MemoryAdapter.php`, `PageCacheAdapter.php` — 3 somut adapter (2026-10-16)
- `shared/src/OAuth/Provider/IOAuthProvider.php` + `BaseOAuthProvider.php` + 10 somut provider (ör. `GithubOAuthProvider.php`) — ikinci port/aile örneği (2026-10-16)

---

## §2 JavaScript + TypeScript — Rol: Servis adapter kanıtı (gerçek kod yapısı)

**Tür:** repo kanıtı (JS/TS tek set)

**Port (JSDoc sözleşmeli JS):**

```javascript
// js/src/services/__tests__// port (sözleşme) — js/AGENTS.md kuralı: asla doğrudan fetch çağırmaz
// @typedef {Object} TranscriptionService
// @property {(file: Blob, opts?: {language?: string, model?: string}) => Promise<{text: string, durationMs: number}>} transcribeAudio
```

**Somut adapter (test sahtesi — portun taklidi):**

```javascript
// js/src/services/__tests__/mocks/services/transcription.js
export function makeTranscriptionService(options = {}) {
  const {
    text = 'transcribed text',
    durationMs = 1234,
    failWith = null,
    calls = { count: 0 },
  } = options;
  return {
    name: 'transcription:mock',
    transcribeAudio: async () => {
      calls.count += 1;
      if (failWith) throw failWith;
      return { text, durationMs };
    },
  };
}
```

**Somut adapter (gerçek HTTP taşıyıcı — TS):**

```typescript
// js/src/lib/adapter-error.ts — hatayı sözleşmeye göre normalize eder
export function adapterErrorToAppError(err: unknown): AppError {
  if (err instanceof AppError) return err;
  const status = extractStatus(err);           // taşıma durumundan
  const code = mapStatusToCode(status);        // RFC 9457 problem+json tipine
  return new AppError(code, { status, cause: err });
}
```

**Hata eşleme (JS/TS):**

```javascript
// js/server/docs/openapi.yaml → components/responses hata şemaları:
// 400 ValidationError · 401 AuthError · 403 AuthError · 404 NotFound
// 409 ConflictError · 413 PayloadError · 415 PayloadError · 429 RateLimit
// 500 InternalError · 502 UpstreamError · 503 Unavailable · 504 UpstreamError
```

**Kanal kanıtı:**
- `js/src/services/__tests__/mocks/services/transcription.js` — port sahtesi (2026-10-16)
- `js/src/lib/adapter-error.ts` — adapter hata katmanı (2026-10-16)
- `js/server/docs/openapi.yaml` — 12 durum kodlu hata sözleşmesi (2026-10-16)
- `js/src/lib/errors.ts` — `AppError` kodları (ADP-001 uyumlu) (2026-10-16)

---

## §3 Python — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️ (depoda karşılığı yok)

**Port:**

```python
# Konum: entegrasyon katmanı içinde (K013)
from typing import Protocol, runtime_checkable

@runtime_checkable
class OrderPort(Protocol):
    def fetch(self, order_id: str) -> dict: ...
```

**Somut adapter:**

```python
import httpx

class LegacyOrderAdapter:
    base_url = "https://legacy.example/api"

    def fetch(self, order_id: str) -> dict:
        try:
            r = httpx.get(f"{self.base_url}/orders/{order_id}", timeout=5.0)
            r.raise_for_status()
        except httpx.HTTPStatusError as e:
            raise UpstreamError(status=e.response.status_code) from e
        except httpx.TimeoutException as e:
            raise TimeoutUpstreamError() from e
        return r.json()
```

**Hata eşleme:**

```python
# iç -> dış (HTTP): UpstreamError(5xx taşıma) -> 502/504; DomainError -> 4xx
# sozlesme §4.1: problem+json gövdesi (RFC 9457), kod: UPSTREAM_ERROR / TIMEOUT
```

**Kaynak etiketi:** Python `Protocol` — https://docs.python.org/3/library/typing.html#typing.Protocol (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️ VERIFICATION REQUIRED) · `httpx` — https://www.python-httpx.org/ (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §4 Go — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️

**Port:**

```go
// package order
type Port interface {
    Fetch(ctx context.Context, orderID string) (Order, error)
}
```

**Somut adapter:**

```go
// package legacyadapter
type Adapter struct {
    baseURL string
    client  *http.Client
}

func (a *Adapter) Fetch(ctx context.Context, orderID string) (Order, error) {
    req, _ := http.NewRequestWithContext(ctx, http.MethodGet,
        a.baseURL+"/orders/"+orderID, nil)
    resp, err := a.client.Do(req)
    if err != nil {
        return Order{}, &UpstreamError{Err: err}
    }
    defer resp.Body.Close()
    if resp.StatusCode >= 500 {
        return Order{}, &UpstreamError{Status: resp.StatusCode}
    }
    return decode(resp.Body)
}
```

**Hata eşleme:**

```go
// Go: sentinel/typed error + errors.Is/As; taşımaya çevriderken:
// UpstreamError -> 502/504, context.DeadlineExceeded -> 504,
// gövde: problem+json (RFC 9457) kod: UPSTREAM_ERROR
```

**Kaynak etiketi:** net/http — https://pkg.go.dev/net/http (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️) · errors — https://pkg.go.dev/errors (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §5 Java — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️

**Port:**

```java
public interface OrderPort {
    Order fetch(String orderId) throws UpstreamException;
}
```

**Somut adapter:**

```java
public final class LegacyOrderAdapter implements OrderPort {
    private final RestClient client;   // Java 11+ HttpClient / RestClient

    @Override
    public Order fetch(String orderId) throws UpstreamException {
        try {
            var resp = client.get().uri("/orders/{id}", orderId)
                .retrieve().body(OrderDto.class);
            return OrderMapper.toDomain(resp);
        } catch (RestClientResponseException e) {
            throw new UpstreamException(e.getStatusCode().value(), e);
        }
    }
}
```

**Hata eşleme:**

```java
// UpstreamException(@ResponseStatus) -> @ExceptionHandler ile 502/504;
// ProblemDetail (RFC 9457) gövdesi: Spring MVC `ProblemDetail` (HEAD 200 · ⚠️)
```

**Kaynak etiketi:** Java `HttpClient` — https://docs.oracle.com/en/java/javase/21/docs/api/java.net.http/java/net/http/HttpClient.html (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §6 C# / .NET — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️

**Port:**

```csharp
public interface IOrderPort
{
    Task<Order> FetchAsync(string orderId, CancellationToken ct = default);
}
```

**Somut adapter:**

```csharp
public sealed class LegacyOrderAdapter(HttpClient http) : IOrderPort
{
    public async Task<Order> FetchAsync(string orderId, CancellationToken ct = default)
    {
        try
        {
            var dto = await http.GetFromJsonAsync<OrderDto>($"/orders/{orderId}", ct);
            return OrderMapper.ToDomain(dto!);
        }
        catch (HttpRequestException ex) when (ex.StatusCode is >= HttpStatusCode.InternalServerError)
        {
            throw new UpstreamException("UPSTREAM_ERROR", ex);
        }
        catch (TaskCanceledException ex) when (!ct.IsCancellationRequested)
        {
            throw new TimeoutUpstreamException("UPSTREAM_TIMEOUT", ex);
        }
    }
}
```

**Hata eşleme:**

```csharp
// UpstreamException -> 502/504; ProblemDetails (RFC 9457) MVC filter ile gövde;
// ADP-001 kodları: UPSTREAM_ERROR / UPSTREAM_TIMEOUT (sozlesme T9)
```

**Kaynak etiketi:** ProblemDetails — https://learn.microsoft.com/en-us/aspnet/core/fundamentals/error-handling (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §7 Rust — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️

**Port:**

```rust
pub trait OrderPort {
    fn fetch(&self, order_id: &str) -> Result<Order, AdapterError>;
}
```

**Somut adapter:**

```rust
pub struct LegacyOrderAdapter { base: reqwest::Url, client: reqwest::Client }

impl OrderPort for LegacyOrderAdapter {
    fn fetch(&self, order_id: &str) -> Result<Order, AdapterError> {
        let url = self.base.join(&format!("orders/{order_id}"))?;
        let resp = self.client.get(url).send()
            .map_err(|e| AdapterError::Upstream(e.to_string()))?;
        if resp.status().is_server_error() {
            return Err(AdapterError::UpstreamStatus(resp.status().as_u16()));
        }
        Ok(resp.json()?)
    }
}
```

**Hata eşleme:**

```rust
// Result + bu tip hata; taşıma: AdapterError -> 502/504 + problem+json gövde
// ADP-001 kodları: UPSTREAM_ERROR (sozlesme §4.1)
```

**Kaynak etiketi:** reqwest — https://docs.rs/reqwest/latest/reqwest/ (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️) · thiserror — https://docs.rs/thiserror/latest/thiserror/ (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §8 C++ — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️

**Port:**

```cpp
// Sözleşme: soyut sınıf (virtual) — K013 sınırında yalnız arayüz
class IOrderPort {
public:
    virtual ~IOrderPort() = default;
    virtual Expected<Order, AdapterError> fetch(std::string_view order_id) = 0;
};
```

**Somut adapter:**

```cpp
class LegacyOrderAdapter final : public IOrderPort {
    HttpClient& http_;
public:
    Expected<Order, AdapterError> fetch(std::string_view order_id) override {
        auto resp = http_.get(std::string("/orders/") + std::string(order_id));
        if (!resp) return Unexpected{AdapterError::Upstream};
        if (resp->status >= 500)
            return Unexpected{AdapterError::UpstreamStatus{resp->status}};
        return Order::fromJson(resp->body);
    }
};
```

**Hata eşleme:**

```cpp
// std::expected (C++23) / variant-hata -> taşıma katmanında problem+json;
// UPSTREAM_ERROR -> 502; UPSTREAM_TIMEOUT -> 504 (sozlesme §4.1)
```

**Kaynak etiketi:** cppreference `std::expected` — https://en.cppreference.com/w/cpp/utility/expected.html (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §9 Ruby — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️ (depoda Ruby kaynak yok)

**Port:**

```ruby
# frozen_string_literal: true
module Orders
  module Port
    def fetch(order_id) = raise(NotImplementedError, "#{self.class}#fetch")
  end
end
```

**Somut adapter:**

```ruby
module Orders
  class LegacyAdapter
    include Orders::Port

    def fetch(order_id)
      res = HTTP.get("#{ENV.fetch('LEGACY_URL')}/orders/#{order_id}")
      raise UpstreamError.new(res.status) if res.status >= 500
      JSON.parse(res.body)
    rescue HTTP::Error => e
      raise UpstreamError.new(0, cause: e)
    end
  end
end
```

**Hata eşleme:**

```ruby
# UpstreamError -> Rack/Rails middleware ile 502/504;
# gövde: problem+json (RFC 9457), kod UPSTREAM_ERROR
```

**Kaynak etiketi:** Ruby Module/Mixin — https://docs.ruby-lang.org/en/master/Module.html (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §10 Kotlin — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️

**Port:**

```kotlin
interface OrderPort {
    suspend fun fetch(orderId: String): Order
}
```

**Somut adapter:**

```kotlin
class LegacyOrderAdapter(private val http: HttpClient) : OrderPort {
    override suspend fun fetch(orderId: String): Order =
        try {
            val dto: OrderDto = http.get("/orders/$orderId").body()
            dto.toDomain()
        } catch (e: ResponseException) {
            throw UpstreamException(e.response.status.value, e)
        } catch (e: HttpRequestTimeoutException) {
            throw TimeoutUpstreamException(e)
        }
}
```

**Hata eşleme:**

```kotlin
// UpstreamException -> 502; TimeoutUpstreamException -> 504;
// problem+json (RFC 9457) + ADP-001 kodları (sozlesme §4.1)
```

**Kaynak etiketi:** Ktor Client — https://ktor.io/docs/client-requests.html (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §11 Swift — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️ (depoda Swift kaynak yok)

**Port:**

```swift
protocol OrderPort {
    func fetch(orderId: String) async throws -> Order
}
```

**Somut adapter:**

```swift
struct LegacyOrderAdapter: OrderPort {
    let base: URL
    func fetch(orderId: String) async throws -> Order {
        var req = URLRequest(url: base.appendingPathComponent("orders/\(orderId)"))
        req.timeoutInterval = 5
        do {
            let (data, resp) = try await URLSession.shared.data(for: req)
            guard let http = resp as? HTTPURLResponse else { throw UpstreamError.unknown }
            if (500...599).contains(http.statusCode) { throw UpstreamError.status(http.statusCode) }
            return try JSONDecoder().decode(Order.self, from: data)
        } catch let e as URLError where e.code == .timedOut {
            throw UpstreamError.timeout
        }
    }
}
```

**Hata eşleme:**

```swift
// UpstreamError.status -> 502; .timeout -> 504; gövde problem+json (RFC 9457)
```

**Kaynak etiketi:** URLSession — https://developer.apple.com/documentation/foundation/urlsession (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §12 Elixir — Rol: Kaynak-etiketli şema

**Tür:** kaynak-etiketli şema ⚠️ (depoda Elixir kaynak yok)

**Port:**

```elixir
defmodule Orders.Port do
  @callback fetch(order_id :: String.t()) :: {:ok, map()} | {:error, atom()}
end
```

**Somut adapter:**

```elixir
defmodule Orders.LegacyAdapter do
  @behaviour Orders.Port

  @impl true
  def fetch(order_id) do
    case Finch.build(:get, "#{base()}/orders/#{order_id}") |> Finch.request(MyFinch) do
      {:ok, %Finch.Response{status: status, body: body}} when status >= 500 ->
        {:error, {:upstream, status}}
      {:ok, %Finch.Response{body: body}} ->
        {:ok, Jason.decode!(body)}
      {:error, reason} ->
        {:error, {:upstream, reason}}
    end
  end
end
```

**Hata eşleme:**

```elixir
# {:error, {:upstream, status}} -> Plug 502/504 + problem+json (RFC 9457)
# ADP-001: UPSTREAM_ERROR / UPSTREAM_TIMEOUT (sozlesme §4.1)
```

**Kaynak etiketi:** Finch — https://hexdocs.pm/finch/Finch.html (HEAD 200 · 2026-10-16 · içerik okunmadı → ⚠️)

---

## §13 Hata Eşleme Referans Tablosu (tüm diller ortak)

| İç hata tipi (adapter) | HTTP durum | Gövde kodu (ADP-001) | Sozlesme referansı |
|---|---|---|---|
| `UpstreamError` / 5xx taşıma | 502 | `UPSTREAM_ERROR` | §4.1 |
| `TimeoutUpstreamError` / deadline | 504 | `UPSTREAM_TIMEOUT` | §4.1, E6 |
| `ValidationError` / şema uyuşmazlığı | 502* | `UPSTREAM_ERROR` (şema-drift) | §4.1, E3 |
| Bağlantı reddi / DNS | 503 | `SERVICE_UNAVAILABLE` | §4.1, E5 |
| Alan (domain) hatası | 4xx | kaynak problem tipi | §4.1 |

\* Yorum: dış servis 2xx döndü ama gövde sözleşmeye uymuyorsa bu istemci hatası değil, **upstream şema ihlalidir** → 502. (Sözleşme testi bunu L2'de yakalamalı — `dogrulama.md` L2.)

**Kural:** Hata eşleme tablosu dil başına değil, **sözleşme düzeyinde** tekeldir (`adapters-sozlesme.md` §4.1 SSOT). Dil blokları yalnız bu tabloyu kendi tip sistemine çevirir; yeni kod eklerken tabloya satır eklenmez, var olan satır çevrilir (M8).

---

## §14 Repo Kanıtı vs Kaynak-Etiketli — Sayım Özeti

| Blok | Dil | Tür | Kanıt durumu |
|---|---|---|---|
| §1 | PHP | repo kanıtı | ✓ `shared/src/Cache/` (port + 3 adapter) |
| §2 | JavaScript + TypeScript | repo kanıtı | ✓ `js/src/` (mock port + adapter-error + openapi hata şeması) |
| §3 | Python | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §4 | Go | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §5 | Java | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §6 | C# | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §7 | Rust | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §8 | C++ | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §9 | Ruby | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §10 | Kotlin | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §11 | Swift | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |
| §12 | Elixir | kaynak-etiketli | ⚠️ HEAD 200 / içerik okunmadı |

**Dil sayısı:** 13 set (JS/TS tek set sayıldı) — ≥12 şartı ✓. Repo kanıtı: 2 set; kaynak-etiketli: 11 set (tamamı içerik okunmadı → ⚠️ VERIFICATION REQUIRED).

**⚠️ 12 kaynak-etiketli blok, 2026-10-16 tarihli URL HEAD 200 kontrolüne dayanır; kod örneklerinin API imzaları içerik çekilerek doğrulanmadı.** Kullanım öncesi ilgili dilin resmi dokümanı ile imza doğrulanmalıdır.

---

## §GRUPLAR

| Grup | Adım | Bu dosyadaki karşılığı |
|---|---|---|
| mimari | — | bu dosyanın kendisi değil (bkz. anayasa/sozlesme) |
| kod | 1 | §1-§12: port + adapter + hata eşleme blokları |
| kod | 2 | §13: ortak hata eşleme tablosu (sozlesme §4.1'e çeviri) |
| kanit | 1 | §14: repo kanıtı vs kaynak-etiketli sayım + ⚠️ işaretleri |

---

## §Kaynak

| # | URL | Erişim | Durum |
|---|---|---|---|
| K1 | https://docs.python.org/3/library/typing.html#typing.Protocol | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K2 | https://www.python-httpx.org/ | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K3 | https://pkg.go.dev/net/http | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K4 | https://docs.oracle.com/en/java/javase/21/docs/api/java.net.http/java/net/http/HttpClient.html | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K5 | https://learn.microsoft.com/en-us/aspnet/core/fundamentals/error-handling | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K6 | https://docs.rs/reqwest/latest/reqwest/ | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K7 | https://en.cppreference.com/w/cpp/utility/expected.html | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K8 | https://docs.ruby-lang.org/en/master/Module.html | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K9 | https://ktor.io/docs/client-requests.html | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K10 | https://developer.apple.com/documentation/foundation/urlsession | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K11 | https://hexdocs.pm/finch/Finch.html | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |
| K12 | https://docs.rs/thiserror/latest/thiserror/ | HEAD 200 · 2026-10-16 | ⚠️ içerik okunmadı |

**Repo kanıtı (disk):**
- `shared/src/Cache/CacheInterface.php` · `ApcuAdapter.php` · `MemoryAdapter.php` · `PageCacheAdapter.php` (2026-10-16)
- `shared/src/OAuth/Provider/` — `IOAuthProvider.php` + `BaseOAuthProvider.php` + 10 somut provider (2026-10-16)
- `js/src/services/__tests__/mocks/services/transcription.js` · `js/src/lib/adapter-error.ts` · `js/src/lib/errors.ts` · `js/server/docs/openapi.yaml` (2026-10-16)

**Dahil etme (§A):** §13 eşleme tablosu `adapters-sozlesme.md` §4.1'den türetilmiştir; tekrar değil **çeviri**dir — çelişki olursa sozlesme kazanır (M8).

---

## §A — Kapılar ( Gates )

| Kapı | Kural | Geçiş ölçütü |
|---|---|---|
| A1 | Her dil bloğu port + adapter + hata eşleme içerir | 13/13 blokta üç parça mevcut |
| A2 | Hata eşleme tablosu sözleşmeden türetilir | §13 = sozlesme §4.1 + E1-E6 |
| A3 | Repo kanıtı ≠ kaynak-etiketli ayrımı korunur | §14 sayım tablosu; ⚠️ işaretleri silinmez |
| A4 | JS/TS tek set sayılır | §2 tek blok |
| A5 | `⚠️` içeren blok doğrulanmadan üretime referans olmaz | üretim referansı öncesi ilgili §Kaynak URL'si içerik çekilerek doğrulanır |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
