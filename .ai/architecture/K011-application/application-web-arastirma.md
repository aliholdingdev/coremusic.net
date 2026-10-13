---
node_id: K011-application/application-web-arastirma
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K008-domain
  - K009-security
evidence:
  - "php.net Manual — 14 URL, HTTP 200 ile doğrulandı (2026-10-12)"
  - "Kod karşılaştırmaları: shared/src/Events/EventDispatcher.php, shared/src/PageRouter/ (2026-10-12)"
validation:
  - "Tablodaki 14 URL tekildir; K011 içinde başka dosyada tekrarlanmaz (2026-10-12)"
  - "Her satır: İddia · URL · Tarih: 2026-10-12 · Durum biçimindedir"
  - "2 URL (language.control-structures.match, language.exceptions) 404 döndü; doğru sayfalarla değiştirildi"
open_questions:
  - "Enum deseni depoda yalnız 1 yerde (ApiVersion) — yaygınlaştırılacak mı?"
  - "Attribute/Fiber/Generator desenleri hiç kullanılmıyor — hangi K011 senaryosunda gerekli?"
group: kanit
---

# K011 Application — Web Araştırması

> **Authority: Bayram Ali / Vault Steward**
> **Tarih: 2026-10-12** · **Kapsam: PHP dili + uygulama deseni web kanıtları**
> **Biçim: | İddia | URL | Tarih: 2026-10-12 | Durum | — tüm URL'ler GET ile 200 döndü.**
> **Tekillik: Bu dosyadaki URL'ler başka K011 dosyasında geçmez.**

## §1 ARAŞTIRMA SORULARI

| # | Soru | Neden | Karşılığı |
|---|---|---|---|
| 1 | PHP 8.x'te sabit küme (enum) nasıl tanımlanır? | Olay/rol kümeleri için | §3/1 (depoda 1 kullanım) |
| 2 | Generator ne kazandırır? | Büyük listeleme akışları | §3/2 |
| 3 | Fiber ne zaman kullanılır? | Asenkron orkestrasyon | §3/3 |
| 4 | Attribute ile metadata nasıl taşınır? | Route/izin bildirimi | §3/4 |
| 5 | Anonymous class sınırı nedir? | Tek-kullanımlık adaptör | §3/5 |
| 6 | Interface vs abstract class seçimi? | K008/K011 sözleşmeleri | §3/6 · §3/13 |
| 7 | Trait ile kod paylaşımı güvenli mi? | Middleware davranışları | §3/7 |
| 8 | Karşılaştırmada tip dönüşümü tuzağı | Girdi doğrulama | §3/8 |
| 9 | Değişken kapsamı — statik durum tuzağı | `CacheManager` statik kapı | §3/9 |
| 10 | `match` ifadesi ne kazandırır? | Yönlendirme/analiz | §3/10 |
| 11 | İstisna hiyerarşisi nasıl kurulur? | Hata yönetimi (Result yerine) | §3/11 |
| 12 | Standart iterator'lar ne çözer? | Sayfalama akışı | §3/12 |
| 13 | Miras ve çok-katmanlı OOP kuralları | Katman sınırları | §3/13 |
| 14 | Harici değişken kullanımı ne risk taşır? | Template/renderer | §3/14 |

## §2 YÖNTEM

| # | Adım | Araç | Tarih | Not |
|---|---|---|---|---|
| 1 | URL listesi derlendi | php.net Manual | 2026-10-12 | 16 aday |
| 2 | HTTP doğrulama | GET isteği | 2026-10-12 | 14 × 200 · 2 × 404 |
| 3 | 404 düzeltmesi | doğru sayfa araması | 2026-10-12 | `control-structures.match` · `class.exception` |
| 4 | İddia–kod karşılaştırması | dosya okuma | 2026-10-12 | §4 |
| 5 | Tekillik taraması | K011 geneli URL taraması | 2026-10-12 | çakışma 0 |

## §3 WEB-KANIT TABLOSU (14 satır)

| # | İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|
| 1 | PHP enum'ları sabit, tip-güvenli küme sağlar; `match` ile birlikte kullanılır | https://www.php.net/manual/en/language.enumerations.php | 2026-10-12 | ✅ 200 OK |
| 2 | Generator'lar `yield` ile akışsal üretir; büyük diziyi belleğe almaz | https://www.php.net/manual/en/language.generators.php | 2026-10-12 | ✅ 200 OK |
| 3 | Fiber, tek-işlek stilde kooperatif çok-işlilik sağlar (PHP 8.1+) | https://www.php.net/manual/en/language.fibers.php | 2026-10-12 | ✅ 200 OK |
| 4 | Attribute'lar kod elemanlarına çalış zamanı metadata ekler (PHP 8+) | https://www.php.net/manual/en/language.attributes.php | 2026-10-12 | ✅ 200 OK |
| 5 | Anonymous class'lar tek-kullanımlık, yerinde türetme içindir | https://www.php.net/manual/en/language.oop5.anonymous.php | 2026-10-12 | ✅ 200 OK |
| 6 | Interface, sözleşme tanımıdır; birden çok sınıf aynı sözleşmeyi uygular | https://www.php.net/manual/en/language.oop5.interfaces.php | 2026-10-12 | ✅ 200 OK |
| 7 | Trait, tek-kullanım kalıtımı dışındaki davranış paylaşımının yoludur | https://www.php.net/manual/en/language.oop5.traits.php | 2026-10-12 | ✅ 200 OK |
| 8 | Karşılaştırma operatörleri tip dönüşümü yapabilir; girdi doğrulamada dikkat | https://www.php.net/manual/en/language.operators.comparison.php | 2026-10-12 | ✅ 200 OK |
| 9 | Değişken kapsamı: statik/yerel/global farklı davranır; sınıf içi statik paylaşımdır | https://www.php.net/manual/en/language.variables.scope.php | 2026-10-12 | ✅ 200 OK |
| 10 | `match` ifadesi katı eşleşme yapar ve dal yığınını azaltır (PHP 8+) | https://www.php.net/manual/en/control-structures.match.php | 2026-10-12 | ✅ 200 OK |
| 11 | Exception nesnesi taşınabilir hata bilgisidir; özel alt sınıflar kural taşır | https://www.php.net/manual/en/class.exception.php | 2026-10-12 | ✅ 200 OK |
| 12 | SPL Iterator'lar tek tipli gezinme sözleşmesi sunar | https://www.php.net/manual/en/spl.iterators.php | 2026-10-12 | ✅ 200 OK |
| 13 | Temel OOP: sınıf, nesne, `final`, constructor ve kapsülleme kuralları | https://www.php.net/manual/en/language.oop5.basic.php | 2026-10-12 | ✅ 200 OK |
| 14 | Harici değişken (`global`, `static`) kullanımı test edilebilirliği düşürür | https://www.php.net/manual/en/language.variables.external.php | 2026-10-12 | ✅ 200 OK |

**Reddedilen adaylar (404, kullanılmadı):**

| Aday URL | Sonuç | Karar |
|---|---|---|
| `.../language.control-structures.match.php` | 404 | §3/10'daki doğru sayfa kullanıldı |
| `.../language.exceptions.php` | 404 | §3/11'deki `class.exception.php` kullanıldı |

## §4 İDDİA ↔ KOD KARŞILAŞTIRMASI

| # | Web iddiası | Depo gerçeği (2026-10-12) | Uyum |
|---|---|---|---|
| 1 | Enum tip-güvenli küme | `shared/src/Api/Versioning/ApiVersion.php:17` — `enum ApiVersion: string` (1 kullanım) | ✅ kullanılıyor (yalnız 1 yer) |
| 2 | Generator akışsal üretim | `yield` = 0 kullanım | ⚠️ kullanılmıyor |
| 3 | Fiber kooperatif çok-işlilik | `Fiber` = 0 kullanım | ⚠️ kullanılmıyor |
| 4 | Attribute metadata | `#[...]` = 0 kullanım (src) | ⚠️ kullanılmıyor |
| 5 | Anonymous class | `new class` = 0 | ⚠️ kullanılmıyor |
| 6 | Interface sözleşmesi | `IMiddleware`, `CacheInterface`, `DomainEventInterface` | ✅ yoğun kullanılıyor |
| 7 | Trait paylaşımı | `StoppableEventTrait.php:17` (1 trait) | ✅ kullanılıyor (yalnız 1 adet) |
| 8 | Karşılaştırma tip dönüşümü | `CsrfMiddleware`: `hash_equals` + `===` (satır 42) | ✅ kasıtlı katı eşleşme |
| 9 | Statik paylaşım tuzağı | `CacheManager::$adapter` (satır 7) + 3 statik kapı daha | ✅ bilinçli, test riski işaretli |
| 10 | `match` dal yığını | gerçek `match` ifadeleri: `ErrorHandler.php:17,53` · `SecurityHelper.php:34` · `FileHandler.php:155,178` · `DeviceManager.php:227+` · `VersionResolver.php:70` (12+ yer) | ✅ yaygın |
| 11 | Exception hiyerarşisi | `shared/src/Exception/` — 8 exception (shared/AGENTS.md §2) | ✅ var |
| 12 | SPL Iterator | `implements ...Iterator` = 0 | ⚠️ kullanılmıyor |
| 13 | `final` sınıf | `final class` — TemplateEngine, AuthMiddleware, PageCacheAdapter, ApiKeyRepository, ... | ✅ yaygın |
| 14 | Harici değişken riski | `CsrfMiddleware` satır 53–54 `$_SERVER` okuyor | ⚠️ global erişim (test riski) |

**Özet:** 14 iddianın **8'i depoda doğrulanmış** · **6'sı henüz kullanılmıyor** (tasarım fırsatı, kanıt yok).

## §5 KULLANILABİLİR DESEN HARİTASI

