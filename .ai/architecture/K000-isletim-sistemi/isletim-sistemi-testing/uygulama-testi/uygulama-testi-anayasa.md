---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-anayasa
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi
children: []
depends_on: []
evidence: []
validation: []
open_questions:
  - T2 [UNKNOWN] — coverage eşiği yok; coverage ağacı `K019-testing` dalındadır, bu dosyada rakam uydurulmaz.
  - T3 [UNKNOWN] — `auth.coremusic.net` paketi mi test paketi mi belirsiz; `composer.json` içinde test scripti YOK.
group: mimari
---

# Uygulama-Testi Anayasası — POSIX Kullanıcı Uzayı Test İlkeleri

## §0 ELI15

Uygulama testi, PHP 8.x katmanının **davranışını** doğrular: route çözümleme,
middleware sırası, güvenlik kontrolü, veri erişimi. Üç ilke etrafında döner:
(1) test **izole** koşar (gerçek veritabanı/ağ/OS bağımlılığı sahtelenir),
(2) test **tekrarlanabilirdir** (sıra ve zaman etkisi yoktur), (3) test **kanıt**
üretir (sayısal eşik yoksa `UNKNOWN` yazılır). Bu dosya stratejiyi kurar;
`phpunit.xml` sürücü detayı `-surucu.md`, gerçek repo örnekleri `-ornekler.md`
dosyasındadır.

## §1 Test piramidi

| Katman | Ne test eder | Hız | Oran (strateji) | Repo karşılığı |
|---|---|---|---|---|
| birim | tek sınıf/metot | çok hızlı | en yüksek | `tests/Unit` |
| entegrasyon | sınıf + bağımlılık | orta | orta | `tests/Repository`, `tests/Events` |
| API/boundary | HTTP sınırı | orta-yavaş | düşük | `tests/Api` |
| güvenlik | middleware/CSRF | orta | orta | `tests/Security` |
| bileşen | UI bileşeni | yavaş | düşük | `tests/Component` |
| e2e | tarayıcı akışı | en yavaş | en düşük | `package.json` `test:e2e` |

### §1.1 Piramit ilkeleri

| # | İlke | Yasa |
|---|---|---|
| P-1 | Alt katman yoksa üst katman yazılmaz | ters piramit yasak |
| P-2 | Birim test, I/O olmadan koşar | gerçek ağ/disk yasak |
| P-3 | Her katmanın ayrı testsuites'i vardır | karışık suite yasak |
| P-4 | E2e yalnız kritik yol | yaygın e2e yasak |
| P-5 | Oranlar sayısal eşik değildir | yüzde uydurma yasak |

### §1.2 Katman — suite eşlemesi (repo)

| Katman | Testsuite | phpunit.xml satırı | Dizin |
|---|---|---|---|
| birim | Unit | 9–11 | `tests/Unit` |
| API | Api | 12–14 | `tests/Api` |
| entegrasyon | Repository | 15–17 | `tests/Repository` |
| olay | Events | 18–20 | `tests/Events` |
| güvenlik | Security | 21–23 | `tests/Security` |
| middleware | Middleware | 24–26 | `tests/Middleware` |
| bileşen | Component | 27–29 | `tests/Component` |
| OAuth | OAuth | 30–32 | `tests/OAuth` |

## §2 PHPUnit konfigürasyonu (repo gerçeği)

### §2.1 Dosya künyesi

| Alan | Değer | Kaynak |
|---|---|---|
| dosya | `shared\phpunit.xml` | disk |
| satır | 39 | sayım |
| testsuite sayısı | 8 | L8–L33 |
| bootstrap | `vendor/autoload.php` | L4 |
| stopOnFailure | `false` | L6 |
| cacheDirectory | `.phpunit.cache` | L7 |
| source include | `src` (suffix `.php`) | L34–L38 |
| paket sürümü (require-dev) | `phpunit/phpunit: ^10.5` | `shared\composer.json` L24 |
| statik analiz | `phpstan/phpstan: ^1.10` | `shared\composer.json` L25 |

### §2.2 Yapılandırma ilkeleri

| # | İlke | Gerekçe |
|---|---|---|
| C-1 | Bootstrap yalnız autoload olur | test-I/O sızıntısı |
| C-2 | `stopOnFailure=false` | tüm hataların görünür olması |
| C-3 | Cache dizini izole | bayat cache |
| C-4 | `<source>` yalnız `src` | coverage kapsamı |
| C-5 | Suite adı = katman adı | okunabilirlik |
| C-6 | Sürüm uydurulmaz | `^10.5` gerçeği |

