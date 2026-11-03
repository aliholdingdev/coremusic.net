<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

use CoreMusic\Api\Repository\IUserProfileRepository;
use CoreMusic\Exception\ValidationException;

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

        // F10: avatar_url şema allowlist'i — yalnız boş (avatar silme) veya
        // http(s):// ile başlayan, host'u çözülebilen mutlak URL kabul edilir.
        // javascript:/data:/vbscript:/protokol-relatif (//evil) ve göreli
        // yollar 422 ile reddedilir (DB kolonu varchar(500) "Profile avatar URL").
        if ($avatarUrl !== '' && !self::isSafeAvatarUrl($avatarUrl)) {
            throw ValidationException::multiple([
                'avatar_url' => 'avatar_url yalnız http(s):// ile başlayan geçerli bir URL olabilir.',
            ]);
        }

        $this->profiles->updateProfile($userId, $displayName, $avatarUrl);

        return $this->profiles->findByUserId($userId) ?? $current;
    }

    /**
     * F10: scheme + host allowlist (dış URL doğrulama noktası).
     */
    private static function isSafeAvatarUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url($url);
        if ($parts === false || !isset($parts['host'])) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if ($scheme !== 'http' && $scheme !== 'https') {
            return false;
        }

        // Host çözülebilir olmalı (bariz bozuk/parasit girdiyi de eler).
        return (bool) filter_var($parts['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
    }
}
