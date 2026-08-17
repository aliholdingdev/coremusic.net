---
type: architecture
category: l2
title: "Guard Pipeline — Auth Guard (PHP + JS)"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Guard Pipeline — Auth Guard (PHP + JS)

**Zorunlu Bağlantılar:** [[spa-router]] · [[js-router]] · [[ADR-083-spa-router]] · [[ADR-043-auth-subdomain-consolidation]]

**Referans Proje:** `reference-project/coremusic-shared/src/PageRouter/AuthGuard.php`, `AuthUrlBuilder.php`, `PageRouterHelper.php`, `reference-project/assets.coremusic.net/js/router/guards.js`, `GuardPipeline.js`

---

## 1. Amaç

PHP ve JS tarafındaki auth guard mekanizmasını tanımlar. PHP tarafı server-side, JS tarafı client-side guard çalıştırır.

---

## 2. PHP Guard — AuthGuard

**SRP:** Tek sorumluluk — auth gerektiren rotalar için erişim kontrolü.

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * AuthGuard — Auth guard mantığını PageRouter'dan ayırır (SRP).
 *
 * 6 kontrol sırasıyla çalışır:
 *   1. Auth required + giriş yapılmamış → /login redirect
 *   2. Rol yetkisi kontrolü → 403 forbidden
 *   3. İzin kontrolü → 403 forbidden
 *   4. Auth redirect rotaları (login/register) → auth servise yönlendir
 *   5. Giriş yapmış kullanıcı auth sayfalarına → /home redirect
 *   6. Logout → auth servise yönlendir
 */
final class AuthGuard
{
    public function __construct(
        private readonly PageRouterHelper $authHelper,
        private readonly AuthUrlBuilder  $urlBuilder,
    ) {}

