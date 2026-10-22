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

    /**
     * Dropdown menü envanteri (Adım 11) — route'lar gerçek disk yüzeyinden:
     * /ayarlar + /history = header.php dropdown (L99-L100) · admin = admin.coremusic.net
     * (PNG: admin-1920 · T17 admin-login/register/layout spec'leri).
     *
     * @var list<array{id: string, label: string, route: string, roles: list<string>}>
     */
    private const MENU_ITEMS = [
        [
            'id'    => 'settings',
            'label' => 'Ayarlar',
            'route' => '/ayarlar',
            'roles' => ['user', 'admin'],
        ],
        [
            'id'    => 'history',
            'label' => 'Geçmiş',
            'route' => '/history',
            'roles' => ['user', 'admin'],
        ],
        [
            'id'    => 'admin',
            'label' => 'Admin Panel',
            'route' => 'https://admin.coremusic.net/',
            'roles' => ['admin'],
        ],
    ];

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
                'menu'               => [],
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
            'menu'               => $this->menuForRole((string) ($user['role'] ?? 'user')),
            'search_suggestions' => $this->searchSuggestions(),
        ];
    }

    /**
     * Rol bazlı menü — SUNUCU FİLTRESİ (Adım 11 Hard Rule 2): istemciye yalnız
     * kullanıcının rolüne uygun öğeler gider; yetkisiz öğe yanıt gövdesine hiç
     * yazılmaz (client'ta "gizle" yetmez — menü öğesi API'den gelmiyorsa DOM'a
     * hiç basılmaz).
     *
     * @return list<array{id: string, label: string, route: string, roles: list<string>}>
     */
    private function menuForRole(string $role): array
    {
        return array_values(array_filter(
            self::MENU_ITEMS,
            static fn (array $item): bool => in_array($role, $item['roles'], true),
        ));
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
