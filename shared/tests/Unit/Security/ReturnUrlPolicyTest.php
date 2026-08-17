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
}
