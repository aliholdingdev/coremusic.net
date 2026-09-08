<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Event;

readonly class MediaUploaded
{
    public function __construct(
        public string $mediaId,
        public string $userId,
        public string $fileName,
        public int $sizeBytes,
        public string $timestamp,
    ) {}

    public static function create(string $mediaId, string $userId, string $fileName, int $sizeBytes): self
    {
        return new self($mediaId, $userId, $fileName, $sizeBytes, date('c'));
    }
}
