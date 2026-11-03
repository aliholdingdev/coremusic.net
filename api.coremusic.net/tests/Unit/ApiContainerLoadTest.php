<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Container\ApiContainerBase;
use PHPUnit\Framework\TestCase;

/**
 * Regresyon: Faz 4b'de container dosyasının <?php/namespace satırları silinmiş,
 * php -l dosyayı HTML olarak yuttuğu için suite yeşil kalmıştı (runtime'da
 * CLASS_MISSING → tüm dispatch route'ları fatal). Bu test container'ın gerçekten
 * yüklendiğini, dispatch metotlarının backing property'lerini ve her
 * implemented:true route'un bir match kolonuna sahip olduğunu korur.
 *
 * F12: hedef `ApiContainerBase`'tir (v1/v2 container'larının template tabanı).
 * Legacy `ApiContainer` üretim kodunda 0 referansla silindi (bkz. audit raporu F12);
 * V1Container/V2Container bu sınıftan extend edilir ve index.php yalnız onları
 * kullanır (index.php:200 `$container = ($version === 'v2') ? new V2Container() : new V1Container()`).
 */
final class ApiContainerLoadTest extends TestCase
{
    private const SOURCE = '/include/Container/ApiContainerBase.php';

    public function testContainerLoadsViaAutoloader(): void
    {
        self::assertTrue(class_exists(ApiContainerBase::class), 'ApiContainerBase autoload ile yüklenemiyor — <?php/namespace başlığı eksik olabilir');
        self::assertTrue(method_exists(ApiContainerBase::class, 'handle'));
    }

    public function testDispatchMethodsAndBackingPropertiesExist(): void
    {
        $ref = new \ReflectionClass(ApiContainerBase::class);

        foreach (['historyController', 'userHeaderController', 'musicController', 'playlistController', 'userController', 'streamController', 'billingController'] as $member) {
            self::assertTrue($ref->hasMethod($member), "Eksik dispatch metodu: {$member}");
            self::assertTrue($ref->hasProperty($member), "Eksik backing property: \${$member}");
        }
    }

    public function testEveryImplementedRouteHandlerHasDispatchArm(): void
    {
        $base = dirname(__DIR__, 2);
        $routes = require $base . '/config/routes.php';
        $source = (string) file_get_contents($base . self::SOURCE);

        $implemented = [];
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method) {
            foreach ($routes[$method] ?? [] as $route) {
                if (($route['implemented'] ?? false) === true && isset($route['handler'])) {
                    $implemented[(string) $route['handler']] = true;
                }
            }
        }

        self::assertNotEmpty($implemented, 'implemented:true route bulunamadı — routes.php yapısı değişmiş');

        foreach (array_keys($implemented) as $handler) {
            self::assertMatchesRegularExpression(
                "/'" . preg_quote($handler, '/') . "'\s*=>/",
                $source,
                "ApiContainerBase::handle() match kolonu eksik: {$handler}"
            );
        }
    }

    /**
     * F12: legacy duplike üretim kodunda kalmamalı.
     *
     * ApiContainer, ApiContainerBase ile aynı işi kendi static alanlarıyla
     * tekrarlıyordu; index.php onu hiç instantiate etmez (yalnız V1/V2).
     */
    public function testLegacyApiContainerIsRemoved(): void
    {
        $path = dirname(__DIR__, 2) . '/include/Container/ApiContainer.php';
        self::assertFileDoesNotExist($path, 'Legacy ApiContainer.php silinmiş olmalı (F12 — 0 üretim referansı)');
    }
}