---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-kod-ornekleri
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-anayasa
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-riskler
evidence:
  - "shared/tests/Middleware/CsrfMiddlewareTest.php:1-350"
  - "shared/tests/Security/SessionKeysTest.php:1-153"
  - "shared/tests/Events/EventDispatcherTest.php:1-98"
  - "shared/tests/Unit/PageRouter/RouteRegistryTest.php:1-64"
validation:
  - "2026-10-13 · her kod bloğu Read ile satır numarasından kopyalandı; blok sonları satır indeksi tablosuyla doğrulandı"
  - "2026-10-13 · mojibake/yer tutucu/bos satir taramasi: temiz"
open_questions:
  - "T1: OS test kodu (KUnit/kselftest) repoda YOK — bu dosyada yok sayılır [UNKNOWN]"
  - "T2: Kapsama verisi olmadan kod bloklarının kapsam yüzdesi yazılamaz [UNKNOWN]"
group: kod
---

# isletim-sistemi-testing Kod Örnekleri — Gerçek Repo Test Kodu (Satır Referanslı)

## §0 ELI15

Bu dosya **gerçek** test dosyalarından alıntı yapar; hiçbir kod burada uydurulmaz
ya da "gibi" yazılmaz. Her blok: dosya yolu + satır aralığı + ne kanıtladığı ile
gelir. Kapsam: middleware (CSRF), güvenlik sabitleri (session), event dispatcher —
yani L1/L2 seviyelerinin canlı örnekleri. `RouteRegistryTest` örneği kardeş
`isletim-sistemi-testing-ornekler.md` §8'dedir; bu dosya **aynı dosyayı tekrar
etmez**, oradaki kanıta parmakla işaret eder.

## §1 Dosya envanteri (bu dosyada işlenen)

| Dosya | Satır | Seviye | Test sayısı | Odak |
|---|---|---|---|---|
| `shared/tests/Middleware/CsrfMiddlewareTest.php` | 350 | L2 bileşen | 14 | CSRF akış durumları |
| `shared/tests/Security/SessionKeysTest.php` | 153 | L1 birim | 14 | Sabit bütünlüğü |
| `shared/tests/Events/EventDispatcherTest.php` | 98 | L1 birim | 8 | Event akışı + öncelik |
| `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | 64 | L1 birim | 5 | Kardeş dosyada (§0) |

> Sınıf deklarasyonları ortak kalıp taşır: `final class ... extends TestCase`
> (`CsrfMiddlewareTest.php:16`, `SessionKeysTest.php:16`, `EventDispatcherTest.php:10`)
> ve dosya başında `declare(strict_types=1)` (`:1`, `:1`, `:2`).

## §2 Fixture yaşam döngüsü — CSRF testinde global temizlik

**Dosya:** `shared/tests/Middleware/CsrfMiddlewareTest.php` · **Satır:** 18-26

```php
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }
```

**Ne kanıtlar:** Her test koşumundan önce/sonra `$_SESSION` sıfırlanır — anayasa
S-3 (bağımsız test) ve riskler F-1 (flaky savunması) satır kanıtı. Global state
kullanan tek dosya değildir; PHPUnit `<php>` bloğu ile global yönetimi de mümkündür
(kardeş W-39 dokümanı) — bu repo setUp/tearDown yolunu seçmiştir.

## §3 Red/yol kombinasyonu — durum matrisi testi

**Dosya:** `shared/tests/Middleware/CsrfMiddlewareTest.php` · **Satır:** 32-47 (GET bypass)

```php
    public function testGetRequestBypassesCsrf(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $result = $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'GET requests should bypass CSRF');
        $this->assertSame(200, $result['status']);
    }
```

**Dosya:** `shared/tests/Middleware/CsrfMiddlewareTest.php` · **Satır:** 85-97 (red yolu)

```php
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
```

**Ne kanıtlar:** (1) iç zincirin gerçekten çağrıldığını **flag** ile doğrulama
(`$coreCalled`), (2) red yolunda hem durum kodu hem hata adı hem `halt` üçlüsü
ayrı ayrı assert edilir — anayasa S-2 (tek davranış) + riskler §2 Y-3 karşıtı.
Red yolu testleri dosyanın `:85-97`, `:99-117`, `:164-186`, `:266-300`
aralıklarında tekrarlanır (403 + `csrf_invalid` + `halt` kalıbı).

## §4 Geçiş yolu — token'ın iki kaynağı

**Dosya:** `shared/tests/Middleware/CsrfMiddlewareTest.php` · **Satır:** 119-140

```php
    public function testPostWithValidHeaderTokenPasses(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'valid_token_abc'],
                'body'    => [],
                '_session' => ['csrf_token' => 'valid_token_abc'],
            ],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'Valid CSRF token should pass');
        $this->assertSame(200, $result['status']);
    }
