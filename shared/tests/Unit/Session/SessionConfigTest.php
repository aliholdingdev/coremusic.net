<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Session;

use PHPUnit\Framework\TestCase;
use CoreMusic\Session\SessionConfig;

final class SessionConfigTest extends TestCase
{
    public function testTimeoutConstantsMatchAdr011(): void
    {
        $this->assertSame(3600, SessionConfig::IDLE_TIMEOUT);
        $this->assertSame(1800, SessionConfig::MAX_LIFETIME);
        $this->assertSame(1800, SessionConfig::ROTATION_INTERVAL);
    }

    public function testSessionDirectoryPermissionsAre0750(): void
    {
        $this->assertSame(0750, SessionConfig::DIR_PERMISSIONS);
    }
    public function testCookieParamsAreHttpOnlyAndSameSiteLax(): void
    {
        $config = SessionConfig::fromEnvironment();
        $params = $config->cookieParams();

        $this->assertTrue($params['httponly']);
        $this->assertSame('Lax', $params['samesite']);
        $this->assertSame('/', $params['path']);
    }
}
