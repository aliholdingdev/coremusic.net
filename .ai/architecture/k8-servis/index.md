---
title: "K8 Servis Katmanı - Genel Bakış"
layer: K8
category: "Servis"
date: 2026-09-20
version: "1.0.0"
status: "implemented"
---

# K8 Servis Katmanı

## Genel Bakış

K8 Servis Katmanı, COREMUSIC mimarisinde uygulama mantığını barındıran merkezi iş katmanıdır. Tüm iş süreçleri, veri doğrulama ve iş mantığı bu katmanda gerçekleştirilir. Servisler birbirleriyle dependency injection ve event-driven patterns ile etkileşime girer.

Servis katmanı, presentation katmanından (K14-K20) gelen istekleri işler, repository katmanına (K7) veri erişimini sağlar ve cross-cutting concerns'leri (logging, caching, security) orchestrate eder. Her servis tek bir sorumluluk alanına sahip olup single responsibility principle'a uygun olarak tasarlanmıştır.

## Mimari Konum

```
┌─────────────────────────────────────────────────────┐
│  K14-K20 Presentation (UI/CLI/Web/Mobile)           │
├─────────────────────────────────────────────────────┤
│  K8 SERVİS KATMANI                                  │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐         │
│  │ Control   │ │ Media     │ │ Audio     │         │
│  │ Service   │ │ Service   │ │ Service   │         │
│  └───────────┘ └───────────┘ └───────────┘         │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐         │
│  │ Device    │ │ Network   │ │ AI        │         │
│  │ Service   │ │ Service   │ │ Service   │         │
│  └───────────┘ └───────────┘ └───────────┘         │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐         │
│  │ Download  │ │ Health    │ │ Search    │         │
│  │ Service   │ │ Service   │ │ Service   │         │
│  └───────────┘ └───────────┘ └───────────┘         │
│  ┌───────────┐ ┌───────────┐                        │
│  │Notif.     │ │ Sync      │                        │
│  │Service    │ │ Service   │                        │
│  └───────────┘ └───────────┘                        │
├─────────────────────────────────────────────────────┤
│  K7 Repository Katmanı (Data Access)                │
├─────────────────────────────────────────────────────┤
│  K5-K6 Infrastructure (Cache, Queue, Messaging)     │
└─────────────────────────────────────────────────────┘
```

## Servis Haritası

| Servis | Sorumluluk Alanı | Ana Entegrasyon |
|--------|-----------------|-----------------|
| Control Service | Kullanıcı yönetimi, ayarlar, sistem kontrolü | K7 Repository, K5 Cache |
| Media Service | Medya kütüphanesi, metadata, albüm kapakları | K7 Repository, K9 AI |
| Audio Service | Çalma kontrolü, kuyruk, oturum yönetimi | K3 Audio Engine, K6 Queue |
| Device Service | Cihaz keşfi, kayıt, durum takibi | K2 Driver, K6 Network |
| Network Service | DLNA, AirPlay, WebRTC, çoklu oda senkronizasyonu | K2 Driver, K6 Network |
| AI Service | Analiz, öneriler, sesli komut işleme | K9 AI Engine, K7 Repository |
| Download Service | Dosya indirme, ilerleme takibi | K5 Storage, K6 Network |
| Health Service | Sağlık kontrolleri, readiness/liveness probe | K5 Monitoring |
| Search Service | Tam metin arama, bulanık eşleştirme, filtreler | K7 Repository, K5 Cache |
| Notification Service | Push bildirimleri, uyarılar | K5 Messaging, K6 Network |
| Sync Service | Çapraz cihaz senkronizasyonu, playlist sync | K7 Repository, K6 Network |

## Servis İletişim Şekilleri

### 1. Synchronous Communication (HTTP/gRPC)
- Presentation → Service: REST API calls
- Service → Service: gRPC internal calls (low-latency)
- Service → Repository: ORM queries

### 2. Asynchronous Communication (Message Queue)
- Event publishing via RabbitMQ/Redis Streams
- Command pattern for long-running operations
- Saga pattern for distributed transactions

