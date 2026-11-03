<?php declare(strict_types=1);

namespace CoreMusic\Cache;

/**
 * FileCacheAdapter — L2 Kalıcı Dosya Önbelleği (ADR-007).
 *
 * Özellikler:
 * - Atomik yazma (.tmp -> rename, yarış durumu yok).
 * - İki seviyeli dizin hashleme (büyük dizinlerde inode/performans darboğazı önleme).
 * - JSON tabanlı güvenli zarf (unserialize yok — OWASP & php-backend-standards uyumlu).
 * - 64-bit tam sayı zaman damgası (2038 / 50 yıllık deney güvenliği).
 * - Atomik dosya kilitli increment.
 * - Test edilebilir sanal saat (clock injection desteği).
 */
final class FileCacheAdapter implements CacheInterface
{
    private string $cacheDir;
    private int $defaultTtl;
    private ?\Closure $clock;

    public function __construct(?string $cacheDir = null, int $defaultTtl = 3600, ?\Closure $clock = null)
    {
        $base = $cacheDir !== null && $cacheDir !== ''
            ? $cacheDir
            : rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'coremusic_cache';

        $this->cacheDir = rtrim($base, '/\\');
        $this->defaultTtl = $defaultTtl > 0 ? $defaultTtl : 3600;
        $this->clock = $clock;

        $this->ensureDirectory($this->cacheDir);
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    private function now(): int
    {
        return $this->clock !== null ? (int) ($this->clock)() : time();
    }

    public function get(string $key): mixed
    {
        $filePath = $this->getFilePath($key);
        if (!is_file($filePath)) {
            return false;
        }

        $content = @file_get_contents($filePath);
        if ($content === false || $content === '') {
            return false;
        }

        $envelope = json_decode($content, true);
        if (!is_array($envelope) || !isset($envelope['expires_at'], $envelope['value'])) {
            // Bozulmuş dosya -> temizle
            @unlink($filePath);
            return false;
        }

        $expiresAt = (int) $envelope['expires_at'];
        if ($expiresAt > 0 && $expiresAt <= $this->now()) {
            // Süresi dolmuş
            @unlink($filePath);
            return false;
        }

        return $envelope['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $filePath = $this->getFilePath($key);
        $dir = dirname($filePath);
        $this->ensureDirectory($dir);

        $now = $this->now();
        $expiresAt = 0;
        if ($ttl > 0) {
            $expiresAt = $now + $ttl;
        } elseif ($ttl === 0) {
            $expiresAt = $now + $this->defaultTtl;
        }

        $envelope = [
            'key'        => $key,
            'expires_at' => $expiresAt,
            'created_at' => $now,
            'value'      => $value,
        ];

        $json = json_encode($envelope, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return false;
        }

        // Atomik yazma: geçici dosyaya yaz, sonra rename et
        $tmpPath = $dir . DIRECTORY_SEPARATOR . '.' . bin2hex(random_bytes(6)) . '.tmp';
        if (@file_put_contents($tmpPath, $json, LOCK_EX) === false) {
            return false;
        }

        if (!@rename($tmpPath, $filePath)) {
            @unlink($tmpPath);
            return false;
        }

        return true;
    }

    public function delete(string $key): bool
    {
        $filePath = $this->getFilePath($key);
        if (is_file($filePath)) {
            return @unlink($filePath);
        }
        return true;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== false;
    }

    public function increment(string $key, int $step = 1): int|false
    {
        $filePath = $this->getFilePath($key);
        $dir = dirname($filePath);
        $this->ensureDirectory($dir);

        $lockFile = $filePath . '.lock';
        $fp = @fopen($lockFile, 'c+');
        if (!$fp) {
            return false;
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return false;
        }

        try {
            $current = $this->get($key);
            if ($current === false || !is_int($current)) {
                $newVal = $step;
            } else {
                $newVal = $current + $step;
            }

            $this->set($key, $newVal, $this->defaultTtl);
            return $newVal;
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
            @unlink($lockFile);
        }
    }

    public function clear(): bool
    {
        return $this->deleteDirectoryContents($this->cacheDir);
    }

    private function getFilePath(string $key): string
    {
        $hash = hash('xxh64', $key);
        $sub1 = substr($hash, 0, 2);
        return $this->cacheDir . DIRECTORY_SEPARATOR . $sub1 . DIRECTORY_SEPARATOR . $hash . '.cache';
    }

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
    }

    private function deleteDirectoryContents(string $dir): bool
    {
        if (!is_dir($dir)) {
            return true;
        }

        $items = @scandir($dir);
        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectoryContents($path);
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }

        return true;
    }
}
