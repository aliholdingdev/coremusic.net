<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Event;

readonly class UserLoggedIn
{
    public function __construct(
        public string $userId,
        public string $ipAddress,
        public string $userAgent,
        public string $timestamp,
    ) {}

    public static function create(string $userId, string $ipAddress, string $userAgent): self
    {
        return new self($userId, $ipAddress, $userAgent, date('c'));
    }
}
