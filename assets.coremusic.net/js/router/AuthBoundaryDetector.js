import { isAuthUrl } from './config/auth-routes.js';
import { isCrossOrigin } from './UrlUtils.js';

export default class AuthBoundaryDetector {
    #logger;

    constructor(logger) {
        this.#logger = logger;
    }

    detect(fromUrl, toUrl) {
        const from = isAuthUrl(fromUrl);
        const to = isAuthUrl(toUrl);
        return {
            hasBoundary: from !== to,
            from: from ? 'auth' : 'non-auth',
            to: to ? 'auth' : 'non-auth'
        };
    }

    reloadIfNeeded(targetUrl, currentUrl) {
        if (isCrossOrigin(targetUrl)) {
            window.location.href = targetUrl;
            return true;
        }
        const { hasBoundary } = this.detect(currentUrl || window.location.pathname, targetUrl);
        if (hasBoundary) {
            window.location.href = targetUrl;
            return true;
        }
        return false;
    }
}
