<?php
declare(strict_types=1);

/**
 * Music Search Request DTO.
 *
 * @file MusicSearchRequest.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Request;

/**
 * Music Search Request Data Transfer Object.
 */
final class MusicSearchRequest
{
    public function __construct(
        private readonly string $query = '',
        private readonly string $genre = '',
        private readonly string $artist = '',
        private readonly int $page = 1,
        private readonly int $limit = 20
    ) {}

    /**
     * Get search query.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get genre filter.
     */
    public function getGenre(): string
    {
        return $this->genre;
    }

    /**
     * Get artist filter.
     */
    public function getArtist(): string
    {
        return $this->artist;
    }

    /**
     * Get page number.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Get limit.
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            query: $data['query'] ?? '',
            genre: $data['genre'] ?? '',
            artist: $data['artist'] ?? '',
            page: max(1, $data['page'] ?? 1),
            limit: min(100, max(1, $data['limit'] ?? 20))
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'genre' => $this->genre,
            'artist' => $this->artist,
            'page' => $this->page,
            'limit' => $this->limit,
        ];
    }
}
