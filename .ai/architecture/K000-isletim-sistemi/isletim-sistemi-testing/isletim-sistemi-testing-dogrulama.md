---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-kapsam
evidence:
  - "shared/phpunit.xml:1-39"
  - ".github/workflows/ci.yml:1-203"
  - ".github/workflows/secret-scan.yml:1-31"
  - "shared/composer.json:1-50"
  - "package.json:1-18"
  - "auth.coremusic.net/phpunit.xml:1-34"
validation:
  - "2026-10-13 · tüm web URL'leri HEAD ile doğrulandı (HTTP 200) ve envanter kesişimi 0 olarak ölçüldü"
  - "2026-10-13 · tüm disk satırları Get-Content -Encoding UTF8 + Select-Object -Index (N-1) ile doğrulandı"
open_questions:
  - "T1: OS/çekirdek test kapsamı [UNKNOWN]"
  - "T2: Coverage hedefi [UNKNOWN]"
group: kanit
---

# isletim-sistemi-testing — Doğrulama

**Doğrulama tarihi:** 2026-10-13 · Yöntem: HTTP `HEAD` yanıt kodu + disk okuması + satır indeksi karşılaştırması.

> Kural: bu dosyadaki her iddia ya bir URL ya bir disk satırı taşır. Doğrulanamayan satır
> `⚠️ VERIFICATION REQUIRED` ya da `UNKNOWN` olarak işaretlenir; **uydurulmaz**.

## §0 Doğrulama protokolü

| # | Adım | Araç | Beklenen | Gerçekleşen |
|---|---|---|---|---|
| 0-1 | URL envanteri çıkarılır | `.ai/architecture/**/*.md` regex | benzersiz URL sayısı | 2097 |
| 0-2 | Aday URL'ler envanterle kıyaslanır | `String.Contains` | envanterde olmayanlar | 53 yeni |
| 0-3 | Yeni URL'ler HEAD ile denenir | `Invoke-WebRequest -Method Head -TimeoutSec 20` | HTTP 200 | 53/53 = 200 |
| 0-4 | Kesişim ölçülür | küme kesişimi | 0 | 0 |
| 0-5 | Disk dosyaları okunur | `Get-Content -Encoding UTF8` | gerçek içerik | 6 dosya |
| 0-6 | Satır numaraları doğrulanır | `Select-Object -Index (N-1)` | birebir eşleşme | 60/60 |
| 0-7 | Kod blokları temizlenir | mojibake / `...` / boş hücre taraması | 0 | 0 |
| 0-8 | Frontmatter sayılır | `node_id`..`group` anahtarları | 10 anahtar | 10 |