    public function check(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
    {
        return $this->checkAuthRequired($uri, $route, $isSpaRequest)
            ?? $this->checkRole($uri, $route)
            ?? $this->checkPermission($uri, $route)
            ?? $this->checkAuthRedirectRoute($uri, $isSpaRequest)
            ?? $this->checkAuthenticatedUserOnAuthPage($uri, $isSpaRequest)
            ?? $this->checkLogout($uri, $isSpaRequest)
            ?? null;
    }

    // 1. Auth required + giriş yapılmamış
    private function checkAuthRequired(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
    {
        if (!$route->requiresAuth || $this->authHelper->checkAuthenticated()) return null;
        return $this->urlBuilder->redirectAuth('login', '/' . $uri, $isSpaRequest);
    }

    // 2. Rol yetkisi
    private function checkRole(string $uri, SpaRoute $route): ?array
    {
        if ($route->requiredRole === null || $this->authHelper->checkRole($route->requiredRole)) return null;
        return RouteResult::forbidden('/' . $uri);
    }

    // 3. İzin
    private function checkPermission(string $uri, SpaRoute $route): ?array
    {
        if ($route->requiredPermission === null || $this->authHelper->checkPermission($route->requiredPermission)) return null;
        return RouteResult::forbidden('/' . $uri);
    }

    // 4. Auth redirect route
    private function checkAuthRedirectRoute(string $uri, bool $isSpaRequest): ?array
    {
        if (!AuthRouteConfig::isAuthRedirectRoute($uri) || $this->authHelper->checkAuthenticated()) return null;
        return $this->urlBuilder->redirectAuth($uri, null, $isSpaRequest);
    }

    // 5. Giriş yapmış kullanıcı auth sayfasında
    private function checkAuthenticatedUserOnAuthPage(string $uri, bool $isSpaRequest): ?array
    {
        if (!$this->authHelper->checkAuthenticated() || !AuthRouteConfig::isAuthRedirectRoute($uri) || $uri === 'logout') return null;
        return $isSpaRequest ? RouteResult::forbidden('/home') : RouteResult::redirect('/home');
    }

    // 6. Logout
    private function checkLogout(string $uri, bool $isSpaRequest): ?array
    {
        if ($uri !== 'logout' || !$this->authHelper->checkAuthenticated()) return null;
        return $this->urlBuilder->redirectAuth('logout', null, $isSpaRequest);
    }
}
```

---

## 3. PHP AuthUrlBuilder — Auth URL Oluşturma

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * AuthUrlBuilder — Auth redirect URL oluşturma (SRP).
 *
 * ADR-043 uyumlu: Auth domain scheme DomainConfig'den alınır.
 */
final class AuthUrlBuilder
{
    public function __construct(
        private readonly DomainConfig     $domainConfig,
        private readonly PageRouterHelper $authHelper,
    ) {}

    public function redirectAuth(string $path, ?string $returnPath, bool $isSpaRequest): array
    {
        $target = $this->buildAuthUrl($path, $returnPath);
        header('X-Auth-Required: true');
        header('X-Auth-Status: unauthenticated');
        return $isSpaRequest ? RouteResult::forbidden($target) : RouteResult::redirect($target);
    }

    private function buildAuthUrl(string $path, ?string $returnPath = null): string
    {
        $scheme     = $this->domainConfig->isHttps() ? 'https' : 'http';
        $authDomain = AuthRouteConfig::getAuthUrl($scheme);
        return AuthRouteConfig::buildAuthRedirectUrl($path, $returnPath ?? '', $authDomain, $this->buildCallbackDomain());
    }

    private function buildCallbackDomain(): string
    {
        $scheme = $this->domainConfig->isHttps() ? 'https' : 'http';
        $host   = $this->domainConfig->getHost() ?? 'home.coremusic.net';
        $port   = $this->domainConfig->getPort();
        $suffix = ($port !== 80 && $port !== 443) ? ':' . $port : '';
        return $scheme . '://' . $host . $suffix;
    }
}
```

---

## 4. PHP PageRouterHelper — Auth Helper

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * PageRouterHelper — Auth guard helper with injectable session provider.
 *
 * SOLID DIP: Session erişimi SessionProviderInterface üzerinden lazy loading ile yapılır.
 */
final class PageRouterHelper
{
    private readonly SessionProviderInterface $sessionProvider;

    public function __construct(?SessionProviderInterface $sessionProvider = null)
    {
        $this->sessionProvider = $sessionProvider ?? new SessionProvider();
    }

    public function checkAuthenticated(): bool
    {
        if ($this->isTestBypassActive()) return true;
        return !empty($this->getSession()['MM_UserID']);
    }

    public function checkRole(string $role): bool
    {
        return ($this->getSession()['MM_UserRole'] ?? '') === $role;
    }

    public function checkPermission(string $permission): bool
    {
        return in_array($permission, (array)($this->getSession()['MM_Permissions'] ?? []), true);
    }

    private function getSession(): array { return $this->sessionProvider->getSession(); }

    private function isTestBypassActive(): bool
    {
        return (defined('TEST_MODE') && TEST_MODE === true)
            || (defined('FORCE_AUTH_BYPASS') && FORCE_AUTH_BYPASS === true);
    }
}
```

---

## 5. JS GuardPipeline — Client-Side Guards

```javascript
/**
 * GuardPipeline — Client-side guard yönetimi.
 *
 * Her guard: (url, config) => boolean | { redirect: string }
 */
export default class GuardPipeline {
    #guards = [];
    #logger;

    constructor(logger) { this.#logger = logger; }

    register(fn) { this.#guards.push(fn); }

    async run(url, config) {
        for (const guard of this.#guards) {
            const result = await guard(url, config);
            if (result === false) {
                this.#logger.warn('GuardPipeline', 'guard_rejected', { url, guard: guard.name });
                return false;
            }
            if (result && typeof result === 'object' && result.redirect) {
                return result;
            }
        }
        return true;
    }
}
```

### JS guards.js — Guard Tanımları

```javascript
/**
 * authGuard — Auth gerektiren sayfalar için guard.
 *
 * Protected route'a giriş yapılmamış kullanıcı → /login redirect.
 */
export function authGuard(url, config) {
    const protectedRoutes = config.protectedRoutes || [];
    const isProtected = protectedRoutes.some(route => url.startsWith('/' + route));
    if (isProtected && !config.user?.id) {
        return { redirect: '/login' };
    }
    return true;
}

/**
 * roleGuard — Rol bazlı guard (gelecek için hazır).
 */
export function roleGuard(url, config) {
    return true;
}

/**
 * permissionGuard — İzin bazlı guard (gelecek için hazır).
 */
export function permissionGuard(url, config) {
    return true;
}
```

---

## 6. Guard Akış Diyagramı

```
PHP Tarafı (Server-Side):
  Request → PageRouterKernel → Middleware Pipeline → PageRouter::dispatch()
    → AuthGuard::check()
        ├── 1. checkAuthRequired()     → redirect/forbidden/null
        ├── 2. checkRole()             → forbidden/null
        ├── 3. checkPermission()       → forbidden/null
        ├── 4. checkAuthRedirectRoute()→ redirect/null
        ├── 5. checkAuthenticatedUserOnAuthPage() → redirect/forbidden/null
        └── 6. checkLogout()           → redirect/null
    → RouteResult → ResponseEmitter

JS Tarafı (Client-Side):
  Link Click → Router::navigate()
    → GuardPipeline::run()
        ├── 1. authGuard()             → { redirect: '/login' } / true
        ├── 2. roleGuard()             → true
        └── 3. permissionGuard()       → true
    → NavigationOrchestrator → Fetch → ContentPatcher
```

---

## 7. Guard Karşılaştırma

| Özellik | PHP AuthGuard | JS GuardPipeline |
|---------|---------------|------------------|
| **Çalışma zamanı** | Server-side | Client-side |
| **Kontrol sayısı** | 6 | 3 (şimdilik) |
| **Session erişimi** | `$_SESSION` (lazy) | `config.user` (inject) |
| **Redirect yöntemi** | HTTP redirect / JSON forbidden | SPA navigate |
| **CSRF koruması** | CsrfMiddleware | CsrfSyncManager |
| **Rate limiting** | RateLimiterMiddleware | Yok |
| **Rol kontrolü** | `checkRole()` | `roleGuard()` (hazır) |
| **İzin kontrolü** | `checkPermission()` | `permissionGuard()` (hazır) |

---

## 8. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **PHP guard başarısız** | JSON 403 + redirect URL | ADR-043 |
| **JS guard başarısız** | SPA navigate to /login | ADR-021 |
| **Auth servisi down** | PHP: 500 error / JS: error state | — |
| **Session timeout** | PHP: idle timeout check / JS: auth boundary | ADR-011 |
| **Bypass auth** | `TEST_MODE` / `FORCE_AUTH_BYPASS` | ADR-008 |
| **Cross-subdomain** | Cookie domain `.coremusic.net` | ADR-043 |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[spa-router]] | PHP SPA PageRouter |
| [[js-router]] | JS SPA Router |
| [[route-config]] | Route yapısı + SpaRoute DTO |
| [[ADR-008-bypass-auth-middleware]] | Bypass auth |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |

---

## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 008, 021, 043, 083 |
| **Zero Hallucination** | ✅ (referans proje tabanlı) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-16
**Mode:** Red Team · Human Mode · Truth Mode
