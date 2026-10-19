<?php
declare(strict_types=1);

/**
 * Playlist Created Domain Event.
 *
 * @file PlaylistCreatedEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Domain Event: Playlist created.
 */
final class PlaylistCreatedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $playlistId,
        private readonly string $createdBy,
        private readonly string $name
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'playlist.created';
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
            'playlistId' => $this->playlistId,
            'createdBy' => $this->createdBy,
            'name' => $this->name,
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }

    /**
     * Get playlist ID.
     */
    public function getPlaylistId(): string
    {
        return $this->playlistId;
    }

    /**
     * Get user ID who created the playlist.
     */
    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    /**
     * Get playlist name.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
