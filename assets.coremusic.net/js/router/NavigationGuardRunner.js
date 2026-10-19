export default class NavigationGuardRunner {
    #guards;
    #logger;

    constructor(guards, logger) {
        this.#guards = guards;
        this.#logger = logger;
    }

    async run(ctx, isProtected = false) {
        const routeMeta = {
            auth: isProtected,
            requiredRole: ctx.meta?.requiredRole || null,
            requiredPermission: ctx.meta?.requiredPermission || null
        };

        const result = await this.#guards.run({
            to: ctx.to,
            from: ctx.from,
            meta: routeMeta,
            user: ctx.user
        });

        if (!result.pass) {
            return { pass: false, redirect: result.redirect || null };
        }

        return { pass: true, guardMs: result.guardMs };
    }
}
