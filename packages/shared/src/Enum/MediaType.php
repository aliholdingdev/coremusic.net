<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Enum;

enum MediaType: string
{
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case IMAGE = 'image';
    case DOCUMENT = 'document';

    public function mimeCategory(): string
    {
        return match ($this) {
            self::AUDIO => 'audio',
            self::VIDEO => 'video',
            self::IMAGE => 'image',
            self::DOCUMENT => 'application',
        };
    }
}
