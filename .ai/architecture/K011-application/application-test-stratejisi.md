---
node_id: K011-application/application-test-stratejisi
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K019-testing
  - K009-security
evidence:
  - "shared/tests/ — 44 PHP test dosyası (2026-10-12)"
  - "shared/tests/Middleware/ — 9 test dosyası (2026-10-12)"
  - "shared/tests/Api/ — 8 test dosyası (2026-10-12)"
  - "shared/phpunit.xml + shared/composer.json (PHPUnit ^10.5/^11.0 — shared/AGENTS.md §4 kanıtı)"
validation:
  - "Her test grubu dosya sayısı glob ile sayıldı: 2026-10-12"
open_questions:
  - "Coverage raporu hangi ortamda çalıştırılacak ve eşiği kim onaylayacak?"
  - "Use case yokken stratejinin §4 satırları bekleyedurur mu?"
group: kod
---

# K011 Application — Test Stratejisi

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: K011 test stratejisi (kapı: coverage ≥ %80)**
> **Araç: PHPUnit (composer kanıtı) · 44 mevcut test dosyası (2026-10-12)**

## §1 STRATEJİ ÖZETİ

| # | İlke | Uygulama | Kapı |
|---|---|---|---|
| 1 | Her yeni sınıf = test dosyası | PR/commit öncesi | zorunlu |
| 2 | Mevcut test bozulmaz | regresyon | zorunlu |
| 3 | Güvenlik denetimi test'siz değişmez | CSRF/auth/izin | zorunlu |
| 4 | `verify-required` katmanı üretilir üretilmez testi yazılır | use case, komut | planlı |
| 5 | Coverage ≥ %80 (K011 kodu) | PHPUnit coverage | hedef |

## §2 TEST PİRAMİDİ (K011)

| Katman | Kapsam | Mevcut (2026-10-12) | Hedef |
|---|---|---|---|
| Birim | Middleware, Cache, PageRouter, Events | 44 dosya (tüm `shared/tests/`) | her sınıf için 1 dosya |
| Bütün | API pipeline + Gateway | `Api/` 8 dosya | her uç için 1 |
| Sözleşme | Uzak servis (ödeme, e-posta) | yok → `verify-required` | K012 ile |
| Uçtan uca | Tarayıcı akışı | yok (K015/K019 kapsamı) | K019 |

## §3 MEVCUT TEST ENVANTERİ (2026-10-12)

| # | Grup | Dosya | K011 karşılığı |
|---|---|---|---|
| 1 | `tests/Middleware/` | 9 | §4.1 zincir |
| 2 | `tests/Api/` | 8 | §4.2 zarf |
| 3 | `tests/Unit/PageRouter/` | 6 | §4.3 yönlendirme |
| 4 | `tests/Repository/` | 4 (2 test + 2 yardımcı) | veri erişim |
| 5 | `tests/Events/` | 2 | §4.4 olay |
| 6 | `tests/Unit/Security/` + `tests/Security/` | 4 | K009 ile ortak |
| 7 | Diğer (Theme/Device/Log/Config/OAuth/Component/Fixture) | 11 | K015/K016 ile ortak |
| 8 | **Toplam** | **44** | — |

**Middleware test listesi (9):** `ValidationMiddlewareTest` · `SessionManagerMiddlewareTest` · `RateLimiterMiddlewareTest` · `OriginCheckMiddlewareTest` · `MiddlewarePipelineTest` · `CsrfMiddlewareTest` · `BypassAuthMiddlewareTest` · `BypassAuthMiddlewareFailClosedTest` · `AuthMiddlewareTest`.

**API test listesi (8):** `VersioningTest` · `RouteTableTest` · `ResponseNormalizationMiddlewareTest` · `RequestValidationMiddlewareTest` · `GatewayMethodRoutingTest` · `DtoTest` · `ApiResponseTest` · `ApiPipelineSecurityTest`.

**PageRouter test listesi (6):** `SpaRouteTest` · `RouteRegistryTest` · `HtmlShellRendererGuardConfigTest` · `HtmlShellRendererChromeTest` · `AuthUrlBuilderTest` · `AuthGuardTest`.

## §4 KAPSAM BOŞLUKLARI (senaryo bazlı)

### §4.1 Middleware zinciri

