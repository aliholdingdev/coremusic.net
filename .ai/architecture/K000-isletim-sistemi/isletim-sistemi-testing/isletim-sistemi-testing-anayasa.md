---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-kapsam
evidence:
  - "shared/phpunit.xml:1-39"
  - "shared/composer.json:1-50"
  - ".github/workflows/ci.yml:1-203"
  - "shared/tests/Unit/PageRouter/RouteRegistryTest.php:1-64"
validation:
  - "2026-10-13 · resmi doküman URL'leri webfetch ile HTTP 200 doğrulandı (docs.kernel.org kselftest, kunit usage; docs.phpunit.de 11.5 configuration)"
  - "2026-10-13 · repo satır kanıtları Read ile alındı; T1/T2 UNKNOWN korundu, coverage rakamı yazılmadı"
open_questions:
  - "T1: OS/çekirdek test (KUnit/kselftest) hedef mimaride kapsam mı, yalnız referans mı? [UNKNOWN]"
  - "T2: Coverage hedefi/eşiği yok — rakam uydurma yasak [UNKNOWN]"
group: mimari
---

# isletim-sistemi-testing Anayasası — Test Seviyeleri ve Strateji

## §0 ELI15

Test, kodun "doğru olduğunu" kanıtlayan değil, **yanlış olduğunu arayan** disiplindir.
Bu anayasa dört soruyu yanıtlar: (1) **neyi** hangi seviyede test ederiz, (2) **hangi
araçla** koşarız, (3) **kapıdan** ne geçer, (4) **başarı** hangi ölçümle anlaşılır.
Bu dosya yalnızca ilkeleri ve seviye sınırlarını kurar; komut akışı
`isletim-sistemi-testing-surucu.md`'de, riskler `-riskler.md`'de, kanıt
`-web-arastirma.md` ve `-kod-ornekleri.md`'dedir. Repo'da hâlâ çalışan bir test
altyapısı vardır (PHPUnit + Vitest + Playwright); **çekirdek/OS düzeyi test ise
repoda yoktur** ve bu dosyada yalnız hedef referans olarak taşınır.

## §1 Test seviyeleri (bu repoda karşılığı olan katman)

| Seviye | Ne test eder | Bu repoda karşılığı | Araç (disk kanıtı) | Durum |
|---|---|---|---|---|
| L0 Çekirdek/OS | Çekirdek alt sistemleri, sürücüler | Yok | KUnit · kselftest (hedef referans) | Repoda 0 isabet — negatif kanıt |
| L1 Birim | Tek sınıf/servis davranışı | `shared/tests/` altındaki Unit dizinleri | PHPUnit ^10.5 (`shared/composer.json:24`) | IMPLEMENTED |
| L2 Bileşen | Middleware zinciri, event akışı | `shared/tests/Middleware/`, `shared/tests/Events/` | PHPUnit (aynı binary) | IMPLEMENTED |
| L3 Uygulama/E2E | Tarayıcı akışı | JS tarafı | Vitest + Playwright (`ci.yml:158`, `ci.yml:203`) | IMPLEMENTED (e2e soft kapı) |
| L4 CI kapısı | Statik analiz + bağımlılık audit + secret | `ci.yml` 6 job | PHPStan · composer audit · GitLeaks | IMPLEMENTED |

> Kural: bir seviye, üst seviyenin yerine **geçemez**. PHPUnit yeşilliği L4'ü, L4
> yeşilliği L0'ı kanıtlamaz. L0 bu depoda temsil edilmediği için "L0 yeşil"
> iddiası **üretilemez** — bu bilinçli bir boşluktur (kapsam.md §A.2).

## §2 OS testi ≠ uygulama testi (ayrım matrisi)

| Boyut | OS/çekirdek testi (L0) | Uygulama testi (L1-L3) |
|---|---|---|
| Hedef | Çekirdek alt sistemi, sürücü, syscall | PHP servisi, middleware, JS akışı |
| Koşum ortamı | Boot edilmiş kernel (QEMU/Sandbox) veya çekirdek modülü | Paylaşılan PHP süreci / Node süreci |
| Başarısızlık etkisi | Makine çöker; test ortamı da çökebilir | Süreç döner, CI job'ı kırmızı olur |
| İzolasyon | Boot + donanım (ya da emülasyon) | `setUp`/`tearDown` ve suite seçimi |
| Örnek araç | `kselftest` harness (`docs.kernel.org/dev-tools/kselftest.html`) | PHPUnit (`docs.phpunit.de`) |
| Bu depodaki kanıt | 0 isabet (`kunit`, `kselftest` — kapsam.md §F.1 negatif kanıt) | 44 PHP test dosyası (`shared/tests/**/*.php` glob) |

> Bu ayrım zorunludur: "testler geçiyor" cümlesi bu depoda **yalnızca** L1-L4 için
> anlam taşır. L0 için kanıt yoktur; T1 açık sorusu bu yüzden kapatılmaz.

