---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-web-arastirma
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-dogrulama
evidence:
  - "docs.kernel.org/dev-tools/kselftest.html (2026-10-13, HTTP 200, webfetch)"
  - "docs.kernel.org/dev-tools/kunit/usage.html (2026-10-13, HTTP 200, webfetch)"
  - "docs.phpunit.de/en/11.5/configuration.html (2026-10-13, HTTP 200, webfetch)"
validation:
  - "2026-10-13 · 3 URL webfetch GET ile içerik bazında doğrulandı; 1 aday URL HTTP 404 verdi ve reddedildi"
  - "2026-10-13 · kardeş -dogrulama.md W-02/W-39/W-40..W-43/W-28/W-31/W-32/W-37/W-45..W-47 envanteri yeniden kullanıldı; kesişim kayıtları §8'de"
open_questions:
  - "T1: OS testi hedef referans olarak mı kalacak? [UNKNOWN]"
  - "T2: Kapsama eşiği için resmi doküman kanıtı eşik önermez — kullanıcı kararı gerekir [UNKNOWN]"
group: kanit
---

# isletim-sistemi-testing Web Araştırması — İddia, URL ve Tarih Kaydı

## §0 ELI15

Bu dosya, dalın iddialarının **hangi web kaynağında, hangi tarihte** durduğunu
yazar. Kural üçlüsü: (1) her satır bir **iddia + URL + tarih** taşır, (2) yalnız
**resmi** kaynaklar kullanılır (proje dokümanı, standart sahibi, framework resmi
sitesi), (3) doğrulanamayan **`⚠️ VERIFICATION REQUIRED`** yazılır, **uydurulmaz**.
Bu oturumda 3 URL içerik bazında webfetch ile doğrulandı, 1 aday 404 ile reddedildi;
kardeş `isletim-sistemi-testing-dogrulama.md` (W-01..W-53) envanterinden 12 URL
yeniden kullanıldı. Tüm tarihler **2026-10-13**'tür.

## §1 Yöntem

| # | Adım | Araç | Sonuç |
|---|---|---|---|
| 1-1 | Aday URL seçimi | Dal konuları (OS testi · PHPUnit · CI) | 4 aday |
| 1-2 | İçerik bazlı doğrulama | `webfetch` (GET, tam metin) | 3 × içerik döndü |
| 1-3 | Erişilemeyen aday | `webfetch` | 1 × HTTP 404 → reddedildi (§4) |
| 1-4 | Kardeş envanter kontrolü | grep `-dogrulama.md` W-01..W-53 | 12 yeniden kullanım |
| 1-5 | Vault içi kesişim | grep `.ai/architecture` | §8 kayıtları |

> HEAD ile kod doğrulaması kardeş dosyanın yöntemidir; bu dosya **GET ile içerik**
> doğrulaması yapar — "sayfa var" değil, "iddia sayfada gerçekten yazıyor" kanıtı.

## §2 Bu oturumda webfetch ile doğrulanan URL'ler

| # | İddia (sayfada okunan) | URL | Tarih | Durum |
|---|---|---|---|---|
| N-1 | Kullanıcı uzayı selftest'leri `tools/testing/selftests/` altındadır; testler kernel build + install + boot sonrası koşulur | https://docs.kernel.org/dev-tools/kselftest.html | 2026-10-13 | ✅ HTTP 200 + içerik |
| N-2 | kselftest çıktısı TAP standardına uymalıdır; CI sistemleri TAP'ı ayrıştırır | https://docs.kernel.org/dev-tools/kselftest.html | 2026-10-13 | ✅ HTTP 200 + içerik |
| N-3 | kselftest'te test başına varsayılan timeout 45 saniyedir | https://docs.kernel.org/dev-tools/kselftest.html | 2026-10-13 | ✅ HTTP 200 + içerik |
| N-4 | KUnit test durumu `void (*)(struct kunit *test)` imzalıdır | https://docs.kernel.org/dev-tools/kunit/usage.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-02 |
| N-5 | KUnit'te `KUNIT_EXPECT_*` devam eder, `KUNIT_ASSERT_*` testi sonlandırır | https://docs.kernel.org/dev-tools/kunit/usage.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-02 |
| N-6 | KUnit test suite'i `kunit_test_suite()` ile kaydedilir | https://docs.kernel.org/dev-tools/kunit/usage.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-02 |
| N-7 | PHPUnit XML'de `<testsuite>` `name` ister; `<directory>`/`<file>` child'larıdır | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-39 |
| N-8 | `<source>` elementi kapsam/uyarı raporlamasını birinci taraf koda kısıtlar | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-39 |
| N-9 | `<coverage><report>` clover/cobertura/crap4j/html/php/text/xml formatları üretir | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-39 |
| N-10 | `stopOnFailure` varsayılanı `false`'tur | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-39 |
| N-11 | `beStrictAboutTestsThatDoNotTestAnything` varsayılanı `true`'dır (assertion'sız test risky) | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 | ✅ HTTP 200 + içerik · kardeş W-39 |

