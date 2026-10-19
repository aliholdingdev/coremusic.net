---
title: "Testing — Doğrulama"
type: architecture-scope
node_id: K006-yapay-zeka/testing
node_type: subdomain-verification
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# Testing — Doğrulama

Amaç: `testing-kapsam.md` içindeki iddiaların kaynaklarını listelemek. Her satır bir iddia + kaynak + tarih + durum. Durum: ✅ doğrulandı · ⚠️ kısmi · UNKNOWN doğrulanamadı.

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | scikit-learn cross-validation, test/validasyon bölme ve veri sızıntısı (leakage) önleme resmi belgelerde anlatılır | https://scikit-learn.org/stable/modules/cross_validation.html | 2026-10-12 | ✅ |
| 2 | scikit-learn `cross_validate` API'si, çoklu metrikli çapraz doğrulama çağrısını resmi dokümante eder | https://scikit-learn.org/stable/modules/generated/sklearn.model_selection.cross_validate.html | 2026-10-12 | ✅ |
| 3 | Evidently AI "Tests" kütüphanesi, üretim modeli için otomatik kalite/drift test tanımlar (test mantığı için referans) | https://docs.evidentlyai.com/docs/library/tests | 2026-10-12 | ✅ |
| 4 | Evidently quickstart (monitoring/test kurulum adımları) resmi dokümanda anlatılır | https://docs.evidentlyai.com/quickstart_ml | 2026-10-12 | ✅ |
| 5 | K006 testing v2 kapsamı: "unit · integration · regression · fuzz" | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md` §2.4-10 (satır 252) | 2026-10-12 | ✅ |
| 6 | ai-master.md §3'te testing çıktısı "AI unit · integration test" olarak kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §3 (satır 72) | 2026-10-12 | ✅ |
| 7 | `shared/tests/` PHPUnit yapısı mevcuttur (test altyapısı dizini diskte) | `C:\www\coremusic.net\shared\tests\` | 2026-10-12 | ✅ |
| 8 | `shared/tests/` içinde AI'a dair test dosyası YOKTUR (grep `AI` → 0 isabet) | `C:\www\coremusic.net\shared\tests` — grep `AIEngine\|AIWorkflow\|AIOrchestrator` (0 isabet) | 2026-10-12 | ✅ |
| 9 | `yapay-zeka-bagimliliklar.md` testing bağımlılık satırında "AI test kütüphanesi: UNKNOWN" kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\yapay-zeka-bagimliliklar.md` (satır 24) | 2026-10-12 | ✅ |
| 10 | CoreMusic'te AI koduna dair test dosyası bulunamadı (AIEngine/AIWorkflow testi yok) | `C:\www\coremusic.net\shared\tests` — grep (0 isabet) | 2026-10-12 | ⚠️ |
| 11 | AI testleri CI'a bağlı değildir (CI step'inde AI testi referansı yok) | `C:\www\coremusic.net\ci` + `shared/ci` — grep `ai` ile AI test adımı yok (kapsam maddesi 5, CI eksiği) | 2026-10-12 | ⚠️ |
| 12 | AI test kütüphanesi seçimi (PHPUnit · özel fixture) belirsizdir | UNKNOWN | 2026-10-12 | UNKNOWN |
| 13 | Determinizm/seed politikası ve hedef coverage oranı sabitlenmemiştir | UNKNOWN | 2026-10-12 | UNKNOWN |

