<?php
declare(strict_types=1);

/**
 * Authentication Middleware for API requests.
 *
 * @file AuthenticationMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Api\ApiResponse;
use CoreMusic\Api\Routing\RouteTable;

/**
 * Authentication Middleware for hybrid session/JWT authentication.
 */
final class AuthenticationMiddleware
{
    /**
     * Auth'sız (public) route önekleri. AuthorizationMiddleware de bunu paylaşır.
     *
     * @var string[]
     */
    public const PUBLIC_ROUTE_PATTERNS = [
        '/api/v1/auth/login',
        '/api/v1/auth/register',
        '/api/v1/auth/forgot-password',
        '/api/v1/public',
    ];

    public function __construct(
        private readonly ISessionManager $sessionManager
    ) {}

    /**
     * Process authentication for API requests.
     */
    public function __invoke(array $request, callable $next): array
    {
        // 404 / 405: handler hiç çalışmayacak (yanıtı Gateway üretir) → auth
        // kontrolü burada 401 ile sonlanmamalı; bilinmeyen yol 404, tanımlı
        // olmayan method 405 + Allow döner (RFC 9110).
        $routeStatus = $request['_route_status'] ?? null;
        if ($routeStatus === RouteTable::STATUS_NOT_FOUND
            || $routeStatus === RouteTable::STATUS_METHOD_NOT_ALLOWED
        ) {
            return $next($request);
        }

        // Skip authentication for public routes
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }

        // Check if user is authenticated via session
        if ($this->sessionManager->isAuthenticated()) {
            $userId = $this->sessionManager->getUserId();
            $request['_auth_user'] = [
                'id' => $userId,
                'authenticated' => true,
                'method' => 'session',
            ];
            return $next($request);
        }

        // Check for JWT token in Authorization header
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($authHeader, 'Bearer ')) {
            // JWT doğrulaması henüz uygulanmadı: validateJwtToken() her zaman
            // null döndüğü için bu dal 401'e düşer (davranış değişmez).
            $this->validateJwtToken(substr($authHeader, 7));
        }

        // Not authenticated
        return ApiResponse::error(
            'UNAUTHORIZED',
            'Authentication required',
            401
        );
    }

    /**
     * Check if the route is public (no auth required).
     *
     * Kaynak: pipeline'a aktarılan `_route['public']` (RouteTable) VEYA
     * `PUBLIC_ROUTE_PATTERNS` — ikisi de public ise erişim açıktır
     * (pattern listesi route eşleşmediğinde fallback olarak kalır).
     */
    private function isPublicRoute(array $request): bool
    {
        $route = $request['_route'] ?? null;
        if (is_array($route) && !empty($route['public'])) {
            return true;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        foreach (self::PUBLIC_ROUTE_PATTERNS as $pattern) {
            if (str_starts_with($uri, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate JWT token (simplified implementation).
     *
     * Henüz hiçbir zaman doğrulanmış bir token döndürmez; gerçek JWT
     * doğrulaması eklenene kadar dönüş tipi yalnızca `null`'dır.
     */
    private function validateJwtToken(string $token): null
    {
        // This is a simplified JWT validation
        // In production, this would use a proper JWT library
        // with RS256 verification

        // For now, return null (not validated)
        // Real implementation would:
        // 1. Decode JWT header
        // 2. Verify signature with public key
        // 3. Check expiration
        // 4. Extract user claims

        return null;
    }
}
