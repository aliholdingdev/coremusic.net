---
node_id: K012-services/integration-service
node_type: module
role: ornekler
status: draft
parent_id: K012-services/integration-service
children: []
depends_on:
  - "integration-service/index.md"
  - "integration-service-anayasa.md"
  - "integration-service-surucu.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — shared/src/Contracts/Events/IntegrationEventInterface.php 46 satır (birebir blok §1)"
  - "S1 — shared/src/Events/Integration/AuthValidatedEvent.php 86 satır (birebir blok §2)"
  - "S1 — shared/src/Events/Integration/NotificationEvent.php 104 satır (birebir blok §3)"
  - "S1 — shared/src/Events/Integration/SessionCreatedEvent.php 94 satır (birebir blok §4)"
  - "S1 — shared/src/Events/EventDispatcher.php 119 satır (birebir blok §5)"
  - "S1 — shared/src/Events/StoppableEventTrait.php 36 satır (birebir blok §6)"
validation:
  - "§1–§7 blokları 2026-10-13 read — dosya satırlarıyla birebir"
  - "§8–§13 analiz tabloları bloklardan türetildi"
open_questions:
  - "Domain event sınıflarının tam metni bu dosyaya alınmadı → ornekler kapsamı yalnız Integration + dispatcher + trait"
  - "EventDispatcherTest.php tam metni okunmadı (yalnız özet eşleme) → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: v2 §2.12 + shared/src/Events/ (S1, 2026-10-13 read)"
group: kod
---

# integration-service — Örnekler (Examples)

> **Ön not:** bu dosya, bu servise ait **birebir kod bloklarını** taşır.
> Her blok `Dosya:` + `Satır:` künyesiyle, okunduğu tarihte diskteki haliyle
> verilir (S1, 2026-10-13). Bloklar arası analiz satırları ** türetilmiş
> gözlemdir** ve blok içine eklenmez. Rol ayrımı: anayasa özet + kural,
> surucu akış, bu dosya ham kod.

## §1 IntegrationEventInterface — Tam Dosya

Dosya: `shared/src/Contracts/Events/IntegrationEventInterface.php` / Satır: 1–46

```php
<?php
declare(strict_types=1);

/**
 * Integration Event interface for cross-service communication.
 *
 * @file IntegrationEventInterface.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Contracts\Events;

/**
 * Interface for integration events.
 */
interface IntegrationEventInterface
{
    /**
     * Get the event name.
     *
     * @return string Event name
     */
    public function eventName(): string;

    /**
     * Get the source service that originated the event.
     *
     * @return string Source service name
     */
    public function source(): string;

    /**
     * Get the event payload data.
     *
     * @return array Event payload
     */
    public function payload(): array;

    /**
     * Get the timestamp when the event occurred.
     *
     * @return \DateTimeImmutable Event timestamp
     */
    public function occurredOn(): \DateTimeImmutable;
}
```

## §2 AuthValidatedEvent — Tam Dosya

Dosya: `shared/src/Events/Integration/AuthValidatedEvent.php` / Satır: 1–86

```php
<?php
declare(strict_types=1);

/**
 * Auth Validated Integration Event.
 *
 * @file AuthValidatedEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Integration;

use CoreMusic\Contracts\Events\IntegrationEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Integration Event: Auth validated.
 */
final class AuthValidatedEvent implements IntegrationEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $userId,
        private readonly string $source,
        private readonly \DateTimeImmutable $validatedAt
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'auth.validated';
    }

    /**
     * Get the source service.
     */
    public function source(): string
    {
        return $this->source;
    }

    /**
     * Get the event payload data.
     */
    public function payload(): array
    {
        return [
            'userId' => $this->userId,
            'source' => $this->source,
            'validatedAt' => $this->validatedAt->format('c'),
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }

    /**
     * Get the timestamp when the event occurred.
     */
    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    /**
     * Get user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get validation timestamp.
     */
    public function getValidatedAt(): \DateTimeImmutable
    {
        return $this->validatedAt;
    }
}
```

## §3 NotificationEvent — Tam Dosya

Dosya: `shared/src/Events/Integration/NotificationEvent.php` / Satır: 1–104

```php
<?php
declare(strict_types=1);

/**
 * Notification Integration Event.
 *
 * @file NotificationEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Integration;

use CoreMusic\Contracts\Events\IntegrationEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Integration Event: Notification.
 */
final class NotificationEvent implements IntegrationEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $userId,
        private readonly string $type,
        private readonly string $message,
        private readonly array $data = []
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'notification.sent';
    }

    /**
     * Get the source service.
     */
    public function source(): string
    {
        return 'notification.service';
    }

    /**
     * Get the event payload data.
     */
    public function payload(): array
    {
        return [
            'userId' => $this->userId,
            'type' => $this->type,
            'message' => $this->message,
            'data' => $this->data,
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }

    /**
     * Get the timestamp when the event occurred.
     */
    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    /**
     * Get user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get notification type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get notification message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get notification data.
     */
    public function getData(): array
    {
        return $this->data;
    }
}
```

