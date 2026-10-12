<?php
declare(strict_types=1);

/**
 * BFF (Backend for Frontend) Layer.
 *
 * @file BffLayer.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Bff;

use CoreMusic\Contracts\Api\BffInterface;

/**
 * BFF Layer for client-specific response transformation.
 */
final class BffLayer
{
    private array $transformers = [];

    /**
     * Register a BFF transformer.
     */
    public function register(BffInterface $transformer): void
    {
        $this->transformers[$transformer->getClientType()] = $transformer;
    }

    /**
     * Transform response for specific client type.
     */
    public function transform(array $response, string $clientType): array
    {
        $transformer = $this->transformers[$clientType] ?? null;
        
        if ($transformer === null) {
            // Default to SPA transformer if available
            $transformer = $this->transformers['spa'] ?? null;
        }
        
        if ($transformer === null) {
            // No transformer available, return original response
            return $response;
        }
        
        return $transformer->transform($response, $this->getClientContext($clientType));
    }

    /**
     * Get client context based on client type.
     */
    private function getClientContext(string $clientType): array
    {
        return [
            'clientType' => $clientType,
            'timestamp' => time(),
            'requestId' => $_SERVER['HTTP_X_REQUEST_ID'] ?? uniqid(),
        ];
    }

    /**
     * Get client type from request.
     */
    public static function getClientTypeFromRequest(): string
    {
        // Check X-Client-Type header first
        $clientType = $_SERVER['HTTP_X_CLIENT_TYPE'] ?? null;
        if ($clientType !== null) {
            return strtolower($clientType);
        }
        
        // Fallback to query parameter
        $clientType = $_GET['client_type'] ?? null;
        if ($clientType !== null) {
            return strtolower($clientType);
        }
        
        // Default to spa
        return 'spa';
    }
}
