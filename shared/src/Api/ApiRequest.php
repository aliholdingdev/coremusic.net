<?php
declare(strict_types=1);

/**
 * PSR-7 compliant API Request wrapper.
 *
 * @file ApiRequest.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api;

/**
 * API Request wrapper for PSR-7 compliance.
 */
final class ApiRequest
{
    private string $method;
    private string $uri;
    private array $body;
    private array $headers;
    private array $queryParams;
    private array $attributes = [];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->parseUri();
        $this->queryParams = $_GET;
        $this->headers = $this->parseHeaders();
        $this->body = $this->parseBody();
    }

    /**
     * Get the request method.
     */
    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    /**
     * Get the request URI.
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the request body as array.
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Get a specific header value.
     */
    public function getHeader(string $name): ?string
    {
        $lowerName = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $lowerName) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get query parameters.
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Get a request attribute.
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Set a request attribute.
     */
    public function withAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Parse URI from server variables.
     */
    private function parseUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        
        if ($queryString !== '') {
            $uri = strtok($uri, '?');
        }
        
        return $uri;
    }

    /**
     * Parse headers from server variables.
     */
    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$headerName] = $value;
            }
        }
        
        // Add content-type if present
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }
        
        return $headers;
    }

    /**
     * Parse request body from php://input.
     */
    private function parseBody(): array
    {
        if ($this->method === 'GET') {
            return [];
        }

        $contentType = $this->getHeader('content-type') ?? '';
        
        if (str_contains($contentType, 'application/json')) {
            $rawBody = file_get_contents('php://input');
            $decoded = json_decode($rawBody, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return $_POST;
        }
        
        return [];
    }
}
