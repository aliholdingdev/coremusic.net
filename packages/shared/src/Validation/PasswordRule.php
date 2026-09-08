<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Validation;

readonly class PasswordRule
{
    private int $minLength;
    private bool $requireUppercase;
    private bool $requireDigit;
    private bool $requireSpecial;

    public function __construct(
        int $minLength = 8,
        bool $requireUppercase = true,
        bool $requireDigit = true,
        bool $requireSpecial = true,
    ) {
        $this->minLength = $minLength;
        $this->requireUppercase = $requireUppercase;
        $this->requireDigit = $requireDigit;
        $this->requireSpecial = $requireSpecial;
    }

    public function validate(string $value): ?string
    {
        if (strlen($value) < $this->minLength) {
            return "Sifre en az {$this->minLength} karakter olmalidir.";
        }

        if ($this->requireUppercase && !preg_match('/[A-Z]/', $value)) {
            return 'Sifre en az bir buyuk harf icermelidir.';
        }

        if ($this->requireDigit && !preg_match('/[0-9]/', $value)) {
            return 'Sifre en az bir rakam icermelidir.';
        }

        if ($this->requireSpecial && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
            return 'Sifre en ozel bir karakter icermelidir.';
        }

        return null;
    }
}
