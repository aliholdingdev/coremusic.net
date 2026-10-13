---
type: system
category: electronics-software
title: "CoreMusic Electronics Software Architecture"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics Software Architecture

**Zorunlu Bağlantılar:** [[electronic/platform-architecture]] · [[electronic/device-architecture]] · [[electronic/service-architecture]] · [[electronic/operating-system-architecture]] · [[brain.md]]

---

## 1. Amaç

CoreMusic ELECTRONICS yazılım mimarisi **monolitik değil**, modüler, bağımsız ve sorumlulukları ayrılmış bileşenlerden oluşur. Her bileşen tek bir sorumluluk alanına sahiptir ve bağımlılıklar sıkı şekilde kontrol edilir. **Domain Layer**, tüm iş mantığının bulunduğu bağımsız çekirdektir ve framework, veritabanı, UI veya donanımdan **tamamen bağımsızdır**.

---

## 2. Mimari Katmanlar (5 Katman)

```
┌─────────────────────────────────────────────────────┐
│  Presentation Layer                                 │
│  Web, Mobile, Desktop, CLI, Embedded UI             │
├─────────────────────────────────────────────────────┤
│  Application Layer                                  │
│  Use Cases, Application Services, DTO, Orchestration│
├─────────────────────────────────────────────────────┤
│  Domain Layer  ← ÇEKİRDEK (bağımsız)                │
│  Business Rules, Entities, Value Objects,            │
│  Aggregates, Domain Events, Domain Services         │
├─────────────────────────────────────────────────────┤
│  Infrastructure Layer                               │
│  Repositories, Message Queue, External Services,    │
│  Database, Cache, Filesystem                        │
├─────────────────────────────────────────────────────┤
│  Hardware Layer                                     │
│  Device Drivers (ASIO, WASAPI, ALSA), Firmware,     │
│  I2S, SPI, USB, CAN Bus                            │
└─────────────────────────────────────────────────────┘
```

| Katman | Sorumluluk | Bağımlılık |
|--------|------------|------------|
| Presentation | UI, user interaction | → Application |
| Application | Use cases, orchestration | → Domain |
| Domain | Business rules, entities | → HİÇBİR ŞEYE bağımlı DEĞİL |
| Infrastructure | Data access, external services | → Domain arayüzlerini uygular |
| Hardware | Device drivers, firmware | → Infrastructure |

---

## 3. Domain Layer — Çekirdek

Domain Layer, tüm iş mantığının bulunduğu **bağımsız çekirdektir**. Framework, veritabanı, UI veya donanımdan **tamamen bağımsızdır**.

### 3.1 Domain Bileşenleri

| Bileşen | Açıklama | Örnek |
|---------|----------|-------|
| Entities | Benzersiz kimliğe sahip nesneler | Device, User, AudioTrack, Playlist |
| Value Objects | Değişmez değer nesneleri | SampleRate, Gain, Frequency, Volume |
| Aggregates | Tutarlılık sınırı | DeviceAggregate, PlaylistAggregate, AudioAggregate |
| Domain Services | İş mantığı servisleri | AudioProcessingService, DSPService |
| Domain Events | Olay üretimi | DeviceConnected, FirmwareUpdated, AudioStarted |
| Repositories | Veri erişim arayüzü | DeviceRepository, UserRepository |

### 3.2 Domain Bağımsızlığı

```
Domain Layer → Hiçbir şeye bağımlı DEĞİL
Application Layer → Domain'e bağımlı
Infrastructure Layer → Domain arayüzlerini uygular
Presentation Layer → Application'a bağımlı
```

**Dependency Inversion Principle (DIP):** Dış dünya (DB, HW, UI), Domain'in arayüzlerini uygular. Detaylar, soyutluklara bağımlı olmalı.

### 3.3 Domain İletişimi

```
Application Service → Domain Service → Repository → Infrastructure
```

Domain katmanı asla doğrudan Infrastructure veya Presentation'ı import etmez. Sadece arayüzleri (interface) tanımlar.

---

## 4. Modüller (14 Modül)

