---
node_id: K009-security/rate-limiting/rate-limiting-bilesenler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/rate-limiting/rate-limiting-anayasa
evidence:
  - shared/src/Middleware/RateLimiterMiddleware.php
  - shared/src/Security/CacheRateLimiter.php
  - shared/src/Api/Middleware/RateLimitMiddleware.php
  - shared/src/Contracts/IRateLimiter.php
  - shared/src/Exception/RateLimitException.php
  - shared/src/Cache/CacheInterface.php
  - auth.coremusic.net/include/Container/AuthContainer.php:61-71
validation:
  - AD-01, AD-05, AD-14 (rate-limiting-dogrulama.md)
open_questions:
  - CacheManager adapter seçim zinciri (APCu → Memory) canlı ortamda UNKNOWN
group: mimari
---

# K009 · rate-limiting · Bileşenler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Mimari katman — rate limiting bileşen envanteri

Bu belge, rate limiting'i oluşturan **sınıf/sözleşme/depo bileşenlerini**,
rollerini, bağımlılıklarını ve DI bağlantılarını listeler. Davranış
`[[rate-limiting-surucu]]`, kod `[[rate-limiting-ornekler]]`,
kontroller `[[rate-limiting-dogrulama]]` içindedir.

## BAĞLANTILAR

| Link | Ne verir |
|---|---|
| [[rate-limiting-anayasa]] | Bileşenlerin uyguladığı kararlar |
| [[rate-limiting-surucu]] | Bileşenler arası akış |
| [[rate-limiting-ornekler]] | Bileşen kodlarının birebir alıntısı |
| [[rate-limiting-dogrulama]] | Bileşen başına AD kanıtı |
| [[rate-limiting-kapsam]] | Kapsam sınırları |
| [[security-master]] | K009 güvenlik düğümü ana sayfası |

---

## 1. Bileşen Envanteri

| # | Bileşen | Dosya | Rol |
|---|---|---|---|
| B1 | `RateLimiterMiddleware` | `shared/src/Middleware/RateLimiterMiddleware.php` | HTTP ana sürücü: 429/503 üretir, sayaç yazar |
| B2 | `CacheRateLimiter` | `shared/src/Security/CacheRateLimiter.php` | `IRateLimiter` uygulaması — auth servisi sayaç erişimi |
| B3 | `RateLimitMiddleware` | `shared/src/Api/Middleware/RateLimitMiddleware.php` | API pipe sürücü (`api:`/`ip:` anahtarı) |
| B4 | `IRateLimiter` | `shared/src/Contracts/IRateLimiter.php` | Sözleşme: `isLimited` / `increment` (+`reset`) |
| B5 | `RateLimitException` | `shared/src/Exception/RateLimitException.php` | Servis katmanı 429 istisnası + `Retry-After` değeri |
| B6 | `CacheInterface` | `shared/src/Cache/CacheInterface.php` | Sayaç deposu sözleşmesi (`set`/`get`/`increment`/`delete`) |
| B7 | `ApcuAdapter` | `shared/src/Cache/ApcuAdapter.php` | Birincil depo: APCu (`apcu_store`/`apcu_inc`) |
| B8 | `MemoryAdapter` | `shared/src/Cache/MemoryAdapter.php` | Fallback depo: süreç içi bellek |
| B9 | `CacheManager` | `shared/src/Cache/CacheManager.php` | Adaptör seçici (`getAdapter()`) |
| B10 | `PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php` | Genel uç eşik sabitleri (60/60) |
| B11 | `AuthContainer` | `auth.coremusic.net/include/Container/AuthContainer.php` | DI: `IRateLimiter → CacheRateLimiter(CacheManager::getAdapter())` |
| B12 | `AuthService` | `auth.coremusic.net/include/Service/AuthService.php` | İki boyutlu sayaç mantığı (login/register/reset) |
| B13 | `AuthController` | `auth.coremusic.net/include/Controller/AuthController.php` | `RateLimitException → 429 + Retry-After` eşlemesi |
| B14 | `LoggerFactory::securityEvent` | `shared/src/Log/LoggerFactory.php` | Gözlemlenebilirlik: `rate_limiter_unavailable`, `rate_limited`, `rate_limiter_increment_failed` |
| B15 | `RateLimiterMiddlewareTest` | `shared/tests/Middleware/RateLimiterMiddlewareTest.php` | 10 birim testi (doğrulama katmanı) |

