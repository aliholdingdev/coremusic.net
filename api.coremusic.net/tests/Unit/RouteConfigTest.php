<?php
declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

use CoreMusic\Api\Middleware\RequestValidationMiddleware;
use CoreMusic\Api\Middleware\Validator;
use CoreMusic\Api\Routing\RouteTable;
use PHPUnit\Framework\TestCase;

/**
 * Faz 1b route sözleşmesi (ADR-084 Contract First · ADR-020 §1.1-B.1):
 *   - 6 POST auth ucu implemented=true + AuthController action'ı (405 kalktı)
 *   - doğrulama kuralları tanımlı (422 + error.fields)
 *   - GET /api/v1/auth/login public=true, GET /api/v1/auth/me private
 *   - set-gender public (gender gate girişten önce çalışır)
 */
final class RouteConfigTest extends TestCase
{
    /** @var array<string, array<string, array<string, mixed>>> */
    private array $routes;

    private RouteTable $table;

    protected function setUp(): void
    {
        $rawRoutes = require dirname(__DIR__, 2) . '/config/routes.php';
        $this->routes = $this->replaceVersionPlaceholder($rawRoutes, 'v1');
        $this->table  = RouteTable::fromArray($this->routes);
    }

    /**
     * Route dizisindeki {version} placeholder'ını gerçek versiyonla değiştirir.
     *
     * @param array<string, mixed> $routes
     * @param string $version
     * @return array<string, mixed>
     */
    private function replaceVersionPlaceholder(array $routes, string $version): array
    {
        $result = [];
        foreach ($routes as $method => $routesByMethod) {
            $result[$method] = [];
            foreach ($routesByMethod as $path => $routeConfig) {
                $newPath = str_replace('{version}', $version, $path);
                $result[$method][$newPath] = $routeConfig;
            }
        }
        return $result;
    }

    public function testRoutesFileDeclaresBothMethods(): void
    {
        $this->assertArrayHasKey('GET', $this->routes);
        $this->assertArrayHasKey('POST', $this->routes);
    }

    public function testSixPostAuthRoutesAreImplementedWithActions(): void
    {
        $expected = [
            '/api/v1/auth/login'          => 'login',
            '/api/v1/auth/register'       => 'register',
            '/api/v1/auth/forgot-password' => 'forgotPassword',
            '/api/v1/auth/reset-password' => 'resetPassword',
            '/api/v1/auth/set-gender'     => 'setGender',
            '/api/v1/auth/logout'         => 'logout',
        ];

        foreach ($expected as $path => $action) {
            $this->assertArrayHasKey($path, $this->routes['POST'], "{$path} POST kaydı tanımlı olmalı");
            $this->assertTrue(
                (bool) ($this->routes['POST'][$path]['implemented'] ?? false),
                "{$path} implemented=true olmalı (Faz 1b — AuthController bağlı)"
            );
            $this->assertSame(
                $action,
                $this->routes['POST'][$path]['action'] ?? null,
                "{$path} action anahtarı AuthController dispatch'i için zorunlu"
            );
        }
    }

    public function testGetLoginRouteIsPublic(): void
    {
        $this->assertTrue((bool) ($this->routes['GET']['/api/v1/auth/login']['public'] ?? false));
    }

    public function testPostLoginRouteIsPublicAndPostLogoutIsPrivate(): void
    {
        $this->assertTrue((bool) ($this->routes['POST']['/api/v1/auth/login']['public'] ?? false));
        $this->assertFalse((bool) ($this->routes['POST']['/api/v1/auth/logout']['public'] ?? true));
    }

    public function testSetGenderIsPublicBecauseGenderGateRunsPreLogin(): void
    {
        $this->assertTrue((bool) ($this->routes['POST']['/api/v1/auth/set-gender']['public'] ?? false));
    }

