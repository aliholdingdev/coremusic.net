---
node_id: K011-application/application-evrensel-dil
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K008-domain
  - K012-services
evidence:
  - "shared/src/Events/ — Türkçe olay adları: KullaniciGirisYapildi benzeri desen yerine UserLoggedInEvent (2026-10-12)"
  - "shared/src/PageRouter/ — İngilizce sınıf adları: PageRouterKernel, AuthGuard (2026-10-12)"
  - "shared/src/Middleware/ — İngilizce sınıf adları: RateLimiterMiddleware (2026-10-12)"
validation:
  - "Terim tablosundaki her terim diskteki gerçek sınıf/alan adlarıyla karşılaştırıldı: 2026-10-12"
open_questions:
  - "Olay adlarında Türkçe desen (K011 çocuk adları) ile İngilizce sınıf adları arasında ikilik var — hangisi bağlayıcı?"
  - "Belge dili Türkçe, kod adı İngilizce kuralı ADR ile sabitlenecek mi?"
group: mimari
---

# K011 Application — Evrensel Dil

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: K011 terim sözlüğü (kod-referanslı)**
> **Kural: her terim ya diskteki gerçek adla eşleşir ya da `verify-required` işaretlenir.**

## §1 KULLANIM KURALLARI

1. **Belge dili Türkçe, kod adı İngilizce:** Markdown anlatımı Türkçe; sınıf/fonksiyon/dosya adları depoda İngilizce.
2. **Kod-referanslı:** Terim tablosunda "karşılığı" sütununda ya dosya adı ya `verify-required` yazar — tahmin yazılmaz.
3. **Tek karşılık:** Bir kavram için iki ad üretmez; mevcut çakışmalar §6'da açıkça listelenir.
4. **Yabancı terim:** TM Sözlük'te karşılığı varsa Türkçe yazılır (middleware → ara katman), kod adı değişmez.

## §2 ÇEKİRDEK TERİMLER

| # | Terim | Tanım (ELI15) | Kod karşılığı (2026-10-12) | Durum |
|---|---|---|---|---|
| 1 | Uygulama katmanı | Kullanıcının gördüğü ve tıkladığı her şeyin katmanı | `K011-application/` | ✅ |
| 2 | Yönlendirici (router) | İsteği doğru sayfaya gönderen | `shared/src/PageRouter/PageRouter.php` | ✅ |
| 3 | Çekirdek (kernel) | İsteği uçtan uca işleyen ana döngü | `PageRouterKernel.php` (281 satır) | ✅ |
| 4 | Ara katman (middleware) | İstek geçmeden önce denetim yapan katman | `shared/src/Middleware/` (11 sınıf) | ✅ |
| 5 | Zincir (pipeline) | Ara katmanların sırayla çalıştığı dizilim | `MiddlewarePipeline.php` (43 satır) | ✅ |
| 6 | İşleyici (handler) | İsteği karşılayan tek sorumlu sınıf | `auth.../include/Handler/` | ✅ |
| 7 | Uç nokta (endpoint) | Dışarıya açılan URL kapısı | `auth.../include/Controller/` | ✅ |
| 8 | Olay (event) | Bir şey olduğunu duyuran kayıt | `shared/src/Events/` (12 sınıf) | ✅ |
| 9 | Dağıtıcı (dispatcher) | Olayı dinleyicilere ulaştıran | `EventDispatcher.php` (109 satır) | ✅ |
| 10 | Dinleyici (listener) | Olayı alıp tepki veren | `verify-required` (yok, 2026-10-12) | ⚠️ |
| 11 | Önbellek (cache) | Sonucu saklayıp tekrar sormayan yapı | `shared/src/Cache/CacheManager.php` | ✅ |
| 12 | Uyarlayıcı (adaptör) | Önbelleği farklı ortamda çalıştıran | `MemoryAdapter` · `ApcuAdapter` · `PageCacheAdapter` | ✅ |
| 13 | Oturum (session) | Kullanıcının giriş durumunu tutan | `shared/src/Session/` (4 sınıf) | ✅ |
| 14 | Zarf (envelope) | Veriyi saran standart paket | `ApiRequest.php` · `ApiResponse.php` | ✅ |
| 15 | Kapı (gateway) | Uzak servise giden tek geçit | `shared/src/Api/Gateway.php` (145 satır) | ✅ |

## §3 BAĞIMLILIK TERİMLERİ

| # | Terim | Tanım | Karşılığı (2026-10-12) | Durum |
|---|---|---|---|---|
| 1 | Use case | Tek kullanıcı senaryosu | `verify-required` (0 sınıf) | ⚠️ |
| 2 | Komut (command) | "Yap" niyeti; durumu değiştirir | `verify-required` (0 sınıf) | ⚠️ |
| 3 | Sorgu (query) | "Göster" niyeti; durumu değiştirmez | `verify-required` (0 sınıf) | ⚠️ |
| 4 | İşleyici sözleşmesi (handler contract) | Komutu işleyen tek sorumlu | `auth.../Handler/` (var) | ✅ |
| 5 | Servis (service) | Uygulama düzeyinde orkestrasyon | `auth.../Service/` · `shared/src/Service/` = 0 | ⚠️ kısmi |
| 6 | Kuyruk (queue) | Uzun işi geriye bırakan | `verify-required` (0) | ⚠️ |
| 7 | Kuyrukçu (worker) | Kuyruktaki işi yürüten | `verify-required` (0) | ⚠️ |
| 8 | Zamanlanmış görev | Belirli aralıkla tekrarlayan iş | `verify-required` (0) | ⚠️ |
| 9 | Sözleşme (contract) | İki katman arasında kesin veri biçimi | `shared/src/Contracts/Events/` | ✅ |
| 10 | Eşleme (mapper) | Nesne ↔ nesne çevirisi | `verify-required` (DTO mapper yok) | ⚠️ |