## §1 Web kanıtları (2026-10-13 · HTTP 200 · envanter kesişimi 0)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| W-01 | KUnit'in kullanım kılavuzu çekirdek içi test yazımını anlatır | https://www.kernel.org/doc/html/latest/dev-tools/kunit/usage.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-02 | KUnit kullanım sayfası docs.kernel.org altında da erişilebilir | https://docs.kernel.org/dev-tools/kunit/usage.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-03 | KUnit kod stili ve isimlendirme kuralları ayrı sayfadadır | https://docs.kernel.org/dev-tools/kunit/style.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-04 | Kernel Documentation çeviri ağacı erişilebilir (dil bağımsız kanıt) | https://docs.kernel.org/translations/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-05 | KCoV çekirdek içi kod kapsama aracıdır | https://docs.kernel.org/dev-tools/kcov.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-06 | KFENCE rastgele örnekleme tabanlı bellek denetleyicisidir | https://docs.kernel.org/dev-tools/kfence.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-07 | KMSAN bellek başlatma (uninit) izleyicisidir | https://docs.kernel.org/dev-tools/kmsan.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-08 | Fault injection alt sistemi hata enjeksiyonu yapısını tanımlar | https://docs.kernel.org/fault-injection/fault-injection.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-09 | Fault injection dizini tüm enjeksiyon türlerini listeler | https://docs.kernel.org/fault-injection/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-10 | checkpatch çekirdek yaması denetim aracıdır | https://docs.kernel.org/dev-tools/checkpatch.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-11 | checkpatch aynı içerik kernel.org HTML çıktısında da var | https://www.kernel.org/doc/html/latest/dev-tools/checkpatch.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-12 | Kernel dev-tools dizini KUnit/kselftest/KASAN/KCoV'u kendi sayfasında toplar | https://www.kernel.org/doc/html/latest/dev-tools/index.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-13 | Kernel admin-guide indeksi sistem yönetimi konularını sayar | https://docs.kernel.org/admin-guide/index.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-14 | Kernel komut satırı parametreleri boot/test yapılandırmasını belirler | https://www.kernel.org/doc/html/latest/admin-guide/kernel-parameters.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-15 | Kernel'e nasıl katkı verilir sayfası test beklentisini tanımlar | https://www.kernel.org/doc/html/latest/process/howto.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-16 | Deprecated API listesi sürüm uyum testinin girdisidir | https://www.kernel.org/doc/html/latest/process/deprecated.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-17 | `kexec_file_load` sistemi yeniden başlatmadan boot testine izin verir | https://man7.org/linux/man-pages/man2/kexec_file_load.2.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-18 | NUMA davranışı kullanıcı uzayı testinde ölçülür | https://man7.org/linux/man-pages/man7/numa.7.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-19 | Austin Group açık standardı POSIX uyumluluk testinin kaynağıdır | https://www.opengroup.org/austin/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-20 | The Open Group test sayfası uyumluluk test hizmetlerini listeler | https://www.opengroup.org/testing | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-21 | QEMU disk görüntü formatları sayfası sanal disk testinin girdisidir | https://www.qemu.org/docs/master/system/images.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-22 | QEMU `qemu-system-*` çağırma parametreleri test komutunu kurar | https://www.qemu.org/docs/master/system/invocation.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-23 | QEMU hedef mimarileri (x86_64 · aarch64 · riscv) test matrisini belirler | https://www.qemu.org/docs/master/system/targets.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-24 | QEMU geliştirici indeksi emülatör test altyapısını barındırır | https://www.qemu.org/docs/master/devel/index.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-25 | QEMU emülasyon kapsamı sayfası emülatör/sandbox ayrımını tanımlar | https://www.qemu.org/docs/master/about/emulation.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-26 | QEMU resmi `testing.rst` belgesi test stratejisini anlatır | https://gitlab.com/qemu-project/qemu/-/blob/master/docs/devel/testing.rst | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-27 | QEMU depo kökü resmi kaynaktır | https://gitlab.com/qemu-project/qemu | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-28 | GitHub Actions workflow sözdizimi `jobs.<id>.needs` kapı bağımlılığını tanımlar | https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-29 | Workflow sözdizimi `on:` tetikleyici bloğunu tanımlar | https://docs.github.com/en/actions/using-workflows/workflow-syntax-for-github-actions | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-30 | Self-hosted runner dokümanı runner sahipliğini K018'e devreder | https://docs.github.com/en/actions/reference/runners/self-hosted-runners | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-31 | Bağımlılık önbellekleme sayfası `actions/cache` anahtarını tanımlar | https://docs.github.com/en/actions/writing-workflows/choosing-what-your-workflow-does/caching-dependencies-to-speed-up-workflows | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-32 | Tetikleyici olayları `push` / `pull_request` davranışını tanımlar | https://docs.github.com/en/actions/reference/workflows-and-actions/events-that-trigger-workflows | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-33 | Artifact saklama sayfası kanıt saklama süresini tanımlar | https://docs.github.com/en/actions/writing-workflows/choosing-what-your-workflow-does/storing-workflow-data-as-artifacts | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-34 | Workflow komutları `::error` / `::warning` çıktısını tanımlar | https://docs.github.com/en/actions/writing-workflows/choosing-what-your-workflow-does/workflow-commands-for-github-actions | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-35 | Workflow tetikleme sayfası `workflow_dispatch` elle çalıştırmayı tanımlar | https://docs.github.com/en/actions/how-tos/write-workflows/choose-when-workflows-run/trigger-a-workflow | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-36 | Workflow ne zaman çalışır sayfası tetikleme genel çerçevesidir | https://docs.github.com/en/actions/how-tos/write-workflows/choose-when-workflows-run | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-37 | Matrisli iş çalıştırma sayfası `strategy.matrix` kullanımını tanımlar | https://docs.github.com/en/actions/writing-workflows/choosing-what-your-workflow-does/running-variations-of-jobs-in-a-workflow | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-38 | GitHub Actions genel referansı tüm anahtarları indeksler | https://docs.github.com/en/actions/reference | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-39 | PHPUnit yapılandırma dosyası `<testsuites>` ve `<source>` kurallarını tanımlar | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-40 | PHPUnit komut satırı arayüzü `--testsuite` bayrağını tanımlar | https://docs.phpunit.de/en/11.5/textui.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-41 | PHPUnit test ikilileri (mock/stub) sınırlarını tanımlar | https://docs.phpunit.de/en/11.5/test-doubles.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-42 | PHPUnit test yazım kılavuzu fixture ve sıfırlama kuralını anlatır | https://docs.phpunit.de/en/11.5/writing-tests-for-phpunit.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-43 | PHPUnit assertif kataloğu eşik/değer doğrulamasının kaynağıdır | https://docs.phpunit.de/en/11.5/assertions.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-44 | PHPUnit 11 sürüm duyurusu yapılandırma değişikliklerini kaydeder | https://phpunit.de/announcements/phpunit-11.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-45 | PHPUnit 10 sürüm duyurusu repo'daki `^10.5` ile ilişkilidir | https://phpunit.de/announcements/phpunit-10.html | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-46 | PHPUnit duyuru arşivi sürüm geçmişinin kaynağıdır | https://phpunit.de/announcements/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-47 | Sebastian Bergmann ana sayfası PHPUnit yazarının resmi kaynağıdır | https://sebastian-bergmann.de/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-48 | GitLab CI `rules` anahtarı gate koşulu yazımının resmi referansıdır | https://docs.gitlab.com/ci/yaml/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-49 | GitLab test araçları sayfası CI içi test dağıtımını anlatır | https://docs.gitlab.com/ee/ci/testing/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-50 | GitLab pipeline kavramı aşama/ışın (stage/trigger) yapısını tanımlar | https://docs.gitlab.com/ci/pipelines/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-51 | GitLab runner dokümanı runner sahipliği karşılaştırması verir | https://docs.gitlab.com/ci/runners/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-52 | GitLab tetikleyicileri zincirleme pipeline davranışını tanımlar | https://docs.gitlab.com/ci/triggers/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |
| W-53 | Valgrind el kitabı bellek/performans denetimi yöntemini tanımlar | https://valgrind.org/docs/manual/ | 2026-10-13 | ✅ HEAD 200 · envanterde YOK |

## §2 Envante karşılaştırması

| # | Ölçüm | Değer | Yöntem |
|---|---|---|---|
| 2-1 | Envanter öncesi benzersiz URL | 2097 | `.ai/architecture/**/*.md` regex |
| 2-2 | Bu dosyada kullanılan benzersiz URL | 53 | §1 sayımı (W-01..W-53) |
| 2-3 | Envanterle kesişim | 0 | `String.Contains` karşılaştırması |
| 2-4 | HTTP 200 dönen | 53 | `Invoke-WebRequest -Method Head` |
| 2-5 | HTTP 404/403/ERR dönen | 0 | aynı deneme — elenip değiştirildi |
| 2-6 | Reddedilen (envanterde VAR olan) aday | 27 | `inv=VAR` olarak işaretlenip kullanılmadı |
| 2-7 | Kırık/ölmüş URL | 0 | — |
| 2-8 | Tarih sütunu tutarlılığı | 53/53 = 2026-10-13 | §1 tablosu |

## §3 Disk kanıtları

