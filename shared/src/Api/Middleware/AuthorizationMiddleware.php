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
        // Public route'larda auth/authorization zorunlu değildir; aksi halde
        // login/register uçları 401 ile kilitlenir (AuthenticationMiddleware
        // ile aynı liste paylaşılır).
        if ($this->isPublicRoute()) {
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
        if ($route === null) {
            // No route config, allow access (should not happen in normal flow)
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
     * Public route kontrolü — AuthenticationMiddleware::PUBLIC_ROUTE_PATTERNS
     * tek kaynaktır.
     */
    private function isPublicRoute(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        foreach (AuthenticationMiddleware::PUBLIC_ROUTE_PATTERNS as $pattern) {
            if (str_starts_with($uri, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
