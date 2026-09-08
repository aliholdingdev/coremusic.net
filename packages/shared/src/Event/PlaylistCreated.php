<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Event;

readonly class PlaylistCreated
{
    public function __construct(
        public string $playlistId,
        public string $userId,
        public string $name,
        public string $timestamp,
    ) {}

    public static function create(string $playlistId, string $userId, string $name): self
    {
        return new self($playlistId, $userId, $name, date('c'));
    }
}
