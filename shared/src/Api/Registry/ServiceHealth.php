<?php
declare(strict_types=1);

/**
 * Service Health Aggregator.
 *
 * @file ServiceHealth.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Registry;

/**
 * Service Health Aggregator for distributed health checks.
 */
final class ServiceHealth
{
    public function __construct(
        private readonly ServiceRegistry $registry
    ) {}

    /**
     * Execute parallel health checks.
     */
    public function checkAll(): array
    {
        return $this->registry->healthCheck();
    }

    /**
     * Get aggregated health status.
     */
    public function getAggregatedStatus(): array
    {
        $healthResults = $this->registry->healthCheck();
        
        $totalServices = count($healthResults);
        $healthyCount = 0;
        $degradedCount = 0;
        $unhealthyCount = 0;
        
        foreach ($healthResults as $result) {
            match ($result['status']) {
                'healthy' => $healthyCount++,
                'degraded' => $degradedCount++,
                'unhealthy' => $unhealthyCount++,
                default => null,
            };
        }
        
        // Determine overall status
        $overallStatus = 'healthy';
        if ($unhealthyCount > 0) {
            $overallStatus = 'unhealthy';
        } elseif ($degradedCount > 0) {
            $overallStatus = 'degraded';
        }
        
        return [
            'overall' => $overallStatus,
            'services' => [
                'total' => $totalServices,
                'healthy' => $healthyCount,
                'degraded' => $degradedCount,
                'unhealthy' => $unhealthyCount,
            ],
            'details' => $healthResults,
            'timestamp' => time(),
        ];
    }

    /**
     * Check if a specific service is healthy.
     */
    public function isServiceHealthy(string $serviceName): bool
    {
        $healthResults = $this->registry->healthCheck();
        
        return isset($healthResults[$serviceName]) 
            && $healthResults[$serviceName]['status'] === 'healthy';
    }

    /**
     * Get unhealthy services.
     */
    public function getUnhealthyServices(): array
    {
        $healthResults = $this->registry->healthCheck();
        $unhealthy = [];
        
        foreach ($healthResults as $name => $result) {
            if ($result['status'] !== 'healthy') {
                $unhealthy[$name] = $result;
            }
        }
        
        return $unhealthy;
    }
}
