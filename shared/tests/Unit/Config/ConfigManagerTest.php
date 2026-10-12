<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Config;

use PHPUnit\Framework\TestCase;
use CoreMusic\Config\ConfigManager;

final class ConfigManagerTest extends TestCase
{
    public function testGetWithDotNotation(): void
    {
        $config = new ConfigManager([
            'app' => ['name' => 'CoreMusic', 'env' => 'production'],
            'db' => ['host' => 'localhost'],
        ]);

        $this->assertSame('CoreMusic', $config->get('app.name'));
        $this->assertSame('production', $config->get('app.env'));
        $this->assertSame('localhost', $config->get('db.host'));
    }

    public function testGetWithDefault(): void
    {
        $config = new ConfigManager(['app' => ['name' => 'CoreMusic']]);
        $this->assertSame('default', $config->get('app.missing', 'default'));
        $this->assertNull($config->get('nonexistent.key'));
    }

    public function testGetSecureThrowsOnMissing(): void
    {
        $this->expectException(\CoreMusic\Exception\ServerException::class);
        $config = new ConfigManager([]);
        $config->getSecure('missing.key');
    }

    public function testSetAndGetRoundTrip(): void
    {
        $config = new ConfigManager([]);
        $config->set('app.name', 'TestApp');
        $this->assertSame('TestApp', $config->get('app.name'));
    }

    public function testHasReturnsCorrectly(): void
    {
        $config = new ConfigManager(['app' => ['name' => 'Test']]);
        $this->assertTrue($config->has('app.name'));
        $this->assertFalse($config->has('app.missing'));
        $this->assertFalse($config->has('nonexistent'));
    }

    public function testIsProduction(): void
    {
        $prod = new ConfigManager(['app' => ['env' => 'production']]);
        $this->assertTrue($prod->isProduction());

        $dev = new ConfigManager(['app' => ['env' => 'development']]);
        $this->assertFalse($dev->isProduction());
    }

    public function testIsDevelopment(): void
    {
        $dev = new ConfigManager(['app' => ['env' => 'development']]);
        $this->assertTrue($dev->isDevelopment());

        $prod = new ConfigManager(['app' => ['env' => 'production']]);
        $this->assertFalse($prod->isDevelopment());
    }

    public function testMaskSecret(): void
    {
        $config = new ConfigManager([]);
        $masked = $config->maskSecret('abcdefghij', 3, 3);
        $this->assertSame('abc****hij', $masked);
    }

    public function testAllReturnsFullConfig(): void
    {
        $data = ['app' => ['name' => 'Test'], 'db' => ['host' => 'localhost']];
        $config = new ConfigManager($data);
        $this->assertSame($data, $config->all());
    }

    public function testClearCacheResetsCache(): void
    {
        $config = new ConfigManager(['app' => ['name' => 'Test']]);
        $config->get('app.name'); // populate cache
        $config->clearCache();
        // Should still work after cache clear
        $this->assertSame('Test', $config->get('app.name'));
    }
}
