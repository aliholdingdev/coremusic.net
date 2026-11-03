<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\Admin\Auth\AdminAuthBridge;
use CoreMusic\Admin\Routing\DevLoginGate;
use PHPUnit\Framework\TestCase;

/**
 * /dev-login P0 üretim gate'i (DevLoginGate).
 *
 * Kanıt hattı:
 *   production (DEBUG yanlışlıkla AÇIK olsa bile) → 404 + dev-login aksiyonu HİÇ çalışmaz
 *     → $_SESSION'da admin anahtarı (MM_UserID) YOKTUR,
 *   development + DEBUG → mevcut davranış aynen korunur (aksiyon + 302),
 *   rotanın kendisi /dev-login değilse gate hiç devreye girmez.
 */
final class DevLoginGateTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    /** Üretim + DEBUG yanlışlıkla açık: 404, aksiyon çağrılmaz, session'a YAZMA. */
    public function testProductionRequestIsBlockedAndSessionNeverWritten(): void
    {
        $actionCalls = 0;

        $status = DevLoginGate::handle(
            'production',
            true,
            DevLoginGate::PATH,
            function () use (&$actionCalls): void {
                $actionCalls++;
                $_SESSION['MM_UserID'] = 'adm-uuid-001';
            },
        );

        self::assertSame(404, $status);
        self::assertSame(0, $actionCalls, 'üretimde dev-login aksiyonu asla çalışmaz');
        self::assertSame([], $_SESSION, 'session admin anahtarı YAZILMADI');
        self::assertArrayNotHasKey('MM_UserID', $_SESSION);
    }

    /** Üretim + DEBUG kapalı: kapı yine 404 döner (kapı hiçbir koşulda açılmaz). */
    public function testProductionRequestWithoutDebugFlagIsAlsoBlocked(): void
    {
        $actionCalls = 0;

        $status = DevLoginGate::handle(
            'production',
            false,
            DevLoginGate::PATH,
            function () use (&$actionCalls): void {
                $actionCalls++;
            },
        );

        self::assertSame(404, $status);
        self::assertSame(0, $actionCalls);
        self::assertSame([], $_SESSION);
    }

    /** Geliştirme modu: mevcut davranış aynen — aksiyon çalışır (gerçek AdminAuthBridge), 302. */
    public function testDevelopmentModeKeepsExistingBehaviour(): void
    {
        $actionCalls = 0;

        $status = DevLoginGate::handle(
            'development',
            true,
            DevLoginGate::PATH,
            function () use (&$actionCalls): void {
                $actionCalls++;
                // bootstrap.php'deki dev-login aksiyonunun birebir karşılığı
                (new AdminAuthBridge('http://127.0.0.1:9'))->establishSession([
                    'id'           => 'adm-uuid-001',
                    'username'     => 'Bayram Ali',
                    'email'        => 'admin@coremusic.net',
                    'display_name' => 'Bayram Ali',
                    'account_type' => 'admin',
                    'avatar_url'   => '',
                    'gender'       => 'female',
                ]);
            },
        );

        self::assertSame(302, $status);
        self::assertSame(1, $actionCalls);
        self::assertSame('adm-uuid-001', $_SESSION['MM_UserID']);
        self::assertSame('admin', $_SESSION['MM_AccountType']);
        self::assertArrayHasKey('_session_rotated_at', $_SESSION);
    }

    /** Geliştirme modu + DEBUG kapalı: kapı devreye girmez, istek kernel'e bırakılır (eski davranış). */
    public function testDevelopmentModeWithDebugDisabledFallsThrough(): void
    {
        $actionCalls = 0;

        $status = DevLoginGate::handle(
            'development',
            false,
            DevLoginGate::PATH,
            function () use (&$actionCalls): void {
                $actionCalls++;
            },
        );

        self::assertNull($status);
        self::assertSame(0, $actionCalls);
        self::assertSame([], $_SESSION);
    }

    /** /dev-login DIŞındaki rotalar gate'e girmez — üretim dahil normal akış korunur. */
    public function testOtherRoutesAreNeverIntercepted(): void
    {
        $actionCalls = 0;
        $routes = ['/admin-dashboard', '/admin-songs', '', '/dev-loginx'];

        foreach ($routes as $route) {
            $status = DevLoginGate::handle(
                'production',
                true,
                $route,
                function () use (&$actionCalls): void {
                    $actionCalls++;
                },
            );
            self::assertNull($status, 'rota gate tarafından kesilmemeli: ' . $route);
        }

        self::assertSame(0, $actionCalls);
        self::assertSame([], $_SESSION);
    }

    /**
     * Kablolama kilidi: bootstrap gerçekten gate'e bağlanmalı ve APP_ENV_MODE'u
     * ilk parametre olarak geçmeli (gate sökülürse bu test kırmızıya döner).
     */
    public function testBootstrapWiresTheProductionGate(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../config/bootstrap.php');

        self::assertStringContainsString('DevLoginGate::handle(', $source);
        // EOL-agnostik (dosya CRLF): gate APP_ENV_MODE + DEBUG_MODE ile beslenmeli
        self::assertMatchesRegularExpression('/APP_ENV_MODE,\s+DEBUG_MODE/u', $source);
        self::assertStringNotContainsString("\$requestUri === '/dev-login'", $source);
    }
}