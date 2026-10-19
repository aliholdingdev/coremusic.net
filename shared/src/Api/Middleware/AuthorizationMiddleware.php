<?php
declare(strict_types=1);

/**
 * Authorization Middleware for API requests.
 *
 * @file AuthorizationMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

use CoreMusic\Api\ApiResponse;
use CoreMusic\Api\Routing\RouteTable;

/**
 * Authorization Middleware for RBAC validation.
 */
final class AuthorizationMiddleware
{
    /**
     * Process authorization for API requests.
     */
    public function __invoke(array $request, callable $next): array
    {
        // 404 / 405: handler çalışmayacak → RBAC uygulanmaz, yanıt Gateway'de
        // üretilir (bilinmeyen yol 404, method uyuşmazlığı 405 + Allow).
        $routeStatus = $request['_route_status'] ?? null;
        if ($routeStatus === RouteTable::STATUS_NOT_FOUND
            || $routeStatus === RouteTable::STATUS_METHOD_NOT_ALLOWED
        ) {
            return $next($request);
        }

        // Public route'larda auth/authorization zorunlu değildir; aksi halde
        // login/register uçları 401 ile kilitlenir.
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }

        // Check if user is authenticated
        $authUser = $request['_auth_user'] ?? null;
        if ($authUser === null) {
            return ApiResponse::error(
                'UNAUTHORIZED',
                'Authentication required',
                401
            );
        }

        // Get route configuration
        $route = $request['_route'] ?? null;
        if (!is_array($route)) {
            // Eşleşmeyen yol (404) veya method uyuşmazlığı (405): route
            // yapılandırması yok → RBAC uygulanmaz, yanıt Gateway'de üretilir.
            return $next($request);
        }

        // Check required role
        $requiredRole = $route['requiredRole'] ?? null;
        if ($requiredRole !== null) {
            $userRoles = $authUser['roles'] ?? [];
            if (!in_array($requiredRole, $userRoles, true)) {
                return ApiResponse::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    403
                );
            }
        }

        // Check required permission
        $requiredPermission = $route['requiredPermission'] ?? null;
        if ($requiredPermission !== null) {
            $userPermissions = $authUser['permissions'] ?? [];
            if (!in_array($requiredPermission, $userPermissions, true)) {
                return ApiResponse::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    403
                );
            }
        }

        return $next($request);
    }

    /**
     * Public route kontrolü — pipeline'a aktarılan `_route['public']`
     * (RouteTable) + `AuthenticationMiddleware::PUBLIC_ROUTE_PATTERNS`
     * fallback'i.
     */
    private function isPublicRoute(array $request): bool
    {
        $route = $request['_route'] ?? null;
        if (is_array($route) && !empty($route['public'])) {
            return true;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        foreach (AuthenticationMiddleware::PUBLIC_ROUTE_PATTERNS as $pattern) {
            if (str_starts_with($uri, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
