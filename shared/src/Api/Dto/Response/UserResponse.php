<?php
declare(strict_types=1);

/**
 * User Response DTO.
 *
 * @file UserResponse.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Response;

/**
 * User Response Data Transfer Object.
 */
final class UserResponse
{
    public function __construct(
        private readonly string $id,
        private readonly string $username,
        private readonly string $email,
        private readonly string $displayName,
        private readonly string $avatar,
        private readonly string $gender,
        private readonly array $roles
    ) {}

    /**
     * Get user ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

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
     * Get avatar URL.
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * Get gender.
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * Get roles.
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            username: $data['username'] ?? '',
            email: $data['email'] ?? '',
            displayName: $data['displayName'] ?? '',
            avatar: $data['avatar'] ?? '',
            gender: $data['gender'] ?? 'neutral',
            roles: $data['roles'] ?? []
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'displayName' => $this->displayName,
            'avatar' => $this->avatar,
            'gender' => $this->gender,
            'roles' => $this->roles,
        ];
    }
}
