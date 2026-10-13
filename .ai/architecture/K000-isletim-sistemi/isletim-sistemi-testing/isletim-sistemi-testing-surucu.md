---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-anayasa
evidence:
  - "shared/composer.json:46-49"
  - "shared/phpunit.xml:4-33"
  - ".github/workflows/ci.yml:24-203"
  - ".github/workflows/secret-scan.yml:18-31"
validation:
  - "2026-10-13 · komut akışı ci.yml satırlarından türetildi (Read 1-203); hiçbir komut uydurulmadı"
  - "2026-10-13 · PHPUnit komut satırı iddiaları docs.phpunit.de (kardeş W-40) ile sınırlandırıldı"
open_questions:
  - "T1: OS testi sürücüsü (kselftest/KUnit komut zinciri) repoda yok — hedef referans olarak kalır [UNKNOWN]"
  - "T2: Kapsama komutu eşiği ve raporu yok — --coverage-* komutu hiçbir job'da koşmuyor [UNKNOWN]"
group: mimari
---

# isletim-sistemi-testing Sürücüsü — Test Orkestrasyonu ve Komut Akışı

## §0 ELI15

Anayasa "neyi" test edeceğimizi söyler; bu dosya **nasıl koşturduğumuzu** yazar.
Sürücü üç katmandır: (1) **yerel komut** (geliştirici `composer test` çalıştırır),
(2) **CI iş akışı** (`ci.yml` job'ları komutları sırayla ve paralel koşar),
(3) **kapı kararı** (job kırmızı ise birleştirme engellenir). Her komut bu dosyada
**ya bir repo satırından ya da resmi dokümandan** gelir; zincirde uydurma adım yoktur.
OS düzeyi test (kselftest/KUnit) bu depoda **temsil edilmediği için sürücüsü de yoktur** —
yalnız hedef referans §10'da taşınır.

## §1 Komut akışı — yerel

| # | Komut | Yer | Satır kanıtı | Ne yapar |
|---|---|---|---|---|
| C-1 | `composer test` | `shared/` | `composer.json:47` (`"test": "phpunit"`) | Tüm suite'i koşar |
| C-2 | `composer stan` | `shared/` | `composer.json:48` (`"stan": "phpstan analyse src --level=5"`) | Statik analiz |
| C-3 | `vendor/bin/phpunit` | proje kökü | `ci.yml:105` | CI'nin çağırdığı binary |
| C-4 | `vendor/bin/phpunit --testsuite Unit` | `auth.coremusic.net/` | `ci.yml:80` (matrix args) | Yalnız Unit suite |
| C-5 | `npm run test:js` | repo kökü | `ci.yml:158` | JS unit (Vitest) |
| C-6 | `npm run test:js:coverage` | repo kökü | `ci.yml:176` | JS kapsaması (soft job) |
| C-7 | `npm run test:e2e` | repo kökü | `ci.yml:203` | Playwright e2e (soft job) |

> C-1 ve C-2 **composer script**'lerdir; CI bu scriptleri değil binary'yi çağırır
> (`ci.yml:105` `vendor/bin/phpunit`, `ci.yml:63` `composer stan`). İki çağırma
> yolu aynı aracı hedefler, ayrılmaları sürüklenme üretmez.

## §2 CI job akışı (ci.yml — 203 satır)

| Sıra | Job | Adımlar (özet) | Süre sınırı | Bağımlılık |
|---|---|---|---|---|
| 1 | `php-lint` | checkout → setup-php (apcu.enable_cli=1) → composer install ×3 → `composer stan` | 15 dk (`ci.yml:28`) | — |
| 2 | `php-test` | checkout → setup-php → composer install (matrix) → `vendor/bin/phpunit "$PHPUNIT_ARGS"` | 15 dk (`ci.yml:70`) | `needs: php-lint` (`ci.yml:68`) |
| 3 | `composer-audit` | checkout → setup-php → install + `composer audit` ×3 | 10 dk (`ci.yml:113`) | — |
| 4 | `js-test` | checkout → setup-node 26 → `npm ci` → `npm run test:js` | 10 dk (`ci.yml:145`) | — |
| 5 | `js-coverage` | checkout → setup-node 26 → `npm ci` → `npm run test:js:coverage` | 15 dk (`ci.yml:164`) | `needs: js-test` (`ci.yml:162`) · soft |
| 6 | `js-e2e` | checkout → setup-php → setup-node 26 → `npm ci` → playwright install chromium → `npm run test:e2e` | 20 dk (`ci.yml:181`) | — · soft |

`php-test` matrix'i iki satırlıdır (`ci.yml:74-80`): `shared` (boş args) ve
`auth.coremusic.net` (`--testsuite Unit`). Matrix notu, `tests/Integration`
dizininin diskte yokluğunu belgeler (`ci.yml:77-78`) — yani suite seçimi
**disk kanıtıyla** gerekçelendirilmiştir.

## §3 Sıralama ve paralellik

| İlişki | Değer | Sonuç |
|---|---|---|
| lint → test | `needs: php-lint` (`ci.yml:68`) | Test, lint geçmeden başlamaz |
| js-test → js-coverage | `needs: js-test` (`ci.yml:162`) | Kapsama, unit geçmeden başlamaz |
| php-test ↔ composer-audit | bağımlılık yok | Paralel koşar |
| php-test ↔ js-* | bağımlılık yok | Paralel koşar |
| Tetikleyiciler | `push` + `pull_request` (`ci.yml:10-12`) | Her iki olayda da tüm job'lar |
| Eşzamanlılık | `concurrency` + `cancel-in-progress: true` (`ci.yml:17-19`) | Aynı ref için eski koşu iptal |
| Yetki | `permissions: contents: read` (`ci.yml:14-15`) | Job token'ı yazma yetkisi almaz |

## §4 Ortam ve bağımlılık katmanları

| Katman | Değer | Satır |
|---|---|---|
| PHP sürümü | `PHP_VERSION: '8.4'` (tek kaynak env) | `ci.yml:21-22` |
| PHP uzantıları | `apcu, mbstring, json, pdo, pdo_mysql` | `ci.yml:37` |
| APCu CLI | `ini-values: apcu.enable_cli=1` | `ci.yml:38` (ve `ci.yml:90`) |
| Composer önbelleği | `actions/cache@v4` + `hashFiles('**/composer.lock')` | `ci.yml:41-46` |
| Node sürümü | `node-version: '26'` | `ci.yml:151` (ve `ci.yml:172`, `ci.yml:194`) |
| npm önbelleği | `cache: npm` | `ci.yml:152` |
| Node kurulumu | `npm ci` (kilitli kurulum) | `ci.yml:155`, `ci.yml:174`, `ci.yml:197` |

> `apcu.enable_cli=1` zorunludur: APCu adaptörü CLI'da (test sürecinde) kapalı
> gelir; bu satır olmadan cache'e bağlı testler sahte hata üretir.

## §5 Suite seçimi (PHPUnit sürücüsü)

| Durum | Komut | Neden |
|---|---|---|
| `shared` | `vendor/bin/phpunit` (args boş — `ci.yml:76`) | 8 testsuite'ın tamamı diskte mevcut |
| `auth.coremusic.net` | `vendor/bin/phpunit --testsuite Unit` (`ci.yml:80`) | `tests/Integration` dizini diskte YOK (`ci.yml:77-78` notu) |
| Yerel tek test | `vendor/bin/phpunit --filter <ad>` | PHPUnit CLI yeteneği (doküman: kardeş W-40) |
| Bootstrap | `phpunit.xml:4` | Autoloader kaydı |

Suite yapılandırmasının XML karşılığı `shared/phpunit.xml:8-33` (8 `<testsuite>`);
kaynak sınırı `phpunit.xml:34-38` (`<source>`). Kapsama raporu **bu zincirde yok**.

## §6 Başarısızlık yönetimi

| Olay | Sürücü davranışı | Kanıt |
|---|---|---|
| Lint kırmızı | `php-test` hiç başlamaz (`needs`) | `ci.yml:68` |
| Test kırmızı | Job kırmızı → sert kapı → birleştirme engellenir | `php-test` job'unda `continue-on-error` YOK |
| Kapsama eşiği tutmazsa | Job kırmızı görünür ama birleştirmeyi **engellemez** | `continue-on-error: true` (`ci.yml:167`) |
| e2e kararsızsa | Job kırmızı görünür ama engellemez | `continue-on-error: true` (`ci.yml:183`) |
| Audit advisory bulursa | `composer audit` çıkış kodu → job kırmızı (sert) | `ci.yml:126` (soft yok) |
| Secret bulunursa | Ayrı workflow kırmızı → eskalasyon `.github/AGENTS.md` §3.2 | `secret-scan.yml:28-31` |
| Runner zaman aşımı | `timeout-minutes` job'ı keser | `ci.yml:28,70,113,145,164,181` |

## §7 Secret tarama sürücüsü (secret-scan.yml — 31 satır)

| Adım | Değer | Satır |
|---|---|---|
| Tetikleyici | `push` + `pull_request` | `secret-scan.yml:7-9` |
| Yetki | `permissions: contents: read` | `secret-scan.yml:11-12` |
| İş | `gitleaks` → ubuntu-latest | `secret-scan.yml:19-22` |
| Checkout | `fetch-depth: 0` (tüm geçmiş — aksi halde tarama kör kalır) | `secret-scan.yml:26` |
| Aksiyon | `gitleaks/gitleaks-action@v2` | `secret-scan.yml:29` |
| Token | `${{ secrets.GITHUB_TOKEN }}` | `secret-scan.yml:31` |
| Yapılandırma | `.gitleaks.toml` YOK → varsayılan config | `secret-scan.yml:4` (dosya notu) |

## §8 Repo disk kanıtları

| Dosya | Okunan aralık | Sürücüye katkısı |
|---|---|---|
| `.github/workflows/ci.yml` | 1-203 | §2-§6'nın tamamı |
| `.github/workflows/secret-scan.yml` | 1-31 | §7 |
| `shared/composer.json` | 1-50 | §1 (scripts L46-49) |
| `shared/phpunit.xml` | 1-39 | §5 (suite L8-33, source L34-38) |
| `auth.coremusic.net/phpunit.xml` | 1-34 | matrix args gerekçesi (`ci.yml:77-78`) |

## §9 Kapsam dışı

| Konu | Sınır |
|---|---|
| Job ekleme/çıkarma onayı | `.github/AGENTS.md` §3.3 sınır 4 (kullanıcı onayı) |
| Deploy adımları | `K018-devops` — bu dalda yok |
| GitLeaks kural dosyası (`.gitleaks.toml`) | Security onayı + ADR (`.github/AGENTS.md` §3.1) |
| OS test komut zinciri (`make kselftest` vb.) | Hedef referans §10 — bu depoda çalışmaz |

## §10 Hedef referans — OS test sürücüsü (bu depoda DEĞİL)

| Zincir | Adım | Kaynak |
|---|---|---|
| KUnit | `kunit.py` / `make targets=...` ile test derleme + çalıştırma | docs.kernel.org kunit usage (2026-10-13 HTTP 200) |
| kselftest | `make -C tools/testing/selftests run_tests` | docs.kernel.org kselftest (2026-10-13 HTTP 200) |
| Raporlama | TAP çıktısı + timeout varsayılanı 45 sn/test | aynı sayfalar |

Bu üç satır **hedef mimari**dir; bu depoda karşılığı olan job/komut **yoktur**
(anayasa §17 negatif kanıt ile aynı gerçek).

## §11 Kaynakça (2026-10-13 · HTTP 200)

| İddia | URL | Not |
|---|---|---|
| kselftest komut akışı + TAP | https://docs.kernel.org/dev-tools/kselftest.html | webfetch 200 |
| KUnit test yazım akışı | https://docs.kernel.org/dev-tools/kunit/usage.html | webfetch 200 · kardeş W-02 |
| PHPUnit CLI (`--testsuite`, filtreleme) | https://docs.phpunit.de/en/11.5/textui.html | kardeş W-40 |
| GitHub Actions workflow sözdizimi (`needs`, `matrix`) | https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax | kardeş W-28 |
| Tetikleyici olayları (`push`, `pull_request`) | https://docs.github.com/en/actions/reference/workflows-and-actions/events-that-trigger-workflows | kardeş W-32 |

## §12 Eke — komut kataloğu (tam)

| Komut | Dosya:Satır | Tür |
|---|---|---|
| `composer install --no-progress --prefer-dist` | `ci.yml:49,53,57,101,125,131,137` | Kurulum |
| `composer stan` | `ci.yml:63` | Kapı |
| `composer audit` | `ci.yml:126,132,138` | Kapı |
| `vendor/bin/phpunit "$PHPUNIT_ARGS"` | `ci.yml:105` | Kapı |
| `npm ci` | `ci.yml:155,174,197` | Kurulum |
| `npm run test:js` | `ci.yml:158` | Kapı |
| `npm run test:js:coverage` | `ci.yml:176` | Soft kapı |
| `npx playwright install --with-deps chromium` | `ci.yml:200` | Kurulum |
| `npm run test:e2e` | `ci.yml:203` | Soft kapı |

## §13 Eke — job × kapı sertliği matrisi

| Job | Sertlik | `continue-on-error` | `timeout-minutes` |
|---|---|---|---|
| `php-lint` | Sert | Yok | 15 |
| `php-test` | Sert | Yok | 15 |
| `composer-audit` | Sert | Yok | 10 |
| `js-test` | Sert | Yok | 10 |
| `js-coverage` | Soft | `true` (`ci.yml:167`) | 15 |
| `js-e2e` | Soft | `true` (`ci.yml:183`) | 20 |
| `gitleaks` (ayrı workflow) | Sert | Yok | 10 |

## §14 Eke — orkestrasyon kararları

| # | Karar | Gerekçe | Reddedilen |
|---|---|---|---|
| O-1 | `php-test`, `php-lint`'e `needs` ile bağlı | Anlamsız test koşumu önlenir | Paralel koşturma |
| O-2 | Matrix `fail-fast: false` (`ci.yml:72`) | Bir projenin hatası diğerini saklamaz | Varsayılan fail-fast |
| O-3 | Suite seçimi args ile (`--testsuite Unit`) | Diskte olmayan dizin `failOnWarning` kırmaz | Tüm suite koşturmak |
| O-4 | Cache anahtarı `composer.lock` hash'i | Kilit değişince cache düşer | Sabit anahtar |
| O-5 | `cancel-in-progress: true` | Eski koşu kaynak israfı | İki eşzamanlı koşu |

## §15 Eke — başarısızlık triyajı

| Belirti | Olasılık kaynağı | İlk bakılacak satır |
|---|---|---|
| `php-test` hiç başlamadı | `php-lint` kırmızı | `ci.yml:68` |
| Suite bulunamadı hatası | `--testsuite` adı ≠ XML adı | `ci.yml:80` × `phpunit.xml:8-33` |
| APCu fonksiyon hatası | `apcu.enable_cli=1` eksik | `ci.yml:38`, `ci.yml:90` |
| JS testleri Node sürümü hatası | engines uyuşmazlığı | `ci.yml:151` |
| Job kırmızı ama birleşti | Soft kapı | `ci.yml:167`, `ci.yml:183` |
| GitLeaks "no commits found" | `fetch-depth` ≠ 0 | `secret-scan.yml:26` |

## §16 Eke — açık sorular

| # | Soru | Etki |
|---|---|---|
| T1 | OS testi sürücüsü bu depoya ne zaman girer? | §10 hedef referans kalır |
| T2 | Kapsama komutu (`--coverage-*`) hiçbir job'da yok; eşiğe karar verilmeden eklenmez mi? | js-coverage soft kapının PHP karşılığı doğmaz |

## §17 Suite kataloğu — `shared/phpunit.xml` 8 suite → komut karşılığı

| # | Suite adı | XML satırı | Hedef dizin | `--testsuite` ile koşum |
|---|---|---|---|---|
| 1 | `Unit` | `phpunit.xml:9-11` | `tests/Unit` | `vendor/bin/phpunit --testsuite Unit` |
| 2 | `Api` | `phpunit.xml:12-14` | `tests/Api` | `--testsuite Api` |
| 3 | `Repository` | `phpunit.xml:15-17` | `tests/Repository` | `--testsuite Repository` |
| 4 | `Events` | `phpunit.xml:18-20` | `tests/Events` | `--testsuite Events` |
| 5 | `Security` | `phpunit.xml:21-23` | `tests/Security` | `--testsuite Security` |
| 6 | `Middleware` | `phpunit.xml:24-26` | `tests/Middleware` | `--testsuite Middleware` |
| 7 | `Component` | `phpunit.xml:27-29` | `tests/Component` | `--testsuite Component` |
| 8 | `OAuth` | `phpunit.xml:30-32` | `tests/OAuth` | `--testsuite OAuth` |

Suite listesini **dosya olmadan** görmek `--list-suites` ile mümkündür
(organizing-tests 11.5 · 2026-10-13 GET 200). Bu depoda `--list-suites`
**hiçbir job'da koşmaz** (workflow grep: 0 isabet) — komut teoriktir, kapıyı
değiştirmez.

## §18 Test hedefi envanteri — suite başına dosya ve method (2026-10-13 sayımı)

| Suite | `*Test.php` | `public function test` | CI args |
|---|---|---|---|
| Unit | 16 | 113 | args boş (tümü) |
| Api | 8 | 67 | args boş |
| Middleware | 9 | 76 | args boş |
| Security | 2 | 40 | args boş |
| Repository | 2 | 25 | args boş |
| Events | 2 | 15 | args boş |
| OAuth | 1 | 8 | args boş |
| Component | 1 | 5 | args boş |
| **Toplam** | **41** | **349** | `ci.yml:76` args `''` |

> Sayım yöntemi: `Get-ChildItem -Filter *Test.php` + `Select-String 'public function test'`.
> Bu **envanterdir, sonuç değildir** — "349 test geçti" **denmez** (koşum kanıtı yok).
> `shared/tests/**` toplam 45 `.php` içerir; kalan 4'ü fixture/altyapı
> (`Fixtures/chrome/header.php`, `footer.php`, `Repository/Sqlite/SqliteDatabaseManager.php`,
> `SqliteDatabaseRegistry.php`).

## §19 Yapılandırma × komut eşlemesi (4 `phpunit.xml`)

| Proje | Dosya | Bootstrap | Suite | CI komutu (satır) |
|---|---|---|---|---|
| shared | `shared/phpunit.xml` (39 s) | `vendor/autoload.php` (L4) | 8 | `vendor/bin/phpunit` args `''` (`ci.yml:76,105`) |
| auth | `auth.coremusic.net/phpunit.xml` (34 s) | `tests/bootstrap.php` (L4) | 1 (Unit) | `vendor/bin/phpunit --testsuite Unit` (`ci.yml:80,105`) |
| home | `home.coremusic.net/phpunit.xml` (18 s) | `vendor/autoload.php` (L4) | 1 (Unit) | **Yok** — matrix 2 satır (`ci.yml:74-80`) |
| api | `api.coremusic.net/phpunit.xml` (29 s) | `tests/bootstrap.php` (L4) | 1 (Unit) | **Yok** — matrix 2 satır |

`--configuration` bayrağı gerekmez: çalışma dizininde `phpunit.xml` adı PHPUnit
tarafından otomatik bulunur (textui 11.5). `working-directory: ${{ matrix.project }}`
(`ci.yml:108`) bu yüzden tek başına yeterlidir.

## §20 Komut senaryoları — doküman destekli, repoda kullanımı işaretli

| # | Senaryo | Komut | Kaynak | Bu depoda |
|---|---|---|---|---|
| S-1 | Tüm suite | `vendor/bin/phpunit` | textui (config otomatik) | ✅ `ci.yml:105` (args `''`) |
| S-2 | Tek suite | `vendor/bin/phpunit --testsuite Unit` | textui `--testsuite` | ✅ `ci.yml:80` |
| S-3 | Suite listesi | `vendor/bin/phpunit --list-suites` | organizing-tests | ✗ 0 isabet |
| S-4 | Tek test | `vendor/bin/phpunit --filter <ad>` | textui `--filter` | ✗ 0 isabet |
| S-5 | Dizin hedefi | `vendor/bin/phpunit tests/Api` | organizing-tests (`*Test.php` taraması) | ✗ 0 isabet |
| S-6 | İlk hatada dur | `vendor/bin/phpunit --stop-on-failure` | textui (9 `--stop-on-*`) | ✗ 0 isabet; XML `stopOnFailure="false"` |
| S-7 | Uyarıda sertleş | `vendor/bin/phpunit --fail-on-warning` | textui (10 `--fail-on-*`) | ✗ 0 isabet; XML'de `failOnWarning` auth/api'de |
| S-8 | Boş suite'i yakala | `vendor/bin/phpunit --fail-on-empty-test-suite` | textui | ✗ 0 isabet |
| S-9 | Yerel statik analiz | `composer stan` | `composer.json:48` | ✅ `ci.yml:63` |
| S-10 | Yerel test | `composer test` | `composer.json:47` | CI binary çağırır (§1 notu) |

## §21 Çıkış sonucu okuma (textui 11.5 · 2026-10-13 GET 200)

| Özet satırı | Koşul |
|---|---|
| `OK` | Hata yok |
| `OK, but some tests were skipped!` | Hata yok ama atlanan var |
| `OK, but there were issues!` | Hata yok ama sorun (risky/uyarı) var |
| `FAILED!` | Hata var, error yok |
| `ERRORED!` | En az bir test error |

| Kural | Sayfadan okunan | Sürücüye etkisi |
|---|---|---|
| `--fail-on-*` | **Yalnız shell exit kodunu** etkiler; özet `OK` yazısını değiştirmez | Job kırmızılığı exit kodundan gelir |
| `--do-not-fail-on-*` | `--fail-on-all-issues`, eşleşen `--fail-on-*` ve XML `failOn*` üzerine geçer | Sertlik tek bayrakla geri alınabilir |
| Progress karakteri | `.` başarılı · `F` assertion hatası · `E` error · `W` uyarı · `R` risky · `D` deprecation · `N` notice · `I` incomplete · `S` skipped | Log okuma anahtarı |

## §22 Hata kataloğu (ek)

| Belirti | Kaynak | Sürücüdeki karşılığı |
|---|---|---|
| `Test directory not found` + `exit 2` | `auth.coremusic.net/phpunit.xml:10-12` (dosya yorumu) | Suite dizini diskte yoksa **tüm suite** başlatılamaz → `--testsuite Unit` (`ci.yml:77-80`) |
| Suite adı bulunamadı | `--testsuite` ≠ XML adı | `ci.yml:80` × `phpunit.xml:9-32` |
| Boş suite | `--fail-on-empty-test-suite` | Bu depoda bayrak yok → boş suite **sessizce geçer** (⚠ eşik kararı T2) |
| Assertion hatası | `F` + `FAILED!` | `php-test` job'u kırmızı (sert) |
| Error (exception/PHP) | `E` + `ERRORED!` | `php-test` job'u kırmızı (sert) |
| Risky/uyarı | `R`/`W` + `OK, but there were issues!` | shared'da exit kodu **0** kalır; auth/api'de `failOnRisky/failOnWarning=true` → exit kodu başarısız |
| Erken durma | `--stop-on-*` | Kullanılmıyor → tüm hata raporu tek koşuda toplanır |

## §23 Ortam doğrulama — komut öncesi kurulum adımları

| Değer | Satır | Neden komut öncesi |
|---|---|---|
| `php-version: ${{ env.PHP_VERSION }}` (`8.4`) | `ci.yml:22,35` | composer `php >=8.4` şartı (`composer.json:8`) |
| `extensions: apcu, mbstring, json, pdo, pdo_mysql` | `ci.yml:37` | `ext-apcu`/`ext-pdo` zorunlu (`composer.json:9-12`) |
| `ini-values: apcu.enable_cli=1` | `ci.yml:38`, `ci.yml:90` | CLI APCu kapalı gelir → sahte hata |
| `tools: composer` | `ci.yml:36` | `composer install` binary'si hazır olmalı |
| `hashFiles('**/composer.lock')` | `ci.yml:44`, `ci.yml:96` | Kilit değişmeden kurulum tekrarlanmaz |
| `node-version: '26'` | `ci.yml:151,172,194` | vitest/jsdom engines aralığı (`ci.yml:151` notu) |
| `npm ci` | `ci.yml:155,174,197` | Kilitli kurulum (package-lock) |

## §24 Negatif kanıt — workflow'larda olmayan PHPUnit bayrakları

| Arama | Kapsam | Sonuç |
|---|---|---|
| `fail-on` | `.github/workflows/*.yml` | 0 isabet |
| `stop-on` | `.github/workflows/*.yml` | 0 isabet |
| `--coverage-` | `.github/workflows/*.yml` | 0 isabet |
| `--list-suites` | `.github/workflows/*.yml` | 0 isabet |
| `--filter` | `.github/workflows/*.yml` | 0 isabet |
| `--configuration` / `-c ` | `.github/workflows/*.yml` | 0 isabet (config otomatik bulundurulur) |

> T2 ile ilişki: `--coverage-*` yok → PHP kapsama raporu **hiçbir komut zincirinde
> yok**; auth/api'deki `<coverage><report>` bloğu sürücü olmadan rapor üretmez
> (dosyanın kendi yorumu, `phpunit.xml:26-27`).

## §25 Eke — orkestrasyon kararları (genişleme)

| # | Karar | Gerekçe | Reddedilen |
|---|---|---|---|
| O-6 | `--list-suites`/`--filter` CI'a eklenmez | Kapıyı değiştirmez, süre ekler | Tanı adımlarını pipeline'a koymak |
| O-7 | `--stop-on-*` kullanılmaz | Tüm hata raporu tek koşuda görünür (`stopOnFailure="false"`) | Erken durma |
| O-8 | `--fail-on-*` eklenmez | Önce ölçüm yok (T2); eşiksiz sert exit kodu kör kırar | `--fail-on-all-issues` |
| O-9 | `home`/`api` matrix dışı bırakıldı | Job ekleme onay ister (`.github/AGENTS.md` §3.3 sınır 4) | Matrix'e sessiz 3. satır |
| O-10 | Suite adları XML'den **türetilir** (elle yazılmaz) | `ci.yml:80` yalnız `Unit` yazar; 8 suite `phpunit.xml:8-33` | Suite adlarını yml'e gömmek |

## §26 Eke — açık sorular (genişleme — kapatılmaz)

| # | Soru | Durum | Kapatma koşulu |
|---|---|---|---|
| T1 | OS testi sürücüsü (`make kselftest`, `kunit.py`) bu depoya ne zaman girer? | `UNKNOWN` | Çekirdek kodu + kullanıcı kararı |
| T2 | Kapsama komutu (`--coverage-text/html`) hangi job'a, hangi eşiğe bağlanır? | `UNKNOWN` | Ölçüm altyapısı + kullanıcı kararı |
| T3 | `home`/`api` testleri CI'a alınacak mı? | `UNKNOWN` | Kullanıcı onayı (job/matris genişlemesi) |
| T4 | `--fail-on-empty-test-suite` gibi bayraklar sertleştirilecek mi? | `UNKNOWN` | T2 kararı sonrası |

## §27 CI adım envanteri (ci.yml 203 satır — adım adım sayım)

| Job | Adım sırası (satır) | Adım sayısı |
|---|---|---|
| `php-lint` | checkout `:30` → setup-php `:32-38` → cache `:40-46` → install shared `:48-50` → install auth `:52-54` → install home `:56-58` → `composer stan` `:62-64` | 7 |
| `php-test` | checkout `:82` → setup-php `:84-90` → cache `:92-98` → install `:100-102` → PHPUnit `:104-108` | 5 |
| `composer-audit` | checkout `:115` → setup-php `:117-121` → install+audit shared `:123-127` → auth `:129-133` → home `:135-139` | 5 |
| `js-test` | checkout `:147` → setup-node `:149-152` → npm ci `:154-155` → Vitest `:157-158` | 4 |
| `js-coverage` | checkout `:169` → setup-node `:170-173` → npm ci `:174` → coverage `:175-176` | 4 |
| `js-e2e` | checkout `:185` → setup-php `:187-190` → setup-node `:192-195` → npm ci `:197` → playwright install `:199-200` → e2e `:202-203` | 6 |
| **Toplam** | 6 job | **31 adım** |

İki iş kuralı bu sayımdan okunur: (1) `composer install` **4 job'da** tekrarlanır
(`php-lint` üç proje ayrı ayrı, `php-test` matrix dizini, `composer-audit` üç proje),
(2) yalnız `js-e2e` **hem PHP hem Node** kurar (`:187-195`) — Playwright HTML
raporu için PHP statik sunucu gereklidir (adımın kendisi `npm run test:e2e`, `:203`).

## §28 Yerel çalıştırma sırası (geliştirici akışı — komut kanıtlı)

| # | Adım | Komut | Satır kaynağı | Yerelde zorunlu mu |
|---|---|---|---|---|
| L-1 | Bağımlılık kur | `composer install --no-progress --prefer-dist` | `ci.yml:49` (komut metni) | Evet (ilk kez) |
| L-2 | Statik analiz | `composer stan` | `composer.json:48` | Kapı öncesi |
| L-3 | Tüm test | `composer test` | `composer.json:47` | Kapı |
| L-4 | Tek suite | `vendor/bin/phpunit --testsuite Middleware` | textui `--testsuite` (N-12) | Tanı için |
| L-5 | Tek test | `vendor/bin/phpunit --filter <ad>` | textui `--filter` (N-13) | Tanı için |
| L-6 | Suite listesi | `vendor/bin/phpunit --list-suites` | organizing-tests (N-31) | Tanı için |
| L-7 | JS test | `npm run test:js` | `package.json:5` | JS değişikliğinde |
| L-8 | JS kapsam | `npm run test:js:coverage` | `package.json:7` | Bilgi (soft) |
| L-9 | E2E | `npm run test:e2e` | `package.json:8` | Bilgi (soft) |
| L-10 | Bağımlılık audit | `composer audit` | `ci.yml:126` | Güvenlik |

> L-4..L-6 **doküman destekli yerel komutlardır**; hiçbiri CI'da koşmaz (§24 negatif
> kanıt). CI'ın çağırdığı tek PHPUnit komutu `vendor/bin/phpunit "$PHPUNIT_ARGS"` (`ci.yml:105`).

