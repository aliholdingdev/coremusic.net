<?php
declare(strict_types=1);

/**
 * User Logged Out Domain Event.
 *
 * @file UserLoggedOutEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Domain Event: User logged out.
 */
final class UserLoggedOutEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $userId,
        private readonly string $sessionId
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'user.logged_out';
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
            'userId' => $this->userId,
            'sessionId' => $this->sessionId,
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }

    /**
     * Get user ID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get session ID.
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }
}
