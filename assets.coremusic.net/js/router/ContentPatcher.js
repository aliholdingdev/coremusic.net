import { isAuthRoute } from './config/auth-routes.js';

export default class ContentPatcher {
    #domPatcher;
    #csrfSync;
    #lifecycle;
    #errorHandler;
    #logger;

    constructor({ domPatcher, csrfSync, lifecycle, logger, errorHandler }) {
        this.#domPatcher = domPatcher;
        this.#csrfSync = csrfSync;
        this.#lifecycle = lifecycle;
        this.#logger = logger;
        this.#errorHandler = errorHandler;
    }

    async patch(responseData, target, container) {
        this.#lifecycle.unmount();
        await this.#domPatcher.patchDOM(responseData.html, container);

        if (responseData.csrfToken) {
            this.#csrfSync.update(responseData.csrfToken);
        }

        const isAuth = isAuthRoute(target);
        document.body.classList.toggle('auth-page', isAuth);

        this.#lifecycle.mount(container);
    }

    renderInitial(state, container) {
        if (!container || state.error) return;
        if (state.container) {
            this.#domPatcher.safeSetHTML(container, state.container);
            container.setAttribute('aria-busy', 'false');
            this.#csrfSync.update(state.csrf_token);
            this.#lifecycle.mount(container);
            if (state.meta?.title) document.title = state.meta.title;
        }
    }
}
