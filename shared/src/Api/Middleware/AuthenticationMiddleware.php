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
use CoreMusic\Security\JwtService;

/**
 * Authentication Middleware for hybrid session/JWT authentication.
 *
 * P1-9 (B-F-01): Bearer dalı artık gerçek RS256 doğrulama yapar (JwtService);
 * imza/exp/iss/aud geçerli VE (varsa) jti revocation kontrolü sağlarsa
 * `_auth_user.method = 'jwt'` ile devam eder. Aksi halde 401 (fail-closed).
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

    /**
     * @param \Closure(string): bool|null $accessTokenValidator
     *        jti → hâlâ geçerli mi? (user_tokens revocation; null = stateless kabul,
     *        yalnız exp sınırıyla — API index her zaman bağlar)
     */
    public function __construct(
        private readonly ISessionManager $sessionManager,
        private readonly ?JwtService $jwt = null,
        private readonly ?\Closure $accessTokenValidator = null,
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

        // Check for JWT token in Authorization header (P1-9: gerçek RS256 doğrulama)
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($authHeader, 'Bearer ') && $this->jwt !== null) {
            $claims = $this->jwt->validate(substr($authHeader, 7));

            if ($claims !== null) {
                $revoked = $this->accessTokenValidator !== null
                    && !($this->accessTokenValidator)((string) $claims['jti']);
                if (!$revoked) {
                    $request['_auth_user'] = [
                        'id'           => (string) $claims['sub'],
                        'authenticated' => true,
                        'method'       => 'jwt',
                    ];
                    return $next($request);
                }
                // revocation edilmiş jti → 401'e düşer (fail-closed)
            }
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
}
