---
type: architecture
category: contracts
title: "API Event System — Event Driven Architecture"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Event System — Event Driven Architecture

**Zorunlu Bağlantılar:** [[api-architecture-master]]

---

## 1. Amaç

CoreMusic platformundaki tüm modüller arası iletişimi sağlayan Event Driven Architecture (EDA) standartlarını tanımlar. PSR-14 Event Dispatcher tabanlı publisher/subscriber modeli ile gevşek bağlı (loosely coupled) sistem tasarımı hedeflenir.

---

## 2. Temel İlkeler

| İlke | Açıklama |
|------|----------|
| Loose Coupling | Event producer'lar consumer'lardan bağımsızdır |
| Event Sourcing | Tüm state değişiklikleri event olarak kaydedilir |
| Single Responsibility | Her event tek bir domain olayını temsil eder |
| Immutability | Event payload'ları değiştirilemez (immutable) |
| Idempotency | Consumer'lar aynı event'i teke işleyebilmeli |

---

## 3. PSR-14 Event Dispatcher

| Bileşen | Sorumluluk |
|---------|------------|
| `EventDispatcherInterface` | Event'leri publish eder, subscriber'ları çağırır |
| `ListenerProviderInterface` | Subscriber'ları event türüne göre kaydeder |
| `CallableListener` | Dinleme fonksiyonu (closure veya callable) |

```php
use Psr\EventDispatcher\EventDispatcherInterface;

final class MusicEventDispatcher
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    ) {}

    public function dispatch(object $event): object
    {
        return $this->dispatcher->dispatch($event);
    }
}
```

---

## 4. Event Kataloğu

| Event Adı | Trigger | Payload | Domain |
|-----------|---------|---------|--------|
| `UserRegistered` | Kullanıcı kaydı | userId, email, timestamp | Auth |
| `UserLoggedIn` | Başarılı giriş | userId, sessionId, ip, timestamp | Auth |
| `UserLoggedOut` | Çıkış | userId, sessionId, timestamp | Auth |
| `MusicAdded` | Şarkı ekleme | musicId, userId, artist, title, timestamp | Music |
| `MusicPlayed` | Şarkı çalma | musicId, userId, duration, timestamp | Music |
| `MusicDeleted` | Şarkı silme | musicId, userId, timestamp | Music |
| `PlaylistCreated` | Çalma listesi oluşturma | playlistId, userId, name, timestamp | Playlist |
| `PlaylistUpdated` | Çalma listesi güncelleme | playlistId, userId, changes[], timestamp | Playlist |
| `DownloadStarted` | İndirme başlatma | downloadId, userId, source, url, timestamp | Download |
| `DownloadCompleted` | İndirme tamamlanma | downloadId, userId, filePath, format, timestamp | Download |
| `DownloadFailed` | İndirme hatası | downloadId, userId, error, timestamp | Download |
| `AlbumAdded` | Albüm ekleme | albumId, userId, artist, title, timestamp | Album |
| `SearchPerformed` | Arama | userId, query, resultCount, timestamp | Search |
| `SettingsChanged` | Ayar değişikliği | userId, setting, oldValue, newValue, timestamp | Settings |
| `SecurityAlert` | Güvenlik olayı | userId, type, severity, ip, timestamp | Security |

---

## 5. Publisher/Subscriber Pattern

### 5.1 Publisher

```php
final class MusicPublisher
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    ) {}

    public function musicAdded(Music $music, User $user): void
    {
        $event = new MusicAddedEvent(
            musicId: $music->getId(),
            userId: $user->getId(),
            artist: $music->getArtist(),
            title: $music->getTitle()
        );

        $this->dispatcher->dispatch($event);
    }
}
```

### 5.2 Subscriber

```php
final class MusicAddedSubscriber
{
    public function __invoke(MusicAddedEvent $event): void
    {
        // Loglama
        // Cache invalidation
        // Bildirim gönderme
        // Analytics kaydı
    }
}
```

### 5.3 Registration

```php
$provider->addListener(MusicAddedEvent::class, [new MusicAddedSubscriber(), '__invoke']);
$provider->addListener(MusicAddedEvent::class, [new CacheInvalidationSubscriber(), '__invoke']);
$provider->addListener(MusicAddedEvent::class, [new NotificationSubscriber(), '__invoke']);
```

---

## 6. Event Flow Diyagramı

```
Publisher (Service)
    │
    ▼
EventDispatcher::dispatch(Event)
    │
    ├──► Listener 1 (Logging)
    │
    ├──► Listener 2 (Cache Invalidation)
    │
    ├──► Listener 3 (Notification)
    │
    └──► Listener 4 (Analytics)
```

---

## 7. Async Events

