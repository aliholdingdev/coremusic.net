<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use PHPUnit\Framework\TestCase;
use CoreMusic\Middleware\MiddlewarePipeline;
use CoreMusic\Interfaces\Middleware\IMiddleware;

/**
 * MiddlewarePipeline — Sıra Doğrulama Testleri
 *
 * ADR-010/011/012/013/022 ile doğrudan ilgili.
 * Pipeline sırası değişirse CSP/CSRF bozulur.
 *
 * @covers \CoreMusic\Middleware\MiddlewarePipeline
 */
final class MiddlewarePipelineTest extends TestCase
{
    /* ============================================================
       PIPELINE SIRASI TESTLERİ
       ============================================================ */

    public function testEmptyPipelineRunsCore(): void
    {
        $pipeline = new MiddlewarePipeline();
        $result = $pipeline->run(['method' => 'GET'], function (array $req) {
            return ['status' => 200, 'body' => 'ok'];
        });

        $this->assertSame(200, $result['status']);
        $this->assertSame('ok', $result['body']);
    }

    public function testSingleMiddlewareIsExecuted(): void
    {
        $executed = [];
        $middleware = $this->createMiddleware(function (array $req, callable $next) use (&$executed) {
            $executed[] = 'm1_before';
            $response = $next($req);
            $executed[] = 'm1_after';
            return $response;
        });

        $pipeline = new MiddlewarePipeline();
        $pipeline->pipe($middleware);

        $pipeline->run(['method' => 'GET'], function (array $req) use (&$executed) {
            $executed[] = 'core';
            return ['status' => 200];
        });

        $this->assertSame(['m1_before', 'core', 'm1_after'], $executed);
    }

    public function testMultipleMiddlewareExecutionOrder(): void
    {
        $executed = [];

        $m1 = $this->createMiddleware(function (array $req, callable $next) use (&$executed) {
            $executed[] = 'm1_before';
            $response = $next($req);
            $executed[] = 'm1_after';
            return $response;
        });

        $m2 = $this->createMiddleware(function (array $req, callable $next) use (&$executed) {
            $executed[] = 'm2_before';
            $response = $next($req);
            $executed[] = 'm2_after';
            return $response;
        });

        $m3 = $this->createMiddleware(function (array $req, callable $next) use (&$executed) {
            $executed[] = 'm3_before';
            $response = $next($req);
            $executed[] = 'm3_after';
            return $response;
        });

        $pipeline = new MiddlewarePipeline();
        $pipeline->pipe($m1)->pipe($m2)->pipe($m3);

        $pipeline->run(['method' => 'GET'], function (array $req) use (&$executed) {
            $executed[] = 'core';
            return ['status' => 200];
        });

        // Pipeline: m1 -> m2 -> m3 -> core -> m3 -> m2 -> m1 (onion model)
        $this->assertSame([
            'm1_before', 'm2_before', 'm3_before',
            'core',
            'm3_after', 'm2_after', 'm1_after',
        ], $executed);
    }

    public function testPipelinePreservesRequestData(): void
    {
        $capturedRequest = null;

        $middleware = $this->createMiddleware(function (array $req, callable $next) use (&$capturedRequest) {
            $capturedRequest = $req;
            return $next($req);
        });

        $pipeline = new MiddlewarePipeline();
        $pipeline->pipe($middleware);

        $request = ['method' => 'POST', 'uri' => '/test', 'body' => ['key' => 'value']];
        $pipeline->run($request, fn(array $req) => ['status' => 200]);

        $this->assertSame($request, $capturedRequest);
    }

    public function testMiddlewareCanModifyRequest(): void
    {
        $middleware = $this->createMiddleware(function (array $req, callable $next) {
            $req['modified'] = true;
            return $next($req);
        });

        $capturedRequest = null;
        $pipeline = new MiddlewarePipeline();
        $pipeline->pipe($middleware);

        $pipeline->run(['method' => 'GET'], function (array $req) use (&$capturedRequest) {
            $capturedRequest = $req;
            return ['status' => 200];
        });

        $this->assertTrue($capturedRequest['modified'] ?? false);
    }

    public function testMiddlewareCanShortCircuit(): void
    {
        $coreCalled = false;

        $middleware = $this->createMiddleware(function (array $req, callable $next) {
            // Short-circuit: don't call $next
            return ['httpStatus' => 403, 'halt' => true];
        });

        $pipeline = new MiddlewarePipeline();
        $pipeline->pipe($middleware);

        $result = $pipeline->run(['method' => 'GET'], function (array $req) use (&$coreCalled) {
            $coreCalled = true;
            return ['status' => 200];
        });

        $this->assertFalse($coreCalled, 'Core should not be called when middleware short-circuits');
        $this->assertSame(403, $result['httpStatus']);
    }

