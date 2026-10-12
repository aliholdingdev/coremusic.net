<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\CoversClass;
use CoreMusic\Middleware\BypassAuthMiddleware;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Security\SecurityHelper;

/**
 * BypassAuthMiddleware — FAIL-CLOSED testleri (ADR-008)
 *
 * Üretim kodu fail-closed: BYPASS_USER_UUID / BYPASS_ROLE / BYPASS_USERNAME
 * sabitlerinden EN AZ BİRİ tanımsız veya boşsa loadBypassConfig() `[]` döner ve
 * handle() bypass YAPMAZ (request['_auth'] hiç dolmaz, session'a yazılmaz).
 *
 * Neden ayrı process?
 * PHP'de define edilen sabit undefine edilemez. Aynı süreçte çalışan
 * BypassAuthMiddlewareTest setUp() içinde sabitleri tanımladığı için fail-closed
 * senaryosu ancak izole bir süreçte deterministik olarak doğrulanabilir.
 * preserveGlobalState(false) → üst süreçteki sabitler alt sürece taşınmaz.
 */
#[CoversClass(BypassAuthMiddleware::class)]
final class BypassAuthMiddlewareFailClosedTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testFailClosedWhenNoConstantsDefinedDoesNotInjectAuth(): void
    {
        // Precondition: bu süreçte bypass credential'ları tanımsız olmalı
        $this->assertFalse(defined('BYPASS_USER_UUID'), 'BYPASS_USER_UUID tanımsız olmalı');
        $this->assertFalse(defined('BYPASS_ROLE'), 'BYPASS_ROLE tanımsız olmalı');
        $this->assertFalse(defined('BYPASS_USERNAME'), 'BYPASS_USERNAME tanımsız olmalı');

        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $this->assertTrue(SecurityHelper::isTestBypassActive($config), 'gate açık olmalı — yoksa test anlamsız');

        $middleware = new BypassAuthMiddleware($config);
        $captured = null;

        $result = $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        // Fail-closed: credential yokken bypass yok
        $this->assertIsArray($captured, 'next() çağrılıyor');
        $this->assertArrayNotHasKey('_auth', $captured, 'fail-closed: credential yokken _auth doldurulmamalı');
        $this->assertArrayNotHasKey('MM_UserID', $_SESSION, 'fail-closed: session\'a bypass kimliği yazılmamalı');
        $this->assertArrayNotHasKey('MM_UserRole', $_SESSION);
        $this->assertArrayNotHasKey('MM_Username', $_SESSION);
        $this->assertSame(200, $result['status'], 'istek akışa normal devam etmeli');
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testFailClosedWhenSingleConstantMissingDoesNotInjectAuth(): void
    {
        // Kısmi credential: yalnız UUID tanımlı → rol ve username eksik → bypass yok
        $this->assertFalse(defined('BYPASS_USER_UUID'), 'BYPASS_USER_UUID tanımsız olmalı');
        define('BYPASS_USER_UUID', '00000000000000000000000000000001');

        $this->assertFalse(defined('BYPASS_ROLE'), 'BYPASS_ROLE tanımsız kalmalı');
        $this->assertFalse(defined('BYPASS_USERNAME'), 'BYPASS_USERNAME tanımsız kalmalı');

        $config = new ConfigManager(['app' => ['env' => 'testing', 'force_auth_bypass' => 'true']]);
        $this->assertTrue(SecurityHelper::isTestBypassActive($config), 'gate açık olmalı');

        $middleware = new BypassAuthMiddleware($config);
        $captured = null;

        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $this->assertIsArray($captured, 'next() çağrılıyor');
        $this->assertArrayNotHasKey('_auth', $captured, 'fail-closed: eksik credential ile bypass yapılmamalı');
        $this->assertArrayNotHasKey('MM_UserID', $_SESSION, 'fail-closed: session boş kalmalı');
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testProductionGateStaysClosedEvenWithConstantsDefined(): void
    {
        // Üretim + sabitler tanımlı → gate kapalı → bypass yok (çift katman)
        defined('BYPASS_USER_UUID') ? null : define('BYPASS_USER_UUID', '00000000000000000000000000000001');
        defined('BYPASS_ROLE')      ? null : define('BYPASS_ROLE', 'admin');
        defined('BYPASS_USERNAME')  ? null : define('BYPASS_USERNAME', 'test_user');

        $config = new ConfigManager(['app' => ['env' => 'production', 'test_mode' => 'true']]);
        $this->assertFalse(SecurityHelper::isTestBypassActive($config), 'productionda gate kapalı kalmalı');

        $middleware = new BypassAuthMiddleware($config);
        $captured = null;

        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $this->assertIsArray($captured, 'next() çağrılıyor');
        $this->assertArrayNotHasKey('_auth', $captured, 'productionda bypass yapılmamalı');
        $this->assertArrayNotHasKey('MM_UserID', $_SESSION, 'productionda session\'a bypass kimliği yazılmamalı');
    }
}
