---
node_id: K011-application/application-bagimliliklar
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K008-domain
  - K012-services
  - K009-security
  - K013-infrastructure
evidence:
  - "shared/src/PageRouter/ (16 PHP dosyası, 2026-10-12)"
  - "shared/src/Middleware/ (11 PHP dosyası, 2026-10-12)"
  - "auth.coremusic.net/include/Container/ (DI konteyneri, 2026-10-12)"
validation:
  - "Bağımlılık zinciri elle doğrulandı: 2026-10-12"
open_questions:
  - "Queue ve Worker bağımlılıkları K013-infrastructure içinde mi, K011 içinde mi?"
  - "Ödeme ağ geçidi bağımlılığı (K012-services) sözleşme seviyesinde mi yoksa HTTP seviyesinde mi?"
group: mimari
---

# K011 Application — Bağımlılıklar

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: K011 bağımlılık matrisi**
> **Kaynak SSOT: `coremusic-katmanli-mimari-agaci-v2.md` §2.11 / §4.7 (Bant 1)**

## §1 BAĞIMLILIK İLKELERİ

1. **Tek yön:** K011 yalnızca aşağıya (domain, service, infra) ve yana (security) bağımlıdır; K008 ve K012 K011'e asla bağımlı değildir.
2. **Sözleşme bağımlılığı:** Uzak servis bağımlılıkları (ödeme, e-posta, harita) DTO sözleşmesi üzerinden kurulur; paylaşılan sınıf paylaşımı yoktur.
3. **Çevrimiçi olmayan bağımlılık:** Üçüncü parti paket bağımlılıkları composer ile sabitlenir; lisans denetimi `dependency-manager` sorumluluğundadır.
4. **Kanıt zorunluluğu:** Her bağımlılık satırı ya disk kanıtı (Dosya/Satır) ya da `verify-required` işareti taşır.

## §2 YUKARI BAĞIMLILIKLAR (K011 → DİĞER KUZEYLER)

| # | Kuzey | Tür | K011 içindeki karşılığı | Kanıt (Dosya / Satır) | Durum |
|---|---|---|---|---|---|
| 1 | K008-domain | Sınıf | `Entity`, `ValueObject`, DTO çekirdekleri | `auth.coremusic.net/include/Domain/` (2026-10-12) | ✅ |
| 2 | K012-services | HTTP | Ödeme, e-posta, SMS ağ geçitleri | Yok (2026-10-12) | ⚠️ verify-required |
| 3 | K009-security | Sınıf | Kimlik doğrulama, rol denetimi, CSRF | `shared/src/Middleware/` (2026-10-12) | ✅ |
| 4 | K013-infrastructure | Sınıf | Kuyruk, kuyrukçu, zamanlayıcı, depo adaptörleri | Yok (2026-10-12) | ⚠️ verify-required |
| 5 | K005-cli | Süreç | Kod üretimi, iskeletleme | Yok (2026-10-12) | ⚠️ verify-required |
| 6 | K004-data | Sınıf | Route/istek veri nesneleri | `shared/src/PageRouter/RouteResult.php` (80 satır, 2026-10-12) | Kısmi |
| 7 | K003-application-bus | Sınıf | Komut ve sorgu zarfları | Yok — bkz. §7 | ⚠️ verify-required |
| 8 | K014-observability | Sınıf | Olay yayınlama, ölçüm gönderme | `shared/src/Events/` (2026-10-12) | Kısmi |
| 9 | K015-frontend | Veri | API uçları ve geri dönüş şemaları | `shared/src/Api/` (2026-10-12) | ✅ |
| 10 | K016-ux | Veri | Uç kanıt formatı | Yok (2026-10-12) | ⚠️ verify-required |
| 11 | K011-infra | Dosya | Köprü, denetleyici, montaj | `shared/src/bridge/` — glob = 0 sonuç (2026-10-12) | ⚠️ verify-required |

## §3 AŞAĞI BAĞIMLILIKLAR (DİĞER KUZEYLER → K011)

| # | Kuzey | Ne alır | Beklenen sözleşme | Durum |
|---|---|---|---|---|
| 1 | K008-domain | Uygulama kullanımı tetikleyicileri | Use case'ler domain arayüzlerini çağırır | ✅ (arayüz mevcut) |
| 2 | K012-services | Uygulama orkestrasyonu | Servisler uygulama tarafından çağrılır | ⚠️ verify-required |
| 3 | K015-frontend | HTTP uçları | Route tablosu + DTO şeması | ✅ |
| 4 | K003-application-bus | Komut/sorgu işleyicileri | Handler sözleşmesi | ⚠️ verify-required |
| 5 | K014-observability | Olay abonelikleri | EventDispatcher üzerinden | ✅ |

## §4 PAKET BAĞIMLILIKLARI (composer)

| # | Paket | K011 içindeki kullanımı | Kanıt (Dosya / Satır) | Lisans | Durum |
|---|---|---|---|---|---|
| 1 | `psr/http-message` | Route ve middleware mesaj sözleşmesi | `shared/src/PageRouter/` (2026-10-12) | MIT | ✅ |
| 2 | `psr/http-server-handler` | İşleyici arayüzü | `shared/src/PageRouter/` (2026-10-12) | MIT | ✅ |
| 3 | `psr/http-server-middleware` | Middleware zinciri | `shared/src/Middleware/MiddlewarePipeline.php` (43 satır, 2026-10-12) | MIT | ✅ |
| 4 | `psr/container` | DI konteyneri | `auth.coremusic.net/include/Container/` (2026-10-12) | MIT | ✅ |
| 5 | `psr/log` | Olay günlüğü | `shared/src/Events/` (2026-10-12) | MIT | ✅ |
| 6 | `psr/event-dispatcher` | Olay yayını | `shared/src/Events/EventDispatcher.php` (2026-10-12) | MIT | ✅ |
| 7 | `phpunit/phpunit` | Test çalıştırıcı | `shared/tests/` (2026-10-12) | MIT | ✅ |
| 8 | `phpstan/phpstatik` | Statik analiz | `composer.json` (2026-10-12) | MIT | ✅ yazımda düzeltilecek |
| 9 | `guzzlehttp/guzzle` | Uzak servis istemcisi | Yok (2026-10-12) | MIT | ⚠️ verify-required |
| 10 | `monolog/monolog` | Günlükleyici | Yok (2026-10-12) | MIT | ⚠️ verify-required |