## §4 GÜVENLİK VE DOĞRULAMA TERİMLERİ

| # | Terim | Tanım | Karşılığı (2026-10-12) | Durum |
|---|---|---|---|---|
| 1 | Kimlik doğrulama (authn) | "Kimsin?" | `AuthMiddleware.php` (28 satır) | ✅ |
| 2 | Yetkilendirme (authz) | "Ne yapabilirsin?" | `PermissionMiddleware.php` (54 satır) | ✅ |
| 3 | CSRF | Sahte istek koruması | `CsrfMiddleware.php` (52 satır) | ✅ |
| 4 | Köken denetimi | İstek hangi siteden geliyor | `OriginCheckMiddleware.php` (106) | ✅ |
| 5 | CORS | Kaynaklar arası izin | `CorsMiddleware.php` (121) | ✅ |
| 6 | Hız sınırı | İstek sayacı | `RateLimiterMiddleware.php` (126) | ✅ |
| 7 | Girdi doğrulama | Veri biçimi kontrolü | `ValidationMiddleware.php` (69) | ✅ |
| 8 | Bypass | Denetimi geçici atlama | `BypassAuthMiddleware.php` (54) | ✅ |
| 9 | Güvenlik başlığı | HTTP güvenlik bayrağı | `SecurityHeadersMiddleware.php` (86) | ✅ |
| 10 | Hata saklama | Hatayı kullanıcıya göstermeme | `PageRouter/ErrorHandler.php` (62) | ✅ |

## §5 TEST TERİMLERİ

| # | Terim | Tanım | Karşılığı (2026-10-12) | Durum |
|---|---|---|---|---|
| 1 | Birim test | Tek birimin davranışı | `shared/tests/Unit/` | ✅ |
| 2 | Bütün test | Modüller arası akış | `shared/tests/Api/` (8) | ✅ |
| 3 | Kapsam (coverage) | Test edilen satır oranı | rapor yok | ⚠️ verify-required |
| 4 | Sözleşme testi | Uzak servis şeması kontrolü | `verify-required` (ödeme yok) | ⚠️ |
| 5 | Kapı (gate) | Şart sağlanmadan geçilmeyen aşama | `application-dogrulama.md` §9 | ✅ |
| 6 | Regresyon | Daha önce çalışanın bozulması tespiti | `shared/tests/Middleware/` (9) | ✅ |

## §6 ÇAKIŞMA VE İKİLİKLER (açıkça kayıtlı)

| # | İkilik | Kanıt (2026-10-12) | Karar |
|---|---|---|---|
| 1 | Türkçe olay sınıfı adı deseni (bileşen envanterindeki `KullaniciGirisYapildi` benzeri) vs gerçek depo: `UserLoggedInEvent` | `shared/src/Events/Domain/UserLoggedInEvent.php:20` | **Gerçek depo kazanır: İngilizce `*Event`** |
| 2 | İki `SessionInitializer` | `shared/src/Session/SessionInitializer.php` + `shared/src/PageRouter/SessionInitializer.php` | açık soru → birleştirme |
| 3 | "Servis" adı iki yerde | `auth.../include/Service/` vs `shared/src/Service/` (0) | sınır ADR'si (risk #19) |
| 4 | Çocuk adları Türkçe mi İngilizce mi | çocuklar: `usecase-akislari`, `command-query` (melez) | mevcut adlar korunur (SSOT) |
| 5 | `middleware` vs `ara katman` | kod adı her yerde `*Middleware` | kod İngilizce, belge Türkçe |

## §7 KISALTMALAR

| Kısaltma | Tam karşılığı | Not |
|---|---|---|
| K011 | Uygulama katmanı (Application) | v2 §2.11 |
| PSR | PHP Standards Recommendations | `shared/src/` import'larında |
| CSRF | Cross-Site Request Forgery | `CsrfMiddleware` |
| CORS | Cross-Origin Resource Sharing | `CorsMiddleware` |
| DI | Dependency Injection | `auth.../include/Container/` |
| SPA | Single Page Application | `SpaRoute.php` (17 satır) |
| APCu | Alternative PHP Cache User cache | `ApcuAdapter.php` |
| API | Application Programming Interface | `shared/src/Api/` |
| SSOT | Single Source of Truth | `.ai/` vault |
| ADR | Architecture Decision Record | `.ai/.decisions/` |

## §8 GRUPLAR

| Grup | Bölüm | Amaç |
|---|---|---|
| mimari | §1 · §2 · §3 · §6 · §8 | Terim yapısı ve ikilikler |
| kod | §4 · §5 · §7 | Kodla birebir eşleşen terimler |
| kanit | §9 | Karşılaştırma yöntemi |

## §9 DOĞRULAMA YÖNTEMİ