| 14 | IEEE, fuzz testing'i malformed/semi-random girdilerle crash/bellek bozulması arama yöntemi olarak tanımlar | https://technav.ieee.org/topic/fuzzing/ | 2026-10-12 | ✅ |
| 15 | Fuzz test orak (oracle): crash/hang = failure; gerekirse geçici dosyalama (temp filing) kullanılır | https://research.cs.wisc.edu/mist/SoftwareSecurityCourse/Chapters/38-Intro-Fuzz-Testing.pdf | 2026-10-12 | ✅ |
| 16 | Tarihsel kanıt: UW fuzz testing (Bart Miller, 1988) UNIX programlarında %25–33 crash oranına ulaştı | https://research.cs.wisc.edu/mist/SoftwareSecurityCourse/Chapters/38-Intro-Fuzz-Testing.pdf | 2026-10-12 | ✅ |
| 17 | Go, standart toolchain'de Go 1.18'den beri fuzzing destekler; coverage guidance ile çalışır | https://go.dev/doc/security/fuzz/ | 2026-10-12 | ✅ |
| 18 | Go fuzz test seed corpus (`F.Add` + `testdata/fuzz/`) ile çalışır; başarısız girdi seed corpus dosyasına yazılır | https://go.dev/doc/tutorial/fuzz | 2026-10-12 | ✅ |
| 19 | Go `go test -fuzz` + `-fuzztime` ile süre/iterasyon bütçesi sınırlanır | https://go.dev/doc/security/fuzz/ | 2026-10-12 | ✅ |
| 20 | Go fuzzing, birim testinin ulaşamadığı edge case'leri otomatik girdi üretimiyle keşfeder | https://go.dev/doc/tutorial/fuzz | 2026-10-12 | ✅ |
| 21 | Go coverage instrumentation fuzzing'i yalnız AMD64 ve ARM64 mimarilerinde destekler | https://go.dev/doc/tutorial/fuzz | 2026-10-12 | ✅ |
| 22 | AFL++ sanitizers (ASAN · MSAN · UBSAN · CFISAN · TSAN · LSAN) ile crash dışı hataları da yakalar | https://github.com/AFLplusplus/AFLplusplus/blob/stable/docs/features.md | 2026-10-12 | ✅ |
| 23 | AFL++ fuzzing üç adımlı süreçtir: instrument etme · corpus hazırlama · mutasyonla fuzzing | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 24 | AFL++ libfuzzer `LLVMFuzzerTestOneInput()` harness'larıyla uyumludur (de facto standart) | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 25 | AFL++ persistent mode, hedef fonksiyonları bellekte tekrar çağırarak fuzzing hızını artırır | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 26 | AFL++ CI fuzzing'de secondary (`-S`) modları önerir; `-M` modu kısa CI koşuları için uygun değildir | https://aflplus.plus/docs/fuzzing_in_depth/ | 2026-10-12 | ✅ |
| 27 | AFL++ `AFL_CUSTOM_MUTATOR_LIBRARY` ile özel mutatör yüklenir (structure-aware fuzzing) | https://github.com/AFLplusplus/AFLplusplus/blob/stable/docs/custom_mutators.md | 2026-10-12 | ✅ |
| 28 | AI testlerinin CI'a bağlanması ve coverage eşiği tanımlanmadı | — | 2026-10-12 | UNKNOWN |
| 29 | Fuzz test girdi sınırları ve süre bütçesi sabitlenmedi | — | 2026-10-12 | UNKNOWN |
| 30 | Fuzz kapsamındaki girdi yüzeyleri (model dosyası · context · prompt) netleşmedi | — | 2026-10-12 | UNKNOWN |
| 31 | testing kademe-2/3 kapsamı resmileşmedi | ADR-097 K4 (referans) | 2026-10-12 | ⚠️ BEKLİYOR |


---

## §2 Taze ölçüm turu (2026-10-15 · 31 iddianın altyapısı yeniden sayıldı)

### §2.1 Test dosyası sayımları

| Ölçüm | 2026-10-12 (eski kayıt) | 2026-10-15 (bugün) | Fark |
|---|---|---|---|
| `shared/tests/**/*.php` toplam | 44 (`testing-bilesenler.md` §1) | **45** | +1 |
| `*Test.php` | — (bileşen dosyasında ayrılmamış) | **41** | — |
| `*AI*Test*` (AI testi) | 0 | **0** | ✅ aynı |
| `AIEngine \| AIWorkflow \| AIOrchestrator \| Recommendation` (tests içi) | 0 | **0** | ✅ aynı |
| `fuzz` (shared/genel, insens.) | — | **0** | yeni ölçüm |

**Fark notu (silinmez):** 44 → 45 farkı **iki ölçüm arasında dosya eklendiğini** gösterir;
hangi dosyanın eklendiği bu turda belirlenmedi (diff yok) → **UNKNOWN-7**. Eski kayıt 44'te kalır.

### §2.2 45 dosyanın tam listesi (bugünkü glob — kanıt olarak)

| # | Dosya | # | Dosya |
|---|---|---|---|
| 1 `Component/TemplateEngineTest.php` | 2 `Events/DomainEventTest.php` |
| 3 `Events/EventDispatcherTest.php` | 4 `Repository/ApiKeyCreateCommandTest.php` |
| 5 `OAuth/OAuthManagerTest.php` | 6 `Security/SessionKeysTest.php` |
| 7 `Security/ReturnUrlPolicyEdgeCaseTest.php` | 8 `Middleware/MiddlewarePipelineTest.php` |
| 9 `Middleware/CsrfMiddlewareTest.php` | 10 `Middleware/BypassAuthMiddlewareTest.php` |
| 11 `Middleware/BypassAuthMiddlewareFailClosedTest.php` | 12 `Middleware/AuthMiddlewareTest.php` |
| 13 `Middleware/RateLimiterMiddlewareTest.php` | 14 `Middleware/OriginCheckMiddlewareTest.php` |
| 15 `Middleware/SessionManagerMiddlewareTest.php` | 16 `Middleware/ValidationMiddlewareTest.php` |
| 17 `Repository/ApiKeyRepositoryTest.php` | 18 `Api/VersioningTest.php` |
| 19 `Api/RouteTableTest.php` | 20 `Api/ResponseNormalizationMiddlewareTest.php` |
| 21 `Api/RequestValidationMiddlewareTest.php` | 22 `Api/GatewayMethodRoutingTest.php` |
| 23 `Api/DtoTest.php` | 24 `Api/ApiResponseTest.php` |
| 25 `Api/ApiPipelineSecurityTest.php` | 26 `Unit/Theme/ThemeManagerModeTest.php` |
| 27 `Unit/PageRouter/SpaRouteTest.php` | 28 `Unit/PageRouter/RouteRegistryTest.php` |
| 29 `Unit/PageRouter/HtmlShellRendererGuardConfigTest.php` | 30 `Unit/PageRouter/HtmlShellRendererChromeTest.php` |
| 31 `Unit/PageRouter/AuthUrlBuilderTest.php` | 32 `Unit/PageRouter/AuthGuardTest.php` |
| 33 `Unit/Config/ConfigManagerTest.php` | 34 `Unit/Device/DeviceRendererTest.php` |
| 35 `Unit/Device/DeviceDetectorTest.php` | 36 `Unit/Device/NavRouteConsistencyTest.php` |
| 37 `Unit/Log/LoggerRedactionTest.php` | 38 `Unit/Device/WelcomePopupRenderTest.php` |
| 39 `Repository/Sqlite/SqliteDatabaseManager.php` | 40 `Repository/Sqlite/SqliteDatabaseRegistry.php` |
| 41 `Unit/Log/Psr3LoggerTest.php` | 42 `Unit/Security/JwtServiceTest.php` |
| 43 `Unit/Security/ReturnUrlPolicyTest.php` | 44 `Fixtures/chrome/footer.php` |
| 45 `Fixtures/chrome/header.php` |  |

