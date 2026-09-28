<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Security;

use PHPUnit\Framework\TestCase;
use CoreMusic\Security\ReturnUrlPolicy;

final class ReturnUrlPolicyTest extends TestCase
{
    public function testEmptyUrlReturnsSlash(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl(null));
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl(''));
    }

    public function testRelativePathPasses(): void
    {
        $this->assertSame('/home', ReturnUrlPolicy::getSafeUrl('/home'));
        $this->assertSame('/auth/callback', ReturnUrlPolicy::getSafeUrl('/auth/callback'));
    }

    public function testAllowedHostPasses(): void
    {
        $this->assertSame('https://home.coremusic.net/', ReturnUrlPolicy::getSafeUrl('https://home.coremusic.net/'));
        $this->assertSame('https://auth.coremusic.net/login', ReturnUrlPolicy::getSafeUrl('https://auth.coremusic.net/login'));
    }

    public function testDisallowedHostRedirectsToSlash(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('https://evil.com/steal'));
    }

    public function testJavascriptSchemeBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('javascript:alert(1)'));
    }

    /* ============================================================
       isAllowed() — login redirect_uri zinciri (raw vs decode edilmiş)
       ============================================================ */

    public function testEncodedRedirectUriOnAllowedHostIsAllowed(): void
    {
        $this->assertTrue(ReturnUrlPolicy::isAllowed('http://home.coremusic.net:81/auth/callback?return=%2Fhome'));
        $this->assertTrue(ReturnUrlPolicy::isAllowed('https://auth.coremusic.net/login'));
    }

    public function testRelativeAndEmptyUrlsAreAllowed(): void
    {
        $this->assertTrue(ReturnUrlPolicy::isAllowed(''));
        $this->assertTrue(ReturnUrlPolicy::isAllowed('/'));
        $this->assertTrue(ReturnUrlPolicy::isAllowed('/dashboard'));
    }

    public function testDisallowedHostIsRejected(): void
    {
        $this->assertFalse(ReturnUrlPolicy::isAllowed('http://evil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('https://evil-coremusic.net/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('https://coremusic.net.evil.com/'));
    }

    public function testDangerousSchemeIsRejected(): void
    {
        $this->assertFalse(ReturnUrlPolicy::isAllowed('javascript:alert(1)'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('data:text/html,<script>alert(1)</script>'));
    }

    public function testUserinfoInRawUrlIsRejected(): void
    {
        $this->assertFalse(ReturnUrlPolicy::isAllowed('http://user:pass@home.coremusic.net/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('http://home.coremusic.net@evil.com/'));
    }

    public function testEncodedAuthorityDivergenceIsRejected(): void
    {
        // Raw parse host=evil.com (userinfo var), decode edilmiş parse
        // host=home.coremusic.net → open redirect + auth_key sızıntısı riski.
        $this->assertFalse(ReturnUrlPolicy::isAllowed('http://home.coremusic.net%23@evil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('http://home.coremusic.net%40evil.com/'));
    }
}
