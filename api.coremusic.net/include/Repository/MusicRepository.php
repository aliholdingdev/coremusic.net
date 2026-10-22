<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;

/**
 * MusicRepository — coremusic_musics veri erişimi (IMusicRepository implementasyonu).
 *
 * Faz 4: home.coremusic.net MusicRepository pattern'i API'ye taşındı
 * (lazy registry → DatabaseManager; ADR-002: prepared statement yalnız, SELECT * yok).
 * Şema: musics (id BINARY(16) UUID-v7) + artists + music_files (primary dosya).
 */
final class MusicRepository implements IMusicRepository
{
    private const DB_KEY = 'music';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
    ) {}

    public function findById(string $trackId): ?array
    {
        if (strlen($trackId) !== 32 || !ctype_xdigit($trackId)) {
            return null;
        }

        $sql = 'SELECT HEX(m.id)   AS music_hex,
                       m.title      AS title,
                       m.duration_sec AS duration_sec,
                       a.name       AS artist_name
                  FROM musics m
                  JOIN artists a     ON a.id = m.artist_id
                  JOIN music_files f ON f.music_id = m.id
                 WHERE m.id = UNHEX(:id)
                   AND m.is_deleted = 0
                   AND a.is_deleted = 0
                   AND f.is_deleted = 0
                   AND f.is_primary = 1
                 LIMIT 1';

        $rows = $this->db()->execute($sql, ['id' => $trackId]);

        return $rows === [] ? null : $rows[0];
    }

    public function findPrimaryFile(string $musicIdHex): ?array
    {
        if (strlen($musicIdHex) !== 32 || !ctype_xdigit($musicIdHex)) {
            return null;
        }

        $sql = 'SELECT f.file_path, f.file_format, f.file_size
                  FROM music_files f
                  JOIN musics m ON m.id = f.music_id
                 WHERE f.music_id = UNHEX(:id)
                   AND m.is_deleted = 0
                   AND f.is_deleted = 0
                 ORDER BY f.is_primary DESC, f.id ASC
                 LIMIT 1';

        $rows = $this->db()->execute($sql, ['id' => $musicIdHex]);

        return $rows === [] ? null : $rows[0];
    }

    public function findMany(int $limit, int $offset): array
    {
        // LIMIT/OFFSET int cast ile gömülür (native prepare bu alanlarda bind desteklemez).
        $sql = 'SELECT DISTINCT HEX(m.id)     AS music_hex,
                       m.title        AS title,
                       m.duration_sec AS duration_sec,
                       a.name         AS artist_name
                  FROM musics m
                  JOIN artists a     ON a.id = m.artist_id
                  JOIN music_files f ON f.music_id = m.id
                 WHERE m.is_deleted = 0
                   AND a.is_deleted = 0
                   AND f.is_deleted = 0
                   AND f.is_primary = 1
                 ORDER BY m.created_at DESC, m.id ASC
                 LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

        return $this->db()->execute($sql);
    }

    public function search(string $query, int $limit): array
    {
        // LIKE wildcard kaçışı: kullanıcı girdisinde % _ \ etkisizleşir.
        $like = '%' . addcslashes($query, '%_\\') . '%';

        $sql = 'SELECT DISTINCT HEX(m.id)     AS music_hex,
                       m.title        AS title,
                       m.duration_sec AS duration_sec,
                       a.name         AS artist_name
                  FROM musics m
                  JOIN artists a     ON a.id = m.artist_id
                  JOIN music_files f ON f.music_id = m.id
                 WHERE m.is_deleted = 0
                   AND a.is_deleted = 0
                   AND f.is_deleted = 0
                   AND f.is_primary = 1
                   AND (m.title LIKE :q OR a.name LIKE :q)
                 ORDER BY m.title ASC, m.id ASC
                 LIMIT ' . (int) $limit;

        return $this->db()->execute($sql, ['q' => $like]);
    }

    public function count(): int
    {
        $sql = 'SELECT COUNT(DISTINCT m.id) AS total
                  FROM musics m
                  JOIN music_files f ON f.music_id = m.id
                 WHERE m.is_deleted = 0
                   AND f.is_deleted = 0
                   AND f.is_primary = 1';

        $rows = $this->db()->execute($sql);

        return (int) ($rows[0]['total'] ?? 0);
    }

    private function db(): IDatabaseManager
    {
        return $this->registry->get(self::DB_KEY);
    }
}
