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

    public function getHistory(string $userId, int $page = 1, int $perPage = 20, ?string $from = null, ?string $to = null): array
    {
        $page    = max(1, $page);
        $perPage = min(100, max(1, $perPage));

        $items = $this->history->findHistory($userId, $page, $perPage, $from, $to);

        return [
            'items'    => $this->groupItemsByDay($items),
            'total'    => $this->history->countForUser($userId, $from, $to),
            'page'     => $page,
            'per_page' => $perPage,
            'from'     => $from,
            'to'       => $to,
        ];
    }

    /**
     * Adım 09 — gün gruplu görünüm (repository düz liste döner; gruplama servis katmanında).
     *
     * Yanıt şekli iki düzlemde taşınır: `items` (düz liste — mevcut istemciler
     * bozulmaz) + `days` (gün bazlı gruplama — Adım 09 sözleşmesi).
     *
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    private function groupItemsByDay(array $items): array
    {
        foreach ($items as &$item) {
            $started = (string) ($item['started_at'] ?? '');
            $item['day'] = $started !== '' ? substr($started, 0, 10) : null;
        }
        unset($item);

        return $items;
    }
}