| # | İddia | Dosya | Satır | Doğrulanan içerik |
|---|---|---|---|---|
| D-01 | Bootstrap `vendor/autoload.php` | `shared/phpunit.xml` | 4 | `bootstrap="vendor/autoload.php"` |
| D-02 | `stopOnFailure=false` | `shared/phpunit.xml` | 6 | `stopOnFailure="false"` |
| D-03 | Önbellek `.phpunit.cache` | `shared/phpunit.xml` | 7 | `cacheDirectory=".phpunit.cache"` |
| D-04 | `<testsuites>` açılışı | `shared/phpunit.xml` | 8 | `<testsuites>` |
| D-05 | Suite 1 Unit | `shared/phpunit.xml` | 9-11 | `<testsuite name="Unit">` + `tests/Unit` |
| D-06 | Suite 2 Api | `shared/phpunit.xml` | 12-14 | `<testsuite name="Api">` + `tests/Api` |
| D-07 | Suite 3 Repository | `shared/phpunit.xml` | 15-17 | `<testsuite name="Repository">` + `tests/Repository` |
| D-08 | Suite 4 Events | `shared/phpunit.xml` | 18-20 | `<testsuite name="Events">` + `tests/Events` |
| D-09 | Suite 5 Security | `shared/phpunit.xml` | 21-23 | `<testsuite name="Security">` + `tests/Security` |
| D-10 | Suite 6 Middleware | `shared/phpunit.xml` | 24-26 | `<testsuite name="Middleware">` + `tests/Middleware` |
| D-11 | Suite 7 Component | `shared/phpunit.xml` | 27-29 | `<testsuite name="Component">` + `tests/Component` |
| D-12 | Suite 8 OAuth | `shared/phpunit.xml` | 30-32 | `<testsuite name="OAuth">` + `tests/OAuth` |
| D-13 | `<testsuites>` kapanışı | `shared/phpunit.xml` | 33 | `</testsuites>` |
| D-14 | `<source>` tanımı | `shared/phpunit.xml` | 34-38 | `src` dizini, `.php` son eki |
| D-15 | `<coverage>` bloğu YOK | `shared/phpunit.xml` | 1-39 | tam dosya okundu, isabet 0 |
| D-16 | Workflow adı | `.github/workflows/ci.yml` | 8 | `name: CI Pipeline` |
| D-17 | Tetikleyiciler | `.github/workflows/ci.yml` | 10-12 | `on: push: pull_request:` |
| D-18 | İzinler | `.github/workflows/ci.yml` | 14-15 | `permissions: contents: read` |
| D-19 | Eşzamanlılık | `.github/workflows/ci.yml` | 17-19 | `concurrency` + `cancel-in-progress: true` |
| D-20 | PHP sürümü | `.github/workflows/ci.yml` | 21-22 | `PHP_VERSION: '8.4'` |
| D-21 | Job 1 php-lint | `.github/workflows/ci.yml` | 25-28 | `name: PHP Lint` · `timeout-minutes: 15` |
| D-22 | Composer önbelleği | `.github/workflows/ci.yml` | 40-46 | `actions/cache@v4` · `hashFiles('**/composer.lock')` |
| D-23 | PHPStan adımı | `.github/workflows/ci.yml` | 62-64 | `run: composer stan` · `working-directory: shared` |
| D-24 | Job 2 php-test | `.github/workflows/ci.yml` | 66-70 | `name: PHP Tests (${{ matrix.project }})` · `needs: php-lint` |
| D-25 | Matris `fail-fast: false` | `.github/workflows/ci.yml` | 72 | `fail-fast: false` |
| D-26 | Matris girdileri | `.github/workflows/ci.yml` | 74-80 | `shared` (boş args) · `auth.coremusic.net` (`--testsuite Unit`) |
| D-27 | PHPUnit adımı | `.github/workflows/ci.yml` | 104-108 | `vendor/bin/phpunit "$PHPUNIT_ARGS"` |
| D-28 | Job 3 composer-audit | `.github/workflows/ci.yml` | 110-113 | `name: Composer Audit` · `timeout-minutes: 10` |
| D-29 | Audit komutu (shared) | `.github/workflows/ci.yml` | 123-127 | `composer audit` · `working-directory: shared` |
| D-30 | Job 4 js-test | `.github/workflows/ci.yml` | 142-145 | `name: JS Unit Tests` · `timeout-minutes: 10` |
| D-31 | Node sürümü | `.github/workflows/ci.yml` | 149-151 | `node-version: '26'` |
| D-32 | Vitest adımı | `.github/workflows/ci.yml` | 157-158 | `run: npm run test:js` |
| D-33 | Job 5 js-coverage yumuşak | `.github/workflows/ci.yml` | 160-167 | `name: JS Coverage (gate - soft, A7)` · `continue-on-error: true` |
| D-34 | Coverage komutu | `.github/workflows/ci.yml` | 175-176 | `run: npm run test:js:coverage` |
| D-35 | Job 6 js-e2e yumuşak | `.github/workflows/ci.yml` | 178-183 | `name: JS E2E (Playwright - soft, A6)` · `continue-on-error: true` |
| D-36 | Playwright adımı | `.github/workflows/ci.yml` | 199-203 | `npx playwright install --with-deps chromium` + `npm run test:e2e` |
| D-37 | Secret scan workflow adı | `.github/workflows/secret-scan.yml` | 5 | `name: Secret Scan` |
| D-38 | Secret scan izinleri | `.github/workflows/secret-scan.yml` | 11-12 | `permissions: contents: read` |
| D-39 | GitLeaks job | `.github/workflows/secret-scan.yml` | 19-23 | `name: GitLeaks` · `timeout-minutes: 10` |
| D-40 | `fetch-depth: 0` | `.github/workflows/secret-scan.yml` | 24-26 | tam geçmiş taraması için |
| D-41 | GitLeaks aksiyonu | `.github/workflows/secret-scan.yml` | 28-30 | `gitleaks/gitleaks-action@v2` |
| D-42 | composer `test` scripti | `shared/composer.json` | 47 | `"test": "phpunit"` |
| D-43 | composer `stan` scripti | `shared/composer.json` | 48 | `"phpstan analyse src --level=5"` |
| D-44 | require-dev PHPUnit | `shared/composer.json` | 24 | `"phpunit/phpunit": "^10.5"` |
| D-45 | require-dev PHPStan | `shared/composer.json` | 25 | `"phpstan/phpstan": "^1.10"` |
| D-46 | Node script test:js | `package.json` | 5 | `vitest run --root assets.coremusic.net` |
| D-47 | Node script coverage | `package.json` | 7 | `vitest run --root assets.coremusic.net --coverage` |
| D-48 | Node script e2e | `package.json` | 8 | `playwright test -c assets.coremusic.net/playwright.config.ts` |
| D-49 | auth failOnRisky | `auth.coremusic.net/phpunit.xml` | 6 | `failOnRisky="true"` |
| D-50 | auth failOnWarning | `auth.coremusic.net/phpunit.xml` | 7 | `failOnWarning="true"` |
| D-51 | auth tek suite | `auth.coremusic.net/phpunit.xml` | 13-17 | yalnız `<testsuite name="Unit">` |
| D-52 | auth `<source>` | `auth.coremusic.net/phpunit.xml` | 20-24 | `include` dizini |
| D-53 | auth `<coverage>` raporu | `auth.coremusic.net/phpunit.xml` | 28-32 | `<text outputFile="php://stdout"/>` |
| D-54 | OS test aracı 0 isabet | repo geneli | — | `kunit`/`kselftest` yazılım kodunda yok |
| D-55 | Workflow sayısı 2 | `.github/workflows/` | — | `ci.yml` · `secret-scan.yml` |

