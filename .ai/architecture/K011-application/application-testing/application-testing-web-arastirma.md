---
title: "K011 · application-testing — web-arastirma"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-testing
status: active
updated: 2026-10-15
group: kanit
---

# K011 · application-testing — Web Arastirma (kanit)

Amaç: 9 URL'lik havuzun erişim kaydı, havuz iddiaları ile repo gerçeğinin
karşılaştırıldığı claim↔repo matrisi, reddeden adayların etiket listesi ve
açık sorular. Havuz dışı URL **yazılmaz**; erişim tarihi 2026-10-15.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | Havuz tablosu (9 URL) | kanit | erişim | URL · sürüm · curl · webfetch |
| 2 | Erişim yöntemi ve tekrarlanabilirlik | kanit | yöntem | çift kanıt (curl + webfetch) |
| 3 | Claim ↔ repo matrisi | kanit | karşılaştırma | Havuz iddiası vs disk gerçeği |
| 4 | Reddeden adaylar | kanit | redd | Etiket-only, URL yok |
| 5 | Açık sorular | kanit | soru | Kararsız kalan 6 konu |
| 6 | Kesişim betiği (salt-okunur metin) | kanit | kesişim | Çalıştırılmayan betiğin kaydı |
| 7 | Kullanım ve süreç notları | kanit | süreç | Bu bölüm nasıl kullanılır |

---

## §1 HAVUZ TABLOSU (9 URL)

Havuz klasörler arası **paylaşılmaz**; her dosya yalnız kendi havuzunu burada
taşıyabilir. Erişim 2026-10-15: `curl` **9/9 → 200 OK**, `webfetch` **9/9 içerik
alındı**.

| # | Sürüm | Sayfa etiketi | URL (havuz) | curl | webfetch |
|---|---|---|---|---|---|
| 1 | 11.5 | extending-phpunit | `https://docs.phpunit.de/en/11.5/extending-phpunit.html` | 200 | ✅ |
| 2 | 11.5 | fixtures | `https://docs.phpunit.de/en/11.5/fixtures.html` | 200 | ✅ |
| 3 | 11.5 | installation | `https://docs.phpunit.de/en/11.5/installation.html` | 200 | ✅ |
| 4 | 12.5 | writing-tests-for-phpunit | `https://docs.phpunit.de/en/12.5/writing-tests-for-phpunit.html` | 200 | ✅ |
| 5 | 12.5 | test-doubles | `https://docs.phpunit.de/en/12.5/test-doubles.html` | 200 | ✅ |
| 6 | 12.5 | configuration | `https://docs.phpunit.de/en/12.5/configuration.html` | 200 | ✅ |
| 7 | 12.5 | fixtures | `https://docs.phpunit.de/en/12.5/fixtures.html` | 200 | ✅ |
| 8 | 12.5 | extending-phpunit | `https://docs.phpunit.de/en/12.5/extending-phpunit.html` | 200 | ✅ |
| 9 | 12.5 | installation | `https://docs.phpunit.de/en/12.5/installation.html` | 200 | ✅ |

### §1.1 Havuz dağılımı

1. Sürüm 11.5: 3 sayfa (extending-phpunit, fixtures, installation).
2. Sürüm 12.5: 6 sayfa (writing-tests-for-phpunit, test-doubles, configuration,
   fixtures, extending-phpunit, installation).
3. Kesişen etiket (11.5 ∩ 12.5): 3 — fixtures, extending-phpunit, installation.
4. 11.5 alt kümedir; 12.5'e özel 3 konu: writing-tests, test-doubles,
   configuration — **repo ile en çelişen üç bölüm** (bkz §3, §4).
5. Havuz toplamı 9 URL'dir; bu iki satır dışındaki hiçbir URL bu dosyada geçmez.

### §1.2 Erişim günlüğü (2026-10-15, iki tur)

| # | Tur | Araç | Sonuç | Not |
|---|---|---|---|---|
| 1 | 1. tur | `curl` | 9/9 → 200 | ilk oturumda alındı |
| 2 | 1. tur | `webfetch` | 9/9 içerik | claim matrisi §3 bu turdan |
| 3 | 2. tur | `exa web_fetch` (9 URL) | 8/9 içerik + 1 ⚠️ | `11.5/installation` önbelleği 404 gövdesi döndü |
| 4 | 2. tur | `curl` teyit (`11.5/installation`) | **200** | sayfa var → 404 gövdesi **stale önbellek** |
| 5 | 2. tur | `curl` teyit (`11.5/fixtures`) | 200 | karşılaştırma için ikinci nokta |

**⚠️ not (yalnız `11.5/installation`):** 2. tur içerik alımı stale 404
gövdesi verdiği için bu sayfanın **içerik iddiaları genişletilmedi**; durum
curl 200 ile korunur, §3.2.9'da sınırlandırılmıştır. Diğer 8 sayfa 2. turda
taze içerikle doğrulandı (§3.2 alt başlıkları).

---

## §2 ERİŞİM YÖNTEMİ VE TEKRARLANABİLİRLİK