    public function testPipelineCanBeReused(): void
    {
        $callCount = 0;

        $middleware = $this->createMiddleware(function (array $req, callable $next) use (&$callCount) {
            $callCount++;
            return $next($req);
        });

        $pipeline = new MiddlewarePipeline();
        $pipeline->pipe($middleware);

        $pipeline->run(['method' => 'GET'], fn(array $req) => ['status' => 200]);
        $pipeline->run(['method' => 'POST'], fn(array $req) => ['status' => 200]);

        $this->assertSame(2, $callCount, 'Pipeline should be reusable');
    }

    /* ============================================================
       10-ADIMLI PIPELINE SIRA TESTİ (ADR-010/011/012/013)
       ============================================================ */

    public function testTenStepPipelineOrderIsCorrect(): void
    {
        $executionOrder = [];

        $createOrderMiddleware = function (string $name) use (&$executionOrder) {
            return $this->createMiddleware(function (array $req, callable $next) use ($name, &$executionOrder) {
                $executionOrder[] = $name;
                return $next($req);
            });
        };

        // ADR-010/011/012/013/022 pipeline sırası
        $pipeline = new MiddlewarePipeline();
        $pipeline
            ->pipe($createOrderMiddleware('1_OriginCheck'))
            ->pipe($createOrderMiddleware('2_Cors'))
            ->pipe($createOrderMiddleware('3_RateLimiter'))
            ->pipe($createOrderMiddleware('4_SecurityHeaders'))
            ->pipe($createOrderMiddleware('5_SessionManager'))
            ->pipe($createOrderMiddleware('6_Csrf'))
            ->pipe($createOrderMiddleware('7_BypassAuth'))
            ->pipe($createOrderMiddleware('8_Auth'))
            ->pipe($createOrderMiddleware('9_Permission'))
            ->pipe($createOrderMiddleware('10_Validation'));

        $pipeline->run(['method' => 'GET'], function (array $req) use (&$executionOrder) {
            $executionOrder[] = 'controller';
            return ['status' => 200];
        });

        $expected = [
            '1_OriginCheck',
            '2_Cors',
            '3_RateLimiter',
            '4_SecurityHeaders',
            '5_SessionManager',
            '6_Csrf',
            '7_BypassAuth',
            '8_Auth',
            '9_Permission',
            '10_Validation',
            'controller',
        ];

        $this->assertSame($expected, $executionOrder, 'Middleware pipeline order must match ADR-010/011/012/013/022');
    }

    public function testCspNonceGeneratedBeforeSessionManager(): void
    {
        // CSP nonce SecurityHeaders (#4) üretilir, SessionManager (#5) session'a kaydeder.
        // Sıra değişirse CSP bozulur.
        $nonceGenerated = false;
        $nonceSavedToSession = false;

        $securityHeaders = $this->createMiddleware(function (array $req, callable $next) use (&$nonceGenerated) {
            $nonceGenerated = true; // CSP nonce üretilir
            return $next($req);
        });

        $sessionManager = $this->createMiddleware(function (array $req, callable $next) use (&$nonceGenerated, &$nonceSavedToSession) {
            // Session'a nonce kaydedilir — SecurityHeaders zaten çalışmış olmalı
            $nonceSavedToSession = $nonceGenerated;
            return $next($req);
        });

        $pipeline = new MiddlewarePipeline();
        $pipeline->pipe($securityHeaders)->pipe($sessionManager);

        $pipeline->run(['method' => 'GET'], fn(array $req) => ['status' => 200]);

        $this->assertTrue($nonceGenerated, 'SecurityHeaders must generate nonce first');
        $this->assertTrue($nonceSavedToSession, 'SessionManager must save nonce after SecurityHeaders');
    }

    /* ============================================================
       HELPER
       ============================================================ */

    private function createMiddleware(callable $handler): IMiddleware
    {
        return new class ($handler) implements IMiddleware {
            /** @var \Closure */
            private $handler;

            public function __construct(\Closure $handler)
            {
                $this->handler = $handler;
            }

            public function handle(array $request, callable $next): array
            {
                return ($this->handler)($request, $next);
            }
        };
    }
}
