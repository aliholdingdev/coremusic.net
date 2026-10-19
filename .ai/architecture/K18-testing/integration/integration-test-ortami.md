---
title: "K18-testing/integration — Entegrasyon Test Ortamı: Katmanlar, Veritabanı İzolasyonu ve HTTP Sözleşmesi"
node_id: K18-testing/integration/integration-test-ortami
node_type: module
status: draft
parent_id: K18-testing/integration
children: []
depends_on:
  - K18-testing
  - K18-testing/integration
evidence:
  - "shared/phpunit.xml — Satır 8-33 sekiz testsuite (Unit, Api, Repository, Events, Security, Middleware, Component, OAuth) (2026-10-12)"
  - "auth.coremusic.net/phpunit.xml — Satır 6 failOnRisky, Satır 7 failOnWarning, Satır 10-12 Integration suite kaldırma notu (2026-10-12)"
  - "api.coremusic.net/config/routes.php — Satır 22 (405 + Allow), Satır 53-63 doğrulama kuralları (2026-10-12)"
  - "shared/src/Api/Gateway.php — Satır 125-135 methodNotAllowedResponse (405 + Allow başlığı) (2026-10-12)"
  - "shared/src/Middleware/ValidationMiddleware.php — Satır 74 httpStatus 422 (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 74-80 matrix: shared tam suit, auth yalnizca Unit (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "URL envanteri: 1206 benzersiz URL, bu dosyada çakışma 0 (2026-10-12)"
  - "HTTP durum kodu iddiaları kaynak kod satırıyla eşleştirildi (2026-10-12)"
open_questions:
  - "auth.coremusic.net icin Integration suite ne zaman geri eklenecek — tarih yok (⚠️ VERIFICATION REQUIRED)"
  - "Repository testsuite'i CI'da tam koşuluyor mu (shared args boş → tam suit) fakat veritabanı sağlayıcısı CI ortamında doğrulanmadı (⚠️ VERIFICATION REQUIRED)"
  - "Entegrasyon testleri icin ayrı test konteyneri (test container) tanımı depoda yok (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# K019-testing/integration/integration-test-ortami

> **ELI10:** Birim test tek parçayı sınar; entegrasyon testi parçaların **birleştiği yerleri**
> sınar: route → gateway → middleware → controller zinciri ve veritabanı işlemleri. Bu dosya,
> entegrasyon testinin nerede başlayıp nerede bittiğini, test ortamının nasıl kurulduğunu,
> her testten sonra durumun nasıl sıfırlandığını ve HTTP sözleşmesinin (405/422/429) nasıl
> doğrulandığını tanımlar.

## §1 Amaç

Bu modül, CoreMusic'in **entegrasyon testi kurallarının tek yetkili tanımıdır**. Dört karar bu
dosyada verilir: katman sınırı · ortam kurulumu · durum sıfırlama (reset) · HTTP sözleşmesi
doğrulaması. Birim testten farkı, **tek sınıf değil, iki ya da daha fazla modülün birleşimi**
olmasıdır.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Entegrasyon test nerede başlar? | Katman sınırı (layer boundary) | §4 |
| 2 | Ortam nasıl kurulur? | Ortam katmanları | §5 |
| 3 | Durum nasıl sıfırlanır? | DB reset / izolasyon | §6 |
| 4 | HTTP sözleşmesi nasıl sınanır? | Durum kodu + başlık doğrulaması | §7 |
| 5 | CI hangi suiti koşuyor? | Testsuite matrisi | §8 |
| 6 | `Integration` suiti neden yok? | Mevcut durum | §8.3 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | shared testsuite sayısı | adet | 8 (phpunit.xml Satır 8-33) | mevcut |
| H2 | auth testsuite sayısı | adet | 1 (Unit; Integration kaldırıldı) | auth phpunit.xml Satır 13-17 |
| H3 | Risky/warning kapısı | bayrak | `failOnRisky=true`, `failOnWarning=true` | auth phpunit.xml Satır 6-7 |
| H4 | CI matrix girdisi | adet | 2 (shared, auth.coremusic.net) | ci.yml Satır 74-80 |
| H5 | Ayrı test konteyneri | varlık | depoda yok | ⚠️ VERIFICATION REQUIRED |
| H6 | Entegrasyon suiti CI kapsamı | suit | shared tam suit; auth yalnız Unit | ci.yml Satır 76, 80 |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Katman sınırı | ✅ Birim/entegrasyon ayrımı | ❌ Tek sınıf testi → `unit/` |
| Testsuite yapısı | ✅ Sekiz suit + auth tek suit | ❌ Test yazım deseni → `unit/` |
| Veritabanı izolasyonu | ✅ Transaction ve temizlik stratejisi | ❌ Migration üretimi → veri katmanı |
| HTTP sözleşmesi | ✅ 405, 422, 429, 403 doğrulaması | ❌ Tarayıcıda çalışma → `e2e/` |
| Ortam kurulumu | ✅ Yerel ve CI ortam farkı | ❌ Sunucu kurulumu → K17-devops |
| Yük altında davranış | ✅ Yok (sınır) | ❌ Yük testi → `performance/` |
| Test konteyneri | ✅ Mevcut durum (yok) | ❌ Konteyner üretimi → K17-devops/containers |

