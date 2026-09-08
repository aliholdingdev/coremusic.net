<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Validation;

readonly class ValidationResult
{
    private array $errors;

    private function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public static function success(): self
    {
        return new self([]);
    }

    public static function failure(array $errors): self
    {
        return new self($errors);
    }

    public static function fromSingle(string $field, string $message): self
    {
        return new self(["{$field}: {$message}"]);
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    public function merge(self $other): self
    {
        return new self(array_merge($this->errors, $other->errors));
    }
}