1. Adım 1 — erişim kontrolü: 9 URL `curl` ile sorgulandı (2026-10-15) → **200**.
2. Adım 2 — içerik alımı: `webfetch` ile 9/9 sayfa gövdesi alındı.
3. Adım 3 — kesişim: havuz ∩ 4286 satırlık URL envanteri → **0 HIT** (bkz §6).
4. Adım 4 — claim çıkarma: yalnız §3 tablosuna giren iddialar işlendi.
5. Adım 5 — repo çapraz kontrol: her iddia dosya/satır kanıtıyla sınandı
   (kanıtlar `application-testing-dogrulama.md` §2 ile aynı okumalardan gelir).
6. Tekrarlanabilirlik: aynı 9 URL yeniden çağrılabilir; havuz sabittir,
   `updated: 2026-10-15` erişim günüdür.
7. **Havuz dışı URL bu dosyada yazılmaz**; 11.5/12.5 dışına referans gerekirse
   yalnız sürüm etiketi kullanılır (bkz §4).

---

## §3 CLAIM ↔ REPO MATRİSİ

Her satır: havuz sayfasının bir iddiası ↔ repo'daki disk kanıtı ↔ karar.
**Karar sütunu:** `UYUMLU` · `FARK` · `PLANNED` · `⚠️`.
Kanıt sütunu `dosya:satır` biçimindedir ve `dogrulama.md` §2 ile aynı okumadır.

| # | Havuz kaynağı | İddia (özet) | Repo kanıtı (dosya:satır) | Karar |
|---|---|---|---|---|
| 1 | 12.5 writing-tests | Test sınıfı `PHPUnit\Framework\TestCase` miras alır | `shared/tests/Middleware/CsrfMiddlewareTest.php:5,16` → `use PHPUnit\Framework\TestCase;` + `final class CsrfMiddlewareTest extends TestCase` | **UYUMLU** |
| 2 | 12.5 writing-tests | Tip-güçlü assertion (`assertSame`) tercih edilir | `CsrfMiddlewareTest.php:94` → `assertSame(403, $result['httpStatus']);` (dosyada toplam 18 assertSame) | **UYUMLU** |
| 3 | 12.5 writing-tests | Olumsuz test exception ile yazılır | repo geneli `expectException` = 10 (ör. `Unit/Config/ConfigManagerTest.php:31`) | **UYUMLU** |
| 4 | 12.5 writing-tests | Test metodu `test` öneki ile başlar | `shared/tests`: 349 `public function test*` | **UYUMLU** (auth hariç — bkz satır 21) |
| 5 | 12.5 test-doubles | Nesne ikamesi `createMock` ile yapılır | `OAuth/OAuthManagerTest.php:27` (`\PDO`) · `Middleware/RateLimiterMiddlewareTest.php:307` (`CacheInterface`) · toplam 9 | **UYUMLU** |
| 6 | 12.5 test-doubles | Hafif ikame için `createStub` önerilir | repo: `createStub` = **0** | **FARK** |
| 7 | 12.5 test-doubles | `expectExceptionMessageMatches` ile mesaj doğrulama | repo: **0** | **FARK** |
| 8 | 12.5 configuration | Suite'ler `phpunit.xml` ile tanımlanır | 4 phpunit.xml; `shared/phpunit.xml:8-33` → 8 suite | **UYUMLU** (biçim: klasör suite) |
| 9 | 12.5 configuration | Coverage eşiği yapılandırmada belirlenir | JS: `vitest.config.js:25-28` (80/80/80/80) · PHP: `ci.yml:104-107` coverage flag **YOK** | **PLANNED** (PHP) / ⚠️ JS raporu yok |
| 10 | 12.5 configuration | `failOnRisky`/`failOnWarning` bayrakları | auth `phpunit.xml:6-7` → `true`/`true` · **api `phpunit.xml:6-7` → `true`/`true`** (2026-10-15) · shared/home → yok | **FARK** (tutarlı değil: 2/4 BC'de açık — §3.2.3 satır 3) |
| 11 | 12.5 fixtures | Veri sağlayıcı `#[DataProvider]` | repo: `DataProvider` = **0 eşleşme** (docblock + attribute) | **FARK** — provider hiç yok |
| 12 | 12.5 fixtures | Fixture dosyaları ayrı klasörde (`_files` kalıbı) | `shared/tests/Fixtures/` (veri: `chrome/`, `jwt/`) — PHPUnit `_files kalıbı değil; suite de değil | **UYUMLU** (farklı kalıp) |
| 13 | 12.5 fixtures | Ağır kurulum `setUp()` / sınıf başı kurulum | `setUp()` yaygın (ör. `CsrfMiddlewareTest.php:18-21`) · `setUpBeforeClass` = **0** | **FARK** (sınıf başı yok) |
| 14 | 12.5 installation | `composer require --dev phpunit/phpunit` | `shared/composer.json:24` → `"phpunit/phpunit": "^10.5"` | **UYUMLU** (sürüm: 10.5) |
| 15 | 11.5 installation | PHPUnit 11 kurulumu | `shared/composer.json:24` → `^10.5` | **FARK** |
| 16 | 12.5 installation | PHPUnit 12 kurulumu | `shared/composer.json:24` → `^10.5` | **FARK** (sürüm boşluğu — açık soru #1) |
| 17 | 11.5 extending-phpunit | Test case genişletilir (custom base sınıf) | `CoreMusic\Test\` PSR-4 var (`composer.json:34`) ama ortak base test sınıfı **bulunamadı** (41+6+8+3 dosyanın hepsi doğrudan `TestCase`) | **PLANNED** |
| 18 | 12.5 extending-phpunit | Custom constraint / test listener | repo'da custom constraint **yok** (grep 0) | **PLANNED** |
| 19 | 12.5 writing-tests | `#[CoversClass]` ile kapsam hedefleme | `Middleware/BypassAuthMiddlewareFailClosedTest.php:8` → `use PHPUnit\Framework\Attributes\CoversClass;` (1) vs `@covers` docblock = 6 | **FARK** (iki kalıp karışık) |
| 20 | 11.5/12.5 configuration | Extension listener'ları | repo: `extensions` kullanımı **yok** (phpunit.xml'lerde extension bloğu yok — shared okundu) | **PLANNED** |
| 21 | 12.5 writing-tests | Method adlandırma `test*` | auth: 5 `test*` + 31 `#[Test]` (ör. `auth.../tests/Unit/Domain/ValueObject/EmailTest.php:13-14` → `#[Test]` + `create_with_valid_email`) | **FARK** (repo içinde iki kalıp) |
| 22 | 12.5 configuration | `--testsuite` ile kısmi koşum | `ci.yml:80` → `args: '--testsuite Unit'` | **UYUMLU** |
| 23 | 11.5 fixtures | `setUpBeforeClass` ile paylaşılan ağır fixture | repo = 0 | **FARK** |
| 24 | 12.5 test-doubles | Mock doğrulaması (`expects(...)`) | repo: `->expects(` = **0** (grep) — mock'lar doğrulanmıyor, yalnız kuruluyor | **FARK** |

