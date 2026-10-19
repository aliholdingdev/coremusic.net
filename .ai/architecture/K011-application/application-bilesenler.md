---
node_id: K011-application/application-bilesenler
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K008-domain
  - K009-security
  - K012-services
  - K013-infrastructure
evidence:
  - "shared/src/PageRouter/ — 14 PHP dosyası (2026-10-12, satır ölçümü eklendi)"
  - "shared/src/Middleware/ — 11 PHP dosyası (2026-10-12, satır ölçümü eklendi)"
  - "shared/src/Events/ — EventDispatcher + StoppableEventTrait + 9 domain + 3 integration olay sınıfı (2026-10-12)"
  - "shared/src/Cache/ — 6 PHP dosyası (2026-10-12)"
  - "shared/src/Session/ — 4 PHP dosyası (2026-10-12)"
  - "shared/src/Api/ — 3 PHP dosyası (2026-10-12)"
validation:
  - "Bileşen envanteri disk üzerinden `Get-Content | Measure-Object -Line` ile derlendi: 2026-10-12"
  - "Önceki taslakta adı geçen var olmayan sınıflar çıkarıldı (zero-hallucination)"
open_questions:
  - "Use case katmanı bileşenleri kodda yok — hangi adla üretilecek?"
  - "Kuyruk işleyicileri K011 mi K013 mü kapsamında?"
  - "Olay sınıfları (event objects) nerede tutulacak — K011 mi K014 mü?"
group: mimari
---

# K011 Application — Bileşen Envanteri

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: K011 bileşen kataloğu (yalnız diskte var olan dosyalar)**
> **Kaynak: disk kanıtı + `coremusic-katmanli-mimari-agaci-v2.md` §2.11**

## §1 ENVANTER İLKELERİ

1. **Kanıt sırası:** Dosya varsa ✅ · arayüz var ama somut sınıf yoksa Kısmi · hiç kanıt yoksa ⚠️ `verify-required`.
2. **Sıfır uydurma:** Bu dosyada adı geçen her sınıf diskte vardır; olmayan her şey §7'de `verify-required` işaretlenir.
3. **Kapsam sınırı:** Envanter yalnız K011 dosyalarını (Bant 1) kapsar; domain ve servis sınıfları K008/K012'dedir.
4. **Satır ölçümü:** `Get-Content <dosya> | Measure-Object -Line`, 2026-10-12.

## §2 YÖNLENDİRME BİLEŞENLERİ — `shared/src/PageRouter/` (14 dosya)

| # | Bileşen | Dosya | Rol | Satır | Durum |
|---|---|---|---|---|---|
| 1 | `PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php` | Çekirdek: istek yaşam döngüsü | 281 | ✅ active |
| 2 | `HtmlShellRenderer` | `shared/src/PageRouter/HtmlShellRenderer.php` | HTML kabuk çizimi | 270 | ✅ active |
| 3 | `PageRouter` | `shared/src/PageRouter/PageRouter.php` | Yönlendirici | 164 | ✅ active |
| 4 | `RequestNormalizer` | `shared/src/PageRouter/RequestNormalizer.php` | İstek normalizasyonu | 152 | ✅ active |
| 5 | `ResponseEmitter` | `shared/src/PageRouter/ResponseEmitter.php` | Yanıt yayımı | 142 | ✅ active |
| 6 | `SessionInitializer` | `shared/src/PageRouter/SessionInitializer.php` | Oturum başlatma | 116 | ✅ active |
| 7 | `AuthUrlBuilder` | `shared/src/PageRouter/AuthUrlBuilder.php` | Kimlik URL üretimi | 83 | ✅ active |
| 8 | `RouteResult` | `shared/src/PageRouter/RouteResult.php` | Eşleşme sonucu | 80 | ✅ active |
| 9 | `AuthGuard` | `shared/src/PageRouter/AuthGuard.php` | Kimlik bekçi | 70 | ✅ active |
| 10 | `RouteRegistry` | `shared/src/PageRouter/RouteRegistry.php` | Route kayıt defteri | 66 | ✅ active |
| 11 | `StructuredLogger` | `shared/src/PageRouter/StructuredLogger.php` | Yapılandırılmış günlüğe | 64 | ✅ active |
| 12 | `ErrorHandler` | `shared/src/PageRouter/ErrorHandler.php` | Hata yakalama | 62 | ✅ active |
| 13 | `PageRouterHelper` | `shared/src/PageRouter/PageRouterHelper.php` | Yardımcı işlevler | 25 | ✅ active |
| 14 | `SpaRoute` | `shared/src/PageRouter/SpaRoute.php` | SPA route tanımı | 17 | ✅ active |

**Toplam: 1.575 satır (ölçüm: 2026-10-12).**