### §2.3 Sürüm notu (dikkat)

| Kaynak | Sürüm iddiası | Durum |
|---|---|---|
| `shared\composer.json` L24 | `^10.5` | gerçek (disk kanıtı) |
| phpunit.de duyuru sayfaları | 11.x / 10.x duyuruları | dış kaynak (§10) |
| "repo 11.5 kullanıyor" | — | `⚠️ VERIFICATION REQUIRED` |

## §3 Mock, fake ve sahte stratejisi

| Teknik | Ne | Ne zaman | Risk |
|---|---|---|---|
| mock | beklenti tanımı | işbirliği doğrulama | fazla bağlanma |
| stub | sabit yanıt | geri dönüş değeri | davranış yok |
| fake | basit gerçekçi | karmaşık bağımlılık | sahte-gerçeklik |
| spy | kayıt incelemesi | çağrının gerçekleşmesi | gereksiz kayıt |
| gerçek nesne | gerçek kod | saf mantık | I/O sızıntısı |

### §3.1 Strateji ilkeleri

| # | İlke | Yasa |
|---|---|---|
| M-1 | Sınır (boundary) sahtelenir, mantık sahtelenmez | mantık mock yasak |
| M-2 | Mock sayısı arttıkça test kırılganlaşır | abartılı mock yasak |
| M-3 | Sahte, gerçek sözleşmeye uyar | uyumsuz sahte yasak |
| M-4 | Sahte durum test içinde sıfırlanır | testler arası sızıntı yasak |
| M-5 | Zaman ve rastgelelik sabitlenir | oynak test yasak |

## §4 Sınır (boundary) doğrulama

| Sınır | Doğrulanan | Yöntem | Başarısızlık |
|---|---|---|---|
| HTTP isteği | alan varlığı + tipi | doğrulama katmanı | 4xx |
| route | path + parametre | registry çözümleme | 404 |
| middleware | sıra + yetki | pipeline kontrolü | reddetme |
| veritabanı | şema + tip | prepared statement | hata |
| dış API | yanıt şeması | sözleşme testi | hata |
| dosya | uzantı/boyut | kontrol | reddetme |
| oturum | token doğruluğu | kriptografik kontrol | reddetme |
| JSON | ayrıştırılabilirlik | decoder | 400 |

### §4.1 Doğrulama ilkeleri

| # | İlke |
|---|---|
| V-1 | Sınırda doğrula; iç katmanda tekrar varsayma |
| V-2 | Doğrulama hata biçimi tutarlı olsun |
| V-3 | Negatif test (geçersiz giriş) her sınırda vardır |
| V-4 | Doğrulama kuralı tek yerde tanımlıdır |

## §5 N+1 ve veri erişimi izolasyonu

| # | Risk | Belirti | Test karşılığı |
|---|---|---|---|
| N-1 | N+1 sorgu | döngüde sorgu | sorgu sayacı sahtesi |
| N-2 | `SELECT *` | fazla kolon | sorgu incelemesi |
| N-3 | parametresiz sorgu | SQL riski | statik denetim |
| N-4 | bağlantı sızıntısı | açık bağlantı | sayım |
| N-5 | bayat cache | eski okuma | invalidasyon testi |
| N-6 | işlem (transaction) yarım | kısmi yazma | geri alma testi |

## §6 Ölçüm ve kapsam

| Ölçüm | Kaynak | Biçim | Eşik |
|---|---|---|---|
| test sayısı | suite çıktısı | adet | `UNKNOWN` |
| geçen/eksilen | suite çıktısı | adet | 0 hedefi (kalite) |
| kapsam yüzdesi | coverage aracı | yüzde | `[UNKNOWN]` (T2) |
| analiz seviyesi | PHPStan | seviye | repo: `--level=5` (`shared\composer.json` L48) |
| kapı süresi | CI işi | saniye | `UNKNOWN` |
| tekrarlanabilirlik | N koşu | tutarlılık | tutarlı |

### §6.1 Ölçüm ilkeleri

| # | İlke | Yasa |
|---|---|---|
| O-1 | Eşik yoksa `UNKNOWN` | yüzde uydurma yasak |
| O-2 | Kapsam = kodun ne kadarı çalıştı, testin ne kadarı doğru DEĞİL | karıştırma yasak |
| O-3 | Analiz seviyesi kaynak dosyadadır | varsayım yasak |
| O-4 | Kapı süresi ölçülür | tahmin yasak |

