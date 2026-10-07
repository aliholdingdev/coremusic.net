<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\PageRouter\HtmlShellRenderer;
use PHPUnit\Framework\TestCase;

/**
 * C-F-13 regresyonu — header/footer chrome container DIŞINDA (shell seviyesi).
 *
 * Eski yapı: chrome pages/home.php içinde container'ın içindeydi → SPA patch
 * (replaceChildren + script-strip) footer klasik script'lerini öldürürdü
 * (player kontrolleri tam reload'a kadar ölü) + iç içe <main> (WCAG çift landmark).
 * Yeni yapı: header <main id="main-content"> ÖNCESİ, footer SONRASI.
 */
final class HtmlShellRendererChromeTest extends TestCase
{
    private function renderer(string $headerPath, string $footerPath): HtmlShellRenderer
    {
        $config       = new ConfigManager(['app' => ['name' => 'CoreMusicTest', 'debug' => false, 'version' => '1.0.0']]);
        $domainConfig = new DomainConfig();
        $domainConfig->setOverrides('http', 'home.coremusic.net', 81);

        return new HtmlShellRenderer($config, $domainConfig, $headerPath, $footerPath);
    }

    private function render(HtmlShellRenderer $renderer): string
    {
        return $renderer->render('<div id="page-body">icerik</div>', 'home', [], 'tok', [], []);
    }

    public function testChrome_orderHeaderMainFooter(): void
    {
        $fixtures = dirname(__DIR__, 2) . '/Fixtures/chrome';
        $html = $this->render($this->renderer($fixtures . '/header.php', $fixtures . '/footer.php'));

        $headerPos = strpos($html, 'id="chrome-fixture-header"');
        $mainOpen  = strpos($html, 'id="main-content"');
        $mainClose = strpos($html, '</main>');
        $footerPos = strpos($html, 'id="chrome-fixture-footer"');

        $this->assertNotFalse($headerPos, 'Header chrome render edilmeli');
        $this->assertNotFalse($mainOpen, 'main-content bulunmalı');
        $this->assertNotFalse($mainClose, '</main> bulunmalı');
        $this->assertNotFalse($footerPos, 'Footer chrome render edilmeli');

        $this->assertTrue($headerPos < $mainOpen, 'Header main ÖNCESİNDE olmalı');
        $this->assertTrue($mainClose < $footerPos, 'Footer main SONRASINDA olmalı');
    }

    public function testChrome_containerDoesNotEmbedChrome(): void
    {
        // Chrome, container'ın İÇİNE (main içine) girmemeli — SPA patch onu silerdi.
        $fixtures = dirname(__DIR__, 2) . '/Fixtures/chrome';
        $html = $this->render($this->renderer($fixtures . '/header.php', $fixtures . '/footer.php'));

        $mainOpen  = strpos($html, 'id="main-content"');
        $mainClose = strpos($html, '</main>');
        $insideMain = substr($html, (int) $mainOpen, (int) ($mainClose - $mainOpen));

        $this->assertStringNotContainsString('chrome-fixture-header', $insideMain);
        $this->assertStringNotContainsString('chrome-fixture-footer', $insideMain);
    }

    public function testChrome_skippedWhenPathsEmptyOrMissing(): void
    {
        // Auth host: HEADER_PATH tanımlı değil → '' → chrome yok, hata yok.
        $html = $this->render($this->renderer('', ''));
        $this->assertStringNotContainsString('chrome-fixture', $html);

        // Var olmayan yol da sessizce '' vermeli (uyarı/fatal yok).
        $html2 = $this->render($this->renderer('/nonexistent/header.php', '/nonexistent/footer.php'));
        $this->assertStringNotContainsString('chrome-fixture', $html2);
    }

    public function testMainContentHasNoNestedMainFromContainer(): void
    {
        // C-F-13 ikinci yarısı: container içinde ikinci <main> olmamalı (WCAG).
        $html = $this->render($this->renderer('', ''));

        $mainCount = substr_count($html, '<main');
        $this->assertSame(1, $mainCount, 'Belgede tek <main> landmark olmalı');
    }
}
