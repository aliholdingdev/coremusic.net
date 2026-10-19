<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Config;

use PHPUnit\Framework\TestCase;
use CoreMusic\Config\ConfigManager;

final class ConfigManagerTest extends TestCase
{
    /** @var array<string, string|false> */
    private array $envBackup = [];

    /** @var array<string, mixed> */
    private array $underscoreEnvBackup = [];

    private const TEST_KEYS = ['CM_TEST_ZERO', 'CM_TEST_UNSET', 'CM_TEST_ABC', 'CM_TEST_PRIORITY'];

    protected function setUp(): void
    {
        foreach (self::TEST_KEYS as $key) {
            $this->envBackup[$key] = getenv($key);
            $this->underscoreEnvBackup[$key] = $_ENV[$key] ?? null;
            putenv($key);
            unset($_ENV[$key]);
        }
    }

    protected function tearDown(): void
    {
        foreach (self::TEST_KEYS as $key) {
            putenv($this->envBackup[$key] === false ? $key : $key . '=' . $this->envBackup[$key]);
            if ($this->underscoreEnvBackup[$key] === null) {
                unset($_ENV[$key]);
            } else {
                $_ENV[$key] = $this->underscoreEnvBackup[$key];
            }
        }
    }

    public function testGetEnvZeroIsNotSwallowed(): void
    {
        putenv('CM_TEST_ZERO=0');

        $this->assertSame('0', (new ConfigManager())->getEnv('CM_TEST_ZERO', 'fallback'));
    }

    public function testGetEnvUnsetReturnsDefault(): void
    {
        $config = new ConfigManager();

        $this->assertSame('fallback', $config->getEnv('CM_TEST_UNSET', 'fallback'));
        $this->assertNull($config->getEnv('CM_TEST_UNSET'));
    }

    public function testGetEnvReturnsPlainStringValue(): void
    {
        putenv('CM_TEST_ABC=abc');

        $this->assertSame('abc', (new ConfigManager())->getEnv('CM_TEST_ABC', 'fallback'));
    }

    public function testGetEnvEnvSuperglobalHasPriority(): void
    {
        putenv('CM_TEST_PRIORITY=from-putenv');
        $_ENV['CM_TEST_PRIORITY'] = 'from-underscore-env';

        $this->assertSame('from-underscore-env', (new ConfigManager())->getEnv('CM_TEST_PRIORITY', 'fallback'));
    }

    public function testGetEnvZeroViaEnvSuperglobal(): void
    {
        $_ENV['CM_TEST_ZERO'] = '0';

        $this->assertSame('0', (new ConfigManager())->getEnv('CM_TEST_ZERO', 'fallback'));
    }

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
