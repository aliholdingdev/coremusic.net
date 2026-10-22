-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7)
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_patch`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_patch`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `migration_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'running',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `tables_affected` int unsigned DEFAULT NULL,
  `rows_affected` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ml_db` (`db_name`),
  KEY `idx_ml_status` (`status`),
  KEY `idx_ml_started` (`started_at` DESC),
  CONSTRAINT `migration_log_chk_1` CHECK ((`direction` in (_utf8mb4'up',_utf8mb4'down'))),
  CONSTRAINT `migration_log_chk_2` CHECK ((`status` in (_utf8mb4'running',_utf8mb4'completed',_utf8mb4'failed',_utf8mb4'rolled_back')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `patches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `patch_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target_db` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patch_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'schema',
  `sql_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `version_from` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version_to` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `applied_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_patch_name` (`patch_name`),
  KEY `idx_p_target` (`target_db`),
  KEY `idx_p_status` (`status`),
  KEY `idx_p_type` (`patch_type`),
  CONSTRAINT `patches_chk_1` CHECK ((`patch_type` in (_utf8mb4'schema',_utf8mb4'data',_utf8mb4'fix',_utf8mb4'security'))),
  CONSTRAINT `patches_chk_2` CHECK ((`status` in (_utf8mb4'pending',_utf8mb4'applied',_utf8mb4'skipped',_utf8mb4'failed')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `schema_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schema_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `db_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sql_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `applied_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `execution_ms` int unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'applied',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sv_db_version` (`db_name`,`version`),
  KEY `idx_sv_status` (`status`),
  KEY `idx_sv_applied` (`applied_at` DESC),
  CONSTRAINT `schema_versions_chk_1` CHECK ((`status` in (_utf8mb4'applied',_utf8mb4'rolled_back',_utf8mb4'pending')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

SET FOREIGN_KEY_CHECKS = 1;
