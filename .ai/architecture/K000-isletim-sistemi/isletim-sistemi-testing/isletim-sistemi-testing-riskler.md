---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-riskler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-anayasa
evidence:
  - "shared/tests/Security/SessionKeysTest.php:126-131"
  - "shared/tests/Middleware/CsrfMiddlewareTest.php:18-26"
  - ".github/workflows/ci.yml:165-167"
  - ".github/workflows/ci.yml:182-183"
validation:
  - "2026-10-13 · her bulgu bir repo satırına bağlandı; tahminî flaky oranı/coverage rakamı yazılmadı"
  - "2026-10-13 · anti-pattern kataloğu resmi dokümanlarla (kardeş W-42/W-43) sınırlandırıldı"
open_questions:
  - "T1: OS testi yokluğu bir risk midir, kapsam kararı mıdır? [UNKNOWN] — sahiplenici kararı gerekir"
  - "T2: Kapsama ölçümü olmadığı için yanlış-negatif taraması ölçülemez [UNKNOWN]"
group: kanit
---

# isletim-sistemi-testing Riskleri — Anti-pattern, Flaky ve Yanlış-Negatif

## §0 ELI15

Risk dosyası, testlerin **bizi kandırabileceği** yerleri listeler. Üç sınıf vardır:
(1) **flaky** — test doğru kodda bile bazen kırmızı olur, (2) **yanlış-negatif**
(test yeşildir ama davranış bozuktur), (3) **kapı riski** — kapı kırmızı olsa bile
birleştirmeyi engellemez. Her bulgu bu dosyada **repo satırına** bağlıdır; "olabilir"
diye yazılmış risk yoktur. Ölçülemeyen hiçbir riske rakam **konmaz** (T2 `UNKNOWN`).

## §1 Flaky test riskleri (bu depoda gözlenen yapılar)

| # | Yapı | Kanıt satırı | Neden flaky üretir | Azaltım |
|---|---|---|---|---|
| F-1 | Global state (`$_SESSION`) ile çalışma | `CsrfMiddlewareTest.php:20` (`$_SESSION = []` setUp) · `CsrfMiddlewareTest.php:25` (tearDown) | Global değişken, paralel/arda koşan testlere sızmak zorunda | setUp+tearDown'da sıfırlama — mevcut yapı doğru |
| F-2 | `stopOnFailure=false` (suite ilk hatada durmaz) | `phpunit.xml:6` | Birden çok hata tek koşumda üst üste biner, triyaj karmaşıklaşır | Hata triyajı (§8) — yapı bilinçli, risk düşük |
| F-3 | Soft kapıların kalıcı hâli | `ci.yml:167` · `ci.yml:183` | Kırmızı job görülmeyince gerçek regresyon gözden kaçar | Kapı kapanış tarihi + eşiğe dönüş (karar D-2) |
| F-4 | Zaman bağımlı kod (test edilmedi) | Repoda zaman/saat testi **0 isabet** (grep: `DateTimeNow`-tarzı sabit çağrı testi yok) | Saat dilimi/ DST kaymaları üretimde kırar | TimeProvider enjeksiyonu (gelecek iş) |
| F-5 | Ağaç/bağımlılık testi (order) | `EventDispatcherTest.php:50-65` öncelik sırası testi — deterministik | Öncelik matrisi büyürse elle yazılan sıra kırılabilir | KUnit tarzı tablo-driven (referans) |

> F-1'deki mevcut `setUp`/`tearDown` kullanımı **iyi uygulamadır**; risk, şablondan
> sapan yeni testlerde başlar. Bu dosya suçlu aramaz, **kaynak** gösterir.

## §2 Yanlış-negatif riskleri (yeşil ama yanlış)

