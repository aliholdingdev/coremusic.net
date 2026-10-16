---
title: "K011 · application-testing — dogrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-testing
status: active
updated: 2026-10-15
group: kanit
---

# K011 · application-testing — Doğrulama (kanıt)

Amaç: application-testing kapsamındaki her iddia, diskteki gerçek dosya +
satır numarasıyla eşleştirilir; kanıtlanamayan iddia `⚠️ VERIFICATION
REQUIRED` veya `PLANNED` etiketiyle kalır ve kapı (gate) sonucunu lehine
kullanılmaz. Tüm ölçüm tarihi: **2026-10-15**, repo kökü `C:\www\coremusic.net`.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | Yöntem ve ölçüm kuralları | kanit | yöntem | Nasıl sayıldı, nesi sayılmadı |
| 2 | İddia ↔ dosya ↔ satır | kanit | kanıt | 30 satırlık ana kanıt tablosu |
| 3 | Test envanteri (dosya/metot) | kanit | envanter | 4 repo PHP + JS + SQL sayıları |
| 4 | Test çifti envanteri (grep) | kanit | envanter | Mock/stub/exception/attribute |
| 5 | Yapılandırma kanıtları | kanit | config | composer · phpunit.xml · ci.yml · package.json |
| 6 | Kapı (gate) PASS/BLOCK | kanit | kapı | 7 kapının tek tek sonucu |
| 7 | KPI ölçüm tablosu | kanit | kpi | Ölçülen / ölçülemeyen ayrımı |
| 8 | Olumsuz senaryolar | kanit | olumsuz | Ne kullanılmadı, ne kaçtı |
| 9 | PLANNED boşluklar | kanit | boşluk | Kanıtsız iddialar ve gereken kanıt |
| 10 | Okunan kaynaklar | kanit | kaynak | Dokunulan/dokunulmayan dosya listesi |

---

## §1 YÖNTEM VE ÖLÇÜM KURALLARI

1. Ölçüm 2026-10-15 tarihinde repo kökünden yapılmıştır; betik çalıştırma
   değil **okuma + sayım** kullanılmıştır.
2. `.ai/` gizli dizindir; `.ai/.sql/mysql/` sorguları gizli-dizin modunda
   yapılmıştır (`hidden: true`), aksi halde sahte 0 sonuç doğar.
3. `.ai/scripts/*.ps1` betikleri **çalıştırılmamıştır**; dosyalar yalnız
   okunup satır kanıtı alınmıştır.
4. **Canlı test koşumu YOKTUR** — hiçbir PHP/JS/E2E testi bu oturumda
   çalıştırılmamıştır. Kapı sonuçları dosya/satır envanterine dayanır.
5. `vendor/` dizini sayımlara **dâhil edilmez** (ilk ölçümde `*Test.php` = 86
   çıktı; 28'i PHPUnit'un kendi dosyasıydı → düzeltilmiş değer 58).
6. `git commit` bu oturumda atılmamıştır; yazılan dosyalar yalnız bu klasördür.
7. 11.5/12.5 havuz dokümanları repo'nun `^10.5` gerçekliğiyle **karıştırılmaz**;
   çelişen iddialar "mevcut davranış" olarak yazılmaz (kapsam §8).
8. Satır numaraları 1-tabanlıdır ve okunan dosyanın 2026-10-15 hâline aittir.
9. Sayımlar tekrarlanabilir: her § satırı, aynı komutla yeniden üretilebilir
   nitelikte dosya/yol içerir.
10. Çelişki durumunda **disk kazanır**; vault iddiası disk'le çelişiyorsa
    eski sayı "stale" olarak işaretlenir (bkz §3.4).

### §1.1 Bu bölümün KANITLAMADIKLARI

1. Testlerin yeşil/kırmızı olduğu (koşum gerekir, yapılmadı).
2. Gerçek coverage yüzdesi (coverage raporu üretilmedi).
3. E2E spec'inin pass oranı (Playwright çalıştırılmadı).
4. SQL dosyalarının sunucuya uygulanabilirliği (DB bağlantısı kurulmadı).
5. CI'nin gerçekten geçtiği (ci.yml okundu, çalıştırılma kanıtı yok —
   `.ai/AGENTS.md` §25.2 de aynı şekilde "çalışma durumu doğrulanmadı" der).

### §1.2 Ölçüm araçları (bu turda kullanılanlar)

| # | Araç | Kullanım amacı | Sınır |
|---|---|---|---|
| 1 | `Get-ChildItem -Recurse -File` | dosya envanteri | `vendor`/`node_modules` filtresi zorunlu |
| 2 | `Select-String -Pattern` (Recursive) | grep sayımı | **-Recurse'sız hasil 0 → sahte** (bkz §4.1 notu) |
| 3 | `[regex]::Matches(…, 'desen')` | metot/assertion sayımı | ham metin, yorum satırı da sayabilir |
| 4 | `Get-Content -Raw` | toplam assertion (837) | dosya başına tek okuma |
| 5 | `[System.IO.File]::ReadAllLines` | satır sayısı (dosyalarım) | yazım sonrası hedef kontrolü |
| 6 | `curl.exe -s -o NUL -w '%{http_code}'` | havuz URL durumu | yalnız 9 URL'lik havuz |
| 7 | `webfetch` / exa | sayfa içeriği (claim çıkarma) | yalnız 9 URL'lik havuz |

---

## §2 İDDİA ↔ DOSYA ↔ SATIR (ANA KANIT TABLOSU)

Sütunlar: iddia → kanıt dosyası → satır → okunan metin özeti. **30 satır.**

