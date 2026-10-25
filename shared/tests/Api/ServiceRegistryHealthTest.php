<?php declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Registry\ServiceRegistry;
use PHPUnit\Framework\TestCase;

/**
 * PRD-API-M004: sağlık çağrısı her kayıtlı servis için cevap verir.
 * Ağ bağımlılığı yok: healthy yolu data:// URL ile, unhealthy yolu kapalı port ile.
 */
final class ServiceRegistryHealthTest extends TestCase
{
    public function testHealthCheckAnswersForEveryRegisteredService(): void
    {
        $registry = new ServiceRegistry();
        $registry->register('alive', 'data://text/plain,{"status":"up"}', '');
        $registry->register('dead', 'http://127.0.0.1:1', '/health');

        $results = $registry->healthCheck();

        $this->assertArrayHasKey('alive', $results);
        $this->assertArrayHasKey('dead', $results);
        $this->assertSame('healthy', $results['alive']['status']);
        $this->assertSame(['status' => 'up'], $results['alive']['data']);
        $this->assertSame('unhealthy', $results['dead']['status']);
        $this->assertSame('Connection failed', $results['dead']['message']);
    }

    public function testHealthCheckWithNoServicesReturnsEmptyArray(): void
    {
        $this->assertSame([], (new ServiceRegistry())->healthCheck());
    }

    public function testGetHealthyServicesIncludesOnlyHealthyOnes(): void
    {
        $registry = new ServiceRegistry();
        $registry->register('alive', 'data://text/plain,{"ok":1}', '');
        $registry->register('dead', 'http://127.0.0.1:1', '/health');

        $healthy = $registry->getHealthyServices();

        $this->assertArrayHasKey('alive', $healthy);
        $this->assertArrayNotHasKey('dead', $healthy);
    }

    public function testDiscoverUnknownServiceReturnsEmptyArray(): void
    {
        $this->assertSame([], (new ServiceRegistry())->discover('missing'));
    }
}
