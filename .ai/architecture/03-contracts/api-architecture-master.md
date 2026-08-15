---
type: architecture
category: contracts
title: "API Architecture Master — Enterprise API-First Design"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Architecture Master

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]] · [[ADR-084-api-gateway-architecture]] · [[ADR-087-master-implementation-plan]]

## 1. Amaç

CoreMusic ekosisteminin tüm API mimarisini, servis iletişimini, BFF yapısını, CQRS desenini ve Event Driven Architecture'yı tek bir noktadan tanımlayan **Ana API Mimari Referans**dır.

## 2. CoreMusic API Nedir?

CoreMusic API, klasik REST servisi değildir. Sistemin **sinir sistemi** olarak çalışan merkezi iletişim omurgasıdır. Tüm istemciler, servisler, ses motoru, AI bileşenleri ve gömülü sistemler yalnızca bu API üzerinden haberleşir.

**Temel Prensipler:**
- **API-First:** Kod değil, önce sözleşme yazılır
- **Service-Based:** Tek API yok, servis bazlı ayrışma
- **BFF Pattern:** Her istemci tipi için ayrı Backend
- **CQRS:** Okuma ve yazma tamamen ayrışık
- **Event Driven:** Servisler birbirini doğrudan çağırmaz
- **Contract First:** OpenAPI → DTO → Contract → Validation → Kod

## 3. API Gateway

### 3.1 Gateway Mimarisi

```
                        INTERNET
                            │
                            ▼
                    ┌───────────────┐
                    │  API GATEWAY  │
                    │  (Merkezi)    │
                    └───────┬───────┘
                            │
         ┌──────────────────┼──────────────────┐
         │                  │                  │
         ▼                  ▼                  ▼
   Public API          Internal API        WebSocket API
         │                  │                  │
         └──────────────────┼──────────────────┘
                            │
              ┌─────────────┼─────────────────┐
              │             │                 │
              ▼             ▼                 ▼
         Auth API      Media API        Audio API
         User API      Music API        Player API
         Admin API     Download API     DSP API
         Search API    Library API      Notification API
         AI API        Analytics API    System API
```

### 3.2 Gateway Sorumlulukları

| Görev | Açıklama | Katman |
|-------|----------|--------|
| **Routing** | İsteği doğru servise yönlendirme | L2 |
| **Authentication** | JWT/Session/Token doğrulama | L1 |
| **Authorization** | RBAC + Permission kontrolü | L1 |
| **Rate Limiting** | IP/User bazlı hız kısıtlaması | L1 |
| **Request Validation** | Body, Query, Header doğrulama | L1 |
| **Response Normalization** | Standart response formatı | L2 |
| **Versioning** | API sürüm yönetimi | L2 |
| **Logging** | Tüm isteklerin loglanması | L0 |
| **Correlation ID** | İstek takibi | L0 |
| **Service Discovery** | Servis bulma ve yönlendirme | L0 |

### 3.3 Gateway Akışı

```
Client Request
    │
    ▼
API Gateway
    │
    ├── 1. Correlation ID ata
    ├── 2. Origin/CORS kontrolü
    ├── 3. Rate Limit kontrolü
    ├── 4. Authentication (JWT/Session)
    ├── 5. Authorization (RBAC)
    ├── 6. Request Validation
    ├── 7. Route to Service
    │
    ▼
Service Handler
    │
    ├── 1. Use Case çalıştır
    ├── 2. Domain mantığını uygula
    ├── 3. Repository ile veri erişimi
    │
    ▼
Response
    │
    ├── 1. Response Normalization
    ├── 2. Audit Log
    ├── 3. Rate Limit Headers
    │
    ▼
Client
```

## 4. Servis Bazlı API Yapısı

### 4.1 Servis Haritası

| Servis | Subdomain | Port | Tip | Açıklama |
|--------|-----------|------|-----|----------|
| **Auth API** | auth.coremusic.net | 80 | Public + Internal | Kimlik doğrulama, token yönetimi |
| **User API** | auth.coremusic.net | 80 | Public | Kullanıcı profilleri, tercihler |
| **Music API** | api.coremusic.net | 81 | Public | Müzik kataloğu, arama |
| **Media API** | media.coremusic.net | 5000 | Internal | Medya depolama, streaming |
| **Playlist API** | api.coremusic.net | 81 | Public | Çalma listesi yönetimi |
| **Album API** | api.coremusic.net | 81 | Public | Albüm yönetimi |
| **Artist API** | api.coremusic.net | 81 | Public | Sanatçı yönetimi |
| **Download API** | download.coremusic.net | 3001 | Public + Internal | İndirme kuyruğu |
| **Search API** | api.coremusic.net | 81 | Public | Tam metin arama |
| **Library API** | api.coremusic.net | 81 | Public | Kütüphane yönetimi |
| **Player API** | api.coremusic.net | 81 | Public | Oynatma kontrolleri |
| **Audio API** | audio.coremusic.net | 9741 | Internal | Ses motoru, DSP |
| **DSP API** | audio.coremusic.net | 9741 | Internal | EQ, efektler |
| **Notification API** | api.coremusic.net | 81 | Internal | Bildirim yönetimi |
| **Analytics API** | api.coremusic.net | 81 | Internal | İstatistikler |
| **Admin API** | admin.coremusic.net | 80 | Admin | Yönetim paneli API |
| **AI API** | api.coremusic.net | 81 | Internal | Öneri motoru |
| **System API** | api.coremusic.net | 81 | Internal | Sağlık kontrolü, konfigürasyon |

