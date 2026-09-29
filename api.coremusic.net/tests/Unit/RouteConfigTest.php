<?php
declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

use CoreMusic\Api\Routing\RouteTable;
use PHPUnit\Framework\TestCase;

/**
 * Faz 1a route sözleşmesi (ADR-084 Contract First · ADR-020 §1.1-B.1):
 *   - 6 POST auth kaydı tanımlı ama implemented=false → 405 + Allow (404 değil)
 *   - GET /api/v1/auth/login public=true → 200 davranışı korunur
 *   - Auth POST'larında henüz validation kuralı yok (Faz 1b işi)
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

    public function testSixPostAuthRoutesAreDeclaredButNotImplemented(): void
    {
        $expected = [
            '/api/v1/auth/login',
            '/api/v1/auth/register',
            '/api/v1/auth/forgot-password',
            '/api/v1/auth/reset-password',
            '/api/v1/auth/set-gender',
            '/api/v1/auth/logout',
        ];

        foreach ($expected as $path) {
            $this->assertArrayHasKey($path, $this->routes['POST'], "{$path} POST kaydı tanımlı olmalı");
            $this->assertFalse(
                (bool) ($this->routes['POST'][$path]['implemented'] ?? true),
                "{$path} implemented=false olmalı (Faz 1b'ye kadar 405 + Allow)"
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

    public function testPostLoginYields405WithAllowHeaderListingGet(): void
    {
        $result = $this->table->match('/api/v1/auth/login', 'POST');

        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $result['status'], 'POST login 405 dönmeli (404 değil)');
        $this->assertContains('GET', $result['allow']);
        $this->assertContains('OPTIONS', $result['allow']);
        $this->assertNotContains('POST', $result['allow']);
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

    public function testAuthPostRoutesCarryNoValidationRulesYet(): void
    {
        foreach (array_keys($this->routes['POST']) as $path) {
            $this->assertArrayNotHasKey(
                'validation',
                $this->routes['POST'][$path],
                "{$path} validation kuralı Faz 1b'de eklenmeli (şimdilik çıplak POST 415/422'ye düşmemeli)"
            );
        }
    }
}
