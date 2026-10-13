---
node_id: K001-donanım-test-stratejisi
node_type: testing
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım-anayasa
  - K001-donanım-kod-ornekleri
evidence:
  - shared/phpunit.xml
  - shared/composer.json
  - shared/tests
validation: "2026-10-13: altyapı repo yeniden ölçülürdü (phpunit ^10.5 · 44 test dosyası · vitest L5 · playwright L8); dosya 112 → 500+ satıra genişletildi"
open_questions:
  - "K001 birim testleri hangi `shared/tests` alt klasörüne yerleşecek?"
group: kod
---

# K001-donanım · Test Stratejisi

## §KAPSAM

| # | Kural |
|---|-------|
| 1 | Testler K001 üretim kodu yazıldığında başlar; bu dosya strateji + mevcut altyapı kanıtıdır |
| 2 | Altyapı kanıtı repo Read ile doğrulandı (2026-10-12) ve yeniden ölçüldü (2026-10-13) |
| 3 | Hedef coverage %90+ (proje standardı) |
| 4 | Yeni web URL'leri yalnız envanter-temiz (url-envanter.txt 1673 satır), `.ai/**.md` içinde kullanılmamış ve HTTP 200 doğrulanmış kaynaklarla eklenir (2026-10-13 genişletme kararı) |
| 5 | Doğrulanamayan test iddiası `⚠️ VERIFICATION REQUIRED` olarak yazılır; sayı uydurulmaz |
| 6 | Bu dosyadaki her `Dosya:` / `Satır:` çifti Read ile doğrulanmıştır |

## §MEVCUT ALTYAPI

| # | Bileşen | Kanıt | Değer |
|---|---------|-------|-------|
| 1 | Test framework | `shared/composer.json` L24 | `phpunit/phpunit: ^10.5` |
| 2 | Test komutu | `shared/composer.json` L47 | `"test": "phpunit"` → `composer test` |
| 3 | Statik analiz komutu | `shared/composer.json` L48 | `"stan": "phpstan analyse src --level=5"` |
| 4 | Konfigürasyon | `shared/phpunit.xml` L1 | XML başlangıcı — test discovery düzeni (8 testsuites) |
| 5 | Test dosyaları | `shared/tests` | 44 PHP test dosyası (2026-10-13 sayımı) |
| 6 | JS test betiği | `package.json` L5 | `"test:js": "vitest run --root assets.coremusic.net"` |
| 7 | JS izleme modu | `package.json` L6 | `"test:js:watch": "vitest --root assets.coremusic.net"` |
| 8 | JS coverage betiği | `package.json` L7 | `"test:js:coverage": "vitest run --root assets.coremusic.net --coverage"` |
| 9 | E2E test betiği | `package.json` L8 | `"test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"` |
| 10 | JS spec örnekleri | `assets.coremusic.net/js/components/primitives/` | 6 adet `*.spec.js` (2026-10-13 sayımı) |

> **Düzeltme (2026-10-13):** Eski "JS test çalıştırıcısı `package.json L4`" satırı yanlıştı;
> ilgili betik diskte mevcut değildir ve iddia geri alınmıştır. Doğru kanıt:
> L4 `"scripts"` bloğu açılışı, L5 `test:js` (vitest), L8 `test:e2e` (playwright).

## §TEST PİRAMİDİ (K001)

| Katman | Kapsam | Araç | Hedef |
|--------|--------|------|-------|
| Birim | tip sınıflandırma, profil üretimi, harita eşleme | PHPUnit 10 (`composer test`) | %90+ satır |
| Birim (JS) | primitive bileşen davranışı | Vitest (`npm run test:js`) | spec yeşil |
| Entegrasyon | envanter API (salt-okunur) + DB şeması | PHPUnit + test DB | endpoint doğrulama |
| Kontrat | `/api/devices/*` yanıt şekli | PHPUnit JSON assert | şema stabilitesi |
| E2E (cihaz) | profil render (K003 iş birliği) | Playwright (`npm run test:e2e`) | kullanıcı görünümü |
| Yük | envanter okuma uçları | Grafana k6 (§YENİ-KAYNAK) | gecikme bütçesi |
| Donanım (fiziksel) | DAC/disk tezgâh testi | dalga-4 manuel | doğrulama zinciri |

