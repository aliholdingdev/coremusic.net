<?php
declare(strict_types=1);

/**
 * User Logged In Domain Event.
 *
 * @file UserLoggedInEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Domain Event: User logged in.
 */
final class UserLoggedInEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $userId,
        private readonly string $ipAddress,
        private readonly string $userAgent
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'user.logged_in';
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
            'ipAddress' => $this->ipAddress,
            'userAgent' => $this->userAgent,
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
     * Get IP address.
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * Get user agent.
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
