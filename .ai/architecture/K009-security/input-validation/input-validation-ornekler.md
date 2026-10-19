---
node_id: input-validation-ornekler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - input-validation-anayasa
  - input-validation-surucu
evidence:
  - "shared/src/Middleware/ValidationMiddleware.php:17-83"
  - "shared/src/Api/Middleware/Validator.php:35-97,126-144"
  - "shared/src/Api/Middleware/RequestValidationMiddleware.php:46-96,116-123,165-174"
  - "shared/src/Security/ReturnUrlPolicy.php:25-88,102-112,157-184"
  - "shared/src/Exception/ValidationException.php:21-50"
  - "api.coremusic.net/config/routes.php:53-79"
  - "shared/tests/Middleware/ValidationMiddlewareTest.php:42-57"
  - "shared/tests/Unit/Security/ReturnUrlPolicyTest.php:55-80"
  - "shared/tests/Security/ReturnUrlPolicyEdgeCaseTest.php:20-33,163-193"
validation:
  status: pending
  checks:
    - "≥4 gerçek PHP alıntısı Dosya·Satır ile — OK (8 birincil + 3 test alıntısı)"
    - "Satır hedefi ≥200 — OK"
    - "Her alıntı 2026-10-15'te `read` ile diskten doğrulandı"
open_questions:
  - "⚠️ U-1: validation şemasının tek kaynağı (tek dosya) hâlâ yok — bkz. anayasa U-1."
  - "⚠️ U-6: Validator.php:79'daki @preg_match hata yutmasının testi yok."
  - "⚠️ U-8: DTO katmanı (LoginRequest::fromArray) doğrulama içermiyor; bu dosyalarda DTO alıntısı yok çünkü doğrulama kodu yok."
group: kod
---

# K009 · input-validation · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · rol: docs-writer (kanıt-alıntı)

Bu sayfa üretim kodundan **gerçek, 2026-10-15'te diskten okunmuş** alıntılar içerir.
Her alıntı `Dosya:Satır` künyesi taşır; uydurma satır numarası veya uydurma kod yoktur.
Alıntıların yorumu, red haritası ve kararları [[input-validation-anayasa]] ile
[[input-validation-surucu]] içindedir — burada yalnızca kanıt tekrar edilir.

## §1. Alıntı 1 — Mirror doğrulayıcı: GET atlaması + kural kapısı

**`shared/src/Middleware/ValidationMiddleware.php:17-34`** (salt-okunur, diskten):

```php
public function handle(array $request, callable $next): array
{
    $method = strtoupper($request['method'] ?? 'GET');

    // Sadece state-changing istekleri doğrula
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
        return $next($request);
    }

    $routeMeta    = $request['_route_meta'] ?? [];
    $requiredFields = $routeMeta['required_fields'] ?? [];

    if (empty($requiredFields)) {
        return $next($request);
    }

    $body = $request['body'] ?? [];
    $errors = [];
```

**Kanıt notları:**

- Middleware pipeline sözleşmesi `handle(array, callable): array` — PSR-7 değil
  (sınıf başlığı `L1 — Pipeline #10`, `:8`).
- Kural kaynağı **tek**: `$request['_route_meta']['required_fields']` (`:26-27`).
  Meta boşsa middleware **pasif** (`:29-31`) — test `testEmptyMeta_passesThrough`
  bunu kilitler (`ValidationMiddlewareTest.php:30-40`).
- `GET/HEAD/OPTIONS` tamamen atlanır (`:22-24`) — GET query params **hiç
  doğrulanmaz** (anayasa §10.4 R-10, surucu D-10).

**Kural döngüsü — `ValidationMiddleware.php:36-70`** (salt-okunur):

