<?php
declare(strict_types=1);

/**
 * Notification Integration Event.
 *
 * @file NotificationEvent.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events\Integration;

use CoreMusic\Contracts\Events\IntegrationEventInterface;
use CoreMusic\Events\StoppableEventTrait;

/**
 * Integration Event: Notification.
 */
final class NotificationEvent implements IntegrationEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $userId,
        private readonly string $type,
        private readonly string $message,
        private readonly array $data = []
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * Get the event name.
     */
    public function eventName(): string
    {
        return 'notification.sent';
    }

    /**
     * Get the source service.
     */
    public function source(): string
    {
        return 'notification.service';
    }

    /**
     * Get the event payload data.
     */
    public function payload(): array
    {
        return [
            'userId' => $this->userId,
            'type' => $this->type,
            'message' => $this->message,
            'data' => $this->data,
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
     * Get notification type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get notification message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get notification data.
     */
    public function getData(): array
    {
        return $this->data;
    }
}