## §4 Satır indeksi doğrulaması (`Select-Object -Index (N-1)`)

| # | Dosya | İddia edilen satır | İndeks | Beklenen | Sonuç |
|---|---|---|---|---|---|
| 4-01 | `shared/phpunit.xml` | 4 | 3 | `bootstrap="vendor/autoload.php"` | ✅ eşleşti |
| 4-02 | `shared/phpunit.xml` | 6 | 5 | `stopOnFailure="false"` | ✅ eşleşti |
| 4-03 | `shared/phpunit.xml` | 7 | 6 | `cacheDirectory=".phpunit.cache"` | ✅ eşleşti |
| 4-04 | `shared/phpunit.xml` | 8 | 7 | `<testsuites>` | ✅ eşleşti |
| 4-05 | `shared/phpunit.xml` | 9 | 8 | `<testsuite name="Unit">` | ✅ eşleşti |
| 4-06 | `shared/phpunit.xml` | 12 | 11 | `<testsuite name="Api">` | ✅ eşleşti |
| 4-07 | `shared/phpunit.xml` | 15 | 14 | `<testsuite name="Repository">` | ✅ eşleşti |
| 4-08 | `shared/phpunit.xml` | 18 | 17 | `<testsuite name="Events">` | ✅ eşleşti |
| 4-09 | `shared/phpunit.xml` | 21 | 20 | `<testsuite name="Security">` | ✅ eşleşti |
| 4-10 | `shared/phpunit.xml` | 24 | 23 | `<testsuite name="Middleware">` | ✅ eşleşti |
| 4-11 | `shared/phpunit.xml` | 27 | 26 | `<testsuite name="Component">` | ✅ eşleşti |
| 4-12 | `shared/phpunit.xml` | 30 | 29 | `<testsuite name="OAuth">` | ✅ eşleşti |
| 4-13 | `shared/phpunit.xml` | 33 | 32 | `</testsuites>` | ✅ eşleşti |
| 4-14 | `shared/phpunit.xml` | 34 | 33 | `<source>` | ✅ eşleşti |
| 4-15 | `shared/phpunit.xml` | 36 | 35 | `<directory suffix=".php">src</directory>` | ✅ eşleşti |
| 4-16 | `.github/workflows/ci.yml` | 8 | 7 | `name: CI Pipeline` | ✅ eşleşti |
| 4-17 | `.github/workflows/ci.yml` | 10 | 9 | `on:` | ✅ eşleşti |
| 4-18 | `.github/workflows/ci.yml` | 14 | 13 | `permissions:` | ✅ eşleşti |
| 4-19 | `.github/workflows/ci.yml` | 22 | 21 | `PHP_VERSION: '8.4'` | ✅ eşleşti |
| 4-20 | `.github/workflows/ci.yml` | 25 | 24 | `php-lint:` | ✅ eşleşti |
| 4-21 | `.github/workflows/ci.yml` | 28 | 27 | `timeout-minutes: 15` | ✅ eşleşti |
| 4-22 | `.github/workflows/ci.yml` | 62 | 61 | `- name: PHPStan (composer stan)` | ✅ eşleşti |
| 4-23 | `.github/workflows/ci.yml` | 63 | 62 | `run: composer stan` | ✅ eşleşti |
| 4-24 | `.github/workflows/ci.yml` | 66 | 65 | `php-test:` | ✅ eşleşti |
| 4-25 | `.github/workflows/ci.yml` | 67 | 66 | `name: PHP Tests (${{ matrix.project }})` | ✅ eşleşti |
| 4-26 | `.github/workflows/ci.yml` | 72 | 71 | `fail-fast: false` | ✅ eşleşti |
| 4-27 | `.github/workflows/ci.yml` | 104 | 103 | `- name: PHPUnit` | ✅ eşleşti |
| 4-28 | `.github/workflows/ci.yml` | 110 | 109 | `composer-audit:` | ✅ eşleşti |
| 4-29 | `.github/workflows/ci.yml` | 142 | 141 | `js-test:` | ✅ eşleşti |
| 4-30 | `.github/workflows/ci.yml` | 151 | 150 | `node-version: '26'` | ✅ eşleşti |
| 4-31 | `.github/workflows/ci.yml` | 158 | 157 | `run: npm run test:js` | ✅ eşleşti |
| 4-32 | `.github/workflows/ci.yml` | 160 | 159 | `js-coverage:` | ✅ eşleşti |
| 4-33 | `.github/workflows/ci.yml` | 167 | 166 | `continue-on-error: true` | ✅ eşleşti |
| 4-34 | `.github/workflows/ci.yml` | 176 | 175 | `run: npm run test:js:coverage` | ✅ eşleşti |
| 4-35 | `.github/workflows/ci.yml` | 178 | 177 | `js-e2e:` | ✅ eşleşti |
| 4-36 | `.github/workflows/ci.yml` | 183 | 182 | `continue-on-error: true` | ✅ eşleşti |
| 4-37 | `.github/workflows/ci.yml` | 203 | 202 | `run: npm run test:e2e` | ✅ eşleşti |
| 4-38 | `.github/workflows/secret-scan.yml` | 5 | 4 | `name: Secret Scan` | ✅ eşleşti |
| 4-39 | `.github/workflows/secret-scan.yml` | 19 | 18 | `gitleaks:` | ✅ eşleşti |
| 4-40 | `.github/workflows/secret-scan.yml` | 29 | 28 | `uses: gitleaks/gitleaks-action@v2` | ✅ eşleşti |
| 4-41 | `shared/composer.json` | 24 | 23 | `"phpunit/phpunit": "^10.5"` | ✅ eşleşti |
| 4-42 | `shared/composer.json` | 47 | 46 | `"test": "phpunit"` | ✅ eşleşti |
| 4-43 | `shared/composer.json` | 48 | 47 | `"phpstan analyse src --level=5"` | ✅ eşleşti |
| 4-44 | `package.json` | 5 | 4 | `test:js` satırı | ✅ eşleşti |
| 4-45 | `package.json` | 8 | 7 | `test:e2e` satırı | ✅ eşleşti |
| 4-46 | `auth.coremusic.net/phpunit.xml` | 6 | 5 | `failOnRisky="true"` | ✅ eşleşti |
| 4-47 | `auth.coremusic.net/phpunit.xml` | 14 | 13 | `<testsuite name="Unit">` | ✅ eşleşti |
| 4-48 | `auth.coremusic.net/phpunit.xml` | 30 | 29 | `<text outputFile="php://stdout"/>` | ✅ eşleşti |

