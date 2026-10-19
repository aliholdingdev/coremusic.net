<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Component;

use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Component\RecentTracksComponent;
use PHPUnit\Framework\TestCase;

final class RecentTracksComponentTest extends TestCase
{
    /** @param list<array{t: string, a: string, d: string, art: string}> */
    private static function tracks(int $count): array
    {
        return array_map(
            static fn (int $i): array => [
                't'   => "Track {$i}",
                'a'   => "Artist {$i}",
                'd'   => '00:03:00',
                'art' => "https://assets.example/cover-{$i}.png",
            ],
            range(1, $count)
        );
    }

    public function testKey_returnsRegistryPartialName(): void
    {
        // Arrange + Act
        $component = new RecentTracksComponent(HomeLayoutVariant::Embedded);

        // Assert
        $this->assertSame('recent-tracks', $component->key());
    }

    public function testConstructor_defaultData_embeddedVariantLimitsToFourCards(): void
    {
        // Arrange + Act — defaultTracks() 10 kart üretir, embedded 2×2 grid (Figma node 1639:9904) 4 kart gösterir
        $component = new RecentTracksComponent(HomeLayoutVariant::Embedded);

        // Assert — En Son 4, Playlist 3 (1639:9892 + link kartı), Sıradaki verisi var (1639:9910)
        $this->assertCount(4, $component->cards);
        $this->assertCount(3, $component->playlistCards);
        $this->assertNotNull($component->nextCard);
    }

    public function testConstructor_defaultData_wideAndFourKKeepAllTenCards(): void
    {
        // Arrange + Act
        $wide  = new RecentTracksComponent(HomeLayoutVariant::Wide);
        $fourK = new RecentTracksComponent(HomeLayoutVariant::FourK);

        // Assert — 1920 mockup: En Son 10 kart, Playlist 6 kart, Sıradaki bölümü yok
        $this->assertCount(10, $wide->cards);
        $this->assertCount(10, $fourK->cards);
        $this->assertCount(6, $wide->playlistCards);
        $this->assertNull($wide->nextCard);
    }

    public function testConstructor_overrideData_truncatesToFourOnEmbedded(): void
    {
        // Arrange + Act
        $component = new RecentTracksComponent(HomeLayoutVariant::Embedded, self::tracks(5));

        // Assert — ilk 4 kart korunur (2×2 grid)
        $this->assertCount(4, $component->cards);
        $this->assertStringContainsString('Track 1', $component->cards[0]);
        $this->assertStringContainsString('Track 4', $component->cards[3]);
        $this->assertStringNotContainsString('Track 5', implode('', $component->cards));
    }

    public function testConstructor_overrideData_keepsAllCardsOnWide(): void
    {
        // Arrange + Act
        $component = new RecentTracksComponent(HomeLayoutVariant::Wide, self::tracks(4));

        // Assert
        $this->assertCount(4, $component->cards);
        $this->assertStringContainsString('Track 4', $component->cards[3]);
    }

    public function testConstructor_escapesOverrideTitles(): void
    {
        // Arrange
        $tracks = [['t' => '<b>Raw</b>', 'a' => 'A', 'd' => '00:01:00', 'art' => '']];

        // Act
        $component = new RecentTracksComponent(HomeLayoutVariant::Wide, $tracks);
        $html = implode('', $component->cards);

        // Assert
        $this->assertStringNotContainsString('<b>Raw</b>', $html);
        $this->assertStringContainsString('&lt;b&gt;Raw&lt;/b&gt;', $html);
    }

    public function testConstructor_defaultSeed_embeddedHasOneVideoCardPerSection(): void
    {
        // Arrange + Act — embedded: En Son 4 kart, Playlist 3 kart
        $component = new RecentTracksComponent(HomeLayoutVariant::Embedded);
        $recent    = implode('', $component->cards);
        $playlists = implode('', $component->playlistCards);

        // Assert — video rozeti: En Son 1/4 (Kış Masalı Ensturmental), Playlist 1/3 (Supermix)
        $this->assertSame(1, substr_count($recent, '/Image/res-pink/video.png'));
        $this->assertSame(1, substr_count($playlists, '/Image/res-pink/video.png'));
        $this->assertStringContainsString('Kış Masalı Ensturmental', $component->cards[3]);
        $this->assertStringContainsString("En Sevilen Supermix&#039;im", $component->playlistCards[0]);

        // Her kartta kılıf + rozet (4 + 3 kart, kart başına 1)
        $this->assertSame(4, substr_count($recent, '/Image/res-pink/cd-case.png'));
        $this->assertSame(3, substr_count($playlists, '/Image/res-pink/cd-case.png'));
        $this->assertSame(3, substr_count($recent, '/Image/res-pink/music.png'));
        $this->assertSame(2, substr_count($playlists, '/Image/res-pink/music.png'));
    }

