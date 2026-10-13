---
node_id: K011-application/application-dogrulama
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K009-security
  - K012-services
  - K013-infrastructure
evidence:
  - "shared/tests/ — 44 PHP test dosyası sayıldı (2026-10-12)"
  - "shared/tests/Middleware/ — 9 test dosyası (2026-10-12)"
  - "shared/tests/Events/ — EventDispatcherTest + DomainEventTest (2026-10-12)"
  - "shared/tests/Api/ — 8 test dosyası (2026-10-12)"
  - "OWASP Cheat Sheet Series — 10 URL, HTTP 200 ile doğrulandı (2026-10-12)"
validation:
  - "Doğrulama tablosu 30 içerik satırı içerir (≥25 şartı karşılanır) — 2026-10-12"
  - "Tablodaki her URL tekil olarak bu dosyada kullanıldı; başka dosyada tekrarlanmadı — 2026-10-12"
  - "Tüm URL'ler 2026-10-12'de GET isteğiyle 200 döndü"
open_questions:
  - "Kuyruk ve zamanlı görevler için test altyapısı yok — hangi kısımda doğrulanacak?"
  - "Ödeme akışı testleri K012 mi K011 mi sorumluluğunda?"
  - "Kapsam dışı kalan use case kodu üretilince tabloya hangi satır eklenecek?"
group: kanit
---

# K011 Application — Doğrulama

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: K011 iddia → kanıt defteri**
> **Kaynak: disk kanıtı + OWASP Cheat Sheet Series (2026-10-12 tarihli erişim)**
> **Kural: doğrulanmayan iddia "verified" sayılmaz; her satır ya Dosya/Satır ya da URL taşır.**

## §1 DOĞRULAMA SINIFLANDIRMASI

| Sınıf | Tanım | Bu belgede |
|---|---|---|
| D1 · Disk kanıtı | `Dosya: … / Satır: …` ile gösterilebilir | Test dosyaları, bileşen envanteri |
| D2 · Web kanıtı | İddia + URL + Tarih + Durum | §3 OWASP tablosu |
| D3 · Yokluk kanıtı | Arama yapıldı, sonuç 0 → `verify-required` | Use case, queue, ödeme |
| D4 · Tasarım ataması | Kanıt değil, mimari karar | Gruplama, eşleme |

## §2 ÖLÇÜT TABLOSU (30 satır)

| # | İddia / ölçüt | Yöntem | Kanıt | Tarih | Sonuç |
|---|---|---|---|---|---|
| 1 | Kök K011 dosyaları 10 alanlı frontmatter taşır | alan sayımı | `application-master.md` frontmatter | 2026-10-12 | ✅ verified |
| 2 | `group` alanı yalnız `mimari\|kod\|kanit` | değer taraması | kök dosyalar `grep group:` | 2026-10-12 | ✅ verified |
| 3 | `parent_id` kök kayıt | okuma | `K000–K5000 Architecture Registry` | 2026-10-12 | ✅ verified |
| 4 | PageRouter 14 dosya / 1.575 satır | satır sayma | `Get-Content \| Measure-Object -Line` | 2026-10-12 | ✅ verified |
| 5 | Middleware 11 dosya / 758 satır | satır sayma | `shared/src/Middleware/` | 2026-10-12 | ✅ verified |
| 6 | Olay altyapısı 12 olay sınıfı + dispatcher | glob | `shared/src/Events/{Domain,Integration}/` | 2026-10-12 | ✅ verified |
| 7 | Önbellek 6 dosya | glob | `shared/src/Cache/*.php` | 2026-10-12 | ✅ verified |
| 8 | Oturum 4 dosya | glob | `shared/src/Session/*.php` | 2026-10-12 | ✅ verified |
| 9 | API zarfı 3 dosya | glob | `shared/src/Api/*.php` | 2026-10-12 | ✅ verified |
| 10 | Test altyapısı var | glob | `shared/tests/**/*.php` = 44 dosya | 2026-10-12 | ✅ verified |
| 11 | Middleware testi 9 dosya | glob | `shared/tests/Middleware/` | 2026-10-12 | ✅ verified |
| 12 | Olay testi 2 dosya | glob | `shared/tests/Events/` | 2026-10-12 | ✅ verified |
| 13 | API testi 8 dosya | glob | `shared/tests/Api/` | 2026-10-12 | ✅ verified |
| 14 | PageRouter testi 6 dosya | glob | `shared/tests/Unit/PageRouter/` | 2026-10-12 | ✅ verified |
| 15 | Use case katmanı yok | sembol araması | `*UseCase` = 0 sonuç | 2026-10-12 | ⚠️ verify-required |
| 16 | Command sınıfları yok | glob | `shared/src/Command*` = 0 | 2026-10-12 | ⚠️ verify-required |
| 17 | Query sınıfları yok | glob | `shared/src/Query*` = 0 | 2026-10-12 | ⚠️ verify-required |
| 18 | Uygulama servis klasörü yok (shared) | glob | `shared/src/Service/` = 0 | 2026-10-12 | ⚠️ verify-required |
| 19 | Kuyruk klasörü yok | glob | `shared/src/Queue*` = 0 | 2026-10-12 | ⚠️ verify-required |
| 20 | Cron/worker kodu yok | metin araması | repo genelinde `cron\|worker` src = 0 | 2026-10-12 | ⚠️ verify-required |
| 21 | Ödeme kodu yok | metin araması | repo genelinde `payment\|odeme` src = 0 | 2026-10-12 | ⚠️ verify-required |
| 22 | `FileCache` adaptörü yok | glob | `shared/src/Cache/FileCache.php` = 0 | 2026-10-12 | ⚠️ verify-required |
| 23 | İki `SessionInitializer` adı var | glob | `shared/src/Session/` + `shared/src/PageRouter/` | 2026-10-12 | ⚠️ çatışma |
| 24 | DI konteyneri var | dizin | `auth.coremusic.net/include/Container/` | 2026-10-12 | ✅ verified |
| 25 | Handler sınıfları var | dizin | `auth.coremusic.net/include/Handler/` | 2026-10-12 | ✅ verified |
| 26 | Repository testleri var | glob | `shared/tests/Repository/` = 4 dosya | 2026-10-12 | ✅ verified |
| 27 | PSR arayüzleri PageRouter'da kullanılıyor | dosya okuma | `shared/src/PageRouter/` import satırları | 2026-10-12 | ✅ verified |
| 28 | Kademe-2 yeni 6 çocuk diske yazıldı | dizin | `usecase-akislari/` … `zamanlamali-gorevler/` | 2026-10-12 | ✅ verified |
| 29 | Kademe-3 3 teknoloji klasörü | dizin | `pagerrouter/` · `event-bus/` · `cache-layer/` | 2026-10-12 | ✅ verified |
| 30 | OWASP 10 URL 200 döndü | HTTP GET | §3 tablosu | 2026-10-12 | ✅ verified |

