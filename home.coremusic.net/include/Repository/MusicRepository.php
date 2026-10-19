<?php declare(strict_types=1);

namespace CoreMusic\Home\Repository;

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Database\DatabaseManager;

/**
 * MusicRepository — coremusic_musics veri erişimi (stream + component ortak).
 *
 * ADR-002: yalnız DatabaseManager üzerinden prepared statement; SELECT * yok.
 * Bağluk tembel (lazy): PDO ilk sorguda açılır — DB'siz ortamlarda (testler)
 * bağlantı denemesi yapılmaz.
 */
final class MusicRepository
{
    private ?DatabaseManager $db = null;

    private function __construct(
        private readonly string $dbHost,
        private readonly string $dbName,
        private readonly string $dbUser,
        private readonly string $dbPassword,
        private readonly int $dbPort,
        private readonly string $dbCharset,
    ) {
    }

    /** Ortam değişkenlerinden (defined/ENV) repository kurar — bağlantı henüz açılmaz. */
    public static function fromEnvironment(): self
    {
        $name = defined('DB_MUSIC_NAME')
            ? (string)DB_MUSIC_NAME
            : (string)(($_ENV['DB_MUSIC_NAME'] ?? getenv('DB_MUSIC_NAME')) ?: 'coremusic_musics');

        $host = defined('DB_HOST') ? (string)DB_HOST : (string)(($_ENV['DB_HOST'] ?? 'localhost') ?: 'localhost');
        $user = defined('DB_USER') ? (string)DB_USER : (string)(($_ENV['DB_USER'] ?? '') ?: '');
        $pass = defined('DB_PASSWORD') ? (string)DB_PASSWORD : (string)(($_ENV['DB_PASSWORD'] ?? '') ?: '');
        $port = defined('DB_PORT') ? (int)DB_PORT : (int)(($_ENV['DB_PORT'] ?? 3306) ?: 3306);
        $cs   = defined('DB_CHARSET') ? (string)DB_CHARSET : (string)(($_ENV['DB_CHARSET'] ?? '') ?: 'utf8mb4');

        return new self($host, $name, $user, $pass, $port, $cs);
    }

    /**
     * musicIdHex (32 karakter) için birincil dosya satırı.
     *
     * @return array{file_path: string, file_format: string, file_size: int}|null
     * @throws \Throwable bağlantı/sorgu hatası (stream handler 500 üretir — davranış korunur)
     */
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

    /**
     * En son dinlenen parçalar (deterministik sıralama).
     *
     * @return list<array{music_hex: string, title: string, duration_sec: mixed, artist_name: string}>
     *         boş liste = DB yok / hata / kayıt yok → çağıran demo fallback'e düşer
     *
     * @throws \Throwable bağlantı/sorgu hatası (çağıran \Throwable yakalar)
     */
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

    /** Tembel bağlantı: ilk sorguda DatabaseManager + PDO oluşturulur. */
    private function db(): DatabaseManager
    {
        return $this->db ??= new DatabaseManager(new DatabaseConfig(
            $this->dbHost,
            $this->dbName,
            $this->dbUser,
            $this->dbPassword,
            $this->dbPort,
            $this->dbCharset,
        ));
    }
}