> **Not (paket 8):** `composer.json` içeriği 2026-10-12 tarihinde yeniden okunmalı; paket adı `phpstan/phpstan` olmalıdır. Bu satır SSOT'ta olduğu gibi korunmuş, doğrulama işareti konmuştur.

## §5 ÇEVRİMİÇİ BAĞIMLILIKLAR (HTTP)

| # | Bağımlılık | K011 rolü | Sözleşme | Durum |
|---|---|---|---|---|
| 1 | Ödeme ağ geçidi | `OdemeOnayla` orkestrasyonu | JSON + imza | ⚠️ verify-required |
| 2 | E-posta gönderici | `SifreSifirla` / bildirim | SMTP / API | ⚠️ verify-required |
| 3 | SMS gönderici | İki adımlı doğrulama | REST | ⚠️ verify-required |
| 4 | Harita/coğrafya | Adres tamamlama | REST | ⚠️ verify-required |
| 5 | CDN / varlık sunucusu | Görsel yükleme | HTTPS | ⚠️ verify-required |

## §6 VERİ BAĞIMLILIKLARI

| # | Veri | Yön | Saklama | Kanıt | Durum |
|---|---|---|---|---|---|
| 1 | Oturum anahtarı | Uygulama → K013 | Oturum deposu | `shared/src/Session/` (2026-10-12) | ✅ |
| 2 | Önbellek girişleri | Uygulama → K013 | Önbellek deposu | `shared/src/Cache/` (2026-10-12) | ✅ |
| 3 | Route kayıt verisi | Uygulama → K004 | Route defteri | `shared/src/PageRouter/RouteRegistry.php` (66 satır, 2026-10-12) | ✅ |
| 4 | Kuyruk mesajı | Uygulama → K013 | Kuyruk | Yok (2026-10-12) | ⚠️ verify-required |
| 5 | Zamanlanmış görev kaydı | Uygulama → K013 | Zamanlayıcı deposu | Yok (2026-10-12) | ⚠️ verify-required |

## §7 K003-APPLICATION-BUS BAĞIMLILIĞI (ÖZEL DURUM)

| # | Soru | Kanıt (2026-10-12) | Sonuç |
|---|---|---|---|
| 1 | K003 klasörü var mı? | `.ai/architecture/K003-application-bus/` — var | ✅ |
| 2 | Komut sınıfları var mı? | `shared/src/Command*` — 0 sonuç | ❌ yok |
| 3 | `*Handler` sınıfları var mı? | `auth.coremusic.net/include/Handler/` — var | ✅ |
| 4 | `*UseCase` sınıfları var mı? | 0 sonuç | ❌ yok |

**Sonuç:** K003 ile bağımlılık **sözleşme seviyesinde tasarlı, kod seviyesinde kanıtsız.** `verify-required`.

## §8 BAĞIMLILIK ZİNCİRİ ÖZETİ

```text
K015-frontend
   │  HTTP
   ▼
K011-application ──sözleşme──► K012-services (ödeme, e-posta)
   │                │
   │                └── olay ──► K014-observability
   │  sınıf
   ├──► K008-domain (Entity, VO, DTO)
   ├──► K009-security (auth, rol, CSRF)
   ├──► K004-data (RouteResult, RouteRegistry)
   └──► K013-infrastructure (Cache, Session, Queue)
              ▲
              │ adapte
         K013-infrastructure (dönüş)
```

## §9 RİSK HARİTASI

| # | Bağımlılık riski | Etki | Önlem |
|---|---|---|---|
| 1 | K012-services kanıtsız | Orkestrasyon boşluğu | Contract test + verify-required |
| 2 | Kuyruk/zamanlayıcı yok | Görev akışları eksik | K013 ile ortak sözleşme |
| 3 | Uzak istemci paketi yok | Çevrimiçi bağımlılık elle yazılacak | `guzzlehttp/guzzle` denetimi |
| 4 | Paket adı yazım riski (§4.8) | Kurulum kırılabilir | composer.lock doğrulaması |

## §10 DOĞRULAMA

| # | İddia | Yöntem | Tarih | Sonuç |
|---|---|---|---|---|
| 1 | Domain bağımlılığı mevcut | Dizin listeleme | 2026-10-12 | ✅ |
| 2 | Security bağımlılığı mevcut | Dizin listeleme | 2026-10-12 | ✅ |
| 3 | Services bağımlılığı kanıtsız | Klasör araması | 2026-10-12 | ⚠️ |
| 4 | Kuyruk bağımlılığı kanıtsız | Klasör araması | 2026-10-12 | ⚠️ |
| 5 | PSR paketleri mevcut | `composer.json` + PageRouter | 2026-10-12 | ✅ |
| 6 | K003 komut kodu yok | `*Command` araması | 2026-10-12 | ⚠️ |
| 7 | EventDispatcher mevcut | Dosya listesi | 2026-10-12 | ✅ |
| 8 | Cache/Session mevcut | Dosya listesi | 2026-10-12 | ✅ |

## §11 AÇIK SORULAR

