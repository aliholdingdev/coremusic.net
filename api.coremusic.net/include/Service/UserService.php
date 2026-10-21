<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\IUserProfileRepository;

/**
 * UserService — kullanıcı profil iş katmanı (IUserService implementasyonu).
 *
 * Faz 4. Mass-assignment koruması: yalnız {display_name, avatar_url} uygulanır
 * (users şemasında bio kolonu YOK — USER_COLUMNS doğrulaması). Mevcut değerler
 * korunur: data'da olmayan alan DB'deki değeriyle yazılır.
 */
final class UserService implements IUserService
{
    public function __construct(
        private readonly IUserProfileRepository $profiles,
    ) {}

    public function getProfile(string $userId): ?array
    {
        return $this->profiles->findByUserId($userId);
    }

    public function updateProfile(string $userId, array $data): ?array
    {
        $current = $this->profiles->findByUserId($userId);
        if ($current === null) {
            return null;
        }

        $displayName = array_key_exists('display_name', $data)
            ? trim((string) $data['display_name'])
            : (string) ($current['display_name'] ?? '');
        $avatarUrl = array_key_exists('avatar_url', $data)
            ? trim((string) $data['avatar_url'])
            : (string) ($current['avatar_url'] ?? '');

        $this->profiles->updateProfile($userId, $displayName, $avatarUrl);

        return $this->profiles->findByUserId($userId) ?? $current;
    }
}