---

## 2. Sözleşmeler

### 2.1 `CacheInterface` (sayaç deposu)

**Dosya:** `shared/src/Cache/CacheInterface.php` · **Satır:** 5-13

```php
interface CacheInterface
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl = 0): bool;
    public function delete(string $key): bool;
    public function increment(string $key, int $step = 1): int|false;
    public function clear(): bool;
    public function has(string $key): bool;
}
```

**Kritik imza notu:** `set(): bool` — arayüz sözleşmesi "başarı" bildirir;
**"anahtar yeni mi" bildirmez.** Buna rağmen `RateLimiterMiddleware:74-77`
dönüşü `$isNew` diye yorumlar. Bu **sözleşme/istemci uyuşmazlığı**,
`[[rate-limiting-dogrulama]]` AD-06'daki ⚠️ bulucusunun kaynağıdır.
`increment(): int|false` ise başarısızlıkta `false` — middleware bu `false`
dalını fail-open olarak işler (`:80-84`).

### 2.2 `IRateLimiter` (servis sözleşmesi)

**Dosya:** `shared/src/Contracts/IRateLimiter.php` (10 satır)

Sözleşme üç işlem: `isLimited(key, max, window): bool`,
`increment(key, window): void`, `reset(key): void`. Tek uygulaması
`CacheRateLimiter` (B2).

### 2.3 `RateLimitException`

**Dosya:** `shared/src/Exception/RateLimitException.php` · **Satır:** 5-33

- `private __construct` + statik fabrikalar: `loginRateLimited`,
  `registerRateLimited`, `passwordResetRateLimited` — her biri `$seconds`
  `Retry-After` değeri ve `429` HTTP kodu taşır (`:11`).
- `getRetryAfter(): int` (`:15-18`) → controller bu değeri
  `Retry-After` başlığına yazar (`AuthController.php:62`).

---

## 3. Bağımlılık Grafiği

```
PageRouterKernel (B10, 60/60 sabitleri)
        │
        ▼
RateLimiterMiddleware (B1) ──resolveCacheAdapter──► CacheManager (B9)
        │                                              │
        │ (429/503 yanıt)                              ├─► ApcuAdapter (B7)
        ▼                                              └─► MemoryAdapter (B8)
   LoggerFactory::securityEvent (B14)                      ▲
                                                           │ CacheInterface (B6)
AuthContainer (B11) ── IRateLimiter ──► CacheRateLimiter (B2) ┘
        │
        ▼
AuthService (B12) ──throw──► RateLimitException (B5) ──► AuthController (B13) ──► 429

api.coremusic.net/index.php ──pipe──► RateLimitMiddleware (B3) ──► CacheRateLimiter (B2)
```