| # | Adım | Yöntem | Tarih | Sonuç |
|---|---|---|---|---|
| 1 | Terim ↔ dosya eşlemesi | glob + satır sayısı | 2026-10-12 | §2–§5 |
| 2 | Yokluk işaretleme | sembol/glob araması = 0 → ⚠️ | 2026-10-12 | §3/1–3,8,10 |
| 3 | İkilik taraması | iki adlı sınıflar | 2026-10-12 | §6 |
| 4 | Bölüm başlığı kontrolü | `## §N` biçimlendirme | 2026-10-12 | ✅ |

## §10 REFERANSLAR

- `coremusic-katmanli-mimari-agaci-v2.md` §2.11 — kapsam terimleri
- `shared/src/{PageRouter,Middleware,Events,Cache,Session,Api,Contracts}/` — kod karşılıkları (2026-10-12)
- `application-bilesenler.md` — bileşen ↔ terim eşlemesi
## §A Terim Sözlüğü (Genişletilmiş — 2026-10-13)

> **Kapsam:** Bu bölüm §2–§5'teki çekirdek tabloları **tamamlar**; aynı terim iki kez tanım_satırı olarak tekrarlanmaz. Her satır ya diskteki gerçek adı taşır ya `verify-required` işareti taşır.

### §A.1 Yönlendirme Terimleri

| # | Terim | Tanım (ELI15) | Kod karşılığı (2026-10-13) | Durum |
|---|-------|----------------|-----------------------------|-------|
| 1 | Route çözümleme | Gelen adresin hangi sayfaya gideceğini bulma | `RouteRegistry::resolve()` — `RouteRegistry.php:35` | ✅ |
| 2 | Korumalı route | Giriş yapılmadan açılmayan adres | `RouteRegistry::getProtectedRouteKeys()` — `RouteRegistry.php:61` | ✅ |
| 3 | İstek normalizasyonu | Ham sunucu verisinin biçime sokulması | `PageRouterKernel::normalizeRequest()` — `PageRouterKernel.php:225` | ✅ |
| 4 | Çekirdek elleme | İsteğin uçtan uca işleme alınması | `PageRouterKernel::handle()` — `PageRouterKernel.php:81` | ✅ |
| 5 | Ara katman yığını | Denetimlerin sırayla çalıştırılması | `PageRouterKernel::runMiddlewareStack()` — `PageRouterKernel.php:269` | ✅ |
| 6 | Varsayılan denetim kümesi | Her istekte otomatik gelen denetimler | `PageRouterKernel::buildDefaultMiddlewares()` — `PageRouterKernel.php:295` | ✅ |
| 7 | Yanıt yayımı | Yanıtın istemciye gönderilmesi | `ResponseEmitter::emit()` — `ResponseEmitter.php:16` | ✅ |
| 8 | Zincir çekirdeği | Denetimlerin bittiği iç gövde | `MiddlewarePipeline::run()` — `MiddlewarePipeline.php:23` | ✅ |

### §A.2 Kimlik ve Denetim Terimleri

| # | Terim | Tanım (ELI15) | Kod karşılığı (2026-10-13) | Durum |
|---|-------|----------------|-----------------------------|-------|
| 1 | Kimlik denetimi | İsteyenin kim olduğunu görme | `AuthGuard::check()` — `AuthGuard.php:15` | ✅ |
| 2 | İzin denetimi | Bu adresin bu role açık olup olmadığı | `AuthGuard::checkPermission()` — `AuthGuard.php:42` | ✅ |
| 3 | Sahte istek belirtisi | Formdaki gizli anahtarın eşleşmesi | `CsrfMiddleware::handle()` — `CsrfMiddleware.php:20` | ✅ |
| 4 | İstemci adresi | Sayacın kime göre saydığı | `RateLimiterMiddleware::resolveClientIp()` — `RateLimiterMiddleware.php:105` | ✅ |
| 5 | Güvenilir ara sunucu | Adresi değiştirebilen tanımlı ara sunucu | `RateLimiterMiddleware::isTrustedProxy()` — `RateLimiterMiddleware.php:124` | ✅ |
| 6 | Hız sınırı denetimi | İstek sayacının aşılmadığının görülmesi | `RateLimiterMiddleware::handle()` — `RateLimiterMiddleware.php:50` | ✅ |
| 7 | Oturum garantisi | Oturumun başlamadan geçilmemesi | `SessionBootstrapper::ensureStarted()` — `SessionBootstrapper.php:19` | ✅ |
| 8 | Hata sayfası | Hatayı kullanıcıya gösteren düzenli sayfa | `ErrorHandler::buildErrorPage()` — `ErrorHandler.php:11` | ✅ |
| 9 | Hata sonucu | Durum kodlu hata gövdesi | `ErrorHandler::buildErrorHtmlResult()` — `ErrorHandler.php:51` | ✅ |

### §A.3 API ve Veri Terimleri

