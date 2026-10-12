export default class Logger {
    static LEVELS = { debug: 0, info: 1, warn: 2, error: 3 };
    #traceId = '';
    #level;

    constructor(level = 'info') { this.#level = level; this.newTrace(); }

    newTrace() {
        this.#traceId = crypto.randomUUID ? crypto.randomUUID() : this.#fallback();
        return this.#traceId;
    }

    #fallback() {
        const bytes = new Uint8Array(16);
        crypto.getRandomValues(bytes);
        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;
        const hex = [...bytes].map(b => b.toString(16).padStart(2, '0')).join('');
        return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
    }

    get traceId() { return this.#traceId; }

    log(level, module, event, extra = {}) {
        if (Logger.LEVELS[level] < Logger.LEVELS[this.#level]) return;
        const record = { timestamp: new Date().toISOString(), level, module, event, traceId: this.#traceId, ...extra };
        const transport = window.CoreMusic?.RouterConfig?.logTransport;
        if (typeof transport === 'function') { transport(record); }
        else { const fn = console[level] || console.log; fn.call(console, `[${module}] ${event}`, JSON.stringify(record)); }
    }

    debug(module, event, extra = {}) { this.log('debug', module, event, extra); }
    info(module, event, extra = {}) { this.log('info', module, event, extra); }
    warn(module, event, extra = {}) { this.log('warn', module, event, extra); }
    error(module, event, extra = {}) { this.log('error', module, event, extra); }
}
