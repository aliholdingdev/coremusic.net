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
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
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

        $this->assertNotNull($captured['_auth'] ?? null);
        $this->assertSame('admin', $captured['_auth']['role']);
        $this->assertTrue($captured['_auth']['bypass']);
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

        $this->assertNotNull($captured['_auth'] ?? null);
        $this->assertSame('admin', $captured['_auth']['role']);
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

        $this->assertNotNull($captured['_auth'] ?? null);
    }

    public function testBypassWritesToSession(): void
    {
        $config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
        $middleware = new BypassAuthMiddleware($config);

        $middleware->handle(['method' => 'GET', 'uri' => '/home'], function (array $req) {
            return ['status' => 200];
        });

        $this->assertNotEmpty($_SESSION['MM_UserID'] ?? '');
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

        $this->assertSame('00000000000000000000000000000001', $captured['_auth']['userId']);
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

        $this->assertSame('admin', $captured['_auth']['role']);
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

        $this->assertArrayHasKey('user', $captured['_auth']);
        $this->assertSame('test_user', $captured['_auth']['user']['username']);
        $this->assertSame('admin', $captured['_auth']['user']['role']);
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

        $this->assertSame('data', $captured['_auth']['custom']);
        $this->assertSame('admin', $captured['_auth']['role']);
    }
}
