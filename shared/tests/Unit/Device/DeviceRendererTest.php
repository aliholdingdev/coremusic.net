<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Device;

use PHPUnit\Framework\TestCase;
use CoreMusic\Device\DeviceManager;
use CoreMusic\Device\DeviceRenderer;

final class DeviceRendererTest extends TestCase
{
    protected function tearDown(): void
    {
        DeviceManager::resetInstance();
    }

    public function testTierOfMapsAllDevices(): void
    {
        $expected = [
            'phone'       => 'phone',
            'embedded'    => 'embedded',
            'tablet'      => 'embedded',
            'laptop'      => 'wide',
            'desktop'     => 'wide',
            '4k-tv'       => 'wide',
            '4k-monitor'  => 'wide',
        ];

        foreach ($expected as $device => $tier) {
            $this->assertSame($tier, DeviceRenderer::tierOf($device), "tierOf($device)");
        }
    }

    public function testInvalidDeviceThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DeviceRenderer::fromShell('unknown-device', false, 'home', 'https://assets', '1');
    }

    public function testHomeHeadLinksContainClientContractIds(): void
    {
        $r = DeviceRenderer::fromShell('desktop', false, 'home', 'https://assets.test', '123');

        $html = $r->headLinks();

        $this->assertStringContainsString('id="cm-device-css"', $html);
        $this->assertStringContainsString('id="cm-view-css"', $html);
        $this->assertStringNotContainsString('cm-auth-bundled', $html);
        $this->assertStringContainsString('08_Devices/d-desktop.css', $html);
        $this->assertStringContainsString('09_ViewModes/v-home.css', $html);
        $this->assertStringContainsString('03_Layout/_sidebar.css', $html);
    }

    public function testAuthHeadLinksContainBundledAndDevice(): void
    {
        $r = DeviceRenderer::fromShell('embedded', true, 'home', 'https://assets.test', '123');

        $html = $r->headLinks();

        $this->assertStringContainsString('id="cm-auth-bundled"', $html);
        $this->assertStringContainsString('id="cm-device-css"', $html);
        $this->assertStringNotContainsString('id="cm-view-css"', $html);
        $this->assertStringContainsString('auth-bundled.css', $html);
        $this->assertStringContainsString('d-auth-embedded.css', $html);
    }

    public function testTierAttributeIsEscapedAndPresent(): void
    {
        $r = DeviceRenderer::fromShell('4k-tv', false, 'home', 'https://assets.test', '1');

        $this->assertSame(' data-tier="wide"', $r->tierAttribute());
    }

    public function testLoaderAttributesExposeServerDevice(): void
    {
        $r = DeviceRenderer::fromShell('phone', false, 'home', 'https://assets.test', '1', ' nonce="abc"');

        $attrs = $r->loaderAttributes('dark');

        $this->assertStringContainsString('data-cm-device-loader', $attrs);
        $this->assertStringContainsString('data-server-device="phone"', $attrs);
        $this->assertStringContainsString('data-view-mode="home"', $attrs);
        $this->assertStringContainsString('data-is-auth="false"', $attrs);
        $this->assertStringContainsString('data-color-mode="dark"', $attrs);
        $this->assertStringContainsString('data-assets-url="https://assets.test"', $attrs);
    }

    public function testDeviceCssPathFollowsMap(): void
    {
        $home = DeviceRenderer::fromShell('4k-monitor', false, 'studio', 'https://assets.test', '1');
        $this->assertSame('08_Devices/d-4k.css', $home->deviceCssPath());
        $this->assertSame('09_ViewModes/v-studio.css', $home->viewCssPath());

        $auth = DeviceRenderer::fromShell('4k-monitor', true, 'home', 'https://assets.test', '1');
        $this->assertSame('08_Devices/d-auth-4k-monitor.css', $auth->deviceCssPath());
    }
}
