<?php
declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

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
        $this->routes = require dirname(__DIR__, 2) . '/config/routes.php';
        $this->table  = RouteTable::fromArray($this->routes);
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
        $result = $this->table->match('/api/v1/auth/login', 'PUT');
        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $result['status']);
        $this->assertContains('GET', $result['allow']);
        $this->assertContains('POST', $result['allow']);
        $this->assertContains('OPTIONS', $result['allow']);
        $this->assertNotContains('PUT', $result['allow']);
    }

    public function testUnknownPathIsNotFoundNot405(): void
    {
        $this->assertSame(RouteTable::STATUS_NOT_FOUND, $this->table->match('/api/v1/unknown', 'GET')['status']);
    }

    public function testGetLoginIsFound(): void
    {
        $result = $this->table->match('/api/v1/auth/login', 'GET');

        $this->assertSame(RouteTable::STATUS_OK, $result['status']);
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

        // Şifre gücü: ≥ 12 (ADR-020) — login ve register kuralında da görünmeli
        foreach (['/api/v1/auth/login', '/api/v1/auth/register', '/api/v1/auth/reset-password'] as $path) {
            $this->assertSame(
                12,
                $this->routes['POST'][$path]['validation']['password']['min'] ?? null,
                "{$path} password min=12 olmalı"
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

    public function testLogoutHasNoValidationRulesSoBodylessPostPasses(): void
    {
        $this->assertArrayNotHasKey('validation', $this->routes['POST']['/api/v1/auth/logout']);
    }
}
