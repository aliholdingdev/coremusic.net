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

use CoreMusic\Interfaces\Auth\ISessionManager;
use CoreMusic\Api\ApiResponse;

/**
 * Authentication Middleware for hybrid session/JWT authentication.
 */
final class AuthenticationMiddleware
{
    public function __construct(
        private readonly ISessionManager $sessionManager
    ) {}

    /**
     * Process authentication for API requests.
     */
    public function __invoke(array $request, callable $next): array
    {
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
            $token = substr($authHeader, 7);
            $user = $this->validateJwtToken($token);
            
            if ($user !== null) {
                $request['_auth_user'] = $user;
                return $next($request);
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
     */
    private function isPublicRoute(array $request): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $publicPatterns = [
            '/api/v1/auth/login',
            '/api/v1/auth/register',
            '/api/v1/auth/forgot-password',
            '/api/v1/public',
        ];
        
        foreach ($publicPatterns as $pattern) {
            if (str_starts_with($uri, $pattern)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Validate JWT token (simplified implementation).
     */
    private function validateJwtToken(string $token): ?array
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
