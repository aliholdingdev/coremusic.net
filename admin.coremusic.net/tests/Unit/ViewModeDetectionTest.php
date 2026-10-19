<?php declare(strict_types=1);

namespace CoreMusic\Admin\Test\Unit;

use CoreMusic\ViewMode\ViewModeManager;
use PHPUnit\Framework\TestCase;

/**
 * Görev D2 — ViewMode route tespiti (integration).
 *
 * H1 düzeltmesi: PageRouter route URI'leri başında '/' olmadan gelir
 * (PageRouter::resolveUri trim'ler) → ViewModeManager::detect artık
 * prefix'i normalize eder. admin-* rotaları 'admin' view mode'una,
 * diğerleri ('dashboard' dâhil eski kısa adlar) 'home'a düşer.
 */
final class ViewModeDetectionTest extends TestCase
{
    public function testAdminPrefixedRoutesResolveToAdminMode(): void
    {
        foreach (['admin-dashboard', 'admin-login', 'admin-register', 'admin-songs'] as $route) {
            self::assertSame(
                'admin',
                ViewModeManager::detect([], $route),
                $route . " rotası 'admin' view mode'a düşmeli (H1 düzeltmesi)"
            );
        }
    }

    public function testSlashPrefixedRoutesStillResolve(): void
    {
        self::assertSame('admin', ViewModeManager::detect([], '/admin-dashboard'));
        self::assertSame('studio', ViewModeManager::detect([], 'studio'));
        self::assertSame('studio', ViewModeManager::detect([], '/studio'));
    }

    public function testLegacyShortNamesRemainHome(): void
    {
        self::assertSame('home', ViewModeManager::detect([], 'dashboard'), 'eski kısa ad artık admin rotası değil');
        self::assertSame('home', ViewModeManager::detect([], 'auth/callback'), 'callback dokunulmaz — home kalır');
        self::assertSame('home', ViewModeManager::detect([], ''));
    }

    public function testAdminCssPathIsResolvedForAdminMode(): void
    {
        self::assertSame('09_ViewModes/v-admin.css', ViewModeManager::getCssPath(ViewModeManager::detect([], 'admin-dashboard')));
    }
}
