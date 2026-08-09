---
type: adr
category: routing
title: "ADR-057: Router & Middleware Implementation Plan"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-057: Router & Middleware Implementation Plan

**Status:** Active
**Kategorisi:** Routing + Security
**İlgili Agent:** [[.agents/backend-architect]], [[.agents/security-engineer]]
**İlgili Division:** Software Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformunun **Enterprise Router** ve **Middleware Pipeline** modüllerinin detaylı implementasyon planını tanımlar. Her dosyanın amacı, içeriği, bağımlılıkları ve implementasyon sırası belirlenmiştir.

---

## 2. Bağlam

### 2.1 İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-051 | Platform Rewrite — proje yapısı |
| ADR-052 | Hybrid Auth Architecture — middlewaregereksinimleri |
| ADR-053 | Enterprise Router Architecture — router tasarımı |
| ADR-054 | Enterprise Composer Stack — paket listesi |
| ADR-055 | Project Structure Plan — dosya yapısı |
| ADR-056 | Auth Module Implementation — auth middleware |

### 2.2 Middleware Pipeline (Frozen — ADR-010/011/012/013/022)

```
Request → SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf → Controller
```

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | SessionManager | Session başlatır, CSP nonce üretir | 3600s idle |
| 2 | BypassAuth | Test bypass (`?_bypass=1`), prod'da devre dışı | — |
| 3 | RateLimiter | APCu tabanlı, 60 req/60s | 60s |
| 4 | Auth | Auth bilgisi inject, RBAC kontrolü | — |
| 5 | SecurityHeaders | CSP strict-dynamic, X-Frame-Options, HSTS | — |
| 6 | Csrf | `csrf_token` doğrulama (POST/PUT/DELETE) | — |

---

## 3. Karar — Dosya Bazlı Implementasyon

### 3.1 Router Layer

#### `shared/src/Router/Router.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router;

