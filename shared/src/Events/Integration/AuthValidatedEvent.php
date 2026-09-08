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
