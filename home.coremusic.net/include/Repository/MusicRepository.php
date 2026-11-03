<?php declare(strict_types=1);

namespace CoreMusic\Home\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Home\Container\HomeContainer;

/**
 * MusicRepository — coremusic_musics veri erişimi.
 *
 * Faz 4: home.coremusic.net MusicRepository pattern'i API'ye taşındı
 * (lazy registry → DatabaseManager; ADR-002: prepared statement yalnız, SELECT * yok).
 * Şema: musics (id BINARY(16) UUID-v7) + artists + music_files (primary dosya).
 *
 * P0-1 (2026-10-30): `implements IMusicRepository` kaldırıldı — home'da
 * `CoreMusic\Home\Repository\IMusicRepository` dosyası yok ve repo genelinde
 * bu tip yalnız kendi dosyasında geçiyordu (2 isabet: yorum L9 + implements L15;
 * API'deki aynı adlı arayüz farklı namespace, import edilmiyor) → 0-atıflı ölü kod.
 * `fromEnvironment()` 7 çağırıcı tarafından kullanıldığı için geri kondu.
 */
final class MusicRepository
{
    private const DB_KEY = 'home_music';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
    ) {}

    /**
     * Statik fabrika — çağıran: ComponentLoader:97 · RecentTracksComponent:197 ·
     * MusicStreamHandler:57 · diskplayer:52 · history:63 · nowplaying:63.
     * registry kaydı idempotent ve lazy'dir; bağlantı AÇILMAZ (P0-2).
     */
    public static function fromEnvironment(): self
    {
        return new self(HomeContainer::databaseRegistry());
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
                 LIMIT 1 ';

        $rows = $this->db()->execute($sql, ['id' => $musicIdHex]);

        return $rows === [] ? null : $rows[0];
    }

    public function findRecentTracks(): array
    {
        $sql = 'SELECT HEX(m.id)      AS music_hex,
                       m.title         AS title,
                       m.duration_sec  AS duration_sec,
                       a.name          AS artist_name
                  FROM musics m
                  JOIN artists a   ON a.id = m.artist_id
                  JOIN music_files f ON f.music_id = m.id
                 WHERE m.is_deleted = 0
                   AND a.is_deleted = 0
                   AND f.is_deleted = 0
                   AND f.is_primary = 1
                 ORDER BY m.created_at DESC, m.id ASC
                 LIMIT 12';

        return $this->db()->execute($sql);
    }

    private function db(): IDatabaseManager
    {
        return $this->registry->get(self::DB_KEY);
    }
}