## §3 Strateji ilkeleri

| # | İlke | Gerekçe | Kanıt/dayanak |
|---|---|---|---|
| S-1 | Test, kaynak koduyla **aynı depoda**, kaynak kodun yanında yaşar | Ayrık test deposu sürüklenme üretir | `shared/tests/` ile `shared/src/` komşu |
| S-2 | Her test **tek bir davranışı** doğrular | Çok-amaçlı test belirsiz hata verir | `RouteRegistryTest` 5 test / 5 senaryo (satır 11-63) |
| S-3 | Test bağımsızdır: sıraya ve global duruma bağlı değildir | Sıra-bağımlılık flaky üretir | `CsrfMiddlewareTest` setUp/tearDown `$_SESSION` sıfırlar (satır 18-26) |
| S-4 | Test kodu production kodu kadar denetlenir (strict types) | Test kodu da hata üretir | `declare(strict_types=1)` her test dosyasının 1. satırı |
| S-5 | Kapılar **bağımlılık zinciriyle** sıralanır: lint → test | Bozuk kodda test koşmanın değeri yok | `ci.yml:68` `needs: php-lint` |
| S-6 | Soft kapı (`continue-on-error`) yalnız **geçiş döneminde** kullanılır ve görünür kırmızıdır | Sessiz yeşil, eşiği saklar | `ci.yml:167` ve `ci.yml:183` |
| S-7 | Statik analiz testin yerine **geçmez**, tamamlayıcıdır | Test davranışı, analiz kod şeklini doğrular | PHPStan `--level=5` (`composer.json:48`) ayrı job |
| S-8 | Bağımlılık auditi (supply chain) bir test kapısıdır | Bilinen CVE'li paket testi geçebilir | `composer audit` (`ci.yml:126,132,138`) |
| S-9 | Secret taraması ayrı workflow'dur, test job'ına **gömülmez** | Ayrı sahiplik + ayrı eskalasyon | `secret-scan.yml:19-31` |
| S-10 | Ölçüm yoksa hedef yoktur; eşiği olmayan kapı **rapor** kapısıdır | Eşiksiz kapı motordur | js-coverage soft + eşik `ci.yml:165-167` notu |

## §4 PHPUnit yapılandırması (repo gerçeği × resmi doküman)

| Yapı | Repo kanıtı | Resmi karşılığı |
|---|---|---|
| Sürücü dosya | `shared/phpunit.xml` (39 satır) | XML yapılandırma dosyası |
| Bootstrap | `shared/phpunit.xml:4` | `bootstrap` niteliği |
| Erken durma | `stopOnFailure=false` (`phpunit.xml:6`) — varsayılanla aynı | `stopOnFailure` niteliği (varsayılan `false`) |
| Önbellek | `cacheDirectory` (`phpunit.xml:7`) | `cacheDirectory` niteliği |
| Test dizini | 8 `<testsuite>` (`phpunit.xml:8-33`) | `<testsuites>` / `<testsuite>` elemanları |
| Kaynak sınırı | `<source>` (`phpunit.xml:34-38`) | `<source>` elemanı (kapsama ve uyarı raporlamasını sınırlandırır) |
| Kapsama yapılandırması | **YOK** — `<coverage>` bloğu repoda yok | `<coverage>` elemanı (rapor formatları) |

> Son satırdaki YOK'luk, T2 ile bağlantılıdır: `<source>` tanımlı olsa bile
> kapsama **raporu** yapılandırılmamıştır; "coverage %X" iddiası bu depodan
> **çıkarılamaz**.

## §5 Statik analiz ve bağımlılık audit kapıları

| Kapı | Komut | Satır kanıtı | Ne doğrular |
|---|---|---|---|
| Statik analiz | `composer stan` | `composer.json:48` → `phpstan analyse src --level=5`; `ci.yml:62-64` | Tip ve statik hata sınıfı |
| Bağımlılık auditi | `composer audit` | `ci.yml:126`, `ci.yml:132`, `ci.yml:138` | Bilinen güvenlik advisory'leri |
| Secret tarama | `gitleaks/gitleaks-action@v2` | `secret-scan.yml:29` | Sızdırılmış anahtar desenleri |

Kapıların **üçü de** test job'undan **bağımsız** job'lardır; biri diğerinin
önkoşulu değildir (iş parçacığı paralelliği korunur — surucu.md §3).

## §6 JS test katmanı (ci.yml kanıtı)

| Job | Adım | Satır | Not |
|---|---|---|---|
| `js-test` | `npm run test:js` | `ci.yml:158` | Vitest unit |
| `js-coverage` | `npm run test:js:coverage` | `ci.yml:176` | `needs: js-test` (`ci.yml:162`) + `continue-on-error: true` (`ci.yml:167`) |
| `js-e2e` | `npm run test:e2e` | `ci.yml:203` | Playwright; `continue-on-error: true` (`ci.yml:183`) |