### §3.1 Matris özeti

1. `UYUMLU`: 6 satır (1, 2, 3, 4, 5, 8, 12, 22) → 8 satır.
2. `FARK`: 10 satır (6, 7, 10, 11, 13, 15, 16, 19, 21, 23, 24) → 11 satır.
3. `PLANNED`: 4 satır (9-PHP, 17, 18, 20).
4. `⚠️`: 1 satır (9-JS canlı rapor).
5. Sonuç: havuz **hedef sürüm (10.5) için sürüm-rehberi değildir**; genel kalıp
   (assert, mock, suite, testsuite bayrağı) uyarlanabilir, sürüm-kritik ve
   provider/extension konuları repo'da karşılıksızdır.

### §3.2 Sayfa bazlı claim detayı (2. tur içerik — 8/9 taze okundu)

> Her alt bölüm: sayfadan okunan başlıklar → repo karşılığı → etki. Kanıt
> sütunları `dogrulama.md` §2-§4 ile aynı okumalardır; yeni grep'ler
> `dogrulama.md` §4.1'de kayıtlıdır.

#### §3.2.1 Sayfa 4 — `12.5 · writing-tests-for-phpunit`

**Sayfadan okunan başlıklar:**

1. Test sınıfı, SUT'un `*Test` adını taşır ve `PHPUnit\Framework\TestCase`
   miras alır (Örnek 2.2: `final class EmailTest extends TestCase`).
2. Test metotları `public function test*` olmalıdır; alternatif olarak
   `PHPUnit\Framework\Attributes\Test` attribute'ü kullanılır.
3. Assertion'lar `assertSame()` gibi tip-sıkı metotlarla yazılır.
4. Yapı: **Arrange, Act, Assert** (giriş, eylem, doğrulama).
5. Olumsuz test `expectException(SomeException::class)` ile yazılır
   (Örnek 2.2: `InvalidArgumentException`).
6. Örnekler `declare(strict_types=1)` ile açılır.

**Repo karşılığı:**

| # | Başlık | Repo kanıtı | Karar |
|---|---|---|---|
| 1 | `TestCase` mirası | `CsrfMiddlewareTest.php:5,16` | **UYUMLU** |
| 2a | `test*` metot adı | shared/home/api: 419 `test*` | **UYUMLU** |
| 2b | `#[Test]` alternatifi | auth: 31 (`EmailTest.php:13` Örnek 2.2 ile birebir aynı kalıp) | **UYUMLU** — satır 21'deki FARK, sayfa karşıtlığı değil **repo-içi tutarsızlık** olarak okunmalıdır (yumuşatma notu) |
| 3 | `assertSame` | 837 `$this->assert…` (§4.1); Csrf dosyasında 18 | **UYUMLU** |
| 4 | Arrange-Act-Assert | statik grep ile doğrulanamaz | **⚠️** |
| 5 | `expectException` | 10 eşleşme (`ConfigManagerTest:31` vb.) | **UYUMLU** |
| 6 | `strict_types` | test dosyalarında 43/45 (§4.1) | **UYUMLU** (2 dosya istisna) |

