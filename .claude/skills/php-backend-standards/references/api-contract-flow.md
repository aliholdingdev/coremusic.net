---
title: "php-backend-standards — API Contract Flow"
type: reference
category: skills
version: 4.0.0
status: active
date: 2026-10-21
updated: 2026-10-21
---

# API Contract Flow — Derin Referans

> **Kaynaklar (disk, 2026-10-21):** `.ai/CLAUDE.md` §6A (API-First) ·
> `.ai/.decisions/accepted/ADR-084-api-gateway-architecture.md` ·
> `.ai/.decisions/accepted/ADR-020-api-public-security` (routes.php/Validator üstü yorumlar) ·
> `api.coremusic.net/config/routes.php` · `shared/src/Api/Routing/RouteTable.php` ·
> `shared/src/Api/ApiResponse.php` · `shared/src/Api/Middleware/*` ·
> `.ai/.templates/documentation/api-doc-template.md` · `references/sources.md`.

## §1 Hedef Akış (ADR-084 / `.ai/CLAUDE.md` §6A)

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

**Hiçbir endpoint doğrudan kodlanmaz.** Fakat disk gerçeği şudur:

| Adım | Disk durumu | Kanıt |
|------|-------------|-------|
| OpenAPI Spec | **PLANNED — dosya yok** | `openapi*.{yaml,yml,json}` glob → 0 (api-doc-template §1 §2.2) |
| DTO | **IMPLEMENTED** (11 dosya) | `shared/src/Api/Dto/Request/` 5 · `Dto/Response/` 6 |
| Contract (route tanımı) | **IMPLEMENTED** | `api.coremusic.net/config/routes.php` + `RouteTable` |
| Validation | **IMPLEMENTED** | `RequestValidationMiddleware` + `Validator` (422) |
| Use Case | **PLANNED** | `shared/src/UseCase/` glob → 0; akış şimdilik Controller → Service → Repository |
| Kod (Controller) | **IMPLEMENTED** | `api.coremusic.net/include/Controller/AuthController.php` |

> **OpenAPI henüz yazılmadı → "spec-first uygulanıyor" DİYİLEMEZ.**
> Uygulanan: `routes.php` tabanlı **contract-first**. Spec yazılana kadar akış
> `Contract (routes.php) → DTO → Validation → Controller → Repository` şeklindedir.
> `⚠️ VERIFICATION REQUIRED`: spec üretimi/tarihi diskte yok.

## §2 Route Contract — `routes.php` Alanları

`RouteTable::add()` (`shared/src/Api/Routing/RouteTable.php:42-62`) her kaydı
şu alanlarla normalize eder:

| Alan | Tip | Anlamı | Okuyan |
|------|-----|--------|--------|
| `service` | string | Container çözümleme anahtarı | Gateway |
| `handler` | string | Controller servis adı | Gateway |
| `action` | string | Controller dispatch anahtarı (`AuthController::handle()`) | Controller |
| `public` | bool | Auth'sız erişim | Authentication / Authorization |
| `implemented` | bool | **false → route tanımlı ama handler yok → 405 + Allow (404 DEĞİL)** | RouteTable::find (satır 119) |
| `validation` | array\|null | Kural seti → **422 + `error.fields`** | RequestValidationMiddleware |
| `requiredRole` | string\|null | RBAC rol | AuthorizationMiddleware |
| `requiredPermission` | string\|null | RBAC izin | AuthorizationMiddleware |
| `cacheable` / `cacheTtl` | bool / int | ETag + Cache-Control | ResponseNormalizationMiddleware |
| `method` / `path` | string | `add()` tarafından yazılır (uppercase / normalize) | tüm pipeline |

### §2.1 Üç sonuç (RouteTable docblock)

| Sonuç | HTTP | Gövde |
|-------|------|-------|
| `ok` | handler çağrılır | success |
| `method_not_allowed` | **405 + `Allow:`** yalnız gerçekten sunulan methodlar | `Handler not implemented yet` (Gateway) |
| `not_found` | **404** `NOT_FOUND` | `Route not found` |

`Allow` yalnız `implemented: true` olan methodları listeler (`Gateway:119-121`) —
yani bir uç PATCH'e `implemented: false` ile eklenmişse GET/PATCH ayrımı
doğru yansır, istemciye yanlış umut verilmez.

## §3 Doğrulama Katmanı — 4 Kural Ailesi

`routes.php` `validation` değeri `Validator::validate(array $data, array $rules): array`
tarafından işletilir. Desteklenen kural anahtarları (disk kanıtı: routes.php
+ Validator):

| Kural | Örnek | Hata |
|-------|-------|------|
| `required` | `['required' => true]` | zorunlu alan eksik |
| `type` | `'string'`, `'email'`, `'bool'` | tip uyuşmazlığı |
| `min` | `['min' => 8]` | uzunluk/boyut |
| `regex` | `'/^[a-zA-Z0-9_]{3,30}$/'` | biçim |
| `in` | `['in' => ['male','female','neutral']]` | beyaz liste |