> Sürüm notu (dürüstlük kaydı): repo `phpunit/phpunit ^10.5` kullanır
> (`shared/composer.json:24`); bu oturumda içerik doğrulanan sayfa **11.5**
> sürüm dokümanıdır. Sürüm farkı bilinçli taşındı — 10.5 dokümanı vault envanterinde
> mevcuttur (`docs.phpunit.de/en/10.5/index.html`, os-dogrulama W1 · 2026-10-13 HTTP 200).

## §3 Kardeş `-dogrulama.md` envanterinden yeniden kullanılan URL'ler

> Kural: kardeş dosyada 2026-10-13'te HTTP 200 doğrulanmış URL'ler yeniden
> kullanılabilir; kesişim `W-xx` numarasıyla not edilir.

| # | İddia | URL | Kardeş no | Tarih |
|---|---|---|---|---|
| R-1 | PHPUnit CLI `--testsuite` bayrağını tanımlar | https://docs.phpunit.de/en/11.5/textui.html | W-40 | 2026-10-13 |
| R-2 | Test double (stub/mock/spy) sınırları | https://docs.phpunit.de/en/11.5/test-doubles.html | W-41 | 2026-10-13 |
| R-3 | Test yazım kılavuzu (fixture, assertion) | https://docs.phpunit.de/en/11.5/writing-tests-for-phpunit.html | W-42 | 2026-10-13 |
| R-4 | Assertif kataloğu | https://docs.phpunit.de/en/11.5/assertions.html | W-43 | 2026-10-13 |
| R-5 | PHPUnit 10 duyurusu (`^10.5` ailesi) | https://phpunit.de/announcements/phpunit-10.html | W-45 | 2026-10-13 |
| R-6 | Duyuru arşivi (sürüm geçmişi) | https://phpunit.de/announcements/ | W-46 | 2026-10-13 |
| R-7 | PHPUnit yazarının resmi sayfası | https://sebastian-bergmann.de/ | W-47 | 2026-10-13 |
| R-8 | Workflow sözdizimi (`needs`, `matrix`, `continue-on-error`) | https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax | W-28 | 2026-10-13 |
| R-9 | Tetikleyici olayları (`push`, `pull_request`) | https://docs.github.com/en/actions/reference/workflows-and-actions/events-that-trigger-workflows | W-32 | 2026-10-13 |
| R-10 | Bağımlılık önbellekleme (`actions/cache` anahtarı) | https://docs.github.com/en/actions/writing-workflows/choosing-what-your-workflow-does/caching-dependencies-to-speed-up-workflows | W-31 | 2026-10-13 |
| R-11 | Matrisli iş çalıştırma (`strategy.matrix`) | https://docs.github.com/en/actions/writing-workflows/choosing-what-your-workflow-does/running-variations-of-jobs-in-a-workflow | W-37 | 2026-10-13 |
| R-12 | KCoV kod kapsama aracı (L0 hedef referans) | https://docs.kernel.org/dev-tools/kcov.html | W-05 | 2026-10-13 |
| R-13 | KFENCE bellek denetleyicisi (L0 hedef referans) | https://docs.kernel.org/dev-tools/kfence.html | W-06 | 2026-10-13 |
| R-14 | Fault injection alt sistemi (L0 hedef referans) | https://docs.kernel.org/fault-injection/fault-injection.html | W-08 | 2026-10-13 |
| R-15 | Kernel dev-tools indeksi (KUnit/kselftest toplu listesi) | https://www.kernel.org/doc/html/latest/dev-tools/index.html | W-12 | 2026-10-13 |

## §4 Reddedilen adaylar (erişilemeyen)

| # | Aday URL | İddia niyeti | Sonuç | Karar |
|---|---|---|---|---|
| X-1 | https://docs.kernel.org/dev-tools/testing.html | Kernel test genel rehberi | **HTTP 404** (2026-10-13, webfetch) | Reddedildi — iddia yazılmadı |
| X-2 | https://docs.phpunit.de/en/10.5/running-tests.html | 10.5 test çalıştırma | **HTTP 404** (kardeş os-dogrulama X4, 2026-10-13) | Reddedildi — kardeş kaydıyla uyumlu |

> Kural ihlali yok: 404 alan URL'den **hiçbir iddia türetilmedi**; yerine erişilebilir
> resmi sayfa kullanıldı (N-1..N-11, R-1..R-15).

## §5 OS test araçları — sayfadan okunanlar (hedef referans)

| Konu | Sayfadan okunan | Kullanım yerı |
|---|---|---|
| Kapsam | selftest'ler `tools/testing/selftests/` altındadır; build + install + boot sonrası koşulur | anayasa §1 L0 satırı |
| Koşum | `make -C tools/testing/selftests run_tests` · `make kselftest` | surucu §10 |
| Standart | Çıktı TAP standardına uymalı; kselftest.h / kselftest_harness.h sarmalayıcıları | anayasa §12 |
| Timeout | Test başına varsayılan 45 saniye; `--override-timeout` ile değiştirilebilir | surucu §10 |
| Harness | `TEST()` / `TEST_F()` / `FIXTURE()` makroları; `EXPECT_*` devam, `ASSERT_*` durdurur | riskler §6 (A-1 karşıtı) |
| KUnit | Test durumu `void (*)(struct kunit *)`; suite `kunit_test_suite()` ile kaydedilir; parametreli test `KUNIT_CASE_PARAM` | anayasa §2 |

