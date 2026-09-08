<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Service;

use CoreMusic\Shared\DTO\Media\MediaFileDTO;
use CoreMusic\Shared\DTO\Media\MediaUploadDTO;
use CoreMusic\Shared\ValueObject\MediaId;
use CoreMusic\Shared\ValueObject\UserId;

interface MediaServiceInterface
{
    public function upload(MediaUploadDTO $upload, UserId $userId): MediaFileDTO;

    public function getById(MediaId $id): ?MediaFileDTO;

    public function getByUserId(UserId $userId): array;

    public function delete(MediaId $id, UserId $userId): bool;
}