## §BİRİM TEST SENARYOLARI

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | `classify(audio)` | `type: audio` |
| 2 | `classify(hdd)` | `type: hdd` |
| 3 | `classify(ssd)` | `type: ssd` |
| 4 | Bilinmeyen cihaz | `verify-required` durumu |
| 5 | `deviceProfile()` alanı eksik | Result::err (kural ihlali) |
| 6 | Harita dosyası eksik anahtar | fallback + uyarı |
| 7 | `isEmbedded/isPhone/is4kTv` çakışması | öncelik kuralı (kapsam §4) |
| 8 | `donanaım-type` literalinin korunması | string eşleşme (düzeltilmez) |
| 9 | `powerClass` alanı boş | varsayılan sınıf + işaretleme |
| 10 | Sürücü adı ASCII dışı karakter | reddet (dosya adı kuralı) |
| 11 | DC eşik değeri 0.5 V üzerinde | koruma rölesi tetikleme kaydı |
| 12 | 512 örnek tamponu ±1 sapma | bütçe ihlali uyarısı |

## §ENTEGRASYON SENARYOLARI

| # | Senaryo | Katman |
|---|---------|--------|
| 1 | Envanter endpoint'i auth'suz erişim → 401 | AuthenticationMiddleware L26 |
| 2 | Rate limit aşımı → 429 | RateLimitMiddleware L20 |
| 3 | CSRF token'sız POST → reddet | CsrfMiddleware L7 |
| 4 | Route kaydı → yanıt eşleşmesi | RouteTable L26 |
| 5 | Şema migrated mı → migrate kontrolleri | `.ai/.sql/mysql/` (20 dosya / 4345 satır) |
| 6 | CORS dışı origin → reddet | CorsMiddleware L15 |
| 7 | Katman sırası korunur | ApiMiddlewarePipeline L17 |
| 8 | Sayfa koruması → yönlendirme | AuthGuard L7 |

## §GATE TANIMI

| Gate | Koşul | Karar |
|------|-------|-------|
| Test gate | tüm testler yeşil | PASS/BLOCK |
| Coverage gate | %90+ (birim) | PASS/BLOCK |
| LINT gate | `.ai/CLAUDE.md` §6 | PASS/BLOCK |
| URL gate | benzersiz + 200/3xx/⚠️ işaretli | PASS/BLOCK |
| Yazım gate | `donanaım-type` bozulmamış | PASS/BLOCK |
| Kanıt gate | her `Dosya:`/`Satır:` Read ile doğrulanmış | PASS/BLOCK |
| Sürüm gate | uydurma sürüm sayısı 0 | PASS/BLOCK |

## §ÇALIŞTIRMA

| Adım | Komut | Not |
|------|-------|-----|
| 1 | `cd shared && composer test` | PHPUnit 10 (composer.json L47) |
| 2 | `npm run test:js` | Vitest (package.json L5) |
| 3 | `npm run test:e2e` | Playwright (package.json L8) |
| 4 | `npm run test:js:coverage` | Vitest coverage (package.json L7) |
| 5 | Repo kökünden `.ai/scripts/*.ps1` | vault gate'leri (yalnız kökten) |

## §YASAKLAR

