<?php
declare(strict_types=1);

/**
 * Gateway interface for API request dispatching.
 *
 * @file GatewayInterface.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Contracts\Api;

/**
 * Interface for API Gateway request dispatching.
 */
interface GatewayInterface
{
    /**
     * Dispatch an API request and return the response.
     *
     * @param array $request The incoming request data
     * @return array The response data
     */
    public function dispatch(array $request): array;
}
