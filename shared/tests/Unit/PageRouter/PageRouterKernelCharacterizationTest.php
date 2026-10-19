<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Contracts\Middleware\IMiddleware;
use CoreMusic\PageRouter\PageRouterKernel;
use CoreMusic\PageRouter\RequestNormalizer;
use CoreMusic\PageRouter\RouteRegistry;
use CoreMusic\PageRouter\RouteResult;
use CoreMusic\PageRouter\SpaRoute;
use PHPUnit\Framework\TestCase;

/**
 * PageRouterKernel — KARAKTERİZASYON testleri.
 *
 * Amaç: god-class bölmesi öncesi mevcut davranışı kilit altına almak.
 * Bu testler "doğru davranış" savunmaz; kernel'in BUGÜNKÜ davranışını
 * dosya:satır kanıtlarıyla kayıt eder (bölme sonrası regresyon kapısı).
 *
 * Kernel kaynak referansları (PageRouterKernel.php):
 *   - constructor: 13 param + 8 default collaborator (L39-79)
 *   - handle(): traceId -> normalize -> route_meta -> middleware -> emit (L81-150)
 *   - resolveRouteMeta(): B-F-13 required_fields contract (L152-169)
 *   - wrapInHtmlShell(): redirect/forbidden/error/ok 4 dal (L171-226)
 *   - normalizeRequest(): \$_SERVER global okuması (L252-279)
 *   - extractHeaders(): HTTP_ prefix + CONTENT_TYPE (L281-294)
 *   - runMiddlewareStack(): onion pipeline (L296-303)
 *   - isSpaRequest(): XHR + Accept header (L305-319)
 *   - buildDefaultMiddlewares(): frozen ADR-010/011/012/013/022 sırası (L322-342)
 *   - generateTraceId(): UUID v4 (L344-350)
 *
 * NOT: handle()'un son adımı ResponseEmitter::emit() her yolda exit ile
 * biter (ResponseEmitter.php L54/L80/L91) -> süreç-içi çağrılamaz.
 * Bu yüzden emit-harici akış, dispatchAndWrap helper'ı ile birebir aynalanır.
 *
 * @covers \CoreMusic\PageRouter\PageRouterKernel
 */
final class PageRouterKernelCharacterizationTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $originalServer = [];

    /** @var array<string, mixed> */
    private array $originalSession = [];

    protected function setUp(): void
    {
        $this->originalServer  = $_SERVER;
        $this->originalSession = $_SESSION ?? [];

        // Deterministik baz: RequestNormalizer global okur (L260-271).
        $_SERVER  = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/', 'HTTP_HOST' => 'localhost'];
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SERVER  = $this->originalServer;
        $_SESSION = $this->originalSession;
    }

    // ------------------------------------------------------------------
    // Yardımcılar (reflection: kernel metotları private)
    // ------------------------------------------------------------------

    private function makeKernel(
        ?RouteRegistry $registry = null,
        ?array $middlewares = null,
        ?array $handlers = null,
        ?ConfigManager $config = null,
    ): PageRouterKernel {
        return new PageRouterKernel(
            config:       $config ?? new ConfigManager(),
            domainConfig: new DomainConfig(),
            registry:     $registry,
            middlewares:  $middlewares,
            handlers:     $handlers,
        );
    }

    /**
     * Kernel private metodunu çağırır (LSP yerine reflection — karakterizasyon).
     */
    private function invokePrivate(object $target, string $method, mixed ...$args): mixed
    {
        $ref = new \ReflectionMethod($target, $method);
        return $ref->invoke($target, ...$args);
    }

    /**
     * Kernel private propertysini okur (default collaborator doğrulaması).
     */
    private function getPrivateProperty(object $target, string $name): mixed
    {
        $ref = new \ReflectionProperty($target, $name);
        return $ref->getValue($target);
    }

    /**
     * handle() akışının emit-harici aynası.
     *
     * PageRouterKernel::handle() sırası (L104 -> L109 -> L116 -> L118 -> L120):
     *   route_meta set -> runMiddlewareStack(core) -> dispatch -> (!spa: wrapInHtmlShell)
     * emit (L120) dışarıda bırakılır çünkü emit her yolda exit ile biter.
     *
     * @param array<string, mixed> $request
     * @param \CoreMusic\Contracts\Middleware\IMiddleware[] $middlewares
     * @return array<string, mixed>
     */
    private function dispatchAndWrap(PageRouterKernel $kernel, array $request, bool $isSpa, array $middlewares = []): array
    {
        $ref = new \ReflectionClass($kernel);

        $router = $ref->getProperty('router')->getValue($kernel);

        // L104: pipeline BAŞINDA route meta set edilir.
        $request['_route_meta'] = $this->invokePrivate(
            $kernel,
            'resolveRouteMeta',
            (string)($request['uri'] ?? '')
        );

        $stack = $ref->getMethod('runMiddlewareStack');
        $wrap  = $ref->getMethod('wrapInHtmlShell');

        return $stack->invoke($kernel, $middlewares, $request, function (array $req) use ($kernel, $router, $wrap, $isSpa): array {
            $result = $router->dispatch($req, 'csrf-token', $isSpa);
            if (!$isSpa) {
                return $wrap->invoke($kernel, $result, [], $req);
            }
            return $result;
        });
    }

    // ------------------------------------------------------------------
    // A. Constructor & default wiring — frozen middleware sırası
    // ------------------------------------------------------------------

    /**
     * buildDefaultMiddlewares() sırası ADR-010/011/012/013/022 ile FROZEN.
     * Kernel bölmesinde bu sıra değişmemeli.
     */
    public function testDefaultMiddlewareOrderIsFrozen(): void
    {
        $kernel = $this->makeKernel();

        /** @var array<int, object> $middlewares */
        $middlewares = $this->getPrivateProperty($kernel, 'middlewares');

        $shortNames = array_map(
            static fn (object $m): string => substr(strrchr(get_class($m), '\\') ?: get_class($m), 1),
            $middlewares
        );

        $this->assertSame([
            'OriginCheckMiddleware',
            'CorsMiddleware',
            'RateLimiterMiddleware',
            'SecurityHeadersMiddleware',
            'SessionManagerMiddleware',
            'CsrfMiddleware',
            'BypassAuthMiddleware',
            'AuthMiddleware',
            'PermissionMiddleware',
            'ValidationMiddleware',
        ], $shortNames, 'Frozen pipeline sırası (ADR-010/011/012/013/022) korunmalı');
    }

    public function testInjectedMiddlewaresReplaceDefaults(): void
    {
        $fake = new class implements IMiddleware {
            public function handle(array $request, callable $next): array
            {
                return $next($request);
            }
        };

        $kernel = $this->makeKernel(middlewares: [$fake]);
        $middlewares = $this->getPrivateProperty($kernel, 'middlewares');

        $this->assertSame([$fake], $middlewares, 'Enjekte edilen middleware listesi default\'u ezmez — birebir kullanılır');
    }

    public function testDefaultCollaboratorsAreWired(): void
    {
        $kernel = $this->makeKernel();

        $this->assertInstanceOf(RequestNormalizer::class, $this->getPrivateProperty($kernel, 'normalizer'));
        $this->assertInstanceOf(RouteRegistry::class, $this->getPrivateProperty($kernel, 'registry'));
        $this->assertSame([], $this->getPrivateProperty($kernel, 'handlers'), 'Default handlers boş array');

        // Router her zaman kernel'in registry instance'ı ile kurulur (L62-71) —
        // bölme sonrası iki ayrı registry oluşursa resolveRouteMeta ile dispatch
        // farklı rota görür (sessiz kırılma).
        $router    = $this->getPrivateProperty($kernel, 'router');
        $routerRef = new \ReflectionProperty($router, 'registry');
        $this->assertSame(
            $this->getPrivateProperty($kernel, 'registry'),
            $routerRef->getValue($router),
            'Kernel registry ile PageRouter registry AYNI instance olmalı'
        );
    }

    // ------------------------------------------------------------------
    // B. resolveRouteMeta — B-F-13 required_fields contract (L152-169)
    // ------------------------------------------------------------------

    public function testResolveRouteMetaReturnsRolePermissionAndFields(): void
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(
            page:              'profile',
            requiresAuth:      true,
            title:             'Profil',
            requiredRole:      'admin',
            requiredPermission: 'profile.edit',
            meta:              ['required_fields' => ['email' => 'required']],
            path:              'profile',
        ));

        $kernel = $this->makeKernel(registry: $registry, middlewares: []);
        $meta   = $this->invokePrivate($kernel, 'resolveRouteMeta', '/profile/');

        $this->assertSame('admin', $meta['requiredRole']);
        $this->assertSame('profile.edit', $meta['requiredPermission']);
        $this->assertSame(['email' => 'required'], $meta['required_fields'], 'B-F-13: required_fields route meta üzerinden taşınır');
    }

    public function testResolveRouteMetaReturnsEmptyArrayForUnknownRoute(): void
    {
        $kernel = $this->makeKernel(registry: new RouteRegistry(), middlewares: []);

        $this->assertSame([], $this->invokePrivate($kernel, 'resolveRouteMeta', 'yok-boyle-rota'));
    }

    public function testResolveRouteMetaNormalizesMissingRequiredFieldsToArray(): void
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(
            page:         'kesfet',
            requiresAuth: false,
            title:        'Keşfet',
            path:         'kesfet',
        ));

        $kernel = $this->makeKernel(registry: $registry, middlewares: []);
        $meta   = $this->invokePrivate($kernel, 'resolveRouteMeta', 'kesfet');

        $this->assertNull($meta['requiredRole']);
        $this->assertNull($meta['requiredPermission']);
        $this->assertSame([], $meta['required_fields'], 'required_fields tanımlı değilse [] — middleware pasif kalır (L163-166)');
    }

    // ------------------------------------------------------------------
    // C. isSpaRequest (L305-319)
    // ------------------------------------------------------------------

    public function testIsSpaRequestDetectsXRequestedWithHeader(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $isSpa = $this->invokePrivate($kernel, 'isSpaRequest', [
            'headers' => ['x-requested-with' => 'XMLHttpRequest'],
        ]);

        $this->assertTrue($isSpa, 'X-Requested-With: XMLHttpRequest -> SPA (L308-310)');
    }

    public function testIsSpaRequestDetectsJsonAcceptHeader(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $isSpa = $this->invokePrivate($kernel, 'isSpaRequest', [
            'headers' => ['accept' => 'text/html, application/json'],
        ]);

        $this->assertTrue($isSpa, 'Accept header application/json içeriyorsa SPA (L313-316)');
    }

    public function testIsSpaRequestReturnsFalseForClassicRequest(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $isSpa = $this->invokePrivate($kernel, 'isSpaRequest', [
            'headers' => ['accept' => 'text/html'],
        ]);

        $this->assertFalse($isSpa, 'XHR/Accept yoksa klasik istek');
    }

    // ------------------------------------------------------------------
    // D. generateTraceId — UUID v4 (L344-350)
    // ------------------------------------------------------------------

    public function testGenerateTraceIdIsUuidV4FormatAndUnique(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $a = $this->invokePrivate($kernel, 'generateTraceId');
        $b = $this->invokePrivate($kernel, 'generateTraceId');

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $a,
            'traceId UUID v4 formatında olmalı (RFC 4122)'
        );
        $this->assertNotSame($a, $b, 'İki istek farklı traceId alır');
    }

    // ------------------------------------------------------------------
    // E. normalizeRequest — \$_SERVER global okuması (L252-279)
    // ------------------------------------------------------------------

    public function testNormalizeRequestStripsIndexPhpAndFallsBackToGetPage(): void
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'post',
            'REQUEST_URI'    => '/index.php?page=kesfet',
        ];

        $kernel  = $this->makeKernel(middlewares: []);
        $request = $this->invokePrivate($kernel, 'normalizeRequest', [], ['page' => 'kesfet'], ['a' => '1']);

        $this->assertSame('POST', $request['method'], 'method strtoupper (L269)');
        $this->assertSame('kesfet', $request['uri'], 'index.php + ?page= fallback (L272-274)');
        $this->assertSame(['a' => '1'], $request['body'], 'POST body aynen taşınır');
        $this->assertSame(['page' => 'kesfet'], $request['query'], 'GET query aynen taşınır');
    }

    public function testNormalizeRequestExtractsPlainPath(): void
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/albums/9?ref=x',
        ];

        $kernel  = $this->makeKernel(middlewares: []);
        $request = $this->invokePrivate($kernel, 'normalizeRequest', [], [], []);

        $this->assertSame('albums/9', $request['uri'], 'parse_url path + trim (L263-264, L276-278)');
        $this->assertArrayHasKey('headers', $request);
        $this->assertSame($_SERVER, $request['server'], 'normalize global okur; server snapshot\'u $_SERVER\'dır (L272)');
    }

    public function testNormalizeRequestReadsGlobalsNotServerArgument(): void
    {
        // Karakterizasyon: $server parametresi YOK SAYILIR — method/uri/headers
        // her zaman \$_SERVER'dan okunur (L260-272). Bu bir kusur değil,
        // bölme sırasında contract''ın bilinçli korunması gereken davranışıdır.
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/home',
        ];

        $kernel  = $this->makeKernel(middlewares: []);
        $request = $this->invokePrivate($kernel, 'normalizeRequest', ['REQUEST_METHOD' => 'DELETE'], [], []);

        $this->assertSame('GET', $request['method'], 'Argüman array\'i değil $_SERVER okunur');
        $this->assertSame('home', $request['uri']);
    }

    // ------------------------------------------------------------------
    // F. extractHeaders (L281-294)
    // ------------------------------------------------------------------

    public function testExtractHeadersMapsHttpPrefixAndContentType(): void
    {
        $kernel  = $this->makeKernel(middlewares: []);
        $headers = $this->invokePrivate($kernel, 'extractHeaders', [
            'HTTP_X_FOO'       => 'bar',
            'HTTP_ACCEPT'      => 'application/json',
            'CONTENT_TYPE'     => 'application/x-www-form-urlencoded',
            'REQUEST_METHOD'   => 'GET',
            'HTTPS'            => 'on',
        ]);

        $this->assertSame([
            'x-foo'       => 'bar',
            'accept'      => 'application/json',
            'content-type' => 'application/x-www-form-urlencoded',
        ], $headers, 'Yalnız HTTP_ prefix + CONTENT_TYPE header olur (L285-293)');
    }

    // ------------------------------------------------------------------
    // G. runMiddlewareStack — onion sıra + short-circuit (L296-303)
    // ------------------------------------------------------------------

    public function testRunMiddlewareStackRunsMiddlewaresInOnionOrder(): void
    {
        $state = new \stdClass();
        $state->log = [];

        $m1 = new class($state) implements IMiddleware {
            public function __construct(private readonly \stdClass $state) {}
            public function handle(array $request, callable $next): array
            {
                $this->state->log[] = 'm1';
                return $next($request);
            }
        };
        $m2 = new class($state) implements IMiddleware {
            public function __construct(private readonly \stdClass $state) {}
            public function handle(array $request, callable $next): array
            {
                $this->state->log[] = 'm2';
                return $next($request);
            }
        };

        $kernel = $this->makeKernel(middlewares: []);
        $core   = function (array $req) use ($state): array {
            $state->log[] = 'core';
            return ['httpStatus' => 200, 'type' => 'json', 'body' => []];
        };

        $response = $this->invokePrivate($kernel, 'runMiddlewareStack', [$m1, $m2], ['method' => 'GET'], $core);

        $this->assertSame(['m1', 'm2', 'core'], $state->log, 'İlk pipe edilen dış katmandır (soğan)');
        $this->assertSame(200, $response['httpStatus']);
    }

    public function testRunMiddlewareStackAllowsShortCircuit(): void
    {
        $state = new \stdClass();
        $state->coreCalled = false;

        $blocker = new class implements IMiddleware {
            public function handle(array $request, callable $next): array
            {
                // $next çağrılmaz — pipeline durur (ör. RateLimiter 429).
                return ['httpStatus' => 429, 'type' => 'json', 'body' => [], 'halt' => true];
            }
        };

        $kernel = $this->makeKernel(middlewares: []);
        $core   = function (array $req) use ($state): array {
            $state->coreCalled = true;
            return ['httpStatus' => 200, 'type' => 'json', 'body' => []];
        };

        $response = $this->invokePrivate($kernel, 'runMiddlewareStack', [$blocker], [], $core);

        $this->assertFalse($state->coreCalled, 'next çağrılmazsa core çalışmaz');
        $this->assertSame(429, $response['httpStatus'], 'Middleware short-circuit yanıtı aynen döner');
    }

    // ------------------------------------------------------------------
    // H. wrapInHtmlShell — 4 dal (L171-226)
    // ------------------------------------------------------------------

    public function testWrapInHtmlShellPassesRedirectThrough(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $result = $this->invokePrivate($kernel, 'wrapInHtmlShell', [
            'httpStatus' => 302,
            'type'       => 'redirect',
            'body'       => '',
            'headers'    => ['Location' => '/login'],
        ], [], []);

        $this->assertSame(302, $result['httpStatus']);
        $this->assertSame('redirect', $result['type']);
        $this->assertSame('/login', $result['headers']['Location'], 'Redirect aynen geçirilir (L178-180)');
    }

    public function testWrapInHtmlShellConvertsForbiddenRedirectTo302(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $result = $this->invokePrivate($kernel, 'wrapInHtmlShell', [
            'httpStatus' => 403,
            'type'       => 'json',
            'body'       => ['error' => 'forbidden', 'redirect' => '/profile/a b'],
        ], [], []);

        $this->assertSame(302, $result['httpStatus'], '403 + redirect -> 302 (L182-186)');
        $this->assertSame('redirect', $result['type']);
        // buildRedirectUrl: 'profile' + 'a b' segmentleri rawurlencode -> '/profile/a%20b'
        $this->assertSame('/profile/a%20b', $result['headers']['Location'], 'buildRedirectUrl segment rawurlencode (L244-248)');
    }

    public function testWrapInHtmlShellRenders404InsideShell(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $result = $this->invokePrivate($kernel, 'wrapInHtmlShell', [
            'httpStatus' => 404,
            'type'       => 'json',
            'body'       => ['error' => 'not_found', 'route' => 'ghost'],
        ], [], []);

        $this->assertSame(404, $result['httpStatus'], 'Hata durumunda status korunur (L205)');
        $this->assertSame('html', $result['type'], 'Hata artık SHELL içinde render edilir (C-F-14, L188-211)');
        $this->assertStringContainsString('error-page', $result['body']);
        $this->assertStringContainsString('<h1>404', $result['body']);
        $this->assertStringContainsString('<audio id="audio"', $result['body'], 'Shell kalıcı medya elementini içerir');
    }

    public function testWrapInHtmlShellRendersOkResultAsHtmlDocument(): void
    {
        $kernel = $this->makeKernel(middlewares: []);

        $result = $this->invokePrivate($kernel, 'wrapInHtmlShell', RouteResult::ok(
            '<div>HELLO-CM</div>',
            ['title' => 'TestPage'],
            'tok123',
            'home'
        ), [], []);

        $this->assertSame(200, $result['httpStatus'], 'Başarılı sonuç her zaman 200 HTML (L224)');
        $this->assertSame('html', $result['type']);
        $this->assertStringContainsString('HELLO-CM', $result['body'], 'container shell içine gömülür (L219)');
        $this->assertStringContainsString('<title>TestPage', $result['body']);
        $this->assertStringContainsString('id="csrf-global" value="tok123"', $result['body'], 'csrf_token hidden input (L239)');
    }

    public function testWrapInHtmlShellMergesLiveSessionIntoShellPayload(): void
    {
        // C-F-08/P5: CANLI \$_SESSION snapshot''u kazanır (L213-217).
        $_SESSION = ['MM_UserID' => '42'];

        $kernel = $this->makeKernel(middlewares: []);

        $result = $this->invokePrivate($kernel, 'wrapInHtmlShell', RouteResult::ok(
            '<div>x</div>',
            [],
            'tok',
            'home'
        ), [], []);

        // Karakterizasyon: shell user payload'ı düz JSON tırnak ile üretilir
        // (JSON_HEX_QUOT DEĞİL) — canlı $_SESSION['MM_UserID'] -> user.id.
        $this->assertStringContainsString(
            '"id":"42"',
            $result['body'],
            'Live $_SESSION kimliği shell user payload\'ına enjekte edilir (C-F-08)'
        );
    }

    // ------------------------------------------------------------------
    // I. handle() akışı — dispatchAndWrap (emit-harici ayna)
    // ------------------------------------------------------------------

    public function testSpaUnknownPageRouteReturnsJson404WithoutCrash(): void
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(
            page:         'kernel-ghost-page',
            requiresAuth: false,
            title:        'Ghost',
            path:         'kernel-ghost',
        ));

        $kernel = $this->makeKernel(registry: $registry, middlewares: []);

        $response = $this->dispatchAndWrap($kernel, [
            'method'  => 'GET',
            'uri'     => 'kernel-ghost',
            'headers' => [],
        ], isSpa: true);

        // Sayfa dosyası yok + debug false -> notFound (PageRouter.php renderRoute).
        $this->assertSame(404, $response['httpStatus'], 'Bilinen rota + eksik sayfa dosyası -> 404, crash yok');
        $this->assertSame('not_found', $response['body']['error']);
        $this->assertSame('json', $response['type'], 'SPA isteği shell\'e sarılmaz (L116-118)');
    }

    public function testSpaUnauthenticatedPostIsBlockedBeforeHandler(): void
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(
            page:         'profile',
            requiresAuth: true,
            title:        'Profil',
            handler:      'ProfileHandler',
            path:         'profile',
        ));

        $state = new \stdClass();
        $state->called = false;

        $handlers = [
            'profile' => new class($state) {
                public function __construct(private readonly \stdClass $state) {}
                public function handle(array $request): array
                {
                    $this->state->called = true;
                    return ['handled' => true];
                }
            },
        ];

        $kernel = $this->makeKernel(registry: $registry, middlewares: [], handlers: $handlers);

        $response = $this->dispatchAndWrap($kernel, [
            'method'  => 'POST',
            'uri'     => 'profile',
            'headers' => [],
        ], isSpa: true);

        $this->assertFalse($state->called, 'Kimliksiz istek handler\'a ulaşmamalı (guard L52 -> handler L57 öncesi)');
        $this->assertSame(403, $response['httpStatus']);
        $this->assertArrayHasKey('redirect', $response['body']);
    }

    public function testSpaAuthenticatedPostReachesHandlerWithRouteMeta(): void
    {
        $_SESSION = ['MM_UserID' => '42'];

        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(
            page:         'profile',
            requiresAuth: true,
            title:        'Profil',
            meta:         ['required_fields' => ['name' => 'required']],
            handler:      'ProfileHandler',
            path:         'profile',
        ));

        $state = new \stdClass();
        $state->capturedRouteMeta = null;

        $handlers = [
            'profile' => new class($state) {
                public function __construct(private readonly \stdClass $state) {}
                public function handle(array $request): array
                {
                    // ValidationMiddleware (#10) bu anahtarla çalışır — handler
                    // üzerinden producer contract''ı da doğrulanır (B-F-13).
                    $this->state->capturedRouteMeta = $request['_route_meta'] ?? null;
                    return ['handled' => true];
                }
            },
        ];

        $kernel = $this->makeKernel(registry: $registry, middlewares: [], handlers: $handlers);

        $response = $this->dispatchAndWrap($kernel, [
            'method'  => 'POST',
            'uri'     => 'profile',
            'headers' => [],
        ], isSpa: true);

        $this->assertTrue((bool)($response['handled'] ?? false), 'Kimliği doğrulanmış istek handler\'a ulaşır');
        $this->assertIsArray($state->capturedRouteMeta, 'route_meta pipeline BAŞINDA set edilir (L104)');
        $this->assertSame(['name' => 'required'], $state->capturedRouteMeta['required_fields']);
    }

    public function testClassicUnauthenticatedRequestIsWrappedToLoginRedirect302(): void
    {
        $registry = new RouteRegistry();
        $registry->register(new SpaRoute(
            page:         'profile',
            requiresAuth: true,
            title:        'Profil',
            path:         'profile',
        ));

        $kernel = $this->makeKernel(registry: $registry, middlewares: []);

        $response = $this->dispatchAndWrap($kernel, [
            'method'  => 'GET',
            'uri'     => 'profile',
            'headers' => [],
        ], isSpa: false);

        $this->assertSame(302, $response['httpStatus'], 'Klasik istek guard sonucu 302\'ye sarılır (L178-180)');
        $this->assertSame('redirect', $response['type']);
        $this->assertStringContainsString('login', $response['headers']['Location'], 'Auth login URL\'ine yönlendirir');
    }
}
