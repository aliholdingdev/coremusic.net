<?php
declare(strict_types=1);

/**
 * Stoppable Event Trait.
 *
 * @file StoppableEventTrait.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */

namespace CoreMusic\Events;

/**
 * Trait for stoppable events.
 */
trait StoppableEventTrait
{
    private bool $propagationStopped = false;

    /**
     * Check if event propagation is stopped.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stop event propagation.
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
