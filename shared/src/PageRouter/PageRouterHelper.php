<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class PageRouterHelper
{
    public function checkAuthenticated(): bool
    {
        $userId = $_SESSION['MM_UserID'] ?? $_SESSION['_session_user_id'] ?? null;
        return $userId !== null && (int)$userId > 0;
    }

    public function checkRole(string $requiredRole): bool
    {
        $role = $_SESSION['MM_UserRole'] ?? $_SESSION['_session_user_role'] ?? null;
        return $role !== null && $role === $requiredRole;
    }

    public function checkPermission(string $requiredPermission): bool
    {
        $permissions = $_SESSION['MM_Permissions'] ?? [];
        return in_array($requiredPermission, $permissions, true);
    }
}
