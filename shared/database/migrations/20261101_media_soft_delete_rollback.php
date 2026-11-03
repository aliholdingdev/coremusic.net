<?php declare(strict_types=1);

/**
 * CoreMusic — media_access & media_audit Rollback (coremusic_media)
 * ----------------------------------------------------------------
 * ADR-112: media_access / media_audit Soft-Delete Rollback.
 *
 * @see [[.decisions/draft/ADR-112-media-soft-delete-canli-dump-hizalama]]
 * @see [[.rules/karar-kurallari]] §1
 */

$queries = [
    // 1. media_access — soft-delete kolonlarını kaldır
    "ALTER TABLE `coremusic_media`.`media_access`
        DROP COLUMN `is_deleted`,
        DROP COLUMN `deleted_at`",

    // 2. media_audit — soft-delete ve updated_at kaldır
    "ALTER TABLE `coremusic_media`.`media_audit`
        DROP COLUMN `is_deleted`,
        DROP COLUMN `deleted_at`,
        DROP COLUMN `updated_at`",
];

return $queries;
