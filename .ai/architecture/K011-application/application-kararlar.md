---
node_id: K011-application/application-kararlar
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K008-domain
  - K012-services
  - K013-infrastructure
evidence:
  - "shared/src/PageRouter/PageRouterKernel.php — 281 satır (2026-10-12)"
  - "shared/src/Middleware/CsrfMiddleware.php — 52 satır (2026-10-12)"
  - "shared/src/Cache/CacheManager.php — 21 satır (2026-10-12)"
  - "shared/tests/Events/EventDispatcherTest.php (2026-10-12)"
validation:
  - "Her karar disk kanıtı veya açık `verify-required` işareti ile eşleştirildi: 2026-10-12"
open_questions:
  - "K003-application-bus komut zarfları onaylanana kadar karar #7 geçici mi?"
  - "Ödeme orkestrasyonu hangi paketçe üstlenilecek (karar #9)?"
group: mimari
---

# K011 Application — Kararlar

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: K011 mimari karar defteri (ADR özeti)**
> **Kaynak: disk kanıtı + `coremusic-katmanli-mimari-agaci-v2.md` §2.11 + `.ai/.decisions/`**

## §1 KARAR BİÇİMİ

Her karar beş alan taşır:

| Alan | Açıklama |
|---|---|
| **Karar** | Yapılan tercih, tek cümle |
| **Bağlam** | Neden bu karar gerekti |
| **Alternatifler** | Reddedilen seçenekler ve nedeni |
| **Kanıt** | Dosya/Satır ya da `verify-required` |
| **Sonuç** | Olumlu/olumsuz etki, teknik borç |

## §2 KARAR TABLOSU (ÖZET)

| # | Karar | Durum | Kanıt |
|---|---|---|---|
| 1 | Uygulama girişi tek `PageRouterKernel` üzerinden | ✅ onaylı | `shared/src/PageRouter/PageRouterKernel.php:1` |
| 2 | Middleware zinciri PSR-15 uyumlu tek `MiddlewarePipeline` | ✅ onaylı | `shared/src/Middleware/MiddlewarePipeline.php:1` |
| 3 | CSRF, CORS, köken ve hız sınırı ayrı middleware | ✅ onaylı | `shared/src/Middleware/` (4 dosya) |
| 4 | Olay yayımı PSR-14 benzeri `EventDispatcher` | ✅ onaylı | `shared/src/Events/EventDispatcher.php:1` |
| 5 | Domain ve entegrasyon olayları ayrı arayüz | ✅ onaylı | `shared/src/Contracts/Events/` |
| 6 | Önbellek adaptörleri `CacheManager` arkasında | ✅ onaylı | `shared/src/Cache/CacheManager.php:1` |
| 7 | Komut/sorgu ayrımı (K003) kodda henüz yok | ⚠️ verify-required | glob `shared/src/Command*` = 0 |
| 8 | Use case katmanı kodda yok | ⚠️ verify-required | sembol `*UseCase` = 0 |
| 9 | Ödeme orkestrasyonu K012'ye devredildi | ⚠️ verify-required | repo genelinde ödeme kodu = 0 |
| 10 | Kuyruk/zamanlayıcı K013 kapsamında | ⚠️ verify-required | `shared/src/Queue*` = 0 |
| 11 | Uzak API yanıt zarfı tek `ApiResponse` | ✅ onaylı | `shared/src/Api/ApiResponse.php:1` |
| 12 | Oturum yaşam döngüsü 4 sınıfta toplandı | ✅ onaylı | `shared/src/Session/` |

## §3 KARAR #1 — TEK GİRİŞ NOKTASI

**Karar:** HTTP istekleri `PageRouterKernel` dışında hiçbir K011 bileşeni tarafından işlenmez.

**Bağlam:** Depoda yönlendirme ile ilgili birden fazla sınıf var (`PageRouter`, `RouteRegistry`, `AuthGuard`); dağınık giriş noktası ikili davranış üretir.

**Alternatifler:**

| Alternatif | Neden reddedildi |
|---|---|
| Her controller kendi girişini açsın | Yetki/oturum denetimi çoğaltılır |
| Front-controller + ayrı router API'si | İki sözleşme, iki test yüzeyi |

