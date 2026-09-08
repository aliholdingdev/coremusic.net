<?php
declare(strict_types=1);

/**
 * API Response factory for standardized JSON envelopes.
 *
 * @file ApiResponse.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api;

use CoreMusic\Security\UuidV7;

/**
 * API Response factory with structured envelopes.
 */
final class ApiResponse
{
    private const API_VERSION = '1.0.0';

    /**
     * Create a successful response.
     */
    public static function ok(array $data = [], int $statusCode = 200): array
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('X-Request-ID: ' . UuidV7::generate());
        header('X-API-Version: ' . self::API_VERSION);

        return [
            'data' => $data,
            'meta' => [
                'timestamp' => (new \DateTimeImmutable())->format('c'),
                'version' => self::API_VERSION,
            ],
        ];
    }

    /**
     * Create an error response.
     */
    public static function error(
        string $code,
        string $message,
        int $statusCode = 400,
        array $details = []
    ): array {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('X-Request-ID: ' . UuidV7::generate());
        header('X-API-Version: ' . self::API_VERSION);

        $response = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if (!empty($details)) {
            $response['error']['details'] = $details;
        }

        return $response;
    }

    /**
     * Create a paginated response.
     */
    public static function paginated(
        array $data,
        int $page,
        int $pageSize,
        int $totalItems
    ): array {
        $totalPages = (int) ceil($totalItems / $pageSize);
        
        http_response_code(200);
        header('Content-Type: application/json');
        header('X-Request-ID: ' . UuidV7::generate());
        header('X-API-Version: ' . self::API_VERSION);

        return [
            'data' => $data,
            'meta' => [
                'pagination' => [
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'totalItems' => $totalItems,
                    'totalPages' => $totalPages,
                ],
                'timestamp' => (new \DateTimeImmutable())->format('c'),
                'version' => self::API_VERSION,
            ],
        ];
    }

    /**
     * Create a no content response.
     */
    public static function noContent(): array
    {
        http_response_code(204);
        header('X-Request-ID: ' . UuidV7::generate());
        header('X-API-Version: ' . self::API_VERSION);
        
        return [];
    }

    /**
     * Create a created response.
     */
    public static function created(array $data): array
    {
        http_response_code(201);
        header('Content-Type: application/json');
        header('X-Request-ID: ' . UuidV7::generate());
        header('X-API-Version: ' . self::API_VERSION);

        return [
            'data' => $data,
            'meta' => [
                'timestamp' => (new \DateTimeImmutable())->format('c'),
                'version' => self::API_VERSION,
            ],
        ];
    }
}
