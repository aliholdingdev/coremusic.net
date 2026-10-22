<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * IHistoryService — dinleme geçmişi uçları iş katmanı sözleşmesi (Adım 05).
 *
 * Not: Arayüz HistoryService.php'den ayrıldı (SRP — IMusicService/IPlaylistService
 * dosya deseniyle tutarlılık).
 */
interface IHistoryService
{
    /** Dinleme kaydı ekler; geçersiz id → false. */
    public function recordListening(string $userId, string $musicId, int $durationSec, string $deviceType = 'desktop'): bool;

    /** @return list<array<string, mixed>> Son dinlenenler (ownership: userId filtresi zorunlu). */
    public function getRecentTracks(string $userId, int $limit = 10): array;

    /** @return array{items: list<array<string, mixed>>, total: int, page: int, per_page: int} */
    public function getHistory(string $userId, int $page = 1, int $perPage = 20): array;
}