**Sınır kararı:** entegrasyon testi, **gerçek HTTP ağına çıkmaz**; süreç içi (in-process) çağrı
ile gateway/middleware zincirini çalıştırır. Gerçek tarayıcı ve gerçek ağ yalnız `e2e/`
kapsamındadır.

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| Katman sınırı | layer boundary | Birim ↔ entegrasyon ayrımı | §4 |
| Testsuite | testsuite | phpunit.xml'de tanımlı test grubu | phpunit.xml Satır 8 |
| Reset | state reset | Test sonrası durumu sıfırlama | §6 |
| Transaction izolasyonu | transaction isolation | Testi işlem içinde tutup geri alma | SqliteDatabaseManager Satır 44-54 |
| Sözleşme | contract | Durum kodu + başlık + gövde bekleneni | §7 |
| Allow başlığı | Allow header | 405 yanıtında sunulan method listesi | Gateway.php Satır 135 |
| Doğrulama (validation) | request validation | Gövde kuralları, 422 ile biter | ValidationMiddleware Satır 74 |
| Sağlam (fail-closed) | fail-closed | Belirsizlikte reddetme | ApiPipelineSecurityTest Satır 52 |
| Suit matrisi | suite matrix | CI'da hangi suitin koştuğu | ci.yml Satır 74-80 |
| Test konteyneri | test container | İzole test servis ortamı | depoda yok |

## §4 Katman sınırı — ne entegrasyon, ne birim

Test, bir modülün **sınırını** aştığında entegrasyon sayılır. CoreMusic'te bu sınır somuttur:
route tablosu + gateway + middleware zinciri + controller eylemi.

| # | Test tipi | Kapsam | Örnek repo testi | Hangi suitte |
|---|---|---|---|---|
| 1 | Birim | Tek sınıf/saf mantık | DeviceDetectorTest | Unit |
| 2 | Sözleşme (birim-entegrasyon arası) | Tek sınıf + veri doğrulama | RouteTableTest | Api |
| 3 | Entegrasyon (süreç içi) | Gateway + middleware zinciri | ApiPipelineSecurityTest | Api |
| 4 | Entegrasyon (veritabanı) | Depo + transaction | ApiKeyRepositoryTest | Repository |
| 5 | Entegrasyon (otorizasyon) | Middleware + oturum | BypassAuthMiddlewareTest | Middleware |
| 6 | Uçtan uca | Gerçek tarayıcı + HTTP | yok (Playwright ayrı) | e2e/ |

### §4.1 Sınır karar matrisi

| # | Soru | Evet → | Hayır → | Not |
|---|---|---|---|---|
| 1 | İki ya da daha fazla modül çağrılıyor mu? | Entegrasyon | Birim | §4 satır 3 |
| 2 | Route tablosu okunuyor mu? | Entegrasyon | Birim | RouteTableTest sınırda |
| 3 | Veritabanına yazılıyor mu? | Entegrasyon | Birim | Repository suiti |
| 4 | Gerçek ağ kullanılıyor mu? | E2E | Süreç içi | §2 sınır kararı |
| 5 | Dosya sistemi kullanılıyor mu? | İkisi de olabilir | — | Log testleri birim kalır |

### §4.2 Repo örneği — sınırın iki tarafı

| # | Test | Sınır | Satır | Suit |
|---|---|---|---|---|
| 1 | DeviceDetectorTest | Saf mantık — birim | 26 | Unit |
| 2 | RouteTableTest | Route eşleştirme — sınır | 38-45 | Api |
| 3 | ApiPipelineSecurityTest | CORS + yanıt başlıkları — entegrasyon | 34-49 | Api |
| 4 | RateLimiterMiddlewareTest | Sayaç + yanıt kodu — entegrasyon | 52-71 | Middleware |
| 5 | ApiKeyRepositoryTest | Depo + SQL — entegrasyon | — | Repository |

**Dosya: `shared/tests/Api/RouteTableTest.php` / Satır: 38 · 44 · 45**
**Dosya: `shared/tests/Api/ApiPipelineSecurityTest.php` / Satır: 34 · 49 · 52**

### §4.3 Katman sınırı karar akışı

| # | Adım | Soru | Evet ise | Hayır ise |
|---|---|---|---|---|
| 1 | Tek sınıfta kalıyor mu? | Tek sınıf + saf mantık mı | Birim (`unit/`) | 2. adıma geç |
| 2 | Route/HTTP nesnesi okunuyor mu? | Method/uri eşleşmesi var mı | Entegrasyon (`Api`) | 3. adıma geç |
| 3 | Veritabanına yazılıyor mu? | Depo/transaction var mı | Entegrasyon (`Repository`) | 4. adıma geç |
| 4 | Dosya sistemi kullanılıyor mu? | Log/dizin erişimi var mı | İkisi de — izolasyon şart (§6) | 5. adıma geç |
| 5 | Gerçek ağ çağrısı var mı? | Socket/HTTP istemcisi | E2E (`e2e/`) | Birim (varsayılan) |

**Kural 1:** karar akışında ilk eşleşen satır bağlayıcıdır; sonraki satırlara bakılmaz.
**Kural 2:** karar belirsizse test **entegrasyon** sayılır — dar kapsamlı yanlış etiketleme,
suitin tamamını yavaşlatır; geniş etiketleme tek testi yavaşlatır.
**Kural 3:** bir testin iki suitte birden çalışması yasaktır (çift rapor, çift sayaç).

## §5 Ortam katmanları

Entegrasyon ortamı üç katmandır; üst katman alt katmanı **değiştirmez**, yalnız üzerine kurulur.

