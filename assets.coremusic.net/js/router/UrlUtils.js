const ASSETS_ORIGIN = (typeof window !== 'undefined' && window.CoreMusic?.RouterConfig?.assetsOrigin) || 'https://assets.coremusic.net';

export function isCrossOrigin(url) {
    try { const target = new URL(url, window.location.href); return target.origin !== window.location.origin; }
    catch { return false; }
}

export function normalizeUrl(url, base) {
    try {
        const u = new URL(url, base || window.location.href);
        if (u.origin !== window.location.origin) return u.href;
        return u.pathname;
    } catch {
        const qIdx = url.indexOf('?');
        return url.substring(0, qIdx !== -1 ? qIdx : url.length);
    }
}

export function isSameOrigin(url, allowedOrigins) {
    try {
        const target = new URL(url, window.location.href);
        const allowed = allowedOrigins || [window.location.origin, ASSETS_ORIGIN];
        return allowed.includes(target.origin);
    } catch { return false; }
}

export function normalizeCacheKey(url, base) {
    try {
        const u = new URL(url, base || window.location.origin);
        u.hash = '';
        const params = [...u.searchParams.entries()].sort(([a], [b]) => a.localeCompare(b));
        u.search = params.map(([k, v]) => `${k}=${v}`).join('&');
        return u.pathname + u.search;
    } catch { return url; }
}
