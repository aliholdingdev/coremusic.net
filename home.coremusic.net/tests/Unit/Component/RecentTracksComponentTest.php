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

    public function testConstructor_defaultData_embeddedVariantLimitsToThreeCards(): void
    {
        // Arrange + Act — defaultTracks() 9 kart üretir
        $component = new RecentTracksComponent(HomeLayoutVariant::Embedded);

        // Assert
        $this->assertCount(3, $component->cards);
    }

    public function testConstructor_defaultData_wideAndFourKKeepAllNineCards(): void
    {
        // Arrange + Act
        $wide  = new RecentTracksComponent(HomeLayoutVariant::Wide);
        $fourK = new RecentTracksComponent(HomeLayoutVariant::FourK);

        // Assert
        $this->assertCount(9, $wide->cards);
        $this->assertCount(9, $fourK->cards);
    }

    public function testConstructor_overrideData_truncatesToThreeOnEmbedded(): void
    {
        // Arrange + Act
        $component = new RecentTracksComponent(HomeLayoutVariant::Embedded, self::tracks(5));

        // Assert — ilk 3 kart korunur
        $this->assertCount(3, $component->cards);
        $this->assertStringContainsString('Track 1', $component->cards[0]);
        $this->assertStringContainsString('Track 3', $component->cards[2]);
        $this->assertStringNotContainsString('Track 4', implode('', $component->cards));
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