| # | Katman | İçerik | Kim kullanır | Oluşturma | Var mı? |
|---|---|---|---|---|---|
| 1 | Yerel | `composer test` + yerel PHP | Geliştirici | Manuel | ✅ composer.json Satır 47 |
| 2 | CI | Ubuntu runner + composer install | GitHub Actions | Otomatik | ✅ ci.yml Satır 100-108 |
| 3 | İzole konteyner | Ayrı servis ağı | — | — | ❌ yok (⚠️ VERIFICATION REQUIRED) |

### §5.1 CI ortamı adımları

| # | Adım | ci.yml satırı | Ne yapar | Bağımlılık |
|---|---|---|---|---|
| 1 | Checkout | 82 | Kodu indirir | — |
| 2 | Setup PHP | 84-90 | PHP + eklentiler | apcu, mbstring, json, pdo, pdo_mysql (Satır 89) |
| 3 | Cache Composer | 92-98 | Bağımlılık önbelleği | lock dosyası (Satır 96) |
| 4 | Composer install | 100-102 | Bağımlılıkları kurar | `working-directory: matrix.project` |
| 5 | PHPUnit | 104-108 | Testleri koşar | `PHPUNIT_ARGS: matrix.args` |

### §5.2 Yerel ortam adımları

| # | Adım | Komut | Nerede | Beklenen |
|---|---|---|---|---|
| 1 | Bağımlılık | `composer install` | `shared/` | vendor oluşur |
| 2 | Tam suit | `composer test` | `shared/` | 8 suit koşulur |
| 3 | Tek suit | `vendor/bin/phpunit --testsuite Api` | `shared/` | yalnız Api |
| 4 | Tek dosya | `vendor/bin/phpunit tests/Api/RouteTableTest.php` | `shared/` | tek dosya |
| 5 | Statik analiz | `composer stan` | `shared/` | level 5 temiz |

## §6 Veritabanı izolasyonu ve reset

Her test, **önceki testin bıraktığı durumdan bağımsız** olmalıdır. Depo testleri için bu, ya
işlemi (transaction) geri alma ya da test başına temiz tabloyla başlama demektir.

| # | Strateji | Mekanizma | Hız | Risk | Repo karşılığı |
|---|---|---|---|---|---|
| 1 | Transaction rollback | Test transaction'ı açar, sonunda `rollBack` | hızlı | Bazı sürümler commit gerektirir | `SqliteDatabaseManager` Satır 44-54 |
| 2 | Test başına temizlik | Truncate + seed | yavaş | Yavaşlık | tespit edilmedi |
| 3 | Bellek içi (in-memory) | Ayrı DB örneği | hızlı | Sürüm farkı | SQLite yardımcısı (Repository testleri) |
| 4 | Konteyner + temizlik | Yeni konteyner her suitte | yavaş | Altyapı yok | depoda yok |

### §6.1 Transaction API'si — repo kanıtı

`SqliteDatabaseManager` dört işlem methodu tanımlar; entegrasyon testleri bu methodlarla izolasyon
kurar:

| # | Method | Satır | Görev | Testte kullanımı |
|---|---|---|---|---|
| 1 | `beginTransaction()` | 44 | İşlemi başlatır | Test başında |
| 2 | `commit()` | 49 | İşlemi onaylar | Onay testi |
| 3 | `rollBack()` | 54 | İşlemi geri alır | Test sonunda (reset) |
| 4 | `lastInsertId()` | 39 | Son kimliği döner | Kayıt doğrulaması |

**Dosya: `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` / Satır: 44 · 49 · 54**

### §6.2 Reset kuralları

| # | Kural | Zorunlu mu | İhlal belirtisi | Düzeltme |
|---|---|---|---|---|
| 1 | Her test kendi başlangıcını kurar | evet | Sıra değişince kırılma | §6.1 |
| 2 | Kalıcı kayıt bırakılmaz | evet | İkinci koşumda çakışma | Rollback |
| 3 | Kimlik (id) beklentisi sabitlenmez | evet | Sıra bağımlı assert | Değil kimlik, davranış assert et |
| 4 | Seed verisi dosyada sabit | evet | Farklı sonuç | Sabit fixture |
| 5 | Test sonrası tohum temizliği | evet | Birikim | Reset adımı |

### §6.3 İzolasyon denetim tablosu

| # | Denetim | Yöntem | Kabul | Durum |
|---|---|---|---|---|
| 1 | Testler tek tek koşulabilir mi | `--filter` | yeşil | manuel |
| 2 | Suit iki kez koşulabilir mi | art arda iki koşum | aynı sonuç | ⚠️ VERIFICATION REQUIRED |
| 3 | Sıra değiştirilince sonuç aynı mı | Rastgele sıralama | aynı sonuç | ⚠️ VERIFICATION REQUIRED |
| 4 | Kalıcı dosya/DB kaydı mı? | Koşu sonrası kontrol | temiz | ⚠️ VERIFICATION REQUIRED |
| 5 | CI ile yerel aynı mı? | Aynı suit iki ortamda | aynı sonuç | kısmen (ci.yml Satır 104) |

## §7 HTTP sözleşmesi doğrulaması

Entegrasyon testi, yalnızca "çalışıyor mu" değil, **sözleşmeye uyuyor mu** sorusunu yanıtlar:
durum kodu, zorunlu başlık ve gövde şekli.