```php
foreach ($requiredFields as $field => $rules) {
    $value = $body[$field] ?? null;
    $label = $rules['label'] ?? $field;

    // Zorunlu alan kontrolü
    if (!empty($rules['required']) && ($value === null || $value === '')) {
        $errors[$field] = "{$label} zorunludur.";
        continue;
    }

    // Minimum uzunluk
    if (isset($rules['min_length']) && is_string($value) && strlen($value) < $rules['min_length']) {
        $errors[$field] = "{$label} en az {$rules['min_length']} karakter olmalıdır.";
        continue;
    }

    // Maksimum uzunluk
    if (isset($rules['max_length']) && is_string($value) && strlen($value) > $rules['max_length']) {
        $errors[$field] = "{$label} en fazla {$rules['max_length']} karakter olmalıdır.";
        continue;
    }

    // Email formatı
    if (!empty($rules['email']) && is_string($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $errors[$field] = "{$label} geçerli bir e-posta olmalıdır.";
        continue;
    }

    // Enum değerleri
    if (isset($rules['enum']) && !in_array($value, $rules['enum'], true)) {
        $allowed = implode(', ', $rules['enum']);
        $errors[$field] = "{$label} şu değerlerden biri olmalıdır: {$allowed}.";
        continue;
    }
}
```

**Kanıt notları:**

- 5 kural: `required` (`:41`) · `min_length` (`:47`) · `max_length` (`:53`) ·
  `email` → `filter_var(FILTER_VALIDATE_EMAIL)` (`:59`) · `enum` → strict
  `in_array` (`:65`).
- `min_length/max_length` yalnız `is_string` üzerinde çalışır — sayısal/dizi
  değerlerde uzunluk kuralı **sessizce atlanır** (surucu D-03).
- `enum` ihlalinde **izinli değerler istemciye döner** (`:66-67`) — surucu D-08
  (hata mesajında kural bilgisi sızıntısı) ile aynı kategori.
- `continue` ile alan başına **ilk ihlal** yazılır; diğer kurallar atlanır.

**İhlalde yanıt — `ValidationMiddleware.php:72-83`** (salt-okunur):

```php
if (!empty($errors)) {
    return [
        'httpStatus' => 422,
        'type'       => 'json',
        'body'       => ['error' => 'validation_failed', 'errors' => $errors],
        'headers'    => [],
        'halt'       => true,
    ];
}

return $next($request);
```

- Durum kodu **422** (`httpStatus => 422`, anayasa §11 red haritasıyla birebir);
  `halt => true` pipeline'ı durdurur. (400 yalnızca API katmanında bozuk JSON
  içindir — §3.)
- `$errors` `{alan => insan-okunur mesaj}` haritasıdır — **alan adları ve kural
  mesajları istemciye açık**.

## §2. Alıntı 2 — API kural motoru: validate + ihlal toplama

**`shared/src/Api/Middleware/Validator.php:35-57`** (salt-okunur, diskten):

```php
public function validate(array $data, array $rules): array
{
    $errors = [];

    foreach ($rules as $field => $rule) {
        $exists = array_key_exists($field, $data);
        $value  = $exists ? $data[$field] : null;

        if (!$exists || $value === null || $value === '') {
            if (!empty($rule['required'])) {
                $errors[$field] = "{$field} is required";
            }
            continue;
        }

        foreach ($this->fieldErrors($field, $value, $rule) as $error) {
            $errors[$field] = $error;
            break;
        }
    }

    return $errors;
}
```

**Kanıt notları:**

- Kural biçimi **associative array** — dosya başlığındaki sözleşmeye göre
  (`Validator.php:22-25`): `'email' => ['required' => true, 'type' => 'email']`.
- `required` ihlali ve sonraki kurallar ayrı dallarda toplanır; alan başına
  `break` ile **tek hata** seçilir (`:52`).
- Dönüş `array<alan, mesaj>` — boş dizi = geçerli (`:33` docblock).

**Alan kuralları — `Validator.php:63-97`** (salt-okunur):

```php
private function fieldErrors(string $field, mixed $value, array $rule): array
{
    $errors = [];

    if (isset($rule['type'])) {
        $typeError = $this->typeError($field, $value, (string) $rule['type']);
        if ($typeError !== null) {
            $errors[] = $typeError;
        }
    }

    if ($errors !== []) {
        return $errors;
    }

    if (isset($rule['regex']) && is_string($rule['regex']) && is_scalar($value)) {
        if (@preg_match($rule['regex'], (string) $value) !== 1) {
            $errors[] = "{$field} has an invalid format";
        }
    }

    if (isset($rule['in']) && is_array($rule['in']) && !in_array($value, $rule['in'], true)) {
        $errors[] = "{$field} must be one of: " . implode(', ', array_map('strval', $rule['in']));
    }

    if (isset($rule['min'])) {
        $this->compare($field, $value, (float) $rule['min'], 'min', $errors);
    }

    if (isset($rule['max'])) {
        $this->compare($field, $value, (float) $rule['max'], 'max', $errors);
    }

    return $errors;
}
```

