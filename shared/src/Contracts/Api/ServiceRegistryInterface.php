<?php
declare(strict_types=1);

/**
 * Service Registry interface for service discovery and health checks.
 *
 * @file ServiceRegistryInterface.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Contracts\Api;

/**
 * Interface for service registry management.
 */
interface ServiceRegistryInterface
{
    /**
     * Register a service.
     *
     * @param string $name Service name
     * @param string $url Service URL
     * @param string $healthEndpoint Health check endpoint
     * @return void
     */
    public function register(string $name, string $url, string $healthEndpoint = '/health'): void;

    /**
     * Discover a service by name.
     *
     * @param string $name Service name
     * @return array Service details
     */
    public function discover(string $name): array;

    /**
     * Check health of all registered services.
     *
     * @return array Health status of all services
     */
    public function healthCheck(): array;

    /**
     * Get list of healthy services.
     *
     * @return array List of healthy services
     */
    public function getHealthyServices(): array;
}