`js-coverage` job'ının kendi yorum satırı eşiğin neden kurulmadığını belgeler:
"%80 eşiği şimdilik erişilemez (12.9k satır / <100 test) — job GÖRÜNÜR-KIRMIZI
olsun ama bloklamasın" (`ci.yml:165-166`). Bu, S-6 ilkesinin birebir uygulamasıdır.

## §7 Ölçüm, kapsam ve T2

| Soru | Durum | Dayanak |
|---|---|---|
| PHP kapsama yüzdesi ölçülüyor mu? | Hayır — `<coverage>` bloğu yok | `shared/phpunit.xml` 39 satır (tüm dosya okundu) |
| JS kapsama eşiği var mı? | Eşik **yok**; job soft | `ci.yml:161`, `ci.yml:165-167` |
| Kapsama hedefi (T2) | `UNKNOWN` — kullanıcı kararı gerekir | kapsam.md §F.2 |
| OS test kapsamı (T1) | `UNKNOWN` — L0 bu depoda temsil edilmiyor | kapsam.md §F.1 |

Kural: eşiği olmayan ölçüm **sadece bilgidir**, kapıdır sayılmaz. T2 kapatılmadan
"coverage gate aktif" denemez.

## §8 Repo disk kanıtları

| Dosya | Kapsam (okunan satır) | Ne kanıtlar |
|---|---|---|
| `shared/phpunit.xml` | 1-39 (tam) | 8 testsuite · bootstrap · `<source>` · `<coverage>` YOK |
| `shared/composer.json` | 1-50 (tam) | `phpunit/phpunit ^10.5` (L24) · `phpstan/phpstan ^1.10` (L25) · scripts (L46-49) |
| `.github/workflows/ci.yml` | 1-203 (tam) | 6 job · `needs` · matrix · soft kapılar · süreler |
| `.github/workflows/secret-scan.yml` | 1-31 (tam) | GitLeaks job · `fetch-depth: 0` · `.gitleaks.toml` YOK (L4 notu) |
| `shared/tests/**/*.php` | glob (44 dosya) | L1/L2 test envanteri |
| `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | 1-64 (tam) | Birim test kalıbı (S-2) |

## §9 Riskler (özet — tamamı `-riskler.md`'dedir)

| # | Risk | Sınıf |
|---|---|---|
| R-1 | Soft kapıların kalıcı hâle gelmesi (eşiksiz yeşil) | Kapı riski |
| R-2 | Test bağımlılığı (global state, sıra) | Flaky riski |
| R-3 | Assertion'sız / kendi kendini doğrulayan test | Yanlış-negatif riski |
| R-4 | Kapsama rakamı uydurma (T2 `UNKNOWN` iken) | Doğruluk riski |
| R-5 | OS testi varmış gibi sunmak (repoda 0 isabet) | Doğruluk riski |

## §10 Kapsam dışı

| Konu | Sınır | Neden |
|---|---|---|
| Uygulama testi içeriği (PHPUnit desen detayı) | `K019-testing` | Faz-A teslimi (kapsam.md §E.1) |
| Sürücü/çekirdek implementasyonu | `isletim-sistemi-kernel` | Kod sahipliği farklı dalda |
| Workflow sahipliği ve deploy onayı | `K018-devops` + `.github/AGENTS.md` §3.1 | Yml sahibi DevOps'tur |
| K019 test altyapısının kendisi | `K18-testing` | Bu dal test **stratejisini** kurar, framework kurmaz |

## §11 Kaynakça (2026-10-13 · HTTP 200 · ayrıntı `-web-arastirma.md` §2-§3)

| İddia | URL | Doğrulama |
|---|---|---|
| Kullanıcı uzayı selftest çerçevesi (L0 hedef referans) | https://docs.kernel.org/dev-tools/kselftest.html | 2026-10-13 webfetch 200 |
| KUnit test yazımı (L0 hedef referans) | https://docs.kernel.org/dev-tools/kunit/usage.html | 2026-10-13 webfetch 200 · kardeş W-02 |
| PHPUnit XML yapılandırması | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 webfetch 200 · kardeş W-39 |

## §12 Eke — seviye × araç × ortam matrisi

| Seviye | Araç | Ortam | Job (ci.yml) | Paralel mi |
|---|---|---|---|---|
| L0 | KUnit / kselftest | Boot edilmiş kernel | Repoda job yok | Hayır (temsil edilmiyor) |
| L1 | PHPUnit | ubuntu-latest + PHP 8.4 | `php-test` | Evet (matrix) |
| L2 | PHPUnit | aynı job | `php-test` | Evet |
| L3 (unit JS) | Vitest | Node 26 | `js-test` | Ayrı job |
| L3 (e2e) | Playwright + chromium | Node 26 + PHP 8.4 | `js-e2e` | Ayrı job (soft) |
| L4 | PHPStan / composer audit / GitLeaks | ubuntu-latest | `php-lint` · `composer-audit` · secret-scan workflow | Evet |

## §13 Eke — kalite kapıları kataloğu

| # | Kapı | Job | Sert mi | Satır |
|---|---|---|---|---|
| K-1 | PHP lint + PHPStan | `php-lint` | Sert | `ci.yml:25-64` |
| K-2 | PHP unit tests | `php-test` | Sert | `ci.yml:66-108` |
| K-3 | Composer audit | `composer-audit` | Sert | `ci.yml:110-139` |
| K-4 | JS unit tests | `js-test` | Sert | `ci.yml:142-158` |
| K-5 | JS coverage | `js-coverage` | Soft | `ci.yml:160-176` |
| K-6 | JS e2e | `js-e2e` | Soft | `ci.yml:178-203` |
| K-7 | Secret scan (GitLeaks) | ayrı workflow | Sert | `secret-scan.yml:18-31` |

Toplam 7 kapı · 6 sert + 2 soft sayımı `ci-ve-kalite-kapisi/` dalının
sorumluluğundadır; bu dosya yalnız katalogu tekrarlar.

## §14 Eke — karar kaydı

| # | Karar | Gerekçe | Alternatif (reddedilen) |
|---|---|---|---|
| D-1 | OS testi bu depoda **temsil edilmez** | Çekirdek kodu yok; referans olarak kalmak sahtelik üretir | Sahte "OS test" job'ı eklemek |
| D-2 | Kapsama eşiği konmaz (T2 `UNKNOWN`) | Eşik, kullanıcı kararıdır; uydurma eşik yanlış yeşil üretir | Tahmini %80 eşik |
| D-3 | Statik analiz testten ayrı job | Paralellik + farklı eskalasyon sahibi | Test job'una gömme |
| D-4 | Secret taraması ayrı workflow | `.github/AGENTS.md` §3.1 sahiplik tablosu | ci.yml içine ekleme (onay ister) |
| D-5 | `<coverage>` bloğu eklenmez | Ölçüm altyapısı kurulmadan konan rapor bloğu uydurma üretir | Boş `<coverage>` bloğu |

## §15 Eke — roller

| Rol | Bu anayasadaki sorumluluk | Kaynak |
|---|---|---|
| QA Engineer | Test kapsamı, suite sağlığı, flaky oranı | `.ai/AGENTS.md` §4 (agent 7) |
| DevOps Engineer | Job'lar, kapı sertliği, süreler | `.ai/AGENTS.md` §4 (agent 8) |
| Backend Architect | Test edilen kodun katmanı | `.ai/AGENTS.md` §4 (agent 2) |
| Security Engineer | Secret scan kapsamı, güvenlik testi | `.ai/AGENTS.md` §4 (agent 4) |

## §16 Eke — açık sorular (kapatılmaz)

| # | Soru | Durum | Kapatma koşulu |
|---|---|---|---|
| T1 | OS/çekirdek test kapsam mı, referans mı? | `UNKNOWN` | Çekirdek kodu depoya girince ya da kapsam kararı yazılınca |
| T2 | Kapsama hedefi ne olmalı? | `UNKNOWN` | Ölçüm altyapısı + kullanıcı kararı |

## §17 Eke — negatif kanıt (bu depoda OLMAYAN şey)

| Arama | Sonuç | Anlam |
|---|---|---|
| `kunit` (yazılım kodu) | 0 isabet | KUnit testi yok |
| `kselftest` (yazılım kodu) | 0 isabet | Kullanıcı uzayı selftest yok |
| `<coverage>` (`shared/phpunit.xml`) | 0 isabet | Kapsama raporu yapılandırılmamış |
| `phpunit.xml` kapsama eşiği | 0 isabet | Eşik yok (T2) |

## §18 Eke — anayasa denetim listesi

| # | Kontrol | Beklenen |
|---|---|---|
| 1 | Her iddia bir repo satırına ya da URL'ye bağlı mı? | Evet |
| 2 | T1/T2 `UNKNOWN` olarak mı duruyor? | Evet |
| 3 | Kapsama/eşik rakamı uyduruldu mu? | Hayır |
| 4 | OS testi "var" olarak mı sunuldu? | Hayır — negatif kanıt §17'de |
| 5 | Kardeş dosyalara (surucu/riskler/web/kod) çakışan içerik var mı? | Hayır — bu dosya yalnız ilke + seviye |

## §19 Eke — test envanteri matrisi (2026-10-13 disk ölçümü, kapsam DEĞİL)

> Sayım yöntemi: `Get-ChildItem -Filter *Test.php` + `Select-String 'public function test'`.
> Bunlar **dosya ve method sayımıdır**; "test geçti" ya da "kapsam %X" **iddiası
> üretmez** (T2 korunur).

| Suite (`phpunit.xml:8-33`) | Dizin | `*Test.php` | Method | L1/L2 |
|---|---|---|---|---|
| Unit | `tests/Unit` | 16 | 113 | L1 (Config/Device/Log/PageRouter/Security/Theme) |
| Api | `tests/Api` | 8 | 67 | L2 (pipeline, route, versiyonlama) |
| Middleware | `tests/Middleware` | 9 | 76 | L2 (zincir davranışı) |
| Security | `tests/Security` | 2 | 40 | L1 |
| Repository | `tests/Repository` | 2 | 25 | L1/L2 |
| Events | `tests/Events` | 2 | 15 | L2 (dispatcher) |
| OAuth | `tests/OAuth` | 1 | 8 | L2 |
| Component | `tests/Component` | 1 | 5 | L1 |
| **Toplam** | 8 suite | **41** | **349** | — |

`shared/tests/**` altında toplam **45** `.php` dosyası vardır; test sınıfı olmayan
4 dosya: `Fixtures/chrome/header.php`, `Fixtures/chrome/footer.php`,
`Repository/Sqlite/SqliteDatabaseManager.php`, `Repository/Sqlite/SqliteDatabaseRegistry.php`
(fixture + altyapı — `*Test.php` soneki yok).

> §8 envanter satırındaki `shared/tests/**/*.php` (glob) **44 dosya** yazar; bugün
> (2026-10-13) aynı glob **45** dosya döndürür (41 `*Test.php` + 4 test-sınıfı-olmayan).
> Ölçüm farkı burada **kaydedilir**, §8 satırı sessizce değiştirilmez — sayı iki
> farklı tarihin iki ölçümüdür; hangisinin güncel olduğu **⚠️ VERIFICATION REQUIRED**
> (dosya eklenmesi/çıkarılması git geçmişiyle doğrulanmalı).

## §20 Eke — dört `phpunit.xml` karşılaştırması (repo gerçeği)

| Özellik | `shared` (39 s) | `auth.coremusic.net` (34 s) | `home.coremusic.net` (18 s) | `api.coremusic.net` (29 s) |
|---|---|---|---|---|
| bootstrap | `vendor/autoload.php` (L4) | `tests/bootstrap.php` (L4) | `vendor/autoload.php` (L4) | `tests/bootstrap.php` (L4) |
| `stopOnFailure` | `false` (L6) | Yok | `false` (L6) | Yok |
| `cacheDirectory` | `.phpunit.cache` (L7) | Yok | `.phpunit.cache` (L7) | Yok |
| `failOnRisky` | Yok | `true` (L6) | Yok | `true` (L6) |
| `failOnWarning` | Yok | `true` (L7) | Yok | `true` (L7) |
| `failOnPhpunitWarning` | Yok | `false` (L8) | Yok | `false` (L8) |
| `<testsuite>` sayısı | 8 (L8-33) | 1 — Unit (L13-17) | 1 — Unit (L8-12) | 1 — Unit (L10-14) |
| `<source><include>` | `suffix=".php"` `src` (L34-38) | `include` (L20-24) | `suffix=".php"` `include` (L13-17) | `include` (L17-21) |
| `<coverage><report>` | **YOK** | `text php://stdout` (L28-32) | **YOK** | `text php://stdout` (L23-27) |
| CI'da koşulur mu? | Evet (`ci.yml:75-76`) | Evet (`ci.yml:79-80`) | **Hayır** (matrix 2 satır) | **Hayır** (matrix 2 satır) |