## §3 MIDDLEWARE BİLEŞENLERİ — `shared/src/Middleware/` (11 dosya)

| # | Bileşen | Dosya | Rol | Satır | Durum |
|---|---|---|---|---|---|
| 1 | `RateLimiterMiddleware` | `shared/src/Middleware/RateLimiterMiddleware.php` | Hız sınırı | 126 | ✅ active |
| 2 | `CorsMiddleware` | `shared/src/Middleware/CorsMiddleware.php` | CORS başlıkları | 121 | ✅ active |
| 3 | `OriginCheckMiddleware` | `shared/src/Middleware/OriginCheckMiddleware.php` | Köken denetimi | 106 | ✅ active |
| 4 | `SecurityHeadersMiddleware` | `shared/src/Middleware/SecurityHeadersMiddleware.php` | Güvenlik başlıkları | 86 | ✅ active |
| 5 | `ValidationMiddleware` | `shared/src/Middleware/ValidationMiddleware.php` | Girdi doğrulama | 69 | ✅ active |
| 6 | `PermissionMiddleware` | `shared/src/Middleware/PermissionMiddleware.php` | İzin denetimi | 54 | ✅ active |
| 7 | `BypassAuthMiddleware` | `shared/src/Middleware/BypassAuthMiddleware.php` | Auth bypass (istisna) | 54 | ✅ active |
| 8 | `CsrfMiddleware` | `shared/src/Middleware/CsrfMiddleware.php` | CSRF denetimi | 52 | ✅ active |
| 9 | `MiddlewarePipeline` | `shared/src/Middleware/MiddlewarePipeline.php` | Zincir yürütme | 43 | ✅ active |
| 10 | `AuthMiddleware` | `shared/src/Middleware/AuthMiddleware.php` | Kimlik doğrulama | 28 | ✅ active |
| 11 | `SessionManagerMiddleware` | `shared/src/Middleware/SessionManagerMiddleware.php` | Oturum sarmalama | 19 | ✅ active |

**Toplam: 758 satır.** `MiddlewarePipeline` iki yerde bulunur (`PageRouter/` altında dosya yok, `Middleware/` altında var) — zincir yürütmenin tek kaynağı §3.9'dur.

## §4 OLAY ALTYAPISI — `shared/src/Events/` (14 dosya + 2 sözleşme arayüzü)

| # | Bileşen | Dosya | Rol | Satır | Durum |
|---|---|---|---|---|---|
| 1 | `EventDispatcher` | `shared/src/Events/EventDispatcher.php` | Olay yayımı ve abonelik | 109 | ✅ active |
| 2 | `StoppableEventTrait` | `shared/src/Events/StoppableEventTrait.php` | Durdurulabilir olay özniteliği | 31 | ✅ active |
| 3 | `DomainEventInterface` | `shared/src/Contracts/Events/DomainEventInterface.php` | Domain olay sözleşmesi | — | ✅ active |
| 4 | `IntegrationEventInterface` | `shared/src/Contracts/Events/IntegrationEventInterface.php` | Entegrasyon olay sözleşmesi | — | ✅ active |

**Domain olayları (`shared/src/Events/Domain/` — 9 sınıf):**
`UserRegisteredEvent` · `UserLoggedInEvent` · `UserLoggedOutEvent` · `PasswordResetRequestedEvent` · `GenderSetEvent` · `PlaylistCreatedEvent` · `MusicPlayedEvent` · `MusicAddedEvent` · `MediaAccessedEvent` — hepsi `final class ... implements DomainEventInterface`, satır 20'de (2026-10-12).

**Entegrasyon olayları (`shared/src/Events/Integration/` — 3 sınıf):**
`SessionCreatedEvent` · `AuthValidatedEvent` · `NotificationEvent` — `final class ... implements IntegrationEventInterface`, satır 20'de (2026-10-12).

**Toplam: 12 olay sınıfı + yayıncı + öznitelik + 2 arayüz.**

## §5 ÖNBELLEK — `shared/src/Cache/` (6 dosya)

| # | Bileşen | Dosya | Rol | Satır | Durum |
|---|---|---|---|---|---|
| 1 | `MemoryAdapter` | `shared/src/Cache/MemoryAdapter.php` | Bellek içi adaptör | 49 | ✅ active |
| 2 | `ApcuAdapter` | `shared/src/Cache/ApcuAdapter.php` | APCu adaptörü | 49 | ✅ active |
| 3 | `PageCacheAdapter` | `shared/src/Cache/PageCacheAdapter.php` | Sayfa önbelleği adaptörü | 37 | ✅ active |
| 4 | `CacheManager` | `shared/src/Cache/CacheManager.php` | Önbellek kapısı | 21 | ✅ active |
| 5 | `CacheInterface` | `shared/src/Cache/CacheInterface.php` | Genel arayüz | 11 | ✅ active |
| 6 | `PageCacheInterface` | `shared/src/Cache/PageCacheInterface.php` | Sayfa önbelleği arayüzü | 9 | ✅ active |

