<?php
declare(strict_types=1);

/**
 * Playlist Create Request DTO.
 *
 * @file PlaylistCreateRequest.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Request;

/**
 * Playlist Create Request Data Transfer Object.
 */
final class PlaylistCreateRequest
{
    public function __construct(
        private readonly string $name,
        private readonly string $description = '',
        private readonly bool $isPublic = false
    ) {}

    /**
     * Get playlist name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Is public.
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            isPublic: $data['isPublic'] ?? false
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'isPublic' => $this->isPublic,
        ];
    }
}
