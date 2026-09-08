<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Device;

use PHPUnit\Framework\TestCase;
use CoreMusic\Device\DeviceDetector;
use CoreMusic\Device\DeviceManager;

final class DeviceDetectorTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = '';
        unset($_SERVER['HTTP_X_DEVICE_TYPE']);
        DeviceManager::resetInstance();
    }

    protected function tearDown(): void
    {
        DeviceManager::resetInstance();
    }

    public function testDetectsEmbeddedFromUserAgent(): void
    {
        $rpiAgent = 'Mozilla/5.0 (X11; Linux armv7l) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Raspberry Pi';
        $this->assertSame('embedded', DeviceDetector::detect($rpiAgent));

        $aarchAgent = 'Mozilla/5.0 (X11; Linux aarch64) AppleWebKit/537.36';
        $this->assertSame('embedded', DeviceDetector::detect($aarchAgent));
    }

    public function testDetectsEmbeddedFromHeader(): void
    {
        $_SERVER['HTTP_X_DEVICE_TYPE'] = 'embedded';
        $this->assertSame('embedded', DeviceDetector::detect());
    }

    public function testDetectsEmbedded1024x600Viewport(): void
    {
        $linuxDesktopUa = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36';
        // 1024x600 resolution represents the 7" embedded touchscreen (RPi5)
        $this->assertSame('embedded', DeviceDetector::detect($linuxDesktopUa, 1024, 600));
        $this->assertTrue(DeviceDetector::isEmbedded1024('embedded', 1024, 600));
    }

    public function testDetectsLaptop1024x768Viewport(): void
    {
        $winDesktopUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        // 1024x768 on desktop OS should be treated as laptop/small-desktop
        $this->assertSame('laptop', DeviceDetector::detect($winDesktopUa, 1024, 768));
        $this->assertFalse(DeviceDetector::isEmbedded1024('laptop', 1024, 768));
    }

    public function testDetectsDesktop1080p(): void
    {
        $winDesktopUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $this->assertSame('desktop', DeviceDetector::detect($winDesktopUa, 1920, 1080));
    }

    public function testDetects4kTvAndMonitor(): void
    {
        $tvAgent = 'Mozilla/5.0 (Web0S; SmartTV) AppleWebKit/537.36';
        $this->assertSame('4k-tv', DeviceDetector::detect($tvAgent, 3840, 2160));

        $winDesktopUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $this->assertSame('4k-monitor', DeviceDetector::detect($winDesktopUa, 3840, 2160));
    }

    public function testDetectsPhone(): void
    {
        $iphoneUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)';
        $this->assertSame('phone', DeviceDetector::detect($iphoneUa, 375, 812));
    }

    public function testWelcomePopupOnlyForEmbedded1024(): void
    {
        // 1. Embedded 1024x600 RPi5
        $dmEmbedded = DeviceManager::fromRequest('Raspberry Pi RPi5', 1024, 600);
        $this->assertTrue($dmEmbedded->isEmbedded());
        $this->assertTrue($dmEmbedded->isEmbedded1024());
        $this->assertTrue($dmEmbedded->shouldRenderWelcomePopup());

        // 2. Laptop 1024x768 Small Desktop
        $winUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';
        $dmLaptop = DeviceManager::fromRequest($winUa, 1024, 768);
        $this->assertFalse($dmLaptop->isEmbedded());
        $this->assertFalse($dmLaptop->shouldRenderWelcomePopup());

        // 3. Desktop 1920x1080
        $dmDesktop = DeviceManager::fromRequest($winUa, 1920, 1080);
        $this->assertTrue($dmDesktop->isDesktop());
        $this->assertFalse($dmDesktop->shouldRenderWelcomePopup());

        // 4. 4K TV 3840x2160
        $tvAgent = 'Mozilla/5.0 (Web0S; SmartTV)';
        $dmTv = DeviceManager::fromRequest($tvAgent, 3840, 2160);
        $this->assertTrue($dmTv->is4kTv());
        $this->assertFalse($dmTv->shouldRenderWelcomePopup());

        // 5. Phone 375x812
        $iphoneUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)';
        $dmPhone = DeviceManager::fromRequest($iphoneUa, 375, 812);
        $this->assertTrue($dmPhone->isPhone());
        $this->assertFalse($dmPhone->shouldRenderWelcomePopup());
    }

    public function testFourTierLayoutDecisions(): void
    {
        $winUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';
        $rpiUa = 'Mozilla/5.0 (X11; Linux armv7l) Raspberry Pi';
        $ipadUa = 'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X)';
        $tvUa = 'Mozilla/5.0 (Web0S; SmartTV)';
        $phoneUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0)';

        // Tier 1: 1024px Gömülü & Tablet
        $dmRpi = DeviceManager::fromRequest($rpiUa, 1024, 600);
        $this->assertTrue($dmRpi->shouldRenderEmbeddedLayout());
        $this->assertFalse($dmRpi->shouldRenderWideLayout());
        $this->assertFalse($dmRpi->shouldRender4kLayout());

        $dmIpad = DeviceManager::fromRequest($ipadUa, 820, 1180);
        $this->assertTrue($dmIpad->shouldRenderEmbeddedLayout());
        $this->assertFalse($dmIpad->shouldRenderWideLayout());

        // Tier 2: 1920px FHD, 2K, 3K, Laptops (Standart Ekranlar)
        $dmLaptop = DeviceManager::fromRequest($winUa, 1366, 768);
        $this->assertTrue($dmLaptop->shouldRenderWideLayout());
        $this->assertFalse($dmLaptop->shouldRenderEmbeddedLayout());

        $dmDesktop = DeviceManager::fromRequest($winUa, 1920, 1080);
        $this->assertTrue($dmDesktop->shouldRenderWideLayout());
        $this->assertFalse($dmDesktop->shouldRenderEmbeddedLayout());

        $dm2k = DeviceManager::fromRequest($winUa, 2560, 1440);
        $this->assertTrue($dm2k->shouldRenderWideLayout());
        $this->assertFalse($dm2k->shouldRender4kLayout());

        // Tier 3: 4K TV & 4K Monitör (3840px)
        $dm4kTv = DeviceManager::fromRequest($tvUa, 3840, 2160);
        $this->assertTrue($dm4kTv->shouldRender4kLayout());
        $this->assertFalse($dm4kTv->shouldRenderWideLayout());

        $dm4kMon = DeviceManager::fromRequest($winUa, 3840, 2160);
        $this->assertTrue($dm4kMon->shouldRender4kLayout());
        $this->assertFalse($dm4kMon->shouldRenderWideLayout());

        // Tier 4: Mobil Telefon (<=767px)
        $dmPhone = DeviceManager::fromRequest($phoneUa, 375, 812);
        $this->assertTrue($dmPhone->isPhone());
        $this->assertFalse($dmPhone->shouldRenderEmbeddedLayout());
        $this->assertFalse($dmPhone->shouldRenderWideLayout());
        $this->assertFalse($dmPhone->shouldRender4kLayout());
    }

    /* ============================================================
       PER-REQUEST SINGLETON TESTS
       ============================================================ */

    public function testSingletonReturnsSameInstance(): void
    {
        $dm1 = DeviceManager::instance();
        $dm2 = DeviceManager::instance();
        $this->assertSame($dm1, $dm2, 'instance() must return the same object on repeated calls');
    }

    public function testSingletonWithOverrides(): void
    {
        $dm1 = DeviceManager::instance(['viewMode' => 'home']);
        $this->assertSame('home', $dm1->viewMode());

        // Override viewMode — should update existing instance
        $dm2 = DeviceManager::instance(['viewMode' => 'studio']);
        $this->assertSame('studio', $dm2->viewMode());
        $this->assertSame($dm1, $dm2, 'Same instance must be returned');
    }

    public function testResetInstanceCreatesFreshObject(): void
    {
        $dm1 = DeviceManager::instance();
        DeviceManager::resetInstance();
        $dm2 = DeviceManager::instance();
        $this->assertNotSame($dm1, $dm2, 'resetInstance() must destroy the previous singleton');
    }

    public function testSingletonSharesAcrossTemplates(): void
    {
        // Simulates header.php creating instance, then home.php reusing it
        $headerDm = DeviceManager::instance([
            'viewportW' => 1024,
            'viewportH' => 600,
        ]);
        $this->assertTrue($headerDm->shouldRenderEmbeddedLayout());

        // home.php reuses same instance
        $homeDm = DeviceManager::instance();
        $this->assertSame($headerDm, $homeDm);
        $this->assertTrue($homeDm->shouldRenderEmbeddedLayout());
    }
}
