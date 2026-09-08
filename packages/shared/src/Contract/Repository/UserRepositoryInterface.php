<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Repository;

use CoreMusic\Shared\DTO\User\UserDTO;
use CoreMusic\Shared\ValueObject\UserId;
use CoreMusic\Shared\ValueObject\Email;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?UserDTO;

    public function findByEmail(Email $email): ?UserDTO;

    public function save(UserDTO $user): UserDTO;

    public function delete(UserId $id): bool;

    public function existsByEmail(Email $email): bool;
}
