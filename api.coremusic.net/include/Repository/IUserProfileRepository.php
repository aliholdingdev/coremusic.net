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
    /** @return array<string, mixed>|null */
    public function findByUserId(string $userId): ?array;

    /** Profili upsert eder. */
    public function upsertProfile(string $userId, array $data): bool;
}
