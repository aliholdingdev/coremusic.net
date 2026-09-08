<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Repository;

use CoreMusic\Shared\DTO\Playlist\PlaylistDTO;
use CoreMusic\Shared\ValueObject\PlaylistId;
use CoreMusic\Shared\ValueObject\UserId;

interface PlaylistRepositoryInterface
{
    public function findById(PlaylistId $id): ?PlaylistDTO;

    public function findByUserId(UserId $userId): array;

    public function save(PlaylistDTO $playlist): PlaylistDTO;

    public function delete(PlaylistId $id): bool;
}