**Kanıt notları:**

- `@preg_match` (`:79`): regex çökerse (`false`) `!== 1` → hata üretilir ama
  **uyarı yutulur** — geçersiz regex tanımı üretimde görünmez kalır (anayasa U-6).
- `in` enum strict (`:84`) — `in_array(..., true)`.
- Tip hatası varsa **diğer kurallara hiç girilmez** (`:74-76`).
- `min/max` tek yardımcıda (`compare`, `:102-124`): sayısal → değer, string →
  `mb_strlen` karşılaştırması.

**Tip denetimi — `Validator.php:126-144`** (salt-okunur):

```php
private function typeError(string $field, mixed $value, string $type): ?string
{
    return match ($type) {
        'string' => is_string($value) ? null : "{$field} must be a string",
        'int'    => (is_int($value) || (is_string($value) && preg_match('/^-?\d+$/', $value) === 1))
            ? null : "{$field} must be an integer",
        'float'  => is_float($value) || is_int($value)
            ? null : "{$field} must be a number",
        'bool'   => is_bool($value) ? null : "{$field} must be a boolean",
        'array'  => is_array($value) ? null : "{$field} must be an array",
        'email'  => $this->isEmail($value) ? null : "{$field} must be a valid email address",
        default  => null,
    };
}

private function isEmail(mixed $value): bool
{
    return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}
```

- 7 tip (`string/int/float/bool/array/email` + `default`) — `default => null`:
  **tanımsız tip sessizce geçer** (`:137`) (surucu D-04).
- `email` tipi yalnızca string kabul eder ve `filter_var` kullanır (`:141-144`).

## §3. Alıntı 3 — API middleware zinciri: 415 → 400 → 422

**`shared/src/Api/Middleware/RequestValidationMiddleware.php:46-96`** (salt-okunur):

```php
public function __invoke(array $request, callable $next): array
{
    $route = $request['_route'] ?? null;
    $rules = is_array($route) ? ($route['validation'] ?? null) : null;

    // Route kural içermiyorsan hiçbir girdi kısıtı uygulanmaz
    if (!is_array($rules) || $rules === []) {
        return $next($request);
    }

    $method  = strtoupper((string) ($request['method'] ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET')));
    $isBody  = in_array($method, self::BODY_METHODS, true);

    if ($isBody) {
        $contentType = $this->contentType($request);

        if ($contentType === null || !$this->isAcceptedContentType($contentType)) {
            return ApiResponse::error(
                'UNSUPPORTED_MEDIA_TYPE',
                'Content-Type must be application/json',
                415,
                ['content_type' => $contentType ?? '']
            );
        }

        $decoded = $this->decodeBody($request, $contentType);
        if ($decoded['error'] !== null) {
            return ApiResponse::error('INVALID_JSON', 'Request body is not valid JSON', 400, [
                'reason' => $decoded['error'],
            ]);
        }

        $data = $decoded['data'];
    } else {
        $data = $request['query'] ?? $_GET;
        if (!is_array($data)) { $data = []; }
    }

    $errors = $this->validator->validate($data, $rules);
    if ($errors !== []) {
        $response = ApiResponse::error('VALIDATION_ERROR', 'Validation failed', 422, $errors);
        $response['error']['fields'] = $errors;

        return $response;
    }

    return $next($request);
}
```

**Kanıt notları:**

- Üç hata kodu zincirleme: **415** (content-type, `:63-70`) → **400** (bozuk JSON,
  `:73-77`) → **422** (kural ihlali, `:87-96`).
- `422` yanıtında `error.fields` zorunlu — dosya yorumu: "Faz 3a UI sözleşmesi"
  (`:89-93`).