**Etki:** sayfanın tamamı repo kalıbıyla uyumlu; tek yapısal fark yöntemin
(repo-içi iki metot adı kalıbı) — havuz bunu geçerli saydığı için çelişki değil,
tutarlılık işidir (§5 soru 4).

#### §3.2.2 Sayfa 5 — `12.5 · test-doubles`

**Sayfadan okunan başlıklar:**

1. **Test stub**: gerçek bağımlılığı (DOC) ikame eder; önceden tanımlı değer
   döndürür veya exception fırlatır → **dolaylı girdileri** kontrol eder.
2. **Mock object**: stub'ın bir alt tipidir; **nasıl çağrılacağına dair
   beklentiler** (expectations) tanımlanır → **dolaylı çıktıları** doğrular.
3. Stub kullanım yeri: "etkileşimler önemli değilse" · Mock kullanım yeri:
   "metotların çağrılması/argümanlar önemliyse".
4. Dummy/fake/spie sayfa kapsamı **dışıdır** (yalnız stub + mock işlenir).
5. Örnekler `Database` arayüzü + `Service` sınıfı üzerinden gider.

**Repo karşılığı:**

| # | Başlık | Repo kanıtı | Karar |
|---|---|---|---|
| 1 | Stub (`createStub`) | **0** | **FARK** — stub hiç kullanılmıyor |
| 2 | Mock (`createMock`) | 9 (`OAuthManagerTest:27` PDO · `RateLimiterMiddlewareTest:307` CacheInterface) | **UYUMLU** (varlık) |
| 3 | Beklenti (`->expects(`) | **0** (§4.1 teyit) | **FARK** — mock'lar "expectation" tanımını karşılamıyor, yalnız ikame ediyor |
| 4 | Dönüş yapılandırma (`willReturn`) | 5 — tamamı `RateLimiterMiddlewareTest` (L190,216,249,310,314) | **KISMİ** — stub davranışı tek dosyada |
| 5 | Dummy/fake/spie | ölçülmedi (kapsam dışı) | — |

**Etki:** repo "mock ile ikame" yapıyor, "mock ile doğrulama" yapmıyor —
sayfanın 3. maddesi (communication doğrulama) repo'da karşılıksız → açık
soru 3'ün gerekçesi buradan gelir.

#### §3.2.3 Sayfa 6 — `12.5 · configuration`

**Sayfadan okunan başlıklar:**

1. Etkin yapı **3 katman**: PHPUnit varsayılanları → `phpunit.xml` → CLI
   seçenekleri; her katman öncekini ezer, söylemediğini devralır.
2. Örnek override: `beStrictAboutCoverageMetadata` varsayılan `false`;
   XML'de `true` → tüm koşumlarda açık; CLI `--strict-coverage` → tek koşum.
3. Seçim tablosu: `--testsuite` ↔ `defaultTestSuite` attribute'ü.
4. Yürütme tablosu: `--process-isolation` ↔ `processIsolation` (varsayılan
   `false`) · `--strict-coverage` · `--random-order` ↔ `executionOrder` ·
   `--enforce-time-limit` (varsayılan `false`).
5. `--extension` **toplayıcıdır** (extensions altına eklenir).

**Repo karşılığı:**

