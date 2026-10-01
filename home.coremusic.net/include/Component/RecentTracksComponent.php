<?php declare(strict_types=1);

namespace CoreMusic\Home\Component;

use CoreMusic\Home\Class\AbstractComponent;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\HomeSongButton;
use CoreMusic\Home\Repository\MusicRepository;

/**
 * RecentTracksComponent — Alt satır bölümleri: En Son + Playlistler + Sıradaki (v4.0.0)
 *
 * home.php her varyansta 'recent-tracks''i TEK ÇAĞIRIR; tek view tüm bölümleri üretir:
 *   Wide/4K (1920): En Son (1 sıra × 10 kart) + Playlistler (1 sıra × 6 kart)
 *                   [Sıradaki Şarkılar — mockup'ta YOK (aaa.md ASCII + Figma nodesuz)]
 *   Embedded (1024): .home-layout__bottom--embedded'e 3 doğrudan grid-çocuğu:
 *                   kolon1 En Son (2×2, 4 kart) · kolon2 Playlistler (2×2, 3 kart
 *                   + "Playlist listesini görüntüle" link kartı) · kolon3 Sıradaki
 *                   (1 kart, 186×109 cam panel)
 *
 * Figma/PNG SSOT:
 *   extracted-1024.md — 1639:9904 "Menu En Son Şarkılar" · 1639:9892 "Menu Oynatma Listesi"
 *                       (+1639:9893 link kartı) · 1639:9910 "Sıradaki Şarkı" (185.978×109)
 *   extracted-1920.md — 2856:22290 (En Son kartı, y−220) · 2850:21627 (playlist başlığı,
 *                       fs13 Avalon-Bold ls0.975) · 2890:7922-7927 (6 playlist kartı, y−89)
 *
 * Veri: En Son = coremusic_musics (musics ⋈ artists ⋈ music_files is_primary=1);
 *       DB yoksa PNG demo verisi (testler bu yolu kullanır).
 *       Playlist + Sıradaki = PNG sabit içerik (DB tablosu yok).
 */
final class RecentTracksComponent extends AbstractComponent
{
    /** En Son — wide/4K satırı (PNG 1920: 1 sıra × 10 kart, pitch 184) */
    private const WIDE_CARDS = 10;

    /** En Son — embedded 2×2 grid (Figma 1639:9904 = 4 kart) */
    private const EMBEDDED_CARDS = 4;

    /** Playlist — wide 1 sıra × 6 (PNG 1920: 2890:7922-7927) */
    private const WIDE_PLAYLIST_CARDS = 6;

    /** Playlist — embedded 2×2 = 3 kart + link kartı (Figma 1639:9892/9893) */
    private const EMBEDDED_PLAYLIST_CARDS = 3;

    /** @var list<string> "En Son Dinlenen Şarkılar" mini kart HTML'leri */
    public readonly array $cards;

    /** @var list<string> "Son Oluşturlan & Sistem Taraından Oluşturlan Playlistler" kart HTML'leri */
    public readonly array $playlistCards;

    /** @var array{t: string, album: string, a: string, art: string}|null
     *  Sıradaki Şarkı verisi — yalnız embedded (wide: null, mockup'ta bölüm yok) */
    public readonly ?array $nextCard;

    /** Link kartı ikonu (Figma 1639:9897 — 10×10 album thumb, radius 115) */
    public readonly string $playlistLinkIcon;

    /**
     * @param list<array{t: string, a: string, d: string, art: string, stream?: string}>|null $tracks
     *        override verisi (yalnız "En Son"u besler) — null ise DB, DB yoksa PNG varsayılanları
     * @param MusicRepository|null $repository veri erişimi (ComponentLoader::make injection'ı)
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

        $max = $variant->isWide() ? self::WIDE_CARDS : self::EMBEDDED_CARDS;
        $this->cards = array_slice($cards, 0, $max);

        $playlistMax = $variant->isWide() ? self::WIDE_PLAYLIST_CARDS : self::EMBEDDED_PLAYLIST_CARDS;
        $this->playlistCards = array_map(
            fn (array $p): string => HomeSongButton::html(
                [
                    't'      => (string)$p['t'],
                    's'      => (string)$p['a'],
                    'art'    => (string)$p['art'],
                    'stream' => '',
                ],
                'mini-card__subtitle',
                (string)$p['d']
            ),
            array_slice($this->defaultPlaylists(), 0, $playlistMax)
        );

        $this->nextCard = $variant->isWide() ? null : [
            't'     => 'Göksel - Sevil Neşelen',
            'album' => 'Hayat Rüya Gibi',
            'a'     => 'Göksel',
            'art'   => $this->asset('/Image/res-pink/album-goksel.png'),
        ];

        $this->playlistLinkIcon = $this->asset('/Image/res-pink/album-kursat.png');
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
     * PNG home-1024 node 1639:9904 demo içeriği (4 kart) — 1920 satırı bu
     * 4'lüğün döngüsüyle 10 karta tamamlanır (extracted-1024.md metinleri, birebir).
     *
     * @return list<array{t: string, a: string, d: string, art: string}>
     */
    private function defaultTracks(): array
    {
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/album-kursat.png');

        $seed = [
            ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05', 'art' => $art1],
            ['t' => 'Göksel - Kabahat Seni Se...', 'a' => 'Göksel', 'd' => '00:02:05', 'art' => $art1],
            ['t' => 'Barış Manco - Gulpembe', 'a' => 'Barış Manco', 'd' => '00:04:01', 'art' => $art2],
            ['t' => 'Kış Masalı Ensturmental', 'a' => 'Org Dersleri', 'd' => '00:01:10', 'art' => $art2],
        ];

        $tracks = [];
        while (count($tracks) < self::WIDE_CARDS) {
            foreach ($seed as $t) {
                $tracks[] = $t;
                if (count($tracks) >= self::WIDE_CARDS) {
                    break;
                }
            }
        }

        return $tracks;
    }

    /**
     * PNG playlist içeriği — embedded 3 kart (1639:9899/9900/9901) + wide 6 kart
     * (2890:7922-7927, sıra: Supermix → Ruh haline → Yeni Sevilen, sonra tekrar).
     * Metinler extracted-1024/-1920 'metin:' alanlarından birebir (sic).
     *
     * @return list<array{t: string, a: string, d: string, art: string}>
     */
    private function defaultPlaylists(): array
    {
        $art1 = $this->asset('/Image/res-pink/album-goksel.png');
        $art2 = $this->asset('/Image/res-pink/album-kursat.png');

        $seed = [
            ['t' => "En Sevilen Supermix'im", 'a' => 'Sistem Tarafından Oluşturuldu', 'd' => '00:03:05', 'art' => $art2],
            ['t' => "Ruh haline Göre Günlük mix'im", 'a' => 'Sistem Tarafından Oluşturuldu', 'd' => '01:05:00', 'art' => $art1],
            ['t' => "Yeni Sevilen Türleri Keşfet mix'im", 'a' => 'Sistem Tarafından Oluşturuldu', 'd' => '00:50:15', 'art' => $art2],
        ];

        return array_merge($seed, $seed);
    }
}
