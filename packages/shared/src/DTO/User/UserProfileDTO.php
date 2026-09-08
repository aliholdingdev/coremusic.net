<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\User;

readonly class UserProfileDTO
{
    public function __construct(
        public string $userId,
        public string $displayName,
        public ?string $avatarUrl = null,
        public ?string $bio = null,
        public ?string $locale = null,
    ) {}
}
