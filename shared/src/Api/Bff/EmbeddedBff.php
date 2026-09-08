<?php
declare(strict_types=1);

/**
 * Embedded BFF Transformer.
 *
 * @file EmbeddedBff.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Bff;

use CoreMusic\Contracts\Api\BffInterface;

/**
 * Embedded BFF Transformer for embedded devices (RPi5, etc.).
 */
final class EmbeddedBff implements BffInterface
{
    /**
     * Transform response for embedded clients.
     */
    public function transform(array $apiResponse, array $clientContext): array
    {
        $response = $apiResponse;
        
        // Minify payload for low-bandwidth
        if (isset($response['data']) && is_array($response['data'])) {
            $response['data'] = $this->minifyPayload($response['data']);
        }
        
        // Strip meta information to minimum
        if (isset($response['meta'])) {
            $response['meta'] = $this->minifyMeta($response['meta']);
        }
        
        // Add client-specific metadata
        $response['meta']['clientType'] = 'embedded';
        $response['meta']['timestamp'] = $clientContext['timestamp'] ?? time();
        
        return $response;
    }

    /**
     * Get the client type.
     */
    public function getClientType(): string
    {
        return 'embedded';
    }

    /**
     * Minify payload for low-bandwidth.
     */
    private function minifyPayload(array $data): array
    {
        // Remove unnecessary whitespace and shorten keys
        // This would be implemented based on specific requirements
        
        // Placeholder implementation
        return $data;
    }

    /**
     * Minify meta information.
     */
    private function minifyMeta(array $meta): array
    {
        // Keep only essential meta information
        return [
            'v' => $meta['version'] ?? '1.0.0',
            't' => $meta['timestamp'] ?? time(),
        ];
    }
}
