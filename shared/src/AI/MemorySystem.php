<?php declare(strict_types=1);
/**
 * CoreMusic — Memory System
 * 
 * Session hafızası ve persistent state yönetimi implementasyonu.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 */

namespace CoreMusic\AI;

use CoreMusic\AI\Contracts\MemorySystemInterface;

/**
 * Memory System — Session state, persistent cache, memory hierarchy.
 *
 * Cache Seviyeleri:
 * - L1 (Hot): SSOT dosyaları (CLAUDE, AGENTS, WORKFLOW) — Oturum sonu
 * - L2 (Warm): Görev dosyaları (ADR, architecture) — Görev sonu
 * - L3 (Cool): Referans dosyaları (testing, ui-design) — İsteğe bağlı
 */
class MemorySystem implements MemorySystemInterface
{
    private const HIERARCHY = [
        'brain'  => 5, // En yüksek
        'index'  => 4,
        'agents' => 4,
        'memory' => 3,
        'log'    => 1, // En düşük
    ];

    private const CACHE_TTL = [
        'l1' => 0,      // Oturum sonu (süresiz session'da)
        'l2' => 3600,   // 1 saat
        'l3' => 86400,  // 24 saat
    ];

    /** @var array<string, array<string, array{value: mixed, expires_at: ?int}>> */
    private array $sessions = [];

    /** @var array<string, array{value: mixed, expires_at: ?int}> */
    private array $persistent = [];

    /** @var array<string, array{value: mixed, level: string, expires_at: ?int}> */
    private array $cache = [];

    /**
     * {@inheritdoc}
     */
    public function setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool
    {
        $expiresAt = $ttl > 0 ? time() + $ttl : null;

        $this->sessions[$sessionId][$key] = [
            'value'      => $value,
            'expires_at' => $expiresAt,
        ];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSession(string $sessionId, string $key): mixed
    {
        if (!isset($this->sessions[$sessionId][$key])) {
            return null;
        }

        $entry = $this->sessions[$sessionId][$key];

        // TTL kontrolü
        if ($entry['expires_at'] !== null && $entry['expires_at'] < time()) {
            unset($this->sessions[$sessionId][$key]);
            return null;
        }

        return $entry['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function clearSession(string $sessionId): bool
    {
        unset($this->sessions[$sessionId]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistent(string $key, mixed $value, int $ttl = 0): bool
    {
        $expiresAt = $ttl > 0 ? time() + $ttl : null;

        $this->persistent[$key] = [
            'value'      => $value,
            'expires_at' => $expiresAt,
        ];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistent(string $key): mixed
    {
        if (!isset($this->persistent[$key])) {
            return null;
        }

        $entry = $this->persistent[$key];

        if ($entry['expires_at'] !== null && $entry['expires_at'] < time()) {
            unset($this->persistent[$key]);
            return null;
        }

        return $entry['value'];
    }

    /**
     * {@inheritdoc}
     */
    public function remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed
    {
        // Cache'de var mı kontrol et
        if (isset($this->cache[$key])) {
            $entry = $this->cache[$key];

            if ($entry['expires_at'] === null || $entry['expires_at'] >= time()) {
                return $entry['value'];
            }

            // Süresi dolmuş, temizle
            unset($this->cache[$key]);
        }

        // Yoksa yükle ve cache'le
        $value = $loader();

        $this->cache[$key] = [
            'value'      => $value,
            'level'      => $level,
            'expires_at' => $ttl > 0 ? time() + $ttl : null,
        ];

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchy(): array
    {
        return self::HIERARCHY;
    }

    /**
     * Cache istatistiklerini döndürür.
     *
     * @return array{total: int, by_level: array<string, int>, memory_usage: int}
     */
    public function getCacheStats(): array
    {
        $byLevel = ['l1' => 0, 'l2' => 0, 'l3' => 0];

        foreach ($this->cache as $entry) {
            $level = $entry['level'];
            $byLevel[$level] = ($byLevel[$level] ?? 0) + 1;
        }

        return [
            'total'        => count($this->cache),
            'by_level'     => $byLevel,
            'memory_usage' => $this->estimateMemoryUsage(),
        ];
    }

    /**
     * Süresi dolmuş cache'leri temizler.
     *
     * @return int Temizlenen kayıt sayısı
     */
    public function pruneExpired(): int
    {
        $now = time();
        $removed = 0;

        // Cache temizliği
        foreach ($this->cache as $key => $entry) {
            if ($entry['expires_at'] !== null && $entry['expires_at'] < $now) {
                unset($this->cache[$key]);
                $removed++;
            }
        }

        // Persistent temizliği
        foreach ($this->persistent as $key => $entry) {
            if ($entry['expires_at'] !== null && $entry['expires_at'] < $now) {
                unset($this->persistent[$key]);
                $removed++;
            }
        }

        // Session temizliği
        foreach ($this->sessions as $sessionId => $sessionData) {
            foreach ($sessionData as $key => $entry) {
                if ($entry['expires_at'] !== null && $entry['expires_at'] < $now) {
                    unset($this->sessions[$sessionId][$key]);
                    $removed++;
                }
            }
        }

        return $removed;
    }

    /* ─── Private Methods ─── */

    /**
     * Bellek kullanımını tahmin eder.
     */
    private function estimateMemoryUsage(): int
    {
        $size = 0;

        foreach ($this->cache as $entry) {
            $size += mb_strlen(serialize($entry['value']), '8bit');
        }

        foreach ($this->persistent as $entry) {
            $size += mb_strlen(serialize($entry['value']), '8bit');
        }

        foreach ($this->sessions as $sessionData) {
            foreach ($sessionData as $entry) {
                $size += mb_strlen(serialize($entry['value']), '8bit');
            }
        }

        return $size;
    }
}
