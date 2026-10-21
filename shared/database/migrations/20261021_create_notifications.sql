-- ===========================================================================
-- 20261021_create_notifications.sql
-- Bildirim tabloları — system_notifications (ensure); duplicate ÜRETİLMEDİ
-- ===========================================================================
-- DURUM  : Bildirim işlevi ZATEN İKİ tabloya bölünmüş:
--          · coremusic_system.system_notifications (satır 12-29) — GENEL bildirim
--            (id, user_id, notification_type, title, message, is_read, created_at)
--            Görevdeki "coremusic_notifications" = bu tablonun superset karşılığı.
--          · coremusic_social.social_notifications (satır 100-117) — sosyal
--            etkileşim bildirimi (from_user_id, entity_type/entity_id polymorphic).
--          ÜÇÜNCÜ DUPLICATE "notifications" tablosu ÜRETİLMEDİ (zero-hallucination).
-- PK     : Mevcut pattern korunur — BIGINT UNSIGNED AI (system_notifications zaten AI).
--          (Karar 1 kapsamı yalnız listening/recent_tracks tablolarıdır; notification
--           tablosunun mevcut PK'si DEĞİŞTİRİLMEZ — surgical change.)
-- FK     : Karar 3 — cross-DB FK bu dosyaya TAŞINDI. notification -> users, CASCADE.
-- Charset: utf8mb4 / utf8mb4_unicode_ci (dump teyitli).
-- Idempotent: CREATE TABLE IF NOT EXISTS ✓ · ALTER ADD CONSTRAINT ✗ (pre-check Sonra).
-- ===========================================================================

-- ===========================================================================
-- BÖLÜM 0 — PRE-CHECK (salt-okunur; FK eklemeden ÖNCE orphan=0 doğrula)
-- ===========================================================================
SELECT COUNT(*) AS orphan_notification_user
FROM `coremusic_system`.`system_notifications` n
LEFT JOIN `coremusic_auth`.`users` u ON u.`id` = n.`user_id`
WHERE u.`id` IS NULL AND n.`user_id` IS NOT NULL;

-- ===========================================================================
-- UP
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- system_notifications — GENEL kullanici bildirimleri (ensure; mevcut)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_system`.`system_notifications` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`           BINARY(16)   NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `notification_type` VARCHAR(100) NOT NULL COMMENT 'domain verisi (ENUM degil, §10) — system|promo|warning|...',
  `title`             VARCHAR(255) NOT NULL,
  `message`           TEXT         NOT NULL COMMENT 'task istegi: body',
  `is_read`           TINYINT(1)   NOT NULL DEFAULT 0,
  `read_at`           TIMESTAMP    NULL DEFAULT NULL,
  `is_deleted`        TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`        TIMESTAMP    NULL DEFAULT NULL COMMENT 'soft delete',
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_read` (`user_id`,`is_read`),
  KEY `idx_notifications_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sistem geneli kullanici bildirimleri';

-- ===========================================================================
-- BÖLÜM 1 — cross-DB FK: system_notifications (Karar 3: TAŞINDI, CASCADE)
-- ===========================================================================
-- Index idx_notifications_user_read (user_id, is_read) zaten CREATE'de (§6.4) —
-- user_id sol sütun → FK indeksi karşılanır.
ALTER TABLE `coremusic_system`.`system_notifications`
  ADD CONSTRAINT `fk_system_notifications_user`
    FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- NOT: social_notifications (coremusic_social) AYRI ve FARKLI amaclidir (sosyal
--   etkileşim); burada dokunulmaz. Polymorphic entity_type/entity_id mevcut kodda
--   bilinçli — yeni tabloda polymorphic YOK (§10), sadece ensure.

-- ===========================================================================
-- DOWN (ROLLBACK — yalnız referans; ADR-014 forward-only: production DOWN çalıştırmaz)
-- ===========================================================================
-- ALTER TABLE `coremusic_system`.`system_notifications` DROP FOREIGN KEY `fk_system_notifications_user`;
-- DROP TABLE IF EXISTS `coremusic_system`.`system_notifications`;
-- (system_notifications mevcut DB'de veri barındırır → ASLA DROP edilmez, §9.4.)
