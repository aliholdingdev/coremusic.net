<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class RouteRegistry
{
    /** @var array<string, SpaRoute> */
    private array $routes = [];

    public function register(SpaRoute $route): void
    {
        $this->routes[trim($route->path, '/')] = $route;
    }

    public function loadFromFile(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new \RuntimeException("Route config dosyası bulunamadı: {$filePath}");
        }

        $routes = include $filePath;

        if (!is_array($routes)) {
            throw new \RuntimeException('Route config bir array döndürmelidir.');
        }

        foreach ($routes as $key => $route) {
            if (!$route instanceof SpaRoute) {
                continue;
            }
            $this->routes[trim((string)$key, '/')] = $route;
        }
    }

    public function resolve(string $uri): ?SpaRoute
    {
        $normalized = trim($uri, '/');

        if ($normalized === '') {
            return $this->routes['home'] ?? null;
        }

        if (isset($this->routes[$normalized])) {
            return $this->routes[$normalized];
        }

        foreach ($this->routes as $pattern => $route) {
            if ($this->matchesPattern($pattern, $normalized)) {
                return $route;
            }
        }

        return null;
    }

    public function getRegisteredKeys(): array
    {
        return array_keys($this->routes);
    }

    public function getProtectedRouteKeys(): array
    {
        $protected = [];
        foreach ($this->routes as $key => $route) {
            if ($route->requiresAuth) {
                $protected[] = $key;
            }
        }
        return $protected;
    }

    private function matchesPattern(string $pattern, string $uri): bool
    {
        if (!str_contains($pattern, '{')) {
            return false;
        }

        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '[^/]+', $pattern);
        $regex = '#^' . $regex . '$#';

        return (bool)preg_match($regex, $uri);
    }
}