## §4 SessionCreatedEvent — Tam Dosya

Dosya: `shared/src/Events/Integration/SessionCreatedEvent.php` / Satır: 1–94

```php
<?php
declare(strict_types=1);

/**
 * Session Created Integration Event.
 *
 * @file SessionCreatedEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Integration;

use CoreMusic\Contracts\Events\IntegrationEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Integration Event: Session created.
 */
final class SessionCreatedEvent implements IntegrationEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $sessionId,
        private readonly string $userId,
        private readonly \DateTimeImmutable $expiresAt
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'session.created';
    }

    /**
     * Get the source service.
     */
    public function source(): string
    {
        return 'auth.service';
    }

    /**
     * Get the event payload data.
     */
    public function payload(): array
    {
        return [
            'sessionId' => $this->sessionId,
            'userId' => $this->userId,
            'expiresAt' => $this->expiresAt->format('c'),
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }

    /**
     * Get the timestamp when the event occurred.
     */
    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    /**
     * Get session ID.
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Get user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get expiration timestamp.
     */
    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
```

## §5 EventDispatcher — Tam Dosya

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 1–119

```php
<?php
declare(strict_types=1);

/**
 * Event Dispatcher implementation.
 *
 * @file EventDispatcher.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event Dispatcher implementation following PSR-14.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];
    private array $sortedListeners = [];

    /**
     * Dispatch an event.
     */
    public function dispatch(object $event): object
    {
        $eventName = get_class($event);
        
        if (!isset($this->listeners[$eventName])) {
            return $event;
        }
        
        foreach ($this->getListeners($eventName) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
             
            $listener($event);
        }
        
        return $event;
    }

    /**
     * Add a listener for an event.
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][] = [
            'listener' => $listener,
            'priority' => $priority,
        ];
        
        // Invalidate sorted listeners cache
        unset($this->sortedListeners[$eventName]);
    }

    /**
     * Remove a listener.
     */
    public function removeListener(string $eventName, callable $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        
        $this->listeners[$eventName] = array_filter(
            $this->listeners[$eventName],
            fn($entry) => $entry['listener'] !== $listener
        );
        
        // Invalidate sorted listeners cache
        unset($this->sortedListeners[$eventName]);
    }

    /**
     * Get listeners for an event.
     */
    public function getListeners(string $eventName): array
    {
        if (!isset($this->listeners[$eventName])) {
            return [];
        }
        
        if (!isset($this->sortedListeners[$eventName])) {
            $this->sortedListeners[$eventName] = $this->sortListeners($eventName);
        }
        
        return array_map(
            fn($entry) => $entry['listener'],
            $this->sortedListeners[$eventName]
        );
    }

    /**
     * Check if there are listeners for an event.
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    /**
     * Sort listeners by priority.
     */
    private function sortListeners(string $eventName): array
    {
        $listeners = $this->listeners[$eventName];
        
        usort($listeners, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        return $listeners;
    }
}
```

## §6 StoppableEventTrait — Tam Dosya

Dosya: `shared/src/Events/StoppableEventTrait.php` / Satır: 1–36

```php
<?php
declare(strict_types=1);

/**
 * Stoppable Event Trait.
 *
 * @file StoppableEventTrait.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events;

/**
 * Trait for stoppable events.
 */
trait StoppableEventTrait
{
    private bool $propagationStopped = false;

    /**
     * Check if event propagation is stopped.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stop event propagation.
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
```

## §7 DomainEventInterface — Tam Dosya (Karşılaştırma)

Dosya: `shared/src/Contracts/Events/DomainEventInterface.php` / Satır: 1–39

```php
<?php
declare(strict_types=1);

/**
 * Domain Event interface for event-driven architecture.
 *
 * @file DomainEventInterface.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Contracts\Events;

/**
 * Interface for domain events.
 */
interface DomainEventInterface
{
    /**
     * Get the event name.
     *
     * @return string Event name
     */
    public function eventName(): string;

    /**
     * Get the timestamp when the event occurred.
     *
     * @return \DateTimeImmutable Event timestamp
     */
    public function occurredOn(): \DateTimeImmutable;

    /**
     * Get the event payload data.
     *
     * @return array Event payload
     */
    public function payload(): array;
}
```

**Analiz (§7 → §1 farkı):** `source()` satırı yalnız entegrasyon
interface'indedir; üç method ortaktır: `eventName`, `occurredOn`, `payload`.

## §8 Blok-Dosya Eşlemesi

