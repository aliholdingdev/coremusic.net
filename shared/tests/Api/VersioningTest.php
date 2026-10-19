<?php
declare(strict_types=1);

namespace CoreMusic\Test\Api;

use PHPUnit\Framework\TestCase;
use CoreMusic\Api\Versioning\ApiVersion;
use CoreMusic\Api\Versioning\VersionResolver;

final class VersioningTest extends TestCase
{
    public function testApiVersionEnumValues(): void
    {
        $this->assertEquals('v1', ApiVersion::V1->value);
        $this->assertEquals('v2', ApiVersion::V2->value);
        $this->assertEquals('internal', ApiVersion::INTERNAL->value);
        $this->assertEquals('public', ApiVersion::PUBLIC->value);
        $this->assertEquals('admin', ApiVersion::ADMIN->value);
    }

    public function testApiVersionTryFromValid(): void
    {
        $this->assertEquals(ApiVersion::V1, ApiVersion::tryFrom('v1'));
        $this->assertEquals(ApiVersion::V2, ApiVersion::tryFrom('v2'));
        $this->assertEquals(ApiVersion::ADMIN, ApiVersion::tryFrom('admin'));
    }

    public function testApiVersionTryFromInvalid(): void
    {
        $this->assertNull(ApiVersion::tryFrom('v3'));
        $this->assertNull(ApiVersion::tryFrom(''));
        $this->assertNull(ApiVersion::tryFrom('invalid'));
    }

    public function testApiVersionCasesCount(): void
    {
        $cases = ApiVersion::cases();
        $this->assertCount(5, $cases);
    }

    public function testVersionResolverGetVersionPath(): void
    {
        $resolver = new VersionResolver();
        $this->assertEquals('/api/v1', $resolver->getVersionPath(ApiVersion::V1));
        $this->assertEquals('/api/v2', $resolver->getVersionPath(ApiVersion::V2));
        $this->assertEquals('/api/internal', $resolver->getVersionPath(ApiVersion::INTERNAL));
        $this->assertEquals('/api/public', $resolver->getVersionPath(ApiVersion::PUBLIC));
        $this->assertEquals('/api/admin', $resolver->getVersionPath(ApiVersion::ADMIN));
    }

    public function testVersionResolverCanBeInstantiated(): void
    {
        $resolver = new VersionResolver();
        $this->assertInstanceOf(VersionResolver::class, $resolver);
    }
}
