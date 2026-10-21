<?php declare(strict_types=1);

namespace CoreMusic\Api\Repository;

/**
 * IUserProfileRepository — kullanıcı profil veri erişim sözleşmesi (Faz 3 iskeleti).
 *
 * users.id = BINARY(16) UUID-v7 (coremusic_auth.sql L19).
 * ADR-002: PDO prepared statement yalnız · SELECT * yasak.
 * IMPLEMENTED: hayır — implementasyon Faz 4'te.
 */
interface IUserProfileRepository
{
    /**
     * Aktif kullanıcının profil satırı (users: is_active=1 AND is_deleted=0).
     *
     * @return array{user_id_hex: string, username: string, display_name: string|null, gender: string|null, avatar_url: string|null, created_at: mixed}|null
     */
    public function findByUserId(string $userId): ?array;

    /**
     * Profil alanlarını günceller (UPDATE — kullanıcı middleware'den gelir, INSERT yok).
     * display_name boş string ise NULL YAZILMAZ (DB NOT NULL riskine karşı korunur).
     */
    public function updateProfile(string $userId, string $displayName, ?string $avatarUrl): bool;
}
