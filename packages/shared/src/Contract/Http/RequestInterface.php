<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Contract\Http;

interface RequestInterface
{
    public function method(): string;

    public function uri(): string;

    public function headers(): array;

    public function body(): ?string;

    public function header(string $name): ?string;

    public function hasHeader(string $name): bool;
}