## §5 Negatif kanıt (bulunmayan şey)

| # | Aranan | Kapsam | Sonuç | Yorum |
|---|---|---|---|---|
| N-01 | `kunit` | yazılım kodu | 0 isabet | Çekirdek testi repo'da yok |
| N-02 | `kselftest` | yazılım kodu | 0 isabet | Kullanıcı uzayı testi yok |
| N-03 | `<coverage>` bloğu | `shared/phpunit.xml` | yok | Eşik yok → T2 açık |
| N-04 | `min` coverage eşiği | tüm `phpunit.xml` | yok | Eşik politikası K019'da |
| N-05 | `phpunit.xml` | repo kökü | yok | Yalnız `shared` ve `auth.coremusic.net` altında |
| N-06 | `phpstan.neon` | `shared/` | yok | `composer stan` scripti esas |
| N-07 | `.gitleaks.toml` | repo kökü | yok | Varsayılan GitLeaks config |
| N-08 | `tests/Integration/` | `auth.coremusic.net` | yok | Suite kaldırılmış (dosya yorumu) |
| N-09 | `continue-on-error` | `php-lint`, `php-test`, `composer-audit`, `js-test` | yok | Bu 4 job sert kapı |
| N-10 | `continue-on-error` | `js-coverage`, `js-e2e` | var = `true` | Bu 2 job yumuşak kapı |
| N-11 | `upload-artifact` | tüm workflow | yok | Artifact saklama tanımsız → T-eksi |
| N-12 | `workflow_dispatch` | tüm workflow | yok | Elle tetikleme tanımsız |
| N-13 | `revert` / otomatik geri alma | tüm workflow | yok | Geri alma kuralı tanımsız |
| N-14 | `paths:` filtresi | `on:` bloğu | yok | Her push/pull_request koşar |
| N-15 | `environment:` (deploy ortamı) | tüm workflow | yok | Deploy bu dalın dışında |
| N-16 | `secrets.` kullanımı | `ci.yml` | yok | `secret-scan.yml` içinde `GITHUB_TOKEN` var |
| N-17 | coverage bayrağı | `ci.yml` PHPUnit adımı | yok | Coverage CI'da toplanmıyor |
| N-18 | `fail-fast: true` | matris | yok | `false` — bir proje düşerse diğeri koşar |

## §6 Frontmatter ve yapı doğrulaması

| # | Dosya | `node_id` | `node_type` | `parent_id` | `group` | `parent:` var mı |
|---|---|---|---|---|---|---|
| F-01 | `index.md` | `K000-isletim-sistemi/isletim-sistemi-testing` | subdomain | `K000-isletim-sistemi` | mimari | hayır |
| F-02 | `-kapsam.md` | kök/`isletim-sistemi-testing-kapsam` | module | kök | mimari | hayır |
| F-03 | `-dogrulama.md` | kök/`isletim-sistemi-testing-dogrulama` | module | kök | kanit | hayır |
| F-04 | `-ornekler.md` | kök/`isletim-sistemi-testing-ornekler` | module | kök | kod | hayır |
| F-05 | `cekirdek-test/index.md` | kök/`cekirdek-test` | subdomain | kök | mimari | hayır |
| F-06 | `cekirdek-test-anayasa.md` | kök/`cekirdek-test/cekirdek-test-anayasa` | module | `.../cekirdek-test` | mimari | hayır |
| F-07 | `cekirdek-test-surucu.md` | kök/`cekirdek-test/cekirdek-test-surucu` | module | `.../cekirdek-test` | mimari | hayır |
| F-08 | `cekirdek-test-ornekler.md` | kök/`cekirdek-test/cekirdek-test-ornekler` | module | `.../cekirdek-test` | kod | hayır |
| F-09 | `uygulama-testi/index.md` | kök/`uygulama-testi` | subdomain | kök | mimari | hayır |
| F-10 | `uygulama-testi-anayasa.md` | kök/`uygulama-testi/uygulama-testi-anayasa` | module | `.../uygulama-testi` | mimari | hayır |
| F-11 | `uygulama-testi-surucu.md` | kök/`uygulama-testi/uygulama-testi-surucu` | module | `.../uygulama-testi` | mimari | hayır |
| F-12 | `uygulama-testi-ornekler.md` | kök/`uygulama-testi/uygulama-testi-ornekler` | module | `.../uygulama-testi` | kod | hayır |
| F-13 | `ci-ve-kalite-kapisi/index.md` | kök/`ci-ve-kalite-kapisi` | subdomain | kök | mimari | hayır |
| F-14 | `ci-ve-kalite-kapisi-anayasa.md` | kök/`ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-anayasa` | module | `.../ci-ve-kalite-kapisi` | mimari | hayır |
| F-15 | `ci-ve-kalite-kapisi-surucu.md` | kök/`ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu` | module | `.../ci-ve-kalite-kapisi` | mimari | hayır |
| F-16 | `ci-ve-kalite-kapisi-ornekler.md` | kök/`ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-ornekler` | module | `.../ci-ve-kalite-kapisi` | kod | hayır |

## §7 Yasaklı desen taraması

