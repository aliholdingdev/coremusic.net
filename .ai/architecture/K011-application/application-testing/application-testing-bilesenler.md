---
title: "K011 · application-testing — mimari"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-testing
status: active
updated: 2026-10-15
group: mimari
---

# K011 application-testing — Bileşen & Bağımlılık Haritası (mimari)

**Amaç:** Bu dosya, test altyapısının bileşen envanterini, her bileşenin hangi kullanıcı akışına
kayar olduğunu (bileşen↔akış iz düşümü), test yığınının bağımlılık grafiğini ve envanterden
çıkan boşlukları tek yerde toplar. Tüm sayımlar bu oturumda diskten ölçülmüştür; betikler
**metin olarak** verilir (bu görevde betik çalıştırma yasağı vardır — bkz. kapanış raporu).

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | Yöntem & envanter kuralları | mimari | ölçüm | Sayımların nasıl alındığı, vendor kuralı |
| 2 | Yazılım bileşen envanteri | mimari | envanter | Üretim bileşenleri: PHP modülleri, JS, CSS, SQL |
| 3 | Test bileşeni envanteri | mimari | envanter | Test dosyaları: PHP 58, JS 9, e2e 1 |
| 4 | Bileşen↔akış iz düşümü | mimari | iz | Hangi test hangi kullanıcı akışını korur |
| 5 | Bağımlılık grafiği | mimari | graf | composer→phpunit, package.json→vitest, ci.yml |
| 6 | Konfigürasyon bileşenleri | mimari | config | 4 phpunit.xml + vitest + playwright + ci.yml rolü |
| 7 | Kapsama matrisi | mimari | matris | Bileşen × test tipi kesişimi |
| 8 | Boşluk bileşenleri | mimari | boşluk | Testi olmayan / kapsam dışı bileşenler |
| 9 | Envanter betikleri (metin) | mimari | betik | Tekrar üretilebilir sayımlar |
| 10 | PLANNED bileşenler | mimari | planned | Üretilmesi gereken eksik bileşenler |
| 11 | Kaynaklar | mimari | kaynak | Okunan dosyalar |

---

## §1 — Yöntem & envanter kuralları

### §1.1 Ölçüm kuralları