- Route'ta `validation` yoksa **hiçbir kısıt yok** (`:51-55`) — bu, anayasa U-2
  (`required_fields` üreten route grep 0) ile birlikte okunur: iki doğrulayıcı
  da route tanımına bağımlı ve route tanımları kısmi.
- Body methodları sabit: `POST/PUT/PATCH` (`:34`); GET'de veri `$request['query']`
  veya `$_GET`'ten gelir (`:81`) — mirror'dan farklı olarak GET **burada**
  doğrulanır (yalnız route `validation` tanımlıysa).
- `isAcceptedContentType` — `RequestValidationMiddleware.php:116-123`:
  `application/json` · `application/problem+json` · `application/x-www-form-urlencoded`
  (`str_contains` ile, satır sonu/parametre tolere edilir).
- `decodeJson` — `:165-174`: `json_decode(..., true)` + `is_array` kontrolü;
  değilse `json_last_error_msg()` hata olarak döner.

## §4. Alıntı 4 — Return-URL host whitelist (redirect yüzeyi)

**`shared/src/Security/ReturnUrlPolicy.php:25-56`** (salt-okunur, diskten):

```php
public static function isAllowed(string $url): bool
{
    if ($url === '' || $url === '/') {
        return true;
    }

    // Ham kontrol karakteri (\t \n \r) hiçbir meşru redirect URL'inde
    // bulunmaz. ... (yorum satırı, :31-38)
    if (self::hasControlChars($url)) {
        return false;
    }

    if (str_starts_with($url, '/')) {
        // '//' (protokol-relative) ve '/\' ... dış host'a redirect üretir → açık redirect.
        if (self::isProtocolRelative($url)) {
            return false;
        }

        return true;
    }
    // ... devamı §5'te
}
```

**Protokol-relative yardımcı — `ReturnUrlPolicy.php:102-112`** (salt-okunur):

```php
private static function isProtocolRelative(string $url): bool
{
    foreach ([$url, rawurldecode($url)] as $candidate) {
        $clean = str_replace(["\t", "\n", "\r"], '', $candidate);
        if (str_starts_with($clean, '//') || str_starts_with($clean, '/\\')) {
            return true;
        }
    }

    return false;
}
```

**Kanıt notları:**

- Kontrol karakteri hem **ham** hem **tek katman decode** edilmiş girdide aranır
  (`hasControlChars`, `:121-125`: `strpbrk($url, "\t\n\r")` iki kez).
- Protokol-relative kontrolü iki adayda (`url`, `rawurldecode(url)`) ve
  tab/newline temizliği **kontrolün hemen önünde** yapılır (`:105`) — WHATWG
  parser `\t\n\r` attığı için `/%09/evil.com` varyantı da yakalanır.

**Ham parse + host eşitliği — `ReturnUrlPolicy.php:76-88`** (salt-okunur):

```php
$rawParsed     = parse_url($url);
$decodedParsed = parse_url($decoded);
if ($rawParsed === false || $decodedParsed === false) {
    return false;
}
if (isset($rawParsed['user']) || isset($rawParsed['pass'])) {
    return false;
}
if (strtolower((string)($rawParsed['host'] ?? '')) !== strtolower((string)($decodedParsed['host'] ?? ''))) {
    return false;
}

return true;
```

- `user`/`pass` (userinfo) **ham parse'da** varsa ret (`:81-83`) —
  `http://home.coremusic.net@evil.com/` varyantı.
- Ham ve decode edilmiş host'lar **eşit olmalı** (`:84-86`) — `%23`/`%40`
  encode edilmiş authority sapması engellenir.

## §5. Alıntı 5 — getSafeUrl: ikinci savunma hattı

**`shared/src/Security/ReturnUrlPolicy.php:157-184`** (salt-okunur):

```php
if (filter_var($url, FILTER_VALIDATE_URL) === false) {
    return '/';
}

$parsed = parse_url($url);
if (!$parsed || empty($parsed['host'])) {
    return '/';
}

$host = strtolower($parsed['host']);
$urlScheme = strtolower($parsed['scheme'] ?? '');

if (in_array($urlScheme, ['javascript', 'data', 'vbscript'], true)) {
    return '/';
}

if (isset($parsed['user']) || isset($parsed['pass'])) {
    return '/';
}

foreach (self::ALLOWED_HOSTS as $allowed) {
    if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
        return $url;
    }
}

return '/';
```

