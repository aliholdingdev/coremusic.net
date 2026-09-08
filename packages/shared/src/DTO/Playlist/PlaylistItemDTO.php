<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\Playlist;

readonly class PlaylistItemDTO
{
    public function __construct(
        public string $playlistId,
        public string $mediaId,
        public int $position,
        public string $addedAt = '',
    ) {}
}