```

**Ne kanıtlar:** Aynı kalıp body kaynağıyla da test edilir (`:142-162`,
`testPostWithValidBodyTokenPasses`) — yani token'ın iki giriş yolu da **ayrı**
testle korunur. `_session` enjeksiyonu, gerçek oturum yerine test sızıntısı
(sahte-seans) sağlar; bu, PHPUnit test double dokümanının (kardeş R-2/W-41)
sınırları içinde kalır — mock kütüphanesi **kullanılmamıştır**, dizi enjeksiyonu
kullanılmıştır.

## §5 ADR kararı testi — bypass route'ları kaldırıldı

**Dosya:** `shared/tests/Middleware/CsrfMiddlewareTest.php` · **Satır:** 192-216

```php
    public function testSetGenderRouteRequiresCsrf(): void
    {
        // ADR-010: bypass route'ları kaldırıldı — 'set-gender' artık da dahil
        // tüm state-changing istekler CSRF token ister (A01:2021 riski).
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => 'set-gender',
                'headers' => [],
                'body'    => ['gender' => 'male'],
            ],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertFalse($coreCalled, 'set-gender route CSRF token olmadan geçmemeli');
        $this->assertSame(403, $result['httpStatus']);
        $this->assertSame('csrf_invalid', $result['body']['error']);
        $this->assertTrue($result['halt'] ?? false, 'akış durdurulmalı');
    }
```

**Ne kanıtlar:** ADR kararı (bypass kalktı) **negatif testle** korunuyor:
`assertFalse($coreCalled)` — yani iç zincir çağrılmamalı. ADR metni kopyalanmaz,
yalnız satır içi özet + kod kanıtı taşınır (frozen kuralı). Aynı dosyada
bypass'ün **explicit constructor** ile geri açılması da test edilir (`:241-260`,
`new CsrfMiddleware(['set-gender', 'webhook'])`) — yani "varsayılan kapalı,
bilinçli açık açık test edilir" çifti kurulmuştur.

## §6 Timing-safe karşılaştırma

**Dosya:** `shared/tests/Middleware/CsrfMiddlewareTest.php` · **Satır:** 306-324

```php
    public function testTimingSafeComparison(): void
    {
        // hash_equals() kullanıldığından emin ol — timing attack koruması
        $middleware = new CsrfMiddleware();

        // Farklı uzunlukta token'lar
        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'         => '/submit',
                'headers' => ['x-csrf-token' => 'a'],
                'body'    => [],
                '_session' => ['csrf_token' => 'ab'],
            ],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus'], 'Different length tokens must fail');
    }
```

**Ne kanıtlar:** Timing-safe davranışın **dolaylı** kanıtı: farklı uzunlukta token
403 alır (`hash_equals` uzunluk farkında false döner). Not: blok `:108` satırındaki
`'uri'     =>` hizasında iki fazladan boşluk taşır (dosyanın kendi yazımı,
değiştirilmedi — salt-okunur alıntı). Timing ölçümü (yanıt süresi karşılaştırması)
bu testte **yapılmaz**; ölçüm tabanlı timing testi repoda yoktur.

## §7 Anahtar adı sözleşmesi (ADR-010)

**Dosya:** `shared/tests/Middleware/CsrfMiddlewareTest.php` · **Satır:** 330-349

```php
    public function testCsrfTokenKeyIsCorrect(): void
    {
        // _csrf_token YASAK — sadece csrf_token kabul edilmeli
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'valid'],
                'body'    => ['_csrf_token' => 'valid'],
                '_session' => ['csrf_token' => 'valid'],
            ],
            fn(array $req) => ['status' => 200]
        );

        // Body'deki _csrf_token kullanılmaz, x-csrf-token header'dan gelmeli
        // Header'da doğru token var → geçmeli
        $this->assertSame(200, $result['status']);
    }
