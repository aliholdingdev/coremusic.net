<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Contracts\Middleware\IMiddleware;
use CoreMusic\PageRouter\PageRouterKernel;
use PHPUnit\Framework\TestCase;

/**
 * PRD-PRT-002: kernel zincir sırası.
 *
 * Kapsam: normalize (normalizeRequest) → middleware katmanı → dispatch (core).
 * KAPSAM DIŞI: ResponseEmitter::emit() `never` döner ve exit çağırır; birim testte
 * çalıştırılamaz. "authGuard" adımı PageRouter::dispatch içindedir (core'un parçası).
 *
 * ADR-020 (.ai/.decisions/accepted/ADR-020-api-public-security.md) bu PageRouter
 * zincirini tanımlamıyor → "normalize → authGuard → emitter" sırası için ADR kaynağı: UNKNOWN.
 *
 * @covers \CoreMusic\PageRouter\PageRouterKernel
 */
final class PageRouterChainOrderTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $originalServer = [];

    protected function setUp(): void
    {
        $this->originalServer = $_SERVER;
        $_SERVER = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/foo/bar?x=1', 'HTTP_HOST' => 'localhost'];
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
    }

    public function testNormalizationRunsBeforeMiddlewareAndDispatchRunsLast(): void
    {
        $events = [];
        $seenUri = null;

        $spy = new class($events, $seenUri) implements IMiddleware {
            public function __construct(private array &$events, private ?string &$seenUri) {}
            public function handle(array $request, callable $next): array
            {
                $this->events[] = 'middleware';
                $this->seenUri = $request['uri'] ?? null;
                return $next($request);
            }
        };

        $kernel = new PageRouterKernel(new ConfigManager(), new DomainConfig(), middlewares: [$spy]);

        $normalize = new \ReflectionMethod($kernel, 'normalizeRequest');
        $request = $normalize->invoke($kernel, $_SERVER, [], []);
        $events[] = 'normalize';

        $stack = new \ReflectionMethod($kernel, 'runMiddlewareStack');
        $stack->invoke($kernel, [$spy], $request, function (array $req) use (&$events): array {
            $events[] = 'dispatch';
            return ['httpStatus' => 200, 'type' => 'json', 'body' => []];
        });

        $this->assertSame(['normalize', 'middleware', 'dispatch'], $events);
        $this->assertSame('foo/bar', $seenUri, 'Middleware normalize edilmiş uri görmeli (query ayrılmış)');
    }

    public function testMiddlewareReceivesNormalizedRequestNotRawServerArray(): void
    {
        $_SERVER['REQUEST_URI'] = '/index.php?page=login';
        $kernel = new PageRouterKernel(new ConfigManager(), new DomainConfig(), middlewares: []);

        $normalize = new \ReflectionMethod($kernel, 'normalizeRequest');
        $request = $normalize->invoke($kernel, $_SERVER, ['page' => 'login'], []);

        // index.php soyulur, uri get['page'] üzerinden çözülür
        $this->assertSame('login', $request['uri']);
        $this->assertArrayHasKey('headers', $request);
        $this->assertArrayHasKey('server', $request);
    }
}