### 4.2 API Tipi Ayrımı

```
┌─────────────────────────────────────────────────────────────┐
│                     API TIPLERI                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  PUBLIC API                                                  │
│  ├── Mobil uygulamalar                                       │
│  ├── Web istemcileri (SPA)                                   │
│  ├── Masaüstü uygulamaları                                   │
│  ├── Üçüncü taraf geliştiriciler                             │
│  ├── SDK'lar                                                 │
│  └── Webhook'lar                                             │
│                                                              │
│  INTERNAL API                                                │
│  ├── SPA Router (coremusic panels)                          │
│  ├── Background Workers                                      │
│  ├── Queue Consumers                                         │
│  ├── Cron Jobs                                               │
│  ├── Download Worker                                         │
│  ├── Media Processor                                         │
│  ├── Auth Service (inter-service)                            │
│  ├── Neva Engine (C++ IPC)                                   │
│  ├── WebSocket Handlers                                      │
│  └── CLI Commands                                            │
│                                                              │
│  ADMIN API                                                   │
│  ├── Kullanıcı yönetimi                                      │
│  ├── İçerik yönetimi                                         │
│  ├── Sistem konfigürasyonu                                   │
│  ├── Monitoring                                              │
│  └── Audit Trail                                             │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## 5. BFF (Backend for Frontend)

### 5.1 BFF Neden Gerekli?

Her istemci tipinin farklı ihtiyaçları vardır:
- **SPA:** Tam veri, tüm alanlar
- **Mobile:** Küçük paket, sadece gerekli alanlar
- **Embedded (RPi5):** Minimal JSON, düşük kaynak
- **Desktop:** Orta boy veri, gelişmiş özellikler

### 5.2 BFF Yapısı

```
┌─────────────────────────────────────────────────────────────┐
│                    BFF LAYER                                  │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  SPA BFF                                                     │
│  ├── Tam response body                                       │
│  ├── Tüm alanlar dahil                                       │
│  ├── Pagination detaylı                                      │
│  └── WebSocket desteği                                       │
│                                                              │
│  Mobile BFF                                                  │
│  ├── Minimal response body                                   │
│  ├── Sadece gerekli alanlar                                  │
│  ├── Daha küçük sayfa boyutları                              │
│  └── Offline cache headers                                   │
│                                                              │
│  Embedded BFF (RPi5)                                         │
│  ├── Ultra-minimal JSON                                      │
│  ├── Sadece ID + Name + Status                               │
│  ├── Düşük bandwidth optimizasyonu                           │
│  └── gzip zorunlu                                            │
│                                                              │
│  Desktop BFF                                                 │
│  ├── Orta boy response                                       │
│  ├── Gelişmiş metadata                                       │
│  ├── Batch endpoint desteği                                  │
│  └── WebSocket + SSE desteği                                 │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 5.3 BFF Implementasyonu

```
Client
    │
    ▼
BFF Layer (Router'da eşleme)
    │
    ├── SPA → /api/v1/spa/songs?fields=id,title,artist,album,duration,cover_url
    ├── Mobile → /api/v1/mobile/songs?fields=id,title,artist,duration
    ├── Embedded → /api/v1/embedded/songs?fields=id,title
    └── Desktop → /api/v1/desktop/songs?fields=id,title,artist,album,duration,cover_url,bitrate,format
    │
    ▼
Internal API (tam veri)
    │
    ▼
Service Layer
```

## 6. CQRS (Command Query Responsibility Segregation)

### 6.1 CQRS Neden?

Okuma ve yazma işlemleri farklı kaynaklar kullanır:
- **Write:** Master Database (MySQL)
- **Read:** Cache (Redis/APCu) veya Read Replica

### 6.2 CQRS Akışı

