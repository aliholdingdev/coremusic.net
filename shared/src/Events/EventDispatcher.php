<?php
declare(strict_types=1);

/**
 * Event Dispatcher implementation.
 *
 * @file EventDispatcher.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Log\LoggerInterface;

/**
 * Event Dispatcher implementation following PSR-14.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];
    private array $sortedListeners = [];

    public function __construct(private readonly ?LoggerInterface $logger = null)
    {
    }

    /**
     * Dispatch an event.
     *
     * Abone (listener) hatası yayıcıyı düşürmez: hata loglanır ve zincir sürer (PRD-EVT-003).
     */
    public function dispatch(object $event): object
    {
        $eventName = get_class($event);
        
        if (!isset($this->listeners[$eventName])) {
            return $event;
        }
        
        foreach ($this->getListeners($eventName) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
            
            try {
                $listener($event);
            } catch (\Throwable $e) {
                $this->reportListenerFailure($eventName, $e);
            }
        }
        
        return $event;
    }

    /**
     * Add a listener for an event.
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][] = [
            'listener' => $listener,
            'priority' => $priority,
        ];
        
        // Invalidate sorted listeners cache
        unset($this->sortedListeners[$eventName]);
    }

    /**
     * Remove a listener.
     */
    public function removeListener(string $eventName, callable $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        
        $this->listeners[$eventName] = array_filter(
            $this->listeners[$eventName],
            fn($entry) => $entry['listener'] !== $listener
        );
        
        // Invalidate sorted listeners cache
        unset($this->sortedListeners[$eventName]);
    }

    /**
     * Get listeners for an event.
     */
    public function getListeners(string $eventName): array
    {
        if (!isset($this->listeners[$eventName])) {
            return [];
        }
        
        if (!isset($this->sortedListeners[$eventName])) {
            $this->sortedListeners[$eventName] = $this->sortListeners($eventName);
        }
        
        return array_map(
            fn($entry) => $entry['listener'],
            $this->sortedListeners[$eventName]
        );
    }

    /**
     * Check if there are listeners for an event.
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    private function reportListenerFailure(string $eventName, \Throwable $e): void
    {
        $context = ['event' => $eventName, 'exception' => $e::class, 'message' => $e->getMessage()];

        if ($this->logger !== null) {
            $this->logger->error('Event listener failed', $context);
            return;
        }

        error_log('[EventDispatcher] Event listener failed: ' . json_encode($context, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Sort listeners by priority.
     */
    private function sortListeners(string $eventName): array
    {
        $listeners = $this->listeners[$eventName];
        
        usort($listeners, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });
        
        return $listeners;
    }
}
