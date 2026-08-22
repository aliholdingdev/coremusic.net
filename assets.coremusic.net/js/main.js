/**
 * CoreMusic — main.js v5.0.0
 * Entry point: Modülleri import et ve başlat.
 *
 * @module main
 * @version 5.0.0
 */
import EventBus from './core/EventBus.js';
import CoreMusicApp from './core/CoreMusicApp.js';
import DeviceManager from './managers/DeviceManager.js';
import ThemeManager from './managers/ThemeManager.js';
import ViewModeManager from './managers/ViewModeManager.js';
import SPARouterAdapter from './router/SPARouterAdapter.js';
import PlayerController from './features/PlayerController.js';
import WidgetManager from './features/WidgetManager.js';
import CardManager from './features/CardManager.js';
import ScrollManager from './features/ScrollManager.js';
import TouchManager from './features/TouchManager.js';

(function () {
    'use strict';

    if (typeof history.pushState !== 'function') return;

    const app = new CoreMusicApp({
        modules: {
            EventBus,
            DeviceManager,
            ThemeManager,
            ViewModeManager,
            SPARouterAdapter,
            PlayerController,
            WidgetManager,
            CardManager,
            ScrollManager,
            TouchManager,
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        app.init();
    });

    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.App = app;
    window.CoreMusic.version = '5.0.0';
})();
