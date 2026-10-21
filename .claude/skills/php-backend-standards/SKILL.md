---
name: php-backend-standards
description: "Use when writing or reviewing PHP 8.4 backend code, middleware, endpoints, or PDO queries in CoreMusic — Tetikleyiciler: 'PHP kodla', 'middleware ekle', 'endpoint yaz', 'PDO sorgusu'."
license: "Proprietary — CoreMusic Internal Use Only"
metadata:
  version: 4.0.0
  previous-version: 3.0.0
  format: claude-skill-v3
  author: "Bayram Ali (ULTRATHINK Engineering)"
  category: backend-standards
  tags: [php, php84, backend, api, pdo, psr, clean-architecture, owasp, coremusic]
  updated: 2026-10-21
---

# PHP Backend Standards — CoreMusic (PHP 8.4 · PDO · PSR)

> CoreMusic backend'inde PHP kodu yazarken veya review ederken uygulanan bağlayıcı
> standartlar. Bu skill bir **kılavuz** değil, bir **kapıdır**: §12 Checklist'ten
> geçmeyen kod production'a girmez.

**Kapsam:** `api.coremusic.net/`, `shared/src/` (backend yüzeyleri), `admin.coremusic.net`'in PHP
backend tarafları. **Kapsam dışı:** frontend JS/CSS, DSP/C++ (Neva Engine), donanım, MySQL şema
değişiklikleri (→ Guardrail #16: `data-engineer`), güvenlik politikası değişiklikleri (→ `security-engineer`).

**Destek dosyaları (bu SKILL.md'nin parçasıdır, hepsi §11'de linklenir):**

| Dosya | İçerik |
|---|---|
| `references/sources.md` | Her iddianın kaynağı (web URL + disk kanıtı, erişim tarihi 2026-10-21) |
| `references/changelog.md` | 3.0.0 → 4.0.0 değişiklik kaydı |
| `references/pdo-rules.md` | PDO derinlemesine: prepare, transaction, dynamic SQL, CI gate'leri |
| `references/middleware-pipeline.md` | Donmuş pipeline kapıları + API pipeline gerçeği (disk kanıtlı) |
| `references/api-contract-flow.md` | Contract-first akış, route contract, DTO envanteri, Contract Drift raporu |
| `references/checklist.md` | Kalite kapısı: N1-N10 + standart/middleware/PDO/hata kapıları (PASS/BLOCK) — teslim öncesi son kapı |
| `examples/add-endpoint.md` | Ham HTTP isteği → production kod → hata matrisi (tam döngü) |

---

## §1 Sert Çekirdek Kurallar (ihlali = BLOCK)

Bunlar tartışma kabul etmez; ihlal edilen kural PR'ın merge edilmesini engeller.

| # | Kural | Kanıt / Kaynak |
|---|---|---|
| 1 | `declare(strict_types=1)` **her PHP dosyasının ilk satırı** | `.ai/.templates/backend/php-template.md` Hard Guardrail |
| 2 | **PDO + prepared statement ZORUNLU. ORM YASAK** | ADR-002 (diskte kabul edilmiş) |
| 3 | `SELECT *` yasak — sorguda açık sütun listesi | `.ai/CLAUDE.md` §6 LINT; php-template.md Guardrail #7 |
| 4 | **`unserialize()` user input'tan YASAK** — JSON kullan | `AGENTS.md` §5 Dil standartları; php-template.md Guardrail #5 |
| 5 | CSRF token key'i **yalnız** `csrf_token` — `_csrf_token` yasak | ADR-010 |
| 6 | Salt loglama yasak — her hata sessizce yutulmaz; ya exception ya response | ADR-002 Operasyonel Kural 6 |
| 7 | Kolon adı **asla** string interpolation ile SQL'e girmez; dynamic column = allowlist | ADR-002 Kural 3 |
| 8 | Yeni endpoint **önce** `config/routes.php` contract'ına yazılır — Controller'dan önce | ADR-084 §4; `.ai/CLAUDE.md` §6A |
| 9 | Session, Bearer-authenticated API isteklerinde **asla** başlatılmaz | ADR-010 §1 |

**Hızlı karar tablosu — "şunu nasıl yaparım?":**

| İhtiyaç | Doğru yol | Yanlış yol |
|---|---|---|
| DB erişimi | PDO prepared, explicit columns, transaction | ORM / `SELECT *` / string concat |
| Input doğrulama | Route contract `validation` + `RequestValidation` + DTO `fromArray()` | Controller'da `if (empty(...))` |
| Auth | Session cookie (page) / Bearer JWT (API) — ADR-010 | Kendi token sistemi |
| Yeni endpoint | `routes.php` → Controller → Service → Repository | Controller'a doğrudan SQL |
| Tarih/saat | `date('Y-m-d H:i:s')` tek format; timezone config'de | Farklı formatlar her dosyada |
| ID | `string` (UUIDv7) — DTO'larda `int` değil | `int` auto-increment geçirme |
| Hata | `ApiResponse::error(code, message, status)` + log | `echo` / `die` / `var_dump` |
| Event | `shared/src/Events/` + PSR-14 dispatcher (ADR-086) | Controller'dan başka servise direkt çağrı |
| Config okuma | Config servisi (env > dosya) | `getenv()` her dosyada |
| Cache | `Psr\Cache\CacheItemPoolInterface` | Elle APCu/`$_SESSION` cache |

---

## §2 PHP 8.4 Standartları (dil + toolchain)

**Sürüm gerçeği (php.net, 2026-10-21 erişim):** PHP 8.4 aktif destekte (security fixes
2026-12-31'a kadar, bug fixes 2027-12-31'e kadar); 8.3 security 2025-12-31'de bitti.
CoreMusic 3 composer.json da `"php": ">=8.4"` istiyor — **runtime upgrade'i 8.5'e bu skill'ın
kapsamı değildir**; PHP 8.5 desteği ayrı bir karardır (`⚠️ VERIFICATION REQUIRED`).

### 2.1 Dosya iskeleti ve yazım kuralları

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\DTO\Auth\LoginRequest;
use CoreMusic\Api\ApiResponse;

final class AuthController
{
    public function __construct(private readonly AuthenticationService $auth) {}
}
```

| Kural | Detay |
|---|---|
| Visibility | Her method/property için explicit `public/private/protected` — varsayılan yok |
| Constructor | **Yalnız** constructor injection; `new` ile servis enjeksiyonu yasak (test edilemezlik) |
| Property | Mümkün olduğunca `readonly`; DTO'lar `final class ... implements Arrayable` + `readonly` |
| Tip | Return type ve parameter type **her method'da** zorunlu; `mixed` yalnız dönüşümsüz yerlerde |
| Null | Nullable açık yazılır: `?string`; `null` default'u sessizce verme |
| Enum | Durum alanı `enum` (int-backed DB) — magic string/int yasak |
| Error | `try/catch` yalnız yakalanabilir durumlar için; her yeri `try` ile boğma |
| Naming | Class `PascalCase`; method/property `camelCase`; constant `UPPER_SNAKE`; dosya = class adı |
| Spacing | PSR-12/PER-CS 2.0: 4 space, `{` satır sonunda, `use` blokları sıralı, tek dosyada tek class |

### 2.2 PSR haritası (çekirdek / çekirdek dışı — hangi iddia sınırlı)

| PSR | Ne bağlar | Çekirdek | Çekirdek dışı |
|---|---|---|---|
| PSR-12 (PER-CS 2.0) | Kod stili | `declare` satırı; import sıralaması; visibility | Method chaining indent → PER-CS 2.0 |
| PSR-7 | HTTP message | `ServerRequestInterface`, `ResponseInterface` | Header/cookie helper'ları |
| PSR-15 | Server middleware | ⚠️ Çekirdek: `RequestHandlerInterface`, `MiddlewareInterface` | ⚠️ **CoreMusic middleware'leri bunu uygulamıyor** → §5 ve `references/middleware-pipeline.md` §2 |
| PSR-14 | Event dispatch | `EventDispatcherInterface` — ADR-086 | Listener öncelik sırası → production'da uygulanmadı |
| PSR-3 | Log | ⚠️ `psr/log ^3.0` composer'da var ama kod loggervı PSR-3 interface'inden **türetmiyor** → §8 | — |
| PSR-11 | Container | PHP-DI `ContainerInterface` | — |
| PSR-6 | Cache | `CacheItemPoolInterface` | APCu rate-limit bu değil |

### 2.3 Toolchain (disk composer.json ×3 — `shared`, `admin.coremusic.net`, `api.coremusic.net`)

| Araç | Sürüm | Kural |
|---|---|---|
| PHP | `>=8.4` | Runtime |
| PHP-DI | `^7.0` | Tek DI konteyneri; `new` ile servis kurma |
| nyholm/psr7 + psr7-server | `^1.8` / `^1.1` | PSR-7 message üretimi; kendi Request/Response sınıfı yazma |
| nikic/fast-route | `^1.3` | Routing; route'lar `config/routes.php` contract'ından |
| PHPUnit | `^10.5` | Test; coverage ≥80% gate |
| PHPStan | `^1.10` | **level 5 = 0 error** gate |

Composer bağımlılık ekleme/çıkarma **bu skill kapsamında yasak** — `dependency-manager` +
onay süreci ayrıdır.

### 2.4 PHP 8.4 — dil ve çekirdek kütüphane notları (php.net/releases/8.4, 2026-10-21)

Yeni projede bilinmesi gereken, çekirdeğe yerleşen başlıklar (tam liste sources.md §2):

| Özellik | Kısa not | CoreMusic kararı |
|---|---|---|
| Property hooks | `get`/`set` hook'ları ile property erişimi özelleştirir | DTO'larda **kullanma** — DTO `readonly` ve sade kalır |
| Asymmetric visibility | `public private(set)` gibi tek yönlü görünürlük | DTO mutation'ı `fromArray()` ile; hook yerine |
| Yeni array fonksiyonları | `array_find`, `array_find_key`, `array_any`, `array_all` | Serbest — `array_filter(...)[0]` yerine tercih |
| `http_*` fonksiyonları | Son HTTP yanıt header'larını okuma yardımcıları | Servis katmanında gerekirse; controller'da değil |
| `new` ifadesi parantezsiz | `new Foo()->bar()` yazımı | Stil tercihi — mevcut kodda zorlamayız |
| Lazy objects | `ReflectionClass::newLazyGhost`/`newLazyInstance` ile tembel nesne | **Kullanma** — test harness dışında yasak (karar: erken özellik, üretimde kanıt yok) |
| bcmath genişlemesi | `bcdivmod` gibi yardımcılar | Sadece finansal hesap; string numeric karşılaştırma (`==`) yasak |

**Kural:** 8.4'ün "eski" yazımı (`list()`, `each()` yokluğu, `utf8_*` fonksiyonları) yerine
çekirdek fonksiyonlar; `mb_*`/`iconv_*` ile encoding işle, `str*` ile byte varsayma.

---

## §3 Mimari — 4 Katman ve Bağımlılık Yönü

```
Presentation  →  Route / Controller / Middleware / ViewModel   (iş mantığı YOK)
Application   →  Service / UseCase / Handler / DTO             (framework bağımsız)
Domain        →  Entity / ValueObject / IRepository            (dış bağımlılık YOK)
Infrastructure→  Concrete Repository / DatabaseManager / Adapter
```

**Tek yön kuralı:** Bağımlılık her zaman içe (Domain'e) bakar. Domain'den Controller'a,
Infrastructure'dan Application'a `use` **yasak** — PHPStan bunu tam yakalamaz (kural elde
denetlenir, review'da sorgulanır).

**Depthsiz katman kararı (CoreMusic gerçeği — `references/api-contract-flow.md` §5.2):**
`shared/src/UseCase/` diskte **boş** — bugün Controller → Service → Repository akışı var.
Contract Drift kaydı: **UseCase katmanı eklemek ADR-084'te PLANNED'dir, bugün uydurulmaz.**
Yeni kod `shared/src/Service/`'e yazar; `UseCase` adına sahip sınıf üretme.

### 3.1 Controller kuralları

```php
// ✅ DOĞRU — Controller yalnız orkestrer eder
public function login(ServerRequestInterface $request, array $routeParams): ResponseInterface
{
    try {
        $dto = LoginRequest::fromArray($this->json($request));   // DTO gate
        $result = $this->auth->login($dto);                      // → Service
        return ApiResponse::ok($result->toArray());
    } catch (AuthenticationException) {
        return ApiResponse::error('INVALID_CREDENTIALS', 'Kimlik doğrulama başarısız.', 401);
    }
}

// ❌ YANLIŞ — Controller'da iş mantığı + doğrudan SQL
$result = $this->db->query("SELECT * FROM users WHERE email = '{$email}'");
```

| Controller kuralı | Gerekçe |
|---|---|
| Controller'da SQL / PDO çağrısı yasak | Katman ihlali; test edilemez |
| Try/catch yalnız Controller sınırında (öneri) | Service exception'ı bilinçli fırlatır; her katman wrap'lemez |
| `match()` ile action dispatch (ADR-084 §7.2) | `if/elseif` zinciri yasak — `AuthController` diskte bunu kullanıyor |
| Response üretimi `ApiResponse` üzerinden | Elle JSON kurmak yasak |
| `$this->json($request)` + `json_decode(..., true, 512, JSON_THROW_ON_ERROR)` | Yarım JSON sessizce null olmaz |

### 3.2 Repository kuralları

- Constructor **yalnız** `PDO` veya `IDatabaseManager` — başka servis yok
- Her method'da açık sütun listesi; `SELECT *` yasak
- `JOIN` serbest (explicit column'larla), `N+1` yasak — ilgili veriyi tek sorguda çek
- Sorgu başına **bütçe: en fazla 2 DB round-trip** (liste: count + page)
- `LIMIT/OFFSET` **yalnız** repository'de; pagination bilgisi Controller'a `meta` ile döner
- Sorgu string'i satır sonuna bölünür, yorum satırı ile bağlam etiketlenir (`-- list users`)

### 3.3 DTO kuralları

- `final class ... implements Arrayable` + `readonly` property'ler
- Doğrulama route contract'ta + `RequestValidation` middleware'inde; DTO yalnız shape doğrular
- ID alanları **`string`** (UUIDv7) — `int` geçirme
- Tarih alanları tek format: `Y-m-d H:i:s`
- `fromArray()` / `toArray()` dışına `public` setter yok

---

## §4 PDO — Tek Veri Erişim Katmanı (ADR-002)

Özet burada; **tam kural seti: `references/pdo-rules.md`** (bağlayıcı).

```php
// ✅ PRODUCTION İMZASI — ADR-002 (diskte birebir)
$pdo = new PDO(
    sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $dbName),
    $user,
    $password,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,   // gerçek server-side prepare
    ]
);

