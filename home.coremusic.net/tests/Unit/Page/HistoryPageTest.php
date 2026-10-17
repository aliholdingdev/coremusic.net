<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Page;

use PHPUnit\Framework\TestCase;

final class HistoryPageTest extends TestCase
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

    private function renderHistoryPage(int $viewportW): string
    {
        \CoreMusic\Device\DeviceManager::resetInstance();
        $_GET['_test_resp'] = (string)$viewportW;
        $homeRoot = dirname(__DIR__, 3);
        ob_start();
        try {
            $render = static function () use ($homeRoot) {
                include $homeRoot . '/pages/history.php';
            };
            $render();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }

    public function testHistoryPage_rendersEmbeddedLayoutAt1024(): void
    {
        $html = $this->renderHistoryPage(1024);

        $this->assertStringContainsString('page-history', $html);
        $this->assertStringNotContainsString('page-history--wide', $html);
        $this->assertStringContainsString('history-list__head', $html);
        $this->assertStringNotContainsString('history-cols', $html);
        $this->assertStringNotContainsString('page-sidebar--figma-wide', $html);
    }

    public function testHistoryPage_rendersWideLayoutAt1920(): void
    {
        $html = $this->renderHistoryPage(1920);

        $this->assertStringContainsString('page-history--wide', $html);
        $this->assertStringContainsString('history-cols', $html);
        $this->assertStringContainsString('history-col', $html);
        $this->assertStringContainsString('history-toolbar', $html);
        $this->assertStringContainsString('history-toolbar__title', $html);
        $this->assertStringContainsString('page-sidebar--figma-wide', $html);
        $this->assertStringContainsString('page-sidebar__status-card', $html);
    }
}