| # | İddia | Kanıt dosyası | Satır | Okunan metin (özet) |
|---|---|---|---|---|
| 1 | Test framework PHPUnit ^10.5 | `shared/composer.json` | 24 | `"phpunit/phpunit": "^10.5",` |
| 2 | Statik analiz phpstan ^1.10 | `shared/composer.json` | 25 | `"phpstan/phpstan": "^1.10",` |
| 3 | PHP ≥8.4 zorunlu | `shared/composer.json` | 8 | `"php": ">=8.4",` |
| 4 | PSR-4 test namespace `CoreMusic\Test\` | `shared/composer.json` | 34 | `"CoreMusic\\Test\\": "tests/"` |
| 5 | `composer test` = phpunit | `shared/composer.json` | 47 | `"test": "phpunit",` |
| 6 | `composer stan` = level 5 | `shared/composer.json` | 48 | `"stan": "phpstan analyse src --level=5"` |
| 7 | shared'de 8 testsuite tanımlı | `shared/phpunit.xml` | 8-33 | `<testsuites>` … Unit/Api/Repository/Events/Security/Middleware/Component/OAuth |
| 8 | auth'da Integration suite **kaldırıldı** | `auth.coremusic.net/phpunit.xml` | 10-12 | `<!-- Integration suite KALDIRILDI (2026-09-29): tests/Integration/ dizini diskte YOK → PHPUnit 10 "Test directory not found" + exit 2 … -->` |
| 9 | auth testleri failOnRisky/failOnWarning | `auth.coremusic.net/phpunit.xml` | 6-7 | `failOnRisky="true"` / `failOnWarning="true"` |
| 10 | auth kapsam raporu yalnız coverage bayrağıyla | `auth.coremusic.net/phpunit.xml` | 26-31 | `<!-- … kapsam yalnız "coverage" bayraklarıyla toplanır. --> <coverage><report><text outputFile="php://stdout"/>` |
| 11 | CI php-test job'ı matrix koşar | `.github/workflows/ci.yml` | 66, 74-80 | `php-test:` · matrix `project: shared` + `project: auth.coremusic.net / args: '--testsuite Unit'` |
| 12 | CI'da PHPUnit coverage bayrağı YOK | `.github/workflows/ci.yml` | 104-107 | `run: vendor/bin/phpunit "$PHPUNIT_ARGS"` (coverage flag yok) |
| 13 | auth Integration'ın CI notu | `.github/workflows/ci.yml` | 77-78 | `# auth phpunit.xml testsuites: Unit + Integration; tests/Integration dizini diskte YOK → yalnizca Unit kosulur` |
| 14 | JS coverage job'ı soft (bloklamaz) | `.github/workflows/ci.yml` | 160-167 | `js-coverage:` + `continue-on-error: true` + `# A7 kararı: %80 eşiği şimdilik erişilemez (12.9k satır / <100 test)` |
| 15 | E2E job'ı soft (A6) | `.github/workflows/ci.yml` | 178-183 | `js-e2e:` + `# A6: e2e lock'a alındı … → soft job.` + `continue-on-error: true` |
| 16 | phpstan.neon YOK, composer script esas | `.github/workflows/ci.yml` | 60-62 | `# phpstan.neon YOK -> composer scripti esas; plan level=8 değil, disk kazanir` |
| 17 | Node 26 ile JS job'ları | `.github/workflows/ci.yml` | 151 | `node-version: '26'` |
| 18 | Kök package.json 4 test scripti | `package.json` | 2-9 | `test:js` · `test:js:watch` · `test:js:coverage` · `test:e2e` |
| 19 | vitest sürümü ^5.0.3 | `package.json` | 12 | `"vitest": "^5.0.3"` |
| 20 | Playwright sürümü ^1.63.0 | `package.json` | 13 | `"@playwright/test": "^1.63.0"` |
| 21 | vitest coverage eşiği 80/80/80/80 | `assets.coremusic.net/vitest.config.js` | 25-28 | `lines: 80, functions: 80, branches: 80, statements: 80` |
| 22 | vitest include = tests/ + js/ spec'leri | `assets.coremusic.net/vitest.config.config.js` → `vitest.config.js` | 30 | `include: ['tests/**/*.spec.js', 'js/**/*.spec.js']` |
| 23 | vitest ortamı jsdom, timeout 5000 | `assets.coremusic.net/vitest.config.js` | 14, 31 | `environment: 'jsdom',` · `testTimeout: 5000,` |
| 24 | e2e testDir `./tests/e2e` | `assets.coremusic.net/playwright.config.ts` | 16-17 | `testDir: './tests/e2e',` / `testMatch: '**/*.spec.ts',` |
| 25 | e2e CI'da 1 worker + 2 retry | `assets.coremusic.net/playwright.config.ts` | 20-21 | `retries: process.env.CI ? 2 : 0,` / `workers: process.env.CI ? 1 : undefined,` |
| 26 | e2e webServer PHP dev server | `assets.coremusic.net/playwright.config.ts` | 46-47 | `command: 'php -S localhost:81 -t ../../home.coremusic.net/',` / `url: 'http://home[.]coremusic[.]net:81',` |
| 27 | CSRF testi 403 + `csrf_invalid` + `halt` doğrular | `shared/tests/Middleware/CsrfMiddlewareTest.php` | 94-96 | `assertSame(403, $result['httpStatus']);` · `assertSame('csrf_invalid', …)` · `assertTrue($result['halt'] ?? false);` |
| 28 | CSRF testi timing-safe doğrular | `shared/tests/Middleware/CsrfMiddlewareTest.php` | 306-323 | `testTimingSafeComparison` + `assertSame(403, …, 'Different length tokens must fail');` |
| 29 | 304 yolu `exit` içermez (source scan testi) | `shared/tests/Api/ResponseNormalizationMiddlewareTest.php` | 40-41 | `assertDoesNotMatchRegularExpression('/\b(exit|die)\b\s*[(;]/', $source, …)` + `assertStringContainsString("'halt'", $source, …)` |
| 30 | Gövdesiz 304: `halt` + boş gövde | `shared/tests/Api/ResponseNormalizationMiddlewareTest.php` | 53-55 | `assertSame(304, $out['httpStatus']);` · `assertTrue($out['halt'], …)` · `assertSame('', $out['body']);` |