İki yapılandırma ailesi vardır: **durma/önbellek ailesi** (`stopOnFailure` +
`cacheDirectory`: shared, home) ve **sertlik ailesi** (`failOn*`: auth, api).
Hiçbir dosya `beStrictAboutTestsThatDoNotTestAnything` yazmaz → PHPUnit varsayılanı
(`true`) geçerlidir (kardeş N-11). `<coverage>` bloğu olan iki dosyanın hiçbirinde
CI `--coverage-*` bayrağı **koşmaz** → §7'deki "ölçüm yapılandırılmamış" hükmü
değişmez.

## §21 Eke — CLI bayrak ↔ anayasa ilkesi eşlemesi (docs 11.5 · 2026-10-13 HTTP 200)

| İlke | Bayrak / yapılandırma | Sayfadan okunan | Bu depodaki durum |
|---|---|---|---|
| S-5 (kapı sırası) | `--testsuite <name>` | Yalnız seçili suite koşulur | `ci.yml:80` tek kullanım |
| S-6 (soft kapı) | `--fail-on-*` / `--do-not-fail-on-*` | Exit kodunu belirler | **0 isabet** (workflow grep) |
| S-7 (analiz ≠ test) | `--strict-coverage` | Kapsam metadata sertliği | Kullanılmıyor |
| S-10 (ölçüm → kapı) | `--fail-on-risky` / `--fail-on-warning` | Test geçse de exit kodu başarısız | `ci.yml` 0 · XML'de `failOn*` auth/api'de var |
| S-3 (bağımsızlık) | `--order-by` (`default,defects,depends,duration,no-depends,random,reverse,size`) | Test sırası değiştirilebilir | Kullanılmıyor (sıra bağımlılığı test edilmez) |
| S-2 (tek davranış) | `--filter <pattern>` | Tek test/şablon çalıştırma | Kullanılmıyor |
| Erken durma | `--stop-on-*` (9 bayrak) | İlk hatada durur | **0 isabet**; XML `stopOnFailure="false"` (shared/home) |

