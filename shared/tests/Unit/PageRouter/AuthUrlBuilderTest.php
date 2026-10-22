<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\PageRouter;

use PHPUnit\Framework\TestCase;
use CoreMusic\PageRouter\AuthUrlBuilder;
use CoreMusic\PageRouter\PageRouterHelper;
use CoreMusic\Config\DomainConfig;

final class AuthUrlBuilderTest extends TestCase
{
    private AuthUrlBuilder $builder;

    protected function setUp(): void
    {
        $domainConfig = new DomainConfig();
        $domainConfig->setOverrides('https', 'home.coremusic.net', 443);
        $helper = new PageRouterHelper();
        $this->builder = new AuthUrlBuilder($domainConfig, $helper);
    }

    public function testBuildAuthRedirectLogin(): void
    {
        $result = $this->builder->buildAuthRedirect('login');
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('headers', $result);
        $this->assertStringContainsString('auth.coremusic.net', $result['url']);
        $this->assertStringContainsString('/login', $result['url']);
    }

    public function testBuildAuthRedirectWithReturnPath(): void
    {
        $result = $this->builder->buildAuthRedirect('login', '/dashboard');
        $this->assertStringContainsString('redirect_uri', $result['url']);
        $this->assertStringContainsString('dashboard', $result['url']);
    }

    public function testRedirectAuthSpaReturns403(): void
    {
        $result = $this->builder->redirectAuth('login', null, true);
        $this->assertSame(403, $result['httpStatus']);
        $this->assertArrayHasKey('redirect', $result['body']);
    }

    public function testRedirectAuthFullReturns302(): void
    {
        $result = $this->builder->redirectAuth('login', null, false);
        $this->assertSame(302, $result['httpStatus']);
        $this->assertArrayHasKey('Location', $result['headers']);
    }

    public function testLogoutUrlContainsRedirect(): void
    {
        $result = $this->builder->buildAuthRedirect('logout');
        $this->assertStringContainsString('logout', $result['url']);
        $this->assertStringContainsString('redirect', $result['url']);
    }

    public function testAuthHeadersAreSet(): void
    {
        $result = $this->builder->buildAuthRedirect('login');
        $this->assertSame('true', $result['headers']['X-Auth-Required']);
        $this->assertSame('unauthenticated', $result['headers']['X-Auth-Status']);
    }

    public function testIsAdminContextFalseOnHomeHost(): void
    {
        // setUp host = home.coremusic.net → admin context değil.
        $this->assertFalse($this->builder->isAdminContext());
    }

    public function testIsAdminContextTrueOnAdminHost(): void
    {
        $domainConfig = new DomainConfig();
        $domainConfig->setOverrides('https', 'admin.coremusic.net', 443);
        $builder = new AuthUrlBuilder($domainConfig, new PageRouterHelper());
        $this->assertTrue($builder->isAdminContext());
    }

    public function testAdminLoginRedirectUsesAdminClientId(): void
    {
        // admin/ önekli rota → client_id=coremusic-admin (ADR-043).
        $result = $this->builder->buildAuthRedirect('admin/login');
        $this->assertStringContainsString('/admin/login', $result['url']);
        $this->assertStringContainsString('client_id=coremusic-admin', $result['url']);
    }
}
