<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\User;

use CoreMusic\Shared\Enum\UserRole;
use CoreMusic\Shared\ValueObject\Email;
use CoreMusic\Shared\ValueObject\UserId;

readonly class UserDTO
{
    public function __construct(
        public UserId $id,
        public Email $email,
        public string $displayName,
        public UserRole $role,
        public bool $isActive,
        public string $createdAt,
        public string $updatedAt,
    ) {}
}
