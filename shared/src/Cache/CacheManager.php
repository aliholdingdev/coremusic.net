<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class CacheManager
{
    private static ?CacheInterface $adapter = null;

    public static function getAdapter(): CacheInterface
    {
        if (self::$adapter === null) {
            if (function_exists('apcu_fetch')) {
                self::$adapter = new ApcuAdapter();
            } else {
                self::$adapter = new MemoryAdapter();
            }
        }
        return self::$adapter;
    }

    public static function setAdapter(CacheInterface $adapter): void
    {
        self::$adapter = $adapter;
    }
}