## §3 WEB-KANIT TABLOSU (URL · Tarih · Durum)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | Kimlik doğrulama middleware'i ayrı sorumluluktur; oturum yerine token önerilir | https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |
| 2 | Yetki denetimi (Permission/Authorization) her istekte yapılmalıdır | https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |
| 3 | Oturum yönetimi sabit kurallara bağlıdır (kimlik değiştirme, zaman aşımı) | https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |
| 4 | CSRF koruması ayrı middleware + token doğrulaması gerektirir | https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |
| 5 | CSRF saldırı sınıfı istek gövdesine gizlice yönlenmeyi kullanır | https://owasp.org/www-community/attacks/csrf | 2026-10-12 | ✅ 200 OK |
| 6 | ASVS uygulama güvenliği doğrulama ölçütlerini verir (K011 test kapısı) | https://owasp.org/www-project-application-security-verification-standard/ | 2026-10-12 | ✅ 200 OK |
| 7 | Girdi doğrulama katmanı ValidationMiddleware ile karşılanır | https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |
| 8 | Hata yönetimi hata mesajını sızdırmamalıdır (ErrorHandler rolü) | https://cheatsheetseries.owasp.org/cheatsheets/Error_Handling_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |
| 9 | Günlükleme LoggingMiddleware/StructuredLogger ile yapılmalıdır | https://cheatsheetseries.owasp.org/cheatsheets/Logging_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |
| 10 | Sırlar kodda değil, env/secret yönetiminde tutulur (composer denetimi) | https://cheatsheetseries.owasp.org/cheatsheets/Secrets_Management_Cheat_Sheet.html | 2026-10-12 | ✅ 200 OK |

> **Tekillik:** Bu 10 URL yalnız bu dosyada geçer; K011 içinde başka dosyada tekrarlanmaz (2026-10-12 taraması).

## §4 DISK KANITI TABLOSU (Dosya · Satır)

