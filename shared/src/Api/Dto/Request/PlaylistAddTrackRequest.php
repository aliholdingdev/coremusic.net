<?php
declare(strict_types=1);

/**
 * Playlist Add Track Request DTO.
 *
 * @file PlaylistAddTrackRequest.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Request;

/**
 * Playlist Add Track Request Data Transfer Object.
 */
final class PlaylistAddTrackRequest
{
    public function __construct(
        private readonly string $playlistId,
        private readonly string $musicId,
        private readonly int $position = -1
    ) {}

    /**
     * Get playlist ID.
     */
    public function getPlaylistId(): string
    {
        return $this->playlistId;
    }

    /**
     * Get music ID.
     */
    public function getMusicId(): string
    {
        return $this->musicId;
    }

    /**
     * Get position.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            playlistId: $data['playlistId'] ?? '',
            musicId: $data['musicId'] ?? '',
            position: $data['position'] ?? -1
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'playlistId' => $this->playlistId,
            'musicId' => $this->musicId,
            'position' => $this->position,
        ];
    }
}