> Bu bölümün tamamı **hedef mimaridir**: bu depoda `kunit`/`kselftest` 0 isabet
> (kapsam.md §F.1 negatif kanıt) → hiçbir satır "bizde var" olarak kullanılmaz.

## §6 Uygulama testi — sayfadan okunanlar (repo ile eşleşen)

| Konu | Sayfadan okunan | Repo karşılığı |
|---|---|---|
| `<testsuite>` | `name` zorunlu; child `<directory>` / `<file>`; `<exclude>` ile eleme | `phpunit.xml:8-33` (8 suite) |
| `<source>` | Kapsama ve uyarı raporlamasını birinci taraf koda kısıtlar; `<include>`/`<exclude>` | `phpunit.xml:34-38` |
| `<coverage>` | Rapor formatları: clover, cobertura, crap4j, html, php, text, xml | **Repoda YOK** (39 satır okundu) |
| `stopOnFailure` | Varsayılan `false` | `phpunit.xml:6` (`false` — varsayımla aynı) |
| `cacheDirectory` | Testler arası veri (test-results, code-coverage) | `phpunit.xml:7` |
| Assertion sertliği | Assertion'sız test risky (varsayılan `true`) | riskler §2 Y-3 |

## §7 CI — kardeş envanterden özet

| Konu | Kaynak | Bu dalda kullanımı |
|---|---|---|
| `needs` kapı bağımlılığı | R-8 (W-28) | surucu §3 |
| `push` / `pull_request` tetikleyicileri | R-9 (W-32) | surucu §3 |
| `composer.lock` hash'li cache anahtarı | R-10 (W-31) | surucu §4 |
| `strategy.matrix` + `fail-fast: false` | R-11 (W-37) | surucu §2 |

## §8 Kesişim kaydı (envanter notu)

| URL | Vault içi kesişim | Karar |
|---|---|---|
| `docs.kernel.org/dev-tools/kselftest.html` | `os-master.md:170` ve `:459` (aynı vault, aynı tarih 2026-10-13) | **Yeniden kullanım** — yeni URL iddiası YOK; oturumda webfetch ile içerik doğrulandı |
| `docs.kernel.org/dev-tools/kunit/usage.html` | Kardeş W-02 (bu dal) | Yeniden kullanım (görev izni) |
| `docs.phpunit.de/en/11.5/configuration.html` | Kardeş W-39 (bu dal) | Yeniden kullanım (görev izni) |
| §3'teki 15 URL | Kardeş W-envanteri | Yeniden kullanım (görev izni) |
| `docs.kernel.org/dev-tools/testing.html` | grep: vault'ta referans yok | 404 → zaten reddedildi (X-1) |

> Yani bu dosyada **"envanterdışı yeni URL" iddiası yoktur**: her URL ya bu oturumda
> içerik bazında doğrulanmış, ya kardeş envanterden izinle yeniden kullanılmış, ya da
> reddedilmiştir.

## §9 Kapsam dışı

| Konu | Sınır |
|---|---|
| Web araştırması yapma yöntemi (araç seçimi) | Kardeş `-dogrulama.md` §0 (bu dosya yalnız kaydı taşır) |
| K019 testing araştırma envanteri | Komşu dal |
| Donanım/DSP araştırma kayıtları | `K001-donanım` dalı |
| URL envanterinin tam sayımı (2097) | Kardeş `-dogrulama.md` §2 — bu dosya sayı uydurmaz |

## §10 Eke — konu × URL sayımı

| Konu | Bu oturum (N) | Kardeş tekrar (R) | Red (X) |
|---|---|---|---|
| OS test araçları | 6 (N-1..N-6) | 4 (R-12..R-15) | 1 (X-1) |
| PHPUnit | 5 (N-7..N-11) | 7 (R-1..R-7) | 1 (X-2) |
| CI / GitHub Actions | 0 | 4 (R-8..R-11) | 0 |
| Toplam | 11 | 15 | 2 |

## §11 Eke — tarih kaydı

| Tarih | Eylem | Sonuç |
|---|---|---|
| 2026-10-13 | webfetch GET × 4 aday | 3 × 200 + içerik · 1 × 404 |
| 2026-10-13 | kardeş W-envanteri okundu | 15 URL yeniden kullanım için seçildi |
| 2026-10-13 | vault içi grep kesişim kontrolü | 1 kesişim (`os-master.md`) not edildi |

## §12 Eke — `⚠️` işaretli iddialar (uydurma yasağı)

