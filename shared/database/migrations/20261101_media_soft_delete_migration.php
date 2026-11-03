<?php declare(strict_types=1);

/**
 * CoreMusic — media_access & media_audit Migration (coremusic_media)
 * -----------------------------------------------------------------
 * ADR-112: media_access / media_audit Soft-Delete — Canlı Şema Dump SSOT'a Hizalanır.
 *
 * Vault SSOT: .ai/.sql/mysql/coremusic_media.sql
 *   - media_access: satır 167 (is_deleted), 168 (deleted_at)
 *   - media_audit: satır 193 (is_deleted), 194 (deleted_at)
 *
 * Ön-kontrol (çalıştırmadan önce): sütun varsa migration atlanır.
 *   SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS
 *    WHERE TABLE_SCHEMA='coremusic_media'
 *      AND TABLE_NAME IN ('media_access', 'media_audit')
 *      AND COLUMN_NAME IN ('is_deleted', 'deleted_at');
 *
 * @see [[.decisions/draft/ADR-112-media-soft-delete-canli-dump-hizalama]]
 * @see [[.rules/karar-kurallari]] §1
 */

$queries = [
    // 1. media_access — soft-delete kolonları
    "ALTER TABLE `coremusic_media`.`media_access`
        ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `updated_at`,
        ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_deleted`",

    // 2. media_audit — dump SSOT (coremusic_media.sql:192-194) hizalama (updated_at + soft-delete)
    "ALTER TABLE `coremusic_media`.`media_audit`
        ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
        ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0 AFTER `updated_at`,
        ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_deleted`",
];

return $queries;
