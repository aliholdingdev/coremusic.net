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

    /**
     * Şarkıyı listeye SONA ekler (Adım 07 — yarış koşulu reddi).
     *
     * Tek transaction: `MAX(position)+1` okuma → INSERT → track_count++.
     * Aynı şarkı birden fazla kez eklenebilir (serbest — position ayırır).
     *
     * @return int Atanan position (0 tabanlı)
     */
    public function appendTrack(string $playlistId, string $trackId): int;

    /** Şarkıyı listeden çıkarır (soft delete + track_count decrement). */
    public function deleteTrack(string $playlistId, string $trackId): bool;

    /**
     * Playlist meta günceller (Adım 07) — yalnızca verilen alanlar yazılır.
     *
     * @param array<string, mixed> $fields {name?, description?, visibility?}
     */
    public function updateMeta(string $playlistId, array $fields): bool;

    /**
     * Playlist'i ve tüm şarkı kayıtlarını soft-delete eder (Adım 07 Hard Rule 4).
     * Tek transaction — hard DELETE YASAK.
     */
    public function softDeleteWithTracks(string $playlistId): bool;

    /**
     * Bir listedeki aktif şarkıların music_id'leri (position ASC).
     *
     * @return list<string> 32-char hex music_id listesi
     */
    public function findTrackIds(string $playlistId): array;

    /**
     * Sıralamayı kalıcı olarak yazar (Adım 07) — tek transaction.
     *
     * @param list<string> $musicIds position 0..n-1 sırasıyla (32-char hex)
     */
    public function replacePositions(string $playlistId, array $musicIds): bool;

    /**
     * Bir listedeki şarkılar (position ASC; silinmiş satırlar hariç).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findTracks(string $playlistId, int $limit): array;
}