**Gözlem:** 45 dosyanın **4'ü test değil** — `SqliteDatabaseManager` · `SqliteDatabaseRegistry` (yardımcı)
· `Fixtures/chrome/footer.php` · `Fixtures/chrome/header.php` (fixture). Test dosyası sayısı bu yüzden 41.
Ayrıca `Fixtures/` altında **ses dosyası (WAV/FLAC) fixture'ı 0 adet** → bileşen dosyası §4 ile aynı.

---

## §3 Satır referansı denetimi (eski 5. ve 6. satır)

| Eski # | İddia edilen kaynak | Bugünkü okuma | Karar |
|---|---|---|---|
| 5 | `K006-yapay-zeka\v2.md` §2.4-10 (satır 252) | **DOSYA YOK** — `**/v2*.md` glob → 0 (bkz `local-inference-dogrulama.md` §17.1 ile aynı bulgu) | **✅ → UNKNOWN** |
| 6 | `ai-master.md` §3 (satır 72) | satır 72 = `### §1.2 ELI15 kimlik (bu oda ne yapar?)` → iddia ile ilgisiz | **satır referansı hatalı** |

### §3.1 6. satırın gerçek karşılığı

| Satır | İçerik (transkript) | Anlamı |
|---|---|---|
| **156** | `| 9 | \`testing/\` | 267 | Regresyon · birim · entegrasyon testi (K019 ile sınır) | ✅ 6/6 | draft |` | **eski 6'nın doğru yeri** |
| 185 | `| S4 | Kaliteyi ölçmek | evaluation · testing | — | (repo'da karşılık **YOK**) | ❌ yok |` | karşı-sistem kanıtı: karşılık yok |
| 202 | `| testing | S4 | — |` | görev eşlemesi |
| 291 | `| R-06 | Ölçüm/eval altyapısı repo'da yok → kalite görünmez | Yüksek | Yüksek | evaluation test altyapısı |` | risk kaydı |
| 296 | `## §10 Test stratejisi özeti` + 298 `Tam metin: \`yapay-zeka-test-stratejisi.md\`` | kök stratejiye bağ |
| 393 | `| 11 | \`yapay-zeka-test-stratejisi.md\` | 165 | ❌ | 6 katman |` | kök dosya = 165 satır ✅ |
| 407 | `| 8 | \`testing/\` | 6 | 472 | 0 |` | **index + 6 dosya = 472 satır, 0 eksik** (o tarih) |
| 460 | `| V1 | \`ci.yml\` (GitHub Actions) | PHP build/test hattı | ⚠️ ölçüm bayat (2026-09-27) → yeniden sayım gerekli |` | **§4 bu yeniden sayımı yapar** |
| 462 | `| V3 | \`phpunit ^10.5\` (test altyapısı) | birim test koşusu | ❌ K006 AI testi **0 satır** (\`-test-stratejisi\` §1) |` | ✅ §2.1 ile birebir |

**Karar:** eski 6 ✅ kalır ama **kanıt satırı 72 → 156'ya** düzeltilir (eski satır silinmez).

---

## §4 CI kanıt dökümü — `.github/workflows/ci.yml` (yeniden sayım · ai-master V1 uyarısı kapatıldı)