1. Kuyruk ve zamanlayıcı bağımlılıkları K013 mü K011 mi?
2. Uzak servis istemcisi hangi paketle kurulacak?
3. K003 komut zarfları ne zaman koda dönüşecek?
4. Ödeme sözleşmesi K012 ile hangi DTO üzerinden kesilecek?

## §12 REFERANSLAR

- `coremusic-katmanli-mimari-agaci-v2.md` §2.11, §4.7 — Bant 1 bağımlılık kuralları
- `.ai/architecture/index.md` §8.1 — iki eksen gruplama
- `shared/src/PageRouter/`, `shared/src/Middleware/`, `shared/src/Events/`, `shared/src/Cache/` — disk kanıtı (2026-10-12)
---

## §A Girdi (Input Dependencies)

> **Authority: Bayram Ali / Vault Steward** · **Tarih: 2026-10-13** · **Kapsam: K011'e giren bağımlılık girdileri**
> Bu bölüm, K011-application katmanına **giren** girdileri (sözleşme, paket, çalışma zamanı) tanımlar; §2–§3'teki kuzey/yön matrisinin girdi tarafıdır.

### §A.1 Girdi Türleri

| # | Girdi Türü | Kaynak | K011'deki Karşılığı | Kanıt (Dosya / Satır) | Durum |
|---|------------|--------|---------------------|------------------------|-------|
| 1 | Arayüz sözleşmesi (PSR-15) | `psr/http-server-middleware` | `IMiddleware::handle()` | `shared/src/Middleware/MiddlewarePipeline.php:17` (`pipe`) | ✅ |
| 2 | Arayüz sözleşmesi (PSR-15) | `psr/http-server-middleware` | `IMiddleware` zinciri çalıştırma | `shared/src/Middleware/MiddlewarePipeline.php:23` (`run`) | ✅ |
| 3 | Route girdisi | Uygulama içi defter | `RouteRegistry::resolve()` | `shared/src/PageRouter/RouteRegistry.php:35` | ✅ |
| 4 | Korumalı route anahtarı | Route defteri | `RouteRegistry::getProtectedRouteKeys()` | `shared/src/PageRouter/RouteRegistry.php:61` | ✅ |
| 5 | Ham HTTP girdisi | Sunucu | `PageRouterKernel::normalizeRequest()` | `shared/src/PageRouter/PageRouterKernel.php:225` | ✅ |
| 6 | Middleware tanımları | Uygulama yapılandırması | `PageRouterKernel::buildDefaultMiddlewares()` | `shared/src/PageRouter/PageRouterKernel.php:295` | ✅ |
| 7 | Kimlik girdisi | Oturum | `AuthGuard::check()` | `shared/src/PageRouter/AuthGuard.php:15` | ✅ |
| 8 | İzin girdisi | Rol tablosu | `AuthGuard::checkPermission()` | `shared/src/PageRouter/AuthGuard.php:42` | ✅ |
| 9 | CSRF anahtarı | Form gövdesi | `CsrfMiddleware::handle()` | `shared/src/Middleware/CsrfMiddleware.php:20` | ✅ |
| 10 | İstemci IP girdisi | `REMOTE_ADDR` / proxy başlığı | `RateLimiterMiddleware::resolveClientIp()` | `shared/src/Middleware/RateLimiterMiddleware.php:105` | ✅ |
| 11 | Güvenilir proxy girdisi | Yapılandırma | `RateLimiterMiddleware::isTrustedProxy()` | `shared/src/Middleware/RateLimiterMiddleware.php:124` | ✅ |
| 12 | Olay nesnesi | K011 içi üretici | `EventDispatcher::dispatch()` | `shared/src/Events/EventDispatcher.php:28` | ✅ |
| 13 | Olay dinleyicisi | K014-observability | `EventDispatcher::addListener()` | `shared/src/Events/EventDispatcher.php:50` | ✅ |
| 14 | Önbellek adaptörü | K013-infrastructure | `CacheManager::getAdapter()` | `shared/src/Cache/CacheManager.php:9` | ✅ |
| 15 | Oturum başlatma | K013-infrastructure | `SessionBootstrapper::ensureStarted()` | `shared/src/Session/SessionBootstrapper.php:19` | ✅ |

### §A.2 Girdi Kaynakları (Kuzey Düğümleri → K011)

> Kapsam notu: aşağıdaki satırlar K011'e giren kuzey bağımlılıklarını ve beklenen girdi biçimini taşır; çıkış tarafı §B bölümündedir.

| # | Kuzey Kaynak | Girdi Açıklaması | Beklenen Biçim | Kanıt | Durum |
|---|--------------|------------------|----------------|-------|-------|
| 1 | K008-domain | Entity ve değer nesnesi tanımları | Sınıf / arayüz | `shared/src/` ağacı (2026-10-13) | ✅ |
| 2 | K009-security | Rol ve izin anahtarları | Dizgi anahtarı listesi | `shared/src/PageRouter/AuthGuard.php:42` | ✅ |
| 3 | K013-infrastructure | Önbellek ve oturum adaptörleri | PSR-16 / oturum arayüzü | `shared/src/Cache/CacheManager.php:9` | ✅ |
| 4 | K014-observability | Olay abonelikleri | `addListener` kaydı | `shared/src/Events/EventDispatcher.php:50` | ✅ |
| 5 | K015-frontend | İstek gövdesi ve başlıklar | Dizgi dizisi | `shared/src/PageRouter/PageRouterKernel.php:225` | ✅ |
| 6 | K012-services | Uzak servis DTO girdileri | JSON nesnesi | Yok — §5 satır 1 (verify-required) | ⚠️ |
| 7 | K004-data | Route kayıt satırları | Route defteri satırı | `shared/src/PageRouter/RouteRegistry.php:35` | ✅ |
| 8 | K003-application-bus | Komut/sorgu zarfı | Zarf nesnesi | Yok — §7 satır 4 (verify-required) | ⚠️ |