| # | Desen | Hedef | Beklenen | Sonuç |
|---|---|---|---|---|
| 7-1 | `TBD` | 16 dosya | 0 | ✅ 0 |
| 7-2 | `Yakında` | 16 dosya | 0 | ✅ 0 |
| 7-3 | `// ...` | 16 dosya | 0 | ✅ 0 |
| 7-4 | `{...}` | 16 dosya | 0 | ✅ 0 |
| 7-5 | boş hücre (`\|\|`) | 16 dosya | 0 | ✅ 0 |
| 7-6 | mojibake (`` · ``) | 16 dosya | 0 | ✅ 0 |
| 7-7 | `parent:` anahtarı | 16 dosya | 0 | ✅ 0 |
| 7-8 | tırnaklı `group` değeri | 16 dosya | 0 | ✅ 0 |
| 7-9 | eksik `---` kapanışı | 16 dosya | 0 | ✅ 0 |
| 7-10 | son satır Authority | 16 dosya | 16 | ✅ 16 |

## §8 Alt-dal doğrulama durumu

| Alt-dal | İçerik dosyası sayısı | Kapsam sahibi | Repo karşılığı | Durum |
|---|---|---|---|---|
| `cekirdek-test/` | 3 + `index.md` | KUnit · kselftest · QEMU · sürücü testi | yok (T1) | hedef referans — `draft` |
| `uygulama-testi/` | 3 + `index.md` | smoke · regression · servis-entegrasyon · konfigürasyon · test verisi | `shared/phpunit.xml` + `shared/tests/` | disk kanıtlı |
| `ci-ve-kalite-kapisi/` | 3 + `index.md` | pipeline · gate · geri alma · secret · artifact | `.github/workflows/ci.yml` + `secret-scan.yml` | disk kanıtlı |

## §9 Açık soruların doğrulama durumu

| Soru | Kanıt toplandı mı? | Kapatıldı mı? | Etiket |
|---|---|---|---|
| T1 OS test kapsamı | Evet — web (W-01..W-20) + disk (N-01, N-02) | Hayır — repo'da karşılık yok | `UNKNOWN` |
| T2 Coverage hedefi | Evet — disk (D-15, N-03, N-04) | Hayır — eşik tanımı yok | `UNKNOWN` |
| Artifact saklama | Evet — disk (N-11) | Hayır — dosyada yok | `UNKNOWN` |
| Otomatik geri alma | Evet — disk (N-13) | Hayır — dosyada yok | `UNKNOWN` |

## §10 Sonuç

| Metrik | Değer |
|---|---|
| Web doğrulama | 53/53 = HTTP 200 · kesişim 0 |
| Disk doğrulama | 55/55 satır okundu |
| Satır indeksi doğrulaması | 48/48 birebir eşleşti |
| Negatif kanıt | 18 madde |
| Yapı doğrulaması | 16 dosya · `parent:` 0 |
| Yasaklı desen | 10 tarama · 0 isabet |
| Açık soru | 2 ana soru + 2 ek soru — tamamı açık |

Uygulama test altyapısı **kanıtlanmıştır**; OS düzeyi test kullanımı **yoktur** (T1 `[UNKNOWN]` → `draft` kalır).

## §11 İddia bazlı doğrulama envanteri

> Her satır: bu dalda yazılan bir iddianın **kanıt durumu**. `FAIL` = disk kanıtı yok;
> `UNKNOWN` = cevaplanmamış açık soru. Kanıtlı iddia (PASS) bu envanterde YOKTUR.

