<?php
declare(strict_types=1);

/**
 * Register Request DTO.
 *
 * @file RegisterRequest.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Request;

/**
 * Register Request Data Transfer Object.
 */
final class RegisterRequest
{
    public function __construct(
        private readonly string $username,
        private readonly string $email,
        private readonly string $displayName,
        private readonly string $password,
        private readonly string $gender
    ) {}

    /**
     * Get username.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get display name.
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Get password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get gender.
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'] ?? '',
            email: $data['email'] ?? '',
            displayName: $data['displayName'] ?? '',
            password: $data['password'] ?? '',
            gender: $data['gender'] ?? 'neutral'
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'displayName' => $this->displayName,
            'password' => $this->password,
            'gender' => $this->gender,
        ];
    }
}