| # | Senaryo | Test | Durum |
|---|---|---|---|
| 1 | Sıra doğru (dış → çekirdek) | `MiddlewarePipelineTest` | ✅ |
| 2 | CSRF reddi 403 + log | `CsrfMiddlewareTest` | ✅ |
| 3 | Bypass fail-closed | `BypassAuthMiddlewareFailClosedTest` | ✅ |
| 4 | Hız sınırı eşiği | `RateLimiterMiddlewareTest` | ✅ |
| 5 | **Sıra kilit ihlali (negatif)** | yok | ⚠️ boşluk |
| 6 | **CORS + Origin birlikte** | yok | ⚠️ boşluk |

### §4.2 API zarfı

| # | Senaryo | Test | Durum |
|---|---|---|---|
| 1 | DTO doğrulama | `DtoTest` | ✅ |
| 2 | Yanıt normalizasyonu | `ResponseNormalizationMiddlewareTest` | ✅ |
| 3 | Gateway metod yönlendirme | `GatewayMethodRoutingTest` | ✅ |
| 4 | **Bilinmeyen sürüm 404/410** | `VersioningTest` kısmi | ⚠️ kısmi |
| 5 | **Uzak servis hata zarfı** | yok (Gateway 5xx) | ⚠️ boşluk |

### §4.3 Yönlendirme

| # | Senaryo | Test | Durum |
|---|---|---|---|
| 1 | Route kayıt/eşleşme | `RouteRegistryTest` | ✅ |
| 2 | SPA route | `SpaRouteTest` | ✅ |
| 3 | AuthGuard bekçi | `AuthGuardTest` | ✅ |
| 4 | **405 method ayrımı** | yok (RouteTable notu §405) | ⚠️ boşluk |
| 5 | **404 shell çizimi** | `HtmlShellRenderer*` kısmi | ⚠️ kısmi |

### §4.4 Olay

| # | Senaryo | Test | Durum |
|---|---|---|---|
| 1 | Yayım + dinleyici çağrısı | `EventDispatcherTest` | ✅ |
| 2 | Domain olay sözleşmesi | `DomainEventTest` | ✅ |
| 3 | **Öncelik sırası (priority)** | yok (satır 109–118 sort) | ⚠️ boşluk |
| 4 | **Durdurma (propagation stop)** | yok (satır 37–39) | ⚠️ boşluk |

### §4.5 Üretilmemiş katmanlar (öncelik bekliyor)

| # | Katman | Senaryo sayısı (plan) | Durum |
|---|---|---|---|
| 1 | Use case | her senaryo: mutlu + hata | ⏳ katman yok |
| 2 | Komut/sorgu | idempotency + yetki | ⏳ katman yok |
| 3 | Ödeme | imza + kısmi ödeme + iade | ⏳ katman yok |
| 4 | Kuyruk/worker | tekrar + başarısızlık | ⏳ katman yok |
| 5 | Zamanlanmış görev | kaçırmama + tekrar | ⏳ katman yok |

## §5 KAPILAR (GATE)

| # | Kapı | Koşul | Sahip |
|---|---|---|---|
| 1 | Sınıf kapısı | Yeni sınıf → test dosyası | Backend + QA |
| 2 | Güvenlik kapısı | Auth/CSRF/izin değişikliği → ilgili test | Security |
| 3 | Coverage kapısı | K011 kodu ≥ %80 | QA |
| 4 | Regresyon kapısı | Mevcut 44 dosya yeşil | QA |
| 5 | Katman kapısı | `verify-required` katman üretilir → test aynı PR'da | QA |

## §6 ARAÇ VE YAPILANDIRMA

| # | Araç | Kanıt (2026-10-12) | Not |
|---|---|---|---|
| 1 | PHPUnit | `shared/phpunit.xml` + composer (shared/AGENTS.md §2) | sürüm `^10.5/^11.0` (shared/AGENTS.md §4) |
| 2 | PHPStan | composer kanıtı (shared/AGENTS.md §2) | statik analiz |
| 3 | Coverage aracı | PHPUnit coverage çıktısı | çalıştırma kanıtı yok → `verify-required` |
| 4 | E2E | Playwright (qa agent profili) | K015/K019 kapsamı |

## §7 ÖNCELİK SIRASI

