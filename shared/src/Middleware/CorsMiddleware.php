<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;

/**
 * Cors Middleware (L1 — Pipeline #2)
 *
 * CORS header'larını ayarlar. OriginCheck'ten sonra çalışır.
 * OPTIONS preflight isteklerini 204 ile yanıtlar.
 *
 * ADR-010/022 uyumlu. Frozen sıra: OriginCheck → Cors → RateLimiter → ...
 */
final class CorsMiddleware implements IMiddleware
{
    /** @var string[] */
    private readonly array $allowedMethods;

    /** @var string[] */
    private readonly array $allowedHeaders;

    private readonly bool $allowCredentials;

    /**
     * @param array{allowed_methods?: string[], allowed_headers?: string[], allow_credentials?: bool} $corsConfig
     */
    public function __construct(
        ?array $corsConfig = null,
    ) {
        $this->allowedMethods   = $corsConfig['allowed_methods'] ?? ['GET', 'POST', 'OPTIONS'];
        $this->allowedHeaders   = $corsConfig['allowed_headers'] ?? ['Content-Type', 'X-CSRF-Token', 'X-Requested-With'];
        $this->allowCredentials = $corsConfig['allow_credentials'] ?? true;
    }

    public function handle(array $request, callable $next): array
    {
        $origin  = $request['server']['HTTP_ORIGIN'] ?? '';
        $method  = strtoupper($request['method'] ?? 'GET');

        if ($origin !== '') {
            // OriginCheck zaten izin verdi, header'ları ayarla
            $response = $method === 'OPTIONS'
                ? $this->preflightResponse()
                : $next($request);

            $response['headers'] = array_merge($response['headers'] ?? [], [
                'Access-Control-Allow-Origin'      => $origin,
                'Access-Control-Allow-Credentials'  => $this->allowCredentials ? 'true' : 'false',
                'Access-Control-Allow-Methods'      => implode(', ', $this->allowedMethods),
                'Access-Control-Allow-Headers'      => implode(', ', $this->allowedHeaders),
                'Access-Control-Max-Age'            => '86400',
            ]);

            return $response;
        }

        // Origin yoksa → normal devam
        return $next($request);
    }

    private function preflightResponse(): array
    {
        return [
            'httpStatus' => 204,
            'type'       => 'json',
            'body'       => '',
            'headers'    => [],
            'halt'       => true,
        ];
    }
}
