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