### §A.3 Girdi Doğrulama Kuralları

| # | Kural | Uygulanan Yer | Doğrulama Yöntemi | Sonuç |
|---|-------|---------------|-------------------|-------|
| 1 | Ham girdi sınırda doğrulanır | `PageRouterKernel::normalizeRequest()` | İstek normalize edilmeden işlenmez | ✅ |
| 2 | Kimlik girdisi oturumdan gelir | `AuthGuard::check()` | Oturum yoksa yönlendirme döner | ✅ |
| 3 | CSRF anahtarı gövdede aranır | `CsrfMiddleware::handle()` | Eşleşmeyen anahtar reddedilir | ✅ |
| 4 | IP girdisi proxy kontrolünden geçer | `RateLimiterMiddleware::resolveClientIp()` | Güvenilir proxy yoksa `REMOTE_ADDR` kullanılır | ✅ |
| 5 | Paket girdisi composer ile sabitlenir | `shared/composer.json:8` | `php >=8.4` koşulu | ✅ |
| 6 | Test girdisi composer ile sabitlenir | `shared/composer.json:24` | `phpunit ^10.5` koşulu | ✅ |
| 7 | Analiz girdisi composer betiğiyle gelir | `shared/composer.json:48` | `phpstan analyse src --level=5` | ✅ |
| 8 | CI girdisi sabitlenir | `.github/workflows/ci.yml:22` | `PHP_VERSION: '8.4'` | ✅ |

### §A.4 Girdi Sınırları (K011 Girdisi Olmayanlar)

| # | Girdi | Neden Girdi Değil | Kanıt | Durum |
|---|-------|-------------------|-------|-------|
| 1 | Veritabanı bağlantısı | Doğrudan bağlantı K004 üzerinden kurulur | Paylaşılan kaynakta bağlantı sınıfı yok (2026-10-13) | Dış |
| 2 | Kuyruk kuyruğu işleyicisi | K013 sorumluluğu | Kuyruk/bekleyici sınıfı yok (2026-10-13) | Dış |
| 3 | Ödeme ağ geçidi imzası | K012 sözleşmesi | Ödeme sınıfı yok (2026-10-13) | Dış |
| 4 | Dosya sistemi yolu | Çevre değişkeni üzerinden gelir | Yapılandırma dosyası K011 içinde değil | Dış |
| 5 | Harici API anahtarı | `.env` üzerinden gelir | Anahtar metni dosyaya yazılmaz | Dış |
## §B Çıktı (Output Dependencies)

> **Tarih: 2026-10-13** · Bu bölüm, K011'den **çıkan** bağımlılık çıktılarını (sözleşmeler, yanıt nesneleri, olaylar) tanımlar; §A girdi tarafının simetriğidir.

### §B.1 Çıktı Türleri

| # | Çıktı Türü | Hedef Kuzey | Çıktı Açıklaması | Kanıt (Dosya / Satır) | Durum |
|---|------------|-------------|------------------|------------------------|-------|
| 1 | HTTP yanıt dizisi | K015-frontend | Sayfa ve API yanıtları | `shared/src/PageRouter/ResponseEmitter.php:16` (`emit`) | ✅ |
| 2 | API başarı yanıtı | K015-frontend | `200` gövdesi | `shared/src/Api/ApiResponse.php:26` (`ok`) | ✅ |
| 3 | API sayfalı yanıt | K015-frontend | Sayfalama gövdesi | `shared/src/Api/ApiResponse.php:73` (`paginated`) | ✅ |
| 4 | Sayfalama nesnesi | K015-frontend | Toplam/limit anahtarı | `shared/src/Api/Dto/Response/PaginationResponse.php:70` | ✅ |
| 5 | Hata sayfası | K015-frontend | HTML hata gövdesi | `shared/src/PageRouter/ErrorHandler.php:11` (`buildErrorPage`) | ✅ |
| 6 | Hata sonucu | K015-frontend | Durum kodlu sonuç | `shared/src/PageRouter/ErrorHandler.php:51` (`buildErrorHtmlResult`) | ✅ |
| 7 | Yönlendirme sonucu | K015-frontend | Kimlik yoksa yönlendirme | `shared/src/PageRouter/AuthGuard.php:15` | ✅ |
| 8 | Middleware çıktısı | K011 içi | Zincir sonucu | `shared/src/Middleware/MiddlewarePipeline.php:23` | ✅ |
| 9 | Olay yayını | K014-observability | Olay nesnesi | `shared/src/Events/EventDispatcher.php:28` | ✅ |
| 10 | Korumalı route listesi | K009-security | Anahtar listesi | `shared/src/PageRouter/RouteRegistry.php:61` | ✅ |
| 11 | API dağıtımı | K015-frontend | Yönlendirilmiş yanıt | `shared/src/Api/Gateway.php:50` (`dispatch`) | ✅ |
| 12 | İşleyici çağrısı | K011 içi | Route + istek | `shared/src/Api/Gateway.php:152` (`invokeHandler`) | ✅ |

### §B.2 Çıktı Tüketicileri

| # | Tüketici | Alınan Çıktı | Beklenen Şekil | Sözleşme Kanıtı | Durum |
|---|----------|--------------|----------------|-----------------|-------|
| 1 | K015-frontend | HTTP yanıt | Dizgi dizisi + durum kodu | `ResponseEmitter.php:16` | ✅ |
| 2 | K015-frontend | API gövdesi | Assoc dizi | `ApiResponse.php:26` | ✅ |
| 3 | K015-frontend | Sayfalama | Nesne | `PaginationResponse.php:70` | ✅ |
| 4 | K014-observability | Olay nesnesi | Nesne | `EventDispatcher.php:28` | ✅ |
| 5 | K009-security | Korumalı anahtarlar | Dizgi listesi | `RouteRegistry.php:61` | ✅ |
| 6 | K008-domain | İşleyici dönüşü | Assoc dizi | `Gateway.php:152` | Kısmi |
| 7 | K012-services | Uzak servis gövdesi | JSON nesnesi | Yok — verify-required | ⚠️ |