| # | Modül | Sorumluluk | Katman |
|---|-------|------------|--------|
| 1 | Core System | Temel altyapı, lifecycle, DI container | Domain |
| 2 | Authentication | Kimlik doğrulama, token yönetimi, OAuth | Domain |
| 3 | User Management | Kullanıcı profilleri, roller, RBAC | Domain |
| 4 | Device Management | Cihaz algılama, kayıt, yapılandırma | Domain |
| 5 | Driver Manager | Sürücü yükleme, yönetimi, hot-plug | Infrastructure |
| 6 | DSP Manager | DSP processing, EQ, compressor, limiter | Domain |
| 7 | Audio Engine | Ses oynatma, kayıt, mixing, routing | Domain |
| 8 | Streaming Engine | HTTP, multi-room, adaptive streaming | Infrastructure |
| 9 | Download Service | Queue, resume, retry, cache, anti-ban | Domain |
| 10 | Media Library | Scan, index, metadata, search, albüm/sanatçı | Domain |
| 11 | Playlist Manager | Oluştururma, senkronizasyon, AI playlist | Domain |
| 12 | Update Manager | Firmware, driver, DSP, AI model güncelleme | Infrastructure |
| 13 | AI Services | Recommendation, auto-EQ, room correction | Domain |
| 14 | Diagnostics | Health check, error reporting, monitoring, logging | Infrastructure |

---

## 5. Bağımlılık Kuralları

```
UI Layer ──▶ Application Layer ──▶ Domain Layer ──▶ Interfaces ──▶ Infrastructure Layer ──▶ Hardware Layer
```

| Kural | Açıklama |
|-------|----------|
| ✅ Presentation → Application | UI, Application'ı çağırabilir |
| ✅ Application → Domain | Application, Domain'i çağırabilir |
| ✅ Domain → Interfaces | Domain, arayüzleri tanımlar |
| ✅ Infrastructure → Interfaces | Infrastructure, arayüzleri uygular |
| ❌ Domain → Infrastructure | Domain, Infrastructure'a bağımlı OLAMAZ |
| ❌ Domain → Presentation | Domain, UI'a bağımlı OLAMAZ |
| ❌ Infrastructure → Domain | Infrastructure, Domain'i doğrudan import EDİLEMEZ |

**Dependency Inversion Principle:** Detaylar, soyutluklara bağımlı olmalı.

---

## 6. Plugin Mimarisi

Gelecek genişletmeler için plugin mimarisi:

| Plugin | Açıklama | Öncelik |
|--------|----------|---------|
| Spotify Plugin | Spotify entegrasyonu | Düşük |
| YouTube Plugin | YouTube Music entegrasyonu | Orta |
| Deezer Plugin | Deezer entegrasyonu (FLAC 24/32-bit) | Yüksek |
| Tidal Plugin | Tidal entegrasyonu | Düşük |
| NAS Plugin | NAS depolama entegrasyonu | Orta |
| DLNA Plugin | DLNA/UPnP entegrasyonu | Orta |
| AirPlay Plugin | Apple AirPlay streaming | Yüksek |
| Chromecast Plugin | Google Chromecast streaming | Yüksek |

### 6.1 Plugin Arayüzü

```php
interface PluginInterface
{
    public function getName(): string;
    public function getVersion(): string;
    public function initialize(ContainerInterface $container): void;
    public function getCapabilities(): array;
    public function shutdown(): void;
}
```

---

## 7. Tasarım Kuralları

### 7.1 Mimari Prensipler

| Prensipl | Açıklama | Kaynak |
|----------|----------|--------|
| SOLID | Tek sorumluluk, açık-kapalı, yerine koyma, arayüz ayrımı, tersine bağımlılık | [[brain.md]] §3 |
| Clean Architecture | Katmanlı, bağımsız yapı | [[architecture/l6-electronics]] |
| Hexagonal Architecture | Adapter/Port pattern | [[brain.md]] §3 |
| Onion Architecture | İç içe halka yapısı | [[brain.md]] §3 |
| DDD | Domain-Driven Design | Bu dosya §3 |
| CQRS | Command Query Segregation | [[electronic/service-architecture]] |
| Event Driven | Asenkron olay tabanlı | [[electronic/service-architecture]] |

