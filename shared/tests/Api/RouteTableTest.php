<?php declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Routing\RouteTable;
use PHPUnit\Framework\TestCase;

/**
 * Method-aware route tablosu — 405 / 404 ayrımı (Faz 1a · ADR-084).
 */
final class RouteTableTest extends TestCase
{
    private function table(): RouteTable
    {
        // Yeni sözleşme: "alt kaynak" yalnızca TABLODA açıkça kayıtla vardır.
        //   - `/api/v1/auth/profile` POST kaydıyla  → GET prefix fallback çalışır
        //   - `/api/v1/auth/settings` GET kaydıyla  → POST 405 + Allow: GET
        //   - hiç kaydı olmayan alt yol (ör. does-not-exist) → 404
        return RouteTable::fromArray([
            'GET' => [
                '/api/v1/auth'      => ['service' => 'auth', 'handler' => 'authController'],
                '/api/v1/auth/login' => ['service' => 'auth', 'handler' => 'authController', 'public' => true],
                '/api/v1/auth/settings' => ['service' => 'auth', 'handler' => 'authController'],
            ],
            'POST' => [
                '/api/v1/auth/login' => [
                    'service'     => 'auth',
                    'handler'     => 'authController',
                    'public'      => true,
                    'implemented' => false,
                ],
                '/api/v1/auth/profile' => ['service' => 'auth', 'handler' => 'authController'],
                '/api/v1/user' => ['service' => 'user', 'handler' => 'userController'],
            ],
        ]);
    }

    public function testGetExactRouteMatchesAsOk(): void
    {
        $match = $this->table()->match('/api/v1/auth/login', 'GET');

        $this->assertSame(RouteTable::STATUS_OK, $match['status']);
        $this->assertIsArray($match['route']);
        $this->assertTrue($match['route']['public'], 'Exact kayıt public alanını korumalı');
        $this->assertSame([], $match['allow']);
    }

    public function testExactMatchBeatsPrefixMatch(): void
    {
        $match = $this->table()->match('/api/v1/auth/login/', 'GET'); // trailing slash

        $this->assertSame(RouteTable::STATUS_OK, $match['status']);
        $this->assertSame('/api/v1/auth/login', $match['route']['path']);
    }

    public function testPrefixMatchStillWorksForDeclaredResource(): void
    {
        // `/api/v1/auth/profile` tabloda POST kaydıyla tanımlı ("declared") →
        // GET isteği exact bulamaz, prefix fallback collection route'unu döndürür.
        // Hiç tanımlı olmayan alt yollar için bkz. testUnknownSubPathUnderDeclaredPrefixYields404.
        $match = $this->table()->match('/api/v1/auth/profile', 'GET');

        $this->assertSame(RouteTable::STATUS_OK, $match['status']);
        $this->assertSame('/api/v1/auth', $match['route']['path']);
    }

    public function testPostLoginDeclaredButNotImplementedYields405WithAllow(): void
    {
        $match = $this->table()->match('/api/v1/auth/login', 'POST');

        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $match['status']);
        $this->assertIsArray($match['route'], 'Route tanınmalı (404 değil)');
        $this->assertContains('GET', $match['allow']);
        $this->assertContains('OPTIONS', $match['allow']);
        $this->assertNotContains('POST', $match['allow'], 'Sunulmayan method Allow listesinde olmamalı');
    }

    public function testUnknownMethodOnKnownPathYields405NotNullRoute(): void
    {
        $match = $this->table()->match('/api/v1/auth/login', 'PUT');

        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $match['status']);
        $this->assertNull($match['route']);
        $this->assertContains('GET', $match['allow']);
    }

    public function testUnknownPathYields404Not405(): void
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            $match = $this->table()->match('/api/v1/does-not-exist', $method);

            $this->assertSame(RouteTable::STATUS_NOT_FOUND, $match['status'], "{$method} için 404 beklenir");
            $this->assertNull($match['route']);
            $this->assertSame([], $match['allow'], '404 yanıtında Allow başlığı olmamalı');
        }
    }

    /**
     * Prefix + tanımsız alt yol → 404 (RouteConfigTest tetikleyicisi:
     * `GET /api/v1/auth/does-not-exist` eski davranışta prefix fallback ile
     * private route'a düşüp 401 dönüyordu).
     */
    public function testUnknownSubPathUnderDeclaredPrefixYields404(): void
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            $match = $this->table()->match('/api/v1/auth/does-not-exist', $method);

            $this->assertSame(
                RouteTable::STATUS_NOT_FOUND,
                $match['status'],
                "{$method}: prefix altı tanımsız yol 404 (eski: 401/405)"
            );
            $this->assertNull($match['route'], 'Prefix route\'una fallback yapılmamalı');
            $this->assertSame([], $match['allow'], '404 yanıtında Allow başlığı olmamalı');
        }

        // Prefix kökü (collection ucu) exact kayıt → davranış değişmez
        $this->assertSame(RouteTable::STATUS_OK, $this->table()->match('/api/v1/auth', 'GET')['status']);
    }

    public function testPostOnResourceWithoutPostRouteYields405(): void
    {
        // /api/v1/auth altında GET-only alt kaynak (POST kaydı yok) → 405 + Allow
        $match = $this->table()->match('/api/v1/auth/settings', 'POST');

        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $match['status']);
        $this->assertContains('GET', $match['allow']);
    }

    public function testDefaultsPreserveLegacyGetPrefixBehaviour(): void
    {
        $table = RouteTable::defaults();

        foreach (['auth', 'user', 'music', 'playlist', 'media', 'download'] as $service) {
            $match = $table->match('/api/v1/' . $service, 'GET');
            $this->assertSame(RouteTable::STATUS_OK, $match['status'], "/api/v1/{$service} GET eşleşmeli");
            $this->assertSame($service, $match['route']['service']);
        }

        $this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $table->match('/api/v1/auth', 'POST')['status']);
    }

    public function testAllExposesMethodAwareRegistry(): void
    {
        $all = $this->table()->all();
        $methods = array_column($all, 'method');

        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);

        $postLogin = array_values(array_filter(
            $all,
            static fn (array $row): bool => $row['method'] === 'POST' && $row['path'] === '/api/v1/auth/login'
        ));
        $this->assertCount(1, $postLogin);
        $this->assertFalse($postLogin[0]['route']['implemented']);
    }

    public function testQueryStringAndSlashAreNormalized(): void
    {
        $match = $this->table()->match('/api/v1/auth/login?next=/home', 'get');

        $this->assertSame(RouteTable::STATUS_OK, $match['status']);
        $this->assertSame('GET', $match['route']['method']);
    }
}
