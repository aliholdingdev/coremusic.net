<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;

final class AuthMiddleware implements IMiddleware
{
    public function handle(array $request, callable $next): array
    {
        if (!isset($request['_auth'])) {
            $request['_auth'] = [];
        }

        $session = $request['_session'] ?? [];

        $userId   = $session['MM_UserID'] ?? $session['_session_user_id'] ?? null;
        $role     = $session['MM_UserRole'] ?? $session['_session_user_role'] ?? null;
        $username = $session['MM_Username'] ?? $session['_session_username'] ?? null;

        if ($userId !== null) {
            $request['_auth'] = array_merge($request['_auth'], [
                'userId'   => (int)$userId,
                'role'     => $role !== null ? (string)$role : 'user',
                'username' => $username !== null ? (string)$username : '',
            ]);
        }

        return $next($request);
    }
}
