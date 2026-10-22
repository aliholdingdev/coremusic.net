<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\IPlaylistRepository;

/**
 * PlaylistService — oynatma listesi iş katmanı (IPlaylistService implementasyonu).
 *
 * Faz 4. Sahiplik modeli (güvenlik — enumerate koruma):
 *   - private liste + sahibi değil → null/404 (varlık sızıntısı yok)
 *   - ekleme/çıkarma yalnız sahip (owner_id_hex === userId)
 */
final class PlaylistService implements IPlaylistService
{
    public function __construct(
        private readonly IPlaylistRepository $playlists,
    ) {}

    public function listForUser(string $userId): array
    {
        return $this->playlists->findByUserId($userId);
    }

    public function create(string $userId, array $data): array
    {
        $id = $this->playlists->insert([
            'user_id'     => $userId,
            'name'        => trim((string) ($data['name'] ?? '')),
            'description' => isset($data['description']) && is_string($data['description'])
                ? trim($data['description'])
                : null,
        ]);

        return $this->playlists->findById($id)
            ?? ['playlist_id' => $id, 'name' => trim((string) ($data['name'] ?? ''))];
    }

    public function getWithTracks(string $playlistId, string $userId): ?array
    {
        $playlist = $this->playlists->findById($playlistId);
        if ($playlist === null) {
            return null;
        }

        // Ownership: private ise yalnız sahip görür; public/followers_only herkese.
        if (($playlist['visibility'] ?? 'private') === 'private'
            && ($playlist['owner_id_hex'] ?? '') !== $userId
        ) {
            return null;
        }

        $playlist['tracks'] = $this->playlists->findTracks($playlistId, 500);

        return $playlist;
    }

    public function addTrack(string $playlistId, string $trackId, string $userId): ?array
    {
        $playlist = $this->ownedPlaylist($playlistId, $userId);
        if ($playlist === null) {
            return null;
        }

        // MAX+1 + INSERT + track_count tek transaction (repo::appendTrack —
        // Adım 07 Hard Rule 2/3: yarış koşulu reddedilir).
        $position = $this->playlists->appendTrack($playlistId, $trackId);

        return [
            'playlist_id' => $playlistId,
            'track_id'    => $trackId,
            'position'    => $position,
        ];
    }

    public function removeTrack(string $playlistId, string $trackId, string $userId): bool
    {
        if ($this->ownedPlaylist($playlistId, $userId) === null) {
            return false;
        }

        return $this->playlists->deleteTrack($playlistId, $trackId);
    }

    public function update(string $playlistId, array $data, string $userId): ?array
    {
        if ($this->ownedPlaylist($playlistId, $userId) === null) {
            return null;
        }

        $fields = [];
        if (array_key_exists('name', $data)) {
            $fields['name'] = trim((string) $data['name']);
        }
        if (array_key_exists('description', $data)) {
            $fields['description'] = $data['description'] === null
                ? null
                : trim((string) $data['description']);
        }
        if (array_key_exists('visibility', $data)) {
            $fields['visibility'] = (string) $data['visibility'];
        }

        if ($fields === []) {
            return null;
        }

        $this->playlists->updateMeta($playlistId, $fields);

        return $this->playlists->findById($playlistId);
    }

    public function delete(string $playlistId, string $userId): bool
    {
        if ($this->ownedPlaylist($playlistId, $userId) === null) {
            return false;
        }

        // Hard Rule 4: liste + şarkı kayıtları soft-delete, tek transaction.
        return $this->playlists->softDeleteWithTracks($playlistId);
    }

    public function reorder(string $playlistId, array $trackIds, string $userId): ?array
    {
        if ($this->ownedPlaylist($playlistId, $userId) === null) {
            return null;
        }

        $current = $this->playlists->findTrackIds($playlistId);

        // Normalizasyon (idempotent): geçerli istek sırası + eksikler mevcut
        // sıralarıyla sona; tekrar eden/bilinmeyen id yok sayılır.
        $known    = array_flip($current);
        $order    = [];
        $accepted = [];

        foreach ($trackIds as $trackId) {
            $id = strtolower((string) $trackId);
            if (!isset($known[$id]) || isset($accepted[$id])) {
                continue;
            }
            $accepted[$id] = true;
            $order[]       = $id;
        }

        foreach ($current as $id) {
            if (!isset($accepted[$id])) {
                $accepted[$id] = true;
                $order[]       = $id;
            }
        }

        $this->playlists->replacePositions($playlistId, $order);

        return [
            'playlist_id' => $playlistId,
            'order'       => $order,
        ];
    }

    /** Yalnız sahipse liste döner; değilse null (404 — enumerate koruma). */
    private function ownedPlaylist(string $playlistId, string $userId): ?array
    {
        $playlist = $this->playlists->findById($playlistId);
        if ($playlist === null || ($playlist['owner_id_hex'] ?? '') !== $userId) {
            return null;
        }

        return $playlist;
    }
}
