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
}
