<?php declare(strict_types=1);

namespace CoreMusic\Api\Router;

/**
 * VersionResolver — API versiyon çözümleme (URL path + Accept header).
 *
 * Tek sorumluluk: İstekten API versiyonunu belirlemek.
 * Desteklenen kaynaklar (öncelik sırası):
 * 1. URL path: /api/v1/..., /api/v2/...
 * 2. Accept header: application/vnd.coremusic.v2+json
 * 3. Varsayılan: v1
 */
final class VersionResolver
{
    private const DEFAULT_VERSION = 'v1';
    private const SUPPORTED_VERSIONS = ['v1', 'v2'];

    /**
     * İstekten API versiyonunu çözümle.
     *
     * @param string $requestUri  $_SERVER['REQUEST_URI']
     * @param array<string, string> $headers  getallheaders() çıktısı
     * @return string Versiyon (v1, v2, ...)
     * @throws \RuntimeException Desteklenmeyen versiyon
     */
    public function resolve(string $requestUri, array $headers): string
    {
        // 1. URL path: /api/v1/..., /api/v2/...
        if (preg_match('#^/api/(v\d+)/#', $requestUri, $matches)) {
            $version = $matches[1];
            if (in_array($version, self::SUPPORTED_VERSIONS, true)) {
                return $version;
            }
            throw new \RuntimeException("Unsupported API version in URL: {$version}");
        }

        // 2. Accept header: application/vnd.coremusic.v2+json
        $accept = $headers['Accept'] ?? $headers['accept'] ?? '';
        if (preg_match('/application\/vnd\.coremusic\.(v\d+)\+json/i', $accept, $matches)) {
            $version = strtolower($matches[1]);
            if (in_array($version, self::SUPPORTED_VERSIONS, true)) {
                return $version;
            }
            throw new \RuntimeException("Unsupported API version in Accept header: {$version}");
        }

        // 3. Varsayılan
        return self::DEFAULT_VERSION;
    }

    /**
     * Desteklenen versiyonları döndür.
     *
     * @return list<string>
     */
    public static function getSupportedVersions(): array
    {
        return self::SUPPORTED_VERSIONS;
    }

    /**
     * Versiyonun desteklenip desteklenmediğini kontrol et.
     */
    public static function isSupported(string $version): bool
    {
        return in_array($version, self::SUPPORTED_VERSIONS, true);
    }
}