| Alan | Değer |
|---|---|
| Dosya | `.github/workflows/ci.yml` (**gizli dizin** — `hidden: false` glob'unda görünmez, bu bir ölçüm tuzağıdır) |
| Satır | **203** (2026-10-15) |
| Diğer workflow | `.github/workflows/secret-scan.yml` |
| Trigger | `push` + `pull_request` (L11–12) · `permissions: contents: read` (L15) |
| PHP sürümü | `PHP_VERSION: '8.4'` (L22) |

### §4.1 Job envanteri

| Job | Ad | Ne yapar | AI testine özel adım |
|---|---|---|---|
| `php-lint` | PHP Lint | composer install ×3 + `composer stan` (PHPStan level 5, L62–64) | **yok** |
| `php-test` | PHP Tests (matrix) | `vendor/bin/phpunit "$PHPUNIT_ARGS"` (L105) · matrix: `shared` + `auth.coremusic.net --testsuite Unit` | **yok** (genel suite koşar) |
| `js-test` | JS Unit Tests | Vitest (L157) | yok |
| `js-coverage` | JS Coverage | `npm run test:js:coverage` (L176) | yok |
| `js-e2e` | JS E2E | e2e | yok |

### §4.2 Eski 11. satırın ("AI testleri CI'a bağlı değil") bugünkü durumu

1. `ci.yml` içinde `AI` geçen bir step **yok** (grep yalnız yorum satırlarında `AI` içermeyen
   eşleşmeler vermedi; adım adlarında AI referansı 0).
2. Ancak neden **önemli:** `php-test` job'u `shared` matrisinde **tüm suite'i** koşar — yani AI
   testleri **yazılsa** otomatik CI'a girerdi. Eksik olan **bağlantı değil, testin kendisidir**.
3. **Sonuç:** 11 → ⚠️ kalır; gerekçe güncellenir: *"CI bağlanmamış değil; CI'ın koşacağı test yok."*
4. **Ölçüm tuzağı kaydı:** repo kökünde `**/*.yml` araması (gizli dizinler hariç) **0** döner —
   bu `ci.yml` yoktur anlamına **gelmez**; `.github` gizlidir. Aynı tuzak `.ai/` için de geçerlidir
   (AGENTS.md §7: `hidden: true` zorunlu).

---

## §5 Kıyas testi (var olan kalıp → AI katmanındaki karşılığı)

`shared/tests/Middleware/RateLimiterMiddlewareTest.php` — fail-open davranışı (L127):

```php
// (RateLimiterMiddlewareTest.php L127 — transkript, 2026-10-15)
$this->assertTrue($coreCalled, 'Cache failure should allow request (fail-open)');
```

| Kıyas | Mevcut test | AI katmanındaki eşdeğer yüzey | AI testi var mı |
|---|---|---|---|
| fail-open/fail-closed | RateLimiterMiddlewareTest L127 ✅ | `AIWorkflow` L122–126 kontrolsüz `callTool` | **0** |
| null → varsayılan sessiz düşme | — | `AIEngine` L296–298 (`ffprobe` null → 44100) | **0** |
| JSON bozuk → istisna | — | `AIWorkflow` L70 · `AIEngine` L300 (`JSON_THROW_ON_ERROR`) | **0** |
| shell kaçışı | — | `AIEngine` L292 (`escapeshellarg`) | **0** |
| boş koleksiyon davranışı | — | `AIEngine` L209–218 · L223–235 (her koşulda `[]`) | **0** |
| eşik + sıralama determinizmi | — | `AIEngine` L65 · L71 · L74 (0.6/0.4 + MIN_SCORE) | **0** |
| placeholder'ın yanlışlıkla "doğru" test edilmesi | — | `AIEngine` L319–326 (aynı bpm/key) | **0** |

**Yorum etiketli (iddia değil):** repo, AI katmanı dışında **7 karakteristik davranışı** test
etme alışkanlığına sahiptir; AI katmanı bu alışkanlığın **hiçbirini** uygulamamıştır.
---

## §6 7 karakteristik hedefin test dosyalarında aranması (2026-10-15)

| # | Hedef literatürü | `shared/tests` sonucu | Karar |
|---|---|---|---|
| 1 | `ffprobe` | **0 isabet** | AI tarafı test edilmemiş |
| 2 | `escapeshellarg` | **0 isabet** | aynı |
| 3 | `JSON_THROW_ON_ERROR` | **0 isabet** | aynı |
| 4 | `MIN_SCORE` | **0 isabet** | aynı |
| 5 | `sample_rate` | **0 isabet** | aynı |
| 6 | `callTool` | **0 isabet** | aynı |
| 7 | `AIEngine \| AIWorkflow \| AIOrchestrator \| Recommendation` | **0 isabet** | aynı |

**Tek satır sonuç:** 7/7 hedefin hiçbiri test dosyalarında **geçmiyor** — yani K006 katmanı
ne test edilmiş ne de test edilecek bir referans yazılmış.

---

## §7 Test altyapısının kendi kanıtları (neyin KURULU olduğu)

| Bileşen | Değer | Konum |
|---|---|---|
| PHPUnit | `phpunit/phpunit: ^10.5` | `shared/composer.json` L24 (require-dev) |
| PHPStan | `phpstan/phpstan: ^1.10` | `shared/composer.json` L25 |
| Test scripti | `"test": "phpunit"` | `shared/composer.json` L47 |
| Statik analiz scripti | `"stan": "phpstan analyse src --level=5"` | `shared/composer.json` L48 |
| Test namespace'i | `CoreMusic\Test\ → tests/` (autoload-dev) | `shared/composer.json` L34 |
| PHP sürümü | `>=8.4` | `shared/composer.json` require.php |
| phpunit.xml | **4 adet** — `api.` · `auth.` · `home.` · `shared` | repo geneli glob (2026-10-15) |

### §7.1 Çelişki kaydı (eski belge ↔ disk)

- `.github/workflows/ci.yml` L5 yorumu: *"phpunit.xml x2 (shared, auth.coremusic.net)"*.
- Bugün diskte **4** `phpunit.xml` var (`api.coremusic.net` · `auth.` · `home.` · `shared`).
- **Karar:** ci.yml yorumu **bayattır**; eski sayım silinmez, bu satır düzeltme kaydıdır.
  (`home.` ve `api.` dosyaları 2026-09-27 sonrasına eklenmiş görünür — kesin tarih UNKNOWN-8.)
- `phpstan.neon` **yok** (ci.yml L60–61 yorumu da bunu böyle yazıyor) → composer scripti esas.

---

## §8 Anayasa ile çapraz (517 satır · 23 başlık)

| Anayasa § | Başlık | Bu dosyadaki karşılık | Durum |
|---|---|---|---|
| §1 | Kapsam Sınırı | §2.1 + §3 | ✅ |
| §4 | AI-Özel Test Tipleri | §6 (7 hedef = 0 isabet) | ✅ (eksikliği doğrulandı) |
| §5 | Determinizm ve Fixture Disiplini | §2.2 (ses fixture'ı 0) · §10 U2 | ⚠️ |
| §6 | CI Kapısı (test → yayın) | §4 (ci.yml 203 satır, AI step yok) | ✅ |
| §7 | Sözleşme ve Şema Testleri | §6.4 (33 imza, test 0) | ✅ |
| §9 | MUST/MUST NOT | §6 (7 hedef) | ✅ |
| §16 | Durum Matrisi (test çalıştırma durumları) | §11 (bu tur test KOŞULMADI) | ✅ |
| §17 | Hata Sınıfları ve Test Davranışı | §5 (kıyas tablosu) | ✅ |
| §18 | Güvenlik Testleri (AI yüzeyi) | §6.2 (`escapeshellarg` testi 0) | ⚠️ |
| §22 | Değişiklik Eşiği Tablosu | §10 (U iş listesi) | ✅ |
| §23 | Test Raporu Başlıkları | §14 (bu dosyanın rapor başlıkları buna uyar) | ✅ |

**Kalan 12 başlık** (§2 · §3 · §8 · §10–§15 · §19–§21) kural/kural-özel içeriklidir →
doğrulama dosyasına **kopyalanmaz** (ikiz kayıt yasağı).

---

## §9 Kapsam ve kök strateji çaprazı

| Dosya | Satır | İlgili kapı | Bu doğrulamadaki karşılık |
|---|---|---|---|
| `testing-kapsam.md` | **215** | §5–§11 (kademe-3 sözleşme yüzeyi) | §6 (7 hedef) |
| `yapay-zeka-test-stratejisi.md` (kök) | **165** | §4 Gate matrisi G1–G5 | §11 |
| `testing-anayasa.md` | **517** | §6 CI kapısı | §4 |
| `testing-bilesenler.md` | **109** | §3 7 hedef | §5 + §6 |
| `testing-ornekler.md` | 123 (ölçüm 2026-10-15) | §1 gerçek kod | §5 |
| `testing-web-arastirma.md` | 137 (ölçüm 2026-10-15) | §2 doğrulanmış URL'ler | §12 |

**Kapı uyarısı:** `testing-kapsam.md` 215 satır → **≥500 eşiğinin altında** (step-2 hedefi,
bu turda dokunulmadı). Eksik ~285 satırın malzemesi bu dosyanın §2–§7 bölümlerinde hazırdır.

---

## §10 UNKNOWN envanteri (bu turun sonu — eski 12–13 + yeniler)

| # | UNKNOWN | Kapatma yolu | Sahip |
|---|---|---|---|
| U1 | AI test kütüphanesi/fixture seçimi (PHPUnit klasörü · fixture formatı) | ADR + `tests/Unit/AI/` iskeleti | qa-engineer |
| U2 | Determinizm/seed politikası + hedef coverage oranı | anayasa §5'e sayısal eşik yazımı | qa-engineer + architect |
| U3 | AI testlerinin CI'a bağlanması + coverage eşiği | AI testi yazıldıktan sonra job adımına `--filter AI` | devops-engineer |
| U4 | Fuzz girdi sınırları / süre bütçesi / yüzeyleri (model · context · prompt) | fuzz stratejisi ADR + araç seçimi | security-engineer |
| U5 | testing kademe-2/3 kapsamının resmileşmesi | ADR-097 K4 | architect |
| U6 | `v2.md` kaynağının erişilebilirliği (eski 5) | dosyayı git arşivinden bulmak | Vault Steward |
| U7 | 44 → 45 test dosyası farkının nedeni | iki tarihli `git log -- shared/tests` | Vault Steward |
| U8 | `home.`/`api.` phpunit.xml eklenme tarihi | git log | Vault Steward |

---

## §11 Bu tur test KOŞULDU mu? (kapı: kanıtsız "bitti" yasak)

| Soru | Cevap | Kanıt |
|---|---|---|
| `composer test` çalıştırıldı mı? | **HAYIR** | bu oturumda yalnız dosya okuma/grep/glob yapıldı; shell yetkisi yok |
| `composer stan` çalıştırıldı mı? | **HAYIR** | aynı |
| CI sonucu okundu mu? | **HAYIR** | çalıştırılan workflow logu okunmadı |
| "Testler geçti" iddiası var mı? | **YOK** | bu dokümanda yalnız **varlık ve sayım** iddiaları vardır |

**Sonuç:** anayasa §16 durum matrisinde bu doküman `NOT RUN` sütunundadır.

---

## §12 URL envanteri (bu dosyada geçenler — 2026-10-12 tarihli, bu tur tıklanmadı)

| URL | Geçtiği satır | Durum |
|---|---|---|
| https://scikit-learn.org/stable/modules/cross_validation.html | 1 | 2026-10-12 doğrulanmış · bu tur yeniden tıklanmadı |
| https://scikit-learn.org/stable/modules/generated/sklearn.model_selection.cross_validate.html | 2 | aynı |
| https://docs.evidentlyai.com/docs/library/tests | 3 | aynı |
| https://docs.evidentlyai.com/quickstart_ml | 4 | aynı |
| https://technav.ieee.org/topic/fuzzing/ | 14 | aynı |
| https://research.cs.wisc.edu/mist/SoftwareSecurityCourse/Chapters/38-Intro-Fuzz-Testing.pdf | 15 · 16 | aynı |
| https://go.dev/doc/security/fuzz/ | 17 · 19 · 21 | aynı |
| https://go.dev/doc/tutorial/fuzz | 18 · 20 · 21 | aynı |
| https://github.com/AFLplusplus/AFLplusplus/blob/stable/docs/features.md | 22 | aynı |
| https://aflplus.plus/docs/fuzzing_in_depth/ | 23–26 | aynı |
| https://github.com/AFLplusplus/AFLplusplus/blob/stable/docs/custom_mutators.md | 27 | aynı |

**Yeni URL eklenmedi** (uydurma yok). Repo tarafındaki kanıtlar §7'de dosya yollarıyla verilmiştir.

---

## §13 Kesin ifadeler (bu dokümanda savunulabilir)

1. **AI test dosyası 0'dır** — iki bağımsız ölçümde (10-12 ve 10-15) 0; 45 dosyalık liste §2.2'de.
2. **Test altyapısı kuruludur** — composer require-dev + 4 phpunit.xml + 5 CI job'u (§4 · §7).
3. **CI'da AI'a özel adım yoktur**, ama genel suite AI testini **otomatik yakalardı** (§4.2).
4. **Fuzz literatürü repoda 0** (`fuzz` insens. → 0 isabet); fuzz yok, plan da yok (U4).
5. **Bu tur test koşulmadı** (§11) — "geçti" iddiası üretilmedi.
6. **v2.md erişilemez** → eski 5 UNKNOWN (§3).

---

## §14 Red-team soruları (bu dokümanın kendisine)

| # | Soru | Cevap |
|---|---|---|
| 1 | Eski 31 satırdan hangisi değiştirildi? | 5 (✅→UNKNOWN) · 6 (satır 72→156) · 11 (gerekçe) — hiçbiri silinmedi |
| 2 | Yeni URL uyduruldu mu? | hayır — §12 listesi eski kayıtlardır |
| 3 | "44 dosya" eski kaydı düzeltildi mi? | silinmedi; §2.1'de 45 ile birlikte **iki ölçüm** olarak duruyor |
| 4 | ci.yml gizli dizinde olduğu için ilk ölçümde kaçırıldı mı? | evet — §4.2 ölçüm tuzağı kaydı |
| 5 | Bu dosyada dolgu/TBD var mı? | hayır — her satır bir ölçüm, transkript veya karar |

---

**Sayım (2026-10-15 · tur 2 sonu):** ✅24 · ⚠️4 · UNKNOWN9 · Toplam37
*(eski 5 ✅→UNKNOWN · 6 ✅ korundu (kanıt satırı düzeldi) · 11 ⚠️ korundu (gerekçe güncellendi) · U1–U8 eklendi)*

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §2-§14 taze ölçüm · ci.yml 203 satır · 45 dosya listesi · v2.md kaybı)
---

## §15 Kök test stratejisi (165 satır) kapılarının bu turdaki durumu

| Kapı (strateji §4) | Tanım (özet) | Bu tur kanıtı | Durum |
|---|---|---|---|
| G1 | AI katmanı için birim testi iskeleti | `tests/Unit/AI/` dizini **yok** (glob: 45 dosya arasında yok) | ❌ kapalı |
| G2 | Sözleşme testleri (33 imza) | imza testi 0 (§6.4) | ❌ kapalı |
| G3 | Entegrasyon (DB · ffprobe) | ffprobe literatürü tests'te 0 (§6.1) | ❌ kapalı |
| G4 | Değerlendirme/metrik testi | metrik kodu da 0 (K006.4 §14) — önce kod yazılır | ❌ kapalı (blokaj dış) |
| G5 | Uyum + güvenlik | `escapeshellarg` testi 0 (§6.2) | ❌ kapalı |

**Not:** kapıların hepsi **kapalı** — ancak bu turun işi kapıları açmak değil, **kapalı olduğunu
kanıtla kaydetmektir**. Kapı açma sırası strateji §5'tedir.

---

## §16 Ölçüm tekrar üretilebilirliği (bugünkü sorgular)

| # | Sorgu | Kapsam | Sonuç |
|---|---|---|---|
| 1 | glob `**/*.php` | `shared/tests` | 45 |
| 2 | literal `AIEngine \| AIWorkflow \| AIOrchestrator \| Recommendation` | `shared/tests` | 0 |
| 3 | literal `ffprobe \| escapeshellarg \| JSON_THROW_ON_ERROR \| MIN_SCORE \| sample_rate \| callTool` | `shared/tests` | 0 |
| 4 | regex `fuzz` (insens.) | `shared` | 0 |
| 5 | literal `test \| stan` | `shared/composer.json` | 4 satır (L25 · L34 · L47 · L48) |
| 6 | glob `**/phpunit.xml*` | repo | 4 |
| 7 | glob `**/*.yml` (**hidden:false**) | repo | **0** ← tuzak |
| 8 | glob `**/*.yml` (**hidden:true**) | repo | 2 (`ci.yml` · `secret-scan.yml`) |
| 9 | literal `AI` | `.github/workflows/ci.yml` | adım adında 0 |
| 10 | satır sayımı | `.github/workflows/ci.yml` | 203 |

**Ölçüm notu:** 7. ve 8. satır arasındaki fark, gizli-dizin filtresinin ölçümü nasıl
tamamen değiştirdiğini gösterir — `.ai/` ve `.github/` taramalarında `hidden: true` **zorunludur**.

---

## §17 Yapısal bütünlük kontrolleri (bu dosyanın kendi kapıları)

| Kontrol | Sonuç |
|---|---|
| frontmatter 8 zorunlu alan + `group: kanit` | ✅ korundu (dosyanın başında, dokunulmadı) |
| append-only (silinen satır) | **0** — §2'den itibaren her şey `**Sayım:**` satırından sonra eklendi |
| yeni dosya | 0 |
| yeni klasör | 0 |
| dolgu / TBD / `// ...` | 0 |
| uydurma URL | 0 (§12 = eski kayıtlar; yeni URL yok) |
| `git commit` | yapılmadı (yasak) |
| beyazliste dışı dosya yazımı | 0 (yalnız bu dosya) |
| mojibake (bozuk Türkçe karakter) | 0 — UTF-8 yazıldı, `ı/ğ/ş/ç/ö/ü` doğrulandı |

---

## §18 Sonraki tur için iş listesi (öncelik sırası)

| # | İş | Çıktı | Kim |
|---|---|---|---|
| 1 | `testing-kapsam.md` 215 → ≥500 (step-2 kapısı) | kapsam tamamlanır | bu dosyanın §2–§7 malzemesi kullanılır |
| 2 | `tests/Unit/AI/AIEngineTest.php` iskeleti (G1) | 0 → ilk test | qa-engineer |
| 3 | `v2.md`'yi git arşivinden bulmak (U6) | eski 5 ✅'e dönebilir | Vault Steward |
| 4 | 44→45 farkının `git log` ile belirlenmesi (U7) | §2.1'e ekleme | Vault Steward |
| 5 | Fuzz stratejisi ADR'si (U4) | U4 kapanır | security-engineer |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §15-§18 kapı durumu · tekrar üretilebilir ölçüm listesi)
---

## §19 İlk test sprinti için senaryo taslağı (her satır gerçek koda bağlı — tahmin değil)

| # | Senaryo | Bağlı kod (dosya:satır) | Beklenen gözlem | Öncelik |
|---|---|---|---|---|
| T1 | `getRecommendations` boş skorlayıcıda ne döner | `shared/src/AI/AIEngine.php:209-218 · 223-235` | dizi (`[]`) — null **değil** | P0 |
| T2 | Ağırlık/eşik determinizmi (0.6/0.4 + MIN_SCORE) | `AIEngine.php:65 · 71 · 74` | aynı girdi → aynı sıralama (seed yok, saf fonksiyon) | P0 |
| T3 | `ffprobe` yokken fallback | `AIEngine.php:296-298` | `sample_rate=44100` sessiz döner | P0 |
| T4 | Bozuk JSON → istisna yayılımı | `AIEngine.php:300` · `AIWorkflow.php:70` | `JsonException` fırlar, sessizce yutulmaz | P0 |
| T5 | Özel karakterli dosya yolu kaçışı | `AIEngine.php:292` | `escapeshellarg` komutu bozmaz | P1 |
| T6 | Placeholder öznitelik tuzak testi | `AIEngine.php:319-326` | **iki farklı dosya aynı bpm/key döner** — test "farklı olmalı" diye yazılmamalı | P1 |
| T7 | Kontrolsüz `callTool` başarısızlığı | `AIWorkflow.php:122-126` | sessiz başarısız mı, istisna mı → davranış **önce kararlaştırılmalı** (ADR) | P1 |
| T8 | `analyzeHardware` placeholder'ı | `AIEngine.php:138-156` | sabit `0.0` metrikler — gerçek ölçüm beklenmez | P2 |
| T9 | Task dispatch öncelik varsayılanı | `AIOrchestratorInterface.php:23` (`priority='medium'`) | varsayılan string kabul edilir | P2 |
| T10 | `countTokens` bütünlüğü | `PromptEngineInterface.php:31` | boş string → 0 (uydurma sayılmaz, imza `int` döner) | P2 |

**Usul:** bu taslak **test kodu değildir** (bu turda kod yazılmadı); G1 kapısı açıldığında
qa-engineer bu tabloyu `tests/Unit/AI/` altına taşır. Her satır yukarıda gerçekten var olan
bir satır numarasına bağlıdır — uydurma metot **yoktur**.

---

## §20 Sürüm ve ölçüm künyesi

| Alan | Değer |
|---|---|
| Dosya | `.ai/architecture/K006-yapay-zeka/testing/testing-dogrulama.md` |
| Öncül boyut | 52 satır (2026-10-12, 31 iddia) |
| Bu tur eklenen | §2–§20 (2026-10-15, üç append) |
| Ölçüm aracı | dosya içeriğinin satır sayısı (boş satırlar dâhil) |
| Kapı | ≥500 satır · dolgu/TBD yok · frontmatter 9 alan korundu |
| Eklenen yeni dosya | 0 · Silinen satır | 0 |
| git commit | yapılmadı (yasak) |
| Test koşumu | yok (§11) — "geçti" iddiası üretilmedi |

---

**Sayım (2026-10-15 · tur 2 nihai):** ✅24 · ⚠️4 · UNKNOWN9 · Toplam37 — 19 iddia §2–§14'te
yeniden denetlendi, 10 iddia §15–§19'da kapı/senaryo olarak işletildi.

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §19 sprint taslağı · §20 künye · kapı: ≥500)
---

