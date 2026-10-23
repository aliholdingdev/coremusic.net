<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

interface IHistoryRepository
{
    public function record(string $userId, string $musicId, int $durationSec, string $deviceType = 'desktop'): bool;

    /** @return list<array<string, mixed>> */
    public function findRecentTracks(string $userId, int $limit = 10): array;

    /** @return list<array<string, mixed>> */
    public function findHistory(string $userId, int $page = 1, int $perPage = 20, ?string $from = null, ?string $to = null): array;

    public function countForUser(string $userId, ?string $from = null, ?string $to = null): int;
}
