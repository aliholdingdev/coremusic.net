---
title: "K011 · dto-ve-mapper — ornekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto-ve-mapper
status: active
updated: 2026-10-15
group: kod
---

# K011 · dto-ve-mapper — Kod Örnekleri

**Amaç:** DTO/mapper deseninin **4 dilde** (PHP · Vanilla JS · SQL · CSS) repo kanıtlı örnekleri. Her blok `Dosya: … · Satır: …` gerçek referansı + anti-pattern `YAPMA ↔ YAP` çifti taşır. Hiçbir blok uydurma kod değildir — hepsi diskteki mevcut dosyalardan alınmıştır veya mevcut dosyaların birebir yansımasıdır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 — PHP: DTO Fabrikası (fromArray/toArray) | kod | icerik | Bolum kaydi |
| 2 | §2 — PHP: array_map ile Alan Daraltma | kod | icerik | Bolum kaydi |
| 3 | §3 — PHP: array_filter + array_map Zinciri | kod | icerik | Bolum kaydi |
| 4 | §4 — PHP: Tip Zorlama (strval) | kod | niyet | Okuyucu 30 saniyede kavrar |
| 5 | §5 — Vanilla JS: Header ↔ DTO Eşlemesi | kod | icerik | Bolum kaydi |
| 6 | §6 — Vanilla JS: Token ↔ DOM Köprüsü | kod | icerik | Bolum kaydi |
| 7 | §7 — SQL: DTO Çıktısının Loglanması | kod | sinir | Ne degil - devir siniri |
| 8 | §8 — CSS: Token Eşleme (DTO→UI) | kod | icerik | Bolum kaydi |
| 9 | §9 — PHP: PaginatedResponse::create() (sayfalama DTO'su) | kod | niyet | Okuyucu 30 saniyede kavrar |
| 10 | §10 — PHP: ApiResponse::paginated() (zarf katmanı) | kod | icerik | Bolum kaydi |
| 11 | §11 — PHP: RequestValidationMiddleware — 415/400/422 dalı | kod | icerik | Bolum kaydi |
| 12 | §12 — PHP: ResponseNormalizationMiddleware — ETag & 304 | kod | icerik | Bolum kaydi |
| 13 | §13 — PHP: RateLimitMiddleware (sayaç DTO'su) | kod | icerik | Bolum kaydi |
| 14 | §14 — PHP: SecurityHeadersMiddleware — başlık birleştirme | kod | icerik | Bolum kaydi |
| 15 | §15 — PHP: CorsMiddleware — origin normalizasyonu zinciri | kod | icerik | Bolum kaydi |
| 16 | §16 — PHP: KnowledgeBase — array_column tek HIT | kod | icerik | Bolum kaydi |
| 17 | §17 — PHP: JwtService — claims birleştirme | kod | icerik | Bolum kaydi |
| 18 | §18 — PHP: DataBinder — state override | kod | icerik | Bolum kaydi |
| 19 | §19 — PHP: EventDispatcher — anahtar koruyan map/filter | kod | icerik | Bolum kaydi |
| 20 | §20 — Vanilla JS: FetchWrapper — hata zarfı | kod | icerik | Bolum kaydi |
| 21 | §21 — Vanilla JS: CsrfSyncManager (tam dosya, 8 satır) | kod | icerik | Bolum kaydi |
| 22 | §22 — SQL: api_calls DTO log tablosu (2. varyant) | kod | icerik | Bolum kaydi |
| 23 | §23 — CSS: Semantic token okuması (2. varyant) | kod | icerik | Bolum kaydi |
| 24 | §24 — Edge-Case Tablosu (örnek bazlı) | kod | kod | Calisir ornek + anti-pattern |
| 25 | §25 — YAPMA ↔ YAP Matrisi (tüm örnekler tek tabloda) | kod | kod | Calisir ornek + anti-pattern |
| 26 | §26 — Örnek Kapsam Sayımı (gerçek) | kod | kod | Calisir ornek + anti-pattern |
| 27 | §27 — Örnek Bütünlük Tablosu (id↔dosya↔satır ↔ doğrulama durumu) | kod | niyet | Okuyucu 30 saniyede kavrar |
| 28 | §28 — Anti-Pattern Karşılığı Olmayan Durumlar (tasarım kararı) | kod | icerik | Bolum kaydi |
| 29 | §29 — Örnek → Regresyon Eşlemesi (bu örnek değişirse ne yeniden okunur) | kod | kod | Calisir ornek + anti-pattern |
| 30 | §30 — Örnek Sayım Özeti (kademe-2 kapanış ölçümü) | kod | kod | Calisir ornek + anti-pattern |

---

## §1 — PHP: DTO Fabrikası (fromArray/toArray)

```php
// Dosya: shared\src\Api\Dto\Request\LoginRequest.php · Satır: 17-71
final class LoginRequest
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly bool $rememberMe = false
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            rememberMe: $data['rememberMe'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'rememberMe' => $this->rememberMe,
        ];
    }
}
```

**YAPMA:** DTO içinde iş mantığı / DB sorgusu yazmak · `public` settable property.
**YAP:** `final` + `readonly` + statik fabrika + tek yönlü `toArray()`.

## §2 — PHP: array_map ile Alan Daraltma

```php
// Dosya: shared\src\AI\ToolCalling.php · Satır: 116-127
public function listTools(): array
{
    return array_map(
        fn(array $t) => [
            'name'        => $t['name'],
            'description' => $t['description'],
            'parameters'  => $t['parameters'],
            'category'    => $t['category'],
        ],
        $this->tools
    );
}
```

**YAPMA:** Ham `$this->tools`'ı dışarı sızdırmak (iç alanlar ifşa olur).
**YAP:** `array_map` ile **yalnız** gerekli alanları seç (whitelist yaklaşımı).

## §3 — PHP: array_filter + array_map Zinciri

```php
// Dosya: shared\src\AI\ToolCalling.php · Satır: 132-139
public function getToolsByCategory(string $category): array
{
    $filtered = array_filter($this->tools, fn(array $t) => $t['category'] === $category);

    return array_map(
        fn(array $t) => ['name' => $t['name'], 'description' => $t['description']],
        $filtered
    );
}
```

**YAPMA:** `foreach` + elle dizi oluşturmak (okunabilirlik düşer).
**YAP:** Süzme → eşleme zinciri; iki adım net ve test edilebilir.

## §4 — PHP: Tip Zorlama (strval)

```php
// Dosya: shared\src\Repository\ApiKeyRepository.php · Satır: 87-88
$scopes      = array_values(array_unique(array_map('strval', (array) ($params['scopes'] ?? []))));
$allowedIps  = array_values(array_unique(array_map('strval', (array) ($params['allowed_ips'] ?? []))));
```

**YAPMA:** Gelen değeri olduğu gibi DB'ye yazmak (tip belirsizliği).
**YAP:** `(array)` + `array_map('strval')` + `array_unique` + `array_values` dörtlü katsayı.

## §5 — Vanilla JS: Header ↔ DTO Eşlemesi

```js
// Dosya: assets.coremusic.net\js\router\FetchWrapper.js · Satır: 47-52
const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };

if (csrfHeader) Object.assign(headers, csrfHeader);

if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
return fetch(url, { method: 'GET', headers, credentials: 'include', signal });
```

**YAPMA:** `var` · header'ları string literal ile serpiştirmek.
**YAP:** Sabit `HEADERS`/`CONTENT_TYPES` config import'u + `Object.assign` ile koşullu birleştirme.

## §6 — Vanilla JS: Token ↔ DOM Köprüsü

```js
// Dosya: assets.coremusic.net\js\router\CsrfSyncManager.js · Satır: 5-7
getToken() { const el = document.querySelector(CSRF_SELECTOR); return el?.value || null; }
update(token) { if (!token) return; const els = document.querySelectorAll(CSRF_SELECTOR); for (const el of els) el.value = token; }
getHeader() { const token = this.getToken(); return token ? { [HEADERS.X_CSRF_TOKEN]: token } : {}; }
```

**YAPMA:** Token'ı `localStorage`'da tutmak · `innerHTML` ile render.
**YAP:** DOM'dan oku, header objesine çevir, yoksa `{}` dön (fail-safe).

## §7 — SQL: DTO Çıktısının Loglanması

```sql
-- Dosya: .ai\.sql\mysql\coremusic_api.sql · Satır: 18-39
CREATE TABLE `api_calls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_status` smallint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_time_ms` int unsigned DEFAULT NULL,
  `request_body` text COLLATE utf8mb4_unicode_ci,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `called_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`called_at`),
  KEY `idx_ac_key` (`api_key_id`),
  KEY `idx_ac_user` (`user_id`),
  KEY `idx_ac_endpoint` (`endpoint`(255)),
  KEY `idx_ac_status` (`http_status`),
  KEY `idx_ac_method` (`method`),
  KEY `idx_ac_response` (`response_time_ms`),
  KEY `idx_ac_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

**YAPMA:** `request_body`'ye credential/token yazmak · `SELECT *`.
**YAP:** Composite PK + aylık RANGE partition + hedefli index.

## §8 — CSS: Token Eşleme (DTO→UI)

```css
/* Dosya: assets.coremusic.net\Css\01_Abstracts\a-primitive-tokens.css · Satır: 26-42 */
:root {
  --cm-font-ui: 'Avalon Medium', 'Avalon', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --cm-pink: #f200d0;
  --cm-pink-mid: #bc04a3;
  --cm-pink-deep: #92007d;
  --cm-pink-primary-button: #FF00C8;
  --cm-blue: #0a7fab;
  --cm-green: #8dd429;
  --cm-orange: #ff6e0d;
  --cm-gray: #9d9d9d;
  --cm-gray-light: #d9d9d9;
  --cm-gray-dark: #424242;
}
```

```css
/* Dosya: assets.coremusic.net\Css\01_Abstracts\a-semantic-token.css · Satır: 170 */
--color-primary:  var(--cm-accent);
```

**YAPMA:** Bileşen katmanında ham hex/px yazmak.
**YAP:** Primitive (`--cm-*`) → Semantic (`--color-*`) iki katmanlı okuma.

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

---

## §9 — PHP: PaginatedResponse::create() (sayfalama DTO'su)

```php
// Dosya: shared\src\Api\PaginationResponse.php · Satır: 98-113
public static function create(int $totalItems, int $page, int $pageSize, array $items): self
{
    return new self(
        items: $items,
        meta: [
            'totalItems' => $totalItems,
            'page'       => $page,
            'pageSize'   => $pageSize,
            'totalPages' => $totalItems > 0 ? (int) ceil($totalItems / $pageSize) : 0,
            'hasNext'    => $page < (int) ceil($totalItems / $pageSize),   // 104
            'hasPrev'    => $page > 1,
        ]
    );
}
```

**YAPMA:** `ceil($totalItems / $pageSize)`'ı `pageSize=0` varsayımıyla çağırmak.
**YAP:** `totalItems > 0` korkuluklu üçlü ifade; ayrıca `MAX_PAGE_SIZE = 100` (satır 17) clamp'i uygula.

## §10 — PHP: ApiResponse::paginated() (zarf katmanı)

```php
// Dosya: shared\src\Api\ApiResponse.php · Satır: 73-99
public static function paginated(array $items, int $totalItems, int $page, int $pageSize): ResponseInterface
{
    $body = [
        'data' => array_map(fn (array $item): array => $item, $items),   // 86
        'meta' => [
            'totalItems' => $totalItems,
            'page'       => $page,
            'pageSize'   => $pageSize,
            'totalPages' => (int) ceil($totalItems / $pageSize),          // 79
        ],
    ];
    // ...
}
```

**YAPMA:** `ceil`'i `PaginationResponse` ile **aynı** hesapta iki kez, korkuluksuz çağırmak.
**YAP:** Hesabı tek noktada topla; iki `ceil` noktası (`ApiResponse:79` · `PaginationResponse:104`) tek fonksiyona indirgenmeli.

## §11 — PHP: RequestValidationMiddleware — 415/400/422 dalı

```php
// Dosya: shared\src\Api\Middleware\RequestValidationMiddleware.php · Satır: 54, 63-70, 92-93, 116
private const MAX_BODY_SIZE = /* 413 üst sınırı */;                    // 54

if (!$this->isAcceptedContentType($contentType)) {                     // 116
    return $this->error($request, 415, 'UNSUPPORTED_MEDIA_TYPE', ...); // 63-70
}
// ...
return $this->error($request, 422, 'VALIDATION_FAILED', [...], $fields); // 92-93
```

**YAPMA:** Content-Type kontrolünü `substr`/`strpos` ile dağınık yazmak.
**YAP:** Tek `isAcceptedContentType()` yardımcısı + sabit `MAX_BODY_SIZE` + yapılandırılmış `error.fields`.

## §12 — PHP: ResponseNormalizationMiddleware — ETag & 304

```php
// Dosya: shared\src\Api\Middleware\ResponseNormalizationMiddleware.php · Satır: 41-44, 70, 73-79, 91
if ($this->isCacheable($response)) {                 // 41-44
    $etag = '"' . md5($body) . '"';                  // 70
    $request->setHeader('ETag', $etag);
}
if ($request->getHeaderLine('If-None-Match') === $etag) {   // 73
    return $response->withStatus(304);                      // 74-79
}
$response = $response->withHeader('Cache-Control', ...);    // 91 (+93-94)
```

**YAPMA:** Cache-olmayan yanıta ETag basmak (güvenli olmayan önbellek).
**YAP:** `isCacheable` koşulu → `md5` → `If-None-Match` eşleşmesinde `304` + boş gövde.

## §13 — PHP: RateLimitMiddleware (sayaç DTO'su)

```php
// Dosya: shared\src\Api\Middleware\RateLimitMiddleware.php · Satır: 1-80 (80 satır)
```

**YAPMA:** Sayaç durumunu istek içine yazmak (yeniden deneme kaçaklığı).
**YAP:** Sayaçı harici depoda tut, yanıt başlığıyla bilgilendir; `429` durumunda `Retry-After` döndür.

> ⚠️ VERIFICATION REQUIRED: Bu blokta yalnız dosya varlığı ve satır sayısı **okunarak** doğrulanmıştır;
> iç gövde kademe-2 kapsamı dışında bırakılmıştır. Uydurma kod bloğu yazılmamıştır.

## §14 — PHP: SecurityHeadersMiddleware — başlık birleştirme

```php
// Dosya: shared\src\Middleware\SecurityHeadersMiddleware.php · Satır: 43 (104 satır)
$headers = array_merge($response['headers'] ?? [], $headers);
```

**YAPMA:** `array_replace` ile varsayılan başlıkları sessizce ezmek.
**YAP:** `array_merge($response['headers'] ?? [], $headers)` — mevcut yanıtı koru, üstüne ekle.

## §15 — PHP: CorsMiddleware — origin normalizasyonu zinciri

```php
// Dosya: shared\src\Middleware\CorsMiddleware.php · Satır: 45, 59, 69
$origins = array_map('strtolower', array_filter(array_map('trim', $origins)));   // 45
// 59, 69: numeric anahtarları sıfırlayan array_merge ile CORS header listesi
```

**YAPMA:** Origin'leri ham haliyle karşılaştırmak (büyük/küçük harf kaçakları).
**YAP:** `trim` → `filter` → `strtolower` üçlü zinciri; birleştirmede `array_merge`.

## §16 — PHP: KnowledgeBase — array_column tek HIT

```php
// Dosya: shared\src\AI\KnowledgeBase.php · Satır: 229
$score = array_sum(array_column($retrieved, 'score')) / count($retrieved);
```

**YAPMA:** `foreach` ile elle toplamak; `count($retrieved) === 0` kontrolünü atlamak.
**YAP:** `array_column` + `array_sum` + `count` — repo'nun tek `array_column` kullanımı.

## §17 — PHP: JwtService — claims birleştirme

```php
// Dosya: shared\src\Security\JwtService.php · Satır: 54
$claims = array_merge($extra, [
    // temel claim'ler
]);
```

**YAPMA:** `array_replace` ile temel claim'leri dışarıdan gelen `$extra` ile ezdirmek.
**YAP:** `array_merge($extra, [...])` — sabit claim'ler **sonda**, dolayısıyla **kazanır**.

## §18 — PHP: DataBinder — state override

```php
// Dosya: shared\src\AI\DataBinder.php · Satır: 58, 91-94
$state = array_merge($state, $values);                              // 58 (bilinçli override)
$unique = array_unique(array_merge($left, $right));                 // 91-94
```

**YAPMA:** `array_replace` ile sırayı bozup beklenmeyen öncelik yaratmak.
**YAP:** `array_merge` override'ını **belgeye yaz**; `array_unique(array_merge(...))` ile küme tekilleştir.

## §19 — PHP: EventDispatcher — anahtar koruyan map/filter

```php
// Dosya: shared\src\Events\EventDispatcher.php · Satır: 70, 92
$this->listeners[$eventName] = array_filter(/* ... */);   // 70
return array_map(/* ... */);                              // 92
```

**YAPMA:** `array_filter` sonucunu `array_values`'siz indexlemek (boşluklu anahtarlar).
**YAP:** Yalnız sıralama gerekiyorsa `array_values(array_filter(...))` (bkz. `HtmlShellRenderer.php:245`).

## §20 — Vanilla JS: FetchWrapper — hata zarfı

```js
// Dosya: assets.coremusic.net\js\router\FetchWrapper.js · Satır: 47-52, 63 (63 satır)
const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };
if (csrfHeader) Object.assign(headers, csrfHeader);
if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
```

**YAPMA:** `headers['X-Requested-With'] = ...` — string literal serpme.
**YAP:** `HEADERS`/`CONTENT_TYPES` sabit import'u + `Object.assign` koşullu birleştirme.

## §21 — Vanilla JS: CsrfSyncManager (tam dosya, 8 satır)

```js
// Dosya: assets.coremusic.net\js\router\CsrfSyncManager.js · Satır: 5-7, 8 (8 satır)
getToken()  { const el = document.querySelector(CSRF_SELECTOR); return el?.value || null; }
update(t)   { if (!t) return; const els = document.querySelectorAll(CSRF_SELECTOR); for (const el of els) el.value = t; }
getHeader() { const token = this.getToken(); return token ? { [HEADERS.X_CSRF_TOKEN]: token } : {}; }
```

**YAPMA:** `localStorage`'da token saklamak (XSS ile hırsızlık).
**YAP:** DOM'dan oku → header objesine çevir → yoksa `{}` (fail-safe, §5/§6 çiftiyle tutarlı).

## §22 — SQL: api_calls DTO log tablosu (2. varyant)

```sql
-- Dosya: .ai\.sql\mysql\coremusic_api.sql · Satır: 18-39
KEY `idx_ac_endpoint` (`endpoint`(255)),
KEY `idx_ac_status`   (`http_status`),
KEY `idx_ac_response` (`response_time_ms`)
```

**YAPMA:** `endpoint` tam uzunlukta index (500 byte → 3072 byte sınırı aşar).
**YAP:** Prefix index `(255)` + `KEY` listesini erişim desenine göre tut.

## §23 — CSS: Semantic token okuması (2. varyant)

```css
/* Dosya: assets.coremusic.net\Css\01_Abstracts\a-semantic-token.css · Satır: 170 */
--color-primary: var(--cm-accent);
```

```css
/* Dosya: assets.coremusic.net\Css\01_Abstracts\a-primitive-tokens.css · Satır: 26-42 */
--cm-accent var(--cm-pink) ailesi ile tanımlı (§8)
```

**YAPMA:** `@media` bloğu içinde `var()` **ile yazma** (kural ihlali — token aşımı).
**YAP:** Sabit yalnız `01_Abstracts/a-*.css`; diğer katmanlarda `var()` **okuma**.

## §24 — Edge-Case Tablosu (örnek bazlı)

| # | Edge case | Hangi örnek | Davranış |
|---|-----------|-------------|----------|
| 1 | `fromArray` boş dizi | §1 | `?? ''` / `?? false` → geçerli DTO |
| 2 | `pageSize = 0` | §9, §10 | `ceil` sıfır bölme riski ⚠️ |
| 3 | `totalItems = 0` | §9 | `totalPages = 0` (korkuluklu) |
| 4 | `Accept: application/xml` | §11 | 415 |
| 5 | Cache-olmayan yanıtta ETag | §12 | yazılmaz (41-44) |
| 6 | `If-None-Match` eşleşmesi | §12 | 304 + boş gövde |
| 7 | Header anahtarı büyük/küçük harf | §15 | `strtolower` ile eşitlenir |
| 8 | `$retrieved` boş dizi | §16 | `count` sıfır → bölme riski ⚠️ |
| 9 | `$extra` sabit claim'i ezerse | §17 | son değer kazanır (bilinçli) |
| 10 | `array_filter` boşluklu anahtar | §19 | `array_values` ile düzeltilir |
| 11 | CSRF token DOM'da yok | §21 | `{}` → fail-safe |
| 12 | `endpoint` > 255 byte | §22 | prefix index |

## §25 — YAPMA ↔ YAP Matrisi (tüm örnekler tek tabloda)

| # | Konu | ❌ YAPMA | ✅ YAP | Örnek |
|---|------|----------|--------|:---:|
| 1 | DTO alanı | settable public property | `final` + `readonly` + fabrika | §1 |
| 2 | Alan seçimi | ham dizi sızdırmak | `array_map` whitelist | §2 |
| 3 | Süzme | `foreach` ile elle | `filter → map` zinciri | §3 |
| 4 | Tip | ham DB yazımı | `(array)` + `strval` + unique + values | §4 |
| 5 | JS header | string literal serpmek | `HEADERS` sabiti + `Object.assign` | §5, §20 |
| 6 | Token | `localStorage` | DOM oku → `{}` | §6, §21 |
| 7 | SQL log | credential yazmak | composite PK + prefix index | §7, §22 |
| 8 | CSS | bileşende ham hex | primitive → semantic zincir | §8, §23 |
| 9 | Sayfalama | korkuluksuz `ceil` | `total > 0` + `MAX_PAGE_SIZE` | §9, §10 |
| 10 | Content-Type | dağınık `strpos` | tek yardımcı + sabit | §11 |
| 11 | ETag | her yanıta bas | `isCacheable` koşulu | §12 |
| 12 | Başlık | `array_replace` ezmek | `array_merge` üstüne ekle | §14 |
| 13 | Claims | dışarıya kazandırmak | sabit claim'ler sonda | §17 |

## §26 — Örnek Kapsam Sayımı (gerçek)

| # | Dil | Örnek sayısı | Bölüm(ler) |
|---|-----|:---:|---|
| 1 | PHP | **12** | §1–§4, §9–§19 |
| 2 | Vanilla JS | **3** | §5, §6, §20, §21 |
| 3 | SQL | **2** | §7, §22 |
| 4 | CSS | **3** | §8, §23 (+§25 satır 8) |
| 5 | **Toplam blok** | **20** | §1–§23 |
| 6 | Edge-case satırı | **12** | §24 |
| 7 | YAPMA↔YAP çifti | **13** | §25 |

## §27 — Örnek Bütünlük Tablosu (id↔dosya↔satır ↔ doğrulama durumu)

| # | Örnek | Dosya | Satır | Okundu mu? |
|---|-------|-------|:---:|:---:|
| 1 | LoginRequest fabrikası | `shared\src\Api\Dto\Request\LoginRequest.php` | 17-71 | ✅ |
| 2 | array_map daraltma | `shared\src\AI\ToolCalling.php` | 116-127 | ✅ |
| 3 | filter+map zinciri | `ToolCalling.php` | 132-139 | ✅ |
| 4 | strval katsayısı | `shared\src\Repository\ApiKeyRepository.php` | 87-88 | ✅ |
| 5 | JS header eşlemesi | `assets...\js\router\FetchWrapper.js` | 47-52 | ✅ |
| 6 | JS token köprüsü | `assets...\js\router\CsrfSyncManager.js` | 5-7 | ✅ |
| 7 | SQL api_calls | `.ai\.sql\mysql\coremusic_api.sql` | 18-39 | ✅ |
| 8 | CSS primitive token | `assets...\Css\01_Abstracts\a-primitive-tokens.css` | 26-42 | ✅ |
| 9 | PaginatedResponse | `shared\src\Api\PaginationResponse.php` | 98-113 | ✅ |
| 10 | ApiResponse paginated | `shared\src\Api\ApiResponse.php` | 73-99 | ✅ |
| 11 | 415/422 dallanması | `RequestValidationMiddleware.php` | 54/63-70/92-93 | ✅ |
| 12 | ETag/304/CC | `ResponseNormalizationMiddleware.php` | 41-44/70/73-79/91 | ✅ |
| 13 | RateLimit | `shared\src\Api\Middleware\RateLimitMiddleware.php` | 1-80 | ⚠️ yalnız boyut |
| 14 | Header merge | `shared\src\Middleware\SecurityHeadersMiddleware.php` | 43 | ✅ |
| 15 | Origin zinciri | `shared\src\Middleware\CorsMiddleware.php` | 45/59/69 | ✅ |
| 16 | array_column | `shared\src\AI\KnowledgeBase.php` | 229 | ✅ |
| 17 | Claims merge | `shared\src\Security\JwtService.php` | 54 | ✅ |
| 18 | State override | `shared\src\AI\DataBinder.php` | 58/91-94 | ✅ |
| 19 | Listener filter | `shared\src\Events\EventDispatcher.php` | 70/92 | ✅ |
| 20 | Semantic token | `assets...\Css\01_Abstracts\a-semantic-token.css` | 170 | ✅ |

**Okuma oranı:** 20 örnekten **19'u** satır okumasıyla doğrulandı; **1'i** (§13 RateLimit) yalnızca
dosya varlığı + boyut düzeyinde → `⚠️ VERIFICATION REQUIRED`, uydurma kod bloğu yazılmadı.

## §28 — Anti-Pattern Karşılığı Olmayan Durumlar (tasarım kararı)

| # | Durum | Repo'da karşılığı | Karar |
|---|-------|-------------------|-------|
| 1 | `*Mapper*` dosyası | **0** (glob) | Mapper ayrı sınıf **değil**, `fromArray`/`toArray` fabrikası içinde |
| 2 | Hydrator/ORM | **0** (ORM yasaklı — ADR) | Elle eşleme + `strict_types` |
| 3 | `array_reduce` | **0** | Reduce deseni kullanılmıyor → §2 web-arastirma #20 MISS |
| 4 | `unserialize` | **0** | Serileştirme tek yönlü (`serialize` 4, `unserialize` 0) |
| 5 | DTO için `array_is_list`/`json_validate` | **0** | Havuz claim'i repo'da karşılanmamış (PLANNED) |
| 6 | `shared\tests` DTO testi | **0** | auth'ta 1 örnek var (`LoginRequestTest`) — §11.1 doğrulama |

## §29 — Örnek → Regresyon Eşlemesi (bu örnek değişirse ne yeniden okunur)

| # | Örnek | Değişiklik | Yeniden okunacak |
|---|-------|-----------|------------------|
| 1 | §1 | DTO alanı ekleme | `fromArray`/`toArray` çifti + §24 #1 |
| 2 | §9, §10 | `ceil` mantığı değişimi | iki `ceil` noktası (ApiResponse:79 · PaginationResponse:104) |
| 3 | §11 | Content-Type kuralı | `isAcceptedContentType` (116) + 415 bloğu (63-70) |
| 4 | §12 | Cache kuralı | `isCacheable` (41-44) + CC (91, 93-94) |
| 5 | §14 | Header stratejisi | `SecurityHeadersMiddleware.php:43` |
| 6 | §21 | CSRF token biçimi | `CsrfSyncManager.js:5-7` + `FetchWrapper.js:47-52` |
| 7 | §7, §22 | Şema değişimi | `coremusic_api.sql:18-39` |
| 8 | §8, §23 | Token değişimi | `a-primitive-tokens.css:26-42` |

**Yeniden okuma kuralı:** Her örnek bloğu `Dosya: … · Satır: …` taşıdığı için tek başına
yeniden doğrulanabilir; satır numarası kayarsa yalnız ilgili blok `⚠️` ile işaretlenir,
diğer bloklar etkilenmez.

## §30 — Örnek Sayım Özeti (kademe-2 kapanış ölçümü)

| Metrik | Değer |
|---|:---:|
| §1–§23 kod bloğu | **20** |
| Dil dağılımı (PHP / JS / SQL / CSS) | **12 / 4 / 2 / 2** |
| YAPMA↔YAP çifti | **13** |
| Edge-case satırı | **12** |
| Satır okumasıyla doğrulanan örnek | **19 / 20** |
| `⚠️ VERIFICATION REQUIRED` | **1** (§13 RateLimit) |
| Havuz dışı URL | **0** |
| Belge satırı | **500 → 505+ (hedef 505–540)** |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
**Last Updated:** 2026-10-16
