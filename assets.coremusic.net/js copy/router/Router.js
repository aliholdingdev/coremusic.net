import IRouter from './IRouter.js';
import GuardPipeline from './GuardPipeline.js';
import CacheLayer from './CacheLayer.js';
import LifecycleManager from './LifecycleManager.js';
import FetchWrapper from './FetchWrapper.js';
import Logger from './Logger.js';
import NavigationOrchestrator from './NavigationOrchestrator.js';
import DomPatcher from './DomPatcher.js';
import ContentPatcher from './ContentPatcher.js';
import CsrfSyncManager from './CsrfSyncManager.js';
import AuthBoundaryDetector from './AuthBoundaryDetector.js';
import ScrollRestorer from './ScrollRestorer.js';
import ErrorHandler from './ErrorHandler.js';
import MemoryWatchdog from './MemoryWatchdog.js';
import RouterEventManager from './RouterEventManager.js';
import { normalizeUrl } from './UrlUtils.js';
import { MAIN_CONTENT_SELECTOR } from './config/css-selectors.js';

export default class Router extends IRouter {
    #logger; #cache; #lifecycle; #guards; #fetcher; #nav; #domPatcher; #contentPatcher;
    #csrfSync; #authBoundary; #scrollRestorer; #errorHandler; #memoryWatchdog; #eventManager;
    #user = null; #config;

    constructor(config = {}) {
        super();
        const { slowNavigationMs = 3000, memoryCheckInterval = 5000, memoryThresholdMB = 100, maxPrefetch = 2, ...rest } = config;
        this.#config = { slowNavigationMs, memoryCheckInterval, memoryThresholdMB, maxPrefetch, ...rest };
        this.#logger = config.logger ?? new Logger(config.logLevel ?? 'info');
        this.#csrfSync = config.csrfSync ?? new CsrfSyncManager(this.#logger);
        this.#cache = config.cache ?? new CacheLayer({}, this.#logger);
        this.#lifecycle = config.lifecycle ?? new LifecycleManager(this.#logger);
        this.#guards = config.guards ?? new GuardPipeline(this.#logger);
        this.#fetcher = config.fetcher ?? new FetchWrapper(this.#logger, this.#csrfSync);
        this.#domPatcher = config.domPatcher ?? new DomPatcher(this.#logger);
        this.#errorHandler = config.errorHandler ?? new ErrorHandler(this.#logger);
        this.#contentPatcher = config.contentPatcher ?? new ContentPatcher({ domPatcher: this.#domPatcher, csrfSync: this.#csrfSync, lifecycle: this.#lifecycle, logger: this.#logger, errorHandler: this.#errorHandler });
        this.#authBoundary = config.authBoundary ?? new AuthBoundaryDetector(this.#logger);
        this.#scrollRestorer = config.scrollRestorer ?? new ScrollRestorer(this.#logger);
        this.#memoryWatchdog = config.memoryWatchdog ?? new MemoryWatchdog(this.#cache, this.#logger, { checkInterval: this.#config.memoryCheckInterval, thresholdMB: this.#config.memoryThresholdMB });
        this.#eventManager = config.eventManager ?? new RouterEventManager();
        this.#user = config.user ?? null;
        if (Array.isArray(config.guardFunctions)) { for (const fn of config.guardFunctions) this.#guards.register(fn); }
        this.#nav = config.navigationOrchestrator ?? new NavigationOrchestrator({ guards: this.#guards, cache: this.#cache, fetcher: this.#fetcher, lifecycle: this.#lifecycle, domPatcher: this.#domPatcher, csrfSync: this.#csrfSync, contentPatcher: this.#contentPatcher, authBoundary: this.#authBoundary, scrollRestorer: this.#scrollRestorer, errorHandler: this.#errorHandler, memoryWatchdog: this.#memoryWatchdog, logger: this.#logger, user: this.#user, config: this.#config });
    }

    get currentUrl() { return this.#nav.currentUrl; }
    get csrfSync() { return this.#csrfSync; }
    get navCount() { return this.#nav.navCount; }
    get logger() { return this.#logger; }

    init() {
        if (window.CoreMusic?.RouterConfig?.enabled === false) return;
        if (typeof history.pushState !== 'function') return;
        history.scrollRestoration = 'manual';
        this.#eventManager.bind({
            onNavigate: (url, pushState) => this.#nav.navigate(url, pushState),
            onPrefetch: (url) => this.#nav.prefetch(url),
            onOffline: () => this.#domPatcher.setErrorState('offline'),
            onOnline: () => this.#domPatcher.setErrorState(null),
            onError: (e) => this.#logger.error('Router', 'unhandled_error', { message: e.message }),
            onUnhandledRejection: (e) => this.#logger.error('Router', 'unhandled_error', { message: e.reason?.message || String(e.reason) }),
        });
        if (window.location.search?.includes('auth_key=')) return;
        if (window.location.search) history.replaceState(null, '', window.location.pathname);
        this.#nav.initUrl(window.location.pathname);
        const initialState = this.#config.initialState;
        if (initialState) this.#renderInitialContent(initialState);
    }

    async navigate(url, pushState = true) { await this.#nav?.navigate(url, pushState); }
    async prefetch(url) { await this.#nav?.prefetch(url); }
    invalidateCacheTag(tag) { this.#cache.invalidateTag(tag); }
    clearCache() { this.#cache.clear(); }

    async destroy() {
        this.#fetcher.abort?.(); this.#nav?.abortAll?.(); this.#lifecycle.unmount();
        this.#eventManager.unbind(); this.#memoryWatchdog?.destroy?.();
        this.#nav = null;
    }

    #renderInitialContent(state) {
        const container = document.querySelector(MAIN_CONTENT_SELECTOR);
        this.#contentPatcher.renderInitial(state, container);
    }
}