### 7.2 Kod Standartları

| Standart | Açıklama |
|----------|----------|
| PSR-12 | PHP coding style |
| PSR-4 | Autoloading |
| PSR-15 | HTTP middleware |
| PSR-14 | Event dispatcher |
| PSR-11 | Container interface |
| Strict Types | `declare(strict_types=1)` her dosyada |
| Immutable Objects | Değişmez nesneler |
| Repository Pattern | Veri erişim soyutlaması |
| Service Pattern | İş mantığı soyutlaması |
| Dependency Injection | Bağımlılık enjeksiyonu |

Detaylar: [[.claude/rules/php-standards]], [[ADR-001-vanilla-js-itcss]], [[ADR-002-pdo-mandatory-no-orm]]

---

## 8. Mimari Karşılaştırma

| Özellik | Monolitik | CoreMusic |
|---------|-----------|-----------|
| Yapı | Tek uygulama | Modüler bileşenler |
| Bağımlılık | Yüksek | Düşük (DI, DIP) |
| Test | Zor | Kolay (unit test) |
| Ölçekleme | Dikey | Yatay |
| Deploy | Tek paket | Bağımsız servisler |
| Bakım | Zor | Kolay |
| Plugin | Yok | Var (8 plugin) |
| Domain Bağımsızlığı | Yok | Tam bağımsız |

---

## 9. Cross References

| Dosya | Kapsam |
|-------|--------|
| [[electronic/platform-architecture]] | Platform mimarisi |
| [[electronic/device-architecture]] | Cihaz mimarisi |
| [[electronic/service-architecture]] | Servis mimarisi |
| [[electronic/operating-system-architecture]] | OS mimarisi |
| [[electronic/device-ecosystem]] | Cihaz ekosistemi |
| [[brain.md]] §3 | Core prensipler |
| [[ADR-001-vanilla-js-itcss]] | Frontend standartları |
| [[ADR-002-pdo-mandatory-no-orm]] | DB standartları |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Architectural Layers | 5 (Presentation, Application, Domain, Infrastructure, Hardware) |
| Modules | 14 |
| Plugins | 8 |
| Design Principles | 7 (SOLID, Clean, Hexagonal, Onion, DDD, CQRS, Event Driven) |
| Kod Standartları | 10 |
| Cross References | 8 |

---

## 11. Katmanlar Arası Arayüz Sözleşmeleri

Katmanlar yalnızca **arayüz üzerinden** konuşur. Aşağıdaki tablo hangi sözleşmenin nerede tanımlandığını ve nerede uygulandığını gösterir; sözleşme değişikliği tek taraflı yapılamaz.

| Sözleşme | Tanımlandığı Katman | Uygulandığı Katman | Sorumluluk |
|----------|---------------------|--------------------|------------|
| `IAudioEngine` | Domain | Infrastructure | Oynatma/kayıt/karıştırma |
| `IDeviceRepository` | Domain | Infrastructure | Cihaz kalıcılığı |
| `IDriverAdapter` | Domain | Hardware | ASIO / WASAPI / ALSA soyutlaması |
| `IFirmwareTransport` | Domain | Hardware | Boot, OTA, register yazımı |
| `IDspPipeline` | Domain | Infrastructure | EQ, crossover, limiter aşamaları |
| `IPlugin` | Application | Infrastructure | 8 plugin sağlayıcısı |
| `IEventDispatcher` | Domain | Infrastructure | Domain Event dağıtımı |
| `IHealthProbe` | Application | Infrastructure | Diagnostics / health check |

**Sözleşme değişim kuralı:**

1. Arayüzü **Domain** tanımlar; implementasyonu Infrastructure veya Hardware yazar.
2. Yeni bir zorunlu metot arayüze eklendiğinde tüm implementasyonlar aynı commit'te güncellenir.
3. Arayüz metodu asla donanım tipi (register, handle, buffer) içermez; donanım tipi alt katmanın içindedir.
4. Domain'de `interface` dışında hiçbir `abstract class` paylaşılmaz.
5. Sözleşme ihlali testte yakalanır — bkz. `## 17. Test ve Doğrulama Stratejisi`.

