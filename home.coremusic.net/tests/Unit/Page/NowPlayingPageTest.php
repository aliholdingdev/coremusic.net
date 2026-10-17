<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Page;

use PHPUnit\Framework\TestCase;

final class NowPlayingPageTest extends TestCase
{
    private static bool $bootstrapped = false;

    protected function setUp(): void
    {
        $_SESSION = $_SESSION ?? [];
        $_GET     = [];
        \CoreMusic\Device\DeviceManager::resetInstance();

        if (!self::$bootstrapped) {
            $homeRoot = dirname(__DIR__, 3);
            defined('ROOT_PATH')   || define('ROOT_PATH', $homeRoot);
            defined('HEADER_PATH') || define('HEADER_PATH', $homeRoot . '/header.php');
            defined('FOOTER_PATH') || define('FOOTER_PATH', $homeRoot . '/footer.php');
            self::$bootstrapped = true;
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_GET     = [];
        \CoreMusic\Device\DeviceManager::resetInstance();
    }

    private function renderNowPlayingPage(int $viewportW): string
    {
        \CoreMusic\Device\DeviceManager::resetInstance();
        $_GET['_test_resp'] = (string)$viewportW;
        $homeRoot = dirname(__DIR__, 3);
        ob_start();
        try {
            $render = static function () use ($homeRoot) {
                include $homeRoot . '/pages/nowplaying.php';
            };
            $render();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }

    public function testNowPlayingPage_rendersEmbeddedLayoutAt1024(): void
    {
        $html = $this->renderNowPlayingPage(1024);

        $this->assertStringContainsString('page-player--embedded', $html);
        $this->assertStringNotContainsString('page-player--wide', $html);
        $this->assertStringContainsString('player-table', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
        $this->assertStringNotContainsString('player-visual__art', $html);
        $this->assertStringNotContainsString('player-mini-card', $html);
    }

    public function testNowPlayingPage_rendersWideLayoutAt1920(): void
    {
        $html = $this->renderNowPlayingPage(1920);

        $this->assertStringContainsString('page-player--wide', $html);
        $this->assertStringContainsString('Şimdi Oynatılıyor', $html);
        $this->assertStringContainsString('player-table', $html);
        $this->assertStringContainsString('player-col--stars', $html);
        $this->assertStringContainsString('player-row--active', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
        $this->assertStringContainsString('page-sidebar__info-card', $html);
        $this->assertStringContainsString('page-sidebar__status-card', $html);
        $this->assertStringContainsString('Önerilen Sanatçılar', $html);
        $this->assertStringContainsString('Son Dinlenenler', $html);
    }

    public function testNowPlayingPage_rendersFullscreenLayoutWhenModeRequested(): void
    {
        $_GET['mode'] = 'fullscreen';
        $html = $this->renderNowPlayingPage(1920);

        $this->assertStringContainsString('page-player--fullscreen', $html);
        $this->assertStringContainsString('player-mini-card', $html);
        $this->assertStringContainsString('player-playlist--wide', $html);
        $this->assertStringContainsString('player-bottom-progress', $html);
    }
}