## §29 Secret-scan komut envanteri (ek)

| Adım | Değer | Satır |
|---|---|---|
| Tetikleyici | `push` + `pull_request` | `secret-scan.yml:7-9` |
| Yetki | `permissions: contents: read` | `secret-scan.yml:11-12` |
| Eşzamanlılık | `cancel-in-progress: true` | `secret-scan.yml:14-16` |
| Checkout | `fetch-depth: 0` | `secret-scan.yml:24-26` |
| Tarama | `gitleaks/gitleaks-action@v2` | `secret-scan.yml:28-29` |
| Token | `${{ secrets.GITHUB_TOKEN }}` | `secret-scan.yml:30-31` |
| Süre | `timeout-minutes: 10` | `secret-scan.yml:22` |
| Config | `.gitleaks.toml` YOK → varsayılan (dosya L4 notu) | `secret-scan.yml:4` |

## §30 Komut × kaynak dosya kapsamlı envanter

| Kaynak dosya | Satır | Bu sürücüdeki kullanımı |
|---|---|---|
| `.github/workflows/ci.yml` | 1-203 (tam) | §1-§6, §12-§13, §17, §23, §27 |
| `.github/workflows/secret-scan.yml` | 1-31 (tam) | §7, §29 |
| `shared/composer.json` | 1-50 (tam) | §1 (L46-49), §18 (`require`/`require-dev` L8-25) |
| `shared/phpunit.xml` | 1-39 (tam) | §5, §17 (suite L9-32) |
| `auth.coremusic.net/phpunit.xml` | 1-34 (tam) | §19 + hata kataloğu (L10-12 yorum) |
| `home.coremusic.net/phpunit.xml` | 1-18 (tam) | §19 (CI dışı) |
| `api.coremusic.net/phpunit.xml` | 1-29 (tam) | §19 (CI dışı) |
| `package.json` | 1-18 (tam) | §28 L-7..L-9 |
| `shared/tests/**` | glob | §18 envanter (41 `*Test.php` / 349 method) |