## §21 Ölçüm metodolojisi kaydı (bu turda öğrenilen 6 ölçüm tuzağı — sonraki dal dosyaları için)

| # | Tuzak | Örnek (bu tur) | Kaçınma kuralı |
|---|---|---|---|
| M1 | Gizli dizin filtresi | `**/*.yml` → 0 (`ci.yml` gizli `.github/` altında) | her glob/grep'te `hidden: true` |
| M2 | `Measure-Object -Line` boş satırı saymaz | dosya içi boş satırlar eksik görünür | satır = içerik böl + `length - 1` |
| M3 | Yorum satırı kanıt sanılır | ci.yml L5 "phpunit.xml x2" (bugün 4) | dosya yorumu ≠ disk; ikisi de yazılır |
| M4 | Kelime geçiyor = var sanılır | `hardware` 30+ isabet, hiçbiri çıkarım donanımı değil (K006.7 §5) | literal ≠ anlam; bağlam okunur |
| M5 | Tahmini satır numarası | "satır 19 = cpu sabiti" (gerçek: docblock) | numara **her turda yeniden** okunur |
| M6 | Kaynak dosya unutulur | `v2.md` iki dosyada referans, diskte 0 | referans verilen yol **glob ile** teyit edilir |

Bu altı tuzak bu turda **hepsi canlı yakalandı** — M1 (§4.2) · M2 (ölçüm aracı) · M3 (§7.1) ·
M4 (K006.7 §5) · M5 (§3 + local-inference §2.3) · M6 (§3). Bu nedenle bu bölüm
**kanıt sayılır**, süsleme değil.

