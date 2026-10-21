<?php declare(strict_types=1);

namespace CoreMusic\Api\Service;

/**
 * IUserService — kullanıcı profil uçları iş katmanı sözleşmesi (Faz 3 iskeleti).
 *
 * Faz 4'te implementasyon: profile repository (users BINARY(16) UUID-v7).
 * IMPLEMENTED: hayır — route kayıtları implemented:false (405) kalır.
 */
interface IUserService
{
    /** @return array<string, mixed>|null Bulunamazsa null */
    public function getProfile(string $userId): ?array;

    /**
     * Profil günceller (yalnız sahibi — middleware/controller doğrular).
     * Mass-assignment koruması serviste: yalnız {display_name?, avatar_url?} uygulanır
     * (users şeması: USER_COLUMNS — bio kolonu YOK).
     *
     * @param array<string, mixed> $data {display_name?, avatar_url?}
     * @return array<string, mixed>|null Güncellenmiş profil; kullanıcı yoksa null
     */
    public function updateProfile(string $userId, array $data): ?array;
}
