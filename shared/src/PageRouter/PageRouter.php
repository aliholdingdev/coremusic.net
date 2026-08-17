<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\Cache\PageCacheInterface;

final class PageRouter
{
    private array $handlers = [];

    public function __construct(
        private readonly RouteRegistry        $registry,
        private readonly ConfigManager        $config,
        private readonly DomainConfig         $domainConfig,
        private readonly PageRouterHelper     $authHelper,
        private readonly ?AuthGuard           $authGuard = null,
        private readonly ?AuthUrlBuilder      $urlBuilder = null,
        private readonly ?PageCacheInterface  $pageCache = null,
        array                                 $handlers = [],
    ) {
        $this->handlers = $handlers;
    }

    public function dispatch(array $request, string $csrfToken = '', bool $isSpaRequest = false): array
    {
        $uri = $this->resolveUri($request);

        $guardResult = $this->runGuards($uri, $request, $isSpaRequest);
        if ($guardResult !== null) {
            return $guardResult;
        }

        $route = $this->registry->resolve($uri);

        if ($route === null) {
            return RouteResult::notFound($uri);
        }

        if (($request['method'] ?? 'GET') === 'POST' && $route->handler !== null) {
            $handler = $this->handlers[$uri] ?? null;
            if ($handler !== null && method_exists($handler, 'handle')) {
                return $handler->handle($request);
            }
        }

        $authResult = $this->authGuard?->check($uri, $route, $isSpaRequest);
        if ($authResult !== null) {
            return $authResult;
        }

        return $this->renderRoute($uri, $route, $csrfToken);
    }

    private function runGuards(string $uri, array $request, bool $isSpaRequest): ?array
    {
        if ($uri === '') {
            return $this->handleRootUrl($isSpaRequest);
        }
        return null;
    }

    private function handleRootUrl(bool $isSpaRequest): array
    {
        if ($this->authHelper->checkAuthenticated()) {
            $target = '/home';
        } else {
            $target = $this->urlBuilder->buildAuthRedirect('login')['url'];
            header('X-Auth-Required: true');
            header('X-Auth-Status: unauthenticated');
        }
        if ($isSpaRequest) {
            return RouteResult::forbidden($target);
        }
        return RouteResult::redirect($target);
    }

    private function renderRoute(string $uri, SpaRoute $route, string $csrfToken): array
    {
        $pageFile = $this->resolvePageFile($route);

        if (!is_file($pageFile)) {
            if ($this->isDebug()) {
                $container = $this->renderPlaceholder($route);
                return RouteResult::ok($container, $this->buildMeta($route), $csrfToken, $uri);
            }
            return RouteResult::notFound($uri);
        }

        $container = $this->renderPage($pageFile, $csrfToken);
        return RouteResult::ok($container, $this->buildMeta($route), $csrfToken, $uri);
    }

    private function resolveUri(array $request): string
    {
        foreach (['uri', 'page'] as $key) {
            if (!empty($request[$key]) && is_string($request[$key])) {
                $val = trim($request[$key], '/');
                if (str_contains($val, '?')) {
                    $val = explode('?', $val)[0];
                }
                $val = trim($val);
                if ($val !== '') {
                    return $val;
                }
            }
        }
        return '';
    }

    private function resolvePageFile(SpaRoute $route): string
    {
        $base = defined('PAGES_PATH') ? (string)PAGES_PATH : (__DIR__ . '/../../../pages');
        return rtrim($base, '/\\') . '/' . ltrim($route->page, '/') . '.php';
    }

    private function renderPage(string $pageFile, string $csrfToken = ''): string
    {
        ob_start();
        try {
            $config       = $this->config;
            $domainConfig = $this->domainConfig;
            $isMob        = (bool)$this->config->get('device.isMobile', false);
            $csrfField    = $csrfToken !== ''
                ? '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . '">'
                : '';
            include $pageFile;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string)ob_get_clean();
    }

    private function renderPlaceholder(SpaRoute $route): string
    {
        $title = htmlspecialchars($route->title, ENT_QUOTES, 'UTF-8');
        $page  = htmlspecialchars($route->page, ENT_QUOTES, 'UTF-8');
        return '<div class="page-placeholder"><h1>' . $title . '</h1><p>Sayfa henüz hazır değil: <strong>' . $page . '</strong></p></div>';
    }

    private function buildMeta(SpaRoute $route): array
    {
        return array_merge($route->meta, [
            'title'      => $route->title,
            'auth'       => $route->requiresAuth,
            'role'       => $route->requiredRole,
            'permission' => $route->requiredPermission,
            'cacheable'  => $route->cacheable,
        ]);
    }

    private function isDebug(): bool
    {
        return (bool)$this->config->get('app.debug', false);
    }
}
