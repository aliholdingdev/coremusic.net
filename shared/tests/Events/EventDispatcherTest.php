<?php
declare(strict_types=1);

namespace CoreMusic\Test\Events;

use PHPUnit\Framework\TestCase;
use CoreMusic\Events\EventDispatcher;
use CoreMusic\Events\Domain\UserLoggedInEvent;

final class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testDispatchReturnsEventInstance(): void
    {
        $event = new \stdClass();
        $event->name = 'test';
        $result = $this->dispatcher->dispatch($event);
        $this->assertSame($event, $result);
    }

    public function testAddListenerAndGetListeners(): void
    {
        $listener = function () {};
        $this->dispatcher->addListener('test.event', $listener);
        $listeners = $this->dispatcher->getListeners('test.event');
        $this->assertCount(1, $listeners);
        $this->assertSame($listener, $listeners[0]);
    }

    public function testListenerIsCalledOnDispatch(): void
    {
        $called = false;
        $this->dispatcher->addListener(
            UserLoggedInEvent::class,
            function () use (&$called) {
                $called = true;
            }
        );
        $event = new UserLoggedInEvent('user-1', '127.0.0.1', 'TestAgent');
        $this->dispatcher->dispatch($event);
        $this->assertTrue($called);
    }

    public function testPriorityOrderRespected(): void
    {
        $order = [];
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$order) {
            $order[] = 'low';
        }, 0);
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$order) {
            $order[] = 'high';
        }, 10);
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$order) {
            $order[] = 'medium';
        }, 5);
        $event = new UserLoggedInEvent('user-1', '127.0.0.1', 'TestAgent');
        $this->dispatcher->dispatch($event);
        $this->assertEquals(['high', 'medium', 'low'], $order);
    }

    public function testRemoveListener(): void
    {
        $listener = function () {};
        $this->dispatcher->addListener('test.event', $listener);
        $this->assertCount(1, $this->dispatcher->getListeners('test.event'));
        $this->dispatcher->removeListener('test.event', $listener);
        $this->assertCount(0, $this->dispatcher->getListeners('test.event'));
    }

    public function testHasListenersReturnsTrueWhenListenersExist(): void
    {
        $this->assertFalse($this->dispatcher->hasListeners('test.event'));
        $this->dispatcher->addListener('test.event', function () {});
        $this->assertTrue($this->dispatcher->hasListeners('test.event'));
    }

    public function testGetListenersReturnsEmptyForUnknownEvent(): void
    {
        $this->assertEmpty($this->dispatcher->getListeners('nonexistent.event'));
    }

    public function testMultipleListenersAllCalled(): void
    {
        $count = 0;
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$count) { $count++; });
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$count) { $count++; });
        $this->dispatcher->addListener(UserLoggedInEvent::class, function () use (&$count) { $count++; });
        $event = new UserLoggedInEvent('user-1', '127.0.0.1', 'TestAgent');
        $this->dispatcher->dispatch($event);
        $this->assertEquals(3, $count);
    }
}