**Önemli sıralama:** içeriği `RequestValidationMiddleware` (kapı #7) doğrular;
`Controller` **artık** kendi içinde `required` kontrolü **yazmaz** (tek doğrulama
noktası). Controller yalnız **tip-güvenli DTO dönüşümü** yapar
(`LoginRequest::fromArray()` gibi `fromArray()` zaten var).

## §4 DTO Sözleşmesi (11 dosya — disk)

```
shared/src/Api/Dto/
├── Request/  LoginRequest · RegisterRequest · PlaylistCreateRequest
│             PlaylistAddTrackRequest · MusicSearchRequest · UserUpdateRequest
└── Response/ LoginResponse · MusicResponse · PlaylistResponse
              UserResponse · PaginationResponse
```

DTO kalıbı (`PlaylistCreateRequest` ile birebir doğrulanmış):

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Api\Dto\Request;

final class PlaylistCreateRequest
{
    public function __construct(
        private readonly string $name,
        private readonly string $description = '',
        private readonly bool $isPublic = false
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            isPublic: $data['isPublic'] ?? false
        );
    }

    public function toArray(): array { /* ... */ }
}
```

Kurallar: `readonly` property · `final class` · `declare(strict_types=1)` ·
`fromArray()`/`toArray()` çifti · **iş mantığı yok** (yalnız taşıma + tip).

## §5 Yanıt Sarmalayıcısı ve Error Code'lar

### §5.1 Kodda Üretilen (IMPLEMENTED — `shared/src/Api/ApiResponse.php`)

```json
{ "data": { ... }, "meta": { "timestamp": "...", "version": "1.0.0" } }
{ "error": { "code": "...", "message": "...", "details": { ... } } }
{ "data": [...], "meta": { "pagination": { "page", "pageSize", "totalItems", "totalPages" }, "timestamp", "version" } }
```

Başlıklar: `Content-Type: application/json` · `X-Request-ID` (UuidV7) · `X-API-Version`.

| Kod | HTTP | Kaynak (dosya:satır) |
|-----|------|----------------------|
| `UNSUPPORTED_MEDIA_TYPE` | 415 | `RequestValidationMiddleware:64` |
| `INVALID_JSON` | 400 | `RequestValidationMiddleware:74` |
| `VALIDATION_ERROR` (+ `error.fields`) | **422** | `RequestValidationMiddleware:92-93` |
| `UNAUTHORIZED` | 401 | `AuthenticationMiddleware:103` · `AuthorizationMiddleware:45` |
| `FORBIDDEN` | 403 | `AuthorizationMiddleware:65,78` |
| `NOT_FOUND` | 404 | `Gateway:79` |
| `METHOD_NOT_ALLOWED` (gövde kodu `⚠️ VERIFICATION REQUIRED`) | 405 + `Allow` | `Gateway:74,125` |
| `RATE_LIMIT_EXCEEDED` | 429 | `RateLimitMiddleware:41-45` |
| `INTERNAL_ERROR` | 500 | `Gateway:100` |
| `BAD_REQUEST` | 400 | yalnız testte `ApiResponseTest:45` |

### §5.2 Şablonda Tanımlanan (api-doc-template.md §4.1)

7 kod: `VALIDATION_ERROR`(400) · `UNAUTHORIZED`(401) · `FORBIDDEN`(403) ·
`NOT_FOUND`(404) · `CONFLICT`(409) · `RATE_LIMITED`(429) · `SERVER_ERROR`(500);
sarmalayıcı `{ "status": "success"|"error", ... }`.

### §5.3 ⚠️ Contract Drift — 4 Açık Fark (KARAR GEREKLİ)

| # | Şablon (api-doc-template) | Kod (ApiResponse + Middleware) | Etki |
|---|---------------------------|-------------------------------|------|
| 1 | Her yanıtta `status: success\|error` **zorunlu** (L33, L264) | **`status` alanı hiç yok** — yalnız `data`/`meta`/`error` | İstemci `status` beklerse her yanıt hatalı okunur |
| 2 | Validasyon = **400** `VALIDATION_ERROR` (L305) | **422** `VALIDATION_ERROR` + `error.fields` (ADR-020 §2.2) | HTTP kodu uyuşmazlığı |
| 3 | `RATE_LIMITED` (L310) | `RATE_LIMIT_EXCEEDED` | Kod adı uyuşmazlığı |
| 4 | `SERVER_ERROR` (L311) | `INTERNAL_ERROR` (Gateway:100) | Kod adı uyuşmazlığı |
| 5 | `meta: {total, page, per_page}` | `meta.pagination: {page, pageSize, totalItems, totalPages}` | Sayfalama alanı adları |

**Bu skill bunları "düzeltmez"** — düzeltme kararı Backend Architect +
Security/Docs onayı ve **ADR/şablon revizyonu** ister (Guardrail #16).
Yeni endpoint yazarken: **kodu takip et, dokümanı güncelle** ve drift'i
raporla. `CONFLICT`(409) ve `UNAUTHORIZED`/`FORBIDDEN` kodları kodda henüz
kullanılmıyor (409 için kullanım **⚠️ VERIFICATION REQUIRED**).

## §6 Sayfalama Sözleşmesi

```php
// ✅ ApiResponse::paginated() — kodda var (ApiResponse.php:73-99)
return ApiResponse::paginated($items, $page, $pageSize, $totalItems);
```

- `page` ≥ 1 · `pageSize` uygulama tarafında **clamp** (ör. ≤100)
- `totalPages = ceil(totalItems / pageSize)` — **kod üretir, istemci hesaplamaz**
- Toplam sayım ayrı sorgudur; sorgu bütçesi: **count + sayfa = 2** (N+1 yasak)

## §7 Versiyonlama & Gateway

| Bileşen | Dosya | Görev |
|---------|-------|-------|
| `VersionResolver` / `VersionRegistry` / `ApiVersion` | `shared/src/Api/Versioning/` | URL'den sürüm çözümleme (`/api/v1/...`) |
| `Gateway` | `shared/src/Api/Gateway.php` | `dispatch(array $request): array` — route eşleşmesi → pipeline → handler |
| `RouteTable` | `shared/src/Api/Routing/` | method-aware kayıt defteri (404/405 ayrımı) |
| `ServiceRegistry` | `shared/src/Api/Registry/` | service sağlık/çözümleme |
| Base URL | `https://api.coremusic.net/v1` (api-doc-template §1) | — |