    public function testConstructor_defaultSeed_wideRepeatsVideoCardPerSeedCycle(): void
    {
        // Arrange + Act — wide: En Son 10 kart (4'lük döngü: video 4. ve 8. sırada),
        //                   Playlist 6 kart (3'lük döngü: video 1. ve 4. sırada)
        $component = new RecentTracksComponent(HomeLayoutVariant::Wide);
        $recent    = implode('', $component->cards);
        $playlists = implode('', $component->playlistCards);

        // Assert
        $this->assertSame(2, substr_count($recent, '/Image/res-pink/video.png'));
        $this->assertSame(8, substr_count($recent, '/Image/res-pink/music.png'));
        $this->assertSame(10, substr_count($recent, '/Image/res-pink/cd-case.png'));
        $this->assertSame(2, substr_count($playlists, '/Image/res-pink/video.png'));
        $this->assertSame(4, substr_count($playlists, '/Image/res-pink/music.png'));
        $this->assertSame(6, substr_count($playlists, '/Image/res-pink/cd-case.png'));
    }

    public function testConstructor_overrideDataWithoutKind_defaultsToSongBadge(): void
    {
        // Arrange — override verisinde 'k' yok (DB/dbTracks ile aynı şekil)
        $component = new RecentTracksComponent(HomeLayoutVariant::Wide, self::tracks(3));
        $html      = implode('', $component->cards);

        // Assert — hiç video rozeti yok, her kartta music.png; override verisi de CD katmanı alır (3/3)
        $this->assertSame(0, substr_count($html, '/Image/res-pink/video.png'));
        $this->assertSame(3, substr_count($html, '/Image/res-pink/music.png'));
        $this->assertSame(3, substr_count($html, '/Image/res-pink/cd-case.png'));
        $this->assertSame(3, substr_count($html, '/Image/res-pink/cd-overlay.png'));
    }

    public function testConstructor_overrideDataWithVideoKind_rendersVideoBadge(): void
    {
        // Arrange
        $tracks = [
            ['t' => 'Video Kart', 'a' => 'A', 'd' => '00:01:00', 'art' => '', 'k' => 'video'],
            ['t' => 'Şarkı Kart', 'a' => 'B', 'd' => '00:02:00', 'art' => ''],
        ];

        // Act
        $component = new RecentTracksComponent(HomeLayoutVariant::Embedded, $tracks);

        // Assert — 1 video + 1 music rozeti
        $html = implode('', $component->cards);
        $this->assertSame(1, substr_count($html, '/Image/res-pink/video.png'));
        $this->assertSame(1, substr_count($html, '/Image/res-pink/music.png'));
    }

    public function testConstructor_everyCardRendersCdOverlay(): void
    {
        // Arrange + Act — CD katmanı kapsamı: TÜM song/playlist kartları (kullanıcı kararı)
        $embedded = new RecentTracksComponent(HomeLayoutVariant::Embedded);
        $wide     = new RecentTracksComponent(HomeLayoutVariant::Wide);

        $emRecent = implode('', $embedded->cards);
        $emList   = implode('', $embedded->playlistCards);
        $wdRecent = implode('', $wide->cards);
        $wdList   = implode('', $wide->playlistCards);

        // Assert — her kartta tam 1 CD (kart sayıları testlerde sabitlenen değerler)
        $this->assertSame(4, substr_count($emRecent, '/Image/res-pink/cd-overlay.png'));   // En Son embedded 4/4
        $this->assertSame(3, substr_count($emList, '/Image/res-pink/cd-overlay.png'));     // Playlist embedded 3/3
        $this->assertSame(10, substr_count($wdRecent, '/Image/res-pink/cd-overlay.png'));  // En Son wide 10/10
        $this->assertSame(6, substr_count($wdList, '/Image/res-pink/cd-overlay.png'));     // Playlist wide 6/6

        // Kart başına 1 (yinelenen yok) — tek örnek + kılıf ile karışmaz
        $this->assertSame(4, substr_count($emRecent, 'home-song__mini-card__art-cd'));
        $this->assertSame(4, substr_count($emRecent, '/Image/res-pink/cd-case.png'));
    }
}
