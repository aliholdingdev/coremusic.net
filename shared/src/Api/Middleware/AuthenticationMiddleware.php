<?php
declare(strict_types=1);

/**
 * Authentication Middleware for API requests.
 *
 * @file AuthenticationMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Middleware;

use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Api\ApiResponse;
use CoreMusic\Api\Routing\RouteTable;
use CoreMusic\Security\JwtService;

/**
 * Authentication Middleware for hybrid session/JWT authentication.
 *
 * P1-9 (B-F-01): Bearer dalı artık gerçek RS256 doğrulama yapar (JwtService);
 * imza/exp/iss/aud geçerli VE (varsa) jti revocation kontrolü sağlarsa
 * `_auth_user.method = 'jwt'` ile devam eder. Aksi halde 401 (fail-closed).
 *
 * Onarım 2026-10-24: public route'larda optional authentication — başarılı
 * deneme `_auth_user` set eder (servisler anonymous + authenticated
 * birlikte destekleyebilir), başarısızlık 401 DEĞİL anonim akışla devam
 * eder. Non-public yol fail-closed davranış korunur.
 */
final class AuthenticationMiddleware
{
    /**
     * Auth'sız (public) route önekleri. AuthorizationMiddleware de bunu paylaşır.
     *
     * F19: ölü `/api/v1/public` deseni çıkarıldı — `api.coremusic.net/config/routes.php`
     * içinde bu yola ait route YOK (grep kanıtı: 0 kayıt), yalnız ADR-020 tarihsel
     * metninde geçiyor. Kalıcı public'lik route contract'taki `public => true`
     * alanından gelir; bu liste yalnızcontract'ta alan bulunmayan uçlar için
     * fallback'tir.
     *
     * NOT (batch #2): deseni `routes.php`'den türetmek shared → api bağımlılığı
     * yaratır (yasak katman yönü) — bu yüzden derleme zamanı okuma YAPILMAZ.
     *
     * @var string[]
     */
    public const PUBLIC_ROUTE_PATTERNS = [
        '/api/v1/auth/login',
        '/api/v1/auth/register',
        '/api/v1/auth/forgot-password',
    ];

    /**
     * @param \Closure(string): bool|null $accessTokenValidator
     *        jti → hâlâ geçerli mi? (user_tokens revocation; null = stateless kabul,
     *        yalnız exp sınırıyla — API index her zaman bağlar)
     */
    public function __construct(
        private readonly ISessionManager $sessionManager,
        private readonly ?JwtService $jwt = null,
        private readonly ?\Closure $accessTokenValidator = null,
    ) {}

    /**
     * Process authentication for API requests.
     */
    public function __invoke(array $request, callable $next): array
    {
        // 404 / 405: handler hiç çalışmayacak (yanıtı Gateway üretir) → auth
        // kontrolü burada 401 ile sonlanmamalı; bilinmeyen yol 404, tanımlı
        // olmayan method 405 + Allow döner (RFC 9110).
        $routeStatus = $request['_route_status'] ?? null;
        if ($routeStatus === RouteTable::STATUS_NOT_FOUND
            || $routeStatus === RouteTable::STATUS_METHOD_NOT_ALLOWED
        ) {
            return $next($request);
        }

        // Onarım 2026-10-24 — optional authentication: public route'larda da
        // önce mevcut auth denemesi yapılır (session → Bearer JWT). Başarılıysa
        // `_auth_user` set edilir; BAŞARISIZSA 401 DEĞİL anonim akışla devam
        // edilir. Ek güvenlik kazancı: session-authenticated istemcide
        // `_auth_user.method = 'session'` olduğu için CsrfMiddleware gate'i
        // public state-changing uçlarda (ör. POST auth/set-gender) doğru
        // devreye girer (A01:2011 kapatması).
        if ($this->isPublicRoute($request)) {
            $authUser = $this->tryAuthenticate();
            if ($authUser !== null) {
                $request['_auth_user'] = $authUser;
            }
            return $next($request);
        }

        // Non-public route: auth başarısızsa 401 UNAUTHORIZED (fail-closed) —
        // mevcut davranış birebir korunur.
        $authUser = $this->tryAuthenticate();
        if ($authUser !== null) {
            $request['_auth_user'] = $authUser;
            return $next($request);
        }

        // Not authenticated
        return ApiResponse::error(
            'UNAUTHORIZED',
            'Authentication required',
            401
        );
    }