## §22 Eke — doküman kataloğu: `--stop-on-*` ve `--fail-on-*` (textui 11.5)

| `--stop-on-*` (9) | Ne yapar |
|---|---|
| `--stop-on-defect` | Hatalı, başarısız, uyarı tetikleyen veya risky ilk testte dur |
| `--stop-on-error` | İlk *error* (assertion değil, beklenmeyen durum) |
| `--stop-on-failure` | İlk assertion başarısızlığı |
| `--stop-on-warning` | İlk uyarı |
| `--stop-on-risky` | İlk risky test |
| `--stop-on-deprecation` | İlk deprecation (`E_DEPRECATED`, `E_USER_DEPRECATED` veya PHPUnit deprecation) |
| `--stop-on-notice` | İlk notice (`E_STRICT`, `E_NOTICE`, `E_USER_NOTICE`) |
| `--stop-on-skipped` | İlk atlanan test |
| `--stop-on-incomplete` | İlk tamamlanmamış test |

| `--fail-on-*` (10) | Ne yapar |
|---|---|
| `--fail-on-warning` | Test geçse bile en az bir uyarıda başarısız exit kodu |
| `--fail-on-risky` | Test geçse bile risky testte başarısız exit kodu |
| `--fail-on-deprecation` | Deporation'da başarısız exit kodu |
| `--fail-on-phpunit-deprecation` | PHPUnit deprecation'ında başarısız exit kodu |
| `--fail-on-notice` | Notice'da başarısız exit kodu |
| `--fail-on-incomplete` | Tamamlanmamış testte başarısız exit kodu |
| `--fail-on-skipped` | Atlanmış testte başarısız exit kodu |
| `--fail-on-phpunit-warning` | PHPUnit warning'inde başarısız exit kodu |
| `--fail-on-empty-test-suite` | Suite boşsa başarısız exit kodu |
| `--fail-on-all-issues` | Tümüne eşdeğer |