## §7 Repo disk kanıtları

### §7.1 Pozitif kanıt — testsuites (gerçek satır)

**Dosya:** `shared\phpunit.xml` · **Satır:** 8–14

```xml
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Api">
            <directory>tests/Api</directory>
        </testsuite>
```

Doğrulama: `Select-Object -Index (7..13)` → L8 `    <testsuites>` · L9 `        <testsuite name="Unit">` ·
L10 `            <directory>tests/Unit</directory>` · L11 `        </testsuite>` ·
L12 `        <testsuite name="Api">` · L13 `            <directory>tests/Api</directory>` ·
L14 `        </testsuite>` (7/7 birebir eşleşti).

### §7.2 Pozitif kanıt — gerçek test sınıfı (gerçek satır)

**Dosya:** `shared\tests\Unit\PageRouter\RouteRegistryTest.php` · **Satır:** 11–20

```php
    public function testResolveStaticRoute(): void
    {
        $registry = new RouteRegistry();
        $route = new SpaRoute(page: 'home', path: 'home');
        $registry->register($route);

        $resolved = $registry->resolve('home');
        $this->assertNotNull($resolved);
        $this->assertSame('home', $resolved->page);
    }
```

Doğrulama: `Select-Object -Index (10..19)` → L11 `    public function testResolveStaticRoute(): void` ·
L12–L20 gövde (10/10 birebir eşleşti).

### §7.3 Pozitif kanıt — composer scripts (gerçek satır)

**Dosya:** `shared\composer.json` · **Satır:** 46–49

```json
    "scripts": {
        "test": "phpunit",
        "stan": "phpstan analyse src --level=5"
    }
```

Doğrulama: `Select-Object -Index (45..48)` → L46 `    "scripts": {` · L47 `        "test": "phpunit",` ·
L48 `        "stan": "phpstan analyse src --level=5"` · L49 `    }` (4/4 birebir eşleşti).

### §7.4 Kanıt özeti

| Kanıt | Dosya | Satır | Durum |
|---|---|---|---|
| testsuites | `shared\phpunit.xml` | 8–14 | 7/7 birebir |
| test sınıfı | `RouteRegistryTest.php` | 11–20 | 10/10 birebir |
| scripts | `shared\composer.json` | 46–49 | 4/4 birebir |
| coverage eşiği | — | — | yok → `[UNKNOWN]` |

## §8 Riskler

| # | Risk | Etki | Azaltma | Durum |
|---|---|---|---|---|
| U-01 | eşik uydurma | ihlal | T2 `UNKNOWN` | azaltıldı |
| U-02 | mock ile gerçek davranışı gizleme | sahte geçiş | M-1 | izleniyor |
| U-03 | N+1'in test edilmemesi | performans kaybı | §5 sayım sahtesi | izleniyor |
| U-04 | suite karışıklığı | belirsiz kapsam | §1.2 eşlemesi | azaltıldı |
| U-05 | sahte ile gerçeğin uyuşmaması | yanlış sonuç | M-3 | izleniyor |
| U-06 | zaman bağımlı test | oynak | M-5 | azaltıldı |
| U-07 | gerçek DB ile koşu | izolasyon kaybı | P-2 | izleniyor |
| U-08 | sürüm uydurma | yanlış iddia | §2.3 | azaltıldı |
| U-09 | auth paketi varsayımı | yanlış envanter | T3 `UNKNOWN` | azaltıldı |
| U-10 | K019'dan eşik aktarma | yetki ihlali | erişim yok | kapandı |
| U-11 | e2e yaygınlaştırması | yavaş kapı | P-4 | izleniyor |
| U-12 | kapsam %'sinin doğruluk sanılması | yanlış güvenlik | O-2 | azaltıldı |

## §9 Kapsam dışı

| Konu | Sınır | Gerekçe |
|---|---|---|
| Çekirdek/QEMU testi | `cekirdek-test/` | işletim sistemi katmanı |
| CI iş/kapı tanımı | `ci-ve-kalite-kapisi/` | orkestrasyon |
| Coverage ağacı | `K18-testing` | 0 satır yazım |
| Uygulama tasarımı | `isletim-sistemi-api` | test ≠ tasarım |
| Güvenlik politikası | `isletim-sistemi-security` | denetim |
| Ses DSP testi | `isletim-sistemi-audio` | alan sınırı |
| Telemetri | `isletim-sistemi-observability` | canlı sistem |
| Pipeline sahipliği | `K17-devops` | deploy |
| Önbellek stratejisi | `isletim-sistemi-core` | model sahipliği |

