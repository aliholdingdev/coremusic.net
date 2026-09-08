<?php
declare(strict_types=1);

/**
 * Login Request DTO.
 *
 * @file LoginRequest.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Request;

/**
 * Login Request Data Transfer Object.
 */
final class LoginRequest
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly bool $rememberMe = false
    ) {}

    /**
     * Get email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get remember me flag.
     */
    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            rememberMe: $data['rememberMe'] ?? false
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'rememberMe' => $this->rememberMe,
        ];
    }
}
