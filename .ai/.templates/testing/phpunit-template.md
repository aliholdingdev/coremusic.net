---
title: "PHPUnit Test Template — Backend Test Şablonu"
type: template
category: testing
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: reference
---

# PHPUnit Test Template — Backend Test Şablonu

**Zorunlu Bağlantılar:** [[../../index]] · [[../../brain]] · [[../../.templates/index]] · [[shared/tests/index]]

---

## §1 Amaç ve Disk Kanıtı

Şablon, `shared/tests/` altındaki **22 gerçek test dosyası** ile `shared/phpunit.xml` (5 suite) yapısına göre yazılır.

| Kanıt | Değer |
|---|---|
| Test dizini | `shared/tests/` — 22 dosya (AuthApi, CrudApi, CSRF, EventBus, Container, Router, Security, Cookie, RateLimiter, Validation…) |
| Config | `shared/phpunit.xml` |
| Suite'ler | `Unit`, `Api`, `Events`, `Security`, `Middleware` |
| Auth config | `auth.coremusic.net/phpunit.xml` |
| Kod kalitesi | infection (mutation) + infection.json5 |
| Run | `vendor/bin/phpunit` (proje kökünde) |

```bash
# Tüm suite
vendor/bin/phpunit -c shared/phpunit.xml
# Tek dosya
vendor/bin/phpunit -c shared/phpunit.xml shared/tests/SecurityTest.php
```

---

## §2 Frontmatter / Değişkenler

Test dosyaları sınıf tabanlıdır; şablon değişkenleri:

| Değişken | Açıklama | Örnek |
|---|---|---|
| `{{CLASS_NAME}}` | `{Test}` sonekli sınıf | `RateLimiterTest` |
| `{{SUBJECT}}` | Test edilen birim | `RateLimiter` |
| `{{METHOD_NAME}}` | test metodu `{verilen}_{beklenen}` | `testValidToken_returnsTrue` |

---

## §3 Dosya İskeleti (gerçek imzalarla)

```php
<?php
declare(strict_types=1);

namespace Tests\Unit;              // suite'e göre Unit|Api|Events|Security|Middleware

use PHPUnit\Framework\TestCase;
use CoreMusic\{{SUBJECT}};

final class {{CLASS_NAME}} extends TestCase
{
    private {{SUBJECT}} $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new {{SUBJECT}}();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider provideCases
     */
    public function test_{{behavior}}_{{expected}}(): void
    {
        // Arrange
        $input = '...';

        // Act
        $result = $this->subject->do($input);

        // Assert
        $this->assertSame($expected, $result);
    }

    public static function provideCases(): array
    {
        return [
            'normal'  => ['input', 'expected'],
            'edge'    => ['', 'fallback'],
        ];
    }
}
```

---

## §4 Assertion Kataloğu

| Assertion | Kullanım | Örnek |
|---|---|---|
| `assertSame` | tip+sütun eşitliği (katı) | `assertSame(200, $code)` |
| `assertEquals` | sayısal/gevşek | `assertEquals(10, $count)` |
| `assertTrue/False` | bool | `assertTrue($ok)` |
| `assertNull/NotNull` | null kontrolü | `assertNull($row)` |
| `assertInstanceOf` | tip | `assertInstanceOf(Router::class, $r)` |
| `assertCount` | koleksiyon | `assertCount(3, $routes)` |
| `assertArrayHasKey` | dizi anahtarı | `assertArrayHasKey('id', $row)` |
| `assertStringContainsString` | gövde | `assertStringContainsString('oauth', $html)` |
| `expectException` | hata yolu | `expectException(InvalidArgumentException::class)` |

| Şablon | Metin |
|---|---|
| `{bug-github}-475` | Regression: koş `assertSame` ile eski davranış kilidi |

---

## §5 Auth API Test Deseni (kanıt: AuthApiTest/CrudApiTest)

```php
public function testCreateUser_returns201_withValidToken(): void
{
    $payload = json_encode(['email' => 'a@b.com', 'password' => 's3cret'], JSON_THROW_ON_ERROR);

    $response = $this->postJson('/api/users', $payload, [
        'Authorization: Bearer ' . self::TOKEN,
        'Content-Type: application/json',
    ]);

    $this->assertSame(201, $response['status']);
    $this->assertArrayHasKey('id', $response['body']);
}

public function testCreateUser_returns401_withoutToken(): void
{
    $this->expectException(AuthException::class);   // veya status assert
}
```