// ✅ Prepared statement — user input HER ZAMAN bound parametre
$stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email AND deleted_at IS NULL');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();
```

### 4.1 Durum karar tablosu

| Durum | Karar |
|---|---|
| ORM istendi | **REDDET** → PDO + prepared (ADR-002 donmuş) |
| Dinamik `IN (...)` | Placeholders kadar `:p0..:pN` üret, asla interpolation |
| Dinamik ORDER BY | Allowlist kolon eşlemesi; string birleştirme yasak (ADR-002 Kural 3) |
| Yazma işlemi | `beginTransaction` + tüm execution + `commit`; hata → `rollBack()` |
| Bağlantı koptu (SQLSTATE 2002/2006/2013) | Tek retry (backoff); hata → 503 + log + monitoring (`⚠️ VERIFICATION REQUIRED`: 503 kullanımı production'da doğrulanmadı) |
| Üretimde log | **Sadece metadata** (tablo, satır sayısı, süre, SQLSTATE); **değerler ASLA** |

### 4.2 IDatabaseManager yüzeyi (disk kaynaklı özet)

`IDatabaseManager` transaction ve yazma sarmalayıcısıdır: `execute()` (parametreli SELECT),
`write()` (INSERT/UPDATE/DELETE), `beginTransaction()` / `commit()` / `rollBack()`.
Repository bu arayüzü tercih eder; ham `PDO` yalnız adapter içindedir.
`fetch()` **boolean** kabul eder (`true` = tek satır) — çağırıcı tarafında bu imzaya uy.

### 4.3 Transaction durum makinesi

```
IDLE → beginTransaction() → ACTIVE
ACTIVE → commit()  → IDLE (değişiklik görünür)
ACTIVE → rollBack() → IDLE (değişiklik geri alındı)
ACTIVE → exception → rollBack() zorunlu → exception Controller'a taşınır
```

- Transaction **en küçük** kapsamda tutulur (bir UseCase/Service işi); transaction içinde
  HTTP I/O, dosya yazma, event dispatch **yasak** (deadlock + uzun lock)
- Nested transaction yok; ikinci `beginTransaction()` → `LogicException`

### 4.4 Bağlantı yaşam döngüsü ve config önceliği

```
Boot: Config servisi (env > config dosyası) → DatabaseManager → PDO (kalıcı bağlantı)
İstek: Repository method'u → execute/write (connection pool'dan aynı PDO)
Hata: SQLSTATE 2002/2006/2013 → tek retry (backoff) → hala yok → 503 + ERROR log
Kapanış: connection request scope'ta kapatılır; global static PDO yasak
```

| Kaynak | Öncelik | Örnek |
|---|---|---|
| Environment variable | **1 (en yüksek)** | `DB_DSN`, `DB_USER`, `DB_PASS`, `JWT_SECRET` |
| Config dosyası | 2 | `config/database.php` |
| Kod içi default | 3 | Yalnız non-secret, test edilebilir default'lar |

- Secret'lar (`DB_PASS`, `JWT_SECRET`) **asla** koda/`.md`/`.log`'a gömülmez — env'den okunur
- Config dosyası commit'lenir; secret içeren config commit'lenmez (`.gitignore` + env)
- Test ortamı ayrı DSN/schema kullanır; test production DB'ye **temas etmez**
- `PDO` nesnesi **stateless paylaşılır**; istek başına yeni bağlantı yasak (pool kuralı)

**OWASP eşlemesi:** A05:2025 Injection — SQLi CWE-89. Kod katmanındaki tam kapılar:
`references/pdo-rules.md` §4 ve §7 (CI statik gate'leri: `ATTR_EMULATE_PREPARES => true`,
string concat sorgu, `SELECT *` taraması).

---

## §5 Middleware — Donmuş Pipeline

**Sayfa/SPA pipeline'ı (10 kapı, donmuş):**

```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager
→ Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