### §B.3 Çıktı Sözleşmeleri (Biçim Kuralları)

| # | Çıktı | Biçim Kuralı | Doğrulama | Kanıt |
|---|-------|--------------|-----------|-------|
| 1 | API yanıt gövdesi | Anahtar adları dize | `200` gövdesi kontrolü | `ApiResponse.php:26` |
| 2 | Sayfalama gövdesi | `total`/`limit`/`offset` anahtarları | Diziden nesne dönüşümü | `PaginationResponse.php:70` |
| 3 | Hata gövdesi | Durum kodu ile eşleşir | `5xx`/`4xx` eşleşmesi | `ErrorHandler.php:51` |
| 4 | HTTP başlıkları | Yalnız izin verilen başlıklar | Ek başlık parametresi | `ResponseEmitter.php:16` |
| 5 | Olay nesnesi | Nesne tipi anahtardır | Dinleyici kaydı | `EventDispatcher.php:50` |
| 6 | API dağıtımı | Route eşleşmesi zorunlu | Eşleşme yoksa boş sonuç | `Gateway.php:50` |

### §B.4 Çıktı Sınırları (K011 Çıkışı Olmayanlar)

| # | Çıktı | Neden Çıkış Değil | Kanıt | Durum |
|---|-------|-------------------|-------|-------|
| 1 | Ham SQL sorgusu | Sorgular K004 katmanında kalır | Paylaşılan kaynakta sorgu metni yok (2026-10-13) | Dış |
| 2 | Dosya yazma | Depolama K013 sorumluluğu | K011 çıktısında dosya yok | Dış |
| 3 | Kuyruk mesajı | Kuyruk K013 sorumluluğu | Kuyruk sınıfı yok (2026-10-13) | Dış |
| 4 | Ödeme isteği | Uzak sözleşme K012 | Ödeme sınıfı yok (2026-10-13) | Dış |
| 5 | SMTP komutu | E-posta K012 sorumluluğu | Gönderici sınıfı yok (2026-10-13) | Dış |

### §B.5 Çıktı Doğrulama Adımları (2026-10-13)

| # | Adım | Doğrulanan Çıktı | Kanıt | Sonuç |
|---|------|------------------|-------|-------|
| 1 | Yanıt yayımı imzası | `emit` dönüş tipi `never` | `ResponseEmitter.php:16` | ✅ |
| 2 | API başarı gövdesi | `ok` varsayılan durum 200 | `ApiResponse.php:26` | ✅ |
| 3 | Sayfalama gövdesi | `paginated` imzası mevcut | `ApiResponse.php:73` | ✅ |
| 4 | Hata sayfası üretimi | `buildErrorPage` imzası mevcut | `ErrorHandler.php:11` | ✅ |
| 5 | API dağıtımı | `dispatch` imzası mevcut | `Gateway.php:50` | ✅ |
## §C Çalışma Zamanı Bağımlılıkları (Runtime)

> **Tarih: 2026-10-13** · Bu bölüm, K011'in **çalışma anında** ihtiyaç duyduğu bağımlılıkları (uzantılar, adaptörler, süreçler) tanımlar.

### §C.1 Çalışma Zamanı Öğeleri

| # | Öğe | Tür | K011 Kullanımı | Kanıt (Dosya / Satır) | Durum |
|---|-----|-----|----------------|------------------------|-------|
| 1 | APCu uzantısı | PHP uzantısı | Önbellek adaptörü | `shared/src/Cache/ApcuAdapter.php:17` (`set`) | ✅ |
| 2 | Önbellek adaptörü | PSR-16 | Önbellek oku/yaz | `shared/src/Cache/CacheManager.php:9` | ✅ |
| 3 | Oturum başlatma | PHP oturumu | Oturum garantisi | `shared/src/Session/SessionBootstrapper.php:19` | ✅ |
| 4 | Middleware zinciri | Çalışma akışı | İstek işleme | `shared/src/PageRouter/PageRouterKernel.php:269` | ✅ |
| 5 | Varsayılan middleware kümesi | Çalışma akışı | Zamanlama | `shared/src/PageRouter/PageRouterKernel.php:295` | ✅ |
| 6 | Olay dağıtıcı | Çalışma akışı | Olay yayını | `shared/src/Events/EventDispatcher.php:28` | ✅ |
| 7 | Hata yakalayıcı | Çalışma akışı | Hata sayfası | `shared/src/PageRouter/ErrorHandler.php:11` | ✅ |
| 8 | Zaman aşımı değeri | Yapılandırma | İstek üst sınırı | Doğrulanmadı — verify-required | ⚠️ |

### §C.2 Çalışma Zamanı Bağımlılık Zinciri