**Kanıt notları:**

- Whitelist sabittir — `ReturnUrlPolicy.php:7-15`:
  `coremusic.net` · `home.` · `auth.` · `music.` · `admin.` · `localhost` · `127.0.0.1`.
- Alt alan adı eşleşmesi `str_ends_with($host, '.' . $allowed)` (`:178`) —
  `evil-coremusic.net` eşleşmez çünkü nokta-prefiks zorunlu; `coremusic.net.evil.com`
  de eşleşmez (suffix `evil.com`).
- Tehlikeli şema üçlüsü ret: `javascript|data|vbscript` (`:169-171`).
- Whitelist dışı her şey `/` (`:183`) — fail-safe: hata yerine güvenli dönüş.

## §6. Alıntı 6 — Uç bazlı validation şeması (route tanımı)

**`api.coremusic.net/config/routes.php:53-79`** (salt-okunur, diskten):

```php
'/api/v1/auth/login' => [
    'service'     => 'auth',
    'handler'     => 'authController',
    'action'      => 'login',
    'public'      => true,
    'implemented' => true,
    'validation'  => [
        // identity: e-posta VEYA kullanıcı adı (LoginRequest::fromArray)
        'email'    => ['required' => true, 'type' => 'string'],
        'password' => ['required' => true, 'type' => 'string', 'min' => 8],
        'gender'   => ['in' => ['male', 'female', 'neutral']],
    ],
],
'/api/v1/auth/register' => [
    'service'     => 'auth',
    'handler'     => 'authController',
    'action'      => 'register',
    'public'      => true,
    'implemented' => true,
    'validation'  => [
        'username'    => ['required' => true, 'type' => 'string', 'regex' => '/^[a-zA-Z0-9_]{3,30}$/'],
        'email'       => ['required' => true, 'type' => 'email'],
        'password'    => ['required' => true, 'type' => 'string', 'min' => 8],
        'gender'      => ['required' => true, 'in' => ['male', 'female', 'neutral']],
        'agree_terms' => ['required' => true, 'type' => 'bool'],
    ],
],
```

**Kanıt notları:**

- Bu dosyada `validation` anahtarı toplam **5 uçta** var (`:59, :72, :86, :96, :108` —
  login, register, forgot-password, reset-password, set-gender). `logout` gibi
  uçlarda yok → o uçlar doğrulamasız (`RequestValidationMiddleware.php:53-55`
  kuralı gereği).
- `login.email` `type => string` — **email formatı bu uçta route katmanında
  doğrulanmıyor** (format kontrolü yalnız `type => email` uçlarında, örn.
  register `:74`).
- `username regex` (`:73`) — `Validator.php:79`'daki `@preg_match` yolunu kullanır;
  desen `^[a-zA-Z0-9_]{3,30}$` → ReDoS riski yok (lineer desen), ancak bu
  desenin kendisi `min`/`max` ile çakışmaz, regex tek başına yeterlidir.
- `password min => 8` → `Validator::compare` → `is_string` ise `mb_strlen >= 8`
  (`Validator.php:115-123`).

## §7. Alıntı 7 — ValidationException fabrikaları (422)

**`shared/src/Exception/ValidationException.php:10-50`** (salt-okunur, diskten):

```php
private function __construct(string $message, string $errorCode, array $errors = [])
{
    parent::__construct($message, $errorCode, 422);
    $this->errors = $errors;
}

public function getErrors(): array
{
    return $this->errors;
}

public static function emptyFields(): self
{
    return new self('E-posta ve şifre zorunludur.', 'EMPTY_FIELDS');
}

public static function multiple(array $errors): self
{
    $first = reset($errors) ?: 'Doğrulama hatası.';
    return new self($first, 'VALIDATION_ERRORS', $errors);
}

public static function invalidEmail(): self
{
    return new self('Geçerli bir e-posta adresi girin.', 'INVALID_EMAIL');
}
```

