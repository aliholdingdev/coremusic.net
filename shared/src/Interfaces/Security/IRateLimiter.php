<?php declare(strict_types=1);

namespace CoreMusic\Interfaces\Security;

interface IRateLimiter
{
    public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool;
    public function increment(string $key, int $windowSeconds): void;
    public function reset(string $key): void;
}