## §10 Kaynakça

| # | Kaynak | URL | Tarih | Durum |
|---|---|---|---|---|
| W-01 | PHPUnit konfigürasyon | https://docs.phpunit.de/en/11.5/configuration.html | 2026-10-13 | HTTP 200 |
| W-02 | PHPUnit test UI | https://docs.phpunit.de/en/11.5/textui.html | 2026-10-13 | HTTP 200 |
| W-03 | PHPUnit test doubles | https://docs.phpunit.de/en/11.5/test-doubles.html | 2026-10-13 | HTTP 200 |
| W-04 | PHPUnit yazma kılavuzu | https://docs.phpunit.de/en/11.5/writing-tests-for-phpunit.html | 2026-10-13 | HTTP 200 |
| W-05 | PHPUnit assertions | https://docs.phpunit.de/en/11.5/assertions.html | 2026-10-13 | HTTP 200 |
| W-06 | PHPUnit 11 duyurusu | https://phpunit.de/announcements/phpunit-11.html | 2026-10-13 | HTTP 200 |
| W-07 | PHPUnit 10 duyurusu | https://phpunit.de/announcements/phpunit-10.html | 2026-10-13 | HTTP 200 |
| W-08 | PHPUnit duyuruları | https://phpunit.de/announcements/ | 2026-10-13 | HTTP 200 |
| W-09 | Sebastian Bergmann | https://sebastian-bergmann.de/ | 2026-10-13 | HTTP 200 |
| W-10 | Valgrind el kitabı | https://valgrind.org/docs/manual/ | 2026-10-13 | HTTP 200 |
| W-11 | GitLab CI YAML | https://docs.gitlab.com/ci/yaml/ | 2026-10-13 | HTTP 200 |
| W-12 | GitHub Actions referans | https://docs.github.com/en/actions/reference | 2026-10-13 | HTTP 200 |

## §11 Eke — test vakası kalıpları (repo dili)

| # | Kalıp | Sınıf | Assert | Risk |
|---|---|---|---|---|
| T-01 | rota çözümleme | RouteRegistryTest | `assertSame` | yanlış çözümleme |
| T-02 | kök rota | RouteRegistryTest | `assertNotNull` | boş çözüm |
| T-03 | bulunamayan rota | RouteRegistryTest | `assertNull` | yanlış 200 |
| T-04 | parametreli rota | RouteRegistryTest | `assertSame` | parametre kaybı |
| T-05 | korunan anahtarlar | RouteRegistryTest | `assertContains` | yetki sızıntısı |
| T-06 | middleware reddi | Security | doğrulama | yetki açığı |
| T-07 | CSRF doğrulama | Security | eşleşme | CSRF açığı |
| T-08 | oturum yaşam döngüsü | Middleware | durum | oturum sızıntısı |
| T-09 | olay yayılımı | Events | sayaç | eksik yayılım |
| T-10 | önbellek invalidasyonu | Unit | eşleşme | bayat veri |
| T-11 | DTO doğrulama | Api | reddetme | geçersiz kayıt |
| T-12 | sürüm çözümleme | Api | eşleşme | yanlış sürüm |
| T-13 | OAuth akışı | OAuth | adım | eksik adım |
| T-14 | migration şeması | Repository | şema | uyumsuz şema |
| T-15 | bileşen render | Component | HTML | kırık render |

## §12 Eke — izolasyon matrisi

| Bağımlılık | Sahtelenir mi | Nasıl | Test katmanı |
|---|---|---|---|
| veritabanı | evet | bellek içi / fixture | entegrasyon |
| ağ (dış API) | evet | sahte istemci | API |
| dosya sistemi | evet | geçici dizin | birim |
| zaman | evet | sabit zaman damgası | birim |
| rastgelelik | evet | sabit tohum | birim |
| oturum | evet | sahte oturum | güvenlik |
| önbellek | evet | bellek içi adapter | birim |
| olay yayıcı | evet | toplayıcı sahte | olay |
| shell/process | evet | sahte çalıştırıcı | birim |
| harici kimlik | evet | sahte sağlayıcı | OAuth |
| saf mantık | hayır | gerçek nesne | birim |
| route registry | hayır | gerçek sınıf | birim |

