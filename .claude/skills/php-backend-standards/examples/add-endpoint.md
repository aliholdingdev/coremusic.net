---
title: "php-backend-standards — Örnek: Uçtan Uca Yeni Endpoint"
type: example
category: skills
version: 4.0.0
status: active
date: 2026-10-21
updated: 2026-10-21
---

# Örnek — Ham İstekten Production Yanıtına (Uçtan Uca)

> **Uyarı (Zero-Hallucination):** Aşağıdaki uç **kurgusaldır** —
> `GET /api/v1/playlist/{id}/tracks` bu adla **diskte tanımlı değildir**
> (routes.php'de yalnız `service` önek kayıtları var). Amaç, mevcut
> dosya/kalıpları (`routes.php`, `RouteTable`, `Validator`, DTO,
> `ApiResponse`, PDO repository) birebir kullanarak tam döngüyü göstermektir.
> Gerçek uç eklerken sahip olduğu dosyaları oku.

---

## 0. Girdi — Ham HTTP İsteği

```http
GET /api/v1/playlist/018f3f2a-6b1e-7c00-9a3b-1d2e3f4a5b6c/tracks?page=2&per_page=50 HTTP/1.1
Host: api.coremusic.net
Authorization: Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6ImsxIn0...
X-Requested-With: XMLHttpRequest
Accept: application/json
```

**Beklenen riskler (henüz hiçbir kapı geçilmedi):**
sahte Origin (`OriginCheck`) · brute-force (`RateLimit`) · kimlik yok/sahte (`Authentication`)
· yanlış role (`Authorization`) · bozuk query (`RequestValidation`) ·
SQL injection (`repository`) · N+1 (`track` başına sorgu) · sayfalama taşması.

---

## 1. Sözleşme — Önce `routes.php` (ADR-084 · §1 akışı)

```php
// api.coremusic.net/config/routes.php  →  'GET' bloğu
'/api/v1/playlist/{id}/tracks' => [
    'service'     => 'playlist',
    'handler'     => 'playlistController',
    'action'      => 'tracks',
    'public'      => false,          // auth zorunlu
    'implemented' => true,           // false → 405 + Allow
    'requiredRole'       => 'regular',   // RBAC (AuthorizationMiddleware)
    'requiredPermission' => null,
    'cacheable'   => true,           // ETag + Cache-Control
    'cacheTtl'    => 60,
    'validation'  => [
        'id'       => ['required' => true, 'type' => 'string'],
        'page'     => ['required' => false, 'type' => 'string'],
        'per_page' => ['required' => false, 'type' => 'string'],
    ],
],
```

> **Parametreli yol notu:** `RouteTable` kayıt formatı **exact path**
> (`/api/v1/auth/me`) üzerine kuruludur (`RouteTable::normalize`, `find()`).
> `{id}` placeholder'lı yolun eşleşmesi için ilgili matcher davranışı **okunmalı** —
> `⚠️ VERIFICATION REQUIRED` (bu skill placeholder eşleşmesini iddia etmez).

**Kapı 0 — route eşleşmezse:** `not_found` → **404 `NOT_FOUND`**;
yol var method yoksa → **405 + `Allow:`** (`implemented` filtresi
`RouteTable:119`). Bu, endpoint kodundan **önce** döner.

---

## 2. Kapılar — Sırayla Ne Olur (API pipeline, ADR-020)

| # | Kapı | Bu istekte | Başarı koşulu |
|---|------|-----------|----------------|
| 1 | ResponseNormalization | cacheable → ETag üretimi | geçer |
| 2 | OriginCheck | Origin `api.coremusic.net` ile eşleşmeli | yoksa **403** |
| 3 | Cors | yalnız header yazar | — |
| 4 | RateLimit | APCu 60/60s, key = istemci | aşıldıysa **429 + `Retry-After`** |
| 5 | Authentication | Bearer RS256 doğrula + jti revocation | başarısız → **401 `UNAUTHORIZED`** |
| 6 | Csrf | `method = 'jwt'` → **atlanır** (session değil) | — |
| 7 | RequestValidation | query kuralları (§1) | ihlal → **422 + `error.fields`** |
| 8 | Authorization | `roles` içinde `regular` var mı? | yoksa **403 `FORBIDDEN`** |

**CSRF neden atlandı:** `index.php:165` — `$_auth_user.method !== 'session'`
ise CsrfMiddleware çalışmaz (ADR-010: Bearer tarayıcı tarafından otomatik
gönderilmez → yüzey yok). Aynı uç **session cookie** ile gelseydi
`csrf_token` zorunlu olurdu.

---

## 3. DTO — Girdi Tip-Güvenli Hale Gelir

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Api\Dto\Request;

/**
 * Playlist tracks — sayfalama isteği (query string).
 */
final class PlaylistTracksRequest
{
    private const PER_PAGE_MAX = 100;

    private function __construct(
        public readonly string $playlistId,
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    /** @param array<string, mixed> $query */
    public static function fromArray(array $query): self
    {
        $page    = max(1, (int) ($query['page'] ?? 1));          // clamp
        $perPage = (int) ($query['per_page'] ?? 20);
        $perPage = min(self::PER_PAGE_MAX, max(1, $perPage));    // üst sınır

        return new self(
            playlistId: (string) ($query['id'] ?? ''),
            page: $page,
            perPage: $perPage,
        );
    }
}
```

Kurallar: `final` · `readonly` · `declare(strict_types=1)` · **iş mantığı yok** ·
clamp DTO'da (repository'ye asla ham `per_page` gitmez).

---

## 4. Controller — HTTP ↔ Servis Çevirisi (iş mantığı YOK)

`AuthController` kalıbı (`api.coremusic.net/include/Controller/AuthController.php:45`):

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Dto\Request\PlaylistTracksRequest;
use CoreMusic\Api\Dto\Response\PaginationResponse;
use CoreMusic\Contracts\Playlist\IPlaylistRepository;   // ⚠️ arayüz adı diskte doğrulanmalı
use CoreMusic\Exception\NotFoundException;

final class PlaylistController
{
    public function __construct(
        private readonly IPlaylistRepository $playlists,
    ) {}

    /**
     * @param array<string, mixed> $route   RouteTable kaydı (action)
     * @param array<string, mixed> $request pipeline isteği
     * @return array<string, mixed>          ApiResponse gövdesi
     */
    public function handle(array $route, array $request): array
    {
        $action = (string) ($route['action'] ?? '');

        try {
            return match ($action) {
                'tracks' => $this->tracks($request),
                default  => ['error' => ['code' => 'NOT_FOUND', 'message' => 'Uç bulunamadı.']],
            };
        } catch (NotFoundException $e) {
            return ['error' => ['code' => 'NOT_FOUND', 'message' => $e->getMessage()]];
        }
        // Genel istisna Gateway'de yakalanır → 500 INTERNAL_ERROR
        // (CTRL'de yakalayıp ham mesaj gösterme — CWE-209)
    }

    /** @return array<string, mixed> */
    private function tracks(array $request): array
    {
        $dto = PlaylistTracksRequest::fromArray($request['query'] ?? []);

        $result = $this->playlists->listTracks(
            playlistId: $dto->playlistId,
            limit: $dto->perPage,
            offset: ($dto->page - 1) * $dto->perPage,
        );

        return ApiResponse::paginated($result['items'], $dto->page, $dto->perPage, $result['total']);
    }
}
```

**Yapılmayanlar (hepsi yasak):** `echo`/`exit` · PDO çağrısı · SQL string ·
`isset($_GET)` · kendi `http_response_code()` set'in (`ApiResponse` yapar) ·
`SELECT *` · log'a ham SQL.

---

## 5. Repository — Prepared PDO + Açık Sütun + Sayfalama

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Repository;

use CoreMusic\Contracts\Database\IDatabaseRegistry;   // shared/AGENTS Zorunlu #1

/**
 * SELECT yalnız hazırlık statement ile; DatabaseManager dışında bağlantı YOK.
 */
final class PlaylistTrackRepository
{
    public function __construct(
        private readonly IDatabaseRegistry $databases,
    ) {}

    /**
     * @return array{items: list<array<string, mixed>>, total: int}
     */
    public function listTracks(string $playlistId, int $limit, int $offset): array
    {
        $db = $this->databases->get('music');   // 18 DB'lik registry (ADR-003)

        // --- Toplam (ayrı sorgu; sorgu bütçesi = 2) ---------------------
        $countStmt = $db->execute(
            'SELECT COUNT(*) AS c
               FROM playlist_tracks
              WHERE playlist_id = :playlist_id',
            ['playlist_id' => $playlistId]        // prepared — string değer
        );
        $total = (int) ($countStmt[0]['c'] ?? 0);

        // --- Sayfa (açık sütun listesi — SELECT * YASAK) ----------------
        $pageStmt = $db->execute(
            'SELECT pt.track_id, t.title, t.artist_id, t.duration_ms, pt.added_at
               FROM playlist_tracks pt
               JOIN tracks t ON t.id = pt.track_id
              WHERE pt.playlist_id = :playlist_id
              ORDER BY pt.added_at DESC
              LIMIT :limit OFFSET :offset',
            [
                'playlist_id' => $playlistId,
                'limit'       => $limit,          // int cast DTO'da yapıldı
                'offset'      => $offset,
            ]
        );

        return ['items' => $pageStmt, 'total' => $total];
    }
}
```

**Neden N+1 yok:** `JOIN` tek sorgudur — `foreach ($items as $t) { ... 'SELECT ... WHERE id = ?' }`
deseni **yazılmaz** (ADR-002 §5.1 #6 query budget).
**Neden `IN()` yok:** istek başına tek playlist → gerekmedi; olsa bile
`pdo-rules.md` §3.1 deseni kullanılırdı.

> `IDatabaseManager::execute(string $sql, array $params = []): array`
> imzası diskten doğrulanmıştır (`shared/src/Contracts/Database/IDatabaseManager.php:7`).
> `limit/offset`'in `:limit` bağlanabilmesi, PDO'nun tam-literali bağlama
> kuralına ve `ATTR_EMULATE_PREPARES=false` ayarına bağlıdır →
> `references/pdo-rules.md` §2/§3.

---

## 6. Yanıt — 200

```http
HTTP/1.1 200 OK
Content-Type: application/json
X-Request-ID: 018f4a2b-...          ← UuidV7 (ApiResponse)
X-API-Version: 1.0.0
ETag: "..."                          ← ResponseNormalization (cacheable)
Cache-Control: max-age=60
```

```json
{
  "data": [
    { "track_id": "…", "title": "…", "artist_id": "…", "duration_ms": 214000, "added_at": "2026-10-21T09:14:00+00:00" }
  ],
  "meta": {
    "pagination": { "page": 2, "pageSize": 50, "totalItems": 96, "totalPages": 2 },
    "timestamp": "2026-10-21T10:02:11+00:00",
    "version": "1.0.0"
  }
}
```

> **Drift uyarısı (raporlanır, sessizce "düzeltmez"):** `api-doc-template.md`
> `status: "success"` alanı ve `meta: {total, page, per_page}` bekler; kod
> `{data, meta.pagination}` üretir (`ApiResponse::paginated`). Ayrıntı:
> `references/api-contract-flow.md` §5.3.

---

## 7. Hata Yolları — Tam Matris

| Senaryo | Kapı | HTTP | Gövde |
|---------|------|------|-------|
| Bu yol hiç kayıtlı değil | RouteTable | **404** | `{"error":{"code":"NOT_FOUND","message":"Route not found"}}` |
| Yol var, method yanlış (GET→POST) | RouteTable | **405 + `Allow:`** | Handler not implemented yet |
| Origin reddi | #2 OriginCheck | **403** | origin hatası |
| 61. istek | #4 RateLimit | **429** + `Retry-After: 60` | `RATE_LIMIT_EXCEEDED` |
| Geçersiz/eksik token | #5 Auth | **401** | `UNAUTHORIZED` |
| `per_page=5000` (kural ihlali yoksa clamp edilir; `page=abc` → ihlal) | #7 Validation | **422** | `VALIDATION_ERROR` + `error.fields` |
| Rol `premium` gerektiriyor, kullanıcıda `regular` var | #8 Authorization | **403** | `FORBIDDEN` |
| Playlist DB'de yok | Controller/Repo | **404** | `NOT_FOUND` |
| Bozuk JSON gövde (POST versiyonunda) | #7 | **400** | `INVALID_JSON` |
| `Content-Type: text/plain` | #7 | **415** | `UNSUPPORTED_MEDIA_TYPE` |
| Sorgu exception | Gateway | **500** | `INTERNAL_ERROR` (ham SQL/log'a, istemciye değil) |
| DB bağlantı yok | DatabaseManager | **503** (ADR-002 §2.2b retry sonrası) | `⚠️ VERIFICATION REQUIRED` — repo kodunda 503 kullanımı doğrulanmalı |

---

## 8. Test Kapısı

| Test | Nereye | Ne doğrular |
|------|--------|-------------|
| `RouteConfigTest` (genişletilir) | `api.coremusic.net/tests/Unit/` | kayıt alanları + `implemented` 405 davranışı |
| `ApiResponseTest` | `shared/tests/Api/` | `paginated` meta `{page,pageSize,totalItems,totalPages}` |
| Controller testi | `api.coremusic.net/tests/Unit/` | action eşlemesi, 404/409/429 çevirileri |
| Middleware sıra testi | `shared/tests/Middleware/` | kapı sırası değişmedi |
| Repo sorgu testi | `shared/tests/Repository/` | prepared + açık sütun + count/page bütçesi |

QA hedefi: **coverage ≥ %80** (`AGENTS.md` §16) · PHPStan **level 5 = 0 hata**.

---

## 9. Kapı Önündeki Son Checklist

- [ ] 1. Sözleşme: `routes.php` kaydı + `implemented` + `validation` + RBAC
- [ ] 2. DTO: `final` + `readonly` + clamp
- [ ] 3. Controller: `match($action)` + yalnız çeviri
- [ ] 4. Repository: prepared + açık sütun + `IN()`/ORDER BY whitelist kuralları
- [ ] 5. Yanıt: `ApiResponse` (X-Request-ID otomatik) + `meta.pagination`
- [ ] 6. Hata: 7 kapı kodu doğru, ham mesaj yok (CWE-209)
- [ ] 7. Test yeşil + PHPStan 0
- [ ] 8. Doküman `docs/api/` + `api-doc-template.md` (Guardrail #16)
- [ ] 9. Drift varsa rapor (sessiz düzeltme YASAK)
- [ ] 10. **Commit subagent'ta değil — orkestratörde**