- Constructor `private` (`:10`) — nesne yalnızca statik fabrikalardan üretilir.
- HTTP durumu **422** sabit (`:12`).
- `multiple()` ilk mesajı `reset()` ile seçer, tam haritayı `errors` taşır
  (`:26-30`) — `RequestValidationMiddleware` benzeri `error.fields` haritasının
  controller katmanındaki karşılığı.
- Fabrika listesi tam: `emptyFields` · `multiple` · `invalidEmail` ·
  `invalidToken` · `tokenExpired` · `passwordTooShort(int $min)` (`:21-50`).

## §8. Alıntı 8 — Test kanıtları (davranış kilitleri)

**`shared/tests/Middleware/ValidationMiddlewareTest.php:42-57`** (salt-okunur):

```php
public function testMissingRequiredField_halt422(): void
{
    $result = $this->handle([
        'method' => 'POST',
        'uri' => '/home',
        '_route_meta' => [
            'required_fields' => ['email' => ['required' => true, 'label' => 'E-posta', 'email' => true]],
        ],
        'body' => [],
    ]);

    $this->assertTrue($result['halt'] ?? false);
    $this->assertSame(422, $result['httpStatus']);
    $this->assertSame('validation_failed', $result['body']['error']);
    $this->assertArrayHasKey('email', $result['body']['errors']);
}
```

**Ek kilitler (aynı dosya):**

- `testEmptyMeta_passesThrough` (`:30-40`) — `required_fields` boşsa pasif.
- `testGetRequests_skipValidation` (`:76-86`) — GET atlanır.
- `testEnumAndEmailRules_enforced` (`:88-105`) — `email: 'bozuk'` + `role: 'superuser'`
  → her iki alan da `errors` haritasında.

**`shared/tests/Unit/Security/ReturnUrlPolicyTest.php:55-60`** (salt-okunur):

```php
public function testDisallowedHostIsRejected(): void
{
    $this->assertFalse(ReturnUrlPolicy::isAllowed('http://evil.com/'));
    $this->assertFalse(ReturnUrlPolicy::isAllowed('https://evil-coremusic.net/'));
    $this->assertFalse(ReturnUrlPolicy::isAllowed('https://coremusic.net.evil.com/'));
}
```

**`shared/tests/Unit/Security/ReturnUrlPolicyTest.php:74-80`** (salt-okunur):

```php
public function testEncodedAuthorityDivergenceIsRejected(): void
{
    // Raw parse host=evil.com (userinfo var), decode edilmiş parse
    // host=home.coremusic.net → open redirect + auth_key sızıntısı riski.
    $this->assertFalse(ReturnUrlPolicy::isAllowed('http://home.coremusic.net%23@evil.com/'));
    $this->assertFalse(ReturnUrlPolicy::isAllowed('http://home.coremusic.net%40evil.com/'));
}
```

**`shared/tests/Security/ReturnUrlPolicyEdgeCaseTest.php:20-33`** (salt-okunur):

```php
public function testDataSchemeBlocked(): void
{
    $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('data:text/html,<script>alert(1)</script>'));
}

public function testVbscriptSchemeBlocked(): void
{
    $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('vbscript:MsgBox("XSS")'));
}

public function testJavascriptSchemeBlocked(): void
{
    $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('javascript:document.cookie'));
}
```

**Whitelist bütünlük testi — `ReturnUrlPolicyEdgeCaseTest.php:163-193`** (salt-okunur):

```php
public function testAllWhitelistedHostsAllowed(): void
{
    $hosts = [
        'coremusic.net',
        'home.coremusic.net',
        'auth.coremusic.net',
        'music.coremusic.net',
        'admin.coremusic.net',
        'localhost',
        '127.0.0.1',
    ];

    foreach ($hosts as $host) {
        $result = ReturnUrlPolicy::getSafeUrl("https://{$host}/test");
        $this->assertStringContainsString($host, $result, "Host {$host} should be allowed");
    }
}

public function testNonWhitelistedHostsBlocked(): void
{
    $hosts = ['evil.com', 'google.com', 'facebook.com', 'coremusic-evil.com'];

    foreach ($hosts as $host) {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl("https://{$host}/steal"),
            "Host {$host} should be blocked");
    }
}
```

