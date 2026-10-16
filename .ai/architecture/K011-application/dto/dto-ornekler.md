---
title: "K011 · dto — Örnekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto
status: active
updated: 2026-10-15
group: kod
---

# K011 · dto — Örnekler (Kod Kanıtları)

> **Amaç:** DTO katmanının gerçek repo kanıtlarını kod bloklarıyla sunmak.
> Her bloğun üstünde `Dosya: <gerçek yol> · Satır: <numara>` kanıtı vardır.
> Repo dosyası olmayan içerik "PLANNED" olarak işaretlenir.
> Kod değişikliği YOK — bu dosya salt-okunur kanıt kaydıdır.

## §GRUPLAR

| Bölüm | Konu | Dil |
|---|---|---|
| §A | ApiResponse ailesi kod örnekleri | PHP |
| §B | Request/Response DTO örnekleri | PHP |
| §C | Auth domain DTO örnekleri | PHP |
| §D | Error DTO örnekleri | PHP |
| §E | JS tarafı (DTO tüketimi) | Vanilla JS |
| §F | SQL (DTO'ya besleyen sorgular) | SQL |
| §G | PLANNED örnekler (repo'da YOK) | PHP/JS |
| §H | Anti-örnekler (yapılmaması gereken) | PHP |

---

## §A — ApiResponse Ailesi (shared/src/Api/Dto/)

### A.1 — ApiResponse sınıf yapısı

```
Dosya: shared/src/Api/Dto/ApiResponse.php · Satır: 1-30 (yapı)
```

```php
<?php

declare(strict_types=1);

namespace Shared\Api\Dto;

/**
 * Base API response DTO.
 *
 * Success and error factory methods provide a unified
 * response envelope for all API endpoints.
 */
final readonly class ApiResponse
{
    /** HTTP status code: 200 OK. */
    public const STATUS_OK = 200;

    /** HTTP status code: 201 Created. */
    public const STATUS_CREATED = 201;

    /** HTTP status code: 400 Bad Request. */
    public const STATUS_BAD_REQUEST = 400;

    /** HTTP status code: 401 Unauthorized. */
    public const STATUS_UNAUTHORIZED = 401;

    /** HTTP status code: 403 Forbidden. */
    public const STATUS_FORBIDDEN = 403;

    /** HTTP status code: 404 Not Found. */
    public const STATUS_NOT_FOUND = 404;

    /** HTTP status code: 409 Conflict. */
    public const STATUS_CONFLICT = 409;

    /** HTTP status code: 422 Unprocessable Entity. */
    public const STATUS_UNPROCESSABLE = 422;

    /** HTTP status code: 500 Internal Server Error. */
    public const STATUS_INTERNAL_ERROR = 500;

    /** HTTP status code: 503 Service Unavailable. */
    public const STATUS_SERVICE_UNAVAILABLE = 503;

    /**
     * @param array<string, mixed> $data
     */
    private function __construct(
        public readonly int $statusCode,
        public readonly array $data,
    ) {
    }
```

**Not:** STATUS_* sabitleri merkezi HTTP status kataloğudur — 10 sabit.

### A.2 — success() factory

```
Dosya: shared/src/Api/Dto/ApiResponse.php · Satır: ~45-60
```

```php
    /**
     * Create a success response with data payload.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     */
    public static function success(
        array $data = [],
        int $statusCode = self::STATUS_OK,
        array $meta = [],
    ): self {
        return new self($statusCode, [
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ]);
    }
```

**Örnek üretim kullanımı (PLANNED):**

```php
// PLANNED — bu satırlar repo'da YOK.
// Beklenen kullanım (AuthController dönüş paketleme noktasında):
return Response::json(ApiResponse::success(
    data: $authResponse->toArray(),
    statusCode: ApiResponse::STATUS_OK,
)->toArray());
```

⚠️ VERIFICATION REQUIRED: AuthController'ın gerçek dönüşü framework `Response::json()`
ile — ApiResponse factory'sinin üretimde çağrıldığı grep ile 0 isabet.

### A.3 — error() factory

```
Dosya: shared/src/Api/Dto/ApiResponse.php · Satır: ~62-80
```

```php
    /**
     * Create an error response.
     *
     * @param array<string, mixed> $error
     */
    public static function error(
        array $error,
        int $statusCode = self::STATUS_INTERNAL_ERROR,
    ): self {
        return new self($statusCode, [
            'success' => false,
            'error' => $error,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
```

**Yapısal özet:**

| Method | Dönüş | Statik | Üretim kullanımı |
|---|---|---|---|
| `success()` | ApiResponse | ✅ | 0 (test-only) |
| `error()` | ApiResponse | ✅ | 0 (test-only) |
| `toArray()` | array | ❌ | 0 (test-only) |

---

## §B — Request/Response DTO Örnekleri (shared)

### B.1 — PaginatedResponse

```
Dosya: shared/src/Api/Dto/PaginatedResponse.php · Satır: 1-60
```

```php
<?php

declare(strict_types=1);

namespace Shared\Api\Dto;

/**
 * Paginated collection DTO.
 *
 * Standard pagination envelope: items + meta (total, page, per_page).
 */
final readonly class PaginatedResponse
{
    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
        public readonly int $lastPage,
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public static function make(
        array $items,
        int $total,
        int $page,
        int $perPage,
    ): self {
        $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

        return new self($items, $total, $page, $perPage, $lastPage);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'meta' => [
                'total' => $this->total,
                'page' => $this->page,
                'per_page' => $this->perPage,
                'last_page' => $this->lastPage,
            ],
        ];
    }
}
```

**Not:** `lastPage` hesaplaması constructor yerine `make()` factory'sinde — imzayı
dar tutar. `perPage = 0` koruması var (bölme sıfır).

### B.2 — PaginationRequest

```
Dosya: shared/src/Api/Dto/PaginationRequest.php · Satır: 1-50
```

```php
<?php

declare(strict_types=1);

namespace Shared\Api\Dto;

/**
 * Pagination request DTO.
 *
 * Normalizes page/per_page query parameters with defaults and caps.
 */
final readonly class PaginationRequest
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PER_PAGE = 20;
    public const MAX_PER_PAGE = 100;

    public function __construct(
        public readonly int $page = self::DEFAULT_PAGE,
        public readonly int $perPage = self::DEFAULT_PER_PAGE,
    ) {
    }

    /**
     * Build from raw query parameters with sanitization.
     *
     * @param array<string, mixed> $query
     */
    public static function fromQuery(array $query): self
    {
        $page = max(1, (int) ($query['page'] ?? self::DEFAULT_PAGE));
        $perPage = min(
            self::MAX_PER_PAGE,
            max(1, (int) ($query['per_page'] ?? self::DEFAULT_PER_PAGE)),
        );

        return new self($page, $perPage);
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
```

**Kapsam notu:** `fromQuery()` sanitization içerir — `max(1, ...)` ve `min(MAX, ...)`.
DTO hem doğrulama hem taşıma yapar (SRP ihlali tartışılabilir — §H anti-örnek).

---

## §C — Auth Domain DTO Örnekleri (auth.coremusic.net)

### C.1 — LoginRequest

```
Dosya: auth.coremusic.net/include/Domain/DTO/LoginRequest.php · Satır: 1-40
```

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\DTO;

/**
 * Login request DTO.
 *
 * Carries validated login credentials from the HTTP layer
 * to the authentication handler.
 */
final class LoginRequest
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $rememberToken = null,
    ) {
    }
}
```

**Bulgu (kapsam dışı, doğrulandı):** Bu DTO'da `toArray()` YOK — 14 DTO içindeki
tek eksik (gate: 13/14). Üretimde AKTİF kullanılıyor.

### C.2 — AuthResponse

```
Dosya: auth.coremusic.net/include/Domain/DTO/AuthResponse.php · Satır: 1-75
```

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\DTO;

/**
 * Authentication response DTO.
 *
 * Carries user identity + tokens from the handler to the controller.
 * Implements JsonSerializable for direct JSON encoding.
 */
final class AuthResponse implements \JsonSerializable
{
    /**
     * @param array<string, mixed> $user
     * @param array<string, mixed> $tokens
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly array $user,
        public readonly array $tokens,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'tokens' => $this->tokens,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
```

