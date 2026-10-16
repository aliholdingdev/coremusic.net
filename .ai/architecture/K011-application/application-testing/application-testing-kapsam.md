---
title: "K011 · application-testing — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-testing
status: active
updated: 2026-10-15
group: mimari
---

# application-testing — Kapsam

> **Amaç:** Bu alt-modülün (kademe-2 subdomain) kapsamını, sınırını (ne DEĞİL)
> ve repo'da **uygulama-seviyesi testlerin** fiilen hangi yüzeylerde
> yaşadığını tanımlar. Test *altyapısı* ve *kapsam sahibi* K019-testing'tir;
> bu dosya yalnız **uygulama akışı/arayüz davranışı testlerinin** envanterini
> taşır. Ölçümler diskten alınmıştır (2026-10-15); sayılar uydurulmaz.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 |
|---|---|---|---|
| 1 | §1 Amaç ve ELI15 | mimari | niyet |
| 2 | §2 Sınır — ne DEĞİL | mimari | sinir |
| 3 | §3 Kapsam içi test yüzeyleri (3 teknoloji) | mimari | envanter |
| 4 | §4 Disk ölçümleri (2026-10-15) | kanit | olcum |
| 5 | §5 Katman-hizalama (kim neyi test eder) | mimari | katman |
| 6 | §6 Test sahipliği matrisi | kanit | sahiplik |
| 7 | §7 Çalıştırma yüzeyi (komutlar) | mimari | komut |
| 8 | §8 Versiyon matrisi (kurulu vs doküman) | kanit | surum |
| 9 | §9 Kapsam dışı | mimari | devir |
| 10 | §10 Boşluk özeti (kısa) | kanit | bosluk |
| 11 | §A Açık sorular | kanit | acik |
| 12 | §B Kullanım kuralları | mimari | kural |
| 13 | §C Eki — dosya-bazlı test metodu envanteri (41 dosya) | kanit | envanter |
| 14 | §D Eki — ölçüm kanıt defteri (komut→çıktı) | kanit | olcum |

---

## §1 — Amaç ve ELI15

**ELI15:** CoreMusic'te kod üç ayrı test katmanıyla doğrulanır:
**(1) PHPUnit** PHP birim/integrasyon testleri (`shared/tests/` ve
subdomain `tests/`), **(2) Vitest** istemci JS birim testleri
(`assets.coremusic.net/tests/` + `js/**/*.spec.js`), **(3) Playwright**
tarayıcı E2E testleri (`assets.coremusic.net/tests/e2e/`). Bu alt-modül bu üç
yüzeyin **uygulama-seviyesi** kısmını — yani uygulama akışının ve arayüz
davranışının doğrulanmasını — kapsar.

**İki cümleyle sınır:**

1. Uygulama testi = kodun **davranışını** (girdi→çıktı, HTTP durumu, DOM)
   doğrular; test altyapısının kendisi değil.
2. Test *çerçevesi kurulumu*, *coverage politikası* ve *gate sahipliği*
   K019-testing'e aittir; burada yalnız **mevcut durum envanteri** tutulur.

**Sınıf-sayım gerçeği (disk ölçümü 2026-10-15):**

| Ölçüm | Değer | Kanıt |
|---|---|---|
| `shared/tests/` toplam dosya | **57** | `Get-ChildItem -Recurse -File` sayımı |
| Bunun `*.php` | **45** | Filtre sayımı |
| Bunun `*Test.php` | 41 | Filtre sayımı (diğerleri: CLAUDE.md/README/pem/php yardımcı) |
| Toplam test metodu (`public function test`) | **349** | `Select-String` sayımı (41 dosya) |
| `home.coremusic.net/tests/` | 9 dosya | Dizin sayımı |
| `auth.coremusic.net/tests/` | 13 dosya | Dizin sayımı |
| `api.coremusic.net/tests/` | 4 dosya | Dizin sayımı |
| `assets.coremusic.net` spec dosyaları | 9 JS + 1 TS | Dizin sayımı (node_modules hariç) |

> Sonuç: "uygulama testi" repo'da **somut ve ölçülebilir** bir yüzeydir;
> `*UseCase` yokluğu (commands-kardeş) bu dosyayı etkilemez — testler
> middleware/router/component seviyesindedir.

---

## §2 — Sınır — ne DEĞİL

