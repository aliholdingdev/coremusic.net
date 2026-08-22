/**
 * CoreMusic — main.js v5.0.0
 * Ana entry point. PHP HtmlShellRenderer tarafından yüklenir.
 * SPA Router + tüm modülleri başlatır.
 *
 * @module main
 * @version 5.0.0
 */
import Router from './router/Router.js';
import { authGuard, roleGuard, permissionGuard } from './router/guards.js';

/* ─── Core Modüller ─── */
import EventBus from './core/EventBus.js';
import CoreMusicApp from './core/CoreMusicApp.js';

/* ─── Manager Modüller ─── */
import DeviceManager from './managers/DeviceManager.js';
import ThemeManager from './managers/ThemeManager.js';
import ViewModeManager from './managers/ViewModeManager.js';

/* ─── Feature Modüller ─── */
import PlayerController from './features/PlayerController.js';
import WidgetManager from './features/WidgetManager.js';
import CardManager from './features/CardManager.js';
import ScrollManager from './features/ScrollManager.js';
import TouchManager from './features/TouchManager.js';

(function () {
    'use strict';

    /* ─── 1. CoreMusicApp ─── */
    const eventBus = new EventBus();
    const app = new CoreMusicApp({ eventBus });

    /* ─── 2. SPA Router (mevcut Router.js) ─── */
    const routerConfig = window.CoreMusic?.RouterConfig || {};
    let router = null;

    if (routerConfig.enabled !== false && typeof history.pushState === 'function') {
        const guardFunctions = [authGuard, roleGuard, permissionGuard];
        if (typeof routerConfig.customGuard === 'function') {
            guardFunctions.push(routerConfig.customGuard);
        }

        router = new Router({ ...routerConfig, guardFunctions });
        router.init();
        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.Router = router;

        /* Router event'lerini EventBus'e bridge'le */
        eventBus.emit('router:ready', { router });
    }

    /* ─── 3. Diğer Modüller ─── */
    document.addEventListener('DOMContentLoaded', () => {
        /* Manager'lar */
        const deviceManager = new DeviceManager(eventBus);
        deviceManager.init();
        app.registerModule('device', deviceManager);

        const themeManager = new ThemeManager(eventBus);
        themeManager.init();
        app.registerModule('theme', themeManager);

        const viewModeManager = new ViewModeManager(eventBus);
        viewModeManager.init();
        app.registerModule('viewMode', viewModeManager);

        /* Feature'lar */
        const player = new PlayerController(eventBus);
        player.init();
        app.registerModule('player', player);

        const widgets = new WidgetManager(eventBus);
        widgets.init();
        app.registerModule('widgets', widgets);

        const cards = new CardManager(eventBus);
        cards.init();
        app.registerModule('cards', cards);

        const scroll = new ScrollManager(eventBus);
        scroll.init();
        app.registerModule('scroll', scroll);

        const touch = new TouchManager(eventBus);
        touch.init();
        app.registerModule('touch', touch);

        /* App ready */
        app.setRunning();
        eventBus.emit('app:ready');

        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.App = app;
        window.CoreMusic.EventBus = eventBus;
        window.CoreMusic.version = '5.0.0';
    });

    window.addEventListener('popstate', () => {
        /* popstate handled by RouterEventManager */
    });
})();