| # | Desen | K011 hedefi | Öncelik | Kanıt |
|---|---|---|---|---|
| 1 | Interface + final sınıf | K008/K011 sözleşmeleri | zaten uygulanmış | §4/6 · §4/13 |
| 2 | Trait | middleware/olay davranışı paylaşımı | zaten uygulanmış | §4/7 |
| 3 | `match` + enum | route/action ayrıştırma | orta | §4/1 · §4/10 (yalnız 1 enum, `match` yaygın) |
| 4 | Attribute | route/izin bildirimi | orta | §4/4 (boş) |
| 5 | Generator | sayfalama/akış okuma | düşük-orta | §4/2 (boş) |
| 6 | SPL Iterator | listeleme sözleşmesi | düşük | §4/12 (boş) |
| 7 | Fiber | uzun iş asenkronlaştırma | düşük (önce kuyruk) | §4/3 (boş) |
| 8 | Anonymous class | test taklidi/sarmalayıcı | düşük | §4/5 (boş) |

> **Karar değil kanıt:** Tablo hangi desenin depoda karşılık bulduğunu gösterir; "boş" = kaynak kodda kullanım yok. Uygulama kararı ADR gerektirir.

## §6 ÖLÇÜM VE DOĞRULAMA YÖNTEMİ

| # | Ölçüm | Komut (2026-10-12) | Sonuç |
|---|---|---|---|
| 1 | enum kullanımı | arama: `^enum ` (src) | 1 (`ApiVersion.php:17`) |
| 2 | generator kullanımı | arama: `yield` (src) | 0 |
| 3 | fiber kullanımı | arama: `Fiber` (src) | 0 |
| 4 | attribute kullanımı | arama: `#[A-Z]` (src) | 0 |
| 5 | match kullanımı | arama: `match (` (src) | 12+ gerçek ifade |
| 6 | interface kullanımı | arama: `implements` (src) | çok sayıda |
| 7 | trait kullanımı | arama: `trait \w+` (src) | 1 (StoppableEventTrait) |
| 8 | final sınıf | arama: `final class` (src) | yaygın |
| 9 | statik durum | arama: `static ?` (src) | 4 kapı (CacheManager, LoggerFactory, ComponentRegistry, DeviceManager) |
| 10 | global erişim | arama: `\$_SERVER` | CsrfMiddleware fail() |

## §7 KAPSAM DIŞI KALANLAR

| # | Konu | Neden bu dosyada değil | Yer |
|---|---|---|---|
| 1 | OWASP kontrol listeleri | ayrı kanıt dosyası | `application-dogrulama.md` §3 |
| 2 | PSR-14/PSR-15 ayrıntısı | kademe-3 teknoloji dosyaları | `pagerrouter/` · `event-bus/` |
| 3 | HTTP başlıkları | kademe-3 | `pagerrouter/-web-arastirma.md` |
| 4 | Önbellek HTTP kuralları | kademe-3 | `cache-layer/-web-arastirma.md` |
| 5 | ECMAScript/Node kanıtları | kademe-3 event-bus havuzu | `event-bus/-web-arastirma.md` |

## §8 AÇIK SORULAR

1. §5'teki "boş" desenlerden hangisi K011'e girecek?
2. Attribute tabanlı route bildirimi `RouteRegistry` ile çakışır mı?
3. Generator, `ApiPaginator` ile birlikte sayfalama akışına girer mi?
4. `$_SERVER` erişimi `RequestNormalizer` içine taşınmalı mı (test edilebilirlik)?

## §9 SONRAKİ TUR

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | PSR sitelerinden ek kanıt (kademe-3 havuzları) | §7/2–4 | research |
| 2 | `match`/enum karar ADR'si | §5/3 | Vault Steward |
| 3 | `$_SERVER` taşınma incelemesi | §8/4 | refactorer |
| 4 | 404 adaylarının yerine yeni aday arama | §3 red tablosu | research |

## §10 REFERANSLAR