    /**
     * Onarım 2026-10-24: mevcut istemci auth denemesi (optional auth çekirdeği).
     *
     * Önce session (ISessionManager::isAuthenticated), sonra Bearer JWT
     * (P1-9: gerçek RS256 doğrulama + jti revocation). Herhangi biri
     * başarılıysa `_auth_user` dizisi döner; aksi halde null — çağıran
     * public'te anonim devam eder, non-public'te 401 fail-closed üretir.
     *
     * @return array{id: string, authenticated: bool, method: string, roles: string[], permissions: string[]}|null
     */
    private function tryAuthenticate(): ?array
    {
        // Check if user is authenticated via session
        if ($this->sessionManager->isAuthenticated()) {
            return [
                'id'            => $this->sessionManager->getUserId(),
                'authenticated' => true,
                'method'        => 'session',
                // RBAC (AuthorizationMiddleware requiredRole/requiredPermission):
                'roles'         => self::normalizeList($this->sessionManager->get('MM_UserRole')),
                'permissions'   => self::normalizeList($this->sessionManager->get('MM_Permissions')),
            ];
        }

        // Check for JWT token in Authorization header (P1-9: gerçek RS256 doğrulama)
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($authHeader, 'Bearer ') && $this->jwt !== null) {
            $claims = $this->jwt->validate(substr($authHeader, 7));

            if ($claims !== null) {
                $revoked = $this->accessTokenValidator !== null
                    && !($this->accessTokenValidator)((string) $claims['jti']);
                if (!$revoked) {
                    return [
                        'id'            => (string) $claims['sub'],
                        'authenticated' => true,
                        'method'        => 'jwt',
                        // RBAC (AuthorizationMiddleware requiredRole/requiredPermission):
                        'roles'         => self::normalizeList($claims['roles'] ?? null),
                        'permissions'   => self::normalizeList($claims['permissions'] ?? null),
                    ];
                }
                // revocation edilmiş jti → null döner (fail-closed)
            }
        }

        return null;
    }

    /**
     * RBAC liste normalizasyonu: tek string → tek elemanlı dizi (boş değilse),
     * dizi → yalnız string elemanlar (sıra korunur), diğer tüm tipler → [].
     *
     * @return string[]
     */
    private static function normalizeList(mixed $value): array
    {
        if (is_string($value)) {
            return $value === '' ? [] : [$value];
        }

        if (is_array($value)) {
            return array_values(array_filter($value, 'is_string'));
        }

        return [];
    }

    /**
     * Check if the route is public (no auth required).
     *
     * Kaynak: pipeline'a aktarılan `_route['public']` (RouteTable) VEYA
     * `PUBLIC_ROUTE_PATTERNS` — ikisi de public ise erişim açıktır
     * (pattern listesi route eşleşmediğinde fallback olarak kalır).
     */
    private function isPublicRoute(array $request): bool
    {
        $route = $request['_route'] ?? null;
        if (is_array($route) && !empty($route['public'])) {
            return true;
        }

        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        $uri  = is_string($path) && $path !== '' ? $path : '/';

        foreach (self::PUBLIC_ROUTE_PATTERNS as $pattern) {
            // Segment sınırı: '/api/v1/auth/login-evil' gibi önek-uydurmalar public sayılmaz.
            if ($uri === $pattern || str_starts_with($uri, $pattern . '/')) {
                return true;
            }
        }

        return false;
    }
}