| # | Adım | Önce Gelen | Sonra Gelen | Kanıt | Durum |
|---|------|------------|-------------|-------|-------|
| 1 | Oturum başlatma | İstek | Kimlik kontrolü | `SessionBootstrapper.php:19` | ✅ |
| 2 | İstek normalizasyonu | Sunucu | Route çözümleme | `PageRouterKernel.php:225` | ✅ |
| 3 | Route çözümleme | Normalized istek | Kimlik kontrolü | `RouteRegistry.php:35` | ✅ |
| 4 | Kimlik kontrolü | Route | İzin kontrolü | `AuthGuard.php:15` | ✅ |
| 5 | İzin kontrolü | Kimlik | İşleyici | `AuthGuard.php:42` | ✅ |
| 6 | CSRF denetimi | Form | İşleyici | `CsrfMiddleware.php:20` | ✅ |
| 7 | Hız sınırı denetimi | İstemci IP | İşleyici | `RateLimiterMiddleware.php:50` | ✅ |
| 8 | İşleyici çağrısı | Route | Yanıt | `Gateway.php:152` | ✅ |
| 9 | Yanıt yayımı | Yanıt | İstemci | `ResponseEmitter.php:16` | ✅ |
| 10 | Olay yayımı | İşleyici | Dinleyiciler | `EventDispatcher.php:28` | ✅ |

### §C.3 Çalışma Zamanı Ortam Gereksinimleri

| # | Gereksinim | Minimum | Doğrulanan Değer | Kanıt | Durum |
|---|------------|---------|------------------|-------|-------|
| 1 | PHP sürümü | 8.4 | `>=8.4` | `shared/composer.json:8` | ✅ |
| 2 | Test çalıştırıcı | PHPUnit 10.5 | `^10.5` | `shared/composer.json:24` | ✅ |
| 3 | Statik analiz | PHPStan 5 | `--level=5` | `shared/composer.json:48` | ✅ |
| 4 | CI PHP sürümü | 8.4 | `'8.4'` | `.github/workflows/ci.yml:22` | ✅ |
| 5 | CI hattı | GitHub Actions | `CI Pipeline` | `.github/workflows/ci.yml:8` | ✅ |
| 6 | APCu uzantısı | Yüklü | Doğrulanmadı | Doğrulama ortamda yapılır | ⚠️ |

### §C.4 Çalışma Zamanı Riskleri

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|------|----------|------|---------|-------|
| 1 | APCu yoksa önbellek düşer | Orta | Orta | Adaptör soyutlaması | Kontrollü |
| 2 | Oturum başlatılmazsa kimlik kırılır | Düşük | Yüksek | `ensureStarted` çağrısı | Kontrollü |
| 3 | Middleware sırası değişirse CSRF atlanabilir | Düşük | Yüksek | Varsayılan küme sabitlenir | Kontrollü |
| 4 | Güvenilir proxy yanlış tanımlanır | Orta | Yüksek | `isTrustedProxy` denetimi | Kontrollü |
| 5 | Olay dinleyicisi hata verirse akış kesilmez | Düşük | Düşük | Dinleyici hatası yalıtılır | Kontrollü |
## §D Sürüm Uyumluluk Matrisi (Version Compatibility)

> **Tarih: 2026-10-13** · Bu bölüm, K011 bağımlılıklarının **sürüm uyumluluğunu** sabitlenmiş composer/CI değerleriyle tanımlar; hiçbir sürüm tahmin edilmez.

### §D.1 Paket Sürüm Matrisi

| # | Paket | Bildirilen Sürüm | Kaynak Dosya | Satır | Durum |
|---|-------|------------------|--------------|-------|-------|
| 1 | `php` (çalışma zamanı) | `>=8.4` | `shared/composer.json` | 8 | ✅ |
| 2 | `phpunit/phpunit` (test) | `^10.5` | `shared/composer.json` | 24 | ✅ |
| 3 | `phpstan` (betik düzeyi) | `--level=5` | `shared/composer.json` | 48 | ✅ |
| 4 | `CI PHP_VERSION` | `'8.4'` | `.github/workflows/ci.yml` | 22 | ✅ |
| 5 | `psr/http-server-middleware` | Doğrulanmadı | Kilit dosyası okunmadı | — | ⚠️ |
| 6 | `psr/http-message` | Doğrulanmadı | Kilit dosyası okunmadı | — | ⚠️ |
| 7 | `psr/container` | Doğrulanmadı | Kilit dosyası okunmadı | — | ⚠️ |
| 8 | `psr/event-dispatcher` | Doğrulanmadı | Kilit dosyası okunmadı | — | ⚠️ |

### §D.2 Sürüm Yükseltme Kuralları

| # | Kurallar | Tetikleyici | Doğrulama | Karar |
|---|----------|-------------|-----------|-------|
| 1 | PHP sürümü yalnız `composer.json` üzerinden ilerletilir | Yeni PHP çıkışı | `shared/composer.json:8` yeniden okunur | Karar ADR ile |
| 2 | PHPUnit ana sürümü atlanmaz | Test hatası | Test paketi betiği | Karar QA ile |
| 3 | PHPStan düzeyi kademeli artırılır | Yeni bulgu | `composer.json:48` | Karar Backend ile |
| 4 | CI PHP sürümü composer ile eşleşir | Sürüm değişikliği | `ci.yml:22` ↔ `composer.json:8` | Eşleşme zorunlu |
| 5 | PSR paketleri ana sürüm sabitlenmez | Kilit güncellemesi | Kilit dosyası | Karar ADR ile |
| 6 | Uzantı bağımlılıkları sürümle değil davranışla denetlenir | Ortam değişimi | Ortamda deneme | Karar DevOps ile |

### §D.3 Sürüm Uyumsuzluğu Senaryoları

| # | Senaryo | Belirti | Etki | Çözüm | Durum |
|---|---------|---------|------|-------|-------|
| 1 | PHP 8.4 altı ortam | Composer kurulumu reddedilir | Kurulum durur | Ortam yükseltmesi | Kapalı |
| 2 | PHPUnit 9 ile çalıştırma | Test API'si bulunamaz | Testler koşmaz | Sürüm sabitleme | Kapalı |
| 3 | PHPStan düzeyi 5 altı | Bulgu sayısı değişir | Analiz raporu kayar | Deyim düzeyi sabitlenir | Kapalı |
| 4 | CI sürümü composer'dan sapan | Farklı dil davranışı | Geçici hata | Eşleşme denetimi | Kapalı |
| 5 | PSR arayüzü eksik | Sınıf bulunamaz | Çökme | Paket kurulumu | Açık — §D.1 satır 5–8 |
| 6 | APCu uzantısı yok | Adaptör çağrısı başarısız | Önbellek düşer | Uyarlama katmanı | Açık — §C.1 satır 1 |