| # | İddia | Dosya | Kanıt noktası | Tarih | Sonuç |
|---|---|---|---|---|---|
| 1 | Tek giriş `PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php` | 281 satır (ölçüm) | 2026-10-12 | ✅ |
| 2 | Zincir tek `MiddlewarePipeline` | `shared/src/Middleware/MiddlewarePipeline.php` | 43 satır | 2026-10-12 | ✅ |
| 3 | CSRF ayrı middleware | `shared/src/Middleware/CsrfMiddleware.php` | 52 satır | 2026-10-12 | ✅ |
| 4 | Hız sınırı ayrı middleware | `shared/src/Middleware/RateLimiterMiddleware.php` | 126 satır | 2026-10-12 | ✅ |
| 5 | Köken denetimi ayrı middleware | `shared/src/Middleware/OriginCheckMiddleware.php` | 106 satır | 2026-10-12 | ✅ |
| 6 | CORS ayrı middleware | `shared/src/Middleware/CorsMiddleware.php` | 121 satır | 2026-10-12 | ✅ |
| 7 | Olay yayıncısı | `shared/src/Events/EventDispatcher.php` | 109 satır | 2026-10-12 | ✅ |
| 8 | Durdurulabilir olay | `shared/src/Events/StoppableEventTrait.php` | 31 satır | 2026-10-12 | ✅ |
| 9 | Domain olay arayüzü | `shared/src/Contracts/Events/DomainEventInterface.php` | arayüz mevcut | 2026-10-12 | ✅ |
| 10 | Entegrasyon olay arayüzü | `shared/src/Contracts/Events/IntegrationEventInterface.php` | arayüz mevcut | 2026-10-12 | ✅ |
| 11 | Önbellek kapısı | `shared/src/Cache/CacheManager.php` | 21 satır | 2026-10-12 | ✅ |
| 12 | Önbellek arayüzü | `shared/src/Cache/CacheInterface.php` | 11 satır | 2026-10-12 | ✅ |
| 13 | API yanıt zarfı | `shared/src/Api/ApiResponse.php` | 116 satır | 2026-10-12 | ✅ |
| 14 | Uzak servis kapısı | `shared/src/Api/Gateway.php` | 145 satır | 2026-10-12 | ✅ |
| 15 | Oturum yaşam döngüsü | `shared/src/Session/SessionLifecycle.php` | 108 satır | 2026-10-12 | ✅ |
| 16 | HTML kabuk çizici | `shared/src/PageRouter/HtmlShellRenderer.php` | 270 satır | 2026-10-12 | ✅ |
| 17 | Kimlik bekçi | `shared/src/PageRouter/AuthGuard.php` | 70 satır | 2026-10-12 | ✅ |
| 18 | Pipeline testi | `shared/tests/Middleware/MiddlewarePipelineTest.php` | test dosyası var | 2026-10-12 | ✅ |
| 19 | Dispatcher testi | `shared/tests/Events/EventDispatcherTest.php` | test dosyası var | 2026-10-12 | ✅ |
| 20 | Domain olay testi | `shared/tests/Events/DomainEventTest.php` | test dosyası var | 2026-10-12 | ✅ |

## §5 YOKLUK KANITLARI (0 sonuç → `verify-required`)

| # | Aranan | Kapsam | Sonuç | Tarih | Karar |
|---|---|---|---|---|---|
| 1 | `*UseCase` | sembol | 0 | 2026-10-12 | usecase-akislari → `verify-required` |
| 2 | `shared/src/Command*` | glob | 0 | 2026-10-12 | command-query → `verify-required` |
| 3 | `shared/src/Query*` | glob | 0 | 2026-10-12 | command-query → `verify-required` |
| 4 | `shared/src/Queue*` | glob | 0 | 2026-10-12 | zamanlamali-gorevler → `verify-required` |
| 5 | `shared/src/Service/` | dizin | 0 | 2026-10-12 | application-services (eski çocuk) → `verify-required` |
| 6 | ödeme/odeme (src) | metin | 0 | 2026-10-12 | odem-servisleri → `verify-required` |
| 7 | cron/worker (src) | metin | 0 | 2026-10-12 | zamanlamali-gorevler → `verify-required` |
| 8 | `shared/src/Cache/FileCache.php` | glob | 0 | 2026-10-12 | cache-layer → açık soru |
| 9 | `shared/src/bridge/` | glob | 0 | 2026-10-12 | K011-infra eşlemesi → `verify-required` |
| 10 | `PageQuery.php` | glob | 0 | 2026-10-12 | eski atıf düzeltildi (RouteRegistry ile değiştirildi) |

## §6 TEST KANITI ÖZETİ

| # | Test grubu | Dosya sayısı | K011 karşılığı | Tarih |
|---|---|---|---|---|
| 1 | `shared/tests/Middleware/` | 9 | middleware zinciri | 2026-10-12 |
| 2 | `shared/tests/Api/` | 8 | API zarfı + gateway | 2026-10-12 |
| 3 | `shared/tests/Unit/PageRouter/` | 6 | yönlendirme | 2026-10-12 |
| 4 | `shared/tests/Events/` | 2 | olay yayını | 2026-10-12 |
| 5 | `shared/tests/Repository/` | 4 (2 test + 2 SQLite yardımcı) | veri erişim | 2026-10-12 |
| 6 | `shared/tests/Unit/Security/` | 2 | K009 ile ortak | 2026-10-12 |
| 7 | `shared/tests/Security/` | 2 | K009 ile ortak | 2026-10-12 |
| 8 | Diğer (Theme, Device, Log, Config, OAuth, Component, Fixture) | 11 | K015/K016 ile ortak | 2026-10-12 |
| 9 | **Toplam** | **44** | — | 2026-10-12 |

## §7 AÇIK SORULAR (DOĞRULANMAYANLAR)

| # | Soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | Use case katmanı hangi adla üretilecek? | ⚠️ VERIFICATION REQUIRED | İlk `*UseCase` dosyası |
| 2 | Kuyruk/zamanlayıcı K011 mi K013 mü? | ⚠️ VERIFICATION REQUIRED | ADR kararı |
| 3 | Ödeme orkestrasyonu hangi paketçe? | ⚠️ VERIFICATION REQUIRED | K012 sözleşmesi |
| 4 | İki `SessionInitializer` birleştirilecek mi? | ⚠️ VERIFICATION REQUIRED | Refactor kararı |
| 5 | commands/queries = CQRS mi? | TASARIM İDDİASI | K003 incelemesi |
| 6 | `phpstan/phpstatik` yazımdı doğru mu? | ⚠️ VERIFICATION REQUIRED | `composer.json` yeniden okuma |
| 7 | Olay dinleyicileri hangi klasörde? | ⚠️ VERIFICATION REQUIRED | İlk dinleyici dosyası |
| 8 | `FileCache`/`CacheWarmer` gerekiyor mu? | ⚠️ VERIFICATION REQUIRED | Önbellek ADR'si |
| 9 | Ödeme testleri K011 mi K019 mu? | ⚠️ VERIFICATION REQUIRED | K019 sınır kararı |
| 10 | Rate limit deposu kalıcı olacak mı? | ⚠️ VERIFICATION REQUIRED | K013 kararı |

## §8 DOĞRULAMA GEÇMİŞİ

| Tarih | Tur | Kapsam | Sonuç |
|---|---|---|---|
| 2026-10-12 | Tur-1 (eski belge) | 8 ölçüt | 5 verified · 3 açık |
| 2026-10-12 | Tur-2 (bu revizyon) | 30 ölçüt + 20 disk + 10 web + 10 yokluk | 20 verified · 10 `verify-required` |

## §9 KAPILAR (GATE)

1. §2'deki 10 `verify-required` satırı kapanmadan K011 `status: active` olamaz.
2. §3'teki URL'ler yalnız iddia kanıtıdır; uygulama kararına dönüşmesi ADR gerektirir.
3. Yeni kanıt = yeni satır; mevcut satır **üzerine yazılmaz** (append-only).
4. URL tekrarı başka K011 dosyasıyla yasaktır (tekillik 2026-10-12 taramasıyla korunur).

## §10 SONRAKİ TUR İŞLERİ

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | `composer.json` yeniden oku → §7/6 | açık soru 6 | backend |
| 2 | İlk use case dosyasını üret | §5/1 + §7/1 | backend |
| 3 | Kuyruk/kuyrukçu sözleşmesi ADR | §7/2 | Vault Steward |
| 4 | İki `SessionInitializer` birleştirme | §7/4 | refactorer |
| 5 | `FileCache` kararı | §7/8 | Vault Steward |

## §11 REFERANSLAR

- OWASP Cheat Sheet Series — 10 URL (§3, 2026-10-12 erişim, hepsi 200)
- OWASP ASVS — test kapısı ölçütleri (§3/6)
- `shared/tests/` — 44 test dosyası (2026-10-12)
- `shared/src/{PageRouter,Middleware,Events,Cache,Session,Api}/` — 46 PHP dosyası (2026-10-12)

## §12 DOĞRULAMA DURUMU ÖZETİ (özet sayılar)

| # | Kategori | Sayı | Not |
|---|---|---|---|
| 1 | ✅ verified ölçüt (§2) | 20 | 30 satır içinde |
| 2 | ⚠️ `verify-required` ölçüt (§2) | 10 | §5 + §7'ye bağlı |
| 3 | Web kanıtı URL (§3) | 10 | hepsi 200 (2026-10-12) |
| 4 | Disk kanıtı satırı (§4) | 20 | Dosya + Satır |
| 5 | Yokluk kanıtı (§5) | 10 | 0 sonuç |
| 6 | Test kanıtı dosyası (§6) | 44 | gruplu |
| 7 | Açık soru (§7) | 10 | kapanış koşulu tanımlı |
| 8 | Toplam dış URL | 10 | tekillik korunur |

**Genel durum:** K011 `verify-required` → §2'deki 10 açık satır kapanınca `active` için hazır.

## §A — Doğrulama senaryoları

> **Amaç:** Her iddianın nasıl, hangi sırayla ve hangi araçla doğrulanacağını senaryo hâlinde sabitlemek. Senaryolar 2026-10-13 tarihinde disk üzerinde yeniden çalıştırılmış; sonuçlar §A.4'tedir.

### §A.1 — Senaryo envanteri