---

## 12. Modül → Katman Dağılımı ve Dış Bağımlılık

§4'teki 14 modülün katman dağılımı ve dış bağımlılık sınırları:

| # | Modül | Katman | Dış Bağımlılık (izinli) | Repo Karşılığı |
|---|-------|--------|--------------------------|----------------|
| 1 | Core System | Domain | Hiçbir dış servis yok | `shared/src/Api/Gateway.php` (kismen) |
| 2 | Authentication | Domain | Session / token deposu | `shared/src/Middleware/` |
| 3 | User Management | Domain | Veritabanı (repository üzerinden) | `UNKNOWN` |
| 4 | Device Management | Domain | DeviceRepository | Vault içi — repo karşılığı yok |
| 5 | Driver Manager | Infrastructure | İşletim sistemi sürücü API'si | Vault içi — repo karşılığı yok |
| 6 | DSP Manager | Domain | IDspPipeline arayüzü | Vault içi — repo karşılığı yok |
| 7 | Audio Engine | Domain | IAudioEngine + IDriverAdapter | `assets.coremusic.net/js/coreplayer/coreplayer.controls.js` (kismen) |
| 8 | Streaming Engine | Infrastructure | HTTP istemcisi | `shared/src/PageRouter/` |
| 9 | Download Service | Domain | Dosya sistemi (kuyruk üzerinden) | `UNKNOWN` |
| 10 | Media Library | Domain | İndeks deposu | `UNKNOWN` |
| 11 | Playlist Manager | Domain | Olay yayını | `shared/src/Api/Bff/` (kismen) |
| 12 | Update Manager | Infrastructure | OTA taşıyıcısı → `[[firmware/ota-update]]` | Vault içi — repo karşılığı yok |
| 13 | AI Services | Domain | Model çıkarım katmanı | `shared/src/AI/` |
| 14 | Diagnostics | Infrastructure | Log / metrik akışı | `UNKNOWN` |

**Dağılım özeti:** Domain **9** modül · Infrastructure **4** modül · Hardware (doğrudan modül değil, `## 12` arayüzleri üzerinden) **1** entegrasyon noktası.

**Hücre notu:** `UNKNOWN` işareti, bu revizyon kapsamında repo okuması yapılmayan yüzeydir; tahmini doldurulmaz.

---

## 13. Ses Oynatma Akışı (Uçtan Uca)

| # | Adım | Katman | Taşınan Veri |
|---|------|--------|--------------|
| 1 | Kullanıcı oynatma komutu verir | Presentation | Olay (play / pause / seek) |
| 2 | Durum `PlaybackRepository` içinde tutulur | Presentation | Oynatma durumu |
| 3 | Sayfa iskeleti ses elemanını render eder | Presentation | HTML `<audio>` yerleşimi |
| 4 | Sayfa yönlendirme ve içerik servisine gider | Application | İstek + DTO |
| 5 | Uç servis kaydı `ServiceRegistry` üzerinden çözülür | Application | Servis adresi |
| 6 | İş mantığı uygulanır (playlist, sıradaki parça) | Domain | Domain Event |
| 7 | Kaynak bulunur (dosya / akış) | Infrastructure | Byte akışı |
| 8 | DSP zinciri işletilir (EQ → crossover → limiter) | Domain | 32-bit float örnekler |
| 9 | Sürücü adaptörü örneği donanıma iletir | Hardware | ASIO / WASAPI / ALSA tamponu |
| 10 | Donanım çıkışı üretir; ölçüm geri beslemesi toplanır | Hardware | Analog çıkış + telemetri |

**Kesim noktası (dürüstlük):** 1–6. adımlar repo'da kod kanıtıyla desteklenir (`## 14`); 7–10. adımlar **vault** (`[[electronic/audio-architecture]]`, `[[dsp/index]]`, `[[drivers/index]]`) kaynaklıdır ve repo'da karşılığı **YOK**.