| API test kuralı | Değer |
|---|---|
| Body | `json_encode(..., JSON_THROW_ON_ERROR)` |
| Header | `Content-Type: application/json` |
| Auth | `Authorization: Bearer` (CSRF testleri ayrı suite) |
| Assert hedefi | HTTP status + response body anahtarları |

---

## §6 Event/Security/Middleware Suite Kuralları

| Suite | Ne test edilir | Örnek dosya |
|---|---|---|
| `Events` | EventBus publish/subscribe, dinleyici sırası | `EventBusTest.php` |
| `Security` | token doğrulama, imza, XSS temizleme | `SecurityTest.php` |
| `Middleware` | CSRF, RateLimiter, Cookie | `CSRFTest.php`, `RateLimiterTest.php`, `CookieTest.php` |
| `Unit` | saf sınıf (Container, Router, Validation) | `ContainerTest.php` |
| `Api` | uçtan uca HTTP davranışı | `AuthApiTest.php`, `CrudApiTest.php` |

```bash
# Suite'e göre filtre
vendor/bin/phpunit -c shared/phpunit.xml --testsuite Security
```

---

## §7 Doğrulama & Hata Masası

| # | Adım | Beklenen |
|---|---|---|
| 1 | `vendor/bin/phpunit -c shared/phpunit.xml` | 0 failure |
| 2 | Yeni test suite adıyla eşleşiyor mu | adımsız `Tests\{Suite}` namespace |
| 3 | Mutation | `infection` (infection.json5) — mutation skoru düşüşü yok |
| 4 | Bağımlılık | gerçek DB/network yerine sahte (Unit suite saf kalmalı) |

| Hata | Neden | Çözüm |
|---|---|---|
| `Class not found` | autoload/test namespace | composer `autoload-dev` `Tests\` kontrol |
| Suite'te görünmüyor | yanlış namespace/klasör | `phpunit.xml` testsuite yolu |
| `risky test` | assertion yok | en az 1 assert ekle |
| DB bağlantı hatası | Unit suite'te gerçek DB | Unit saf; Api suite'te sahte sunucu |

---

## §8 Gerçek Test Dosyası Galerisi (22 dosya — ızgara)

| # | Dosya | Suite | Ne doğrular |
|---|---|---|---|
| 1 | `AuthApiTest.php` | Api | login/register/token uçları, 201/401 |
| 2 | `CrudApiTest.php` | Api | create/read/update/delete durum kodları |
| 3 | `CSRFTest.php` | Middleware | token yokken 403, yanlış token reddi |
| 4 | `RateLimiterTest.php` | Middleware | limit aşımında 429, pencere sıfırlama |
| 5 | `CookieTest.php` | Middleware | HttpOnly/SameSite bayrakları |
| 6 | `EventBusTest.php` | Events | publish→subscribe sırası, unsubscribe |
| 7 | `ContainerTest.php` | Unit | singleton/transient çözümleme, döngü hatası |
| 8 | `RouterTest.php` | Unit | route eşleşme, method 405, param yakalama |
| 9 | `SecurityTest.php` | Security | token imza, XSS temizleme, hash doğrulama |
| 10-22 | Validation, DB, Helper… | ilgili suite | `ls shared/tests/` |

```bash
# tek suite koşusu
vendor/bin/phpunit -c shared/phpunit.xml --testsuite Events
```

### §8.1 EventBus Test Örneği (Events suite)

```php
public function testPublish_invokesSubscriberInOrder(): void
{
    $bus = new EventBus();
    $seen = [];
    $bus->on('route:change', function ($p) use (&$seen) { $seen[] = 'a'; });
    $bus->on('route:change', function ($p) use (&$seen) { $seen[] = 'b'; });

    $bus->publish('route:change', ['path' => '/x']);

    $this->assertSame(['a', 'b'], $seen);
}

public function testUnsubscribe_stopsDelivery(): void
{
    $bus = new EventBus();
    $spy = $this->createMock(\stdClass::class);   // veya vi.fn benzeri callable sarmalayıcı
    $id = $bus->on('e', function () {});
    $bus->off($id);
    $bus->publish('e', []);
    $this->addToAssertionCount(1);               // çağrı olmadı
}
```

### §8.2 CSRF / RateLimiter Test Deseni (Middleware)

```php
public function testMissingToken_returns403(): void
{
    $mw = new CsrfMiddleware();
    $result = $mw->handle(new Request(headers: []));
    $this->assertSame(403, $result->status);
}

