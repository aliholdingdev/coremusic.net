<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\Playlist;

use CoreMusic\Shared\ValueObject\PlaylistId;

readonly class PlaylistDTO
{
    public function __construct(
        public PlaylistId $id,
        public string $userId,
        public string $name,
        public ?string $description = null,
        public bool $isPublic = false,
        public int $itemCount = 0,
        public string $createdAt = '',
        public string $updatedAt = '',
    ) {}
}
