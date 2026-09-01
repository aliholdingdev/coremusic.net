<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObject;

/**
 * Gender ValueObject — Cinsiyet temsili.
 *
 * Sadece izin verilen değerleri kabul eder: male, female, neutral.
 */
final class Gender
{
    private const ALLOWED = ['male', 'female', 'neutral'];

    private function __construct(
        public readonly string $value,
    ) {}

    public static function create(string $gender): self
    {
        $gender = strtolower(trim($gender));
        if (!in_array($gender, self::ALLOWED, true)) {
            $gender = 'neutral';
        }
        return new self($gender);
    }

    public function isNeutral(): bool
    {
        return $this->value === 'neutral';
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
