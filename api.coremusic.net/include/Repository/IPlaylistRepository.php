<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

/**
 * IPlaylistRepository — oynatma listesi veri erişim sözleşmesi (Faz 3 iskeleti).
 *
 * ADR-002: PDO prepared statement yalnız · SELECT * yasak.
 * IMPLEMENTED: hayır — implementasyon Faz 4'te.
 */
interface IPlaylistRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByUserId(string $userId): array;

    /** @return array<string, mixed>|null */
    public function findById(string $playlistId): ?array;

    /**
     * Liste oluşturur.
     *
     * @param array<string, mixed> $data {user_id, name, description?, is_public?}
     * @return string Oluşturulan listenin id'si
     */
    public function insert(array $data): string;

    /** Şarkıyı listeye ekler (position: sona). */
    public function insertTrack(string $playlistId, string $trackId, int $position): bool;

    /** Şarkıyı listeden çıkarır. */
    public function deleteTrack(string $playlistId, string $trackId): bool;

    /** Bir listedeki en yüksek position (ekleme için). */
    public function maxPosition(string $playlistId): int;
}
