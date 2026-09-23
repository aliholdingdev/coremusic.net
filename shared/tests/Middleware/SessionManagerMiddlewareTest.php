<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use PHPUnit\Framework\TestCase;
use CoreMusic\Middleware\SessionManagerMiddleware;
use CoreMusic\Session\SessionLifecycle;

/**
 * SessionManagerMiddleware — Session Yönetimi Testleri
 *
 * ADR-011: Session management
 * Session başlatma, CSP nonce yönetimi, request enrichment.
 *
 * Not: SessionLifecycle final olduğundan mock yerine gerçek instance kullanılır.
 *
 * @covers \CoreMusic\Middleware\SessionManagerMiddleware
 */
final class SessionManagerMiddlewareTest extends TestCase
{
    private SessionManagerMiddleware $middleware;
    private SessionLifecycle $sessionLifecycle;

    protected function setUp(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Test ortamında session başlat
        ini_set('session.use_cookies', '0');
        session_start();

        $this->sessionLifecycle = new SessionLifecycle();
        $this->middleware = new SessionManagerMiddleware($this->sessionLifecycle);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /* ============================================================
       SESSION BAŞLATMA
       ============================================================ */

    public function testSessionIsStarted(): void
    {
        $coreCalled = false;

        $this->middleware->handle(
            ['method' => 'GET'],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled);
    }

    /* ============================================================
       CSP NONCE YÖNETİMİ
       ============================================================ */

    public function testCspNonceInjectedIntoRequest(): void
    {
        $capturedRequest = null;

        $this->middleware->handle(
            ['method' => 'GET'],
            function (array $req) use (&$capturedRequest) {
                $capturedRequest = $req;
                return ['status' => 200];
            }
        );

        $this->assertArrayHasKey('_csp_nonce', $capturedRequest);
        $this->assertNotEmpty($capturedRequest['_csp_nonce'], 'CSP nonce should be generated');
        $this->assertIsString($capturedRequest['_csp_nonce']);
    }

    public function testCspNonceIsBase64Encoded(): void
    {
        $capturedRequest = null;

        $this->middleware->handle(
            ['method' => 'GET'],
            function (array $req) use (&$capturedRequest) {
                $capturedRequest = $req;
                return ['status' => 200];
            }
        );

        $nonce = $capturedRequest['_csp_nonce'];
        // Base64 formatında olmalı (harfler, rakamlar, +, /, =)
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9+\/]+=*$/', $nonce);
    }

    public function testExternalNonceIsStoredInSession(): void
    {
        // İlk istekte nonce gönder
        $capturedRequest = null;
        $this->middleware->handle(
            ['method' => 'GET', '_csp_nonce' => 'external-abc'],
            function (array $req) use (&$capturedRequest) {
                $capturedRequest = $req;
                return ['status' => 200];
            }
        );

        // External nonce session'a kaydedilmeli
        $this->assertSame('external-abc', $_SESSION['csp_nonce']);
        $this->assertSame('external-abc', $capturedRequest['_csp_nonce']);
    }

    public function testNullExternalNonceGeneratesNew(): void
    {
        // External nonce yoksa yeni üretilmeli
        $this->middleware->handle(
            ['method' => 'GET'],
            fn(array $req) => ['status' => 200]
        );

        $this->assertNotEmpty($_SESSION['csp_nonce']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $_SESSION['csp_nonce']);
    }

    /* ============================================================
       SESSION DATA ENJEKSİYONU
       ============================================================ */

    public function testSessionDataInjectedIntoRequest(): void
    {
        $_SESSION['test_key'] = 'test_value';
        $_SESSION['user_id'] = 42;

        $capturedRequest = null;

        $this->middleware->handle(
            ['method' => 'GET'],
            function (array $req) use (&$capturedRequest) {
                $capturedRequest = $req;
                return ['status' => 200];
            }
        );

        $this->assertArrayHasKey('_session', $capturedRequest);
        $this->assertSame('test_value', $capturedRequest['_session']['test_key']);
        $this->assertSame(42, $capturedRequest['_session']['user_id']);
    }

    public function testCsrfTokenInSessionData(): void
    {
        // SessionLifecycle csrf_token üretir
        $capturedRequest = null;

        $this->middleware->handle(
            ['method' => 'GET'],
            function (array $req) use (&$capturedRequest) {
                $capturedRequest = $req;
                return ['status' => 200];
            }
        );

        $this->assertArrayHasKey('csrf_token', $capturedRequest['_session']);
        $this->assertNotEmpty($capturedRequest['_session']['csrf_token']);
    }

    /* ============================================================
       SESSION TIMEOUT
       ============================================================ */

    public function testIdleTimeoutSetsLastActive(): void
    {
        $this->middleware->handle(
            ['method' => 'GET'],
            fn(array $req) => ['status' => 200]
        );

        $this->assertArrayHasKey('_session_last_active', $_SESSION);
        $this->assertIsNumeric($_SESSION['_session_last_active']);
    }

    /* ============================================================
       REQUEST MODIFICATION
       ============================================================ */

    public function testRequestPassedToNextMiddleware(): void
    {
        $capturedRequest = null;

        $this->middleware->handle(
            ['method' => 'POST', 'uri' => '/test'],
            function (array $req) use (&$capturedRequest) {
                $capturedRequest = $req;
                return ['status' => 200];
            }
        );

        // Orijinal request verileri korunmalı
        $this->assertSame('POST', $capturedRequest['method']);
        $this->assertSame('/test', $capturedRequest['uri']);

        // Session verileri eklenmeli
        $this->assertArrayHasKey('_csp_nonce', $capturedRequest);
        $this->assertArrayHasKey('_session', $capturedRequest);
    }

    public function testMultipleRequestsShareSession(): void
    {
        // İlk istek
        $this->middleware->handle(
            ['method' => 'GET'],
            fn(array $req) => ['status' => 200]
        );

        $firstNonce = $_SESSION['csp_nonce'] ?? null;

        // İkinci istek — aynı session
        $capturedRequest = null;
        $this->middleware->handle(
            ['method' => 'GET'],
            function (array $req) use (&$capturedRequest) {
                $capturedRequest = $req;
                return ['status' => 200];
            }
        );

        // Session devam etmeli
        $this->assertNotNull($firstNonce);
        $this->assertArrayHasKey('_session', $capturedRequest);
    }
}