Dokümanda okunan iki kritik kural: (1) `--fail-on-*` **yalnızca shell exit kodunu**
etkiler, özet bölümündeki `OK` yazısını değiştirmez; (2) `--do-not-fail-on-*`
karşıtları `--fail-on-all-issues`, eşleşen `--fail-on-*` ve XML'deki
`failOnAllIssues`/`failOn*` niteliklerinin **üzerine geçer**.

## §23 Eke — risky test tanımı (risky-tests 11.5 · 2026-10-13 HTTP 200)

| Kural | Sayfadan okunan | İlgili XML/CLI | Bu depoda |
|---|---|---|---|
| Kullanışsız test | Assertion yapmayan test strict (varsayılan açık) | `beStrictAboutTestsThatDoNotTestAnything="false"` · `--dont-report-useless-tests` | Varsayılan `true` geçerli (yazılmamış) |
| Risky ↔ kapsam | Risky testler **kod kapsamına katkı vermez** | `--strict-coverage` · `beStrictAboutCoverageMetadata` | Yok |
| Çıktı sertliği | Test sırasında çıktı üreten test risky | `--disallow-test-output` · `beStrictAboutOutputDuringTests` | Yok |
| Zaman aşımı | `pcntl` varsa süre sınırı: small 1 sn · medium 10 sn · large 60 sn | `--enforce-time-limit` · `enforceTimeLimit` | Yok |
| Global state | Global state'i değiştiren test risky | `--strict-global-state` · `beStrictAboutChangesToGlobalState` | Yok (§19'daki setUp/tearDown tam ters yöndür) |

> Bu satırlar **doküman tanımıdır**; bu depoda hiçbirinde değer yazılmadığı için
> PHPUnit varsayılanları geçerlidir. Riskler §6 A-2 / anayasa S-3 ile kesişir:
> `CsrfMiddlewareTest` setUp/tearDown global state'i **sıfırlar** (tek dosya değil).

## §24 Eke — seviye × dosya × CI işi

| Seviye | Dizin(ler) | Dosya | Job | Matrix |
|---|---|---|---|---|
| L1 birim | `tests/Unit`, `tests/Security`, `tests/Component` | 19 | `php-test` | `shared` (args boş) |
| L2 bileşen | `tests/Middleware`, `tests/Events`, `tests/Api`, `tests/OAuth`, `tests/Repository` | 22 | `php-test` | `shared` (args boş) |
| L1/L2 (auth) | `auth.coremusic.net/tests/Unit` | 6 `*Test.php` (+ `bootstrap.php`) | `php-test` | `auth.coremusic.net` (`--testsuite Unit`) |
| L3 unit JS | `assets.coremusic.net` (Vitest) | — | `js-test` | Yok (ayrı job) |
| L3 e2e | Playwright | — | `js-e2e` (soft) | Yok |
| L0 çekirdek | — | 0 | **Job yok** | — |

