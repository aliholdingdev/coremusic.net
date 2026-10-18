<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Contracts\Middleware\IMiddleware;

/**
 * Permission Middleware (L1 — Pipeline #9)
 *
 * RBAC yetki kontrolü. Route meta'sındaki requiredRole/requiredPermission'ları kontrol eder.
 * AuthMiddleware'den sonra çalışır — $request['_auth'] dolu olmalı.
 * Fail-closed: requiredRole/requiredPermission varken auth boşsa 401 (public rotalar etkilenmez).
 *
 * ADR-010/043 uyumlu. Frozen sıra: ...Auth → Permission → Validation → Controller
 */
final class PermissionMiddleware implements IMiddleware
{
    public function handle(array $request, callable $next): array
    {
        $routeMeta = $request['_route_meta'] ?? [];
        $auth      = $request['_auth'] ?? [];

        // Fail-closed: rota role/permission isterken auth yoksa istek $next'e DEĞİL 401'e düşer.
        if (empty($auth['userId'])) {
            if (($routeMeta['requiredRole'] ?? null) !== null
                || ($routeMeta['requiredPermission'] ?? null) !== null
            ) {
                return $this->unauthenticated();
            }

            // Kimlik gerektirmeyen (public) rotalar → davranış değişmez.
            return $next($request);
        }

        $userRole = $auth['role'] ?? 'user';

        // Role kontrolü
        $requiredRole = $routeMeta['requiredRole'] ?? null;
        if ($requiredRole !== null && $userRole !== $requiredRole) {
            return $this->forbidden('role_insufficient', "Required role: {$requiredRole}");
        }

        // Permission kontrolü
        $requiredPermission = $routeMeta['requiredPermission'] ?? null;
        if ($requiredPermission !== null) {
            $userPermissions = $auth['permissions'] ?? [];
            if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
                return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
            }
        }

        return $next($request);
    }

    private function forbidden(string $code, string $message): array
    {
        // P5-32: authz reddi gözlemlenir (öncekte sessizce 403 dönüyordu).
        \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('authz_denied', [
            'reason' => $code,
            'uri'    => $_SERVER['REQUEST_URI'] ?? '-',
            'ip'     => $_SERVER['REMOTE_ADDR'] ?? '-',
        ]);

        return [
            'httpStatus' => 403,
            'type'       => 'json',
            'body'       => ['error' => $code, 'message' => $message],
            'headers'    => [],
            'halt'       => true,
        ];
    }

    /**
     * Fail-closed: yetki istenen rota, kimlik doğrulanmadan geçmez (401).
     */
    private function unauthenticated(): array
    {
        \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('authz_denied', [
            'reason' => 'unauthenticated',
            'uri'    => $_SERVER['REQUEST_URI'] ?? '-',
            'ip'     => $_SERVER['REMOTE_ADDR'] ?? '-',
        ]);

        return [
            'httpStatus' => 401,
            'type'       => 'json',
            'body'       => ['error' => 'unauthenticated', 'message' => 'Authentication required'],
            'headers'    => [],
            'halt'       => true,
        ];
    }
}