| # | Desen | Kanıt | Neden yanlış-negatif | Düzeltme yönü |
|---|---|---|---|---|
| Y-1 | Kendi kendini doğrulayan assertion | `SessionKeysTest.php:130` — `$this->assertSame('csrf_token', 'csrf_token');` | Sabit ile sabiti karşılar; hiçbir kodu test etmez, her zaman yeşildir | ADR-010 anahtarını üreten koddan oku (`SessionKeys` sabiti) ya da testi kaldır |
| Y-2 | Yalnız sabit assertion | `SessionKeysTest.php:22-65` (9 sabit testi) | Sabit değişirse kırılır, davranış testi değildir — düşük sinyal | Davranış testleriyle tamamla (dizi üyeliği `:71-84` zaten var) |
| Y-3 | Assertion yok | PHPUnit `beStrictAboutTestsThatDoNotTestAnything` varsayılan `true` | Assertion'sız test "risky" işaretlenir; repo'da bu yapı **0 isabet** | Koru — bu varsayılan kapıdır |
| Y-4 | `assertEquals` ile tip kayması | `EventDispatcherTest.php:64`, `:96` (`assertEquals`) | `assertEquals` tipleri gevşek karşılar (array vs Collection) | Kritik yerde `assertSame` (ör. `RouteRegistryTest.php:19`) |
| Y-5 | Mock fazlalığı (test edilen şey mock'un kendisi) | Repoda mock kütüphanesi **0 isabet** (composer.json require-dev: yalnız phpunit + phpstan) | Mock bağımlılığı değişince test hâlâ yeşil | Şu an risk yok — kütüphane yok; eklenirse denetim gerekir |
| Y-6 | Kapsama gözleminin yokluğu | `phpunit.xml` `<coverage>` YOK (39 satır okundu) | "Testler geçti" ile "kod test edildi" eşanlamlı sanılır | T2 kararıyla ölçüm (anayasa §7) |

## §3 Kapı riskleri (CI yüzeyi)

| # | Risk | Kanıt | Etki | Azaltım |
|---|---|---|---|---|
| K-1 | Soft kapı eşiği belirsiz | `ci.yml:165-166` (yorum: "%80 eşiği şimdilik erişilemez") | Eşik belirsiz → kapanış tarihi belirsiz → kapı kalıcı soft | Eşik + tarih kararı (T2) |
| K-2 | e2e soft | `ci.yml:182-183` ("ilk koşular bilinen-riskli") | Üretim kırılması e2e'den kaçabilir | Stabilizasyon sonrası sertleştirme |
| K-3 | Suite seçimi disk kanıtına bağlı | `ci.yml:77-80` (Integration dizini yok → Unit) | Dizin eklenince suite otomatik açılmaz | Dizin eklenince args gözden geçirilir |
| K-4 | Secret tarama config'siz | `secret-scan.yml:4` (`.gitleaks.toml` YOK → varsayılan) | Özel desenler taranmaz | ADR + Security onayı ile config |
| K-5 | Audit ≠ test | `ci.yml:126` ayrı job | Bilinen CVE'li paket yeşil testle birleşebilir | Audit job'ı sert — mevcut yapı iyi |
| K-6 | **Projeler arası test kapsamı asimetrisi** | `ci.yml:71-80` (`php-test` matrix yalnız `shared` + `auth.coremusic.net`) · `home.coremusic.net` yalnız `ci.yml:56-58` (lint install) ve `ci.yml:135-139` (audit install) | `home.coremusic.net/tests/` altındaki **6 test dosyası CI'da hiç koşulmuyor** — regressyon sessiz kalır | `home.coremusic.net` matrix'e eklenir (args gözden geçirilir) |
| K-7 | **Kapsam raporu üretilmiyor (sürücü yok)** | `ci.yml:37` ve `ci.yml:88` `extensions: apcu, mbstring, json, pdo, pdo_mysql` — `xdebug`/`pcov` **yok** · `auth.coremusic.net/phpunit.xml:28-32` `<coverage><report><text outputFile="php://stdout"/></report></coverage>` tanımlı | Rapor bloğu tanımlı olsa da sürücü olmadığından kapsam verisi üretilmez; `<coverage>` bloğu **sessizce boş** döner | `setup-php` extensions'a `pcov` eklenir (T2 kararıyla) |
| K-8 | **Kapı sertliği tutarsız** | `auth.coremusic.net/phpunit.xml:6-8` `failOnRisky="true"` · `failOnWarning="true"` · `failOnPhpunitWarning="false"` · `shared/phpunit.xml:1-39` ve `home.coremusic.net/phpunit.xml:1-18`'de bu üç alanın **hiçbiri yok** | Aynı depoda iki farklı risk-toleransı: auth kırmızı yapan davranış (risky/warning), shared'de yeşil kalır | Sertlik seti tek dosyaya taşınır ya da fark bilinçli kabul edilir |
| K-9 | **CI yorumu disk gerçeğiyle çelişiyor** | `ci.yml:4-5` yorumu "phpunit.xml x2 (shared, auth.coremusic.net)" · diskte **3** phpunit.xml (`shared\phpunit.xml` 39 satır · `auth.coremusic.net\phpunit.xml` 34 satır · `home.coremusic.net\phpunit.xml` 18 satır) | Yeni eklenen config görünmez kalır; K-6 kök nedeni bu bayat yorumdur | Yorum güncellenir (doküman drift riski) |

## §4 Doğruluk riskleri (rakam/iddia)

| # | Risk | Kural |
|---|---|---|
| D-1 | "Coverage %80" gibi rakam uydurma | T2 `UNKNOWN`; `<coverage>` YOK → ölçülen değer yoktur, **yazılamaz** |
| D-2 | "OS testleri geçiyor" iddiası | `kunit`/`kselftest` 0 isabet → L0 testi **yoktur**, iddia yasak |
| D-3 | Geçmiş oturumdan rakam taşıma | Her rakam dosyanın kendi ölçümü olmalı; taşınıyorsa kaynak dosya adı yazılır |
| D-4 | URL iddiası | Yalnız 2026-10-13 `HTTP 200` doğrulanmış URL'ler; doğrulanmayan `⚠️ VERIFICATION REQUIRED` |

## §5 Repo gözlemleri (satır kanıtlı, sınıflandırılmış)

| Dosya:Satır | Gözlem | Sınıf | Şiddet |
|---|---|---|---|
| `SessionKeysTest.php:130` | Tautolojik assertion (`assertSame` iki sabit literal) | Yanlış-negatif | Yüksek |
| `SessionKeysTest.php:86-89` | `assertCount(7, USER_KEYS)` — yapı testi | İyi | — |
| `CsrfMiddlewareTest.php:18-26` | setUp/tearDown global temizliği | İyi (flaky savunması) | — |
| `CsrfMiddlewareTest.php:306-324` | Farklı uzunlukta token → 403 (timing-safe) | İyi | — |
| `RouteRegistryTest.php:19` | `assertSame` (tip-darı) | İyi | — |
| `EventDispatcherTest.php:64` | `assertEquals` (gevşek) | İyileştirilebilir | Düşük |
| `phpunit.xml:6` | `stopOnFailure=false` | Bilinçli | — |
| `ci.yml:167`, `ci.yml:183` | `continue-on-error: true` | Bilinçli geçiş | Orta (süre) |
| `ci.yml:71-80` | `php-test` matrix yalnız `shared` + `auth.coremusic.net` | Kapsam boşluğu | **Yüksek** (K-6) |
| `ci.yml:56-58`, `ci.yml:135-139` | `home.coremusic.net` yalnız install/audit'da — test job'ında yok | Kapsam boşluğu | **Yüksek** (K-6) |
| `ci.yml:37`, `ci.yml:88` | `extensions` listesinde `xdebug`/`pcov` yok | Kapsam üretilemez | Orta (K-7) |
| `ci.yml:4-5` | Yorum "phpunit.xml x2" — diskte 3 adet | Doküman drift | Düşük (K-9) |
| `ci.yml:72` | `fail-fast: false` | İyi — matrix'te biri kırılınca diğeri koşmaya devam eder | — |
| `ci.yml:77-80` | `auth.coremusic.net` `args: '--testsuite Unit'` | İyi — bilinçli daraltma | — |
| `ci.yml:151` | `node-version: '26'` + `cache: npm` | İyi | — |
| `auth.coremusic.net/phpunit.xml:6-8` | `failOnRisky`/`failOnWarning` true, `failOnPhpunitWarning` false | Tutarsız sertlik | Orta (K-8) |
| `auth.coremusic.net/phpunit.xml:10-12` | Integration suite 2026-09-29'da kaldırıldı (`tests/Integration/` diskte YOK) | İyi — bilinçli | — |
| `auth.coremusic.net/phpunit.xml:4` | `bootstrap="tests/bootstrap.php"` (shared `vendor/autoload.php`) | İyi — fark bilinçli | — |
| `auth.coremusic.net/phpunit.xml:20-24` | `<source><include><directory>include` | İyi — `src` yerine `include` (farklı kaynak ağacı) | — |
| `home.coremusic.net/phpunit.xml:9-11` | Tek suite `Unit` → `tests/Unit` | İyi | — |
| `home.coremusic.net/phpunit.xml:13-16` | `<source><include><directory suffix=".php">include` | İyi | — |
| `shared/composer.json` scripts | `test: "phpunit"` · `stan: "phpstan analyse src --level=5"` | İyi — kapı komutunun sahibi | — |
| `package.json` scripts | `test:js` · `test:js:coverage` · `test:e2e` (`-c assets.coremusic.net/playwright.config.ts`) | İyi | — |

## §6 Anti-pattern kataloğu (kural → sonuç)

| # | Anti-pattern | Sonuç | Bu depoda? |
|---|---|---|---|
| A-1 | Assertion'sız test | Hiçbir şey doğrulamaz | Yok (PHPUnit risky varsayılanı korur) |
| A-2 | Sabit-sabit assertion | Sonsuz yeşil | **Var** (`SessionKeysTest.php:130`) |
| A-3 | Test içinde sleep | Zaman bağımlı, yavaş, flaky | Yok (0 isabet) |
| A-4 | Testler arası gizli bağımlılık | Sıra değişince kırmızı | Yok (setUp temizliği) |
| A-5 | Try-catch ile hata yutma | Test asla kırmızı olmaz | Yok (try-catch 0 isabet) |
| A-6 | Testi atla (`markTestSkipped`) ile yeşil | Kapsam yanılsaması | Yok (skip 0 isabet) |
| A-7 | Kapsamı ölçmeden eşik koymak | Uydurma kapı | Yok (eşik yok — bilinçli) |
| A-8 | Soft kalıcı kapı | Sessiz regresyon | **Risk** (`ci.yml:167,183`) |

## §7 Azaltım planı (öncelik sırası, rakamsız)

| # | Eylem | Hedef bulgu | Sahip |
|---|---|---|---|
| M-1 | `SessionKeysTest.php:130` düzeltmesi: gerçek sabiti (`SessionKeys` sınıfı) karşılaştırmak ya da testi kaldırmak | Y-1 | QA Engineer |
| M-2 | T2 kararı: kapsama ölçümü + eşiği (şimdilik `UNKNOWN`) | Y-6, K-1 | Kullanıcı + QA |
| M-3 | Soft kapı kapanış tarihi yazmak | K-1, K-2 | DevOps Engineer |
| M-4 | `.gitleaks.toml` (Security onayı ile) | K-4 | Security + DevOps |
| M-5 | Zaman bağımlılığı için seam (TimeProvider) | F-4 | Backend Architect |

## §8 Hata triyajı (bir koşumda birden çok kırmızı)

| Adım | Aksiyon | Gerekçe |
|---|---|---|
| 1 | Lint job'ı kontrol | Test öncesi kapı yoksa test sonuçları güvenilmez |
| 2 | İlk kırmızıyı al (`stopOnFailure=false` → liste uzun) | Regresyon kökü genelde tek |
| 3 | Suite bazında izole koş (`--testsuite`) | `ci.yml:80` deseni |
| 4 | Soft kırmızıyı ayır (coverage/e2e) | `ci.yml:167,183` — bloklamaz |
| 5 | Flaky sanılanı 3 kez izole koş | Tekrarlanmayan = flaky, tekrarlanan = gerçek |

## §9 Ölçüm (bu dosyanın kendi kanıtı)

| Ölçüm | Değer | Yöntem |
|---|---|---|
| Satır kanıtlı bulgu | 8 (`§5` tablosu) | Read ile satır okuması |
| Tautolojik assertion | 1 (`SessionKeysTest.php:130`) | Manuel inceleme (tam dosya 153 satır okundu) |
| Mock kütüphanesi | 0 | `composer.json` require-dev (L23-26) |
| Sleep/skip/try-catch anti-pattern | 0 | `shared/tests/**` grep |
| Soft kapı | 2 job | `ci.yml:167`, `ci.yml:183` |
| Ölçülemeyen | Flaky oranı, coverage yüzdesi | T2 `UNKNOWN` — rakam yazılmaz |

## §10 Kapsam dışı

| Konu | Sınır |
|---|---|
| Test yazma/düzeltme uygulaması | Bu dosya yalnız raporlar; kod QA'nın işi (M-1 sahibi) |
| Kapı sertleştirme kararı | `.github/AGENTS.md` §3.3 sınır 4 (onay) |
| K019 test anti-pattern envanteri | Komşu dal — bu dosya yalnız bu dalın gözlemlerini taşır |
| OS testi risk envanteri | L0 testi yok → envanter de yok (T1) |

## §11 Kaynakça (2026-10-13 · HTTP 200)

| İddia | URL | Not |
|---|---|---|
| PHPUnit test yazım kılavuzu (fixture, assertion) | https://docs.phpunit.de/en/11.5/writing-tests-for-phpunit.html | kardeş W-42 |
| PHPUnit assertif kataloğu | https://docs.phpunit.de/en/11.5/assertions.html | kardeş W-43 |
| PHPUnit test double sınırları | https://docs.phpunit.de/en/11.5/test-doubles.html | kardeş W-41 |
| GitHub Actions `continue-on-error` davranışı | https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax | kardeş W-28 |

## §12 Eke — şiddet × etki matrisi

| Şiddet / Etki | Düşük | Orta | Yüksek |
|---|---|---|---|
| Yüksek | — | Y-4 (gevşek assert) | **Y-1** (tautoloji) |
| Orta | F-2 (durma modu) | K-1, K-2 (soft kapılar) | D-2 (sahte OS iddiası) |
| Düşük | Y-2 (sabit testleri) | K-3 (suite seçimi) | K-5 (audit ayrımı — aslında iyi) |

## §13 Eke — sınıf × bulgu sayımı

| Sınıf | Bulgu | Kaynak |
|---|---|---|
| Flaky | 5 (F-1..F-5) | §1 |
| Yanlış-negatif | 6 (Y-1..Y-6) | §2 |
| Kapı | 5 (K-1..K-5) | §3 |
| Doğruluk | 4 (D-1..D-4) | §4 |
| Anti-pattern kataloğu | 8 (A-1..A-8; 2'si repoda: A-2, A-8) | §6 |

## §14 Eke — açık sorular

| # | Soru | Kapanış |
|---|---|---|
| T1 | OS testi yokluğu risk mi, kapsam kararı mı? | Kullanıcı kararı |
| T2 | Kapsama ölçümü + eşiği | Ölçüm altyapısı + karar |

## §15 Eke — CI matrisi: proje × job kapsam matrisi (2026-10-13 genişletmesi)

> Kanıt: `.github/workflows/ci.yml` **203 satırın tamamı** okundu (Read). Bu tablo
> K-6/K-7/K-9 bulgularının kaynağıdır; hiçbir hücre tahmin değildir.

| Proje | `php-lint` (L25-64) | `php-test` (L66-108) | `composer-audit` (L110-139) | Test dosyası | CI'da koşulan? |
|---|---|---|---|---|---|
| `shared` | ✅ install L48-50 | ✅ `args: ''` (L75-76) | ✅ L123-127 | 44 `.php` / **40 `*Test.php`** | ✅ Evet |
| `auth.coremusic.net` | ✅ install L52-54 | ✅ `args: '--testsuite Unit'` (L79-80) | ✅ L129-133 | 7 `.php` / **6 `*Test.php`** | ✅ Evet (yalnız Unit) |
| `home.coremusic.net` | ✅ install L56-58 | ❌ **matrix'te YOK** | ✅ L135-139 | 7 `.php` / **6 `*Test.php`** | ❌ **HAYIR — K-6** |

> JS yüzeyi (`package.json` scripts — disk kanıtı): `test:js` → `ci.yml:158` (js-test,
> **sert**) · `test:js:coverage` → `ci.yml:176` (js-coverage, **soft** L167) ·
> `test:e2e` → `ci.yml:203` (js-e2e, **soft** L183).

**Kapı sertliği özeti (6 job):**

| Job | Satır | Sertlik | Kanıt |
|---|---|---|---|
| `php-lint` | L25-64 | **SERT** (PHPStan `composer stan` → `phpstan analyse src --level=5`) | `ci.yml:60-64` · `shared/composer.json` scripts |
| `php-test` | L66-108 | **SERT** (`fail-fast: false` — L72) | `ci.yml:104-108` |
| `composer-audit` | L110-139 | **SERT** (3 projede `composer audit`) | `ci.yml:123-139` |
| `js-test` | L142-158 | **SERT** (`npm run test:js`) | `ci.yml:157-158` |
| `js-coverage` | L160-176 | SOFT (`continue-on-error: true` L167) | `ci.yml:165-167` |
| `js-e2e` | L178-203 | SOFT (`continue-on-error: true` L183) | `ci.yml:182-183` |

## §16 Eke — test dosyası envanteri (dosya bazlı ölçüm, 2026-10-13)

> Yöntem: `Get-ChildItem -Recurse -Filter *.php` ve `-Filter *Test.php` — üç proje
> için ayrı ayrı sayıldı. `*Test.php` = PHPUnit'un varsayılan test-suffix eşlemesi
> (textui.html `--test-suffix` varsayılanları: `Test.php` ve `.phpt`).

| Proje | Toplam `.php` | `*Test.php` | Fark (fixture/bootstrap) | Not |
|---|---|---|---|---|
| `shared/tests/` | 44 | 40 | 4 (`Fixtures/` altındaki `header.php`, `footer.php` + 2 PEM çifti hariç) | `Fixtures` için suite **tanımlı değil** (grep 0 isabet) |
| `auth.coremusic.net/tests/` | 7 | 6 | 1 (`bootstrap.php`) | `tests/Integration/` **diskte YOK** — auth phpunit.xml L10-12 yorumuyla tutarlı |
| `home.coremusic.net/tests/` | 7 | 6 | 1 (`Support/FakeComponent.php`) | `tests/Unit/` altında 6 test — **CI'da hiç koşulmuyor (K-6)** |
| **Toplam** | **58** | **52** | 6 | CI'da koşulan: **46** (`*Test.php` 52 − home 6) |

**`shared/tests/` alt dizin dağılımı** (phpunit.xml suite tanımıyla birebir eşleşir):

| Alt dizin | `*Test.php` | phpunit.xml suite (satır) | Eşleşme |
|---|---|---|---|
| `Unit/` | 15 | `Unit` (L9-11) | ✅ |
| `Middleware/` | 9 | `Middleware` (L24-26) | ✅ |
| `Api/` | 8 | `Api` (L12-14) | ✅ |
| `Repository/` | 4 | `Repository` (L15-17) | ✅ |
| `Events/` | 2 | `Events` (L18-20) | ✅ |
| `Security/` | 2 | `Security` (L21-23) | ✅ |
| `Component/` | 1 | `Component` (L27-29) | ✅ |
| `OAuth/` | 1 | `OAuth` (L30-32) | ✅ |
| `Fixtures/` | 0 (2 `.php` fixture) | **suite YOK** | ⚠️ suite'e ihtiyaç yok — fixture'lar test değil |

> 8 suite × karşılık gelen dizin = 40 `*Test.php`; **hiçbir test dosyası suitesiz değil**.

## §17 Eke — üç phpunit.xml karşılaştırması (2026-10-13 genişletmesi)

> K-7/K-8/K-9 bulgularının kaynağı. Üç dosyanın tamamı satır satır okundu.

| Alan | `shared/phpunit.xml` (39 satır) | `auth.coremusic.net/phpunit.xml` (34 satır) | `home.coremusic.net/phpunit.xml` (18 satır) |
|---|---|---|---|
| `bootstrap` | `vendor/autoload.php` (L4) | `tests/bootstrap.php` (L4) | `vendor/autoload.php` (L4) |
| `stopOnFailure` | `false` (L6) | **yok** (varsayılan `false`) | `false` (L6) |
| `cacheDirectory` | `.phpunit.cache` (L7) | **yok** | `.phpunit.cache` (L7) |
| `failOnRisky` | **yok** | `true` (L6) | **yok** |
| `failOnWarning` | **yok** | `true` (L7) | **yok** |
| `failOnPhpunitWarning` | **yok** | `false` (L8) | **yok** |
| Suite sayısı | **8** (L9-32) | **1** (Unit, L14-16) | **1** (Unit, L9-11) |
| `<source>` include | `src` + `suffix=".php"` (L34-38) | `include` (L20-24) | `include` + `suffix=".php"` (L13-16) |
| `<coverage><report>` | **YOK** (dosya L39'da biter) | `text outputFile="php://stdout"` (L28-32) | **YOK** (dosya L18'de biter) |
| Sürücü (xdebug/pcov) | `ci.yml:37` extensions'da **yok** | `ci.yml:88` extensions'da **yok** | `ci.yml:190` extensions'da **yok** |

**K-7 gerekçesi (üç satır bir arada):** auth'ta `<coverage><report>` tanımlıdır
(L28-32), ancak `setup-php` adımının `extensions` listesi üç job'da da
`apcu, mbstring, json, pdo, pdo_mysql` ile sınırlıdır (`ci.yml:37`, `:88`, `:190`) —
`xdebug` veya `pcov` **hiçbirinde yok**. Sürücü olmadan PHPUnit kapsam verisi
**üretemez**; `<coverage>` bloğu sessizce boş döner ve `php://stdout` raporu
kapsamsız basılır. Bu, Y-6'nın ("kapsama gözlemi yok") **kök nedenidir**.

> `phpunit.xml` envanter düzeltmesi (K-9): `ci.yml:4-5` yorumu "phpunit.xml x2"
> der; diskte **3** adet vardır (`shared` 39 · `auth.coremusic.net` 34 ·
> `home.coremusic.net` 18 satır). Yorum bayattır.

## §18 Eke — PHPUnit CLI kapısı kataloğu (textui.html, 2026-10-13 HTTP 200 + içerik)

> Kaynak: `https://docs.phpunit.de/en/11.5/textui.html` — bu oturumda webfetch ile
> **GET içerik** doğrulandı (kardeş W-40). Aşağıdaki her bayrak sayfadan okunmuştur;
> repo'da **kullanılmıyor** (ci.yml'de `vendor/bin/phpunit "$PHPUNIT_ARGS"` yalnız
> `--testsuite` alır — `ci.yml:105`, `:80`). Bu katalog **hedef referanstır**.

| Bayrak ailesi | Üyeler (sayfada okunan) | Bu depoda? |
|---|---|---|
| `--stop-on-*` (9) | `defect` · `error` · `failure` · `warning` · `risky` · `deprecation` · `notice` · `skipped` · `incomplete` | ❌ 0 kullanım |
| `--fail-on-*` (10) | `warning` · `risky` · `deprecation` · `phpunit-deprecation` · `notice` · `incomplete` · `skipped` · `phpunit-warning` · `empty-test-suite` · `all-issues` | ❌ 0 kullanım |
| `--do-not-fail-on-*` (9) | yukarıdaki `--fail-on-*` üyelerinin counterparts'ları (`all-issues` hariç hepsi) | ❌ 0 kullanım |
| `--display-*` (7) | `incomplete` · `skipped` · `deprecations` · `phpunit-deprecations` · `errors` · `notices` · `warnings` · `all-issues` | ❌ 0 kullanım |

**K-8 ile ilişki:** `auth.coremusic.net/phpunit.xml:6-8` `failOnRisky`/`failOnWarning`
XML attribute'larını `true` yapar. Sayfadaki karşılığı olan CLI bayrakları
`--fail-on-risky` / `--fail-on-warning`'dir. XML yolu **etkin** (auth'ta);
CLI yolu **hiç kullanılmıyor**. `--fail-on-empty-test-suite` ise hiçbir config'de
yok → **boş suite yeşil döner** (repo'da böyle bir suite tespit edilmedi; K-3
uyarısı korunur).

