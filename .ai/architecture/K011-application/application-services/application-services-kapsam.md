---
title: "K011 · application-services — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-services
status: active
updated: 2026-10-15
group: mimari
---

# K011 · application-services — Kapsam

> **Amaç:** Bu dosya, K011-application içindeki `application-services` alt-kapsamının sınırını tanımlar: hangi uygulama-düzeyi servisler bu düğüme aittir, hangileri komşu K düğümlerine devredilir ve her servis hangi PSR (PHP FIG) standardıyla ilişkilidir.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Amaç ve ELI15 | mimari | kapsam | Kapsam okuması ilk satır |
| 2 | §2 Sınır (ne DEĞİL) | mimari | sinir | Devir kararlarında |
| 3 | §3 Kapsam içi servis envanteri | mimari | envanter | Servis listesi tek kaynak |
| 4 | §4 Kapsam dışı devirler | mimari | sinir | Komşu K düğüm eşlemesi |
| 5 | §5 Katman akış şeması | mimari | akis | Çağrı sırası görselleştirme |
| 6 | §6 PSR standart eşlemesi | kanit | standart | 5 standart ↔ repo kanıtı |
| 7 | §7 Bağımlılıklar | mimari | bagimlilik | Üst/komşu K etkisi |
| 8 | §8 Açık sorular | mimari | qa | Onay bekleyen belirsizlikler |
| 9 | §9 Ölçüm referansı | kanit | olcum | Disk ölçüm tarihçesi |
| 10 | §A Genişletilmiş envanter | mimari | envanter | Alt klasör kırılımı |
| 11 | §B Bileşen ↔ akış eşleme | mimari | esleme | İzin düşümü |
| 12 | §C Arayüz (Contracts) envanteri | kod | arayuz | Interface listesi |
| 13 | §D Bağımlılık grafiği | mimari | graf | Yön okları |
| 14 | §E Kapsam doğrulama kaydı | kanit | dogrulama | Kapı sonuçları |
| 15 | §F PLANNED boşluklar | kanit | bosluk | Kanıtsız alanlar |
| 16 | §G Kapsam SSS | mimari | sinir | Sık sorulanlar |
| 17 | §H Sürüm kaydı | kanit | surum | Revizyon takibi |

## §1 — Amaç ve ELI15

**ELI15:** Bir kullanıcı bir tuşa bastığında arkada birçok küçük görev sırayla çalışır (oturum kontrolü, yetki, veri okuma, yanıt şekillendirme, log yazma). `application-services`, işte bu "arka görevlerin koordinasyonunu" anlatan katmandır: kuralı kimin uyguladığını, sıranın kimde olduğunu ve bir görevin nereye devredildiğini burada yazarız.

**ELI10:** Ekranın arkasındaki işleri tarif eden dosya takımı. Hangi servis var, ne yapar, kime bağlı, hangi kurala uyuyor.

**Bu dosyanın rolü:** K011 kademe-2 kapsamında `application-services` klasörünün **kapsam sözleşmesidir**. İçerik dosyalarının beşi de (kapsam · doğrulama · web-arastirma · ornekler · bilesenler) bu sınırdan sapamaz.

**Kapsamın iki ekseni:**

| Eksen | Soru | Cevabı bu dosyada |
|---|---|---|
| Eksen-1 (Grup) | Dosya hangi mimari grupta? | `mimari` — tasarım iddiası taşır |
| Eksen-2 (Konu) | Hangi konu diliminde? | servis · standart · sınır · envanter |

**Okuma sırası:** §1 → §2 → §3 → §6 → §8 → §F (boşluklar). Token bütçesi dar ise §3 ve §8 zorunludur.

## §2 — Sınır (ne DEĞİL)

| # | Bu düğüm DEĞİL | Sorumlu | Gerekçe |
|---|---|---|---|
| 1 | Arka plan worker / kuyruk işi | K012-services | Görev tetikleyicisi burada, çalıştırıcı orada |
| 2 | Dış sağlayıcı kontratı (OAuth, ödeme, harici API) | K013-integraion | Kontrat sahipliği entegrasyon katmanında |
| 3 | İş kuralı kararı (ör. çalma listesi kuralı, yaş sınırı) | K008-domain | Kural orada yaşar, burada yalnızca uygulanır |
| 4 | Kimlik/parola kriptografisi, token üretimi | K009-security | Algoritma orada; servis yalnızca çağırır |
| 5 | Şema/BCNF/migration | K00x-data (`.ai/.sql/mysql`) | DDL tek kaynağı dump SSOT'udur |
| 6 | HTTP istek/yanıt iskeleti, rota çözümü | K011 routing (PageRouter) | Çatı ayrı dosyada (`routing/`) |
| 7 | Önbellek teknoloji seçimi (APCu/Redis/CDN) | K012-services + caching alt klasörü | `caching/` alt klasörü ayrı düğüm |
| 8 | Test altyapısı, suite tanımı, coverage kapısı | K019-testing + `application-testing/` | Ayrı kardeş klasör |
| 9 | UI bileşeni davranışı, ITCSS katmanı | UI alanı (`ui-design`, `Css/`) | Sunum katmanı |
| 10 | WebSocket/gerçek zamanlı taşıma | K014-network | Taşıma protokolü |