    public function testPostLoginIsRoutableNowAndMethodMismatchStillYields405(): void
    {
        $ok = $this->table->match('/api/v1/auth/login', 'POST');
        $this->assertSame(RouteTable::STATUS_OK, $ok['status'], 'POST login artık handlera gider (405 değil)');
        $this->assertSame('login', $ok['route']['action'] ?? null);

        // Yol biliniyor, method sunulmuyor → 405 + Allow (RFC 9110 §15.5.6)
        // GET /auth/login implemented=false → Allow listesinde GET YOK (yalnız POST + OPTIONS)
        $result = $this->table->match('/api/v1/auth/login', 'PUT');
        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $result['status']);
        $this->assertNotContains('GET', $result['allow'], 'GET login POST-only → Allow listesinde yok');
        $this->assertContains('POST', $result['allow']);
        $this->assertContains('OPTIONS', $result['allow']);
        $this->assertNotContains('PUT', $result['allow']);
    }

    public function testUnknownPathIsNotFoundNot405(): void
    {
        $this->assertSame(RouteTable::STATUS_NOT_FOUND, $this->table->match('/api/v1/unknown', 'GET')['status']);

        // Gerçek tetikleyici: GET prefix kaydının (/api/v1/auth) altındaki
        // TANIMSIZ yol. Eski davranış: str_starts_with prefix fallback →
        // private route → authenticate edilmemiş istemciye 401 (sözleşme ihlali).
        // Sözleşme: tanımsız alt yol = rota yok → 404, route null, Allow'sız.
        $result = $this->table->match('/api/v1/auth/does-not-exist', 'GET');
        $this->assertSame(RouteTable::STATUS_NOT_FOUND, $result['status']);
        $this->assertNull($result['route'], 'Prefix route\'una fallback yapılmamalı');
        $this->assertSame([], $result['allow'], '404 yanıtında Allow başlığı olmamalı');

        // Aynı kural POST ve diğer prefix aileleri için de geçerli
        $this->assertSame(RouteTable::STATUS_NOT_FOUND, $this->table->match('/api/v1/auth/does-not-exist', 'POST')['status']);
        $this->assertSame(RouteTable::STATUS_NOT_FOUND, $this->table->match('/api/v1/user/does-not-exist', 'GET')['status']);
    }

    public function testGetLoginIsMethodNotAllowed405(): void
    {
        $result = $this->table->match('/api/v1/auth/login', 'GET');

        // GET login POST-only → implemented=false → 405 + Allow (RFC 9110 §15.5.6)
        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $result['status']);
        $this->assertContains('POST', $result['allow']);
        $this->assertContains('OPTIONS', $result['allow']);
        $this->assertNotContains('GET', $result['allow']);
        $this->assertFalse((bool) ($result['route']['implemented'] ?? true));
        $this->assertTrue((bool) ($result['route']['public'] ?? false));
    }

    public function testGetMeRouteIsImplementedAndPrivate(): void
    {
        $result = $this->table->match('/api/v1/auth/me', 'GET');

        $this->assertSame(RouteTable::STATUS_OK, $result['status']);
        $this->assertSame('me', $result['route']['action'] ?? null);
        $this->assertFalse((bool) ($result['route']['public'] ?? true));
        $this->assertTrue((bool) ($result['route']['implemented'] ?? false));
    }

    public function testAuthPostRoutesCarryValidationRules(): void
    {
        foreach (['/api/v1/auth/login', '/api/v1/auth/register', '/api/v1/auth/forgot-password', '/api/v1/auth/reset-password', '/api/v1/auth/set-gender'] as $path) {
            $rules = $this->routes['POST'][$path]['validation'] ?? null;
            $this->assertIsArray($rules, "{$path} validation kuralı zorunlu (422 + error.fields)");
            $this->assertNotEmpty($rules, "{$path} validation kural seti boş olamaz");
        }

        // Şifre gücü: ≥ 8 (ADR-020 — kullanıcı kararıyla 12 → 8)
        // login ve register kuralında da görünmeli
        foreach (['/api/v1/auth/login', '/api/v1/auth/register', '/api/v1/auth/reset-password'] as $path) {
            $this->assertSame(
                8,
                $this->routes['POST'][$path]['validation']['password']['min'] ?? null,
                "{$path} password min=8 olmalı"
            );
        }

        // Zorunlu alanlar (Faz 3a UI sözleşmesi)
        $register = $this->routes['POST']['/api/v1/auth/register']['validation'];
        foreach (['username', 'email', 'password', 'gender', 'agree_terms'] as $field) {
            $this->assertTrue((bool) ($register[$field]['required'] ?? false), "register.{$field} required olmalı");
        }
        $this->assertSame(
            ['male', 'female', 'neutral'],
            $register['gender']['in'] ?? null
        );
    }

    /**
     * Şifre tabanı sınırı (12 → 8 kararı):
     *   tam 8 karakter GEÇERLİ · 7 karakter 422 VALIDATION_ERROR + error.fields.password
     * Gerçek route kuralı (config/routes.php) + gerçek kural motoru (Validator)
     * + gerçek middleware (RequestValidationMiddleware) üzerinden koşulur.
     */
    public function testPasswordBoundary8CharsPassesAnd7CharsYields422(): void
    {
        $route  = $this->routes['POST']['/api/v1/auth/register'];
        $rules  = $route['validation'];
        $base   = [
            'username'    => 'demo',
            'email'       => 'demo@example.com',
            'gender'      => 'female',
            'agree_terms' => true,
        ];

        // 1) 8 karakter → kural motoru password hatası üretmez
        $errors8 = (new Validator())->validate($base + ['password' => 'abcd1234'], $rules);
        $this->assertArrayNotHasKey('password', $errors8, 'Tam 8 karakterlik şifre geçerli olmalı');

        // 2) 7 karakter → 422 VALIDATION_ERROR + error.fields.password
        http_response_code(200);
        $middleware = new RequestValidationMiddleware();
        $response   = $middleware(
            [
                'method' => 'POST',
                'server' => ['CONTENT_TYPE' => 'application/json'],
                'body'   => json_encode($base + ['password' => 'abc1234'], JSON_THROW_ON_ERROR),
                '_route' => $route,
            ],
            static fn (array $request): array => ['passthrough' => true]
        );

        $this->assertSame('VALIDATION_ERROR', $response['error']['code'] ?? null, '7 karakter 422 VALIDATION_ERROR');
        $this->assertArrayNotHasKey('passthrough', $response, 'Kısa şifre handler\'a geçmemeli');
        $this->assertSame(422, http_response_code());
        $this->assertStringContainsString('8', (string) ($response['error']['fields']['password'] ?? ''));
    }

    /**
     * PRD-API-006: controller'ı olmayan rota kayıtlı kalmaz.
     * media/download handler'ları include/ altında yoktu ve hiçbir istemci kullanmıyordu
     * (placeholder 200 üretiyordu) → rota kaldırıldı; 404 beklenir.
     */
    public function testMediaAndDownloadRoutesWithoutControllerAreNotRegistered(): void
    {
        foreach (['/api/v1/media', '/api/v1/download'] as $path) {
            $result = $this->table->match($path, 'GET');
            $this->assertSame(RouteTable::STATUS_NOT_FOUND, $result['status'], "{$path} kayıtlı olmamalı");
        }
    }

    public function testLogoutHasNoValidationRulesSoBodylessPostPasses(): void
    {
        $this->assertArrayNotHasKey('validation', $this->routes['POST']['/api/v1/auth/logout']);
    }

    // ─── Faz 4: route kayıtları implemented:true (handler dispatch hazır) ───

    /**
     * Yeni uçlar implemented:true — handler dispatch'e hazır (Faz 4);
     * action anahtarları ApiContainer match'ine bağlanır.
     */
    public function testPhase3NewRoutesAreNowImplementedWithActions(): void
    {
        $getExpectations = [
            '/api/v1/music/tracks' => 'tracks',
            '/api/v1/music/search' => 'search',
            '/api/v1/music/track'  => 'show',
            '/api/v1/playlist'     => 'index',
            '/api/v1/user/profile' => 'profile',
        ];
        foreach ($getExpectations as $path => $action) {
            $result = $this->table->match($path, 'GET');
            $this->assertSame(
                RouteTable::STATUS_OK,
                $result['status'],
                "{$path} Faz 4 implemented:true — STATUS_OK beklenir"
            );
            $this->assertSame($action, $result['route']['action'] ?? null, "{$path} action dispatch'e hazır");
            $this->assertTrue((bool) ($result['route']['implemented'] ?? false));
        }

        foreach (['/api/v1/playlist' => 'create', '/api/v1/playlist/tracks' => 'addTrack'] as $path => $action) {
            $result = $this->table->match($path, 'POST');
            $this->assertSame(RouteTable::STATUS_OK, $result['status'], "{$path} POST implemented:true");
            $this->assertSame($action, $result['route']['action'] ?? null);
        }

        $put = $this->table->match('/api/v1/user/profile', 'PUT');
        $this->assertSame(RouteTable::STATUS_OK, $put['status']);
        $this->assertSame('updateProfile', $put['route']['action'] ?? null);
    }

    public function testPhase3PlaylistRoutesCarryValidationRules(): void
    {
        $create = $this->routes['POST']['/api/v1/playlist']['validation'] ?? null;
        $this->assertIsArray($create, 'POST /api/v1/playlist validation kural seti (Faz 4 422 kapısı)');
        $this->assertTrue((bool) ($create['name']['required'] ?? false));

        $addTrack = $this->routes['POST']['/api/v1/playlist/tracks']['validation'] ?? null;
        $this->assertIsArray($addTrack);
        $this->assertTrue((bool) ($addTrack['playlist_id']['required'] ?? false));
        $this->assertTrue((bool) ($addTrack['track_id']['required'] ?? false));
    }

    public function testPhase3PrivateRoutesAreNotPublic(): void
    {
        $this->assertFalse((bool) ($this->routes['GET']['/api/v1/user/profile']['public'] ?? true));
        $this->assertFalse((bool) ($this->routes['POST']['/api/v1/playlist']['public'] ?? true));
        $this->assertFalse((bool) ($this->routes['PUT']['/api/v1/user/profile']['public'] ?? true));
        $this->assertTrue((bool) ($this->routes['GET']['/api/v1/music/tracks']['public'] ?? false));
    }

    // ─── F2: action'sız prefix GET kayıtları artık 200 placeholder ÜRETMEZ ───

    /**
     * F2: `/api/v1/{auth,user,music}` GET prefix kayıtlarında `action` yoktu ve
     * `implemented:true` kalınca Gateway::invokeHandler action'sız route için
     * 200 "Handler not implemented yet" döndürüyordu (sözleşme ihlali).
     * Artık `implemented:false` → RouteTable 405 + Allow (RFC 9110 §15.5.6).
     */
    public function testPrefixCollectionRoutesAre405NotPlaceholder(): void
    {
        foreach (['/api/v1/auth', '/api/v1/user', '/api/v1/music'] as $path) {
            $result = $this->table->match($path, 'GET');

            $this->assertSame(
                RouteTable::STATUS_METHOD_NOT_ALLOWED,
                $result['status'],
                "{$path} implemented:false → 405 (200 placeholder YASAK)"
            );
            $this->assertFalse((bool) ($result['route']['implemented'] ?? true), "{$path} implemented=false olmalı");
            $this->assertArrayNotHasKey('action', (array) $result['route'], "{$path} action'sız → handler'a gidemez");
        }

        // Not: '/api/v1/playlist' anahtarı PHP'de son kayıt kazanımıyla daha
        // alttaki exact GET kaydı (action: index) tarafından ezilir → o satır
        // fiilen ölüdür, STATUS_OK döner (dokümante edilen beklenen davranış).
        $playlist = $this->table->match('/api/v1/playlist', 'GET');
        $this->assertSame(RouteTable::STATUS_OK, $playlist['status']);
        $this->assertSame('index', $playlist['route']['action'] ?? null);
    }

    // ─── F3: login identity-OR — route'da email zorunlu DEĞİL ───

    /**
     * Controller identity-OR okur (`$body['email'] ?? ['identity'] ?? ['username']`)
     * ve boş identity AuthService::login'de 422 üretir. email `required`
     * OLUNCA username-only login gateway'de 422 ile ölüyordu → route'da opsiyonel.
     */
    public function testLoginValidationAcceptsUsernameOnlyIdentity(): void
    {
        $rules = $this->routes['POST']['/api/v1/auth/login']['validation'];

        $this->assertArrayNotHasKey('required', $rules['email'] ?? [], 'email required OLMAMALI (identity-OR)');

        // username-only gövde kural motorundan temiz geçmeli
        $errors = (new Validator())->validate(
            ['username' => 'demo', 'password' => 'uzun-sifre-123'],
            $rules
        );
        $this->assertSame([], $errors, 'username-only login route doğrulamasında 422 OLMAMALI');

        // password kuralı (min 8) ve gender in-listi korunur → güvenlik açılmadı
        $short = (new Validator())->validate(['email' => 'a@b.co', 'password' => 'kisa'], $rules);
        $this->assertArrayHasKey('password', $short);
        $badGender = (new Validator())->validate(
            ['email' => 'a@b.co', 'password' => 'uzun-sifre-123', 'gender' => 'robot'],
            $rules
        );
        $this->assertArrayHasKey('gender', $badGender);
    }

    // ─── F16: GET /music/track → id zorunlu (422 + error.fields) ───

    public function testMusicTrackIdIsRequiredAtRouteLevel(): void
    {
        $route = $this->routes['GET']['/api/v1/music/track'];
        $this->assertTrue((bool) ($route['validation']['id']['required'] ?? false), 'id required olmalı');

        http_response_code(200);
        $middleware = new RequestValidationMiddleware();
        $denied = $middleware(
            [
                'method' => 'GET',
                'query'  => [],          // ?id yok
                '_route' => $route,
            ],
            static fn (array $request): array => ['passthrough' => true]
        );

        $this->assertSame(422, http_response_code());
        $this->assertSame('VALIDATION_ERROR', $denied['error']['code'] ?? null);
        $this->assertArrayHasKey('id', (array) ($denied['error']['fields'] ?? []));
        $this->assertArrayNotHasKey('passthrough', $denied);

        http_response_code(200);
        $allowed = $middleware(
            [
                'method' => 'GET',
                'query'  => ['id' => '01900000-0000-7000-8000-000000000000'],
                '_route' => $route,
            ],
            static fn (array $request): array => ['passthrough' => true]
        );
        $this->assertArrayHasKey('passthrough', $allowed, 'id verilmişse geçmeli');
    }

    // ─── F8: /stream contract-first kayıt ───

    /**
     * Gerçek istek index.php §1.1 byte-path'inde (Gateway ÖNCESİ) yakalanır;
     * bu kayıt openapi.yaml /stream ile hizalı tek noktadan ilandır.
     * RouteTable exact-match olduğu için gerçek 32-hex id bu satıra DÜŞMEZ.
     */
    public function testStreamRouteIsRegisteredAsPrivateContractEntry(): void
    {
        $route = $this->routes['GET']['/api/v1/stream/{id}'] ?? null;

        $this->assertIsArray($route, 'GET /api/v1/stream/{id} kaydı routes.php\'de olmalı (F8)');
        $this->assertSame('streamController', $route['handler'] ?? null);
        $this->assertSame('stream', $route['action'] ?? null);
        $this->assertTrue((bool) ($route['implemented'] ?? false));
        $this->assertFalse((bool) ($route['public'] ?? true), 'stream girişe zorunlu (fail-closed)');
    }

    // ─── Billing: contract-first kayıt (openapi.yaml /billing/plan + /billing/invoices) ───

    /**
     * İki GET ucu da private (own-data, auth zorunlu) + implemented + action'lı;
     * plan ucunda query kuralı YOK (validation opsiyonel alan taşımaz).
     */
    public function testBillingRoutesAreRegisteredPrivateWithActions(): void
    {
        foreach (['/api/v1/billing/plan' => 'plan', '/api/v1/billing/invoices' => 'invoices'] as $path => $action) {
            $result = $this->table->match($path, 'GET');

            $this->assertSame(RouteTable::STATUS_OK, $result['status'], "{$path} kayıtlı olmalı");
            $this->assertSame($action, $result['route']['action'] ?? null);
            $this->assertSame('billingController', $result['route']['handler'] ?? null);
            $this->assertTrue((bool) ($result['route']['implemented'] ?? false));
            $this->assertFalse((bool) ($result['route']['public'] ?? true), "{$path} private (own-data)");
        }

        $this->assertArrayNotHasKey(
            'validation',
            $this->routes['GET']['/api/v1/billing/plan'],
            'plan ucunda query parametresi yok → validation kural seti olmamalı'
        );
    }

    /**
     * Route kuralı yalnız type:int (gateway non-int → 422). min/max route'a
     * KONMAZ: Validator string değerde mb_strlen ölçer → page=0'ı geçirirdi;
     * aralık (page≥1, pageSize 1..100) BillingController'da (BillingControllerTest).
     */
    public function testBillingInvoicesCarryOptionalIntPaginationRules(): void
    {
        $rules = $this->routes['GET']['/api/v1/billing/invoices']['validation'] ?? null;

        $this->assertIsArray($rules, 'invoices validation kural seti zorunlu');
        foreach (['page', 'pageSize'] as $field) {
            $this->assertSame('int', $rules[$field]['type'] ?? null, "{$field} type=int olmalı");
            $this->assertArrayNotHasKey('required', $rules[$field], "{$field} opsiyonel (varsayılan sayfa)");
            $this->assertArrayNotHasKey('min', $rules[$field], "{$field} min YASAK (Validator min/max string'te UZUNLUK ölçer)");
            $this->assertArrayNotHasKey('max', $rules[$field], "{$field} max YASAK (aynı gerekçe)");
        }

        // Gerçek middleware: ?page=abc → 422 VALIDATION_ERROR + error.fields.page
        http_response_code(200);
        $middleware = new RequestValidationMiddleware();
        $denied = $middleware(
            [
                'method' => 'GET',
                'query'  => ['page' => 'abc'],
                '_route' => $this->routes['GET']['/api/v1/billing/invoices'],
            ],
            static fn (array $request): array => ['passthrough' => true]
        );

        $this->assertSame(422, http_response_code());
        $this->assertSame('VALIDATION_ERROR', $denied['error']['code'] ?? null);
        $this->assertArrayHasKey('page', (array) ($denied['error']['fields'] ?? []));
        $this->assertArrayNotHasKey('passthrough', $denied);

        // Sayısal geçerli değer gateway'den geçer (controller aralık kontrolüne kalır)
        http_response_code(200);
        $allowed = $middleware(
            [
                'method' => 'GET',
                'query'  => ['page' => '0', 'pageSize' => '5'],
                '_route' => $this->routes['GET']['/api/v1/billing/invoices'],
            ],
            static fn (array $request): array => ['passthrough' => true]
        );
        $this->assertArrayHasKey('passthrough', $allowed, 'page=0 tip-uyumlu → gateway 422 VERMEZ (aralık controller’da)');
    }

    // ─── F13: RBAC alanları — admin/privilege uçları dışında DOLDURULMAZ ───

    /**
     * F13 (kullanıcı onayı 2026-10-30): AuthorizationMiddleware
     * requiredRole/requiredPermission'i honor eder ama routes.php'de admin/
     * privilege ucu YOK → 0 route kısıtlanır (üye uçları sınırsız kalır —
     * alan yok = kısıt yok). Bu test bilinçli kilit: ileride admin ucu eklenirse
     * burası bilerek güncellenmelidir; sessiz genişleme/daralma yakalanır.
     *
     * Şüpheli — raporlandı (Zero-Hallucination, kısıtlanmadı):
     *  1) Rol sözlüğü uyumsuz: kod `user`/`admin` (UserHeaderService MENU_ITEMS)
     *     ve `free`/`free_user` (auth register) üretir; CLAUDE.md K6 seti
     *     regular/premium/studio/car/admin/system — üretilen değer DEĞİL.
     *     Uydurulan requiredRole herkesi 403'lerdi → alan boş bırakıldı.
     *  2) GET /user/header-data public:true (anonim boş menü — tasarıma uygun,
     *     yüzey şüpheli).
     *  3) 4 adet implemented:false GET prefix kaydı (dead entry).
     */
    public function testNoRouteDeclaresRbacRoleOrPermissionFields(): void
    {
        $restricted = [];
        foreach ($this->table->all() as $entry) {
            $route = $entry['route'];
            $role  = $route['requiredRole'] ?? null;
            $perm  = $route['requiredPermission'] ?? null;
            if ($role !== null || $perm !== null) {
                $restricted[] = $entry['method'] . ' ' . $entry['path']
                    . ' role=' . var_export($role, true)
                    . ' perm=' . var_export($perm, true);
            }
        }

        $this->assertSame(
            [],
            $restricted,
            'F13: routes.php\'de admin/privilege ucu yok — requiredRole/requiredPermission '
            . 'dolduran satır beklenmiyor. Eklenirse bu KİLİT bilerek güncellenmeli. '
            . 'Şüpheli satırlar: ' . implode(' | ', $restricted)
        );
    }

    /**
     * ADR-113: API V1 & V2 Paralel Route Test Paritesi (TASK-12)
     *
     * routes.php tek tablo olarak /api/{version}/ kalıbını kullanır.
     * v1 ve v2 için üretilen tüm rotaların metot, handler, action,
     * public, implemented ve validation özellikleri %100 birebir aynı olmalıdır.
     */
    public function testV1AndV2RouteParity(): void
    {
        $rawRoutes = require dirname(__DIR__, 2) . '/config/routes.php';
        $v1Routes = $this->replaceVersionPlaceholder($rawRoutes, 'v1');
        $v2Routes = $this->replaceVersionPlaceholder($rawRoutes, 'v2');

        $v1Table = RouteTable::fromArray($v1Routes);
        $v2Table = RouteTable::fromArray($v2Routes);

        $v1Entries = $v1Table->all();
        $v2Entries = $v2Table->all();

        $this->assertCount(count($v1Entries), $v2Entries, 'V1 ve V2 rota sayıları eşit olmalı');

        foreach ($v1Entries as $v1Entry) {
            $expectedV2Path = str_replace('/v1/', '/v2/', $v1Entry['path']);
            $v1Match = $v1Table->match($v1Entry['path'], $v1Entry['method']);
            $v2Match = $v2Table->match($expectedV2Path, $v1Entry['method']);

            $this->assertSame(
                $v1Match['status'],
                $v2Match['status'],
                "V1 ve V2 match status eşleşmiyor: {$v1Entry['path']} vs {$expectedV2Path} ({$v1Entry['method']})"
            );

            $this->assertSame(
                $v1Entry['route']['handler'] ?? null,
                $v2Match['route']['handler'] ?? null,
                "{$expectedV2Path} handler v1 ile eşleşmiyor"
            );

            $this->assertSame(
                $v1Entry['route']['action'] ?? null,
                $v2Match['route']['action'] ?? null,
                "{$expectedV2Path} action v1 ile eşleşmiyor"
            );

            $this->assertSame(
                $v1Entry['route']['public'] ?? false,
                $v2Match['route']['public'] ?? false,
                "{$expectedV2Path} public bayrağı v1 ile eşleşmiyor"
            );

            $this->assertSame(
                $v1Entry['route']['implemented'] ?? false,
                $v2Match['route']['implemented'] ?? false,
                "{$expectedV2Path} implemented bayrağı v1 ile eşleşmiyor"
            );

            $this->assertSame(
                $v1Entry['route']['validation'] ?? null,
                $v2Match['route']['validation'] ?? null,
                "{$expectedV2Path} validation kuralları v1 ile eşleşmiyor"
            );
        }
    }
}