## §19 Eke — bu genişletmenin ölçüm kaydı

| Ölçüm | Genişletme öncesi | Genişletme sonrası | Yöntem |
|---|---|---|---|
| Dosya satırı | 176 | bkz. rapor | Read toplam satır |
| Satır kanıtlı bulgu (§5) | 8 | 8 + 16 = 24 | `ci.yml` 203 satır + 3 phpunit.xml + 3 composer.json + package.json okundu |
| Kapı riski (§3) | 5 (K-1..K-5) | 5 + 4 = 9 (K-6..K-9) | matris + config karşılaştırması |
| Yeni doğrulanmış repo kaynağı | 4 dosya | + `home.coremusic.net/phpunit.xml` (18 satır) · `home.coremusic.net/composer.json` · `auth.coremusic.net/composer.json` · `package.json` | Read / Select-String |
| Yeni HTTP 200 + içerik URL | 0 (bu dosya) | **1** — `docs.phpunit.de/en/11.5/textui.html` | webfetch GET, 2026-10-13 |
| Kardeş envanterden yeniden | 4 (W-41/42/43/28) | 4 (değişmedi) | `-dogrulama.md` §W tablosu |
| Uydurma rakam / tahmin | 0 | **0** | T1/T2 `UNKNOWN` korundu |

