<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

/**
 * IMusicRepository — müzik veri erişim sözleşmesi (Faz 3 iskeleti · Faz 4 yuvası).
 *
 * ADR-002: PDO prepared statement yalnız · SELECT * yasak (kolonlar implementasyonda).
 * IMPLEMENTED: hayır — implementasyon Faz 4'te (home.coremusic.net taşınması).
 */
interface IMusicRepository
{
    /** @return array<string, mixed>|null */
    public function findById(string $trackId): ?array;

    /**
     * Birincil dosya satırı (stream için).
     *
     * @return array{file_path: string, file_format: string, file_size: int}|null
     */
    public function findPrimaryFile(string $musicIdHex): ?array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findMany(int $limit, int $offset): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $limit): array;

    /** Toplam sayfa hesabı için sayaç. */
    public function count(): int;
}
