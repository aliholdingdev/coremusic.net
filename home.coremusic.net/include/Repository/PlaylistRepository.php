<?php declare(strict_types=1);

namespace CoreMusic\Home\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Database\ConnectionFactory;

/**
 * PlaylistRepository — coremusic_playlist şeması veri erişimi.
 *
 * ADR-002: yalnız DatabaseManager üzerinden prepared statement; SELECT * yok.
 * Tembel (lazy) bağlantı: ilk sorguda açılır.
 * Bağlantı kaynağı TEKTTİR: shared ConnectionFactory registry — repository
 * kendi DatabaseManager'ını KURMAZ (aynı şemaya tek connection).
 */
final class PlaylistRepository
{
    private ?IDatabaseManager $db = null;

    public function __construct(
        private readonly string $dbHost,
        private readonly string $dbName,
        private readonly string $dbUser,
        private readonly string $dbPassword,
        private readonly int $dbPort,
        private readonly string $dbCharset,
    ) {}

    public static function fromEnvironment(): self
    {
        $name = defined('DB_PLAYLIST_NAME')
            ? (string)DB_PLAYLIST_NAME
            : (string)(($_ENV['DB_PLAYLIST_NAME'] ?? getenv('DB_PLAYLIST_NAME')) ?: 'coremusic_playlist');

        $host = defined('DB_HOST') ? (string)DB_HOST : (string)(($_ENV['DB_HOST'] ?? 'localhost') ?: 'localhost');
        $user = defined('DB_USER') ? (string)DB_USER : (string)(($_ENV['DB_USER'] ?? '') ?: '');
        $pass = defined('DB_PASSWORD') ? (string)DB_PASSWORD : (string)(($_ENV['DB_PASSWORD'] ?? '') ?: '');
        $port = defined('DB_PORT') ? (int)DB_PORT : (int)(($_ENV['DB_PORT'] ?? 3306) ?: 3306);
        $cs   = defined('DB_CHARSET') ? (string)DB_CHARSET : (string)(($_ENV['DB_CHARSET'] ?? '') ?: 'utf8mb4');

        return new self($host, $name, $user, $pass, $port, $cs);
    }

    /**
     * Vitrin / Home için öne çıkan sistem veya genel çalma listeleri.
     *
     * @return list<array{playlist_hex: string, name: string, description: ?string, cover_art_url: ?string, total_tracks: int}>
     */
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
                 LIMIT :limit';

        try {
            return $this->db()->execute($sql, ['limit' => $limit]);
        } catch (\Throwable) {
            return [];
        }
    }

    private function db(): IDatabaseManager
    {
        if ($this->db === null) {
            $factory = ConnectionFactory::getInstance();
            $key     = 'home_' . $this->dbName;
            if (!$factory->registry()->has($key)) {
                $factory->registry()->registerMySql(
                    $key,
                    $this->dbHost,
                    $this->dbName,
                    $this->dbUser,
                    $this->dbPassword,
                    $this->dbPort,
                    $this->dbCharset
                );
            }
            $this->db = $factory->registry()->get($key);
        }

        return $this->db;
    }
}