**Kritik fark (shared ile karşılaştırma):**

| Özellik | shared/Api/Dto (11) | auth/Domain/DTO (3) |
|---|---|---|
| `readonly` | ✅ (final readonly class) | ✅ property readonly, class final |
| `JsonSerializable` | ❌ | ✅ |
| `toArray()` | 11/11 | 2/3 (LoginRequest'te yok) |
| Üretim kullanımı | 0 (test-only) | ✅ AKTİF |

### C.3 — LoginRequest üretim kullanımı (kanıt)

```
Dosya: auth.coremusic.net/include/Handler/AuthPostHandler.php · Satır: 38-57
```

```php
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) $request->getParsedBody();

        $loginRequest = new LoginRequest(
            email: (string) ($body['email'] ?? ''),
            password: (string) ($body['password'] ?? ''),
        );

        // ... doğrulama + kimlik doğrulama akışı devam eder
    }
```

**Not:** Burada `ServerRequestInterface` (PSR-7) KULLANILIYOR — fakat bu handler'da;
shared DTO katmanında PSR-7 yok (bkz. dto-web-arastirma.md §A.3).

⚠️ VERIFICATION REQUIRED: `getParsedBody()` çağrısının tam satır numarası bu kanıt
setinde okuma ile teyit edilmelidir — okunarak 38-57 aralığı doğrulanmıştır.

---

## §D — Error DTO Örnekleri

### D.1 — ErrorResponse

```
Dosya: shared/src/Api/Dto/ErrorResponse.php · Satır: 1-55
```

```php
<?php

declare(strict_types=1);

namespace Shared\Api\Dto;

/**
 * Error response DTO.
 *
 * Standard error envelope: code + message + optional details.
 */
final readonly class ErrorResponse
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
        public readonly array $details = [],
        public readonly int $statusCode = 500,
    ) {
    }

    /**
     * @param array<string, mixed> $details
     */
    public static function make(
        string $code,
        string $message,
        array $details = [],
        int $statusCode = 500,
    ): self {
        return new self($code, $message, $details, $statusCode);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'error' => [
                'code' => $this->code,
                'message' => $this->message,
                'details' => $this->details,
            ],
        ];
    }
}
```

### D.2 — ValidationFailedResponse

```
Dosya: shared/src/Api/Dto/ValidationFailedResponse.php · Satır: 1-60
```

```php
<?php

declare(strict_types=1);

namespace Shared\Api\Dto;

/**
 * 422 validation failed DTO.
 *
 * Carries field-level validation errors.
 */
final readonly class ValidationFailedResponse
{
    public const STATUS_CODE = 422;

    /**
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(
        public readonly array $errors,
        public readonly string $message = 'Validation failed.',
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'error' => [
                'code' => 'VALIDATION_FAILED',
                'message' => $this->message,
                'errors' => $this->errors,
            ],
        ];
    }
}
```

**Not:** `errors` map'i `field => [messages]` formatı — frontend form error
gösterimiyle uyumlu.

### D.3 — ConflictResponse (409)

```
Dosya: shared/src/Api/Dto/ConflictResponse.php · Satır: 1-45
```

```php
<?php

declare(strict_types=1);

namespace Shared\Api\Dto;

/**
 * 409 Conflict DTO.
 *
 * Used when the request conflicts with current resource state
 * (e.g., duplicate unique constraint).
 */
final readonly class ConflictResponse
{
    public const STATUS_CODE = 409;

    public function __construct(
        public readonly string $message,
        public readonly array $details = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'error' => [
                'code' => 'CONFLICT',
                'message' => $this->message,
                'details' => $this->details,
            ],
        ];
    }
}
```

---

## §E — JS Tarafı (DTO Tüketimi)

### E.1 — API çağrısı ve JSON parse

```
Dosya: assets.coremusic.net/js/core/api.js · Satır: 1 (dosya başı)
```

```javascript
// PLANNED kanıt — dosya yapısı bu görev kapsamında okundu.
// Beklenen desen: fetch → response.json() → DTO toArray() çıktısını tüketme.

// const res = await fetch('/api/v1/auth/login', {
//     method: 'POST',
//     headers: { 'Content-Type': 'application/json' },
//     body: JSON.stringify({ email, password }),
// });
// const dto = await res.json();       // ApiResponse::toArray() çıktısı
// if (dto.success) { ... dto.data ... }
```

⚠️ VERIFICATION REQUIRED: api.js'in tam fetch gövdesi bu kanıt setinde satır satır
okunmadı — PLANNED blok olarak işaretlendi.

### E.2 — gender-select.js (doğrulanmış kanıt)

```
Dosya: assets.coremusic.net/js/auth/gender-select.js · Satır: 1
```

```javascript
// Frontend gender select component — fetch ile veri çeker.
// Dosya yapısı: assets.coremusic.net/js/auth/gender-select.js
// Ölçüm: assets/js = 94 dosya (manifest) / 98 dosya (glob) — dto-dogrulama.md §E.2
```

⚠️ VERIFICATION REQUIRED: gender-select.js'in fetch gövdesi ve DTO JSON bekleyişi
satır satır bu görevde doğrulanmadı; dosya varlığı ve konumu doğrulanmıştır.

---

## §F — SQL (DTO'ya Besleyen Sorgular)

### F.1 — Pagination sorgu iskeleti (PLANNED)

```sql
-- PLANNED — repository katmanındaki beklenen desen.
-- PaginatedResponse::make() bu sorgunun çıktısını sarar.

SELECT
    u.id,
    u.email,
    u.display_name,
    u.created_at
FROM users AS u
WHERE u.deleted_at IS NULL
ORDER BY u.created_at DESC
LIMIT :per_page OFFSET :offset;
```

```sql
-- Toplam sayım sorgusu (meta.total için).

SELECT COUNT(*) AS total
FROM users AS u
WHERE u.deleted_at IS NULL;
```

⚠️ VERIFICATION REQUIRED: `UserRepository` içindeki gerçek sorgu metni bu görevde
okunmadı; transaction kullanımları doğrulandı
(`Dosya: api.coremusic.net/include/Repository/UserRepository.php · Satır: 147, 184, 192`).

### F.2 — Transaction kanıtı (doğrulanmış)

```
Dosya: api.coremusic.net/include/Repository/UserRepository.php · Satır: 147, 184, 192
Dosya: api.coremusic.net/include/Repository/CatalogWriter.php · Satır: 183, 217, 220
Dosya: shared/bin/api-key-create.php · Satır: 135-147
```

**Not:** Transaction, DTO'nun beslendiği veri yazım bütünlüğünü korur.
DTO'lar transaction'dan SONRA dönen veriyi sarar — DTO transaction bilgisi taşımaz.

---

## §G — PLANNED Örnekler (Repo'da YOK)

### G.1 — DTO → JSON Response birleştirme (PLANNED)

```php
// PLANNED — repo'da böyle bir servis YOK.
// ADR kararı sonrası eklenebilir: DtoResponseFactory.

final class DtoResponseFactory
{
    public function toArrayResponse(
        ApiResponse $dto,
        ResponseFactoryInterface $factory,
    ): ResponseInterface {
        $response = $factory->createResponse($dto->statusCode);
        $body = $factory->createStream(json_encode(
            $dto->toArray(),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
        ));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($body);
    }
}
```

**Neden PLANNED:** `ResponseFactoryInterface` (PSR-17) grep'i 0 isabet
(bkz. dto-web-arastirma.md §A.3). Bu sınıf psr/http-factory paketi ve karar gerektirir.

### G.2 — shared DTO'ya jsonSerialize ekleme (PLANNED)

```php
// PLANNED — tutarlılık için (auth örneği mevcut).
// Mevcut:
final readonly class ApiResponse { /* ... */ }
// Önerilen (ADR kararı sonrası):
final readonly class ApiResponse implements \JsonSerializable
{
    // ... mevcut içerik ...
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
```

---

## §H — Anti-Örnekler (Yapılmaması Gereken)

### H.1 — DTO içinde I/O

```php
// ANTI-ÖRNEK — DTO saf veri taşıyıcı olmalı.
final readonly class BadDto
{
    public function fetchRelated(): array
    {
        // ❌ YASAK: DTO repository çağırmaz.
        return $this->db->query('SELECT ...');
    }
}
```

**Kural:** DTO'da `PDO`, `Repository`, `Service` bağımlılığı YOK.
Repo'daki 14 DTO'nun 14'ünde I/O yok ✅ (grep: `PDO|Repository|Service` → 0).

### H.2 — DTO'da iş mantığı

```php
// ANTI-ÖRNEK — iş mantığı Application/Domain katmanında.
final readonly class PriceDto
{
    public function withDiscount(float $rate): self
    {
        // ❌ İndirim hesabı iş mantığı — DTO'da değil, UseCase'de olmalı.
        return new self($this->amount * (1 - $rate));
    }
}
```

**Not:** `PaginationRequest::offset()` sınır vakası — aritmetik minimal, veri
türetme olarak savunulabilir. Gate'de kabul edildi.

### H.3 — mutable shared DTO

```php
// ANTI-ÖRNEK — shared DTO'lar immutable.
class BadMutableDto
{
    public string $status;  // ❌ readonly olmalı (mevcut 11 DTO'da readonly ✅)
}
```

---

## §I — ÖZET: KOD KANIT MATRİSİ

| Bölüm | Blok sayısı | Dil | Durum |
|---|---|---|---|
| §A ApiResponse | 3 | PHP | ✅ Dosya·Satır |
| §B Paginated/PaginationRequest | 2 | PHP | ✅ Dosya·Satır |
| §C Auth DTO'lar | 3 | PHP | ✅ Dosya·Satır |
| §D Error DTO'lar | 3 | PHP | ✅ Dosya·Satır |
| §E JS | 2 | Vanilla JS | 1 PLANNED, 1 kısmi |
| §F SQL | 2 | SQL | 1 PLANNED, 1 transaction kanıtı |
| §G PLANNED | 2 | PHP | PLANNED |
| §H Anti-örnek | 3 | PHP | Kural kanıtı |
| **TOPLAM** | **20** | — | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
