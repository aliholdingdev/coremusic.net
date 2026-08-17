export function authGuard(ctx) {
    const isAuth = ctx.meta?.auth === true;
    if (isAuth && !ctx.user?.id) {
        return { redirect: '/login' };
    }
    return true;
}

export function roleGuard(ctx) {
    if (ctx.meta?.requiredRole && ctx.user?.role !== ctx.meta.requiredRole) {
        return { redirect: '/403' };
    }
    return true;
}

export function permissionGuard(ctx) {
    if (ctx.meta?.requiredPermission && (!ctx.user?.permissions || !ctx.user.permissions.includes(ctx.meta.requiredPermission))) {
        return { redirect: '/403' };
    }
    return true;
}
