<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\Media;

use CoreMusic\Shared\Enum\MediaType;
use CoreMusic\Shared\ValueObject\MediaId;

readonly class MediaFileDTO
{
    public function __construct(
        public MediaId $id,
        public string $userId,
        public string $title,
        public MediaType $type,
        public string $mimeType,
        public int $sizeBytes,
        public ?int $durationMs = null,
        public ?string $storagePath = null,
        public string $createdAt = '',
    ) {}
}