| # | Bölüm | Dosya | Satır | Kod satırı (dosyada) |
|---|---|---|---|---|
| 1 | §1 | IntegrationEventInterface.php | 1–46 | 46 |
| 2 | §2 | AuthValidatedEvent.php | 1–86 | 86 |
| 3 | §3 | NotificationEvent.php | 1–104 | 104 |
| 4 | §4 | SessionCreatedEvent.php | 1–94 | 94 |
| 5 | §5 | EventDispatcher.php | 1–119 | 119 |
| 6 | §6 | StoppableEventTrait.php | 1–36 | 36 |
| 7 | §7 | DomainEventInterface.php | 1–39 | 39 |

**Blok bütünlüğü:** 7 blok = 7 dosyanın tamamı (kırpma yok); başlık ve
künye satırları kod bloğunun dışındadır.

## §9 İmza Analizi

| Dosya | Üye | İmza |
|---|---|---|
| IntegrationEventInterface | method ×4 | `eventName(): string` · `source(): string` · `payload(): array` · `occurredOn(): \DateTimeImmutable` |
| AuthValidatedEvent | constructor | `(string $userId, string $source, \DateTimeImmutable $validatedAt)` |
| NotificationEvent | constructor | `(string $userId, string $type, string $message, array $data = [])` |
| SessionCreatedEvent | constructor | `(string $sessionId, string $userId, \DateTimeImmutable $expiresAt)` |
| EventDispatcher | public ×5 | `dispatch(object): object` · `addListener(string, callable, int=0): void` · `removeListener(string, callable): void` · `getListeners(string): array` · `hasListeners(string): bool` |
| EventDispatcher | private ×1 | `sortListeners(string): array` |
| StoppableEventTrait | public ×2 + alan ×1 | `isPropagationStopped(): bool` · `stopPropagation(): void` · `bool $propagationStopped` |

## §10 Kod İçi Referanslar (Birebir)

| Dosya | Satır | Referans |
|---|---|---|
| 7 dosyanın tamamı | 9 | `@see ADR-086-event-driven-architecture` |
| EventDispatcher.php | 14–15 | `use Psr\EventDispatcher\EventDispatcherInterface;` · `use Psr\EventDispatcher\StoppableEventInterface;` |
| EventDispatcher.php | 18 | "Event Dispatcher implementation following PSR-14." |
| 3 Integration event | 14 | `use CoreMusic\Contracts\Events\IntegrationEventInterface;` |
| 3 Integration event | 15 | `use CoreMusic\Events\StoppableEventTrait;` |
| EventDispatcher.php | 57, 75 | `// Invalidate sorted listeners cache` (kod içi tek İngilizce yorum satırı) |

## §11 Dikkat Çeken Satırlar (Analiz)

| # | Satır | Gözlem |
|---|---|---|
| 1 | AuthValidatedEvent:45–48 vs diğer ikisi | `source()` birinde constructor, ikisinde sabit string |
| 2 | NotificationEvent:30 | tek default parametre (`array $data = []`) |
| 3 | EventDispatcher:30 | `get_class($event)` — dispatch anahtarı sınıf adı |
| 4 | EventDispatcher:37–38 | duraklama kontrolü döngü başında, `break` |
| 5 | EventDispatcher:28–45 | `try/catch` yok |
| 6 | EventDispatcher:72 | `!==` — callable kimlik karşılaştırması |
| 7 | EventDispatcher:114 | `$b['priority'] <=> $a['priority']` — azalan |
| 8 | StoppableEventTrait:19 | `private bool $propagationStopped = false` — mutable olay alanı |
| 9 | 3 event:31–32 | `$this->occurredOn = new \DateTimeImmutable();` — constructor içinde |

## §12 Blok-Dışında Kalanlar (Kapsam Notu)

| Öğe | Neden yok | Giden yer |
|---|---|---|
| EventDispatcherTest.php tam metni | okunmadı (yalnız özet eşleme, surucu §34) | ⚠️ VERIFICATION REQUIRED |
| Domain event 9 dosyanın tam metni | bu servis kapsamı değil | ilgili servis dosyaları |
| Production tetikleyici kodu | diskte YOK (anayasa §12) | yok — kayıt §13 |
| K013 adaptör kodu | beyazliste dışı | K013-integraion |

## §13 Kayıt

| Tarih | Kayıt |
|---|---|
| 2026-10-13 | §1–§7 birebir bloklar S1 read ile alındı; §8–§12 analiz; eksikler §12'de işaretli |

## §14 Değişiklik Kaydı

| Tarih | Değişiklik | Not |
|---|---|---|
| 2026-10-13 | ornekler dosyası yazıldı — 7 tam kod bloğu + imza/referans analizleri | group kod |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