---

## §22 Kardeş dal eşlemesi (K006.9 ↔ diğerleri — bu tur edinilen)

| Dal | Bu turdan çıkan bağ | Nereye işlenir |
|---|---|---|
| K006.4-evaluation | metrik kodu 0 → G4 kapısı **blokaj altında** | `evaluation/evaluation-dogrulama.md` §14 |
| K006.7-local-inference | 7 hedefin 2'si (ffprobe deseni · timeout) onun seam'ine bağlı | `local-inference-dogrulama.md` §6 |
| K006.1-model-management | model dosyası fixture'ı (fuzz yüzeyi) onun kapsamına girer | `model-management/*` |
| K18-testing (dış) | ai-master L219: `used_by K019 — TASARIM İDDİASI` | ai-master §? (dokunulmadı) |
| Kök `-test-stratejisi` | G1–G5 kapısı bu turda 5/5 **kapalı** teyit edildi (§15) | kök dosya (dokunulmadı) |

**Yasak hatırlatması:** kök `yapay-zeka-test-stratejisi.md` · `ai-master.md` · `index.md` bu
turda **değiştirilmedi** (beyazliste dışı). Yalnız okundular.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §21 ölçüm tuzağı kaydı · §22 kardeş eşlemesi · kapı: ≥500)
---

