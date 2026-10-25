<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Handler;

use CoreMusic\Auth\Handler\AutoRedirectHandler;
use CoreMusic\Auth\Service\SessionManager;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use PHPUnit\Framework\TestCase;

/**
 * PRD-AUT-003 regresyonu — AutoRedirectHandler oturum okumasını SessionManager
 * üzerinden yapar. Yalnız redirect'e ulaşmayan (exit'siz) yollar test edilir;
 * başarılı redirect header()/exit içerdiğinden birim testinde çalıştırılmaz.
 */
final class AutoRedirectHandlerTest extends TestCase
{
    private ?array $originalGet = null;

    protected function setUp(): void
    {
        $this->originalGet = $_GET;
        $_GET = [];
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_GET = $this->originalGet ?? [];
        $_SESSION = [];
    }

    private function handler(): AutoRedirectHandler
    {
        return new AutoRedirectHandler(
            new ConfigManager([]),
            new DomainConfig(null),
            new SessionManager(),
        );
    }

    public function testPostRequest_returnsFalse(): void
    {
        $_GET['redirect_uri'] = 'https://home.coremusic.net/auth/callback';

        $this->assertFalse($this->handler()->handle('login', 'POST'));
    }

    public function testNonAuthPage_returnsFalse(): void
    {
        $_GET['redirect_uri'] = 'https://home.coremusic.net/auth/callback';

        $this->assertFalse($this->handler()->handle('select-gender', 'GET'));
    }

    public function testMissingRedirectUri_returnsFalse(): void
    {
        $this->assertFalse($this->handler()->handle('login', 'GET'));
    }
}