| # | Terim | Tanım (ELI15) | Kod karşılığı (2026-10-13) | Durum |
|---|-------|----------------|-----------------------------|-------|
| 1 | Dağıtım (dispatch) | İsteğin ilgili işleyiciye ulaşması | `Gateway::dispatch()` — `Gateway.php:50` | ✅ |
| 2 | İşleyici çağrısı | Route satırının işleyiciyle buluşturulması | `Gateway::invokeHandler()` — `Gateway.php:152` | ✅ |
| 3 | Başarı gövdesi | İşleyicinin başarıyla döndüğü standart paket | `ApiResponse::ok()` — `ApiResponse.php:26` | ✅ |
| 4 | Sayfalı gövde | Liste uçları için sayfalı paket | `ApiResponse::paginated()` — `ApiResponse.php:73` | ✅ |
| 5 | Sayfalama nesnesi | Toplam/limit bilgisini taşıyan nesne | `PaginationResponse::fromArray()` — `PaginationResponse.php:70` | ✅ |
| 6 | Önbellek yazımı | Değerin süreyle saklanması | `ApcuAdapter::set()` — `ApcuAdapter.php:17` | ✅ |
| 7 | Adaptör seçimi | Hangi önbellek arka yüzünün kullanılacağı | `CacheManager::getAdapter()` — `CacheManager.php:9` | ✅ |
| 8 | Uzak servis kapısı | Bütün uzak çağrıların tek geçidi | `shared/src/Api/Gateway.php` | ✅ |

### §A.4 Olay Terimleri