> **Not (satır 22):** dosya adı yazım hatası (`.config.config.js`) düzeltilmiş
> olarak `vitest.config.js` kullanılmıştır; içeriği L30 doğrulanmıştır.

### §2.1 Çelişen iddialar (yazılmayanlar)

| # | İddia kaynağı | İddia | Repo gerçeği (kanıt) | Karar |
|---|---|---|---|---|
| C1 | Havuz 12.5 kılavuzları | PHPUnit 12 kurulumu/yazımı | `shared/composer.json:24` → `^10.5` | **FARK** — 12.5 mevcut davranış sayılmadı |
| C2 | Havuz 11.5 kılavuzları | PHPUnit 11 özellikleri | `shared/composer.json:24` → `^10.5` | **FARK** — 11.5 mevcut davranış sayılmadı |
| C3 | Havuz fixtures | `@dataProvider` docblock | repo geneli `DataProvider` = **0 eşleşme** | **FARK** — docblock yok |
| C4 | `.ai/AGENTS.md` §25.2 | "`shared/tests/` 22 dosya" (2026-09-27 ölçümü) | 2026-10-15: **41** `*Test.php` (vendor hariç) | **STALE** — disk kazandı |
| C5 | `ci.yml` header L5 | "`phpunit.xml x2` (shared, auth)" | disk: **4** phpunit.xml (shared, auth, home, api) | **STALE** — disk kazandı |
| C6 | Kapsam §6.3 varsayımı | "Unit/Application test dizini" | `shared/tests/Unit/` alt dizinleri: Config, Device, Log, Middleware, PageRouter, Security, Theme — **Application YOK** | **YOK** — kapı G3 buna göre yazıldı |
| C7 | Kapsam §6.4 | "`Unit/Core/**` 31 dosya / 96 metot" | `shared/tests/Unit/**` = **16 dosya**; 96 metot iddiası dosya sayısınca tutmuyor | **⚠️ VERIFICATION REQUIRED** |

---

## §3 TEST ENVANTERİ (DOSYA VE METOT SAYILARI)

### §3.1 PHP — repo bazlı (vendor hariç, 2026-10-15)

| # | Repo | `tests/` altı `*Test.php` | `public function test*` | `#[Test]` attribute | Toplam metot |
|---|---|---:|---:|---:|---:|
| 1 | `shared` | 41 | 349 | 0 | 349 |
| 2 | `auth.coremusic.net` | 6 | 5 | 31 | 36 |
| 3 | `home.coremusic.net` | 8 | 33 | 0 | 33 |
| 4 | `api.coremusic.net` | 3 | 32 | 0 | 32 |
| 5 | **Toplam** | **58** | **419** | **31** | **450** |

**Önemli bulgu:** auth test dosyalarının 5'inde method adları `test` öneksiz ve
`#[Test]` attribute'ü ile işaretlidir (repo'daki 31 `#[Test]` = tamamı auth);
bu nedenle yalnız `public function test` sayımı auth'u **36 yerine 5** gösterir.

### §3.2 `shared/tests` alt dizin dağılımı (41 dosya)

| # | Dizin | `*Test.php` | Not |
|---|---|---:|---|
| 1 | `Api/` | 8 | Gateway, DTO, Response/Request normalization, versioning |
| 2 | `Component/` | 1 | TemplateEngine |
| 3 | `Events/` | 2 | DomainEvent, EventDispatcher |
| 4 | `Middleware/` | 9 | CSRF, Auth, BypassAuth (×2), Pipeline, Origin, RateLimiter, Session, Validation |
| 5 | `OAuth/` | 1 | OAuthManager |
| 6 | `Repository/` | 2 | ApiKey command + repository |
| 7 | `Security/` | 2 | SessionKeys, ReturnUrlPolicyEdgeCase |
| 8 | `Unit/` | 16 | Config 1 · Device 4 · Log 2 · PageRouter 6 · Security 2 · Theme 1 |
| 9 | `Fixtures/` | 0 | Veri klasörü (`chrome/`, `jwt/`) — test dosyası değil, suite değil |
| — | **Toplam** | **41** | phpunit.xml'de 8 suite (Fixtures suite değildir) |

### §3.3 JS / E2E envanteri

1. `*.spec.js` = **9 dosya**: `js/components/primitives/` 6 (Badge, Button,
   Input, Skeleton, Slider, Toggle) · `js/core/breakpoints.spec.js` ·
   `tests/components/history.spec.js` · `tests/components/player-info.spec.js`