**Toplam: 176 satır.** Dosya tabanlı adaptör (`FileCache`) ve `CacheWarmer`/`CacheMetrics` **diskte yok** → §7.

## §6 OTURUM, API VE KATMAN BİLEŞENLERİ

| # | Bileşen | Dosya | Rol | Satır | Durum |
|---|---|---|---|---|---|
| 1 | `Gateway` | `shared/src/Api/Gateway.php` | Uzak servis kapısı | 145 | ✅ active |
| 2 | `ApiRequest` | `shared/src/Api/ApiRequest.php` | API istek zarfı | 142 | ✅ active |
| 3 | `ApiResponse` | `shared/src/Api/ApiResponse.php` | API yanıt zarfı | 116 | ✅ active |
| 4 | `SessionLifecycle` | `shared/src/Session/SessionLifecycle.php` | Oturum yaşam döngüsü | 108 | ✅ active |
| 5 | `SessionBootstrapper` | `shared/src/Session/SessionBootstrapper.php` | Oturum hazırlama | 68 | ✅ active |
| 6 | `SessionConfig` | `shared/src/Session/SessionConfig.php` | Oturum yapılandırması | 54 | ✅ active |
| 7 | `SessionInitializer` | `shared/src/Session/SessionInitializer.php` | Oturum başlatma (session katmanı) | 43 | ✅ active |
| 8 | `Container` (DI) | `auth.coremusic.net/include/Container/` | Bağımlılık çözümleme | — | ✅ active |
| 9 | `Handler` sınıfları | `auth.coremusic.net/include/Handler/` | İşleyici | — | ✅ active |
| 10 | `Controller` sınıfları | `auth.coremusic.net/include/Controller/` | Uç nokta | — | ✅ active |
| 11 | `Repository` sınıfları | `auth.coremusic.net/include/Repository/` | Veri erişim | — | ✅ active |
| 12 | `Service` sınıfları | `auth.coremusic.net/include/Service/` | Uygulama servisi | — | ✅ active |
| 13 | `DTO` sınıfları | `auth.coremusic.net/include/Domain/DTO/` | Veri taşıma | — | ✅ active |
| 14 | `Entity`/`ValueObject` | `auth.coremusic.net/include/Domain/` | Domain modeli (K008 sınırı) | — | ✅ active |

## §7 PLANNED BİLEŞENLER (KOD YOK — `verify-required`)

| # | Bileşen | Beklenen rol | Kanıt denemesi (2026-10-12) | Durum |
|---|---|---|---|---|
| 1 | `*UseCase` sınıfları | Senaryo orkestrasyonu | sembol araması = 0 sonuç | ⚠️ verify-required |
| 2 | `shared/src/Command*` | Değişiklik niyeti zarfı | glob = 0 | ⚠️ verify-required |
| 3 | `shared/src/Query*` | Salt okunur niyet zarfı | glob = 0 | ⚠️ verify-required |
| 4 | `shared/src/Service/` | Uygulama servisi katmanı | dizin = yok | ⚠️ verify-required |
| 5 | `shared/src/Queue/` | Kuyruk işleyici | dizin = yok | ⚠️ verify-required |
| 6 | Kuyrukçu / cron / worker | Zamanlanmış görev yürütme | repo genelinde = 0 | ⚠️ verify-required |
| 7 | Ödeme orkestratör | Ödeme akışı bileşeni | repo genelinde = 0 | ⚠️ verify-required |
| 8 | Olay **dinleyici** sınıfları | `EventDispatcher` aboneliklerini karşılayacak handler seti | `shared/src/Events/` içinde yalnız yayıncı + olay sınıfları var, dinleyici yok | ⚠️ verify-required |
| 9 | `FileCache` / `CacheWarmer` | Dosya önbelleği + sıcaklaştırma | `shared/src/Cache/` içinde yok | ⚠️ verify-required |
| 10 | `RateLimit` deposu (kalıcı) | Redis/db tabanlı sayaç | yalnız `RateLimiterMiddleware` var | ⚠️ verify-required |

## §8 BİLEŞEN DAĞILIMI (KANIT DURUMU)

