<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\IMusicRepository;

/**
 * MusicService — müzik uçları iş katmanı (IMusicService implementasyonu).
 *
 * Faz 4. Sınırlar: sayfa/perPage clamp (1..100), arama limiti (1..50).
 * Cinsiyet segmenti (P0-4 header) bu katmanda DEĞİL — musics şemasında gender
 * kolonu doğrulanmadığı için segmentleme uydurulmaz (zero-hallucination).
 */
final class MusicService implements IMusicService
{
    public function __construct(
        private readonly IMusicRepository $music,
    ) {}

    public function listTracks(int $page, int $perPage): array
    {
        $page    = max(1, $page);
        $perPage = min(100, max(1, $perPage));

        return [
            'items'    => $this->music->findMany($perPage, ($page - 1) * $perPage),
            'total'    => $this->music->count(),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function searchTracks(string $query, int $limit): array
    {
        $items = $this->music->search(trim($query), min(50, max(1, $limit)));

        return ['items' => $items, 'total' => count($items)];
    }

    public function getTrack(string $trackId): ?array
    {
        return $this->music->findById(strtolower($trackId));
    }
}
