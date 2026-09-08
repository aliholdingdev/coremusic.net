<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Http;

readonly class Headers
{
    private array $headers;

    public function __construct(array $headers = [])
    {
        $this->headers = array_change_key_case($headers, CASE_LOWER);
    }

    public function get(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function has(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    public function all(): array
    {
        return $this->headers;
    }

    public function with(string $name, string $value): self
    {
        $headers = $this->headers;
        $headers[strtolower($name)] = $value;
        return new self($headers);
    }

    public function without(string $name): self
    {
        $headers = $this->headers;
        unset($headers[strtolower($name)]);
        return new self($headers);
    }

    public static function contentType(string $type): self
    {
        return new self(['content-type' => $type]);
    }

    public static function json(): self
    {
        return self::contentType('application/json; charset=utf-8');
    }

    public static function bearerToken(string $token): self
    {
        return new self(['authorization' => "Bearer {$token}"]);
    }
}
