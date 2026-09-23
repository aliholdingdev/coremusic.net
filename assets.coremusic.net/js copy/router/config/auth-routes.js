export const AUTH_ROUTES = Object.freeze(['login', 'register', 'select-gender', 'forgot-password', 'reset-password']);
export const AUTH_DOMAINS = Object.freeze(['auth.coremusic.net']);

export function isAuthDomain(hostname) {
    return AUTH_DOMAINS.includes(hostname);
}

export function isAuthRoute(uri) {
    const normalized = uri.replace(/^\/+/, '');
    return AUTH_ROUTES.includes(normalized);
}

export function isAuthUrl(url) {
    try {
        const u = new URL(url, window.location.href);
        return isAuthDomain(u.hostname) || isAuthRoute(u.pathname);
    } catch {
        return isAuthRoute(url);
    }
}