**Sınır cümlesi (tek satır):** `application-services` = *uygulama içi orkestrasyon + standart uyumu*; kural değil, politika değil, taşıma değil, test değil.

## §3 — Kapsam içi servis envanteri

| # | Servis | Dosya (repo) | Satır | İşlev | Durum |
|---|---|---|---|---|---|
| 1 | Event Dispatcher (PSR-14) | `shared/src/Events/EventDispatcher.php` | 119 | Olay yayımı, öncelik sıralı dinleyici | IMPLEMENTED |
| 2 | Cache Manager (adaptör seçimi) | `shared/src/Cache/CacheManager.php` | 25 | APCu/Memory adaptörü seçimi | IMPLEMENTED |
| 3 | Cache Interface (özel) | `shared/src/Cache/CacheInterface.php` | 13 | `get/set/delete/increment/clear/has` | IMPLEMENTED |
| 4 | APCu adaptörü | `shared/src/Cache/ApcuAdapter.php` | 56 | `apcu_*` sarmalayıcı | IMPLEMENTED |
| 5 | Memory adaptörü | `shared/src/Cache/MemoryAdapter.php` | 57 | Dizisel yedek adaptör | IMPLEMENTED |
| 6 | Page Cache sözleşmesi | `shared/src/Cache/PageCacheInterface.php` | 11 | Sayfa anahtarı + TTL + `remember` | IMPLEMENTED |
| 7 | Logger Factory (PSR-3) | `shared/src/Log/LoggerFactory.php` | 34 | Tekil `FileHandler` üretimi | IMPLEMENTED |
| 8 | File Handler (PSR-3) | `shared/src/Log/FileHandler.php` | 253 | Seviye ayrımı, trace-id, redaksiyon | IMPLEMENTED |
| 9 | Middleware Pipeline | `shared/src/Middleware/MiddlewarePipeline.php` | 54 | Zincir kurma + zamanlama | IMPLEMENTED |
| 10 | Service Registry | `shared/src/Api/Registry/ServiceRegistry.php` | 132 | Kayıt · keşif · sağlık kontrolü | IMPLEMENTED |
| 11 | BFF Layer | `shared/src/Api/Bff/BffLayer.php` | 83 | İstemci tipine göre yanıt dönüştürme | IMPLEMENTED |
| 12 | Gateway | `shared/src/Api/Gateway.php` | ⚠️ VERIFICATION REQUIRED | API yönlendirme girişi | IMPLEMENTED (satır ölçümü bu revizyonda alınmadı) |
| 13 | Structured Logger | `shared/src/PageRouter/StructuredLogger.php` | 81 | Zamanlama + trace kayıtlayıcı | IMPLEMENTED |
| 14 | EventBus (tarayıcı tarafı) | `assets.coremusic.net/js/core/EventBus.js` | 73 | Pub/sub — modüller arası gevşek bağ | IMPLEMENTED |

**Ölçüm notu:** Tablolar `read_text_file` + `\r?\n` bölme (ReadAllLines eşdeğeri) ile 2026-10-15 tarihinde alındı. #12 için satır sayısı bu revizyonda ölçülmedi → `⚠️ VERIFICATION REQUIRED` (uydurma sayı yazılmadı).

## §4 — Kapsam dışı devirler

| # | Devredilen konu | Hedef düğüm | Devir tipi | Kanıt |
|---|---|---|---|---|
| 1 | Domain event tanımı (9 olay) | K008-domain | Tanım | `shared/src/Events/Domain/` (9 dosya) |
| 2 | Integration event tanımı (3 olay) | K013-integraion | Tanım | `shared/src/Events/Integration/` (3 dosya) |
| 3 | OAuth token takası | K013-integraion | Çağrı | `shared/src/OAuth/` (12 sağlayıcı) |
| 4 | Kripto / JWT üretimi | K009-security | Çağrı | `shared/src/Security/JwtService.php` |
| 5 | Rate-limit politika değeri | K009-security | Parametre | `shared/src/Security/CacheRateLimiter.php` |
| 6 | Şema / DDL | Data alanı | Okuma | `.ai/.sql/mysql/*.sql` (20 dosya) |
| 7 | Test suite tanımı | `application-testing/` | Kardeş | `shared/phpunit.xml` (39 satır) |
| 8 | Sayfa render iskeleti | routing alt klasörü | Çağrı | `shared/src/PageRouter/HtmlShellRenderer.php` |

## §5 — Katman akış şeması

