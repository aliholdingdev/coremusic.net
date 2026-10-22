<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\IHistoryRepository;

/**
 * HistoryService — dinleme geçmişi iş katmanı (IHistoryService implementasyonu).
 *
 * Adım 05: Arayüz ayrı dosyaya taşındı (IHistoryService.php) — SRP + dosya deseni
 * tutarlılığı. Sınırlar: sayfa/perPage clamp (1..100), limit clamp (1..50).
 */
final class HistoryService implements IHistoryService
{
    public function __construct(
        private readonly IHistoryRepository $history,
    ) {}

    public function recordListening(string $userId, string $musicId, int $durationSec, string $deviceType = 'desktop'): bool
    {
        return $this->history->record($userId, $musicId, $durationSec, $deviceType);
    }

    public function getRecentTracks(string $userId, int $limit = 10): array
    {
        return $this->history->findRecentTracks($userId, $limit);
    }

    public function getHistory(string $userId, int $page = 1, int $perPage = 20): array
    {
        $page    = max(1, $page);
        $perPage = min(100, max(1, $perPage));

        return [
            'items'    => $this->history->findHistory($userId, $page, $perPage),
            'total'    => $this->history->countForUser($userId),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }
}
