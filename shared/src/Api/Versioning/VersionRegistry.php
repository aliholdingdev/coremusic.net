<?php
declare(strict_types=1);

/**
 * API Version Registry for route mapping.
 *
 * @file VersionRegistry.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Versioning;

/**
 * Registry for API version route mappings.
 */
final class VersionRegistry
{
    private array $routes = [];

    /**
     * Register a route for a specific API version.
     */
    public function registerRoute(
        ApiVersion $version,
        string $service,
        string $pattern,
        callable $handler
    ): void {
        $this->routes[$version->value][] = [
            'service' => $service,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    /**
     * Match a route against registered routes.
     */
    public function matchRoute(ApiVersion $version, string $uri): ?array
    {
        $versionRoutes = $this->routes[$version->value] ?? [];
        
        foreach ($versionRoutes as $route) {
            if ($this->matchPattern($route['pattern'], $uri)) {
                return [
                    'handler' => $route['handler'],
                    'params' => $this->extractParams($route['pattern'], $uri),
                ];
            }
        }
        
        return null;
    }

    /**
     * Match URI against a route pattern.
     */
    private function matchPattern(string $pattern, string $uri): bool
    {
        // Convert route pattern to regex
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        return (bool) preg_match($regex, $uri);
    }

    /**
     * Extract parameters from URI based on pattern.
     */
    private function extractParams(string $pattern, string $uri): array
    {
        $params = [];
        
        // Extract parameter names from pattern
        if (preg_match_all('#\{(\w+)\}#', $pattern, $matches)) {
            // Convert pattern to regex with named groups
            $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            
            if (preg_match($regex, $uri, $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
            }
        }
        
        return $params;
    }

    /**
     * Pre-register standard v1 service routes.
     */
    public function registerStandardV1Routes(): void
    {
        $services = ['auth', 'user', 'music', 'playlist', 'media', 'download'];
        
        foreach ($services as $service) {
            $this->registerRoute(
                ApiVersion::V1,
                $service,
                "/api/v1/{$service}",
                function () use ($service) {
                    return ["service" => $service, "action" => "index"];
                }
            );
            
            $this->registerRoute(
                ApiVersion::V1,
                $service,
                "/api/v1/{$service}/{id}",
                function () use ($service) {
                    return ["service" => $service, "action" => "show"];
                }
            );
        }
    }
}
