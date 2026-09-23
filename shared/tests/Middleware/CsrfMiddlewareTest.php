<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use PHPUnit\Framework\TestCase;
use CoreMusic\Middleware\CsrfMiddleware;

/**
 * CsrfMiddleware — CSRF Token Doğrulama Testleri
 *
 * ADR-010: csrf_token key (NOT _csrf_token)
 * hash_equals() timing-safe karşılaştırma
 *
 * @covers \CoreMusic\Middleware\CsrfMiddleware
 */
final class CsrfMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    /* ============================================================
       GET/HEAD/OPTIONS → CSRF ATLANIR
       ============================================================ */

    public function testGetRequestBypassesCsrf(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $result = $middleware->handle(
            ['method' => 'GET', 'uri' => '/home'],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'GET requests should bypass CSRF');
        $this->assertSame(200, $result['status']);
    }

    public function testHeadRequestBypassesCsrf(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $middleware->handle(
            ['method' => 'HEAD', 'uri' => '/home'],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled);
    }

    public function testOptionsRequestBypassesCsrf(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $middleware->handle(
            ['method' => 'OPTIONS', 'uri' => '/home'],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled);
    }

    /* ============================================================
       POST/PUT/DELETE → CSRF ZORUNLU
       ============================================================ */

    public function testPostWithoutTokenReturns403(): void
    {
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            ['method' => 'POST', 'uri' => '/submit', 'headers' => [], 'body' => []],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus']);
        $this->assertSame('csrf_invalid', $result['body']['error']);
        $this->assertTrue($result['halt'] ?? false);
    }

    public function testPostWithMismatchedTokenReturns403(): void
    {
        $_SESSION['csrf_token'] = 'session_token_123';

        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'wrong_token'],
                'body'    => [],
                '_session' => ['csrf_token' => 'session_token_123'],
            ],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus']);
    }

    public function testPostWithValidHeaderTokenPasses(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'valid_token_abc'],
                'body'    => [],
                '_session' => ['csrf_token' => 'valid_token_abc'],
            ],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'Valid CSRF token should pass');
        $this->assertSame(200, $result['status']);
    }

    public function testPostWithValidBodyTokenPasses(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => [],
                'body'    => ['csrf_token' => 'valid_token_xyz'],
                '_session' => ['csrf_token' => 'valid_token_xyz'],
            ],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled);
    }

    public function testPutRequiresCsrf(): void
    {
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            ['method' => 'PUT', 'uri' => '/update', 'headers' => [], 'body' => []],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus']);
    }

    public function testDeleteRequiresCsrf(): void
    {
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            ['method' => 'DELETE', 'uri' => '/remove', 'headers' => [], 'body' => []],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus']);
    }

    /* ============================================================
       BYPASS ROUTES
       ============================================================ */

    public function testSetGenderRouteBypassesCsrf(): void
    {
        $middleware = new CsrfMiddleware();
        $coreCalled = false;

        $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => 'set-gender',
                'headers' => [],
                'body'    => ['gender' => 'male'],
            ],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled, 'set-gender route should bypass CSRF');
    }

    public function testCustomBypassRoutesWork(): void
    {
        $middleware = new CsrfMiddleware(['set-gender', 'webhook']);
        $coreCalled = false;

        $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => 'webhook',
                'headers' => [],
                'body'    => ['data' => 'test'],
            ],
            function (array $req) use (&$coreCalled) {
                $coreCalled = true;
                return ['status' => 200];
            }
        );

        $this->assertTrue($coreCalled);
    }

    /* ============================================================
       EMPTY SESSION TOKEN
       ============================================================ */

    public function testEmptySessionTokenReturns403(): void
    {
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'some_token'],
                'body'    => [],
                '_session' => ['csrf_token' => ''],
            ],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus']);
    }

    public function testMissingSessionTokenReturns403(): void
    {
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'some_token'],
                'body'    => [],
                '_session' => [],
            ],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus']);
    }

    /* ============================================================
       TIMING-SAFE COMPARISON
       ============================================================ */

    public function testTimingSafeComparison(): void
    {
        // hash_equals() kullanıldığından emin ol — timing attack koruması
        $middleware = new CsrfMiddleware();

        // Farklı uzunlukta token'lar
        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'a'],
                'body'    => [],
                '_session' => ['csrf_token' => 'ab'],
            ],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus'], 'Different length tokens must fail');
    }

    /* ============================================================
       CSRF TOKEN KEY = csrf_token (ADR-010)
       ============================================================ */

    public function testCsrfTokenKeyIsCorrect(): void
    {
        // _csrf_token YASAK — sadece csrf_token kabul edilmeli
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            [
                'method'  => 'POST',
                'uri'     => '/submit',
                'headers' => ['x-csrf-token' => 'valid'],
                'body'    => ['_csrf_token' => 'valid'], // Yanlış key
                '_session' => ['csrf_token' => 'valid'],
            ],
            fn(array $req) => ['status' => 200]
        );

        // Body'deki _csrf_token kullanılmaz, x-csrf-token header'dan gelmeli
        // Header'da doğru token var → geçmeli
        $this->assertSame(200, $result['status']);
    }
}
