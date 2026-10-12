<?php
declare(strict_types=1);

namespace CoreMusic\Test\Events;

use PHPUnit\Framework\TestCase;
use CoreMusic\Events\Domain\UserLoggedInEvent;
use CoreMusic\Events\Domain\UserRegisteredEvent;
use CoreMusic\Events\Domain\GenderSetEvent;

final class DomainEventTest extends TestCase
{
    public function testUserLoggedInEventName(): void
    {
        $event = new UserLoggedInEvent('user-1', '127.0.0.1', 'Mozilla/5.0');
        $this->assertEquals('user.logged_in', $event->eventName());
    }

    public function testUserLoggedInEventPayload(): void
    {
        $event = new UserLoggedInEvent('user-1', '192.168.1.1', 'Chrome/120');
        $payload = $event->payload();
        $this->assertEquals('user-1', $payload['userId']);
        $this->assertEquals('192.168.1.1', $payload['ipAddress']);
        $this->assertEquals('Chrome/120', $payload['userAgent']);
        $this->assertArrayHasKey('timestamp', $payload);
    }

    public function testUserLoggedInGetters(): void
    {
        $event = new UserLoggedInEvent('u1', '10.0.0.1', 'Agent');
        $this->assertEquals('u1', $event->getUserId());
        $this->assertEquals('10.0.0.1', $event->getIpAddress());
        $this->assertEquals('Agent', $event->getUserAgent());
    }

    public function testUserRegisteredEventPayload(): void
    {
        $event = new UserRegisteredEvent('user-2', 'john', 'john@test.com', 'male');
        $this->assertEquals('user.registered', $event->eventName());
        $payload = $event->payload();
        $this->assertEquals('john', $payload['username']);
        $this->assertEquals('john@test.com', $payload['email']);
        $this->assertEquals('male', $payload['gender']);
    }

    public function testGenderSetEventPayload(): void
    {
        $event = new GenderSetEvent('user-3', 'female');
        $this->assertEquals('user.gender_set', $event->eventName());
        $this->assertEquals('user-3', $event->getUserId());
        $this->assertEquals('female', $event->getGender());
        $payload = $event->payload();
        $this->assertEquals('female', $payload['gender']);
    }

    public function testEventOccurredOnIsDateTimeImmutable(): void
    {
        $event = new UserLoggedInEvent('u1', '127.0.0.1', 'Test');
        $this->assertInstanceOf(\DateTimeImmutable::class, $event->occurredOn());
    }

    public function testEventTimestampIsRecent(): void
    {
        $before = new \DateTimeImmutable('-1 second');
        $event = new UserLoggedInEvent('u1', '127.0.0.1', 'Test');
        $after = new \DateTimeImmutable('+1 second');
        $this->assertGreaterThanOrEqual($before, $event->occurredOn());
        $this->assertLessThanOrEqual($after, $event->occurredOn());
    }
}