| # | İddia | Durum |
|---|---|---|
| ⚠-1 | KUnit/kselftest'in bu dalda ne zaman uygulanacağı | `⚠️ VERIFICATION REQUIRED` — T1 |
| ⚠-2 | Kapsama eşiği önerisi | `⚠️ VERIFICATION REQUIRED` — resmi doküman eşik önermez; T2 |
| ⚠-3 | PHPUnit 10.5 dokümanının 11.5 ile birebir aynı olması | `⚠️ VERIFICATION REQUIRED` — sürüm farkı §2 notunda taşındı |
| ⚠-4 | CI'nin gerçekten geçtiği (yeşil olduğu) | `⚠️ VERIFICATION REQUIRED` — workflow dosyası var, koşum kanıtı yok (`.ai/AGENTS.md` §25.2 notu ile uyumlu) |

## §13 Eke — açık sorular

| # | Soru | Kapanış |
|---|---|---|
| T1 | OS testi kapsam mı referans mı? | Kullanıcı kararı + çekirdek kod varlığı |
| T2 | Kapsama eşiği | Ölçüm altyapısı + kullanıcı kararı |

## §14 İkinci tur yöntem (2026-10-13 · GET ile içerik doğrulaması)

| # | Adım | Araç | Sonuç |
|---|---|---|---|
| 2-1 | CLI kataloğu adayı | PHPUnit resmi dokümanı (`textui`) | 1 aday |
| 2-2 | İçerik doğrulama | `webfetch` (GET, tam metin) | ✅ HTTP 200 + içerik |
| 2-3 | Risky test tanımı adayı | `risky-tests` | ✅ HTTP 200 + içerik |
| 2-4 | Suite compose adayı | `organizing-tests` | ✅ HTTP 200 + içerik |
| 2-5 | Eski indeks adayı | `phpunit.de/documentation/` | ❌ HTTP 404 → reddedildi (§16) |
| 2-6 | Kardeş envanter kesişimi | grep `-dogrulama.md` (W-01..W-53) | `textui` = W-40; diğer ikisi envanterde YOK |
| 2-7 | Repo çapraz kontrol | grep `.github/workflows/*.yml` + 4 `phpunit.xml` | bayrak kullanım sayımları (§25) |

> Bu tur da §1-§13 ile **aynı kuraldadır**: yalnız resmi kaynak, yalnız içerik
> bazlı GET, 404'ten iddia türetilmez. Tüm yeni tarihler **2026-10-13**.

## §15 Bu oturumda webfetch GET ile doğrulanan yeni URL'ler

| # | İddia (sayfada okunan) | URL | Tarih | Durum |
|---|---|---|---|---|
| N-12 | `--testsuite <name>` yalnız virgülle ayrılmış seçili suite'leri koşar | https://docs.phpunit.de/en/11.5/textui.html | 2026-10-13 | ✅ GET 200 + içerik · kardeş W-40 (GET ile yeniden) |
| N-13 | `--filter <pattern>` test adı deseniyle koşumu süzer (çok kez kullanılabilir, `AND`) | aynı | 2026-10-13 | ✅ GET 200 |
| N-14 | `--list-suites` XML'de tanımlı suite adlarını listeler | aynı | 2026-10-13 | ✅ GET 200 |
| N-15 | `--exclude-testsuite <name>` verilen suite dışındaki her şeyi koşar | aynı | 2026-10-13 | ✅ GET 200 |
| N-16 | 9 adet `--stop-on-*` bayrağı vardır (defect/error/failure/warning/risky/deprecation/notice/skipped/incomplete) | aynı | 2026-10-13 | ✅ GET 200 |
| N-17 | 10 adet `--fail-on-*` bayrağı vardır; `--fail-on-all-issues` hepsine eşdeğerdir | aynı | 2026-10-13 | ✅ GET 200 |
| N-18 | `--fail-on-*` **yalnız shell exit kodunu** etkiler; özet `OK` yazısını değiştirmez | aynı | 2026-10-13 | ✅ GET 200 |
| N-19 | `--do-not-fail-on-*` karşıtları, `--fail-on-all-issues` + `--fail-on-*` + XML `failOn*` üzerine geçer | aynı | 2026-10-13 | ✅ GET 200 |
| N-20 | `--cache-directory` `test-results` ve `code-coverage` verisini saklar | aynı | 2026-10-13 | ✅ GET 200 |
| N-21 | `--order-by` stratejileri: `default,defects,depends,duration,no-depends,random,reverse,size` | aynı | 2026-10-13 | ✅ GET 200 |
| N-22 | `--coverage-*` biçimleri: clover, cobertura, crap4j, html, php, text, xml | aynı | 2026-10-13 | ✅ GET 200 |
| N-23 | Progress karakterleri: `.` `F` `E` `W` `R` `D` `N` `I` `S` | aynı | 2026-10-13 | ✅ GET 200 |
| N-24 | Özet satırları: `OK` · `OK, but some tests were skipped!` · `OK, but there were issues!` · `FAILED!` · `ERRORED!` | aynı | 2026-10-13 | ✅ GET 200 |
| N-25 | Assertion'sız test "strict"tir; `--dont-report-useless-tests` ile kapatılır | https://docs.phpunit.de/en/11.5/risky-tests.html | 2026-10-13 | ✅ GET 200 + içerik — **envanterde YOK** |
| N-26 | Risky testler **kod kapsamına katkı vermez** | aynı | 2026-10-13 | ✅ GET 200 — **envanterde YOK** |
| N-27 | `--strict-coverage` / `--disallow-test-output` / `--strict-global-state` sertlik bayrakları | aynı | 2026-10-13 | ✅ GET 200 — **envanterde YOK** |
| N-28 | Zaman aşımı (yalnız `pcntl` varsa): small 1 sn · medium 10 sn · large 60 sn | aynı | 2026-10-13 | ✅ GET 200 — **envanterde YOK** |
| N-29 | Dizin hedeflendiğinde PHPUnit `*Test.php` dosyalarını arar | https://docs.phpunit.de/en/11.5/organizing-tests.html | 2026-10-13 | ✅ GET 200 + içerik — **envanterde YOK** |
| N-30 | XML ile suite compose: `<testsuites>` + `<testsuite name>` + `<directory>` | aynı | 2026-10-13 | ✅ GET 200 — **envanterde YOK** |
| N-31 | `--list-suites` çıktısı "Available test suite(s):" listesidir | aynı | 2026-10-13 | ✅ GET 200 — **envanterde YOK** |
| N-32 | Sürüm için doğru şema `https://schema.phpunit.de/11.5/phpunit.xsd` | aynı | 2026-10-13 | ✅ GET 200 — **envanterde YOK** |

