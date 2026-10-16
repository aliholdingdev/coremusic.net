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
| 10 | 12.5 configuration | `failOnRisky`/`failOnWarning` bayrakları | `auth.coremusic.net/phpunit.xml:6-7` → `true`/`true` · `shared/phpunit.xml` → yok | **FARK** (tutarlı değil: yalnız auth) |
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

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
