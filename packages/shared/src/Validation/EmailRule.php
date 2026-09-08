<?php

declare(strict_types=1);

namespace CoreMusic\Shared\Validation;

readonly class EmailRule
{
    public function validate(string $value): ?string
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return 'E-posta adresi bos olamaz.';
        }

        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            return 'Gecersiz e-posta formati.';
        }

        $domain = substr($trimmed, strrpos($trimmed, '@') + 1);
        if (strlen($domain) < 3) {
            return 'Gecersiz e-posta domaini.';
        }

        return null;
    }
}
