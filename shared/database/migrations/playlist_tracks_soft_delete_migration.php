<?php declare(strict_types=1);

/**
 * CoreMusic — playlist_tracks Migration (coremusic_playlist)
 *
 * Vault SSOT (.ai/.sql/mysql/coremusic_playlist.sql, satır 106-107) ile canlı
 * şema arasındaki drift'i kapatır: canlı coremusic_playlist.playlist_tracks
 * tablosunda is_deleted/deleted_at sütunları yok (SHOW COLUMNS kanıtı
 * 2026-10-24) — Adım 07 repository kodu soft-delete kullanır (Hard Rule 4).
 *
 * ADR-040 (BCNF) + ADR-014 (forward-only) compliant.
 * Soft delete + timestamp zorunlu (migration-template §4.1 #3/#4).
 *
 * NOT: MySQL 8/9 `ADD COLUMN IF NOT EXISTS` desteklemez — dosya ikinci kez
 * çalıştırılırsa 1060 (duplicate column) hatası verir; önce §3.4 ön-kontrolü
 * çalıştırın.
 *
 * @see [[decisions/index]] — ADR-014 · ADR-040
 * @see [[.templates/infrastructure/migration-template]] — Guardrail #16
 */

$queries = [

// ============================================================
// playlist_tracks — soft-delete sütunları (şema drift onarımı)
// ============================================================
// Ön-kontrol (çalıştırmadan önce): sütun varsa bu migration atlanır.
//   SELECT COLUMN_NAME FROM information_schema.COLUMNS
//    WHERE TABLE_SCHEMA='coremusic_playlist'
//      AND TABLE_NAME='playlist_tracks'
//      AND COLUMN_NAME IN ('is_deleted','deleted_at');
"ALTER TABLE `coremusic_playlist`.`playlist_tracks`
    ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `updated_at`",

"ALTER TABLE `coremusic_playlist`.`playlist_tracks`
    ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_deleted`",

];

return $queries;