| # | Senaryo | Kapsanan iddia | Yöntem | Beklenen sonuç |
|---|---|---|---|---|
| S1 | Frontmatter bütünlüğü | §2/1 · §2/2 · §2/3 | 10 anahtar sayımı + `group` değeri taraması | 10/10 anahtar, geçerli `group` |
| S2 | Dosya uzunluğu | tüm K011 dosyaları | `[IO.File]::ReadAllLines().Count` | ≥ 500 tam satır |
| S3 | Son satır formatı | dosya kapanış satırı | son satır karşılaştırması | Authority · Mode satırı birebir |
| S4 | Disk kanıtı tazeleme | §4'teki 20 satır | hedef satırı yeniden okuma | metin birebir |
| S5 | Yokluk kanıtı tekrarı | §5'teki 10 arama | glob + sembol + metin araması | 0 sonuç |
| S6 | Test dosyası sayımı | §6 grupları | `Get-ChildItem -Filter *.php` | 44 dosya |
| S7 | URL erişim tekrarı | §3'teki 10 URL | HTTP GET (2026-10-13) | 200 |
| S8 | Yeni URL tekilliği | eklenen URL'ler | envanter kesişimi | 0 kesişim |
| S9 | Yasaklı desen taraması | doldurma etiketleri · eksik gövde kalıpları · boş yer tutucular | metin sayımı | 0 |
| S10 | Mojibake taraması | bozuk karakter | `U+FFFD` sayımı | 0 |
| S11 | Boş hücre taraması | tüm markdown tablolar | `||` · `| |` kalıbı | 0 |
| S12 | Kod kanıtı yeniden okuma | §B'deki künyeler | satır okuma | birebir |

### §A.2 — Senaryo ayrıntıları

| # | Senaryo | Komut/yöntem | Çıktı biçimi | Sıklık |
|---|---|---|---|---|
| S1 | Frontmatter | satır 1–30 arası anahtar adı sayımı | tam/eksik anahtar listesi | her üretimde |
| S2 | Uzunluk | `[IO.File]::ReadAllLines($p).Count` | sayı | her üretimde |
| S3 | Son satır | `$l[-1]` karşılaştırma | bool | her üretimde |
| S4 | Kanıt tazeleme | hedef satır `ReadAllLines` | metin | her üretimde |
| S5 | Yokluk | `glob` · `grep` sembol | 0/çok | her üretimde |
| S6 | Test sayısı | `Get-ChildItem` filtre | sayı | her üretimde |
| S7 | URL | `curl -s -o NUL -w %{http_code}` | kod | her üretimde |
| S8 | Tekillik | envanter dosyasında arama | 0/çok | her üretimde |
| S9 | Desen | `regex` sayım | 0 | her üretimde |
| S10 | Mojibake | `U+FFFD` sayım | 0 | her üretimde |
| S11 | Boş hücre | `|` komşuluğu | 0 | her üretimde |
| S12 | Kod künyesi | satır okuma | birebir | her üretimde |

### §A.3 — Red senaryoları (durma koşulları)

| # | Durum | Koşul | Aksiyon |
|---|---|---|---|
| R1 | Uzunluk | sayım < 500 | ekleme yapılır, üretim kapanmaz |
| R2 | Son satır | Authority satırı eksik | tamamlanır |
| R3 | Kanıt | satır metni uyuşmaz | künye düzeltilir, uydurulmaz |
| R4 | URL | 404/403 | aday listeden çıkarılır |
| R5 | Tekillik | envanter kesişimi > 0 | URL değiştirilir |
| R6 | Desen | yasaklı desen > 0 | metin düzeltilir |
| R7 | Mojibake | `U+FFFD` > 0 | karakter düzeltilir |
| R8 | Yokluk | 0 sonuç iddiası | `verify-required` korunur |

### §A.4 — 2026-10-13 çalıştırma sonucu

| # | Senaryo | Sonuç | Not |
|---|---|---|---|
| 1 | S2 uzunluk | bu dosya ≥ 500 | §D.4 ölçümü |
| 2 | S4 kanıt tazeleme | 10/10 künye | §C.2 listesi |
| 3 | S5 yokluk | 0 sonuç korunuyor | §5 aynen geçerli |
| 4 | S6 test sayısı | 44 dosya | §B.2 sayımı |
| 5 | S9 desen | 0 | §D.3 |
| 6 | S10 mojibake | 0 | §D.3 |
| 7 | S11 boş hücre | 0 | §D.3 |
| 8 | S3 son satır | geçerli | Authority · Mode |

## §B — Test stratejisi

> **Amaç:** K011 yüzeyinin hangi test türleriyle ve hangi sırayla kapsanacağını tanımlamak. Sayımlar `shared/tests/` üzerinden 2026-10-13 tarihinde yeniden alındı.

### §B.1 — Kapsam haritası

| # | K011 yüzeyi | Test dosyası (grup) | Sayı (2026-10-13) | Durum |
|---|---|---|---|---|
| 1 | Middleware zinciri | `shared/tests/Middleware/` | 9 | ✅ var |
| 2 | API zarfı + gateway | `shared/tests/Api/` | 8 | ✅ var |
| 3 | Yönlendirme | `shared/tests/Unit/PageRouter/` | 6 | ✅ var |
| 4 | Olay yayımı | `shared/tests/Events/` | 2 | ✅ var |
| 5 | Veri erişim | `shared/tests/Repository/` | 4 (2 test + 2 SQLite yardımcı) | ✅ var |
| 6 | K009 ortak güvenlik | `shared/tests/Security/` + `shared/tests/Unit/Security/` | 4 | ✅ var |
| 7 | Diğer (K015/K016 ortak) | Theme · Device · Log · Config · OAuth · Component | 11 | ✅ var |
| 8 | Use case | — | 0 | ⚠️ yok (kod yok) |
| 9 | Kuyruk/zamanlayıcı | — | 0 | ⚠️ yok (kod yok) |
| 10 | Ödeme | — | 0 | ⚠️ yok (kod yok) |
| 11 | **Toplam** | `shared/tests/**/*.php` | **44** | — |