| # | İş | Gerekçe | Bağımlılık |
|---|---|---|---|
| 1 | §4.1/5–6 boşlukları | güvenlik yüzeyi | yok |
| 2 | §4.4/3–4 olay boşlukları | yayım doğruluğu | yok |
| 3 | Coverage çalıştır + rapor | kapı 3 ölçülmüyor | yok |
| 4 | §4.5 use case testleri | katman üretilince | use case iskeleti |
| 5 | Sözleşme testleri | ödeme/e-posta | K012 |

## §8 DOĞRULAMA

| # | İddia | Yöntem | Tarih | Sonuç |
|---|---|---|---|---|
| 1 | 44 test dosyası | glob sayımı | 2026-10-12 | ✅ |
| 2 | Middleware 9 · API 8 · PageRouter 6 · Events 2 | grup sayımı | 2026-10-12 | ✅ |
| 3 | Test adları gerçek | dosya adları glob ile | 2026-10-12 | ✅ |
| 4 | Boşluklar gerçek (test yok) | ad araması | 2026-10-12 | ✅ |
| 5 | Coverage değeri bilinmiyor | rapor yok → `verify-required` | 2026-10-12 | ⚠️ açık |

## §9 AÇIK SORULAR

1. Coverage eşiği (%80) hangi komutla ölçülecek?
2. §4.5 satırları katman üretilene kadar "beklemede" mi sayılacak?
3. E2E testleri K019'da mı K011'de mi?
4. `Repository/` içindeki 2 yardımcı dosya test mi sayılacak?

## §10 REFERANSLAR

- `shared/tests/` — 44 dosya (2026-10-12 glob sayımı)
- `shared/phpunit.xml` · `shared/composer.json` — araç kanıtı
- `shared/AGENTS.md` §3–§4 — QA sorumlulukları, PHPUnit sürümü
- `application-dogrulama.md` §6 — test kanıtı özeti

## §A — Test piramidi

> **Amaç:** §2'deki özet piramidi katman katman açmak; her katmanın sorusunu, K011 karşılığını ve mevcut/durumunu sabitlemek. Dosya sayımları 2026-10-13 tarihinde `shared/tests/` üzerinden yeniden alındı.

### §A.1 — Piramit katmanları

| # | Katman | Soru | K011 karşılığı | Mevcut | Hedef |
|---|---|---|---|---|---|
| 1 | Birim | doğru mu? | middleware · cache · events · pagerouter | 35 dosya (§3 grupları 1–5) | her sınıf için 1 |
| 2 | Birleşim (integration) | birlikte doğru mu? | API pipeline + Gateway | 8 (`tests/Api/`) | her uç için 1 |
| 3 | Sözleşme | dış yüzey sabit mi? | uzak servis (ödeme, e-posta) | 0 | K012 ile |
| 4 | Uçtan uca | akış tamam mı? | tarayıcı akışı | 0 | K019 |
| 5 | Statik analiz | tip/desen ihlali var mı? | PHPStan level 5 | yapılandırma var (§D/8) | her PR'da |

### §A.2 — Katman ağırlığı (K011)

| # | Katman | Ağırlık | Gerekçe | Mevcut dağılım |
|---|---|---|---|---|
| 1 | Birim | %60 | hızlı geri bildirim | 35/44 dosya |
| 2 | Birleşim | %25 | zincir + uç davranışı | 8/44 dosya |
| 3 | Sözleşme | %10 | dış bağımlılık | 0 (kod yok) |
| 4 | E2E | %5 | akış doğrulaması | 0 (K019) |
| 5 | Statik | zorunlu | PHPStan level 5 | yapılandırma |

### §A.3 — Piramit–bileşen eşlemesi

| # | Bileşen grubu | Birim | Birleşim | Sözleşme | Boşluk |
|---|---|---|---|---|---|
| 1 | Middleware (11) | 9 test | — | — | sıra + CORS birlikteliği |
| 2 | PageRouter (14) | 6 test | — | — | 405 · 404 shell |
| 3 | Events (14 + 2) | 2 test | — | — | öncelik · durdurma |
| 4 | Api (3) | — | 8 test | 0 | 5xx zarfı |
| 5 | Cache (6) | 0 | — | — | adaptör seçimi testi yok |
| 6 | Session (4) | oturum testleri parçalı | — | — | yaşam döngüsü testi yok |

