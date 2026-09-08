<?php
declare(strict_types=1);

/**
 * Desktop BFF Transformer.
 *
 * @file DesktopBff.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Bff;

use CoreMusic\Contracts\Api\BffInterface;

/**
 * Desktop BFF Transformer for desktop applications.
 */
final class DesktopBff implements BffInterface
{
    /**
     * Transform response for desktop clients.
     */
    public function transform(array $apiResponse, array $clientContext): array
    {
        $response = $apiResponse;
        
        // Add extended metadata payloads
        if (isset($response['data']) && is_array($response['data'])) {
            $response['data'] = $this->addExtendedMetadata($response['data']);
        }
        
        // Add system attributes
        $response['meta']['system'] = [
            'platform' => php_uname('s'),
            'phpVersion' => PHP_VERSION,
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        ];
        
        // Add client-specific metadata
        $response['meta']['clientType'] = 'desktop';
        $response['meta']['timestamp'] = $clientContext['timestamp'] ?? time();
        
        return $response;
    }

    /**
     * Get the client type.
     */
    public function getClientType(): string
    {
        return 'desktop';
    }

    /**
     * Add extended metadata to data.
     */
    private function addExtendedMetadata(array $data): array
    {
        // This would be implemented based on specific API responses
        // For example, adding file sizes, creation dates, etc.
        
        // Placeholder implementation
        return $data;
    }
}