## §31 Eke — sürücü denetim listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | Her komut bir repo satırından ya da 2026-10-13 HTTP 200 dokümandan mı? | Evet |
| 2 | `--stop-on-*` / `--fail-on-*` / `--coverage-*` kullanılıyor mu? | Hayır — §24 negatif kanıt |
| 3 | OS test komutu (`make kselftest`, `kunit.py`) repoda mı? | Hayır — §10 hedef referans (T1 `UNKNOWN`) |
| 4 | Kapsama eşiği/komutu yazıldı mı? | Hayır — T2 `UNKNOWN` |
| 5 | `home`/`api` testleri CI'da koşuyor mu? | Hayır — matrix 2 satır (§19) |

## §32 Eke — aksiyon × satır tam indeksi (ci.yml)

| Aksiyon | Satırlar | Adet |
|---|---|---|
| `actions/checkout@v4` | `:30`, `:82`, `:115`, `:147`, `:169`, `:185` | 6 |
| `shivammathur/setup-php@v2` | `:32-38`, `:84-90`, `:117-121`, `:187-190` | 4 |
| `actions/cache@v4` | `:40-46`, `:92-98` | 2 |
| `actions/setup-node@v4` | `:149-152`, `:170-173`, `:192-195` | 3 |
| `gitleaks/gitleaks-action@v2` | `secret-scan.yml:29` | 1 |