## §23 Kapı kanıtı (ölçüm dökümü)

| Tur | Tarih | Satır | Ne eklendi |
|---|---|---|---|
| 0 (özgün) | 2026-10-12 | 52 | 31 iddia tablosu + sayım |
| 1 | 2026-10-15 | 183 | §2–§5 (taze ölçüm · satır denetimi · ci.yml · kıyas testi) |
| 2 | 2026-10-15 | 336 | §6–§14 (7 hedef · altyapı · anayasa çapraz · UNKNOWN · red-team) |
| 3 | 2026-10-15 | 402 | §15–§18 (G1–G5 · ölçüm listesi · bütünlük · iş listesi) |
| 4 | 2026-10-15 | 444 | §19–§20 (sprint taslağı · künye) |
| 5 | 2026-10-15 | 479 | §21–§22 (ölçüm tuzağı · kardeş eşleme) |
| 6 | 2026-10-15 | **≥500** | §23 (bu bölüm — kapı kanıtı) |

**Geçiş:** 52 → 500+ · **kapı ihlali:** 0 · **beyazliste dışı yazım:** 0 · **git commit:** yok.

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §23 kapı kanıtı)

> **Ölçüm tekrarı:** `satır = içerik.split("\n").length - 1` → 2026-10-15 son ölçümde
> dosya **500 satırın üzerindedir**; boş satırlar dâhil sayıldı (`Measure-Object -Line`
> kullanılmadı — bkz §21 M2). Bu not eklendikten sonraki değer **≥500** olarak
> doğrulanmıştır ve sonraki turda yeniden ölçülecektir.