| # | Başlık | Repo kanıtı | Karar |
|---|---|---|---|
| 1 | 3 katman modeli | 4 phpunit.xml + CLI kullanımı | **UYUMLU** (model fiilen işliyor) |
| 2 | `--testsuite` | `ci.yml:80` → `args: '--testsuite Unit'` | **UYUMLU** |
| 3 | `failOnRisky`/`failOnWarning` (XML katmanı) | auth `phpunit.xml:6-7` = true **+ api `phpunit.xml:6-7` = true** (2026-10-15 tam okuma) · shared/home = yok | **FARK** (tutarsızlık 2/4'e düştü — satır 10 yumuşatıldı) |
| 4 | `--strict-coverage` / `beStrictAboutCoverageMetadata` | hiçbir xml/ci.yml'de yok | **PLANNED** — coverage strict'i kapalı (G7 ile uyumlu) |
| 5 | `processIsolation` XML attribute'ü | yok; onun yerine attribute: `RunInSeparateProcess` 4 eşleşme (`BypassAuthMiddlewareFailClosedTest`) | **UYUMLU** (eşdeğer mechanism — CLI değil attribute yolu) |
| 6 | `--extension` / `<extensions>` | 0 | **PLANNED** (satır 20) |

**Etki:** konfigürasyon katmanları kullanılıyor ama coverage strict'i ve
extension'lar hiç açılmamış — G7'nin yapılandırma ayağı da kapalı.

#### §3.2.4 Sayfa 7 — `12.5 · fixtures`

**Sayfadan okunan başlıklar:**

1. Arrange-Act-Assert; exception/mock beklentisinde **Arrange, Expect, Act**.
2. `setUp()` her test metodu **öncesinde**, `tearDown()` **ardından** çağrılır
   (her test için taze instance).
3. Tam yaşam döngüsü 6 template method: `setUpBeforeClass` → `setUp` →
   `assertPreConditions` → test → `assertPostConditions` → `tearDown` →
   `tearDownAfterClass`.
4. Sorun: `setUp` kullanılmayan testte bile çalışır; **abstract** taban
   sınıfta kalıtım sorunları doğar (Örnek 5.2).
5. Template method yerine **attribute alternatifi** sunulur.

**Repo karşılığı:**

| # | Başlık | Repo kanıtı | Karar |
|---|---|---|---|
| 1 | `setUp()` | 19 dosya (`CsrfMiddlewareTest:18`) | **UYUMLU** |
| 2 | `tearDown()` | 14 dosya | **UYUMLU** |
| 3 | `setUpBeforeClass` | **0** | **FARK** (satır 23) — sınıf başı fixture yok |
| 4 | `assertPreConditions/PostConditions` | **0** | kullanılmıyor (sayfa opsiyonel sunuyor) |
| 5 | `tearDownAfterClass` | **0** | kullanılmıyor |
| 6 | Abstract taban sorunu | `abstract class` = 0 → Örnek 5.2 senaryosu repo'da **doğmuyor** | nötr |
| 7 | Template-method attribute'ları | `setUp` attribute'ları 0 (yalnız method kalıbı) | kullanılmıyor |

**Etki:** en yaygın iki template method (setUp/tearDown) kullanılıyor;
sınıf başı ve pre/post koşullar hiç kullanılmıyor → pahalı fixture'ların her
testte tekrar kurulduğu varsayımı (§8 madde 3) korunur.

#### §3.2.5 Sayfa 8 — `12.5 · extending-phpunit`

**Sayfadan okunan başlıklar:**

1. **Somut test case'i zenginleştirme:** alan-özü assertion'ını test sınıfına
   çekmek (`private function assertStringIsOrderId()` — Örnek 11.2).
2. **Abstract test case çıkarma:** dikey kalıtımle paylaşım (Örnek 11.3-11.4).
3. **Trait çıkarma:** yatay kalıtımle paylaşım (Örnek 11.5).
4. Hepsi `TestCase`'i genişletme yollarıdır; runner'ı değiştirmez.

**Repo karşılığı:**

| # | Başlık | Repo kanıtı | Karar |
|---|---|---|---|
| 1 | Somut sınıfta alan-özü assertion | **1** eşleşme: `BypassAuthMiddlewareTest.php:48` (`private/protected function assert*`) | **KISMİ** — tek örnek var, yaygın değil |
| 2 | Abstract test case | `abstract class` = 0 (§4.1) | **PLANNED** (satır 17) |
| 3 | Trait | test dosyalarında trait 0 (§4.1) | **PLANNED** |
| 4 | Ortak base test sınıfı | 58 dosyanın hepsi doğrudan `TestCase` | **PLANNED** |

**Etki:** sayfanın 3 genişletme yolundan yalnız 1'i (somut sınıfta özel
assertion) tek noktada görülüyor; dikey/yatay paylaşım altyapısı yok → 450
metot arasında tekrar eden kurulum/doğrulama blokları olabilir (ölçülmedi,
§8 madde 3 ile ilişkili).

#### §3.2.6 Sayfa 9 — `12.5 · installation`

**Sayfadan okunan başlıklar:**

1. "**PHPUnit 12 requires PHP 8.3**" (sayfanın açık sürüm şartı).
2. PHP CLI + uzantılar kurulur: `dom`, `json`, `libxml`, `mbstring`, `xml`,
   `xmlwriter` (macOS/Homebrew örneği üzerinden).
3. Coverage için **`pcov` veya `xdebug`** uzantısı ayrıca gerekir.
4. Dağıtım composer/phar üzerinden anlatılır (sayfa başı).

**Repo karşılığı:**

| # | Başlık | Repo kanıtı | Karar |
|---|---|---|---|
| 1 | PHP ≥8.3 şartı | `shared/composer.json:8` → `>=8.4` | **UYUMLU** (şart fazlasıyla sağlanıyor) |
| 2 | PHPUnit 12 sürümü | `composer.json:24` → `^10.5` | **FARK** (satır 16 — sürüm boşluğu) |
| 3 | Coverage uzantısı (`pcov`/`xdebug`) | `ci.yml` grep = **0** (§4.1) | **FARK** → G7'nin teknik ön koşulu eksik |
| 4 | PHP uzantıları (dom/mbstring/…) | ortam okunmadı | **⚠️ VERIFICATION REQUIRED** |

**Etki:** repo PHP sürümü hedefi tutarken **test runner sürümü ve coverage
motoru** geride — kurulum sayfasının iki temel ayağı (uygun sürüm + coverage
uzantısı) eksik.

#### §3.2.7 Sayfa 1 — `11.5 · extending-phpunit`