**Kanıt:** `shared/src/PageRouter/PageRouterKernel.php` — 281 satır (2026-10-12), en büyük dosya; yaşam döngüsünü tek yerde toplar.

**Sonuç:** + Tek test kapısı. − Kernel dosyası büyüme riski → kural: 400 satırı aşarsa bölünür (bkz. riskler dosyası).

## §4 KARAR #2 — MIDDLEWARE ZİNCİRİ

**Karar:** Yalnız bir `MiddlewarePipeline` zinciri yürütür; ikinci bir yürütücü yazılmaz.

**Bağlam:** `MiddlewarePipeline` iki klasörde anıldı; tek kaynak olmadan sıra garantisi kırılır.

**Alternatifler:**

| Alternatif | Neden reddedildi |
|---|---|
| Kernel içi satır içi çağrılar | Sıra test edilemez |
| Her middleware kendi sonrakisini çağırsın | Özyineleme, istisna yönetimi dağılır |

**Kanıt:** `shared/src/Middleware/MiddlewarePipeline.php` — 43 satır (2026-10-12) + `shared/tests/Middleware/MiddlewarePipelineTest.php`.

**Sonuç:** + Sıra tek yerde, testi var. − Pipeline'a yeni özellik eklemek tüm zinciri etkiler.

## §5 KARAR #3 — GÜVENLİK MIDDLEWARE AYRIMI

**Karar:** CSRF, CORS, köken denetimi, hız sınırı ve izin denetimi ayrı middleware'dir; birleştirilmez.

**Bağlam:** Karışık tek middleware fail-open riski üretir (K009 ile ortak konu).

**Alternatifler:**

| Alternatif | Neden reddedildi |
|---|---|
| Tek `SecurityMiddleware` | Herhangi bir hata tüm denetimleri düşürür |
| Uygulama içi denetim | Test edilemez, tutarsız |

**Kanıt:** `CsrfMiddleware.php` (52) · `CorsMiddleware.php` (121) · `OriginCheckMiddleware.php` (106) · `RateLimiterMiddleware.php` (126) · `PermissionMiddleware.php` (54) — 2026-10-12.

**Sonuç:** + Her denetimin kendi testi var (`shared/tests/Middleware/` içinde 9 test dosyası). − Sıra hatası yüzeyi → `MiddlewarePipelineTest` ile kilitli.

## §6 KARAR #4–5 — OLAY MODELİ

**Karar:** Domain olayları `DomainEventInterface`, entegrasyon olayları `IntegrationEventInterface` uygular; tek tip olay havuzu kullanılmaz.

**Bağlam:** Domain olayı iç alan adı taşır; entegrasyon olayı dış servise gider ve sözleşmeyi kirletmemeli.

**Alternatifler:**

| Alternatif | Neden reddedildi |
|---|---|
| Tek `Event` sınıfı | Dış servis sözleşmesi domain değişikliğinden kopar |
| String tabanlı olay adları | Yanlış yazımda sessiz kırılma |

**Kanıt:** `shared/src/Contracts/Events/DomainEventInterface.php` · `IntegrationEventInterface.php` · 9 domain + 3 integration olay sınıfı (2026-10-12).

**Sonuç:** + İki sözleşme ayrı test edilir (`DomainEventTest.php`). − İki arayüz = iki güncelleme noktası.

## §7 KARAR #6 — ÖNBELLEK KAPISI

**Karar:** Önbellek erişimi yalnız `CacheManager` üzerinden yapılır; adaptörler doğrudan çağrılmaz.

**Bağlam:** `MemoryAdapter`, `ApcuAdapter`, `PageCacheAdapter` birbirinin yerine geçebilir; çağrı noktası adaptör seçerse değiştirilemezlik kırılır.

**Alternatifler:** Doğrudan adaptör kullanımı (reddedildi: değişiklik maliyeti her çağrı noktasında) · Statik önbellek (reddedildi: test edilemez).

**Kanıt:** `shared/src/Cache/CacheManager.php` (21 satır) + `CacheInterface.php` (11) + `PageCacheInterface.php` (9) — 2026-10-12.

