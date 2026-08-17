<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

use CoreMusic\Config\AuthRouteConfig;

final class AuthGuard
{
    public function __construct(
        private readonly PageRouterHelper $authHelper,
        private readonly AuthUrlBuilder   $urlBuilder,
        private readonly bool $skipAuthRedirect = false,
    ) {}

    public function check(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
    {
        return $this->checkAuthRequired($uri, $route, $isSpaRequest)
            ?? $this->checkRole($uri, $route)
            ?? $this->checkPermission($uri, $route)
            ?? $this->checkAuthRedirectRoute($uri, $isSpaRequest)
            ?? $this->checkAuthenticatedUserOnAuthPage($uri, $isSpaRequest)
            ?? $this->checkLogout($uri, $isSpaRequest)
            ?? null;
    }

    private function checkAuthRequired(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
    {
        if (!$route->requiresAuth || $this->authHelper->checkAuthenticated()) {
            return null;
        }
        return $this->urlBuilder->redirectAuth('login', '/' . $uri, $isSpaRequest);
    }

    private function checkRole(string $uri, SpaRoute $route): ?array
    {
        if ($route->requiredRole === null || $this->authHelper->checkRole($route->requiredRole)) {
            return null;
        }
        return RouteResult::forbidden('/' . $uri);
    }

    private function checkPermission(string $uri, SpaRoute $route): ?array
    {
        if ($route->requiredPermission === null || $this->authHelper->checkPermission($route->requiredPermission)) {
            return null;
        }
        return RouteResult::forbidden('/' . $uri);
    }

    private function checkAuthRedirectRoute(string $uri, bool $isSpaRequest): ?array
    {
        if ($this->skipAuthRedirect
            || !AuthRouteConfig::isAuthRedirectRoute($uri)
            || $this->authHelper->checkAuthenticated()
        ) {
            return null;
        }
        $path = $uri === 'logout' ? 'logout' : $uri;
        return $this->urlBuilder->redirectAuth($path, null, $isSpaRequest);
    }

    private function checkAuthenticatedUserOnAuthPage(string $uri, bool $isSpaRequest): ?array
    {
        if (!$this->authHelper->checkAuthenticated()
            || !AuthRouteConfig::isAuthRedirectRoute($uri)
            || $uri === 'logout'
        ) {
            return null;
        }
        if ($isSpaRequest) {
            return RouteResult::forbidden('/home');
        }
        return RouteResult::redirect('/home');
    }

    private function checkLogout(string $uri, bool $isSpaRequest): ?array
    {
        if ($uri !== 'logout' || !$this->authHelper->checkAuthenticated()) {
            return null;
        }
        return $this->urlBuilder->redirectAuth('logout', null, $isSpaRequest);
    }
}