- **Sıra değiştirilemez**; `MiddlewarePipelineTest` sırayı lock'lar
- CSP nonce akışı: `SecurityHeaders` üretir → `SessionManager` sessie yazar
- Yeni kapı eklemek ≠ kapı değiştirmek: yeni kapı **dibe** eklenir; mevcut kapı
  değiştirmek **önce** `.ai/CLAUDE.md` §6 gate'i → vault update → Commit

**API pipeline'ı (8 kapı — `api.coremusic.net/index.php` disk kanıtı):**

```
ResponseNormalization → OriginCheck → Cors → RateLimit → Authentication
→ Csrf (yalnız session scope) → RequestValidation → Authorization → Gateway
```

**Önemli gerçek (v4.0 düzeltmesi):** CoreMusic middleware'leri PSR-15 `MiddlewareInterface`'i
**uygulamıyor** — `handle(array $context, callable $next): array` / `__invoke` imzası var,
`psr/http-server-middleware` composer'da yok. Yeni middleware **mevcut array-tabanlı imzayla**
yazılır, PSR-15 uydurulmaz. Tam kapı-tablosu, hata matrisi ve 5 sık hata:
**`references/middleware-pipeline.md`**.

### 5.1 Kapı-gate karar tablosu (özet)

| İstek | Hangi kapılar neden geçer/elenir |
|---|---|
| OPTIONS preflight | RateLimit eler (CORS preflight'ta gerekmez) → 429 olabilir |
| GET /public/* | RateLimit geçer; auth yok |
| GET /api/* Bearer | RateLimit + Authentication geçer; **Csrf bypass** (session yok) |
| POST /auth/* | RateLimit → 429 (`RATE_LIMITED`, AuthController diskinde) |
| Yanlış Origin | OriginCheck → 403 |

### 5.2 Kapı davranış özeti (API, status kodlarıyla)

| # | Kapı | Başarısızlık | Not |
|---|---|---|---|
| 1 | ResponseNormalization | — | Yanıt envelope'unu sonleştirir |
| 2 | OriginCheck | 403 | Cross-origin doğrulama |
| 3 | Cors | 405/200 | Preflight |
| 4 | RateLimit | **429** `RATE_LIMITED` | APCu 60/60s |
| 5 | Authentication | **401** `UNAUTHORIZED` | JWT verify; `alg:none` reddi |
| 6 | Csrf | **403** | Yalnız session scope; Bearer'da skip |
| 7 | RequestValidation | **422** `VALIDATION_ERROR` + `error.fields` | Route contract kuralları |
| 8 | Authorization | **403** `FORBIDDEN` | `requiredRole`/`requiredPermission` |
| 9 | Gateway | **500** `INTERNAL_ERROR` | Son catch; `X-Request-ID` |

---

## §6 Contract-First Endpoint Akışı (ADR-084)

**Zincir (her yeni endpoint için zorunlu sıralı adım):**

1. **Route contract**: `api.coremusic.net/config/routes.php` → `POST /api/v1/...` satırı
   (`service`, `public`, `validation`, `cacheable` alanları)
2. **DTO**: `shared/src/Api/DTO/...` → `fromArray()` shape doğrulaması
3. **Controller**: `match($action)` → Service
4. **Service**: iş kuralı + transaction
5. **Repository**: prepared PDO + explicit columns
6. **Response**: `ApiResponse::ok(...)` envelope
7. **Test**: controller + repository, coverage gate

Route contract alanları, 404/405(+`Allow`)/422 semantiği, DTO envanteri, pagination envelope'u
ve **5 maddelik Contract Drift raporu** (template ↔ kod ↔ AuthController tutarsızlıkları):
**`references/api-contract-flow.md`**. **Tam uçtan uca örnek: `examples/add-endpoint.md`**
(ham HTTP isteğinden 8 kapıdan geçip PDO sorgusuna inen tam döngü).

**Kabul edilmiş drift (raporlandı, sessizce düzeltilmedi):**
Şablon `{status:success}` ↔ kod `{data,meta}` ↔ AuthController `{success:true,data}`;
doğrulama 400 ↔ 422; `RATE_LIMITED` ↔ `RATE_LIMIT_EXCEEDED`; `SERVER_ERROR` ↔ `INTERNAL_ERROR`.
**Kod gerçeği `ApiResponse` + ADR-020'dir** — yeni kod şablonu değil kodu takip eder.

### 6.1 ApiResponse yardımcıları ve hata kataloğu (disk kanıtlı)

| Yardımcı | Kullanım |
|---|---|
| `ApiResponse::ok($data, $meta)` | 200 liste/tekil |
| `ApiResponse::created($data)` | 201 oluşturma |
| `ApiResponse::noContent()` | 204 silme |
| `ApiResponse::paginated($items, $page, $pageSize, $totalItems)` | 200 + `meta.pagination` |
| `ApiResponse::error($code, $message, $status, $details)` | 4xx/5xx |

| Hata kodu | Status | Üreten yer |
|---|---|---|
| `UNAUTHORIZED` | 401 | Authentication |
| `FORBIDDEN` | 403 | Authorization |
| `NOT_FOUND` | 404 | Route/Controller (kayıt yok) |
| `INVALID_JSON` | 400 | Body ayrıştırma |
| `VALIDATION_ERROR` (+`error.fields`) | 422 | RequestValidation |
| `UNSUPPORTED_MEDIA_TYPE` | 415 | Content-Type gate |
| `RATE_LIMITED` | 429 | RateLimit (+AuthController kendi throttle'ı) |
| `EMAIL_TAKEN` / `USERNAME_TAKEN` | 409 | AuthController (domain çakışma) |
| `INTERNAL_ERROR` | 500 | Gateway |

Her yanıt header'ları: `X-Request-ID` (UUIDv7), `X-API-Version: 1.0.0`.

### 6.2 HTTP status kararı

| Durum | Kod |
|---|---|
| Okuma başarılı | 200 |
| Oluşturma | 201 |
| Silme başarılı | 204 (gövde yok) |
| Route yok / kayıt yok | 404 |
| Yöntem yok | 405 + `Allow` header'ı |
| Kimlik yok | 401 |
| Yetki yok | 403 |
| Body JSON değil | 400 `INVALID_JSON` |
| Doğrulama hatası | 422 `VALIDATION_ERROR` |
| Yanlış Content-Type | 415 |
| Rate limit | 429 |
| Çakışma (unique) | 409 |
| Beklenmeyen | 500 `INTERNAL_ERROR` (log + `X-Request-ID`) |

### 6.3 Route contract örneği ve cacheable kararı

Diskteki contract biçimine uygun örnek satır (`api.coremusic.net/config/routes.php`):

```php
[
    'method'            => 'GET',
    'path'              => '/api/v1/tracks/{id}',
    'service'           => TrackController::class,
    'handler'           => 'show',
    'action'            => 'show',
    'public'            => false,
    'implemented'       => true,
    'validation'        => ['id' => 'uuid'],
    'requiredRole'      => 'user',
    'requiredPermission'=> null,
    'cacheable'         => true,
    'cacheTtl'          => 300,
],
```

| Alan | Kural |
|---|---|
| `service`/`handler` | Controller sınıfı + method adı; `match($action)` dispatch'i bunu kullanır |
| `public` | `true` yalnız `BypassAuth` ALLOWLIST route'ları (`/api/v1/auth/*`); gerekçe yorumda |
| `implemented` | `false` = contract var, kod yok → Gateway 404 (contract-first akışı korur) |
| `validation` | `RequestValidation` kapısının okuduğu kurallar; Controller'da tekrar doğrulama yazma |
| `requiredRole`/`requiredPermission` | Authorization kapısı; ikisi birden = AND |
| `cacheable`/`cacheTtl` | Yalnız GET + `public:false` güvenli; mutasyon asla cache'lenmez |

**Cache kararı tablosu:**

| Endpoint tipi | Cacheable? | Gerekçe |
|---|---|---|
| GET liste/tekil (okuma) | Evet (`cacheable:true`, TTL saniye) | Stateless okuma |
| GET auth'lu profil gibi kişisel | Dikkat — cache anahtarına kullanıcı id dahil et | A01 ihlali riski |
| POST/PUT/PATCH/DELETE | **Asla** | Stale write |
| 4xx/5xx yanıtlar | Asla | Hata cache'i zehirler |
| Rate-limited login | Asla | Brute-force kapısını kapatır |

---

## §7 Güvenlik — OWASP Top 10:2025 Karşılığı

2025 sürümünde A01–A10 (`owasp.org/Top10/2025/`, 2026-10-21): A01 Broken Access Control ·
A02 Cryptographic Failures · A03 Software Supply Chain Failures (eski SSRF yerine) ·
A04 Insecure Design · A05 Injection · A06 Identification & Authentication Failures (adı
değişti) · A07 Tampering (güncellendi) · A08 Software & Data Integrity Failures (güncellendi) ·
A09 Security Logging & Monitoring Failures (güncellendi) · A10 SSRF (yeni).
Üretim sürümleri listesindeki 10 kategori: A01, A02, A03, A04, A05, A06, A07, A08, A09, A10
(A09 doğrulama sorunları — A10 "Common Weakness Enumeration" başlığı altında verilmiş,
`references/sources.md` §1 kaydındaki notu oku).

| OWASP 2025 | PHP yüzeyi | Kural |
|---|---|---|
| A01 Access | `BypassAuth` yalnız ALLOWLIST (`/api/v1/auth/*`); auth'dan kaçış kodda **yok** | Yeni public route = contract'ta `public:true` + gerekçe |
| A02 Crypto | `password_hash()` (algo config'de) / `password_verify()`; MD5/SHA-1/DES yasak | Parola düz metin log'a/DB'ye girmez |
| A03 Supply Chain | `composer.lock` commit'lenir; sürüm bump = `dependency-manager` onayı | `require`'da `*`/dev-branch yasak |
| A04 Insecure Design | Rate limit (APCu, 60/60s), session idle 3600s | Tasarım gate'i: her mutasyon endpoint'ine auth + CSRF + validation |
| A05 Injection | PDO prepared (A05:2025 = 37 CWE, SQLi CWE-89 + XSS'in altında) | User input SQL'e **hiçbir yerde** string olarak girmez |
| A06 Auth Failures | `password_needs_rehash` session'da; Bearer geçince session invalidation | Login brute-force = RateLimit 429 |
| A07 Tampering | JWT verify: `HS256` + `JWT_SECRET` + **`alg:none` reddi** + exp | `base64_decode` ile kendi JWT kontrolü yasak |
| A08 Integrity | `composer.lock` + CI; event listener'lar `Events/` altındaki tek kaynak | `unserialize()` yasak |
| A09 Logging/Monitoring | Her 4xx/5xx log + `X-Request-ID` korelasyonu | Salt `error_log()` ile geçiştirme yasak |
| A10 SSRF | Harici URL fetch = proxy/allowlist (servis katmanında) | User-controlled URL doğrudan `file_get_contents` yasak |

**CSRF (ADR-010, 3 katman):** çift submit cookie + `Origin/Referer` doğrulaması + `SameSite=Lax`.
Token key **`csrf_token`**. Bearer-authenticated API istekleri CSRF'den muaftır ama **Origin
check'i geçer**. `GET`'te token döndürme (state değiştirmez).

**Session:** cookie `HttpOnly; Secure; SameSite=Lax` — ADR-002 §7.1
(`SameSite=Strict` ADR-010 §2.1'de gerekçeli istisna).

**Parola ve identity kuralları:**

| Kural | Detay |
|---|---|
| Hash | `password_hash()`; doğrulama `password_verify()` |
| Rehash | `password_needs_rehash()` session'da — algo/option değişince oturumda güncelle |
| Algoritma | Config'de (`passwordAlgo`); sabit kodda değil |
| Parola policy | ≥12 karakter, büyük/küçük/rakam/special; `password_policy` metni 422 ile döner |
| Token/log | Parola, JWT, cookie değeri log'a/response'a asla yazılmaz |

**Güvenlik politikası değişikliği** (header/CSRF/auth mantığı) = `security-engineer` onayı
(Guardrail #16) — bu skill güvenlik **kodunu** yazar, politikayı **değiştirmez**.

---

## §8 Hata, Log ve Observability

| Seviye | Ne loglanır | Ne loglanmaz |
|---|---|---|
| ERROR | SQLSTATE, kod, trace id, stack (production'da stack sadece log'a) | User input değerleri, parola, token |
| WARN | Rate limit aşımı, fallback, retry | Her 200 |
| INFO | İşlem başlangıç/bitiş (metadata), transaction commit | Tablo içerikleri |

- `echo` / `print_r` / `var_dump` / `dd()` production kodunda **yasak** (sadece debug gate altında)
- Her response `X-Request-ID` (UUIDv7) + `X-API-Version: 1.0.0` taşır (`ApiResponse`, diskte)
- Kullanıcıya dönen hata: kısa mesaj + kod; stack/SQL detayı **asla** response'a sızmaz
- Log context'i yapılandırılmış: `['request_id' => ..., 'user_id' => ..., 'action' => ...]`
- PSR-3: `psr/log ^3.0` composer'da ama kod `CoreMusic\Shared\Logging\Logger`'ı PSR-3
  `LoggerInterface`'inden **türetmiyor** — yeni kod PSR-3 interface'iyle uyumlu imza kullanır
  (`⚠️ VERIFICATION REQUIRED`: mevcut Logger'ın PSR-3'e taşınması ayrı bir iş)

**Hata akışı (üretim):**

```
Service exception fırlatır
  → Controller catch (bilinen domain hataları: 401/403/404/409/422)
  → Bilinmeyen exception → Gateway son catch → 500 INTERNAL_ERROR
  → Log: ERROR + request_id + SQLSTATE (değerler yok)
  → Response: kısa mesaj + X-Request-ID
```

---

## §9 Test ve CI Gate'leri

| Gate | Eşik | Kapsam |
|---|---|---|
| PHPUnit coverage | ≥80% | Controller + Repository + doğrulama mantığı |
| PHPStan | Level 5, **0 error** | Tüm `shared/src` + `api.coremusic.net` |
| CI statik tarama | 0 isabet | `SELECT *`, `unserialize(`, `ATTR_EMULATE_PREPARES => true`, string concat sorgu (`references/pdo-rules.md` §7) |
| Pipeline sırası | Lock | `MiddlewarePipelineTest` — sıra değişince kırmızı |

Test yazarken:
- `PDO`'yu **mock'la**; gerçek DB entegrasyon testinde (CI'da ayrı DSN, ephemeral schema)
- Her controller test'i: happy path + 401 + 403 + 422 + 409 matrisi
- Repository test'i: en az 1 JOIN senaryosu, 0 sonuç senaryosu, transaction rollback senaryosu

**Endpoint test matrisi (zorunlu):**

| Senaryo | Beklenen |
|---|---|
| Geçerli istek | 200/201/204 + doğru envelope |
| Token yok | 401 `UNAUTHORIZED` |
| Yetkisiz rol | 403 `FORBIDDEN` |
| Eksik/geçersiz alan | 422 `VALIDATION_ERROR` + `error.fields` |
| Unique çakışma | 409 |
| Geçersiz JSON | 400 `INVALID_JSON` |
| Yanlış Content-Type | 415 |
| Rate limit | 429 |

### 9.3 CI gate sırası (mutation riski azalan sıra)

| # | Aşama | Ne yakalar | Kırmızı ise |
|---|---|---|---|
| 1 | Statik tarama (`references/pdo-rules.md` §7) | Yasaklı kalıplar: `SELECT *`, `unserialize(`, `EMULATE_PREPARES => true`, concat SQL | Kodu düzelt; tool değil |
| 2 | PHPStan level 5 | Tip/null/imza hataları | 0 error olana kadar geçmez |
| 3 | PHPUnit (unit) | Mantık + DTO + Controller matrisi | Coverage ≥80% şartı ile |
| 4 | `MiddlewarePipelineTest` | Pipeline sırası değişimi | Sıra kasıtlı ise önce `.ai/CLAUDE.md` §6 gate |
| 5 | Coverage raporu | Yeni kodun testi eksik mi | Yeni satır = yeni test |

**Sıra mantığı:** en ucuz ve en kesin kalıp taraması önce; pahalı coverage en sonda.
Mutation riski azalan: statik → tip → mantık → bütünsel (pipeline) → ölçüm (coverage).

---

## §10 Anti-Pattern Sıçrama Panosu (en sık 10 hata)

| # | Hata | Sonuç | Düzeltme |
|---|---|---|---|
| 1 | `SELECT *` | Schema kırılganlığı, sızıntı | Açık sütun listesi |
| 2 | String concat SQL | SQLi (A05) | Prepared + bound |
| 3 | Controller'da `new Repository(...)` | Test edilemez | Constructor injection |
| 4 | `if/elseif` action dispatch | Bakım çürümesi | `match($action)` |
| 5 | Response elle JSON | Drift | `ApiResponse` |
| 6 | Doğrulama Controller'da | Her endpoint'te tekrar | Route contract `validation` + DTO |
| 7 | ID `int` | UUIDv7 geçişte kırılır | ID = `string` |
| 8 | `unserialize()` / magic getter | RCE riski + A08 ihlali | JSON + explicit method |
| 9 | Transaction içinde HTTP/dosya/event | Deadlock, uzun lock | Transaction en küçük kapsamda |
| 10 | `var_dump`/`dd()` production'da | Sızıntı | Log + `ApiResponse::error` |

Şablonun tam anti-pattern kataloğu: `.ai/.templates/backend/php-template.md` §3 (bağlayıcı).

### 10.1 Hızlı teşhis tablosu (semptom → kök neden → düzeltme)

| Semptom | Muhtemel kök neden | Düzeltme |
|---|---|---|
| `422 VALIDATION_ERROR` ama alan dolu | Route contract `validation` kuralı DTO ile çelişiyor | Contract'ı DTO shape'e hizala (kod gerçektir) |
| `403` Bearer istekte | Authorization `requiredRole`/`requiredPermission` eşleşmiyor | Rol/permission contract'ta; kapıyı değil rolü düzelt |
| `429` beklenmeyen istekte | Aynı IP/APCu anahtarında paylaşımlı throttle; OPTIONS preflight rate-limit'ten geçmiyor | `references/middleware-pipeline.md` §4 |
| `404` implement edilmiş route'ta | `implemented:false` veya FastRoute path `{id}` uyuşmazlığı | Contract `implemented:true`; path birebir |
| `405` + `Allow` | Method contract'ta yok | Route'a method ekle (contract-first!) |
| 500 ama log boş | Salt loglama yasak ihlali: exception yutulmuş | `catch` → exception ya da `ApiResponse::error` |
| Encoding bozuk (`?` karakterler) | `charset=utf8mb4` DSN'de yok / tablo latin1 | DSN `charset=utf8mb4` + tablo/kolon utf8mb4 |
| `Invalid JSON` ama body geçerli | `Content-Type: application/json` yok → 415/400 | Client header'ı; gate'i gevşetme |
| Testte DB hatası | Test production DSN'ine bağlanıyor | Ayrı test DSN/schema (§4.4) |

---

## §11 Kaynaklar ve Doğrulama

Bu skill'deki her dış iddia `references/sources.md`'de kayıtlıdır (web URL + erişim tarihi
2026-10-21 veya disk kanıtı yolu). Kayıtsız iddia = uydurma = BLOCK. Sürüm değişikliğinde
önce sources.md güncellenir.

**Değişiklik kaydı:** `references/changelog.md` (N10) — her sürümde eklenen/silinen/düzeltilen
kurallar gerekçesiyle.

**Bu skill'in güncellenme kuralı:** CoreMusic backend kararları değişince (ADR, `.ai/CLAUDE.md`
§6/§6A, composer sürümü) skill **o oturumda** güncellenir; skill kuralı ile ADR çakışırsa
**ADR kazanır** ve skill düzeltilir.

---

## §12 Uygulama Kapısı — Commit Öncesi Checklist

Kod yazdın. Şimdi bu 12 maddeyi tek tek işaretle; **hepsi geçmeden commit/PR yok**:

1. [ ] İlk satır `declare(strict_types=1)` — her dosyada
2. [ ] Hiçbir yerde `SELECT *`, string concat SQL, `unserialize(user`
3. [ ] Tüm sorgular prepared; dynamic ORDER BY/IN allowlist'li
4. [ ] Yazma işlemleri transaction'da; hata → rollback
5. [ ] Controller'da iş mantığı/SQL yok; injection constructor ile
6. [ ] Endpoint önce `routes.php` contract'ında; validation/validationRules tanımlı
7. [ ] DTO `final + readonly + fromArray()`; ID'ler `string`
8. [ ] Response `ApiResponse` üzerinden; kod adları mevcut enum'larla eşleşiyor
9. [ ] Middleware mevcut imzayla (`handle(array, callable): array`); pipeline sırası değişmedi
10. [ ] CSRF key `csrf_token`; Bearer istekte session başlatılmadı
11. [ ] PHPUnit coverage ≥80%; PHPStan level 5 = 0 error; CI statik tarama 0 isabet
12. [ ] Log'da kullanıcı değeri/parola/token yok; `X-Request-ID` response'da

Bir madde "ama bu seferlik olur" ise: **olmaz**. İstisna = ADR + `.ai/CLAUDE.md` güncellemesi.

---

## §13 Sürüm Notu (v3.0.0 → v4.0.0)

- PHP 8.4 gerçeklikleri (PER-CS 2.0, EOL takvimi), OWASP **2025** güncel haritası,
  middleware gerçeği (PSR-15 değil, array-tabanlı), UseCase katmanının bugün boş olduğu,
  şablon-kod Contract Drift'i artık **açıkça** raporlanıyor
- PSR-15/PSR-14 "çekirdek" iddiaları sınırlandırıldı; `⚠️ VERIFICATION REQUIRED` disiplini
- Tam kaynak envanteri (`references/sources.md`) ve changelog (`references/changelog.md`) eklendi

Detay: `references/changelog.md`.

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