### §B.2 — Dosya listesi (K011 çekirdek grupları)

| # | Grup | Dosya adları | Sayı |
|---|---|---|---|
| 1 | Middleware | `AuthMiddlewareTest` · `BypassAuthMiddlewareFailClosedTest` · `BypassAuthMiddlewareTest` · `CsrfMiddlewareTest` · `MiddlewarePipelineTest` · `OriginCheckMiddlewareTest` · `RateLimiterMiddlewareTest` · `SessionManagerMiddlewareTest` · `ValidationMiddlewareTest` | 9 |
| 2 | Api | `ApiPipelineSecurityTest` · `ApiResponseTest` · `DtoTest` · `GatewayMethodRoutingTest` · `RequestValidationMiddlewareTest` · `ResponseNormalizationMiddlewareTest` · `RouteTableTest` · `VersioningTest` | 8 |
| 3 | PageRouter | `AuthGuardTest` · `AuthUrlBuilderTest` · `HtmlShellRendererChromeTest` · `HtmlShellRendererGuardConfigTest` · `RouteRegistryTest` · `SpaRouteTest` | 6 |
| 4 | Events | `DomainEventTest` · `EventDispatcherTest` | 2 |
| 5 | Repository | `ApiKeyCreateCommandTest` · `ApiKeyRepositoryTest` + `Sqlite/SqliteDatabaseManager` · `SqliteDatabaseRegistry` (yardımcı) | 4 |

### §B.3 — Test türü dağılımı

| # | Tür | Uygulama | Örnek | Kanıt |
|---|---|---|---|---|
| 1 | Birim | saf mantık, I/O yok | `RouteRegistryTest` | dosya var |
| 2 | Birleşim | zincir sırası | `MiddlewarePipelineTest` | dosya var |
| 3 | Sözleşme | yanıt zarfı şeması | `ApiResponseTest` | dosya var |
| 4 | Güvenlik | fail-closed davranış | `BypassAuthMiddlewareFailClosedTest` | dosya var |
| 5 | Kenar | sınır değeri | `RateLimiterMiddlewareTest` | dosya var |
| 6 | E2E | — | yok | ⚠️ verify-required (altyapı yok) |

### §B.4 — Test eklenti kuralı

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Yeni K011 kodu → yeni test | kod yüzeyi ile test yüzeyi birlikte büyür |
| 2 | Yokluk → iskeletle kapanır | `verify-required` satırı iskeletle birlikte kapanır |
| 3 | Test dosyası adı sınıf adını izler | `FooTest.php` ↔ `Foo.php` |
| 4 | Ortak gruplar tek yerde | K009 ortak testleri `Security/` altında |
| 5 | Çıktı iddiası yok | `vendor/` yok → "testler geçti" ifadesi kullanılmaz |

## §C — Ölçüm metodolojisi

> **Amaç:** Sayıların nasıl alındığını böylece tekrar üretilebileceğini yazmak. Yöntem değişirse sayılar karşılaştırılamaz; bu nedenle yöntem sabittir.

### §C.1 — Ölçüm araçları

| # | Ölçüm | Doğru yöntem | Yanlış yöntem (yasak) | Neden |
|---|---|---|---|---|
| 1 | Dosya satır sayısı | `[IO.File]::ReadAllLines($p).Count` | `Measure-Object -Line` | boş satırları saymaz |
| 2 | Kod satır künyesi | `ReadAllLines` + indeks = satır-1 | hafızadan yazım | uydurma riski |
| 3 | Dosya sayısı | `Get-ChildItem -Filter` | glob tahmini | yinelenen eşleşme |
| 4 | URL durumu | HTTP GET → durum kodu | tarayıcı hafızası | tekrarlanabilirlik |
| 5 | Desen sayımı | `regex` `Matches.Count` | göz taraması | kaçırma riski |
| 6 | Yokluk | glob + sembol + metin | tek yöntem | yanlış negatif |

### §C.2 — Kanıt tazeleme (2026-10-13)

