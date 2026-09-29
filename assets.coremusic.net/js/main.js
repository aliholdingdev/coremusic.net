/**
 * CoreMusic — main.js v6.1.0
 * Ana entry point. PHP HtmlShellRenderer tarafından yüklenir.
 * SPA Router + tüm modülleri başlatır.
 *
 * @module main
 * @version 6.1.0
 */
import Router from './router/Router.js';
import { authGuard, roleGuard, permissionGuard } from './router/guards.js';

/* ─── Core Modüller ─── */
import EventBus from './core/EventBus.js';
import CoreMusicApp from './core/CoreMusicApp.js';

/* ─── Manager Modüller ─── */
import DeviceManager from './managers/DeviceManager.js';
import ScaleManager from './managers/ScaleManager.js';
import ThemeManager from './managers/ThemeManager.js';
import ViewModeManager from './managers/ViewModeManager.js';
import SidebarManager from './managers/SidebarManager.js';

/* ─── Feature Modüller ─── */
import PlayerController from './features/PlayerController.js';
import WidgetManager from './features/WidgetManager.js';
import CardManager from './features/CardManager.js';
import ScrollManager from './features/ScrollManager.js';
import TouchManager from './features/TouchManager.js';

/* --- Component System (v1.0) --- */
import ComponentRegistry from './components/base/ComponentRegistry.js';
import ComponentLoader from './components/base/ComponentLoader.js';
import AccordionComponent from './components/interactive/AccordionComponent.js';
import InfiniteScroll from './components/interactive/InfiniteScroll.js';
import PlayerInfoComponent from './components/interactive/PlayerInfoComponent.js';

/* Batch 3 migrasyonu (2026-09-27) — cm-tabs/cm-dropdown/cm-toast → composites/.
   Gerekçe: şablonlarda data-cm-component="cm-tabs|cm-dropdown|cm-toast" YOK,
   .cm-* için CSS YOK (stilsiz); .toast için c-toast.css var.
   interactive/{Tabs,Dropdown,Toast}Component.js silinmez — ayrı onay turu. */
import TabsComponent from './components/composites/TabsComponent.js';
import DropdownComponent from './components/composites/DropdownComponent.js';
import ToastComponent from './components/composites/ToastComponent.js';

/* --- Primitives (Faz 2 Batch 1 — Figma 18:2907) --- */
import ButtonComponent from './components/primitives/ButtonComponent.js';
import InputComponent from './components/primitives/InputComponent.js';
import ToggleComponent from './components/primitives/ToggleComponent.js';
import SliderComponent from './components/primitives/SliderComponent.js';
import BadgeComponent from './components/primitives/BadgeComponent.js';
import SkeletonComponent from './components/primitives/SkeletonComponent.js';

/* --- Composites (Faz 2 Batch 2 — Figma 18:2907) --- */
import NavLinkComponent from './components/composites/NavLinkComponent.js';
import HeroComponent from './components/composites/HeroComponent.js';
import CardComponent from './components/composites/CardComponent.js';
import ModalComponent from './components/composites/ModalComponent.js';
import AvatarComponent from './components/composites/AvatarComponent.js';
import TooltipComponent from './components/composites/TooltipComponent.js';
import ProgressComponent from './components/composites/ProgressComponent.js';
import WidgetAreaComponent from './components/composites/WidgetAreaComponent.js';
import QuickAppsComponent from './components/composites/QuickAppsComponent.js';
import MiniCardComponent from './components/composites/MiniCardComponent.js';

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

        const scaleManager = new ScaleManager(eventBus);
        scaleManager.init();
        app.registerModule('scale', scaleManager);

        const themeManager = new ThemeManager(eventBus);
        themeManager.init();
        app.registerModule('theme', themeManager);

        const viewModeManager = new ViewModeManager(eventBus);
        viewModeManager.init();
        app.registerModule('viewMode', viewModeManager);

        /* Sidebar Manager */
        const sidebarManager = new SidebarManager(eventBus, {
            userId: window.CoreMusic?.userId || 'guest',
            assetsUrl: window.CoreMusic?.assetsUrl || '',
        });
        sidebarManager.init();
        app.registerModule('sidebar', sidebarManager);

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

        /* --- Component System v1.0 --- */
        const componentRegistry = ComponentRegistry.getInstance();
        componentRegistry
            .register('cm-tabs', TabsComponent)
            .register('cm-dropdown', DropdownComponent)
            .register('cm-accordion', AccordionComponent)
            .register('cm-toast', ToastComponent)
            .register('cm-infinite-scroll', InfiniteScroll)
            .register('cm-player-info', PlayerInfoComponent)
            /* Faz 2 Batch 1 — primitive bileşenler (Figma 18:2907) */
            .register('cm-button', ButtonComponent)
            .register('cm-input', InputComponent)
            .register('cm-toggle', ToggleComponent)
            .register('cm-slider', SliderComponent)
            .register('cm-badge', BadgeComponent)
            .register('cm-skeleton', SkeletonComponent)
            /* Faz 2 Batch 2 — composite bileşenler (Figma 18:2907) */
            .register('cm-nav-link', NavLinkComponent)
            .register('cm-hero', HeroComponent)
            .register('cm-card', CardComponent)
            .register('cm-modal', ModalComponent)
            .register('cm-avatar', AvatarComponent)
            .register('cm-tooltip', TooltipComponent)
            .register('cm-progress', ProgressComponent)
            .register('cm-home-widget-grid', WidgetAreaComponent)
            .register('cm-home-quick-apps', QuickAppsComponent)
            .register('cm-home-mini-card', MiniCardComponent);

        const componentLoader = new ComponentLoader(componentRegistry);
        componentLoader.scan();
        componentLoader.observe();
        app.registerModule('components', { registry: componentRegistry, loader: componentLoader });

        /* Toast singleton'ı global erişime aç */
        window.CoreMusic.Toast = ToastComponent.getInstance();

        /* App ready */
        app.setRunning();
        eventBus.emit('app:ready');

        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.App = app;
        window.CoreMusic.EventBus = eventBus;
        window.CoreMusic.version = '6.1.0';
    });

    window.addEventListener('popstate', () => {
        /* popstate handled by RouterEventManager */
    });
})();