### 3. Event-Driven Patterns
```
Audio Service ──publish──► TrackChangedEvent ──subscribe──► AI Service
Download Service ──publish──► DownloadCompletedEvent ──subscribe──► Media Service
Device Service ──publish──► DeviceConnectedEvent ──subscribe──► Network Service
```

## Teknik Detaylar

### Service Lifecycle Management

Her servis bir `IService` arayüzüne bağlı olarak yaşar:

```cpp
// K8/include/IService.h
namespace CoreMusic::K8 {

class IService {
public:
    virtual ~IService() = default;
    virtual void initialize() = 0;
    virtual void start() = 0;
    virtual void stop() = 0;
    virtual void shutdown() = 0;
    virtual ServiceStatus getStatus() const = 0;
    virtual std::string getServiceName() const = 0;
};

enum class ServiceStatus {
    Uninitialized,
    Initializing,
    Running,
    Degraded,
    Stopping,
    Stopped,
    Error
};

} // namespace CoreMusic::K8
```

### Service Registry

Tüm servisler merkezi bir ServiceRegistry üzerinden kayıtlıdır:

```cpp
// K8/include/ServiceRegistry.h
namespace CoreMusic::K8 {

class ServiceRegistry {
public:
    static ServiceRegistry& instance();

    void registerService(const std::string& name, std::shared_ptr<IService> service);
    std::shared_ptr<IService> getService(const std::string& name);
    std::vector<std::string> getRegisteredServices() const;
    void initializeAll();
    void startAll();
    void stopAll();

private:
    ServiceRegistry() = default;
    std::unordered_map<std::string, std::shared_ptr<IService>> services_;
};

} // namespace CoreMusic::K8
```

### Dependency Injection Container

Servisler arası bağımlılıklar bir DI container üzerinden yönetilir:

```cpp
// K8/include/DIContainer.h
namespace CoreMusic::K8 {

class DIContainer {
public:
    template<typename TInterface, typename TImplementation>
    void registerSingleton() {
        static_assert(std::is_base_of_v<TInterface, TImplementation>);
        singletons_[typeid(TInterface).name()] =
            std::make_shared<TImplementation>();
    }

    template<typename TInterface>
    std::shared_ptr<TInterface> resolve() {
        auto it = singletons_.find(typeid(TInterface).name());
        if (it != singletons_.end()) {
            return std::static_pointer_cast<TInterface>(it->second);
        }
        throw std::runtime_error("Service not registered");
    }

private:
    std::unordered_map<std::string, std::shared_ptr<void>> singletons_;
};

} // namespace CoreMusic::K8
```

### Error Handling Pattern

Her servis katmanında uniform hata yönetimi uygulanır:

```cpp
// K8/include/ServiceResult.h
namespace CoreMusic::K8 {

template<typename T>
class ServiceResult {
public:
    static ServiceResult<T> success(T data) {
        return ServiceResult<T>(std::move(data), std::nullopt);
    }

    static ServiceResult<T> error(ServiceError error) {
        return ServiceResult<T>(std::nullopt, std::move(error));
    }

    bool isSuccess() const { return data_.has_value(); }
    const T& data() const { return data_.value(); }
    const ServiceError& error() const { return error_.value(); }

private:
    ServiceResult(std::optional<T> data, std::optional<ServiceError> error)
        : data_(std::move(data)), error_(std::move(error)) {}

    std::optional<T> data_;
    std::optional<ServiceError> error_;
};

struct ServiceError {
    int code;
    std::string message;
    std::string details;
    ServiceErrorCode errorCode;
};

enum class ServiceErrorCode {
    NotFound,
    Validation,
    Authentication,
    Authorization,
    Conflict,
    Internal,
    External,
    Timeout,
    RateLimited
};

} // namespace CoreMusic::K8
```

### Middleware Pipeline

Servis istekleri bir middleware pipeline'ından geçer:

```cpp
// K8/include/MiddlewarePipeline.h
namespace CoreMusic::K8 {

class MiddlewarePipeline {
public:
    using MiddlewareFunc = std::function<ServiceResult<ServiceResponse>(
        const ServiceRequest&, std::function<ServiceResult<ServiceResponse>(
            const ServiceRequest&)> next)>;

    void use(MiddlewareFunc middleware);
    ServiceResult<ServiceResponse> execute(const ServiceRequest& request);

private:
    std::vector<MiddlewareFunc> middlewares_;
};

} // namespace CoreMusic::K8
```

### Configuration Management

Her servis kendi config yapısını alır:

```yaml
# config/k8-services.yaml
services:
  control-service:
    port: 8081
    timeout: 30s
    maxConcurrentRequests: 100
    cache:
      enabled: true
      ttl: 300s

  media-service:
    port: 8082
    timeout: 60s
    maxConcurrentRequests: 50
    storage:
      artworkPath: /var/coremusic/artworks
      metadataCache: true

  audio-service:
    port: 8083
    timeout: 10s
    maxConcurrentRequests: 200
    engine:
      bufferSize: 1024
      sampleRate: 48000

  network-service:
    port: 8084
    timeout: 15s
    dlna:
      enabled: true
      friendlyName: "COREMUSIC"
    airplay:
      enabled: true
      deviceName: "COREMUSIC Speaker"

  ai-service:
    port: 8085
    timeout: 120s
    maxConcurrentRequests: 10
    models:
      musicAnalysis: "coremusic-analyzer-v2"
      recommendation: "coremusic-recommender-v1"
      voiceProcessing: "coremusic-voice-v1"
```

## Performance Metrics

| Metrik | Hedef | Açıklama |
|--------|-------|----------|
| Response Latency (p50) | < 10ms | Servis yanıt süresi |
| Response Latency (p99) | < 50ms | Servis yanıt süresi |
| Throughput | > 1000 req/s | Saniye başına istek |
| Error Rate | < 0.1% | Hata oranı |
| Availability | 99.99% | Servis kullanılabilirlik |
| Memory Usage | < 512MB | Servis bellek kullanımı |

## Güvenlik Mimarisi

- **Authentication**: JWT token-based auth (K5 Security)
- **Authorization**: Role-based access control (RBAC)
- **Rate Limiting**: Token bucket algorithm
- **Input Validation**: Schema-based validation
- **Encryption**: TLS 1.3 for inter-service communication
- **Audit Logging**: All service calls logged

## Monitoring ve Observability

- **Metrics**: Prometheus-compatible metrics endpoint
- **Logging**: Structured JSON logging with correlation IDs
- **Tracing**: OpenTelemetry distributed tracing
- **Health Checks**: Liveness and readiness probes
- **Alerting**: Rule-based alerting via Grafana

## Testing Stratejisi

- **Unit Tests**: Her servis için bağımsız unit testler
- **Integration Tests**: Servisler arası entegrasyon testleri
- **Contract Tests**: API contract testleri (Pact)
- **Load Tests**: Stres ve yük testleri (k6)
- **Chaos Tests**: Fault injection testleri

## Bağımlılıklar

| Katman | Bağımlılık | Açıklama |
|--------|-----------|----------|
| K5 | Infrastructure | Cache, Queue, Security, Monitoring |
| K6 | Network | Network abstractions, protocols |
| K7 | Repository | Data access, ORM, queries |
| K9 | AI | AI engine, ML models |

## Roadmap

1. **Faz 1**: Control, Media, Audio servisleri (Core)
2. **Faz 2**: Device, Network, AI servisleri (Connectivity)
3. **Faz 3**: Download, Health, Search servisleri (Utility)
4. **Faz 4**: Notification, Sync servisleri (Enhancement)

## Durum: Implementasyon

- **Faz 1**: ✅ Tamamlandı
- **Faz 2**: 🔄 Devam ediyor
- **Faz 3**: ⏳ Beklemede
- **Faz 4**: ⏳ Beklemede