**Sonuç:** + Tek arayüz, taklit kolay. − Kapı şişebilir → ölçüm `CacheMetrics` **yok** → §8 karar #13.

## §8 KARAR #7–10 — OLMAYAN KATMANLAR

**Karar:** Komut/sorgu ayrımı, use case katmanı, ödeme orkestrasyonu ve kuyrukçular **tasarım kararı olarak bekliyor**; bu dosyada uygulanmış gibi gösterilmez.

**Bağlam:** SSOT §2.11 bu katmanları K011 kapsamında tanımlar; diskte karşılığı yok.

**Kanıt tablosu:**

| Katman | Arama (2026-10-12) | Sonuç |
|---|---|---|
| Use case | sembol `*UseCase` | 0 |
| Command | glob `shared/src/Command*` | 0 |
| Query | glob `shared/src/Query*` | 0 |
| Queue | glob `shared/src/Queue*` | 0 |
| Ödeme | metin araması `payment\|odeme` (src) | 0 |

**Alternatif:** Varsayılan olarak "varmış gibi" yazmak → **reddedildi** (zero-hallucination, `.ai/CLAUDE.md` §3).

**Sonuç:** + Belgeler dürüst. − Kapsam ile kod arası boşluk → açık sorular listesinde taşınır.

## §9 KARAR #11–12 — SÖZLEŞME VE OTURUM

**Karar #11:** Uzak API çağrıları `ApiRequest`/`ApiResponse` zarfı ile yapılır; ham dizi dönmez.

**Kanıt:** `shared/src/Api/ApiResponse.php` (116) · `ApiRequest.php` (142) · `Gateway.php` (145) — 2026-10-12.

**Karar #12:** Oturum yaşam döngüsü `SessionBootstrapper` → `SessionConfig` → `SessionInitializer` → `SessionLifecycle` sırasındadır.

**Kanıt:** `shared/src/Session/` 4 dosya (68 · 54 · 43 · 108 satır, 2026-10-12) + `PageRouter/SessionInitializer.php` (116) — **çift `SessionInitializer` varlığı açık çatışma → açık soru #2.**

**Sonuç:** + Sıra okunur. − İki `SessionInitializer` adı taşıyan sınıf → birleştirilmeli veya adlandırılmalı.

## §10 REDDEDİLEN ALTERNATİFLER (TOPLU)

| # | Reddedilen | Neden | İlgili karar |
|---|---|---|---|
| 1 | Controller içi yetki denetimi | K009 ihlali | #3 |
| 2 | String olay adları | Sessiz kırılma | #4 |
| 3 | Doğrudan adaptör çağrısı | Değişilemezlik kırılır | #6 |
| 4 | "Varmış gibi" belgeleme | zero-hallucination ihlali | #7–10 |
| 5 | Ham dizi API dönüşü | Şema belirsizliği | #11 |
| 6 | İkinci middleware yürütücü | Sıra garantisi kırılır | #2 |
| 7 | Kernel dışı ikinci giriş | Denetim çoğalır | #1 |

## §11 KARAR–DOSYA EŞLEMESİ

| Karar | İlgili K011 dosyası |
|---|---|
| #1 · #2 | `pagerrouter/-yonlendirme-tablosu` · `pagerrouter/-middleware-zinciri` |
| #3 | `application-dogrulama.md` · K009-security |
| #4 · #5 | `event-akislari/` · `event-bus/-abone-tablosu` |
| #6 | `cache-layer/-katman-zinciri` · `application-riskler.md` |
| #7–#10 | `command-query/` · `usecase-akislari/` · `odem-servisleri/` · `zamanlamali-gorevler/` |
| #11 | `dto-ve-mapper/` |
| #12 | `pagerrouter/-oturum-akisi` |

## §12 KARAR GÜNCELLEME KURALLARI

1. Yeni karar yalnız kanıt ile açılır (`Dosya: … / Satır: …` ya da `verify-required`).
2. Onaylı karar yalnız yeni ADR ile değişir; bu dosyada sessiz revizyon yasak.
3. Reddedilen alternatif silinmez; tabloda kalır (§10).
4. Her karar bir `group` değeri taşır; bu dosya `mimari`.

