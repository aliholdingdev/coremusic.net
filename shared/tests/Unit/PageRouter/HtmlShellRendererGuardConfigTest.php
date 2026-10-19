<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use CoreMusic\Config\ConfigManager;
use CoreMusic\Config\DomainConfig;
use CoreMusic\PageRouter\HtmlShellRenderer;
use PHPUnit\Framework\TestCase;

/**
 * C-F-08 regresyonu — RouterConfig guard alanları.
 *
 * 1) protectedRoutes slash'siz çıkıyordu ('playlist') ama client
 *    normalizeUrl pathname ('/playlist') ile karşılaştırıyordu → includes()
 *    hiç eşleşmiyordu (guard'lar ölü kod).
 * 2) user: null hardcoded idi → authGuard hiçbir zaman gerçek kullanıcıyı göremiyordu.
 * 3) user payload'ı yalnız kimlik görünümüdür; session'a ait sır (csrf_token vb.) sızmamalı.
 */
final class HtmlShellRendererGuardConfigTest extends TestCase
{
    /** @param array<string, mixed> $sessionData @param list<string> $protectedRoutes */
    private function render(array $sessionData, array $protectedRoutes): string
    {
        $config       = new ConfigManager(['app' => ['name' => 'CoreMusicTest', 'debug' => false, 'version' => '1.0.0']]);
        $domainConfig = new DomainConfig();
        $domainConfig->setOverrides('http', 'home.coremusic.net', 81);

        $renderer = new HtmlShellRenderer($config, $domainConfig);

        return $renderer->render(
            '<div id="main-inner">x</div>',
            'home',
            [],
            'test-csrf-token',
            $protectedRoutes,
            $sessionData
        );
    }

    public function testProtectedRoutes_areEmittedWithLeadingSlash(): void
    {
        $html = $this->render([], ['home', 'playlist', 'ayarlar']);

        $this->assertStringContainsString(
            'protectedRoutes: ["/home","/playlist","/ayarlar"]',
            $html,
            'C-F-08: client normalizeUrl pathname\'i ile eşleşmesi için leading-slash zorunlu'
        );
    }

    public function testUser_isNullWhenNoSessionIdentity(): void
    {
        $html = $this->render([], []);

        $this->assertStringContainsString('user: null', $html);
    }

    public function testUser_payloadCarriesIdentityRolePermissions(): void
    {
        $html = $this->render([
            'MM_UserID'      => '018f00000000000000000000000000aa',
            'MM_Username'    => 'bayram',
            'MM_UserRole'    => 'admin',
            'MM_Permissions' => ['library.manage', 'user.delete'],
        ], []);

        $this->assertStringContainsString('"id":"018f00000000000000000000000000aa"', $html);
        $this->assertStringContainsString('"username":"bayram"', $html);
        $this->assertStringContainsString('"role":"admin"', $html);
        $this->assertStringContainsString('"permissions":["library.manage","user.delete"]', $html);
    }

    public function testUser_payloadNeverLeaksSessionSecrets(): void
    {
        $html = $this->render([
            'MM_UserID'   => '018f00000000000000000000000000aa',
            'MM_Username' => 'bayram',
            // Sır niteliğinde session değerleri — RouterConfig'e GİRMEMELİ:
            'csrf_token'            => 'SENTINEL_SERVER_CSRF',
            '_session_user_id'      => 'SENTINEL_SHOULD_NOT_APPEAR',
        ], []);

        $this->assertStringNotContainsString('SENTINEL_SERVER_CSRF', $html);
        $this->assertStringNotContainsString('SENTINEL_SHOULD_NOT_APPEAR', $html);
    }

    public function testUser_isJsonEncodedWithScriptEscaping(): void
    {
        // XSS: kimlik alanları inline <script> içine JSON_HEX_* ile gömülür.
        $html = $this->render([
            'MM_UserID'   => '018f00000000000000000000000000aa',
            'MM_Username' => '</script><img src=x onerror=alert(1)>',
        ], []);

        $this->assertStringNotContainsString('</script><img', $html, 'Kullanıcı adı script kapanışını kıramaz');
        $this->assertStringContainsString('user: {', $html);
    }
}