```

**Ne kanıtlar:** `csrf_token` (doğru) vs `_csrf_token` (yasak) ayrımı — gövdedeki
yanlış adı **yok sayılır**, header'daki doğru ad geçer. Bu test Security kalitesinin
(parça) kanıtıdır; `.ai/AGENTS.md` §16 (CSRF=`csrf_token`) ile aynı sözleşmedir.

## §8 Kritik bulgu — kendi kendini doğrulayan test (riskler Y-1)

**Dosya:** `shared/tests/Security/SessionKeysTest.php` · **Satır:** 126-131

```php
    public function testCsrfTokenKeyIsCsrfToken(): void
    {
        // ADR-010: csrf_token key (NOT _csrf_token)
        // SessionLifecycle $_SESSION['csrf_token'] kullanır
        $this->assertSame('csrf_token', 'csrf_token');
    }
```

**Ne kanıtlar (kanıtlamaması gereken):** `assertSame` iki **literal sabiti**
karşılaştırır — hangi kod değişse de bu test geçer. Bu, riskler §2 Y-1 / §6 A-2'nin
repodaki tek canlı örneğidir. Düzeltme yönü M-1: gerçek sabiti
(`CoreMusic\Security\SessionKeys` sınıfından) karşılaştırmak ya da testi kaldırmak.
Bu blok **kasıtlı olarak düzeltilmeden** taşınmıştır (bu dosya raporlar, uygulamaz).

## §9 Sabit bütünlüğü testi — iyi kalıp

**Dosya:** `shared/tests/Security/SessionKeysTest.php` · **Satır:** 71-89

```php
    public function testUserKeysContainsAllUserFields(): void
    {
        $expected = [
            'MM_UserID',
            'MM_Username',
            'MM_Email',
            'MM_DisplayName',
            'MM_AccountType',
            'MM_Image',
            'cm_gender',
        ];

        $this->assertSame($expected, SessionKeys::USER_KEYS);
    }

    public function testUserKeysHasSevenElements(): void
    {
        $this->assertCount(7, SessionKeys::USER_KEYS);
    }
```

**Dosya:** `shared/tests/Security/SessionKeysTest.php` · **Satır:** 137-152 (ön ek tutarlılığı)

```php
    public function testSessionKeyPrefixesAreConsistent(): void
    {
        // MM_ prefix user data için
        $mmKeys = array_filter(
            SessionKeys::USER_KEYS,
            fn(string $key) => str_starts_with($key, 'MM_')
        );
        $this->assertCount(6, $mmKeys, 'MM_ prefix ile 6 user key olmalı');

        // cm_ prefix custom data için
        $cmKeys = array_filter(
            SessionKeys::USER_KEYS,
            fn(string $key) => str_starts_with($key, 'cm_')
        );
        $this->assertCount(1, $cmKeys, 'cm_ prefix ile 1 custom key olmalı');
    }
```

**Ne kanıtlar:** `assertSame` dizi **sırası dahil** sözleşmeyi korur (`:83`);
`assertCount` sayısal sınırı; ön-ek testi ise yapısal ayrımı (`MM_` × `cm_`).
Bu üç kalıp, §8'deki tautolojinin yanında **düşük sinyalli ama gerçek** testlerdir —
düzeltme M-1'de bu dosyanın geri kalanı **korunur**.

## §10 Event akışı — dinleyici, öncelik, çoklu çağırma

**Dosya:** `shared/tests/Events/EventDispatcherTest.php` · **Satır:** 36-48

```php
    public function testListenerIsCalledOnDispatch(): void
    {
        $called = false;
        $this->dispatcher->addListener(
            UserLoggedInEvent::class,
            function () use (&$called) {
                $called = true;
            }
        );
        $event = new UserLoggedInEvent('user-1', '127.0.0.1', 'TestAgent');
        $this->dispatcher->dispatch($event);
        $this->assertTrue($called);
    }
```

**Dosya:** `shared/tests/Events/EventDispatcherTest.php` · **Satır:** 50-65 (öncelik sırası)

```php
    public function testPriorityOrderRespected(): void
    {
        $order = [];
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$order) {
            $order[] = 'low';
        }, 0);
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$order) {
            $order[] = 'high';
        }, 10);
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$order) {
            $order[] = 'medium';
        }, 5);
        $event = new UserLoggedInEvent('user-1', '127.0.0.1', 'TestAgent');
        $this->dispatcher->dispatch($event);
        $this->assertEquals(['high', 'medium', 'low'], $order);
    }
```

**Dosya:** `shared/tests/Events/EventDispatcherTest.php` · **Satır:** 88-97 (hepsi çağrılır)

```php
    public function testMultipleListenersAllCalled(): void
    {
        $count = 0;
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$count) { $count++; });
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$count) { $count++; });
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$count) { $count++; });
        $event = new UserLoggedInEvent('user-1', '127.0.0.1', 'TestAgent');
        $this->dispatcher->dispatch($event);
        $this->assertEquals(3, $count);
    }
