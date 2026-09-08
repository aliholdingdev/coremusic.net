<?php
declare(strict_types=1);

/**
 * Playlist Response DTO.
 *
 * @file PlaylistResponse.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Response;

/**
 * Playlist Response Data Transfer Object.
 */
final class PlaylistResponse
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $description,
        private readonly int $trackCount,
        private readonly array $tracks
    ) {}

    /**
     * Get playlist ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get name.
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
     * Get track count.
     */
    public function getTrackCount(): int
    {
        return $this->trackCount;
    }

    /**
     * Get tracks.
     */
    public function getTracks(): array
    {
        return $this->tracks;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            trackCount: $data['trackCount'] ?? 0,
            tracks: $data['tracks'] ?? []
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'trackCount' => $this->trackCount,
            'tracks' => $this->tracks,
        ];
    }
}
