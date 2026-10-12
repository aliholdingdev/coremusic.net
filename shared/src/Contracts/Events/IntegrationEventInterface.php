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