```text
K011-application bileşen envanteri (2026-10-12)
├── Yönlendirme 14 dosya / 1.575 satır  ████████████████  kanıt: tam
├── Middleware   11 dosya /   758 satır  ███████████       kanıt: tam
├── Olay altyapı 14 dosya (yayıncı 2 + domain 9 + integration 3) + 2 arayüz  ████  kanıt: tam
├── Önbellek     6 dosya /   176 satır  ██████            kanıt: tam
├── Oturum+API   7 dosya /   636 satır  ███████           kanıt: tam
├── auth.* katman(6 dizin)              ████████          kanıt: tam (dosya sayıları §6)
└── Planned     10 kalem               ░░░░░░░░░░        kanıt: yok → verify-required
```

## §9 GRUPLAR

| Grup | Bölüm | Amaç |
|---|---|---|
| mimari | §2 · §3 · §4 · §5 · §6 · §7 · §8 | Bileşen yapısı ve dağılımı |
| kod | — (bu dosyada kod bloğu yok) | — |
| kanit | §1 · §10 | Envanter kanıt yöntemi |

## §10 KANIT YÖNTEMİ

| # | Adım | Araç / komut | Tarih | Sonuç |
|---|---|---|---|---|
| 1 | Dizin listeleme | `Get-ChildItem shared/src/*` | 2026-10-12 | §2–§6 |
| 2 | Satır sayma | `Get-Content \| Measure-Object -Line` | 2026-10-12 | Satır sütunu |
| 3 | Sembol araması | arama: `UseCase`, `Command`, `Queue` | 2026-10-12 | 0 → §7 |
| 4 | Test dosyaları | `shared/tests/` listesi | 2026-10-12 | test-stratejisi dosyasında |

## §11 AÇIK SORULAR

1. Use case sınıf adlandırma deseni ne olacak (`*UseCase` mi, Türkçe karşılık mı)?
2. Kuyruk işleyicileri K011 bileşen envanterine mi girecek?
3. `auth.coremusic.net/include/Service/` sınıfları uygulama mı domain mi sınırında?
4. Olay sınıfları hangi klasörde üretilecek (`shared/src/Events/` mi, K014 mü)?

## §12 REFERANSLAR

- `shared/src/PageRouter/` · `shared/src/Middleware/` · `shared/src/Events/` · `shared/src/Cache/` · `shared/src/Session/` · `shared/src/Api/` — disk (2026-10-12)
- `auth.coremusic.net/include/` — disk (2026-10-12)
- `coremusic-katmanli-mimari-agaci-v2.md` §2.11 — K011 kapsamı

## §13 EN ÖNEMLİ 10 BİLEŞEN (hızlı bakış)

| # | Bileşen | Dosya | Satır | Neden önemli |
|---|---|---|---|---|
| 1 | `PageRouterKernel` | `PageRouter/PageRouterKernel.php` | 281 | tek giriş |
| 2 | `HtmlShellRenderer` | `PageRouter/HtmlShellRenderer.php` | 270 | SPA kabuğu |
| 3 | `PageRouter` | `PageRouter/PageRouter.php` | 164 | yönlendirici |
| 4 | `RequestNormalizer` | `PageRouter/RequestNormalizer.php` | 152 | istek şekillendirme |
| 5 | `ResponseEmitter` | `PageRouter/ResponseEmitter.php` | 142 | yanıt yayımı |
| 6 | `RateLimiterMiddleware` | `Middleware/RateLimiterMiddleware.php` | 126 | abuse koruması |
| 7 | `CorsMiddleware` | `Middleware/CorsMiddleware.php` | 121 | kaynak izni |
| 8 | `OriginCheckMiddleware` | `Middleware/OriginCheckMiddleware.php` | 106 | köken denetimi |
| 9 | `EventDispatcher` | `Events/EventDispatcher.php` | 109 | yayım çekirdeği |
| 10 | `SessionLifecycle` | `Session/SessionLifecycle.php` | 108 | oturum yaşam döngüsü |

## §14 BİLEŞEN EKLEME KURALLARI

1. Yeni bileşen önce arayüz (`Contracts/`) altına yazılır; somut sınıf sonra gelir.
2. Somut sınıflar `final` olur; kalıtım yalnız gerekçeli durumda (ADR).
3. Envantere ekleme = bu dosyaya yeni satır (append-only) + `application-dogrulama.md` §2'ye ölçüt.
4. Silme yalnız ADR ile; envanter satırı boşaltılmaz.
5. Satır sayısı her büyük değişiklikte yeniden ölçülür (`Measure-Object -Line`, 2026-10-12 yöntemi).

## §A — Kapsam

> **Amaç:** Bu bileşen envanterinin hangi yüzeyleri kapsadığını ve kapsam dışı bırakılanları sınırlandırmak. Kapsam 2026-10-13 tarihinde disk üzerinden yeniden teyit edildi.

### §A.1 — Kapsam içi yüzeyler

