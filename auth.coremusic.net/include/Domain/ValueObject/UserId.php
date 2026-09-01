<?php declare(strict_types=1);

namespace CoreMusic\Auth\Domain\ValueObject;

/**
 * UserId ValueObject — UUID hex string temsili.
 *
 * BINARY(16) UUID v7 ile uyumlu.
 */
final class UserId
{
    private function __construct(
        public readonly string $hex,
    ) {}

    /**
     * UUID hex string'den oluştur.
     */
    public static function fromHex(string $hex): self
    {
        $hex = trim($hex);
        if (strlen($hex) !== 32 || !ctype_xdigit($hex)) {
            throw new \InvalidArgumentException('Invalid UUID hex: ' . $hex);
        }
        return new self($hex);
    }

    /**
     * Rastgele UUID v7 oluştur.
     */
    public static function generate(): self
    {
        return new self(\CoreMusic\Security\UuidV7::generateHex());
    }

    public function __toString(): string
    {
        return $this->hex;
    }

    public function equals(self $other): bool
    {
        return $this->hex === $other->hex;
    }
}
