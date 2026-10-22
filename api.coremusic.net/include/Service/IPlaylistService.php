<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * IPlaylistService — oynatma listesi uçları iş katmanı sözleşmesi (Faz 4 + Adım 07).
 *
 * Sahiplik modeli (IDOR): her yazma/okumada ownership doğrulanır; erişim yoksa
 * null/false → controller 404 (enumerate koruma).
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

    /** Liste + şarkıları; sahibi değilse ve private ise null. */
    public function getWithTracks(string $playlistId, string $userId): ?array;

    /**
     * Listeye şarkı ekler (position otomatik sona — transaction içinde MAX+1).
     *
     * @return array<string, mixed>|null Eklenen ilişki {playlist_id, track_id, position};
     *         liste yoksa veya sahiplik ihlalinde null
     */
    public function addTrack(string $playlistId, string $trackId, string $userId): ?array;

    /** Listeden şarkı çıkarır; liste yok veya sahiplik ihlali → false. */
    public function removeTrack(string $playlistId, string $trackId, string $userId): bool;

    /**
     * Playlist meta günceller (Adım 07) — yalnız sahip; erişim yoksa null.
     *
     * @param array<string, mixed> $data {name?, description?, visibility?}
     * @return array<string, mixed>|null Güncellenmiş liste; null = 404 (enumerate koruma)
     */
    public function update(string $playlistId, array $data, string $userId): ?array;

    /** Playlist'i soft-delete eder (şarkı kayıtları dahil — tek transaction); null → false. */
    public function delete(string $playlistId, string $userId): bool;

    /**
     * Sıralamayı kalıcı yazar (Adım 07) — yalnız sahip.
     *
     * Normalizasyon: istekteki bilinmeyen id'ler yok sayılır, istekte olmayan
     * aktif şarkılar mevcut sıralarıyla SONA eklenir (idempotent).
     *
     * @param list<string> $trackIds 32-char hex music_id listesi
     * @return array<string, mixed>|null {playlist_id, order}; null = 404
     */
    public function reorder(string $playlistId, array $trackIds, string $userId): ?array;
}