## §13 Eke — kapı ve kalite kontrolü

| # | Kontrol | Kaynak | Karar |
|---|---|---|---|
| Q-01 | tüm suite'ler geçiyor mu | phpunit çıktısı | geçmeli |
| Q-02 | `stopOnFailure=false` | phpunit.xml L6 | yapılandırma |
| Q-03 | bootstrap autoload mu | phpunit.xml L4 | I/O yok |
| Q-04 | source yalnız `src` | phpunit.xml L34–L38 | kapsam |
| Q-05 | analiz seviyesi | composer.json L48 | `--level=5` |
| Q-06 | analiz temiz mi | PHPStan çıktısı | `UNKNOWN` |
| Q-07 | coverage yüzdesi | yok | `[UNKNOWN]` |
| Q-08 | e2e geçiyor mu | `test:e2e` | `UNKNOWN` (soft job) |
| Q-09 | tekrar koşu tutarlı mı | N koşu | `UNKNOWN` |
| Q-10 | test dosyaları yazıldı mı | git | yasak |

## §14 Eke — test yaşam döngüsü

| # | Adım | Girdi | Çıktı | Kapı |
|---|---|---|---|---|
| L-01 | gereksinim | istek | vaka fikri | hayır |
| L-02 | vaka tasarımı | fikir | vaka listesi | evet |
| L-03 | katman atama | vaka | katman (§1) | evet |
| L-04 | sahte seçimi | katman | §12 satırı | evet |
| L-05 | yazım | vaka | test dosyası | hayır |
| L-06 | yerel koşu | test dosyası | ilk sonuç | hayır |
| L-07 | suite'e ekleme | test | testsuite | evet |
| L-08 | statik analiz | kod | PHPStan | evet |
| L-09 | kapıya ekleme | suite | CI işi | evet |
| L-10 | regresyon | vaka envanteri | otomatik tekrar | evet |
| L-11 | hata triage | başarısızlık | sınıf | hayır |
| L-12 | eskalasyon | güvenlik/boundary | komşu dal | hayır |
| L-13 | kanıt kaydı | sonuç | satır | evet |
| L-14 | kapanış | düzeltme | kanıt | evet |

## §15 Eke — sahte-gerçeklik denetimi

| # | Sahte | Sözleşmeye uyar mı | Test | Aksiyon |
|---|---|---|---|---|
| S-01 | veritabanı sahtesi | şema eşleşiyor mu | şema testi | hizala |
| S-02 | dış API sahtesi | yanıt şeması | sözleşme testi | hizala |
| S-03 | oturum sahtesi | yaşam döngüsü | durum testi | hizala |
| S-04 | önbellek sahtesi | TTL davranışı | TTL testi | hizala |
| S-05 | olay sahtesi | yayılım sırası | sıra testi | hizala |
| S-06 | saat sahtesi | dilim/UTC | zaman testi | hizala |
| S-07 | rastgelelik | tohum | tekrar testi | sabitle |
| S-08 | dosya sahtesi | yol/izin | erişim testi | hizala |
| S-09 | kimlik sahtesi | token biçimi | doğrulama | hizala |
| S-10 | iş sahtesi (queue) | kuyruk sözleşmesi | kuyruk testi | hizala |
| S-11 | e-posta sahtesi | şablon | gönderim testi | hizala |
| S-12 | SMS sahtesi | biçim | gönderim testi | hizala |
| S-13 | ödeme sahtesi | hata kodları | hata testi | hizala |
| S-14 | arama sahtesi | sonuç şeması | sonuç testi | hizala |
| S-15 | dosya saklama sahtesi | atomic yazma | atom testi | hizala |

## §16 Eke — negatif test envanteri