| # | Yasak |
|---|-------|
| 1 | Yer tutucu kodlar, erteleme sözcükleri ve doldurucu ifadeler (Guardrail #6) |
| 2 | Doğrulanmamış test iddiası (coverage %90 YOKKEN "geçti" demek) |
| 3 | phpunit.xml edit — salt-okunur kanıt |
| 4 | K001 testini K003/K005 testiyle karıştırmak |
| 5 | Envantere girmemiş veya `.ai` içinde tekrar eden URL eklemek |
| 6 | Diskte olmayan dosyayı kanıt göstermek |

## §A TEST ALTYAPI ENVANTERİ (2026-10-13 ölçümü)

| # | Envanter satırı | Ölçüm | Kanıt |
|---|-----------------|-------|-------|
| A1 | PHP test dosyası | 44 | `shared/tests` recursive sayımı |
| A2 | `tests/Unit` alt klasörü | 7 | Config · Device · Log · Middleware · PageRouter · Security · Theme |
| A3 | `tests/Unit/Device` testleri | 3 | DeviceDetectorTest · DeviceRendererTest · WelcomePopupRenderTest |
| A4 | phpunit.xml testsuites | 8 | `shared/phpunit.xml` okuma |
| A5 | `shared/src/Middleware` sınıfı | 11 | dizin sayımı (PHP dosyası) |
| A6 | `shared/src/Api/Middleware` sınıfı | 7 | dizin sayımı (PHP dosyası) |
| A7 | `shared/src/Device` sınıfı | 4 | DeviceManager · DeviceDetector · DeviceRenderer · DeviceCssMap |
| A8 | Primitive spec dosyası | 6 | `assets.coremusic.net/js/components/primitives/*.spec.js` |
| A9 | SQL şema dosyası | 20 | `.ai/.sql/mysql/` — toplam 4345 satır |
| A10 | CI iş akışı adı | `CI Pipeline` | `.github/workflows/ci.yml` L8 |

## §B JS TEST KATMANI AYRINTISI

| # | Öğe | Değer | Kanıt |
|---|-----|-------|-------|
| B1 | Çalıştırıcı | Vitest 5.x (devDependency) | package.json L16 `vitest: ^5.0.3` |
| B2 | Coverage eklentisi | `@vitest/coverage-v8` | package.json L12 `^5.0.3` |
| B3 | DOM ortamı | jsdom | package.json L14 `jsdom: ^30.1.1` |
| B4 | E2E çerçevesi | `@playwright/test` | package.json L11 `^1.63.0` |
| B5 | E2E kök paket | playwright | package.json L15 `^1.63.0` |
| B6 | Erişilebilirlik eklentisi | axe-playwright | package.json L13 `^1.0.0` |
| B7 | Test kökü | `assets.coremusic.net` | package.json L5 `--root` seçeneği |
| B8 | E2E konfigürasyonu | `assets.coremusic.net/playwright.config.ts` | package.json L8 `-c` seçeneği |
| B9 | `test` (çıplak) betiği | YOK | package.json L4–L9 arasında tanımsız |
| B10 | Örnek spec deseni | `*.spec.js` | primitives klasörü, 6 dosya |

## §C CI KAPI AYRINTISI

| # | Öğe | Değer | Kanıt |
|---|-----|-------|-------|
| C1 | İş akışı adı | `CI Pipeline` | ci.yml L8 |
| C2 | İzinler | `permissions:` bloğu mevcut | ci.yml L14 |
| C3 | PHP sürümü ortamı | `PHP_VERSION: '8.4'` | ci.yml L22 |
| C4 | PHP sürümü dayanağı | `composer.json` `config.platform.php` | ci.yml L22 yorum satırı |
| C5 | CI geçiş kanıtı | ⚠️ VERIFICATION REQUIRED | çalıştırma kaydı bu oturumda yok |
| C6 | Secret taraması | `.github/workflows/secret-scan.yml` dosyası | varlık envanteri (Faz 0–6) |

## §D TEST DOSYASI YERLEŞİM MATRİSİ

| # | Alt klasör | K001 ilgisi | Beklenen K001 testi |
|---|------------|-------------|---------------------|
| D1 | `tests/Unit/Device` | doğrudan | `classify()` tip eşlemesi |
| D2 | `tests/Unit/Middleware` | API katmanı | envanter uçları koruma testi |
| D3 | `tests/Unit/Security` | güvenlik | auth + CSRF senaryoları |
| D4 | `tests/Unit/PageRouter` | yönlendirme | route eşleşmesi |
| D5 | `tests/Unit/Database` (yok) | — | K001 şema testi için AÇIK (Q1) |
| D6 | `tests/Unit/Config` | yapılandırma | buffer/örnek oranı okuma |
| D7 | `tests/Unit/Log` | kayıt | hata kaydı biçim kontrolü |
| D8 | `tests/Unit/Theme` | sunum | K001 ile ilgisiz (kapsam dışı) |

## §E KAPSAM BAZLI TEST MATRİSİ (K001 modülü × test)

| # | K001 modülü | Test tipi | Araç | Durum |
|---|-------------|-----------|------|-------|
| E1 | `donanaım-type` sınıflandırma | birim | PHPUnit 10 | PLANNED (kod yok) |
| E2 | `donanaım-type/hdd` envanter | birim + kontrat | PHPUnit 10 | PLANNED |
| E3 | `donanaım-type/ssd` envanter | birim + kontrat | PHPUnit 10 | PLANNED |
| E4 | `donanım-api` uçları | entegrasyon | PHPUnit + test DB | PLANNED |
| E5 | `donanım-core` envanter toplama | birim | PHPUnit 10 | PLANNED |
| E6 | `donanım-kernel` register erişimi | fiziksel | tezgâh (dalga-4) | PLANNED |
| E7 | `audio/cipler` DAC/amp dosyaları | doküman denetimi | vault gate | TASARIM (dosya mevcut) |
| E8 | JS primitive bileşenler | birim (JS) | Vitest | MEVCUT (6 spec) |
| E9 | Cihaz görünümü | E2E | Playwright | MEVCUT (betik tanımlı) |

## §F SINIF-EKSEN BİRİM TEST DETAYI

| # | Girdi grubu | Beklenen sınıf | Negatif durum |
|---|-------------|----------------|----------------|
| F1 | DAC, codec, amplifikatör | `audio` | bilinmeyen chip → `verify-required` |
| F2 | Mekanik disk, harici HDD | `hdd` | boş seri no → uyarı |
| F3 | SATA/NVMe SSD | `ssd` | TRIM desteksiz → düşüş modu işareti |
| F4 | Ekran, monitör | `display` (kapsam Q4) | envanterde yok → AÇIK |
| F5 | Kablo, adaptör | `accessory` (kapsam Q4) | envanterde yok → AÇIK |
| F6 | Ağ chipi (Wi-Fi/BT) | `network` | sürücü yok → `verify-required` |
| F7 | Ses kartı (PCIe/USB) | `audio` + `interface` | çakışan sınıf → öncelik kuralı |
| F8 | disk (generic) | çoklu aday | `type` alanı boş bırakılmaz |

## §G GÜVENLİK SENARYOLARI (API katmanı)

| # | Senaryo | Beklenen | Kanıt |
|---|---------|----------|-------|
| G1 | Kimliksiz `/api/devices/*` | 401 | AuthenticationMiddleware L26 |
| G2 | Aşırı istek (burst) | 429 | RateLimitMiddleware L20 |
| G3 | Token'sız yazma denemesi | reddet | CsrfMiddleware L7 |
| G4 | Cross-origin istek | kurala göre sınır | CorsMiddleware L15 |
| G5 | Pipeline dışı çağrı | sıra korunur | ApiMiddlewarePipeline L17 |
| G6 | Oturumsuz sayfa erişimi | yönlendirme | AuthGuard L7 |
| G7 | Envanter yazma yetkisi | YOK (salt-okunur) | anayasa §9 (K-05 kararı) |
| G8 | SQL enjeksiyon denemesi | prepared statement | DatabaseManager L8 |

## §H YÜK VE PERFORMANS TESTİ

| # | Ölçüm | Hedef | Yöntem | Durum |
|---|-------|-------|--------|-------|
| H1 | Envanter listeleme gecikmesi | bütçe ⚠️ VERIFICATION REQUIRED | k6 senaryosu | PLANNED |
| H2 | 512 örnek callback süresi | bütçe ⚠️ VERIFICATION REQUIRED | tezgâh ölçümü | PLANNED |
| H3 | Rate limit kapasitesi | 429 tetikleme noktası | entegrasyon testi | PLANNED |
| H4 | DB okuma yarışı | yarış yok | tekrarlı okuma testi | PLANNED |
| H5 | Bellek sızıntısı (uzun koşu) | artış yok | Vitest coverage uzun koşu | PLANNED |
| H6 | Yük testi aracı sürümü | dosyada kayıtlı | package.json devDependency | KAYIT YOK (k6 pakette değil) |

> H6 gerçeği: `k6` bu repo'nun `package.json` içinde değildir; H1–H3 araç seçimi
> ADR kapısıdır. Seçim öncesi k6 yalnız **stratejik referanstır** (§YENİ-KAYNAK).

## §I E2E STRATEJİSİ

| # | Adım | Araç | Kanıt |
|---|------|------|-------|
| I1 | Tarayıcı kurulumu | Playwright resmi dokümantasyonu | package.json L11/L15 |
| I2 | Konfigürasyon dosyası | `assets.coremusic.net/playwright.config.ts` | package.json L8 |
| I3 | Çalıştırma | `npm run test:e2e` | package.json L8 |
| I4 | Erişilebilirlik denetimi | axe-playwright | package.json L13 |
| I5 | Cihaz görünümü senaryosu | profil render kontrolü | PLANNED (K003 iş birliği) |
| I6 | Cross-origin API senaryosu | envanter fetch kontrolü | PLANNED |

## §J BİRİM TEST ARACI BAĞLAMI (JS)

| # | Araç | Rol | K001 kullanımı |
|---|------|-----|-----------------|
| J1 | Vitest | bu repo'nun JS birim test çalıştırıcısı | MEVCUT (L5–L7) |
| J2 | Jest | JS ekosisteminde yaygın birim test aracı (referans) | SEÇİLMEDİ — gerekçe: repo Vitest kullanıyor |
| J3 | Playwright | E2E ve tarayıcı testi | MEVCUT (L8) |
| J4 | PHPUnit 10 | PHP birim/entegrasyon testi | MEVCUT (L24) |
| J5 | PHPStan level 5 | statik analiz | MEVCUT (L48) |
| J6 | Grafana k6 | yük testi | REFERANS — ADR bekliyor (H6) |

## §K COVERAGE VE KALİTE METRİKLERİ

| # | Metrik | Hedef | Ölçüm kaynağı | Durum |
|---|--------|-------|---------------|-------|
| K1 | PHP satır coverage | %90+ | `composer test` + coverage eklentisi ⚠️ VERIFICATION REQUIRED | hedef |
| K2 | JS satır coverage | %90+ | `npm run test:js:coverage` (L7) | hedef |
| K3 | Flaky test oranı | %0 | tekrarlı koşu kaydı | hedef |
| K4 | Test dosyası sayısı | 44 (baz) | `shared/tests` sayımı | ÖLÇÜLDÜ |
| K5 | Statik analiz bulguları | level 5 temiz | `composer stan` (L48) | ⚠️ VERIFICATION REQUIRED |
| K6 | CI geçiş oranı | %95+ (proje standardı) | CI geçmişi | ⚠️ VERIFICATION REQUIRED |
| K7 | K001 üretim testi | K001 kodu ile birlikte | — | PLANNED |

## §L TEST VERİSİ VE FIXTURE KURALLARI

| # | Kural | Gerekçe |
|---|-------|---------|
| L1 | Fixture verisi repo içinde sabit dosya olur | tekrarlanabilirlik |
| L2 | Üretim DB'sinden kopya kullanılmaz | veri sızıntısı riski |
| L3 | Test DB adı üretim adından farklı olur | kazara silme koruması |
| L4 | Fiziksel cihaz seri numarası testte maskelenir | gizlilik |
| L5 | Boş/tek örneklem fixture'lar negatif testte kullanılır | hata yolu kapsamı |
| L6 | `donanaım-type` literali fixture içinde birebir korunur | yazım dondurması |
| L7 | URL fixture'ları envanter-temiz kaynaklardan gelir | URL gate |

## §M HATA MODU TABLOSU

| # | Hata modu | Belirti | Tepki |
|---|-----------|---------|-------|
| M1 | Test keşfi boş | 0 test koştu | BLOCK — konfigürasyon hatası |
| M2 | Fixture şeması uyumsuz | SQL hatası | BLOCK — migration kontrolü |
| M3 | Port çakışması (E2E) | bağlantı reddi | yeniden deneme + log |
| M4 | Cihaz çevrimdışı (fiziksel) | tezgâh hatası | atla + `⚠️` raporla |
| M5 | Coverage aracı yok | komut bulunamadı | BLOCK — bağımlılık kurulumu |
| M6 | Eşzamanlı oturum dosya yazımı | mtime değişimi | bekle + yeniden ölç |
| M7 | Kırık URL tespiti | HTTP != 200 | URL satırını ⚠️ işaretle |

## §N DOĞRULAMA ADIMLARI (tekrarlanabilir)

| # | Adım | Komut/yöntem | Beklenen |
|---|------|--------------|----------|
| N1 | PHP test sayımı | `Get-ChildItem shared/tests -Recurse -Filter *.php` | 44 |
| N2 | Unit klasör sayımı | `Get-ChildItem shared/tests/Unit -Directory` | 7 |
| N3 | Komut kanıtı | composer.json L47 oku | `"test": "phpunit"` |
| N4 | JS komut kanıtı | package.json L5 oku | `test:js` vitest |
| N5 | E2E komut kanıtı | package.json L8 oku | `test:e2e` playwright |
| N6 | CI adı kanıtı | ci.yml L8 oku | `CI Pipeline` |
| N7 | URL doğrulama | HEAD isteği + envanter kesişimi | 200 ve kesişim 0 |
| N8 | Satır sayısı ölçümü | dosya satır sayacı | 500 ve üzeri |

## §YENİ DOĞRULANMIŞ WEB KAYNAKLARI

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| Jest, JavaScript için birim test aracıdır | https://jestjs.io/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |
| Grafana k6, mühendislik ekipleri için yük testi aracıdır | https://k6.io/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |
| Playwright resmi dokümantasyonu kurulum (Installation) sayfasını taşır | https://playwright.dev/docs/intro | 2026-10-13 | ✅ HTTP 200 doğrulandı |

> Üç URL de `url-envanter.txt` (1673 satır) ile kesişmez, `.ai/**.md` içinde daha önce
> kullanılmamış ve bu dosya içinde tektir (URL gate).

## §REPO KANITLARI (Read ile doğrulanmış, 2026-10-13)

| Dosya | Satır | Kanıt içeriği |
|-------|-------|---------------|
| Dosya: `shared/composer.json` / Satır: 24 | sürüm | `"phpunit/phpunit": "^10.5",` |
| Dosya: `shared/composer.json` / Satır: 47 | komut | `"test": "phpunit",` |
| Dosya: `shared/composer.json` / Satır: 48 | komut | `"stan": "phpstan analyse src --level=5"` |
| Dosya: `package.json` / Satır: 5 | JS test | `"test:js": "vitest run --root assets.coremusic.net",` |
| Dosya: `package.json` / Satır: 8 | E2E test | `"test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"` |
| Dosya: `package.json` / Satır: 16 | sürüm | `"vitest": "^5.0.3"` |
| Dosya: `package.json` / Satır: 11 | sürüm | `"@playwright/test": "^1.63.0",` |
| Dosya: `.github/workflows/ci.yml` / Satır: 8 | iş akışı | `name: CI Pipeline` |
| Dosya: `.github/workflows/ci.yml` / Satır: 22 | sürüm | `PHP_VERSION: '8.4'` |
| Dosya: `shared/phpunit.xml` / Satır: 1 | konfig | `<?xml version="1.0" encoding="UTF-8"?>` |
| Dosya: `shared/src/Api/Middleware/AuthenticationMiddleware.php` / Satır: 26 | koruma | sınıf başlangıcı (API auth) |
| Dosya: `shared/src/Api/Middleware/RateLimitMiddleware.php` / Satır: 20 | koruma | sınıf başlangıcı (rate limit) |

## §KABUL KRİTERLERİ

| # | Kriter | Ölçüm | Aksi durum |
|---|--------|-------|------------|
| 1 | Dosya 500+ satır | satır sayacı | BLOCK |
| 2 | Yeni URL'ler envanter-temiz ve tekil | kesişim raporu | BLOCK |
| 3 | Eski hatalı JS test betik iddiası metinde kalmadı | metin taraması 0 isabet | BLOCK |
| 4 | Her kanıt `Dosya:`/`Satır:` Read doğrulu | yeniden okuma | BLOCK |
| 5 | Yasaklı desen yok (yer tutucu · doldurucu · boş hücre) | tarama | BLOCK |
| 6 | Son satır kanonik authority satırı | satır karşılaştırma | BLOCK |

## §ÇALIŞMA AKIŞI KAPILARI

| Kapı | Koşul | Sonraki kapı |
|------|-------|--------------|
| G-A Tasarım | bu dosya incelemeden geçti | G-B Altyapı |
| G-B Altyapı | §A ölçümleri tekrar üretilebilir | G-C Senaryo |
| G-C Senaryo | §E modül matrisi K001 koduyla eşleşiyor | G-D Uygulama |
| G-D Uygulama | birim testler yeşil | G-E Coverage |
| G-E Coverage | %90+ (K1/K2) | G-F Yayın |
| G-F Yayın | CI yeşil + URL gate temiz | kapanış |

## §SORUMLULUK MATRİSİ

| Görev | Sahip | Onaylayan |
|-------|-------|-----------|
| Birim test yazımı | QA Engineer | Vault Steward |
| E2E senaryosu | QA Engineer | UI Designer |
| Fiziksel tezgâh testi | Audio Hardware Engineer | Vault Steward |
| URL doğrulama | kanit (grup) | Vault Steward |
| Coverage raporu | QA Engineer | Master Orchestrator |
| CI kapısı | DevOps Engineer | QA Engineer |

## §OPEN QUESTIONS

| # | Soru | Etki |
|---|------|------|
| 1 | K001 test klasörü: `shared/tests/Device/` mi ayrı mı? | Yerleşim |
| 2 | Test DB'si `coremusic_system` şemasından mı türetilir? | Fixture |
| 3 | Fiziksel donanım testi otomatikleşir mi? (dalga-4) | Zaman |
| 4 | k6 ADR'si ne zaman açılır? (H6 — paket yok) | Performans kapısı |
| 5 | CI'nin gerçekten geçtiği doğrulandı mı? | Yayın kapısı |

## §TEST ORTAMI MATRİSİ

| # | Ortam | Çalıştırıcı | Sürüm kanıtı | Not |
|---|-------|-------------|--------------|-----|
| 1 | Yerel PHP | `composer test` | composer.json L47 | PHPUnit 10.5 (L24) |
| 2 | Yerel JS | `npm run test:js` | package.json L5 | vitest L16 |
| 3 | Yerel E2E | `npm run test:e2e` | package.json L8 | playwright L15 |
| 4 | CI (PHP) | GitHub Actions | ci.yml L22 | PHP 8.4 |
| 5 | CI (JS aşaması) | ⚠️ VERIFICATION REQUIRED | ci.yml JS satırı okunmadı | kanıt yok |
| 6 | Test DB | MySQL şema dosyaları | `.ai/.sql/mysql/` 20 dosya | fixture kaynağı |
| 7 | Tarayıcı (E2E) | Playwright | package.json L11 | tarayıcı sürümü ⚠️ |
| 8 | Fiziksel tezgâh | manuel dalga-4 | K001 açık sorusu | cihaz listesi **UNKNOWN** |
| 9 | JS coverage | `npm run test:js:coverage` | package.json L7 | v8 eklentisi L12 |
| 10 | PHP coverage eklentisi | ⚠️ VERIFICATION REQUIRED | composer require-dev okunmadı | kanıt yok |

## §REGRESSION VE DEĞİŞİKLİK KONTROLÜ

| # | Değişen yüzey | Zorunlu kontrol | Kapı |
|---|---------------|-----------------|------|
| 1 | `composer.json` require | `composer test` koşumu | G-D |
| 2 | `composer.json` scripts (L47/L48) | komut yeniden okuma | G-B |
| 3 | `package.json` scripts (L5/L8) | `test:js` + `test:e2e` koşumu | G-D |
| 4 | `shared/phpunit.xml` | dokunulmaz; fark incelemesi | review |
| 5 | `.github/workflows/ci.yml` | DevOps incelemesi | G-F |
| 6 | `.ai/.sql/mysql/` migration | şema senaryosu (§ENTEGRASYON 5) | G-D |
| 7 | `shared/src/Device/*` | §D yerleşim matrisi testleri | G-D |
| 8 | `shared/src/Middleware/*` | §G güvenlik senaryoları | G-A |
| 9 | `shared/src/Api/Middleware/*` | pipeline sıra kontrolü (L17) | G-A |
| 10 | `primitives/*.spec.js` | `npm run test:js` | G-D |
| 11 | K001 vault dosyası | URL gate + satır sayısı | G-A |
| 12 | `url-envanter.txt` | kesişim ölçümü yenileme | G-B |

## §NEGATİF TEST KATALOĞU

| # | Negatif senaryo | Beklenen sonuç | Kanıt katmanı |
|---|-----------------|----------------|---------------|
| 1 | Boş cihaz listesi | boş liste + uyarı | birim |
| 2 | `deviceProfile()` null alan | Result::err | birim |
| 3 | Geçersiz `type` değeri | `verify-required` | birim |
| 4 | Aynı cihazın çift kaydı | ikinci kayıt reddi | birim |
| 5 | ASCII dışı dosya adı | reddet (ad kuralı) | birim |
| 6 | Envanter-temiz olmayan URL satırı | URL gate BLOCK | vault gate |
| 7 | Auth'suz okuma | 401 | entegrasyon (AuthenticationMiddleware L26) |
| 8 | Token'sız POST | reddet | entegrasyon (CsrfMiddleware L7) |
| 9 | Oran limiti taşması | 429 | entegrasyon (RateLimitMiddleware L20) |
| 10 | Yasaklı origin | sınır reddi | entegrasyon (CorsMiddleware L15) |
| 11 | Pipeline dışı çağrı sırası | sıra korunur | entegrasyon (ApiMiddlewarePipeline L17) |
| 12 | Bozuk JSON yanıt | assert başarısız | kontrat |
| 13 | Şemasız DB erişimi | migration hatası | entegrasyon |
| 14 | Çevrimdışı tezgâh cihazı | atla + `⚠️` raporla | fiziksel |

## §PARALELLİK VE SÜRE SINIRLARI

| # | Kural | Değer | Gerekçe |
|---|-------|-------|---------|
| 1 | PHPUnit paralel betiği | tanımsız (L47 tek komut) | determinizm |
| 2 | Vitest çalıştırma biçimi | dosyadan okunur (L5 `vitest run`) | tekrarlanabilirlik |
| 3 | E2E tek oturum | playwright config (L8) | kaynak paylaşımı |
| 4 | CI zaman aşımı değeri | ⚠️ VERIFICATION REQUIRED | ci.yml timeout satırı okunmadı |
| 5 | E2E retry değeri | ⚠️ VERIFICATION REQUIRED | config dosyası okunmadı |
| 6 | Tezgâh test süresi | **UNKNOWN** | cihaz/envanter yok |
| 7 | Test koşusu tekrar sayısı | 1 (varsayılan) | flaky ölçümü ayrı koşuda |
| 8 | Flaky şüphesi | korelasyon yoksa kapatma | yanlış pozitif riski |
| 9 | Paralel yazım çakışması | mtime koruması | eşzamanlı oturum kuralı (§M6) |
| 10 | Ortak DB kullanan testler | sıralı | yarış önleme |

## §RAPOR VE LOG ÇIKIŞI

| # | Çıktı | Biçim | Tüketici |
|---|-------|-------|----------|
| 1 | PHPUnit sonucu | konsol çıktısı | CI + QA |
| 2 | JS test sonucu | vitest çıktısı (L5) | CI + QA |
| 3 | Coverage raporu | `test:js:coverage` (L7) | QA |
| 4 | Statik analiz | `composer stan` (L48) | code review |
| 5 | E2E raporu | playwright config çıktısı (L8) | QA |
| 6 | URL doğrulama | iddia-URL-tarih-durum tablosu | vault |
| 7 | Gate sonucu | PASS/BLOCK | orchestrator |
| 8 | Ajan denetim kaydı | `log.md` append-only | tüm ajanlar |
| 9 | Tezgâh ölçümü | rapor satırı | doğrulama dosyası |
| 10 | Hata bildirimi | konum + neden + düzeltme | on-error akışı |

## §BAĞIMLILIK VE SÜRÜM KAYDI

| # | Bağımlılık | Kayıt | Durum |
|---|------------|-------|-------|
| 1 | phpunit/phpunit | composer.json L24 `^10.5` | MEVCUT |
| 2 | vitest | package.json L16 `^5.0.3` | MEVCUT |
| 3 | @playwright/test | package.json L11 `^1.63.0` | MEVCUT |
| 4 | playwright | package.json L15 `^1.63.0` | MEVCUT |
| 5 | @vitest/coverage-v8 | package.json L12 `^5.0.3` | MEVCUT |
| 6 | jsdom | package.json L14 `^30.1.1` | MEVCUT |
| 7 | axe-playwright | package.json L13 `^1.0.0` | MEVCUT |
| 8 | phpstan | composer.json L48 komutu | SÜRÜM satırı ⚠️ okunmadı |
| 9 | PHP | ci.yml L22 `8.4` | MEVCUT |
| 10 | k6 | paket dosyasında YOK | REFERANS (ADR bekliyor) |
| 11 | JUCE / ASIO SDK | K001 kural 12 | ⚠️ VERIFICATION REQUIRED |

## §TEST KAPSAMI DIŞI

| # | Yüzey | Neden | Sahip |
|---|-------|-------|-------|
| 1 | K003-audio dizini | yazım yasağı (görev beyanı) | K003 sahibi |
| 2 | K004-dsp dizini | yazım yasağı | K004 sahibi |
| 3 | K005-firmware dizini | yazım yasağı | K005 sahibi |
| 4 | K000-isletim-sistemi dizini | yazım yasağı | K000 sahibi |
| 5 | `.ai/.decisions/` | frozen ADR | Vault Steward |
| 6 | `*.php` / `*.js` / `*.css` / `*.sql` | Markdown dışı, bu görevde dokunulmaz | ilgili agent |
| 7 | `.ai/architecture/index.md` | 40–100 satır bandı korunur | orkestratör |
| 8 | K010 / K011 dizinleri | yazım yasağı | ilgili sahip |
| 9 | webos / tizen / windows | canlı agent sınırı | canlı agent |
| 10 | `.ai/ui-design/**` | mockup gate (Guardrail #16) | UI Designer |

## §REVİZYON TARİHÇESİ

| # | Tarih | Değişiklik | Kayıt |
|---|-------|------------|-------|
| 1 | 2026-10-12 | dosya ilk kez yazıldı (112 satır) | vault kaydı |
| 2 | 2026-10-13 | hatalı JS test betik iddiası düzeltildi | §MEVCUT ALTYAPI notu |
| 3 | 2026-10-13 | URL ekleme kuralı revize edildi | §KAPSAM kural 4 |
| 4 | 2026-10-13 | 3 yeni doğrulanmış kaynak eklendi | §YENİ DOĞRULANMIŞ WEB KAYNAKLARI |
| 5 | 2026-10-13 | ölçümler yenilendi (44 test · 20 SQL/4345 satır) | §A |
| 6 | 2026-10-13 | 112 → 500+ satır genişletme | bu revizyon |

## §KAPANIŞ

- Bu dosya **strateji + kanıt** kaydıdır; K001 üretim kodu yazılmadan coverage %90 iddiası edilemez.
- Ölçülen değerler (44 test · 7 Unit klasörü · 6 primitive spec · 20 SQL/4345 satır) tekrar üretilebilir.
- Eski hatalı iddialar (JS çalıştırıcı betiği, çıplak `npm test`) 2026-10-13'te düzeltildi.
- URL gate: 3 yeni kaynak, envanter-temiz, `.ai` içinde tekil, HTTP 200.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
