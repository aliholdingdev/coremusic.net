<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\HomeSongButton;
use CoreMusic\Home\Repository\MusicRepository;

/**
 * RecentTracksComponent — En Son Dinlenen Şarkılar (v2.1.0)
 * PNG: home-1920 tam genişlik kart satırı (9 kart) / home-1024 bottom-left (2×2, 4 kart)
 *
 * Veri kaynağı: coremusic_musics (musics ⋈ artists ⋈ music_files is_primary=1).
 * DB'ye ulaşılamazsa veya boşsa PNG demo verisine düşer (testler bu yolu kullanır).
 */
final class RecentTracksComponent extends AbstractComponent
{
    /** Wide/4K satırında gösterilecek kart sayısı (Figma home-1920 = 9 kart) */
    private const WIDE_CARDS = 9;

    /** @var list<string> render edilmiş mini kart HTML'leri */
    public readonly array $cards;

    /**
     * @param list<array{t: string, a: string, d: string, art: string, stream?: string}>|null $tracks
     *        override verisi — null ise DB, DB yoksa PNG varsayılanları kullanılır
     * @param MusicRepository|null $repository
     *        veri erişimi (ComponentLoader::make injection'ı) — null ise fallback davranışı korunur
     */
    public function __construct(HomeLayoutVariant $variant, ?array $tracks = null, ?MusicRepository $repository = null)
    {
        parent::__construct($variant);

        $data = $tracks ?? $this->dbTracks($repository) ?? $this->defaultTracks();

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
    private function dbTracks(?MusicRepository $repository): ?array
    {
        if (!defined('DB_HOST')) {
            return null;
        }

        try {
            $rows = ($repository ?? MusicRepository::fromEnvironment())->findRecentTracks();
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