use CoreMusic\Router\Cache\RouteCache;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher\GroupCountBased;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Router
{
    private ?Dispatcher $dispatcher = null;
    private array $routeList = [];
    private bool $isProduction;

    public function __construct(
        private readonly RouteCollector $routeCollector,
        private readonly RouteCache $routeCache,
        bool $isProduction = false,
    ) {
        $this->isProduction = $isProduction;
    }

    public function get(string $path, array $handler): RouteDefinition
    {
        $route = new RouteDefinition($path, $handler, ['GET']);
        $this->routeCollector->addRoute('GET', $path, $handler);
        $this->routeList[] = $route;

        return $route;
    }

    public function post(string $path, array $handler): RouteDefinition
    {
        $route = new RouteDefinition($path, $handler, ['POST']);
        $this->routeCollector->addRoute('POST', $path, $handler);
        $this->routeList[] = $route;

        return $route;
    }

    public function put(string $path, array $handler): RouteDefinition
    {
        $route = new RouteDefinition($path, $handler, ['PUT']);
        $this->routeCollector->addRoute('PUT', $path, $handler);
        $this->routeList[] = $route;

        return $route;
    }

    public function delete(string $path, array $handler): RouteDefinition
    {
        $route = new RouteDefinition($path, $handler, ['DELETE']);
        $this->routeCollector->addRoute('DELETE', $path, $handler);
        $this->routeList[] = $route;

        return $route;
    }

    public function group(string $prefix, callable $callback): GroupDefinition
    {
        $group = new GroupDefinition($prefix, $this);
        $callback($group);

        return $group;
    }

    public function dispatch(ServerRequestInterface $request): array
    {
        $httpMethod = $request->getMethod();
        $uri = $request->getUri()->getPath();

        // Normalization
        $uri = rtrim($uri, '/') ?: '/';

        return $this->getDispatcher()->dispatch($httpMethod, $uri);
    }

    private function getDispatcher(): Dispatcher
    {
        if ($this->dispatcher !== null) {
            return $this->dispatcher;
        }

        // Production: use cache
        if ($this->isProduction) {
            $cachedRoutes = $this->routeCache->getRoutes();
            if ($cachedRoutes !== null) {
                $this->dispatcher = new GroupCountBased($cachedRoutes);
                return $this->dispatcher;
            }
        }

        // Build routes
        $this->dispatcher = new GroupCountBased(
            $this->routeCollector->getData()
        );

        // Cache in production
        if ($this->isProduction) {
            $this->routeCache->setRoutes($this->routeCollector->getData());
        }

        return $this->dispatcher;
    }

    public function getRouteList(): array
    {
        return $this->routeList;
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** nikic/fast-route
**Çıktı:** Router class (120+ satır)

---

#### `shared/src/Router/RouteDefinition.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router;

final class RouteDefinition
{
    private array $middleware = [];
    private ?string $name = null;
    private array $conditions = [];

    public function __construct(
        private readonly string $path,
        private readonly array $handler,
        private readonly array $methods,
    ) {
    }

    public function middleware(string $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function where(string $key, string $pattern): self
    {
        $this->conditions[$key] = $pattern;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** Yok
**Çıktı:** RouteDefinition class

---

#### `shared/src/Router/GroupDefinition.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router;

final class GroupDefinition
{
    private array $middleware = [];

    public function __construct(
        private readonly string $prefix,
        private readonly Router $router,
    ) {
    }

    public function get(string $path, array $handler): RouteDefinition
    {
        return $this->router->get($this->prefix . $path, $handler);
    }

    public function post(string $path, array $handler): RouteDefinition
    {
        return $this->router->post($this->prefix . $path, $handler);
    }

    public function put(string $path, array $handler): RouteDefinition
    {
        return $this->router->put($this->prefix . $path, $handler);
    }

    public function delete(string $path, array $handler): RouteDefinition
    {
        return $this->router->delete($this->prefix . $path, $handler);
    }

    public function middleware(string $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** Router.php
**Çıktı:** GroupDefinition class

---

#### `shared/src/Router/Attributes/Route.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Route
{
    public function __construct(
        public readonly string $path,
        public readonly array $methods = ['GET'],
        public readonly ?string $name = null,
    ) {
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** PHP 8 Attributes
**Çıktı:** Route attribute

---

#### `shared/src/Router/Attributes/Middleware.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Middleware
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** PHP 8 Attributes
**Çıktı:** Middleware attribute

---

#### `shared/src/Router/Attributes/Guard.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Guard
{
    public function __construct(
        public readonly string $role,
        public readonly string $redirect = '/login',
    ) {
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** PHP 8 Attributes
**Çıktı:** Guard attribute (RBAC)

---

#### `shared/src/Router/Cache/RouteCache.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router\Cache;

final class RouteCache
{
    private string $cacheDir;
    private ?array $cachedRoutes = null;

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function getRoutes(): ?array
    {
        if ($this->cachedRoutes !== null) {
            return $this->cachedRoutes;
        }

        $cacheFile = $this->cacheDir . '/routes.cache';

        if (file_exists($cacheFile)) {
            $this->cachedRoutes = require $cacheFile;
            return $this->cachedRoutes;
        }

        return null;
    }

    public function setRoutes(array $routes): void
    {
        $cacheFile = $this->cacheDir . '/routes.cache';
        $content = '<?php return ' . var_export($routes, true) . ';';
        file_put_contents($cacheFile, $content);
    }

    public function clear(): void
    {
        $cacheFile = $this->cacheDir . '/routes.cache';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
        $this->cachedRoutes = null;
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** Yok
**Çıktı:** Route cache

---

### 3.2 HTTP Layer

#### `shared/src/Http/HttpKernel.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Http;

use CoreMusic\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use CoreMusic\Http\Response\ResponseEmitter;

final class HttpKernel implements RequestHandlerInterface
{
    public function __construct(
        private readonly Router $router,
        private readonly array $middlewarePipeline,
        private readonly ResponseEmitter $responseEmitter,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Dispatch route
        $routeResult = $this->router->dispatch($request);

        // 2. Build middleware pipeline
        $handler = $this->createHandler($routeResult);

        // 3. Execute middleware pipeline
        return $handler->handle($request);
    }

    private function createHandler(array $routeResult): RequestHandlerInterface
    {
        // Build middleware chain from route result
        // Return final handler
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** Router.php, PSR-15
**Çıktı:** HTTP Kernel

---

#### `shared/src/Http/Response/ResponseEmitter.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Http\Response;

use Psr\Http\Message\ResponseInterface;

final class ResponseEmitter
{
    public function emit(ResponseInterface $response): void
    {
        // Send status line
        header(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));

        // Send headers
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        // Send body
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }

        echo $body;
    }
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** PSR-7
**Çıktı:** Response emitter

---

### 3.3 Middleware Layer (L1 — Security)

#### `shared/src/Security/Middleware/SessionManagerMiddleware.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use CoreMusic\Security\Service\CspNonceGenerator;
use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SessionManagerMiddleware implements MiddlewareInterface
{
    private const SESSION_COOKIE = 'COREMUSIC_SESS';
    private const SESSION_LIFETIME = 3600;
    private const IDLE_TIMEOUT = 1800;

    public function __construct(
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly CspNonceGenerator $cspNonceGenerator,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'name' => self::SESSION_COOKIE,
                'cookie_lifetime' => self::SESSION_LIFETIME,
                'cookie_httponly' => true,
                'cookie_secure' => true,
                'cookie_samesite' => 'Lax',
                'cookie_domain' => '.coremusic.net',
            ]);
        }

        // 2. Generate CSP nonce
        $nonce = $this->cspNonceGenerator->generate();

        // 3. Validate session if exists
        $sessionId = $_SESSION['session_id'] ?? null;
        if ($sessionId !== null) {
            $session = $this->sessionRepository->findById($sessionId);

            if ($session === null || $session->isExpired() || $session->hasTimedOut(self::IDLE_TIMEOUT)) {
                // Destroy invalid session
                session_destroy();
                $_SESSION = [];
            } else {
                // Update last activity
                $this->sessionRepository->updateLastActivity($sessionId);
            }
        }

        // 4. Inject into request
        $request = $request
            ->withAttribute('session_id', $sessionId)
            ->withAttribute('csp_nonce', $nonce)
            ->withAttribute('is_authenticated', isset($_SESSION['user_id']));

        return $handler->handle($request);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** SessionRepositoryInterface.php
**Çıktı:** Session manager middleware (80+ satır)

---

#### `shared/src/Security/Middleware/BypassAuthMiddleware.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BypassAuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Only in development
        if ($_ENV['APP_ENV'] !== 'development') {
            return $handler->handle($request);
        }

        // Check bypass parameter
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['_bypass']) && $queryParams['_bypass'] === '1') {
            // Set bypass attributes
            $request = $request
                ->withAttribute('is_authenticated', true)
                ->withAttribute('user_id', 'bypass-user')
                ->withAttribute('user_roles', ['super_admin']);
        }

        return $handler->handle($request);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** ADR-008 (BypassAuth)
**Çıktı:** Bypass auth middleware

---

#### `shared/src/Security/Middleware/RateLimiterMiddleware.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use CoreMusic\Security\Service\RateLimiter;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RateLimiterMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RateLimiter $rateLimiter,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ipAddress = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'rate:' . $ipAddress;

        $limit = $this->rateLimiter->consume($key, 60, 60); // 60 requests per 60 seconds

        if (!$limit->isAccepted()) {
            $retryAfter = $limit->getRetryAfter()->getTimestamp() - time();

            return new \Nyholm\Psr7\Response(
                429,
                [
                    'Content-Type' => 'application/json',
                    'Retry-After' => (string) $retryAfter,
                    'X-RateLimit-Limit' => '60',
                    'X-RateLimit-Remaining' => '0',
                    'X-RateLimit-Reset' => (string) $limit->getRetryAfter()->getTimestamp(),
                ],
                json_encode([
                    'error' => 'Too many requests',
                    'retry_after' => $retryAfter,
                ])
            );
        }

        // Add rate limit headers
        $response = $handler->handle($request);

        return $response
            ->withHeader('X-RateLimit-Limit', '60')
            ->withHeader('X-RateLimit-Remaining', (string) $limit->getRemainingTokens());
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** RateLimiter.php
**Çıktı:** Rate limiter middleware

---

#### `shared/src/Security/Middleware/AuthMiddleware.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use CoreMusic\Auth\Infrastructure\Security\JwtTokenManager;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JwtTokenManager $jwtManager,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Check if already authenticated (from SessionManager)
        if ($request->getAttribute('is_authenticated') === true) {
            return $handler->handle($request);
        }

        // 2. Try JWT from Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        if (str_starts_with($authHeader, 'Bearer ')) {
            $jwt = substr($authHeader, 7);

            try {
                $payload = $this->jwtManager->decode($jwt);

                // Validate token type
                if ($payload['type'] !== 'access') {
                    return $this->unauthorized($request);
                }

                // Check if token is revoked
                // ...

                // Get user
                $user = $this->userRepository->findById($payload['sub']);
                if ($user === null || !$user->isActive()) {
                    return $this->unauthorized($request);
                }

                // Inject user into request
                $request = $request
                    ->withAttribute('is_authenticated', true)
                    ->withAttribute('user_id', $user->getId())
                    ->withAttribute('user_roles', $user->getRoles())
                    ->withAttribute('user', $user);

                return $handler->handle($request);
            } catch (\Exception $e) {
                return $this->unauthorized($request);
            }
        }

        // 3. Not authenticated
        return $this->unauthorized($request);
    }

    private function unauthorized(ServerRequestInterface $request): ResponseInterface
    {
        // Check if this is an API request
        $uri = $request->getUri()->getPath();
        if (str_starts_with($uri, '/api/')) {
            return new \Nyholm\Psr7\Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'Unauthorized'])
            );
        }

        // Redirect to login
        $returnUrl = urlencode($request->getUri()->getPath());
        return new \Nyholm\Psr7\Response(
            302,
            ['Location' => "/login?return={$returnUrl}"]
        );
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** JwtTokenManager.php, UserRepositoryInterface.php
**Çıktı:** Auth middleware (100+ satır)

---

#### `shared/src/Security/Middleware/SecurityHeadersMiddleware.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use CoreMusic\Security\Service\SecurityHeaderService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SecurityHeaderService $headerService,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $this->headerService->addHeaders($response);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** SecurityHeaderService.php
**Çıktı:** Security headers middleware

---

#### `shared/src/Security/Middleware/CsrfMiddleware.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CsrfMiddleware implements MiddlewareInterface
{
    private const TOKEN_ID = 'csrf_token';
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function __construct(
        private readonly CsrfTokenManager $csrfManager,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();

        // Skip CSRF for safe methods
        if (in_array($method, self::SAFE_METHODS, true)) {
            return $handler->handle($request);
        }

        // Get token from request
        $body = $request->getParsedBody();
        $token = $body[self::TOKEN_ID]
            ?? $request->getHeaderLine('X-CSRF-Token');

        // Validate
        if (!$this->csrfManager->isTokenValid(self::TOKEN_ID, $token)) {
            return new \Nyholm\Psr7\Response(
                403,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'Invalid CSRF token'])
            );
        }

        return $handler->handle($request);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** symfony/security-csrf
**Çıktı:** CSRF middleware

---

### 3.4 Security Services

#### `shared/src/Security/Service/CspNonceGenerator.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

final class CspNonceGenerator
{
    public function generate(): string
    {
        return base64_encode(random_bytes(32));
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** Yok
**Çıktı:** CSP nonce generator

---

#### `shared/src/Security/Service/RateLimiter.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\Contracts\Cache\CacheInterface;

final class RateLimiter
{
    public function __construct(
        private readonly RateLimiterFactory $factory,
    ) {
    }

    public function consume(string $key, int $limit, int $intervalSeconds): object
    {
        $limiter = $this->factory->create($key, $limit, $intervalSeconds . '-seconds');
        return $limiter->consume(1);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** symfony/rate-limiter
**Çıktı:** Rate limiter service

---

#### `shared/src/Security/Service/SecurityHeaderService.php`

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

use Psr\Http\Message\ResponseInterface;

final class SecurityHeaderService
{
    public function __construct(
        private readonly bool $isProduction = false,
        private readonly string $cspNonce = '',
    ) {
    }

    public function addHeaders(ResponseInterface $response): ResponseInterface
    {
        $headers = [
            'X-Content-Type-Options' => ['nosniff'],
            'X-Frame-Options' => ['DENY'],
            'X-XSS-Protection' => ['1; mode=block'],
            'Referrer-Policy' => ['strict-origin-when-cross-origin'],
            'Permissions-Policy' => ['camera=(), microphone=(), geolocation=()'],
        ];

        // CSP
        $csp = $this->buildCspDirective();
        $headers['Content-Security-Policy'] = [$csp];

        // HSTS (production only)
        if ($this->isProduction) {
            $headers['Strict-Transport-Security'] = ['max-age=31536000; includeSubDomains'];
        }

        foreach ($headers as $name => $values) {
            $response = $response->withHeader($name, $values);
        }

        return $response;
    }

    private function buildCspDirective(): string
    {
        $nonce = $this->cspNonce;

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'strict-dynamic'",
            "style-src 'self' 'nonce-{$nonce}'",
            "img-src 'self' data: https:",
            "font-src 'self'",
            "connect-src 'self'",
            "media-src 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }
}
```

**Sorumlu:** Security Engineer
**Ön Koşul:** ADR-012 (CSP)
**Çıktı:** Security header service

---

## 4. Implementasyon Sırası

| # | Dosya | Sorumlu | Tahmini | Bağımlılık |
|---|-------|---------|---------|------------|
| 1 | Router/Router.php | Backend Architect | 60 dk | nikic/fast-route |
| 2 | Router/RouteDefinition.php | Backend Architect | 15 dk | — |
| 3 | Router/GroupDefinition.php | Backend Architect | 20 dk | #1 |
| 4 | Router/Attributes/Route.php | Backend Architect | 5 dk | PHP 8 |
| 5 | Router/Attributes/Middleware.php | Backend Architect | 5 dk | PHP 8 |
| 6 | Router/Attributes/Guard.php | Backend Architect | 5 dk | PHP 8 |
| 7 | Router/Cache/RouteCache.php | Backend Architect | 20 dk | — |
| 8 | Http/HttpKernel.php | Backend Architect | 45 dk | #1, #13 |
| 9 | Http/Response/ResponseEmitter.php | Backend Architect | 15 dk | PSR-7 |
| 10 | Security/Service/CspNonceGenerator.php | Security Engineer | 5 dk | — |
| 11 | Security/Service/SecurityHeaderService.php | Security Engineer | 25 dk | ADR-012 |
| 12 | Security/Service/RateLimiter.php | Security Engineer | 15 dk | symfony/rate-limiter |
| 13 | Security/Middleware/SessionManagerMiddleware.php | Security Engineer | 40 dk | #10 |
| 14 | Security/Middleware/BypassAuthMiddleware.php | Security Engineer | 10 dk | ADR-008 |
| 15 | Security/Middleware/RateLimiterMiddleware.php | Security Engineer | 20 dk | #12 |
| 16 | Security/Middleware/AuthMiddleware.php | Security Engineer | 45 dk | ADR-052 |
| 17 | Security/Middleware/SecurityHeadersMiddleware.php | Security Engineer | 10 dk | #11 |
| 18 | Security/Middleware/CsrfMiddleware.php | Security Engineer | 15 dk | symfony/security-csrf |
| 19 | config/services.php | Backend Architect | 30 dk | #1-#18 |
| 20 | config/middleware.php | Backend Architect | 15 dk | #13-#18 |
| 21 | config/routes.php | Backend Architect | 30 dk | #1 |
| 22 | Tests | QA Engineer | 90 dk | #1-#21 |

**Toplam Tahmini:** ~8.5 saat

---

## 5. Hard Guardrails

| # | Guardrail | Uygulama |
|---|-----------|----------|
| G1 | Middleware sırası frozen | SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf |
| G2 | PSR-15 Middleware | Tüm middleware PSR-15 |
| G3 | Named routes | URL oluşturma için zorunlu |
| G4 | Route cache | Production'da zorunlu |
| G5 | DI Container | Controller resolver zorunlu |
| G6 | csrf_token key | Değiştirilmez |
| G7 | ORM yasak | Sadece PDO prepared statement |
| G8 | Framework yasak | Sadece Vanilla JS + PHP native |

---

## 6. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| ADR-010 | CSRF koruma | csrf_token |
| ADR-011 | Session yönetimi | Cookie, timeout |
| ADR-012 | CSP nonce | Content Security Policy |
| ADR-013 | Rate limiting | APCu, 60 req/60s |
| ADR-008 | BypassAuth | Test bypass |
| ADR-051 | Platform Rewrite | Sıfırdan yazım |
| ADR-052 | Hybrid Auth | Auth middleware |
| ADR-053 | Enterprise Router | Router tasarımı |
| ADR-054 | Composer Stack | Paket listesi |
| ADR-055 | Project Structure | Dosya yapısı |
| ADR-056 | Auth Module | Auth implementasyonu |

---

## 7. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 1.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-09 |
| Sections | 7 |
| Router Dosya | 7 |
| HTTP Dosya | 2 |
| Middleware Dosya | 6 |
| Service Dosya | 3 |
| Config Dosya | 3 |
| Implementasyon Adım | 22 |
| Tahmini Süre | ~8.5 saat |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
