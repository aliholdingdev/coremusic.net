<?php
declare(strict_types=1);

/**
 * Mobile BFF Transformer.
 *
 * @file MobileBff.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Bff;

use CoreMusic\Contracts\Api\BffInterface;

/**
 * Mobile BFF Transformer for mobile applications.
 */
final class MobileBff implements BffInterface
{
    /**
     * Transform response for mobile clients.
     */
    public function transform(array $apiResponse, array $clientContext): array
    {
        $response = $apiResponse;
        
        // Strip redundant fields
        if (isset($response['data']) && is_array($response['data'])) {
            $response['data'] = $this->stripRedundantFields($response['data']);
        }
        
        // Structure pagination for mobile
        if (isset($response['meta']['pagination'])) {
            $response['meta']['pagination'] = $this->structurePagination(
                $response['meta']['pagination']
            );
        }
        
        // Optimize CDN image URLs
        if (isset($response['data']['imageUrl'])) {
            $response['data']['imageUrl'] = $this->optimizeImageUrl(
                $response['data']['imageUrl']
            );
        }
        
        // Add client-specific metadata
        $response['meta']['clientType'] = 'mobile';
        $response['meta']['timestamp'] = $clientContext['timestamp'] ?? time();
        
        return $response;
    }

    /**
     * Get the client type.
     */
    public function getClientType(): string
    {
        return 'mobile';
    }

    /**
     * Strip redundant fields for mobile.
     */
    private function stripRedundantFields(array $data): array
    {
        // Remove fields that are not needed on mobile
        $fieldsToRemove = ['internalId', 'debugInfo', 'serverTimestamp'];
        
        foreach ($fieldsToRemove as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }

    /**
     * Structure pagination for mobile.
     */
    private function structurePagination(array $pagination): array
    {
        return [
            'current' => $pagination['page'] ?? 1,
            'per_page' => $pagination['pageSize'] ?? 20,
            'total' => $pagination['totalItems'] ?? 0,
            'has_more' => ($pagination['page'] ?? 1) < ($pagination['totalPages'] ?? 1),
        ];
    }

    /**
     * Optimize image URL for CDN.
     */
    private function optimizeImageUrl(string $url): string
    {
        // Add CDN parameters for mobile optimization
        // This would be implemented based on specific CDN requirements
        
        // Placeholder implementation
        return $url;
    }
}