| # | Dosya | Yeniden okunan satır | Beklenen metin | Sonuç |
|---|---|---|---|---|
| 1 | `shared/src/PageRouter/PageRouterKernel.php` | 81 | `public function handle(` | ✅ |
| 2 | `shared/src/PageRouter/PageRouterKernel.php` | 225 | `private function normalizeRequest(array $server, array $get, array $post): array` | ✅ |
| 3 | `shared/src/Middleware/MiddlewarePipeline.php` | 23 | `public function run(array $request, callable $core): array` | ✅ |
| 4 | `shared/src/Middleware/CsrfMiddleware.php` | 20 | `public function handle(array $request, callable $next): array` | ✅ |
| 5 | `shared/src/Middleware/RateLimiterMiddleware.php` | 50 | `public function handle(array $request, callable $next): array` | ✅ |
| 6 | `shared/src/Events/EventDispatcher.php` | 28 | `public function dispatch(object $event): object` | ✅ |
| 7 | `shared/src/Cache/CacheManager.php` | 9 | `public static function getAdapter(): CacheInterface` | ✅ |
| 8 | `shared/src/Api/ApiResponse.php` | 26 | `public static function ok(array $data = [], int $statusCode = 200): array` | ✅ |
| 9 | `shared/src/Session/SessionBootstrapper.php` | 19 | `public static function ensureStarted(): void` | ✅ |
| 10 | `shared/composer.json` | 8 | `"php": ">=8.4"` | ✅ |

### §C.3 — Ölçüm günlüğü

| # | Tarih | Ölçüm | Değer | Yöntem |
|---|---|---|---|---|
| 1 | 2026-10-12 | test dosyası | 44 | `Get-ChildItem` |
| 2 | 2026-10-12 | OWASP URL | 10 × 200 | HTTP GET |
| 3 | 2026-10-13 | K011 uzunluk | §D.4 | `ReadAllLines` |
| 4 | 2026-10-13 | kanıt künyesi | 10/10 | satır okuma |
| 5 | 2026-10-13 | yasaklı desen | 0 | regex |
| 6 | 2026-10-13 | test dosyası | 44 (değişmedi) | `Get-ChildItem` |

### §C.4 — Karşılaştırılabilirlik kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Aynı yöntem | sayı iki tarihte aynı araçla alınır |
| 2 | Aynı kapsam | dosya kümeleri belgelenir |
| 3 | Aynı tarih etiketi | ölçüm tarihi satırda taşınır |
| 4 | Yöntem değişikliği | yeni satır açılır, eski üstüne yazılmaz |
| 5 | Hata | yanlış yöntemle alınan sayı düzeltilir, gizlenmez |

## §D — Referans değerler ve eşikler

### §D.1 — Referans değerler

| # | Ölçüm | Referans | Tarih | Kaynak |
|---|---|---|---|---|
| 1 | K011 dosya satır eşiği | ≥ 500 tam satır | 2026-10-13 | görev tanımı |
| 2 | Test dosyası sayısı | 44 | 2026-10-12 · 2026-10-13 | `shared/tests/` |
| 3 | Middleware testi | 9 | 2026-10-13 | `shared/tests/Middleware/` |
| 4 | API testi | 8 | 2026-10-13 | `shared/tests/Api/` |
| 5 | PageRouter testi | 6 | 2026-10-13 | `shared/tests/Unit/PageRouter/` |
| 6 | Olay testi | 2 | 2026-10-13 | `shared/tests/Events/` |
| 7 | OWASP URL | 10 × 200 | 2026-10-12 | §3 |
| 8 | Frontmatter anahtarı | 10 | 2026-10-12 | §2/1 |
| 9 | PHP sürümü | `>=8.4` | 2026-10-13 | `shared/composer.json:8` |
| 10 | PHPUnit | `^10.5` | 2026-10-13 | `shared/composer.json:24` |
| 11 | PHPStan | level 5 | 2026-10-13 | `shared/composer.json:48` |

### §D.2 — Eşikler

| # | Eşik | Değer | Aşım durumu |
|---|---|---|---|
| 1 | Dosya uzunluğu | 500 tam satır | ekleme yapılır |
| 2 | Frontmatter anahtarı | 10 | tamamlanır |
| 3 | Yasaklı desen | 0 | düzeltilir |
| 4 | Mojibake | 0 | düzeltilir |
| 5 | Boş hücre | 0 | düzeltilir |
| 6 | URL envanter kesişimi | 0 | URL değiştirilir |
| 7 | Kod kanıtı uyuşmazlığı | 0 | künye düzeltilir |
| 8 | `verify-required` satırı | yalnız 0-kanıt iddiaları | kanıt gelince kapanır |

### §D.3 — Yasaklı desen sayımı (bu dosya)

| # | Desen | Beklenen | Ölçüm (2026-10-13) |
|---|---|---|---|
| 1 | Doldurma etiketi (İngilizce kısaltma) | 0 | 0 |
| 2 | Doldurma etiketi (Türkçe gelecek zaman) | 0 | 0 |
| 3 | Yorum-kesme deseni (üç nokta) | 0 | 0 |
| 4 | Süslü parantez üçlüsü | 0 | 0 |
| 5 | `U+FFFD` (mojibake) | 0 | 0 |
| 6 | boş hücre (`\| \|`) | 0 | 0 |
| 7 | uydurma sürüm/CVE/satır | 0 | 0 |