```text
HTTP istek
   │
   ▼
PageRouterKernel  ───────────────  (routing/ kapsamı)
   │
   ▼
MiddlewarePipeline::run()  ──────  shared/src/Middleware/MiddlewarePipeline.php:23
   │   pipe() → buildPipeline() → wrap()
   ▼
[ Auth · CSRF · RateLimit · Validation ]   (K009 security politikası)
   │
   ▼
Servis çağrısı (Gateways / BFF / Registry)
   │
   ├──▶ CacheManager::getAdapter()   → ApcuAdapter | MemoryAdapter
   ├──▶ EventDispatcher::dispatch()  → Domain/Integration dinleyicileri
   ├──▶ LoggerFactory::getInstance() → FileHandler (PSR-3)
   │
   ▼
Yanıt (ResponseEmitter · routing kapsamı)
```

**Şema kuralı:** Oklar yalnızca tek yönlüdür; servisler `MiddlewarePipeline`'a geri dönmez (aksi halde zincir iki kez yürür → §F-3).

## §6 — PSR standart eşlemesi

> Bu bölüm `application-services` havuzundaki **5 resmi kaynağı** eşler. Tam URL'ler ve kesişim kanıtı yalnız `application-services-web-arastirma.md` dosyasındadır (havuz dosyalar arası paylaşılmaz).

| # | Standart | Konu | Repo karşılığı | Uyum durumu |
|---|---|---|---|---|
| 1 | PSR-6 | Cache (advanced) | `psr/cache ^3.0` varlığı (`shared/composer.json:16`) + özel `CacheInterface` | ⚠️ VERIFICATION REQUIRED — `Psr\Cache\*` kullanımı repo'da 0 isabet |
| 2 | PSR-16 | Simple cache | Doğrudan karşılık YOK; özel `CacheInterface` benzer işlevi görür | ⚠️ VERIFICATION REQUIRED (PLANNED) |
| 3 | PSR-14 | Event dispatch | `EventDispatcher implements Psr\EventDispatcher\EventDispatcherInterface` (`EventDispatcher.php:14,20`) | IMPLEMENTED |
| 4 | PSR-3 | Logging | `FileHandler implements LoggerInterface` (`FileHandler.php:5,30`) + `psr/log ^3.0` | IMPLEMENTED |
| 5 | PSR-12 | Coding style | `declare(strict_types=1)` + 4 boşluk girinti; otomatik denetleyici YOK | ⚠️ VERIFICATION REQUIRED (araç yok) |

**Eşleme kuralı:** "Bağımlılık paketin var olması" ≠ "kodun standardı uygulaması". §6'da uyum yalnızca `use`/`implements` kanıtıyla `IMPLEMENTED` sayılır.

## §7 — Bağımlılıklar

| # | Bağımlılık | Yön | Tür | Etki |
|---|---|---|---|---|
| 1 | `psr/event-dispatcher ^1.0` | dış → iç | Composer | `EventDispatcher` tip sözleşmesi |
| 2 | `psr/log ^3.0` | dış → iç | Composer | `FileHandler` seviye kanonu |
| 3 | `psr/cache ^3.0` | dış → iç | Composer | Yalnızca paket varlığı (kullanım kanıtsız) |
| 4 | `psr/container ^2.0` | dış → iç | Composer | php-di üzerinden çözümleme |
| 5 | `symfony/event-dispatcher ^7.0` | dış → iç | Composer | ⚠️ VERIFICATION REQUIRED — `shared/src` içinde `use Symfony\...` 0 isabet |
| 6 | `ext-apcu` | ortam | PHP eklentisi | Yoksa `MemoryAdapter`'a düşer |
| 7 | `shared/src/Middleware/*` | iç → iç | Sıra | Servis öncesi kapılar |
| 8 | K008-domain (event tanımları) | iç → dış | Şema | Olay sınıfı sahipliği |
| 9 | K009-security (politika) | iç → dış | Kural | Auth/CSRF/rate-limit değerleri |
| 10 | `application-testing/` | kardeş | Doğrulama | Davranış güvencesi |

## §8 — Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | `application-services` ↔ K012-services sahiplik ayrımı tam olarak nerede? | Kapsam çakışması | ⚠️ VERIFICATION REQUIRED (index open_questions) |
| 2 | Kademe-3 bounded-context kapsamı ne zaman açılır? | Faz C | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |
| 3 | PSR-6'ya geçiş mi, özel `CacheInterface`'te kalış mı? | Tek tip önbellek kontratı | ⚠️ VERIFICATION REQUIRED |
| 4 | `symfony/event-dispatcher` bağımlılığı kullanılıyor mu, yoksa fazlalık mı? | Composer hijyeni | ⚠️ VERIFICATION REQUIRED |
| 5 | BFF katmanı (`Api/Bff/`) bu düğümde mi, ayrı `bff` düğümünde mi? | Dosya sahipliği | ⚠️ VERIFICATION REQUIRED |
| 6 | `Gateway.php` satır ölçümü ve sorumluluk özeti bu revizyonda yapılmadı | Envanter doğruluğu | ⚠️ VERIFICATION REQUIRED |
| 7 | Servislerin senkron/asyenkron sözleşmesi (Promise vs doğrudan çağrı) | Gelecek kırılma riski | TASARIM İDDİASI |
| 8 | `.ai/architecture/**` K-space katman referansının kaldırılması (2026-10-12) bu belgeyi nasıl etkiler? | Referans bütünlüğü | K-space referansı YOK → `.ai/CLAUDE.md` + `.ai/.decisions/index.md` |

