<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\Auth;

readonly class LoginResponse
{
    public function __construct(
        public TokenPair $tokens,
        public string $userId,
        public string $expiresAt,
    ) {}
}
