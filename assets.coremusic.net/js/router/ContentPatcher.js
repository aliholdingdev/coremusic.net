import { isAuthRoute } from './config/auth-routes.js';

export default class ContentPatcher {
    #domPatcher;
    #csrfSync;
    #lifecycle;
    #logger;

    constructor({ domPatcher, csrfSync, lifecycle, logger }) {
        this.#domPatcher = domPatcher;
        this.#csrfSync = csrfSync;
        this.#lifecycle = lifecycle;
        this.#logger = logger;
    }

    async patch(responseData, target, container) {
        this.#lifecycle.unmount();
        await this.#domPatcher.patchDOM(responseData.html, container);

        if (responseData.csrfToken) {
            this.#csrfSync.update(responseData.csrfToken);
        }

        // C-F-14: target artik query korur — route anahtari pathname'dir.
        const isAuth = isAuthRoute(target.split('?')[0]);
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
