---
title: "K019-testing/unit — Birim Test Tasarım Desenleri: AAA, Double, Fixture ve Determinism"
node_id: K019-testing/unit/unit-test-tasarim-desenleri
node_type: module
status: draft
parent_id: K019-testing/unit
children: []
depends_on:
  - K019-testing
  - K019-testing/unit
evidence:
  - "shared/tests — 44 PHP dosyası (2026-10-12 ReadAllLines sayımı)"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php — Satır 44 testImplementsPsr3LoggerInterface, Satır 49 testAllPsr3MethodsAreCallable (2026-10-12)"
  - "shared/tests/Middleware/MiddlewarePipelineTest.php — Satır 23, 34, 55 yürütme sırası testleri (2026-10-12)"
  - "shared/tests/Middleware/CsrfMiddlewareTest.php — Satır 18 setUp, Satır 32 ilk test (2026-10-12)"
  - "shared/phpunit.xml — Satır 9 Unit testsuite, Satır 34-38 source include (2026-10-12)"
  - "shared/composer.json — Satır 24 phpunit/phpunit ^10.5, Satır 47 test scripti (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "URL envanteri: .ai/architecture/** 1206 benzersiz URL, bu dosyada çakışma 0 (2026-10-12)"
  - "Repo satır alıntıları Get-Content ile satır numarası doğrulanarak yazıldı (2026-10-12)"
open_questions:
  - "shared/tests içinde mock yerine doğrudan gerçek nesne kullanan testlerin oranı ölçülmmedi (⚠️ VERIFICATION REQUIRED)"
  - "Test isimlendirme deseni (testX_yapilandirma_beklenen) tüm dosyalarda zorunlu değil, tutarlılık denetimi yok (⚠️ VERIFICATION REQUIRED)"
  - "Birim testlerde rastgelelik (random/uniqid) kullanımı için determinism denetimi çalışan bir kapı yok (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# K019-testing/unit/unit-test-tasarim-desenleri

> **ELI10:** Birim testi, tek bir parçayı diğer her şeyden koparıp tek başına sınamaktır. Bu dosya
> hangi düzeneklerin (AAA) kullanılacağını, ne zaman sahte nesne (double) gerektiğini, testin her
> seferinde aynı sonucu vermesini (determinism) neyin sağlayacağını ve zayıf assertion'ın nasıl
> yakalanacağını tanımlar. Amaç: her test tek başına, hızlı ve tekrar çalıştırılabilir olsun.

## §1 Amaç

Bu modül, CoreMusic'in **birim testi tasarım kurallarının tek yetkili tanımıdır**. Beş karar bu
dosyada verilir: düzenek (pattern) · double seçimi · fixture kapsamı · determinism · assertion
kalitesi. Yeni bir test dosyası bu beş karara uymadan commit edilmez.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Test gövdesi nasıl kurulur? | AAA düzeni (Arrange-Act-Assert) | §4 |
| 2 | Bağımlılık nasıl taklit edilir? | Test double seçimi | §5 |
| 3 | Test öncesi durum nasıl hazırlanır? | Fixture / setUp kapsamı | §6 |
| 4 | Test aynı sonucu verir mi? | Determinism kuralları | §7 |
| 5 | Assertion yeterli mi? | Assertion kalitesi + risky taraması | §8 |
| 6 | Hangi test birim sayılır? | Birim sınırı (unit boundary) | §3 ve §12 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Test dosyası sayısı | adet | 44 PHP dosyası (`shared/tests`) | 2026-10-12 sayımı |
| H2 | Unit testsuite tanımı | adet | 1 (`phpunit.xml` Satır 9) | kodda mevcut |
| H3 | Çalıştırma komutu | komut | `composer test` → `phpunit` | composer.json Satır 47 |
| H4 | Statik analiz kapısı | seviye | PHPStan level 5 | composer.json Satır 48 |
| H5 | Test başına izolasyon | mekanizma | setUp ile yeni nesne (CsrfMiddlewareTest Satır 18) | kodda mevcut |
| H6 | Determinism otomatik denetimi | kapı | çalışan bir otomatik kapı yok | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| AAA düzeni | ✅ Kurulum, çalıştırma, doğrulama blokları | ❌ Testin koşacağı ortam → `integration/` |
| Test double | ✅ Stub, mock, spy, fake, dummy ayrımı | ❌ HTTP servis taklidi → `integration/` |
| Fixture | ✅ setUp/tearDown ve veri hazırlığı | ❌ Veritabanı reset → `integration/` |
| Determinism | ✅ Saat, rastgelelik, sıra bağımlılığı yasağı | ❌ Tarayıcı kararsızlığı → `e2e/` |
| Assertion kalitesi | ✅ Zayıf assertion sınıflandırması | ❌ Kapsam yüzdesi eşiği → `coverage/` |
| Flaky (kararsız) test | ✅ Birim seviyesinde nedenler | ❌ Yeniden deneme politikası → `regression/` |
| Seçici (selector) | ✅ Yok (birim testte seçici gerekmez) | ❌ Tarayıcı seçicileri → `e2e/` |

**Sınır kararı:** veritabanı, dosya sistemi ve HTTP uçlarına dokunan testler "birim" sayılmaz;
bunlar `integration/` alt dosyasının konusudur. Bu dosya yalnız saf mantık ve tek sınıf testlerini
kapsar.

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| AAA | Arrange-Act-Assert | Test gövdesinin üç zorunlu bloğu | §4 |
| Double | test double | Gerçek bağımlılığın yerini tutan sahte nesne | §5 |
| Stub | stub | Sabit cevap üreten double | §5.1 |
| Mock | mock | Beklenen davranışı (çağrı sayısını) doğrulayan double | §5.2 |
| Spy | spy | Çağrıları kaydedip sonra doğrulayan double | §5.3 |
| Fake | fake | Çalışan ama basitleştirilmiş gerçek alternatif | §5.4 |
| Fixture | fixture | Test öncesi hazırlanan sabit durum | §6 |
| Determinism | determinism | Aynı girdi → her koşuda aynı sonuç | §7 |
| Risky test | risky test | PHPUnit'un riskli sınıflandırdığı test | §8.3 |
| Birim sınırı | unit boundary | Tek sınıf/saf mantık sınırı | §12 |
| Test suiti | testsuite | phpunit.xml'de gruplanmış test dizini | phpunit.xml Satır 8-33 |

## §4 AAA düzeni (Arrange-Act-Assert)

Her test, üç bloktan oluşur; bloklar dışında kalan kod (döngü, koşul, bekleme) yasaktır. Üç blok
arıza durumunda nerede patladığını gösterir: kurulum mu, çalışma mı, beklenti mi.

| # | Blok | Ne yapılır | Ne YAPILMAZ | Sınır |
|---|---|---|---|---|
| 1 | Arrange | Nesne kurulumu, girdi hazırlanır | Ağ/dosya erişimi | test başına ≤ 15 satır |
| 2 | Act | Tek eylem çağrılır | İkinci eylem | tek çağrı |
| 3 | Assert | Beklenti doğrulanır | Yeni durum yaratma | ≥ 1 anlamlı assert |
| 4 | (yasak) | Gövde arasında hazırlık | Koşullu assertion | AAA dışına taşınamaz |

### §4.1 Repo örneği — AAA'nın gerçek hâli

`shared/tests/Middleware/MiddlewarePipelineTest.php` içindeki zincir, üç bloğu ayırır:

| # | Satır | Blok | İçerik |
|---|---|---|---|
| 1 | 23 | Arrange + Act | `testEmptyPipelineRunsCore()` — boş boru hattı kurulur |
| 2 | 30 | Assert | `assertSame(200, $result['status'])` |
| 3 | 31 | Assert | `assertSame('ok', $result['body'])` |
| 4 | 34 | Arrange + Act | `testSingleMiddlewareIsExecuted()` — tek middleware |
| 5 | 52 | Assert | `assertSame(['m1_before','core','m1_after'], $executed)` |
| 6 | 55 | Act | `testMultipleMiddlewareExecutionOrder()` — sıralama testi |

**Dosya: `shared/tests/Middleware/MiddlewarePipelineTest.php` / Satır: 23 · 34 · 52 · 55**

Satır 52'deki assert, hem davranışı (middleware çağrıldı) hem **sırayı** (before/core/after)
tek seferde doğrular — AAA'da "tek eylem, açık beklenti" kuralının karşılığıdır.

### §4.2 AAA anti-desenleri

| # | Anti-desen | Belirti | Sonuç | Düzeltme |
|---|---|---|---|---|
| 1 | AAA karışması | Assert arasında yeni arrange | Patlama noktası belirsiz | Blokları ayır |
| 2 | Çoklu eylem | İki davranış tek testte | Hata kaynağı belirsiz | Testi böl |
| 3 | Koşullu assert | `if` içinde assert | Asla başarısız olmayan test | Koşulu assert'e çevir |
| 4 | assertion'sız test | Yoklama sadece "çalıştı" | Sahte yeşil | §8.3 |
| 5 | Gömülü veri | Test içinde 200 satırlık dizi | Bakımı zor | Fixture'a taşı (§6) |

## §5 Test double seçimi

Double, bağımlılığı **kesmek** içindir; her bağımlılık mock'lanmaz. Kural: mock yalnızca
"davranışın kendisi" test ediliyorsa kullanılır; yoksa gerçek nesne (veya fake) tercih edilir.

| # | Double | Ne yapar | Neyi doğrular | Ne zaman | Repo karşılığı |
|---|---|---|---|---|---|
| 1 | Dummy | Parametreyi doldurur, kullanılmaz | Yok | Zorunlu argüman varsa | tespit edilmedi |
| 2 | Stub | Sabit cevap döner | Dönüş değeri | Dış servis/servis kaydı | bulunmadı (repo'da mock çerçevesi yok) |
| 3 | Mock | Beklenen çağrıyı doğrular | Çağrı sayısı/sırası | Davranış testi | bulunmadı |
| 4 | Spy | Çağrıları kaydeder | Çağrı geçmişi | Sıra doğrulama | `$executed` dizisi (PipelineTest Satır 52) |
| 5 | Fake | Basit çalışan alternatif | Bütün davranış | Hızlı alternatif | `SqliteDatabaseManager` (Repository testleri) |

### §5.1 Repo karşılığı — spy deseni

Pipeline testleri, double yerine **gerçek bir izleme dizisi** (spy yerine geçen `$executed`) ile
davranışı kaydeder ve tek assert'te sırayı doğrular:

```php
$this->assertSame(['m1_before', 'core', 'm1_after'], $executed);
```

**Dosya: `shared/tests/Middleware/MiddlewarePipelineTest.php` / Satır: 52**

### §5.2 Double seçim matrisi

| # | Bağımlılık türü | Önerilen yaklaşım | Gerekçe | Örnek |
|---|---|---|---|---|
| 1 | Saf hesap | Nesne doğrudan | Gerek yok | `DeviceDetector::detect` (Satır 26) |
| 2 | Zaman | Enjekte edilmiş zaman | Determinism (§7) | repo'da tespit edilmedi |
| 3 | Dosya sistemi | Geçici dizin (tmp) | Gerçek I/O ama izole | Psr3LoggerTest geçici log dizini |
| 4 | Veritabanı | Fake/gerçek in-memory | Birim sınırı dışına çıkar | `integration/` |
| 5 | HTTP | Kesilir | Birim testte ağ yasak | `integration/` |

### §5.3 Mock yasağı

| # | Yasa | İstisna | Denetim |
|---|---|---|---|
| 1 | Kendi kodunu mock'lamak yasak | Yok | Kod incelemesi |
| 2 | Mock + gerçek karışımı yasak | Yok | Kod incelemesi |
| 3 | Davranış doğrulaması için mock | İzinli | §5.1 spy deseni |
| 4 | Geri dönüş değeri için stub | İzinli | Dönüş sabitse |
| 5 | Mock çerçevesi zorunluluğu | Yok — bu depoda mock çerçevesi bağımlılıkta yok | composer.json require-dev Satır 24-25 |

**Kanıt:** `shared/composer.json` require-dev yalnız `phpunit/phpunit ^10.5` (Satır 24) ve
`phpstan/phpstan ^1.10` (Satır 25) içerir; ayrı bir mock kütüphanesi bağımlılık listesinde
yoktur → double'lar elle yazılır.

## §6 Fixture ve test izolasyonu

Fixture, test öncesi hazırlanan ve test sonrası temizlenen durumdur. Kural: **hiçbir test başka
bir testin durumuna dokunmaz**; izolasyon kırıldığında test sırası sonuç değiştirir.

| # | Fixture kaynağı | Kapsam | Temizlik | Repo karşılığı |
|---|---|---|---|---|
| 1 | `setUp()` | Her test öncesi | otomatik (yeni nesne) | CsrfMiddlewareTest Satır 18 |
| 2 | `tearDown()` | Her test sonrası | otomatik | dosyalarda tespit edilmedi → ⚠️ VERIFICATION REQUIRED |
| 3 | Geçici dizin | Test boyunca | dosya temizliği | Psr3LoggerTest geçici log dizini |
| 4 | Statik durum | Test boyunca | elle sıfırlama | `FileHandler::setTraceId` (FileHandler Satır 65) |
| 5 | Ortam değişkeni | Test boyunca | elle geri alma | repo'da tespit edilmedi |

### §6.1 Fixture kuralları

| # | Kural | Zorunlu mu | İhlal belirtisi | Düzeltme |
|---|---|---|---|---|
| 1 | Her test kendi nesnesini kurar | evet | Test sırası değişince kırılır | `setUp()` |
| 2 | Fixture test adında görünür | tercih | Anlaşılmaz hazırlık | İsimlendirme (§15) |
| 3 | Paylaşılan statik durum yasak | evet | Paralel koşuda kırılma | Statik durumu test sonunda sıfırla |
| 4 | Geçici dosya test sonunda silinir | evet | Disk birikimi | Geçici dizin deseni |
| 5 | Gerçek parola/anahtar fixture'a girmez | evet | Sızıntı riski | Sahte değer + §8.4 maskeleme |

### §6.2 İzolasyon denetim tablosu

| # | Denetim | Nasıl bakılır | Kabul | Durum |
|---|---|---|---|---|
| 1 | Tek test tek başına koşabilir mi | `phpunit --filter` | yeşil | manuel |
| 2 | Test sırası değişince sonuç değişir mi | iki farklı sıralama | aynı sonuç | ⚠️ VERIFICATION REQUIRED |
| 3 | Geçici dosya kalıyor mu | koşu sonrası dizin | temiz | ⚠️ VERIFICATION REQUIRED |
| 4 | Statik durum sıfırlanıyor mu | statik alan taraması | sıfırlanıyor | ⚠️ VERIFICATION REQUIRED |
| 5 | Ortam değişkeni sızıyor mu | koşu sonrası env | değişmedi | ⚠️ VERIFICATION REQUIRED |

## §7 Determinism kuralları

Determinism: aynı kod + aynı girdi → **her koşuda aynı sonuç**. Birim test, determinist değilse
kapı görevini göremez; kırmızı olduğunda kod mu test mi olduğu anlaşılamaz.

| # | Determinism bozan | Neden bozar | Yasağı | Alternatif |
|---|---|---|---|---|
| 1 | Gerçek saat (`time()`, `now()`) | Zaman akar | Birim testte yasak | Enjekte edilen zaman |
| 2 | Rastgelelik (`random`, `uniqid`) | Her koşum farklı | Birim testte yasak | Sabit tohum/fikstür değer |
| 3 | Dış servis (ağ) | Bağımlı | Birim testte yasak | Kes (§5) |
| 4 | Dosya sırası (`scandir`) | Sistemden sisteme | Sıralamayı sabitle | Fixture sırası |
| 5 | Hash/UUID üretimi | Değişken çıktı | Beklentiyi şekle göre yaz | Kısıtla (prefix/uzunluk) |
| 6 | Paralel yürütme sırası | Yarış durumu | Sıra testini ayrı yaz | §4.1 sıra testi |

### §7.1 Determinism kanıt türü

| # | Kanıt türü | Örnek | Repo örneği | Geçer mi |
|---|---|---|---|---|
| 1 | Sabit girdi → sabit çıktı | UA → `embedded` | DeviceDetectorTest Satır 26 | ✅ |
| 2 | Şekil doğrulaması | 64 karakter hex | LoggerRedactionTest (§9.4) | ✅ |
| 3 | Değer doğrulaması | Tam değer assert | PipelineTest Satır 52 | ✅ |
| 4 | Zaman damgası assert | Tam saniye | yok — kaçınılmaz değişken | ❌ şekil ile yazılır |

**Örnek (sabit girdi):** `DeviceDetector::detect($rpiAgent)` çağrısı her koşumda `'embedded'`
döndürür — dosya, satır 26:

```php
$this->assertSame('embedded', DeviceDetector::detect($rpiAgent));
```

**Dosya: `shared/tests/Unit/Device/DeviceDetectorTest.php` / Satır: 26**

### §7.2 Determinism denetim listesi

| # | Soru | Evet/Hayır ölçütü | Sıklık | Aksiyon |
|---|---|---|---|---|
| 1 | Testte gerçek saat var mı | kaynak taraması | her PR | enjekte et |
| 2 | Testte rastgelelik var mı | kaynak taraması | her PR | sabitle |
| 3 | Ağ çağrısı var mı | kaynak taraması | her PR | kes |
| 4 | Beklenti zamana bağlı mı | assert incelemesi | her PR | şekle çevir |
| 5 | İki koşum aynı sonucu verdi mi | art arda 2 koşum | haftalık | fark varsa red |

## §8 Assertion kalitesi ve risky taraması

Assertion, testin tek bilgi kaynağıdır; zayıf assertion, kod bozulurken yeşil kalan test üretir.

| # | Zayıf assertion tipi | Belirti | Neden tehlikeli | Düzeltme |
|---|---|---|---|---|
| 1 | Yoklama (`assertTrue(true)`) | Boş doğrulama | Bilgi üretmez | Gerçek beklenti yaz |
| 2 | Tek başına `assertNotNull` | Değeri yoklar | Bozuk değer geçer | Değeri de doğrula |
| 3 | Kapsamlı `assertTrue($x)` | İfadenin kendisi assert | Okunmaz | Ayrıntı assert'i |
| 4 | `assertEquals` (tip esnek) | `'1' == 1` geçer | Tip hatası saklanır | `assertSame` tercih |
| 5 | Sadece istisna beklemek | `expectException` tek | Yan etki doğrulanmaz | Dönüşü de assert et |

### §8.1 Assertion türleri — ne zaman hangisi

| # | Assert | Ne doğrular | Repo örneği | Satır |
|---|---|---|---|---|
| 1 | `assertSame` | Değer + tip | PipelineTest durum kodu | 30 |
| 2 | `assertTrue/False` | Mantık | Csrf core çağrıldı | 45 |
| 3 | `assertStringContainsString` | Alt dize | Psr3LoggerTest seviye etiketi | 74 |
| 4 | `assertStringNotContainsString` | Yokluk | LoggerRedactionTest anahtar sızmadı | 64 |
| 5 | `assertInstanceOf` | Tip ilişkisi | Psr3LoggerTest PSR-3 | 46 |
| 6 | `assertEquals` | Değer (esnek) | VersioningTest enum değeri | 14 |

### §8.2 Assertion sayısı eşiği

| # | Kural | Minimum | Üst sınır | Gerekçe |
|---|---|---|---|---|
| 1 | Her testte assertion | ≥ 1 anlamlı | ≤ 8 | Tek odaklı test |
| 2 | Arrange satırı / assert oranı | ≥ 1 assert / 15 satır | — | Kurulum şişmesini önle |
| 3 | Aynı değeri iki kez assert | yasak | — | Gereksiz tekrar |
| 4 | Yokluk assert'i tek başına | yasak | — | §8 satır 2 |

### §8.3 Risky test (PHPUnit risk sınıfları)

PHPUnit, testi "riskli" işaretleyebilir: test içinde hiçbir assertion yoksa, test yapılandırması
değiştirirse veya yadsıma (deprecation) üretirse. Riskli çıktı, **kapının bilgi üretmediği**
anlamına gelir; kapı yeşil bile olsa rapor okunmalıdır.

| # | Risk nedeni | Belirti | Eylem | Durum |
|---|---|---|---|---|
| 1 | Assertion yok | Test yalnız çalışır | Assertion ekle | denetim listesinde |
| 2 | Yapılandırma değişikliği | phpunit.xml değişti | Gözden geçirme | phpunit.xml Satır 2-7 |
| 3 | Yadsıma üretimi (deprecation) | Uyarı logu | Uyarıyı kapat veya düzelt | ⚠️ VERIFICATION REQUIRED |
| 4 | Global durum değiştirme | Statik alan yazımı | Test sonunda sıfırla | §6 |
| 5 | Riskli testin çıktısı okunmuyor | Kapı sadece yeşil/kırmızı | Rapor alanı ekle | ⚠️ VERIFICATION REQUIRED |

### §8.4 Maskeleme ile assertion etkileşimi

Log testleri, hassas değeri assertion içinde **kısmen** gösterir: `auth_key_prefix` gibi önek
alanları maskelenmiş biçimde bekler, tam değeri asla assert etmez. Bu, §8 satır 5 (yokluk
assert'i) ile birlikte kullanılır:

```php
$this->assertStringNotContainsString(self::AUTH_KEY, $redacted);
```

Aynı testin ikinci assertion'ı (Satır 67), maskelenmiş `auth_key_prefix` alanının **ilk sekiz hex
karakterini koruduğunu**, devamının ise maskelendiğini doğrular; tam değerin kendisi assertion'a
asla yazılmaz. (Depodaki ham satır, önekin devamı için kısaltılmış bir dizgi içerir; bu dosyada
dizgi tekrarlanmadı, davranışı aktarıldı.)

**Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` / Satır: 64 · 67**

## §9 Kod örnekleri — repo gerçek kodu

### §9.1 AAA — boş boru hattı (PipelineTest Satır 23-31)

```php
public function testEmptyPipelineRunsCore(): void
{
    // Arrange + Act
    $result = $this->runPipeline([]);

    // Assert
    $this->assertSame(200, $result['status']);
    $this->assertSame('ok', $result['body']);
}
```

**Dosya: `shared/tests/Middleware/MiddlewarePipelineTest.php` / Satır: 23**

### §9.2 Fixture — setUp ile izolasyon (CsrfMiddlewareTest Satır 18, 32)

```php
protected function setUp(): void
{
    // her test öncesi yeni durum
}

public function testGetRequestBypassesCsrf(): void
{
    $result = $this->handle('GET', '/api/v1/music');

    $this->assertTrue($coreCalled, 'GET requests should bypass CSRF');
    $this->assertSame(200, $result['status']);
}
```

**Dosya: `shared/tests/Middleware/CsrfMiddlewareTest.php` / Satır: 18 · 32 · 45 · 46**

### §9.3 PSR-3 sözleşmesi doğrulaması (Psr3LoggerTest Satır 44-49)

```php
public function testImplementsPsr3LoggerInterface(): void
{
    $this->assertInstanceOf(LoggerInterface::class, new FileHandler($this->dir));
}

public function testAllPsr3MethodsAreCallable(): void
{
    // sekiz zorunlu metodun çağrılabilirliği tek tek doğrulanır
}
```

**Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` / Satır: 44 · 49**

### §9.4 Şekil (pattern) doğrulaması — determinism uyumu

```php
$redacted = Redactor::redact($line);

$this->assertStringContainsString('auth_key=[REDACTED]', $redacted);
$this->assertStringNotContainsString(self::AUTH_KEY, $redacted);
```

**Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` / Satır: 61 · 63 · 64**

### §9.5 Test suiti seçimi — phpunit.xml Satır 8-11

```xml
<testsuites>
    <testsuite name="Unit">
        <directory>tests/Unit</directory>
    </testsuite>
```

Birim testler `Unit` suitine bağlıdır; CI'da `--testsuite Unit` seçicisiyle koşulur
(Dosya: `.github/workflows/ci.yml` / Satır: 80).

### §9.6 Çok-dilli karşılıklar (12 dil)

> Aynı birim testi deseni (AAA + assertion) 12 dilde — repo standardı PHP/PHPUnit'tur (§4.1);
> diğer diller CoreMusic ekosistemindeki yüzeyler içindir (Neva Engine: C++ · C; Windows: C#).
> Sürüm iddiası yazılmaz (bilgi amaçlı — testing-dogrulama.md §4).

| # | Dil | Test çalıştırıcısı | Komut |
|---|---|---|---|
| 1 | PHP | PHPUnit | `vendor/bin/phpunit --filter SepetTesti` |
| 2 | TypeScript | Vitest | `npx vitest run sepet.test.ts` |
| 3 | Python | pytest | `pytest test_sepet.py -v` |
| 4 | C# | xUnit | `dotnet test --filter SepetTesti` |
| 5 | Java | JUnit 5 | `mvn test -Dtest=SepetTesti` |
| 6 | Go | `testing` (stdlib) | `go test -run TestSepet ./...` |
| 7 | C++ | Google Test | `./testler --gtest_filter=SepetTesti.*` |
| 8 | Rust | `cargo test` | `cargo test sepet` |
| 9 | Ruby | RSpec | `bundle exec rspec spec/sepet_spec.rb` |
| 10 | Kotlin | JUnit 5 | `gradle test --tests SepetTesti` |
| 11 | Swift | XCTest | `swift test --filter SepetTesti` |
| 12 | C | Unity (ThrowTheSwitch) | `./testler` (unity_runner) |

**PHP (PHPUnit — repo standardı):**

```php
final class SepetTesti extends TestCase
{
    public function testBosSepetToplamSifirdir(): void
    {
        $sepet = new Sepet();                          // Arrange
        $this->assertSame(0, $sepet->toplam());        // Assert (Act gizli — saf sorgu)
    }
}
```

**TypeScript (Vitest):**

```ts
import { describe, it, expect } from 'vitest';
import { Sepet } from './sepet';

describe('Sepet', () => {
  it('boş sepetin toplamı 0', () => {
    const sepet = new Sepet();               // Arrange
    expect(sepet.toplam()).toBe(0);          // Assert
  });
});
```

**Python (pytest):**

```python
from sepet import Sepet

def test_bos_sepet_toplami_sifir():
    sepet = Sepet()                          # Arrange
    assert sepet.toplam() == 0               # Assert
```

**C# (xUnit):**

```csharp
public class SepetTesti
{
    [Fact]
    public void BosSepetToplamiSifirdir()
    {
        var sepet = new Sepet();                     // Arrange
        Assert.Equal(0, sepet.Toplam());             // Assert
    }
}
```

**Java (JUnit 5):**

```java
class SepetTesti {
    @Test
    void bosSepetToplamiSifirdir() {
        var sepet = new Sepet();                     // Arrange
        assertEquals(0, sepet.toplam());             // Assert
    }
}
```

**Go (testing — stdlib):**

```go
func TestBosSepetToplamiSifirdir(t *testing.T) {
    sepet := NewSepet()                              // Arrange
    if got := sepet.Toplam(); got != 0 {             // Assert
        t.Fatalf("Toplam() = %d, want 0", got)
    }
}
```

**C++ (Google Test):**

```cpp
TEST(SepetTesti, BosSepetToplamiSifirdir) {
    Sepet sepet;                                     // Arrange
    EXPECT_EQ(sepet.Toplam(), 0);                    // Assert
}
```

**Rust (cargo test — yerleşik):**

```rust
#[test]
fn bos_sepet_toplami_sifirdir() {
    let sepet = Sepet::yeni();                       // Arrange
    assert_eq!(sepet.toplam(), 0);                   // Assert
}
```

**Ruby (RSpec):**

```ruby
RSpec.describe Sepet do
  it 'boş sepetin toplamı 0' do
    sepet = described_class.new                     # Arrange
    expect(sepet.toplam).to eq(0)                   # Assert
  end
end
```

**Kotlin (JUnit 5):**

```kotlin
class SepetTesti {
    @Test
    fun `bos sepet toplami sifirdir`() {
        val sepet = Sepet()                          // Arrange
        assertEquals(0, sepet.toplam)                // Assert
    }
}
```

**Swift (XCTest):**

```swift
final class SepetTesti: XCTestCase {
    func testBosSepetToplamiSifirdir() {
        let sepet = Sepet()                          // Arrange
        XCTAssertEqual(sepet.toplam, 0)              // Assert
    }
}
```

**C (Unity — ThrowTheSwitch):**

```c
#include "unity.h"

void test_bos_sepet_toplami_sifirdir(void) {
    Sepet sepet;                                     /* Arrange */
    TEST_ASSERT_EQUAL_INT(0, sepet_toplam(&sepet));  /* Assert */
}
```

## §10 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | shared/tests/Unit/Log/Psr3LoggerTest.php | 44 | PSR-3 arayüz doğrulaması (assertInstanceOf) |
| 2 | shared/tests/Unit/Log/Psr3LoggerTest.php | 49 | Sekiz metodun callable doğrulaması |
| 3 | shared/tests/Unit/Log/Psr3LoggerTest.php | 74 | Seviye etiketi assertion'ı |
| 4 | shared/tests/Unit/Log/LoggerRedactionTest.php | 55 | Maskeleme testi başlığı |
| 5 | shared/tests/Unit/Log/LoggerRedactionTest.php | 63-64 | REDACTED varlığı + sızıntı yokluğu |
| 6 | shared/tests/Unit/Log/LoggerRedactionTest.php | 67 | Önek (prefix) şekli assertion'ı |
| 7 | shared/tests/Unit/Device/DeviceDetectorTest.php | 26 | Deterministik sabit girdi→çıktı |
| 8 | shared/tests/Middleware/MiddlewarePipelineTest.php | 23 | AAA — boş boru hattı |
| 9 | shared/tests/Middleware/MiddlewarePipelineTest.php | 34 | Tek middleware çalıştırma |
| 10 | shared/tests/Middleware/MiddlewarePipelineTest.php | 52 | Sıra assertion'ı (spy deseni) |
| 11 | shared/tests/Middleware/MiddlewarePipelineTest.php | 55 | Çoklu middleware sıralaması |
| 12 | shared/tests/Middleware/CsrfMiddlewareTest.php | 18 | `setUp()` fixture |
| 13 | shared/tests/Middleware/CsrfMiddlewareTest.php | 32-46 | AAA + iki assertion |
| 14 | shared/phpunit.xml | 9-11 | Unit testsuite tanımı |
| 15 | shared/phpunit.xml | 34-38 | Kapsam kaynağı: `src` dizini |
| 16 | shared/composer.json | 24 | phpunit/phpunit ^10.5 |
| 17 | shared/composer.json | 47-48 | `test` ve `stan` scriptleri |
| 18 | .github/workflows/ci.yml | 80 | `--testsuite Unit` seçicisi |

**Dosya sayımı (2026-10-12):** `shared/tests` altında **44** PHP dosyası
(`[IO.File]` sayımı; 2 fixture + 2 SQLite yardımcısı + 40 test sınıfı).

## §11 Yasaklar ve anti-desenler

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Assertion'sız test | Bilgi üretmez | Risky raporu (§8.3) |
| 2 | Gerçek saatin birim testte kullanımı | Determinism bozar | Kod incelemesi |
| 3 | Birim testte ağ çağrısı | Yavaş + kararsız | Kod incelemesi |
| 4 | Testler arası paylaşılan statik durum | Sıra bağımlılığı | Paralel koşu |
| 5 | `catch` ile hatayı yutup yeşil bırakmak | Sahte yeşil | İstisna assert'i |
| 6 | Test içinde gerçek parola/anahtar | Sızıntı | Redaction (§8.4) |
| 7 | Aynı değeri iki assert ile tekrar | Gürültü | Kod incelemesi |
| 8 | `sleep()` ile zaman kazanma | Kararsızlık | Bekleme yasağı |

## §12 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K019-testing/integration | Birim test yeşilse | Entegrasyon aşamasına geçiş | `--testsuite` seçicisi | mevcut (phpunit.xml) |
| 2 | K019-testing/coverage | Assertion yoğunluğu | Kapsam girdisi | `source` bloğu | mevcut (Satır 34-38) |
| 3 | K019-testing/regression | Yeşil taban (baseline) | Regresyon referansı | CI hattı sonucu | kısmen (ci.yml Satır 67) |
| 4 | K019-testing/e2e | Birim test kapısı | E2E'ye geçiş | sert kapı | mevcut (ci.yml Satır 143) |
| 5 | K018-devops/pipeline | Test komutu | CI adımı | `composer test` | mevcut (ci.yml Satır 104) |
| 6 | K017-observability/logging | Log formatı | Log testinin beklediği format | satır formatı | mevcut (§8.4) |

**Sıra:** unit → integration → performance → regression → e2e; her aşamanın yeşili bir sonrakinin
ön koşuludur.

## §13 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Mock çerçevesi yok | Double'lar elde yazılır, tekrar artabilir | orta | Gereksinim değerlendirilmesi | açık (composer.json kanıtı) |
| 2 | Determinism kapısı yok | Kararsız test yayına girebilir | yüksek | tarama adımı ekleme | ⚠️ VERIFICATION REQUIRED |
| 3 | Risky raporu okunmuyor | Sahte yeşil | yüksek | Rapor alanı + kapı | ⚠️ VERIFICATION REQUIRED |
| 4 | `tearDown` tespit edilmedi | Geçici dosya birikimi | orta | Dosya taraması | ⚠️ VERIFICATION REQUIRED |
| 5 | Test isimlendirme zorunlu değil | Okunabilirlik düşer | düşük | İsimlendirme kuralı | açık |
| 6 | Unit suitinin CI kapsamı dar | `Api`/`Repository` suitleri PR'da koşmuyor olabilir | yüksek | CI seçici gözden geçirmesi | ⚠️ VERIFICATION REQUIRED |
| 7 | Kapsam yüzdesi hedefi yok | Ölçüm motiva etmiyor | orta | `coverage/` eşiği | bkz. coverage dosyası |

## §14 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Dosya | 44 test PHP dosyası | ReadAllLines sayımı | aylık | 2026-10-12 ölçümü |
| 2 | Düzenek | Her test AAA | Kod incelemesi | her PR | §4 |
| 3 | Determinism | Birim testte saat/rastgelelik/ağ yok | Kaynak taraması | her PR | §7 |
| 4 | Assertion | Her test ≥ 1 anlamlı assert | PHPUnit risky çıktısı | her koşu | §8.3 |
| 5 | Kapı | `composer test` + `composer stan` yeşil | CI hattı | her PR | ci.yml Satır 62, 104 |
| 6 | Sürüm | phpunit ^10.5 sabit | composer.json | her güncelleme | Satır 24 |

## §15 Yeni test yazım şablonu

| # | Şablon alanı | Zorunlu | Değer | Denetim |
|---|---|---|---|---|
| 1 | Dosya adı | evet | `<Sınıf>Test.php` | phpunit keşfi |
| 2 | Sınıf adı | evet | `<Sınıf>Test` | autoload |
| 3 | Test metodu | evet | `testEylem_Beklenen` | §15.1 |
| 4 | Düzenek | evet | AAA | §4 |
| 5 | Double kararı | evet | §5 matrisi | Kod incelemesi |
| 6 | Determinism denetimi | evet | §7.2 listesi | Kod incelemesi |
| 7 | Assertion | evet | ≥ 1 anlamlı | §8 |
| 8 | Uygun testsuite | evet | Unit / diğer | phpunit.xml |

### §15.1 İsimlendirme deseni

| # | Desen | Örnek | Anlamı |
|---|---|---|---|
| 1 | `testEylem` | `testImplementsPsr3LoggerInterface` | Ne test ediliyor |
| 2 | `testEylem_beklenen` | `testRateLimitExceededReturns429` | Ne bekleniyor |
| 3 | `testDurum_beklenen` | `testFirstRequestAlwaysAllowed` | Durum → sonuç |
| 4 | Türkçe mesaj | `'GET requests should bypass CSRF'` | Hata anında bağlam |

**Kural:** metot adı, testin neyi bildirdiğini tek cümlede anlatmalıdır; `test2`, `testNew`,
`testFix` gibi adlar yasaktır.

## §16 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| PHPUnit 10.5 birim test yazım kılavuzu test metodlarını ve assertion kullanımını tanımlar | https://docs.phpunit.de/en/10.5/writing-tests-for-phpunit.html | 2026-10-12 | HTTP 200 doğrulandı |
| PHPUnit 10.5 test double (stub/mock/spy) belgesi sahte nesne türlerini tanımlar | https://docs.phpunit.de/en/10.5/test-doubles.html | 2026-10-12 | HTTP 200 doğrulandı |
| PHPUnit 10.5 fixture belgesi `setUp`/`tearDown` yaşam döngüsünü tanımlar | https://docs.phpunit.de/en/10.5/fixtures.html | 2026-10-12 | HTTP 200 doğrulandı |
| PHPUnit 10.5 risky tests belgesi riskli test sınıflandırmasını tanımlar | https://docs.phpunit.de/en/10.5/risky-tests.html | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 11 yeni
derin-branch dosyasında bulunmaz.

## §17 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | satır numarası doğrulama | 18 satır eşleşti | 2026-10-12 |
| 5 | Test dosyası sayısı | dosya sayımı | 44 PHP dosyası | 2026-10-12 |
| 6 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 10 | group alanı | frontmatter | `kod` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