### §A.4 — Piramit kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Alt katman olmadan üst katman yazılmaz | birleşim, birimi tamamlar |
| 2 | Her boşluk satırı | §4 tablolarında görünür kalır |
| 3 | Kod yok → test yok | §4.5 `⏳` satırları uydurulmaz |
| 4 | Statik analiz test değildir | ayrı kapı (§D/8) |
| 5 | Ölçüm yöntemi sabit | dosya sayımı `Get-ChildItem -Filter` |

## §B — Test türleri

### §B.1 — Tür envanteri

| # | Tür | K011 örneği | Test dosyası | Durum |
|---|---|---|---|---|
| 1 | Mutlu yol | zincir çekirdeğe ulaşır | `MiddlewarePipelineTest` | ✅ |
| 2 | Red (fail-closed) | boş CSRF reddi | `CsrfMiddlewareTest` | ✅ |
| 3 | Red (fail-closed) | bypass reddi | `BypassAuthMiddlewareFailClosedTest` | ✅ |
| 4 | Sınır değeri | hız sınırı eşiği | `RateLimiterMiddlewareTest` | ✅ |
| 5 | Yetki reddi | korumalı route | `AuthGuardTest` | ✅ |
| 6 | Sözleşme | yanıt zarfı | `ApiResponseTest` | ✅ |
| 7 | Yönlendirme | metod → uç | `GatewayMethodRoutingTest` | ✅ |
| 8 | Olay yayımı | dinleyici çağrısı | `EventDispatcherTest` | ✅ |
| 9 | Domain sözleşmesi | olay alanları | `DomainEventTest` | ✅ |
| 10 | Girdi doğrulama | geçersiz gövde | `RequestValidationMiddlewareTest` | ✅ |
| 11 | Sürüm | sürüm ayrımı | `VersioningTest` (kısmi) | ⚠️ kısmi |
| 12 | Negatif sıra | sıra ihlali | yok | ⚠️ boşluk |
| 13 | Birliktelik | CORS + Origin | yok | ⚠️ boşluk |
| 14 | Uzak hata | Gateway 5xx | yok | ⚠️ boşluk |
| 15 | Öncelik/durdurma | dispatcher sırası | yok | ⚠️ boşluk |

### §B.2 — Tür–kapı eşlemesi

| # | Tür | Hangi kapıda zorunlu | Dayanak |
|---|---|---|---|
| 1 | Mutlu yol | sınıf kapısı | §D/1 |
| 2 | Red (fail-closed) | güvenlik kapısı | §D/2 |
| 3 | Sınır değeri | güvenlik kapısı | §D/2 |
| 4 | Sözleşme | birleşim kapısı | §D/9 |
| 5 | Negatif sıra | zincir kapısı | §D/10 |
| 6 | Uzak hata | birleşim kapısı | §D/9 |

### §B.3 — Test adlandırma ve yerleşim

| # | Kural | Kalıp | Örnek |
|---|---|---|---|
| 1 | Dosya adı | `<Sınıf>Test.php` | `CsrfMiddlewareTest.php` |
| 2 | Dizin | hedef katmanın adı | `tests/Middleware/` ↔ `src/Middleware/` |
| 3 | Birim yerleşim | `tests/Unit/<Alan>/` | `tests/Unit/PageRouter/` |
| 4 | Ortak güvenlik | `tests/Security/` + `tests/Unit/Security/` | K009 ile paylaşılır |
| 5 | Yardımcı dosya | test sayılmaz | `tests/Repository/Sqlite/` 2 yardımcı |

### §B.4 — Yazım kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | I/O bağımsız | birim test dosya/sağ ağ çağırmaz |
| 2 | Taklit (mock) ölçülü | gerçek arayüz varsa taklit asgari |
| 3 | Her test tek davranış | birden çok iddia birleştirilmez |
| 4 | Durum sıfırlama | statik kapı (`CacheManager`) test sonunda sıfırlanır |
| 5 | Uydurma iddia yok | çalıştırılmayan test "geçti" diye yazılmaz |

## §C — Ortam / fixture

### §C.1 — Ortam envanteri

| # | Ortam | Kanıt | Durum |
|---|---|---|---|
| 1 | PHPUnit yapılandırması | `shared/phpunit.xml` | ✅ mevcut |
| 2 | Bağımlılık tanımı | `shared/composer.json` (PHPUnit `^10.5`, satır 24) | ✅ mevcut |
| 3 | CI değişkeni | `.github/workflows/ci.yml:22` `PHP_VERSION: '8.4'` | ✅ mevcut |
| 4 | CI pipeline | `.github/workflows/ci.yml:8` `CI Pipeline` | ✅ mevcut |
| 5 | `vendor/` bağımlılıkları | diskte yok | ⚠️ çalıştırma kanıtı yok |
| 6 | Coverage çıktısı | rapor dosyası yok | ⚠️ `verify-required` |