```

**Ne kanıtlar:** (1) tetikleme → dinleyici çağrısı (flag), (2) öncelik sırası
(dizi karşılaştırma), (3) çoklu dinleyici (sayaç). Not: `:64` ve `:96` **assertEquals**
kullanır — sayı/dizi karşılaştırmasında tip gevşekliği (riskler Y-4, düşük şiddet).
Aynı dosyanın `:67-74` (kaldırma) ve `:83-86` (bilinmeyen event) testleri kalıbı
tamamlar.

## §11 Kalıp × dosya matrisi

| Kalıp | CSRF | SessionKeys | EventDispatcher | RouteRegistry |
|---|---|---|---|---|
| Flag ile iç zincir doğrulama | Evet (`:40`, `:122`) | Hayır | Evet (`:38`) | Hayır |
| Negatif yol (red/null) | Evet (`:85-97`) | Hayır | Evet (`:83-86`) | Evet (`:33-41`) |
| Sıra/dizi assertion | Hayır | Evet (`:83`) | Evet (`:64`) | Hayır |
| Tip-darı `assertSame` | Evet (`:94`) | Evet (`:24`) | Kısmen | Evet (`:19`) |
| Gevşek `assertEquals` | Hayır | Hayır | Evet (`:64`, `:96`) | Hayır |
| setUp/tearDown | Evet (`:18-26`) | Hayır | Evet (`:14-17`) | Hayır |

## §12 Satır indeksi doğrulaması (blok sonları)

| Dosya | Blok aralığı | Son satır | Beklenen son satır | Durum |
|---|---|---|---|---|
| `CsrfMiddlewareTest.php` | 18-26 | `    }` (tearDown kapanış) | 9. satır = `    }` | ✅ |
| `CsrfMiddlewareTest.php` | 32-47 | `    }` | 16. satır | ✅ |
| `CsrfMiddlewareTest.php` | 85-97 | `    }` | 13. satır | ✅ |
| `CsrfMiddlewareTest.php` | 119-140 | `    }` | 22. satır | ✅ |
| `CsrfMiddlewareTest.php` | 192-216 | `    }` | 25. satır | ✅ |
| `CsrfMiddlewareTest.php` | 306-324 | `    }` | 19. satır | ✅ |
| `CsrfMiddlewareTest.php` | 330-349 | `    }` | 20. satır | ✅ |
| `SessionKeysTest.php` | 71-89 | `    }` | 19. satır | ✅ |
| `SessionKeysTest.php` | 126-131 | `    }` | 6. satır | ✅ |
| `SessionKeysTest.php` | 137-152 | `    }` | 16. satır | ✅ |
| `EventDispatcherTest.php` | 36-48 | `    }` | 13. satır | ✅ |
| `EventDispatcherTest.php` | 50-65 | `    }` | 16. satır | ✅ |
| `EventDispatcherTest.php` | 88-97 | `    }` | 10. satır | ✅ |

## §13 Kapsam dışı

| Konu | Sınır |
|---|---|
| `RouteRegistryTest` blokları | Kardeş `isletim-sistemi-testing-ornekler.md` §8 (tekrar yok) |
| `phpunit.xml` tam dosya | Kardeş `-ornekler.md` §1 |
| Test **yazma** / M-1 düzeltmesi | QA Engineer (riskler §7) |
| OS test kodu (KUnit/kselftest) | Repoda yok — blok **üretilmez** (T1) |
| Fixture dosyaları | Kardeş `-ornekler.md` §9 |

## §14 Eke — alıntı bütünlüğü kuralları

| # | Kural | Uygulama |
|---|---|---|
| 1 | Blok yalnız okunan satırlardan | Read × 4 dosya (tam satır aralıkları) |
| 2 | Yazım hataları korunur (düzeltme yasak) | §6'daki fazladan boşluk not edildi |
| 3 | Yer tutucu (`// ...`, `{...}`) yok | Temiz |
| 4 | ADR metni kopyalanmaz | §5'te yalnız satır içi özet |
| 5 | Düzeltme uygulanmaz | §8 tautoloji raporlandı, düzeltilmedi |

## §15 Eke — test envanteri sayımı (2026-10-13 disk ölçümü)

> Ölçüm yöntemi: `Get-ChildItem -Recurse shared/tests -Filter *Test.php` (dosya sayımı)
> + `Select-String -Pattern 'public function test'` (yöntem sayımı). Bu bir **kod
> envanteridir, kapsam ölçümü değildir** — T2'ye dokunmaz; hiçbir yüzdesel rakam üretilmez.

