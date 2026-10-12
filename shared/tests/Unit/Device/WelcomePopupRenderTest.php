<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Device;

use PHPUnit\Framework\TestCase;
use CoreMusic\Device\DeviceManager;

final class WelcomePopupRenderTest extends TestCase
{
    public function testPopupOnlyIncludedForEmbedded1024(): void
    {
        // 1. Embedded Raspberry Pi 5 / 7" Touchscreen (1024x600)
        $rpiDm = DeviceManager::fromRequest(
            userAgent: 'Mozilla/5.0 (X11; Linux armv7l) Raspberry Pi',
            viewportW: 1024,
            viewportH: 600
        );
        $this->assertTrue($rpiDm->isEmbedded());
        $this->assertTrue($rpiDm->shouldRenderWelcomePopup(), 'Welcome popup MUST be rendered for RPi5 1024x600');

        // 2. Desktop 1080p (1920x1080)
        $desktopDm = DeviceManager::fromRequest(
            userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            viewportW: 1920,
            viewportH: 1080
        );
        $this->assertFalse($desktopDm->shouldRenderWelcomePopup(), 'Welcome popup MUST NOT be rendered for desktop 1080p');

        // 3. Laptop 1024x768 (Small Desktop)
        $laptopDm = DeviceManager::fromRequest(
            userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            viewportW: 1024,
            viewportH: 768
        );
        $this->assertFalse($laptopDm->shouldRenderWelcomePopup(), 'Welcome popup MUST NOT be rendered for laptop 1024x768');

        // 4. 4K TV (3840x2160)
        $tvDm = DeviceManager::fromRequest(
            userAgent: 'Mozilla/5.0 (Web0S; SmartTV)',
            viewportW: 3840,
            viewportH: 2160
        );
        $this->assertFalse($tvDm->shouldRenderWelcomePopup(), 'Welcome popup MUST NOT be rendered for 4K TV');

        // 5. Phone (375x812)
        $phoneDm = DeviceManager::fromRequest(
            userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)',
            viewportW: 375,
            viewportH: 812
        );
        $this->assertFalse($phoneDm->shouldRenderWelcomePopup(), 'Welcome popup MUST NOT be rendered for Mobile Phone');
    }
}