### §C.2 — Fixture ve yardımcılar

| # | Yardımcı | Dosya | Rol | Test mi? |
|---|---|---|---|---|
| 1 | SQLite veritabanı yöneticisi | `tests/Repository/Sqlite/SqliteDatabaseManager.php` | test veritabanı kurulumu | hayır (yardımcı) |
| 2 | SQLite kayıt defteri | `tests/Repository/Sqlite/SqliteDatabaseRegistry.php` | şema kaydı | hayır (yardımcı) |
| 3 | Chrome kabuk başlığı | `tests/Fixtures/chrome/header.php` | kabuk testi girdisi | hayır (fixture) |
| 4 | Chrome kabuk altbilgisi | `tests/Fixtures/chrome/footer.php` | kabuk testi girdisi | hayır (fixture) |

### §C.3 — Ortam kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Yardımcı ≠ test | `Sqlite/` ve `Fixtures/` sayımda test sayılmaz |
| 2 | Gerçek şema | repository testleri gerçek SQLite şemasında koşar |
| 3 | Ortam bağımsızlık | testler ortam değişkenine bağlı değil |
| 4 | CI = lokal sürüm | `ci.yml:22` ile `composer.json:8` aynı major |
| 5 | Bağımlılık yoksa iddia yok | `vendor/` yokken "testler geçti" yazılır |

### §C.4 — Çalıştırma kaydı

| # | Çalıştırma | Tarih | Sonuç |
|---|---|---|---|
| 1 | Dosya sayımı | 2026-10-12 | 44 |
| 2 | Dosya sayımı tekrarı | 2026-10-13 | 44 (değişmedi) |
| 3 | PHPUnit koşusu | — | ⚠️ `vendor/` yok → koşulmadı |
| 4 | Coverage koşusu | — | ⚠️ koşulmadı |
| 5 | PHPStan koşusu | — | ⚠️ koşulmadı |

## §D — Gate eşikleri

### §D.1 — Kapı tablosu

| # | Kapı | Eşik | Ölçüm | Aşım durumu |
|---|---|---|---|---|
| 1 | Sınıf kapısı | yeni sınıf → test dosyası | dosya varlığı | birleşmez |
| 2 | Güvenlik kapısı | auth/CSRF/izin değişikliği → test | diff + test dosyası | birleşmez |
| 3 | Coverage | K011 kodu ≥ %80 | PHPUnit coverage | hedef (ölçülmedi) |
| 4 | Regresyon | 44 dosya yeşil | test koşusu | birleşmez |
| 5 | Katman kapısı | `verify-required` katman → test aynı PR'da | PR denetimi | birleşmez |
| 6 | Birleşim kapısı | API uçları için 1 test/uç | `tests/Api/` | kısmi |
| 7 | Sözleşme kapısı | uzak servis şeması | K012 | beklemede |
| 8 | Statik analiz | PHPStan level 5 | `composer.json:48` | yapılandırma hazır |
| 9 | Sıra kapısı | zincir sırası kilitli | `MiddlewarePipelineTest` | ✅ |
| 10 | Negatif sıra | sıra ihlali reddi | yok | ⚠️ boşluk → §7/1 |

### §D.2 — Eşik–kanıt tablosu

| # | Eşik | Kanıt (2026-10-13) | Durum |
|---|---|---|---|
| 1 | 44 test dosyası | `Get-ChildItem` sayımı = 44 | ✅ |
| 2 | Grup sayıları | 9 · 8 · 6 · 4 · 2 | ✅ |
| 3 | Coverage ≥ %80 | rapor yok | ⚠️ açık (§9/1) |
| 4 | PHPUnit sürümü | `composer.json:24` = `^10.5` | ✅ |
| 5 | CI PHP sürümü | `ci.yml:22` = `'8.4'` | ✅ |
| 6 | PHPStan seviyesi | `composer.json:48` = level 5 | ✅ |
| 7 | E2E | yok | K019 |

### §D.3 — Kapı sorumlulukları

