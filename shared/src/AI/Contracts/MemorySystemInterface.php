<?php declare(strict_types=1);
/**
 * CoreMusic — Memory System Interface
 * 
 * Session hafızası ve persistent state yönetimi arayüzü.
 *
 * @package CoreMusic\AI\Contracts
 * @version 1.0.0
 */

namespace CoreMusic\AI\Contracts;

interface MemorySystemInterface
{
    /**
     * Session state'i kaydeder.
     *
     * @param string $sessionId Oturum ID'si
     * @param string $key       Anahtar
     * @param mixed  $value     Değer
     * @param int    $ttl       TTL (saniye, 0 = süresiz)
     * @return bool Başarı durumu
     */
    public function setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool;

    /**
     * Session state'i okur.
     *
     * @param string $sessionId Oturum ID'si
     * @param string $key       Anahtar
     * @return mixed|null Değer veya null
     */
    public function getSession(string $sessionId, string $key): mixed;

    /**
     * Session'ı siler.
     *
     * @param string $sessionId Oturum ID'si
     * @return bool Başarı durumu
     */
    public function clearSession(string $sessionId): bool;

    /**
     * Persistent state kaydeder (oturumdan bağımsız).
     *
     * @param string $key   Anahtar
     * @param mixed  $value Değer
     * @param int    $ttl   TTL (saniye, 0 = süresiz)
     * @return bool Başarı durumu
     */
    public function setPersistent(string $key, mixed $value, int $ttl = 0): bool;

    /**
     * Persistent state okur.
     *
     * @param string $key Anahtar
     * @return mixed|null Değer veya null
     */
    public function getPersistent(string $key): mixed;

    /**
     * Cache stratejisi — L1/L2/L3 seviyeleri.
     *
     * @param string $key   Cache anahtarı
     * @param callable $loader Veri yükleyici fonksiyon
     * @param string   $level  Cache seviyesi (l1, l2, l3)
     * @param int      $ttl   TTL (saniye)
     * @return mixed Cached veya fresh veri
     */
    public function remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed;

    /**
     * Memory hierarchy'yi döndürür.
     *
     * @return array{brain: int, index: int, agents: int, memory: int, log: int} Öncelik seviyeleri
     */
    public function getHierarchy(): array;
}