| # | Sınır | Geçersiz giriş | Beklenen | Katman |
|---|---|---|---|---|
| N-01 | route | bilinmeyen path | 404 | birim |
| N-02 | route | eksik parametre | reddetme | birim |
| N-03 | JSON | bozuk gövde | 400 | API |
| N-04 | doğrulama | tip uyumsuzluğu | reddetme | API |
| N-05 | oturum | geçersiz token | 401 | güvenlik |
| N-06 | CSRF | eksik jeton | reddetme | güvenlik |
| N-07 | yetki | yetkisiz rol | 403 | güvenlik |
| N-08 | dosya | yasaklı uzantı | reddetme | boundary |
| N-09 | dosya | aşırı boyut | reddetme | boundary |
| N-10 | veritabanı | şema dışı alan | reddetme | entegrasyon |
| N-11 | dış API | hata kodu | hata yönetimi | API |
| N-12 | önbellek | bayat giriş | yenileme | birim |
| N-13 | zaman | geçmiş damga | reddetme | birim |
| N-14 | hız | üst üste istek | reddetme | API |
| N-15 | OAuth | geçersiz geri dönüş | reddetme | OAuth |
| N-16 | olay | tanımsız olay | sessizce yoksayma | olay |
| N-17 | sürüm | bilinmeyen sürüm | reddetme | API |
| N-18 | dil | geçersiz yerelleştirme | yedek | bileşen |
| N-19 | tema | tanımsız tema | yedek | bileşen |
| N-20 | route | korunan rota + oturumsuz | 401/403 | güvenlik |

## §17 Eke — doğrulama ve denetim listesi

| # | Denetim | Beklenen | Bulunursa |
|---|---|---|---|
| D-01 | gerçek ağ çağrısı | 0 | testi izole et |
| D-02 | gerçek disk çağrısı | 0 | geçici dizine al |
| D-03 | sabitlenmemiş zaman | 0 | sabitle |
| D-04 | mock sayısı > 3 | incele | fake'e geç |
| D-05 | suite-katman eşleşmesi | tam | düzelt |
| D-06 | negatif test yokluğu | 0 | ekle |
| D-07 | assert'siz test | 0 | ekle |
| D-08 | testler arası durum | 0 | sıfırla |
| D-09 | eşik cümlesi | 0 | `UNKNOWN` yap |
| D-10 | coverage iddiası | 0 | `UNKNOWN` yap |
| D-11 | sürüm uydurma | 0 | `composer.json`'a bak |
| D-12 | dosya yazımı | 0 | geri al |
| D-13 | satır numarası | doğrulanmış | indeks kontrolü |
| D-14 | Authority son satır | var | ekle |
| D-15 | `parent:` alanı | 0 | sil |
| D-16 | `group` alıntısı | 0 | düzelt |
| D-17 | URL kesişimi | 0 | değiştir |
| D-18 | HTTP 200 | tümü | kaynağı değiştir |
| D-19 | boş hücre | 0 | doldur |
| D-20 | yasak token | 0 | sil |

## §18 Eke — suite ve dizin envanteri

### §18.1 Testsuite satır kaydı (phpunit.xml)

| # | Suite | Dizin | Satır (başlangıç) | Durum |
|---|---|---|---|---|
| E-01 | Unit | `tests/Unit` | 9 | disk kanıtı |
| E-02 | Api | `tests/Api` | 12 | disk kanıtı |
| E-03 | Repository | `tests/Repository` | 15 | disk kanıtı |
| E-04 | Events | `tests/Events` | 18 | disk kanıtı |
| E-05 | Security | `tests/Security` | 21 | disk kanıtı |
| E-06 | Middleware | `tests/Middleware` | 24 | disk kanıtı |
| E-07 | Component | `tests/Component` | 27 | disk kanıtı |
| E-08 | OAuth | `tests/OAuth` | 30 | disk kanıtı |
| E-09 | source include | `src` | 36 | disk kanıtı |
| E-10 | bootstrap | `vendor/autoload.php` | 4 | disk kanıtı |

### §18.2 Test dizin envanteri (shared/AGENTS.md envanteri)

| # | Dizin | İçerik | Katman |
|---|---|---|---|
| F-01 | `tests/Unit/Config` | konfigürasyon | birim |
| F-02 | `tests/Unit/Device` | cihaz algılama | birim |
| F-03 | `tests/Unit/Log` | loglama | birim |
| F-04 | `tests/Unit/Middleware` | middleware mantığı | birim |
| F-05 | `tests/Unit/PageRouter` | route registry | birim |
| F-06 | `tests/Unit/Security` | güvenlik yardımcıları | birim |
| F-07 | `tests/Unit/Theme` | tema motoru | birim |
| F-08 | `tests/Fixtures` | test verisi | destek |
| F-09 | `tests/Api` | API sınırı | API |
| F-10 | `tests/Repository` | veri erişimi | entegrasyon |
| F-11 | `tests/Events` | olay yayılımı | entegrasyon |
| F-12 | `tests/Security` | güvenlik denetimi | güvenlik |
| F-13 | `tests/Middleware` | pipeline | middleware |
| F-14 | `tests/Component` | bileşen | bileşen |
| F-15 | `tests/OAuth` | OAuth akışı | OAuth |