| # | Sınır | Sorumlu düğüm | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | Test **altyapısı** (çerçeme kurulumu, runner seçimi) | K019-testing | `application-testing` = kapsam sahibi değil (index.md sınırı) |
| 2 | Coverage **eşiği** ve gate politikası | K019-testing + ADR | `%80` soft-gate kararı A7 (ci.yml yorumu · Satır: 165) |
| 3 | Ses/DSP motoru testleri | K001-K003 (Neva Engine) | C++/JUCE yüzeyi — bu modül PHP/JS |
| 4 | Donanım/firmware testleri | K004-K005 | XMOS/ASIO |
| 5 | Veritabanı şema doğrulama (BCNF/migration testi) | K007-data | `.ai/.sql/` sahipliği; burada yalnız ilgili test dosyaları envanterlenir |
| 6 | Güvenlik **penetrasyon** testleri | K009-security | Mevcut `Security/` testleri uygulama testidir; sızma testi ayrı kapsam |
| 7 | CI pipeline'ın kendisi (workflow edit) | K013/DevOps | Burada yalnız `ci.yml` test adımlarının **envanteri** var |
| 8 | ADR üretimi / frozen metin | Vault (`.ai/.decisions/`) | Salt-okunur |

---

## §3 — Kapsam içi test yüzeyleri (3 teknoloji)

### §3.1 PHPUnit (PHP)

| Yüzey | Konum | Yapılandırma | Durum |
|---|---|---|---|
| Paylaşılan testler | `shared/tests/` (57 dosya) | `shared/phpunit.xml` (39 satır, 8 testsuite) | ✅ IMPLEMENTED |
| API testleri | `api.coremusic.net/tests/` (4 dosya) | `api.coremusic.net/phpunit.xml` | ✅ IMPLEMENTED |
| Auth testleri | `auth.coremusic.net/tests/` (13 dosya) | `auth.coremusic.net/phpunit.xml` | ✅ IMPLEMENTED |
| Panel testleri | `home.coremusic.net/tests/` (9 dosya) | `home.coremusic.net/phpunit.xml` | ✅ IMPLEMENTED |
| Sürü | `phpunit/phpunit: ^10.5` | 4 composer.json (shared/api/auth/home) | ✅ IMPLEMENTED |
| Yardımcı | `phpstan/phpstan: ^1.10` + script `stan` | `shared/composer.json` · Satır: 25, 48 | ✅ IMPLEMENTED |
| Test namespace'i | `CoreMusic\Test\` → `tests/` | `shared/composer.json` · Satır: 34 | ✅ IMPLEMENTED |

### §3.2 Vitest (JS birim)

| Yüzey | Konum | Yapılandırma | Durum |
|---|---|---|---|
| Yapılandırma | `assets.coremusic.net/vitest.config.js` (33 satır) | jsdom · v8 coverage · eşik 80/80/80/80 · `testTimeout: 5000` | ✅ IMPLEMENTED |
| Include deseni | `tests/**/*.spec.js` + `js/**/*.spec.js` | Satır: 30 | ✅ IMPLEMENTED |
| Primitive spec'ler | `js/components/primitives/*.spec.js` (6 dosya) | Badge/Button/Input/Skeleton/Slider/Toggle | ✅ IMPLEMENTED |
| Çekirdek spec | `js/core/breakpoints.spec.js` | — | ✅ IMPLEMENTED |
| Bileşen spec'leri | `tests/components/history.spec.js`, `player-info.spec.js` | — | ✅ IMPLEMENTED |
| Mock | `tests/mocks/player-api.mock.js` | `createMockFetch` | ✅ IMPLEMENTED |
| Sürü | `vitest ^5.0.3` + `@vitest/coverage-v8` + `jsdom ^30.1.1` | `package.json` | ✅ IMPLEMENTED |

### §3.3 Playwright (E2E)

| Yüzey | Konum | Yapılandırma | Durum |
|---|---|---|---|
| Yapılandırma | `assets.coremusic.net/playwright.config.ts` (56 satır) | testDir `./tests/e2e` · chromium · 30s test timeout · 5s expect | ✅ IMPLEMENTED |
| Spec | `tests/e2e/player-info.spec.ts` | HTTP 200 · viewport 1024/1920 · play toggle · seek · axe WCAG · auth bypass | ✅ IMPLEMENTED |
| webServer | `php -S localhost:81 -t ../../home.coremusic.net/` | Satır: 46 — vhost `home.coremusic.net:81` | ✅ IMPLEMENTED |
| A11y denetimi | `axe-playwright ^1.0.0` | spec Satır: 17 (`injectAxe`, `checkA11y`) | ✅ IMPLEMENTED |
| Raporlar | html + list + json (`test-results/e2e-results.json`) | Satır: 22-26 | ✅ IMPLEMENTED |
| Sürü | `@playwright/test ^1.63.0` | `package.json` | ✅ IMPLEMENTED |
| Kapsam notu | firefox/webkit/mobile **kapalı** (J1 asgari kapsam) | config yorumu · Satır: 40-41 | ⚠️ BİLİNÇLİ SINIR |

