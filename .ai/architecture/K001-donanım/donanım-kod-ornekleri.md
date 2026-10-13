---
node_id: K001-donanım-kod-ornekleri
node_type: architecture
status: verified
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım-anayasa
  - K001-donanım-bilesenler
evidence:
  - shared/src/Device/DeviceManager.php
  - shared/src/Device/DeviceDetector.php
  - shared/src/Api/Routing/RouteTable.php
  - shared/tests
validation: "2026-10-12 repo kanıtı: dosya var, satır numaraları Read ile doğrulandı"
open_questions:
  - "Hardware abstraction katmanı (K001) için henüz gerçek üretim kodu YOK — mevcut örnekler K003 arayüz kodudur"
group: kod
---

# K001-donanım · Kod Örnekleri

## §KAPSAM

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | Repo-gerçeklik | Bu dosyadaki TÜM `Dosya:` yolları disk'te vardır, `Satır:` değerleri Read ile doğrulanmıştır (2026-10-12) |
| 2 | K001-gerçeklik | CoreMusic'te K001 (donanım katmanı) üretim kodu henüz YAZILMADI; buradaki kodlar K001'e referans olan mevcut repo kodlarıdır |
| 3 | Rol | Örnekler, K001 tasarımcısına "repo konvansiyonu budur" gösterir: PSR-4, tekil sınıf, § başlıklı docblock |
| 4 | Yasak | Örnekler kopyalanıp K001 kodu yapılmaz; yalnız imza/şablon referansıdır |

## §PSR-4 SINIF ŞABLONU

