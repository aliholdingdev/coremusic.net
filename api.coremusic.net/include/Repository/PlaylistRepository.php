<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Security\UuidV7;

/**
 * PlaylistRepository — coremusic_playlist veri erişimi (IPlaylistRepository implementasyonu).
 *
 * Faz 4. Şema: playlists (BINARY(16) UUID-v7 PK) + playlist_tracks
 * (BINARY(16) UUID-v7 PK, UNIQUE(playlist_id, music_id, position) — Karar 4
 * korunur; sema SSOT: .ai/.sql/mysql/coremusic_playlist.sql canlı dump 2026-10-22).
 * ADR-002: prepared statement yalnız · SELECT * yok · ID'ler UNHEX(:id) hex-string bind.
 */
final class PlaylistRepository implements IPlaylistRepository
{
    private const DB_KEY = 'playlist';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
    ) {}

    public function findByUserId(string $userId): array
    {
        return $this->db()->execute(
            'SELECT HEX(p.id)            AS playlist_id,
                    p.name               AS name,
                    p.description        AS description,
                    p.visibility         AS visibility,
                    p.track_count        AS track_count,
                    p.total_duration     AS total_duration,
                    p.created_at         AS created_at
               FROM playlists p
              WHERE p.user_id = UNHEX(:uid)
                AND p.is_deleted = 0
              ORDER BY p.created_at DESC, p.id ASC',
            ['uid' => $userId]
        );
    }

    public function findById(string $playlistId): ?array
    {
        if (strlen($playlistId) !== 32 || !ctype_xdigit($playlistId)) {
            return null;
        }

        $rows = $this->db()->execute(
            'SELECT HEX(p.id)            AS playlist_id,
                    HEX(p.user_id)       AS owner_id_hex,
                    p.name               AS name,
                    p.description        AS description,
                    p.visibility         AS visibility,
                    p.track_count        AS track_count,
                    p.total_duration     AS total_duration,
                    p.created_at         AS created_at
               FROM playlists p
              WHERE p.id = UNHEX(:id)
                AND p.is_deleted = 0
              LIMIT 1',
            ['id' => $playlistId]
        );

        return $rows === [] ? null : $rows[0];
    }

    public function insert(array $data): string
    {
        $id = UuidV7::generateHex();

        $this->db()->write(
            'INSERT INTO playlists (id, user_id, name, description)
             VALUES (UNHEX(:id), UNHEX(:uid), :name, :description)',
            [
                'id'          => $id,
                'uid'         => (string) ($data['user_id'] ?? ''),
                'name'        => (string) ($data['name'] ?? ''),
                'description' => isset($data['description']) ? (string) $data['description'] : null,
            ]
        );

        return $id;
    }

    public function appendTrack(string $playlistId, string $trackId): int
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            // Yarış koşulu reddi (Adım 07 Hard Rule 3): önce playlist satırı
            // FOR UPDATE ile kilitlenir → aynı listenin eşzamanlı eklemeleri
            // sıraya girer; MAX+1 bu kilit içinde okunur.
            // NOT: MAX tüm satırları kapsar (is_deleted DAHİL) — aksi hâlde aynı
            // şarkının eski soft-delete satırıyla UNIQUE(playlist_id, music_id,
            // position) çakışması 500 üretirdi.
            $db->execute(
                'SELECT id FROM playlists WHERE id = UNHEX(:pid) FOR UPDATE',
                ['pid' => $playlistId]
            );

            $rows = $db->execute(
                'SELECT COALESCE(MAX(position), -1) AS max_pos
                   FROM playlist_tracks
                  WHERE playlist_id = UNHEX(:pid)',
                ['pid' => $playlistId]
            );
            $position = (int) ($rows[0]['max_pos'] ?? -1) + 1;

            $db->write(
                'INSERT INTO playlist_tracks (playlist_id, music_id, position)
                 VALUES (UNHEX(:pid), UNHEX(:mid), :position)',
                ['pid' => $playlistId, 'mid' => $trackId, 'position' => $position]
            );

            // Denormalize sayaç tutarlılığı (playlist_stats'a dokunulmaz — Faz 4 kapsamı dışı).
            $db->write(
                'UPDATE playlists SET track_count = track_count + 1 WHERE id = UNHEX(:pid)',
                ['pid' => $playlistId]
            );

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }

        return $position;
    }

    public function deleteTrack(string $playlistId, string $trackId): bool
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            $db->write(
                'UPDATE playlist_tracks
                    SET is_deleted = 1, deleted_at = NOW()
                  WHERE playlist_id = UNHEX(:pid)
                    AND music_id = UNHEX(:mid)
                    AND is_deleted = 0',
                ['pid' => $playlistId, 'mid' => $trackId]
            );

            $db->write(
                'UPDATE playlists
                    SET track_count = GREATEST(track_count - 1, 0)
                  WHERE id = UNHEX(:pid)',
                ['pid' => $playlistId]
            );

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }

        return true;
    }

    public function updateMeta(string $playlistId, array $fields): bool
    {
        // Yalnızca izin verilen alanlar (controller/service whitelist'i ile aynı küme).
        $allowed = ['name', 'description', 'visibility'];
        $sets    = [];
        $params  = ['id' => $playlistId];

        foreach ($allowed as $column) {
            if (!array_key_exists($column, $fields)) {
                continue;
            }
            $sets[]                = "{$column} = :{$column}";
            $params[$column]       = $fields[$column];
        }

        if ($sets === []) {
            return false;
        }

        return $this->db()->write(
            'UPDATE playlists SET ' . implode(', ', $sets) . ' WHERE id = UNHEX(:id) AND is_deleted = 0',
            $params
        );
    }

    public function softDeleteWithTracks(string $playlistId): bool
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            // Hard Rule 4: hard DELETE YASAK — liste + şarkı kayıtları soft-delete.
            $db->write(
                'UPDATE playlists
                    SET is_deleted = 1, deleted_at = NOW()
                  WHERE id = UNHEX(:pid)
                    AND is_deleted = 0',
                ['pid' => $playlistId]
            );

            $db->write(
                'UPDATE playlist_tracks
                    SET is_deleted = 1, deleted_at = NOW()
                  WHERE playlist_id = UNHEX(:pid)
                    AND is_deleted = 0',
                ['pid' => $playlistId]
            );

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }

        return true;
    }

    public function findTrackIds(string $playlistId): array
    {
        $rows = $this->db()->execute(
            'SELECT HEX(pt.music_id) AS music_hex
               FROM playlist_tracks pt
              WHERE pt.playlist_id = UNHEX(:pid)
                AND pt.is_deleted = 0
              ORDER BY pt.position ASC, pt.id ASC',
            ['pid' => $playlistId]
        );

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = strtolower((string) $row['music_hex']);
        }

        return $ids;
    }

    public function replacePositions(string $playlistId, array $musicIds): bool
    {
        if ($musicIds === []) {
            return true;
        }

        $db = $this->db();
        $db->beginTransaction();

        try {
            foreach (array_values($musicIds) as $position => $musicId) {
                $db->write(
                    'UPDATE playlist_tracks
                        SET position = :position, updated_at = NOW()
                      WHERE playlist_id = UNHEX(:pid)
                        AND music_id = UNHEX(:mid)
                        AND is_deleted = 0',
                    [
                        'pid'      => $playlistId,
                        'mid'      => (string) $musicId,
                        'position' => $position,
                    ]
                );
            }

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }

        return true;
    }

    public function findTracks(string $playlistId, int $limit): array
    {
        return $this->db()->execute(
            'SELECT HEX(pt.music_id)  AS music_hex,
                    m.title           AS title,
                    m.duration_sec    AS duration_sec,
                    a.name            AS artist_name,
                    pt.position       AS position
               FROM playlist_tracks pt
               JOIN musics m ON m.id = pt.music_id
               JOIN artists a ON a.id = m.artist_id
              WHERE pt.playlist_id = UNHEX(:pid)
                AND pt.is_deleted = 0
                AND m.is_deleted = 0
                AND a.is_deleted = 0
              ORDER BY pt.position ASC, pt.id ASC
              LIMIT ' . (int) $limit,
            ['pid' => $playlistId]
        );
    }

    private function db(): IDatabaseManager
    {
        return $this->registry->get(self::DB_KEY);
    }
}