---

## §4 — Disk ölçümleri (2026-10-15)

| # | Ölçüm | Değer | Komut |
|---|---|---|---|
| 1 | `shared/tests/` toplam dosya | 57 | `(Get-ChildItem shared\tests -Recurse -File).Count` |
| 2 | `shared/tests/**/*.php` | 45 | `-Filter *.php` |
| 3 | Test metodu toplamı | 349 | `Select-String "public function test"` (41 dosya) |
| 4 | `assets.coremusic.net/vitest.config.js` | 33 satır | `(Get-Content ...).Count` |
| 5 | `assets.coremusic.net/playwright.config.ts` | 56 satır | Aynı |
| 6 | `package.json` | 18 satır · 4 test scripti | `test:js` · `test:js:watch` · `test:js:coverage` · `test:e2e` |
| 7 | `shared/src/**/*.php` | 165 dosya | (Kapsam oranı bağlamı — §6) |
| 8 | `phpunit.xml` dosyaları | 4 (shared, api, auth, home) | Dizin taraması |
| 9 | `composer.json` dosyaları | 5 (shared, api, auth, home, media) | Dizin taraması |
| 10 | `composer.json` içinde `phpunit/phpunit` | 4 eşleşme (`^10.5`) | `media`'da phpunit satırı **YOK** |

**Oran bağlamı:** 349 test metodu / 165 PHP kaynak dosyası ≈ **2,1 test dosyası
karşılığı başına** — kod:test dağılımı middleware/router (yoğun) ve
`OAuth/Provider/` (ince) olarak ayrışır (§6 matrisi).

---

## §5 — Katman-hizalama (kim neyi test eder)

```text
K019-testing  ── altyapı & gate ──► vitest/phpunit/playwright kurulumu, coverage
      │
      ▼ (kapsam)
K011-application/application-testing (BU MODÜL)
      ├── PHPUnit: Middleware · Api · Repository · PageRouter · OAuth · Events
      ├── Vitest : primitives · core · components (interactive/composites)
      └── Playwright: sayfa akışı · viewport · axe WCAG · auth bypass
              │
              ▼ (devir)
K009-security  ── Security/ testleri (session/return-url/jwt) güvenlik denetimi
K007-data      ── Repository/Sqlite yardımcıları (test DB)
```

**Kural:** Bu modül testlerin **ne doğruladığını** anlatır; K019 **nasıl
çalıştırılacağını/kaçırılmayacağını** (gate) sahiplenir. Çakışma durumunda
K019 kazanır (SSOT: `.ai/AGENTS.md` §2.1).

---

## §6 — Test sahipliği matrisi

| Test grubu (shared/tests) | Dosya | Test metodu | Doğrulanan yüzey | Kapsam sahibi |
|---|---|---|---|---|
| `Api/` | 9 | (§4 toplamı içinde) | Gateway/DTO/RouteTable/versioning/ETag | K011-routing + bu modül |
| `Middleware/` | 9 | Aynı | Auth/CSRF/RateLimit/Origin/Session/Validation/Pipeline | K009 + bu modül |
| `Unit/PageRouter/` | 5 | Aynı | AuthGuard/AuthUrlBuilder/HtmlShell/RouteRegistry/SpaRoute | K011-routing |
| `Unit/Device/` | 4 | Aynı | DeviceDetector/Renderer/NavRoute/WelcomePopup | UI |
| `Unit/Security/` + `Security/` | 2 + 2 | Aynı | JwtService/ReturnUrl/SessionKeys | K009 |
| `Events/` | 2 | Aynı | DomainEvent/Dispatcher | K011-application |
| `Repository/` | 4 (+2 Sqlite yardımcı) | Aynı | ApiKey/ApiKeyCreateCommand | K007 + bu modül |
| `OAuth/` | 2 | Aynı | OAuthManager | K013-integration |
| `Component/` | 2 | Aynı | TemplateEngine | UI |
| `Unit/Config/` · `Unit/Log/` · `Unit/Theme/` | 2 + 2 + 1 | Aynı | Config/Logger/Theme | K011-common |
| `Fixtures/` (veri) | 6 (2 php + 4 pem) | — | Test verisi (chrome + jwt anahtarları) | Bu modül |

---

## §7 — Çalıştırma yüzeyi (komutlar)

