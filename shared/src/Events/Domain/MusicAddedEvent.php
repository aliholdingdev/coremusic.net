<?php
declare(strict_types=1);

/**
 * Music Added Domain Event.
 *
 * @file MusicAddedEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Domain Event: Music track added.
 */
final class MusicAddedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $musicId,
        private readonly string $addedBy,
        private readonly string $title
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'music.added';
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
            'addedBy' => $this->addedBy,
            'title' => $this->title,
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
     * Get user ID who added the music.
     */
    public function getAddedBy(): string
    {
        return $this->addedBy;
    }

    /**
     * Get music title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
