<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class ResponseEmitter
{
    private const JSON_HEADERS = [
        'Content-Type'           => 'application/json; charset=utf-8',
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control'          => 'no-store, no-cache, must-revalidate, max-age=0',
    ];

    /** Pipeline dışı yanıtlar için yedek CSP (ADR-012 — nonce yokken 'self' varyantı). */
    private const FALLBACK_CSP = "default-src 'self'; script-src 'self' https:; style-src 'self' https://assets.coremusic.net fonts.googleapis.com; style-src-attr 'unsafe-inline'; img-src 'self' data: https://assets.coremusic.net; font-src 'self' https://assets.coremusic.net fonts.gstatic.com; connect-src 'self' https://assets.coremusic.net; media-src 'self' https://assets.coremusic.net; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";

    public function emit(array $response, ?string $traceId = null, bool $isSpa = false, array $extraHeaders = []): never
    {
        $httpStatus = $response['httpStatus'] ?? 200;
        $type       = $response['type']       ?? 'html';
        $body       = $response['body']       ?? '';
        $headers    = $response['headers']    ?? [];

        http_response_code($httpStatus);
        $allHeaders = array_merge($headers, $extraHeaders);

        if ($type === 'redirect' || $httpStatus === 302) {
            $location = $headers['Location'] ?? '/';
            $this->redirect($location, $httpStatus, $traceId, $allHeaders);
        }

        if ($isSpa || $type === 'json') {
            $this->sendJson(
                is_array($body) ? $body : ['error' => 'internal_error'],
                $httpStatus,
                $traceId,
                $allHeaders,
            );
        }

        $this->sendHtml((string)$body, $httpStatus, $traceId, $allHeaders);
    }

    public function sendJson(array $data, int $statusCode = 200, ?string $traceId = null, array $extraHeaders = []): never
    {
        http_response_code($statusCode);
        foreach (self::JSON_HEADERS as $k => $v) {
            header("$k: $v");
        }
        $this->emitTraceAndExtras($traceId, $extraHeaders);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        exit;
    }

    public function sendHtml(string $html, int $statusCode = 200, ?string $traceId = null, array $extraHeaders = []): never
    {
        http_response_code($statusCode);
        $etag = '"' . hash('xxh64', $html) . '"';

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
            http_response_code(304);
            $this->emitTraceAndExtras($traceId, $extraHeaders);
            exit;
        }

        header('Content-Type: text/html; charset=UTF-8');
        header('ETag: ' . $etag);
        header('Vary: Accept-Encoding, Cookie');
        header('Content-Length: ' . strlen($html));
        $this->emitTraceAndExtras($traceId, $extraHeaders);
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        echo $html;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        exit;
    }

    public function redirect(string $location, int $statusCode = 302, ?string $traceId = null, array $extraHeaders = []): never
    {
        http_response_code($statusCode);
        header("Location: $location");
        $this->emitTraceAndExtras($traceId, $extraHeaders);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        exit;
    }

    private function emitTraceAndExtras(?string $traceId, array $extraHeaders): void
    {
        if ($traceId !== null) {
            header('X-Trace-Id: ' . $traceId);
        }
        foreach ($extraHeaders as $name => $value) {
            if (strcasecmp((string)$name, 'Vary') === 0) {
                $value = $this->mergeVary((string)$value);
            }
            header($name . ': ' . $value);
        }
        $this->applyFallbackSecurityHeaders();
    }

    /** Eklenen Vary token'ını daha önce gönderilmiş Vary ile birleştirir (Cookie vary korunur). */
    private function mergeVary(string $value): string
    {
        foreach (headers_list() as $sent) {
            if (stripos($sent, 'Vary:') !== 0) {
                continue;
            }
            $existing = trim(substr($sent, strlen('Vary:')));
            foreach (explode(',', $existing) as $token) {
                $token = trim($token);
                if ($token !== '' && stripos($value, $token) === false) {
                    $value .= ', ' . $token;
                }
            }
        }

        return $value;
    }

    /**
     * Pipeline dışında üretilen yanıtlar (kernel catch 500, OriginCheck 403,
     * RateLimiter 429/503, auth pre-kernel) için yedek güvenlik başlıkları.
     * SecurityHeadersMiddleware (ADR-012) zaten CSP yazdıysa DOKUNMAZ — nonce zinciri değişmez.
     */
    private function applyFallbackSecurityHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        $fallback = [
            'Content-Security-Policy' => self::FALLBACK_CSP,
            'X-Content-Type-Options'  => 'nosniff',
            'X-Frame-Options'         => 'DENY',
            'Referrer-Policy'         => 'strict-origin-when-cross-origin',
        ];

        foreach ($fallback as $name => $value) {
            if (!$this->hasSentHeader($name)) {
                header($name . ': ' . $value);
            }
        }
    }

    private function hasSentHeader(string $name): bool
    {
        $prefix = strtolower($name) . ':';
        foreach (headers_list() as $sent) {
            if (stripos($sent, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }
}
