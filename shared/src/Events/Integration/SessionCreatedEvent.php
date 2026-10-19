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
