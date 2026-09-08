<?php

declare(strict_types=1);

namespace CoreMusic\Shared\ValueObject;

use InvalidArgumentException;

readonly class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Gecersiz e-posta adresi: {$value}");
        }
        $this->value = strtolower($trimmed);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function domain(): string
    {
        return substr($this->value, strrpos($this->value, '@') + 1);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