| Aksiyon sürümü | Değer | Not |
|---|---|---|
| checkout | `@v4` | 6 job'un tamamında |
| setup-php | `@v2` | PHP kuran 4 job |
| cache | `@v4` | Yalnız PHP job'larında (Node `cache: npm` ayrı) |
| setup-node | `@v4` | 3 JS job |
| gitleaks | `@v2` | Ayrı workflow |

> Sürüm pin'leri **dosyadan okunmuştur**; "güncel mi" sorusu bu dosyanın **kapsamı
> dışıdır** (güncelleme = DevOps + onay, `.github/AGENTS.md` §3.1).

## §33 Eke — sürüm ve tarih kaydı

| Kayıt | Değer | Kanıt |
|---|---|---|
| PHP eşiği | `>=8.4` | `shared/composer.json:8` |
| PHPUnit sürümü (repo) | `^10.5` | `shared/composer.json:24` |
| PHPStan sürümü | `^1.10` | `shared/composer.json:25` |
| Doküman sürümü (okunan) | PHPUnit 11.5 | textui/risky-tests/organizing-tests (2026-10-13 GET 200) |
| Node sürümü (CI) | `26` | `ci.yml:151` |
| Ölçüm tarihi | 2026-10-13 | Bu oturum |
| Envanter sayımı | 41 `*Test.php` · 349 method · 45 `.php` | `shared/tests/**` glob |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
