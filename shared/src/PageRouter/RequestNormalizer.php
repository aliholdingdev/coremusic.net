<?php declare(strict_types=1);

namespace CoreMusic\PageRouter;

final class RequestNormalizer
{
    private const MAX_PORT = 65535;

    private array $envOverrides = [];

    public function __construct(array $envOverrides = [])
    {
        $this->envOverrides = $envOverrides;
    }

    public function normalize(): array
    {
        $hasSession = function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE;

        $host = $this->get('HTTP_HOST', '');
        if ($host !== '') {
            $host = parse_url('http://' . $host, PHP_URL_HOST) ?: $host;
        }
        $host = $this->filterVar($host, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'localhost';

        $scheme = $this->determineScheme($host);

        $port = (int)($this->get('SERVER_PORT', '80'));
        if ($scheme === 'https' && $port === 80) {
            $port = 443;
        } elseif ($scheme === 'http' && $port === 443) {
            $port = 80;
        }

        $uri = $this->get('REQUEST_URI', '/');
        if ($uri !== '/') {
            $uri = preg_replace('#^(/index\.php)+#', '', $uri) ?: '/';
        }
        if ($uri !== '/') {
            $uri = preg_replace('#^/+#', '/', $uri);
        }
        $uri = $this->filterVar($uri, FILTER_SANITIZE_URL) ?: '/';

        $parsedPath  = parse_url($uri, PHP_URL_PATH) ?: '/';
        $parsedQuery = parse_url($uri, PHP_URL_QUERY) ?: '';

        $this->populateGlobals($host, $scheme, $port, $uri, $parsedPath, $parsedQuery);

        return [
            'scheme'     => $scheme,
            'host'       => $host,
            'port'       => $port,
            'path'       => $parsedPath,
            'query'      => $parsedQuery,
            'uri'        => $uri,
            'hasSession' => $hasSession,
        ];
    }

    private function determineScheme(string $host): string
    {
        if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
            return 'http';
        }

        $forwardedProto = $this->get('HTTP_X_FORWARDED_PROTO', '');
        if ($forwardedProto !== '') {
            $proto = strtolower(trim(explode(',', $forwardedProto)[0]));
            if (in_array($proto, ['http', 'https'], true)) {
                return $proto;
            }
        }

        if ($this->get('HTTPS', '') !== '' && strtolower($this->get('HTTPS', '')) !== 'off') {
            return 'https';
        }

        return 'http';
    }

    private function populateGlobals(string $host, string $scheme, int $port, string $uri, string $parsedPath, string $parsedQuery): void
    {
        $_SERVER['HTTP_HOST']      = $host;
        $_SERVER['SERVER_NAME']    = $host;
        $_SERVER['REQUEST_URI']    = $uri;
        $_SERVER['SCRIPT_NAME']    = '/index.php';
        $_SERVER['QUERY_STRING']   = $parsedQuery;
        $_SERVER['PATH_INFO']      = $parsedPath;
        $_SERVER['REQUEST_PATH']   = $parsedPath;
        $_SERVER['PHP_SELF']       = '/index.php' . $parsedPath;
        $_SERVER['SERVER_PORT']    = (string)$port;
        $_SERVER['REQUEST_SCHEME'] = $scheme;

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = array_map('trim', explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR']));
            if (!empty($ips[0])) {
                $_SERVER['REMOTE_ADDR'] = $ips[0];
            }
        }
    }

    private function get(string $key, string $default = ''): string
    {
        if (isset($this->envOverrides[$key])) {
            return $this->envOverrides[$key];
        }
        return isset($_SERVER[$key]) ? (string)$_SERVER[$key] : $default;
    }

    private function filterVar(string $value, int $filter): string
    {
        if ($value === '') {
            return '';
        }
        $result = filter_var($value, $filter);
        return ($result === false) ? '' : (string)$result;
    }
}
