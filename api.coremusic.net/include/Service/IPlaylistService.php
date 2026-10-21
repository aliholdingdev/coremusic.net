<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * IPlaylistService — oynatma listesi uçları iş katmanı sözleşmesi (Faz 3 iskeleti).
 *
 * Faz 4'te implementasyon: PlaylistRepository + ownership doğrulama.
 * IMPLEMENTED: hayır — route kayıtları implemented:false (405) kalır.
 */
interface IPlaylistService
{
    /** Kullanıcının oynatma listeleri (izinsiz erişim yok — userId zorunlu). */
    public function listForUser(string $userId): array;

    /**
     * Liste oluşturur.
     *
     * @param array<string, mixed> $data {name, description?, is_public?}
     * @return array<string, mixed> Oluşturulan liste (id dahil)
     */
    public function create(string $userId, array $data): array;

    /** Liste + şarkıları; sahibi değilse ve public değilse null. */
    public function getWithTracks(string $playlistId, string $userId): ?array;

    /**
     * Listeye şarkı ekler (position otomatik sona).
     *
     * @return array<string, mixed> Eklenen ilişki (playlist_id, track_id, position)
     */
    public function addTrack(string $playlistId, string $trackId, string $userId): array;

    /** Listeden şarkı çıkarır; sahiplik ihlalinde false. */
    public function removeTrack(string $playlistId, string $trackId, string $userId): bool;
}