## §20 Eke — `secret-scan.yml` tam iş analizi (31 satır okundu, 2026-10-13)

> Kanıt: `.github/workflows/secret-scan.yml` **31 satırın tamamı** okundu. Bağımsız
> workflow'dur (`ci.yml`'dan ayrı dispatch tetiklenir).

| Alan | Değer | Satır |
|---|---|---|
| `name` | `Secret Scan` | L5 |
| Tetikleyici | `push` + `pull_request` | L7-9 |
| `permissions` | `contents: read` | L11-12 |
| `concurrency` | `group: ${{ github.workflow }}-${{ github.ref }}` · `cancel-in-progress: true` | L14-16 |
| Job | tek: `gitleaks` (ad `GitLeaks`) | L19-20 |
| `runs-on` | `ubuntu-latest` | L21 |
| `timeout-minutes` | **10** | L22 |
| Checkout | `actions/checkout@v4` | L24 |
| `fetch-depth` | **0** — yorum L26: "no commits found" hatasına karşı | L25-26 |
| Adım | `gitleaks/gitleaks-action@v2` | L28-29 |
| `env` | `GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}` | L30-31 |
| `.gitleaks.toml` | **YOK** — yorum L4 bunu açıkça yazıyor | L4 |

**Risk bağı:** K-4 (§3) ile birebir örtüşür — varsayılan desen seti kullanılıyor.
Ek bulgu: workflow **tek job'ludur**; `ci.yml`'daki 6 job'dan bağımsız çalışır, yani
secret taraması CI lint/test kapılarıyla **eşzamanlı ve bağımsız** bir sert kapıdır
(bu iyi bir yapıdır — K-5'in zıttı: burada kapı gerçekten sert, çünkü tarama
bağımsız dispatch ile her push/PR'da çalışır).

> ⚠️ VERIFICATION REQUIRED: Bu workflow'un repoda **gerçekten geçip geçmediği**
> doğrulanmadı (CI çalışma geçmişi bu oturumda okunmadı). "Geçti" iddiası
> **üretilmez** — yalnız config okundu.

## §21 Eke — test dosyası tam envanteri (dosya adları, 2026-10-13)

> Yöntem: `Get-ChildItem -Recurse -Filter *Test.php`. Her ad diskten okunmuştur.

**`shared/tests/` — 40 `*Test.php` (suite bazlı):**

| Suite | Dosya adları |
|---|---|
| `Unit` (15) | `ConfigManagerTest` · `DeviceDetectorTest` · `DeviceRendererTest` · `WelcomePopupRenderTest` · `LoggerRedactionTest` · `Psr3LoggerTest` · `AuthGuardTest` · `AuthUrlBuilderTest` · `HtmlShellRendererChromeTest` · `HtmlShellRendererGuardConfigTest` · `RouteRegistryTest` · `SpaRouteTest` · `JwtServiceTest` · `ReturnUrlPolicyTest` · `ThemeManagerModeTest` |
| `Middleware` (9) | `AuthMiddlewareTest` · `BypassAuthMiddlewareFailClosedTest` · `BypassAuthMiddlewareTest` · `CsrfMiddlewareTest` · `MiddlewarePipelineTest` · `OriginCheckMiddlewareTest` · `RateLimiterMiddlewareTest` · `SessionManagerMiddlewareTest` · `ValidationMiddlewareTest` |
| `Api` (8) | `ApiPipelineSecurityTest` · `ApiResponseTest` · `DtoTest` · `GatewayMethodRoutingTest` · `RequestValidationMiddlewareTest` · `ResponseNormalizationMiddlewareTest` · `RouteTableTest` · `VersioningTest` |
| `Repository` (2) | `ApiKeyCreateCommandTest` · `ApiKeyRepositoryTest` |
| `Events` (2) | `DomainEventTest` · `EventDispatcherTest` |
| `Security` (2) | `ReturnUrlPolicyEdgeCaseTest` · `SessionKeysTest` |
| `Component` (1) | `TemplateEngineTest` |
| `OAuth` (1) | `OAuthManagerTest` |

**`shared/tests/` — `*Test.php` OLMAYAN 4 `.php` (destek/fixture):**

| Dosya | Boyut | Rol |
|---|---|---|
| `Fixtures/chrome/header.php` | (fixture) | Şablon fixture'ı — suite'e dahil değil |
| `Fixtures/chrome/footer.php` | (fixture) | Şablon fixture'ı — suite'e dahil değil |
| `Repository/Sqlite/SqliteDatabaseManager.php` | 2868 byte | Test yardımcı sınıfı — test dosyası değil |
| `Repository/Sqlite/SqliteDatabaseRegistry.php` | 900 byte | Test yardımcı sınıfı — test dosyası değil |

> `Fixtures/chrome/` altında ayrıca `test-private.pem` · `test-public.pem` ·
> `test2-private.pem` · `test2-public.pem` vardır (`.php` değildir, 44 sayımına
> girmez). Bunlar **test amaçlı anahtar çiftleridir** — `test-` öneki taşıdıkları
> için gerçek sır değildir; yine de **dokunulmaz** (bu dosya salt raporlar).

**`auth.coremusic.net/tests/` — 6 `*Test.php`:**

| Dosya | Katman |
|---|---|
| `Unit/Domain/DTO/LoginRequestTest.php` | Domain DTO |
| `Unit/Domain/Entity/UserTest.php` | Domain Entity |
| `Unit/Domain/ValueObject/EmailTest.php` | Value Object |
| `Unit/Domain/ValueObject/GenderTest.php` | Value Object |
| `Unit/Domain/ValueObject/PasswordTest.php` | Value Object |
| `Unit/Service/SessionManagerTest.php` | Service |

> + `tests/bootstrap.php` (1 `.php`, test değil) · `tests/Integration/` **diskte YOK**
> → auth phpunit.xml L10-12 yorumu ve ci.yml L77-78 yorumu **ikisi de doğru**.

**`home.coremusic.net/tests/` — 6 `*Test.php` (CI'da hiç koşulmuyor — K-6):**

| Dosya | Katman |
|---|---|
| `Unit/Bootstrap/EntryPointCacheSafetyTest.php` | Bootstrap/cache güvenliği |
| `Unit/Component/HomeSongButtonTest.php` | Component |
| `Unit/Component/RecentTracksComponentTest.php` | Component |
| `Unit/Layout/HomeLayoutVariantTest.php` | Layout |
| `Unit/Loader/ComponentLoaderTest.php` | Loader |
| `Unit/Page/HomeChromeSmokeTest.php` | Sayfa smoke |

> + `tests/Support/FakeComponent.php` (1 `.php`, test yardımcı sınıfı).
> Bu 6 dosyanın hiçbiri `ci.yml:71-80` matrix'inde yer almaz → **K-6'nın tam
> kapsamıdır**: home'un kapıdan geçtiği tek şey PHP lint ve composer audit'tir.

## §22 Eke — JS test altyapısı kanıtları (disk, 2026-10-13)

> K-6'nın JS yüzeyi. `package.json` (repo kökü) ve `assets.coremusic.net/` diskten okundu.

| Kanıt | Değer | Kaynak |
|---|---|---|
| Kök `package.json` `scripts` | `test:js` · `test:js:watch` · `test:js:coverage` · `test:e2e` | `package.json:4-9` |
| `test:js` komutu | `vitest run --root assets.coremusic.net` | `package.json:5` |
| `test:js:coverage` komutu | `vitest run --root assets.coremusic.net --coverage` | `package.json:7` |
| `test:e2e` komutu | `playwright test -c assets.coremusic.net/playwright.config.ts` | `package.json:8` |
| `devDependencies` | `@playwright/test ^1.63.0` · `playwright ^1.63.0` · `vitest ^5.0.3` · `@vitest/coverage-v8 ^5.0.3` · `jsdom ^30.1.1` · `axe-playwright ^1.0.0` | `package.json:10-17` |
| `assets.coremusic.net/vitest.config.ts` | **YOK** | glob yok |
| `assets.coremusic.net/vitest.config.js` | **VAR** | disk |
| `assets.coremusic.net/playwright.config.ts` | **VAR** | disk (`package.json:8` bunu işaret eder) |
| `assets.coremusic.net/package.json` | **YOK** (tek kök `package.json` var) | glob |
| Node sürümü (CI) | `'26'` — yorum: vitest engines `^22.12||^24||>=26`; jsdom `^22.22.2||^24.15||>=26` | `ci.yml:151` |

**K-6 gerekçesi — JS tarafında kapsam nasıl?:** `test:js` kök `package.json`'dan
çağrılıyor ve `--root assets.coremusic.net` ile daraltılıyor. `js-test` job'ı
(`ci.yml:142-158`) **serttir**. Dolayısıyla JS tarafında K-6 benzeri bir boşluk
**yoktur** — asimetri **yalnız PHP yüzeyindedir** (`home.coremusic.net`).

> ⚠️ VERIFICATION REQUIRED: `vitest.config.js` içeriği ve `assets.coremusic.net/`
> altında **kaç JS test dosyası** olduğu bu oturumda sayılmadı. `ci.yml:165`
> yorumu "12.9k satır / <100 test" der — bu bir **CI yorumudur, disk ölçümü
> değildir**; dosyada gerçek sayı olarak taşınmaz.

## §23 Eke — PHPUnit CLI: izolasyon, sıralama ve risk-bayrak kataloğu (textui.html)

> Kaynak: `https://docs.phpunit.de/en/11.5/textui.html` — 2026-10-13 webfetch GET
> **HTTP 200 + içerik** (kardeş W-40). §18'deki `--stop-on-*`/`--fail-on-*`
> ailelerinin **tamamlayıcısıdır**; buradaki her bayrak sayfadan okunmuştur.

| Aile | Üyeler (sayfada okunan) | Repo kullanımı |
|---|---|---|
| `--process-isolation` | Her testi ayrı PHP sürecinde koşar | ❌ 0 |
| `--globals-backup` | Global/super-global önce yedekler, test sonrası geri yükler | ❌ 0 |
| `--static-backup` | Sınıf static property'lerini yedekler/geri yükler | ❌ 0 |
| `--strict-coverage` | Kapsam metadata'sına sıkı | ❌ 0 |
| `--strict-global-state` | Global state değişimine sıkı | ❌ 0 |
| `--disallow-test-output` | Test sırasında çıktıya sıkı | ❌ 0 |
| `--enforce-time-limit` | Test boyutuna göre süre sınırı | ❌ 0 |
| `--default-time-limit <sec>` | Boyutsuz testler için varsayılan süre | ❌ 0 |
| `--dont-report-useless-tests` | "Hiçbir şey test etmeyen" testleri raporlamaz (risky-varsayılanının tersi) | ❌ 0 |
| `--order-by <order>` | `default` · `defects` · `depends` · `duration` · `no-depends` · `random` · `reverse` · `size` | ❌ 0 |
| `--random-order-seed <N>` | Rastgele sıralama tohumu | ❌ 0 |
| `--cache-result` / `--do-not-cache-result` | Test sonuçları cache'i (`test-results`) | ❌ 0 |
| `--bootstrap <file>` | Test öncesi PHP scripti | XML'den (`phpunit.xml:4`) |
| `-c\|--configuration <file>` / `--no-configuration` | XML config seçimi | ❌ 0 (varsayılan dosya) |
| `--filter <pattern>` | Test adı deseniyle filtre (mantıksal ve) | ❌ 0 |
| `--group` / `--exclude-group` | Test grubu seçimi (virgül ayrımı PHPUnit 12'de kalkıyor) | ❌ 0 |
| `--list-suites` / `--list-tests` / `--list-tests-xml` | Envanti̇r listeleme | ❌ 0 |
| `--test-suffix <suffix>` | Varsayılanlar: `Test.php` ve `.phpt` | varsayılan |

**§16 ile ilişki:** `--test-suffix` varsayılanı `Test.php` olduğundan §16'daki
`*Test.php` sayımı **PHPUnit'un kendi tanımıyla birebir**dır — bu bir tahmin
değil, sayfadan okunan varsayılandır.

**Flaky azaltımı ile ilişki (F-1..F-5):** Sayfadaki `--order-by defects|random` +
`--random-order-seed`, **sıra-bağımlılık flaky'ini** (F-5 ve A-4) tespit etmek için
kullanılabilecek araçlardır. Repo'da **0 kullanım** → bu araçlar bilinmiyor ya da
ihtiyaç görülmemiş. Azaltım planına (§7) **eklenebilir**; burada yalnızca
**araç varlığı** kaydedilir, uygulama önerilmez (bu dosya salt raporlar).

**Zaman bağımlılığı ile ilişki (F-4):** `--enforce-time-limit` / `--default-time-limit`
süre sınırlarını zorlar; ancak **saat/zaman kaynağı** bağımlılığını test etmez. F-4
için gereken `TimeProvider` seam'idir — bu bayraklar F-4'ü **kapatmaz**. Bu ayrım
kasten yazıldı: bayrak var ≠ risk kapanır.

## §24 Eke — kapı bileşeni → komut eşlemesi (tam, 2026-10-13)

> Tümü diskten okunmuş komutlardır; hiçbirinde tahmini değer yoktur.

| Kapı | Adım | Komut | Satır |
|---|---|---|---|
| `php-lint` | Composer install (shared) | `composer install --no-progress --prefer-dist` | `ci.yml:48-50` |
| `php-lint` | Composer install (auth) | aynı | `ci.yml:52-54` |
| `php-lint` | Composer install (home) | aynı | `ci.yml:56-58` |
| `php-lint` | **PHPStan** | `composer stan` → `phpstan analyse src --level=5` | `ci.yml:62-64` · `shared/composer.json` |
| `php-test` | PHPUnit (matrix) | `vendor/bin/phpunit "$PHPUNIT_ARGS"` · `PHPUNIT_ARGS: ${{ matrix.args }}` | `ci.yml:104-108` |
| `composer-audit` | install + audit ×3 | `composer install --no-progress --prefer-dist` + `composer audit` | `ci.yml:123-139` |
| `js-test` | npm | `npm ci` | `ci.yml:154-155` |
| `js-test` | Vitest | `npm run test:js` | `ci.yml:157-158` |
| `js-coverage` | Vitest coverage | `npm run test:js:coverage` | `ci.yml:175-176` |
| `js-e2e` | Playwright install | `npx playwright install --with-deps chromium` | `ci.yml:199-200` |
| `js-e2e` | Playwright | `npm run test:e2e` | `ci.yml:202-203` |
| `gitleaks` | GitLeaks | `gitleaks/gitleaks-action@v2` | `secret-scan.yml:28-29` |

**PHP sürümü:** `env.PHP_VERSION: '8.4'` (`ci.yml:21-22`) — yorum L22 bunu üç
`composer.json`'daki `"php": ">=8.4"` iddiasına bağlar. **Doğrulandı:**
`auth.coremusic.net/composer.json:15` ve `home.coremusic.net/composer.json:15`
gerçekten `"php": ">=8.4"` yazar (`shared/composer.json` aynı ailede — §17
karşılaştırmasının tamamlayıcısı).

**PHPUnit sürümü üçlemesi (tutarlı):** `shared/composer.json:24` ·
`auth.coremusic.net/composer.json:26` · `home.coremusic.net/composer.json:22` —
üçü de `"phpunit/phpunit": "^10.5"`. **Sürüm tutarlılığı iyi** (D-3 riskine
karşı: sürüm iddiası üç ayrı disk satırından geldi, tek kaynak değil).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