| # | Durum | Ne anlama gelir | Repo kaynağı | Test kanıtı |
|---|---|---|---|---|
| 1 | 200 | Başarı | RouteTableTest Satır 42 (`STATUS_OK`) | ✅ |
| 2 | 401 | Oturum yok | routes.php Satır 41 (auth/me 200/401) | ✅ |
| 3 | 403 | Yasak (origin/CORS) | OriginCheckMiddlewareTest Satır 54 | ✅ |
| 4 | 405 | Method izinli değil + `Allow` | Gateway.php Satır 125-135 | ✅ (kod kanıtı) |
| 5 | 422 | Doğrulama hatası | ValidationMiddleware.php Satır 74 | ✅ (kod kanıtı) |
| 6 | 429 | Hız sınırı aşıldı | RateLimiterMiddlewareTest Satır 70 | ✅ |
| 7 | 404 | Bulunamadı | Gateway 405/404 ayrımı (Satır 105) | ✅ (kod kanıtı) |

### §7.1 405 sözleşmesi

| # | Bileşen | Dosya / Satır | Ne yapar |
|---|---|---|---|
| 1 | Route tablosu | `api.coremusic.net/config/routes.php` / Satır 22 | 405 + Allow davranışını belgeler |
| 2 | Gateway | `shared/src/Api/Gateway.php` / Satır 117 | "Yol biliniyor, method sunulmuyor → 405 + Allow" |
| 3 | Gateway yanıtı | `shared/src/Api/Gateway.php` / Satır 125-135 | `ApiResponse::error` çağrısı `METHOD_NOT_ALLOWED` + 405, ardından `Allow` başlığı |
| 4 | Public uçlar | `api.coremusic.net/config/routes.php` / Satır 36-39 | 4 uç POST-only → GET 405 |

