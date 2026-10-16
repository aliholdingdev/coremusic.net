---
node_id: K009-security/security-testing/security-testing-ornekler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-testing/security-testing-kapsam
  - K009-security/security-testing/security-testing-surucu
evidence: "11 gerçek alıntı (filesystem read 2026-10-15): 6 test dosyası + composer.json + 2 workflow"
validation: "Alıntılar Dosya·Satır · uydurma snippet 0 · ≥4 alıntı şartı aşıldı (11)"
open_questions: "SecurityHeaders için AYRI test dosyası YOK — yalnız MiddlewarePipelineTest içinde (§4) ⚠️"
group: kod
---

# K009 · security-testing · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Gerçek test/CI alıntıları (filesystem read, diskten).

## §1 ALINTI LİSTESİ

| # | Dosya | Satır | Konu |
|---|---|---|---|
| Ö-01 | `shared/tests/Middleware/CsrfMiddlewareTest.php` | 85-97 | CSRF'siz POST → 403 |
| Ö-02 | `shared/tests/Middleware/CsrfMiddlewareTest.php` | 306-324 | timing-safe karşılaştırma |
| Ö-03 | `shared/tests/Middleware/RateLimiterMiddlewareTest.php` | 52-74 | limit aşımı → 429 |
| Ö-04 | `shared/tests/Middleware/MiddlewarePipelineTest.php` | 176-221 | 10 adımlık pipeline sırası |
| Ö-05 | `shared/tests/Middleware/MiddlewarePipelineTest.php` | 254-264 | CSP `connect-src` başlığı |
| Ö-06 | `shared/tests/Api/ApiPipelineSecurityTest.php` | 52-63 | CORS fail-closed |
| Ö-07 | `shared/tests/Middleware/OriginCheckMiddlewareTest.php` | 67-74 | boş allowlist → 403 |
| Ö-08 | `shared/tests/Unit/Log/LoggerRedactionTest.php` | 70-88 | parola/token/Authorization redaksiyonu |
| Ö-09 | `shared/composer.json` | 23-26 · 46-49 | require-dev + scriptler |
| Ö-10 | `.github/workflows/ci.yml` | 62-64 · 104-108 · 123-127 | PHPStan · PHPUnit · audit |
| Ö-11 | `.github/workflows/secret-scan.yml` | 24-31 | gitleaks adımı |

## §2 Ö-01 — CSRF'SİZ POST → 403 (NEGATİF VAKA)

~~~php
// shared/tests/Middleware/CsrfMiddlewareTest.php · satır 85-97
public function testPostWithoutTokenReturns403(): void
{
    $middleware = new CsrfMiddleware();

    $result = $middleware->handle(
        ['method' => 'POST', 'uri' => '/submit', 'headers' => [], 'body' => []],
        fn(array $req) => ['status' => 200]
    );

    $this->assertSame(403, $result['httpStatus']);
    $this->assertSame('csrf_invalid', $result['body']['error']);
    $this->assertTrue($result['halt'] ?? false);
}
~~~

