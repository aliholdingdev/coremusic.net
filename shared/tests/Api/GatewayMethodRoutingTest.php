<?php declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Gateway;
use CoreMusic\Api\Middleware\ApiMiddlewarePipeline;
use CoreMusic\Api\Registry\ServiceRegistry;
use CoreMusic\Api\Routing\RouteTable;
use CoreMusic\Api\Versioning\VersionResolver;
use PHPUnit\Framework\TestCase;

/**
 * Gateway + route tablosu entegrasyonu (Faz 1a):
 *   - `_route` pipeline array'ine aktarılır (Faz 2 notu #2)
 *   - 405 (+ Allow) vs 404 ayrımı
 *   - GET davranışları korunur
 */
final class GatewayMethodRoutingTest extends TestCase
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

    private function apiRouteTable(): RouteTable
    {
        $path = dirname(__DIR__, 3) . '/api.coremusic.net/config/routes.php';
        $this->assertFileExists($path);

        return RouteTable::fromArray(require $path);
    }

    private function dispatch(string $method, string $uri, ?ApiMiddlewarePipeline $pipeline = null): array
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;

        $gateway = new Gateway(
            new VersionResolver(),
            new ServiceRegistry(),
            $pipeline ?? new ApiMiddlewarePipeline(),
            $this->apiRouteTable(),
        );

        return $gateway->dispatch(['server' => $_SERVER, 'method' => $method, 'uri' => $uri]);
    }

    public function testPostLoginIsNot404(): void
    {
        $result = $this->dispatch('POST', '/api/v1/auth/login');

        // Faz 1b: POST login implemented=true → handler'a gider (405 kalktı).
        // Bu testte Gateway'e resolver verilmez → Faz 0 placeholder gövdesi döner.
        $this->assertSame('auth', $result['data']['service'] ?? null);
        $this->assertArrayNotHasKey('error', $result, 'POST login artık 404/405 değil');
        $this->assertLessThan(400, http_response_code() ?: 200);
    }

    public function testMethodMismatchReturns405WithAllowHeader(): void
    {
        $result = $this->dispatch('PUT', '/api/v1/auth/login');

        $this->assertSame('METHOD_NOT_ALLOWED', $result['error']['code'] ?? null);
        $this->assertArrayHasKey('Allow', $result['headers'] ?? [], 'Allow başlığı response.headers üzerinden gitmeli');
        $this->assertStringContainsString('GET', $result['headers']['Allow']);
    }

    public function testUnknownPathReturns404WithoutAllowHeader(): void
    {
        $result = $this->dispatch('GET', '/api/v1/unknown-endpoint');

        $this->assertSame('NOT_FOUND', $result['error']['code'] ?? null);
        $this->assertArrayNotHasKey('Allow', $result['headers'] ?? []);
    }

    public function testGetLoginKeepsWorking(): void
    {
        $result = $this->dispatch('GET', '/api/v1/auth/login');

        $this->assertSame('auth', $result['data']['service'] ?? null);
    }

    public function testRouteIsVisibleToPipelineMiddlewares(): void
    {
        $captured = null;

        $pipeline = new ApiMiddlewarePipeline();
        $pipeline->pipe(static function (array $request, callable $next) use (&$captured): array {
            $captured = $request['_route'] ?? null;

            return $next($request);
        });

        $this->dispatch('POST', '/api/v1/auth/login', $pipeline);

        $this->assertIsArray($captured, '_route pipeline array\'ine taşınmalı (Faz 2 notu #2)');
        $this->assertSame('auth', $captured['service']);
        $this->assertTrue($captured['public']);
        // Faz 1b: AuthController bağlantısı
        $this->assertTrue($captured['implemented']);
        $this->assertSame('login', $captured['action']);
    }

    public function testUnmatchedRouteStillEntersPipeline(): void
    {
        $entered = false;

        $pipeline = new ApiMiddlewarePipeline();
        $pipeline->pipe(static function (array $request, callable $next) use (&$entered): array {
            $entered = true;

            return $next($request);
        });

        $result = $this->dispatch('GET', '/api/v1/unknown-endpoint', $pipeline);

        $this->assertTrue($entered, 'Eşleşmeyen rotalar da pipeline\'dan geçmeli');
        $this->assertSame('NOT_FOUND', $result['error']['code'] ?? null);
        $this->assertNull($result['_route'] ?? null, 'Pipeline isteğinde _route null olmalı');
    }

    public function testGetOnServicePrefixStillReturnsServicePayload(): void
    {
        $result = $this->dispatch('GET', '/api/v1/music');

        $this->assertSame('music', $result['data']['service'] ?? null);
    }

    public function testPostOnGetOnlyResourceReturns405(): void
    {
        $result = $this->dispatch('POST', '/api/v1/music');

        $this->assertSame('METHOD_NOT_ALLOWED', $result['error']['code'] ?? null);
        $this->assertStringContainsString('GET', (string) ($result['headers']['Allow'] ?? ''));
    }
}