**Sıra kuralı:** Bağımlılıklar tek yönlüdür — servis (B12) cache'i (B6-B9)
**doğrudan** çağırmaz, hep `IRateLimiter` (B4) üzerinden çağırır. Bu,
hexagonal sınırı korur (`shared/AGENTS.md` §4: `src/` içinde subdomain iş
kuralı gömmez; auth sayaç mantığı `AuthService`'te kalır).

---

## 4. DI Bağlantısı (gerçek kablo)

**Dosya:** `auth.coremusic.net/include/Container/AuthContainer.php` · **Satır:** 61-71

```php
IRateLimiter::class => function () {
    return new CacheRateLimiter(CacheManager::getAdapter());
},

IAuthService::class => \DI\autowire(AuthService::class)
    ->constructor(
        \DI\get(IUserRepository::class),
        \DI\get(ISessionManager::class),
        \DI\get(IRateLimiter::class),
        $_ENV['APP_PEPPER'] ?? '',
    ),
```

**Okuma notu:**

- `CacheRateLimiter` **her zaman** `CacheManager::getAdapter()` ile kurulur —
  adaptör seçimi (APCu/Memory) tek noktadır (B9).
- `getAdapter()` RuntimeException fırlatırsa? `RateLimiterMiddleware`
  bunu `resolveCacheAdapter()`'da yakalayıp `null`'a çevirir
  (`:129-136`); **AuthContainer'da aynı koruma görünmemiştir** — konteyner
  kapanışında (`function ()`) yakalama yoktur ⚠️ (konteyner düzeyi hata
  davranışı UNKNOWN).
- `$_ENV['APP_PEPPER']` oradan gelir; secret'lar vault'a yazılmaz
  (guardrail).

---

## 5. Middleware Bağlantısı (HTTP)

**Dosya:** `auth.coremusic.net/index.php` · **Satır:** 75-119 (özet okuma:
`RateLimiterMiddleware` yanıtının `halt` işlenmesi). `halt: true` dönen
yanıtlar (429/503) front controller tarafından basılır; `$next` zinciri
kesilir. `api.coremusic.net/index.php:140-164` ise B3'ü pipe'a dizer ve
`withLimits()` ile uç başına eşik verir (`RateLimitMiddleware.php:73-79`).

---

## 6. Gözlemlenebilirlik Bileşeni

`RateLimiterMiddleware` üç securityEvent üretir:

| Olay | Tetik | Satır |
|---|---|---|
| `rate_limiter_unavailable` (`mode: fail-closed`) | depo yok + auth ucu | `:57` |
| `rate_limiter_unavailable` (`mode: fail-open`) | depo yok + genel uç | `:67` |
| `rate_limiter_increment_failed` | `increment` false/null | `:81` |
| `rate_limited` (`code: 429`) | eşik aşıldı | `:88-92` |

**Anlam:** fail-open ve fail-closed **her ikisi de loglanır** → olay
sonradan `log` üzerinden denetlenebilir (P5-32 notu: 429 artık gözlemlenir,
`:87`). Bu, `.ai/AGENTS.md` §18 "fail-open tespiti" için **ham veri
kaynağıdır**.

---

## 7. Test Bileşeni

`shared/tests/Middleware/RateLimiterMiddlewareTest.php` (319 satır, 10
test) bağımsız `CacheInterface` sahtesi ile hem fail-open hem fail-closed
dallarını sürer (envanter: `[[rate-limiting-dogrulama]]` §3).

**Eksik bileşen-test eşleşmeleri ⚠️:**

| Bileşen | Test | Durum |
|---|---|---|
| B1 `RateLimiterMiddleware` | `RateLimiterMiddlewareTest` (10 test) | ✅ var |
| B2 `CacheRateLimiter` | — | ⚠️ yok |
| B3 `RateLimitMiddleware` (API) | — | ⚠️ yok |
| B12 `AuthService` iki boyutlu sayaç | — | ⚠️ yok (auth testleri ValueObject ağırlıklı) |

---

## 8. Bileşen Karar Özeti

- **Tek depo, iki arayüz:** B6 (`CacheInterface`) hem B1'in doğrudan
  cache erişimi hem B2'nin erişimi için ortaktır → `set()` uyuşmazlığı
  iki katmanı birden etkiler.
- **İki istisna yolu:** B5 (servis) ile B1'in doğrudan yanıt dizisi
  (middleware) — ikisi de 429 üretir ama `Retry-After` kaynağı farklıdır
  (istisna `getRetryAfter()` vs `windowSeconds`).
- **Sorumluluk ayrımı:** Eşik/süreç **sabitleri** proje sahibinde
  (AuthService/PageRouterKernel), **mekanik** shared katmanda
  (`shared/AGENTS.md` §2 envanteri: `src/Middleware/` RateLimiter ADR-013).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
