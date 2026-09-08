<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Event;

readonly class UserLoggedOut
{
    public function __construct(
        public string $userId,
        public string $timestamp,
    ) {}

    public static function create(string $userId): self
    {
        return new self($userId, date('c'));
    }
}
