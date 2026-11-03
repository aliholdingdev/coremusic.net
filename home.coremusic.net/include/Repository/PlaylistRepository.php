<?php declare(strict_types=1);

namespace CoreMusic\Home\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Home\Container\HomeContainer;

/**
 * PlaylistRepository — coremusic_playlist şeması veri erişimi.
 *
 * ADR-002: yalnız DatabaseManager üzerinden prepared statement; SELECT * yok.
 * Bağlantı kaynağı TEKTTİR: shared ConnectionFactory registry — repository
 * kendi DatabaseManager'ını KURMAZ (aynı şemaya tek connection).
 *
 * P0-1/P0-2 (2026-10-30): `fromEnvironment()` geri kondu (çağırıcı:
 * RecentTracksComponent:236) + `home_playlist` kaydı HomeContainer'da.
 */
final class PlaylistRepository
{
    private const DB_KEY = 'home_playlist';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
    ) {}

    /**
     * Statik fabrika — registry kaydı idempotent ve lazy'dir; bağlantı AÇILMAZ.
     */
    public static function fromEnvironment(): self
    {
        return new self(HomeContainer::databaseRegistry());
    }

    public function findFeaturedPlaylists(int $limit = 6): array
    {
        $sql = 'SELECT HEX(p.id)         AS playlist_hex,
                       p.name            AS name,
                       p.description     AS description,
                       p.cover_art_url   AS cover_art_url,
                       p.total_tracks    AS total_tracks
                  FROM playlists p
                 WHERE p.is_deleted = 0
                   AND p.visibility = \'public\'
                 ORDER BY p.play_count DESC, p.created_at ASC
                 LIMIT ' . (int) $limit;

        try {
            return $this->db()->execute($sql, []);
        } catch (\Throwable) {
            return [];
        }
    }

    private function db(): IDatabaseManager
    {
        return $this->registry->get(self::DB_KEY);
    }
}
