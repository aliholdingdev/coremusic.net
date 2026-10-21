<?php declare(strict_types=1);

namespace CoreMusic\Api\Controller;

use CoreMusic\Api\Repository\IMusicRepository;
use CoreMusic\Api\Repository\IPlaylistRepository;
use CoreMusic\Api\Repository\IUserProfileRepository;

final class UserHeaderController extends AbstractController
{
    public function __construct(
        private readonly IUserProfileRepository $userRepo,
        private readonly IPlaylistRepository $playlistRepo,
        private readonly IMusicRepository $musicRepo,
    ) {}

    /**
     * @param array<string, mixed> $route
     * @param array<string, mixed> $request
     */
    public function handle(array $route, array $request): array
    {
        $userId = $this->authUserId($request);

        // Kullanıcı giriş yapmışsa profil ve playlist'leri çek
        $userData = null;
        $playlists = [];
        if ($userId !== null) {
            $profile = $this->userRepo->findByUserId($userId);
            if ($profile !== null) {
                $userData = [
                    'id'       => $userId,
                    'username' => $profile['username'] ?? 'Kullanıcı',
                    'email'    => $profile['email'] ?? '',
                    'role'     => $profile['role'] ?? 'user',
                    'gender'   => $profile['gender'] ?? 'neutral',
                ];
            }
            // İlk 5 çalma listesi (kısayollar)
            $allPlaylists = $this->playlistRepo->findByUserId($userId);
            $playlists = array_slice($allPlaylists, 0, 5);
        }

        // Arama önerileri için popüler parçalar (en çok dinlenen veya son eklenen 5 parça)
        $popularTracks = $this->musicRepo->findMany(5, 0);
        $searchSuggestions = array_map(static function (array $track): array {
            return [
                'id'          => $track['music_hex'] ?? '',
                'title'       => $track['title'] ?? '',
                'artist_name' => $track['artist_name'] ?? '',
            ];
        }, $popularTracks);

        return $this->success([
            'user'               => $userData,
            'is_authenticated'   => $userData !== null,
            'playlist_shortcuts' => $playlists,
            'unread_notifications' => 0, // Bildirim sistemi
            'search_suggestions' => $searchSuggestions,
        ]);
    }

    private function authUserId(array $request): ?string
    {
        $id = $request['_auth_user']['id'] ?? null;
        if (!is_string($id) || $id === '') {
            return null;
        }

        return $id;
    }
}
