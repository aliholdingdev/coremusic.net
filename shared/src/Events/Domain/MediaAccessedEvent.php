<?php
declare(strict_types=1);

/**
 * Media Accessed Domain Event.
 *
 * @file MediaAccessedEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Domain Event: Media accessed.
 */
final class MediaAccessedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $mediaId,
        private readonly string $userId,
        private readonly string $accessType
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'media.accessed';
    }

    /**
     * Get the timestamp when the event occurred.
     */
    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    /**
     * Get the event payload data.
     */
    public function payload(): array
    {
        return [
            'mediaId' => $this->mediaId,
            'userId' => $this->userId,
            'accessType' => $this->accessType,
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }

    /**
     * Get media ID.
     */
    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    /**
     * Get user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get access type (stream, download, etc.).
     */
    public function getAccessType(): string
    {
        return $this->accessType;
    }
}