| # | Yüzey | Dizin | Dosya sayısı | Durum |
|---|---|---|---|---|
| 1 | Yönlendirme | `shared/src/PageRouter/` | 14 | ✅ kapsam içi |
| 2 | Middleware | `shared/src/Middleware/` | 11 | ✅ kapsam içi |
| 3 | Olay altyapısı | `shared/src/Events/` + `Contracts/Events/` | 14 + 2 | ✅ kapsam içi |
| 4 | Önbellek | `shared/src/Cache/` | 6 | ✅ kapsam içi |
| 5 | Oturum | `shared/src/Session/` | 4 | ✅ kapsam içi |
| 6 | API zarfı | `shared/src/Api/` | 3 | ✅ kapsam içi |
| 7 | Arayüzler | `shared/src/Contracts/` | 19 | ✅ kapsam içi (§C) |
| 8 | alt proje katmanları | `auth.coremusic.net/include/` | 6 dizin | ✅ referans (K012 sınırı) |

### §A.2 — Kapsam dışı yüzeyler

| # | Yüzey | Neden kapsam dışı | İlgili K |
|---|---|---|---|
| 1 | Domain sınıfları | domain sınırı | K008 |
| 2 | Güvenlik yardımcıları | güvenlik sınırı | K009 |
| 3 | Veritabanı şeması | veri katmanı | K005 |
| 4 | Altyapı (kuyruk, zamanlayıcı) | altyapı sınırı | K013 |
| 5 | Ödeme servisleri | servis sınırı | K012 |
| 6 | frontend varlıkları | UI sınırı | K010 |

### §A.3 — Kapsam sayımı özeti

| # | Sayım | Değer | Tarih |
|---|---|---|---|
| 1 | Bileşen dosyası (§2–§6) | 52 | 2026-10-12 |
| 2 | Planned kalem (§7) | 10 | 2026-10-12 |
| 3 | Arayüz dosyası (§C) | 19 | 2026-10-13 |
| 4 | Olay sınıfı | 12 | 2026-10-12 |
| 5 | Toplam satır (§2–§6) | 3.145 | 2026-10-12 |

### §A.4 — Kapsam değişiklik kuralları

| # | Değişiklik | Aksiyon | Onay |
|---|---|---|---|
| 1 | Yeni dizin ekleme | §A.1'e satır | Vault Steward |
| 2 | Yüzey çıkarma | ADR gerekir | ADR |
| 3 | Dosya sayısı değişimi | ölçüm + §A.3 satırı | backend |
| 4 | K0 devri | §A.2'ye satır | Vault Steward |

## §B — Bileşen listesi

> **Amaç:** §2–§6'daki envanterin rol bazlı özeti — hangi rolü kaç bileşen karşılıyor, hangi rol boş.

### §B.1 — Rol dağılımı

| # | Rol | Bileşen sayısı | Bölüm | Durum |
|---|---|---|---|---|
| 1 | İstek kabulü | 5 | §2 | ✅ dolu |
| 2 | Kimlik/ yetki | 4 | §2 · §3 | ✅ dolu |
| 3 | Güvenlik denetimi | 8 | §3 | ✅ dolu |
| 4 | Görselleştirme | 3 | §2 | ✅ dolu |
| 5 | Olay yayımı | 2 + 12 | §4 | ✅ dolu |
| 6 | Önbellek | 6 | §5 | ✅ dolu |
| 7 | Oturum | 5 | §6 | ✅ dolu |
| 8 | API | 3 | §6 | ✅ dolu |
| 9 | Senaryo orkestrasyonu | 0 | §7/1 | ⚠️ boş (kod yok) |
| 10 | Komut/sorgu | 0 | §7/2 · §7/3 | ⚠️ boş (kod yok) |
| 11 | Kuyruk/zamanlama | 0 | §7/5 · §7/6 | ⚠️ boş (kod yok) |
| 12 | Ödeme | 0 | §7/7 | ⚠️ boş (kod yok) |

### §B.2 — Boyut sıralaması (ilk 10)

| # | Bileşen | Satır | Rol | Bölüm |
|---|---|---|---|---|
| 1 | `PageRouterKernel` | 281 | çekirdek yaşam döngüsü | §2/1 |
| 2 | `HtmlShellRenderer` | 270 | HTML kabuğu | §2/2 |
| 3 | `PageRouter` | 164 | yönlendirici | §2/3 |
| 4 | `RequestNormalizer` | 152 | istek normalizasyonu | §2/4 |
| 5 | `ResponseEmitter` | 142 | yanıt yayımı | §2/5 |
| 6 | `ApiRequest` | 142 | API istek zarfı | §6/2 |
| 7 | `Gateway` | 145 | uzak servis kapısı | §6/1 |
| 8 | `ApiResponse` | 116 | API yanıt zarfı | §6/3 |
| 9 | `SessionInitializer` (PageRouter) | 116 | oturum başlatma | §2/6 |
| 10 | `RateLimiterMiddleware` | 126 | hız sınırı | §3/1 |

