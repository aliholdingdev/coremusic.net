import Router from './Router.js';
import { authGuard, roleGuard, permissionGuard } from './guards.js';

(function () {
    'use strict';
    const config = window.CoreMusic?.RouterConfig || {};
    if (config.enabled === false) return;
    if (typeof history.pushState !== 'function') return;

    const guardFunctions = [authGuard, roleGuard, permissionGuard];
    if (typeof config.customGuard === 'function') guardFunctions.push(config.customGuard);

    const router = new Router({ ...config, guardFunctions });
    router.init();
    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.Router = router;

    window.addEventListener('popstate', () => {
        // popstate handled by RouterEventManager
    });
})();