| Özellik | Değer |
|---------|-------|
| Senkron Event | Hemen yürütülür (transaction içinde) |
| Asenkron Event | Kuyruğa alınır (AMQP/Redis Streams) |
| Kuyruk | Redis Streams veya RabbitMQ |
| Retry | Max 3, exponential backoff |
| Dead Letter Queue | Başarısız event'ler için ayrı kuyruk |

```php
final class AsyncEventBus
{
    public function publishAsync(object $event): void
    {
        $payload = json_encode([
            'event' => get_class($event),
            'data' => (array) $event,
            'timestamp' => time(),
            'id' => $this->generateEventId()
        ]);

        $this->redis->xAdd('events:queue', '*', ['payload' => $payload]);
    }
}
```

---

## 8. Event Payload Yapısı

```php
abstract class AbstractDomainEvent
{
    public function __construct(
        public readonly string $eventId,
        public readonly string $eventType,
        public readonly DateTimeImmutable $occurredAt,
        public readonly array $metadata = []
    ) {}
}

final class MusicAddedEvent extends AbstractDomainEvent
{
    public function __construct(
        public readonly int $musicId,
        public readonly int $userId,
        public readonly string $artist,
        public readonly string $title,
        array $metadata = []
    ) {
        parent::__construct(
            eventId: Uuid::uuid4()->toString(),
            eventType: 'music.added',
            occurredAt: new DateTimeImmutable('now', new DateTimeZone('UTC')),
            metadata: $metadata
        );
    }
}
```

---

## 9. Event Naming Convention

| Kural | Format | Örnek |
|-------|--------|-------|
| Event Adı | `PastTenseVerb + Noun` | `MusicAdded`, `UserRegistered` |
| Event Type | `domain.past-tense-verb` | `music.added`, `user.registered` |
| Namespace | `App\Event\{Domain}\` | `App\Event\Music\MusicAddedEvent` |

**Yasak:**
- ❌ `AddMusicEvent` (process adı)
- ❌ `MusicAdd` (eksik tense)
- ✅ `MusicAdded` (doğru)

---

## 10. Domain Events vs Integration Events

| Özellik | Domain Event | Integration Event |
|---------|-------------|-------------------|
| Kapsam | Tek modül içinde | Modüller arası |
| Payload | Zengin, detaylı | Minimal, gerekli |
| Sync | Senkron | Asenkron |
| Örnek | `MusicPlayedEvent` | `MusicPlayedIntegrationEvent` |
| Kullanım | Cache invalidation | Microservice iletişim |

---

## 11. Event Store

| Özellik | Değer |
|---------|-------|
| Depolama | `coremusic_events` tablosu |
| Format | JSON payload |
| Saklama | 90 gün aktif, sonra archive |
| Query | Event type, aggregate ID, timestamp |

```sql
CREATE TABLE coremusic_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id CHAR(36) NOT NULL UNIQUE,
    event_type VARCHAR(100) NOT NULL,
    aggregate_type VARCHAR(50) NOT NULL,
    aggregate_id BIGINT UNSIGNED NOT NULL,
    payload JSON NOT NULL,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_aggregate (aggregate_type, aggregate_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
```

---

## 12. Event Replay

| Özellik | Değer |
|---------|-------|
| Amaç | State yeniden oluşturma |
| Trigger | Sistem hatası, veri kurtarma |
| Limit | Son 30 gün |
| Hız | 1000 event/sn |

```php
final class EventReplay
{
    public function replay(string $aggregateType, string $aggregateId): object
    {
        $events = $this->eventStore->getEvents($aggregateType, $aggregateId);

        $state = null;
        foreach ($events as $event) {
            $state = $this->apply($state, $event);
        }

        return $state;
    }
}
```

---

## 13. Edge Cases

| Durum | Çözüm |
|-------|-------|
| Event handler hatası | Dead Letter Queue + retry |
| Duplicate event | Idempotency key kontrolü |
| Event storm | Rate limiting per event type |
| Serialization hatası | Fallback to JSON + log warning |
| Timeout | Async events 5s timeout |

---

## 14. Warnings

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | Event handler'da DB transaction başlatma | Deadlock riski |
| 2 | Heavy processing senkron event'de | Timeout, blocking |
| 3 | Event payload'da mutable state | Race condition |
| 4 | Event ordering ihlali | State tutarsızlığı |
| 5 | Circular event (A→B→A) | Infinite loop |

---

## 15. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| [[api-architecture-master]] | Ana mimari referans | Master |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | Event throttling |

---

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Event Count | 15 domain event |
| PSR-14 Compliance | ✅ |
| Async Support | ✅ |
| Event Store | ✅ |
| Replay Capability | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