```
┌─────────────────────────────────────────────────────────────┐
│                    CQRS PATTERN                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  COMMAND SIDE (Yazma)                                        │
│  ├── Controller → Command Handler → Use Case                │
│  ├── Domain Entity → Repository Interface                   │
│  ├── Repository Impl → PDO → MySQL Master                   │
│  └── Domain Event yayını                                    │
│                                                              │
│  ─────────────────────────────────────────────────────────   │
│                                                              │
│  QUERY SIDE (Okuma)                                          │
│  ├── Controller → Query Handler → Use Case                  │
│  ├── Read Model → Cache Interface                           │
│  ├── Cache Impl → Redis/APCu/File                           │
│  └── Response DTO                                           │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 6.3 CQRS Implementasyonu

```php
// COMMAND
class CreatePlaylistCommand
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly ?string $description = null,
    ) {}
}

class CreatePlaylistHandler
{
    public function __construct(
        private PlaylistRepositoryInterface $repository,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function handle(CreatePlaylistCommand $command): PlaylistDTO
    {
        $playlist = Playlist::create($command->userId, $command->name, $command->description);
        $this->repository->save($playlist);
        $this->eventDispatcher->dispatch(new PlaylistCreatedEvent($playlist));
        return PlaylistDTO::fromEntity($playlist);
    }
}

// QUERY
class GetPlaylistQuery
{
    public function __construct(
        public readonly int $playlistId,
        public readonly int $userId,
    ) {}
}

class GetPlaylistHandler
{
    public function __construct(
        private PlaylistReadRepositoryInterface $readRepository,
    ) {}