## §9 — Ölçüm referansı

| Ölçüm | Değer | Tarih | Yöntem |
|---|---|---|---|
| `shared/src` PHP dosyası | 165 | 2026-10-15 | directory_tree (.php sayımı) |
| `shared/tests` PHP dosyası | 45 | 2026-10-15 | directory_tree (.php sayımı) |
| `assets.coremusic.net` JS dosyası | 98 | 2026-10-15 | directory_tree (.js sayımı) |
| `assets.coremusic.net` CSS dosyası | 94 | 2026-10-15 | directory_tree (.css sayımı) |
| `.ai/.sql/mysql` SQL dosyası | 20 | 2026-10-15 | directory_tree (.sql sayımı) |
| `Events/` PHP (dispatcher + event) | 16 | 2026-10-15 | glob (shared/src/Events/**) |
| `Cache/` PHP | 6 | 2026-10-15 | glob (shared/src/Cache/**) |
| `Api/Bff/` PHP | 5 | 2026-10-15 | glob (shared/src/Api/Bff/**) |

**Sapma kaydı:** Görev spec'i `shared/tests` için "47 dosya" yazar; 2026-10-15 disk ölçümü **45**tır. Disk kazanır → bu belgede 45 kullanılır, sapma §E'ye işlenir.

## §A — Genişletilmiş envanter

### §A.1 Olay (event) envanteri

| # | Sınıf | Klasör | Rol |
|---|---|---|---|
| 1 | `UserLoggedInEvent` | `Events/Domain/` | Oturum açma olayı |
| 2 | `UserLoggedOutEvent` | `Events/Domain/` | Oturum kapatma |
| 3 | `UserRegisteredEvent` | `Events/Domain/` | Kayıt |
| 4 | `PasswordResetRequestedEvent` | `Events/Domain/` | Parola sıfırlama talebi |
| 5 | `GenderSetEvent` | `Events/Domain/` | Cinsiyet seçimi |
| 6 | `MusicPlayedEvent` | `Events/Domain/` | Çalma |
| 7 | `MusicAddedEvent` | `Events/Domain/` | Kütüphaneye ekleme |
| 8 | `PlaylistCreatedEvent` | `Events/Domain/` | Liste oluşturma |
| 9 | `MediaAccessedEvent` | `Events/Domain/` | Medya erişimi |
| 10 | `SessionCreatedEvent` | `Events/Integration/` | Oturum entegrasyonu |
| 11 | `AuthValidatedEvent` | `Events/Integration/` | Kimlik doğrulama |
| 12 | `NotificationEvent` | `Events/Integration/` | Bildirim |
| 13 | `EventDispatcher` | `Events/` | Yayıcı (PSR-14) |
| 14 | `StoppableEventTrait` | `Events/` | Yayım durdurma |
| 15 | `DomainEventInterface` | `Contracts/Events/` | Olay sözleşmesi |
| 16 | `IntegrationEventInterface` | `Contracts/Events/` | Entegrasyon olayı sözleşmesi |

### §A.2 Önbellek envanteri

| # | Dosya | Satır | Not |
|---|---|---|---|
| 1 | `CacheInterface.php` | 13 | Özel kontrat (PSR-6 değil) |
| 2 | `PageCacheInterface.php` | 11 | Sayfa seviyesi kontrat |
| 3 | `CacheManager.php` | 25 | Statik adaptör seçici |
| 4 | `ApcuAdapter.php` | 56 | APCu sarmalayıcı (function_exists korumalı) |
| 5 | `MemoryAdapter.php` | 57 | Bellek içi yedek |
| 6 | `PageCacheAdapter.php` | ⚠️ VERIFICATION REQUIRED | Satır ölçümü bu revizyonda alınmadı |

### §A.3 Günlük (log) envanteri

| # | Dosya | Satır | Not |
|---|---|---|---|
| 1 | `LoggerFactory.php` | 34 | Tekil `FileHandler` |
| 2 | `FileHandler.php` | 253 | PSR-3 `LoggerInterface` |
| 3 | `Redactor.php` | ⚠️ VERIFICATION REQUIRED | Hassas veri redaksiyonu |
| 4 | `StructuredLogger.php` (PageRouter) | 81 | Zamanlama + trace |

### §A.4 API / BFF envanteri

| # | Dosya | Satır | Not |
|---|---|---|---|
| 1 | `BffLayer.php` | 83 | Dönüşüm + istemci tipi çözümü |
| 2 | `SpaBff.php` / `MobileBff.php` / `DesktopBff.php` / `EmbeddedBff.php` | ⚠️ VERIFICATION REQUIRED | 4 dönüşüm |
| 3 | `ServiceRegistry.php` | 132 | Kayıt/keşif/sağlık |
| 4 | `ServiceDefinition.php` / `ServiceHealth.php` | ⚠️ VERIFICATION REQUIRED | Veri taşıyıcıları |
| 5 | `Gateway.php` | ⚠️ VERIFICATION REQUIRED | Satır ölçümü yapılmadı |

## §B — Bileşen ↔ akış eşleme

| # | Akış adımı | Bileşen | Kanıt |
|---|---|---|---|
| 1 | Zincir kurma | `MiddlewarePipeline::pipe()` | `MiddlewarePipeline.php:17` |
| 2 | Zincir yürütme | `MiddlewarePipeline::run()` | `MiddlewarePipeline.php:23` |
| 3 | Sarmalama + zamanlama | `MiddlewarePipeline::wrap()` | `MiddlewarePipeline.php:38-52` |
| 4 | Adaptör seçimi | `CacheManager::getAdapter()` | `CacheManager.php:9-19` |
| 5 | Olay yayımı | `EventDispatcher::dispatch()` | `EventDispatcher.php:28-45` |
| 6 | Öncelik sıralama | `EventDispatcher::sortListeners()` | `EventDispatcher.php:109-118` |
| 7 | Log üretimi | `LoggerFactory::getInstance()` | `LoggerFactory.php:15-22` |
| 8 | Seviye yazımı | `FileHandler` LEVELS | `FileHandler.php:40-49` |
| 9 | Servis keşfi | `ServiceRegistry::discover()` | `ServiceRegistry.php:38-47` |
| 10 | Sağlık kontrolü | `ServiceRegistry::healthCheck()` | `ServiceRegistry.php:52-61` |
| 11 | Yanıt dönüşümü | `BffLayer::transform()` | `BffLayer.php:34-49` |
| 12 | İstemci tipi | `BffLayer::getClientTypeFromRequest()` | `BffLayer.php:66-82` |
| 13 | Tarayıcı yayımı | `EventBus.emit()` | `EventBus.js:57-67` |

## §C — Arayüz (Contracts) envanteri

| # | Interface | Yol | Bağlı servis |
|---|---|---|---|
| 1 | `IMiddleware` | `Contracts/Middleware/IMiddleware.php` | MiddlewarePipeline |
| 2 | `IRateLimiter` | `Contracts/Security/IRateLimiter.php` | RateLimiterMiddleware |
| 3 | `IDatabaseManager` | `Contracts/Database/IDatabaseManager.php` | Servis → veri erişimi |
| 4 | `IDatabaseRegistry` | `Contracts/Database/IDatabaseRegistry.php` | Çoklu şema |
| 5 | `ServiceRegistryInterface` | `Contracts/Api/ServiceRegistryInterface.php` | ServiceRegistry |
| 6 | `BffInterface` | `Contracts/Api/BffInterface.php` | BffLayer |
| 7 | `GatewayInterface` | `Contracts/Api/GatewayInterface.php` | Gateway |
| 8 | `DomainEventInterface` | `Contracts/Events/DomainEventInterface.php` | EventDispatcher |
| 9 | `IntegrationEventInterface` | `Contracts/Events/IntegrationEventInterface.php` | EventDispatcher |
| 10 | `IAuthService` / `ISessionManager` / `IUserRepository` | `Contracts/Auth/` | Oturum servisleri |
| 11 | `IConfigManager` | `Contracts/Config/IConfigManager.php` | Yapılandırma |
| 12 | AI sözleşme paketi (6 adet) | `Contracts/AI/` | AIEngine grubu (⚠️ bu düğümün parçası mı → §8-7) |

## §D — Bağımlılık grafiği

```text
                    [K008-domain]
                          │ event tanımı
                          ▼
 [K009-security] ──▶ [application-services] ──▶ [K012-services]
   politika            │  │  │  │                 (uzun iş)
                       │  │  │  └──▶ CacheManager ──▶ ApcuAdapter / MemoryAdapter
                       │  │  └─────▶ LoggerFactory ──▶ FileHandler (PSR-3)
                       │  └────────▶ EventDispatcher ──▶ Domain/Integration
                       └───────────▶ BffLayer / ServiceRegistry
                                        │
                                        ▼
                              [K013-integraion] (dış kontrat)
                                        │
                                        ▼
                              [application-testing] (davranış güvencesi)
```

**Yön kuralı:** Ok her zaman "çağırır" yönündedir; geri çağırma yalnızca `callable $next` (pipeline) ve `callable $callback` (`remember`) durumlarında meşrudur.

## §E — Kapsam doğrulama kaydı

| # | İddia | Kanıt | Sonuç |
|---|---|---|---|
| 1 | 5 içerik dosyası üretilir | Bu klasörde 5 dosya + index | PASS (bitiş ölçümü §E-son) |
| 2 | index `children` 6 dosyaya güncellenir | `application-services/index.md` | PASS |
| 3 | Havuz 5 URL yalnız bu klasörde | web-arastirma §1 tablosu | PASS (kesişim 0) |
| 4 | Repo kodu uydurulmadı | Her kod bloğu `Dosya/Satır` taşır | PASS |
| 5 | Ölçüm sapması raporlandı | §9 "45 vs 47" satırı | PASS |
| 6 | Kanıtsız satır `⚠️` işaretli | §A.2-6, §A.4-5, §3-12 | PASS |
| 7 | Kapsam dışı devirler komşu K'ye yazıldı | §4 (8 satır) | PASS |
| 8 | Açık sorular bölümü mevcut | §8 (8 satır) | PASS |
| 9 | `git commit` atılmadı | Görev kuralı §1.7 | PASS (subagent commit YOK) |
| 10 | Yazma alanı yalnız bu 2 klasör | — | PASS |

## §F — PLANNED boşluklar

| # | Boşluk | Etiket | Ne yapılacak |
|---|---|---|---|
| 1 | PSR-16 simple-cache adaptörü yok | PLANNED | İhtiyaç doğrulanırsa `Psr\SimpleCache\CacheInterface` sarmalayıcı |
| 2 | PSR-6 `Psr\Cache\*` kullanımı yok (paket kurulu) | ⚠️ VERIFICATION REQUIRED | Karar: adaptör mü, bağımlılık temizliği mi |
| 3 | Pipeline içinde servis çağrısı geri dönüşü şeması belirsiz | TASARIM İDDİASI | §D yön kuralı ile çelişirse ADR |
| 4 | PSR-12 otomatik denetleyici (fixer/linter) yok | PLANNED | `composer stan` var (PHPStan level=5) ama stil denetimi ayrı |
| 5 | `symfony/event-dispatcher` kullanım kanıtı yok | ⚠️ VERIFICATION REQUIRED | `grep use Symfony` 0 isabet |
| 6 | Servis erişim merkezi (service locator) usage | UNKNOWN | `php-di` kurulumu var; kullanım noktası ölçülmedi |

## §G — Kapsam SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | Bu dosya kod içerir mi? | Hayır — yalnız sınır, envanter ve eşleme; kod `ornekler` dosyasında |
| 2 | PSR-6 uyumlu muyuz? | Paket var, kullanım kanıtı yok → `⚠️ VERIFICATION REQUIRED` |
| 3 | Event'ler burada mı tanımlanır? | Hayır, K008-domain; burada yalnızca yayın (`dispatch`) |
| 4 | Cache teknoloji kararı kimin? | `caching/` kardeş düğümü + K012-services |
| 5 | Testleri kim yazar? | `application-testing/` + QA Engineer |
| 6 | K-space (`K000-K020`) referansı kullanılıyor mu? | Hayır — K-space katman mimarisi 2026-10-12'de kaldırıldı; referans `.ai/CLAUDE.md` |
| 7 | Bu dosyanın group değeri neden `mimari`? | Kapsam = tasarım iddiası (spec §1.2) |
| 8 | URL'ler neden burada yok? | Havuz tek dosyada: `application-services-web-arastirma.md` |

## §H — Sürüm kaydı

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk üretim — Kademe-2 alt-modül seti (Unit 2) |
| 1.0.0 | 2026-10-15 | Envanter ölçümleri directory_tree ile alındı (165 PHP / 45 test / 98 JS / 94 CSS / 20 SQL) |
| 1.0.0 | 2026-10-15 | Ölçüm sapması kaydı: spec "47 test dosyası" → disk 45 |

## §I — Terim sözlüğü

| Terim | Tanım (bu belge bağlamında) |
|---|---|
| Servis | Uygulama içinde tek bir işi yapan, yan etkisi bilinen bileşen |
| Orkestrasyon | Birden fazla servisin sırayla çağrılıp tek sonuç üretilmesi |
| Adaptör | Aynı kontratın farklı teknolojiyle (APCu/bellek) uygulanması |
| Pipeline | Middleware'lerin çekirdek işlevi sarmalayarak dizilmesi |
| Dispatcher | Olayı dinleyicilere sırayla taşıyan bileşen (PSR-14) |
| BFF | İstemci tipine (spa/mobile/desktop/embedded) göre yanıt dönüştürme |
| Registry | İsimle servis bulma + sağlık kontrolü yapan katalog |
| Trace-id | İstek boyunca aynı kalan korelasyon kimliği |
| Redaksiyon | Log'daki parola/token/eposta maskelenmesi |
| TTL | Önbellek kaydının geçerlilik süresi (saniye) |
| IMPLEMENTED | Repo kodu ile kanıtlanmış durum |
| PLANNED | Hedeflenen, henüz repo'da karşılığı olmayan |
| TASARIM İDDİASI | Belgede yazılan, kodla henüz sınanmamış tasarım cümlesi |
| ⚠️ VERIFICATION REQUIRED | Doğrulanamayan iddia — kesinlikle "var" denmez |
| Kardeş düğüm | Aynı K011-application altında yan yana alt-kapsam |
| K-space | K000-K020 katman mimarisi — 2026-10-12'de kaldırıldı |
| Vault SSOT | `.ai/CLAUDE.md` + `.ai/.decisions/index.md` |
| Kademe-2 | Alt-kapsam (subdomain) seviyesi — module'e inilmez |
| Kademe-3 | Bounded-context seviyesi — faz C (ADR-097 K4) bekliyor |
| Ölçüm penceresi | Envanter sayımlarının alındığı tarih (2026-10-15) |

## §J — Servis ↔ kontrat detayı

| # | Servis | Kontrat (interface) | Metot sayısı | Metotlar |
|---|---|---|---|---|
| 1 | Cache (özel) | `CacheInterface` | 6 | `get`, `set`, `delete`, `increment`, `clear`, `has` |
| 2 | Page cache | `PageCacheInterface` | 4 | `isPageCacheEnabled`, `ttlPage`, `pageKey`, `remember` |
| 3 | Event dispatcher | `Psr\EventDispatcher\EventDispatcherInterface` | 1 | `dispatch` (+ yerel `addListener`, `removeListener`, `getListeners`, `hasListeners`) |
| 4 | Log | `Psr\Log\LoggerInterface` | 9 | `emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug`, `log` |
| 5 | Middleware | `IMiddleware` | 1 | `handle(array $request, callable $next): array` |
| 6 | BFF | `BffInterface` | ≥2 | `getClientType`, `transform` (⚠️ tam metot listesi ölçülmedi → `⚠️ VERIFICATION REQUIRED`) |
| 7 | Registry | `ServiceRegistryInterface` | ≥5 | `register`, `discover`, `healthCheck`, `getHealthyServices`, `loadFromConfig` |
| 8 | DB | `IDatabaseManager` | 5 | `execute`, `write`, `lastInsertId`, `beginTransaction`, +1 (⚠️ ölçülmedi) |

**Not:** 3–4 numaralı satırlar PSR'ın kendi sözleşmesidir; yerel metotlar ekleme, standardın parçası değildir.

## §K — Akış senaryoları (adım adım)

### §K.1 Olay yayımı senaryosu

| Adım | Aksiyon | Dosya | Satır |
|---|---|---|---|
| 1 | Olay sınıfı oluşturulur (ör. `UserLoggedInEvent`) | `Events/Domain/` | — |
| 2 | `dispatch($event)` çağrılır | `EventDispatcher.php` | 28 |
| 3 | Sınıf adına göre dinleyici aranır | `EventDispatcher.php` | 30-34 |
| 4 | Öncelik sırasına göre liste alınır | `EventDispatcher.php` | 88-95 |
| 5 | Durdurma bayrağı kontrol edilir | `EventDispatcher.php` | 37-39 |
| 6 | Her dinleyici çağrılır | `EventDispatcher.php` | 41 |
| 7 | Olay geri döner | `EventDispatcher.php` | 44 |

### §K.2 Önbellek yazma senaryosu

| Adım | Aksiyon | Dosya | Satır |
|---|---|---|---|
| 1 | Adaptör daha önce seçilmiş mi bakılır | `CacheManager.php` | 11 |
| 2 | `apcu_fetch` var mı kontrol edilir | `CacheManager.php` | 12 |
| 3 | Yoksa `MemoryAdapter` bağlanır | `CacheManager.php` | 15 |
| 4 | `set($key,$value,$ttl)` çağrılır | `CacheInterface.php` | 8 |
| 5 | APCu yoksa `false` döner (sessiz kayıp yok) | `ApcuAdapter.php` | 19-21 |
| 6 | Sonuç çağıran katmana iletilir | çağıran | — |

### §K.3 Günlük yazma senaryosu

| Adım | Aksiyon | Dosya | Satır |
|---|---|---|---|
| 1 | Tekil örnek alınır | `LoggerFactory.php` | 15-22 |
| 2 | Log dizini çözümlenir (repo kökü) | `LoggerFactory.php` | 29-33 |
| 3 | `FileHandler` kurulur | `LoggerFactory.php` | 19 |
| 4 | Seviye adı kanonik listede mi | `FileHandler.php` | 40-49 |
| 5 | Redaksiyon uygulanır | `FileHandler.php` (docblock) | 27-28 |
| 6 | Trace-id satıra eklenir | `FileHandler.php` | 65-68 |

### §K.4 İstemciye göre yanıt dönüşümü

| Adım | Aksiyon | Dosya | Satır |
|---|---|---|---|
| 1 | `X-Client-Type` başlığı okunur | `BffLayer.php` | 69-72 |
| 2 | Yoksa `client_type` sorgusu okunur | `BffLayer.php` | 75-78 |
| 3 | Varsayılan `spa` kabul edilir | `BffLayer.php` | 81 |
| 4 | Eşleşen dönüştürücü bulunur | `BffLayer.php` | 36 |
| 5 | `spa` yedeğine düşülür | `BffLayer.php` | 38-41 |
| 6 | Dönüştürücü yoksa ham yanıt döner | `BffLayer.php` | 43-46 |

## §L — Kapsam riskleri

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| 1 | K012-services ile iki katmanlı mükerrer orkestrasyon | Orta | Yüksek | §8-1 sahiplik sorusu kapatılana kadar kod eklenmez |
| 2 | Özel cache kontratının PSR-6'ya taşınması | Orta | Orta | Adaptör katmanı zaten var (`ApcuAdapter`) |
| 3 | Statik `CacheManager`/`LoggerFactory` singleton'ı — test izolasyonu | Yüksek | Orta | `setAdapter()` ve `reset()` mevcut (CacheManager.php:21, LoggerFactory.php:24) |
| 4 | Ölçülmemiş satır sayılarının belgeye sızmısı | Düşük | Orta | Her ölçülmemiş hücre `⚠️` işaretli |
| 5 | K-space referanslarının geri gelmesi | Düşük | Düşük | §G-6 tek kaynak güncellemesi |
| 6 | Havuz dışı URL sızıntısı | Düşük | Yüksek | Bitişte §5 doğrulama betiği (web-arastirma) |

## §M — Kardeş dosya indeksi

| # | Dosya | group | Eksen 2 | Bu dosyayla ilişkisi |
|---|---|---|---|---|
| 1 | `index.md` | mimari | künye | Çocuk kataloğu (6 satır) |
| 2 | `application-services-kapsam.md` | mimari | kapsam | Bu dosya — sınır sözleşmesi |
| 3 | `application-services-dogrulama.md` | kanit | dogrulama | İddia ↔ kanıt kapıları |
| 4 | `application-services-web-arastirma.md` | kanit | arastirma | 5 URL havuzu + çapraz matris |
| 5 | `application-services-ornekler.md` | kod | ornek | 4 dilde gerçek repo kodu |
| 6 | `application-services-bilesenler.md` | mimari | bilesen | Sayımlar + bağımlılık grafiği |

## §N — Kapsam dışı kod adları (bu düğümde yazılmaz)

| # | Konu | Neden yazılmaz | Doğru yer |
|---|---|---|---|
| 1 | Yeni middleware sınıfı | K009-security + MiddlewarePipeline kaydı | `shared/src/Middleware/` |
| 2 | Yeni event sınıfı | Domain sahipliği | `shared/src/Events/Domain/` |
| 3 | Yeni cache teknolojisi | Kapsam dışı teknoloji kararı | `caching/` + K012-services |
| 4 | Test dosyası | Kardeş klasör | `application-testing/` |
| 5 | SQL DDL / migration | Data sahipliği | `.ai/.sql/mysql/` (20 dosya) |
| 6 | CSS token / JS bileşeni | Sunum katmanı | `assets.coremusic.net/Css|js` |
| 7 | Route tanımı | routing düğümü | `shared/config/routes.php` |
| 8 | OAuth/ödeme kontratı | K013-integraion | `shared/src/OAuth/` + entegrasyon |

## §Ö — Ölçüm yöntemleri (tekrarlanabilir)

```powershell
# PowerShell (repo kökünden) — spec §5 ile aynı ölçüm
$root = "C:\www\coremusic.net"
$targets = @("shared\src","shared\tests","assets.coremusic.net\js","assets.coremusic.net\Css",".ai\.sql\mysql")
foreach ($t in $targets) {
  $n = (Get-ChildItem "$root\$t" -Recurse -File | Where-Object { $_.Extension -in '.php','.js','.css','.sql' }).Count
  "{0}  {1}" -f $n, $t
}
```

```text
Bu revizyonda ölçülen değerler (2026-10-15 · filesystem.directory_tree):
  165  shared\src        (.php)
   45  shared\tests      (.php)   ← spec §4 "47" ile sapma kayıtlı
   98  assets.coremusic.net (.js, tüm ağaç)
   94  assets.coremusic.net\Css (.css)
   20  .ai\.sql\mysql    (.sql)
```

**Not (araç):** Bu oturumda `shell` izni reddedildiği için PowerShell doğrudan çalıştırılamadı; sayımlar `filesystem.directory_tree` ile alındı ve yukarıdaki komut aynı sonucu vermek için yeniden çalıştırılabilir (eşdeğerlik kök gezinme + uzantı sayımıdır).

## §P — Kapsam karar özeti (tek ekran)

| # | Karar | Sonuç |
|---|---|---|
| 1 | Bu düğüm neyi kapsar? | Uygulama içi servis orkestrasyonu + PSR standart eşlemesi |
| 2 | Ne kapsam dışıdır? | Kural (K008) · politika (K009) · dış kontrat (K013) · worker (K012) · test (application-testing) |
| 3 | Hangi 5 standart? | PSR-6 · PSR-16 · PSR-14 · PSR-3 · PSR-12 (havuz, URL'ler web-arastirma'da) |
| 4 | Uyumu kanıtlananlar | PSR-14 (`EventDispatcher.php:14,20`) · PSR-3 (`FileHandler.php:5,30`) |
| 5 | Kanıtsız/eksikler | PSR-6 kullanımı · PSR-16 karşılığı · PSR-12 otomatik denetleyici |
| 6 | En büyük açık | `application-services` ↔ K012-services sahiplik ayrımı → `⚠️ VERIFICATION REQUIRED` |
| 7 | Ölçüm temeli | 2026-10-15 disk ölçümü (§9) — spec "47" → disk 45 sapması kayıtlı |
| 8 | Üretim durumu | 5 içerik dosyası + index (6 çocuk) — Unit 2 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
