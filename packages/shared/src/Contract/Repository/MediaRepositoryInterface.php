<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Repository;

use CoreMusic\Shared\DTO\Media\MediaFileDTO;
use CoreMusic\Shared\ValueObject\MediaId;
use CoreMusic\Shared\ValueObject\UserId;

interface MediaRepositoryInterface
{
    public function findById(MediaId $id): ?MediaFileDTO;

    public function findByUserId(UserId $userId): array;

    public function save(MediaFileDTO $media): MediaFileDTO;

    public function delete(MediaId $id): bool;
}
