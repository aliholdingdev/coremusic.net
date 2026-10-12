<?php
declare(strict_types=1);

/**
 * SPA BFF Transformer.
 *
 * @file SpaBff.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Bff;

use CoreMusic\Contracts\Api\BffInterface;

/**
 * SPA BFF Transformer for web applications.
 */
final class SpaBff implements BffInterface
{
    /**
     * Transform response for SPA clients.
     */
    public function transform(array $apiResponse, array $clientContext): array
    {
        $response = $apiResponse;
        
        // Enrich with nested relational graphs
        if (isset($response['data']) && is_array($response['data'])) {
            $response['data'] = $this->enrichWithRelations($response['data']);
        }
        
        // Add auth token if available
        if (isset($clientContext['authToken'])) {
            $response['meta']['authToken'] = $clientContext['authToken'];
        }
        
        // Add client-specific metadata
        $response['meta']['clientType'] = 'spa';
        $response['meta']['timestamp'] = $clientContext['timestamp'] ?? time();
        
        return $response;
    }

    /**
     * Get the client type.
     */
    public function getClientType(): string
    {
        return 'spa';
    }

    /**
     * Enrich data with nested relational graphs.
     */
    private function enrichWithRelations(array $data): array
    {
        // This would be implemented based on specific API responses
        // For example, adding related resources inline
        
        // Placeholder implementation
        return $data;
    }
}