public function testBurstOverLimit_returns429(): void
{
    $rl = new RateLimiter(limit: 3, windowSeconds: 60);
    for ($i = 0; $i < 3; $i++) { $this->assertTrue($rl->allow('ip:1.2.3.4')); }
    $this->assertFalse($rl->allow('ip:1.2.3.4'));   // 4. istek reddi
}
```

### §8.3 Validation / Router (Unit)

```php
public function testRouteParams_areExtracted(): void
{
    $router = new Router();
    $router->get('/user/{id}', fn() => 'ok');
    $match = $router->match('GET', '/user/42');
    $this->assertTrue($match->found);
    $this->assertSame('42', $match->params['id']);
}

public function testInvalidEmail_failsRule(): void
{
    $v = Validator::make(['email' => 'not-an-email'], ['email' => 'required|email']);
    $this->assertTrue($v->fails());
    $this->assertArrayHasKey('email', $v->errors());
}
```

---

## §9 Mock / Double Kataloğu

| Teknik | Kullanım | Örnek |
|---|---|---|
| `createMock(Class::class)` | tam sahte | `MailerInterface` |
| `method('x')->willReturn()` | dönüş değeri | API 201 sahtesi |
| `expects($this->once())` | çağrı sayısı | event bir kez yayın |
| `willReturnOnConsecutiveCalls` | sıra dönüşleri | 429 sonrası 200 |
| inline anonim sınıf | basit stub | repository |
| gerçek + saf (Unit) | mümkünse mock'siz | Container/Router |

```php
$mailer = $this->createMock(MailerInterface::class);
$mailer->expects($this->once())
       ->method('send')
       ->with($this->callback(fn($m) => str_contains($m->to, 'a@b.com')));
```

---

## §10 Mutation & Kalite (infection)

| Kavram | Aksiyon | Eşik |
|---|---|---|
| Mutation | `vendor/bin/infection` (`infection.json5`) | drift olmamalı |
| Riskli alan | assertion kırılganlığı | §4 assert katı |
| Coverage | clover `coverage.xml` | hedef ≥ %80 (AGENTS.md) |
| Test kodu | PHPStan benzeri denetim | kendi CI'ında |

```bash
vendor/bin/infection --threads=4 --only-covered --min-msi=80
```

---

## §11 Hata Masası (geniş)

| Hata | Neden | Çözüm |
|---|---|---|
| `failed asserting that ... identical` | `==` vs `===` beklentisi | `assertSame`/`assertEquals` eşleştir |
| `Test code or tested code did not (only) throw` | fazladan throw beklentisi | `expectException` doğru metoda |
| Suite boş (0 test) | wrong namespace/dir | `Tests\{Suite}` + phpunit.xml yolu |
| Sahte zaman sızması | timer mock | `tearDown` restore |
| Api suite ağ hatası | gerçek HTTP | yerel sahte sunucu / process izole |
| `risky` etiketi | assertion yok | min 1 assert + `addToAssertionCount` |
| infection bulguları | specsiz kalan dal | veri sağlayıcı ile dal kapat |

---

## §12 Test Verisi & İzolasyon

| Kural | Uygulama |
|---|---|
| Sıfır durum | `setUp` her testte yeni nesne |
| DB (Api suite) | transaction rollback / sahte repository |
| Saat | sabit timestamp (gerçek `now` yasak) |
| Rastgelelik | seed'li RNG, deterministik |
| Dosya | `sys_get_temp_dir()` + test sonu temizlik |
| Global | `$_SERVER/$_GET` yazma → `Request` sarmalayıcı |

```php
private const FROZEN_NOW = '2026-09-23 12:00:00';

protected function setUp(): void
{
    parent::setUp();
    $this->clock = new FrozenClock(self::FROZEN_NOW);
}
```

### §12.1 Veri sağlayıcı (data provider) deseni

```php
public static function tokenCases(): array
{
    return [
        'geçerli'        => [self::VALID, true],
        'süresi dolmuş'  => [self::EXPIRED, false],
        'bozuk imza'     => [self::TAMPERED, false],
        'boş'            => ['', false],
    ];
}

