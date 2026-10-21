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
     *
     * @param array<string, mixed> $data {display_name?, bio?, avatar_url?}
     * @return array<string, mixed> Güncellenmiş profil
     */
    public function updateProfile(string $userId, array $data): array;
}
