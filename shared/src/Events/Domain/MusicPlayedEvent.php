<?php
declare(strict_types=1);

/**
 * Music Played Domain Event.
 *
 * @file MusicPlayedEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Domain Event: Music track played.
 */
final class MusicPlayedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $musicId,
        private readonly string $userId,
        private readonly int $duration
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'music.played';
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
            'musicId' => $this->musicId,
            'userId' => $this->userId,
            'duration' => $this->duration,
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }

    /**
     * Get music ID.
     */
    public function getMusicId(): string
    {
        return $this->musicId;
    }

    /**
     * Get user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get duration in seconds.
     */
    public function getDuration(): int
    {
        return $this->duration;
    }
}
