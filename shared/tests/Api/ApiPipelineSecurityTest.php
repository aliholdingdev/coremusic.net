<?php
declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Auth\ApiSessionManager;
use CoreMusic\Api\Gateway;
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;
use CoreMusic\Api\Middleware\AuthorizationMiddleware;
use CoreMusic\Api\Registry\ServiceRegistry;
use CoreMusic\Api\Versioning\VersionResolver;
use CoreMusic\Middleware\CorsMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * ADR-020 regresyon testleri:
 *   §1.1-B.1 pipeline kaydı · §2.2E CORS (Bearer başlıkları + fail-closed) · §2.2F hata sızıntısı
 */
final class ApiPipelineSecurityTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    public function testCorsDefaultAllowedHeadersContainBearerAndApiKey(): void
    {
        $middleware = new CorsMiddleware([
            'allowed_origins' => ['https://auth.coremusic.net'],
            'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
        ]);

        $response = $middleware->handle(
            ['server' => ['HTTP_ORIGIN' => 'https://auth.coremusic.net'], 'method' => 'OPTIONS'],
            static fn (): array => ['httpStatus' => 204, 'type' => 'json', 'body' => '', 'headers' => [], 'halt' => true]
        );

        $allowedHeaders = $response['headers']['Access-Control-Allow-Headers'] ?? '';
        $this->assertStringContainsString('Authorization', $allowedHeaders);
        $this->assertStringContainsString('X-Api-Key', $allowedHeaders);
        $this->assertSame('https://auth.coremusic.net', $response['headers']['Access-Control-Allow-Origin'] ?? null);
    }

    public function testCorsFailsClosedForUnknownOrigin(): void
    {
        $middleware = new CorsMiddleware(['allowed_origins' => ['https://auth.coremusic.net']]);

        $response = $middleware->handle(
            ['server' => ['HTTP_ORIGIN' => 'https://evil.example'], 'method' => 'OPTIONS'],
            static fn (): array => ['httpStatus' => 204, 'type' => 'json', 'body' => '', 'headers' => [], 'halt' => true]
        );

        $this->assertArrayNotHasKey('Access-Control-Allow-Origin', $response['headers']);
        $this->assertArrayNotHasKey('Access-Control-Allow-Credentials', $response['headers']);
    }

    public function testCorsAddsNoHeaderWhenOriginMissing(): void
    {
        $middleware = new CorsMiddleware(['allowed_origins' => ['https://auth.coremusic.net']]);

        $response = $middleware->handle(
            ['server' => [], 'method' => 'GET'],
            static fn (): array => ['httpStatus' => 200, 'type' => 'json', 'body' => '', 'headers' => [], 'halt' => false]
        );

        $this->assertArrayNotHasKey('Access-Control-Allow-Origin', $response['headers']);
    }

    public function testGatewayDoesNotLeakExceptionMessageToClient(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/auth';

        $pipeline = new ApiMiddlewarePipeline();
        $pipeline->pipe(static function (): array {
            throw new \RuntimeException('DB_PASSWORD_LEAKED_IN_EXCEPTION');
        });

        $gateway = new Gateway(new VersionResolver(), new ServiceRegistry(), $pipeline);
        $result = $gateway->dispatch(['server' => $_SERVER, 'method' => 'GET', 'uri' => '/api/v1/auth']);

        $this->assertSame('INTERNAL_ERROR', $result['error']['code'] ?? null);
        $this->assertArrayNotHasKey('details', $result['error']);
        $this->assertStringNotContainsString('DB_PASSWORD_LEAKED_IN_EXCEPTION', (string) json_encode($result));
    }

    public function testGatewayResolvesRouteAfterNamespaceFix(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/auth';

        $gateway = new Gateway(new VersionResolver(), new ServiceRegistry(), new ApiMiddlewarePipeline());
        $result = $gateway->dispatch(['server' => $_SERVER, 'method' => 'GET', 'uri' => '/api/v1/auth']);

        $this->assertArrayHasKey('data', $result);
        $this->assertSame('auth', $result['data']['service'] ?? null);
    }

    public function testUnmatchedRouteStillGoesThroughPipeline(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/unknown';

        $entered = false;
        $pipeline = new ApiMiddlewarePipeline();
        $pipeline->pipe(static function (array $request, callable $next) use (&$entered): array {
            $entered = true;

            return $next($request);
        });

        $gateway = new Gateway(new VersionResolver(), new ServiceRegistry(), $pipeline);
        $result = $gateway->dispatch(['server' => $_SERVER, 'method' => 'GET', 'uri' => '/api/v1/unknown']);

        $this->assertTrue($entered, 'Eşleşmeyen rotalar da pipeline\'dan geçmeli (rate limit/CORS kaçağı olmamalı)');
        $this->assertSame('NOT_FOUND', $result['error']['code'] ?? null);
    }

    public function testAuthorizationMiddlewareAllowsPublicRouteWithoutAuth(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/auth/login';
        $middleware = new AuthorizationMiddleware();
        $called = false;

        $response = $middleware([], function () use (&$called): array {
            $called = true;

            return ['ok' => true];
        });

        $this->assertTrue($called);
        $this->assertSame(['ok' => true], $response);
    }

    public function testAuthorizationMiddlewareRejectsUnauthenticatedPrivateRoute(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/v1/user';
        $middleware = new AuthorizationMiddleware();

        $response = $middleware([], static fn (): array => ['ok' => true]);

        $this->assertSame('UNAUTHORIZED', $response['error']['code'] ?? null);
    }

    public function testApiSessionManagerIsUnauthenticatedWithoutSession(): void
    {
        $session = new ApiSessionManager();

        $this->assertFalse($session->isAuthenticated());
        $this->assertNull($session->getUserId());
    }

    public function testIndexRegistersPipelineInFrozenOrder(): void
    {
        $indexPath = dirname(__DIR__, 3) . '/api.coremusic.net/index.php';
        $this->assertFileExists($indexPath);

        $source = (string) file_get_contents($indexPath);
        $start = strpos($source, 'new ApiMiddlewarePipeline()');
        $this->assertNotFalse($start, 'ApiMiddlewarePipeline index.php\'de kurulmalı (ADR-020 §1.1-B.1)');

        $chain = substr($source, $start, 3000);
        $markers = [
            'ResponseNormalizationMiddleware',
            '$corsMiddleware->handle',
            'new RateLimitMiddleware',
            'new AuthenticationMiddleware',
            'new RequestValidationMiddleware',
            'new AuthorizationMiddleware',
        ];

        $last = -1;
        foreach ($markers as $marker) {
            $position = strpos($chain, $marker);
            $this->assertNotFalse($position, "{$marker} pipeline'a kayıtlı olmalı");
            $this->assertGreaterThan($last, $position, "{$marker} frozen sırada olmalı");
            $last = $position;
        }
    }
}