## §25 Eke — ölçüm ile iddia ayrımı

| Sayı | Ne olduğu | Ne DEĞİL |
|---|---|---|
| 41 `*Test.php` | Dosya sayısı | "41 test geçti" |
| 349 `public function test` | Method sayısı | "349 assertion" veya "kapsam %X" |
| 8 `<testsuite>` | XML tanımı | "8 suite koşuldu" (koşum kanıtı ⚠) |
| 2 matrix satırı | CI test kapsamı | "3 proje test ediliyor" (home/api matrix dışı) |
| `%80` (`ci.yml:165` yorumu) | Erişilemezlik kaydı | Hedef değer (T2) |

## §26 Eke — karar kaydı (genişleme)

| # | Karar | Gerekçe | Alternatif (reddedilen) |
|---|---|---|---|
| D-6 | `failOn*` yalnız auth/api'de; shared'a **eklenmez** | İki proje farklı sertlikte yaşar; tek taraflı sıkılaştırma `ci.yml` dışında davranış değiştirir | Tüm projeleri aynı yapmak (onay ister) |
| D-7 | `--fail-on-all-issues` CI'a **konmaz** | Eşik konmadan sert exit kodu, mevcut risky/warning sayısını ölçmeden kırar | Bayrağı doğrudan `ci.yml`'e eklemek |
| D-8 | `home`/`api` phpunit.xml CI'a **alınmaz** | `ci.yml:74-80` matrix 2 satırdır; kapsam değişikliği job ekleme sayılır (onay ister) | Matrix'e sessizce 3. satır eklemek |
| D-9 | `--stop-on-*` kullanılmaz | `stopOnFailure="false"` bilinçlidir: tüm hata raporu tek koşuda görünür | Erken durma (tanı gizler) |
| D-10 | Envanter sayıları kapsam olarak **sunulmaz** | Ölçüm altyapısı yok (T2 `UNKNOWN`) | "349 test" → "kapsam yüksek" safsatası |