- Test dosyası başlığı: "OWASP A01 (Broken Access Control) + A03 (Injection) coverage"
  (`ReturnUrlPolicyEdgeCaseTest.php:10`).

## §9. Alıntı yok — DTO katmanı (negatif kanıt)

**`shared/src/Api/Dto/Request/LoginRequest.php`** — `fromArray` üretimi
doğrulama **içermez** (alan okuma + `?? ''` fallback). Bu yüzden bu sayfada DTO
alıntısı yapılmamıştır; kanıt yokluğu kanıttır: **⚠️ U-8** — DTO'lar doğrulama
katmanı değildir ve öyle davranmamalıdır; mevcut kodda `$errors` üretimi yoktur.
(İlgili open_question, bu dosyanın frontmatter'ında.)

## §10. Örnek → Kural eşlemesi

| # | Örnek | Dosya·Satır | Kanıtladığı kural | Surucu xref |
|---|---|---|---|---|
| 1 | Mirror kural kapısı + 422 | ValidationMiddleware.php:17-34, 36-70, 72-83 | 5 kural · GET atlaması · meta bağımlılığı | D-01, D-03, D-10 |
| 2 | Kural motoru + tip/regex/enum | Validator.php:35-57, 63-97, 126-144 | associative kural şeması · @preg_match · default geç | D-04, D-06 (U-6) |
| 3 | 415 → 400 → 422 zinciri | RequestValidationMiddleware.php:46-96 | medya tipi · bozuk JSON · error.fields | §10.4 R-10 |
| 4 | isAllowed + isProtocolRelative | ReturnUrlPolicy.php:25-56, 76-88, 102-112 | kontrol karakteri · userinfo · authority sapması | §10.3 R-11 |
| 5 | getSafeUrl ikinci hat | ReturnUrlPolicy.php:157-184 | sabit whitelist · scheme ret · fail-safe `/` | §10.3 R-11 |
| 6 | 5 uçta validation şeması | routes.php:53-79 (+ :86, :96, :108) | uç bazlı kural · login'de email format yok | §6, U-3 |
| 7 | Exception fabrikaları | ValidationException.php:10-50 | 422 tek kaynak · errors haritası | §10.4 |
| 8 | Test kilitleri | ValidationMiddlewareTest.php:42-57 · ReturnUrlPolicyTest.php:55-60, 74-80 · EdgeCaseTest.php:20-33, 163-193 | davranış sözleşmesi | §7 adım 3-4 |
| 9 | DTO doğrulama yokluğu | LoginRequest.php (`fromArray`) | ⚠️ negatif kanıt | U-8 |

## §11 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[index]] | üst | bu çocuk künyesi |
| [[input-validation-anayasa]] | üst | kurallar (IV-01..IV-16) — alıntıların yorumu |
| [[input-validation-surucu]] | üst | red haritası R-01..R-13 — alıntıların sürüş karşılığı |
| [[input-validation-dogrulama]] | yan | AD-xx kanıt tablosu (alıntı satırlarının özeti) |
| [[input-validation-bilesenler]] | yan | bileşen envanteri (B-1..B-10) |
| [[rate-limiting-index]] | öncesi | 429 kotası |
| [[authorization-kapsam]] | sonrası | 403/401 kararı |

## §12 Doğrulama

1. Her alıntı 2026-10-15'te `read` ile diskten okundu ve satır aralığıyla birebir.
2. 8 birincil + 3 test alıntısı = 11 gerçek PHP bloğu; uydurma kod yok.
3. Bir önceki taslaktaki uydurma alıntılar (fnmatch/JsonResponse ailesi, kurgusal
   `normalizeUrl`) gerçek dosyalarla karşılaştırılarak kaldırıldı — bu sürüm
   yalnızca diskte okunan içeriği yansıtır.
4. `status: verify-required` — kod değişebilir; her vault revizyonunda yeniden
   doğrulanır.
5. ⚠️ işaretli 3 açık soru: U-1 (şema tek kaynak yok), U-6 (`@preg_match` yutma),
   U-8 (DTO'da doğrulama yok).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
