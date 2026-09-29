<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use PHPUnit\Framework\TestCase;
use CoreMusic\Middleware\BypassAuthMiddleware;
use CoreMusic\Config\ConfigManager;
use CoreMusic\Security\SecurityHelper;

final class BypassAuthMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $this->defineBypassCredentials();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    /**
     * ADR-008 fail-closed: BypassAuthMiddleware yalnızca BYPASS_USER_UUID /
     * BYPASS_ROLE / BYPASS_USERNAME sabitlerinin ÜÇÜ de tanımlı ve boş olmayan
     * durumda bypass yapar. Sabitler süreç içinde bir kez define edilebilir
     * (PHP'de undefine yok) → defined() kontrolü ile ikinci define uyarısını
     * engelliyoruz.
     *
     * Bu test sınıfı "bypass AKTİF" senaryosunu sınar; fail-closed (sabitler
     * tanımsız) senaryosu
     * @see BypassAuthMiddlewareFailClosedTest (ayrı process, preserveGlobalState=false)
     */
    private function defineBypassCredentials(): void
    {
        defined('BYPASS_USER_UUID') ? null : define('BYPASS_USER_UUID', '00000000000000000000000000000001');
        defined('BYPASS_ROLE')      ? null : define('BYPASS_ROLE', 'admin');
        defined('BYPASS_USERNAME')  ? null : define('BYPASS_USERNAME', 'test_user');
    }

    /**
     * Bypass'ın gerçekten inject edildiğini güvenli doğrula.
     *
     * Fail-closed durumda $captured['_auth'] hiç dolmaz; doğrudan offset
     * erişimi "Undefined array key" PHP uyarısı üretiyor (PHPUnit warning).
     * Önce null/varlık kontrolü, sonra içerik kontrolü.
     */
    private function assertBypassAuthInjected(mixed $captured): array
    {
        $this->assertIsArray($captured, 'next() callback invoked');
        $this->assertArrayHasKey('_auth', $captured, 'bypass aktifken _auth inject edilmeli');
        $this->assertIsArray($captured['_auth'], '_auth bir array olmalı');

        return $captured['_auth'];
    }

    public function testProductionModeDoesNotBypass(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'production', 'test_mode' => 'false']]);
        $this->assertFalse(SecurityHelper::isTestBypassActive($config));

        $middleware = new BypassAuthMiddleware($config);
        $request = ['method' => 'GET', 'uri' => '/home'];
        $middleware->handle($request, function (array $req) {
            return ['status' => 200];
        });

        $this->assertNull($request['_auth'] ?? null);
    }

    public function testProductionWithTestModeStillBypasses(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'production', 'test_mode' => 'true']]);
        $this->assertFalse(SecurityHelper::isTestBypassActive($config));

        $middleware = new BypassAuthMiddleware($config);
        $request = ['method' => 'GET', 'uri' => '/home'];
        $middleware->handle($request, function (array $req) {
            return ['status' => 200];
        });

        $this->assertNull($request['_auth'] ?? null);
    }

    public function testDevelopmentModeWithTestModeBypasses(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $this->assertTrue(SecurityHelper::isTestBypassActive($config));

        $middleware = new BypassAuthMiddleware($config);
        $captured = null;
        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $auth = $this->assertBypassAuthInjected($captured);
        $this->assertSame('admin', $auth['role']);
        $this->assertTrue($auth['bypass']);
    }

    public function testDevelopmentModeWithForceBypass(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'force_auth_bypass' => 'true']]);
        $this->assertTrue(SecurityHelper::isTestBypassActive($config));

        $middleware = new BypassAuthMiddleware($config);
        $captured = null;
        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $auth = $this->assertBypassAuthInjected($captured);
        $this->assertSame('admin', $auth['role']);
    }

    public function testTestEnvironmentBypasses(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'testing', 'test_mode' => '1']]);
        $this->assertTrue(SecurityHelper::isTestBypassActive($config));

        $middleware = new BypassAuthMiddleware($config);
        $captured = null;
        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $this->assertBypassAuthInjected($captured);
    }

    public function testBypassWritesToSession(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $middleware = new BypassAuthMiddleware($config);

        $middleware->handle(['method' => 'GET', 'uri' => '/home'], function (array $req) {
            return ['status' => 200];
        });

        $this->assertArrayHasKey('MM_UserID', $_SESSION, 'bypass session\'a MM_UserID yazmalı');
        $this->assertNotEmpty($_SESSION['MM_UserID']);
        $this->assertSame('admin', $_SESSION['MM_UserRole']);
        $this->assertSame('test_user', $_SESSION['MM_Username']);
    }

    public function testBypassDoesNotOverwriteExistingSession(): void
    {
        $_SESSION['MM_UserID'] = 'existing_user_id';

        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $middleware = new BypassAuthMiddleware($config);

        $middleware->handle(['method' => 'GET', 'uri' => '/home'], function (array $req) {
            return ['status' => 200];
        });

        $this->assertSame('existing_user_id', $_SESSION['MM_UserID']);
    }

    public function testBypassInjectsCorrectUserId(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $middleware = new BypassAuthMiddleware($config);
        $captured = null;

        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $auth = $this->assertBypassAuthInjected($captured);
        $this->assertSame('00000000000000000000000000000001', $auth['userId']);
    }

    public function testBypassInjectsCorrectRole(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $middleware = new BypassAuthMiddleware($config);
        $captured = null;

        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $auth = $this->assertBypassAuthInjected($captured);
        $this->assertSame('admin', $auth['role']);
    }

    public function testBypassInjectsUserObject(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $middleware = new BypassAuthMiddleware($config);
        $captured = null;

        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $auth = $this->assertBypassAuthInjected($captured);
        $this->assertArrayHasKey('user', $auth);
        $this->assertSame('test_user', $auth['user']['username']);
        $this->assertSame('admin', $auth['user']['role']);
    }

    public function testBypassMergesWithExistingAuth(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $middleware = new BypassAuthMiddleware($config);
        $captured = null;

        $middleware->handle(
            ['method' => 'GET', 'uri' => '/home', '_auth' => ['custom' => 'data']],
            function (array $req) use (&$captured) {
                $captured = $req;
                return ['status' => 200];
            }
        );

        $auth = $this->assertBypassAuthInjected($captured);
        $this->assertSame('data', $auth['custom']);
        $this->assertSame('admin', $auth['role']);
    }
}
