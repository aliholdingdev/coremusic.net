<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Container\ApiContainer;
use PHPUnit\Framework\TestCase;

/**
 * Regresyon: Faz 4b'de ApiContainer.php'nin <?php/namespace satırları silinmiş,
 * php -l dosyayı HTML olarak yuttuğu için suite yeşil kalmıştı (runtime'da
 * CLASS_MISSING → tüm dispatch route'ları fatal). Bu test container'ın gerçekten
 * yüklendiğini, dispatch metotlarının backing property'lerini ve her
 * implemented:true route'un bir match kolonuna sahip olduğunu korur.
 */
final class ApiContainerLoadTest extends TestCase
{
    public function testContainerLoadsViaAutoloader(): void
    {
        self::assertTrue(class_exists(ApiContainer::class), 'ApiContainer autoload ile yüklenemiyor — <?php/namespace başlığı eksik olabilir');
        self::assertTrue(method_exists(ApiContainer::class, 'handle'));
    }

    public function testDispatchMethodsAndBackingPropertiesExist(): void
    {
        $ref = new \ReflectionClass(ApiContainer::class);

        foreach (['historyController', 'userHeaderController', 'musicController', 'playlistController', 'userController'] as $member) {
            self::assertTrue($ref->hasMethod($member), "Eksik dispatch metodu: {$member}");
            self::assertTrue($ref->hasProperty($member), "Eksik backing property: \${$member}");
        }
    }

    public function testEveryImplementedRouteHandlerHasDispatchArm(): void
    {
        $base = dirname(__DIR__, 2);
        $routes = require $base . '/config/routes.php';
        $source = (string) file_get_contents($base . '/include/Container/ApiContainer.php');

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
                "ApiContainer::handle() match kolonu eksik: {$handler}"
            );
        }
    }
}
