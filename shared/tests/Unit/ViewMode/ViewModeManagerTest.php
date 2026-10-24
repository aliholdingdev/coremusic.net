<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\ViewMode;

use PHPUnit\Framework\TestCase;
use CoreMusic\ViewMode\ViewModeManager;

final class ViewModeManagerTest extends TestCase
{
    public function testSessionModeTakesPriorityOverRoute(): void
    {
        $this->assertSame('car', ViewModeManager::detect(['view_mode' => 'car'], '/studio/x'));
    }

    public function testSessionModeIsCaseInsensitive(): void
    {
        $this->assertSame('pro', ViewModeManager::detect(['view_mode' => 'PRO']));
    }

    public function testInvalidSessionModeFallsBackToRoute(): void
    {
        $this->assertSame('studio', ViewModeManager::detect(['view_mode' => 'hacker'], '/studio/album'));
    }

    public function testRoutePrefixMapsToMode(): void
    {
        $this->assertSame('pro', ViewModeManager::detect([], 'pro/dashboard'));
        $this->assertSame('admin', ViewModeManager::detect([], '/admin'));
    }

    public function testDefaultsToHomeWithoutSessionOrRoute(): void
    {
        $this->assertSame('home', ViewModeManager::detect([]));
        $this->assertSame('home', ViewModeManager::detect([], '/unknown'));
    }

    public function testIsValidAcceptsOnlyKnownModes(): void
    {
        $this->assertTrue(ViewModeManager::isValid('studio'));
        $this->assertFalse(ViewModeManager::isValid('unknown'));
    }

    public function testCssPathFallsBackToHomeForInvalidMode(): void
    {
        $this->assertSame('09_ViewModes/v-car.css', ViewModeManager::getCssPath('car'));
        $this->assertSame('09_ViewModes/v-home.css', ViewModeManager::getCssPath('nope'));
    }

    public function testInjectDataAttributeUsesValidModeOnly(): void
    {
        $this->assertSame('studio', ViewModeManager::injectDataAttribute('STUDIO'));
        $this->assertSame('home', ViewModeManager::injectDataAttribute('hacker'));
    }
}
