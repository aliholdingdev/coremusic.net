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

        $position = $this->playlists->maxPosition($playlistId) + 1;
        $this->playlists->insertTrack($playlistId, $trackId, $position);

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
