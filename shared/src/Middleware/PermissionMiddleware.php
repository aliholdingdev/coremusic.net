<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;

/**
 * Permission Middleware (L1 — Pipeline #9)
 *
 * RBAC yetki kontrolü. Route meta'sındaki requiredRole/requiredPermission'ları kontrol eder.
 * AuthMiddleware'den sonra çalışır — $request['_auth'] dolu olmalı.
 *
 * ADR-010/043 uyumlu. Frozen sıra: ...Auth → Permission → Validation → Controller
 */
final class PermissionMiddleware implements IMiddleware
{
    public function handle(array $request, callable $next): array
    {
        $routeMeta = $request['_route_meta'] ?? [];
        $auth      = $request['_auth'] ?? [];

        // Auth bilgisi yoksa → PermissionMiddleware ilgilenmez, sonraki middleware'a geç
        if (empty($auth['userId'])) {
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
        return [
            'httpStatus' => 403,
            'type'       => 'json',
            'body'       => ['error' => $code, 'message' => $message],
            'headers'    => [],
            'halt'       => true,
        ];
    }
}