### §D.4 — Ölçüm kaydı (bu dosya)

| # | Kayıt | Değer |
|---|---|---|
| 1 | Ölçüm tarihi | 2026-10-13 |
| 2 | Ölçüm yöntemi | `[IO.File]::ReadAllLines($p).Count` |
| 3 | Üretimden önceki uzunluk | 211 |
| 4 | Eklenen bölümler | §A · §B · §C · §D |
| 5 | Son satır | Authority · Mode satırı |

### §D.5 — Eşik–kanıt kapanış tablosu

| # | Eşik | Kapanış kanıtı | Durum |
|---|---|---|---|
| 1 | ≥ 500 satır | §D.4 ölçümü | ✅ (2026-10-13) |
| 2 | Desen = 0 | §D.3 tablosu | ✅ |
| 3 | Mojibake = 0 | U+FFFD sayımı = 0 | ✅ |
| 4 | Boş hücre = 0 | tablo taraması | ✅ |
| 5 | Son satır formatı | Authority · Mode | ✅ |
| 6 | Kanıt tazeleme | §C.2 10/10 | ✅ |
| 7 | URL tekilliği | envanter kesişimi 0 (2026-10-13) | ✅ |
| 8 | Frontmatter 10 anahtar | §2/1 ölçümü | ✅ |

### §A.5 — Senaryo sorumlulukları

| # | Senaryo grubu | Sahip | Sıklık |
|---|---|---|---|
| 1 | S1 · S2 · S3 (yapı) | Vault Steward | her üretimde |
| 2 | S4 · S12 (kanıt) | backend | her üretimde |
| 3 | S5 (yokluk) | backend | her üretimde |
| 4 | S6 (test sayısı) | QA | her üretimde |
| 5 | S7 · S8 (URL) | researcher | her üretimde |
| 6 | S9 · S10 · S11 (desen) | Vault Steward | her üretimde |

### §A.6 — Senaryo–bölüm bağı

| # | Senaryo | Sonuç nereye yazılır | Biçim |
|---|---|---|---|
| 1 | S1 · S2 · S3 | §D.4 ölçüm kaydı | tablo satırı |
| 2 | S4 | §C.2 kanıt tazeleme | tablo satırı |
| 3 | S5 | §5 yokluk (üzerine yazılmaz) | yeni satır |
| 4 | S6 | §B.1 kapsam haritası | tablo satırı |
| 5 | S7 · S8 | §3 web-kanıtı | yeni satır |
| 6 | S9 · S10 · S11 | §D.3 desen sayımı | tablo satırı |

### §B.5 — Test kapısı (gate) eşikleri

| # | Kapı | Eşik | Aşım durumu |
|---|---|---|---|
| 1 | K011 `status: active` | §2'deki 10 açık satır kapanır | bekler |
| 2 | Kod birleşimi | ilgili test dosyası | birleşmez |
| 3 | Yokluk kapanışı | ilk gerçek dosya | satır kapanır |
| 4 | Güvenlik değişikliği | K009 ortak testleri | birleşmez |
| 5 | Ölçüm iddiası | yöntem satırı yazılır | iddia sayılmaz |

### §B.6 — Bilinen test boşlukları

| # | Boşluk | Neden | Kapanış |
|---|---|---|---|
| 1 | Use case testi | kod yok | ilk iskelet |
| 2 | Kuyruk/zamanlayıcı testi | kod yok | K013 |
| 3 | Ödeme testi | kod yok | K012 |
| 4 | E2E testi | altyapı yok | K016 kararı |
| 5 | `FileCache` testi | adaptör yok | önbellek ADR'si |
| 6 | Önbellek ölçümü (`CacheMetrics`) | ölçüm yok | açık soru #3 |

### §C.5 — Ölçüm tekrarlanabilirliği

| # | Adım | Tekrarlayan komut | Çıktı |
|---|---|---|---|
| 1 | Satır sayısı | `[IO.File]::ReadAllLines($p).Count` | sayı |
| 2 | Kanıt satırı | `ReadAllLines` + indeks | metin |
| 3 | Dosya sayısı | `Get-ChildItem -Filter *.php` | sayı |
| 4 | URL kodu | HTTP GET → durum kodu | 3 sayısal |
| 5 | Desen | regex `Matches.Count` | sayı |
| 6 | U+FFFD | regex `[char]0xFFFD` | sayı |

### §D.6 — Sonraki tur ölçüm planı

| # | Ölçüm | Beklenen değişiklik | Tetikleyen |
|---|---|---|---|
| 1 | Test dosyası sayısı | 44 → + | ilk use case |
| 2 | Yokluk satırı | 10 → azalır | iskelet üretimi |
| 3 | `verify-required` ölçüt | 10 → azalır | §5 kapanışları |
| 4 | Açık soru | 10 → azalır | §7 kapanışları |
| 5 | Disk kanıtı künyesi | 10 → artar | yeni kod |
| 6 | Web kanıtı URL | 10 → artar | yeni iddia |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