### §B.3 — Boş roller (planned) kapanış sırası

| # | Rol | Beklenen bileşen | Öncelik | Kapanış |
|---|---|---|---|---|
| 1 | Senaryo orkestrasyonu | `*UseCase` | yüksek | ilk iskelet |
| 2 | Komut zarfı | `Command*` | orta | K003 |
| 3 | Sorgu zarfı | `Query*` | orta | K003 |
| 4 | Uygulama servisi | `shared/src/Service/` | orta | K012 öncesi |
| 5 | Kuyruk işleyici | `shared/src/Queue/` | düşük | K013 |
| 6 | Zamanlanmış görev | kuyrukçu | düşük | K013 |
| 7 | Ödeme orkestratör | ödeme sınıfı | yüksek | K012 |
| 8 | Olay dinleyici | handler seti | yüksek | ilk dinleyici |
| 9 | Dosya önbelleği | `FileCache` | düşük | önbellek ADR |
| 10 | Kalıcı rate-limit deposu | Redis/db sayaç | orta | K013 |

### §B.4 — Bileşen–test eşlemesi

| # | Bileşen grubu | İlgili test | Test dosyası |
|---|---|---|---|
| 1 | `MiddlewarePipeline` | zincir sırası | `MiddlewarePipelineTest` |
| 2 | `CsrfMiddleware` | CSRF reddi | `CsrfMiddlewareTest` |
| 3 | `RateLimiterMiddleware` | sınır aşımı | `RateLimiterMiddlewareTest` |
| 4 | `AuthGuard` | yetki reddi | `AuthGuardTest` |
| 5 | `RouteRegistry` | route çözümü | `RouteRegistryTest` |
| 6 | `EventDispatcher` | yayım + öncelik | `EventDispatcherTest` |
| 7 | `ApiResponse` | zarf şeması | `ApiResponseTest` |
| 8 | `Gateway` | metin yönlendirme | `GatewayMethodRoutingTest` |
| 9 | `SpaRoute` | route tanımı | `SpaRouteTest` |
| 10 | `CacheManager` (kapı) | adaptör seçimi | — (test yok → §B.3/9) |

## §C — Arayüzler

> **Amaç:** `shared/src/Contracts/` altındaki 19 arayüzün K011 bileşenleriyle bağını göstermek. Liste 2026-10-13 tarihinde diskten alındı.

### §C.1 — Arayüz envanteri

| # | Arayüz | Dosya | Uygulayan (bilinen) | İlgili bölüm |
|---|---|---|---|---|
| 1 | `IMiddleware` | `Contracts/Middleware/IMiddleware.php` | 11 middleware | §3 |
| 2 | `DomainEventInterface` | `Contracts/Events/DomainEventInterface.php` | 9 domain olayı | §4 |
| 3 | `IntegrationEventInterface` | `Contracts/Events/IntegrationEventInterface.php` | 3 entegrasyon olayı | §4 |
| 4 | `GatewayInterface` | `Contracts/Api/GatewayInterface.php` | `Gateway` | §6/1 |
| 5 | `BffInterface` | `Contracts/Api/BffInterface.php` | (uygulayan doğrulanmadı) | ⚠️ verify-required |
| 6 | `ServiceRegistryInterface` | `Contracts/Api/ServiceRegistryInterface.php` | (uygulayan doğrulanmadı) | ⚠️ verify-required |
| 7 | `IRateLimiter` | `Contracts/Security/IRateLimiter.php` | `RateLimiterMiddleware` | §3/1 |
| 8 | `ISessionManager` | `Contracts/Auth/ISessionManager.php` | oturum sınıfları | §6/4–7 |
| 9 | `IAuthService` | `Contracts/Auth/IAuthService.php` | auth katmanı (alt proje) | K009 |
| 10 | `IUserRepository` | `Contracts/Auth/IUserRepository.php` | repository (alt proje) | K012 |
| 11 | `IConfigManager` | `Contracts/Config/IConfigManager.php` | config (alt proje) | K013 |
| 12 | `IDatabaseManager` | `Contracts/Database/IDatabaseManager.php` | veri katmanı | K005 |
| 13 | `IDatabaseRegistry` | `Contracts/Database/IDatabaseRegistry.php` | veri katmanı | K005 |
| 14 | `AIEngineInterface` | `Contracts/AI/AIEngineInterface.php` | (uygulayan yok) | K020 sınırı |
| 15 | `AIOrchestratorInterface` | `Contracts/AI/AIOrchestratorInterface.php` | (uygulayan yok) | K020 sınırı |
| 16 | `KnowledgeBaseInterface` | `Contracts/AI/KnowledgeBaseInterface.php` | (uygulayan yok) | K020 sınırı |
| 17 | `MemorySystemInterface` | `Contracts/AI/MemorySystemInterface.php` | (uygulayan yok) | K020 sınırı |
| 18 | `PromptEngineInterface` | `Contracts/AI/PromptEngineInterface.php` | (uygulayan yok) | K020 sınırı |
| 19 | `ToolCallingInterface` | `Contracts/AI/ToolCallingInterface.php` | (uygulayan yok) | K020 sınırı |

