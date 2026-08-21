<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class PageRouterHelper
{
    public function checkAuthenticated(): bool
    {
        // TODO: Session key namespace inconsistency — SessionManager writes to MM_UserID,
        // but _session_user_id fallback exists for legacy code. Once all code uses MM_*
        // namespace consistently, remove the _session_user_id fallback.
        $userId = $_SESSION['MM_UserID'] ?? $_SESSION['_session_user_id'] ?? null;
        $result = $userId !== null && $userId !== '' && is_string($userId);
        if (!$result) {
            error_log('[PageRouterHelper] checkAuthenticated FAILED - session_id=' . session_id() . ' session_status=' . session_status());
            error_log('[PageRouterHelper] session keys: ' . implode(', ', array_keys($_SESSION)));
            error_log('[PageRouterHelper] MM_UserID=' . ($_SESSION['MM_UserID'] ?? 'NULL') . ' _session_user_id=' . ($_SESSION['_session_user_id'] ?? 'NULL'));
        }
        return $result;
    }

    public function checkRole(string $requiredRole): bool
    {
        // TODO: Same namespace inconsistency as checkAuthenticated() — MM_UserRole vs _session_user_role.
        // Remove _session_user_role fallback once all code uses MM_* namespace.
        $role = $_SESSION['MM_UserRole'] ?? $_SESSION['_session_user_role'] ?? null;
        return $role !== null && $role === $requiredRole;
    }

    public function checkPermission(string $requiredPermission): bool
    {
        $permissions = $_SESSION['MM_Permissions'] ?? [];
        return in_array($requiredPermission, $permissions, true);
    }
}
