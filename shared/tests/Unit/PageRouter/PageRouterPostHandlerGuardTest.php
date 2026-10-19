<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\PageRouter\AuthGuard;
use CoreMusic\PageRouter\AuthUrlBuilder;
use CoreMusic\PageRouter\PageRouter;
use CoreMusic\PageRouter\PageRouterHelper;
use CoreMusic\PageRouter\RouteRegistry;
use CoreMusic\PageRouter\SpaRoute;
use PHPUnit\Framework\TestCase;

/**
 * PageRouter — POST handler / auth guard SIRASI regresyonu.
 *
 * Yetki atlama yüzeyi: POST handler bloğu authGuard ÖNCESİNE taşınırsa kimliksiz
 * istek handler'a ulaşır. Guard her zaman önce çalışmalı.
 *
 * @covers \CoreMusic\PageRouter\PageRouter
 */
final class PageRouterPostHandlerGuardTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    /**
     * POST handler'lı 'profile' rotasıyla dispatch eder.
     *
     * @param array<string, mixed> $request
     * @return array{result: array<string, mixed>, handlerCalled: bool}
     */
    private function dispatch(array $request): array
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(
            page: 'profile',
            requiresAuth: true,
            title: 'Profil',
            path: 'profile',
            handler: 'ProfileHandler',
        ));

        $helper       = new PageRouterHelper();
        $domainConfig = new DomainConfig();
        $urlBuilder   = new AuthUrlBuilder($domainConfig, $helper);

        $handlerState = new \stdClass();
        $handlerState->called = false;

        $handlers = [
            'profile' => new class ($handlerState) {
                public function __construct(private readonly \stdClass $state) {}

                public function handle(array $request): array
                {
                    $this->state->called = true;

                    return ['handled' => true];
                }
            },
        ];

        $router = new PageRouter(
            registry: $registry,
            config: new ConfigManager(),
            domainConfig: $domainConfig,
            authHelper: $helper,
            authGuard: new AuthGuard($helper, $urlBuilder),
            urlBuilder: $urlBuilder,
            handlers: $handlers,
        );

        return [
            'result'        => $router->dispatch($request, 'csrf-token', true),
            'handlerCalled' => (bool)$handlerState->called,
        ];
    }

    public function testUnauthenticatedPostRequestIsBlockedByGuard(): void
    {
        $_SESSION = [];

        ['result' => $result, 'handlerCalled' => $handlerCalled] = $this->dispatch([
            'method' => 'POST',
            'uri'    => 'profile',
        ]);

        $this->assertFalse($handlerCalled, 'Kimliksiz istek POST handler\'a ulaşmamalı (yetki atlama)');
        $this->assertSame(403, $result['httpStatus'], 'SPA isteği guard tarafından reddedilmeli');
        $this->assertArrayHasKey('redirect', $result['body']);
    }

    public function testAuthenticatedPostRequestReachesHandler(): void
    {
        $_SESSION = ['MM_UserID' => '42'];

        ['result' => $result, 'handlerCalled' => $handlerCalled] = $this->dispatch([
            'method' => 'POST',
            'uri'    => 'profile',
        ]);

        $this->assertTrue($handlerCalled, 'Kimliği doğrulanmış istek handler\'a ulaşmalı');
        $this->assertTrue((bool)($result['handled'] ?? false));
    }
}
