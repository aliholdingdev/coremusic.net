export function combineSignals(...signals) {
    const controller = new AbortController();
    const attached = [];
    const cleanup = () => { for (const { target, handler } of attached) target.removeEventListener('abort', handler); attached.length = 0; };
    for (const signal of signals) {
        if (signal.aborted) { controller.abort(); return controller.signal; }
        const handler = () => controller.abort();
        signal.addEventListener('abort', handler, { once: true });
        attached.push({ target: signal, handler });
    }
    controller.signal.addEventListener('abort', cleanup, { once: true });
    return controller.signal;
}

export function createTimeout(ms) {
    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), ms);
    return { signal: controller.signal, clear: () => clearTimeout(id) };
}
