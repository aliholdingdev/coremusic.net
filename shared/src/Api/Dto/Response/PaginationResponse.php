<?php
declare(strict_types=1);

/**
 * Pagination Response DTO.
 *
 * @file PaginationResponse.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Dto\Response;

/**
 * Pagination Response Data Transfer Object.
 */
final class PaginationResponse
{
    public function __construct(
        private readonly array $data,
        private readonly int $page,
        private readonly int $pageSize,
        private readonly int $totalItems,
        private readonly int $totalPages
    ) {}

    /**
     * Get data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get current page.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Get page size.
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * Get total items.
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * Get total pages.
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: $data['data'] ?? [],
            page: $data['page'] ?? 1,
            pageSize: $data['pageSize'] ?? 20,
            totalItems: $data['totalItems'] ?? 0,
            totalPages: $data['totalPages'] ?? 0
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'page' => $this->page,
            'pageSize' => $this->pageSize,
            'totalItems' => $this->totalItems,
            'totalPages' => $this->totalPages,
        ];
    }

    /**
     * Create pagination response from data and counts.
     */
    public static function create(
        array $data,
        int $page,
        int $pageSize,
        int $totalItems
    ): self {
        $totalPages = (int) ceil($totalItems / $pageSize);
        
        return new self(
            data: $data,
            page: $page,
            pageSize: $pageSize,
            totalItems: $totalItems,
            totalPages: $totalPages
        );
    }
}