## §27 Eke — negatif kanıt (genişleme)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `fail-on` / `stop-on` | `.github/workflows/*.yml` | 0 isabet |
| `--coverage-` | `.github/workflows/*.yml` | 0 isabet |
| `--list-suites` / `--filter` | `.github/workflows/*.yml` | 0 isabet |
| `beStrictAbout` | 4 `phpunit.xml` | 0 isabet |
| `failOn` | `shared` + `home` phpunit.xml | 0 isabet (yalnız auth/api'de) |
| `stopOnFailure` | `auth` + `api` phpunit.xml | 0 isabet (yalnız shared/home'da) |
| `kunit` / `kselftest` | repo (kapsam.md §F.1) | 0 isabet — L0 temsil edilmiyor |

## §28 Eke — kaynakça genişleme (2026-10-13 · webfetch GET · HTTP 200)

| İddia | URL | Durum |
|---|---|---|
| CLI bayrak kataloğu (`--stop-on-*`, `--fail-on-*`, `--testsuite`, `--filter`, `--order-by`) | https://docs.phpunit.de/en/11.5/textui.html | 2026-10-13 GET 200 + içerik (kardeş W-40 ile aynı URL, HEAD yerine GET) |
| Risky test kuralları (useless, strict-coverage, output, timeout, global state) | https://docs.phpunit.de/en/11.5/risky-tests.html | 2026-10-13 GET 200 + içerik — **envanterde YOK (yeni)** |
| Suite compose + dizin taramasının `*Test.php` araması + `--list-suites` | https://docs.phpunit.de/en/11.5/organizing-tests.html | 2026-10-13 GET 200 + içerik — **envanterde YOK (yeni)** |

## §29 Eke — test dosyası uçları (method sayımı, 2026-10-13)

| Yön | Dosya | Method | Not |
|---|---|---|---|
| En yüksek | `Security/ReturnUrlPolicyEdgeCaseTest.php` | 24 | Bu dosyada bloğu yok (kapsam dışı) |
| Yüksek | `Middleware/CsrfMiddlewareTest.php` | 16 | §2-§7 örnekleri buradan |
| Yüksek | `Security/SessionKeysTest.php` | 16 | §8-§9 örnekleri buradan |
| Yüksek | `Repository/ApiKeyRepositoryTest.php` | 16 | L1/L2 sınırı |
| Yüksek | `Unit/Security/ReturnUrlPolicyTest.php` | 16 | L1 |
| Düşük | `Middleware/AuthMiddlewareTest.php` | 4 | L2 |
| Düşük | `Unit/PageRouter/HtmlShellRendererChromeTest.php` | 4 | L1 |
| Düşük | `Middleware/BypassAuthMiddlewareFailClosedTest.php` | 3 | fail-closed davranışı |
| Düşük | `Unit/Device/NavRouteConsistencyTest.php` | 2 | L1 |
| Düşük | `Unit/PageRouter/SpaRouteTest.php` | 2 | L1 |
| En düşük | `Unit/Device/WelcomePopupRenderTest.php` | 1 | L1 |

> Method sayısı **test kalitesinin ölçütü değildir**: §8'deki tautoloji testi de
> bir "method" sayılır. Bu tablo **envanterdir**; "kapsam yüksek" **çıkarımı yapılmaz**
> (T2 `UNKNOWN`).

## §30 Eke — kapı × adım × sertlik haritası (ci.yml 31 adım)

| Job | Adım | Sertlik | `continue-on-error` | İlke (§3) |
|---|---|---|---|---|
| `php-lint` | `composer stan` (`:63`) | Sert | Yok | S-7 |
| `php-test` | `vendor/bin/phpunit "$PHPUNIT_ARGS"` (`:105`) | Sert | Yok | S-1..S-4 |
| `composer-audit` | `composer audit` ×3 (`:126,132,138`) | Sert | Yok | S-8 |
| `js-test` | `npm run test:js` (`:158`) | Sert | Yok | S-1 |
| `js-coverage` | `npm run test:js:coverage` (`:176`) | Soft | `true` (`:167`) | S-6, S-10 |
| `js-e2e` | `npm run test:e2e` (`:203`) | Soft | `true` (`:183`) | S-6 |
| secret-scan | `gitleaks-action@v2` (`secret-scan.yml:29`) | Sert | Yok | S-9 |

## §31 Eke — anayasa denetim listesi (genişleme)

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 6 | `--fail-on-*`/`--stop-on-*` CI'a sessizce eklendi mi? | Hayır | ✅ grep 0 (§27) |
| 7 | Kapsama yüzdesi/raporu bu revizyonda yazıldı mı? | Hayır | ✅ yazılmadı |
| 8 | 4 `phpunit.xml` karşılaştırması diskten mi? | Evet | ✅ §20 |
| 9 | Envanter sayıları "geçen test" olarak sunuldu mu? | Hayır | ✅ §25 ayrım tablosu |
| 10 | `home`/`api` CI dışı olduğu kaydedildi mi? | Evet | ✅ §20 son satır |
| 11 | Yeni URL'ler HTTP 200 + içerik ile mi doğrulandı? | Evet | ✅ §28 (3 URL) |
| 12 | Kardeş envanter kesişimi `W-xx` ile mi? | Evet | ✅ §28 (W-40) |

## §32 Eke — risk kaydı (genişleme)

| # | Risk | Sınıf | Karşıt kontrol |
|---|---|---|---|
| R-6 | İki yapılandırma ailesi (durma vs sertlik) arasındaki farkın fark edilmemesi | Kapı riski | §20 karşılaştırma tablosu |
| R-7 | `<coverage><report>` bloğu varmış gibi "kapsam var" sanılması | Doğruluk riski | §21 + §27 negatif kanıt |
| R-8 | 45 `.php` / 44 kayıt farkının (ölçüm tarihi) sessizce "eski" sayılması | Veri bütünlüğü | §19 notu — `⚠️ VERIFICATION REQUIRED` |
| R-9 | Matrix dışı iki projenin test edildiği sanılması | Doğruluk riski | §20 "CI'da koşulur mu?" sütunu |

## §33 Eke — bu revizyonun kanıt kaydı

| Kaynak | Okuma | Anayasadaki yeri |
|---|---|---|
| `shared/phpunit.xml` | 1-39 (tam) | §4, §19, §20 |
| `shared/composer.json` | 1-50 (tam) | §1 (`^10.5` L24), §5 (L48), §8 |
| `auth.coremusic.net/phpunit.xml` | 1-34 (tam) | §20, §21 |
| `home.coremusic.net/phpunit.xml` | 1-18 (tam) | §20, §27 |
| `api.coremusic.net/phpunit.xml` | 1-29 (tam) | §20, §27 |
| `.github/workflows/ci.yml` | 1-203 (tam) | §5, §6, §13, §30 |
| `.github/workflows/secret-scan.yml` | 1-31 (tam) | §5, §13, §30 |
| `package.json` | 1-18 (tam) | §24 |
| `shared/tests/**` glob | 45 `.php` / 41 `*Test.php` / 349 method | §19, §24, §29 |
| `docs.phpunit.de/en/11.5/textui.html` | GET 200 · 2026-10-13 | §21, §22 |
| `docs.phpunit.de/en/11.5/risky-tests.html` | GET 200 · 2026-10-13 | §23, §28 |
| `docs.phpunit.de/en/11.5/organizing-tests.html` | GET 200 · 2026-10-13 | §28 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
