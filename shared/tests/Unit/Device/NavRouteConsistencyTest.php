<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Device;

use PHPUnit\Framework\TestCase;
use CoreMusic\Device\DeviceManager;

/**
 * Regresyon koruması — C-F-14 kök nedeni:
 * DeviceManager NAV_LINKS kayıtlı olmayan rotalara (/kutuphane, /goz-at,
 * /hakkimizda) link veriyordu → SPA navigasyonu sunucudan 404 aldı →
 * tam sayfa 404 tüm DOM'u siliyordu (header/footer/player/audio gidiyordu).
 *
 * SSOT: shared/config/routes.php — nav'daki HER href kayıtlı bir route olmalı.
 */
final class NavRouteConsistencyTest extends TestCase
{
    /** @return array<string, list<string>> cihaz => href listesi */
    private static function navLinksByDevice(): array
    {
        $ref   = new \ReflectionClass(DeviceManager::class);
        $const = $ref->getReflectionConstant('NAV_LINKS');
        self::assertNotNull($const, 'NAV_LINKS const bulunamadı');

        $navLinks = $const->getValue();
        $out = [];
        foreach ($navLinks as $device => $links) {
            $out[$device] = array_map(
                static fn (array $link): string => (string) $link['href'],
                $links
            );
        }
        return $out;
    }

    /** @return list<string> routes.php kayıtlı route anahtarları */
    private static function registeredRoutes(): array
    {
        $routesFile = dirname(__DIR__, 3) . '/config/routes.php';
        self::assertFileExists($routesFile, 'routes.php bulunamadı (SSOT kaynağı)');

        $routes = include $routesFile;
        self::assertIsArray($routes, 'routes.php array döndürmeli');

        return array_keys($routes);
    }

    public function testEveryNavLinkTargetsRegisteredRoute(): void
    {
        $registered = self::registeredRoutes();
        $deadLinks = [];

        foreach (self::navLinksByDevice() as $device => $hrefs) {
            foreach ($hrefs as $href) {
                $slug = ltrim($href, '/');
                if (!in_array($slug, $registered, true)) {
                    $deadLinks[] = "{$device}: {$href}";
                }
            }
        }

        self::assertSame(
            [],
            $deadLinks,
            "NAV_LINKS kayıtsız rotalara link veriyor (404 → ölü nav):\n"
            . implode("\n", $deadLinks)
            . "\nÇözüm: rotayı shared/config/routes.php'ye EKLE ya da nav'dan KALDIR."
        );
    }

    public function testNavLinksAreNonEmptyForEveryDevice(): void
    {
        foreach (self::navLinksByDevice() as $device => $hrefs) {
            self::assertNotEmpty($hrefs, "Cihaz '{$device}' için nav linki yok");
        }
    }
}
