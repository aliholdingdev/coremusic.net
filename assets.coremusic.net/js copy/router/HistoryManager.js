export default class HistoryManager {
    #logger;
    #snapshot = null;

    constructor(logger) {
        this.#logger = logger;
    }

    snapshot(currentUrl) {
        this.#snapshot = { url: currentUrl, historyLength: history.length };
    }

    rollback() {
        if (this.#snapshot) {
            const url = this.#snapshot.url;
            this.#snapshot = null;
            return url;
        }
        return null;
    }

    push(url, title) {
        try {
            history.pushState(null, title, url);
            return true;
        } catch {
            this.#logger?.error('HistoryManager', 'pushstate_failed');
            return false;
        }
    }

    replace(url) {
        history.replaceState(null, '', url);
    }
}