| # | Kapı | Sorumlu | Devreye alma |
|---|---|---|---|
| 1 | Sınıf | Backend + QA | PR denetimi |
| 2 | Güvenlik | Security Engineer | diff denetimi |
| 3 | Coverage | QA | rapor + eşik |
| 4 | Regresyon | QA | test koşusu |
| 5 | Katman | QA | PR denetimi |
| 6 | Birleşim | QA | `tests/Api/` |
| 7 | Sözleşme | Backend + QA | K012 |
| 8 | Statik | Backend | composer `stan` betiği |

### §D.4 — Boşluk kapanış sırası

| # | Boşluk (§4) | Eşik | Öncelik | Kapanış koşulu |
|---|---|---|---|---|
| 1 | Sıra kilit ihlali | §D/10 | yüksek | negatif test yazıldı |
| 2 | CORS + Origin birliktelik | §D/6 | yüksek | birlikte test yazıldı |
| 3 | Gateway 5xx zarfı | §D/6 | orta | hata zarfı testi |
| 4 | Dispatcher öncelik/durdurma | §D/9 | orta | 2 test |
| 5 | 405 method ayrımı | §D/6 | orta | route testi |
| 6 | Coverage ölçümü | §D/3 | yüksek | rapor + sayı |

### §D.5 — Üretim kaydı

| # | Kayıt | Değer |
|---|---|---|
| 1 | Üretim tarihi | 2026-10-13 |
| 2 | Önceki §1–§10 | aynen korundu (2026-10-12) |
| 3 | Eklenen bölümler | §A · §B · §C · §D |
| 4 | Test dosyası tekrar sayımı | 44 (değişmedi) |
| 5 | Çalıştırma iddiası | yok (`vendor/` yok) |

### §A.5 — Katman–boşluk matrisi

| # | Katman | Test var | Test yok | Boşluk oranı |
|---|---|---|---|---|
| 1 | Birim (middleware) | 9 | 2 (§4.1/5–6) | düşük |
| 2 | Birim (pagerouter) | 6 | 2 (§4.3/4–5) | düşük |
| 3 | Birim (events) | 2 | 2 (§4.4/3–4) | orta |
| 4 | Birim (cache) | 0 | tüm adaptörler | yüksek |
| 5 | Birleşim (api) | 8 | 2 (§4.2/4–5) | düşük |
| 6 | Sözleşme | 0 | tümü | tam |
| 7 | E2E | 0 | tümü | tam |

### §A.6 — Piramit büyüme planı

| # | Hedef | Mevcut | Yol |
|---|---|---|---|
| 1 | Birim dosya ≥ sınıf sayısı | 35 dosya | her yeni sınıf +1 |
| 2 | Birleşim 1 test/uç | 8 | eksik uçlar |
| 3 | Sözleşme ≥ 1/servis | 0 | K012 |
| 4 | E2E ≥ 1/akış | 0 | K019 |
| 5 | Cache testi ≥ 1/adaptör | 0 | §D/4 önceliği |

### §B.5 — Test türü sayaç özeti

| # | Tür | Sayı (2026-10-13) | Değişim |
|---|---|---|---|
| 1 | Mutlu yol kapsayan | 15 | — |
| 2 | Red/fail-closed kapsayan | 3 | — |
| 3 | Sınır değeri kapsayan | 1 | — |
| 4 | Kısmi | 2 | — |
| 5 | Boşluk (test yok) | 6 | §4 listesi |
| 6 | Bekleyen katman (kod yok) | 5 | §4.5 |

### §B.6 — Negatif test kataloğu

| # | Negatif senaryo | Beklenen sonuç | İlgili bileşen | Durum |
|---|---|---|---|---|
| 1 | Boş CSRF token | 403 + log | `CsrfMiddleware` | ✅ |
| 2 | Bypass yolu yetkisiz | reddet | `BypassAuthMiddleware` | ✅ |
| 3 | Sınır aşımı | 429 | `RateLimiterMiddleware` | ✅ |
| 4 | Yetkisiz route | reddet | `AuthGuard` | ✅ |
| 5 | Geçersiz girdi | 400 | `ValidationMiddleware` | ✅ |
| 6 | Bilinmeyen sürüm | 404/410 | versioning | ⚠️ kısmi |
| 7 | Sıra ihlali | reddet | zincir | ⚠️ yok |
| 8 | Uzak 5xx | hata zarfı | `Gateway` | ⚠️ yok |
| 9 | Köken reddi | reddet | `OriginCheckMiddleware` | ✅ |
| 10 | Bilinmeyen metod | 405 | route tablosu | ⚠️ yok |

