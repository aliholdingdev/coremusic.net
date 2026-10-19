<?php declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\ApiRequest;
use PHPUnit\Framework\TestCase;

/**
 * withAttribute immutability (PSR-7 `with*` sözleşmesi):
 *   - orijinal request attribute'suz kalır
 *   - klon attribute taşır
 *   - chaining her adımda yeni instance üretir
 */
final class ApiRequestAttributeTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $serverBackup = [];

    /** @var array<string, mixed> */
    private array $getBackup = [];

    /** @var array<string, mixed> */
    private array $postBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
        $this->getBackup = $_GET;
        $this->postBackup = $_POST;

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/test';
        $_SERVER['QUERY_STRING'] = '';
        $_GET = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        $_GET = $this->getBackup;
        $_POST = $this->postBackup;
    }

    public function testWithAttributeReturnsNewInstanceAndKeepsOriginalUnchanged(): void
    {
        $original = new ApiRequest();
        $modified = $original->withAttribute('route', ['path' => '/test']);

        $this->assertNotSame($original, $modified, 'withAttribute klon döndürmeli, mutasyon yapmamalı');
        $this->assertNull($original->getAttribute('route'), 'orijinal request attribute\'suz kalmalı');
        $this->assertSame(['path' => '/test'], $modified->getAttribute('route'));
    }

    public function testWithAttributeIsChainable(): void
    {
        $original = new ApiRequest();
        $modified = $original
            ->withAttribute('version', 'v1')
            ->withAttribute('route', ['path' => '/test']);

        $this->assertNull($original->getAttribute('version'));
        $this->assertNull($original->getAttribute('route'));
        $this->assertSame('v1', $modified->getAttribute('version'));
        $this->assertSame(['path' => '/test'], $modified->getAttribute('route'));
    }

    public function testWithAttributeOverwritesSameKeyOnCloneOnly(): void
    {
        $original = (new ApiRequest())->withAttribute('version', 'v1');
        $modified = $original->withAttribute('version', 'v2');

        $this->assertSame('v1', $original->getAttribute('version'));
        $this->assertSame('v2', $modified->getAttribute('version'));
    }

    public function testAttributeZeroIsPreserved(): void
    {
        $modified = (new ApiRequest())->withAttribute('count', 0);

        $this->assertSame(0, $modified->getAttribute('count'));
    }

    public function testGatewayStyleRouteAttributeVisibleAfterCall(): void
    {
        // Gateway dispatch akışının simülasyonu (Gateway.php:82 davranışı):
        // dönüş değeri KULLANILIR; aksi halde immutable kopya kaybolur.
        $apiRequest = new ApiRequest();
        $apiRequest = $apiRequest->withAttribute('version', 'v1');

        $route = ['service' => 'auth', 'action' => 'login'];
        $apiRequest = $apiRequest->withAttribute('route', $route);

        $this->assertSame('v1', $apiRequest->getAttribute('version'));
        $this->assertSame($route, $apiRequest->getAttribute('route'));
    }
}