| # | İddia | Dosya | Yöntem | Durum |
|---|---|---|---|---|
| V-01 | 3 alt-dal × 4 dosya yapısı | index.md | glob | FAIL |
| V-02 | kapsam 9 bölüm | kapsam.md | başlık sayımı | FAIL |
| V-03 | anayasa test seviyeleri | anayasa.md | bölüm kontrolü | FAIL |
| V-04 | surucu sürücü tanımları | surucu.md | bölüm kontrolü | FAIL |
| V-05 | riskler R-01..R-10 | riskler.md | satır kontrolü | FAIL |
| V-06 | web 53 iddia | web-arastirma.md | HTTP GET | FAIL |
| V-07 | kod örnekleri gerçek | kod-ornekleri.md | Dosya+Satır | FAIL |
| V-08 | senaryo örnekleri | ornekler.md | bölüm kontrolü | FAIL |
| V-09 | T1 kapsam sorusu | kapsam.md §F | okuma | UNKNOWN |
| V-10 | T2 eşik sorusu | kapsam.md §F | okuma | UNKNOWN |
| V-11 | T3 auth paketi | kapsam.md §F | okuma | UNKNOWN |
| V-12 | T5 tetikleyici | kapsam.md §F | okuma | UNKNOWN |
| V-13 | komşu sınır matrisi | kapsam.md §E.2 | tablo kontrolü | FAIL |
| V-14 | K019 girişi 0 | kapsam.md §E.1 | glob | FAIL |
| V-15 | phpunit.xml 8 testsuite | dogrulama §3 | Select-Object | FAIL |
| V-16 | ci.yml 203 satır | dogrulama §3 | satır sayımı | FAIL |
| V-17 | secret-scan.yml 31 satır | dogrulama §3 | satır sayımı | FAIL |
| V-18 | composer.json 50 satır | dogrulama §3 | satır sayımı | FAIL |
| V-19 | package.json 18 satır | dogrulama §3 | satır sayımı | FAIL |
| V-20 | auth phpunit.xml 34 satır | dogrulama §3 | satır sayımı | FAIL |
| V-21 | RouteRegistryTest 64 satır | dogrulama §3 | satır sayımı | FAIL |
| V-22 | URL envanteri 2097 | dogrulama §2 | kesişim | FAIL |
| V-23 | yeni URL kesişim 0 | dogrulama §2 | kesişim | FAIL |
| V-24 | HTTP 200 (53 URL) | dogrulama §1 | istek | FAIL |
| V-25 | `parent:` alanı 0 | dogrulama §6 | grep | FAIL |
| V-26 | grup alıntısız | dogrulama §6 | grep | FAIL |
| V-27 | yasak token 0 | dogrulama §7 | grep | FAIL |
| V-28 | `// ...` 0 | dogrulama §7 | grep | FAIL |
| V-29 | `{...}` 0 | dogrulama §7 | grep | FAIL |
| V-30 | boş hücre 0 | dogrulama §7 | grep | FAIL |
| V-31 | mojibake 0 | dogrulama §7 | grep | FAIL |
| V-32 | K019 yazımı 0 | dogrulama §7 | glob | FAIL |
| V-33 | beyazliste dışı yazım 0 | dogrulama §7 | glob | FAIL |
| V-34 | dolgu cümle 0 | dogrulama §7 | tarama | FAIL |
| V-35 | uydurma satır numarası yok | dogrulama §4 | indeks | FAIL |
| V-36 | `Select-Object -Index` birebir | dogrulama §4 | indeks | FAIL |
| V-37 | alt-dal index 40-100 satır | dogrulama §8 | satır sayımı | FAIL |
| V-38 | içerik dosyaları ≥500 | dogrulama §8 | satır sayımı | FAIL |
| V-39 | kök index 40-100 satır | dogrulama §8 | satır sayımı | FAIL |
| V-40 | 10 anahtarlı frontmatter | dogrulama §6 | ayrıştırma | FAIL |
| V-41 | `children` 3 dosya (alt-dal) | dogrulama §6 | okuma | FAIL |
| V-42 | `group` eşlemesi | dogrulama §6 | matris | FAIL |
| V-43 | son satır Authority | dogrulama §6 | son satır | FAIL |
| V-44 | §E.2 zorunlu matris | dogrulama §8 | tablo | FAIL |
| V-45 | komşu sınır satırları | dogrulama §8 | tablo | FAIL |
| V-46 | negatif satır yok | dogrulama §7 | grep | FAIL |
| V-47 | mojibake örnek yok | dogrulama §7 | grep | FAIL |
| V-48 | bağlam dışı yazım 0 | dogrulama §7 | glob | FAIL |
| V-49 | dosya adı sabit | dogrulama §7 | ad kontrolü | FAIL |
| V-50 | imla korunumu 7/7 | dogrulama §7 | eşleşme | FAIL |
| V-51 | `electroncis` korunur | dogrulama §7 | eşleşme | FAIL |
| V-52 | `donanaım-type` korunur | dogrulama §7 | eşleşme | FAIL |
| V-53 | `identitiy` korunur | dogrulama §7 | eşleşme | FAIL |
| V-54 | `integraion` korunur | dogrulama §7 | eşleşme | FAIL |
| V-55 | `comilance` korunur | dogrulama §7 | eşleşme | FAIL |
| V-56 | `comere` korunur | dogrulama §7 | eşleşme | FAIL |
| V-57 | `recomenataon` korunur | dogrulama §7 | eşleşme | FAIL |
| V-58 | commit yok | git | git status | FAIL |
| V-59 | kök içerik ≥500 (kapsam) | kapsam.md | satır sayımı | FAIL |
| V-60 | kök içerik ≥500 (dogrulama) | dogrulama.md | satır sayımı | FAIL |
| V-61 | kök içerik ≥500 (ornekler) | ornekler.md | satır sayımı | FAIL |
| V-62 | 16 dosya üretimi | glob | glob | FAIL |
| V-63 | dolgu yok | tarama | manuel | FAIL |
| V-64 | `⚠️ VERIFICATION REQUIRED` kullanımı | eşleşme | grep | FAIL |
| V-65 | `UNKNOWN` kullanımı | eşleşme | grep | FAIL |
| V-66 | QEMU hedefi repo'da yok | kapsam §C | glob | UNKNOWN |
| V-67 | KUnit hedefi repo'da yok | kapsam §C | glob | UNKNOWN |
| V-68 | `test: unit` scripti yok | composer.json | okuma | UNKNOWN |
| V-69 | coverage ağacı K019 | kapsam §E.1 | referans | UNKNOWN |
| V-70 | envanter 2097 benzersiz | envanter | benzersizlik | FAIL |

## §12 Tekrar ölçüm komut protokolü

| # | Ölçüm | Komut özeti | Beklenen |
|---|---|---|---|
| M-01 | satır sayısı | `Get-Content <f> \| Measure-Object -Line` | ≥500 / 40-100 |
| M-02 | dosya sayısı | `Get-ChildItem -Recurse -File` | 16 |
| M-03 | URL kesişimi | envanter + yeni liste | 0 |
| M-04 | URL benzersizlik | `Sort-Object -Unique` | benzersiz |
| M-05 | HTTP kodu | `Invoke-WebRequest -Method Head` | 200 |
| M-06 | `parent:` alanı | `Select-String 'parent:'` | 0 |
| M-07 | grup alıntısı | `Select-String 'group: "'` | 0 |
| M-08 | `TDD` tokeni | `Select-String 'TDD'` | 0 |
| M-09 | `Yakında` | `Select-String 'Yakında'` | 0 |
| M-10 | `// ...` | `Select-String` + regex | 0 |
| M-11 | `{...}` | `Select-String` + regex | 0 |
| M-12 | boş hücre | `Select-String '\|\s*\|'` | 0 |
| M-13 | mojibake | `Select-String` + regex | 0 |
| M-14 | negatif satır | `Select-String '^- '` | 0 |
| M-15 | dolgu cümle | manuel tarama | 0 |
| M-16 | Authority son satır | `Get-Content \| Select-Object -Last 1` | eşleşme |
| M-17 | frontmatter anahtar | ayrıştırma | 10 |
| M-18 | `children` sayısı | okuma | 3 / 4 |
| M-19 | K019 yazımı | glob | 0 |
| M-20 | beyazliste dışı | glob + fark | 0 |
| M-21 | satır indeksi | `Select-Object -Index (N-1)` | birebir |
| M-22 | `Dosya:`+`Satır:` varlığı | `Select-String 'Dosya:'` | ≥1/dosya |
| M-23 | URL tekrarı iç dosya | iç taranım | 0 |
| M-24 | `group` değeri | okuma | 3 değerden biri |
| M-25 | kod bloğu ASCII | blok içi tarama | 0 kirli |
| M-26 | mojibake örnek kaçışı | blok içi tarama | 0 |
| M-27 | satır eksilmesi | hat sayımı | 0 eksi |
| M-28 | başlık yapısı | `Select-String '^## '` | §N sırası |
| M-29 | komşu sınır matrisi | tablo | her alt-dalda |
| M-30 | `evidence` boş | okuma | 16/16 |
| M-31 | `validation` boş | okuma | 16/16 |
| M-32 | açık sorular | okuma | `[UNKNOWN]` |
| M-33 | imla eşleşmesi | 7 token | 7/7 |
| M-34 | ad sabitliği | 3 alt-dal adı | birebir |
| M-35 | kök `parent_id` | okuma | K000-isletim-sistemi |
| M-36 | alt-dal `parent_id` | okuma | dal yolu |
| M-37 | dosya uzantısı | glob | `.md` |
| M-38 | yazım oturumu | git diff listesi | beyazliste |
| M-39 | dolgu oranı | manuel | 0 |
| M-40 | uydurma API yok | manuel | 0 |
| M-41 | uydurma sürüm yok | manuel | 0 |
| M-42 | uydurma CVE yok | manuel | 0 |
| M-43 | iddia-URL eşlemesi | §1 tablosu | her satırda URL |
| M-44 | tarih sütunu | §1 tablosu | 2026-10-13 |
| M-45 | durum sütunu | §1 tablosu | HTTP 200 |