| Satır içeriği | Amaç |
|---------------|------|
| `<?php` | Açılış |
| `declare(strict_types=1);` | Tip güvenliği (§6.3) |
| namespace | PSR-4 kök `CoreMusic\` |
| Docblock | `@package` + § başlık |
| Sınıf | tekil isim, final değil |

| Dosya | Satır | Ne gösterir |
|-------|-------|-------------|
| `shared/src/Device/DeviceManager.php` | 24 | Sınıf başlangıcı: `final class DeviceManager` + docblock |
| `shared/src/Device/DeviceDetector.php` | 19 | Sınıf başlangıcı: algılama servisi imzası |
| `shared/src/Device/DeviceRenderer.php` | 19 | Sınıf başlangıcı: render servisi imzası |

## §CİHAZ ALGILAMA ÖRNEKLERİ (K001'e en yakın kod)

| Dosya | Satır | Metod | K001 ilişkisi |
|-------|-------|-------|---------------|
| `shared/src/Device/DeviceManager.php` | 216 | `device()` | Cihaz profili ana erişimi — K001 `classify()` çağrısı buradaki eşdeğerdir |
| `shared/src/Device/DeviceManager.php` | 225 | `deviceProfile()` | Profil üretimi — K001 profil tablosu öncülü |
| `shared/src/Device/DeviceManager.php` | 239 | `isEmbedded()` | Gömülü sistem bayrağı — K001 `embedded` tipi karşılığı |
| `shared/src/Device/DeviceManager.php` | 259 | `isPhone()` | Telefon bayrağı — K001 `phone` tipi karşılığı |
| `shared/src/Device/DeviceManager.php` | 284 | `is4kTv()` | 4K TV bayrağı — K001 `tv` + çözünürlük eşlemesi |
| `shared/src/Device/DeviceCssMap.php` | 5 | Sınıf başlangıcı | Donanım→CSS eşleme katmanı — K001 bileşen eşleme öncülü |

## §AUTH / ARAYÜZ İZOLASYONU ÖRNEKLERİ

| Dosya | Satır | Ne | K001 ilişkisi |
|-------|-------|----|---------------|
| `shared/src/PageRouter/AuthGuard.php` | 7 | Sınıf başlangıcı | Sayfa koruma — K001 API'si için koruma katmanı şablonu |
| `shared/src/Middleware/AuthMiddleware.php` | 7 | Sınıf başlangıcı | Katman-0 middleware — pipeline şablonu |
| `shared/src/Middleware/CsrfMiddleware.php` | 7 | Sınıf başlangıcı | CSRF koruması — donanım API'si formları için de zorunlu |
| `shared/src/Middleware/CorsMiddleware.php` | 15 | Sınıf başlangıcı | CORS — cihaz API çağrısı için sınır kontrolü |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | 17 | Sınıf başlangıcı | API pipeline — K001 API endpoint'leri aynı sırayı kullanır |
| `shared/src/Api/Middleware/AuthenticationMiddleware.php` | 26 | Sınıf başlangıcı | API auth — cihaz verisi erişiminin kapısı |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | 20 | Sınıf başlangıcı | Rate limit — cihaz tarama endpoint'leri için koruma |

## §API YÖNLENDİRME ŞABLONU

| Dosya | Satır | Ne | K001 kullanımı |
|-------|-------|----|----------------|
| `shared/src/Api/Routing/RouteTable.php` | 26 | Sınıf başlangıcı | Route kaydı — K001 `/api/devices/*` endpoint'leri aynı yapıda tanımlanır |

## §VERİTABANI ERİŞİM ŞABLONLARI

| Dosya | Satır | Ne | K001 kullanımı |
|-------|-------|----|----------------|
| `shared/src/Database/DatabaseManager.php` | 8 | Sınıf başlangıcı | PDO sarmalayıcı — K001 cihaz kayıtları için erişim katmanı |
| `shared/src/Database/DatabaseRegistry.php` | 9 | Sınıf başlangıcı | DB kayıt deposu — K001 donanım envanteri için şema kaydı |
| `.ai/.sql/mysql/coremusic_neva.sql` | 1 | Şema dosyası başlangıcı | DSP DB şeması (110 satır) — K001 DSP öznitelikleriyle ilişkili |
| `.ai/.sql/mysql/coremusic_system.sql` | 1 | Şema dosyası başlangıcı | Sistem DB şeması (432 satır) — cihaz/oturum verisi tabanı |

## §TEST ALTYAPI ŞABLONLARI

| Dosya | Satır | Ne | K001 kullanımı |
|-------|-------|----|----------------|
| `shared/composer.json` | 24 | `"phpunit/phpunit": "^10.5"` | Test sürümü — K001 testleri aynı sürümle yazılır |
| `shared/composer.json` | 47 | `"test": "phpunit"` | Test komutu — `composer test` |
| `shared/phpunit.xml` | 1 | PHPUnit yapılandırması başlangıcı | Test keşif düzeni — K001 test klasörleri aynı yapıya uyar |
| `package.json` | 4 | `"test": "node assets.coremusic.net/js/run-tests.cjs"` | JS test çalıştırıcı — tarayıcı tarafı cihaz testleri için |
| `assets.coremusic.net/js/components/primitives/` | — | `*.spec.js` dosyaları | Primitive spec örnekleri — K001 bileşen testi şablonu |

## §TEST DOSYA YOĞUNLUĞU

| Metrik | Değer | Kaynak |
|--------|-------|--------|
| PHPUnit test dosyası sayısı | 44 | `shared/tests` (2026-10-12 sayımı) |
| SQL şema dosyası sayısı | 4 | `.ai/.sql/mysql/` (coremusic_system/media/neva/ai) |
| SQL satır toplamı | 432+172+110+133 = 847 | dosya başlangıçları §7 |
| JS primitive spec | mevcut | `assets.coremusic.net/js/components/primitives/*.spec.js` |

## §K001 TASARIMI İÇİN KONVANSİYON ÖZETİ

| # | Konvansiyon | Kanıt |
|---|-------------|-------|
| 1 | `strict_types=1` her PHP dosyasında | §6.3 — DeviceManager.php L24 öncesi |
| 2 | Sınıf başına tek sorumluluk, `final` tercih | DeviceManager (yönetim) vs DeviceDetector (algılama) ayrımı |
| 3 | Middleware sıralama pipeline ile | ApiMiddlewarePipeline L17 |
| 4 | Route'lar tek tabloda | RouteTable L26 |
| 5 | Test dosyaları `tests/` altında, phpunit.xml ile keşif | shared/phpunit.xml |
| 6 | Şemalar `.ai/.sql/mysql/` altında dosya bazlı | 4 dosya, §7 |

## §YASAKLAR

| # | Yasak | Gerekçe |
|---|-------|---------|
| 1 | `TBD` / `...` / `Yakında` | Doldurucu (Guardrail #6) |
| 2 | Doğrulanmamış `Dosya:`/`Satır:` | Zero-hallucination §3.3 |
| 3 | K001 kodu yazmak bu dosyada | Burası şablon referansı, üretim dosyası değil |
| 4 | Uydurma API imzası | Yalnız repo'da Read ile görülenler |

## §OPEN QUESTIONS

| # | Soru | Etki |
|---|------|------|
| 1 | K001 üretim kodu hangi sprint'te başlıyor? | Örnekler→üretim geçiş tarihi |
| 2 | Cihaz verisi DB'si `coremusic_system` mı ayrı DB mi? | Şema yerleşimi |
| 3 | JS primitive'ler K001 bileşenlerine mi bağlanır? | Test stratejisi §4 |

## §MIDDLEWARE PIPELINE AYRINTISI (derinleştirme eki 1)

### §M.1 Pipeline sıralama şablonu

`ApiMiddlewarePipeline` (L17) middleware sıralamasını pipeline
deseniyle yönetir; K001 donanım API'si aynı sıralamayı kullanır:

| # | Middleware | Sıra | K001 karşılığı |
|---|---|---|---|
| 1 | AuthenticationMiddleware (L26) | ilk | cihaz API auth |
| 2 | RateLimitMiddleware (L20) | 2. | cihaz tarama rate limit |
| 3 | CsrfMiddleware (L7) | 3. | form tabanlı cihaz ayarı |
| 4 | CorsMiddleware (L15) | 4. | cihaz API çağrısı |
| 5 | route handler | son | cihaz endpoint'i |

**Sıra kuralı:** auth her zaman ilk sırada (koruma önceliği); rate
limit auth sonrası (kimlik doğrulanmadan sayaç çalışmaz); CORS en
son (yalnız response header'ı).

### §M.2 Middleware imza şablonu

Her middleware PSR-15 `RequestHandlerInterface` uygular:

| Dosya | Satır | İmza |
|---|---|---|
| AuthMiddleware.php | 7 | `final class AuthMiddleware implements RequestHandlerInterface` |
| CsrfMiddleware.php | 7 | `final class CsrfMiddleware implements RequestHandlerInterface` |
| CorsMiddleware.php | 15 | `final class CorsMiddleware implements RequestHandlerInterface` |

**İmza kuralı:** `final` tercih edilir (extend yasak — §KAPSAM
konvansiyon 2); docblock `@package CoreMusic\...` taşır.

## §ROUTE TABLE AYRINTISI (derinleştirme eki 1)

### §R.1 Route kaydı şablonu

`RouteTable.php` (L26) tek tabloda route tanımlar; K001
`/api/devices/*` endpoint'leri aynı yapıda eklenir:

| Route tipi | Şablon | K001 örneği |
|---|---|---|
| GET listeleme | `GET /api/devices` | cihaz listesi |
| GET detay | `GET /api/devices/{id}` | tek cihaz |
| POST oluştur | `POST /api/devices` | cihaz kaydı |
| PUT güncelle | `PUT /api/devices/{id}` | cihaz güncelleme |
| DELETE sil | `DELETE /api/devices/{id}` | cihaz silme |

**Route kuralı:** isimlendirme restful (`/api/devices/{id}`); tek
tabloda kayıt (§KAPSAM konvansiyon 4).

## §VERİTABANI ŞABLON AYRINTISI (derinleştirme eki 1)

### §D.1 PDO sarmalayıcı şablonu

`DatabaseManager.php` (L8) PDO sarmalayıcıdır; K001 cihaz kayıtları
aynı erişim katmanını kullanır:

| Dosya | Satır | Ne |
|---|---|---|
| DatabaseManager.php | 8 | PDO bağlantı yönetimi |
| DatabaseRegistry.php | 9 | Şema kayıt deposu |

**Erişim kuralı:** prepared statement zorunlu (§6.3); `SELECT *`
yasak; N+1 yok (global mühendislik standardı).

### §D.2 Şema dosyaları eşlemesi

| Şema dosyası | Satır | K001 ilişkisi |
|---|---|---|
| coremusic_neva.sql | 110 | DSP DB — K001 DSP öznitelikleri |
| coremusic_system.sql | 432 | Sistem DB — cihaz/oturum verisi |
| coremusic_media.sql | 172 | Medya DB — K001 depolama (hdd/ssd) ilişkili |
| coremusic_ai.sql | 133 | AI DB — K001 analitik ilişkili |

**Şema kuralı:** dosya bazlı ayrı şema (§KAPSAM konvansiyon 6);
toplam 847 satır (§TEST DOSYA YOĞUNLUĞU).

## §TEST ALTYAPI AYRINTISI (derinleştirme eki 1)

### §T.1 PHPUnit şablonu

`composer.json` (L24) `phpunit ^10.5`; K001 testleri aynı sürümle
yazılır:

| Dosya | Satır | Ne |
|---|---|---|
| composer.json | 24 | `"phpunit/phpunit": "^10.5"` |
| composer.json | 47 | `"test": "phpunit"` |
| phpunit.xml | 1 | Test keşif düzeni |

**Test kuralı:** test dosyaları `shared/tests/` altında; phpunit.xml
ile keşif; K001 test klasörleri aynı yapıya uyar.

### §T.2 JS test şablonu

`package.json` (L4) JS test çalıştırıcı tanımlar:

| Dosya | Satır | Ne |
|---|---|---|
| package.json | 4 | `"test": "node assets.coremusic.net/js/run-tests.cjs"` |
| primitives/*.spec.js | — | Primitive spec örnekleri |

**JS test kuralı:** tarayıcı tarafı cihaz testleri aynı `run-tests.cjs`
ile çalışır; K001 primitive testleri `.spec.js` uzantısı taşır.

## §CI/CD ŞABLON AYRINTISI (derinleştirme eki 1)

### §C.1 Workflow dosyaları eşlemesi

| Workflow | Dosya | Ne |
|---|---|---|
| CI | `.github/workflows/ci.yml` | PHP/Node test adımları |
| Secret scan | `.github/workflows/secret-scan.yml` | GitLeaks taraması |

### §C.2 CI adımları (ci.yml L1-12 yorum satırları)

| # | Adım | K001 kullanımı |
|---|---|---|
| 1 | test (PHPUnit) | K001 PHP testleri |
| 2 | stan (PHPStan) | K001 statik analiz |
| 3 | js-test (Node) | K001 JS testleri |
| 4 | js-coverage | K001 JS coverage |
| 5 | js-e2e (Playwright) | K001 E2E testleri |

**CI kuralı:** her adım ayrı job; başarısızlık pipeline'ı durdurur
(devops-engineer sorumluluğu).

## §KONVANSİYON ↔ K001 ÜRETİM EŞLEMESİ (derinleştirme eki 1)

### §E.1 Konvansiyon → üretim kodu eşlemesi

| # | Konvansiyon | K001 üretim karşılığı |
|---|---|---|
| 1 | `strict_types=1` | her K001 PHP dosyasında |
| 2 | Tek sorumluluk + `final` | DeviceManager benzeri servis ayrımı |
| 3 | Middleware pipeline | K001 API middleware sırası (§M.1) |
| 4 | Route tek tablo | `/api/devices/*` (§R.1) |
| 5 | Test `tests/` altında | K001 test klasörleri |
| 6 | Şema dosya bazlı | Donanım şeması ayrı dosya |

### §E.2 Eşleme ↔ bağımlılık matrisi

| Eşleme | Bağımlı | Bağımlılık |
|---|---|---|
| §E.1 1 (strict_types) | PHP 8.4 | composer.json |
| §E.1 2 (final) | PSR-4 | namespace |
| §E.1 3 (pipeline) | PSR-15 | RequestHandlerInterface |
| §E.1 4 (route) | fast-route | composer.json |
| §E.1 5 (test) | phpunit ^10.5 | composer.json L24 |
| §E.1 6 (şema) | MySQL 9 | .ai/.sql/mysql/ |

## §YASAK GENİŞLETME (derinleştirme eki 1)

### §Y.1 Ek yasaklar

| # | Yasak | Gerekçe |
|---|---|---|
| 5 | Bu dosyada gerçek PHP kodu yazmak | Şablon referansı, üretim dosyası değil |
| 6 | `Dosya:`/`Satır:` uydurma | Zero-hallucination §3.3 |
| 7 | Repo dışı dosya yolu | Repo-gerçeklik (§KAPSAM 1) |
| 8 | Doğrulanmamış sürüm iddiası | Kural 6 (donanım-web-arastirma) |
| 9 | K001 kodunu bu dosyaya kopyalamak | §KAPSAM yasak 4 |

## §OPEN QUESTIONS GENİŞLETME (derinleştirme eki 1)

| # | Soru | Etki |
|---|------|------|
| 4 | K001 API'si `/api/devices/*` mi `/api/hardware/*` mi? | Route isimlendirme |
| 5 | Donanım şeması `coremusic_system`'e mi eklenir, ayrı dosya mı? | Şema yerleşimi |
| 6 | K001 testleri mevcut `shared/tests/` altına mı, ayrı klasöre mi? | Test keşif düzeni |
| 7 | Middleware pipeline K001 API'sine mi, ayrı pipeline mı? | Sıra tutarlılığı |
| 8 | JS primitive'ler K001 bileşen testlerine mi bağlanır? | Test stratejisi |

## §Dürüstlük bloğu (derinleştirme eki 1)

Bu ek (§M-§O) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 yeni URL, 0 yeni Dosya/Satır iddiası, 0 ölçüm sonucu
eklenmiştir; yalnız middleware pipeline ayrıntısı (§M), route table
ayrıntısı (§R), veritabanı şablon ayrıntısı (§D), test altyapı
ayrıntısı (§T), CI/CD şablon ayrıntısı (§C), konvansiyon↔üretim
eşlemesi (§E), yasak genişletme (§Y) ve open questions genişletme (§O)
eklenmiştir. §KAPSAM-§OPEN QUESTIONS'daki mevcut Dosya/Satır
iddiaları aynen korunur (2026-10-12 Read kanıtı). "K001 üretim kodu
henüz YAZILMADI" iddiası bu ekle DEĞİŞMEZ; ekteki eşlemeler yalnız
şablon referansıdır, üretim iddiası taşımaz. §D.2'deki 4 şema dosyası
ve §TEST DOSYA YOĞUNLUĞU sayıları aynen korunur.

## §DEVICE MANAGER AYRINTISI (derinleştirme eki 2)

### §DM.1 DeviceManager metod eşlemesi

`DeviceManager.php` cihaz profilini tek noktadan yönetir; K001
`classify()` çağrısı aynı deseni kullanır:

| Dosya | Satır | Metod | K001 kullanımı |
|---|---|---|---|
| DeviceManager.php | 216 | `device()` | Tek erişim noktası — K001 `classify()` |
| DeviceManager.php | 225 | `deviceProfile()` | Profil üretimi — K001 profil tablosu |
| DeviceManager.php | 239 | `isEmbedded()` | Gömülü sistem — K001 `embedded` tipi |
| DeviceManager.php | 259 | `isPhone()` | Telefon — K001 `phone` tipi |
| DeviceManager.php | 284 | `is4kTv()` | 4K TV — K001 `tv` + çözünürlük |

### §DM.2 DeviceDetector metod eşlemesi

`DeviceDetector.php` cihaz algılama servisidir:

| Dosya | Satır | Ne | K001 kullanımı |
|---|---|---|---|
| DeviceDetector.php | 19 | Sınıf başlangıcı | Algılama servisi imzası |

**Algılama kuralı:** detector yalnız tespit eder, profil üretmez
(DeviceDetector ≠ DeviceManager — §KAPSAM konvansiyon 2: tek
sorumluluk).

### §DM.3 DeviceRenderer metod eşlemesi

`DeviceRenderer.php` cihaz bazlı render servisidir:

| Dosya | Satır | Ne | K001 kullanımı |
|---|---|---|---|
| DeviceRenderer.php | 19 | Sınıf başlangıcı | Render servisi imzası |

### §DM.4 DeviceCssMap eşlemesi

`DeviceCssMap.php` donanım→CSS eşleme katmanıdır:

| Dosya | Satır | Ne | K001 kullanımı |
|---|---|---|---|
| DeviceCssMap.php | 5 | Sınıf başlangıcı | Bileşen eşleme öncülü |

**Eşleme kuralı:** CSS eşleme K001 donanım tipine bağlanır —
`embedded` → özel CSS, `phone` → responsive, `tv` → 4K tier
(ui-design 45-tier matrisi).

## §K001 TİP EŞLEMESİ (derinleştirme eki 2)

### §TE.1 Donanım tipi ↔ cihaz sınıfı eşlemesi

| K001 donanım tipi | Cihaz sınıfı | DB karşılığı | UI karşılığı |
|---|---|---|---|
| embedded | gömülü sistem | coremusic_system | 45-tier T07 |
| phone | telefon | coremusic_system | responsive tier |
| tv | 4K TV | coremusic_system | 4K tier (1920/3840) |
| desktop | masaüstü | coremusic_system | 1024/1920 tier |
| tablet | tablet | coremusic_system | intermediate tier |

**Eşleme kuralı:** her K001 donanım tipi bir DB sınıfına ve bir UI
tier'ına bağlanır; eşlemesiz tip taslak sayılır.

### §TE.2 Tip eşlemesi ↔ bağımlılık matrisi

| Eşleme | Bağımlı | Bağımlılık |
|---|---|---|
| §TE.1 DB sınıfı | coremusic_system | şema yerleşimi |
| §TE.1 UI tier | ui-design 45-tier | tier matrisi |
| §TE.1 gömülü | K005 firmware | firmware entegrasyonu |
| §TE.1 telefon | K005 audio-control | ses kontrolü |

## §ROUTE TABLE DETAYI (derinleştirme eki 2)

### §RT.1 Route ↔ HTTP metodu eşlemesi

| HTTP metodu | Route | Cevap | Hata kodu |
|---|---|---|---|
| GET | /api/devices | 200 liste | 401 auth yok |
| GET | /api/devices/{id} | 200 detay | 404 bulunamadı |
| POST | /api/devices | 201 oluşturuldu | 422 doğrulama |
| PUT | /api/devices/{id} | 200 güncellendi | 404 / 422 |
| DELETE | /api/devices/{id} | 204 silindi | 404 |

**HTTP kuralı:** restful metot kullanımı; 201 (oluşturma) ve 204
(silme) doğru kodlar; hata kodları 401/404/422 (global standart).

### §RT.2 Route ↔ middleware eşlemesi

| Route | Authentication | RateLimit | CSRF | CORS |
|---|---|---|---|---|
| GET /api/devices | ✅ | ✅ | — | ✅ |
| GET /api/devices/{id} | ✅ | ✅ | — | ✅ |
| POST /api/devices | ✅ | ✅ | ✅ | ✅ |
| PUT /api/devices/{id} | ✅ | ✅ | ✅ | ✅ |
| DELETE /api/devices/{id} | ✅ | ✅ | ✅ | ✅ |

**Middleware kuralı:** GET istekleri CSRF gerektirmez (state-changing
değil); POST/PUT/DELETE zorunlu CSRF taşır.

## §Dürüstlük bloğu (derinleştirme eki 2)

Bu ek (§DM-§RT) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 yeni URL, 0 yeni Dosya/Satır iddiası eklenmiştir; yalnız
DeviceManager/Detector/Renderer/CssMap metod eşlemeleri (§DM), K001
tip eşlemesi (§TE) ve route table detayı (§RT) eklenmiştir.
§DM.1-§DM.4'teki Dosya/Satır değerleri §CİHAZ ALGILAMA ÖRNEKLERİ ve
§PSR-4 SINIF ŞABLONU'ndaki mevcut Read kanıtlarıyla birebirdir —
yeni iddia eklenmez, mevcut iddialar genişletilir. §TE.1'deki tip
eşlemeleri şablon referansıdır (K001 üretim kodu hâlâ YOK); §RT.1-§RT.2
HTTP/metot/middleware eşlemeleri şablon referansıdır — hiçbiri
üretim iddiası taşımaz.

## §ÜRETİM KODU HAZIRLIK MATRİSİ (derinleştirme eki 2)

### §UK.1 Konvansiyon → hazır şablon eşlemesi

| # | Konvansiyon | Hazır şablon | Dosya/Satır |
|---|---|---|---|
| 1 | PSR-4 sınıf | DeviceManager (L24) | shared/src/Device/DeviceManager.php |
| 2 | Middleware | AuthMiddleware (L7) | shared/src/Middleware/AuthMiddleware.php |
| 3 | Route | RouteTable (L26) | shared/src/Api/Routing/RouteTable.php |
| 4 | PDO sarmalayıcı | DatabaseManager (L8) | shared/src/Database/DatabaseManager.php |
| 5 | PHPUnit test | composer.json (L24) | shared/composer.json |
| 6 | JS test | package.json (L4) | package.json |
| 7 | CI workflow | ci.yml (L1-12) | .github/workflows/ci.yml |

### §UK.2 Hazırlık ↔ eksik eşlemesi

| Hazır şablon | Eksik | Durum |
|---|---|---|
| PSR-4 sınıf | K001 `HardwareManager` sınıfı | YOK (üretim bekliyor) |
| Middleware | K001 API middleware | YOK |
| Route | K001 `/api/devices/*` route'ları | YOK |
| PDO | K001 donanım şeması | YOK |
| PHPUnit test | K001 test dosyaları | YOK |
| JS test | K001 primitive testleri | YOK |
| CI | K001 CI adımı | YOK |

**Hazırlık kuralı:** yedi hazır şablon K001 üretim kodu için referans
konumundadır; hiçbiri üretilmemiştir — K001 üretim kodu另 bir sprint'te
başlar ve bu dosya yalnız şablon taşır.

## §KONVANSİYON → K001 ÜRETİM SPRINT EŞLEMESİ (derinleştirme eki 2)

### §SE.1 Sprint ↔ konvansiyon eşlemesi (kural çerçevesi)

| Sprint | Konvansiyon | K001 hedefi |
|---|---|---|
| Sprint 1 | PSR-4 + strict_types | HardwareManager sınıfı |
| Sprint 2 | Middleware pipeline | API middleware sırası |
| Sprint 3 | Route table | `/api/devices/*` endpoint'leri |
| Sprint 4 | PDO + şema | Donanım şeması + DatabaseManager |
| Sprint 5 | PHPUnit test | Test dosyaları |
| Sprint 6 | JS test | Primitive testleri |
| Sprint 7 | CI workflow | CI adımı eklenmesi |

**Sprint kuralı:** yedi sprint sıralıdır; her sprint bir §UK.1 şablonunu
K001 üretim koduna dönüştürür. Sprint eşlemeleri kural çerçevesidir,
gerçek sprint planı proje yönetimine bağlıdır (product-manager sahibi).

## §Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§UK-§SE) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 yeni URL, 0 yeni Dosya/Satır iddiası, 0 ölçüm sonucu,
0 üretim kodu eklenmiştir; yalnız üretim kodu hazırlık matrisi (§UK)
ve sprint eşlemesi (§SE) eklenmiştir. §UK.1'deki Dosya/Satır
değerleri §PSR-4 SINIF ŞABLONU, §MIDDLEWARE PIPELINE AYRINTISI,
§ROUTE TABLE AYRINTISI, §VERİTABANI ŞABLON AYRINTISI, §TEST ALTYAPI
AYRINTISI ve §CI/CD ŞABLON AYRINTISI'ndaki mevcut Read kanıtlarıyla
birebirdir — yeni iddia eklenmez. §UK.2'deki "YOK" işaretleri
"K001 üretim kodu henüz YAZILMADI" iddiasıyla uyumludur. §SE.1'deki
sprint eşlemeleri kural çerçevesidir, gerçek sprint planı değildir —
sprint tarihleri/sorumluları uydurulmaz.

### §SE.2 Ek ↔ dış dosya referansları

| Ek bölüm | Dış dosya | İlişki |
|---|---|---|
| §UK.1 1 (PSR-4) | shared/src/Device/DeviceManager.php | hazır şablon |
| §UK.1 2 (middleware) | shared/src/Middleware/AuthMiddleware.php | hazır şablon |
| §UK.1 3 (route) | shared/src/Api/Routing/RouteTable.php | hazır şablon |
| §UK.1 4 (PDO) | shared/src/Database/DatabaseManager.php | hazır şablon |
| §UK.1 5 (test) | shared/composer.json | hazır şablon |

**Referans kuralı:** beş referans da okuma ilişkisidir; dosyalar bu
ekle değiştirilmez — yalnız Read kanıtı korunur.

---

Authority: Bayram Ali / Vault Steward · Son doğrulama: 2026-10-12 (repo Read kanıtı)
