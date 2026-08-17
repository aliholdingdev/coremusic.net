export default class IRouter {
    init() { throw new Error('IRouter.init() must be implemented'); }
    async navigate(url, pushState = true) { throw new Error('IRouter.navigate() must be implemented'); }
    async prefetch(url) { throw new Error('IRouter.prefetch() must be implemented'); }
    destroy() { throw new Error('IRouter.destroy() must be implemented'); }
}
