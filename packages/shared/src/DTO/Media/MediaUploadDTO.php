<?php

declare(strict_types=1);

namespace CoreMusic\Shared\DTO\Media;

readonly class MediaUploadDTO
{
    public function __construct(
        public string $title,
        public string $fileName,
        public string $mimeType,
        public int $sizeBytes,
        public string $tmpPath,
    ) {}
}