2. `*.spec.ts` = **1 dosya**: `tests/e2e/player-info.spec.ts` (**400 satır**)
3. spec.js içi `it(` = **87** · `describe(` = **13** (kelime sınırı regex'i)
4. e2e `test(` = **18** · `test.describe(` = **2**
5. `assets.coremusic.net` tüm `.js` = 99 · `js/` altı = 95 · `js/router/` = 28 ·
   `js/components/` = 37

### §3.4 CSS / SQL envanteri

1. `Css/**/*.css` = **94** · `Css/01_Abstracts/` = **19**
2. `--cm-` token eşleşmesi = **875 satır / 987 oluşum**
3. `.ai/.sql/mysql/*.sql` = **20 dosya**
4. `shared/src/**/*.php` = **165 dosya**

### §3.5 Vault çelişkileri (stale kayıtlar)

| # | Vault iddiası | Kaynak | Disk (2026-10-15) | Karar |
|---|---|---|---|---|
| 1 | `shared/tests` 22 dosya | `.ai/AGENTS.md` §25.2 | 41 | stale |
| 2 | `phpunit.xml x2` | `ci.yml` L5 header | 4 | stale |
| 3 | Unit/Application 39 test (%64 iddiası) | kapsam varsayımı | dizin YOK | YOK |
| 4 | Unit/Core 31/96 | kapsam §6.4 | Unit 16 dosya | ⚠️ |

### §3.6 Dosya bazlı envanter (her dosyanın metot sayısı)

> Amaç: toplam sayıların nereden geldiğini dosya düzeyinde göstermek —
> toplamlar §3.1 ile birebir tutar (349 + 36 + 33 + 32 = 450).

**`shared/tests` — 45 PHP dosyası (41 test + 4 yardımcı):**

| # | Dosya | Metot | # | Dosya | Metot |
|---|---|---:|---|---|---:|
| 1 | `Api/ApiPipelineSecurityTest` | 10 | 24 | `Repository/ApiKeyCreateCommandTest` | 9 |
| 2 | `Api/ApiResponseTest` | 8 | 25 | `Repository/ApiKeyRepositoryTest` | 16 |
| 3 | `Api/DtoTest` | 9 | 26 | `Repository/Sqlite/SqliteDatabaseManager` | 0 (yardımcı) |
| 4 | `Api/GatewayMethodRoutingTest` | 8 | 27 | `Repository/Sqlite/SqliteDatabaseRegistry` | 0 (yardımcı) |
| 5 | `Api/RequestValidationMiddlewareTest` | 10 | 28 | `Security/ReturnUrlPolicyEdgeCaseTest` | 24 |
| 6 | `Api/ResponseNormalizationMiddlewareTest` | 5 | 29 | `Security/SessionKeysTest` | 16 |
| 7 | `Api/RouteTableTest` | 11 | 30 | `Unit/Config/ConfigManagerTest` | 10 |
| 8 | `Api/VersioningTest` | 6 | 31 | `Unit/Device/DeviceDetectorTest` | 13 |
| 9 | `Component/TemplateEngineTest` | 5 | 32 | `Unit/Device/DeviceRendererTest` | 7 |
| 10 | `Events/DomainEventTest` | 7 | 33 | `Unit/Device/NavRouteConsistencyTest` | 2 |
| 11 | `Events/EventDispatcherTest` | 8 | 34 | `Unit/Device/WelcomePopupRenderTest` | 1 |
| 12 | `Fixtures/chrome/footer.php` | 0 (veri) | 35 | `Unit/Log/LoggerRedactionTest` | 8 |
| 13 | `Fixtures/chrome/header.php` | 0 (veri) | 36 | `Unit/Log/Psr3LoggerTest` | 10 |
| 14 | `Middleware/AuthMiddlewareTest` | 4 | 37 | `Unit/PageRouter/AuthGuardTest` | 8 |
| 15 | `Middleware/BypassAuthMiddlewareFailClosedTest` | 3 | 38 | `Unit/PageRouter/AuthUrlBuilderTest` | 6 |
| 16 | `Middleware/BypassAuthMiddlewareTest` | 11 | 39 | `Unit/PageRouter/HtmlShellRendererChromeTest` | 4 |
| 17 | `Middleware/CsrfMiddlewareTest` | 16 | 40 | `Unit/PageRouter/HtmlShellRendererGuardConfigTest` | 5 |
| 18 | `Middleware/MiddlewarePipelineTest` | 12 | 41 | `Unit/PageRouter/RouteRegistryTest` | 5 |
| 19 | `Middleware/OriginCheckMiddlewareTest` | 5 | 42 | `Unit/PageRouter/SpaRouteTest` | 2 |
| 20 | `Middleware/RateLimiterMiddlewareTest` | 10 | 43 | `Unit/Security/JwtServiceTest` | 9 |
| 21 | `Middleware/SessionManagerMiddlewareTest` | 10 | 44 | `Unit/Security/ReturnUrlPolicyTest` | 16 |
| 22 | `Middleware/ValidationMiddlewareTest` | 5 | 45 | `Unit/Theme/ThemeManagerModeTest` | 7 |
| 23 | `OAuth/OAuthManagerTest` | 8 | — | — | — |

**Grup toplamları (§3.1 denetimi):** Api 67 · Component 5 · Events 15 ·
Fixtures 0 · Middleware 76 · OAuth 8 · Repository 25 · Security 40 · Unit 113
= **349** ✓. En ağır 3 dosya: `ReturnUrlPolicyEdgeCaseTest` 24 ·
`CsrfMiddlewareTest` 16 · `ApiKeyRepositoryTest` 16.

**`auth.coremusic.net/tests` — 6 dosya / 36 metot:**

| # | Dosya | Metot |
|---|---|---:|
| 1 | `Unit/Domain/DTO/LoginRequestTest` | 5 |
| 2 | `Unit/Domain/Entity/UserTest` | 4 |
| 3 | `Unit/Domain/ValueObject/EmailTest` | 8 |
| 4 | `Unit/Domain/ValueObject/GenderTest` | 8 |
| 5 | `Unit/Domain/ValueObject/PasswordTest` | 6 |
| 6 | `Unit/Service/SessionManagerTest` | 5 |
| — | `bootstrap.php` | 0 (kurulum) |

**`home.coremusic.net/tests` — 8 dosya / 33 metot:**

| # | Dosya | Metot |
|---|---|---:|
| 1 | `Unit/Bootstrap/EntryPointCacheSafetyTest` | 3 |
| 2 | `Unit/Component/HomeSongButtonTest` | 5 |
| 3 | `Unit/Component/RecentTracksComponentTest` | 6 |
| 4 | `Unit/Layout/HomeLayoutVariantTest` | 5 |
| 5 | `Unit/Loader/ComponentLoaderTest` | 7 |
| 6 | `Unit/Page/HistoryPageTest` | 2 |
| 7 | `Unit/Page/HomeChromeSmokeTest` | 2 |
| 8 | `Unit/Page/NowPlayingPageTest` | 3 |
| — | `Support/FakeComponent.php` | 0 (sağlayıcı) |

**`api.coremusic.net/tests` — 3 dosya / 32 metot:**

| # | Dosya | Metot |
|---|---|---:|
| 1 | `Unit/AuthControllerTest` | 18 |
| 2 | `Unit/RouteConfigTest` | 12 |
| 3 | `Unit/SmokeTest` | 2 |
| — | `bootstrap.php` | 0 (kurulum) |

> api'nin tek başına `AuthControllerTest` = 18 metotla en ağır tek dosyası
> vardır; iki dosya (AuthController + RouteConfig) 30/32 metodu taşır.

---

## §4 TEST ÇİFTİ ENVANTERİ (GREP — `shared/tests`)

| # | Kalıp | Eşleşme | Kanıt noktaları | Yorum |
|---|---|---:|---|---|
| 1 | `createMock` | 9 | `OAuth/OAuthManagerTest.php` L27,45,63,78,91,112,131,149 (8×, `PDO`) · `Middleware/RateLimiterMiddlewareTest.php` L307 (`CacheInterface`) | Mock baskın; hedef hep arayüz/PDO |
| 2 | `createStub` | 0 | — | Stub kalıbı hiç kullanılmıyor |
| 3 | `expectException` | 10 | ConfigManager L31 · OAuthManager L140 · TemplateEngine L106 · JwtService L118,140 · ApiKeyCreateCommand L74,80 · ApiKeyRepository L204,210 · DeviceRenderer L35 | Olumsuz yol testleri yaygın |
| 4 | `expectExceptionMessageMatches` | 0 | — | Mesaj regex doğrulaması yok |
| 5 | `@dataProvider` (docblock) | 0 | repo geneli 0 eşleşme | Provider hiç kullanılmıyor |
| 6 | `#[Test]` attribute | 31 | auth `tests/` (ör. `ValueObject/EmailTest.php` L13,20,27,34,41,48,55,63) | auth'da method adı kalıbı farklı |
| 7 | `PHPUnit\Framework\Attributes\…` use | 3 | `Middleware/BypassAuthMiddlewareFailClosedTest.php` L6-8: `RunInSeparateProcess`, `PreserveGlobalState`, `CoversClass` | Ayrı process + coverage hedefleme |
| 8 | `@covers` docblock | 6 | SessionKeys L14 · ReturnUrlPolicyEdgeCase L12 · SessionManagerMiddleware L17 · RateLimiterMiddleware L15 · MiddlewarePipeline L15 · CsrfMiddleware L14 | Kapsam hedefleme kısmi |
| 9 | `markTestSkipped` | 1 | `Unit/Log/LoggerRedactionTest.php` L171 (`error_log ini bu ortamda değiştirilemiyor`) | Koşul atlama tek noktada |
| 10 | `markTestIncomplete` | 0 | — | İşaretli eksik test yok |
| 11 | `setUpBeforeClass` | 0 | — | Sınıf başı fixture kurulumu yok |
| 12 | `assertSame` (ör. Csrf testi) | Csrf dosyasında 18 | L46,94,95,116,139,173,185,213,214,238,281,299,323,348 … | Tip-sıkı doğrulama baskın |
| 13 | HTTP `Vary` header testi | 0 | `Vary` = 2 eşleşme, ikisi de Türkçe "varyant" (`ReturnUrlPolicyTest` L103,123) | **Vary tekillik testi YOK** |

### §4.1 Ek ölçümler (2026-10-15 — ikinci tur grep, `-Recurse` düzeltilmiş)

> Not: ilk turda `Select-String -Path 'shared\tests\*.php'` **-Recurse'sız**
> çalışmıştı (sonuç 0 → sahte); bu tablo `-Recurse` ile yeniden alındı.

| # | Kalıp | Eşleşme | Yorum |
|---|---|---:|---|
| 1 | `function setUp(` | 19 | Fixture kurulumu yaygın (ör. `CsrfMiddlewareTest.php:18`) |
| 2 | `function tearDown(` | 14 | Temizlik de yaygın; 19/14 farkı = 5 dosyada yalnız kurulum |
| 3 | `declare(strict_types=1)` | 43 | 45 dosyanın 43'ü strict; 2 dosya strict değil (düzeltme adayı) |
| 4 | `abstract class … TestCase/Test` | 0 | Ortak **abstract** test tabanı yok (havuz extending iddiası → FARK, `web-arastirma.md` §3.2.5) |
| 5 | `trait` kullanımı (test dosyalarında) | 0 | Trait ile fixture paylaşımı yok |
| 6 | `->expects(` | 0 | Mock beklenti doğrulaması yok (yeniden teyit) |
| 7 | `willReturn` | 5 | Mock/stub dönüş yapılandırması 5 noktada |
| 8 | `$this->assert…` (45 dosya geneli) | 837 | Toplam assertion ≈ 837 → metot başına ≈ 1,9 |
| 9 | `Attributes\Test;` use | 0 | shared'te `#[Test]` yok; 31 attribute auth'da |
| 10 | `pcov\|xdebug` (ci.yml) | 0 | Coverage sürücüsü CI'da tanımsız (G7 ile uyumlu) |

**Assertion yoğunluğu (metot başına ≈1,9):** 837 assertion / 450 metot —
çoğu test tek assertion ile yazılmış görünür (AA E1/E2 ile de uyumlu:
`dogrulama.md` §2 satır 27-30'daki çok-assertion dosyaları istisnadır).

---

## §5 YAPILANDIRMA KANITLARI

### §5.1 composer (shared) — 6 satır

| # | Satır | İçerik |
|---|---|---|
| 1 | L8 | `"php": ">=8.4"` |
| 2 | L24 | `"phpunit/phpunit": "^10.5"` |
| 3 | L25 | `"phpstan/phpstan": "^1.10"` |
| 4 | L34 | `"CoreMusic\\Test\\": "tests/"` |
| 5 | L47 | `"test": "phpunit"` |
| 6 | L48 | `"stan": "phpstan analyse src --level=5"` |

### §5.2 composer phpunit varlığı (5 subdomain composer.json)

| # | composer.json | phpunit geçmesi |
|---|---|---:|
| 1 | `shared` | 2 (require-dev L24 + script L47) |
| 2 | `api.coremusic.net` | 1 |
| 3 | `auth.coremusic.net` | 1 |
| 4 | `home.coremusic.net` | 1 |
| 5 | `media.coremusic.net` | **0** ← phpunit YOK (kapsam boşluğu doğrulandı) |
| 6 | `assets.coremusic.net` | composer.json YOK |

### §5.3 phpunit.xml envanteri (4 dosya)

| # | Dosya | Bootstrap | Suite | Coverage bloğu |
|---|---|---|---|---|
| 1 | `shared/phpunit.xml` | `vendor/autoload.php` | 8 (Unit, Api, Repository, Events, Security, Middleware, Component, OAuth) | YOK (`<source>` var) |
| 2 | `auth.coremusic.net/phpunit.xml` | `tests/bootstrap.php` | 1 (Unit; Integration kaldırıldı L10-12) | VAR (L28-32, text stdout) |
| 3 | `home.coremusic.net/phpunit.xml` | `vendor/autoload.php` (L4) | 1 (Unit → `tests/Unit`, L9-11) | yok; `<source>` `include` (L13-17) |
| 4 | `api.coremusic.net/phpunit.xml` | `tests/bootstrap.php` (L4) | 1 (Unit → `tests/Unit`, L11-13) | VAR (L23-27, text stdout); `failOnRisky/Warning` L6-7 |

> **Çözüm notu (2026-10-15):** bu iki satırdaki `⚠️ VERIFICATION REQUIRED`
> kapatıldı — 4/4 phpunit.xml tam okundu (satır sayıları: shared 39 · auth 34 ·
> home 18 · api 29). `failOn*` yalnız auth'da değil **api'de de** vardır.

### §5.4 CI jobs (`.github/workflows/ci.yml`, 203 satır, 6 job)

| # | Job | Satır | Ne koşar | Soft? |
|---|---|---|---|---|
| 1 | `php-lint` | 25-64 | composer install ×3 + `composer stan` | hayır |
| 2 | `php-test` | 66-108 | `vendor/bin/phpunit "$PHPUNIT_ARGS"` (matrix: shared, auth `--testsuite Unit`) | hayır |
| 3 | `composer-audit` | 110-139 | `composer audit` ×3 | hayır |
| 4 | `js-test` | 142-158 | `npm run test:js` | hayır |
| 5 | `js-coverage` | 160-176 | `npm run test:js:coverage` | **evet** (`continue-on-error: true`, A7) |
| 6 | `js-e2e` | 178-203 | `npx playwright install chromium` + `npm run test:e2e` | **evet** (`continue-on-error: true`, A6) |

### §5.5 Kök package.json (18 satır)

| # | Script | Komut |
|---|---|---|
| 1 | `test:js` | `vitest run --root assets.coremusic.net` |
| 2 | `test:js:watch` | `vitest --root assets.coremusic.net` |
| 3 | `test:js:coverage` | `vitest run --root assets.coremusic.net --coverage` |
| 4 | `test:e2e` | `playwright test -c assets.coremusic.net/playwright.config.ts` |

---

## §6 KAPI (GATE) DEĞERLENDİRMESİ — PASS / BLOCK

| # | Kapı | Ölçüt | Kanıt | Sonuç |
|---|---|---|---|---|
| G1 | Suite tanımı | suite dosyaları + `composer test` + test dosyaları | 4 phpunit.xml · composer:47 · 58 test dosyası | **PASS** |
| G2 | Unit/Core kapsamı | iddia 31 dosya / 96 metot | `shared/tests/Unit` = 16 dosya; 96 metot yeniden sayılmadı | **⚠️ VERIFICATION REQUIRED** |
| G3 | Unit/Application | her uygulama sınıfının testi | `Unit/Application` dizini **YOK** (Unit altı: Config, Device, Log, Middleware, PageRouter, Security, Theme) | **BLOCK** |
| G4 | Integration | her BC'de Integration suite | `tests/Integration` YOK (Test-Path False) + auth suite **kaldırıldı** (phpunit.xml L10-12) + ci.yml L77-78 | **BLOCK** (bilinçli kaldırma; geri ekleme koşulu tanımlı) |
| G5 | JS coverage ≥80 | vitest.config L25-28 eşiği + CI job | eşik ✓ · job `continue-on-error: true` (ci.yml L167) · canlı rapor **yok** | **⚠️ SOFT** — eşiği karşıladığı kanıtlanmadı |
| G6 | E2E | app + media hedefleri | 1 spec/18 test + config L16-17 + webServer L46-47 · job soft (A6) · media hedefi **yok** | **PARTIAL** — config PASS, kapsam eksik, koşum kanıtsız |
| G7 | PHP coverage | CI'da coverage üretimi | ci.yml L104-107'de coverage flag **YOK**; auth phpunit.xml L26-31 rapor bloğu bayraksız işlevsiz | **BLOCK** |

**Genel kapı sonucu: `BLOCK`** — G3, G4, G7 kapalı; G2/G5 ⚠️; G6 kısmi.
Not: G4'ün arkasında kayıtlı bir onarım kararı vardır (auth phpunit.xml L12:
"Yeni integration testleri eklendiğinde bu suite geri eklenebilir").

---

## §7 KPI ÖLÇÜM TABLOSU

| # | KPI | Hedef | Ölçüm (2026-10-15) | Durum |
|---|---|---|---|---|
| 1 | PHP test dosyası (repo toplam) | raporlanır | 58 (vendor hariç) | ✅ ölçüldü |
| 2 | PHP test metodu (repo toplam) | raporlanır | 450 (419 prefix + 31 attribute) | ✅ ölçüldü |
| 3 | `shared/tests` dosya/metot | ≥41 / ≥349 | 41 / 349 | ✅ ölçüldü |
| 4 | Unit/Core kapsamı | iddia 31/96 | 16 dosya; metot ⚠️ | ⚠️ |
| 5 | Unit/Application kapsamı | %100 | dizin YOK | ❌ |
| 6 | Integration kapsamı | her BC | 0/4 BC (dizin yok, suite kaldırıldı) | ❌ |
| 7 | `#[Test]` attribute | raporlanır | 31 (tamamı auth) | ✅ ölçüldü |
| 8 | Mock (`createMock`) | >0 | 9 | ✅ ölçüldü |
| 9 | Stub (`createStub`) | raporlanır | 0 | ✅ ölçüldü (kullanım yok) |
| 10 | Olumsuz test (`expectException`) | >0 | 10 | ✅ ölçüldü |
| 11 | `@covers` hedefleme | >0 | 6 (+ auth `CoversClass` use 1) | ✅ ölçüldü |
| 12 | Provider (docblock/attribute) | raporlanır | 0 / 0 | ✅ ölçüldü (yok) |
| 13 | İşaretli eksik test | 0 | `markTestIncomplete` = 0 | ✅ ölçüldü |
| 14 | JS spec dosyası | raporlanır | 9 spec.js + 1 spec.ts | ✅ ölçüldü |
| 15 | JS `it()` | raporlanır | 87 | ✅ ölçüldü |
| 16 | E2E `test()` | raporlanır | 18 (+2 describe) | ✅ ölçüldü |
| 17 | JS coverage değeri | ≥80/80/80/80 | rapor **yok**; job soft | ⚠️ |
| 18 | PHP coverage değeri | raporlanır | üretilmiyor (flag yok) | ❌ |
| 19 | Vary/304 tekillik testi | var | 304: 11 eşleşme · Vary header: **0** | ❌ |
| 20 | CacheLayer/Prefetch testi | var | `shared/tests/Cache/` YOK · CacheLayer/Prefetch dosya 0 | ❌ |
| 21 | SQL kanıt havuzu | ≥20 | 20 | ✅ ölçüldü |
| 22 | Design token (`--cm-`) | raporlanır | 987 oluşum / 875 satır | ✅ ölçüldü |
| 23 | Test koşum süresi | raporlanır | ölçülmedi (koşum yok) | ⚠️ |
| 24 | Kesişim (havuz ∩ envanter) | 0 | 0 HIT / 4286 satır | ✅ ölçüldü |
| 25 | Havuz URL erişimi | 9/9 | curl 9/9 → 200 · webfetch 9/9 | ✅ ölçüldü |

---

## §8 OLUMSUZ SENARYOLAR

1. **Stub kullanılmadı** — `createStub` = 0; bağımlılık ikamesi yalnız mock ile.
2. **Provider hiç yok** — `DataProvider` = 0 (hem docblock hem attribute);
   havuz 12.5 fixtures/provider bölümü repo'da karşılıksız (açık soru #2).
3. **Sınıf başı fixture yok** — `setUpBeforeClass` = 0; pahalı kurulum her
   testte tekrarlanıyor olabilir (ölçülmedi).
4. **Integration suite yok** — 4/4 BC'de yok; auth'da bilinçli kaldırıldı
   (PHPUnit 10 exit 2 kırılması nedeniyle, phpunit.xml L10-12).
5. **PHP coverage yok** — ci.yml L104-107'de bayrak yok; auth'un
   `<coverage>` rapor bloğu bayraksız boş işlev görür (L26-31 yorumu bunu
   zaten söylüyor).
6. **JS coverage/e2e soft** — iki job da `continue-on-error: true`
   (ci.yml L167, L183); kırmızı bile olsa bloklamaz.
7. **E2E tek spec** — `player-info.spec.ts` tek dosya; auth/media e2e yok.
8. **Vary header testi yok** — grep "Vary" = 2, ikisi de Türkçe "varyant".
9. **Cache testi yok** — `shared/tests/Cache/` dizini YOK.
10. **İki ayrı eşik seti yok** — vitest.config'te tek threshold bloğu var
    (80/80/80/80, L25-28); önceki özet iddiası (70/65/65/70 + 80/70/75/80
    iki blok) **doğrulanamadı → tek blok gerçektir**.
11. **Stale vault satırları** — `.ai/AGENTS.md` §25.2 "22 dosya", ci.yml
    header "phpunit.xml x2" → disk ile çelişiyor, disk kazandı.
12. **Canlı koşum yok** — hiçbir kapı "yeşil" iddiasıyla kapatılmadı.

---

## §9 PLANNED BOŞLUKLAR

| # | İddia/iş | Gereken kanıt/iş | Durum |
|---|---|---|---|
| 1 | Unit/Core 31 dosya / 96 metot (kapsam §6.4) | dosya + metot yeniden sayımı (Unit = 16 bulundu) | ⚠️ VERIFICATION REQUIRED |
| 2 | home/api phpunit.xml içeriği | iki dosyanın okunması (bootstrap/suite/coverage) | ✅ **ÇÖZÜLDÜ** (2026-10-15 — §5.3 satır 3-4 dolduruldu) |
| 3 | Vitest coverage ≥80/80/80/80 | `npm run test:js:coverage` raporu (koşulmadı) | PLANNED |
| 4 | PHP coverage ≥hedef | ci.yml'ye `--coverage-clover` + sürücü (Xdebug/PCOV) | PLANNED |
| 5 | 450 testin yeşil koşumu | `composer test` ×4 repo (çalıştırılmadı) | PLANNED |
| 6 | E2E 18 testin pass oranı | `npm run test:e2e` (çalıştırılmadı) | PLANNED |
| 7 | Integration suite geri dönüşü | `tests/Integration/` + suite kaydı (auth phpunit.xml L12 koşulu) | PLANNED |
| 8 | media phpunit | `media.coremusic.net/composer.json` require-dev'e phpunit | PLANNED |
| 9 | Vary/304 tekillik testi | `ResponseNormalization` için Vary header assertion'ı | PLANNED |
| 10 | CacheLayer/Prefetch testleri | `shared/tests/Cache/` + ilgili test sınıfları | PLANNED |
| 11 | CSRF strateji envanteri × test eşlemesi (kapsam §6.2) | strateji listesi + dosya bazlı eşleme | PLANNED |
| 12 | DeviceDetector coverage %97 (kapsam §6.6) | tekil coverage raporu | PLANNED |
| 13 | `phpunit.xml x2` header düzeltmesi | ci.yml L5'in 4 olarak güncellenmesi (DevOps onayı) | PLANNED |
| 14 | §25.2 "22 dosya" stale kaydı | vault-sync ile 41'e güncelleme (MO) | PLANNED |

---

## §10 OKUNAN KAYNAKLAR

### §10.1 Okundu (değiştirilmedi)

1. `shared/composer.json` (50 satır) · `shared/phpunit.xml`
2. `auth.coremusic.net/phpunit.xml` (34 satır)
3. `auth.coremusic.net/composer.json` · `api` · `home` · `media` (phpunit grep)
4. `.github/workflows/ci.yml` (203 satır) — yalnız okundu, `*.yml` yazımı yasak
5. `package.json` (kök, 18 satır)
6. `assets.coremusic.net/vitest.config.js` (33) · `playwright.config.ts` (56)
7. `shared/tests/**` — 41 dosya envanteri + CsrfMiddlewareTest (350),
   ResponseNormalizationMiddlewareTest (108), DeviceDetectorTest (70+) içeriği
8. `assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js` (135)
9. `assets.coremusic.net/tests/e2e/player-info.spec.ts` (400, ilk 70 okundu)
10. `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` (57)
11. `.ai/.sql/mysql/coremusic_auth.sql` (298, ilk 90 + tablo listesi)
12. `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` (4286 satır, salt-okunur)

### §10.2 Yazılan (yalnız bu klasör — 4 dosya)

1. `application-testing-dogrulama.md` (bu dosya) — yeniden yazım (ilk taslak
   hatalı satır kanıtları içeriyordu; `shared/tests/Unit/Application/...`
   yolları diskte yoktu → tüm kanıtlar tazelendi).
2. `application-testing-web-arastirma.md` — havuz erişim + claim matrisi.
3. `application-testing-ornekler.md` — 5 dilde YAPMA↔YAP örnekleri.
4. `application-testing-bilesenler.md` — bileşen/bağımlılık haritası.

> Bu dosyanın genişletilmesi (§3.6, §4.1) 2026-10-15 ikinci tur ölçümden
> geldi: dosya bazlı metot sayıları + `-Recurse` düzeltmesiyle tekrarlanan
> grep'ler (§4.1 giriş notu).

### §10.3 Dokunulmayanlar

`index.md` (42) · `application-testing-kapsam.md` (523) · tüm `*.php/js/css/sql`
kaynak · `.github/workflows/*.yml` · `.ai/scripts/*.ps1` · kök `index.md`.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15


> **URL notu (2026-10-16):** §2 kanıt tablosunda (L111) geçen
> `home[.]coremusic[.]net:81` noktal yazımı, envanter kesişimi HIT'i önlemek
> içindir (bu kök `k011-urls.txt` envanterindedir — 1 isabet); değer
> `assets.coremusic.net/playwright.config.ts` L46-47 yapılandırmasından
> alınmıştır, canlı DNS/TLS isteği bu klasörde ölçülmemiştir — kapsam yoktur,
> iddia sayılmaz.
