<?php
declare(strict_types=1);

/**
 * Password Reset Requested Domain Event.
 *
 * @file PasswordResetRequestedEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Domain;

use CoreMusic\Contracts\Events\DomainEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Domain Event: Password reset requested.
 */
final class PasswordResetRequestedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $userId,
        private readonly string $email,
        private readonly string $token
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'user.password_reset_requested';
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
            'email' => $this->email,
            'token' => $this->token,
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
     * Get email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get token.
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
