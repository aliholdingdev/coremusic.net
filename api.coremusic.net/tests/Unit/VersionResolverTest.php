<?php declare(strict_types=1);

namespace CoreMusic\Api\Test\Unit;

use CoreMusic\Api\Router\VersionResolver;
use PHPUnit\Framework\TestCase;

/**
 * VersionResolverTest — ADR-113 API Versiyon Çözümleme Testleri (TASK-12)
 *
 * Sürüm öncelik sırası:
 * 1. URL path: /api/v1/..., /api/v2/...
 * 2. Accept header: application/vnd.coremusic.v2+json
 * 3. Varsayılan: v1
 */
final class VersionResolverTest extends TestCase
{
    private VersionResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new VersionResolver();
    }

    public function testResolveFromUrlPath(): void
    {
        self::assertSame('v1', $this->resolver->resolve('/api/v1/auth/login', []));
        self::assertSame('v2', $this->resolver->resolve('/api/v2/music/tracks', []));
    }

    public function testResolveFromAcceptHeader(): void
    {
        $headers = ['Accept' => 'application/vnd.coremusic.v2+json'];
        self::assertSame('v2', $this->resolver->resolve('/api/music/tracks', $headers));

        $headersLower = ['accept' => 'application/vnd.coremusic.v1+json'];
        self::assertSame('v1', $this->resolver->resolve('/api/music/tracks', $headersLower));
    }

    public function testUrlPathPrecedesAcceptHeader(): void
    {
        $headers = ['Accept' => 'application/vnd.coremusic.v2+json'];
        // URL path v1, Accept header v2 -> URL path kazanır
        self::assertSame('v1', $this->resolver->resolve('/api/v1/auth/login', $headers));
    }

    public function testFallbackToDefaultVersion(): void
    {
        self::assertSame('v1', $this->resolver->resolve('/api/auth/me', []));
        self::assertSame('v1', $this->resolver->resolve('/', []));
    }

    public function testUnsupportedUrlVersionThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported API version in URL: v3');

        $this->resolver->resolve('/api/v3/auth/login', []);
    }

    public function testUnsupportedAcceptHeaderVersionThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported API version in Accept header: v9');

        $this->resolver->resolve('/api/music/tracks', ['Accept' => 'application/vnd.coremusic.v9+json']);
    }

    public function testSupportedVersionsListAndHelper(): void
    {
        self::assertSame(['v1', 'v2'], VersionResolver::getSupportedVersions());
        self::assertTrue(VersionResolver::isSupported('v1'));
        self::assertTrue(VersionResolver::isSupported('v2'));
        self::assertFalse(VersionResolver::isSupported('v3'));
    }
}