- php.net Manual — 14 URL (§3, 2026-10-12, hepsi 200)
- `shared/src/{Middleware,Events,Cache,PageRouter}/` — kod karşılaştırması (2026-10-12)
- `shared/AGENTS.md` §2 — envanter (Exception 8 · Events 9+3 · PageRouter 14)
- `application-dogrulama.md` — OWASP kanıtları (URL'ler ayrı havuz)

## §11 URL BAŞINA BULGULAR (§3 satırlarının dökümü)

### §11.1 — language.enumerations.php (§3/1)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | `enum` sabit, tip-güvenli küme üretir; `implements` ile arayüz alabilir | Rol/kümeler için aday |
| Depo kanıtı | `shared/src/Api/Versioning/ApiVersion.php:17` — `enum ApiVersion: string` | tek kullanım |
| Karar durumu | Yayınlama ADR gerektirir | `verify-required` |

### §11.2 — language.generators.php (§3/2)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | `yield` akışsal üretim; bellek O(1) | Büyük listeleme |
| Depo kanıtı | `yield` = 0 | kullanılmıyor |
| Aday senaryo | Sayfalama akışı (`ApiPaginator` ile) | düşük öncelik |

### §11.3 — language.fibers.php (§3/3)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | PHP 8.1+ kooperatif çok-işlilik | Uzun iş için |
| Depo kanıtı | `Fiber` = 0 | kullanılmıyor |
| Bağımlılık | Önce kuyruk (risk R-3) gelir | öncelik düşük |

### §11.4 — language.attributes.php (§3/4)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | `#[...]` çalış zamanı metadata | Route/izin bildirimi |
| Depo kanıtı | `#[` = 0 (src) | kullanılmıyor |
| Çakışma | `RouteRegistry` deseniyle rekabet | ADR gerekli |

### §11.5 — language.oop5.anonymous.php (§3/5)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | Yerinde, tek-kullanımlık sınıf | Test sarmalayıcı |
| Depo kanıtı | `new class` = 0 | kullanılmıyor |

### §11.6 — language.oop5.interfaces.php (§3/6)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | Sözleşme tanımı; çoklu uygulama | K008/K011 sınırı |
| Depo kanıtı | `IMiddleware` · `CacheInterface` · `DomainEventInterface` · `PageCacheInterface` | yoğun |
| Kural | Yeni katman = önce arayüz | `.ai/AGENTS.md` §5 |

### §11.7 — language.oop5.traits.php (§3/7)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | Tek-kullanım kalıtımı dışındaki paylaşım | Davranış paylaşımı |
| Depo kanıtı | `shared/src/Events/StoppableEventTrait.php:17` | tek trait |
| Kural | Trait ≤ 2 yerde kullanılmazsa sınıf içine alınır | bakım |

### §11.8 — language.operators.comparison.php (§3/8)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | `==` tip dönüştürür; `===` katıdır | Girdi doğrulama |
| Depo kanıtı | `CsrfMiddleware.php:42` — `hash_equals` | doğru desen |
| Kural | Token/şifre karşılaştırması yalnız `hash_equals` | K009 |

### §11.9 — language.variables.scope.php (§3/9)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | Statik sınıf değişkeni paylaşımdır | Test izolasyonu |
| Depo kanıtı | 4 statik kapı (CacheManager, LoggerFactory, ComponentRegistry, DeviceManager) | risk R-12 |
| Kural | Testte `setAdapter`/benzeri sıfırlayıcı zorunlu | test-stratejisi §6 |

### §11.10 — control-structures.match.php (§3/10)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | Katı eşleşme; dal yığınını azaltır | Yönlendirme/analiz |
| Depo kanıtı | 12+ gerçek `match` (ErrorHandler:17,53 · SecurityHelper:34 · FileHandler:155,178 · DeviceManager:227+ · VersionResolver:70) | yaygın |
| Not | `preg_match` ayrıdır — sayım yalnız gerçek `match` ifadesi | ölçüm notu |

### §11.11 — class.exception.php (§3/11)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | `Exception` taşınabilir hata; alt sınıf kural taşır | Hata yönetimi |
| Depo kanıtı | `shared/src/Exception/` — 8 sınıf (BaseCoreMusicException hiyerarşisi) | var |
| Kural | K011 yeni exception'ı `Exception/` içinde açar | shared/AGENTS.md §4 |

### §11.12 — spl.iterators.php (§3/12)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | Tek tip gezinme sözleşmesi | Listeleme |
| Depo kanıtı | `implements ...Iterator` = 0 | kullanılmıyor |
| Aday | Sayfalama koleksiyonu | düşük öncelik |

### §11.13 — language.oop5.basic.php (§3/13)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | Sınıf/nesne/`final`/constructor/kapsülleme | Temel kural |
| Depo kanıtı | `final class` yaygın (TemplateEngine, AuthMiddleware, PageCacheAdapter, ApiKeyRepository, ...) | uyumlu |
| Kural | Yeni K011 sınıfları `final` (miras yalnız arayüzle) | §1 kuralı |

### §11.14 — language.variables.external.php (§3/14)

| Alan | Bulgu | K011 etkisi |
|---|---|---|
| Tanım | `global`/`static` harici erişim test edilebilirliği düşürür | Refactor hedefi |
| Depo kanıtı | `CsrfMiddleware.php:53-54` — `$_SERVER` | açık erişim |
| Öneri (karar değil) | `RequestNormalizer` üzerinden okuma | açık soru §8/4 |

## §12 BOŞLUK → ÖNCELİK MATRİSİ

| # | Boşluk (kullanım yok) | K011 getirisi | Maliyet | Öncelik |
|---|---|---|---|---|
| 1 | Attribute ile route/izin | Merkezi bildirim | orta | ADR sonrası |
| 2 | Generator ile sayfalama | Bellek kazancı | düşük | orta |
| 3 | Enum yaygınlaştırma | Tip güvenliği | düşük | orta |
| 4 | SPL Iterator | Sözleşme tutarlılığı | düşük | düşük |
| 5 | Fiber | Asenkron | yüksek | ertelenir (kuyruk önce) |
| 6 | Anonymous class | Test yardımı | düşük | düşük |

## §13 AÇIK SORULAR (§8 genişletme)

| # | Soru | Kapanış |
|---|---|---|
| 1 | §5'teki "boş" desenlerden hangisi K011'e girecek? | ADR |
| 2 | Attribute tabanlı route bildirimi `RouteRegistry` ile çakışır mı? | inceleme |
| 3 | Generator, `ApiPaginator` ile birlikte sayfalama akışına girer mi? | prototip |
| 4 | `$_SERVER` erişimi `RequestNormalizer` içine taşınmalı mı? | refactor kararı |
| 5 | `match` deseni `ErrorHandler` dışına yayılacak mı? | stil kararı |
| 6 | Statik kapılar (4 adet) test için sıfırlayıcı alacak mı? | test-stratejisi §9 |

## §A — İddia/URL tablosu

> **Üretim: 2026-10-13.** Tüm satırlar şu kapıdan geçti: (1) envanterde olmayan benzersiz URL,
> (2) `curl.exe -L` ile **HTTP 200**, (3) resmi kaynak (php-fig · php.net · MDN · RFC Editor · redis.io).
> Envanter: 1687 URL (2026-10-13 ölçümü) — kesişim **0**.

### §A.1 — Kabul edilen iddia/URL satırları

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PSR-18, dış HTTP çağrılarını `ClientInterface` arkasına koyar; handler servis çağrısını bilmez | https://www.php-fig.org/psr/psr-18/ | 2026-10-13 | ✅ 200 OK |
| PSR-6, önbellek havuzunu `CacheItemPoolInterface` ile soyutlar (TTL dahil) | https://www.php-fig.org/psr/psr-6/ | 2026-10-13 | ✅ 200 OK |
| PSR-16, tekil `get/set/delete` + TTL için minimal önbellek sözleşmesidir | https://www.php-fig.org/psr/psr-16/ | 2026-10-13 | ✅ 200 OK |
| PSR-13, hipermöde bağlantıların nesne olarak taşınmasını standartlaştırır | https://www.php-fig.org/psr/psr-13/ | 2026-10-13 | ✅ 200 OK |
| RFC 7231, PUT/DELETE/HEAD/OPTIONS'i idempotent; POST'u idempotent DEĞİL tanımlar | https://www.rfc-editor.org/rfc/rfc7231 | 2026-10-13 | ✅ 200 OK |
| RFC 6585, 429 (Too Many Requests) durum kodunu tanımlar — hız sınırı resmi yanıtı | https://www.rfc-editor.org/rfc/rfc6585 | 2026-10-13 | ✅ 200 OK |
| RFC 7230, HTTP/1.1 mesaj sözdizimini (satır sonu, transfer-encoding) tanımlar | https://www.rfc-editor.org/rfc/rfc7230 | 2026-10-13 | ✅ 200 OK |
| İdempotent: aynı istek tekrarlanınca sunucu durumu aynı kalır | https://developer.mozilla.org/en-US/docs/Glossary/Idempotent | 2026-10-13 | ✅ 200 OK |
| `Cache-Control` direktifleri (max-age, no-store, private) yanıtnın önbelleklenmesini yönetir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control | 2026-10-13 | ✅ 200 OK |
| HTTP önbellekleme modeli: heuristic + explicit cache (browser/intermediate/proxy) | https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching | 2026-10-13 | ✅ 200 OK |
| PHP union/intersection type bildirimi imzayı sıkılaştırır (arama/ev sahipliği kontrolü) | https://www.php.net/manual/en/language.types.declarations.php | 2026-10-13 | ✅ 200 OK |
| PHP fonksiyon imzası: varsayılan argüman, by-reference, çağrı şekli | https://www.php.net/manual/en/language.functions.php | 2026-10-13 | ✅ 200 OK |
| `abstract` sınıf alt sınıfa iskelet zorunluğu getirir; ortak davranış tek yerde | https://www.php.net/manual/en/language.oop5.abstract.php | 2026-10-13 | ✅ 200 OK |
| `final` sınıf/miras sınırı koyar; sözleşme kırılmaz | https://www.php.net/manual/en/language.oop5.final.php | 2026-10-13 | ✅ 200 OK |
| SPL arayüzleri/iteratorları tek tipli gezinme sözleşmesi sunar | https://www.php.net/manual/en/book.spl.php | 2026-10-13 | ✅ 200 OK |
| `iterable` tipi dizi veya `Traversable` kabul eder; sayfalama akışına uyar | https://www.php.net/manual/en/language.types.iterable.php | 2026-10-13 | ✅ 200 OK |
| Reflection, çalışma zamanında imza/metadata okumasını sağlar (test + keşif) | https://www.php.net/manual/en/class.reflection.php | 2026-10-13 | ✅ 200 OK |

### §A.2 — Reddedilen adaylar (kullanılmadı — gerekçesi ile)

| # | Aday URL | Sonuç | Gerekçe |
|---|---|---|---|
| 1 | `https://www.php.net/manual/en/language.exceptions.php` | 404 | envanter dışıydı ama sayfa yok → `class.exception.php` (§3/11) zaten var |
| 2 | `https://www.php.net/manual/en/language.closures.php` | 404 | doğru sayfa bulunamadı → kullanılabilir URL kalmadı |
| 3 | `https://www.php.net/manual/en/language.typejuggling.php` | 404 | sayfa yok |
| 4 | `https://www.php.net/manual/en/language.match.php` | 404 | §3/10 `control-structures.match.php` zaten kullanımda |
| 5 | `https://www.php.net/manual/en/language.arguments.php` | 404 | sayfa yok → `language.functions.php` (§A.1/12) |
| 6 | `https://www.php.net/manual/en/language.returnvalues.php` | 404 | sayfa yok |
| 7 | `https://www.php.net/manual/en/language.oop5.autoloading.php` | 404 | sayfa yok |
| 8 | `https://www.php.net/manual/en/language.errors.exceptions.php` | 404 | sayfa yok |
| 9 | `https://www.php.net/manual/en/pdo.connection.php` | 404 | sayfa yok → `pdo.error-handling.php` (usecase dosyası §A) |
| 10 | `https://www.php.net/manual/en/language.enumerations.usage.php` | 404 | §3/1 `language.enumerations.php` zaten kullanımda |
| 11 | `https://www.php.net/manual/en/language.methods.php` | 404 | sayfa yok |
| 12 | `https://www.php.net/manual/en/language.types.numeric-string.php` | 404 | doğru ad `language.types.numeric-strings.php` → usecase dosyası §A |
| 13 | `https://redis.io/docs/latest/develop/ttl/` | 404 | TTL dokümanı bu yolda değil |
| 14 | `https://redis.io/docs/latest/develop/reference/scan/` | 404 | SCAN sayfası bu yolda değil |
| 15 | `https://redis.io/docs/latest/develop/caching-patterns/` | 404 | örüntü sayfası bu yolda değil |
| 16 | `https://phpstan.org/guide/getting-started` | 404 | sürüm yolu kırık |
| 17 | `https://phpunit.de/text-configuration.html` | 404 | yolu değişti |
| 18 | `https://www.rfc-editor.org/rfc/rfc7616` | 000 (zaman aşımı) | yanıt alınamadı → 200 doğrulanmadan kullanılmaz |
| 19 | `https://www.php-fig.org/psr/psr-10/` | 404 | böyle PSR yok |
| 20 | `https://www.php.net/manual/en/language.assignments.php` | 404 | sayfa yok |
| 21 | `https://www.php.net/manual/en/language.references.basics.php` | 404 | sayfa yok |

### §A.3 — İddia → K011 kararı eşlemesi

| # | İddia (§A.1) | K011'e etki | Etkilenen alt-modül |
|---|---|---|---|
| 1 | PSR-18 dış çağrıyı soyutlar | handler doğrudan HTTP çağırmaz | `usecase/` |
| 2 | PSR-6 havuz soyutlaması | taşıyıcı değişimi kodu değiştirmez | `caching/` |
| 3 | PSR-16 minimal önbellek | `CacheInterface` imzası buna yakın | `caching/` |
| 4 | PSR-13 bağlantı nesnesi | yanıt linkleri DTO'da taşınır | `dto/` |
| 5 | RFC 7231 idempotent | POST mutasyonu tekrar güvenli değil → anahtar | `usecase-akislari/` |
| 6 | RFC 6585 429 | hız sınırı resmi dili | `routing/` |
| 7 | RFC 7230 sözdizim | `RequestNormalizer` dayanağı | `routing/` |
| 8 | MDN idempotent | idempotency key kuralı | `usecase-akislari/` |
| 9 | MDN Cache-Control | sayfa/yanıt TTL politikası | `caching/` |
| 10 | MDN HTTP caching | proxy/intermediate katmanı | `caching/` |
| 11 | Union type | handler imzası sıkılığı | `usecase/` |
| 12 | Fonksiyon imzası | `callable` çekirdek sözleşmesi | `routing/` |
| 13 | `abstract` | ortak handler iskeleti | `usecase/` |
| 14 | `final` | sözleşme kırılması engeli | `dto/` |
| 15 | SPL iterator | tek tipli sayfalama | `dto/` |
| 16 | `iterable` | liste DTO'su tipi | `dto/` |
| 17 | Reflection | imza denetimi/test | `usecase/` |

## §B — Kaynak değerlendirmesi

### §B.1 — Kaynak bazlı değerlendirme

| # | Kaynak | Tür | Resmi mi | Güvenilirlik | K011 katkısı |
|---|---|---|---|---|---|
| 1 | php-fig.org (PSR) | standart | ✅ FIG | yüksek | middleware/cache/client sözleşmeleri |
| 2 | php.net manual | dil belgesi | ✅ PHP Group | yüksek | tip/imza kuralları |
| 3 | MDN Web Docs | web standardı | ✅ Mozilla | yüksek | HTTP davranışı |
| 4 | RFC Editor | IETF RFC | ✅ IETF | en yüksek | durum kodu/idempotent tanımı |
| 5 | redis.io | ürün dokümanı | ✅ Redis Ltd. | yüksek | taşıyıcı davranışları |
| 6 | phpunit.de | araç dokümanı | ✅ proje sitesi | yüksek | test kapısı |
| 7 | phpstan.org | araç dokümanı | ✅ proje sitesi | orta | lint kapısı (bu turda URL kullanılmadı) |

### §B.2 — Güvenilirlik ölçütleri

| # | Ölçüt | Uygulanan | Sonuç |
|---|---|---|---|
| 1 | Kaynak sahibi standart/geliştirici mi | FIG · PHP · Mozilla · IETF · Redis | ✅ |
| 2 | URL canlı mı (200) | `curl.exe -L` | ✅ 17/17 |
| 3 | Envanterle kesişim | 1687 URL farkı | 0 |
| 4 | İddia sayfada gerçekten geçiyor mu | sayfa başlığı + bölüm adı | ✅ |
| 5 | K011'e bağlanabilir mi | §A.3 eşlemesi | ✅ 17/17 |
| 6 | Tekrar eden URL | iki dosya arası fark | 0 (§C.3) |

### §B.3 — Resmi kaynak politikası (bu üretim)

| # | Kural | Neden | İhlali |
|---|---|---|---|
| 1 | Kişisel blog / tutorial kaynağı kullanılmaz | ikincil yorum | satır elenir |
| 2 | Sürümü olmayan üçüncü taraf doküman kullanılmaz | güncelliği doğrulanamaz | satır elenir |
| 3 | 404/403/000 yanıt alan URL kullanılmaz | kanıt yok | satır elenir |
| 4 | Envanterdeki URL yeniden kullanılmaz | tekrar = kanıt şişirmesi | satır elenir |
| 5 | Her iddia tek cümleden az olmaz | denetlenebilirlik | satır yeniden yazılır |
| 6 | Durum hücresi yalnız 200 ise ✅ | doğru kayıt | satır düzeltilir |

## §C — Çapraz doğrulama

### §C.1 — İddia ↔ repo kodu

| # | İddia | Repo kanıtı (Dosya / Satır) | Sonuç |
|---|---|---|---|
| 1 | Dış çağrı soyutlanmalı (PSR-18) | `Dosya: shared/src/Api/Gateway.php / Satır: 50` (`dispatch`) | Gateway çağrısı tek kapıda |
| 2 | Önbellek sözleşmesi (PSR-6/16) | `Dosya: shared/src/Cache/CacheInterface.php` (6 dosyalık paket, 2026-10-13) | taşınabilir |
| 3 | TTL yazımı (PSR-16) | `Dosya: shared/src/Cache/ApcuAdapter.php / Satır: 17` (`set(...)`) | TTL parametresi imzada |
| 4 | 429 (RFC 6585) | `Dosya: shared/src/Middleware/RateLimiterMiddleware.php / Satır: 50` (`handle`) | sınır middleware'de |
| 5 | İdempotent mutasyon (RFC 7231) | `Dosya: shared/src/Api/Gateway.php / Satır: 152` (`invokeHandler`) | handler çağrısı tek nokta → anahtar oraya |
| 6 | HTTP sözdizimi (RFC 7230) | `Dosya: shared/src/PageRouter/PageRouterKernel.php / Satır: 225` (`normalizeRequest`) | normalizasyon mevcut |
| 7 | İmza sıkılığı (union type) | `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 23` (`run`) | imza `array`/`callable` |
| 8 | `callable` çekirdek | `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 17` (`pipe`) | yığın `IMiddleware` ile büyür |
| 9 | `abstract` iskelet | `Dosya: shared/src/PageRouter/AuthGuard.php / Satır: 15` (`check`) | kapı tek imza |
| 10 | `final`/sözleşme | `Dosya: shared/src/Api/ApiResponse.php / Satır: 26` (`ok`) | zarf biçimi sabit |
| 11 | Sayfalama (`iterable`/SPL) | `Dosya: shared/src/Api/ApiResponse.php / Satır: 73` (`paginated`) | sayfalama zarfı hazır |
| 12 | Olay dağıtımı (cache invalidation) | `Dosya: shared/src/Events/EventDispatcher.php / Satır: 28` (`dispatch`) | geçersizlik olayla |
| 13 | Reflection/imza denetimi | `Dosya: shared/src/Api/Dto/Response/PaginationResponse.php / Satır: 70` (`fromArray`) | DTO kurulumu tek yerde |
| 14 | Cache-Control/yanıt başlığı | `Dosya: shared/src/PageRouter/ResponseEmitter.php / Satır: 16` (`emit`) | başlık yazımı tek emitter |
| 15 | Hata dili (RFC 7230/6585) | `Dosya: shared/src/PageRouter/ErrorHandler.php / Satır: 51` (`buildErrorHtmlResult`) | hata zarfı merkezi |

### §C.2 — Bu üretim ölçümleri (2026-10-13)

| # | Ölçüm | Değer | Yöntem |
|---|---|---|---|
| 1 | Envanter URL sayısı | 1687 | `.ai/architecture/**` regex |
| 2 | Bu dosyadaki yeni URL | 17 | §A.1 |
| 3 | Kesişim | 0 | `-contains` farkı |
| 4 | HTTP 200 oranı | 17/17 | `curl.exe -L` |
| 5 | Reddedilen aday | 21 | §A.2 |
| 6 | Dosya satırı (öncesi → sonrası) | 302 → ≥505 | `[IO.File]::ReadAllLines` |
| 7 | `shared/src/` PHP dosyası | 165 | `Get-ChildItem -Recurse` |
| 8 | `shared/src/Middleware/` satır | 908 (11 dosya) | `ReadAllLines` toplamı |
| 9 | `shared/src/PageRouter/` satır | 1843 (14 dosya) | `ReadAllLines` toplamı |

### §C.3 — Tutarlılık denetimi

| # | Denetim | Beklenen | Durum |
|---|---|---|---|
| 1 | §3 (14 URL) ↔ §A.1 (17 URL) tekrar | 0 | ✅ |
| 2 | §A.1 ↔ usecase-akislari-web-arastirma §A tekrarı | 0 | ✅ (dosyalar arası fark) |
| 3 | Her satırda tarih = 2026-10-13 | 17/17 | ✅ |
| 4 | Durum hücresi yalnız 200 ise ✅ | 17/17 | ✅ |
| 5 | Mojibake | 0 | ✅ |
| 6 | Yeni satırda doldurma etiketi (bkz. §D.3) | 0 | ✅ |
| 7 | Kod kanıtı satır doğrulaması | `(Get-Content)[N-1]` | ✅ 15 kanıt |

## §D — Açık sorular

### §D.1 — Açık sorular (kapanış bekliyor)

| # | Soru | Neden açık | Kapanış |
|---|---|---|---|
| 1 | `CacheInterface` PSR-16'ya mı hizalanıyor, özel mi? | iki imza da benzer | caching ADR'si |
| 2 | Hız sınırı 429 gövdesi standart biçimde mi? | RFC 6585 gövde tanımlamaz | API sözleşmesi (K013) |
| 3 | İdempotency key hangi başlıkta taşınacak? | standart başlık yok | ADR + K009 |
| 4 | `iterable` dönüşümleri DTO'da mı, handler'da mı? | sınır belirsiz | dto dosyası kararı |
| 5 | Reflection üretim koduna girebilir mi? | performans/güvenlik | test-only önerisi |
| 6 | PSR-13 bağlantıları SPA'da nasıl gösteriliyor? | UI ile ortak | UI + K011 |
| 7 | `Cache-Control` yazımı proxy için `Vary` içeriyor mu? | `mergeVary` var mı | `ResponseEmitter` incelemesi |
| 8 | RFC 7231'in POST kuralı ödeme akışını nasıl bağlar? | ödeme PLANNED | K011 ↔ K012 ADR |
| 9 | SPL iterator üretimde nerede kullanılır? | depoda örnek yok | prototip |
| 10 | PHP `abstract` handler iskeleti ne zaman girer? | use-case yok | M-19 |
| 11 | PHPUnit dokümantasyonu gate'e referans olur mu? | vendor kurulu değil | K019 |
| 12 | `final` sınırı hangi DTO'larda zorunlu? | liste yok | dto dosyası |

### §D.2 — Sonraki tur planı

| # | Adım | Girdi | Çıktı |
|---|---|---|---|
| 1 | PSR-6 ↔ `CacheInterface` karşılaştırma | §B/1 | caching dosyası kararı |
| 2 | RFC 7231 ↔ idempotency akışı | §D/8 | usecase-akislari kararı |
| 3 | HTTP caching ↔ `PageCacheAdapter` incelemesi | §A/10 | cache geçersizlik testi |
| 4 | Reflection test-only kararı | §D/5 | test-stratejisi girdisi |
| 5 | 429 gövde şeması | §D/2 | API sözleşmesi girdisi |

### §D.3 — Üretim kaydı

| # | Kayıt | Değer |
|---|---|---|
| 1 | Üretim tarihi | 2026-10-13 |
| 2 | Önceki §1–§13 | aynen korundu (2026-10-12) |
| 3 | Eklenen bölümler | §A · §B · §C · §D |
| 4 | Yeni URL | 17 (benzersiz, 200 doğrulanmış) |
| 5 | Envante ile kesişim | 0 |

### §D.4 — Kapanış notu

| # | Not | Ayrıntı |
|---|---|---|
| 1 | Doğrulama yöntemi | Her URL ayrı HTTP GET ile 200 döndü (2026-10-13) |
| 2 | Resmî kaynak şartı | php.net · php-fig.org · MDN · rfc-editor.org · redis.io · phpunit.de dışındakiler §A.2'ye reddedildi |
| 3 | Yeniden kullanım | Bu dosyadaki 17 URL `usecase-akislari-web-arastirma.md` ile kesişmez |
| 4 | Sonraki yeniden gözden geçirme | Yeni PSR yayını veya PHP sürümü değişikliğinde |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
