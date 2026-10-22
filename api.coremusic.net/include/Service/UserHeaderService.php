<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\IMusicRepository;
use CoreMusic\Api\Repository\INotificationRepository;
use CoreMusic\Api\Repository\IPlaylistRepository;
use CoreMusic\Api\Repository\IUserProfileRepository;

/**
 * UserHeaderService — header özet iş katmanı (IUserHeaderService implementasyonu).
 *
 * Adım 05: Katman ihlali düzeltmesi — controller'ın 4 repository bağımlılığı
 * buraya taşındı (Controller→Service→Repository tek yön). Ownership: her
 * kullanıcı-sahipli veri userId filtresiyle sorgulanır (IDOR önleme).
 * Anonim istek: user null, liste boş, bildirim 0 — hata değil.
 */
final class UserHeaderService implements IUserHeaderService
{
    private const PLAYLIST_SHORTCUT_LIMIT = 5;

    private const SEARCH_SUGGESTION_LIMIT = 5;

    public function __construct(
        private readonly IUserProfileRepository $profiles,
        private readonly IPlaylistRepository $playlists,
        private readonly IMusicRepository $music,
        private readonly INotificationRepository $notifications,
    ) {}

    public function getHeaderSummary(?string $userId): array
    {
        // Anonim özet — hata değil, boş/varsayılan veri (Adım 05 §9: boş liste hata değil).
        if ($userId === null) {
            return [
                'user'               => null,
                'is_authenticated'   => false,
                'playlist_shortcuts' => [],
                'unread_notifications' => 0,
                'search_suggestions' => $this->searchSuggestions(),
            ];
        }

        $profile = $this->profiles->findByUserId($userId);
        $user = null;
        if ($profile !== null) {
            $user = [
                'id'       => $userId,
                'username' => (string) ($profile['username'] ?? 'Kullanıcı'),
                'email'    => (string) ($profile['email'] ?? ''),
                'role'     => (string) ($profile['role'] ?? 'user'),
                'gender'   => (string) ($profile['gender'] ?? 'neutral'),
            ];
        }

        $allPlaylists = $this->playlists->findByUserId($userId);

        return [
            'user'               => $user,
            'is_authenticated'   => $user !== null,
            'playlist_shortcuts' => array_slice($allPlaylists, 0, self::PLAYLIST_SHORTCUT_LIMIT),
            'unread_notifications' => $this->notifications->countUnread($userId),
            'search_suggestions' => $this->searchSuggestions(),
        ];
    }

    /** Popüler/son parçalardan arama önerileri — public veri, ownership yok. */
    private function searchSuggestions(): array
    {
        $popularTracks = $this->music->findMany(self::SEARCH_SUGGESTION_LIMIT, 0);

        return array_map(static function (array $track): array {
            return [
                'id'          => (string) ($track['music_hex'] ?? ''),
                'title'       => (string) ($track['title'] ?? ''),
                'artist_name' => (string) ($track['artist_name'] ?? ''),
            ];
        }, $popularTracks);
    }
}
