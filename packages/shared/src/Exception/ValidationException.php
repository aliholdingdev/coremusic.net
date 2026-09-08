<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Exception;

use InvalidArgumentException;
use RuntimeException;

class ValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(array $errors, ?\Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct('Dogrulama hatasi: ' . implode(', ', $errors), 422, $previous);
    }

    public static function fromSingle(string $field, string $message): self
    {
        return new self(["{$field}: {$message}"]);
    }

    public static function fromArray(array $errors): self
    {
        return new self($errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function forField(string $field): ?string
    {
        foreach ($this->errors as $error) {
            if (str_starts_with($error, "{$field}:")) {
                return substr($error, strlen("{$field}: ") );
            }
        }
        return null;
    }
}
