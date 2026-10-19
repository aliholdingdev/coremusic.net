<?php
declare(strict_types=1);

/**
 * Music Response DTO.
 *
 * @file MusicResponse.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Response;

/**
 * Music Response Data Transfer Object.
 */
final class MusicResponse
{
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $artist,
        private readonly string $album,
        private readonly int $duration,
        private readonly string $coverUrl,
        private readonly string $streamUrl
    ) {}

    /**
     * Get music ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get artist.
     */
    public function getArtist(): string
    {
        return $this->artist;
    }

    /**
     * Get album.
     */
    public function getAlbum(): string
    {
        return $this->album;
    }

    /**
     * Get duration in seconds.
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Get cover URL.
     */
    public function getCoverUrl(): string
    {
        return $this->coverUrl;
    }

    /**
     * Get stream URL.
     */
    public function getStreamUrl(): string
    {
        return $this->streamUrl;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            title: $data['title'] ?? '',
            artist: $data['artist'] ?? '',
            album: $data['album'] ?? '',
            duration: $data['duration'] ?? 0,
            coverUrl: $data['coverUrl'] ?? '',
            streamUrl: $data['streamUrl'] ?? ''
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'artist' => $this->artist,
            'album' => $this->album,
            'duration' => $this->duration,
            'coverUrl' => $this->coverUrl,
            'streamUrl' => $this->streamUrl,
        ];
    }
}
