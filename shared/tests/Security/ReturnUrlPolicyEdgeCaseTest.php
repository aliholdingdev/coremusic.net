<?php declare(strict_types=1);

namespace CoreMusic\Test\Security;

use PHPUnit\Framework\TestCase;
use CoreMusic\Security\ReturnUrlPolicy;

/**
 * ReturnUrlPolicy — Edge Case Security Tests
 * OWASP A01 (Broken Access Control) + A03 (Injection) coverage
 *
 * @covers \CoreMusic\Security\ReturnUrlPolicy
 */
final class ReturnUrlPolicyEdgeCaseTest extends TestCase
{
    /* ============================================================
       SCHEME INJECTION TESTS (OWASP A03)
       ============================================================ */

    public function testDataSchemeBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('data:text/html,<script>alert(1)</script>'));
    }

    public function testVbscriptSchemeBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('vbscript:MsgBox("XSS")'));
    }

    public function testJavascriptSchemeBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('javascript:document.cookie'));
    }

    public function testHttpSchemeAllowedForWhitelistedHost(): void
    {
        $result = ReturnUrlPolicy::getSafeUrl('http://localhost:81/home');
        $this->assertStringContainsString('localhost', $result);
    }

    /* ============================================================
       USERINFO INJECTION TESTS (OWASP A01)
       ============================================================ */

    public function testUserinfoInJECTIONBlocked(): void
    {
        // user:pass@host pattern — credential leakage riski
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('https://admin:password@evil.com/steal'));
    }

    public function testUserinfoWithoutPasswordBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('https://admin@evil.com/steal'));
    }

    public function testUserinfoWithColonBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('https://user:@coremusic.net/'));
    }

    /* ============================================================
       SUBDOMAIN MATCHING TESTS
       ============================================================ */

    public function testSubdomainOfAllowedHostPasses(): void
    {
        $result = ReturnUrlPolicy::getSafeUrl('https://api.coremusic.net/v1/songs');
        $this->assertStringContainsString('api.coremusic.net', $result);
    }

    public function testDeepSubdomainPasses(): void
    {
        $result = ReturnUrlPolicy::getSafeUrl('https://cdn.assets.coremusic.net/image.png');
        $this->assertStringContainsString('cdn.assets.coremusic.net', $result);
    }

    public function testEvilSubdomainBlocked(): void
    {
        // evil-coremusic.net is NOT a subdomain of coremusic.net
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('https://evil-coremusic.net/steal'));
    }

    public function testSimilarDomainBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('https://coremusic.net.evil.com/steal'));
    }

    /* ============================================================
       URL-ENCODED BYPASS TESTS
       ============================================================ */

    public function testUrlEncodedJavascriptBlocked(): void
    {
        $encoded = urlencode('javascript:alert(1)');
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl($encoded));
    }

    public function testDoubleEncodedBypassBlocked(): void
    {
        $doubleEncoded = urlencode(urlencode('javascript:alert(1)'));
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl($doubleEncoded));
    }

    public function testUrlEncodedUserinfoBlocked(): void
    {
        $encoded = urlencode('https://admin:pass@evil.com/');
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl($encoded));
    }

    /* ============================================================
       EDGE CASE URLS
       ============================================================ */

    public function testNullReturnsSlash(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl(null));
    }

    public function testEmptyStringReturnsSlash(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl(''));
    }

    public function testWhitespaceReturnsSlash(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('   '));
    }

    public function testFragmentOnlyReturnsSlash(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('#section'));
    }

    public function testRelativePathWithQueryPasses(): void
    {
        $this->assertSame('/home?page=2', ReturnUrlPolicy::getSafeUrl('/home?page=2'));
    }

    public function testRelativePathWithFragmentPasses(): void
    {
        $this->assertSame('/home#section', ReturnUrlPolicy::getSafeUrl('/home#section'));
    }

    /* ============================================================
       PORT-BASED BYPASS TESTS
       ============================================================ */

    public function testAllowedHostWithNonStandardPortPasses(): void
    {
        $result = ReturnUrlPolicy::getSafeUrl('https://localhost:81/home');
        $this->assertStringContainsString('localhost', $result);
    }

    public function testDisallowedHostWithPortBlocked(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('https://evil.com:443/steal'));
    }

    /* ============================================================
       WHITELIST COMPLETENESS
       ============================================================ */

    public function testAllWhitelistedHostsAllowed(): void
    {
        $hosts = [
            'coremusic.net',
            'home.coremusic.net',
            'auth.coremusic.net',
            'music.coremusic.net',
            'admin.coremusic.net',
            'localhost',
            '127.0.0.1',
        ];

        foreach ($hosts as $host) {
            $result = ReturnUrlPolicy::getSafeUrl("https://{$host}/test");
            $this->assertStringContainsString($host, $result, "Host {$host} should be allowed");
        }
    }

    public function testNonWhitelistedHostsBlocked(): void
    {
        $hosts = [
            'evil.com',
            'google.com',
            'facebook.com',
            'coremusic-evil.com',
        ];

        foreach ($hosts as $host) {
            $this->assertSame('/', ReturnUrlPolicy::getSafeUrl("https://{$host}/steal"), "Host {$host} should be blocked");
        }
    }
}
