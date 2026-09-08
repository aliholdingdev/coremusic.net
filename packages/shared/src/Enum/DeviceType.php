<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Enum;

enum DeviceType: string
{
    case WEB = 'web';
    case MOBILE = 'mobile';
    case DESKTOP = 'desktop';
    case EMBEDDED = 'embedded';
    case CAST = 'cast';

    public function label(): string
    {
        return match ($this) {
            self::WEB => 'Web',
            self::MOBILE => 'Mobil',
            self::DESKTOP => 'Masaustu',
            self::EMBEDDED => 'Gomulu',
            self::CAST => 'Yayin',
        };
    }
}