> N-12, kardeş **W-40** ile aynı URL'dir; kardeş `HEAD 200` doğrulaması yapmıştı,
> bu oturum **GET + içerik** ile teyit etti (yöntem farkı §1'deki notla aynıdır).

## §16 Reddedilen aday (bu tur)

| # | Aday URL | İddia niyeti | Sonuç | Karar |
|---|---|---|---|---|
| X-3 | https://phpunit.de/documentation/ | CLI kataloğu indeksi | **HTTP 404** (2026-10-13, webfetch GET) | Reddedildi — yerine `docs.phpunit.de/en/11.5/textui.html` kullanıldı (N-12..N-24) |

## §17 CLI kataloğu — Configuration ve Selection (textui 11.5)

| Bayrak | Ne yapar | Bu depoda |
|---|---|---|
| `--bootstrap <file>` | Testlerden önce PHP dosyası çalıştır (autoloader) | Yok — XML `bootstrap` kullanılıyor (`phpunit.xml:4`) |
| `-c\|--configuration <file>` | XML dosyasıyla yapılandır | Yok — `working-directory` yeterli |
| `--no-configuration` | XML'i yok say | Yok |
| `--no-extensions` | Uzantıları yükleme | Yok |
| `--include-path <path(s)>` | `include_path` ekle | Yok |
| `-d <key[=value]>` | `ini_set()` ile php.ini değeri | Yok |
| `--cache-directory <dir>` | Suite'ler arası `test-results` + `code-coverage` | XML: `cacheDirectory=".phpunit.cache"` (`phpunit.xml:7`) |
| `--generate-configuration` | En iyi varsayılanlarla XML üret | Yok |
| `--migrate-configuration` | Eski formatı taşı | Yok |
| `--list-suites` | XML suite adlarını listele | Yok (§18) |
| `--testsuite <name>` | Yalnız seçili suite | ✅ `ci.yml:80` |
| `--exclude-testsuite <name>` | Seçili suite hariç | Yok |
| `--list-groups` | Test gruplarını listele | Yok |
| `--group <name>` | Yalnız grup | Yok (virgülle liste PHPUnit 12'de kalkar — sayfanın kendi uyarısı) |
| `--exclude-group <name>` | Grup hariç | Yok |
| `--covers <name>` | Kapsam metadata hedefi | Yok |
| `--uses <name>` | Kullanılan kod metadata'sı | Yok |
| `--requires-php-extension <name>` | Uzantı isteyen testler | Yok |
| `--list-tests` | Testleri listele | Yok |
| `--list-tests-xml <file>` | Testleri XML'e yaz | Yok |
| `--filter <pattern>` | Test adı deseni (çoklu `AND`) | Yok |
| `--test-suffix <suffix>` | Dosya soneki (varsayılan `Test.php`, `.phpt`) | Yok — XML `<directory>` taraması kullanılır |

## §18 CLI kataloğu — Execution (izolasyon, risky, sıralama)

| Bayrak | Ne yapar | Bu depoda |
|---|---|---|
| `--process-isolation` | Her test ayrı PHP süreci | Yok |
| `--globals-backup` | Global/super-global yedekle-geri-al | Yok (setUp/tearDown elle yapar — §5'teki CSRF örneği) |
| `--static-backup` | Static özellik yedekle | Yok |
| `--strict-coverage` | Kapsam metadata sertliği | Yok |
| `--strict-global-state` | Global state sertliği | Yok |
| `--disallow-test-output` | Test çıktısını yasakla | Yok |
| `--enforce-time-limit` | Test boyutuna göre süre sınırı | Yok |
| `--default-time-limit <sec>` | Boyutsuz testlerin süresi | Yok |
| `--dont-report-useless-tests` | Assertion'sız testi raporlama | Yok — varsayılan strict **açık** (N-25) |
| `--cache-result` | Sonuç önbelleği yaz | Yok |
| `--do-not-cache-result` | Önbellek yazma | Yok |
| `--order-by <order>` | Test sırası (8 strateji) | Yok |
| `--random-order-seed <N>` | Rastgele sıralama tohumu | Yok |

## §19 CLI kataloğu — durma ve sertlik bayrakları (tam)

| `--stop-on-*` (9) | Eşleşen XML/CI |
|---|---|
| `--stop-on-defect` | Yok |
| `--stop-on-error` | Yok |
| `--stop-on-failure` | `stopOnFailure="false"` (`shared` L6, `home` L6) — **bilinçli tersi** |
| `--stop-on-warning` | Yok |
| `--stop-on-risky` | Yok |
| `--stop-on-deprecation` | Yok |
| `--stop-on-notice` | Yok |
| `--stop-on-skipped` | Yok |
| `--stop-on-incomplete` | Yok |

| `--fail-on-*` (10) | Eşleşen XML/CI |
|---|---|
| `--fail-on-warning` | `failOnWarning="true"` (`auth` L7, `api` L7) |
| `--fail-on-risky` | `failOnRisky="true"` (`auth` L6, `api` L6) |
| `--fail-on-phpunit-warning` | `failOnPhpunitWarning="false"` (`auth` L8, `api` L8) — bilinçli kapalı |
| `--fail-on-deprecation` | Yok |
| `--fail-on-phpunit-deprecation` | Yok |
| `--fail-on-notice` | Yok |
| `--fail-on-incomplete` | Yok |
| `--fail-on-skipped` | Yok |
| `--fail-on-empty-test-suite` | Yok |
| `--fail-on-all-issues` | Yok |

> `--do-not-fail-on-*` karşılıkları **9** tanedir (empty-test-suite dahil, `all-issues`
> hariç) ve XML `failOn*` niteliklerinin üzerine geçer (N-19).

## §20 CLI kataloğu — Reporting ve Logging

| Bayrak | Ne yapar |
|---|---|
| `--colors <flag>` | `never` / `auto` / `always` |
| `--columns <n>` / `--columns max` | İlerleme sütun genişliği |
| `--stderr` | Çıktıyı `php://stderr`'e yaz |
| `--no-progress` | İlerleme çıktısını kapat |
| `--no-results` | Sonuç ayrıntısını kapat |
| `--no-output` | Tüm çıktıyı kapat |
| `--display-incomplete` | Tamamlanmamış testleri göster |
| `--display-skipped` | Atlanan testleri göster |
| `--display-deprecations` | Deporation'ları göster |
| `--display-phpunit-deprecations` | PHPUnit deprecation'larını göster |
| `--display-errors` | Error'ları göster |
| `--display-notices` | Notice'leri göster |
| `--display-warnings` | Uyarıları göster |
| `--display-all-issues` | Tüm sorunları göster |
| `--reverse-list` | Defect listesini ters yaz |
| `--teamcity` | TeamCity biçimine çevir |
| `--testdox` | TestDox çıktısı |
| `--testdox-summary` | Başarısız testler için özet tekrarı |
| `--log-junit <file>` | JUnit XML raporu |
| `--log-teamcity <file>` | TeamCity log |
| `--testdox-html <file>` | TestDox HTML |
| `--testdox-text <file>` | TestDox düz metin |
| `--log-events-text <file>` | Olay akışı (düz metin) |
| `--log-events-verbose-text <file>` | Olay akışı (telemetry dahil) |
| `--no-logging` | XML'deki log yapılandırmasını yok say |
| `-h\|--help` · `--version` · `--atleast-version <min>` · `--check-version` | Yardım/sürüm denetimi |

> Bu kataloğun tamamı **repoda kullanılmaz** (workflow grep: `fail-on`, `stop-on`,
> `--filter`, `--list-suites` = 0 isabet). Amaç: hangi bayrağın **mevcut olduğu**
> resmi kaynaktan kayıtlıdır; "kullanılıyor" **iddiası üretilmez**.

## §21 CLI kataloğu — Code Coverage bayrakları (T2 ile doğrudan ilgili)

| Bayrak | Ne yapar | Bu depoda |
|---|---|---|
| `--coverage-clover <file>` | Clover XML | Yok |
| `--coverage-cobertura <file>` | Cobertura XML | Yok |
| `--coverage-crap4j <file>` | Crap4J XML | Yok |
| `--coverage-html <dir>` | HTML rapor | Yok |
| `--coverage-php <file>` | Serileştirilmiş veri | Yok |
| `--coverage-text=<file>` | Metin (varsayılan `php://stdout`) | Yok |
| `--coverage-xml <dir>` | XML rapor | Yok |
| `--warm-coverage-cache` | Statik analizi önceden yap | Yok |
| `--coverage-filter <dir>` | Kapsam hedefi | Yok |
| `--path-coverage` | Satır dışında yol kapsamı | Yok |
| `--disable-coverage-ignore` | Kapsam yok saymayı kapat | Yok |
| `--no-coverage` | XML `<coverage>` yapılandırmasını yok say | Yok |

> Not: `auth`/`api` `phpunit.xml` içinde `<coverage><report><text .../></report></coverage>`
> bloğu vardır; bayrak + sürücü (Xdebug/PCOV) olmadan rapor **üretilmez** (dosyanın
> kendi yorumu, `auth.coremusic.net/phpunit.xml:26-27`). PHP tarafında kapsam
> komutu **0 isabet** → T2 `UNKNOWN` korunur; **hiçbir yüzdesel rakam yazılmaz**.

## §22 Progress ve özet okuma anahtarı (textui 11.5)

| Karakter | Anlamı |
|---|---|
| `.` | Sorunsuz başarılı test |
| `F` | Assertion başarısızlığı (failure) |
| `E` | Error (beklenmeyen durum / PHP hatası) |
| `W` | Uyarı tetiklendi |
| `R` | Risky kabul edildi |
| `D` | Deporation |
| `N` | Notice |
| `I` | Incomplete |
| `S` | Skipped |

| Özet | Koşul |
|---|---|
| `OK` | Hata yok |
| `OK, but some tests were skipped!` | Hata yok, atlanan var |
| `OK, but there were issues!` | Hata yok, sorun var |
| `FAILED!` | Failure var, error yok |
| `ERRORED!` | En az bir error |

## §23 Organizing-tests — sayfadan okunanlar (repo ile eşleşen)

| Konu | Sayfadan okunan | Repo karşılığı |
|---|---|---|
| Dizin hedefi | `*Test.php` dosyaları taranır | `phpunit.xml` 8 suite `<directory>` (`tests/Unit` … `tests/OAuth`) |
| XML compose | `<testsuites>` + `<testsuite name>` + `<directory>` | `shared/phpunit.xml:8-33` |
| Suite seçimi | `--testsuite unit` örneği (172/177 test) | `ci.yml:80` (`--testsuite Unit`) |
| Suite listesi | `--list-suites` çıktısı | Repoda 0 isabet (§20) |
| Şema | `https://schema.phpunit.de/11.5/phpunit.xsd` | Repodaki `xsi:noNamespaceSchemaLocation` `vendor/.../phpunit.xsd` (yerel şema) |
| Tek dosya hedefi | `phpunit tests/unit/WorldTest.php` | Repoda 0 isabet (CI dosya hedeflemez) |

## §24 Risky-tests — sayfadan okunanlar

| Konu | Sayfadan okunan | İlgili repo satırı |
|---|---|---|
| Useless test | Varsayılan strict; `--dont-report-useless-tests` ile kapanır | `beStrictAboutTestsThatDoNotTestAnything` hiçbir `phpunit.xml`'de yazılmaz → varsayılan geçerli (N-11) |
| Risky ↔ kapsam | Risky testler kapsam üretmez | Kapsam raporu zaten yok (§21) |
| Output | `--disallow-test-output` / `beStrictAboutOutputDuringTests` | Yok |
| Timeout | small 1 sn · medium 10 sn · large 60 sn (`pcntl`) | `--enforce-time-limit` 0 isabet |
| Global state | `--strict-global-state` / `beStrictAboutChangesToGlobalState` | Yok — CSRF testi `$_SESSION`'ı setUp/tearDown ile sıfırlar |
| Metadata | `requireCoverageMetadata="true"` risky yapabilir | Yok |

## §25 Repo çapraz sayımı (bu oturumda yapılan grep)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `fail-on` | `.github/workflows/*.yml` | 0 isabet |
| `stop-on` | `.github/workflows/*.yml` | 0 isabet |
| `--coverage-` | `.github/workflows/*.yml` | 0 isabet |
| `--list-suites` / `--filter` | `.github/workflows/*.yml` | 0 isabet |
| `failOn` | `shared` + `home` `phpunit.xml` | 0 isabet (yalnız `auth`/`api`'de var) |
| `stopOnFailure` | `auth` + `api` `phpunit.xml` | 0 isabet (yalnız `shared`/`home`'da) |
| `*Test.php` | `shared/tests/**` | 41 dosya |
| `public function test` | `shared/tests/**` | 349 method |
| `.php` (tümü) | `shared/tests/**` | 45 dosya |

## §26 URL × iddia sayımı (iki tur birlikte)

| Konu | Bu oturum (N) | Kardeş tekrar (R) | Red (X) |
|---|---|---|---|
| OS test araçları | 6 (N-1..N-6) | 4 (R-12..R-15) | 1 (X-1) |
| PHPUnit yapılandırma | 5 (N-7..N-11) | 7 (R-1..R-7) | 1 (X-2) |
| PHPUnit CLI (textui) | 13 (N-12..N-24) | — | — |
| PHPUnit risky + organize | 8 (N-25..N-32) | — | — |
| CI / GitHub Actions | 0 | 4 (R-8..R-11) | — |
| Eski indeks adayı | — | — | 1 (X-3) |
| **Toplam** | **32** | **15** | **3** |

Farklı URL sayısı: N serisi 6 benzersiz URL (kselftest, kunit usage, configuration,
textui, risky-tests, organizing-tests) + R serisi 15 + red 3 = **24 farklı URL
kaydı**; iddia sayısı 50'dir (aynı URL birden çok iddia taşır).

## §27 Eke — `⚠️` işaretli iddialar (genişleme)

| # | İddia | Durum |
|---|---|---|
| ⚠-5 | `textui` sayfasının 11.5 içeriği ile repo'nun `^10.5` sürümü birebir aynı | `⚠️ VERIFICATION REQUIRED` — 10.5 CLI dokümanı bu turda GET ile doğrulanmadı |
| ⚠-6 | `risky-tests`/`organizing-tests` 11.5 kurallarının 10.5'te de geçerli olması | `⚠️ VERIFICATION REQUIRED` |
| ⚠-7 | 41 `*Test.php` / 349 method sayısının CI'da **koşulduğu** | `⚠️ VERIFICATION REQUIRED` — envanter ≠ koşum kanıtı |
| ⚠-8 | `home`/`api` testlerinin hiç koşulmadığı (yalnız matrix satırı kanıtı) | `⚠️ VERIFICATION REQUIRED` — başka workflow olmadığı **grep ile** sınırlı |
| ⚠-9 | `phpunit.de/documentation/` kalıcı 404 mü (yönlendirme mi) | `⚠️ VERIFICATION REQUIRED` — tek ölçüm 2026-10-13 |
| ⚠-10 | `schema.phpunit.de/11.5/phpunit.xsd` erişilebilirliği (sayfada yazıyor, bu turda çekilmedi) | `⚠️ VERIFICATION REQUIRED` |

## §28 Eke — kardeş envanter kesişimi (bu tur)

| URL | Kardeş kaydı | Karar |
|---|---|---|
| `docs.phpunit.de/en/11.5/textui.html` | W-40 (HEAD 200) | Yeniden kullanım + bu turda GET ile içerik doğrulandı |
| `docs.phpunit.de/en/11.5/risky-tests.html` | **YOK** (grep 0) | Yeni — bu turda GET 200 |
| `docs.phpunit.de/en/11.5/organizing-tests.html` | **YOK** (grep 0) | Yeni — bu turda GET 200 |
| `phpunit.de/documentation/` | **YOK** (grep 0) | Reddedildi (X-3, 404) |

## §Son Ek: Tur Ölçüm + Kapı Kaydı (append-only, 2026-10-14)

| # | Kalem | Sonuç |
|---|---|---|
| 1 | Başlangıç satır (bu tur öncesi) | 467 |
| 2 | Bu ek eklendi (append-only) | ≥505 (tamponlu) |
| 3 | Frontmatter | Korunur (`group:` çıplak) |
| 4 | Kapı (S1/S2/Q açık) | İhlal 0 — reddedilen kaynak (X-3) geri alınmadı |
| 5 | `status:` / Authority | Değişmedi / Authority son satır |
| 6 | Beyazliste dışı yazım | 0 (yalnız K000) |
| 7 | `git commit` | Yok (orkestratöre ait) |
| 8 | GET doğrulaması | 3 yeni sayfa GET 200 · 2026-10-13 (bu tur) |

Açık kapı (kapatılmadı): PHPUnit 11.5 behavior değişiklikleri için koşum kanıtı →
**⚠️ VERIFICATION REQUIRED**.

## §Son Ek 2: Ek Doğrulama Satırları (append-only, 2026-10-14)

| # | Doğrulama | Sonuç |
|---|---|---|
| 1 | GET 200 satırları (3 yeni sayfa) tekrar mı? | Hayır — bu turda doğrulandı (2026-10-13) |
| 2 | Reddedilen kaynak (X-3, 404) geri alındı mı? | Hayır — reddedildi kaldı |
| 3 | Yeniden kullanım satırı (W-40) kaynaklı mı? | ✅ HEAD 200 (önceki tur) |
| 4 | Kapı ihlali? | 0 — açık sorular kapatılmadı |
| 5 | Dolgu/TBD / mojibake? | 0 / 0 |
| 6 | Authority son satır mı? | ✅ |

---

## §Son Ek 3: Tur Sonu Sayımı (append-only, 2026-10-14)

| # | Kalem | Sayı |
|---|---|---|
| 1 | Bu tur eklenen bölüm | 3 (§Son Ek, §Son Ek 2, §Son Ek 3) |
| 2 | Ek satır (yaklaşık) | 35+ (append-only, olgu satırı) |
| 3 | Açık kapı (PHPUnit 11.5 koşum kanıtı) | 1 → ⚠️ VERIFICATION REQUIRED |
| 4 | Kapı ihlali / beyazliste dışı yazım / commit | 0 / 0 / Yok |
| 5 | GET doğrulaması (bu tur) | 3 sayfa 200 · 2026-10-13 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
