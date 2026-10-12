<?php
declare(strict_types=1);

/**
 * API Version resolver.
 *
 * @file VersionResolver.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Versioning;

use CoreMusic\Api\ApiRequest;

/**
 * Resolves API version from request.
 */
final class VersionResolver
{
    /**
     * Resolve API version from request.
     */
    public function resolve(ApiRequest $request): ApiVersion
    {
        // First, try to get version from URI path
        $uri = $request->getUri();
        $version = $this->extractVersionFromUri($uri);
        
        if ($version !== null) {
            return $version;
        }
        
        // Fallback to Accept-Version header
        $acceptVersion = $request->getHeader('Accept-Version');
        if ($acceptVersion !== null) {
            return $this->mapHeaderToVersion($acceptVersion);
        }
        
        // Default to V1
        return ApiVersion::V1;
    }

    /**
     * Get the version path prefix.
     */
    public function getVersionPath(ApiVersion $version): string
    {
        return '/api/' . $version->value;
    }

    /**
     * Extract version from URI path.
     */
    private function extractVersionFromUri(string $uri): ?ApiVersion
    {
        if (preg_match('#^/api/(v\d+|internal|public|admin)#', $uri, $matches)) {
            $versionString = $matches[1];
            return ApiVersion::tryFrom($versionString) ?? ApiVersion::V1;
        }
        
        return null;
    }

    /**
     * Map Accept-Version header to ApiVersion.
     */
    private function mapHeaderToVersion(string $header): ApiVersion
    {
        return match (strtolower($header)) {
            'v1' => ApiVersion::V1,
            'v2' => ApiVersion::V2,
            'internal' => ApiVersion::INTERNAL,
            'public' => ApiVersion::PUBLIC,
            'admin' => ApiVersion::ADMIN,
            default => ApiVersion::V1,
        };
    }
}
