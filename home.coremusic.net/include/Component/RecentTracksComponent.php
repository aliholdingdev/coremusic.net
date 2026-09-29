<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\HomeSongButton;
use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Database\DatabaseManager;

/**
 * RecentTracksComponent — En Son Dinlenen Şarkılar (v2.1.0)
 * PNG: home-1920 tam genişlik kart satırı (9 kart) / home-1024 bottom-left (2×2, 4 kart)
 *
 * Veri kaynağı: coremusic_musics (musics ⋈ artists ⋈ music_files is_primary=1).
 * DB'ye ulaşılamazsa veya boşsa PNG demo verisine düşer (testler bu yolu kullanır).
 */
final class RecentTracksComponent extends AbstractComponent
{
    /** Veritabanından çekilecek en fazla kayıt sayısı */
    private const DB_LIMIT = 12;

    /** Wide/4K satırında gösterilecek kart sayısı (Figma home-1920 = 9 kart) */
    private const WIDE_CARDS = 9;

    /** @var list<string> render edilmiş mini kart HTML'leri */
    public readonly array $cards;

    /**
     * @param list<array{t: string, a: string, d: string, art: string, stream?: string}>|null $tracks
     *        override verisi — null ise DB, DB yoksa PNG varsayılanları kullanılır
     */
    public function __construct(HomeLayoutVariant $variant, ?array $tracks = null)
    {
        parent::__construct($variant);

        $data = $tracks ?? $this->dbTracks() ?? $this->defaultTracks();

        $cards = array_map(
            fn (array $t): string => HomeSongButton::html(
                [
                    't'      => (string)$t['t'],
                    's'      => (string)$t['a'],
                    'art'    => (string)($t['art'] ?? ''),
                    'stream' => (string)($t['stream'] ?? ''),
                ],
                'mini-card__subtitle',
                (string)$t['d']
            ),
            $data
        );

        $max = $variant->isWide() ? self::WIDE_CARDS : 3;
        $this->cards = array_slice($cards, 0, $max);
    }

    public function key(): string
    {
        return 'recent-tracks';
    }

    /**
     * DB'den son eklenen parçalar (deterministik sıralama).
     *
     * @return list<array{t: string, a: string, d: string, art: string, stream: string}>|null
     *         null = DB yok / hata / boş liste → demo fallback
     */
    private function dbTracks(): ?array
    {
        if (!defined('DB_HOST')) {
            return null;
        }

        $dbName = defined('DB_MUSIC_NAME') ? DB_MUSIC_NAME : 'coremusic_musics';

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
                 LIMIT ' . self::DB_LIMIT;

        try {
            $db  = new DatabaseManager(new DatabaseConfig(
                (string)DB_HOST,
                (string)$dbName,
                (string)DB_USER,
                (string)DB_PASSWORD,
                (int)DB_PORT,
                (string)DB_CHARSET,
            ));
            $rows = $db->execute($sql);
        } catch (\Throwable) {
            return null;
        }

        if ($rows === []) {
            return null;
        }

        $tracks = [];
        foreach ($rows as $row) {
            $hex = (string)($row['music_hex'] ?? '');
            if ($hex === '') {
                continue;
            }
            $tracks[] = [
                't'      => (string)$row['title'],
                'a'      => (string)$row['artist_name'],
                'd'      => self::formatDuration($row['duration_sec'] ?? null),
                'art'    => '',
                'stream' => '/stream/' . strtolower($hex),
            ];
        }

        return $tracks !== [] ? $tracks : null;
    }

    /** Saniye → HH:MM:SS (NULL → 00:00:00 — import duration_sec yazmaz). */
    private static function formatDuration(mixed $seconds): string
    {
        if (!is_numeric($seconds)) {
            return '00:00:00';
        }
        $s = max(0, (int)$seconds);
        return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
    }

    /**
     * PNG home-1920 satır sırası (9 kart); 'art' mutlak asset URL'dir.
     *
     * @return list<array{t: string, a: string, d: string, art: string}>
     */
    private function defaultTracks(): array
    {
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/album-kursat.png');

        return [
            ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:05:00', 'art' => $art1],
            ['t' => 'Göksel - Kabahat Senin Se', 'a' => 'Göksel', 'd' => '00:04:12', 'art' => $art2],
            ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $art1],
            ['t' => 'Göksel - Donbil Neşeler', 'a' => 'Göksel', 'd' => '00:04:37', 'art' => $art2],
            ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:05:00', 'art' => $art1],
            ['t' => 'Göksel - Kabahat Senin Se', 'a' => 'Göksel', 'd' => '00:04:12', 'art' => $art2],
            ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $art1],
            ['t' => 'Göksel - Donbil Neşeler', 'a' => 'Göksel', 'd' => '00:04:37', 'art' => $art2],
            ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $art1],
        ];
    }
}