### §D.4 Sürüm Kanıt Kaydı

| # | İddia | Kaynak | Tarih | Sonuç |
|---|-------|--------|-------|-------|
| 1 | PHP `>=8.4` | `shared/composer.json:8` | 2026-10-13 | ✅ satır içeriği okundu |
| 2 | PHPUnit `^10.5` | `shared/composer.json:24` | 2026-10-13 | ✅ satır içeriği okundu |
| 3 | PHPStan düzey 5 | `shared/composer.json:48` | 2026-10-13 | ✅ satır içeriği okundu |
| 4 | CI PHP 8.4 | `.github/workflows/ci.yml:22` | 2026-10-13 | ✅ satır içeriği okundu |
| 5 | CI hattı adı | `.github/workflows/ci.yml:8` | 2026-10-13 | ✅ satır içeriği okundu |
| 6 | PSR paket sürümleri | Kilit dosyası | 2026-10-13 | ⚠️ doğrulanmadı |

## §E Devre Dışı Bırakma ve Alternatifler (Disable & Alternatives)

> **Tarih: 2026-10-13** · Bu bölüm, K011 bağımlılıklarının **nasıl devre dışı bırakılabileceğini** ve her birinin **alternatifini** tanımlar; geçici çözüm değil, kontrollü kapatma senaryosudur.

### §E.1 Devre Dışı Bırakma Senaryoları

| # | Bağımlılık | Kapatma Yöntemi | Kapanınca Ne Olur | Kanıt | Durum |
|---|------------|-----------------|-------------------|-------|-------|
| 1 | Önbellek adaptörü | Adaptör kaydı değişir | Sorgular yeniden çalışır | `CacheManager.php:9` | Kontrollü |
| 2 | APCu önbelleği | Uzantı devre dışı | Bellek önbelleği düşer | `ApcuAdapter.php:17` | Kontrollü |
| 3 | Hız sınırı middleware | Kümeden çıkarılır | İstek üst sınırı kalkar | `PageRouterKernel.php:295` | Kontrollü |
| 4 | Olay yayımı | Dinleyici kaydı kaldırılır | Ölçüm olayları kaybolur | `EventDispatcher.php:50` | Kontrollü |
| 5 | Oturum garantisi | Çağrı kaldırılır | Kimlik kırılabilir | `SessionBootstrapper.php:19` | Dikkatli |
| 6 | CSRF denetimi | Middleware çıkarılır | Güvenlik açığı doğar | `CsrfMiddleware.php:20` | Yasak |

### §E.2 Alternatif Bağımlılıklar

| # | Mevcut Bağımlılık | Alternatif | Değişim Maliyeti | Karar Kriteri | Durum |
|---|-------------------|------------|------------------|---------------|-------|
| 1 | APCu önbellek | Dosya tabanlı önbellek | Orta | Uzantı yokluğu | Seçenek |
| 2 | APCu önbellek | Uzak önbellek sunucusu | Yüksek | Çok süreçli dağıtım | Seçenek |
| 3 | Statik analiz (PHPStan) | Alternatif analiz aracı | Orta | Bulgu kapsamı | Değerlendirilmedi |
| 4 | PHPUnit | Alternatif test çalıştırıcı | Yüksek | Ekosistem uyumu | Reddedildi |
| 5 | Uzak servis istemcisi | Elle yazılmış istemci | Yüksek | Bakım yükü | Değerlendirilmedi |
| 6 | Olay dağıtıcı | Doğrudan çağrı | Düşük | Bağımsız ölçekleme | Reddedildi |

### §E.3 Kapatma Risk Tablosu

| # | Kapatılan Bağımlılık | Güvenlik Etkisi | Performans Etkisi | Veri Etkisi | Karar |
|---|----------------------|-----------------|------------------|-------------|-------|
| 1 | CSRF denetimi | Kritik açık | Yok | Yok | Kapatılamaz |
| 2 | Hız sınırı | Saldırı yüzeyi artar | Yük artışı | Yok | Yalnız bakım anında |
| 3 | Oturum garantisi | Kimlik kırılması | Yok | Oturum kaybı | Yalnız test ortamında |
| 4 | Önbellek | Yok | Yanıt yavaşlar | Yok | Geçici kabul edilir |
| 5 | Olay yayımı | Yok | Ölçüm kaybı | Kayıt kaybı | Geçici kabul edilir |
| 6 | Hata yakalayıcı | Bilgi sızıntısı riski | Yok | Yok | Kapatılamaz |

### §E.4 Geri Alma (Rollback) Adımları

| # | Adım | Geri Alma Eylemi | Doğrulama | Sahip | Süre |
|---|------|------------------|-----------|-------|------|
| 1 | Middleware çıkarma | Kümeye geri eklenir | Varsayıdan küme denetimi | Backend | 5 dk |
| 2 | Adaptör değişimi | Önceki adaptör kaydı | Önbellek oku denemesi | Backend | 10 dk |
| 3 | Olay dinleyicisi kaldırma | Kayıt geri konur | Dinleyici sayısı | Backend | 5 dk |
| 4 | Sürüm düşürme | Composer kilit gerisi | Test paketinin koşması | QA | 30 dk |
| 5 | Ortam değişimi | CI değeri gerisi | `ci.yml:22` denetimi | DevOps | 15 dk |
| 6 | Kapatma kararı iptali | Karar kaydı güncellenir | ADR satırı | Vault Steward | 10 dk |