### §C.5 — Ortam doğrulama listesi

| # | Kontrol | Beklenen | Sonuç (2026-10-13) |
|---|---|---|---|
| 1 | `phpunit.xml` var mı? | evet | ✅ |
| 2 | `composer.json` PHPUnit sürümü | `^10.5` | ✅ (satır 24) |
| 3 | CI PHP major = composer PHP major | 8.4 = 8.4 | ✅ |
| 4 | `vendor/` var mı? | hayır | ⚠️ koşu kanıtı yok |
| 5 | Coverage raporu var mı? | hayır | ⚠️ |
| 6 | Fixture dosyaları var mı? | 2 | ✅ |
| 7 | SQLite yardımcıları var mı? | 2 | ✅ |

### §C.6 — Fixture kullanım kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Fixture değişmez | test girdisi sabitlenir |
| 2 | Yardımcı test sayılmaz | §C.2 sayımı etkilemez |
| 3 | Gerçek şema | repository testleri gerçek DDL ile |
| 4 | Ortam değişkeni yok | testler tek başına koşabilir |
| 5 | Temizlik | test sonrası durum sıfırlanır |

### §D.6 — Kapı çalışma akışı

| # | Adım | Kapı | Red durumu |
|---|---|---|---|
| 1 | PR aç | sınıf kapısı | test yoksa |
| 2 | Diff incele | güvenlik kapısı | güvenlik dosyası değiştiyse |
| 3 | Test koş | regresyon | kırmızıysa |
| 4 | Statik analiz | PHPStan | hata varsa |
| 5 | Coverage raporla | coverage | %80 altındaysa |
| 6 | Katman denetimi | katman kapısı | `verify-required` kapanmadıysa |

### §D.7 — Kapı–dosya eşlemesi

| # | Kapı | İlgili K011 dosyası |
|---|---|---|
| 1 | Sınıf kapısı | `application-kod-ornekleri.md` |
| 2 | Güvenlik kapısı | K009-security · `application-dogrulama.md` |
| 3 | Coverage | K019-testing |
| 4 | Regresyon | bu dosya §3 |
| 5 | Katman kapısı | `usecase-akislari/` · `command-query/` |
| 6 | Sözleşme | `odem-servisleri/` |

### §D.8 — Ölçüm özeti

| # | Ölçüm | Değer | Tarih |
|---|---|---|---|
| 1 | Test dosyası | 44 | 2026-10-13 |
| 2 | Gruplar | 9 · 8 · 6 · 4 · 2 | 2026-10-13 |
| 3 | Boşluk satırı | 6 (§B.5/5) | 2026-10-13 |
| 4 | Bekleyen katman | 5 (§B.5/6) | 2026-10-13 |
| 5 | Yasaklı desen | 0 | 2026-10-13 |
| 6 | Coverage | bilinmiyor | açık |

### §D.9 — Sonraki tur işleri

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | Coverage çalıştır + eşik onayı | §D/3 · §9/1 | QA |
| 2 | Negatif sıra testi | §D.4/1 | Backend |
| 3 | CORS + Origin birliktelik testi | §D.4/2 | Security |
| 4 | Gateway 5xx zarf testi | §D.4/3 | Backend |
| 5 | Dispatcher öncelik + durdurma testi | §D.4/4 | Backend |
| 6 | 405 route testi | §D.4/5 | Backend |
| 7 | Cache adaptör testi | §A.6/5 | QA |

### §D.10 — Kapanış kontrol listesi

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | Piramit katmanları tanımlı | 5 | ✅ (§A.1) |
| 2 | Test türleri envanteri | 15 satır | ✅ (§B.1) |
| 3 | Ortam kanıtları | 6 satır | ✅ (§C.1) |
| 4 | Kapılar ve eşikler | 10 satır | ✅ (§D.1) |
| 5 | Boşluklar görünür | 6 + 5 | ✅ |
| 6 | Çalıştırma iddiası yok | — | ✅ |
| 7 | Son satır formatı | Authority · Mode | ✅ |
| 8 | Uzunluk | ≥ 500 tam satır | ✅ |
| 9 | Yasaklı desen | 0 | ✅ |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
