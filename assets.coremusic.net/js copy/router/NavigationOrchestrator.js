import NavigationGuardRunner from './NavigationGuardRunner.js';
import ContentFetcher from './ContentFetcher.js';
import ContentPatcher from './ContentPatcher.js';
import FocusManager from './FocusManager.js';
import HistoryManager from './HistoryManager.js';
import PrefetchManager from './PrefetchManager.js';
import { normalizeUrl, isCrossOrigin } from './UrlUtils.js';
import { ERROR_TYPES } from './config/error-types.js';
import { MAIN_CONTENT_SELECTOR } from './config/css-selectors.js';

const MAX_REDIRECT_DEPTH = 5;
const TITLE_NOT_FOUND = 'Sayfa Bulunamadı';

export default class NavigationOrchestrator {
    #state = { currentUrl: null, inProgress: false, navCount: 0 };
    #guards; #contentFetcher; #contentPatcher; #focusManager; #historyManager;
    #prefetchManager; #authBoundary; #scrollRestorer; #errorHandler; #memoryWatchdog;
    #logger; #user = null; #config;

    constructor(deps, overrides = {}) {
        const { guards, cache, fetcher, lifecycle, domPatcher, csrfSync, contentPatcher, authBoundary, scrollRestorer, errorHandler, memoryWatchdog, logger, config = {}, user = null } = deps;
        this.#guards = overrides.guardRunner ?? new NavigationGuardRunner(guards, logger);
        this.#contentFetcher = overrides.contentFetcher ?? new ContentFetcher(cache, fetcher, logger);
        this.#contentPatcher = overrides.contentPatcher ?? contentPatcher;
        this.#focusManager = overrides.focusManager ?? new FocusManager();
        this.#historyManager = overrides.historyManager ?? new HistoryManager(logger);
        this.#prefetchManager = overrides.prefetchManager ?? new PrefetchManager(cache, fetcher, logger, config?.maxPrefetch || 2);
        this.#authBoundary = authBoundary;
        this.#scrollRestorer = scrollRestorer;
        this.#errorHandler = errorHandler;
        this.#memoryWatchdog = memoryWatchdog;
        this.#logger = logger;
        this.#user = user;
        this.#config = { ...config };
        if (Array.isArray(this.#config.protectedRoutes)) this.#config.protectedRoutes = Object.freeze([...this.#config.protectedRoutes]);
    }

    get currentUrl() { return this.#state.currentUrl; }
    get navCount() { return this.#state.navCount; }

    initUrl(url) { this.#state.currentUrl = url; }

    async navigate(url, pushState = true, depth = 0) {
        const target = normalizeUrl(url);
        if (isCrossOrigin(target)) { window.location.href = target; return; }
        if (depth > MAX_REDIRECT_DEPTH) { window.location.href = target; return; }
        if (target === this.#state.currentUrl && !this.#state.inProgress) return;
        this.#state.inProgress = true;
        const container = document.querySelector(MAIN_CONTENT_SELECTOR);
        if (!container) { window.location.href = target; return; }
        container.setAttribute('aria-busy', 'true');
        const isProtected = this.#config.protectedRoutes?.includes(target) ?? false;
        const guardResult = await this.#guards.run({ to: target, from: this.#state.currentUrl, meta: { requiredRole: null, requiredPermission: null }, user: this.#user }, isProtected);
        if (!guardResult.pass) { if (guardResult.redirect) { if (isCrossOrigin(guardResult.redirect)) { window.location.href = guardResult.redirect; return; } await this.navigate(guardResult.redirect, true, depth + 1); return; } container.setAttribute('aria-busy', 'false'); this.#state.inProgress = false; return; }
        if (this.#authBoundary.reloadIfNeeded(target, this.#state.currentUrl)) return;
        try {
            const { responseData, fetchMs, cacheHit } = await this.#contentFetcher.fetch(target, container);
            if (responseData === null) { container.setAttribute('aria-busy', 'false'); this.#state.inProgress = false; return; }
            if (responseData.error) {
                if (responseData.error === ERROR_TYPES.FORBIDDEN && responseData.redirect) { this.#state.inProgress = false; container.setAttribute('aria-busy', 'false'); if (isCrossOrigin(responseData.redirect)) { window.location.href = responseData.redirect; } else if (responseData.redirect !== this.#state.currentUrl) { await this.navigate(responseData.redirect, true, depth); } return; }
                if (responseData.error === ERROR_TYPES.NOT_FOUND) { this.#state.inProgress = false; container.setAttribute('aria-busy', 'false'); container.dataset.error = '404'; return; }
                this.#state.inProgress = false; container.setAttribute('aria-busy', 'false'); return;
            }
            await this.#contentPatcher.patch(responseData, target, container);
            if (pushState) history.pushState(null, document.title, target);
            this.#state.currentUrl = target;
            this.#state.navCount++;
            this.#state.inProgress = false;
            container.setAttribute('aria-busy', 'false');
            this.#focusManager.moveFocus();
            this.#scrollRestorer.scrollToTop();
            this.#memoryWatchdog?.check(this.#state.navCount);
        } catch (err) {
            this.#state.inProgress = false;
            container.setAttribute('aria-busy', 'false');
            this.#logger?.error('NavigationOrchestrator', 'navigation_error', { routeTo: target, error: err.message });
        }
    }

    async prefetch(url) { await this.#prefetchManager.prefetch(normalizeUrl(url)); }
    abortAll() { this.#prefetchManager.abortAll(); }
}