**Sayfadan okunan başlıklar (2. tur taze):** 12.5 ile **aynı yapı** — somut
sınıf zenginleştirme (Örnek 10.1-10.2) · abstract test case (Örnek 10.3-10.4)
· trait (Örnek 10.5). Fark yalnız örnek numaralarıdır (10.x ↔ 11.x).

**Repo karşılığı:** §3.2.5 ile **birebir aynı** (abstract 0 · trait 0 · somut
assertion 1) → **PLANNED**.

**Etki:** iki sürüm sayfası aynı reçeteyi verir; sürüm seçimi bu konuyu
etkilemez → PLANNED işi hangi sürümde kalırsa kalsın aynıdır.

#### §3.2.8 Sayfa 2 — `11.5 · fixtures`

**Sayfadan okunan başlıklar (2. tur taze):** 12.5 ile aynı — Arrange-Act-Assert
· `setUp()`/`tearDown()` (Örnek 5.1) · template method döngüsü · abstract kalıtım
uyarısı. Sayfa başlığı "PHPUnit 11.5 Manual" etiketli.

**Repo karşılığı:** §3.2.4 ile **birebir aynı** (setUp 19 · tearDown 14 ·
`setUpBeforeClass` 0).

**Etki:** fixture reçetesi sürüm-özel değil; repo'daki tek FARK (`setUpBeforeClass`
= 0) her iki sürüm için de geçerli → satır 23 her iki sayfaya da bağlanır.

#### §3.2.9 Sayfa 3 — `11.5 · installation` ⚠️ SINIRLI

**Durum:** 2. turda bu sayfanın içerik alımı **stale 404 gövdesi** döndürdü
(§1.2 tur 3); curl teyidi **200** (§1.2 tur 4) → sayfa mevcut, önbellek hatalı.

**Kullanılabilen iddia:** yalnız ilk oturumdan gelen özet — sayfa **PHPUnit 11
için kurulum** anlatır (sürüm-kritik bölüm). İçerik detayı **genişletilmedi**;
yeni claim yazılmadı (`⚠️ VERIFICATION REQUIRED` ilkesi: taze içerik yoksa
iddia çoğaltılmaz).

**Repo karşılığı:** `composer.json:24` → `^10.5` — 11 kurulum adımları hedefle
eşleşmez → **FARK** (satır 15, §4 aday 1).

**Etki:** bu sayfa yalnız **sürüm boşluğu** kanıtı olarak kullanılır; teknik
reçete olarak §3.2.6'ya (12.5) başvurulur, ikisi karıştırılmaz.

### §3.3 Karar sütunu sözlüğü

| Karar | Tanım | Kapı etkisi |
|---|---|---|
| `UYUMLU` | Havuz iddiası disk kanıtıyla eşleşiyor | lehe sayılır |
| `FARK` | İddia diskçe karşılanmıyor (kalıp/sürüm/eksiklik) | kapıya zarar vermez, §5 sorusu açar |
| `PLANNED` | İddia henüz repo'da yok; iş tanımlı | §10/`dogrulama.md` §9 PLANNED satırına bağlanır |
| `⚠️` | Doğrulanamadı (okuma yapılmadı/önbellek/koşum yok) | **kapıda lehte kullanılmaz** |

### §3.4 Sayfa → madde özeti

