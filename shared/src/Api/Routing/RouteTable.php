<?php
declare(strict_types=1);

/**
 * Method-aware route tablosu (ADR-084 Contract First / ADR-020 §1.1-B.1).
 *
 * @file RouteTable.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Routing;

/**
 * API route kayıtları — GET-only prefix tablosu yerine METHOD-aware kayıt defteri.
 *
 * Üç ayrık sonuç üretir:
 *   - ok                  → route + method eşleşti (handler çağrılır)
 *   - method_not_allowed  → yol biliniyor, method sunulmuyor → 405 + Allow
 *   - not_found           → yol tabloda yok → 404 (prefix altı tanımsız alt
 *                           yollar dahil — bkz. find()/isDeclared())
 *
 * Route tanımı pipeline'a `_route` olarak aktarılır; Authentication /
 * Authorization / RequestValidation / ResponseNormalization aynı kaydı okur.
 */
final class RouteTable
{
    public const STATUS_OK = 'ok';
    public const STATUS_METHOD_NOT_ALLOWED = 'method_not_allowed';
    public const STATUS_NOT_FOUND = 'not_found';

    /**
     * @var array<string, list<array{path: string, route: array<string, mixed>}>>
     */
    private array $routes = [];

    /**
     * Tek bir route kaydı ekle.
     *
     * @param array<string, mixed> $route
     */
    public function add(string $method, string $path, array $route = []): self
    {
        $method = strtoupper($method);
        $path   = self::normalize($path);

        $route['method']   = $method;
        $route['path']     = $path;
        $route['public']   = (bool) ($route['public'] ?? false);
        $route['implemented'] = (bool) ($route['implemented'] ?? true);
        $route['cacheable']   = (bool) ($route['cacheable'] ?? false);
        $route['validation']  = isset($route['validation']) && is_array($route['validation'])
            ? $route['validation']
            : null;
        $route['requiredRole']       = $route['requiredRole'] ?? null;
        $route['requiredPermission'] = $route['requiredPermission'] ?? null;
        $route['cacheTtl']           = (int) ($route['cacheTtl'] ?? 0);

        $this->routes[$method][] = ['path' => $path, 'route' => $route];

        return $this;
    }

    /**
     * Yapılandırma dosyasından tablo kur.
     *
     * Biçim: ['GET' => ['/api/v1/auth' => ['service' => 'auth', ...]], 'POST' => [...]]
     *
     * @param array<string, array<string, array<string, mixed>>> $definition
     */
    public static function fromArray(array $definition): self
    {
        $table = new self();

        foreach ($definition as $method => $paths) {
            if (!is_array($paths)) {
                continue;
            }
            foreach ($paths as $path => $route) {
                $table->add((string) $method, $path, $route);
            }
        }

        return $table;
    }

    /**
     * Gateway constructor'a route verilmediğinde kullanılan varsayılan tablo
     * (eski hardcoded GET prefix kayıtları — kök uçlar aynen eşleşir;
     * tanımsız alt yollar 404).
     */
    public static function defaults(): self
    {
        return self::fromArray([
            'GET' => [
                '/api/v1/auth'     => ['service' => 'auth',     'handler' => 'authController'],
                '/api/v1/user'     => ['service' => 'user',     'handler' => 'userController'],
                '/api/v1/music'    => ['service' => 'music',    'handler' => 'musicController'],
                '/api/v1/playlist' => ['service' => 'playlist', 'handler' => 'playlistController'],
                '/api/v1/media'    => ['service' => 'media',    'handler' => 'mediaController'],
                '/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],
            ],
        ]);
    }