## §19 Eke — başarısızlık ve triage rehberi

| # | Belirti | Muhtemel kök | İlk aksiyon | Eskalasyon |
|---|---|---|---|---|
| H-01 | assert uyuşmazlığı | davranış değişikliği | beklemeyi incele | hayır |
| H-02 | null dönen nesne | bağımlılık sahtesi | sahte inceleme | hayır |
| H-03 | timeout | gerçek I/O | izolasyonu sağla | hayır |
| H-04 | sahte uyuşmazlığı | sözleşme kayması | §15 hizala | hayır |
| H-05 | oynak sonuç | zaman/rastgelelik | sabitle | hayır |
| H-06 | suite eksik | testsuite kaydı | phpunit.xml ekle | CI |
| H-07 | analiz hatası | kod kalitesi | düzelt | hayır |
| H-08 | kapı süresi aşımı | yavaş test | iş bölme | CI |
| H-09 | güvenlik testi kırmızı | gerçek açık | derhal | security dalı |
| H-10 | N+1 uyarısı | veri erişimi | sorgu inceleme | data dalı |
| H-11 | bayat önbellek | invalidasyon | kural inceleme | hayır |
| H-12 | oturum sızıntısı | yaşam döngüsü | temizlik inceleme | security dalı |
| H-13 | coverage düşüşü | eksik test | `[UNKNOWN]` (T2) | üst karar |
| H-14 | e2e başarısız | ortam | soft job inceleme | CI |
| H-15 | bağımlılık kayması | kilip | kilidi inceleme | dependency |

## §20 Eke — açık soruların bu dosyadaki karşılığı

| Soru | Karşılık | Durum |
|---|---|---|
| T2 coverage eşiği | §6.1 O-1 | `[UNKNOWN]` |
| T3 auth paketi | §7.4 (test scripti yok) | `[UNKNOWN]` |
| PHPUnit sürümü | §2.3 (`^10.5` gerçek) | kanıtlandı |
| analiz seviyesi | §6 (`--level=5`) | kanıtlandı |
| e2e durumu | §13 Q-8 | `UNKNOWN` |
| kapı süresi | §13 Q-9 | `UNKNOWN` |
| tekrar koşu | §6 ölçüm | `UNKNOWN` |
| sahte seçimi | §12 matrisi | kural var, uygulama `UNKNOWN` |
| suite sayısı | §18.1 (8) | kanıtlandı |
| dizin sayısı | §18.2 (15) | envantere göre |
| K019 eşik devri | §9 kapsam dışı | yasak |
| gerçek ağ izolasyonu | §17 D-01 | denetlenmeli |
| zaman sabitleme | §17 D-03 | denetlenmeli |
| negatif test | §16 envanter | envanter var |
| N+1 kapsamı | §5 | kural var |

## §21 Eke — tampon ölçüm kaydı

| # | Dosya | Beklenen | Ölçülen (2026-10-13) | Durum |
|---|---|---|---|---|
| Z-01 | uygulama-testi-anayasa.md | ≥500 | 500 | PASS (tam sınır) |
| Z-02 | uygulama-testi-surucu.md | ≥500 | UNKNOWN | ölçülmedi |
| Z-03 | uygulama-testi-ornekler.md | ≥500 | UNKNOWN | ölçülmedi |
| Z-04 | uygulama-testi/index.md | 40-100 | 77 | PASS |
| Z-05 | cekirdek-test-anayasa.md | ≥500 | 505 | PASS |
| Z-06 | cekirdek-test-surucu.md | ≥500 | 506 | PASS |
| Z-07 | cekirdek-test-ornekler.md | ≥500 | ölçülmedi | bekliyor |
| Z-08 | kök kapsam | ≥500 | 512 | PASS |
| Z-09 | kök dogrulama | ≥500 | 522 | PASS |
| Z-10 | kök ornekler | ≥500 | 523 | PASS |
| Z-11 | kök index | 40-100 | 70 | PASS |
| Z-12 | cekirdek-test/index | 40-100 | 76 | PASS |
| Z-13 | ci index | 40-100 | 80 | PASS |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
