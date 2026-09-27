<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Component;

use CoreMusic\Home\Component\HomeSongButton;
use PHPUnit\Framework\TestCase;

final class HomeSongButtonTest extends TestCase
{
    /** @return array{t: string, s?: string, art: string} */
    private static function track(string $title = 'Göksel - Sevil Neşelen', string $subtitle = 'Göksel', string $art = ''): array
    {
        return ['t' => $title, 's' => $subtitle, 'art' => $art];
    }

    public function testHtml_rendersTitleSubtitleAndMetaLabel(): void
    {
        // Arrange
        $item = self::track('Şarkı Başlığı', 'Sanatçı');

        // Act
        $html = HomeSongButton::html($item, 'mini-card__subtitle', '00:05:00');

        // Assert
        $this->assertStringContainsString('class="home-song__mini-card"', $html);
        $this->assertStringContainsString('<h3 class="home-song__mini-card__title">Şarkı Başlığı</h3>', $html);
        $this->assertStringContainsString('<p class="home-song__mini-card__subtitle">Sanatçı</p>', $html);
        $this->assertStringContainsString('<span class="mini-card__subtitle">00:05:00</span>', $html);
        $this->assertStringContainsString('href="/playlist"', $html);
        $this->assertStringContainsString('data-no-spa', $html);
    }

    public function testHtml_escapesXssInTitleSubtitleAndMeta(): void
    {
        // Arrange
        $item = self::track(
            '<script>alert("xss")</script>',
            '<img src=x onerror=alert(1)>',
            ''
        );

        // Act
        $html = HomeSongButton::html($item, 'mini-card__subtitle', '" onmouseover="evil()');

        // Assert — ham HTML/güvenlik bypass karakteri çıkışta kalmamalı
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringNotContainsString('" onmouseover="evil()"', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $html);
        $this->assertStringContainsString('<span class="mini-card__subtitle">&quot; onmouseover=&quot;evil()</span>', $html);
    }

    public function testHtml_emptyArt_usesDefaultAlbumFallback(): void
    {
        // Arrange + Act
        $withEmptyArt = HomeSongButton::html(self::track('T', 'S', ''), 'm', 'meta');

        // Assert — host ASSETS_URL'e bağlı olabilir; path sabittir.
        $this->assertStringContainsString('/Image/res-pink/default-album.png', $withEmptyArt);
    }

    public function testHtml_missingArtKey_usesDefaultAlbumFallback(): void
    {
        // Arrange + Act
        $html = HomeSongButton::html(['t' => 'T', 's' => 'S'], 'm', 'meta');

        // Assert
        $this->assertStringContainsString('/Image/res-pink/default-album.png', $html);
        $this->assertStringContainsString('<h3 class="home-song__mini-card__title">T</h3>', $html);
    }

    public function testHtml_providedArtUrl_isRenderedEscaped(): void
    {
        // Arrange
        $art = 'https://assets.coremusic.net/Image/res-pink/album-goksel.png?a=1&b=2';

        // Act
        $html = HomeSongButton::html(self::track('T', 'S', $art), 'm', 'meta');

        // Assert
        $this->assertStringContainsString(
            'src="https://assets.coremusic.net/Image/res-pink/album-goksel.png?a=1&amp;b=2"',
            $html
        );
        $this->assertStringNotContainsString('/Image/res-pink/default-album.png', $html);
    }
}