/** @test @dataProvider tokenCases */
public function testToken_validation(string $token, bool $expected): void
{
    $svc = new TokenService(new FrozenClock(self::FROZEN_NOW));
    $this->assertSame($expected, $svc->verify($token));
}
```

---

## §13 Performans & Bütçe

| Metrik | Hedef | Ölçüm |
|---|---|---|
| Suite süresi (tüm) | < 60 sn | phpunit `--log-junit` |
| Tek test | < 2 sn | risky timeout |
| Coverage (kod) | ≥ %80 | clover `coverage.xml` |
| Mutation MSI | ≥ %80 | infection |
| Sahte oranı | makul (oversmocking yok) | kod incelemesi |

```bash
vendor/bin/phpunit -c shared/phpunit.xml --log-junit build/junit.xml
# süre raporu için junit çıktısını yorumla
```

---

## §14 CI Köprüsü (bağlantı)

| Konu | Referans |
|---|---|
| `php-test` job komutu | [[../infrastructure/github-actions-template]] §3.2 |
| Coverage çıktısı CI | aynı dosya §8.4 (artefakt) |
| Migration guard | aynı dosya §8.3 |
| JS birimi ayrı katman | [[vitest-template]] |

```yaml
# CI içi çalıştırma (hedef — workflows YOK, onay akışı §4.1)
      - run: vendor/bin/phpunit -c shared/phpunit.xml --coverage-clover=coverage.xml
```

---

## §15 Öncelik Sırası (yazım sırası)

```
1. Regresyon testi (yaşanan hata)     → ilk yazılır, asla silinmez
2. Hata yolu (exception/4xx/5xx)
3. Kenar durum (boş, tek, çok büyük)
4. Mutlu yol (happy path)
5. Performans/limit (rate, timeout)
```

| Kural | Aksiyon |
|---|---|
| Her public API | en az 1 mutlu + 1 hata testi |
| Hata düzeltildi | o hata için test zorunlu (bug-*) |
| Silinen davranış | test de silinir (kodla birlikte) |
| Test adı | ne yazıyorsa o: `{konu}_{durum}_{beklenen}` |

---

## §16 Sık Yazılan Test Parçaları

### §16.1 Exception & status

```php
public function testExpiredToken_throwsAuthException(): void
{
    $clock = new FrozenClock('2026-09-23 12:00:00');
    $svc   = new TokenService($clock, ttlSeconds: 60);
    $token = $svc->issue('user-1');           // 12:00:00
    $clock->advance(61);                      // 12:01:01 → süresi doldu

    $this->expectException(AuthException::class);
    $this->expectExceptionMessage('token expired');
    $svc->verify($token);
}
```

### §16.2 Koleksiyon & sıralama

```php
public function testRouter_routesAreMatchedInRegistrationOrder(): void
{
    $router = new Router();
    $router->get('/a', fn() => 'first');
    $router->get('/a/b', fn() => 'second');

    $this->assertSame('first', $router->dispatch('GET', '/a'));
    $this->assertSame('second', $router->dispatch('GET', '/a/b'));
    $this->assertCount(2, $router->routes());
}
```

### §16.3 Zeit/Saat (frozen clock)

```php
public function testRateLimit_windowResetsAfterWindow(): void
{
    $clock = new FrozenClock('2026-09-23 12:00:00');
    $rl = new RateLimiter(limit: 2, windowSeconds: 60, clock: $clock);

    $this->assertTrue($rl->allow('k'));
    $this->assertTrue($rl->allow('k'));
    $this->assertFalse($rl->allow('k'));       // 3. reddi

    $clock->advance(61);
    $this->assertTrue($rl->allow('k'));        // pencere sıfır
}
```

### §16.4 String/Hash

```php
public function testHash_verifyMatches_andRoundsDiffer(): void
{
    $h1 = password_hash('secret', PASSWORD_BCRYPT);
    $this->assertTrue(password_verify('secret', $h1));
    $this->assertFalse(password_verify('wrong', $h1));
    $this->assertNotSame($h1, password_hash('secret', PASSWORD_BCRYPT)); // tuz farkı
}
```

---

## §17 Test İsimlendirme Sözlüğü

| Türkçe kalıp | İngilizce kalıp | Örnek |
|---|---|---|
| `{konu}_gecerliDoner{Beklenen}` | `valid_returns{Expected}` | `token_gecerliDonerTrue` |
| `{konu}_{durum}Atar{Kod}` | `{case}_throws{Exception}` | `token_sresiDolmusAtarAuth` |
| `{konu}_{sinirde}` | `{subject}_boundary{N}` | `limiter_sinirdeTamEsitlik` |
| regression etiketi | `testGitHub475_...` | hata numarası gövdede |

| Kural | Değer |
|---|---|
| `test` öneki veya `@test` | ikisi tutarlı kullanılacak |
| tek davranışı anlatır | “ve/and” yok (iki durum → iki test) |
| assert tek odak | test başına ana 1 assert + opsiyonel guard |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
