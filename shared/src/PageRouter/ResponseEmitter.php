<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class ResponseEmitter
{
    private const JSON_HEADERS = [
        'Content-Type'           => 'application/json; charset=utf-8',
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control'          => 'no-store, no-cache, must-revalidate, max-age=0',
    ];

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
            header($name . ': ' . $value);
        }
    }
}
