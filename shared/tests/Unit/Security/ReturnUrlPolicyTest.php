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

    public function testProtocolRelativeUrlIsRejected(): void
    {
        // Açık redirect: '//' ve '/\' tarayıcıda dış host'a gider.
        // '/%2F...' ham tarafta masum görünür, decode sonrası protokol-relative.
        $this->assertFalse(ReturnUrlPolicy::isAllowed('//evil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('/\\evil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('/%2Fevil.com/'));

        // Korunan davranış: tek '/' ile başlayan path'ler aynı-origin.
        $this->assertTrue(ReturnUrlPolicy::isAllowed('/dashboard'));
        $this->assertTrue(ReturnUrlPolicy::isAllowed('/'));
    }

    public function testProtocolRelativeGetSafeUrlFallsBackToSlash(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('//evil.com/'));
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('/\\evil.com/'));
        $this->assertSame('/dashboard', ReturnUrlPolicy::getSafeUrl('/dashboard'));
    }

    /* ============================================================
       Tab/newline varyantı — WHATWG parser \t \n \r siler,
       '/%09/evil.com' tarayıcıda '//evil.com' olur (gerçek open redirect)
       ============================================================ */

    public function testTabAndNewlineProtocolRelativeVariantsAreRejected(): void
    {
        $this->assertFalse(ReturnUrlPolicy::isAllowed('/%09/evil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('/%0A/evil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('/%0D/evil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('/%0Aevil.com/'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed("/\t/evil.com/"));
        $this->assertFalse(ReturnUrlPolicy::isAllowed("/\n/evil.com/"));

        // Korunan davranış: normal path'ler temizlikten etkilenmemeli.
        $this->assertTrue(ReturnUrlPolicy::isAllowed('/dashboard'));
        $this->assertTrue(ReturnUrlPolicy::isAllowed('/muzik/dinle'));
    }

    public function testEncodedControlCharInQueryIsRejected(): void
    {
        // Query içi varyant: '%0A' tek katman decode'da literal newline
        // olur; hiçbir meşru redirect URL'inde ham kontrol karakteri yok.
        $this->assertFalse(ReturnUrlPolicy::isAllowed('return%0A=1'));
        $this->assertFalse(ReturnUrlPolicy::isAllowed('/dashboard?next=%0Aevil.com'));

        // getSafeUrl() ikinci savunma hattı da aynı reddetmeyi üretir;
        // kontrol karakteri içermeyen normal path'ler korunur.
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('return%0A=1'));
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('/dashboard?next=%0Aevil.com'));
        $this->assertSame('/dashboard', ReturnUrlPolicy::getSafeUrl('/dashboard'));
    }

    public function testTabProtocolRelativeIsSanitizedToSlashInGetSafeUrl(): void
    {
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl('/%09/evil.com/'));
        $this->assertSame('/', ReturnUrlPolicy::getSafeUrl("/\t/evil.com/"));
        $this->assertSame('/muzik/dinle', ReturnUrlPolicy::getSafeUrl('/muzik/dinle'));
    }
}
