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

    /** @var string[] */
    private readonly array $allowedOrigins;

    private readonly bool $allowCredentials;

    /**
     * @param array{allowed_methods?: string[], allowed_headers?: string[], allow_credentials?: bool, allowed_origins?: string[]} $corsConfig
     */
    public function __construct(
        ?array $corsConfig = null,
    ) {
        $this->allowedMethods   = $corsConfig['allowed_methods'] ?? ['GET', 'POST', 'OPTIONS'];
        // ADR-020 §2.2E: preflight, Bearer (Authorization) ve API key başlıklarını
        // düşürmemeli — aksi halde API istekleri tarayıcıdan 401 döner.
        $this->allowedHeaders   = $corsConfig['allowed_headers'] ?? [
            'Content-Type',
            'X-CSRF-Token',
            'X-Requested-With',
            'Authorization',
            'X-Api-Key',
        ];
        $this->allowCredentials = $corsConfig['allow_credentials'] ?? true;
        $this->allowedOrigins = array_map('strtolower', array_filter(array_map('trim', $corsConfig['allowed_origins'] ?? [])));
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
                'Vary' => 'Origin',
            ]);

            // Yalnız allowlist'e ait origin reflekt edilir (ADR-010/022 — wildcard yok,
            // allowCredentials=true ile birlikte rastgele origin yansıtmak yasak).
            if (!$this->isOriginAllowed($origin)) {
                return $response;
            }

            $response['headers'] = array_merge($response['headers'], [
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

    /**
     * Fail-closed origin kontrolü (ADR-010/022 — wildcard yok).
     *
     * İzin listesi hem tam origin ("https://auth.coremusic.net") hem de yalnız
     * host ("auth.coremusic.net") verilebilir; her iki biçim kabul edilir.
     * Şema ve port yalnızca izin girdisi belirttiyse bağlayıcıdır.
     */
    private function isOriginAllowed(string $origin): bool
    {
        $parsed = parse_url($origin);
        if ($parsed === false || empty($parsed['host'])) {
            return false;
        }

        $originLower  = strtolower($origin);
        $originScheme = strtolower((string) ($parsed['scheme'] ?? ''));
        $originHost   = strtolower((string) $parsed['host']);
        $originPort   = isset($parsed['port']) ? (int) $parsed['port'] : null;

        foreach ($this->allowedOrigins as $allowed) {
            // 1) Tam origin eşleşmesi
            if ($allowed === $originLower) {
                return true;
            }

            // 2) Host eşleşmesi (izin girdisi scheme içerebilir veya içermeyebilir)
            $allowedParsed = parse_url(str_contains($allowed, '://') ? $allowed : '//' . $allowed);
            if ($allowedParsed === false || empty($allowedParsed['host'])) {
                continue;
            }
            if (strtolower((string) $allowedParsed['host']) !== $originHost) {
                continue;
            }

            // Şema/port yalnızca izin girdisi belirttiyse bağlayıcıdır.
            $allowedScheme = strtolower((string) ($allowedParsed['scheme'] ?? ''));
            if ($allowedScheme !== '' && $allowedScheme !== $originScheme) {
                continue;
            }
            if (isset($allowedParsed['port']) && (int) $allowedParsed['port'] !== $originPort) {
                continue;
            }

            return true;
        }

        return false;
    }
}
