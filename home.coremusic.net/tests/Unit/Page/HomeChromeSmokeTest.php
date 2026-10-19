<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Page;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\PageRouter\HtmlShellRenderer;
use PHPUnit\Framework\TestCase;

/**
 * C-F-13 entegrasyon smoke'u — chrome (header/footer) shell'e taşındı.
 *
 * 1) pages/home.php artık <header>/<footer>/<main> ÜRETMEMELİ (chrome shell'de,
 *    sayfa gövdesi yalnız içerik).
 * 2) Shell + gerçek HEADER_PATH/FOOTER_PATH ile birleştirince: tek <main>,
 *    header main öncesi, footer main sonrası.
 */
final class HomeChromeSmokeTest extends TestCase
{
    private static bool $bootstrapped = false;

    protected function setUp(): void
    {
        $_SESSION = $_SESSION ?? [];
        $_GET     = [];

        if (!self::$bootstrapped) {
            // Test bootstrap yalnız autoload — sayfa sabitlerini burada tanımla.
            $homeRoot = dirname(__DIR__, 3);
            defined('ROOT_PATH')  || define('ROOT_PATH', $homeRoot);
            defined('HEADER_PATH') || define('HEADER_PATH', $homeRoot . '/header.php');
            defined('FOOTER_PATH') || define('FOOTER_PATH', $homeRoot . '/footer.php');
            self::$bootstrapped = true;
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    private function renderHomePage(): string
    {
        $homeRoot = dirname(__DIR__, 3);
        ob_start();
        try {
            include $homeRoot . '/pages/home.php';
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }

    public function testHomePage_emitsNoChromeAndNoMain(): void
    {
        $html = $this->renderHomePage();

        $this->assertStringContainsString('page-home', $html, 'Sayfa içeriği render edilmeli');
        $this->assertStringNotContainsString('<main', $html, '<main landmark artık shell\'de (WCAG tek main)');
        $this->assertStringNotContainsString('<header', $html, 'Header chrome sayfa gövdesinden çıkarıldı');
        $this->assertStringNotContainsString('<footer', $html, 'Footer chrome sayfa gövdesinden çıkarıldı');
    }

    public function testShellComposition_singleMainWithChromeAround(): void
    {
        $container = $this->renderHomePage();

        $config       = new ConfigManager(['app' => ['name' => 'CoreMusicTest', 'debug' => false, 'version' => '1.0.0']]);
        $domainConfig = new DomainConfig();
        $domainConfig->setOverrides('http', 'home.coremusic.net', 81);

        $renderer = new HtmlShellRenderer($config, $domainConfig, HEADER_PATH, FOOTER_PATH);
        $html     = $renderer->render($container, 'home', [], 'tok', [], []);

        $this->assertSame(1, substr_count($html, '<main'), 'Belgede tek <main>');
        $this->assertStringNotContainsString('<main class="page-home', $html, 'İç içe page-home main YOK');

        $mainPos  = strpos($html, 'id="main-content"');
        $headPos  = strpos($html, '<header');
        $footPos  = strpos($html, '<footer');
        $this->assertNotFalse($headPos, 'Gerçek header shell\'de render edilmeli');
        $this->assertNotFalse($footPos, 'Gerçek footer shell\'de render edilmeli');
        $this->assertNotFalse($mainPos, 'main-content bulunmalı');
        $this->assertTrue($headPos < $mainPos, 'Header main öncesi');
        $this->assertTrue($footPos > $mainPos, 'Footer main sonrası');
    }
}
