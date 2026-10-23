<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Security\UuidV7;

final class HistoryRepository implements IHistoryRepository
{
    private const DB_KEY = 'user';

    public function __construct(
        private readonly IDatabaseRegistry $userRegistry,
        private readonly IDatabaseRegistry $musicRegistry,
    ) {}

    public function record(string $userId, string $musicId, int $durationSec, string $deviceType = 'desktop'): bool
    {
        if (strlen($userId) !== 32 || strlen($musicId) !== 32) {
            return false;
        }

        $id = UuidV7::generateHex();
        $sql = 'INSERT INTO user_listening_history 
                (id, user_id, music_id, listen_duration_sec, started_at, device_type)
                VALUES (UNHEX(:id), UNHEX(:uid), UNHEX(:mid), :duration, NOW(), :device)';

        return $this->userDb()->write($sql, [
            'id'       => $id,
            'uid'      => $userId,
            'mid'      => $musicId,
            'duration' => $durationSec,
            'device'   => $deviceType,
        ]);
    }

    public function findRecentTracks(string $userId, int $limit = 10): array
    {
        if (strlen($userId) !== 32) {
            return [];
        }

        $limit = min(50, max(1, $limit));

        // En son dinlenen tekil parçaları al
        $sql = 'SELECT HEX(h.music_id) AS music_hex, 
                       MAX(h.started_at) AS last_listened_at
                  FROM user_listening_history h
                 WHERE h.user_id = UNHEX(:uid)
                 GROUP BY h.music_id
                 ORDER BY last_listened_at DESC
                 LIMIT ' . $limit;

        $historyRows = $this->userDb()->execute($sql, ['uid' => $userId]);
        if ($historyRows === []) {
            return [];
        }

        return $this->hydrateTracksWithMusicDb($historyRows);
    }

    public function findHistory(string $userId, int $page = 1, int $perPage = 20, ?string $from = null, ?string $to = null): array
    {
        if (strlen($userId) !== 32) {
            return [];
        }

        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage));
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT HEX(h.id) AS history_id,
                       HEX(h.music_id) AS music_hex,
                       h.listen_duration_sec,
                       h.started_at,
                       h.device_type
                  FROM user_listening_history h
                 WHERE h.user_id = UNHEX(:uid)';

        $params = ['uid' => $userId];
        [$sql, $params] = $this->applyDateRange($sql, $params, $from, $to);

        $sql .= ' ORDER BY h.started_at DESC
                  LIMIT ' . $perPage . ' OFFSET ' . $offset;

        $historyRows = $this->userDb()->execute($sql, $params);
        if ($historyRows === []) {
            return [];
        }

        return $this->hydrateTracksWithMusicDb($historyRows);
    }

    public function countForUser(string $userId, ?string $from = null, ?string $to = null): int
    {
        if (strlen($userId) !== 32) {
            return 0;
        }

        $sql = 'SELECT COUNT(id) AS total FROM user_listening_history WHERE user_id = UNHEX(:uid)';
        $params = ['uid' => $userId];
        [$sql, $params] = $this->applyDateRange($sql, $params, $from, $to);

        $rows = $this->userDb()->execute($sql, $params);

        return (int) ($rows[0]['total'] ?? 0);
    }

    /**
     * Adım 09 — tarih aralığı filtresi (parametreli sorgu: tarih asla SQL'e gömülmez).
     *
     * Hard Rule 3: yalnız `Y-m-d` formatı kabul edilir; başka bir değer
     * sessizce yok sayılır (injection kapıları kapatılır). `from`/`to` tek
     * başlarına da çalışır (aralık opsiyonel).
     *
     * @param array<string, mixed> $params
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function applyDateRange(string $sql, array $params, ?string $from, ?string $to): array
    {
        if ($from !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) === 1) {
            $sql .= ' AND h.started_at >= :from_date';
            $params['from_date'] = $from . ' 00:00:00';
        }
        if ($to !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to) === 1) {
            $sql .= ' AND h.started_at <= :to_date';
            $params['to_date'] = $to . ' 23:59:59';
        }

        return [$sql, $params];
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private function hydrateTracksWithMusicDb(array $rows): array
    {
        $hexes = array_unique(array_filter(array_column($rows, 'music_hex')));
        if ($hexes === []) {
            return $rows;
        }

        $inClauses = [];
        $params = [];
        foreach ($hexes as $idx => $hex) {
            $paramName = 'id' . $idx;
            $inClauses[] = 'UNHEX(:' . $paramName . ')';
            $params[$paramName] = $hex;
        }

        $sql = 'SELECT HEX(m.id) AS music_hex,
                       m.title,
                       m.duration_sec,
                       a.name AS artist_name
                  FROM musics m
                  JOIN artists a ON a.id = m.artist_id
                 WHERE m.id IN (' . implode(',', $inClauses) . ')
                   AND m.is_deleted = 0';

        $musicDetails = $this->musicDb()->execute($sql, $params);
        $indexedDetails = [];
        foreach ($musicDetails as $item) {
            $indexedDetails[(string) $item['music_hex']] = $item;
        }

        $hydrated = [];
        foreach ($rows as $r) {
            $hex = (string) ($r['music_hex'] ?? '');
            $details = $indexedDetails[$hex] ?? [
                'title' => 'Bilinmeyen Parça',
                'duration_sec' => 0,
                'artist_name' => 'Bilinmeyen Sanatçı',
            ];
            $hydrated[] = array_merge($r, $details);
        }

        return $hydrated;
    }

    private function userDb(): IDatabaseManager
    {
        return $this->userRegistry->get(self::DB_KEY);
    }

    private function musicDb(): IDatabaseManager
    {
        return $this->musicRegistry->get('music');
    }
}
