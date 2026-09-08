<?php
declare(strict_types=1);

/**
 * API Version enum.
 *
 * @file ApiVersion.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Versioning;

/**
 * API Version enum with backed string values.
 */
enum ApiVersion: string
{
    case V1 = 'v1';
    case V2 = 'v2';
    case INTERNAL = 'internal';
    case PUBLIC = 'public';
    case ADMIN = 'admin';
}
