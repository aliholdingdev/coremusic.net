const ASSETS_ORIGIN = (typeof window !== 'undefined' && window.CoreMusic?.RouterConfig?.assetsOrigin) || 'https://assets.coremusic.net';

export function isCrossOrigin(url) {
    try { const target = new URL(url, window.location.href); return target.origin !== window.location.origin; }
    catch { return false; }
}

export function normalizeUrl(url, base) {
    try {
        const u = new URL(url, base || window.location.href);
        if (u.origin !== window.location.origin) return u.href;
        // C-F-14: query KORUNUR — SPA navigasyonunda query-driven özellikler
        // (arama/filtre/sayfalama) çalışmıyordu. Rota ANAHTARI pathname'dir;
        // guard eşleşmesi orchestrator'da path-only yapılır.
        return u.pathname + u.search;
    } catch {
        // Parse başarısız: hash düşer, query korunur (yukarıdaki politika ile aynı).
        const hIdx = url.indexOf('#');
        return hIdx !== -1 ? url.substring(0, hIdx) : url;
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