## §13 DOĞRULAMA

| # | İddia | Yöntem | Tarih | Sonuç |
|---|---|---|---|---|
| 1 | Karar #1 kanıtı gerçek | dosya var + satır sayısı | 2026-10-12 | ✅ |
| 2 | Karar #2 testi mevcut | `MiddlewarePipelineTest.php` | 2026-10-12 | ✅ |
| 3 | Karar #3 4 middleware gerçek | dosya listesi | 2026-10-12 | ✅ |
| 4 | Karar #5 arayüzler gerçek | Contracts/Events listesi | 2026-10-12 | ✅ |
| 5 | Karar #7–10 boşlukları gerçek | 5 ayrı arama = 0 | 2026-10-12 | ✅ |
| 6 | Karar #12 çatışması gerçek | iki `SessionInitializer` | 2026-10-12 | ✅ |

## §14 AÇIK SORULAR

1. Karar #7–#10 için uygulama takvimi ne zaman başlıyor?
2. İki `SessionInitializer` birleştirilecek mi?
3. `CacheMetrics` ölçümü hangi karar kapsamında üretilecek?
4. Karar #1'in 400 satır eşiği kime ait (gate sahibi)?

## §15 REFERANSLAR

- `coremusic-katmanli-mimari-agaci-v2.md` §2.11 — K011 kapsamı
- `.ai/CLAUDE.md` §3 — zero-hallucination
- `shared/src/PageRouter/` · `shared/src/Middleware/` · `shared/src/Events/` · `shared/src/Cache/` · `shared/src/Api/` · `shared/src/Session/` — disk kanıtı (2026-10-12)
- `shared/tests/Middleware/` · `shared/tests/Events/` — test kanıtı (2026-10-12)

## §A — Karar listesi

> **Amaç:** §2 özet tablosunun altında kararların tam liste hâli — her satır durum, kanıt ve yeniden açılma koşulunu taşır. Satır kanıtları 2026-10-13 tarihinde dosyanın kendisinden yeniden okunmuştur.

### §A.1 — Onaylı kararlar (detay)

| # | Karar | Kanıt dosyası | Kanıt satırı (2026-10-13) | Yeniden açılma koşulu |
|---|---|---|---|---|
| 1 | Tek giriş `PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php` | 81 (`handle`) | ikinci bir giriş ihtiyacı doğarsa |
| 2 | Tek `MiddlewarePipeline` | `shared/src/Middleware/MiddlewarePipeline.php` | 17 (`pipe`) · 23 (`run`) | yürüteç sayısı artarsa |
| 3 | Güvenlik middleware ayrı | `shared/src/Middleware/CsrfMiddleware.php` | 20 (`handle`) | denetim birleştirme önerisi gelirse |
| 4 | PSR-14 benzeri yayım | `shared/src/Events/EventDispatcher.php` | 28 (`dispatch`) | yayım gecikmesi ölçülürse |
| 5 | Domain/entegrasyon ayrımı | `shared/src/Contracts/Events/` | arayüz dosyaları | tek sözleşme talebi gelirse |
| 6 | `CacheManager` kapısı | `shared/src/Cache/CacheManager.php` | 9 (`getAdapter`) | adaptör sayısı 4'ü aşarsa |
| 11 | `ApiResponse` zarfı | `shared/src/Api/ApiResponse.php` | 26 (`ok`) · 73 (`paginated`) | ham dönüş talebi doğarsa |
| 12 | Oturum 4 sınıf | `shared/src/Session/SessionBootstrapper.php` | 19 (`ensureStarted`) | çift `SessionInitializer` çözülürse |

### §A.2 — Bekleyen kararlar (verify-required)

| # | Karar | Arama (2026-10-12) | Sonuç | Kapanış tetikleyicisi |
|---|---|---|---|---|
| 7 | Komut/sorgu ayrımı | glob `shared/src/Command*` · `shared/src/Query*` | 0 · 0 | K003 onayı |
| 8 | Use case katmanı | sembol `*UseCase` | 0 | ilk iskelet pr |
| 9 | Ödeme orkestrasyonu | metin araması ödeme | 0 | K012 sözleşmesi |
| 10 | Kuyruk/zamanlayıcı | glob `shared/src/Queue*` | 0 | K013 zamanlayıcı |