### §E.5 §E Kapanış Notu

Devre dışı bırakma, bağımlılığı **silmek** değil; kontrollü biçimde kapatıp etkisini ölçmektir. §E.1'deki altı senaryodan beşi kontrollü, biri (CSRF) kapatılamaz; §E.3 güvenlik, performans ve veri etkisini ayrı sütunlarda taşır. Alternatifler (§E.2) yalnız kriterle listelenir; reddedilenler de gerekçesiyle kayıtlıdır. Geri alma adımları (§E.4) sahip ve süre taşır — belirsiz rollback planı plan sayılmaz.
### §E.6 Devre Dışı Bırakma Kontrol Listesi

| # | Kontrol | Zorunlu mu | Doğrulama Yöntemi | Geçme Eşiği |
|---|---------|------------|-------------------|-------------|
| E-1 | Kapatma gerekçesi yazılı mı? | Evet | Karar satırı | Gerekçe mevcut |
| E-2 | Güvenlik etkisi değerlendirildi mi? | Evet | §E.3 sütunu | Kapatılamaz işareti yok |
| E-3 | Performans etkisi ölçüldü mü | Hayır (geçici kapatma) | Yanıt süresi kaydı | Eşik aşım yok |
| E-4 | Geri alma adımı tanımlı mı? | Evet | §E.4 satırı | Sahip + süre dolu |
| E-5 | Test ortamında denendi mi? | Evet | Test paketi koşusu | Kırık yok |
| E-6 | CI değeri etkileniyor mu? | Evet | `ci.yml:22` | Eşleşme korunur |
| E-7 | Vault'a kaydedildi mi? | Evet | Karar dosyası | Kayıt mevcut |

### §E.7 Kapatma Sonrası İzleme (Post-Disable Monitoring)

| # | İzlenen Sinyal | Eşik | Aşım Durumunda Aksiyon | Frekans |
|---|----------------|------|------------------------|---------|
| E-8 | Yanıt süresi artışı | %20 | Önbellek geri alınır | Saatlik |
| E-9 | Hata oranı | %1 | Kapatma geri alınır | Anlık |
| E-10 | 429 yanıt sayısı | Sıfır | Hız sınırı geri konur | Saatlik |
| E-11 | Oturum hata sayısı | Sıfır | Oturum garantisi geri konur | Anlık |
| E-12 | Olay kaybı | Sıfır | Dinleyici geri konur | Günlük |
| E-13 | Bellek kullanımı artışı | %30 | Adaptör geri alınır | Saatlik |

### §E.8 Bağımlılık Kapatma Kayıt Şeması

| Alan | Açıklama | Zorunlu mu | Örnek Değer |
|------|----------|------------|-------------|
| `kapatilan` | Bağımlılık adı | Evet | Önbellek adaptörü |
| `tarih` | ISO 8601 tarih | Evet | 2026-10-13 |
| `gerekce` | Neden kapatıldığı | Evet | Uzantı yokluğu |
| `etki` | §E.3 etki özeti | Evet | Performans etkisi |
| `geri_alma` | §E.4 adım kodu | Evet | E-2 |
| `sahip` | Karar sahibi | Evet | Backend |
| `durum` | Açık / kapalı | Evet | Açık |

### §A.5 Girdi Red Senaryoları (Hangi Girdi Kabul Edilmez)

| # | Red Edilen Girdi | Neden | Yakalanan Yer | Kanıt |
|---|------------------|-------|---------------|-------|
| 1 | Normalize edilmemiş istek | Biçim belirsiz | `PageRouterKernel::normalizeRequest()` | `PageRouterKernel.php:225` |
| 2 | Oturumsuz kimlik isteği | Kimlik kanıtı yok | `AuthGuard::check()` | `AuthGuard.php:15` |
| 3 | Eşleşmeyen CSRF anahtarı | Sahte istek riski | `CsrfMiddleware::handle()` | `CsrfMiddleware.php:20` |
| 4 | Güvenilmeyen proxy IP'si | IP sahteciliği | `RateLimiterMiddleware::isTrustedProxy()` | `RateLimiterMiddleware.php:124` |
| 5 | Eşleşmeyen route | Tanımsız uç | `RouteRegistry::resolve()` | `RouteRegistry.php:35` |
| 6 | Tanımsız işleyici | Boş dağıtım | `Gateway::invokeHandler()` | `Gateway.php:152` |

### §A.6 Girdi Kanıt Kaydı (2026-10-13 Ölçümü)

| # | Girdi İddiası | Doğrulama Yöntemi | Sonuç |
|---|---------------|-------------------|-------|
| 1 | PSR-15 arayüzleri kullanılıyor | `MiddlewarePipeline.php` satır 17/23 okundu | ✅ |
| 2 | Route çözümleme mevcut | `RouteRegistry.php` satır 35 okundu | ✅ |
| 3 | Kimlik girdisi oturumdan gelir | `AuthGuard.php` satır 15 okundu | ✅ |
| 4 | CSRF girdisi gövdeden gelir | `CsrfMiddleware.php` satır 20 okundu | ✅ |
| 5 | IP girdisi proxy denetiminden geçer | `RateLimiterMiddleware.php` satır 105/124 okundu | ✅ |
| 6 | Composer girdisi 8.4 alt sınırı | `composer.json` satır 8 okundu | ✅ |
| 7 | Kuyruk girdisi mevcut | Sınıf araması: 0 sonuç | ⚠️ verify-required |
| 8 | Uzak servis girdisi mevcut | Sınıf araması: 0 sonuç | ⚠️ verify-required |
---

**Last Updated:** 2026-10-13

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode