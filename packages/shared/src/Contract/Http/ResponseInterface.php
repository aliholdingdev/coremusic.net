<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Http;

interface ResponseInterface
{
    public function statusCode(): int;

    public function body(): string;

    public function headers(): array;

    public function header(string $name): ?string;

    public function withStatus(int $status): static;

    public function withBody(string $body): static;

    public function withHeader(string $name, string $value): static;

    public function json(mixed $data, int $status = 200): static;
}
