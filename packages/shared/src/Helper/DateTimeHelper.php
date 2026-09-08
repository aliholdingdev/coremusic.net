<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Helper;

class DateTimeHelper
{
    public static function now(): string
    {
        return date('c');
    }

    public static function timestamp(): int
    {
        return time();
    }

    public static function format(string $date, string $format = 'Y-m-d H:i:s'): string
    {
        $dt = new \DateTime($date);
        return $dt->format($format);
    }

    public static function toIso8601(string $date): string
    {
        $dt = new \DateTime($date);
        return $dt->format('c');
    }

    public static function fromTimestamp(int $timestamp): string
    {
        $dt = new \DateTime("@{$timestamp}");
        return $dt->format('c');
    }

    public static function diff(string $from, string $to): array
    {
        $fromDt = new \DateTime($from);
        $toDt = new \DateTime($to);
        $diff = $fromDt->diff($toDt);

        return [
            'y' => $diff->y,
            'm' => $diff->m,
            'd' => $diff->d,
            'h' => $diff->h,
            'i' => $diff->i,
            's' => $diff->s,
            'invert' => $diff->invert,
        ];
    }

    public static function addDays(string $date, int $days): string
    {
        $dt = new \DateTime($date);
        $dt->modify("+{$days} days");
        return $dt->format('c');
    }

    public static function isExpired(string $date): bool
    {
        $dt = new \DateTime($date);
        return $dt < new \DateTime();
    }

    public static function microsecondTimestamp(): float
    {
        $time = microtime(true);
        return round($time, 6);
    }
}
