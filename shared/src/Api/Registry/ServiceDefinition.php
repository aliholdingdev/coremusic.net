<?php
declare(strict_types=1);

/**
 * Service Definition Value Object.
 *
 * @file ServiceDefinition.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Registry;

/**
 * Service Definition Value Object.
 */
final class ServiceDefinition
{
    public function __construct(
        private readonly string $name,
        private readonly string $url,
        private readonly string $healthEndpoint = '/health',
        private readonly int $timeout = 5,
        private readonly string $version = '1.0.0',
        private readonly string $status = 'unknown'
    ) {}

    /**
     * Get service name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get service URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get health endpoint.
     */
    public function getHealthEndpoint(): string
    {
        return $this->healthEndpoint;
    }

    /**
     * Get timeout in seconds.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get service version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get service status.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'healthEndpoint' => $this->healthEndpoint,
            'timeout' => $this->timeout,
            'version' => $this->version,
            'status' => $this->status,
        ];
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            url: $data['url'] ?? '',
            healthEndpoint: $data['healthEndpoint'] ?? '/health',
            timeout: $data['timeout'] ?? 5,
            version: $data['version'] ?? '1.0.0',
            status: $data['status'] ?? 'unknown'
        );
    }

    /**
     * Create with updated status.
     */
    public function withStatus(string $status): self
    {
        return new self(
            name: $this->name,
            url: $this->url,
            healthEndpoint: $this->healthEndpoint,
            timeout: $this->timeout,
            version: $this->version,
            status: $status
        );
    }
}