---

## 14. Repoya Dayalı Uygulama Kanıtları

Her kanıt repo diskinden, satır numarası `Select-Object -Index (N-1)` ile birebir doğrulanmıştır.

**Kanıt 1 — Arayüz ayrımı gerçek kodda mevcut (Domain'in tanımladığı sözleşme)**

`Dosya: shared/src/Contracts/Api/ServiceRegistryInterface.php` · `Satır: 17`

```php
interface ServiceRegistryInterface
```

**Kanıt 2 — Arayüzü uygulayan somut sınıf (Dependency Inversion karşılığı)**

`Dosya: shared/src/Api/Registry/ServiceRegistry.php` · `Satır: 19`

```php
final class ServiceRegistry implements ServiceRegistryInterface
```

**Kanıt 3 — Middleware zinciri tek sorumluluklu sınıflarla kurulmuş**

`Dosya: shared/src/Middleware/MiddlewarePipeline.php` · `Satır: 8`

```php
final class MiddlewarePipeline
```

**Kanıt 4 — HTTP middleware ayrı bir pipeline olarak ayrıştırılmış**

`Dosya: shared/src/Api/Middleware/ApiMiddlewarePipeline.php` · `Satır: 17`

```php
final class ApiMiddlewarePipeline
```

**Kanıt 5 — BFF katmanı Presentation/Application sınırını temsil ediyor**

`Dosya: shared/src/Api/Bff/BffLayer.php` · `Satır: 19`

```php
final class BffLayer
```

**Kanıt 6 — Gömülü BFF, ikinci bir uygulama katmanı olarak var**

`Dosya: shared/src/Api/Bff/EmbeddedBff.php` · `Satır: 19`

```php
class EmbeddedBff
```

**Kanıt 7 — Bağımlılık enjeksiyonu constructor üzerinden `readonly` yapılıyor**

`Dosya: shared/src/Api/Gateway.php` · `Satır: 30`

```php
private readonly ServiceRegistryInterface $serviceRegistry,
```

**Kanıt 8 — Sabit koleksiyonlar `private const` ile sınırlandırılmış**

`Dosya: shared/src/AI/KnowledgeBase.php` · `Satır: 25`

```php
private const CATEGORIES = ['adr', 'architecture', 'security', 'audio', 'hardware', 'workflow', 'template'];
```

**Yorum:** Kanıt 1–2 §11'deki arayüz/uygulama ayrımının, Kanıt 3–5 §2'deki katman ayrımının, Kanıt 7 §5'teki DI kuralının repo karşılığını gösterir. Buna karşılık **14 modülün yalnızca bir kısmı repoda mevcuttur**; `Driver Manager`, `DSP Manager`, `Update Manager` için repo kodu bulunmaz.

---

## 15. Hata Yönetimi ve Geri Kazanım

| Durum | Katman Davranışı | Geri Kazanım |
|-------|------------------|--------------|
| Kullanıcı girdisi geçersiz | Presentation reddeder, Domain'e ulaşmaz | Kullanıcıya hata görseli |
| Uç servis yanıt vermiyor | Application timeout uygular | 3 deneme → fallback servis |
| Domain kuralı ihlali | Domain, hata nesnesi (Domain Failure) döner | Application failure'ı yorumlar |
| Repository erişim hatası | Infrastructure exception'ı sarar | Kuyruğa yaz, sonra yeniden dene |
| DSP aşırı seviye | Domain limiter devreye alır | Seviye düşürme + olay yayını |
| Sürücü kaybı (ASIO device loss) | Hardware adaptörü fallback'e geçer | WASAPI fallback — `[[electronic/operating-system-architecture]]` |
| Firmware güncelleme başarısız | Update Manager rollback ister | `[[firmware/ota-update]]` rollback akışı |
| Diagnostics: health check başarısız | Infrastructure sinyal yayınlar | Monitoring uyarısı |

**Kural:** Hata mesajı **asla** donanım register değerini veya ham buffer içeriğini kullanıcı katmanına geçirmez; Domain Failure taşıdığı alanlar sabittir.

---

## 16. Performans ve Gerçek Zamanlı Kısıtlar

| Kısıt | Değer | Dayanak | Durum |
|-------|-------|---------|-------|
| Ses veri tipi | 32-bit float | ADR-017 | Bağlayıcı |
| Buffer / örnekleme hedefi | 512 sample · 48 kHz | Şartname | `⚠️ VERIFICATION REQUIRED` (ölçülmedi) |
| Audio thread tahsisi | `malloc` / `new` / `delete` yasak | L0 kuralı | Bağlayıcı |
| Audio thread kilidi | `mutex` / `lock_guard` yasak | L0 kuralı | Bağlayıcı |
| Callback imzası | `noexcept` zorunlu | L0 kuralı | Bağlayıcı |
| Cache hizası | `alignas(64)` | L0 kuralı | Bağlayıcı |
| Compile-time hesap | `constexpr` tercih edilir | L0 kuralı | Tercih |
| ASIO Exclusive Lock | Tek uygulama, çakışma = 0 | L0 kuralı | Bağlayıcı |

**Web katmanı ile DSP katmanı arasındaki tip farkı:** Web tarafında PHP `float` varsayılan olarak çift duyarlıklı gerçel sayıdır; DSP zincirinde ise ADR-017 gereği 32-bit float kullanılır. İki taraf arasındaki her değer sınırında **açık dönüştürme** yapılır; sessiz kayıp kabul edilmez.

---

## 17. Test ve Doğrulama Stratejisi

| Seviye | Ne Test Edilir | Araç | Kabul |
|--------|----------------|------|-------|
| Unit | Domain kuralları, Value Object'ler | PHPUnit | Kapsam ≥%80 (hedef) |
| Unit | Arayüz implementasyon eşleşmesi | PHPUnit | Tüm implementasyonlar testte |
| Integration | Repository + veri erişimi | PHPUnit | Hazırlanmış ifadeler, `SELECT *` yok |
| Integration | Middleware pipeline sıralaması | PHPUnit | Sıra değişimi testte kırmızı |
| Contract | Katmanlar arası arayüz sözleşmesi | PHPUnit | §11 tablosunun tamamı |
| Static | Tip ve kural denetimi | PHPStan | Kayıt: `.ai` kural dosyaları |
| E2E | Oynatma akışı §13 | Playwright | Akış 1–6 hatasız |
| Ölçüm | Gerçek zamanlı callback süresi | Gerçek ASIO cihazı | `⚠️ VERIFICATION REQUIRED` — yapılmadı |

**Test edilmeyen yüzey (açık beyan):** Donanım tarafındaki sürücü, firmware ve amplifier koruma döngüleri bu depoda test edilemez — repo'da karşılık kod yoktur (`## 18`).

---

## 18. Kapsam Sınırı ve Dürüstlük Beyanı

| Konu | Durum |
|------|-------|
| Bu dosyadaki 5 katman, 14 modül, 8 plugin | Vault tasarım belgesi — repo'da birebir karşılığı **zorunlu değil** |
| Repo'da electronics/devre/firmware kaynak kodu | **YOK** (CoreMusic = PHP 8.x + JS web platformu) |
| `## 13` akışının 1–6. adımları | Repo kod kanıtlıdır (`## 14`) |
| `## 13` akışının 7–10. adımları | Vault kaynaklıdır, repo karşılığı **YOK** |
| PSR standartları | PHP-FIG tarafından yayımlanır — `## 19` |
| Ölçülen performans değeri (gecikme, CPU) | `UNKNOWN` — ölçüm yapılmadı |
| JUCE 9 · ASIO SDK 2.3.4 sürüm kanıtı | `⚠️ VERIFICATION REQUIRED` |

**Yasak yüzeyler:** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · `.ai/electronic/{amplifier,hardware,drivers,dsp}/**` · `firmware/AGENTS.md` · `firmware/CLAUDE.md` · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql` (okunur, yazılmaz).

---

## 19. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| PSR standartlarının yayıncısı ve kapsamı PHP-FIG'dir | https://www.php-fig.org/ | 2026-10-13 | HTTP 200 ✓ |
| PHP `float` tipi IEEE 754 çift duyarlıklı gerçel sayıdır | https://www.php.net/manual/en/language.types.float.php | 2026-10-13 | HTTP 200 ✓ |

---

## 20. Riskler ve Açık Sorular

| # | Soru | Durum | Sonraki Adım |
|---|------|-------|--------------|
| 1 | 14 modülün kaç repoda gerçekten uygulandığı | `UNKNOWN` (kısmi okuma yapıldı) | Modül→dizin eşlemesi |
| 2 | `## 11` arayüz adlarının repo tip adlarıyla eşleşip eşleşmediği | `⚠️ VERIFICATION REQUIRED` | Depo tip taraması |
| 3 | Gerçek callback süresi ve jitter ölçümü | `⚠️ VERIFICATION REQUIRED` | ASIO cihazında ölçüm |
| 4 | Test kapsamının gerçek yüzdesi | `UNKNOWN` | PHPUnit kapsam raporu |
| 5 | Plugin arayüzünün (§6.1) repo'da uygulanması | `UNKNOWN` | `PluginInterface` araması |
| 6 | `[[electronic/service-architecture]]` içindeki 13 servis ile §4'teki 14 modülün eşleşmesi | `⚠️ VERIFICATION REQUIRED` | İki dosyanın çapraz okuması |

---

## 21. Terim Sözlüğü (Bu Dosyaya Özgü)

| Terim | Tanım | İlgili Bölüm |
|-------|-------|--------------|
| Domain Layer | İş mantığının yaşadığı, dış bağımlılıktan arındırılmış çekirdek katman | `## 3` |
| Value Object | Kimliksiz, değişmez, eşitliği değerine göre belirlenen nesne | `## 3.1` |
| Aggregate | Tutarlılık sınırı belirleyen nesne kümesi | `## 3.1` |
| Domain Event | Domain'de gerçekleşen, kayıtlı ve yayılabilir olgu | `## 3.1` |
| DIP | Dependency Inversion Principle — detaylar soyutluğa bağımlıdır | `## 5` |
| Dependency Injection | Bağımlılığın dışarıdan, kurucu üzerinden verilmesi | `## 14` (Kanıt 7) |
| Interface Segregation | İstemci yalnızca kullandığı arayüzü görür | `## 11` |
| BFF | Backend For Frontend — sunum katmanına özel elçi | `## 14` (Kanıt 5) |
| Middleware Pipeline | İstek/yanıt üzerinde sırayla çalışan katman zinciri | `## 14` (Kanıt 3) |
| Repository Pattern | Veri erişimini arayüz arkasına gizleyen desen | `## 7.2` |
| DI Container | Nesne üretimini dışarıdan yöneten konteyner | `## 4` (Modül 1) |
| Plugin Sağlayıcısı | Harici entegrasyonu arayüz üzerinden takan birim | `## 6` |
| Domain Failure | Hata nesnesi — istisna değil, dönen değer olarak taşınır | `## 15` |
| Fallback | Ana yol başarısızken devreye giren ikinci yol | `## 15` |
| Health Check | Sistem bileşeninin canlılık/uygunluk sinyali | `## 15` |
| Zero-allocation | Ses iş parçacığında tahsis yasağı | `## 16` |
| Lock-free | Ses iş parçacığında kilit yasağı | `## 16` |
| `noexcept` | Çağrı hiçbir istisna yaymaz sözleşmesi | `## 16` |
| `alignas(64)` | Cache satırı hizalaması | `## 16` |
| `constexpr` | Derleme zamanında hesaplama anahtarı | `## 16` |
| ASIO Exclusive Lock | Ses aygıtının tek uygulama tarafından ele geçirilmesi | `## 16` |
| 32-bit float | ADR-017 gereği ses verisinin taşıdığı tip | `## 16` |
| Kesim noktası | Vault iddiası ile repo kanıtının ayrıldığı sınır | `## 13` |
| Olgunluk | Dosyanın içerik derinliği — ölçüm ile belirlenir | `[[electronic/index]]` §13 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