    public function handle(GetPlaylistQuery $query): ?PlaylistDTO
    {
        return $this->readRepository->findById($query->playlistId, $query->userId);
    }
}
```

## 7. Event Driven Architecture

### 7.1 Event Akışı

```
┌─────────────────────────────────────────────────────────────┐
│                    EVENT DRIVEN ARCHITECTURE                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Service A (Publisher)                                       │
│  │                                                           │
│  ├── Domain Event oluştur                                    │
│  │  例: MusicAddedEvent { musicId, userId, genre }           │
│  │                                                           │
│  ├── Event Bus'a yayınla                                     │
│  │                                                           │
│  ▼                                                           │
│  Event Bus (PSR-14)                                          │
│  │                                                           │
│  ├── Service B (Subscriber) → Search Index güncelle          │
│  ├── Service C (Subscriber) → AI Öneri güncelle              │
│  ├── Service D (Subscriber) → Notification gönder            │
│  └── Service E (Subscriber) → Analytics güncelle             │
│                                                              │
│  Hiçbir servis diğerini doğrudan çağırmaz!                   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 7.2 Event Kataloğu

| Event | Publisher | Subscriber'lar | Açıklama |
|-------|-----------|----------------|----------|
| `UserRegistered` | Auth Service | Notification, Analytics, AI | Yeni kullanıcı |
| `UserLoggedIn` | Auth Service | Analytics, Security | Giriş olayı |
| `MusicAdded` | Media Service | Search, AI, Analytics, Notification | Yeni müzik eklendi |
| `MusicPlayed` | Player Service | Analytics, AI, History | Müzik çalındı |
| `PlaylistCreated` | Playlist Service | Analytics | Yeni çalma listesi |
| `DownloadStarted` | Download Service | Notification, Analytics | İndirme başladı |
| `DownloadCompleted` | Download Service | Media, Search, Notification | İndirme tamamlandı |
| `MediaEncoded` | Media Service | Search, Thumbnail | Encode tamamlandı |
| `DeviceConnected` | Device Service | Player, Notification | Cihaz bağlandı |
| `EqPresetChanged` | Audio Service | Analytics | EQ ayarı değişti |

## 8. Modüler Shared Library (`coremusic/*`)

### 8.1 Paket Bölünmesi

```
coremusic/
├── contracts/          ← DTO, Request, Response, Enums, ValueObjects
├── http/               ← HttpClient, ApiClient, RetryPolicy, CircuitBreaker
├── auth/               ← Auth Client, JWT, OAuth, Permission
├── security/           ← Cryptography, RateLimiter, CSRF
├── cache/              ← Cache Interface (Redis/APCu/File)
├── events/             ← Event Dispatcher, Event Bus
├── openapi/            ← OpenAPI Generator, Swagger
├── sdk/                ← Client SDK Generator
├── logger/             ← PSR-3 Logger Wrapper
├── support/            ← Helpers, Validators, Serializers
├── validation/         ← Request Validation Rules
├── queue/              ← Queue Interface (Redis/Database)
├── storage/            ← Storage Interface (Local/NAS/S3)
├── config/             ← Configuration Management
├── monitoring/         ← Metrics, Tracing, Health Check
├── testing/            ← Test Helpers, Fixtures, Mocks
├── api-client/         ← Typed API Client
├── websocket/          ← WebSocket Client/Server
└── observability/      ← Correlation ID, Audit Trail
```

### 8.2 Paket Bağımlılık Kuralları

```
contracts ← (hiçbir bağımlılık yok — en alt katman)
    ↑
http ← contracts
    ↑
auth ← contracts, http
    ↑
security ← contracts
    ↑
cache ← contracts
    ↑
events ← contracts
    ↑
validation ← contracts
    ↑
queue ← contracts
    ↑
storage ← contracts
    ↑
monitoring ← contracts
    ↑
logger ← (PSR-3)
    ↑
config ← contracts
    ↑
support ← contracts
    ↑
api-client ← http, auth, contracts
    ↑
sdk ← api-client, contracts
```

## 9. Katman Mimarisi (Enterprise)

```
┌─────────────────────────────────────────────────────────────┐
│                 CLIENT LAYER (L3)                            │
├─────────────────────────────────────────────────────────────┤
│  SPA · Mobile · Desktop · Embedded · Third Party · SDK      │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 BFF LAYER                                    │
├─────────────────────────────────────────────────────────────┤
│  SPA BFF · Mobile BFF · Embedded BFF · Desktop BFF          │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 API GATEWAY (L2)                             │
├─────────────────────────────────────────────────────────────┤
│  Routing · Versioning · Authentication · Authorization      │
│  Rate Limit · Request Validation · Response Normalization   │
│  Correlation ID · Service Discovery · Audit Log             │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 MIDDLEWARE PIPELINE (L1)                     │
├─────────────────────────────────────────────────────────────┤
│  Origin Check · CORS · Rate Limit · Security Headers        │
│  Session · CSRF · Auth · RBAC · Validation                  │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 APPLICATION LAYER                            │
├─────────────────────────────────────────────────────────────┤
│  Use Cases · Command Handlers · Query Handlers              │
│  CQRS · DTO Mapping · Event Dispatch                        │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 DOMAIN LAYER                                 │
├─────────────────────────────────────────────────────────────┤
│  Entities · Value Objects · Domain Services                 │
│  Business Rules · Domain Events · Repository Contracts      │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                 INFRASTRUCTURE LAYER (L0)                    │
├─────────────────────────────────────────────────────────────┤
│  PDO MySQL · Redis · Filesystem · FFmpeg                    │
│  External APIs · Queue · Cache · Storage                    │
└─────────────────────────────────────────────────────────────┘
```

## 10. Yasaklar

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| SPA → PDO | SPA → ApiClient → Gateway |
| Controller → Repository | Controller → Use Case → Repository |
| Service → Service DB | Service → API Call |
| Tek monolitik shared | Modüler `coremusic/*` paketler |
| Kod öncesi yazma | Contract First (OpenAPI) |
| `SELECT *` | Explicit column list |
| ORM | PDO prepared statement |
| Hardcoded secrets | `.env` / credential vault |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | API-First: Önce sözleşme, sonra kod | Kod revert edilir |
| 2 | BFF Pattern: Her istemci kendi DTO'sunu alır | Gereksiz veri transferi |
| 3 | CQRS: Read/Write ayrışık | Performans düşüklüğü |
| 4 | Event Driven: Servisler birbirini çağırmaz | Bağımlılık artışı |
| 5 | Gateway: Tek giriş noktası | Güvenlik açığı |
| 6 | Contract First: OpenAPI önce | Uyumsuzluk riski |
| 7 | Modüler Shared: `coremusic/*` paketler | Kod tekrarı |
| 8 | Domain bağımsız: Altyapıyı bilmez | Teknoloji bağımlılığı |

## 12. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[api-design-rules]] | Tasarım kuralları |
| [[api-versioning]] | Sürüm yönetimi |
| [[api-security]] | Güvenlik katmanı |
| [[api-authentication]] | Kimlik doğrulama |
| [[api-error-codes]] | Hata kodları |
| [[api-event-system]] | Olay sistemi |
| [[api-websocket]] | Gerçek zamanlı iletişim |
| [[api-rate-limit]] | Hız kısıtlaması |
| [[api-pagination]] | Sayfalama |
| [[api-filtering]] | Filtreleme |
| [[api-validation]] | Doğrulama |
| [[api-idempotency]] | İdempotency |
| [[api-internal-contract]] | İç servis sözleşmesi |
| [[api-public-contract]] | Dış API sözleşmesi |
| [[api-sdk]] | SDK üretimi |
| [[api-testing]] | Test stratejisi |
| [[api-observability]] | Gözlemlenebilirlik |
| [[api-roadmap]] | Yol haritası |
| [[middleware-pipeline]] | Middleware detayı |
| [[shared-library]] | Shared library |

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır Sayısı** | ~350 |
| **ADR Uyumlu** | ✅ 001, 002, 007, 042, 051, 053, 054 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
