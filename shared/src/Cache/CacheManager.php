<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class CacheManager
{
    private static ?CacheInterface $adapter = null;
    private static ?FileCacheAdapter $fileAdapter = null;

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

    public static function getFileAdapter(?string $cacheDir = null): FileCacheAdapter
    {
        if (self::$fileAdapter === null || $cacheDir !== null) {
            $instance = new FileCacheAdapter($cacheDir);
            if ($cacheDir === null) {
                self::$fileAdapter = $instance;
            }
            return $instance;
        }
        return self::$fileAdapter;
    }

    public static function setFileAdapter(?FileCacheAdapter $fileAdapter): void
    {
        self::$fileAdapter = $fileAdapter;
    }

    /**
     * ADR-007 standardına uygun namespace anahtarı üretir:
     * app:v{version}:{domain}:{key}
     */
    public static function buildKey(string $domain, string $key, int $version = 1): string
    {
        $cleanDomain = strtolower(trim($domain, " :/\t\n\r\0\x0B"));
        $cleanKey    = strtolower(trim($key, " :/\t\n\r\0\x0B"));
        $v           = max(1, $version);

        return "app:v{$v}:{$cleanDomain}:{$cleanKey}";
    }

    /**
     * Cache-aside helper (okuma-ağırlıklı varsayılan model — ADR-007).
     */
    public static function remember(string $key, int $ttl, callable $callback, ?CacheInterface $adapter = null): mixed
    {
        $cache = $adapter ?? self::getAdapter();
        $cached = $cache->get($key);
        if ($cached !== false) {
            return $cached;
        }

        $fresh = $callback();
        $cache->set($key, $fresh, $ttl);
        return $fresh;
    }

    public static function reset(): void
    {
        self::$adapter = null;
        self::$fileAdapter = null;
    }
}