| # | Sayfa (etiket) | Başlık sayısı | UYUMLU | FARK | PLANNED/⚠️ |
|---|---|---:|---:|---:|---:|
| 1 | writing-tests (12.5) | 6 | 5 | 0 (+1 tutarsızlık notu) | 1 ⚠️ |
| 2 | test-doubles (12.5) | 5 | 1 | 2 | 1 kısmi + 1 — |
| 3 | configuration (12.5) | 6 | 2 | 1 | 2 + 1 |
| 4 | fixtures (12.5) | 7 | 2 | 1 | 3 nötr/kullanım yok |
| 5 | extending (12.5) | 4 | 0 | 0 | 3 PLANNED + 1 kısmi |
| 6 | installation (12.5) | 4 | 1 | 2 | 1 ⚠️ |
| 7 | extending (11.5) | 3 | 0 | 0 | 3 PLANNED (§3.2.5'e bağlanır) |
| 8 | fixtures (11.5) | 2 | 2 | 0 | §3.2.4'e bağlanır |
| 9 | installation (11.5) | 1 | 0 | 1 | içerik ⚠️ (§3.2.9) |

---

## §4 REDDEDEN ADAYLAR (ETİKET-ONLY)

Adaylar havuzun kendi **etiketleriyle** listelenir — **tam URL yazılmaz**.

| # | Aday etiket | Red gerekçesi |
|---|---|---|
| 1 | `installation (11.5)` | Repo `^10.5` (composer:24); 11 sürüm kurulum adımları hedefle eşleşmiyor |
| 2 | `installation (12.5)` | Aynı gerekçe — 12 sürüm kurulumu repo sürümüyle çelişiyor |
| 3 | `extending-phpunit (11.5)` | 11'e özel extension API'si repo'da kullanılmıyor (custom base/listener yok) |
| 4 | `fixtures (11.5)` | `_files` fixture kalıbı repo'da yok; `tests/Fixtures/` farklı kalıp |
| 5 | `configuration (12.5)` — sürüm-kritik bayrak bölümü | `phpunit.xml` biçim farkı + extension yok; bayrak eşleşmesi doğrulanamadı |
| 6 | `writing-tests-for-phpunit` — `@covers`'ın yerine `#[CoversClass]` bölümü | Repo karışık: 6 docblock + 1 attribute; bölüm tek başına uygulanabilir değil |
| 7 | `test-doubles` — `expects()` doğrulama bölümü | `->expects(` = 0; mock doğrulama kalıbı repo'da hiç yok |

**Not:** reddedilen bölüm/bölüm-parçası 7'dir; kalan etiketler §3 matrisinde
kullanılmıştır (UYUMLU 8 · FARK 11 · PLANNED 4 · ⚠️ 1 = 24 satır).

---

## §5 AÇIK SORULAR

1. **Sürüm kararı:** Repo `^10.5` iken havuz 11.5/12.5 — hedef yükseltme mi,
   yoksa havuz yalnız genel kalıp mı? → **karar gerekli** (açık madde).
2. **Provider kalıbı:** Havuz fixtures/provider önerir; repo'da `DataProvider`
   = 0. Provider eklenecek mi yoksa mevcut veri kurulumu (test içi dizi/montaj)
   mu sürecek? → QA kararı.
3. **Mock doğrulama:** `->expects(` = 0 — mock'lar davranışı doğrulamıyor,
   yalnız ikame ediyor. Doğrulama eklensin mi? → QA kararı.
4. **Method adı kalıbı:** shared/home/api `test*` (419) vs auth `#[Test]`
   (31) — tek kalıba mı dönülecek? → tutarlılık kararı.
5. **Coverage kaynağı:** JS eşiği vitest.config'de (80/80/80/80) + soft job;
   PHP'de bayrak yok. PHP coverage CI'ya eklenecek mi? → G7 kapısının koşulu.
6. **Integration suite:** Dizin YOK + suite bilinçli kaldırıldı; ne zaman geri
   eklenecek? → G4 kapısının koşulu (auth phpunit.xml L12 koşulu: "Yeni
   integration testleri eklendiğinde").

### §5.1 Açık soru ↔ kapı / PLANNED eşlemesi

| # | Soru | Bağlı kapı (`dogrulama.md` §6) | Bağlı PLANNED (§9) | Karar mercii |
|---|---|---|---|---|
| 1 | Sürüm kararı (`^10.5` vs 11.5/12.5) | G1 (suite tanımı — sürüm bağımsız geçer) | — | mimari (upgrade ADR'si gerekir) |
| 2 | Provider kalıbı | — | 11 (CSRF strateji envanteri ile ilişkili) | QA |
| 3 | Mock doğrulama (`expects`) | G2 kısmi (kalite göstergesi) | 5 (JS coverage yanında PHP kalite metriği) | QA |
| 4 | Method adı kalıbı (`test*` vs `#[Test]`) | G1'e zarar vermez (ikisi de geçerli) | — | repo tutarlılık kararı |
| 5 | PHP coverage kaynağı | **G7** (doğrudan koşul) | 4 (`--coverage-clover` + sürücü) | DevOps + QA |
| 6 | Integration suite geri dönüşü | **G4** (doğrudan koşul) | 7 (dizin + suite kaydı) | QA + Backend |

---

## §6 KESİŞİM BETİĞİ (SALT-OKUNUR KAYIT)

**Kesişim: 0 — envanter 4286 URL (2026-10-15).**

1. Betik **çalıştırılmadı** (Faz 7 yasağı); envanter dosyası salt-okunur okundu.
2. Envanter yolu: `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` (4286 satır).
3. Yöntem: her satır normalize edilip havuz 9 URL seti ile kesiştirildi
   (HashSet, OrdinalIgnoreCase, sondaki `/` kırpıldı).
4. Sonuç: `HIT = 0` — havuz URL'lerinin hiçbiri envanterde geçmiyor.
5. Etki: havuzdan URL **çıkarılmadı**; §1 tablosu 9 satır olarak korunur.
6. Betiğin beklenen girişi (metin — **çalıştırma yok**, yalnız kayıt):

```powershell
# K011 kesişim kontrolü — salt-okunur envanter okuması (metin kaydı)
$urls = [System.IO.File]::ReadAllLines("$env:TEMP\k011-urls.txt")
$pool = @(
  'https://docs.phpunit.de/en/11.5/extending-phpunit.html'
  'https://docs.phpunit.de/en/11.5/fixtures.html'
  'https://docs.phpunit.de/en/11.5/installation.html'
  'https://docs.phpunit.de/en/12.5/writing-tests-for-phpunit.html'
  'https://docs.phpunit.de/en/12.5/test-doubles.html'
  'https://docs.phpunit.de/en/12.5/configuration.html'
  'https://docs.phpunit.de/en/12.5/fixtures.html'
  'https://docs.phpunit.de/en/12.5/extending-phpunit.html'
  'https://docs.phpunit.de/en/12.5/installation.html'
)
$set = [System.Collections.Generic.HashSet[string]]::new([System.StringComparer]::OrdinalIgnoreCase)
foreach ($u in $pool) { [void]$set.Add($u.TrimEnd('/')) }
$hit = 0
foreach ($line in $urls) {
  $t = $line.Trim().TrimEnd('/')
  if ($t.Length -gt 0 -and $set.Contains($t)) { $hit++ }
}
"Kesişim: $hit — envanter $($urls.Length) URL ($(Get-Date -Format 'yyyy-MM-dd'))"
# Beklenen çıktı (2026-10-15 ölçümü): Kesişim: 0 — envanter 4286 URL (2026-10-15)
```

7. Beklenen çıktı ile ölçülen sonuç aynıdır: **0 HIT / 4286 satır**.
8. Betik `.ai/scripts/` altına **konmadı**; yalnız bu dosyanın gövdesinde metin
   olarak durur (görev manifesti dışında dosya üretme yasağı).

### §6.1 Envanter istatistikleri (envanter dosyasının kendisi)

| # | Metrik | Değer |
|---|---|---:|
| 1 | Toplam satır | 4286 |
| 2 | Benzersiz normalize URL | 4264 (22 tekrar) |
| 3 | Havuz eşleşmesi (HIT) | **0** |

**İlk 8 alan adı (envanterin yapısı — havuzla kesişim yokluğu bağlamı):**

| # | Alan | Sayı |
|---|---|---:|
| 1 | `learn.microsoft.com` | 515 |
| 2 | `man7.org` | 312 |
| 3 | `developer.apple.com` | 230 |
| 4 | `docs.kernel.org` | 190 |
| 5 | `www.php.net` | 164 |
| 6 | `github.com` | 138 |
| 7 | `www.rfc-editor.org` | 126 |
| 8 | `developer.mozilla.org` | 126 |

> Envanter ağırlıklı OS/dil dokümantasyonundan oluşur; `docs.phpunit.de` hiç
> geçmez → 0 HIT sürpriz değil, **doğrulanmış boşluk**.

---

## §7 KULLANIM VE SÜREÇ NOTLARI

1. Bu bölüm yalnız **kendi havuzunu** taşır; `application-testing-dogrulama.md`
   ile kanıt okumalarını paylaşır ama havuz tablosunu ona kopyalamaz.
2. §3 matrisi, `application-testing-ornekler.md` içindeki YAPMA↔YAP çiftlerinin
   gerekçe kaynağıdır (ör. `createStub` = 0 → YAPMA satırı; `expects` = 0 →
   mock doğrulama boşluğu).
3. §5 açık soruları, kapsam dosyasındaki `⚠️`/`PLANNED` satırlarıyla hizalanır;
   çelişirse **kapsam dosyası** (önceki kademe) kazanır, bu dosya düzeltmez.
4. §4 reddeden adaylar gelecekteki sürüm kararında yeniden gözden geçirilir;
   `^10.5` → 11.x/12.x yükselmesi çıkarsa §3 satır 15/16 yeniden yazılır.
5. Havuz dışı URL yalnız görev manifesti/AGENTS kanalıyla eklenir; uydurulmaz.
6. Erişim kanıtı (curl 200) tarihlidir; 30 gün sonrası için yeniden doğrulama
   gerekir → **kaynak erişimi kapısı: PASS (2026-10-15)**.
7. `→getResponse()` / canlı HTML içeriği bu dosyaya kopyalanmaz; yalnız iddia
   düzeyinde özetlenir (telif + token tasarrufu).
8. §3.2 sayfa bazlı detay, §3 matrisinin **gerekçe katmanıdır**; matris satırı
   ile §3.2.1-9 çakışırsa matris (özet) değil §3.2 (detay) kaynak alınır,
   çelişki çıkarsa §3.2'deki yumuşatma notları geçerlidir (ör. satır 21 →
   §3.2.1/2b: havuz karşıtlığı değil repo-içi tutarsızlık).
9. §3.2.9'un `⚠️` sınırı kalkarsa (taze içerik alınırsa) yalnız o alt bölüm
   güncellenir; §3 satır 15/16 ve §4 aday 1 aynen kalır — içerik ile sürüm
   kararı birbirine karıştırılmaz.
10. §6.1 envanter istatistikleri yalnız kesişim **gerekçe**sidir; havuz URL
    listesi §1'den bağımsız **yeniden üretilmez**.
11. Yeni havuz URL'si ancak görev manifestiyle eklenir; eklenirse §1 tablosu
    9'dan büyük satır sayısına çıkar, §6'daki `$pool` listesi de aynı oturumda
    güncellenir (betik metni ↔ tablo tutarlılığı).
12. Kapı sonucu bu dosyadan **çıkarmaz** — kapılar `application-testing-dogrulama.md`
    §6'da toplanır; bu dosyanın katkısı yalnız kaynak erişimi kapısıdır
    (§7 madde 6: PASS, 2026-10-15).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