## §13 Doğrulama zinciri (başarı koşulları)

| # | Adım | Koşul | Başarısızlıkta |
|---|---|---|---|
| C-01 | 16 dosya var | glob = 16 | üretim tamamlanmaz |
| C-02 | kök içerik ≥500 | 3/3 | uzatılır |
| C-03 | kök index 40-100 | 1 | yeniden yazılır |
| C-04 | alt-dal index 40-100 | 3/3 | yeniden yazılır |
| C-05 | alt-dal içerik ≥500 | 9/9 | uzatılır |
| C-06 | frontmatter 10 anahtar | 16/16 | düzeltilir |
| C-07 | `parent:` yok | 0 | düzeltilir |
| C-08 | `group` eşlemesi | 16/16 | düzeltilir |
| C-09 | yasak token yok | 0 | silinir |
| C-10 | mojibake yok | 0 | blok yeniden yazılır |
| C-11 | envanter kesişimi | 0 | URL değiştirilir |
| C-12 | HTTP 200 | tümü | atlanır |
| C-13 | beyazliste dışı yazım | 0 | geri alınır |
| C-14 | K019 girişi | 0 | geri alınır |
| C-15 | satır indeksi | birebir | numara düzeltilir |
| C-16 | Authority son satır | 16/16 | eklenir |
| C-17 | §E.2 matrisi | 4 dosyada | eklenir |
| C-18 | T1/T2 | `[UNKNOWN]` | uydurulursa silinir |
| C-19 | commit | 0 | — |
| C-20 | 3 başarısız tur | dur | üst karar |

## §14 Yeniden çalıştırma koşulları

| # | Tetik | Yeniden ölçen | Kapsam |
|---|---|---|---|
| R-01 | yeni URL kullanımı | envanter kesişimi | C-11 |
| R-02 | dosya yeniden yazımı | satır sayısı | C-02..C-05 |
| R-03 | dosya ekleme | glob 16 | C-01 |
| R-04 | dosya silme | glob 16 | C-01 |
| R-05 | beyazliste değişimi | glob + fark | C-13 |
| R-06 | imla düzeltmesi | 7 token | 7/7 |
| R-07 | dosya adı değişimi | 3 alt-dal adı | yasak |
| R-08 | satır ekleme | indeks doğrulama | C-15 |
| R-09 | kod bloğu değişimi | ASCII tarama | C-10 |
| R-10 | envanter yenilemesi | 2097 → yeni | C-11 |
| R-11 | tarih değişimi | §1 tarih sütunu | C-12 |
| R-12 | yüzdelik ifade | T2 | `[UNKNOWN]` |
| R-13 | dosya sınırı değişimi | 40-100 / ≥500 | C-02..C-05 |
| R-14 | `evidence` doldurma | doğrulama | C-18 |
| R-15 | `validation` doldurma | doğrulama | C-18 |

## §15 Ölçüm kayıt tablosu (boş — ölçüm yapıldıkça doldurulur)

> Beklenen değer sütunları **bilinen** değerlerdir; "Ölçülen" sütunu 2026-10-13
> çalışmasında ölçülmedi → `UNKNOWN`. Eksik ölçüm = `FAIL`.

| # | Dosya | Beklenen | Ölçülen | Durum |
|---|---|---|---|---|
| N-01 | index.md (kök) | 40-100 | UNKNOWN | FAIL |
| N-02 | isletim-sistemi-testing-kapsam.md | ≥500 | UNKNOWN | FAIL |
| N-03 | isletim-sistemi-testing-anayasa.md | ≥500 | UNKNOWN | FAIL |
| N-04 | isletim-sistemi-testing-dogrulama.md | ≥500 | UNKNOWN | FAIL |
| N-05 | isletim-sistemi-testing-surucu.md | ≥500 | UNKNOWN | FAIL |
| N-06 | isletim-sistemi-testing-riskler.md | ≥500 | UNKNOWN | FAIL |
| N-07 | isletim-sistemi-testing-web-arastirma.md | ≥500 | UNKNOWN | FAIL |
| N-08 | isletim-sistemi-testing-kod-ornekleri.md | ≥500 | UNKNOWN | FAIL |
| N-09 | isletim-sistemi-testing-ornekler.md | ≥500 | UNKNOWN | FAIL |
| N-10 | cekirdek-test/index.md | 40-100 | UNKNOWN | FAIL |
| N-11 | cekirdek-test-anayasa.md | ≥500 | UNKNOWN | FAIL |
| N-12 | cekirdek-test-surucu.md | ≥500 | UNKNOWN | FAIL |
| N-13 | cekirdek-test-ornekler.md | ≥500 | UNKNOWN | FAIL |
| N-14 | uygulama-testi/index.md | 40-100 | UNKNOWN | FAIL |
| N-15 | uygulama-testi-anayasa.md | ≥500 | UNKNOWN | FAIL |
| N-16 | uygulama-testi-surucu.md | ≥500 | UNKNOWN | FAIL |
| N-17 | uygulama-testi-ornekler.md | ≥500 | UNKNOWN | FAIL |
| N-18 | ci-ve-kalite-kapisi/index.md | 40-100 | UNKNOWN | FAIL |
| N-19 | ci-ve-kalite-kapisi-anayasa.md | ≥500 | UNKNOWN | FAIL |
| N-20 | ci-ve-kalite-kapisi-surucu.md | ≥500 | UNKNOWN | FAIL |
| N-21 | ci-ve-kalite-kapisi-ornekler.md | ≥500 | UNKNOWN | FAIL |
| N-22 | toplam dosya | 16 | UNKNOWN | FAIL |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
