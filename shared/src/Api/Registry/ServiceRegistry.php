<?php
declare(strict_types=1);

/**
 * Service Registry implementation.
 *
 * @file ServiceRegistry.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Registry;

use CoreMusic\Contracts\Api\ServiceRegistryInterface;

/**
 * Service Registry implementation.
 */
final class ServiceRegistry implements ServiceRegistryInterface
{
    private array $services = [];

    /**
     * Register a service.
     */
    public function register(string $name, string $url, string $healthEndpoint = '/health'): void
    {
        $this->services[$name] = new ServiceDefinition(
            name: $name,
            url: $url,
            healthEndpoint: $healthEndpoint
        );
    }

    /**
     * Discover a service by name.
     */
    public function discover(string $name): array
    {
        $service = $this->services[$name] ?? null;
        
        if ($service === null) {
            return [];
        }
        
        return $service->toArray();
    }

    /**
     * Check health of all registered services.
     */
    public function healthCheck(): array
    {
        $results = [];
        
        foreach ($this->services as $name => $service) {
            $results[$name] = $this->checkServiceHealth($service);
        }
        
        return $results;
    }

    /**
     * Get list of healthy services.
     */
    public function getHealthyServices(): array
    {
        $healthy = [];
        
        foreach ($this->services as $name => $service) {
            $health = $this->checkServiceHealth($service);
            if ($health['status'] === 'healthy') {
                $healthy[$name] = $service->toArray();
            }
        }
        
        return $healthy;
    }

    /**
     * Load services from configuration.
     */
    public function loadFromConfig(array $config): void
    {
        foreach ($config as $name => $serviceConfig) {
            $this->register(
                name: $name,
                url: $serviceConfig['url'] ?? '',
                healthEndpoint: $serviceConfig['healthEndpoint'] ?? '/health'
            );
        }
    }

    /**
     * Check health of a single service.
     */
    private function checkServiceHealth(ServiceDefinition $service): array
    {
        $url = $service->getUrl() . $service->getHealthEndpoint();
        
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => $service->getTimeout(),
                    'method' => 'GET',
                ],
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                return [
                    'status' => 'unhealthy',
                    'message' => 'Connection failed',
                ];
            }
            
            $data = json_decode($response, true);
            
            return [
                'status' => 'healthy',
                'message' => 'OK',
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'message' => $e->getMessage(),
            ];
        }
    }
}