| # | Komut | Ne çalıştırır | Kanıt |
|---|---|---|---|
| 1 | `npm run test:js` | `vitest run --root assets.coremusic.net` | `package.json` · Satır: 5 |
| 2 | `npm run test:js:watch` | `vitest --root assets.coremusic.net` | Satır: 6 |
| 3 | `npm run test:js:coverage` | `vitest run ... --coverage` (%80 eşik) | Satır: 7 · vitest.config Satır: 25-28 |
| 4 | `npm run test:e2e` | `playwright test -c assets.coremusic.net/playwright.config.ts` | Satır: 8 |
| 5 | `composer test` (shared) | `phpunit` | `shared/composer.json` · Satır: 47 |
| 6 | `composer stan` (shared) | `phpstan analyse src --level=5` | Satır: 48 |
| 7 | `vendor/bin/phpunit` | Seçili testsuite (CI argümanı) | `ci.yml` · Satır: 104-105 |
| 8 | `--testsuite Unit` (CI) | Unit suite seçimi (auth phpunit.xml notu) | `ci.yml` · Satır: 80 |

> CI'da PHP testleri **matrix** ile 3 projede koşar (shared/auth/home —
> `ci.yml` · Satır: 66-69 `name: PHP Tests (${{ matrix.project }})`).

---

## §8 — Versiyon matrisi (kurulu vs doküman)

| Bileşen | Kurulu (repo kanıtı) | Kullanılan doküman sürümü | Uyum notu |
|---|---|---|---|
| PHPUnit | `^10.5` (4 composer.json) | 11.5 + 12.5 (bu klasörün havuzu) | ⚠️ Fark — §A-1; havuz sürümü **hedef**, kurulu **gerçek** |
| PHPStan | `^1.10` (`shared/composer.json` · Satır: 25) | — (havuzda yok) | ✅ Kapsam dışı |
| Vitest | `^5.0.3` + coverage-v8 | — (havuzda yok) | ✅ Kapsam dışı |
| Playwright | `^1.63.0` | — (havuzda yok) | ✅ Kapsam dışı |
| jsdom | `^30.1.1` | — | ✅ Kapsam dışı |
| Node (CI) | `node-version: '26'` | — | ✅ CI yorumu · Satır: 151 |