| Not | |
|---|---|
| Reddi **kod** değil, **test** doğruluyor | anayasa R-05 (negatif vaka) |
| Üç assertion: durum + gövde + durdurma | dolgu assertion yok |
| ADR-010 | `csrf_token` anahtarı (dosya başlık docblock'u, satır 11) |

## §3 Ö-02 — TIMING-SAFE KARŞILAŞTIRMA

~~~php
// shared/tests/Middleware/CsrfMiddlewareTest.php · satır 306-324
public function testTimingSafeComparison(): void
{
    // hash_equals() kullanıldığından emin ol — timing attack koruması
    $middleware = new CsrfMiddleware();

    // Farklı uzunlukta token'lar
    $result = $middleware->handle(
        [
            'method'  => 'POST',
            'uri'     => '/submit',
            'headers' => ['x-csrf-token' => 'a'],
            'body'    => [],
            '_session' => ['csrf_token' => 'ab'],
        ],
        fn(array $req) => ['status' => 200]
    );

    $this->assertSame(403, $result['httpStatus'], 'Different length tokens must fail');
}
~~~

| Not | |
|---|---|
| `hash_equals` kullanımı | `.ai/AGENTS.md` §16 Security standardı |
| Aynı dosyada bypass route reddi | satır 192-216 (`testSetGenderRouteRequiresCsrf`) |
| ADR-010 notu | "bypass route'ları kaldırıldı" — satır 194-195 yorumu |

## §4 Ö-03 — RATE LIMIT AŞIMI → 429

~~~php
// shared/tests/Middleware/RateLimiterMiddlewareTest.php · satır 52-74
public function testRateLimitExceededReturns429(): void
{
    $cache = $this->createCacheMock([
        'set' => false,     // Mevcut key — yeni değil
        'increment' => 61,  // Limit aşıldı (60+1)
    ]);

    $middleware = new RateLimiterMiddleware(
        maxRequests: 60,
        windowSeconds: 60,
        cache: $cache
    );

    $result = $middleware->handle(
        ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
        fn(array $req) => ['status' => 200]
    );

    $this->assertSame(429, $result['httpStatus']);
    $this->assertSame('rate_limit_exceeded', $result['body']['error']);
    $this->assertSame('60', $result['headers']['Retry-After']);
    $this->assertTrue($result['halt']);
}
~~~

| İlgili test (aynı dosya) | Satır | Konu |
|---|---|---|
| `testFirstRequestAlwaysAllowed` | 23-46 | mutlu yol |
| `testRateLimitExactlyAtMaxAllowed` | 76-99 | sınır değeri (60/60) |
| `testCacheIncrementFailureAllowsRequest` | 105-128 | **fail-open** — ⚠️ anayasa M-03 ile tartışmalı |
| `testNoCacheAvailableAllowsRequest` | 159-177 | cache yok → geçiş |
| `testCustomMaxRequests` | 279-299 | 429 + `Retry-After: 30` |

> **Açık soru:** `RateLimiterMiddlewareTest:127` "Cache failure should allow request (fail-open)"
> diyor; `security-test-stratejisi.md` T-10 ise "Cache yokken /login **fail-closed**" bekliyor.
> Bu çelişki `dogrulama` §AD-09'da işaretlidir → `⚠️ VERIFICATION REQUIRED`.

## §5 Ö-04 — PIPELINE SIRASI (SECURITYHEADERS DAHİL)

~~~php
// shared/tests/Middleware/MiddlewarePipelineTest.php · satır 187-220
// ADR-010/011/012/013/022 pipeline sırası
$pipeline = new MiddlewarePipeline();
$pipeline
    ->pipe($createOrderMiddleware('1_OriginCheck'))
    ->pipe($createOrderMiddleware('2_Cors'))
    ->pipe($createOrderMiddleware('3_RateLimiter'))
    ->pipe($createOrderMiddleware('4_SecurityHeaders'))
    ->pipe($createOrderMiddleware('5_SessionManager'))
    ->pipe($createOrderMiddleware('6_Csrf'))
    ->pipe($createOrderMiddleware('7_BypassAuth'))
    ->pipe($createOrderMiddleware('8_Auth'))
    ->pipe($createOrderMiddleware('9_Permission'))
    ->pipe($createOrderMiddleware('10_Validation'));

// ...
$this->assertSame($expected, $executionOrder, 'Middleware pipeline order must match ADR-010/011/012/013/022');
~~~

| Not | |
|---|---|
| **SecurityHeaders ayrı test dosyası YOK** | yalnızca bu dosyada sıra + CSP testleri var → `⚠️` |
| CSP nonce sırası | satır 223-248 (`testCspNonceGeneratedBeforeSessionManager`) |
| Sıra bozulursa | "Sıra değişirse CSP bozulur" (satır 226 yorumu) |

## §6 Ö-05 — CSP `connect-src` REGRESYONU

~~~php
// shared/tests/Middleware/MiddlewarePipelineTest.php · satır 254-264
public function testCspConnectSrcAllowsApiOrigin(): void
{
    $middleware = new \CoreMusic\Middleware\SecurityHeadersMiddleware(new \CoreMusic\Config\DomainConfig());

    $response = $middleware->handle(['method' => 'GET'], static fn (): array => ['httpStatus' => 200]);

    $csp = $response['headers']['Content-Security-Policy'] ?? '';
    $this->assertStringContainsString("connect-src 'self' ", $csp);
    $this->assertMatchesRegularExpression("/connect-src [^;]*api\.coremusic\.net/", $csp);
    $this->assertMatchesRegularExpression("/connect-src [^;]*assets\.coremusic\.net/", $csp);
}
~~~

| Not | |
|---|---|
| Regresyon başlığı | "CSP CONNECT-SRC REGRESSION (api.coremusic.net)" — satır 251 |
| Fallback CSP | `ResponseEmitter::FALLBACK_CSP` reflection ile test edilir — satır 266-269 |
| `SecurityHeadersMiddleware` gerçek örneği | `new` ile kurulur (mock değil) |

## §7 Ö-06 — CORS FAIL-CLOSED

~~~php
// shared/tests/Api/ApiPipelineSecurityTest.php · satır 52-63
public function testCorsFailsClosedForUnknownOrigin(): void
{
    $middleware = new CorsMiddleware(['allowed_origins' => ['https://auth.coremusic.net']]);

    $response = $middleware->handle(
        ['server' => ['HTTP_ORIGIN' => 'https://evil.example'], 'method' => 'OPTIONS'],
        static fn (): array => ['httpStatus' => 204, 'type' => 'json', 'body' => '', 'headers' => [], 'halt' => true]
    );

    $this->assertArrayNotHasKey('Access-Control-Allow-Origin', $response['headers']);
    $this->assertArrayNotHasKey('Access-Control-Allow-Credentials', $response['headers']);
}
~~~

| Dosya başlık docblock'u (satır 15-18) | |
|---|---|
| "ADR-020 regresyon testleri" | §1.1-B.1 pipeline kaydı · §2.2E CORS (Bearer + fail-closed) · §2.2F hata sızıntısı |
| Aynı dosyada hata sızıntısı testi | `testGatewayDoesNotLeakExceptionMessageToClient` — satır 77 |
| `Authorization` / `X-Api-Key` başlıkları | satır 34-50 |

## §8 Ö-07 — BOŞ ALLOWLİST → 403 (PROD FAIL-CLOSED)

~~~php
// shared/tests/Middleware/OriginCheckMiddlewareTest.php · satır 67-74
public function testEmptyAllowlistInProduction_rejectsOrigin(): void
{
    $mw = new OriginCheckMiddleware(true, ['allowed_origins' => []]);

    $result = self::probe($mw, 'https://auth.coremusic.net');

    $this->assertSame(403, $result['httpStatus'], 'Prod + boş allowlist = fail-closed');
}
~~~

| Dosya docblock'u (satır 8-15) | |
|---|---|
| F-04 regresyonu | allowlist girdi formatı normalizasyonu |
| "fail-broken" riski | normalizasyon yoksa tüm cross-origin 403 olurdu |
| İlgili red | satır 47-56 — izinsiz host yine 403 |

## §9 Ö-08 — LOG REDAKSİYONU (PAROLA / TOKEN / AUTHORIZATION)

~~~php
// shared/tests/Unit/Log/LoggerRedactionTest.php · satır 70-88
public function testPasswordIsRedactedInKeyValueAndJsonFormats(): void
{
    // key=value
    $kv = Redactor::redact('[AUTH] login_failed email=a@b.com password=hunter2 ip=127.0.0.1');
    $this->assertStringContainsString('password=[REDACTED]', $kv);
    $this->assertStringNotContainsString('hunter2', $kv);
    $this->assertStringContainsString('ip=127.0.0.1', $kv);

    // JSON / dizi context — çok kelimeli değer dahil, tırnak yapısı korunur.
    $json = Redactor::redact('{"module":"auth","password":"my pass word","csrf_token":"abc123"}');
    $this->assertStringContainsString('"password":"[REDACTED]"', $json);
    $this->assertStringNotContainsString('my pass word', $json);
    $this->assertStringContainsString('"csrf_token":"[REDACTED]"', $json);

    // key: value + auth-scheme (Bearer) çok kelimeli değer.
    $bearer = Redactor::redact('Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.payload-signature');
    $this->assertStringContainsString('Authorization: [REDACTED]', $bearer);
    $this->assertStringNotContainsString('eyJhbGciOiJIUzI1NiJ9', $bearer);
}
~~~

| İlgili testler (aynı dosya) | Satır |
|---|---|
| `testAuthKeyInQueryStringIsRedacted` | 55-68 |
| `testCleanLinePassesThroughUnchanged` (false-positive) | 90-100 |
| `Psr3LoggerTest` → `"password":"[REDACTED]"` | `Unit/Log/Psr3LoggerTest.php:160` |

## §10 Ö-09 — TEST ALTYAPISI (composer.json)

~~~json
// shared/composer.json · satır 23-26
"require-dev": {
    "phpunit/phpunit": "^10.5",
    "phpstan/phpstan": "^1.10"
},
~~~

~~~json
// shared/composer.json · satır 46-49
"scripts": {
    "test": "phpunit",
    "stan": "phpstan analyse src --level=5"
}
~~~

| Not | |
|---|---|
| PHPUnit `^10.5` | `.ai/AGENTS.md` §4 QA satırı ile eşleşir |
| PHPStan `^1.10` | **tek SAST aracı** (anayasa §6) |
| `phpstan.neon` YOK | `ci.yml:60-61` yorumu: "composer scripti esas; plan level=8 değil, disk kazanır" |

## §11 Ö-10 — CI KAPILARI (ci.yml)

~~~yaml
# .github/workflows/ci.yml · satır 62-64
- name: PHPStan (composer stan)
  run: composer stan
  working-directory: shared
~~~

~~~yaml
# .github/workflows/ci.yml · satır 104-108
- name: PHPUnit
  run: vendor/bin/phpunit "$PHPUNIT_ARGS"
  env:
    PHPUNIT_ARGS: ${{ matrix.args }}
  working-directory: ${{ matrix.project }}
~~~

~~~yaml
# .github/workflows/ci.yml · satır 123-127
- name: Composer install + audit (shared)
  run: |
    composer install --no-progress --prefer-dist
    composer audit
  working-directory: shared
~~~

| Kapı notları | |
|---|---|
| `php-test` → `needs: php-lint` (satır 68) | lint geçmeden test çalışmaz |
| matrix (satır 74-80) | `shared` (tümü) · `auth.coremusic.net` (`--testsuite Unit`) |
| `js-coverage` `continue-on-error: true` (satır 167) | **soft** — bilinçli A7 kararı |
| `js-e2e` `continue-on-error: true` (satır 183) | **soft** — A6 kararı |
| `permissions: contents: read` (satır 14-15) | enaz yetki ilkesi |

## §12 Ö-11 — SECRET SCAN (gitleaks)

~~~yaml
# .github/workflows/secret-scan.yml · satır 24-31
- uses: actions/checkout@v4
  with:
    fetch-depth: 0   # github-actions-template.md §10: "gitleaks: no commits found" -> fetch-depth: 0

- name: Run GitLeaks
  uses: gitleaks/gitleaks-action@v2
  env:
    GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
~~~

| Not | |
|---|---|
| `fetch-depth: 0` | geçmişin tamamı taranır (satır 26) |
| `.gitleaks.toml` YOK | "yalnizca security-scanning.md dokuman ornegi -> varsayilan gitleaks config" (satır 4) |
| Tetikleyici | `on: push` + `pull_request` (satır 7-9) |

## §13 ANTI-PATTERN TARAMASI (BU ÇOCUK)

| Anti-pattern | Sonuç | Kanıt |
|---|---|---|
| Assert'siz test | 0 | tüm örneklerde assertion var (§2-§9) |
| Sadece happy-path | 0 | 403/429/fail-closed negatif vakalar mevcut |
| Fail-open güvenlik kodu | 1 **tartışmalı** | `RateLimiterMiddlewareTest:105-128` (cache yoksa geçiş) → ⚠️ §4 notu |
| Uydurma satır numarası | 0 | tüm alıntılar diskten okundu |
| Kopyalanmış mock aşırılığı | 0 | tek helper `createCacheMock` (`RateLimiterMiddlewareTest:305-318`) |
| SecurityHeaders ayrı testi | **YOK** | yalnız `MiddlewarePipelineTest` içinde → ⚠️ |
| DAST örneği | **YOK** | ağdan istek atan test yok → ⚠️ |

## §14 ALINTI ÖZETİ

| Metrik | Değer |
|---|---|
| Toplam alıntı | 11 (≥4 şart ✅) |
| Test dosyası alıntısı | 6 |
| Config/workflow alıntısı | 3 (composer ×2 blok, ci.yml ×3 blok, secret-scan ×1 blok) |
| Uydurma snippet | 0 |
| Satır numarası doğrulanmamış iddia | 0 |

## §15 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[security-testing/security-testing-anayasa]] | üst | kapı kuralı |
| [[security-testing/security-testing-surucu]] | üst | hangi kapıda çalışır |
| [[security-testing/security-testing-dogrulama]] | kardeş | bu alıntılar AD-xx satırlarına eşleşir |
| [[security-test-stratejisi]] | kaynak | T-01..T-20 vakaları |
| [[security-kod-ornekleri]] | kardeş | K009 kök kod örnekleri (bu dosya test örneklerini taşır) |
| [[authentication/authentication-ornekler]] | kardeş | auth üretim kodu alıntıları |
| [[secrets/secrets-ornekler]] | kardeş | secret/redaction alıntıları |
| `K019-testing` | dışı | genel test kapsamı |

## §16 REVİZYON

| Tarih | Değişiklik |
|---|---|
| 2026-10-15 | İlk yazım — 11 alıntı, diskten okuma, uydurma 0 |
| 2026-10-15 | `git commit` ATILMADI (yasak) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
