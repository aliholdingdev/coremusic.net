<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\Auth;

use CoreMusic\Shared\ValueObject\Email;

readonly class LoginRequest
{
    public function __construct(
        public Email $email,
        public string $password,
        public ?string $deviceToken = null,
    ) {}
}
