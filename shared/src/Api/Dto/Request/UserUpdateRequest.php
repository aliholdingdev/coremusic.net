<?php
declare(strict_types=1);

/**
 * User Update Request DTO.
 *
 * @file UserUpdateRequest.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Request;

/**
 * User Update Request Data Transfer Object.
 */
final class UserUpdateRequest
{
    public function __construct(
        private readonly string $displayName = '',
        private readonly string $email = '',
        private readonly string $avatar = ''
    ) {}

    /**
     * Get display name.
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Get email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get avatar.
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            displayName: $data['displayName'] ?? '',
            email: $data['email'] ?? '',
            avatar: $data['avatar'] ?? ''
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'displayName' => $this->displayName,
            'email' => $this->email,
            'avatar' => $this->avatar,
        ];
    }
}