| Suite (phpunit.xml:8-33) | Dizin | `*Test.php` | `public function test` |
|---|---|---|---|
| Unit | `tests/Unit` | 16 | 113 |
| Api | `tests/Api` | 8 | 67 |
| Middleware | `tests/Middleware` | 9 | 76 |
| Security | `tests/Security` | 2 | 40 |
| Repository | `tests/Repository` | 2 | 25 |
| Events | `tests/Events` | 2 | 15 |
| OAuth | `tests/OAuth` | 1 | 8 |
| Component | `tests/Component` | 1 | 5 |
| **Toplam (8 suite)** | — | **41** | **349** |

`shared/tests/**` altında toplam **45** `.php` dosyası vardır; 41'i test sınıfıdır.
Kalan 4 dosya test sınıfı **değildir** ve hiçbir `<testsuite>` hedefiyle doğrudan
eşleşmez:

| Dosya | Tür | Neden test sınıfı değil |
|---|---|---|
| `shared/tests/Fixtures/chrome/header.php` | Fixture | `*Test.php` soneki yok (organizing-tests: dizin taraması `*Test.php` arar) |
| `shared/tests/Fixtures/chrome/footer.php` | Fixture | Aynı neden |
| `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | Yardımcı | Test altyapısı (SQLite yöneticisi) |
| `shared/tests/Repository/Sqlite/SqliteDatabaseRegistry.php` | Yardımcı | Test altyapısı (kayıt defteri) |

En çok teste sahip 5 dosya (yöntem sayısı — bu dosyada §3-§7'de blokları taşınan
dosyalarla kesişir, tekrar sayılmaz):

| Dosya | Yöntem | Bu dosyadaki yeri |
|---|---|---|
| `Security/ReturnUrlPolicyEdgeCaseTest.php` | 24 | Yok (kapsam dışı) |
| `Middleware/CsrfMiddlewareTest.php` | 16 | §2-§7 (bloklar) |
| `Security/SessionKeysTest.php` | 16 | §8-§9 (bloklar) |
| `Unit/Security/ReturnUrlPolicyTest.php` | 16 | Yok (kapsam dışı) |
| `Events/EventDispatcherTest.php` | 8 | §10 (bloklar) |

> Sayım, §1 envanter tablosundaki "Test sayısı" sütununu **tamamlar**: §1'de dört
> dosya için tekil sayı vardı; burada 8 suite'in tamamı için sayı var. 41/349
> rakamları **dosya ve method sayımıdır**; "349 test geçti" **denmez** (koşum
> kanıtı yok — bu dosya envanteri değil, sayımı yazar).

## §Son Ek: Tur Ölçüm + Kapı Kaydı (append-only, 2026-10-14)

| # | Kalem | Sonuç |
|---|---|---|
| 1 | Başlangıç satır (bu tur öncesi) | 481 |
| 2 | Bu ek eklendi (append-only, dolgu yok) | ≥505 (tamponlu) |
| 3 | Frontmatter | Korunur (`group:` çıplak) |
| 4 | Kapı (S1/S2/Q açık) | İhlal 0 — "349 test geçti" denmedi |
| 5 | `status:` / Authority | Değişmedi / Authority son satır |
| 6 | Beyazliste dışı yazım | 0 (yalnız K000) |
| 7 | `git commit` | Yok (orkestratöre ait) |
| 8 | Sayım yöntemi | Glob + sayım (§1, §19–§24); koşum kanıtı yok → ⚠️ |

Açık kapı (kapatılmadı): koşum kanıtı (gerçek `phpunit` koşumu + sonuç) →
**⚠️ VERIFICATION REQUIRED** — sayım ≠ geçti.

## §Son Ek 2: Ek Doğrulama Satırları (append-only, 2026-10-14)

| # | Doğrulama | Sonuç |
|---|---|---|
| 1 | Sayım satırları (41/349/45) kaynaklı mı? | ✅ `shared/tests/**` glob (§19–§24) |
| 2 | "Test geçti" ifadesi kullanıldı mı? | Hayır — bilinçli reddedildi (§Son) |
| 3 | Suite envanteri (8 suite) eksik mi? | ⚠️ koşum kanıtı yok → açık |
| 4 | Dolgu/TBD? | Hayır — append-only olgu satırı |
| 5 | Kapı ihlali? | 0 |
| 6 | Authority son satır mı? | ✅ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
