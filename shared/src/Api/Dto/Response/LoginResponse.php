<?php
declare(strict_types=1);

/**
 * Login Response DTO.
 *
 * @file LoginResponse.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Response;

/**
 * Login Response Data Transfer Object.
 */
final class LoginResponse
{
    public function __construct(
        private readonly string $accessToken,
        private readonly string $refreshToken,
        private readonly int $expiresIn,
        private readonly array $user
    ) {}

    /**
     * Get access token.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get refresh token.
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * Get expiration time in seconds.
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * Get user data.
     */
    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: $data['accessToken'] ?? '',
            refreshToken: $data['refreshToken'] ?? '',
            expiresIn: $data['expiresIn'] ?? 3600,
            user: $data['user'] ?? []
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken,
            'expiresIn' => $this->expiresIn,
            'user' => $this->user,
        ];
    }
}