**Doğrulama kuralı:** 405 testi **yalnız durum kodunu değil**, `Allow` başlığının gerçekten
sunulan methodları içerdiğini de assert etmelidir (Gateway Satır 119-121: `Allow` yalnızca
`implemented` route'ları listeler).

### §7.2 422 sözleşmesi

| # | Bileşen | Dosya / Satır | Ne yapar |
|---|---|---|---|
| 1 | Middleware | `shared/src/Middleware/ValidationMiddleware.php` / Satır 74 | `httpStatus: 422` üretir |
| 2 | İstisna | `shared/src/Exception/ValidationException.php` / Satır 12 | `parent::__construct($message, $errorCode, 422)` |
| 3 | Kurallar | `api.coremusic.net/config/routes.php` / Satır 61-63 | `required`, `min` = 8, `in` (male, female, neutral) |

**Doğrulama kuralı:** 422 testi, **hangi alanın** hatalı olduğunu gövdeden okur; yalnız kodu
assert etmek yetersizdir (§8.2 anti-desen 5).

### §7.3 Sözleşme test matrisi

| # | Senaryo | Beklenen kod | Ek doğrulama | Suit | Durum |
|---|---|---|---|---|---|
| 1 | GET + POST-only uç | 405 | `Allow: POST, OPTIONS` | Api | kod kanıtı var, test kapsamı ⚠️ |
| 2 | Geçersiz gövde | 422 | Hatalı alan adı | Api | kod kanıtı var, test kapsamı ⚠️ |
| 3 | Hız sınırı aşımı | 429 | Hata kodu `rate_limit_exceeded` | Middleware | test var (Satır 70-71) |
| 4 | Yasak origin | 403 | Reddedilen origin | Middleware | test var (Satır 54) |
| 5 | Oturumsuz istek | 401 | auth/me uçları | Api | routes.php Satır 41 |
| 6 | Bilinmeyen yol | 404 | Ayrım 405/404 | Api | Gateway Satır 105 |

### §7.4 Sözleşme doğrulama şablonu (yeni test)

| # | Şablon adımı | Zorunlu alan | Örnek beklenen | Denetim |
|---|---|---|---|---|
| 1 | İstek senaryosu | Method + yol | `GET /api/v1/auth/login` | §7.3 |
| 2 | Beklenen durum kodu | Tek kod | 405 | §7.1 |
| 3 | Zorunlu başlık | İlgili başlık | `Allow` | §7.1 |
| 4 | Başlık değeri | Liste içeriği | `POST, OPTIONS` | Gateway Satır 119-121 |
| 5 | Gövde şekli | `error` alanı | `METHOD_NOT_ALLOWED` | Gateway Satır 130 |
| 6 | Hata kodu | Sabit dize | `rate_limit_exceeded` (429) | RateLimiter Satır 71 |
| 7 | Uygun suit | Seçim | Api / Middleware | §4.3 |

**Kural:** 2, 3 ve 5 numaralı alanların **üçü birden** assert edilmedikçe sözleşme testi tam
sayılmaz; yalnız durum kodu doğrulamak başlık ve gövde sözleşmesini kaybettirir (§11 yasak 4).

## §8 CI entegrasyonu — testsuite matrisi

### §8.1 Matris

| # | Proje | `args` | Kaç suit koşulur | ci.yml satırı | Not |
|---|---|---|---|---|---|
| 1 | shared | boş (tam suit) | 8 | 75-76 | Unit + Api + Repository + Events + Security + Middleware + Component + OAuth |
| 2 | auth.coremusic.net | `--testsuite Unit` | 1 | 79-80 | Integration suiti diskte olmadığı için |

### §8.2 Dosya içi yorum — ci.yml Satır 77-78

```yaml
# auth phpunit.xml testsuites: Unit + Integration; tests/Integration dizini diskte YOK
# (glob kaniti) -> yalnizca Unit kosulur, failOnWarning kirilmasin.
```

Bu iki satır, CI'ın **bilinçli olarak** daraltıldığını kanıtlar: `tests/Integration` dizini yoksa
PHPUnit 10 uyarı üretir ve `failOnWarning=true` (auth phpunit.xml Satır 7) koşuyu düşürür.

### §8.3 `Integration` suitinin mevcut durumu

| # | Gerçek | Kanıt | Sonuç | Durum |
|---|---|---|---|---|
| 1 | auth Integration suite kaldırıldı | auth phpunit.xml Satır 10-12 | Yalnız Unit | bilinçli karar (2026-09-29) |
| 2 | Kaldırma nedeni | "Test directory not found" + exit 2 | Tüm testler başlamıyordu | Satır 11 |
| 3 | Geri ekleme koşulu | Yeni integration testleri | Suite geri eklenebilir | Satır 12 |
| 4 | shared entegrasyon testleri | phpunit.xml Satır 12-32 | Api/Repository/Middleware suitleri mevcut | ✅ |
| 5 | Ortak `Integration` adlı suit | — | Hem shared hem auth'ta ayrı adla yok | ⚠️ VERIFICATION REQUIRED |

### §8.4 Uyarı/risk kapıları

| # | Kapı | Dosya / Satır | Etki | Kapsam |
|---|---|---|---|---|
| 1 | `failOnRisky=true` | auth phpunit.xml / Satır 6 | Riskli test → çıkış 1 | auth |
| 2 | `failOnWarning=true` | auth phpunit.xml / Satır 7 | Uyarı → çıkış 1 | auth |
| 3 | `failOnPhpunitWarning=false` | auth phpunit.xml / Satır 8 | PHPUnit kendi uyarısı düşürmez | auth |
| 4 | `stopOnFailure=false` | shared phpunit.xml / Satır 6 | İlk hatada durmaz, tüm rapor | shared |

### §8.5 Suit bazında test dosyası dağılımı (2026-10-12 sayımı)

`shared/tests` altında toplam **44** PHP dosyası vardır; dizin başına dağılım aşağıdaki gibidir.
Bu sayım, §8.1'deki shared tam suitinin neyi kapsadığını somutlaştırır.

| # | Dizin | Dosya | Suite | Not |
|---|---|---|---|---|
| 1 | `Api` | 8 | Api | Route, CORS, versiyonlama, DTO |
| 2 | `Component` | 1 | Component | Şablon motoru |
| 3 | `Events` | 2 | Events | Olay dağıtıcı |
| 4 | `Middleware` | 9 | Middleware | CSRF, rate limit, auth, origin |
| 5 | `OAuth` | 1 | OAuth | OAuthManager |
| 6 | `Repository` | 2 | Repository | ApiKey depo testleri |
| 7 | `Repository/Sqlite` | 2 | Repository | Yardımcı sınıf (test değil) |
| 8 | `Security` | 2 | Security | JWT, return-url |
| 9 | `Unit/*` | 15 | Unit | Config, Device, Log, PageRouter, Security, Theme |
| 10 | `Fixtures/chrome` | 2 | — | Fixture (test değil; header/footer) |

**Toplam:** 8 + 1 + 2 + 9 + 1 + 2 + 2 + 2 + 15 + 2 = **44** (`[IO.File]` sayımı, 2026-10-12).
`Fixtures` ve `Repository/Sqlite` içindeki 4 dosya test sınıfı değildir; PHPUnit bunları suit
sayımına almaz (dizin seçimi `phpunit.xml` Satır 9-33).

## §9 Kod örnekleri — repo gerçek kodu

### §9.1 405 üretimi — Gateway.php Satır 125-137

```php
private function methodNotAllowedResponse(array $allow): array
{
    $header = implode(', ', $allow);

    $response = ApiResponse::error(
        'METHOD_NOT_ALLOWED',
        'Method not allowed for this endpoint',
        405,
        ['allow' => $header]
    );
    $response['headers'] = ['Allow' => $header];

    return $response;
}
```

**Dosya: `shared/src/Api/Gateway.php` / Satır: 125**

### §9.2 Route sözleşmesi — routes.php Satır 36-39

```php
'/api/v1/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
'/api/v1/auth/register'       => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],
```

`implemented: false` olan uçlara gelen GET isteği 405 döner (routes.php Satır 35 yorumu:
"GET implemented=false → 405 + Allow: POST, OPTIONS").

**Dosya: `api.coremusic.net/config/routes.php` / Satır: 36 · 39**

### §9.3 Hız sınırı sözleşmesi — RateLimiterMiddlewareTest Satır 65-72

```php
$result = $middleware->handle(
    ['method' => 'GET', 'server' => ['REMOTE_ADDR' => '192.168.1.1']],
    fn(array $req) => ['status' => 200]
);

$this->assertSame(429, $result['httpStatus']);
$this->assertSame('rate_limit_exceeded', $result['body']['error']);
$this->assertSame('60', $result['headers']['Retry-After']);
```

Test başlığı `testRateLimitExceededReturns429` Satır 52'dedir; mock, limit aşımını Satır 54-57'de
kurar (`increment => 61`). Üç assertion da sözleşmenin tamamını (kod + hata + başlık) doğrular.

**Dosya: `shared/tests/Middleware/RateLimiterMiddlewareTest.php` / Satır: 52 · 54 · 70 · 71 · 72**

### §9.4 CORS sağlamlaştırma — ApiPipelineSecurityTest Satır 47-49

```php
$this->assertStringContainsString('Authorization', $allowedHeaders);
$this->assertStringContainsString('X-Api-Key', $allowedHeaders);
$this->assertSame('https://auth.coremusic.net', $response['headers']['Access-Control-Allow-Origin'] ?? null);
```

**Dosya: `shared/tests/Api/ApiPipelineSecurityTest.php` / Satır: 47 · 48 · 49**

### §9.5 Çok-dilli karşılıklar (12 dil)

> Entegrasyon testi omurgası (test sunucusu + DB fixture + rollback) 12 dilde — repo standardı
> PHP/PHPUnit'tur (§8.5). Her örnek: Arrange (ortam+fixture) → Act (çağrı) → Assert (sözleşme)
> → teardown (reset). Sürüm iddiası yazılmaz (bilgi amaçlı).

| # | Dil | Entegrasyon aracı | Komut |
|---|---|---|---|
| 1 | PHP | PHPUnit + gerçek HTTP istemci | `vendor/bin/phpunit --testsuite Api` |
| 2 | TypeScript | Vitest + fetch | `npx vitest run ortam.test.ts` |
| 3 | Python | pytest + TestClient/requests | `pytest test_ortam.py -v` |
| 4 | C# | xUnit + WebApplicationFactory | `dotnet test --filter OrtamTesti` |
| 5 | Java | JUnit 5 + RestAssured | `mvn test -Dtest=OrtamTesti` |
| 6 | Go | `net/http/httptest` + `database/sql` | `go test -run TestOrtam ./...` |
| 7 | C++ | Google Test + REST istemci (libcurl) | `./testler --gtest_filter=OrtamTesti.*` |
| 8 | Rust | cargo test + `reqwest` + `sqlx` | `cargo test ortam` |
| 9 | Ruby | RSpec + Rack::Test | `bundle exec rspec spec/ortam_spec.rb` |
| 10 | Kotlin | JUnit 5 + Ktor test host | `gradle test --tests OrtamTesti` |
| 11 | Swift | XCTest + URLSession (yerel sunucu) | `swift test --filter OrtamTesti` |
| 12 | C | Unity + libcurl + ham HTTP | `./testler` (unity_runner) |

**PHP (PHPUnit — repo deseni: §9.1-§9.3):**

```php
final class KapiOrtamTesti extends TestCase
{
    protected function setUp(): void
    {
        $this->db->beginTransaction();             // Arrange: izole transaction
        $this->seedOrtam();                        // Arrange: fixture
    }

    protected function tearDown(): void
    {
        $this->db->rollBack();                     // Teardown: izolasyon
    }

    public function testGecersizYontem405Doner(): void
    {
        $yanit = $this->istemci->delete('/api/v1/oturum'); // Act
        $this->assertSame(405, $yanit->durumKodu());       // Assert
        $this->assertSame('GET', $yanit->baslik('Allow')); // Assert
    }
}
```

**TypeScript (Vitest + fetch):**

```ts
import { beforeEach, afterEach, it, expect } from 'vitest';
import { db } from './db';

beforeEach(async () => { await db.begin(); await db.seedOrtam(); }); // Arrange
afterEach(async () => { await db.rollback(); });                     // Teardown

it('geçersiz yöntem 405 döner', async () => {
  const yanit = await fetch(ASAL + '/api/v1/oturum', { method: 'DELETE' }); // Act
  expect(yanit.status).toBe(405);                                              // Assert
  expect(yanit.headers.get('allow')).toBe('GET');                             // Assert
});
```

**Python (pytest + requests):**

```python
import pytest, requests

@pytest.fixture()
def ortam(db):                      # Arrange: fixture + transaction
    db.begin(); db.seed()
    yield db
    db.rollback()                   # Teardown

def test_gecersiz_yontem_405(ortam, asal_url):
    yanit = requests.delete(f"{asal_url}/api/v1/oturum")   # Act
    assert yanit.status_code == 405                         # Assert
    assert yanit.headers["Allow"] == "GET"                  # Assert
```

**C# (xUnit + WebApplicationFactory):**

```csharp
public class KapiOrtamTesti : IClassFixture<OrtamFabrikasi>
{
    private readonly HttpClient _istemci;
    public KapiOrtamTesti(OrtamFabrikasi fabrika) =>
        _istemci = fabrika.CreateClient();               // Arrange

    [Fact]
    public async Task GecersizYontem405Doner()
    {
        var yanit = await _istemci.DeleteAsync("/api/v1/oturum"); // Act
        Assert.Equal(405, (int)yanit.StatusCode);                // Assert
        Assert.Equal("GET", yanit.Headers.Allow?.ToString());    // Assert
    }
}
```

**Java (JUnit 5 + RestAssured):**

```java
@ExtendWith(OrtamExtension.class)                       // Arrange: DB + sunucu
class KapiOrtamTesti {
    @Test
    void gecersizYontem405Doner() {
        given().when().delete("/api/v1/oturum")          // Act
            .then().statusCode(405)                      // Assert
            .header("Allow", "GET");                     // Assert
    }
}
```

**Go (httptest + database/sql):**

```go
func TestGecersizYontem405Doner(t *testing.T) {
    tx, _ := db.Begin()                                  // Arrange
    defer tx.Rollback()                                  // Teardown
    seedOrtam(tx)

    req := httptest.NewRequest(http.MethodDelete, "/api/v1/oturum", nil)
    ro := httptest.NewRecorder()
    api.Gateway(ro, req)                                 // Act

    if ro.Code != 405 {                                  // Assert
        t.Fatalf("Code = %d, want 405", ro.Code)
    }
}
```

**C++ (Google Test + libcurl):**

```cpp
TEST(OrtamTesti, GecersizYontem405Doner) {
    Ortam ortam;                                        // Arrange: fixture
    CURL *curl = curl_easy_init();
    curl_easy_setopt(curl, CURLOPT_URL, (ASAL + "/api/v1/oturum").c_str());
    curl_easy_setopt(curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_easy_perform(curl);                             // Act
    long kod; curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &kod);
    EXPECT_EQ(kod, 405);                                 // Assert
}
```

**Rust (cargo test + reqwest + sqlx):**

```rust
#[tokio::test]
async fn gecersiz_yontem_405_doner() {
    let mut tx = db::begin().await;                       // Arrange
    seed_ortam(&mut tx).await;

    let yanit = reqwest::Client::new()
        .delete(format!("{ASAL}/api/v1/oturum"))
        .send().await.unwrap();                           // Act
    assert_eq!(yanit.status().as_u16(), 405);             // Assert
    tx.rollback().await;                                  // Teardown
}
```

**Ruby (RSpec + Rack::Test):**

```ruby
RSpec.describe 'Kapı ortamı', type: :request do
  before { db.begin; seed_ortam }      # Arrange
  after  { db.rollback }               # Teardown

  it 'geçersiz yöntem 405 döner' do
    delete '/api/v1/oturum'                                   # Act
    expect(response).to have_http_status(405)                 # Assert
    expect(response.headers['Allow']).to eq('GET')            # Assert
  end
end
```

**Kotlin (JUnit 5 + Ktor test host):**

```kotlin
@ExtendWith(OrtamExtension::class)                       // Arrange
class KapiOrtamTesti {
    @Test
    fun `gecersiz yontem 405 doner`() = testApplication {
        val yanit = client.delete("/api/v1/oturum")       // Act
        assertEquals(405, yanit.status.value)             // Assert
        assertEquals("GET", yanit.headers["Allow"])       // Assert
    }
}
```

**Swift (XCTest + URLSession):**

```swift
final class OrtamTesti: XCTestCase {
    func testGecersizYontem405Doner() throws {
        var ist = URLRequest(url: URL(string: "\(asal)/api/v1/oturum")!)
        ist.httpMethod = "DELETE"
        let bekleme = expectation(description: "yanıt")
        URLSession.shared.dataTask(with: ist) { _, kod, _ in   // Act
            XCTAssertEqual(kod, 405)                            // Assert
            bekleme.fulfill()
        }.resume()
        wait(for: [bekleme], timeout: 5)
    }
}
```

**C (Unity + libcurl):**

```c
void test_gecersiz_yontem_405_doner(void) {
    CURL *curl = curl_easy_init();                        /* Arrange */
    curl_easy_setopt(curl, CURLOPT_URL, ASAL "/api/v1/oturum");
    curl_easy_setopt(curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_easy_perform(curl);                              /* Act */
    long kod; curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &kod);
    TEST_ASSERT_EQUAL_INT(405, kod);                     /* Assert */
}
```

## §10 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | shared/phpunit.xml | 8-33 | Sekiz testsuite tanımı |
| 2 | shared/phpunit.xml | 6 | `stopOnFailure=false` — tam rapor |
| 3 | shared/phpunit.xml | 34-38 | Kapsam kaynağı `src` |
| 4 | auth.coremusic.net/phpunit.xml | 6-7 | `failOnRisky` + `failOnWarning` kapısı |
| 5 | auth.coremusic.net/phpunit.xml | 10-12 | Integration suite kaldırma gerekçesi |
| 6 | auth.coremusic.net/phpunit.xml | 14-16 | Tek kalan Unit suite |
| 7 | api.coremusic.net/config/routes.php | 22 | 405 + Allow sözleşmesi (belge) |
| 8 | api.coremusic.net/config/routes.php | 36-39 | Dört POST-only uç (`implemented:false`) |
| 9 | api.coremusic.net/config/routes.php | 41-48 | auth/me: 200/401 sözleşmesi |
| 10 | api.coremusic.net/config/routes.php | 61-63 | Doğrulama kuralları (required, min 8, in) |
| 11 | shared/src/Api/Gateway.php | 117 | 405 + Allow kuralı (yorum) |
| 12 | shared/src/Api/Gateway.php | 125-137 | 405 yanıt üretimi + Allow başlığı |
| 13 | shared/src/Middleware/ValidationMiddleware.php | 74 | 422 yanıt kodu |
| 14 | shared/src/Exception/ValidationException.php | 12 | 422 istisna kodu |
| 15 | shared/tests/Middleware/RateLimiterMiddlewareTest.php | 70-71 | 429 + hata kodu assertion'ı |
| 16 | shared/tests/Middleware/OriginCheckMiddlewareTest.php | 54 | 403 reddi |
| 17 | shared/tests/Api/ApiPipelineSecurityTest.php | 47-49 | CORS başlık doğrulamaları |
| 18 | shared/tests/Repository/Sqlite/SqliteDatabaseManager.php | 44-54 | Transaction API'si |
| 19 | .github/workflows/ci.yml | 74-80 | Suit matrisi (shared tam, auth Unit) |
| 20 | .github/workflows/ci.yml | 104-108 | PHPUnit adımı + `PHPUNIT_ARGS` |

## §11 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Birim testi entegrasyon suitine koymak | Sınır bulanıklaşır | Kod incelemesi |
| 2 | Testte gerçek ağ çağrısı | Yavaş + kararsız | Kod incelemesi |
| 3 | Kalıcı veritabanı kaydı bırakmak | Sonraki testi bozar | §6.3 denetimleri |
| 4 | 405/422 testinde yalnız kod assert etmek | Başlık/gövde sözleşmesi kaybolur | §7.1-§7.2 |
| 5 | `failOnWarning`'i kapatarak düzeltme | Kök neden gizlenir | auth phpunit.xml Satır 7 denetimi |
| 6 | `tests/Integration` olmadan suit eklemek | PHPUnit exit 2 → tüm testler başlamaz | auth phpunit.xml Satır 11 |
| 7 | Ortam değişkeni ile gizli davranış | Yerel/CI farkı | §5.1 |
| 8 | Ortak fixture'ı birim ile paylaşmak | Sınır taşması | §4.1 |

## §12 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K18-testing/unit | Birim yeşili | Entegrasyon sırası | CI sırası | mevcut (ci.yml needs) |
| 2 | K18-testing/coverage | Entegrasyon kapsamı | Kapsam girdisi | `source` bloğu | mevcut (phpunit.xml Satır 34) |
| 3 | K18-testing/performance | Sözleşme sürekliliği | Yük testi öncesi yeşil | CI kapısı | kısmen |
| 4 | K18-testing/regression | Sözleşme baseline'ı | Regresyon kontrolü | Durum kodu seti | kısmen |
| 5 | K18-testing/e2e | Gerçek ağ senaryoları | Gerçek E2E | — | ayrık (in-process vs ağ) |
| 6 | K17-devops/pipeline | Test suiti seçimi | CI matrisi | `--testsuite` | mevcut (Satır 80) |
| 7 | K017-observability/logging | Log formatı | Log kayıtlı entegrasyon testi | — | ⚠️ VERIFICATION REQUIRED |

## §13 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Test konteyneri yok | Ortam farkı (yerel/CI) | orta | Konteyner kararı | ⚠️ VERIFICATION REQUIRED |
| 2 | auth Integration suite kapalı | auth entegrasyonu test edilmiyor | yüksek | Suite geri ekleme | açık (geri ekleme koşulu yazılı) |
| 3 | 405/422 test kapsamı doğrulanmadı | Sözleşme testi eksik olabilir | yüksek | Test envanteri | ⚠️ VERIFICATION REQUIRED |
| 4 | Reset stratejisi yazılı değil | Sıra bağımlılığı riski | orta | §6 kararı | açık |
| 5 | shared tam suit, CI yalnız Unit sanılıyor | Yanlış güven | orta | matris okuması | çürütüldü: ci.yml Satır 76 boş args |
| 6 | Repository suiti DB sağlayıcısı CI'da doğrulanmadı | CI'da farklı sonuç | orta | Ortam doğrulaması | ⚠️ VERIFICATION REQUIRED |
| 7 | Ortak `Integration` suit adı yok | Terminoloji karmaşası | düşük | Adlandırma kararı | açık |

## §14 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Suit | shared 8 suit | phpunit.xml sayımı | her güncelleme | Satır 8-33 |
| 2 | Kapı | risk/warning → çıkış 1 | auth phpunit.xml | her koşu | Satır 6-7 |
| 3 | Sözleşme | 405 + Allow doğrulaması | Test + kod | her PR | Gateway Satır 135 |
| 4 | Sözleşme | 422 alanı doğrulaması | Test | her PR | ValidationMiddleware Satır 74 |
| 5 | Izolasyon | İki art arda koşum aynı sonuç | Manuel koşu | haftalık | §6.3 |
| 6 | CI | 2 matrix girdisi yeşil | CI hattı | her PR | ci.yml Satır 74-80 |

## §15 Ortam kurulum şablonu (yeni entegrasyon testi)

| # | Şablon alanı | Zorunlu | Değer | Denetim |
|---|---|---|---|---|
| 1 | Uygun testsuite | evet | Unit · Api · Repository · Events · Security · Middleware · Component · OAuth | phpunit.xml |
| 2 | Katman sınırı | evet | ≥ 2 modül (§4.1) | Kod incelemesi |
| 3 | Durum sıfırlama | evet | §6 stratejisi | Test sonu kontrolü |
| 4 | Ağ yokluğu | evet | Süreç içi çağrı | Kod incelemesi |
| 5 | Sözleşme assert | evet | Kod + başlık + gövde | §7 |
| 6 | Determinism | evet | §7.3 sabit seed | §6.2 |
| 7 | CI matrisi | evet | suit adı | ci.yml |

## §16 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| PHPUnit 10.5 test organizasyonu, test dizinleri ve testsuite kavramını tanımlar | https://docs.phpunit.de/en/10.5/organizing-tests.html | 2026-10-12 | HTTP 200 doğrulandı |
| PHPUnit 10.5 yapılandırma belgesi `phpunit.xml` öğelerini ve test suiti tanımını açıklar | https://docs.phpunit.de/en/10.5/configuration.html | 2026-10-12 | HTTP 200 doğrulandı |
| MDN, 405 Method Not Allowed durumunu ve `Allow` başlığı zorunluluğunu tanımlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/405 | 2026-10-12 | HTTP 200 doğrulandı |
| MDN, 422 Unprocessable Content durumunu doğrulama hataları için tanımlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/422 | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 11 yeni
derin-branch dosyasında bulunmaz.

## §17 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | satır numarası doğrulama | 20 satır eşleşti | 2026-10-12 |
| 5 | Durum kodu iddiaları | kaynak kod taraması (405/422/429/403) | 4/4 doğrulandı | 2026-10-12 |
| 6 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 10 | group alanı | frontmatter | `kod` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