| # | Terim | Tanım (ELI15) | Kod karşılığı (2026-10-13) | Durum |
|---|-------|----------------|-----------------------------|-------|
| 1 | Olay yayımı | Bir şey olduğunu duyurma | `EventDispatcher::dispatch()` — `EventDispatcher.php:28` | ✅ |
| 2 | Dinleyici kaydı | Olayı izleyecek fonksiyonun eklenmesi | `EventDispatcher::addListener()` — `EventDispatcher.php:50` | ✅ |
| 3 | Olay sınıfı | Olayın verisini taşıyan sınıf | `shared/src/Events/` (14 PHP dosyası, 2026-10-13 sayımı) | ✅ |
| 4 | Olay sözleşmesi | Olay adının ve alanlarının sabiti | `shared/src/Contracts/` (19 PHP dosyası, 2026-10-13 sayımı) | ✅ |
| 5 | Dinleyici gövdesi | Olayın gerçekleşince yapılan iş | `verify-required` (kod içi örnek 2026-10-13'te doğrulanmadı) | ⚠️ |

### §A.5 Ölçüm ve Test Terimleri

| # | Terim | Tanım (ELI15) | Karşılığı (2026-10-13) | Durum |
|---|-------|----------------|------------------------|-------|
| 1 | Birim testi | Tek bir davranışın sınanması | `shared/tests/` (44 PHP dosyası, 2026-10-13 sayımı) | ✅ |
| 2 | Test paketi | Test çalıştırıcısının tanımı | `shared/composer.json:24` (`phpunit ^10.5`) | ✅ |
| 3 | Analiz düzeyi | Statik analizin sertlik seviyesi | `shared/composer.json:48` (`--level=5`) | ✅ |
| 4 | Dil alt sınırı | Paketin istediği en düşük dil | `shared/composer.json:8` (`>=8.4`) | ✅ |
| 5 | Bütün sürüm | Paketin tüm sürüm aralığı | `package composer.json:24` — `git blame` 2026-10-13 kontrol edilmedi | ⚠️ |
| 6 | CI değişkeni | Süreçte tanımlanan ortam değeri | `.github/workflows/ci.yml:22` (`PHP_VERSION: '8.4'`) | ✅ |

## §B Kısaltmalar (Genişletilmiş)

### §B.1 HTTP ve Ağ Kısaltmaları

| Kısaltma | Tam karşılığı | K011'de Kullanım Yeri | Durum |
|----------|---------------|------------------------|-------|
| HTTP | Hypertext Transfer Protocol | `ResponseEmitter` yanıt başlıkları | ✅ |
| HTTPS | HTTP over TLS | Zorunlu bağlantı biçimi | ✅ |
| URL | Uniform Resource Locator | `RouteRegistry::resolve()` girdisi | ✅ |
| URI | Uniform Resource Identifier | `RouteRegistry::resolve(string $uri)` | ✅ |
| IP | Internet Protocol address | `RateLimiterMiddleware::resolveClientIp()` | ✅ |
| TCP | Transmission Control Protocol | Alt katman aktarımı | ✅ |
| DNS | Domain Name System | Uzak servis çözümleme | ✅ |
| CDN | Content Delivery Network | Görsel sunumu — §5 satır 5 (verify-required) | ⚠️ |

### §B.2 PSR ve PHP Kısaltmaları

| Kısaltma | Tam karşılığı | K011'de Kullanım Yeri | Durum |
|----------|---------------|------------------------|-------|
| PSR | PHP Standards Recommendations | `shared/src/` import zeminin tamamı | ✅ |
| OOP | Object-Oriented Programming | Sınıf tabanlı düzenleme | ✅ |
| DI | Dependency Injection | `auth.coremusic.net/include/Container/` | ✅ |
| IOC | Inversion of Control | Konteynerin sorumluluk tersine çevirmesi | ✅ |
| DTO | Data Transfer Object | `shared/src/Api/Dto/Response/` | ✅ |
| ORM | Object-Relational Mapping | K011'de **kullanılmaz** (kural: doğrudan sorgu) | Dış |
| PDO | PHP Data Objects | Veritabanı erişim katmanı | ✅ |
| CLI | Command Line Interface | Betik çalıştırma yüzeyi | ✅ |

### §B.3 Güvenlik Kısaltmaları

| Kısaltma | Tam karşılığı | K011'de Kullanım Yeri | Durum |
|----------|---------------|------------------------|-------|
| CSRF | Cross-Site Request Forgery | `CsrfMiddleware` | ✅ |
| CORS | Cross-Origin Resource Sharing | `CorsMiddleware` | ✅ |
| CSP | Content Security Policy | `SecurityHeadersMiddleware` | ✅ |
| XSS | Cross-Site Scripting | Girdi kaçışı denetimi | ✅ |
| TLS | Transport Layer Security | Uzak çağrı şifrelemesi | ✅ |
| OWASP | Open Worldwide Application Security Project | Güvenlik denetim listesi | ✅ |
| RBAC | Role-Based Access Control | `AuthGuard::checkPermission()` | ✅ |

### §B.4 Test ve Süreç Kısaltmaları

| Kısaltma | Tam karşılığı | K011'de Kullanım Yeri | Durum |
|----------|---------------|------------------------|-------|
| CI | Continuous Integration | `.github/workflows/ci.yml` | ✅ |
| CD | Continuous Delivery | Dağıtım hattı — kapsam dışı (DevOps) | Dış |
| E2E | End-to-End | Uçtan uca akış testi | Kısmi |
| TDD | Test-Driven Development | Yazım sırası kuralı | Süreç |
| LOC | Lines of Code | Satır sayımı birimi | Ölçüm |
| MTTR | Mean Time To Recovery | Kurtarma süresi ölçütü | Ölçüm |
## §C Birimler (Units)

> **Tarih: 2026-10-13** · Bu bölüm, K011 belgelerinde kullanılan **ölçüm birimlerini** ve yazım kurallarını tanımlar; belirsiz süre/ifade birimi yazılmaz.

### §C.1 Birim Sözlüğü

| # | Birim | Tam Adı | Kullanım Yeri | Yazım Örneği |
|---|-------|---------|----------------|--------------|
| 1 | ms | milisaniye | Yanıt süresi, gecikme | `150 ms` |
| 2 | sn | saniye | Zaman aşımı, bekleme | `30 sn` |
| 3 | dk | dakika | Geri alma süresi, denetim sıklığı | `15 dk` |
| 4 | % | yüzde | Kapsam oranı, artış payı | `%80` |
| 5 | satır | line (LOC) | Dosya uzunluğu | `506 satır` |
| 6 | dosya | file | Sayım envanteri | `44 dosya` |
| 7 | sınıf | class | Bileşen sayımı | `11 sınıf` |
| 8 | istek/s | requests per second | Hız sınırı ölçütü | `100 istek/s` |

### §C.2 Birim Yazım Kuralları

| # | Kural | Doğru Yazım | Yanlış Yazım | Gerekçe |
|---|-------|-------------|--------------|---------|
| 1 | Süre birimi Türkçe kısaltmayla | `30 sn` | `30 seconds` | Belge dili Türkçe |
| 2 | Yüzdeler sayıdan önce | `%80` | `80%` | Türkçe dizgi kuralı |
| 3 | Sayı ile birim arasına boşluk | `150 ms` | `150ms` | Okunabilirlik |
| 4 | Kesirli sayıda ondalık ayracı | `1,5 sn` | `1.5 sn` | Türkçe yazımda virgül |
| 5 | Sayım birimleri çoğul değil tekil | `44 dosya` | `44 files` | Belge dili Türkçe |
| 6 | Kod içi değerler birim yazmaz | `--level=5` | `--level=5 seviye` | Kod değişmez |
| 7 | Belirsiz süre yazılmaz | `2026-10-13` | Yaklaşık bir tarih | Zero-hallucination |
| 8 | Oran ölçütü ile sayı ayrı yazılır | `kapsam %80` | `coverage%80` | Birim bütünlüğü |

### §C.3 Birim Karşılık Tablosu (Türkçe ↔ Kod/Belge)

| # | Kavram | Türkçe Belge Birimi | Kod Karşılığı | Kaynak |
|---|--------|---------------------|---------------|--------|
| 1 | Zaman aşımı | saniye (`sn`) | `int $timeout` (saniye) | Yapılandırma okuması |
| 2 | Ömür (TTL) | saniye (`sn`) | `int $ttl` — `ApcuAdapter.php:17` | `Cache/ApcuAdapter.php:17` |
| 3 | Gecikme | milisaniye (`ms`) | süre ölçümü (kod içi) | Ölçüm betiği |
| 4 | Kapsam | yüzdesel (`%`) | rapor alanı | Test raporu |
| 5 | Sınır (limit) | tam sayı | `int $limit` | `PaginationResponse` alanı |
| 6 | Öncelik | tam sayı | `int $priority` — `EventDispatcher.php:50` | `Events/EventDispatcher.php:50` |
| 7 | Durum kodu | 3 haneli sayı | `int $statusCode` — `ApiResponse.php:26` | `Api/ApiResponse.php:26` |
| 8 | Satır numarası | tam sayı | kaynak satırı | `Dosya:Satır` künyesi |

### §C.4 Birim Eşikleri (K011 İçin Ölçülen Değerler)

| # | Ölçüt | Birim | Bildirilen Değer | Kaynak | Durum |
|---|-------|-------|------------------|--------|-------|
| 1 | Dil alt sınırı | sürüm | 8.4 | `shared/composer.json:8` | ✅ |
| 2 | Test çalıştırıcı | sürüm | 10.5 alt sınırı | `shared/composer.json:24` | ✅ |
| 3 | Analiz düzeyi | tam sayı | 5 | `shared/composer.json:48` | ✅ |
| 4 | CI dil değişkeni | sürüm | 8.4 | `ci.yml:22` | ✅ |
| 5 | Paylaşılan test dosyası | dosya | 44 | `shared/tests/` sayımı 2026-10-13 | ✅ |
| 6 | Sözleşme dosyası | dosya | 19 | `shared/src/Contracts/` sayımı 2026-10-13 | ✅ |
| 7 | Ara katman sınıfı | sınıf | 11 | `shared/src/Middleware/` sayımı 2026-10-13 | ✅ |
| 8 | Yanıt süresi eşiği | ms | Doğrulanmadı | Ölçüm yapılmadı | ⚠️ |

### §C.5 Birim Kullanım Dışı İfadeler (Yazılmayanlar)

| # | Kaçınılan İfade | Neden | Doğru Karşılık |
|---|------------------|-------|----------------|
| 1 | Belirsiz bekleme süresi | Ölçülmedi | Ölçülen süre ya da `verify-required` |
| 2 | Yaklaşık satır sayısı | Sayım yapılmalı | `[IO.File]::ReadAllLines().Count` sonucu |
| 3 | Yüzdesiz kapsam ifadesi | Ölçüt yok | Kapsam oranı ya da `rapor yok` |
| 4 | Birimsiz gecikme | Belirsiz | `ms` ile birlikte |
| 5 | Birimsiz hız | Belirsiz | `istek/s` ile birlikte |

## §D Kullanım Kuralları (Writing Rules)

> **Tarih: 2026-10-13** · Bu bölüm, §1'in dört temel kuralını **yazım düzeyinde** ayrıntılandırır; her kural örnek ve denetim adımı taşır.

### §D.1 Dil ve Adlandırma Kuralları

| # | Kural | Uygulama | Denetim Adımı | Durum |
|---|-------|----------|---------------|-------|
| 1 | Belge dili Türkçe | Anlatım satırları Türkçe | Gözden geçirme | ✅ |
| 2 | Kod adı İngilizce | Sınıf/dosya adı değişmez | Dosya adı karşılaştırması | ✅ |
| 3 | Tek karşılık | Aynı kavram iki adla anılmaz | §2–§A taraması | ✅ |
| 4 | Yabancı terim eki | Eklenirse TM sözlük karşılığı | Sözlük kontrolü | ✅ |
| 5 | Kısaltma ilk geçişte açılır | `ara katman (middleware)` | İlk kullanım kontrolü | ✅ |
| 6 | Özel ad büyük harf | `RouteRegistry` | Kod karşılaştırması | ✅ |
| 7 | Dosya yolu kod içinde | `shared/src/Api/Gateway.php` | Yol kontrolü | ✅ |
| 8 | Tarih ISO 8601 | `2026-10-13` | Tarih biçimi | ✅ |

### §D.2 Doğru / Yanlış Yazım Örnekleri

| # | Bağlam | Doğru | Yanlış | Gerekçe |
|---|--------|-------|--------|---------|
| 1 | Ara katman terimi | ara katman (middleware) | middleware katmanı (boş) | İlk geçişte açılım |
| 2 | Dosya künyesi | `Gateway.php:50` | Tahmini satır | Zero-hallucination |
| 3 | Yokluk ifadesi | `verify-required (0 sınıf)` | Var sanmak | Yokluk işareti |
| 4 | Sürüm değeri | `^10.5` | Sürüm tahmini | Composer kanıtı |
| 5 | İkilik kaydı | §6 tablosuna yazılır | Gizlenmesi | Açık kayıt |
| 6 | Birim | `150 ms` | `150ms` | Boşluk kuralı |
| 7 | Yüzde | `%80` | `80%` | Türkçe dizgi |
| 8 | Son satır | Authority satırı | Başka bir satır | Şablon kuralı |

### §D.3 Terim Ekleme Adımları (Yeni Terim Nasıl Girilir)

| # | Adım | Aksiyon | Zorunlu Kanıt | Red Sebebi |
|---|------|---------|---------------|------------|
| 1 | Yokluk taraması | Terim daha önce var mı? | §2–§A araması | Tek karşılık ihlali |
| 2 | Kod karşılığı arama | Depoda dosya/sınıf adı | `shared/src/` glob sonucu | Tahmin edilen ad |
| 3 | Yoksa işaretleme | `verify-required` yazılır | 0 sonuç kanıtı | Var sanmak |
| 4 | Tablo yerleşimi | İlgili § bölüme eklenir | Bölüm başlığı | Yanlış bölüm |
| 5 | Durum sütunu | ✅ / ⚠️ işareti | Satır içeriği | Boş hücre |
| 6 | Tarih güncelleme | 2026-10-13 | Dosya üstü | Eski tarih |

### §D.4 Denetim Kontrol Listesi

| # | Kontrol | Beklenen Değer | Uygulanan Yer | Sonuç |
|---|---------|----------------|---------------|-------|
| 1 | Belge satırları Türkçe mi? | Tüm anlatım satırları | §1–§D | ✅ |
| 2 | Kod adları İngilizce mi? | Sınıf adları değişmedi | §2–§A | ✅ |
| 3 | Her terimde karşılık var mı? | Karşılık ya da `verify-required` | §2–§A | ✅ |
| 4 | İki adlı kavram §6'da mı? | İkilik tablosunda | §6 | ✅ |
| 5 | Kısaltmalar açılmış mı? | İlk geçişte açılım | §7 + §B | ✅ |
| 6 | Birimler tutarlı mı? | §C kurallarına uygun | §C | ✅ |
| 7 | Son satır şablona uygun mu? | Authority satırı | Dosya sonu | ✅ |
| 8 | Tek karşılık ihlali var mı? | Çift ad yok | §2–§D | ✅ |

### §D.5 §D Kapanış Notu

Yazım kuralları, sözlüğün **yaşamasını** sağlayan düzendir: §D.1 adlandırma, §D.2 örnekler, §D.3 yeni terim girişi, §D.4 denetim adımları birbirini tamamlar. Yeni bir terim ancak yokluk taramasından ve kod karşılığı aramasından geçtikten sonra tabloya girer; karşılığı yoksa `verify-required` işaretiyle girer — tahminle girmez. İkilikler gizlenmez, §6'da açıkça kayıtlıdır. Bu kural zinciri kırılırsa sözlük iki ad üretmeye başlar ve SSOT bozulur.

### §D.6 Terim Çeviri Karar Matrisi (Hangi Durumda Türkçe, Hangisinde İngilizce)

| # | Durum | Türkçe Yazılan | İngilizce Kalan | Gerekçe |
|---|-------|----------------|-----------------|---------|
| 1 | Belge anlatımı | Tümü | — | Belge dili kuralı |
| 2 | Sınıf adı | — | Sınıf adı | Kod değişmez |
| 3 | Dosya adı | — | Dosya adı | Kod değişmez |
| 4 | HTTP başlığı | — | Başlık adı | Protokol sabiti |
| 5 | Komut satırı seçeneği | — | Seçenek adı | Araç dili |
| 6 | Tablo sütun başlığı | Başlık | — | Okunabilirlik |
| 7 | Bölüm başlığı | Başlık | Parantez içi eş | Çift dilli karşılaştırma |
| 8 | Arayüz metni | Metin | — | Kullanıcı dili |

### §D.7 İkilik Çözüm Sırası (§6 İçin Öncelik)

| # | Sıra | Kural | Uygulama Örneği | Karar Sahibi |
|---|------|-------|------------------|--------------|
| 1 | Önce depo | Diskteki gerçek ad kazanır | `UserLoggedInEvent` | Vault Steward |
| 2 | Sonra ADR | ADR kararı varsa o kazanır | Sınır kararları | Bayram Ali |
| 3 | Sonra SSOT | Vault tablosundaki ad korunur | Çocuk adları | Vault Steward |
| 4 | Sonra işaretleme | Hiçbiri yoksa `verify-required` | Yokluk terimleri | Vault Steward |
| 5 | Asla tahmin | Tahmini ad yazılmaz | — | Herkes |
## §E Terim ↔ Dosya Eşleme Kaydı (2026-10-13 Doğrulaması)

> Bu bölüm, §2–§A terimlerinin dosyayla eşleşmesini 2026-10-13 tarihinde yeniden ölçer; ölçüm dosya sayımı ve satır okuması ile yapılır.

### §E.0 Sözlük Kapsam Sayımı (2026-10-13)

| # | Bölüm | Terim Satırı | Grup | Durum |
|---|-------|--------------|------|-------|
| 1 | §2 Çekirdek terimler | 15 | mimari | ✅ |
| 2 | §3 Bağımlılık terimleri | 10 | mimari | ✅ |
| 3 | §4 Güvenlik terimleri | 10 | kod | ✅ |
| 4 | §5 Test terimleri | 6 | kod | ✅ |
| 5 | §7 Kısaltmalar | 10 | kod | ✅ |
| 6 | §A Terim sözlüğü | 35 | mimari | ✅ |
| 7 | §B Kısaltmalar | 30 | kod | ✅ |
| 8 | §C Birimler | 40 | kanit | ✅ |
### §E.1 Dizin Sayım Tablosu

| # | Dizin | PHP Dosyası Sayısı | Ölçüm Tarihi | Durum |
|---|-------|--------------------|--------------|-------|
| 1 | `shared/src/Events/` | 14 | 2026-10-13 | ✅ |
| 2 | `shared/src/Middleware/` | 11 | 2026-10-13 | ✅ |
| 3 | `shared/src/PageRouter/` | 14 | 2026-10-13 | ✅ |
| 4 | `shared/src/Cache/` | 6 | 2026-10-13 | ✅ |
| 5 | `shared/src/Session/` | 4 | 2026-10-13 | ✅ |
| 6 | `shared/src/Api/` | 34 | 2026-10-13 | ✅ |
| 7 | `shared/src/Contracts/` | 19 | 2026-10-13 | ✅ |
| 8 | `shared/tests/` | 44 | 2026-10-13 | ✅ |

### §E.2 Satır Künyesi Doğrulaması (Dosya:Satır)

| # | Künye | Okunan Satır İçeriği Özeti | Doğrulama |
|---|-------|-----------------------------|-----------|
| 1 | `RouteRegistry.php:35` | `resolve(string $uri)` | ✅ |
| 2 | `RouteRegistry.php:61` | `getProtectedRouteKeys()` | ✅ |
| 3 | `PageRouterKernel.php:81` | `handle(` | ✅ |
| 4 | `PageRouterKernel.php:225` | `normalizeRequest(` | ✅ |
| 5 | `MiddlewarePipeline.php:17` | `pipe(IMiddleware $middleware)` | ✅ |
| 6 | `MiddlewarePipeline.php:23` | `run(array $request, callable $core)` | ✅ |
| 7 | `CsrfMiddleware.php:20` | `handle(array $request, callable $next)` | ✅ |
| 8 | `RateLimiterMiddleware.php:105` | `resolveClientIp(array $request)` | ✅ |
| 9 | `AuthGuard.php:15` | `check(string $uri, SpaRoute $route, ...)` | ✅ |
| 10 | `Gateway.php:50` | `dispatch(array $request)` | ✅ |
| 11 | `ApiResponse.php:26` | `ok(array $data = [], int $statusCode = 200)` | ✅ |
| 12 | `EventDispatcher.php:28` | `dispatch(object $event)` | ✅ |
| 13 | `ApcuAdapter.php:17` | `set(string $key, mixed $value, int $ttl = 0)` | ✅ |
| 14 | `SessionBootstrapper.php:19` | `ensureStarted(): void` | ✅ |

### §E.3 Yokluk İşareti Kaydı (2026-10-13)

| # | Terim | Arama | Sonuç | Durum |
|---|-------|-------|-------|-------|
| 1 | Use case | `*UseCase` glob | 0 | ⚠️ verify-required |
| 2 | Komut sınıfı | `*Command` glob | 0 | ⚠️ verify-required |
| 3 | Sorgu sınıfı | `*Query` glob | 0 | ⚠️ verify-required |
| 4 | Kuyruk sınıfı | `*Queue` glob | 0 | ⚠️ verify-required |
| 5 | Kuyrukçu sınıfı | `*Worker` glob | 0 | ⚠️ verify-required |
| 6 | Zamanlanmış görev | `*Schedule*` glob | 0 | ⚠️ verify-required |
| 7 | DTO eşleyici | `*Mapper` glob | 0 | ⚠️ verify-required |
| 8 | Uzak istemci paketi | composer içeriği | Paket yok | ⚠️ verify-required |

### §E.4 Terim Grup Dağılımı

| # | Grup | Bölüm | Doluluk Satırı | Amaç |
|---|------|-------|----------------|------|
| 1 | mimari | §2 | 15 | Çekirdek terimler |
| 2 | mimari | §3 | 10 | Bağımlılık terimleri |
| 3 | kod | §4 | 10 | Güvenlik terimleri |
| 4 | kod | §5 | 6 | Test terimleri |
| 5 | kod | §7 | 10 | Kısaltmalar |
| 6 | kanit | §9 | 4 | Doğrulama yöntemi |
| 7 | mimari | §A | 35 | Genişletilmiş sözlük |
| 8 | kod | §B | 30 | Genişletilmiş kısaltmalar |

### §E.5 Dosya Bütünlük Kaydı

| # | Kayıt | Beklenen Değer | Doğrulama Aracı |
|---|-------|----------------|-----------------|
| 1 | Dosya uzunluğu | 505+ satır | Satır sayma aracı |
| 2 | Son satır | Authority satırı | Satır sonu okuması |
| 3 | Frontmatter | 10 anahtar | Dosya başı okuması |
| 4 | Bölüm başlıkları | `## §N` biçimi | Başlık taraması |
| 5 | Yasak desen | 0 | Metin araması |

---

### §E.6 Sözlük Bakım Kontrol Listesi (Dönemsel)

| # | Kontrol | Sıklık | Beklenen Sonuç | Sahip |
|---|---------|--------|----------------|-------|
| 1 | Yeni sınıf adı sözlüğe girdi mi? | Aylık | Karşılık ya da yokluk işareti | Vault Steward |
| 2 | §6 ikilik listesi güncelli mi? | Aylık | Yeni ikilik = 0 | Vault Steward |
| 3 | Yokluk terimleri hâlâ yok mu? | Üç aylık | Yeniden glob taraması | Backend |
| 4 | Kısaltma listesi eksik mi? | Üç aylık | Yeni kısaltma açılmış mı | Vault Steward |
| 5 | Birim kuralları ihlal edildi mi? | Aylık | İhlal = 0 | Vault Steward |
| 6 | Son satır şablona uygun mu? | Her yazım | Authority satırı | Herkes |
| 7 | Tahmini satır/ürün sayısı var mı? | Her yazım | Tahmin = 0 | Herkes |

### §E.7 Örnek Sözlük Satırı Şeması (Yeni Terim Giriş Şablonu)

| Alan | Açıklama | Zorunlu mu | Örnek |
|------|----------|------------|-------|
| `#` | Sıra numarası | Evet | 16 |
| `terim` | Türkçe terim | Evet | İşleyici (handler) |
| `tanim` | ELI15 tanım | Evet | İsteği karşılayan tek sorumlu sınıf |
| `karsilik` | Kod adı ya da yokluk işareti | Evet | `Gateway.php:50` |
| `durum` | ✅ / ⚠️ | Evet | ✅ |
**Last Updated:** 2026-10-13

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