1. Sayımlar `Get-ChildItem` / `Select-String` ile 2026-10-15'te alındı.
2. `vendor/` ve `node_modules/` her sayımdan **hariç** tutulur (hariç tutulmazsa `*Test.php`
   86 çıkar; 28'i vendor — gerçek 58).
3. `.ai/` gizli dizindir; `.ai/**` aramalarında `hidden: true` şarttır.
4. Betikler bu dosyada **metin** olarak verilir; çalıştırma yetkisi orkestratörde/kapanışta.
5. Ölçüm→metin çifti zorunlu: her sayımla birlikte kaynağı ve (varsa) satır aralığı yazılır.

### §1.2 Ölçüm → metin çifti tablosu

| Ölçüm | Kaynak | Sonuç | Metin |
|---|---|---|---|
| shared PHP test dosyası | `shared/tests/**/*Test.php` (vendor hariç) | 58 | §3.1 |
| shared test metodu | 58 dosya içi `public function test` + `#[Test]` | 450 | §3.1 |
| paylaşılan test dosyası | `shared/tests/**` (tüm dosyalar) | 41 | §3.1 |
| JS unit spec | `*.spec.js` (vendor/node_modules hariç) | 9 | §3.2 |
| JS `it()` | `*.spec.js` + `*.test.js` | 87 | §3.2 |
| e2e spec | `assets.../tests/e2e/*.spec.ts` | 1 (400 satır) | §3.3 |
| üretim PHP | `shared/src/**/*.php` | 165 | §2.1 |
| üretim JS | `assets.../js/**/*.js` | 95 | §2.2 |
| üretim CSS | `Css/**/*.css` | 94 | §2.3 |
| SQL şema | `.ai/.sql/mysql/*.sql` | 20 | §2.4 |
| CI job | `.github/workflows/ci.yml` | 6 (203 satır) | §5.3 |

---

## §2 — Yazılım bileşen envanteri (üretim)

### §2.1 PHP modülleri — `shared/src` (165 dosya)

| # | Modül | Dosya | Test karşılığı (§3.1'e göre) | Durum |
|---|---|---|---|---|
| 1 | Api | 34 | `tests/Api/` (10 metot) | ✅ var |
| 2 | Contracts | 19 | — | ⚠️ test yok (arayüz) |
| 3 | Events | 14 | — | ⚠️ test yok |
| 4 | OAuth | 14 | — | ⚠️ test yok |
| 5 | PageRouter | 14 | — | ⚠️ test yok |
| 6 | Middleware | 11 | `tests/Middleware/` (14 metot) | ✅ var |
| 7 | Component | 10 | — | ⚠️ test yok |
| 8 | Exception | 8 | dolaylı (ResponseNormalization) | 🟡 kısmi |
| 9 | AI | 7 | — | ⚠️ test yok |
| 10 | Security | 6 | — | ⚠️ test yok |
| 11 | Cache | 6 | — | ❌ **CacheLayer/Prefetch 0** |
| 12 | Device | 4 | `tests/Unit/Device/` (4 metot) | ✅ var |
| 13 | Session | 4 | — (middleware dolaylı) | 🟡 kısmi |
| 14 | Config | 4 | — | ⚠️ test yok |
| 15 | Database | 3 | — | ⚠️ test yok |
| 16 | Log | 3 | — | ⚠️ test yok |
| 17 | Repository | 1 | — | ⚠️ test yok |
| 18 | Bootstrap | 1 | — | ⚠️ test yok |
| 19 | Theme | 1 | — | ⚠️ test yok |
| 20 | ViewMode | 1 | — | ⚠️ test yok |
| | **Toplam** | **165** | **3 modülde doğrudan test** | — |

> Doğrudan test kapsamı yalnız Api/Middleware/Device; kalan 17 modülde doğrudan test yoktur
> (§8 boşluk B-1).

### §2.2 JS bileşenleri — `assets.coremusic.net/js` (95 dosya)

| # | Klasör | Dosya | Spec | Durum |
|---|---|---|---|---|
| 1 | components | 37 | 6 (`primitives/*.spec.js`) | 🟡 kısmi (6/37+2 harici) |
| 2 | router | 28 | 0 | ❌ boş |
| 3 | features | 7 | 0 | ❌ boş |
| 4 | core | 6 | 1 (`breakpoints.spec.js`) | 🟡 kısmi |
| 5 | managers | 5 | 0 | ❌ boş |
| 6 | coreplayer | 5 | 0 | ❌ boş |
| 7 | auth | 2 | 0 | ❌ boş |
| 8 | kök dosyalar | 5 | 0 | ❌ boş |
| | **Toplam** | **95** | **7 (js altında)** | — |

> 9 spec'in 2'si `js/` dışında: `tests/components/history.spec.js`,
> `tests/components/player-info.spec.js` (§3.2).

### §2.3 CSS katmanları — `assets.../Css` (94 dosya)

| # | Katman | Dosya | Test | Durum |
|---|---|---|---|---|
| 1 | 01_Abstracts | 19 | vitest `**/*.css` (0 hücre assertion'ı) | 🟡 taşıyıcı |
| 2 | diğer 10 katman | 75 | — | ❌ doğrudan test yok |
| | **Toplam** | **94** | — | — |

> `a-breakpoint-tokens.css` (57 satır) `breakpoints.spec.js` ile **dolaylı** korunur (§4.5).

### §2.4 SQL şema — `.ai/.sql/mysql` (20 dosya)

| # | Dosya | Tablo | İlgili test | Durum |
|---|---|---|---|---|
| 1 | `coremusic_auth.sql` | 13 tablo (298 satır) | auth PHP unit (6/36) | 🟡 kod düzeyi |
| 2 | diğer 19 | — | yok | ❌ test yok |

### §2.5 Fixture / destek bileşenleri (test ağacındaki test-olmayan PHP)

| # | Dosya | Rol | Test |
|---|---|---|---|
| 1 | `shared/tests/Fixtures/chrome/header.php` | DOM fixture verisi | 0 (veri) |
| 2 | `shared/tests/Fixtures/chrome/footer.php` | DOM fixture verisi | 0 (veri) |
| 3 | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | test desteği (SQLite) | 0 |
| 4 | `shared/tests/Repository/Sqlite/SqliteDatabaseRegistry.php` | test desteği (SQLite) | 0 |
| 5 | `auth.coremusic.net/tests/bootstrap.php` | suite kurulumu | 0 |
| 6 | `api.coremusic.net/tests/bootstrap.php` | suite kurulumu | 0 |
| 7 | `home.coremusic.net/tests/Support/FakeComponent.php` | sahte bileşen sağlayıcı | 0 |

> 45 shared PHP dosyasının 4'ü, subdomain test ağaçlarındaki 20 PHP
> dosyasının 3'ü test-olmayan destektir → gerçek test dosyası 41 + 17 = 58
> (§3.1). `Fixtures/jwt/` veri klasörüdür (dosya değil).

---

## §3 — Test bileşeni envanteri

### §3.1 PHP unit/integration (vendor hariç, gerçek 58)

| # | bileşen | Dosya | Metot | `test*` | `#[Test]` |
|---|---|---|---|---|---|
| 1 | paylaşılan (`shared/tests`) | 41 | 349 | 318 | 31 |
| 2 | auth | 6 | 36 | 36 | 0 |
| 3 | home | 8 | 33 | 33 | 0 |
| 4 | api | 3 | 32 | 32 | 0 |
| | **Toplam** | **58** | **450** | **419** | **31** |

`shared/tests` 41 dosyanın kırılımı:

| # | Dizin | Dosya | Örnek |
|---|---|---|---|
| 1 | `Middleware/` | ≥3 | CsrfMiddlewareTest, ResponseNormalizationMiddlewareTest |
| 2 | `Api/` | ≥1 | ResponseNormalizationMiddlewareTest (L7-9: `api/v1/...`) |
| 3 | `Unit/Device/` | ≥1 | DeviceDetectorTest (L17: `sm-g960f`) |
| 4 | `Unit/` (diğer) | çoğunluk | — |
| 5 | **`Unit/Application/`** | **0** | ❌ özet iddiası yanlış (kapı G3) |
| 6 | **`Integration/`** | **0** | ❌ 2026-09-29'da kaldırıldı (kapı G4) |

### §3.2 JS unit (9 spec / 87 `it()`)

| # | Spec | Grup | `it()` |
|---|---|---|---|
| 1 | `primitives/BadgeComponent.spec.js` | C10 primitive | 9 |
| 2 | `primitives/ButtonComponent.spec.js` | C10 primitive | çoğunluk |
| 3 | `primitives/InputComponent.spec.js` | C10 primitive | çoğunluk |
| 4 | `primitives/SkeletonComponent.spec.js` | C10 primitive | çoğunluk |
| 5 | `primitives/SliderComponent.spec.js` | C10 primitive | çoğunluk |
| 6 | `primitives/ToggleComponent.spec.js` | C10 primitive | çoğunluk |
| 7 | `core/breakpoints.spec.js` | device breakpoints | — |
| 8 | `tests/components/history.spec.js` | player history | — |
| 9 | `tests/components/player-info.spec.js` | home player info | — |
| | **Toplam** | — | **87** (spec+test.js) |

### §3.3 Playwright e2e (1 spec / 18 `test()`)

| # | Spec | Satır | `test()` |
|---|---|---|---|
| 1 | `tests/e2e/player-info.spec.ts` | 400 | 18 |

### §3.4 Subdomain test dosyaları (dosya · metot)

| # | auth (6 · 36) | # | home (8 · 33) | # | api (3 · 32) |
|---|---|---|---|---|---|
| 1 | `Unit/Domain/DTO/LoginRequestTest` · 5 | 1 | `Unit/Bootstrap/EntryPointCacheSafetyTest` · 3 | 1 | `Unit/AuthControllerTest` · **18** |
| 2 | `Unit/Domain/Entity/UserTest` · 4 | 2 | `Unit/Component/HomeSongButtonTest` · 5 | 2 | `Unit/RouteConfigTest` · 12 |
| 3 | `Unit/Domain/ValueObject/EmailTest` · 8 | 3 | `Unit/Component/RecentTracksComponentTest` · 6 | 3 | `Unit/SmokeTest` · 2 |
| 4 | `Unit/Domain/ValueObject/GenderTest` · 8 | 4 | `Unit/Layout/HomeLayoutVariantTest` · 5 | — | — |
| 5 | `Unit/Domain/ValueObject/PasswordTest` · 6 | 5 | `Unit/Loader/ComponentLoaderTest` · 7 | — | — |
| 6 | `Unit/Service/SessionManagerTest` · 5 | 6 | `Unit/Page/HistoryPageTest` · 2 | — | — |
| — | — | 7 | `Unit/Page/HomeChromeSmokeTest` · 2 | — | — |
| — | — | 8 | `Unit/Page/NowPlayingPageTest` · 3 | — | — |

### §3.5 `shared/tests` grup toplamları (41 test / 349 metot)

| # | Grup | Dosya | Metot | Bağlı üretim modülü (§2.1) |
|---|---|---:|---:|---|
| 1 | `Api/` | 8 | 67 | Api (34 dosya) |
| 2 | `Middleware/` | 9 | 76 | Middleware (11 dosya) |
| 3 | `Unit/` | 16 | 113 | Config/Device/Log/PageRouter/Security/Theme (13 dosya) |
| 4 | `Security/` | 2 | 40 | Security (6 dosya) — **yalnız 2 test** |
| 5 | `Repository/` | 2 | 25 | Repository (1 dosya) + Database (3) |
| 6 | `Events/` | 2 | 15 | Events (14 dosya) — **2/14** |
| 7 | `OAuth/` | 1 | 8 | OAuth (14 dosya) — **1/14** |
| 8 | `Component/` | 1 | 5 | Component (10 dosya) — **1/10** |
| — | **Toplam** | **41** | **349** | — |

> En dikkatsiz oranlar: OAuth 1/14 · Component 1/10 · Events 2/14 ·
> Security 2/6 (§8 B-1'in dosya düzeyi karşılığı).

---

## §4 — Bileşen↔akış iz düşümü (trace)

> Her satır: kullanıcı akışını üreten bileşen → onu koruyan test → doğrulanan davranış.

### §4.1 PHP akışları

| # | Akış (vault) | Üretim bileşeni | Test bileşeni | Doğrulanan |
|---|---|---|---|---|
| 1 | set-gender POST | `shared/src/Middleware/CsrfMiddleware` (11 middleware) | `shared/tests/Middleware/CsrfMiddlewareTest` (350 satır) | 403/csrf-mismatch, yanlış method, geçerli token, API exempt, bozuk header, X-Forwarded-For, tr-TR cookie path |
| 2 | 304 / ETag / favicon | `shared/src/Api/` + middleware | `shared/tests/Api/ResponseNormalizationMiddlewareTest` (108 satır) | 304 döngü riski (L7-9 URL), camel/snake, `meta.success`, `Cache-Control: public, max-age=300` |
| 3 | cihaz algılama (device-loader beslemesi) | `shared/src/Device/DeviceDetector` | `shared/tests/Unit/Device/DeviceDetectorTest` | model/UA sınıflandırma |
| 4 | session/token (auth) | `shared/src/Session/` + `auth.coremusic.net` | auth 6/36 test | auth birimi |
| 5 | OAuth authorizasyon kodu | `shared/src/OAuth/` (14 dosya) | **yok** | ❌ §8 B-1 |
| 6 | cache/prefetch | `shared/src/Cache/` (6 dosya) | **yok** | ❌ §8 B-2 |

### §4.2 JS/TS akışları

| # | Akış (vault) | Üretim bileşeni | Test bileşeni | Doğrulanan |
|---|---|---|---|---|
| 7 | C10 primitive render | `js/components/primitives/*` (37) | 6 spec | render/state (Badge L14-16 `textContent`) |
| 8 | breakpoint taşıma (1024/1920) | `js/core/breakpoints` + `Css/01_Abstracts/a-breakpoint-tokens.css` | `breakpoints.spec.js` | breakpoint değeri eşleşmesi |
| 9 | player bilgi kutusu | `tests/e2e/player-info.spec.ts` (18 test) + `js/components/player-info` | e2e + `player-info.spec.js` | kutu davranışı uçtan uca |
| 10 | oynatma geçmişi | `js/` history bileşeni | `history.spec.js` | birim düzey |
| 11 | PWA router (4 sayfa SPA) | `js/router/` (28 dosya) | **yok** | ❌ §8 B-3 |
| 12 | player managers/coreplayer | `js/managers/` (5) + `js/coreplayer/` (5) | **yok** | ❌ §8 B-3 |

### §4.3 CSS/DB akışları

| # | Akış | Üretim bileşeni | Test | Durum |
|---|---|---|---|---|
| 13 | K002 breakpoint token | `Css/01_Abstracts/a-breakpoint-tokens.css` (57) | `breakpoints.spec.js` | 🟡 dolaylı |
| 14 | auth şema (13 tablo) | `.ai/.sql/mysql/coremusic_auth.sql` | auth unit kod testi | 🟡 şema doğrulaması yok §8 B-4 |
| 15 | C11 css tokens | `Css/01_Abstracts/*` (19) | vitest include `**/*.css` | 🟡 import-only |

### §4.4 Orphan test bileşeni (akışı olmayan)

| # | Test | Eşleşen akış | Not |
|---|---|---|---|
| 1 | — | — | 9 JS spec + 58 PHP test tamamı §4.1-4.3'e bağlandı; orphan yok |

### §4.5 Kapsama geri izi

| bileşen katmanı | iz düşümü | kapsanan akış |
|---|---|---|
| Middleware (11) | 14 metot | 1 (set-gender CSRF) |
| Api (34) | 10 metot | 1 (304/ETag) |
| Device (4) | 4 metot | 1 (cihaz) |
| JS primitives | 6 spec | 1 (C10) |
| CSS | taşıcı | 1 (breakpoint) |

---

## §5 — Bağımlılık grafiği

### §5.1 PHP test yığını

```text
shared/composer.json
  ├─ require-dev: phpunit/phpunit ^10.5 (L24)
  ├─ autoload-dev: tests→Tests\ (L47-48)
  └─ config.allow-plugins: php-http/discovery (L34)
        │
        ▼
  shared/phpunit.xml ─── 8 suite (L8-33) + source (L34-38, coverage raporu YOK)
        │                 ├─ Unit → tests/Unit
        │                 ├─ Middleware → tests/Middleware (DOĞRULANDI)
        │                 ├─ Feature → tests/Feature
        │                 └─ ...
        ▼
  58 test dosyası / 450 metot (vendor hariç)
        ▲
auth/home/api/phpunit.xml (birim suite'li) ── 17 dosya / 101 metot
```

### §5.2 JS test yığını

```text
kök package.json (18 satır)
  ├─ test:js → vitest run (L5)
  ├─ test:js:coverage → vitest run --coverage (L6)
  └─ test:e2e → playwright test (L7)
        │
        ├─▶ vitest.config.js (33 satır)
        │     ├─ thresholds 80/80/80/80 (L25-28)
        │     └─ include: js/**/*.{spec,test}.js + **/*.css (L30)
        │           ▼
        │     9 spec.js / 87 it()
        │
        └─▶ playwright.config.ts (56 satır)
              ├─ retries 2 / workers 1 (CI, L20-21)
              ├─ webServer: php -S localhost:8000 (L46-47)
              └─ 1 spec.ts / 18 test()
```

### §5.3 CI grafiği — `.github/workflows/ci.yml` (203 satır / 6 job)

```text
on: [push, pull_request] (L6-7)
├─ job security-scan ── trivial joblar: true (L167, L183)
├─ job php-test ── L80 --testsuite Unit ── phpunit.xml (suite'te Dependency/Integration YOK)
├─ job js-test ── coverage flag yok (L104-107)
├─ job e2e-test ── playwright + php -S (config L46-47)
├─ job docker-build
└─ job deploy (workflow_dispatch)
```

### §5.4 Kesişim grafiği (test ↔ kapsam)

```text
PHP: composer → phpunit(10.5) → 4 phpunit.xml → 58 test  → 3 üretim modülü (Api/Middleware/Device)
JS : package.json → vitest(^5.0.3) → vitest.config → 9 spec → primitives/core (+2 harici)
E2E: package.json → playwright(^1.63.0) → playwright.config → 1 spec → home player
CI : ci.yml → 6 job → yukarıdaki üç dal (PHP coverage flag YOK)
```

### §5.5 Bağımlılık notları ve kırılma riskleri

| # | Ok | Risk | Kanıt |
|---|---|---|---|
| 1 | `ci.yml` php-test matrix → **yalnız** shared + auth | home (33) + api (32) testleri **CI'da hiç koşulmuyor** → 65 metot CI dışı | `ci.yml:73-80` (`project: shared`, `project: auth.coremusic.net`) |
| 2 | `ci.yml` L5 header "phpunit.xml x2" | disk 4 → doküman-üretim bağımlılığı stale (okuyan herkes yanılgıya düşer) | `ci.yml:5` vs disk |
| 3 | `composer test` (shared) → `phpunit` | runner sürümü `^10.5`; havuz 11.5/12.5 reçeteleri birebir uygulanamaz | `composer.json:24` |
| 4 | vitest threshold 80 ×4 → 9 spec | eşik erişilemezse job zaten `continue-on-error` → **kör nokta** | `vitest.config.js:25-28` + `ci.yml:167` |
| 5 | playwright `webServer` → `php -S` (home) | e2e yalnız `home` köküne bağlanır; media/auth e2e'si fiziksel olarak bağlanamaz | `playwright.config.ts:46-47` |
| 6 | `phpunit.xml` → `tests/` (suite→dizin) | `Unit/Application` ve `tests/Integration` dizinleri yok → suite eklense bile `exit 2` | auth `phpunit.xml:10-12` |
| 7 | 4 phpunit.xml → 4 repo | ~~home/api xml okunmadı~~ **çözüldü (2026-10-15):** 4/4 okundu — bootstrap/suite/coverage bilgisi §6.1'de | `dogrulama.md` §5.3 |

---

## §6 — Konfigürasyon bileşenleri

| # | Dosya | Satır | Rol | Kritik ayar (kanıt) |
|---|---|---|---|---|
| 1 | `shared/phpunit.xml` | 39 | paylaşılan suite | 8 suite L8-33 · bootstrap L4 · `<coverage>` raporu YOK, `<source>` L34-38 |
| 2 | `auth.coremusic.net/phpunit.xml` | 34 | auth suite | failOnRisky/failOnWarning L6-7; Integration KALDIRILDI L10-12; coverage text stdout L26-31 |
| 3 | `home.coremusic.net/phpunit.xml` | 18 | home suite | bootstrap L4 `vendor/autoload` · 1 suite Unit (L9-11) · failOn YOK · coverage bloğu YOK · `<source>` `include` L13-17 |
| 4 | `api.coremusic.net/phpunit.xml` | 29 | api suite | bootstrap L4 `tests/bootstrap` · failOnRisky/Warning L6-7 (+`failOnPhpunitWarning=false` L8) · 1 suite Unit L11-13 · coverage text stdout L23-27 |
| 5 | `assets.../vitest.config.js` | 33 | JS unit | threshold 80×4 L25-28; include L30 |
| 6 | `assets.../playwright.config.ts` | 56 | e2e | retries/workers L20-21; webServer L46-47 |
| 7 | `.github/workflows/ci.yml` | 203 | CI orkestrasyon | Unit-only L80; coverage flag YOK L104-107; node 26 L151; soft L167/183 |

### §6.1 Konfigürasyon bağımlılık okları

1. `composer.json` → `phpunit.xml` (runner + suite).
2. `package.json` → `vitest.config.js` (threshold/include).
3. `package.json` → `playwright.config.ts` (e2e + webServer).
4. `ci.yml` → 4 phpunit.xml + 2 JS config (job başına çağrı).
5. `phpunit.xml` → `tests/**` (suite→dizin).
6. `vitest.config.include` → 9 spec + 94 css.

### §6.2 4 phpunit.xml karşılaştırması (gözlem — 4/4 tam okuma, 2026-10-15)

| # | Eksen | shared (39 satır) | auth (34) | home (18) | api (29) |
|---|---|---|---|---|---|
| 1 | Bootstrap | `vendor/autoload` L4 | `tests/bootstrap` L4 | `vendor/autoload` L4 | `tests/bootstrap` L4 |
| 2 | Suite sayısı | **8** (L8-33) | 1 (Unit) | 1 (Unit) | 1 (Unit) |
| 3 | `failOnRisky/Warning` | yok | **L6-7 true** | yok | **L6-7 true** (+L8 `failOnPhpunitWarning=false`) |
| 4 | `<coverage>` raporu | yok | text stdout L26-31 | yok | text stdout L23-27 |
| 5 | `<source>` include | `src` (L34-38) | önceden okundu (L26-31 coverage ile birlikte) | `include` L13-17 | `include` L16-21 (PHPUnit 10 notu L16) |
| 6 | `cacheDirectory` | `.phpunit.cache` L7 | — (bu tur okunmadı) | `.phpunit.cache` L7 | yok |

**Gözlemler:**

1. Hepsi PHPUnit 10 `<source>` idiomundadır; api L16 yorumu bunu açıkça
   yazar: *"Kaynak seçimi: PHPUnit 10+ için doğru element `<source>`'tir."*
2. Suite genişliği uçurumu: shared 8 suite → CI'da `PHPUNIT_ARGS` boşken
   **8'inin tamamı** koşulur (matrix satırında args yok, ci.yml L75); auth
   tek suite olduğu için `--testsuite Unit` bayrağı (L80) yalnız auth'da
   anlam taşır.
3. Strictlik 2/4'te: auth + api `failOn*` açar; shared + home açmaz →
   risky/warning davranışı BC'ler arasında **farklıdır** (tutarlılık işi —
   `web-arastirma.md` §3.2.3 satır 3'ün tamamlayıcısı).
4. Coverage raporu 2/4'te tanımlı (auth, api); fiili coverage 4/4'te **yok**
   çünkü hiçbir koşum coverage bayrağı çalıştırmaz (ci.yml L104-107 → G7).
5. `home/phpunit.xml` yalnız 18 satır — en minimal yapı; bootstrap'i
   `vendor/autoload` (shared ile aynı tip), kaynak hedefi `include`.

---

## §7 — Kapsama matrisi (bileşen × test tipi)

| # | bileşen katmanı | PHP unit | JS unit | e2e | coverage bayrağı | hücre durumu |
|---|---|---|---|---|---|---|
| 1 | shared/src/Api | ✅ | — | — | ❌ (CI L104-107) | 🟡 |
| 2 | shared/src/Middleware | ✅ | — | — | ❌ | 🟡 |
| 3 | shared/src/Device | ✅ | — | — | ❌ | 🟡 |
| 4 | shared/src/Cache | ❌ | — | — | ❌ | ❌ |
| 5 | shared/src/OAuth | ❌ | — | — | ❌ | ❌ |
| 6 | shared/src/Router+PageRouter | ❌ | — | — | ❌ | ❌ |
| 7 | js/components/primitives | — | ✅ | ❌ | 🟡 (config var) | 🟡 |
| 8 | js/router | — | ❌ | ❌ | 🟡 | ❌ |
| 9 | js/coreplayer+managers | — | ❌ | 🟡 (e2e tek spec) | 🟡 | 🟡 |
| 10 | Css/01_Abstracts | — | 🟡 (css include) | — | 🟡 | 🟡 |
| 11 | SQL şema | ❌ | — | — | — | ❌ |
| 12 | media/apiKeys | ❌ | — | — | — | ❌ |

### §7.1 Matris okuma notları

1. Hücre işaretleri: ✅ fiilen var · 🟡 kısmi/taşıyıcı · ❌ yok.
2. `coverage bayrağı` sütunu **CI seviyesindedir** (repo lokalinde `composer
   test` coverage üretmez — `dogrulama.md` §5.1); yerel koşum için ayrıca
   `--coverage` gerekir.
3. `js/router` (28 dosya) hiçbir hücrede ✅ görmüyor → §5.5-4 kör noktası ile
   birlikte en riskli tek katman (B-3).
4. `media` ve `cms` subdomain'leri bu matrise **girmez**: media'da phpunit
   yok (B-10), `cms.coremusic.net` diskte yok — matris 4 BC + assets'e kadardır.
5. Matris hücreleri dosya varlığına dayanır; **test geçmişi** iddia etmez
   (koşum yok — `dogrulama.md` §1.1 madde 4).

---

## §8 — Boşluk bileşenleri

| # | Boşluk | Kanıt | Etkilenen bileşen | Öncelik |
|---|---|---|---|---|
| B-1 | 17 PHP modülünde doğrudan test yok (§2.1) | envanter | OAuth/PageRouter/Security/Cache/... | yüksek |
| B-2 | Cache bileşeni 6 dosya / 0 test | `CacheLayer|Prefetch` 0 | `shared/src/Cache` | yüksek |
| B-3 | JS router(28)/managers(5)/coreplayer(5)/features(7) 0 spec | spec listesi §3.2 | SPA navigasyonu + player | yüksek |
| B-4 | SQL şema testi yok | `.ai/.sql/mysql` 20 dosya, test 0 | 13 auth tablosu | orta |
| B-5 | `tests/Integration` yok | auth phpunit.xml L10-12 | integration bileşeni | yüksek |
| B-6 | CI'da PHP coverage flag yok | ci.yml L104-107 | 4 phpunit.xml coverage | yüksek |
| B-7 | Threshold 80 ile fiili bilinmiyor | vitest.config L25-28 | 9 spec | orta |
| B-8 | `Unit/Application` dizini yok | disk 0 | özet "32 test" iddiası | yüksek |
| B-9 | Vary/Cookie header testi 0 | grep 0 | ResponseNormalization | orta |
| B-10 | media subdomain'te phpunit yok | composer grep 0 | media | orta |
| B-11 | e2e tek spec (18 test) | disk 1 spec.ts | çok-sayfa akışlar | orta |
| B-12 | contract test yok (API spec) | envanter 0 | `shared/src/Api` 34 dosya | orta |
| B-13 | home+api testleri CI matrix'te yok | `ci.yml:73-80` → yalnız shared+auth | 11 dosya / 65 metot | yüksek |

### §8.1 Boşluk→aksiyon eşlemesi (PLANNED)

1. B-1/B-2 → §10 P-1 (Cache unit).
2. B-3 → §10 P-6 (router spec).
3. B-5/B-6 → §10 P-3, P-4 (integration + coverage).
4. B-8 → dogrulama.md kapı G3 (blok).
5. B-9 → §10 P-8 (Vary/Cookie).
6. B-13 → §10 P-11 (CI matrix'e home+api eklenmesi).

---

## §9 — Envanter betikleri (metin; çalıştırma yok)

> Bu görevde `.ai/scripts/*.ps1` çalıştırma ve betik icrası yasağından dolayı betikler
> yalnızca metindir; sonuç sütunu bu oturumun gözlemsel ölçümüdür.

| # | Amaç | Betik (PowerShell, tek satır) | Beklenen çıktı (2026-10-15) |
|---|---|---|---|
| 1 | gerçek PHP test dosyası | `Get-ChildItem shared,auth.coremusic.net,home.coremusic.net,api.coremusic.net -Recurse -File -Filter *Test.php \| Where-Object { $_.FullName -notlike '*\vendor\*' }` | 58 |
| 2 | shared test metodu | `Select-String -Path 'shared\tests\*.php' -Pattern 'public function test\|#\[Test\]'` | 349 |
| 3 | DataProvider | `Select-String -Path 'shared\tests\*.php' -Pattern 'DataProvider'` | 0 |
| 4 | spec.js | `Get-ChildItem -Recurse -File -Filter *.spec.js \| Where-Object { $_.FullName -notlike '*node_modules*' -and $_.FullName -notlike '*\vendor\*' }` | 9 |
| 5 | `it()` | `Select-String -Path 'shared\tests\*.spec.js' -Pattern '\bit\('` | 87 |
| 6 | PHP modül dosyaları | `Get-ChildItem shared\src -Directory \| ForEach-Object { (Get-ChildItem $_.FullName -Recurse -File -Filter *.php).Count }` | §2.1 sütunu |
| 7 | threshold | `Select-String -Path 'assets.coremusic.net\vitest.config.js' -Pattern 'thresholds'` | L25-28 (80×4) |
| 8 | coverage flag | `Select-String -Path '.github\workflows\ci.yml' -Pattern 'coverage\|phpunit --coverage'` | L104-107 (flag yok) |

---

## §10 — PLANNED bileşenler

| # | Bileşen | Kapatığı | Grup |
|---|---|---|---|
| P-1 | `shared/tests/Cache/CacheLayerTest` | B-2 | mimari |
| P-2 | `shared/tests/Unit/Application/` dizini + ilk 2 test | B-8 | mimari |
| P-3 | `tests/Integration/` + auth phpunit.xml satır 10-12'nin iadesi | B-5 | kod |
| P-4 | ci.yml php-test job'a `--coverage-php` bayrağı | B-6 | mimari |
| P-5 | vitest `--coverage` fiili raporu + threshold doğrulaması | B-7 | kanıt |
| P-6 | `js/router/*.spec.js` (28 dosyalık katman) | B-3 | kod |
| P-7 | `tests/e2e/` ikinci spec (settings/queue akışı) | B-11 | kod |
| P-8 | ResponseNormalization Vary/Cookie assertion | B-9 | kod |
| P-9 | media phpunit bootstrap | B-10 | mimari |
| P-10 | API contract test (OpenAPI↔yanıt) | B-12 | mimari |
| P-11 | `ci.yml` php-test matrix'e `home.coremusic.net` + `api.coremusic.net` (DevOps onayı) | B-13 | mimari |

---

## §11 — Kaynaklar

| # | Dosya | Ne verdi |
|---|---|---|
| 1 | `shared/src/**` (165) | §2.1 modül sayıları |
| 2 | `assets.coremusic.net/js/**` (95) | §2.2 JS sayıları |
| 3 | `assets.coremusic.net/Css/**` (94) | §2.3 katmanlar |
| 4 | `.ai/.sql/mysql/*.sql` (20) | §2.4 SQL |
| 5 | 58 PHP test + 9 spec + 1 e2e | §3 |
| 6 | `shared/phpunit.xml`, `auth.../phpunit.xml` | §6.1-6.2 |
| 7 | `vitest.config.js`, `playwright.config.ts` | §5.2, §6.5-6.6 |
| 8 | `.github/workflows/ci.yml` (203) | §5.3, §6.7 |
| 9 | `shared/composer.json`, kök `package.json` | §5.1-5.2 |
| 10 | `CsrfMiddlewareTest`, `ResponseNormalizationMiddlewareTest`, `DeviceDetectorTest`, 6 primitive spec, `player-info.spec.ts` | §4 iz düşümleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