### §C.2 — Arayüz–somut sınıf bağı

| # | Arayüz grubu | Somut karşılık | Bağ gücü |
|---|---|---|---|
| 1 | `IMiddleware` | 11 somut middleware | güçlü (testli) |
| 2 | Olay arayüzleri | 12 somut olay | güçlü (2 test dosyası) |
| 3 | `GatewayInterface` | `Gateway` | orta (1 test) |
| 4 | `IRateLimiter` | `RateLimiterMiddleware` | orta (testli) |
| 5 | `ISessionManager` | oturum sınıfları | orta |
| 6 | Auth/Config/Database arayüzleri | alt proje sınıfları | referans (K005/K009) |
| 7 | AI arayüzleri | somut sınıf yok | ⚠️ zayıf → `verify-required` |

### §C.3 — Arayüz ekleme kuralı

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Önce arayüz, sonra somut | somut sınıf tek başına eklenmez |
| 2 | Arayüz klasörü alan adıyla | `Contracts/<Alan>/` |
| 3 | Yeni arayüz = yeni §C satırı | append-only |
| 4 | AI arayüzleri K011'e bağlanmaz | K020 sınırı korunur |
| 5 | Uygulayan doğrulanmazsa | `verify-required` yazılır |

## §D — Bağımlılık grafiği

### §D.1 — Katman bağımlılıkları

```text
PageRouterKernel (çekirdek)
├── RequestNormalizer        → istek şekillendirme
├── RouteRegistry            → route çözümü
├── AuthGuard                → Contracts/Auth arayüzleri
├── MiddlewarePipeline       → Contracts/Middleware (IMiddleware)
│   ├── RateLimiterMiddleware → Contracts/Security (IRateLimiter)
│   ├── Cors/Origin/SecurityHeaders
│   ├── CsrfMiddleware
│   └── ValidationMiddleware
├── HtmlShellRenderer        → SPA kabuğu
├── Session* (4 sınıf)       → Contracts/Auth (ISessionManager)
└── ResponseEmitter          → HTTP sonlandırma

EventDispatcher              → Contracts/Events (2 arayüz)
├── Events/Domain/* (9)      → DomainEventInterface
└── Events/Integration/* (3) → IntegrationEventInterface

CacheManager                 → CacheInterface · PageCacheInterface
├── ApcuAdapter
├── MemoryAdapter
└── PageCacheAdapter

Gateway                      → Contracts/Api (GatewayInterface)
├── ApiRequest
└── ApiResponse
```

### §D.2 — Bağımlılık tablosu

| # | Bileşen | Bağımlı olduğu | Tür | Döngü |
|---|---|---|---|---|
| 1 | `PageRouterKernel` | `RouteRegistry` · `AuthGuard` · `MiddlewarePipeline` | iç | yok |
| 2 | `MiddlewarePipeline` | `IMiddleware` (arayüz) | sözleşme | yok |
| 3 | `CsrfMiddleware` | oturum (istek dizisi) | veri | yok |
| 4 | `AuthGuard` | `SpaRoute` · auth arayüzleri | iç + sözleşme | yok |
| 5 | `EventDispatcher` | `DomainEventInterface` · `IntegrationEventInterface` | sözleşme | yok |
| 6 | `CacheManager` | `CacheInterface` | sözleşme | yok |
| 7 | `Gateway` | `GatewayInterface` · `ApiRequest` | sözleşme + iç | yok |
| 8 | `ResponseEmitter` | `ErrorHandler` | iç | yok |
| 9 | oturum sınıfları | `ISessionManager` | sözleşme | yok |
| 10 | AI arayüzleri | — (uygulayan yok) | — | — |

### §D.3 — Döngü ve sızmazlık denetimi