`shared/src/Api/Bff/` **DECOMMISSIONED 2026-10-18** (`shared/AGENTS.md` §2 —
ADR-084; 0 dış referans kanıtıyla silindi). `.ai/CLAUDE.md` §6A.2'deki BFF
tablosu **hedef mimaridir**, diskte Bff kodu **yoktur** → yeni uç için BFF
katmanı **kurulmaz, varsayılmaz**.

## §8 Event Dökümü (ADR-086)

`shared/src/Events/` — `EventDispatcher` implements
`Psr\EventDispatcher\EventDispatcherInterface`; 9 domain event
(`UserRegistered`, `UserLoggedIn`, `UserLoggedOut`, `PasswordResetRequested`,
`PlaylistCreated`, `MusicAdded`, `MusicPlayed`, `MediaAccessed`, `GenderSet`)
+ 3 integration (`SessionCreated`, `AuthValidated`, `Notification`).

Kural: servisler birbirini doğrudan çağırmaz, event yayınlar —
ama **`Events/` dışından production dispatch kanıtı 0 → wiring PLANNED.**
Yeni uç için: olay yaymak istiyorsan event sınıfını `Events/Domain/` +
`DomainEventInterface` + test ekle (`shared/AGENTS.md` Zorunlu #3);
**ama tetikleyici wiring'ini mevcut kod varmış gibi yazma.**

## §9 CQRS

`.ai/CLAUDE.md` §6A.3 hedefi: `Write: Command → Use Case → Repository → MySQL Master`,
`Read: Query → Read Model → Cache → Response`. **Disk kanıtı: 0 grep isabeti
(ADR-084)** → CQRS **PLANNED**. Yeni uçta okuma/yazma ayrımı **tasarım olarak
korunur** (write repo / read repo ayrımı), ama `Command`/`Query` sınıf
hiyerarşisi varmış gibi yazılmaz.

## §10 Yeni Endpoint Sözleşme Checklist'i

- [ ] `routes.php`'e method-aware kayıt eklendi mi (`service/handler/action/public/implemented/validation`)?
- [ ] `implemented: false` ile geçici kayıt mı (405 davranışı) — yoksa gerçek handler mı?
- [ ] `requiredRole` / `requiredPermission` atandı mı (RBAC)?
- [ ] `validation` kuralları yazıldı mı (422 + `error.fields` otomatik)?
- [ ] DTO `fromArray()` + `readonly` + `final` var mı?
- [ ] Controller `ActionResult`/`ApiResponse` ile mi dönüyor (ham `echo` YASAK)?
- [ ] Yanıt `ApiResponse::ok/created/paginated/error` ile üretildi mi (X-Request-ID otomatik)?
- [ ] Sayfalama `meta.pagination` ile mi (clamp uygulandı)?
- [ ] Drift (§5.3) varsa raporlandı mı — sessiz "düzeltme" YASAK?
- [ ] `docs/api/` dokümanı `api-doc-template.md` ile üretildi mi (Guardrail #16)?
- [ ] Test: `shared/tests/Api/` + `api.coremusic.net/tests/` yeşil mi (`composer test`)?
