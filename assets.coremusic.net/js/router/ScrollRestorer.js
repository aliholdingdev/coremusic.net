export default class ScrollRestorer {
    #logger;

    constructor(logger) {
        this.#logger = logger;
    }

    scrollToTop() {
        try {
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
        } catch {
            window.scrollTo(0, 0);
        }
    }
}
