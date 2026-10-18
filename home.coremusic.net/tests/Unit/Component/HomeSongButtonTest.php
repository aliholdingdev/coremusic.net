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

    public function testHtml_dataKind_videoSinyaliYalnizKVideoDa(): void
    {
        // Arrange — geçerli stream kapısı açıkken tip sinyali k alanından üretilir
        $stream = '/stream/' . str_repeat('a', 32);
        $video  = HomeSongButton::html(['t' => 'V', 's' => 'S', 'k' => 'video', 'stream' => $stream], 'm', 'meta');
        $song   = HomeSongButton::html(['t' => 'T', 's' => 'S', 'k' => 'song', 'stream' => $stream], 'm', 'meta');
        $weird  = HomeSongButton::html(['t' => 'T', 's' => 'S', 'k' => 'banana', 'stream' => $stream], 'm', 'meta');
        $none   = HomeSongButton::html(['t' => 'T', 's' => 'S', 'stream' => $stream], 'm', 'meta');

        // Assert
        $this->assertStringContainsString('data-kind="video"', $video);
        $this->assertStringContainsString('data-kind="audio"', $song);
        $this->assertStringContainsString('data-kind="audio"', $weird);
        $this->assertStringContainsString('data-kind="audio"', $none);
    }

    public function testHtml_dataKind_streamKapisiKapaliykenAttrYok(): void
    {
        // Arrange + Act — stream regex'i geçmiyorsa data bloğu hiç üretilmez (mevcut kapı)
        $html = HomeSongButton::html(['t' => 'T', 's' => 'S', 'k' => 'video', 'stream' => 'javascript:x'], 'm', 'meta');

        // Assert
        $this->assertStringNotContainsString('data-kind', $html);
        $this->assertStringNotContainsString('data-stream', $html);
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

        // Ana kapak attributeleri korunur
        $this->assertStringContainsString('width="50" height="50" loading="lazy"/>', $html);
    }

    public function testHtml_everyCardRendersExactlyOneCdOverlay(): void
    {
        // Arrange + Act — CD katmanı tüm kartlarda (song rozetli, video rozetli, fazladan anahtarlı)
        $cases = [
            'song rozeti'      => self::track('T', 'S', ''),
            'video rozeti'     => ['t' => "En Sevilen Supermix'im", 's' => 'Sistem', 'art' => '', 'k' => 'video'],
            'bilinmeyen tip'   => ['t' => 'T', 's' => 'S', 'art' => '', 'k' => 'radio'],
            'fazladan anahtar' => ['t' => 'T', 's' => 'S', 'art' => '', 'k' => 'song', 'extra' => 'x'],
        ];

        foreach ($cases as $label => $item) {
            $html = HomeSongButton::html($item, 'm', 'meta');

            // Assert — tam 1 CD katmanı, dekoratif, sabit asset
            $this->assertSame(1, substr_count($html, 'home-song__mini-card__art-cd'), $label);
            $this->assertSame(1, substr_count($html, '/Image/res-pink/cd-overlay.png'), $label);
            $this->assertMatchesRegularExpression(
                '#class="home-song__mini-card__art-cd" src="[^"]+/Image/res-pink/cd-overlay\.png" alt="" aria-hidden="true"/>#',
                $html,
                $label
            );
            // Kılıf (cd-case.png) ile karışmaz
            $this->assertSame(1, substr_count($html, '/Image/res-pink/cd-case.png'), $label);
        }
    }
}