    /**
     * URI + method eşleşmesi.
     *
     * @return array{status: string, route: array<string, mixed>|null, allow: string[]}
     */
    public function match(string $uri, string $method): array
    {
        $uri    = self::normalize($uri);
        $method = strtoupper($method);

        $matched = $this->find($method, $uri);

        if ($matched !== null) {
            if ($matched['route']['implemented']) {
                return ['status' => self::STATUS_OK, 'route' => $matched['route'], 'allow' => []];
            }

            // Route tanındı ama controller'ı henüz yok (Faz 1b öncesi):
            // 404 DEĞİL — 405 + Allow (RFC 9110 §15.5.6).
            return [
                'status' => self::STATUS_METHOD_NOT_ALLOWED,
                'route'  => $matched['route'],
                'allow'  => $this->allowedMethods($uri),
            ];
        }

        $allow = $this->allowedMethods($uri);
        if ($allow !== []) {
            return ['status' => self::STATUS_METHOD_NOT_ALLOWED, 'route' => null, 'allow' => $allow];
        }

        return ['status' => self::STATUS_NOT_FOUND, 'route' => null, 'allow' => []];
    }

    /**
     * @return list<array{method: string, path: string, route: array<string, mixed>}>
     */
    public function all(): array
    {
        $flat = [];
        foreach ($this->routes as $method => $entries) {
            foreach ($entries as $entry) {
                $flat[] = ['method' => $method, 'path' => $entry['path'], 'route' => $entry['route']];
            }
        }

        return $flat;
    }

    /**
     * Bir yolu sunan (implemented) methodların Allow listesi.
     *
     * @return string[]
     */
    private function allowedMethods(string $uri): array
    {
        $allow = [];

        foreach (array_keys($this->routes) as $method) {
            $entry = $this->find($method, $uri);
            if ($entry !== null && $entry['route']['implemented']) {
                $allow[] = $method;
            }
        }

        if ($allow !== []) {
            $allow[] = 'OPTIONS';
        }

        return $allow;
    }

    /**
     * Exact eşleşme önce; prefix fallback yalnızca yol TABLODA kayıtlıysa.
     *
     * Eski davranış: `str_starts_with` ile prefix'e düşen HER alt yol (ör.
     * `/api/v1/auth/does-not-exist`) collection route'unu geri döndürüyordu →
     * private route muamelesi görüp authenticate edilmemiş istemciye **401**
     * (404 değil) dönüyordu. Sözleşme ihlali: tanımsız yol = rota yok.
     *
     * Yeni ayrım (ADR-084 Contract First):
     *   - exact kayıt (istenen methodta)        → route (405/401 davranışı değişmez)
     *   - yol tabloda BİR methodda kayıtlıysa   → prefix fallback (davranış değişmez:
     *     bilinen POST-only uçlar eski gibi çözümlemeye devam eder)
     *   - tabloda TANIMSIZ alt yol              → null → 404 (Allow'sız)
     *
     * 405 ayrımı bozulmaz: yol biliniyor ama method sunulmuyorsa `match()`
     * `allowedMethods()` üzerinden yine 405 + Allow üretir (RFC 9110 §15.5.6).
     *
     * @return array{path: string, route: array<string, mixed>}|null
     */
    private function find(string $method, string $uri): ?array
    {
        $entries = $this->routes[$method] ?? [];

        foreach ($entries as $entry) {
            if ($entry['path'] === $uri) {
                return $entry;
            }
        }

        // Tanımsız yol: prefix'e fallback YOK → 404 (eski: 401/405 üretirdi).
        if (!$this->isDeclared($uri)) {
            return null;
        }

        // Segment sınırı: `/api/v1/auth` yalnızca gerçek alt yollarda eşleşir
        // (`/api/v1/authorize` gibi).
        foreach ($entries as $entry) {
            if (str_starts_with($uri, $entry['path'] . '/')) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * Yol tabloda (herhangi bir methodta) exact kayıt olarak tanımlı mı?
     *
     * "Bilinen yol" tanımı: exact kayıt varsa method eksikliği 405'e,
     * hiç kayıt yoksa 404'e gider.
     */
    private function isDeclared(string $uri): bool
    {
        foreach ($this->routes as $entries) {
            foreach ($entries as $entry) {
                if ($entry['path'] === $uri) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function normalize(string $path): string
    {
        $queryPos = strpos($path, '?');
        if ($queryPos !== false) {
            $path = substr($path, 0, $queryPos);
        }

        $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }
}