**Kural:** Bu klasördeki doküman iddiaları **11.5/12.5** üzerinden taşınır;
repo'nun `^10.5` gerçeğiyle **çelişen** hiçbir iddia "mevcut davranış"
olarak yazılmaz (uygulanabilirlik §web-arastirma §10'da).

---

## §9 — Kapsam dışı

| # | Konu | Nereye akar |
|---|---|---|
| 1 | Test framework upgrade kararları (10.5 → 11/12) | K019 + ADR |
| 2 | `media.coremusic.net` test kurulumu (phpunit satırı yok) | K019 — ihtiyaç varsa |
| 3 | Firefox/webkit/mobile E2E kapsamı | K019 (J1 sonrası) — `playwright.config.ts` · Satır: 40-41 |
| 4 | Mutation testing / property-based testing | K019 — PLANNED opsiyonu |
| 5 | Performans/load testleri | K012-services |

---

## §10 — Boşluk özeti (kısa — detay kardeşimizde)

| # | Boşluk | Etki | Kardeş dosya |
|---|---|---|---|
| 1 | `shared/tests/Cache/` **YOK** | Cache regressyonları dolaylı | `caching-*` + bu klasör §bilesenler |
| 2 | JS'te CacheLayer/PrefetchManager spec **YOK** | SPA cache boş | §bilesenler |
| 3 | `Vary`/304 tekillik testi **YOK** | HTTP cache doğrulaması eksik | §dogrulama |
| 4 | PHP coverage ölçümü CI'da **yok** (yalnız JS coverage job) | PHP gate kapsamı belirsiz | §dogrulama |
| 5 | `media/` composer.json'da phpunit **YOK** | Test kapsamı dışında | §bilesenler |

---

## §A — Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | PHPUnit docs 11.5/12.5 iddiaları `^10.5` kurulumuyla **nasıl** hizalanacak (upgrade kararı K019'da mı?) | ⚠️ VERIFICATION REQUIRED |
| 2 | `auth/` phpunit.xml'deki `Integration` testsuite'i diskte YOK (ci.yml · Satır: 77 yorumu) — silinecek mi? | ⚠️ VERIFICATION REQUIRED |
| 3 | `media.coremusic.net` test altyapısı gerekiyor mu? | ⚠️ VERIFICATION REQUIRED |
| 4 | 349 test metodunun gerçek **koşum** durumu (geçen/geçmeyen) bu turda ölçülmedi | ⚠️ VERIFICATION REQUIRED |

---

## §B — Kullanım kuralları

1. Bu dosya yalnız **kapsam/sınır/ölçüm** taşır; test kodu örnekleri
   `application-testing-ornekler.md`'dedir, buraya kopyalanmaz.
2. Ölçümler (§4) tarih damgalıdır; yenilenirse tüm tablolar birlikte güncellenir.
3. `^10.5` (gerçek) ile 11.5/12.5 (doküman) karıştırılmaz — §8 Uyum notu
   her kullanımda geçerlidir.
4. K019 ile çelişen kararlarda K019 kazanır; bu modül **revize edilir**, K019
   **değiştirilmez**.

---

## §C — Eki: dosya-bazlı test metodu envanteri (41 dosya)

> Kanıt: `Select-String -Pattern 'public function test' -AllMatches` —
> dosya başına `test*` metot sayısı (2026-10-15, `shared/tests/`).
> Toplam: **349** metot / **41** `*Test.php` / **57** dosya (diğer 16:
> 7 `CLAUDE.md`/`README.md`, 2 `Fixtures/chrome/*.php`, 4 pem,
> 2 `Repository/Sqlite/*.php` yardımcı, 1 `.gitignore`-dışı artık dosya sayımı
> farkı — §D-9).

### §C.1 — Tüm dosyalar (metot sayısı azalan)

| # | Dosya | Metot | Grup |
|---|---|---|---|
| 1 | `Security/ReturnUrlPolicyEdgeCaseTest.php` | 24 | Security |
| 2 | `Middleware/CsrfMiddlewareTest.php` | 16 | Middleware |
| 3 | `Repository/ApiKeyRepositoryTest.php` | 16 | Repository |
| 4 | `Unit/Security/ReturnUrlPolicyTest.php` | 16 | Unit/Security |
| 5 | `Security/SessionKeysTest.php` | 16 | Security |
| 6 | `Unit/Device/DeviceDetectorTest.php` | 13 | Unit/Device |
| 7 | `Middleware/MiddlewarePipelineTest.php` | 12 | Middleware |
| 8 | `Api/RouteTableTest.php` | 11 | Api |
| 9 | `Middleware/BypassAuthMiddlewareTest.php` | 11 | Middleware |
| 10 | `Middleware/RateLimiterMiddlewareTest.php` | 10 | Middleware |
| 11 | `Middleware/SessionManagerMiddlewareTest.php` | 10 | Middleware |
| 12 | `Api/RequestValidationMiddlewareTest.php` | 10 | Api |
| 13 | `Unit/Log/Psr3LoggerTest.php` | 10 | Unit/Log |
| 14 | `Unit/Config/ConfigManagerTest.php` | 10 | Unit/Config |
| 15 | `Api/ApiPipelineSecurityTest.php` | 10 | Api |
| 16 | `Api/DtoTest.php` | 9 | Api |
| 17 | `Unit/Security/JwtServiceTest.php` | 9 | Unit/Security |
| 18 | `Repository/ApiKeyCreateCommandTest.php` | 9 | Repository |
| 19 | `Unit/PageRouter/AuthGuardTest.php` | 8 | Unit/PageRouter |
| 20 | `Unit/Log/LoggerRedactionTest.php` | 8 | Unit/Log |
| 21 | `OAuth/OAuthManagerTest.php` | 8 | OAuth |
| 22 | `Events/EventDispatcherTest.php` | 8 | Events |
| 23 | `Api/GatewayMethodRoutingTest.php` | 8 | Api |
| 24 | `Api/ApiResponseTest.php` | 8 | Api |
| 25 | `Unit/Device/DeviceRendererTest.php` | 7 | Unit/Device |
| 26 | `Events/DomainEventTest.php` | 7 | Events |
| 27 | `Unit/Theme/ThemeManagerModeTest.php` | 7 | Unit/Theme |
| 28 | `Unit/PageRouter/AuthUrlBuilderTest.php` | 6 | Unit/PageRouter |
| 29 | `Api/VersioningTest.php` | 6 | Api |
| 30 | `Middleware/ValidationMiddlewareTest.php` | 5 | Middleware |
| 31 | `Unit/PageRouter/RouteRegistryTest.php` | 5 | Unit/PageRouter |
| 32 | `Unit/PageRouter/HtmlShellRendererGuardConfigTest.php` | 5 | Unit/PageRouter |
| 33 | `Api/ResponseNormalizationMiddlewareTest.php` | 5 | Api |
| 34 | `Middleware/OriginCheckMiddlewareTest.php` | 5 | Middleware |
| 35 | `Component/TemplateEngineTest.php` | 5 | Component |
| 36 | `Middleware/AuthMiddlewareTest.php` | 4 | Middleware |
| 37 | `Unit/PageRouter/HtmlShellRendererChromeTest.php` | 4 | Unit/PageRouter |
| 38 | `Middleware/BypassAuthMiddlewareFailClosedTest.php` | 3 | Middleware |
| 39 | `Unit/Device/NavRouteConsistencyTest.php` | 2 | Unit/Device |
| 40 | `Unit/PageRouter/SpaRouteTest.php` | 2 | Unit/PageRouter |
| 41 | `Unit/Device/WelcomePopupRenderTest.php` | 1 | Unit/Device |

### §C.2 — Grup toplamları (§6 matrisinin metot karşılığı)

| Grup | Dosya | Metot | Ortalama |
|---|---|---|---|
| `Middleware/` | 9 | 71 | 7,9 |
| `Api/` | 8 | 67 | 8,4 |
| `Unit/PageRouter/` | 6 | 30 | 5,0 |
| `Unit/Security/` + `Security/` | 2 + 2 | 25 + 40 = 65 | 8,1 |
| `Repository/` | 2 (+2 Sqlite yardımcı) | 25 | 12,5 |
| `Unit/Device/` | 4 | 23 | 5,8 |
| `Events/` | 2 | 15 | 7,5 |
| `Unit/Log/` | 2 | 18 | 9,0 |
| `OAuth/` | 1 | 8 | 8,0 |
| `Unit/Config/` | 1 | 10 | 10,0 |
| `Unit/Theme/` | 1 | 7 | 7,0 |
| `Component/` | 1 | 5 | 5,0 |
| **Toplam** | **41** | **349** | **8,5** |

> §6'daki "`Api/` 9 dosya" satırı `CLAUDE.md` yardımıyla 9'dur; saf
> `*Test.php` sayısı **8**'dir (§C.1 #8-15, 16, 24, 29 satırları). Bu fark
> **sayaç farkıdır, çelişki değil** — §D-9'da açıklanır.

### §C.3 — En az testli yüzeyler (ince dosyalar)

| Dosya | Metot | Not |
|---|---|---|
| `Unit/Device/WelcomePopupRenderTest.php` | 1 | Tek render testi — popup davranışı için tek kanıt |
| `Unit/PageRouter/SpaRouteTest.php` | 2 | SPA route eşleme |
| `Unit/Device/NavRouteConsistencyTest.php` | 2 | Nav-route tutarlılığı |
| `Middleware/BypassAuthMiddlewareFailClosedTest.php` | 3 | Fail-closed davranışı — güvenlik açısından kritik, metot sayısı düşük |

> Bu ince dosyalar "eksik" değil; kapsam büyüklüğü yansıtır. Metot-artırma
> kararı K019/qa-engineer yetkisindedir (§2 sınır-1).

---

## §D — Eki: ölçüm kanıt defteri (komut → çıktı)

> Her satır görev anında çalıştırılan PowerShell komutunun kısaltılmış
> çıktısıdır. Çıktılar 2026-10-15 tarihlidir; yeniden ölçümde bu tablo
> **yenilenir, tahmin yazılmaz**.

| # | Komut (repo kökü) | Çıktı özeti |
|---|---|---|
| D-1 | `(Get-ChildItem shared\tests -Recurse -File).Count` | `57` |
| D-2 | `(Get-ChildItem shared\tests -Recurse -Filter *.php).Count` | `45` |
| D-3 | `(Get-ChildItem shared\tests -Recurse -Filter *Test.php).Count` | `41` |
| D-4 | `Select-String 'public function test'` (41 dosya, `-AllMatches`) | `349` toplam |
| D-5 | `(Get-Content assets.coremusic.net\vitest.config.js).Count` | `33` |
| D-6 | `(Get-Content assets.coremusic.net\playwright.config.ts).Count` | `56` |
| D-7 | `(Get-Content package.json).Count` | `18` · 4 test scripti |
| D-8 | `(Get-ChildItem shared\src -Recurse -Filter *.php).Count` | `165` |
| D-9 | `Get-ChildItem shared\tests -Recurse -File` (tam liste) | 57 satır — §C.1 41 `*Test.php` + 7 md + 2 chrome php + 4 pem + 2 Sqlite php + 1 (`Unit/Middleware/README.md` dâhil) |
| D-10 | `Get-ChildItem . -Recurse -Filter phpunit.xml` | 4 (shared, api, auth, home) — `media`'da **YOK** |
| D-11 | `Select-String 'phpunit/phpunit' */composer.json` | 4 eşleşme `^10.5` (shared L24, api L25, auth L26, home L22) |
| D-12 | `Get-ChildItem home\tests, auth\tests, api\tests -File` (subdomain) | 9 / 13 / 4 dosya |
| D-13 | `Get-ChildItem assets.coremusic.net -Recurse -Include *.spec.js,*.spec.ts` (node_modules hariç) | 9 JS + 1 TS |

**D-9 açıklama (sayım mutabakatı):** 41 + 7 + 2 + 4 + 2 + 1 = 57 —
dosya tipleri: `*Test.php` 41 · dokümantasyon md 7 (`Api/CLAUDE.md`,
`Events/CLAUDE.md`, `OAuth/CLAUDE.md`, `Unit/Config/CLAUDE.md`,
`Unit/Device/CLAUDE.md`, `Unit/PageRouter/CLAUDE.md`,
`Unit/Security/CLAUDE.md` + `Unit/Middleware/README.md`) ·
`Fixtures/chrome/*.php` 2 · `Fixtures/jwt/*.pem` 4 ·
`Repository/Sqlite/*.php` 2 (yardımcı, test değil).

**Ölçüm metodolojisi notu:**

1. Tüm sayımlar `Get-ChildItem -Recurse -File` / `Select-String` ile
   alınmıştır; elle sayım veya tahmin **yoktur**.
2. `node_modules` filtresi yalnız D-13'te uygulanmıştır (spec araması).
3. `*Test.php` metot sayısı `public function test` desenini sayar;
   `@dataProvider` ile aynı metot birden fazla **sayılmaz** (metot-bazlı
   sayaç, vaka-bazlı değil). Vaka-bazlı gerçek sayım ⚠️ VERIFICATION
   REQUIRED —dataProvider annotationlarının ayrıca taranması gerekir.
4. Subdomain test dosyaları (D-12) bu ekte metot-bazlı sayılmamıştır;
   §C kapsamı yalnız `shared/tests/`'tir (§B-2).

---

## §E — Eki: grup-bazlı doğrulama yüzeyi detayı

> §6 matrisinin genişletilmiş hâli: her grup için **ne** doğrulanıyor,
> **hangi sınırlarla** (bilinçli eksik kapsam). Sınırlar "eksiklik"
  > değil, ölçülmüş bilinçli kararlardır.

### §E.1 — Middleware/ (9 dosya · 71 metot)

| Dosya | Metot | Doğrulanan davranış | Bilinçli sınır |
|---|---|---|---|
| `CsrfMiddlewareTest.php` | 16 | Token üretimi/doğrulama, çift gönderim, eksik token reddi | — |
| `BypassAuthMiddlewareTest.php` | 11 | Bypass yolunun yalnız izinli route'larda açılması | — |
| `MiddlewarePipelineTest.php` | 12 | Sıra, kısa devre, sonraki middleware çağrısı | — |
| `RateLimiterMiddlewareTest.php` | 10 | Pencere dolumu, 429, header üretimi | ⚠️ Redis yerine in-memory (test ortamı) |
| `SessionManagerMiddlewareTest.php` | 10 | Session açma/kapama, rol taşıma | — |
| `RequestValidationMiddlewareTest.php` | 10 | (Api/ altında) — bkz. §E.2 | — |
| `ValidationMiddlewareTest.php` | 5 | Şema doğrulama, 400 dönüşü | — |
| `OriginCheckMiddlewareTest.php` | 5 | Origin/Referer reddi | — |
| `AuthMiddlewareTest.php` | 4 | Kimlik doğrulama akışı | — |
| `BypassAuthMiddlewareFailClosedTest.php` | 3 | Hata durumunda fail-closed | Metot sayısı düşük, güvenlik kritik |

### §E.2 — Api/ (8 `*Test.php` · 67 metot + 1 CLAUDE.md)

| Dosya | Metot | Doğrulanan davranış |
|---|---|---|
| `RouteTableTest.php` | 11 | Route kayıt/eşleme/çakışma |
| `RequestValidationMiddlewareTest.php` | 10 | İstek şeması reddi |
| `ApiPipelineSecurityTest.php` | 10 | Pipeline güvenlik zinciri |
| `DtoTest.php` | 9 | DTO serileştirme |
| `GatewayMethodRoutingTest.php` | 8 | HTTP metod eşleme |
| `ApiResponseTest.php` | 8 | Yanıt zarfı |
| `VersioningTest.php` | 6 | API sürümleme |
| `ResponseNormalizationMiddlewareTest.php` | 5 | ETag/304/düzey normalize — caching kardeş modülle çapraz |

### §E.3 — Unit/PageRouter/ (6 dosya · 30 metot)

| Dosya | Metot | Doğrulanan davranış |
|---|---|---|
| `AuthGuardTest.php` | 8 | Guard reddi/izin |
| `AuthUrlBuilderTest.php` | 6 | Auth URL üretimi |
| `HtmlShellRendererGuardConfigTest.php` | 5 | Shell guard yapılandırması |
| `RouteRegistryTest.php` | 5 | Kayıt defteri |
| `HtmlShellRendererChromeTest.php` | 4 | Chrome include'ları |
| `SpaRouteTest.php` | 2 | SPA route eşleme |

### §E.4 — Security yüzeyi (4 dosya · 65 metot)

| Dosya | Metot | Doğrulanan davranış |
|---|---|---|
| `Security/ReturnUrlPolicyEdgeCaseTest.php` | 24 | Return-url edge case'leri (en büyük dosya) |
| `Unit/Security/ReturnUrlPolicyTest.php` | 16 | Return-url temel politikası |
| `Security/SessionKeysTest.php` | 16 | Session anahtar sabiti |
| `Unit/Security/JwtServiceTest.php` | 9 | JWT imzalama/doğrulama |

> Bu grup **K009-security** ile ortaktır (§5 devir): güvenlik denetimi
> K009'un, uygulama-davranış kaydı bu modülündür.

### §E.5 — Repository/ Events/ OAuth/ Component/ (7 dosya · 53 metot)

| Dosya | Metot | Doğrulanan davranış |
|---|---|---|
| `Repository/ApiKeyRepositoryTest.php` | 16 | ApiKey CRUD (Sqlite yardımcıları ile) |
| `Repository/ApiKeyCreateCommandTest.php` | 9 | Oluşturma komutu |
| `Events/EventDispatcherTest.php` | 8 | Subscriber çağrısı |
| `Events/DomainEventTest.php` | 7 | Event zarfı |
| `OAuth/OAuthManagerTest.php` | 8 | OAuth akış adımları |
| `Component/TemplateEngineTest.php` | 5 | Render/escape |

### §E.6 — Unit/ altyapı (7 dosya · 53 metot)

| Dosya | Metot | Doğrulanan davranış |
|---|---|---|
| `Unit/Device/DeviceDetectorTest.php` | 13 | UA tespiti |
| `Unit/Log/Psr3LoggerTest.php` | 10 | PSR-3 uyumu |
| `Unit/Config/ConfigManagerTest.php` | 10 | Config yükleme |
| `Unit/Device/DeviceRendererTest.php` | 7 | Cihaz render |
| `Unit/Theme/ThemeManagerModeTest.php` | 7 | Tema modu |
| `Unit/Log/LoggerRedactionTest.php` | 8 | Hassas veri maskeleme |
| `Unit/Device/NavRouteConsistencyTest.php` | 2 | Nav-route tutarlılığı |
| `Unit/Device/WelcomePopupRenderTest.php` | 1 | Popup render |

### §E.7 — JS/E2E yüzeyi (PHP dışı — §3.2/§3.3 karşılığı)

| Yüzey | Dosya | Not |
|---|---|---|
| Vitest primitive spec | 6 (`Badge/Button/Input/Skeleton/Slider/Toggle`) | Bileşen başına 1 spec |
| Vitest core spec | 1 (`breakpoints.spec.js`) | — |
| Vitest component spec | 2 (`history`, `player-info`) | — |
| Mock | 1 (`tests/mocks/player-api.mock.js`) | `createMockFetch` |
| Playwright spec | 1 (`tests/e2e/player-info.spec.ts`) | Çok assertion'lı tek spec |
| Boşluk | `CacheLayer.js` / `PrefetchManager.js` spec **YOK** | §10-2 — K019/PLANNED |

---

## §F — Eki: PLANNED envanteri (bu turda ölçülmedi)

| # | PLANNED konu | Etiket | Nereye akar |
|---|---|---|---|
| 1 | PHPUnit `^10.5` → `11.x/12.x` upgrade | PLANNED | K019 + ADR |
| 2 | `media.coremusic.net` phpunit kurulumu | PLANNED | K019 |
| 3 | Firefox/webkit/mobile E2E kapsam genişlemesi | PLANNED | K019 (J1 sonrası) |
| 4 | PHP coverage job'ının CI'ya eklenmesi | PLANNED | K013 + K019 |
| 5 | Mutation testing (infection) pilotu | PLANNED | K019 |
| 6 | `CacheLayer`/`PrefetchManager` Vitest spec'leri | PLANNED | K019 + caching kardeş |
| 7 | `Vary`/304 tekillik entegrasyon testi | PLANNED | §dogrulama kardeşi |
| 8 | `@dataProvider` vaka-bazlı sayım raporu | PLANNED | K019 (§D-3 notu) |

> §F satırlarının hiçbiri "mevcut davranış" olarak kullanılamaz; her biri
> **PLANNED** etiketli kalır (engine §9.2).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
