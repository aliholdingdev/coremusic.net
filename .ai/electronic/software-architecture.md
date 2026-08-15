---
type: system
category: electronics-software
title: "CoreMusic Electronics Software Architecture"
date: 2026-08-09
updated: 2026-08-10
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

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