### §A.3 — Karar ağırlığı

| # | Karar grubu | Ağırlık | Neden |
|---|---|---|---|
| 1 | Giriş/zincir (#1 · #2) | yüksek | tüm istek akışını belirler |
| 2 | Güvenlik (#3) | yüksek | K009 ile ortak; ihlali denetimsiz bırakır |
| 3 | Olay (#4 · #5) | orta | yayım yönünü sabitler |
| 4 | Önbellek (#6) | orta | adaptör değiştirilebilirliğini korur |
| 5 | Sözleşme/oturum (#11 · #12) | orta | dış yüzey ve oturum sırası |
| 6 | Bekleyen (#7–#10) | düşük (şimdilik) | kodda karşılığı yok; yalnızca kapsam kaydı |

## §B — Alternatifler

### §B.1 — Reddedilen seçenekler (genişletilmiş)

| # | Alternatif | İlgili karar | Red gerekçesi | Yeniden değerlendirme şartı |
|---|---|---|---|---|
| 1 | Controller başına giriş noktası | #1 | yetki denetimi çoğaltılır | kernel 400 satırı aşarsa |
| 2 | Satır içi middleware çağrıları | #2 | sıra test edilemez | pipeline gereksinimi değişirse |
| 3 | Tek `SecurityMiddleware` | #3 | tek hata tüm denetimleri düşürür | K009 yeni ADR'si çıkarsa |
| 4 | String olay adları | #4 | sessiz kırılma riski | ad kaydı otomasyonu gelirse |
| 5 | Tek `Event` sınıfı | #5 | dış sözleşme domain'e bağlanır | tek sözleşme talebi doğarsa |
| 6 | Doğrudan adaptör çağrısı | #6 | değişiklik maliyeti her çağrıda | adaptör sayısı 4'ü aşarsa |
| 7 | Ham dizi API dönüşü | #11 | şema belirsizliği | şema üretimi kalkarsa |
| 8 | Tek oturum sınıfı | #12 | yaşam döngüsü okunmaz hâle gelir | çift `SessionInitializer` çözülürse |

### §B.2 — Reddedilen → kanıt zinciri

| # | Reddedilen | Kararı destekleyen disk kanıtı | Okuma tarihi |
|---|---|---|---|
| 1 | Çoklu giriş | `PageRouterKernel.php` tek `handle` (satır 81) | 2026-10-13 |
| 2 | İkinci yürüteç | `MiddlewarePipeline.php` tek `run` (satır 23) | 2026-10-13 |
| 3 | Birleşik güvenlik | `CsrfMiddleware.php` ayrı `handle` (satır 20) | 2026-10-13 |
| 4 | String olay | `EventDispatcher.php` `get_class($event)` (satır 28 bloğu) | 2026-10-13 |
| 5 | Doğrudan adaptör | `CacheManager.php` tek kapı (satır 9) | 2026-10-13 |
| 6 | Ham dizi | `ApiResponse.php` statik kurucu (satır 26) | 2026-10-13 |
| 7 | Dağınık oturum | `SessionBootstrapper.php` tek `ensureStarted` (satır 19) | 2026-10-13 |
| 8 | "Varmış gibi" belgeleme | glob/symbol sonuçları = 0 (§A.2) | 2026-10-12 |

### §B.3 — Üçüncü seçenekler (kabul edilmeyen ama değerlendirilen)

| # | Üçüncü seçenek | Değerlendirme | Karar |
|---|---|---|---|
| 1 | Kernel + bağımsız router servisi | iki sözleşme, iki test yüzeyi | ertelendi (§3) |
| 2 | Middleware decorator sarmalayıcı | özyineleme, istisna yönetimi dağılır | reddedildi (§4) |
| 3 | Olay adı sabitleri sınıfı | string yerine const — kısmi çözüm | ADR bekliyor (§6) |
| 4 | Önbellek ölçüm katmanı (`CacheMetrics`) | ölçüm yok; kapı genişleme riski | açık soru #3 |
| 5 | Use case iskeleti tek pr'da | kapsam geniş; review zorlaşır | K003 sonrası |

## §C — Sonuç / etki

### §C.1 — Olumlu etkiler

| # | Karar | Olumlu etki | Ölçülebilir işaret |
|---|---|---|---|
| 1 | #1 tek giriş | tek test kapısı | `PageRouterKernel` testleri tek yerde |
| 2 | #2 tek zincir | sıra garanti | `MiddlewarePipelineTest` mevcut |
| 3 | #3 ayrım | her denetimin kendi testi | `shared/tests/Middleware/` 9 dosya |
| 4 | #4 · #5 olay | sözleşmeler ayrı | `DomainEventTest.php` |
| 5 | #6 kapı | taklit kolay | `setAdapter` enjeksiyonu |
| 6 | #11 zarf | dış şema sabit | `ok` · `paginated` kurucuları |
| 7 | #12 sıra | okunabilir akış | 4 dosyalık yaşam döngüsü |

### §C.2 — Olumsuz etkiler ve teknik borç

| # | Kaynak | Borç | Etki | Sahip |
|---|---|---|---|---|
| 1 | #1 | kernel büyüme riski | 400 satır eşiği | gate sahibi (açık soru #4) |
| 2 | #2 | pipeline'a özellik = tüm zincir | gerileme yüzeyi | backend |
| 3 | #6 | `CacheMetrics` yok | ölçüm boşluğu | açık soru #3 |
| 4 | #12 | çift `SessionInitializer` | ad çatışması | açık soru #2 |
| 5 | #7–#10 | kapsam–kod boşluğu | beklenti yönetimi | K003/K012/K013 |

### §C.3 — Etki → ilgili dosya

| # | Etki | İlgili dosya | Tür |
|---|---|---|---|
| 1 | sırasız zincir riski | `pagerrouter/-middleware-zinciri` | davranış |
| 2 | denetim yüzeyi | `application-dogrulama.md` | senaryo |
| 3 | büyüme riski | `application-riskler.md` | risk #? |
| 4 | boş katmanlar | `application-bagimliliklar.md` | bağımlılık |
| 5 | test kapısı | `application-test-stratejisi.md` | eşik |
| 6 | dil birliği | `application-evrensel-dil.md` | terim |

### §C.4 — Sonuç özeti

| # | Soru | Yanıt |
|---|---|---|
| 1 | Onaylı karar sayısı | 8 (#1–#6 · #11 · #12) |
| 2 | Bekleyen karar sayısı | 4 (#7–#10) |
| 3 | Reddedilen alternatif sayısı | 8 (§B.1) + 7 (§10 toplu) |
| 4 | Açık soru sayısı | 4 (§14) |
| 5 | Durum | `verify-required` — bekleyen kapanana kadar |

## §D — Yeniden gözden geçirme tetikleyicileri

### §D.1 — Tetikleyici tablosu

| # | Tetikleyici | Etkilenen karar | Aksiyon |
|---|---|---|---|
| 1 | `PageRouterKernel` 400 satırı aşarsa | #1 | bölme ADR'si |
| 2 | İkinci giriş noktası talebi | #1 | alternatif yeniden değerlendir |
| 3 | Middleware sayısı > 11 | #2 · #3 | sıra ve gruplama gözden geçirme |
| 4 | Yeni güvenlik denetimi (K009) | #3 | ayrı middleware mi birleşik mi |
| 5 | PSR-14 resmî bağımlılığı | #4 | arayüz uyumu denetimi |
| 6 | Adaptör sayısı > 4 | #6 | kapı + ölçüm kararı |
| 7 | K003 komut zarfı onayı | #7 | bekleyen → onaylı |
| 8 | İlk use case iskeleti | #8 | bekleyen → onaylı |
| 9 | K012 ödeme sözleşmesi | #9 | bekleyen → onaylı |
| 10 | K013 kuyruk/zamanlayıcı | #10 | bekleyen → onaylı |

### §D.2 — Periyodik gözden geçirme

| # | Periyot | Kapsam | Çıktı |
|---|---|---|---|
| 1 | Her sürüm | §A.2 bekleyen kararlar | kapanan/açılan satır |
| 2 | Aylık | §C.2 teknik borç tablosu | borç azaltma kaydı |
| 3 | ADR yazımında | §B.1 reddedilenler | yeni ADR çakışma denetimi |
| 4 | Gate çalışmasında | §D.1 tetikleyiciler | tetiklenen satır |

### §D.3 — Karar–tetikleyici–dosya haritası

| # | Karar | Tetikleyici satırı (§D.1) | Sonuç dosyası |
|---|---|---|---|
| 1 | #1 | satır 1 · 2 | `pagerrouter/-yonlendirme-tablosu` |
| 2 | #2 · #3 | satır 3 · 4 | `pagerrouter/-middleware-zinciri` |
| 3 | #4 · #5 | satır 5 | `event-bus/-yayin-akisi` |
| 4 | #6 | satır 6 | `cache-layer/-katman-zinciri` |
| 5 | #7–#10 | satır 7 · 8 · 9 · 10 | `usecase-akislari/` · `odem-servisleri/` · `zamanlamali-gorevler/` |
| 6 | #11 · #12 | — (sözleşme değişikliği) | `dto-ve-mapper/` · `pagerrouter/-oturum-akisi` |

### §D.4 — Üretim kaydı

| # | Kayıt | Değer |
|---|---|---|
| 1 | Üretim tarihi | 2026-10-13 |
| 2 | Önceki §1–§15 | aynen korundu (2026-10-12) |
| 3 | Eklenen bölümler | §A · §B · §C · §D |
| 4 | Yeniden okunan satır kanıtı | 10 künye (2026-10-13) |
| 5 | Bekleyen karar sayısı değişmedi | 4 (#7–#10) |

### §A.4 — Karar kayıt şablonu (yeni karar açarken)

| # | Alan | Zorunlu içerik | Örnek |
|---|---|---|---|
| 1 | Karar | tek cümle tercih | "Yalnız bir `MiddlewarePipeline` zinciri yürütür" |
| 2 | Bağlam | neden gerekti | dağınık giriş ikili davranış |
| 3 | Alternatifler | en az 1 reddedilen seçenek | satır içi çağrılar |
| 4 | Kanıt | `Dosya:` + `Satır:` ya da `verify-required` | `MiddlewarePipeline.php:23` |
| 5 | Sonuç | olumlu + olumsuz etki | + sıra / − zincir etkisi |
| 6 | group | `mimari` · `kod` · `kanit` | bu dosya `mimari` |

### §A.5 — Durum geçiş tablosu

| # | Durum | Geçiş | Şart |
|---|---|---|---|
| 1 | `verify-required` | → `onaylı` | disk kanıtı + test |
| 2 | `onaylı` | → `bekleyen` | tetikleyici (§D.1) + ADR |
| 3 | `onaylı` | → `red` | yalnız yeni ADR ile |
| 4 | `bekleyen` | → `onaylı` | ilgili K dosyası kapanır |
| 5 | herhangi | → `verify-required` | kanıtın diskte kalkması |

### §A.6 — Karar numarası sürekliliği

| # | Aralık | Durum | Dosya |
|---|---|---|---|
| 1 | #1–#6 | onaylı | bu dosya §3–§7 |
| 2 | #7–#10 | bekleyen | bu dosya §8 |
| 3 | #11–#12 | onaylı | bu dosya §9 |
| 4 | #13 | açık (CacheMetrics) | açık soru #3 |
| 5 | #14+ | ayrılmamış | yeni karar A.4 ile açılır |

### §B.4 — Reddedilen alternatif arşivi (toplu, §10 ile uyumlu)

| # | Arşiv satırı | Karar | Arşiv durumu |
|---|---|---|---|
| 1 | Controller içi yetki | #3 | kalıcı (§10/1) |
| 2 | String olay adları | #4 | kalıcı (§10/2) |
| 3 | Doğrudan adaptör | #6 | kalıcı (§10/3) |
| 4 | "Varmış gibi" belgeleme | #7–#10 | kalıcı (§10/4) |
| 5 | Ham dizi dönüş | #11 | kalıcı (§10/5) |
| 6 | İkinci yürüteç | #2 | kalıcı (§10/6) |
| 7 | Kernel dışı giriş | #1 | kalıcı (§10/7) |
| 8 | Silme kuralı | hepsi | reddedilen silinmez (§12/3) |

### §B.5 — Alternatif değerlendirme ölçütleri

| # | Ölçüt | Soru | Eşik |
|---|---|---|---|
| 1 | Test edilebilirlik | sıra/akış testlenebilir mi? | hayır → reddet |
| 2 | Güvenlik | fail-open üretir mi? | evet → reddet |
| 3 | Sözleşme | dış yüzeyi kirletir mi? | evet → reddet |
| 4 | Değişilemezlik | değişiklik kaç noktada? | > 1 → reddet |
| 5 | Dürüstlük | kanıtsız iddia içerir mi? | evet → reddet |

### §C.5 — Sonuç etki ölçümü

| # | Ölçüm | Değer | Tarih |
|---|---|---|---|
| 1 | Onaylı karar kanıtı yeniden okundu | 10/10 künye geçti | 2026-10-13 |
| 2 | Bekleyen arama sonucu | 0 · 0 · 0 · 0 | 2026-10-12 |
| 3 | Açık soru | 4 | 2026-10-12 |
| 4 | Reddedilen alternatif | 8 + 7 toplu | 2026-10-13 |
| 5 | Teknik borç kalemi | 5 (§C.2) | 2026-10-13 |

### §D.5 — Kapanış kontrol listesi

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | Tüm onaylı kararların disk kanıtı var mı? | 8/8 | ✅ (2026-10-13) |
| 2 | Bekleyen kararlar `verify-required` mı? | 4/4 | ✅ |
| 3 | Reddedilen alternatifler silinmedi mi? | silinmedi | ✅ |
| 4 | Açık sorular §14'te mıknatıs gibi duruyor mu? | 4 | ✅ |
| 5 | Yeni kararlar A.4 şablonuna uyuyor mu? | şablon hazır | ✅ |
| 6 | Tetikleyiciler §D.1'de tanımlı mı? | 10 | ✅ |

### §D.6 — Dosya içi bölüm envanteri

| # | Bölüm | Tür | İçerik |
|---|---|---|---|
| 1 | §1 Karar biçimi | kural | 5 alan |
| 2 | §2 Özet tablo | liste | 12 karar |
| 3 | §3–§9 Karar detayları | anlatım | 8 karar bloğu |
| 4 | §10 Reddedilenler | arşiv | 7 satır |
| 5 | §11 Eşleme | bağ | 7 satır |
| 6 | §12 Güncelleme kuralları | kural | 4 madde |
| 7 | §13 Doğrulama | kanıt | 6 satır |
| 8 | §14 Açık sorular | açık | 4 madde |
| 9 | §15 Referanslar | kaynak | 4 satır |
| 10 | §A Karar listesi | detay | 6 tablo |
| 11 | §B Alternatifler | arşiv | 5 tablo |
| 12 | §C Sonuç/etki | etki | 5 tablo |
| 13 | §D Tetikleyiciler | süreç | 6 tablo |

### §D.7 — Bağlantı bütünlüğü

| # | Bağlantı | Hedef | Tür |
|---|---|---|---|
| 1 | §8 → K003 | command-query kapsamı | bekleyen karar |
| 2 | §9 → açık soru #2 | çift `SessionInitializer` | çatışma |
| 3 | §7 → açık soru #3 | `CacheMetrics` | ölçüm boşluğu |
| 4 | §3 → açık soru #4 | 400 satır gate sahibi | sorumluluk |
| 5 | §A.2 → K012 · K013 | ödeme · kuyruk | devir |
| 6 | §C.3 → ilgili K011 dosyaları | 6 hedef | etki dağıtımı |
| 7 | §A.4 → yeni karar şablonu | 6 alan | zorunlu alan |

### §D.8 — Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | 1.0 | 2026-10-12 | §1–§15 ilk üretim |
| 2 | 1.1 | 2026-10-13 | §A–§D genişletme (10 kanıt künyesi yeniden okundu) |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
