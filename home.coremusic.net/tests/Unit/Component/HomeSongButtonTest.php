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

    public function testHtml_withoutKind_rendersCdCaseAndMusicBadge(): void
    {
        // Arrange + Act — 'k' anahtarı yoksa default 'song'
        $html = HomeSongButton::html(self::track('T', 'S', ''), 'm', 'meta');

        // Assert — kılıf + rozet her kartta, şarkı rozeti music.png
        $this->assertStringContainsString('<div class="home-song__mini-card__art">', $html);
        $this->assertStringContainsString('class="home-song__mini-card__art-img" src="', $html);
        $this->assertStringContainsString('class="home-song__mini-card__art-case" src="', $html);
        $this->assertStringContainsString('/Image/res-pink/cd-case.png', $html);
        $this->assertStringContainsString('class="home-song__mini-card__art-badge" src="', $html);
        $this->assertStringContainsString('/Image/res-pink/music.png', $html);
        $this->assertStringNotContainsString('/Image/res-pink/video.png', $html);
    }

    public function testHtml_videoKind_rendersVideoBadgeWithoutMusicBadge(): void
    {
        // Arrange + Act
        $html = HomeSongButton::html(
            ['t' => 'Kış Masalı Ensturmental', 's' => 'Org Dersleri', 'art' => '', 'k' => 'video'],
            'm',
            'meta'
        );

        // Assert
        $this->assertStringContainsString('/Image/res-pink/video.png', $html);
        $this->assertStringNotContainsString('/Image/res-pink/music.png', $html);
        $this->assertStringContainsString('/Image/res-pink/cd-case.png', $html);
    }

    public function testHtml_unknownKind_fallsBackToSongBadge(): void
    {
        // Arrange + Act — 'song'/'video' dışı değer → song rozeti
        $html = HomeSongButton::html(
            ['t' => 'T', 's' => 'S', 'art' => '', 'k' => 'radio'],
            'm',
            'meta'
        );

        // Assert
        $this->assertStringContainsString('/Image/res-pink/music.png', $html);
        $this->assertStringNotContainsString('/Image/res-pink/video.png', $html);
    }

    public function testHtml_artOverlayImagesAreDecorativeAndOrdered(): void
    {
        // Arrange + Act
        $html = HomeSongButton::html(self::track('T', 'S', ''), 'm', 'meta');

        // Assert — WCAG: kılıf/rozet dekoratif (alt="" + aria-hidden)
        $this->assertMatchesRegularExpression(
            '#class="home-song__mini-card__art-case" src="[^"]+/Image/res-pink/cd-case\.png" alt="" aria-hidden="true"/>#',
            $html
        );
        $this->assertMatchesRegularExpression(
            '#class="home-song__mini-card__art-badge" src="[^"]+/Image/res-pink/music\.png" alt="" aria-hidden="true"/>#',
            $html
        );

        // Sıra: kapak → kılıf → rozet
        $cover = strpos($html, 'home-song__mini-card__art-img');
        $case  = strpos($html, 'home-song__mini-card__art-case');
        $badge = strpos($html, 'home-song__mini-card__art-badge');
        $this->assertIsInt($cover);
        $this->assertIsInt($case);
        $this->assertIsInt($badge);
        $this->assertLessThan($case, $cover);
        $this->assertLessThan($badge, $case);

        // Ana kapak attributeleri korunur
        $this->assertStringContainsString('width="50" height="50" loading="lazy"/>', $html);
    }

    public function testHtml_withoutCdFlag_hasNoCdOverlay(): void
    {
        // Arrange + Act — 'cd' yok (default false), ister song ister video rozeti olsun
        $song  = HomeSongButton::html(self::track('T', 'S', ''), 'm', 'meta');
        $video = HomeSongButton::html(
            ['t' => 'T', 's' => 'S', 'art' => '', 'k' => 'video'],
            'm',
            'meta'
        );

        // Assert — CD katmanı hiç basılmaz
        foreach ([$song, $video] as $html) {
            $this->assertStringNotContainsString('home-song__mini-card__art-cd', $html);
            $this->assertStringNotContainsString('/Image/res-pink/cd-ico.png', $html);
            $this->assertStringContainsString('home-song__mini-card__art-badge', $html);
        }

        // Explicit false da aynı sonucu verir
        $explicit = HomeSongButton::html(
            ['t' => 'T', 's' => 'S', 'art' => '', 'k' => 'video', 'cd' => false],
            'm',
            'meta'
        );
        $this->assertStringNotContainsString('/Image/res-pink/cd-ico.png', $explicit);
    }

    public function testHtml_cdFlag_rendersCdOverlayAfterBadge(): void
    {
        // Arrange + Act — Figma "Show CD" düğümü (Plist Video Button)
        $html = HomeSongButton::html(
            ['t' => "En Sevilen Supermix'im", 's' => 'Sistem', 'art' => '', 'k' => 'video', 'cd' => true],
            'm',
            'meta'
        );

        // Assert — CD katmanı: dekoratif + sabit asset
        $this->assertMatchesRegularExpression(
            '#class="home-song__mini-card__art-cd" src="[^"]+/Image/res-pink/cd-ico\.png" alt="" aria-hidden="true"/>#',
            $html
        );
        $this->assertStringContainsString('/Image/res-pink/video.png', $html);
        $this->assertStringContainsString('/Image/res-pink/cd-case.png', $html);

        // Sıra: kapak → kılıf → rozet → CD (z-sırasında CD en üstte = DOM'da en son)
        $cover = strpos($html, 'home-song__mini-card__art-img');
        $case  = strpos($html, 'home-song__mini-card__art-case');
        $badge = strpos($html, 'home-song__mini-card__art-badge');
        $cd    = strpos($html, 'home-song__mini-card__art-cd');
        $this->assertIsInt($cover);
        $this->assertIsInt($case);
        $this->assertIsInt($badge);
        $this->assertIsInt($cd);
        $this->assertLessThan($case, $cover);
        $this->assertLessThan($badge, $case);
        $this->assertLessThan($cd, $badge);

        // Tek CD img (yinelenen yok)
        $this->assertSame(1, substr_count($html, '/Image/res-pink/cd-ico.png'));
    }
}