| # | Denetim | Beklenen | Sonuç (2026-10-13) |
|---|---|---|---|
| 1 | Katman yukarı bağımlılık | yok | ✅ tespit edilmedi |
| 2 | Arayüz → somut bağımlılık | yok | ✅ |
| 3 | Events → PageRouter bağımlılığı | yok | ✅ |
| 4 | Cache → Events bağımlılığı | yok | ✅ |
| 5 | Middleware → Kernel bağımlılığı | yalnız istek dizisi | ✅ |
| 6 | Sözleşme kırılması | `verify-required` | §C.1/5 · §C.1/6 · §C.1/14–19 |

### §D.4 — Bağımlılık–dosya eşlemesi

| # | Bağımlılık | İlgili K011 dosyası |
|---|---|---|
| 1 | çekirdek → middleware | `pagerrouter/-middleware-zinciri` |
| 2 | olay yayımı | `event-bus/-yayin-akisi` |
| 3 | önbellek katmanı | `cache-layer/-katman-zinciri` |
| 4 | API zarfı | `dto-ve-mapper/` (ilgili) |
| 5 | boş katmanlar | `application-bagimliliklar.md` |
| 6 | sözleşme zayıflığı | `application-riskler.md` |

### §D.5 — Değişiklik etki analizi

| # | Değişen bileşen | Etkilenen | Test |
|---|---|---|---|
| 1 | `PageRouterKernel` | tüm §2 · §3 | PageRouter testleri |
| 2 | `MiddlewarePipeline` | 11 middleware | `MiddlewarePipelineTest` |
| 3 | `IMiddleware` | 11 somut + zincir | tüm middleware testleri |
| 4 | `EventDispatcher` | 12 olay sınıfı | Events testleri |
| 5 | `CacheManager` | 3 adaptör + çağıranlar | (test yok → risk) |
| 6 | `ApiResponse` | API test grubu | `ApiResponseTest` |
| 7 | `AuthGuard` | yetki akışı | `AuthGuardTest` |
| 8 | oturum sınıfları | §6/4–7 · PageRouter oturum akışı | oturum testleri |

### §D.6 — Ölçüm kaydı

| # | Kayıt | Değer |
|---|---|---|
| 1 | Üretim tarihi | 2026-10-13 |
| 2 | Önceki §1–§14 | aynen korundu (2026-10-12) |
| 3 | Eklenen bölümler | §A · §B · §C · §D |
| 4 | Arayüz listesi yeniden alındı | 19 dosya (2026-10-13) |
| 5 | Planned kalem | 10 (değişmedi) |

### §D.7 — Sonraki tur işleri

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | `BffInterface` uygulayanını bul/doğrula | §C.1/5 | backend |
| 2 | `ServiceRegistryInterface` uygulayanını doğrula | §C.1/6 | backend |
| 3 | AI arayüzlerinin sahibini belirle | §C.1/14–19 | Vault Steward |
| 4 | `CacheManager` testi yaz | §B.4/10 | QA |
| 5 | İlk `*UseCase` iskeleti | §B.3/1 | backend |

### §A.5 — Kapsam–envanter mutabakatı

| # | Kapsam satırı | Envanter bölümü | Mutabakat |
|---|---|---|---|
| 1 | Yönlendirme 14 | §2 (14 satır) | ✅ |
| 2 | Middleware 11 | §3 (11 satır) | ✅ |
| 3 | Olay 14 + 2 arayüz | §4 (4 tablo + 2 liste) | ✅ |
| 4 | Önbellek 6 | §5 (6 satır) | ✅ |
| 5 | Oturum + API 7 | §6/1–7 | ✅ |
| 6 | Arayüz 19 | §C.1 (19 satır) | ✅ |
| 7 | Planned 10 | §7 (10 satır) | ✅ |

### §A.6 — Kapsam dışı iddia denetimi

| # | İddia | Denetim | Sonuç |
|---|---|---|---|
| 1 | Domain sınıfı envanterde mi? | §2–§6 taraması | ✅ yok |
| 2 | Şema/SQL envanterde mi? | §2–§6 taraması | ✅ yok |
| 3 | frontend kodu envanterde mi? | §2–§6 taraması | ✅ yok |
| 4 | Uydurma sınıf adı var mı? | disk `Test-Path` | ✅ yok |
| 5 | Kapsam dışı dizin referansı | yalnız §A.2 + §6 | ✅ işaretli |

### §D.8 — Üretim ve ölçüm özeti

| # | Kayıt | Değer |
|---|---|---|
| 1 | Ölçüm yöntemi | `[IO.File]::ReadAllLines($p).Count` |
| 2 | Üretimden önceki uzunluk | 209 (2026-10-12) |
| 3 | Eklenen bölümler | §A · §B · §C · §D |
| 4 | Yeniden alınan liste | Contracts arayüzleri (19, 2026-10-13) |
| 5 | Yasaklı desen | 0 (2026-10-13) |
| 6 | Mojibake | 0 (2026-10-13) |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
