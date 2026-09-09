<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

/**
 * HomeLayoutVariant — cihaz/layout varyantı (PNG tier'ları, v2.0.0)
 *
 * - Embedded: ≤1024 · PNG SSOT: .ai/.png/home-1024/
 * - Wide:     ≥1920 · PNG SSOT: .ai/.png/home-1920/
 * - FourK:    ≥2561 · wide markup + 4K ölçek token'ları (CSS otomatik)
 */
enum HomeLayoutVariant
{
    case Embedded;
    case Wide;
    case FourK;

    /** Wide ve FourK aynı markup'ı paylaşır (home-1920 temelli). */
    public function isWide(): bool
    {
        return $this === self::Wide || $this === self::FourK;
    }

    /** DeviceManager karar bayraklarından varyant üretir. */
    public static function fromFlags(bool $isWide, bool $is4k): self
    {
        return $is4k ? self::FourK : ($isWide ? self::Wide : self::Embedded);
    }
}
