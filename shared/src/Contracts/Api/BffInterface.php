<?php
declare(strict_types=1);

/**
 * BFF (Backend for Frontend) interface for response transformation.
 *
 * @file BffInterface.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Contracts\Api;

/**
 * Interface for BFF response transformation.
 */
interface BffInterface
{
    /**
     * Transform API response for specific client type.
     *
     * @param array $apiResponse The original API response
     * @param array $clientContext Client-specific context
     * @return array Transformed response
     */
    public function transform(array $apiResponse, array $clientContext): array;

    /**
     * Get the client type this BFF handles.
     *
     * @return string Client type identifier
     */
    public function getClientType(): string;
}
